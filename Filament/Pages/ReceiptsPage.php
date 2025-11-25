<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use App\Models\Receipt;

class ReceiptsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static string $view = 'filament.pages.receipts';
    protected static ?string $title = 'Recibos';
    protected static ?string $navigationLabel = 'Recibos';
    protected static ?string $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 3; // Após Pedidos e Assinaturas

    public function table(Table $table): Table
    {
        return $table
            ->query(Receipt::query()->with(['order.user']))
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('Número do Recibo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order.order_number')
                    ->label('Pedido Relacionado')
                    ->searchable(),
                TextColumn::make('order.user.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('AOA')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Método de Pagamento')
                    ->formatStateUsing(fn($state) => match($state) {
                        'cash' => 'Dinheiro',
                        'transfer' => 'Transferência',
                        'card' => 'Cartão',
                        default => 'N/A'
                    }),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'issued',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'issued' => 'Emitido',
                        'cancelled' => 'Cancelado',
                        default => 'Pendente'
                    }),
                TextColumn::make('issued_at')
                    ->label('Data de Emissão')
                    ->dateTime('d/m/Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                // Adicionar filtros se necessário
            ])
            ->actions([
                Action::make('view_receipt')
                    ->label('Ver Recibo')
                    ->icon('heroicon-o-document-text')
                    ->url(fn(Receipt $record) => '#') // Placeholder para URL do recibo
                    ->openUrlInNewTab(),
                Action::make('cancel_receipt')
                    ->label('Cancelar Recibo')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn(Receipt $record) => $record->status === 'issued')
                    ->requiresConfirmation()
                    ->action(fn(Receipt $record) => $record->update(['status' => 'cancelled'])),
            ])
            ->headerActions([
                Action::make('configure_automated_receipts')
                    ->label('Configurar Recibos Automatizados')
                    ->icon('heroicon-o-cog')
                    ->url('#') // Placeholder para a página de configuração
                    ->color('primary'),
            ])
            ->defaultSort('issued_at', 'desc');
    }
}
