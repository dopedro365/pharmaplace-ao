<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Formulário Principal -->
        <div class="bg-white rounded-lg shadow">
            <form wire:submit="save">
                {{ $this->form }}
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    @foreach($this->getFormActions() as $action)
                        {{ $action }}
                    @endforeach
                </div>
            </form>
        </div>

        <!-- Formulário de Senha -->
        <div class="bg-white rounded-lg shadow">
            <form wire:submit="updatePassword">
                {{ $this->getPasswordForm() }}
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <x-filament::button type="submit" color="danger">
                        Alterar Senha
                    </x-filament::button>
                </div>
            </form>
        </div>

        @if(auth()->user()->isPharmacy()) 
        <!-- Links Rápidos para Farmácias -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Configurações Avançadas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('filament.painel.pages.pharmacy-location-page') }}" 
                   class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <x-heroicon-o-map-pin class="w-6 h-6 text-blue-600 mr-3" />
                    <div>
                        <div class="font-medium text-blue-900">Localização GPS</div>
                        <div class="text-sm text-blue-600">Configure latitude e longitude</div>
                    </div>
                </a>
                
                <a href="{{ route('filament.painel.resources.delivery-zones.index') }}" 
                   class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <x-heroicon-o-truck class="w-6 h-6 text-green-600 mr-3" />
                    <div>
                        <div class="font-medium text-green-900">Zonas de Entrega</div>
                        <div class="text-sm text-green-600">Gerir áreas e taxas</div>
                    </div>
                </a>
                
                <a href="{{ route('filament.painel.resources.pharmacy-bank-accounts.index') }}" 
                   class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <x-heroicon-o-credit-card class="w-6 h-6 text-purple-600 mr-3" />
                    <div>
                        <div class="font-medium text-purple-900">Contas Bancárias</div>
                        <div class="text-sm text-purple-600">Gerir dados de pagamento</div>
                    </div>
                </a>
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>
