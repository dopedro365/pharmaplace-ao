<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'content',
        'version',
        'is_active',
        'effective_date',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function acceptances()
    {
        return $this->hasMany(UserLegalAcceptance::class);
    }

    public static function getActiveTerms()
    {
        return self::where('type', 'terms_of_use')
            ->where('is_active', true)
            ->latest('effective_date')
            ->first();
    }

    public static function getActivePrivacyPolicy()
    {
        return self::where('type', 'privacy_policy')
            ->where('is_active', true)
            ->latest('effective_date')
            ->first();
    }

    public function getFormattedContentAttribute()
    {
        return nl2br(e($this->content));
    }
}



 