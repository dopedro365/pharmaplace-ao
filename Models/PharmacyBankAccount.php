<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'bank_name',
        'account_holder',
        'account_number',
        'iban',
        'swift_code',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ]; 

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function getFormattedAccountNumberAttribute()
    {
        // Formatar número da conta para exibição
        $number = $this->account_number;
        return substr($number, 0, 4) . ' ' . substr($number, 4, 4) . ' ' . substr($number, 8);
    }

    public function getFormattedIbanAttribute()
    {
        // Formatar IBAN para exibição
        if (!$this->iban) return null;
        
        $iban = $this->iban;
        return substr($iban, 0, 4) . ' ' . substr($iban, 4, 4) . ' ' . substr($iban, 8, 4) . ' ' . substr($iban, 12, 4) . ' ' . substr($iban, 16);
    }
}
