<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrderPaymentProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'pharmacy_transaction_id',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'file_hash',
        'status',
        'extracted_amount',
        'extracted_date',
        'extracted_reference',
        'extracted_data',
        'is_valid',
        'validation_errors',
        'processing_notes',
        'processed_at',
        'processed_by',
        'processing_history',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'validation_errors' => 'array',
        'processing_history' => 'array',
        'extracted_date' => 'date',
        'processed_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_valid' => 'boolean',
        'extracted_amount' => 'decimal:2',
        'file_size' => 'integer'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function pharmacyTransaction(): BelongsTo
    {
        return $this->belongsTo(PharmacyTransaction::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getFileUrl(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    public function fileExists(): bool
    {
        if (!$this->file_path) {
            return false;
        }

        return Storage::disk('public')->exists($this->file_path);
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isProcessed(): bool
    {
        return in_array($this->status, ['verified', 'rejected', 'used']);
    }

    public function canBeReprocessed(): bool
    {
        return in_array($this->status, ['uploaded', 'rejected']) && $this->fileExists();
    }

    public function addToProcessingHistory(string $action, array $data = []): void
    {
        $history = $this->processing_history ?? [];
        
        $history[] = [
            'action' => $action,
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];
        
        $this->update(['processing_history' => $history]);
    }
}
