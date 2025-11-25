<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('pharmacy_id')->label('ID da Farmácia'),
            ExportColumn::make('category_id')->label('ID da Categoria'),
            ExportColumn::make('name')->label('Nome'),
            ExportColumn::make('slug')->label('Slug'),
            ExportColumn::make('description')->label('Descrição'),
            ExportColumn::make('composition')->label('Composição'),
            ExportColumn::make('indications')->label('Indicações'),
            ExportColumn::make('contraindications')->label('Contraindicações'),
            ExportColumn::make('dosage')->label('Dosagem'),
            ExportColumn::make('manufacturer')->label('Fabricante'),
            ExportColumn::make('batch_number')->label('Número do Lote'),
            ExportColumn::make('expiry_date')->label('Data de Validade'),
            ExportColumn::make('barcode')->label('Código de Barras'),
            ExportColumn::make('image')->label('Imagem'),
            ExportColumn::make('requires_prescription')->label('Requer Prescrição'),
            ExportColumn::make('is_controlled')->label('É Controlado'),
            ExportColumn::make('is_active')->label('Ativo'),
            ExportColumn::make('price')->label('Preço'),
            ExportColumn::make('stock_quantity')->label('Quantidade em Estoque'),
            ExportColumn::make('is_available')->label('Disponível'),
            ExportColumn::make('created_at')->label('Criado em'),
            ExportColumn::make('updated_at')->label('Atualizado em'),
            ExportColumn::make('deleted_at')->label('Deletado em'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Exportação concluída: ' . number_format($export->successful_rows) . ' ' . str('linha')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('linha')->plural($failedRowsCount) . ' falharam.';
        }

        return $body;
    }
}
