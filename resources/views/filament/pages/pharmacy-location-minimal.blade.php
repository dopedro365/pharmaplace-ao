<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Debug: Vamos verificar todas as variáveis --}}
        @if(config('app.debug'))
            <div class="p-4 bg-yellow-100 border border-yellow-400 rounded">
                <h4 class="font-bold text-yellow-800">Debug Info:</h4>
                <pre class="text-xs text-yellow-700 mt-2">
Latitude type: {{ gettype($this->latitude ?? 'null') }}
Longitude type: {{ gettype($this->longitude ?? 'null') }}
Latitude value: {{ is_array($this->latitude ?? null) ? 'ARRAY' : ($this->latitude ?? 'NULL') }}
Longitude value: {{ is_array($this->longitude ?? null) ? 'ARRAY' : ($this->longitude ?? 'NULL') }}
                </pre>
            </div>
        @endif

        <form wire:submit="save">
            {{ $this->form }}
            
            <div class="mt-6 flex justify-end">
                @foreach($this->getFormActions() as $action)
                    {{ $action }}
                @endforeach
            </div>
        </form>

        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                Coordenadas Atuais
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Latitude:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">
                        @if(isset($this->latitude) && !is_array($this->latitude))
                            {{ $this->latitude }}
                        @else
                            Não definida
                        @endif
                    </span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Longitude:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">
                        @if(isset($this->longitude) && !is_array($this->longitude))
                            {{ $this->longitude }}
                        @else
                            Não definida
                        @endif
                    </span>
                </div>
            </div>
            
            @if(isset($this->latitude) && isset($this->longitude) && !is_array($this->latitude) && !is_array($this->longitude))
                <div class="mt-3">
                    <a 
                        href="https://www.google.com/maps?q={{ $this->latitude }},{{ $this->longitude }}" 
                        target="_blank"
                        class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ver no Google Maps
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
