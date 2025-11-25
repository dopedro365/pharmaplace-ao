<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Pedidos</h2>
                </div>
                <div class="flex items-center space-x-3">
                    <x-filament::button icon="heroicon-o-plus" wire:click="add_new_order" color="primary">
                        Adicionar novo pedido
                    </x-filament::button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex flex-col justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Vendas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->totalSales }} <span class="text-sm text-gray-500">0%</span></p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex flex-col justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pedidos</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->totalOrders }} <span class="text-sm text-gray-500">0%</span></p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex flex-col justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Valor médio do pedido</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->averageOrderValue }} <span class="text-sm text-gray-500">0%</span></p>
                </div>
            </div>

            <!-- Date Filter and Search -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6 mt-6">
                <div class="flex items-center space-x-2 text-sm text-gray-600 ">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="dateFilter">
                            <option value="last_30_days">Últimos 30 dias</option>
                            <option value="today">Hoje</option>
                            <option value="last_7_days">Últimos 7 dias</option>
                            <option value="this_month">Este mês</option>
                            <option value="all_time">Todo o período</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        {{ $this->table }}
    </div>
</x-filament-panels::page>
