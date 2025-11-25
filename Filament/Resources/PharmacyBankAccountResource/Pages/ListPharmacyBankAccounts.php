<?php

namespace App\Filament\Resources\PharmacyBankAccountResource\Pages;

use App\Filament\Resources\PharmacyBankAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPharmacyBankAccounts extends ListRecords
{
    protected static string $resource = PharmacyBankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nova Conta Bancária')
                ->icon('heroicon-m-plus'),
        ];
    }
}
