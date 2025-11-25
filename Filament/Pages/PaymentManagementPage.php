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
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class PaymentManagementPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static string $view = 'filament.pages.payment-management';
    protected static ?string $title = 'Gestão de Pagamentos';
    protected static ?string $navigationLabel = 'Pagamentos';
    protected static ?string $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 4;

    public $grossVolume = 'AOA 0,00';
    public $successfulPaymentsCount = 0;
    public $dateFilter = 'today'; // Default filter as per image

    public function mount(): void
    {
        $this->calculateStats();
    }

    protected function calculateStats(): void
    {
        $query = Order::query()->whereNotNull('payment_proof');

        switch ($this->dateFilter) {
            case 'today':
                $query->whereDate('created_at', now());
                break;
            case 'last_7_days':
                $query->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                break;
            case 'all_time':
                // No date filter
                break;
        }

        $this->grossVolume = 'AOA ' . number_format($query->sum('total'), 2, ',', '.');
        $this->successfulPaymentsCount = $query->where('status', 'confirmed')->count();
    }

    public function updatedDateFilter(): void
    {
        $this->calculateStats();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->with(['user', 'pharmacy', 'items.product'])->whereNotNull('payment_proof'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Data do pagamento')
                    ->dateTime('d/m/Y, H:i')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable(),
                // Coluna "Produto/Serviço"
                TextColumn::make('items.product.name')
                    ->label('Produto/Serviço')
                    ->bulleted()
                    ->getStateUsing(function (Order $record) {
                        $items = $record->items->map(fn($item) => $item->product->name . ' (' . $item->quantity . 'x)');
                        if ($items->count() > 2) {
                            return $items->take(2)->push('...');
                        }
                        return $items->toArray();
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('items.product', function (Builder $query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('payment_method')
                    ->label('Método de pagamento')
                    ->formatStateUsing(fn($state) => match($state) {
                        'cash' => 'Dinheiro',
                        'transfer' => 'Transferência',
                        'card' => 'Cartão',
                        default => 'N/A'
                    }),
                BadgeColumn::make('status')
                    ->label('Status da transação')
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
                TextColumn::make('created_at') // Reutilizando created_at para "Data da transação"
                    ->label('Data da transação')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('total')
                    ->label('Valor')
                    ->money('AOA'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'payment_verification' => 'Verificando',
                        'confirmed' => 'Confirmado',
                        'cancelled' => 'Rejeitado',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('Método de Pagamento')
                    ->options([
                        'cash' => 'Dinheiro na Entrega',
                        'transfer' => 'Transferência Bancária',
                        'card' => 'Cartão de Crédito/Débito',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('De'),
                        DatePicker::make('created_until')->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
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
            ->headerActions([
                Action::make('export')
                    ->label('Exportar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('secondary'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
