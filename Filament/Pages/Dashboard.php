<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\ChartWidget;
use App\Models\Order;
use App\Models\Product;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\PharmacyProduct;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Visão Geral';
    protected static ?string $navigationLabel = 'Início';

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStatsWidget::class,
            SalesChartWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            RecentOrdersWidget::class,
            TopProductsWidget::class,
            RecentActivitiesWidget::class,
        ];
    }
}

class DashboardStatsWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalSales = Order::where('status', 'delivered')->sum('total');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending_payment')->count();
        $activePharmacies = Pharmacy::where('is_active', true)->count();

        return [
            BaseStatsOverviewWidget\Stat::make('Total de Vendas', 'AOA ' . number_format($totalSales, 2, ',', '.'))
                ->description('Vendas confirmadas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            BaseStatsOverviewWidget\Stat::make('Pedidos', $totalOrders)
                ->description($pendingOrders . ' pendentes')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
                
            BaseStatsOverviewWidget\Stat::make('Valor Médio', 'AOA ' . number_format($totalOrders > 0 ? $totalSales / $totalOrders : 0, 2, ',', '.'))
                ->description('Por pedido')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning'),
                
            BaseStatsOverviewWidget\Stat::make('Farmácias Ativas', $activePharmacies)
                ->description('Farmácias verificadas')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary'),
        ];
    }
}

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Vendas dos Últimos 30 Dias';
    protected static string $color = 'info';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Order::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->where('status', 'delivered')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Vendas (AOA)',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

class RecentOrdersWidget extends \Filament\Widgets\TableWidget
{
    protected static ?string $heading = 'Pedidos Recentes';
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::query()->with(['user', 'pharmacy'])->latest()->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('order_number')
                ->label('Pedido')
                ->searchable(),
            \Filament\Tables\Columns\TextColumn::make('user.name')
                ->label('Cliente')
                ->searchable(),
            \Filament\Tables\Columns\TextColumn::make('pharmacy.name')
                ->label('Farmácia')
                ->searchable(),
            \Filament\Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'warning' => 'pending_payment',
                    'info' => 'confirmed',
                    'success' => 'delivered',
                    'danger' => 'cancelled',
                ]),
            \Filament\Tables\Columns\TextColumn::make('total')
                ->label('Total')
                ->money('AOA'),
            \Filament\Tables\Columns\TextColumn::make('created_at')
                ->label('Data')
                ->dateTime('d/m/Y H:i'),
        ];
    }
}

class TopProductsWidget extends \Filament\Widgets\TableWidget
{
    protected static ?string $heading = 'Produtos Mais Vendidos';
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query()
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->selectRaw('SUM(quantity)');
            }])
            ->orderBy('total_sold', 'desc')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\ImageColumn::make('images')
                ->label('')
                ->getStateUsing(fn($record) => $record->getFirstImage())
                ->circular()
                ->size(40),
            \Filament\Tables\Columns\TextColumn::make('name')
                ->label('Produto')
                ->searchable(),
            \Filament\Tables\Columns\TextColumn::make('category.name')
                ->label('Categoria')
                ->badge(),
            \Filament\Tables\Columns\TextColumn::make('total_sold')
                ->label('Vendidos')
                ->default(0),
        ];
    }
}

class RecentActivitiesWidget extends \Filament\Widgets\Widget
{
    protected static string $view = 'filament.widgets.recent-activities';
    protected static ?string $heading = 'Feed de Atividades';
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $activities = collect([
            [
                'type' => 'order',
                'message' => 'Novo pedido #PH240001 recebido',
                'time' => '2 minutos atrás',
                'icon' => 'heroicon-o-shopping-bag',
                'color' => 'success'
            ],
            [
                'type' => 'pharmacy',
                'message' => 'Farmácia Central solicitou aprovação',
                'time' => '15 minutos atrás',
                'icon' => 'heroicon-o-building-storefront',
                'color' => 'warning'
            ],
            [
                'type' => 'product',
                'message' => 'Produto Paracetamol com estoque baixo',
                'time' => '1 hora atrás',
                'icon' => 'heroicon-o-exclamation-triangle',
                'color' => 'danger'
            ],
        ]);

        return ['activities' => $activities];
    }
}
