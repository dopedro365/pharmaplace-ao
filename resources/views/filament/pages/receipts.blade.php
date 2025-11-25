<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recibos</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Gerencie recibos emitidos para seus clientes.
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <x-filament::button icon="heroicon-o-cog" wire:click="configure_automated_receipts">
                        Configurações de recibos
                    </x-filament::button>
                    <x-filament::dropdown placement="bottom-end">
                        <x-slot name="trigger">
                            <x-filament::button icon="heroicon-m-ellipsis-vertical" color="gray" class="!p-2">
                                Mais ações
                            </x-filament::button>
                        </x-slot>
                        <x-filament::dropdown.list>
                            <x-filament::dropdown.list.item icon="heroicon-o-arrow-down-tray">
                                Exportar
                            </x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>
                </div>
            </div>
        </div>

        <!-- Receipts Table -->
        @if($this->table->getRecords()->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                <div class="flex justify-center mb-4">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Captura%20de%20ecr%C3%A3%202025-07-16%20211940-OPn5Pre9YXfDND6n9M2AMKyMyrLePB.png" alt="No receipts illustration" class="h-48 w-auto object-contain">
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Ainda não há recibos</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Para ativar recibos automatizados, vá para "Configurações de recibos".<br>
                    Depois de criado, você poderá gerenciar seus recibos aqui.
                </p>
                <x-filament::button wire:click="configure_automated_receipts">
                    Configurar recibos automatizados
                </x-filament::button>
                <p class="mt-3 text-sm">
                    <a href="#" class="text-blue-600 hover:underline">Saiba mais</a>
                </p>
            </div>
        @else
            {{ $this->table }}
        @endif
    </div>
</x-filament-panels::page>
