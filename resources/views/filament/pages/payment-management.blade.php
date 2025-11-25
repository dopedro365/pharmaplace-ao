<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Pagamentos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Acompanhe os pagamentos dos seus clientes.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <x-filament::button icon="heroicon-o-arrow-down-tray" wire:click="export">
                    Exportar
                </x-filament::button>
            </div>
        </div>

        <!-- Overview and Stats -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="dateFilter">
                            <option value="today">Visão geral: Hoje</option>
                            <option value="last_7_days">Últimos 7 dias</option>
                            <option value="this_month">Este mês</option>
                            <option value="all_time">Todo o período</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Volume bruto</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->grossVolume }}</p>
                </div>
                <div class="flex flex-col">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pagamentos bem-sucedidos</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->successfulPaymentsCount }}</p>
                </div>
            </div>
        </div>

       
        {{ $this->table }}
    </div>
</x-filament-panels::page>
