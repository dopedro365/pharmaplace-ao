<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PharmacyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'order_id',
        'referencia',
        'valor',
        'data_transferencia',
        'cliente_banco',
        'empresa_banco',
        'cliente_iban',
        'empresa_iban',
        'aplicativo',
        'comprovativo_path',
        'status',
        'observacoes',
    ];
 
    protected $casts = [
        'valor' => 'decimal:2',
        'data_transferencia' => 'date',
    ];

    /**
     * Relacionamento com farmácia
     */
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    /**
     * Relacionamento com pedido
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Verifica se a transação já foi usada
     */
    public function isUsed(): bool
    {
        return $this->status === 'used';
    }

    /**
     * Verifica se a transação está verificada
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Marca a transação como usada
     */
    public function markAsUsed($orderId = null): void
    {
        $this->update([
            'status' => 'used',
            'order_id' => $orderId,
        ]);
    }

    /**
     * Verifica se a data da transferência é válida (não pode ser anterior à data especificada)
     */
    public function isDateValid(Carbon $minimumDate): bool
    {
        return $this->data_transferencia->greaterThanOrEqualTo($minimumDate);
    }

    /**
     * Scope para transações não utilizadas
     */
    public function scopeUnused($query)
    {
        return $query->where('status', '!=', 'used');
    }

    /**
     * Scope para transações verificadas
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope por farmácia
     */
    public function scopeForPharmacy($query, $pharmacyId)
    {
        return $query->where('pharmacy_id', $pharmacyId);
    }
}