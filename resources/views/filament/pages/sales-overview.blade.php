<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Visão geral das vendas</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Analise seu desempenho de vendas e obtenha insights sobre os seus clientes. <a href="#" class="text-blue-600 hover:underline">Saiba mais</a>
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <x-filament::button icon="heroicon-o-arrow-path" color="gray" class="!p-2">
                        <span class="sr-only">Atualizar</span>
                    </x-filament::button>
                    <x-filament::button icon="heroicon-o-pencil-square" color="primary">
                        Assinar
                    </x-filament::button>
                </div>
            </div>
            
            <!-- Date Range Filter -->
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="dateRange">
                        <option value="last_30_days">Últimos 30 dias (17 de jun. Hoje)</option>
                        <option value="this_month">Este mês</option>
                        <option value="last_month">Mês passado</option>
                        <option value="this_year">Este ano</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
                <span>em comparação com o período anterior ({{ $this->comparisonDate }})</span>
            </div>
        </div>

        <!-- Widgets Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Total de vendas (Graph Placeholder) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Total de vendas</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        AOA 0,00 <span class="text-sm text-gray-500">0%</span>
                    </p>
                    <div class="h-48 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400">
                        Gráfico de vendas (placeholder)
                    </div>
                </div>

                <!-- Itens mais vendidos (Table Placeholder) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Itens mais vendidos</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do item</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% do total</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variação</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendas brutas</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td colspan="5" class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Captura%20de%20ecr%C3%A3%202025-07-16%20212047-6kCMRUbOZWW4MlxQJ0KPxEXQqezXcs.png" alt="No sales illustration" class="h-32 w-auto object-contain mb-4">
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nenhuma venda nesse período</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Aqui você verá uma lista dos seus itens mais vendidos.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Vendas por local de faturamento (Map Placeholder) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vendas por local de faturamento</h3>
                    <div class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400">
                        Mapa de vendas (placeholder)
                    </div>
                    <div class="text-center mt-4">
                        <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nenhuma venda nesse período</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Quando seu site tiver vendas, você poderá ver de que países e cidades seus clientes vêm.
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <!-- Principais clientes pagantes (List Placeholder) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Principais clientes pagantes</h3>
                    <div class="flex flex-col items-center justify-center py-8">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Captura%20de%20ecr%C3%A3%202025-07-16%20212047-6kCMRUbOZWW4MlxQJ0KPxEXQqezXcs.png" alt="No clients illustration" class="h-32 w-auto object-contain mb-4">
                        <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nenhum cliente nesse período</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Aqui você verá seus clientes que mais gastaram e quanto eles gastaram.
                        </p>
                    </div>
                </div>

                <!-- Vendas por fonte e categoria (List Placeholder - NOT IMPLEMENTED LOGICALLY) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vendas por fonte e categoria</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm text-gray-700 dark:text-gray-300">
                            <span>Direto</span>
                            <span class="font-medium">0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-700 dark:text-gray-300">
                            <span>Google (orgânico)</span>
                            <span class="font-medium">0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-700 dark:text-gray-300">
                            <span>Email Marketing Wix</span>
                            <span class="font-medium">0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-700 dark:text-gray-300">
                            <span>Google (pago)</span>
                            <span class="font-medium">0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-700 dark:text-gray-300">
                            <span>Facebook (pago)</span>
                            <span class="font-medium">0</span>
                        </div>
                    </div>
                    <div class="text-center mt-4 text-sm text-gray-500 dark:text-gray-400">
                        <p>Nenhum detalhamento relevante</p>
                    </div>
                </div>

                <!-- Clientes novos vs. recorrentes (Placeholder) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Clientes novos vs. recorrentes</h3>
                    <div class="flex flex-col items-center justify-center py-8">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Captura%20de%20ecr%C3%A3%202025-07-16%20212103-pAxqmquElFO9hwrXRCMa4PxrNO6yBo.png" alt="No client data illustration" class="h-32 w-auto object-contain mb-4">
                        <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nenhum detalhamento relevante</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Um detalhamento das vendas por cupons, recorrência e canais aparecerá aqui.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
