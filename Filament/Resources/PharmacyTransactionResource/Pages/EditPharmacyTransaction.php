<?php

namespace App\Filament\Resources\PharmacyTransactionResource\Pages;

use App\Filament\Resources\PharmacyTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPharmacyTransaction extends EditRecord
{
    protected static string $resource = PharmacyTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
