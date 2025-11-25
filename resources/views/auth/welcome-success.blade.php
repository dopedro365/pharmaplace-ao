<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo - RammesPharm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-teal-50 to-orange-50 min-h-screen flex items-center justify-center">
    <div class="max-w-4xl w-full mx-4">
        <!-- Success Animation -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500 rounded-full mb-6 animate-pulse">
                <i class="fas fa-check text-white text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Conta Criada com Sucesso!</h1>
            <p class="text-xl text-gray-600">Bem-vindo ao RammesPharm, {{ Auth::user()->name }}!</p>
        </div>

        <!-- Welcome Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <!-- User Info -->
            <div class="flex items-center justify-center mb-8">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center">
                        <span class="text-teal-600 font-bold text-2xl">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">{{ Auth::user()->name }}</h2>
                        <p class="text-gray-600">{{ Auth::user()->email ?? Auth::user()->phone }}</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if(Auth::user()->role === 'customer') bg-blue-100 text-blue-800
                            @elseif(Auth::user()->role === 'pharmacy') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif
                        ">
                            <i class="fas fa-user mr-2"></i>
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-medium text-green-800 mb-2">Conta Ativada</h3>
                        @if(Auth::user()->role === 'customer')
                            <p class="text-green-700">
                                Sua conta de cliente foi criada e ativada automaticamente. Você já pode começar a explorar medicamentos e fazer pedidos.
                            </p>
                        @else
                            <p class="text-green-700">
                                Sua conta de farmácia foi criada com sucesso. Ela está sendo analisada pela nossa equipe e você será notificado quando for aprovada.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6 text-center">O que você gostaria de fazer agora?</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Explorar Medicamentos -->
                    <a href="{{ route('explore') }}" class="group">
                        <div class="bg-teal-50 border border-teal-200 rounded-lg p-6 text-center hover:bg-teal-100 transition-colors">
                            <div class="w-16 h-16 bg-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-pills text-white text-2xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Explorar Medicamentos</h4>
                            <p class="text-sm text-gray-600">Encontre medicamentos e compare preços entre farmácias</p>
                        </div>
                    </a>

                    <!-- Página Inicial -->
                    <a href="{{ route('home') }}" class="group">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center hover:bg-blue-100 transition-colors">
                            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-home text-white text-2xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Página Inicial</h4>
                            <p class="text-sm text-gray-600">Voltar à página principal e conhecer a plataforma</p>
                        </div>
                    </a>

                    <!-- Painel/Perfil -->
                    @if(in_array(Auth::user()->role, ['admin', 'manager', 'pharmacy']))
                        <a href="{{ route('filament.painel.pages.dashboard') }}" class="group">
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 text-center hover:bg-purple-100 transition-colors">
                                <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cog text-white text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-gray-900 mb-2">Painel Administrativo</h4>
                                <p class="text-sm text-gray-600">Gerenciar sua farmácia e produtos</p>
                            </div>
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="group">
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 text-center hover:bg-purple-100 transition-colors">
                                <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-user text-white text-2xl"></i>
                                </div>
                                <h4 class="font-semibold text-gray-900 mb-2">Meu Perfil</h4>
                                <p class="text-sm text-gray-600">Completar informações do perfil</p>
                            </div>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Additional Info -->
            @if(Auth::user()->role === 'customer')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-blue-800 mb-1">Dicas para começar</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Use a busca para encontrar medicamentos específicos</li>
                                <li>• Compare preços entre diferentes farmácias</li>
                                <li>• Adicione medicamentos ao carrinho e finalize pedidos</li>
                                <li>• Encontre farmácias próximas à sua localização</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @elseif(Auth::user()->role === 'pharmacy')
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-clock text-orange-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-orange-800 mb-1">Próximos passos</h4>
                            <ul class="text-sm text-orange-700 space-y-1">
                                <li>• Aguarde a aprovação da sua conta (1-3 dias úteis)</li>
                                <li>• Você receberá um email quando for aprovada</li>
                                <li>• Após aprovação, poderá adicionar produtos</li>
                                <li>• Configure informações de entrega e pagamento</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} RammesPharm. Todos os direitos reservados.</p>
            <p class="mt-1">Obrigado por escolher nossa plataforma!</p>
        </div>
    </div>

    <!-- Auto redirect after 10 seconds -->
    <script>
        let countdown = 10;
        const countdownElement = document.createElement('div');
        countdownElement.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm';
        countdownElement.innerHTML = `Redirecionamento automático em <span id="countdown">${countdown}</span>s`;
        document.body.appendChild(countdownElement);

        const timer = setInterval(() => {
            countdown--;
            document.getElementById('countdown').textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = '{{ route("home") }}';
            }
        }, 1000);

        // Cancel auto redirect if user interacts
        document.addEventListener('click', () => {
            clearInterval(timer);
            countdownElement.remove();
        });
    </script>
</body>
</html>
