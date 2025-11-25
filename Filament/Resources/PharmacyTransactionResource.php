<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PharmacyTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'referencia' => $this->referencia,
            'valor' => [
                'raw' => $this->valor,
                'formatted' => number_format($this->valor, 2, ',', '.') . ' Kz',
                'currency' => 'AOA'
            ],
            'data_transferencia' => [
                'raw' => $this->data_transferencia,
                'formatted' => $this->data_transferencia->format('d/m/Y'),
                'iso' => $this->data_transferencia->toISOString(),
                'human' => $this->data_transferencia->diffForHumans()
            ],
            'bancos' => [
                'cliente' => $this->cliente_banco,
                'empresa' => $this->empresa_banco
            ],
            'ibans' => [
                'cliente' => $this->cliente_iban,
                'empresa' => $this->empresa_iban
            ],
            'aplicativo' => $this->aplicativo,
            'status' => [
                'value' => $this->status,
                'label' => $this->getStatusLabel(),
                'color' => $this->getStatusColor(),
                'icon' => $this->getStatusIcon()
            ],
            'comprovativo' => [
                'path' => $this->comprovativo_path,
                'url' => $this->getComprovantivoUrl(),
                'exists' => $this->comprovantivoExists(),
                'type' => $this->getComprovantivoType()
            ],
            'observacoes' => $this->observacoes,
            'relationships' => [
                'pharmacy_id' => $this->pharmacy_id,
                'order_id' => $this->order_id,
                'pharmacy' => $this->whenLoaded('pharmacy', function () {
                    return [
                        'id' => $this->pharmacy->id,
                        'name' => $this->pharmacy->name,
                        'nif' => $this->pharmacy->nif
                    ];
                }),
                'order' => $this->whenLoaded('order', function () {
                    return [
                        'id' => $this->order->id,
                        'order_number' => $this->order->order_number,
                        'total_amount' => $this->order->total_amount,
                        'status' => $this->order->status
                    ];
                })
            ],
            'timestamps' => [
                'created_at' => [
                    'raw' => $this->created_at,
                    'formatted' => $this->created_at->format('d/m/Y H:i:s'),
                    'iso' => $this->created_at->toISOString(),
                    'human' => $this->created_at->diffForHumans()
                ],
                'updated_at' => [
                    'raw' => $this->updated_at,
                    'formatted' => $this->updated_at->format('d/m/Y H:i:s'),
                    'iso' => $this->updated_at->toISOString(),
                    'human' => $this->updated_at->diffForHumans()
                ]
            ],
            'validation' => [
                'is_recent' => $this->isRecentTransaction(),
                'is_valid_amount' => $this->isValidAmount(),
                'days_since_transfer' => $this->getDaysSinceTransfer(),
                'can_be_used' => $this->canBeUsed()
            ]
        ];
    }

    /**
     * Get status label in Portuguese
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'verified' => 'Verificado',
            'used' => 'Utilizado',
            'rejected' => 'Rejeitado',
            default => 'Desconhecido'
        };
    }

    /**
     * Get status color for UI
     */
    private function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'verified' => 'green',
            'used' => 'blue',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status icon
     */
    private function getStatusIcon(): string
    {
        return match($this->status) {
            'pending' => 'clock',
            'verified' => 'check-circle',
            'used' => 'check-double',
            'rejected' => 'times-circle',
            default => 'question-circle'
        };
    }

    /**
     * Get comprovativo URL
     */
    private function getComprovantivoUrl(): ?string
    {
        if (!$this->comprovativo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->comprovativo_path);
    }

    /**
     * Check if comprovativo file exists
     */
    private function comprovantivoExists(): bool
    {
        if (!$this->comprovativo_path) {
            return false;
        }

        return Storage::disk('public')->exists($this->comprovativo_path);
    }

    /**
     * Get comprovativo file type
     */
    private function getComprovantivoType(): ?string
    {
        if (!$this->comprovativo_path) {
            return null;
        }

        $extension = pathinfo($this->comprovativo_path, PATHINFO_EXTENSION);
        
        return match(strtolower($extension)) {
            'pdf' => 'PDF',
            'jpg', 'jpeg' => 'JPEG',
            'png' => 'PNG',
            default => strtoupper($extension)
        };
    }

    /**
     * Check if transaction is recent (within 30 days)
     */
    private function isRecentTransaction(): bool
    {
        return $this->data_transferencia->gte(now()->subDays(30));
    }

    /**
     * Check if amount is valid (greater than 100 Kz)
     */
    private function isValidAmount(): bool
    {
        return $this->valor >= 100;
    }

    /**
     * Get days since transfer
     */
    private function getDaysSinceTransfer(): int
    {
        return $this->data_transferencia->diffInDays(now());
    }

    /**
     * Check if transaction can be used for orders
     */
    private function canBeUsed(): bool
    {
        return $this->status === 'verified' && 
               $this->isRecentTransaction() && 
               $this->isValidAmount() &&
               $this->comprovantivoExists();
    }

    /**
     * Additional data when including detailed information
     */
    public function withDetails(): array
    {
        return array_merge($this->toArray(request()), [
            'detailed_info' => [
                'file_size' => $this->getFileSize(),
                'processing_notes' => $this->getProcessingNotes(),
                'validation_history' => $this->getValidationHistory(),
                'related_transactions' => $this->getRelatedTransactions()
            ]
        ]);
    }

    /**
     * Get file size if comprovativo exists
     */
    private function getFileSize(): ?array
    {
        if (!$this->comprovantivoExists()) {
            return null;
        }

        $sizeBytes = Storage::disk('public')->size($this->comprovativo_path);
        
        return [
            'bytes' => $sizeBytes,
            'formatted' => $this->formatFileSize($sizeBytes)
        ];
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get processing notes
     */
    private function getProcessingNotes(): array
    {
        $notes = [];
        
        if (!$this->isRecentTransaction()) {
            $notes[] = [
                'type' => 'warning',
                'message' => 'Transação antiga (mais de 30 dias)'
            ];
        }
        
        if (!$this->isValidAmount()) {
            $notes[] = [
                'type' => 'error',
                'message' => 'Valor muito baixo (mínimo 100 Kz)'
            ];
        }
        
        if (!$this->comprovantivoExists()) {
            $notes[] = [
                'type' => 'error',
                'message' => 'Arquivo do comprovativo não encontrado'
            ];
        }
        
        if ($this->status === 'rejected') {
            $notes[] = [
                'type' => 'error',
                'message' => 'Transação rejeitada: ' . $this->observacoes
            ];
        }
        
        return $notes;
    }

    /**
     * Get validation history (placeholder for future implementation)
     */
    private function getValidationHistory(): array
    {
        // This could be expanded to track validation steps
        return [
            [
                'step' => 'file_upload',
                'status' => 'completed',
                'timestamp' => $this->created_at
            ],
            [
                'step' => 'data_extraction',
                'status' => $this->status === 'pending' ? 'pending' : 'completed',
                'timestamp' => $this->created_at
            ],
            [
                'step' => 'verification',
                'status' => $this->status,
                'timestamp' => $this->updated_at
            ]
        ];
    }

    /**
     * Get related transactions (same reference or IBAN)
     */
    private function getRelatedTransactions(): array
    {
        // This could query for related transactions
        // For now, return empty array
        return [];
    }

    /**
     * Create collection resource
     */
    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'total_amount' => $resource->sum('valor'),
                'total_formatted' => number_format($resource->sum('valor'), 2, ',', '.') . ' Kz',
                'status_summary' => [
                    'pending' => $resource->where('status', 'pending')->count(),
                    'verified' => $resource->where('status', 'verified')->count(),
                    'used' => $resource->where('status', 'used')->count(),
                    'rejected' => $resource->where('status', 'rejected')->count()
                ],
                'recent_transactions' => $resource->filter(function($transaction) {
                    return $transaction->data_transferencia->gte(now()->subDays(7));
                })->count()
            ]
        ]);
    }
}
