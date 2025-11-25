<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Sales Chart -->
            <div class="lg:col-span-2">
                @foreach($this->getFooterWidgets() as $widget)
                    @if($loop->first)
                        @livewire($widget)
                    @endif
                @endforeach
            </div>

            <!-- Recent Orders and Activities -->
            @foreach($this->getFooterWidgets() as $widget)
                @if(!$loop->first)
                    <div>
                        @livewire($widget)
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Suggestions Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sugestões para você</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-photo class="w-8 h-8 text-blue-500" />
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">Crie seu próprio logo</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Crie um logotipo personalizado para seu site ou negócio com o Criador de Logo Wix.
                            </p>
                            <x-filament::button size="sm" class="mt-2">
                                Criar logo
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
