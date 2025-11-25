<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pharmacy_id',
        'category_id',
        'name',
        'slug',
        'description',
        'composition',
        'indications',
        'contraindications',
        'dosage',
        'manufacturer',
        'batch_number',
        'expiry_date',
        'barcode',
        'image',
        'requires_prescription',
        'is_controlled',
        'is_active',
        'price',
        'stock_quantity',
        'is_available',
    ];
 
    protected $casts = [
        'requires_prescription' => 'boolean',
        'is_controlled' => 'boolean',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'expiry_date' => 'date',
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    // **CONFIGURAÇÃO DE DIAS ANTES DA VALIDADE**
    // Altere este valor para modificar quantos dias antes da validade o produto fica indisponível
    const DAYS_BEFORE_EXPIRY_TO_DISABLE = 15; // 30 dias antes da validade

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrl()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    // Métodos de escopo
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock_quantity', '>', 0);
    }

    /**
     * Verifica se o produto está próximo da validade
     */
    public function isNearExpiry(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);
        return $daysUntilExpiry <= (self::DAYS_BEFORE_EXPIRY_TO_DISABLE * 2) && $daysUntilExpiry > 0;
    }

    /**
     * Verifica se o produto está vencido
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    /**
     * Verifica se o produto deve ser automaticamente desabilitado por validade
     */
    public function shouldBeDisabledByExpiry(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);
        return $daysUntilExpiry <= self::DAYS_BEFORE_EXPIRY_TO_DISABLE;
    }

    /**
     * Retorna a cor da badge baseada na data de validade
     */
    public function getExpiryBadgeColor(): string
    {
        if ($this->isExpired()) {
            return 'danger'; // Vermelho - vencido
        }
        
        if ($this->isNearExpiry()) {
            return 'warning'; // Laranja - próximo da validade
        }
        
        return 'success'; // Verde - data boa
    }

    /**
     * Retorna o texto da badge baseada na data de validade
     */
    public function getExpiryBadgeText(): string
    {
        if (!$this->expiry_date) {
            return 'Sem data';
        }

        if ($this->isExpired()) {
            return 'Vencido';
        }
        
        if ($this->isNearExpiry()) {
            $days = now()->diffInDays($this->expiry_date, false);
            return $days . ' dias';
        }
        
        return $this->expiry_date->format('d/m/Y');
    }

    /**
     * Accessor que verifica automaticamente a disponibilidade baseada na validade
     * Este método é chamado TODA VEZ que o atributo is_available é acessado
     */
    public function getIsAvailableAttribute($value): bool
    {
        // Se o produto foi manualmente desabilitado, respeitar essa decisão
        if (!$value) {
            return false;
        }

        // Verificar automaticamente se deve ser desabilitado por validade
        if ($this->shouldBeDisabledByExpiry()) {
            // Atualizar no banco de dados de forma silenciosa
            $this->updateQuietly(['is_available' => false]);
            return false;
        }

        return $value;
    }

    /**
     * Scope para produtos disponíveis (considerando validade automaticamente)
     */
    public function scopeAvailableForSale($query)
    {
        return $query->where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->where(function($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>', now()->addDays(self::DAYS_BEFORE_EXPIRY_TO_DISABLE));
                    });
    }

    /**
     * Boot method para executar verificações automáticas
     */
    protected static function boot()
    {
        parent::boot();

        // Verificar validade ao salvar/atualizar
        static::saving(function ($product) {
            if ($product->shouldBeDisabledByExpiry()) {
                $product->attributes['is_available'] = false;
            }
        });

        // Verificar validade ao recuperar do banco
        static::retrieved(function ($product) {
            if ($product->shouldBeDisabledByExpiry() && $product->attributes['is_available']) {
                $product->updateQuietly(['is_available' => false]);
            }
        });
    }
}
