<?php

namespace App\Filament\Resources\PharmacyBankAccountResource\Pages;

use App\Filament\Resources\PharmacyBankAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPharmacyBankAccount extends EditRecord
{
    protected static string $resource = PharmacyBankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Se esta for marcada como principal, desmarcar outras
        if ($data['is_primary'] ?? false) {
            Auth::user()->pharmacy->bankAccounts()
                ->where('id', '!=', $this->record->id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
