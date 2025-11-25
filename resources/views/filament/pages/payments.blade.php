<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Order;
use App\Filament\Widgets\PaymentStatsWidget;

class PaymentsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static string $view = 'filament.pages.payments';
    protected static ?string $title = 'Pagamentos';
    protected static ?string $navigationLabel = 'Pagamentos';
    protected static ?int $navigationSort = 6;

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentStatsWidget::class,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->with(['user', 'pharmacy'])->whereNotNull('payment_proof'))
            ->columns([
                TextColumn::make('order_number')
                    ->label('Pedido')
                    ->searchable(),
                    
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable(),
                    
                TextColumn::make('pharmacy.name')
                    ->label('Farmácia')
                    ->searchable(),
                    
                TextColumn::make('total')
                    ->label('Valor')
                    ->money('AOA'),
                    
                BadgeColumn::make('status')
                    ->label('Status do Pagamento')
                    ->colors([
                        'warning' => 'payment_verification',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'payment_verification' => 'Verificando',
                        'confirmed' => 'Confirmado',
                        'cancelled' => 'Rejeitado',
                        default => 'Pendente'
                    }),
                    
                ImageColumn::make('payment_proof')
                    ->label('Comprovativo')
                    ->size(40),
                    
                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'payment_verification' => 'Verificando',
                        'confirmed' => 'Confirmado',
                        'cancelled' => 'Rejeitado',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn(Order $record) => $record->status === 'payment_verification')
                    ->action(function (Order $record) {
                        $record->update([
                            'status' => 'confirmed',
                            'payment_verified_at' => now()
                        ]);
                        $this->notify('success', 'Pagamento aprovado!');
                    }),
                Action::make('reject')
                    ->label('Rejeitar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn(Order $record) => $record->status === 'payment_verification')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'cancelled']);
                        $this->notify('success', 'Pagamento rejeitado!');
                    }),
                Action::make('view_proof')
                    ->label('Ver comprovativo')
                    ->icon('heroicon-o-photo')
                    ->url(fn(Order $record) => asset('storage/' . $record->payment_proof))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
