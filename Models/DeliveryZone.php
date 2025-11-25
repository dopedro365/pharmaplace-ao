<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'municipality',
        'province',
        'zone_name',
        'delivery_fee',
        'delivery_time_minutes',
        'minimum_order',
        'is_active',
    ];

     protected function casts(): array
    {
        return [
            'delivery_fee' => 'decimal:2',
            'minimum_order' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relacionamentos
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLocation($query, $municipality, $province)
    {
        return $query->where('municipality', $municipality)
                    ->where('province', $province);
    }
}
