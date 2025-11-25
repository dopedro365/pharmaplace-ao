<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RammesPharm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Adicionando estilos customizados para scrollbar e animações */
        .form-container::-webkit-scrollbar {
            width: 8px;
        }
        .form-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .form-container::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #14b8a6, #0d9488);
            border-radius: 10px;
        }
        .form-container::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #0d9488, #0f766e);
        }

        .typing-cursor::after {
            content: '|';
            animation: blink 1s infinite;
            color: #ffffff;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .floating-pill {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            animation: float 6s ease-in-out infinite;
        }

        .floating-pill:nth-child(1) {
            width: 60px;
            height: 30px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-pill:nth-child(2) {
            width: 40px;
            height: 20px;
            top: 60%;
            left: 80%;
            animation-delay: 2s;
        }

        .floating-pill:nth-child(3) {
            width: 50px;
            height: 25px;
            top: 80%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body class="min-h-screen overflow-hidden">
    <!-- Aplicando o mesmo layout dividido da página de registro -->
    <div class="flex min-h-screen">
        <!-- Left Side - Branding & Animation -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-teal-500 via-teal-600 to-orange-500 relative overflow-hidden">
            <!-- Floating Elements -->
            <div class="floating-elements">
                <div class="floating-pill"></div>
                <div class="floating-pill"></div>
                <div class="floating-pill"></div>
            </div>
            
            <!-- Content -->
            <!-- Centralizando o conteúdo do lado esquerdo -->
            <div class="relative z-10 flex flex-col justify-center items-center text-white p-12 w-full text-center">
                <!-- Logo -->
                <div class="mb-8">
                    <!-- Centralizando o ícone -->
                    <div class="flex justify-center mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full backdrop-blur-sm">
                            <i class="fas fa-pills text-white text-3xl"></i>
                        </div>
                    </div>
                    <h1 class="text-5xl font-bold mb-4 typing-cursor" id="typing-text">RammesPharm</h1>
                    <p class="text-xl text-white/90 font-medium">Conectando saúde e tecnologia</p>
                </div>

                <!-- Features -->
                <div class="space-y-6 text-lg">
                    <h2 class="text-2xl font-bold mb-8">Bem-vindo de volta!</h2>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <span>Acesso rápido e seguro</span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <span>Gerencie sua farmácia online</span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <span>Conecte-se com clientes</span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                        <span>Suporte especializado 24/7</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50 form-container overflow-y-auto">
            <div class="w-full max-w-md">
                <!-- Adicionando logo e nome no topo do formulário -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-teal-600 rounded-full mb-4">
                        <i class="fas fa-pills text-white text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">RammesPharm</h1>
                    <p class="text-gray-600 mt-2">Conectando saúde e tecnologia</p>
                </div>

                <!-- Login Form -->
                <!-- Removendo shadow do card de login -->
                <div class="bg-white rounded-2xl p-8 border border-gray-200">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900">Entrar na sua conta</h2>
                        <p class="text-gray-600 mt-2">Digite suas credenciais para acessar</p>
                    </div>

                    <form method="POST" action="{{ route('login.process') }}" class="space-y-6">
                        @csrf

                        <!-- Login Field (Email ou Telefone) -->
                        <div>
                            <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                                Email ou Telefone
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    id="login" 
                                    name="login" 
                                    value="{{ old('login') }}"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('login') border-red-500 @enderror"
                                    placeholder="Digite seu email ou telefone"
                                    required
                                >
                            </div>
                            @error('login')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Senha
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                    placeholder="Digite sua senha"
                                    required
                                >
                                <button 
                                    type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    onclick="togglePassword()"
                                >
                                    <i id="password-icon" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input 
                                    id="remember" 
                                    name="remember" 
                                    type="checkbox" 
                                    class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                                >
                                <label for="remember" class="ml-2 block text-sm text-gray-700">
                                    Lembrar-me
                                </label>
                            </div>
                            <!-- Adicionando link para recuperar senha -->
                            <a href="{{ route('password.request') }}" class="text-sm text-teal-600 hover:text-teal-500">
                                Esqueceu a senha?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-200"
                        >
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Entrar
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">Não tem conta?</span>
                            </div>
                        </div>
                    </div>

                    <!-- Register Link -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('register') }}" class="text-teal-600 hover:text-teal-500 font-medium">
                            Criar nova conta
                        </a>
                    </div>
                </div>

                <!-- Adicionando link para voltar ao início -->
                <div class="text-center mt-6">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-teal-600 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Voltar ao Início
                    </a>
                </div>

                <!-- Footer -->
                <div class="text-center mt-8 text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} RammesPharm. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Auto-detect input type and show appropriate placeholder
        document.getElementById('login').addEventListener('input', function(e) {
            const value = e.target.value;
            const isEmail = value.includes('@');
            
            if (isEmail) {
                e.target.placeholder = 'Digite seu email';
            } else {
                e.target.placeholder = 'Digite seu telefone (ex: 923456789)';
            }
        });

        // Typing Animation
        function startTypingAnimation() {
            const text = 'RammesPharm';
            const element = document.getElementById('typing-text');
            let currentText = '';
            let isDeleting = false;
            let charIndex = 0;

            function type() {
                if (!isDeleting && charIndex < text.length) {
                    // Typing
                    currentText += text.charAt(charIndex);
                    element.textContent = currentText;
                    charIndex++;
                    setTimeout(type, Math.random() * 200 + 400); // 400-600ms random
                } else if (isDeleting && charIndex > 0) {
                    // Deleting
                    currentText = currentText.slice(0, -1);
                    element.textContent = currentText;
                    charIndex--;
                    setTimeout(type, Math.random() * 150 + 250); // 250-400ms random
                } else if (!isDeleting && charIndex === text.length) {
                    // Pause after typing
                    setTimeout(() => {
                        isDeleting = true;
                        type();
                    }, 4000); // 4 second pause
                } else if (isDeleting && charIndex === 0) {
                    // Pause after deleting, then restart
                    setTimeout(() => {
                        isDeleting = false;
                        type();
                    }, 2000); // 2 second pause
                }
            }

            type();
        }

        // Start animation when page loads
        document.addEventListener('DOMContentLoaded', startTypingAnimation);
    </script>
</body>
</html>
