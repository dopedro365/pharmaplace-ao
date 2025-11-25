<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\SalesOverviewStatsWidget;
use App\Filament\Widgets\TopSellingItemsWidget;
use App\Filament\Widgets\TopPayingClientsWidget;
use Carbon\Carbon;

class SalesOverviewPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.sales-overview';
    protected static ?string $title = 'Visão geral das vendas';
    protected static ?string $navigationLabel = 'Visão geral das vendas';
    protected static ?string $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 5; // Último na seção Vendas

    public $dateRange = 'last_30_days'; // Default filter
    public $comparisonDate = ''; // For comparison period

    public function mount(): void
    {
        // Set default comparison date (e.g., 30 days before the start of the current 30-day period)
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(29); // Last 30 days
        $comparisonStartDate = $startDate->subDays(30);
        $comparisonEndDate = $startDate->subDay();
        $this->comparisonDate = $comparisonStartDate->format('d M.') . ' - ' . $comparisonEndDate->format('d M. Y');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SalesOverviewStatsWidget::class,
            TopSellingItemsWidget::class,
            TopPayingClientsWidget::class,
        ];
    }

    public function updatedDateRange(): void
    {
        // Logic to update comparison date based on new dateRange
        // This is a simplified example, real implementation would be more complex
        $this->comparisonDate = '18 de mai. - 16 de jun. de 2025'; // Hardcoded for demo
    }
}
