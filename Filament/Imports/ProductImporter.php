<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    protected ?int $userId = null;

    public function __construct()
    {
        $this->userId = Auth::id();
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->label('Nome')->requiredMapping()->rules(['required', 'string', 'max:255']),
            ImportColumn::make('slug')->label('Slug')->rules(['nullable', 'string']),
            ImportColumn::make('category_id')->label('Categoria')->rules(['nullable', 'integer']),
            ImportColumn::make('description')->label('Descrição')->rules(['nullable', 'string']),
            ImportColumn::make('composition')->label('Composição')->rules(['nullable', 'string']),
            ImportColumn::make('indications')->label('Indicações')->rules(['nullable', 'string']),
            ImportColumn::make('contraindications')->label('Contraindicações')->rules(['nullable', 'string']),
            ImportColumn::make('dosage')->label('Dosagem')->rules(['nullable', 'string']),
            ImportColumn::make('manufacturer')->label('Fabricante')->rules(['nullable', 'string']),
            ImportColumn::make('batch_number')->label('Número do Lote')->rules(['nullable', 'string']),
            ImportColumn::make('expiry_date')->label('Data de Validade')->rules(['nullable', 'date']),
            ImportColumn::make('barcode')->label('Código de Barras')->rules(['nullable', 'string']),
            ImportColumn::make('image')->label('Imagem')->rules(['nullable', 'string']),
            ImportColumn::make('requires_prescription')->label('Requer Prescrição')->rules(['nullable', 'boolean']),
            ImportColumn::make('is_controlled')->label('É Controlado')->rules(['nullable', 'boolean']),
            ImportColumn::make('is_active')->label('Ativo')->rules(['nullable', 'boolean']),
            ImportColumn::make('price')->label('Preço')->rules(['nullable', 'numeric']),
            ImportColumn::make('stock_quantity')->label('Quantidade em Estoque')->rules(['nullable', 'integer']),
            ImportColumn::make('is_available')->label('Disponível')->rules(['nullable', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        $product = new Product();
        $product->pharmacy_id = $this->userId;
        return $product;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Importação concluída: ' . number_format($import->successful_rows) . ' ' . str('linha')->plural($import->successful_rows) . ' importadas.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('linha')->plural($failedRowsCount) . ' falharam.';
        }

        return $body;
    }
}
