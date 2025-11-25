<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - RammesPharm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Lado esquerdo com branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-teal-500 via-teal-600 to-orange-500 relative overflow-hidden">
            <!-- Elementos flutuantes -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-20 w-16 h-16 bg-white/10 rounded-full animate-pulse"></div>
                <div class="absolute top-40 right-32 w-8 h-8 bg-white/20 rounded-full animate-bounce"></div>
                <div class="absolute bottom-32 left-16 w-12 h-12 bg-white/15 rounded-full animate-pulse"></div>
                <div class="absolute bottom-20 right-20 w-6 h-6 bg-white/25 rounded-full animate-bounce"></div>
            </div>

            <!-- Conteúdo principal -->
            <div class="relative z-10 flex flex-col justify-center items-center text-white p-12 text-center">
                <!-- Logo -->
                <div class="mb-8">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-pills text-3xl text-white"></i>
                    </div>
                    <h1 class="text-4xl font-bold mb-2">RammesPharm</h1>
                    <p class="text-xl text-white/90">Conectando saúde e tecnologia</p>
                </div>

                <h2 class="text-2xl font-semibold mb-8">Nova senha segura</h2>

                <!-- Lista de requisitos -->
                <div class="space-y-4 text-left">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-xl"></i>
                        <span class="text-lg">Mínimo 8 caracteres</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-shield-alt text-xl"></i>
                        <span class="text-lg">Senha forte e segura</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-key text-xl"></i>
                        <span class="text-lg">Confirmação obrigatória</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-lock text-xl"></i>
                        <span class="text-lg">Criptografia avançada</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lado direito com formulário -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Logo mobile -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-pills text-2xl text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">RammesPharm</h1>
                    <p class="text-gray-600">Conectando saúde e tecnologia</p>
                </div>

                <!-- Formulário -->
                <div class="bg-white rounded-2xl border border-gray-200 p-8">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Redefinir Senha</h2>
                        <p class="text-gray-600">Digite sua nova senha para <strong>{{ $user->name }}</strong></p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            @foreach ($errors->all() as $error)
                                <div class="flex items-center mb-1 last:mb-0">
                                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                    <span class="text-red-700">{{ $error }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.reset') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Nova Senha
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    placeholder="Digite sua nova senha"
                                    required
                                >
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Nova Senha
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    placeholder="Confirme sua nova senha"
                                    required
                                >
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            class="w-full bg-gradient-to-r from-teal-500 to-teal-600 text-white py-3 px-4 rounded-lg font-medium hover:from-teal-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all duration-200"
                        >
                            <i class="fas fa-key mr-2"></i>
                            Redefinir Senha
                        </button>
                    </form>

                    <!-- Links -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="text-teal-600 hover:text-teal-700 font-medium">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Voltar ao Login
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-6 text-sm text-gray-500">
                    © 2025 RammesPharm. Todos os direitos reservados.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
