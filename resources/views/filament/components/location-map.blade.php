<div class="space-y-4">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Como usar o mapa:</p>
                <ul class="list-disc list-inside space-y-1 text-blue-700">
                    <li>O mapa mostra a localização salva na base de dados</li>
                    <li>Clique no botão "📍 Usar Minha Localização" para detectar sua posição atual</li>
                    <li>Ou clique diretamente no mapa para definir uma nova posição</li>
                    <li>Use o botão "Preencher Campos" para transferir as coordenadas para o formulário</li>
                    <li>Clique em "Salvar Localização" para confirmar as alterações</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h4 class="font-medium text-gray-900">Mapa Interativo</h4>
                <button type="button" 
                        id="location-btn"
                        onclick="getCurrentLocationFromMap()" 
                        class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                    📍 Usar Minha Localização
                </button>
            </div>
        </div>
        
        <div id="pharmacy-map" style="height: 400px; width: 100%; background-color: #f3f4f6;"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
        <div class="bg-gray-50 p-3 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <strong>Latitude do Mapa:</strong> 
                    <span id="current-lat">
                        @if(isset($latitude) && !is_array($latitude))
                            {{ $latitude }}
                        @else
                            Não definida
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 p-3 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <strong>Longitude do Mapa:</strong> 
                    <span id="current-lng">
                        @if(isset($longitude) && !is_array($longitude))
                            {{ $longitude }}
                        @else
                            Não definida
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão para preencher campos do formulário -->
    <div class="text-center">
        <button type="button" 
                onclick="fillFormFields()" 
                class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Preencher Campos do Formulário
        </button>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #pharmacy-map {
        z-index: 1;
    }
    .leaflet-container {
        height: 400px !important;
        width: 100% !important;
        background-color: #f3f4f6 !important;
    }
    .leaflet-control-container {
        z-index: 1000;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map;
let marker;
let currentLat = {{ is_numeric($latitude ?? null) ? $latitude : -8.8137 }};
let currentLng = {{ is_numeric($longitude ?? null) ? $longitude : 13.2344 }};
let mapInitialized = false;

console.log('Inicializando mapa com coordenadas salvas:', currentLat, currentLng);

// Inicializar mapa quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initMap, 300);
});

function initMap() {
    try {
        if (mapInitialized) {
            console.log('Mapa já inicializado');
            return;
        }
        
        const mapContainer = document.getElementById('pharmacy-map');
        if (!mapContainer) {
            console.error('Container do mapa não encontrado');
            return;
        }

        console.log('Inicializando mapa com coordenadas salvas:', currentLat, currentLng);

        // Limpar container
        mapContainer.innerHTML = '';

        // Criar mapa com as coordenadas salvas
        map = L.map('pharmacy-map', {
            center: [currentLat, currentLng],
            zoom: 15,
            zoomControl: true,
            attributionControl: true
        });
        
        // Adicionar tiles básicos
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Adicionar marcador na posição salva
        marker = L.marker([currentLat, currentLng], {
            draggable: true,
            title: 'Localização da Farmácia (Salva na Base de Dados)'
        }).addTo(map);
        
        // Eventos básicos
        map.on('click', function(e) {
            console.log('Clique no mapa:', e.latlng);
            updateMarkerPosition(e.latlng.lat, e.latlng.lng);
        });
        
        marker.on('dragend', function(e) {
            const position = e.target.getLatLng();
            console.log('Marcador arrastado:', position);
            updateMarkerPosition(position.lat, position.lng);
        });
        
        mapInitialized = true;
        updateDisplay();
        
        console.log('Mapa inicializado com sucesso na posição salva');
        
    } catch (error) {
        console.error('Erro ao inicializar mapa:', error);
        document.getElementById('pharmacy-map').innerHTML = 
            '<div class="flex items-center justify-center h-full text-gray-500 bg-gray-100">' +
            '<div class="text-center p-4">' +
            '<p class="mb-2">Erro ao carregar mapa</p>' +
            '<p class="text-sm text-gray-400 mb-3">Use os métodos alternativos abaixo</p>' +
            '<button onclick="retryMapInit()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Tentar Novamente</button>' +
            '</div>' +
            '</div>';
    }
}

function retryMapInit() {
    mapInitialized = false;
    if (map) {
        map.remove();
        map = null;
    }
    setTimeout(initMap, 100);
}

function updateMarkerPosition(lat, lng) {
    currentLat = parseFloat(lat);
    currentLng = parseFloat(lng);
    
    console.log('Atualizando posição do marcador:', currentLat, currentLng);
    
    if (marker) {
        marker.setLatLng([lat, lng]);
        marker.setPopupContent(`Nova posição: ${currentLat.toFixed(6)}, ${currentLng.toFixed(6)}`);
    }
    
    updateDisplay();
}

function updateDisplay() {
    const latElement = document.getElementById('current-lat');
    const lngElement = document.getElementById('current-lng');
    
    if (latElement) latElement.textContent = currentLat.toFixed(6);
    if (lngElement) lngElement.textContent = currentLng.toFixed(6);
}

// Função para geolocalização - apenas atualiza o mapa
function getCurrentLocationFromMap() {
    const button = document.getElementById('location-btn');
    
    if (!navigator.geolocation) {
        alert('Geolocalização não é suportada neste navegador.');
        return;
    }
    
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Localizando...';
    button.disabled = true;

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            console.log('Localização atual detectada:', lat, lng);
            
            updateMarkerPosition(lat, lng);
            
            if (map) {
                map.setView([lat, lng], 17);
            }
            
            button.innerHTML = originalText;
            button.disabled = false;
            
            alert('Localização atual detectada no mapa! Use o botão "Preencher Campos" para transferir para o formulário.');
        },
        function(error) {
            console.error('Erro na geolocalização:', error);
            
            button.innerHTML = originalText;
            button.disabled = false;
            
            let message = 'Erro ao obter localização: ';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message += 'Permissão negada pelo usuário.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message += 'Localização indisponível.';
                    break;
                case error.TIMEOUT:
                    message += 'Tempo limite excedido.';
                    break;
                default:
                    message += 'Erro desconhecido.';
                    break;
            }
            alert(message);
        },
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 60000
        }
    );
}

// Função para preencher campos do formulário com as coordenadas do mapa
function fillFormFields() {
    const lat = currentLat.toFixed(6);
    const lng = currentLng.toFixed(6);
    
    console.log('Preenchendo campos com:', lat, lng);
    
    // Encontrar e preencher os campos do formulário
    const latInput = document.querySelector('input[wire\\:model*="latitude"]') || 
                   document.querySelector('input[name*="latitude"]') ||
                   document.querySelector('#data\\.latitude');
    const lngInput = document.querySelector('input[wire\\:model*="longitude"]') || 
                   document.querySelector('input[name*="longitude"]') ||
                   document.querySelector('#data\\.longitude');
    
    if (latInput && lngInput) {
        latInput.value = lat;
        lngInput.value = lng;
        
        // Disparar eventos para atualizar Livewire
        latInput.dispatchEvent(new Event('input', { bubbles: true }));
        lngInput.dispatchEvent(new Event('input', { bubbles: true }));
        latInput.dispatchEvent(new Event('change', { bubbles: true }));
        lngInput.dispatchEvent(new Event('change', { bubbles: true }));
        
        alert('Coordenadas preenchidas nos campos! Clique em "Salvar Localização" para confirmar.');
    } else {
        console.error('Campos do formulário não encontrados');
        alert('Erro: Não foi possível encontrar os campos do formulário.');
    }
}

// Expor funções globalmente para uso externo
window.pharmacyMap = {
    updateMarker: updateMarkerPosition,
    invalidateSize: function() {
        if (map) {
            setTimeout(() => map.invalidateSize(), 100);
        }
    }
};
</script>
@endpush
