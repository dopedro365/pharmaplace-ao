<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'rejection_reason',
        'is_active', // Adicionado aqui
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isPharmacy(): bool
    {
        return $this->role === 'pharmacy';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function pharmacy()
    {
        return $this->hasOne(Pharmacy::class);
    }

    /**
     * VERIFICAR SE PODE FAZER COMPRAS
     */
    public function canMakePurchases(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * VERIFICAR SE PODE GERENCIAR PEDIDOS
     */
    public function canManageOrders(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'pharmacy']);
    }
 
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    public function hasUnreadNotifications(): bool
    {
        return $this->notifications()->whereNull('read_at')->exists();
    }

    public function getUnreadNotificationsCount(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'manager', 'pharmacy']);
    }
   
}
