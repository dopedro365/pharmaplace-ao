<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Farmácias Próximas</h1>
                    <p class="mt-1 text-sm text-gray-600">Encontre farmácias na sua região</p>
                </div>
                
                <!-- Location Controls -->
                <div class="mt-4 sm:mt-0 flex space-x-2">
                    @if($userLatitude && $userLongitude)
                        <button wire:click="clearUserLocation" 
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpar Localização
                        </button>
                        <span class="inline-flex items-center px-3 py-2 text-sm text-green-700 bg-green-100 rounded-md">
                            📍 Localização ativa
                        </span>
                    @else
                        <button onclick="getCurrentLocation()" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Usar Minha Localização
                        </button>
                    @endif
                </div>
            </div>

            <!-- Location Display -->
            @if($showLocationDisplay && ($userLatitude && $userLongitude))
                <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-green-800">Localização ativa</p>
                            @if($userAddress)
                                <p class="text-sm text-green-700">{{ $userAddress }}</p>
                            @else
                                <p class="text-sm text-green-700">{{ number_format($userLatitude, 6) }}, {{ number_format($userLongitude, 6) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Location Error -->
            @if($locationError)
                <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-red-700">{{ $locationError }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           id="search"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Nome, endereço, município...">
                </div>

                <!-- Municipality -->
                <div>
                    <label for="municipality" class="block text-sm font-medium text-gray-700 mb-1">Município</label>
                    <select wire:model.live="selectedMunicipality" 
                            id="municipality"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos</option>
                        @foreach($municipalities as $municipality)
                            <option value="{{ $municipality }}">{{ $municipality }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Province -->
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Província</label>
                    <select wire:model.live="selectedProvince" 
                            id="province"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todas</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}">{{ $province }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Ordenar</label>
                    <select wire:model.live="sortBy" 
                            id="sort"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="name">Nome</option>
                        @if($userLatitude && $userLongitude)
                            <option value="distance">Distância</option>
                        @endif
                        <option value="newest">Mais recentes</option>
                        <option value="rating">Avaliação</option>
                    </select>
                </div>

                <!-- Distance Filter -->
                @if($userLatitude && $userLongitude)
                    <div>
                        <label for="distance" class="block text-sm font-medium text-gray-700 mb-1">Distância (km)</label>
                        <select wire:model.live="maxDistance" 
                                id="distance"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Qualquer</option>
                            <option value="1">Até 1 km</option>
                            <option value="2">Até 2 km</option>
                            <option value="5">Até 5 km</option>
                            <option value="10">Até 10 km</option>
                            <option value="20">Até 20 km</option>
                        </select>
                    </div>
                @endif
            </div>

            <!-- Filter Toggles -->
            <div class="mt-4 flex flex-wrap gap-4">
                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model.live="showOnlyActive"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Apenas abertas</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model.live="showOnlyVerified"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Apenas verificadas</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" 
                           wire:model.live="showOnlyWithDelivery"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Com entrega</span>
                </label>

                @if($this->hasActiveFilters())
                    <button wire:click="clearFilters" 
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Limpar filtros
                    </button>
                @endif
            </div>
        </div>

        <!-- Results Info -->
        <div class="mb-6">
            <p class="text-sm text-gray-600">
                Mostrando {{ $pharmacies->firstItem() ?? 0 }}-{{ $pharmacies->lastItem() ?? 0 }} 
                de {{ $pharmacies->total() }} farmácias
                @if($userLatitude && $userLongitude)
                    <span class="text-green-600 font-medium">• Localização ativa</span>
                @endif
            </p>
        </div>

        <!-- Pharmacies Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($pharmacies as $pharmacy)
                @livewire('pharmacy-card', [
                    'pharmacy' => $pharmacy,
                    'userLatitude' => $userLatitude,
                    'userLongitude' => $userLongitude
                ], key($pharmacy->id))
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma farmácia encontrada</h3>
                    <p class="mt-1 text-sm text-gray-500">Tente ajustar os filtros de busca.</p>
                    @if(!$userLatitude || !$userLongitude)
                        <div class="mt-4">
                            <button onclick="getCurrentLocation()" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                Usar Minha Localização
                            </button>
                        </div>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $pharmacies->links() }}
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Carregando...
        </div>
    </div>
</div>

<script>
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                
                // Tentar obter endereço usando geocoding reverso (se disponível)
                let address = '';
                
                @this.call('updateUserLocation', {
                    latitude: latitude,
                    longitude: longitude,
                    address: address
                });
                
                // Mostrar mensagem de sucesso
                console.log('Localização obtida com sucesso!');
            },
            function(error) {
                let errorMessage = 'Erro ao obter localização: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Permissão negada pelo usuário.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Localização indisponível.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Tempo limite excedido.';
                        break;
                    default:
                        errorMessage += 'Erro desconhecido.';
                        break;
                }
                
                @this.call('setLocationError', errorMessage);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000
            }
        );
    } else {
        @this.call('setLocationError', 'Geolocalização não é suportada por este navegador.');
    }
}
</script>
