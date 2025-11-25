<x-filament-panels::page>
    <div class="min-h-screen bg-gray-50">
        <!-- Breadcrumb -->
        <div class="bg-white border-b mb-6">
            <div class="px-6 py-4">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <a href="{{ route('filament.painel.pages.explore-page') }}" class="text-gray-500 hover:text-gray-700">Explorar</a>
                        </li>
                        <li>
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </li>
                        <li>
                            <span class="text-gray-900 font-medium">{{ $record->name }}</span>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-8">
                <!-- Product Images -->
                <div class="space-y-4">
                    <div class="aspect-square bg-white rounded-lg shadow-sm p-8 flex items-center justify-center">
                        @if($record->getFirstImage())
                            <img src="{{ $record->getFirstImage() }}" alt="{{ $record->name }}" class="max-w-full max-h-full object-contain">
                        @else
                            <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z"></path>
                            </svg>
                        @endif
                    </div>
                </div>

                <!-- Product Info -->
                <div class="space-y-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $record->name }}</h1>
                        <p class="text-lg text-gray-600">{{ $record->category->name ?? 'Medicamento' }}</p>
                        
                        <div class="flex items-center mt-4 space-x-4">
                            <div class="flex items-center">
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="ml-2 text-sm text-gray-600">4.2 (89 avaliações)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Price Comparison -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Comparação de Preços</h3>
                        
                        <div class="space-y-4">
                            @foreach($this->getPharmacyProducts() as $index => $pharmacyProduct)
                            <div class="border-2 {{ $index === 0 ? 'border-green-200 bg-green-50' : 'border-gray-200' }} rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 {{ $index === 0 ? 'bg-green-600' : 'bg-blue-600' }} rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h6m-6 4h6m-6 4h6"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $pharmacyProduct->pharmacy->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $pharmacyProduct->pharmacy->address }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold {{ $index === 0 ? 'text-green-700' : 'text-gray-900' }}">
                                            AOA {{ number_format($pharmacyProduct->price, 0) }}
                                        </div>
                                        @if($index === 0)
                                            <div class="text-sm text-green-600">Melhor preço</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">Em estoque</span>
                                        @if($pharmacyProduct->pharmacy->accepts_delivery)
                                            <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">Entrega disponível</span>
                                        @endif
                                    </div>
                                    <button 
                                        wire:click="addToCart({{ $pharmacyProduct->id }})"
                                        class="{{ $index === 0 ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                    >
                                        Adicionar ao Carrinho
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Tabs -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-8">
                        <button 
                            wire:click="setTab('description')"
                            class="border-b-2 {{ $selectedTab === 'description' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-4 px-1 text-sm font-medium"
                        >
                            Descrição
                        </button>
                        <button 
                            wire:click="setTab('technical')"
                            class="border-b-2 {{ $selectedTab === 'technical' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-4 px-1 text-sm font-medium"
                        >
                            Informações Técnicas
                        </button>
                        <button 
                            wire:click="setTab('reviews')"
                            class="border-b-2 {{ $selectedTab === 'reviews' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-4 px-1 text-sm font-medium"
                        >
                            Avaliações (89)
                        </button>
                    </nav>
                </div>
                
                <div class="p-8">
                    @if($selectedTab === 'description')
                        <div class="prose max-w-none">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sobre {{ $record->name }}</h3>
                            <p class="text-gray-700 mb-4">{{ $record->description ?: 'Descrição não disponível.' }}</p>
                            
                            @if($record->indications)
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Indicações</h4>
                            <p class="text-gray-700 mb-4">{{ $record->indications }}</p>
                            @endif
                            
                            @if($record->dosage)
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Posologia</h4>
                            <p class="text-gray-700 mb-4">{{ $record->dosage }}</p>
                            @endif
                            
                            @if($record->requires_prescription)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <div>
                                        <h5 class="font-medium text-yellow-800">Importante</h5>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            Este medicamento requer receita médica. Consulte um profissional de saúde antes do uso.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @elseif($selectedTab === 'technical')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Informações Gerais</h4>
                                <dl class="space-y-2">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600">Fabricante:</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $record->manufacturer ?: 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600">Categoria:</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $record->category->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600">Código de Barras:</dt>
                                        <dd class="text-sm font-medium text-gray-900">{{ $record->barcode ?: 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>
                            
                            @if($record->composition)
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Composição</h4>
                                <p class="text-sm text-gray-700">{{ $record->composition }}</p>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900">Avaliações dos Clientes</h3>
                            <!-- Reviews would be loaded here -->
                            <p class="text-gray-500">Avaliações em breve...</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
