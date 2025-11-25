<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLegalAcceptance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'legal_document_id',
        'document_version',
        'accepted_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    } 

    public function legalDocument()
    {
        return $this->belongsTo(LegalDocument::class);
    }
}
