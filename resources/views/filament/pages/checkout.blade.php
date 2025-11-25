<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Finalizar Compra</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Processe pedidos e finalize compras.
                    </p>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form wire:submit="checkout">
                {{ $this->form }}
                
                <div class="mt-6 flex justify-end">
                    {{ $this->getFormActions() }}
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
