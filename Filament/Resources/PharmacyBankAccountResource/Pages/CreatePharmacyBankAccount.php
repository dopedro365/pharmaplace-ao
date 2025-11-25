<?php

namespace App\Filament\Resources\PharmacyBankAccountResource\Pages;

use App\Filament\Resources\PharmacyBankAccountResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePharmacyBankAccount extends CreateRecord
{
    protected static string $resource = PharmacyBankAccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        
        if ($user->role === 'pharmacy' && $user->pharmacy) {
            $data['pharmacy_id'] = $user->pharmacy->id;
        }
        
        // Se for a primeira conta ou marcada como principal, garantir que seja única
        if ($data['is_primary'] ?? false) {
            \App\Models\PharmacyBankAccount::where('pharmacy_id', $data['pharmacy_id'])
                ->update(['is_primary' => false]);
        }
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
