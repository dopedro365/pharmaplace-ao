<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'license_number',
        'description',
        'address',
        'municipality',
        'province',
        'latitude',
        'longitude',
        'phone',
        'email',
        'whatsapp',
        'opening_hours',
        'logo',
        'images',
        'is_verified',
        'is_active',
        'accepts_delivery',
        'delivery_fee',
        'delivery_time_minutes',
        'minimum_order',
        'rating',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'accepts_delivery' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'delivery_fee' => 'decimal:2',
        'minimum_order' => 'decimal:2',
        'rating' => 'decimal:2',
        'opening_hours' => 'json',
        'images' => 'json',
        'delivery_time_minutes' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(PharmacyBankAccount::class)->where('is_active', true);
    }

    public function primaryBankAccount()
    {
        return $this->hasOne(PharmacyBankAccount::class)->where('is_primary', true)->where('is_active', true);
    }

    public function activeBankAccounts()
    {
        return $this->hasMany(PharmacyBankAccount::class)->where('is_active', true);
    }

    public function deliveryZones()
    {
        return $this->hasMany(DeliveryZone::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeWithDelivery($query)
    {
        return $query->where('accepts_delivery', true);
    }

    // ==================== ACCESSORS & MUTATORS ====================

    public function getFirstImage()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    // ==================== HELPER METHODS ====================

    public function getProductsCount()
    {
        return $this->products()->count();
    }

    public function getActiveProductsCount()
    {
        return $this->products()->where('is_active', true)->count();
    }

    // ==================== LOCATION METHODS ====================

    public function hasLocation()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function hasCoordinates()
    {
        return $this->hasLocation();
    }

    public function getLocationString()
    {
        if ($this->hasLocation()) {
            return $this->latitude . ', ' . $this->longitude;
        }
        return null;
    }

    public function distanceTo($latitude, $longitude)
    {
        if (!$this->hasLocation() || !$latitude || !$longitude) {
            return null;
        }

        $earthRadius = 6371; // km

        $dLat = deg2rad($this->latitude - $latitude);
        $dLon = deg2rad($this->longitude - $longitude);

        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($latitude)) * cos(deg2rad($this->latitude)) * 
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    // ==================== DOCUMENT METHODS ====================

    public function getPendingDocuments()
    {
        return $this->documents()->where('status', 'pending')->get();
    }

    public function getApprovedDocuments()
    {
        return $this->documents()->where('status', 'approved')->get();
    }

    public function hasAllDocumentsApproved()
    {
        $totalDocs = $this->documents()->count();
        $approvedDocs = $this->documents()->where('status', 'approved')->count();
        
        return $totalDocs > 0 && $totalDocs === $approvedDocs;
    }
}
