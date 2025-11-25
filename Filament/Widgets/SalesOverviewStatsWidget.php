<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Carbon\Carbon;

class SalesOverviewStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $totalSales = Order::where('status', 'completed')->sum('total_amount');
        $averageOrderValue = Order::where('status', 'completed')->avg('total_amount');
        $completedOrdersCount = Order::where('status', 'completed')->count();

        return [
            Stat::make('Vendas Totais', 'AOA ' . number_format($totalSales, 2, ',', '.'))
                ->description('Receita total de vendas concluídas')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            Stat::make('Valor Médio do Pedido', 'AOA ' . number_format($averageOrderValue, 2, ',', '.'))
                ->description('Média de valor por pedido concluído')
                ->descriptionIcon('heroicon-m-scale')
                ->color('info'),
            Stat::make('Pedidos Concluídos', $completedOrdersCount)
                ->description('Número total de pedidos finalizados')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
        ];
    }
}
