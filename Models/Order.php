<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pharmacy_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_type',
        'delivery_address', 
        'delivery_municipality',
        'delivery_province',
        'delivery_notes',
        'delivery_fee',
        'subtotal',
        'total',
        'status',
        'payment_method',
        'payment_proof',
        'payment_verified_at',
        'bank_account_id',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'payment_verified_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'delivery_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // STATUS SIMPLIFICADOS
    const STATUS_PAYMENT_VERIFICATION = 'payment_verification';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_RETURNED = 'returned';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(PharmacyBankAccount::class, 'bank_account_id');
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        if (!$this->payment_proof) {
            return null;
        }
        return asset('storage/' . $this->payment_proof);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PAYMENT_VERIFICATION => 'Verificando Pagamento',
            self::STATUS_CONFIRMED => 'Confirmado',
            self::STATUS_DELIVERED => 'Entregue',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_RETURNED => 'Devolvido',
            default => 'Desconhecido'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PAYMENT_VERIFICATION => 'warning',
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_RETURNED => 'info',
            default => 'gray'
        };
    }

    /**
     * Restaurar estoque quando pedido é cancelado ou devolvido
     * MÉTODO MELHORADO COM LOGS E VERIFICAÇÕES
     */
    public function restoreStock(): void
    {
        \Log::info("Order::restoreStock() - Iniciando restauração de estoque para pedido {$this->id}");
        
        $restoredItems = 0;
        $totalItemsProcessed = 0;
        
        foreach ($this->items as $item) {
            $totalItemsProcessed++;
            
            if (!$item->product) {
                \Log::warning("Order::restoreStock() - Produto não encontrado para item {$item->id}");
                continue;
            }
            
            $oldStock = $item->product->stock_quantity;
            
            // Incrementar estoque
            $item->product->increment('stock_quantity', $item->quantity);
            
            // Verificar se o produto deve voltar a ficar disponível
            $newStock = $item->product->fresh()->stock_quantity;
            
            if ($newStock > 0 && !$item->product->is_available) {
                // Verificar se não há outros motivos para indisponibilidade (validade, etc)
                if (!$item->product->shouldBeDisabledByExpiry()) {
                    $item->product->update(['is_available' => true]);
                    \Log::info("Order::restoreStock() - Produto {$item->product->id} marcado como disponível novamente");
                }
            }
            
            \Log::info("Order::restoreStock() - Produto {$item->product->id} ({$item->product->name}): estoque {$oldStock} -> {$newStock} (+{$item->quantity})");
            $restoredItems++;
        }
        
        \Log::info("Order::restoreStock() - Concluído para pedido {$this->id}: {$restoredItems}/{$totalItemsProcessed} itens restaurados");
    }

    /**
     * Verificar se o pedido pode ser cancelado/devolvido
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PAYMENT_VERIFICATION,
            self::STATUS_CONFIRMED
        ]);
    }

    public function canBeReturned(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }
}
