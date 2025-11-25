<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header do Pedido -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Pedido #{{ $this->record->order_number }}</h2>
                    <p class="text-gray-600 mt-1">Criado em {{ $this->record->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">AOA {{ number_format($this->record->total, 2, ',', '.') }}</div>
                    <x-filament::badge 
                        :color="match($this->record->status) {
                            'pending_payment' => 'warning',
                            'payment_verification' => 'info',
                            'confirmed' => 'success',
                            'preparing' => 'primary',
                            'ready_pickup' => 'secondary',
                            'out_delivery' => 'info',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray'
                        }"
                    >
                        {{ $this->record->status_label }}
                    </x-filament::badge>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informações do Cliente -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações do Cliente</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nome</label>
                        <p class="text-gray-900">{{ $this->record->customer_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Telefone</label>
                        <p class="text-gray-900">{{ $this->record->customer_phone }}</p>
                    </div>
                    @if($this->record->customer_email)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900">{{ $this->record->customer_email }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informações da Farmácia -->
            @if(Auth::user()->role === 'customer')
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Farmácia</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nome</label>
                        <p class="text-gray-900">{{ $this->record->pharmacy->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Telefone</label>
                        <p class="text-gray-900">{{ $this->record->pharmacy->phone }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Endereço</label>
                        <p class="text-gray-900">{{ $this->record->pharmacy->address }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Informações de Entrega -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Entrega</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tipo</label>
                        <p class="text-gray-900">{{ $this->record->delivery_type === 'delivery' ? 'Entrega' : 'Retirada' }}</p>
                    </div>
                    @if($this->record->delivery_type === 'delivery')
                    <div>
                        <label class="text-sm font-medium text-gray-500">Endereço</label>
                        <p class="text-gray-900">{{ $this->record->delivery_address }}</p>
                        <p class="text-gray-600 text-sm">{{ $this->record->delivery_municipality }}, {{ $this->record->delivery_province }}</p>
                    </div>
                    @endif
                    @if($this->record->delivery_notes)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Observações</label>
                        <p class="text-gray-900">{{ $this->record->delivery_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Itens do Pedido - COM BOTÃO DE DOWNLOAD USANDO URL DIRETA -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Itens do Pedido</h3>
                <!-- BOTÃO COM URL DIRETA - SEM LIVEWIRE -->
                <x-filament::button 
                    tag="a" 
                    :href="route('order.download.pdf', $this->record)"
                    color="primary"
                    size="sm"
                    icon="heroicon-o-document-arrow-down"
                    target="_blank"
                >
                    Baixar PDF do Pedido
                </x-filament::button>
            </div>
            
            <!-- Container com altura limitada e scroll -->
            <div class="max-h-96 overflow-y-auto border rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço Unit.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($this->record->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                @if($item->product->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($item->product->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">AOA {{ number_format($item->price, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">AOA {{ number_format($item->quantity * $item->price, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Totais - FORA do scroll -->
            <div class="mt-4 border-t pt-4">
                <div class="flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-900">Subtotal:</span>
                            <span class="text-gray-900">AOA {{ number_format($this->record->subtotal, 2, ',', '.') }}</span>
                        </div>
                        @if($this->record->delivery_fee > 0)
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-900">Taxa de Entrega:</span>
                            <span class="text-gray-900">AOA {{ number_format($this->record->delivery_fee, 2, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span class="text-gray-900">Total:</span>
                            <span class="text-gray-900">AOA {{ number_format($this->record->total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações de Pagamento - COM URL DIRETA PARA COMPROVATIVO -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pagamento</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Método de Pagamento</label>
                        <p class="text-gray-900">
                            {{ match($this->record->payment_method) {
                                'cash' => 'Dinheiro na Entrega',
                                'transfer' => 'Transferência Bancária',
                                'card' => 'Cartão de Crédito/Débito',
                                default => 'N/A'
                            } }}
                        </p>
                    </div>
                    
                    @if($this->record->bankAccount)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Conta Bancária</label>
                        <p class="text-gray-900">{{ $this->record->bankAccount->bank_name }}</p>
                        <p class="text-gray-600 text-sm">{{ $this->record->bankAccount->account_number }}</p>
                    </div>
                    @endif
                    
                    @if($this->record->payment_verified_at)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Verificado em</label>
                        <p class="text-gray-900">{{ $this->record->payment_verified_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
                
                @if($this->record->payment_proof)
                <div>
                    <label class="text-sm font-medium text-gray-500 block mb-2">Comprovativo de Pagamento</label>
                    <div class="space-y-2">
                        <!-- Preview da imagem SEM LINK -->
                        <div class="border rounded-lg p-2 bg-gray-50">
                            <img src="{{ $this->record->payment_proof_url }}" alt="Comprovativo" class="max-w-full h-32 object-contain mx-auto">
                        </div>
                        <!-- BOTÃO COM URL DIRETA - SEM LIVEWIRE -->
                        <div class="flex justify-center">
                            <x-filament::button 
                                tag="a" 
                                :href="route('order.download.proof', $this->record)"
                                color="secondary"
                                size="sm"
                                icon="heroicon-o-arrow-down-tray"
                                target="_blank"
                            >
                                Baixar Comprovativo
                            </x-filament::button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Botão Voltar -->
        <div class="flex justify-start">
            <x-filament::button 
                color="gray" 
                tag="a" 
                :href="route('filament.painel.pages.order-management-page')"
                icon="heroicon-o-arrow-left"
            >
                Voltar aos Pedidos
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
