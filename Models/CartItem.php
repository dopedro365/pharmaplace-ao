<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id', // Agora referencia o novo modelo Product
        'pharmacy_id', // Adicionar pharmacy_id para saber de qual farmácia é o produto
        'quantity',
        'unit_price',
        // Adicione outros campos necessários
    ];
 
    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class); // Relacionamento com o novo modelo Product
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function getTotalPrice()
    {
        return $this->unit_price * $this->quantity;
    }
}
