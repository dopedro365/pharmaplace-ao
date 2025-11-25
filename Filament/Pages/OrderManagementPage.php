<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Actions\Action as PageAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\BulkAction;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class OrderManagementPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static string $view = 'filament.pages.order-management';
    protected static ?string $title = 'Pedidos';
    protected static ?string $navigationLabel = 'Pedidos';
    protected static ?string $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 1;

    public $totalSales = 'AOA 0,00';
    public $totalOrders = 0;
    public $averageOrderValue = 'AOA 0,00';
    public $dateFilter = 'last_30_days';

    public function mount(): void
    {
        $this->calculateStats();
    }

    protected function calculateStats(): void
    {
        $user = Auth::user();
        $query = Order::query();

        // Filtrar por role do usuário
        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'pharmacy') {
            $query->where('pharmacy_id', $user->pharmacy->id ?? 0);
        }

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

        $this->totalOrders = $query->count();
        
        // Para customers, não mostrar dados de vendas
        if ($user->role === 'customer') {
            $this->totalSales = 'N/A';
            $this->averageOrderValue = 'N/A';
        } else {
            $totalSalesAmount = $query->whereIn('status', [Order::STATUS_CONFIRMED, Order::STATUS_DELIVERED])->sum('total');
            $this->totalSales = 'AOA ' . number_format($totalSalesAmount, 2, ',', '.');
            $this->averageOrderValue = $this->totalOrders > 0 ? 'AOA ' . number_format($totalSalesAmount / $this->totalOrders, 2, ',', '.') : 'AOA 0,00';
        }
    }

    public function updatedDateFilter(): void
    {
        $this->calculateStats();
    }

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $actions = [];

        // Apenas admin, manager e pharmacy podem adicionar novos pedidos
        if (in_array($user->role, ['admin', 'manager', 'pharmacy'])) {
            $actions[] = PageAction::make('add_new_order')
                ->label('Adicionar novo pedido')
                ->icon('heroicon-o-plus')
                ->url(route('explore'))
                ->color('primary');
        }

        $actions[] = PageAction::make('export')
            ->label('Exportar')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('secondary')
            ->action(function () {
                Notification::make()
                    ->title('Exportação iniciada!')
                    ->body('O arquivo será enviado por email em breve.')
                    ->info()
                    ->send();
            });

        return $actions;
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();
        
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters())
            ->actions($this->getTableActions())
            ->bulkActions($this->getTableBulkActions())
            ->defaultSort('created_at', 'desc');
    }

    protected function getTableQuery(): Builder
    {
        $user = Auth::user();
        $query = Order::query()->with(['user', 'pharmacy', 'items.product']);

        // Aplicar filtros baseados no role
        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'pharmacy') {
            $query->where('pharmacy_id', $user->pharmacy->id ?? 0);
        }

        return $query;
    }

    protected function getTableColumns(): array
    {
        $user = Auth::user();
        $columns = [];

        $columns[] = TextColumn::make('order_number')
            ->label('Pedido')
            ->searchable()
            ->sortable();

        $columns[] = TextColumn::make('created_at')
            ->label('Data de criação')
            ->dateTime('d/m/Y, H:i')
            ->sortable();

        if ($user->role === 'customer') {
            $columns[] = TextColumn::make('pharmacy.name')
                ->label('Farmácia')
                ->searchable();
        } else {
            $columns[] = TextColumn::make('user.name')
                ->label('Cliente')
                ->searchable();
        }

        // STATUS SIMPLIFICADOS
        $columns[] = TextColumn::make('status')
            ->label('Status')
            ->getStateUsing(fn(Order $record) => $record->status_label)
            ->badge()
            ->color(fn(Order $record) => $record->status_color);

        $columns[] = TextColumn::make('total')
            ->label('Total pago')
            ->money('AOA')
            ->sortable();

        $columns[] = TextColumn::make('items_count')
            ->label('Itens')
            ->counts('items')
            ->suffix(fn($state) => $state > 0 ? ' itens' : '')
            ->url(fn(Order $record) => route('filament.painel.pages.order-detail-page', ['record' => $record->id]))
            ->icon('heroicon-o-chevron-right')
            ->color('primary');

        return $columns;
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->label('Status do Pedido')
                ->options([
                    Order::STATUS_PAYMENT_VERIFICATION => 'Verificando Pagamento',
                    Order::STATUS_CONFIRMED => 'Confirmado',
                    Order::STATUS_DELIVERED => 'Entregue',
                    Order::STATUS_CANCELLED => 'Cancelado',
                    Order::STATUS_RETURNED => 'Devolvido',
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
        ];
    }

    protected function getTableActions(): array
    {
        $user = Auth::user();
        
        return [
            ActionGroup::make([
                TableAction::make('view')
                    ->label('Ver detalhes')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Order $record) => route('filament.painel.pages.order-detail-page', ['record' => $record->id])),

                // CONFIRMAR PAGAMENTO
                TableAction::make('confirm_payment')
                    ->label('Confirmar Pagamento')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function (Order $record) use ($user) {
                        if (!in_array($user->role, ['admin', 'manager', 'pharmacy'])) {
                            return false;
                        }
                        if ($record->status !== Order::STATUS_PAYMENT_VERIFICATION) {
                            return false;
                        }
                        if ($user->role === 'pharmacy') {
                            return $record->pharmacy_id === ($user->pharmacy->id ?? 0);
                        }
                        return true;
                    })
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $actionId = uniqid();
                        \Log::info("Action [{$actionId}]: Confirmando pagamento do pedido {$record->id}");
                        
                        // ✅ APENAS ATUALIZAR STATUS - Observer cuidará das notificações
                        $record->update([
                            'status' => Order::STATUS_CONFIRMED,
                            'payment_verified_at' => now()
                        ]);
                        
                        Notification::make()
                            ->title('Pagamento confirmado!')
                            ->success()
                            ->send();
                            
                        \Log::info("Action [{$actionId}]: ✅ Ação concluída - Observer enviará notificações");
                    }),

                // MARCAR COMO ENTREGUE
                TableAction::make('mark_delivered')
                    ->label('Marcar como Entregue')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(function (Order $record) use ($user) {
                        if (!in_array($user->role, ['admin', 'manager', 'pharmacy'])) {
                            return false;
                        }
                        if ($record->status !== Order::STATUS_CONFIRMED) {
                            return false;
                        }
                        if ($user->role === 'pharmacy') {
                            return $record->pharmacy_id === ($user->pharmacy->id ?? 0);
                        }
                        return true;
                    })
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $actionId = uniqid();
                        \Log::info("Action [{$actionId}]: Marcando pedido {$record->id} como entregue");
                        
                        // ✅ APENAS ATUALIZAR STATUS - Observer cuidará das notificações
                        $record->update([
                            'status' => Order::STATUS_DELIVERED,
                            'delivered_at' => now()
                        ]);
                        
                        Notification::make()
                            ->title('Pedido marcado como entregue!')
                            ->success()
                            ->send();
                            
                        \Log::info("Action [{$actionId}]: ✅ Ação concluída - Observer enviará notificações");
                    }),

                // CANCELAR PEDIDO
                TableAction::make('cancel_order')
                    ->label('Cancelar Pedido')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(function (Order $record) use ($user) {
                        if (!in_array($user->role, ['admin', 'manager', 'pharmacy'])) {
                            return false;
                        }
                        if (!$record->canBeCancelled()) {
                            return false;
                        }
                        if ($user->role === 'pharmacy') {
                            return $record->pharmacy_id === ($user->pharmacy->id ?? 0);
                        }
                        return true;
                    })
                    ->form([
                        \Filament\Forms\Components\Textarea::make('cancellation_reason')
                            ->label('Motivo do Cancelamento')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (Order $record, array $data) {
                        $actionId = uniqid();
                        \Log::info("Action [{$actionId}]: Cancelando pedido {$record->id}");
                        
                        // ✅ APENAS ATUALIZAR STATUS - Observer cuidará do resto
                        $record->update([
                            'status' => Order::STATUS_CANCELLED,
                            'cancelled_at' => now(),
                            'cancellation_reason' => $data['cancellation_reason']
                        ]);
                        
                        Notification::make()
                            ->title('Pedido cancelado!')
                            ->body('O estoque foi restaurado automaticamente.')
                            ->success()
                            ->send();
                            
                        \Log::info("Action [{$actionId}]: ✅ Ação concluída - Observer cuidará do estoque e notificações");
                    }),

                // PROCESSAR DEVOLUÇÃO
                TableAction::make('process_return')
                    ->label('Processar Devolução')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('info')
                    ->visible(function (Order $record) use ($user) {
                        if (!in_array($user->role, ['admin', 'manager', 'pharmacy'])) {
                            return false;
                        }
                        if (!$record->canBeReturned()) {
                            return false;
                        }
                        if ($user->role === 'pharmacy') {
                            return $record->pharmacy_id === ($user->pharmacy->id ?? 0);
                        }
                        return true;
                    })
                    ->form([
                        \Filament\Forms\Components\Textarea::make('return_reason')
                            ->label('Motivo da Devolução')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->requiresConfirmation()
                    ->action(function (Order $record, array $data) {
                        $actionId = uniqid();
                        \Log::info("Action [{$actionId}]: Processando devolução do pedido {$record->id}");
                        
                        // ✅ APENAS ATUALIZAR STATUS - Observer cuidará do resto
                        $record->update([
                            'status' => Order::STATUS_RETURNED,
                            'cancellation_reason' => $data['return_reason']
                        ]);
                        
                        Notification::make()
                            ->title('Devolução processada!')
                            ->body('O estoque foi restaurado automaticamente.')
                            ->success()
                            ->send();
                            
                        \Log::info("Action [{$actionId}]: ✅ Ação concluída - Observer cuidará do estoque e notificações");
                    }),

            ])
            ->label('Ações')
            ->icon('heroicon-m-ellipsis-vertical')
            ->size('sm')
            ->color('gray')
            ->button()
        ];
    }

    protected function getTableBulkActions(): array
    {
        $user = Auth::user();
        $bulkActions = [];

        if (in_array($user->role, ['admin', 'manager', 'pharmacy'])) {
            $bulkActions[] = BulkActionGroup::make([
                BulkAction::make('confirm_payments')
                    ->label('Confirmar Pagamentos')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) use ($user) {
                        $bulkId = uniqid();
                        \Log::info("BulkAction [{$bulkId}]: Confirmando múltiplos pagamentos");
                        
                        $confirmed = 0;
                        foreach ($records as $record) {
                            if ($record->status === Order::STATUS_PAYMENT_VERIFICATION) {
                                if ($user->role !== 'pharmacy' || $record->pharmacy_id === ($user->pharmacy->id ?? 0)) {
                                    // ✅ APENAS ATUALIZAR STATUS - Observer cuidará das notificações
                                    $record->update([
                                        'status' => Order::STATUS_CONFIRMED,
                                        'payment_verified_at' => now()
                                    ]);
                                    
                                    $confirmed++;
                                }
                            }
                        }
                        
                        Notification::make()
                            ->title("{$confirmed} pagamento(s) confirmado(s)!")
                            ->success()
                            ->send();
                            
                        \Log::info("BulkAction [{$bulkId}]: ✅ {$confirmed} pagamentos confirmados - Observer enviará notificações");
                    }),
            ]);
        }

        return $bulkActions;
    }
}
