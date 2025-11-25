<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - RammesPharm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Eliminando scroll da página principal */
        html, body {
            overflow: hidden;
            height: 100vh;
        }
        
        .animated-bg {
            background: linear-gradient(-45deg, #0d9488, #f97316, #0891b2, #7c3aed);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .floating-pills {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        .pill {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .step-indicator {
            transition: all 0.3s ease;
        }
        
        .step-indicator.active {
            background: #0d9488;
            color: white;
        }
        
        .step-indicator.completed {
            background: #10b981;
            color: white;
        }
        
        .typing-cursor {
            display: inline-block;
            background-color: currentColor;
            width: 3px;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        /* Scrollbar customizado estilizado */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #0d9488, #14b8a6);
            border-radius: 10px;
            border: 2px solid #f1f5f9;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #0f766e, #0d9488);
        }

        /* Layout sem scroll duplo */
        .main-container {
            height: 100vh;
            overflow: hidden;
        }
        
        .form-container {
            height: 100vh;
            overflow-y: auto;
        }
        .card-form{
            margin-top: 18rem;
        }
        /* Responsivo - em telas menores, permitir scroll normal */
        @media (max-width: 1023px) {
            html, body {
                overflow: auto;
                height: auto;
            }
            
            .main-container {
                height: auto;
                overflow: visible;
            }
            
            .form-container {
                height: auto;
                overflow: visible;
            }
            .card-form{
                margin-top: 5px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Container principal sem scroll -->
    <div class="main-container flex">
        <!-- Left Side - Animated Background fixo -->
        <div class="hidden lg:flex lg:w-1/2 animated-bg relative items-center justify-center">
            <div class="floating-pills">
                <!-- Pílulas flutuantes serão criadas via JavaScript -->
            </div>
            
            <div class="text-center text-white z-10">
                <div class="mb-8">
                    <i class="fas fa-pills text-6xl mb-4"></i>
                    <h1 class="text-5xl font-bold mb-4">
                        <span id="typewriter-text"></span><span class="typing-cursor">&nbsp;</span>
                    </h1>
                    <p class="text-2xl opacity-90 mb-6">Conectando saúde e tecnologia</p>
                </div>
                
                <div class="max-w-lg mx-auto">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8">
                        <h3 class="text-xl font-semibold mb-6">Junte-se à nossa plataforma</h3>
                        <div class="space-y-4 text-base">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-4 text-lg"></i>
                                <span>Acesso a milhares de medicamentos</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-4 text-lg"></i>
                                <span>Plataforma segura e confiável</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-4 text-lg"></i>
                                <span>Conecte-se com farmácias locais</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-4 text-lg"></i>
                                <span>Suporte especializado 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side com scroll customizado apenas no formulário -->
        <div class="w-full lg:w-1/2 form-container custom-scrollbar flex items-start justify-center p-8">
            <div class="max-w-md w-full m-sm-0 card-form" >
                <!-- Logo sempre visível no topo com margem adequada -->
                <div class="text-center mb-8 mt-4">
                    <i class="fas fa-pills text-4xl text-teal-600 mb-2"></i>
                    <h1 class="text-2xl font-bold text-gray-900">RammesPharm</h1>
                    <p class="text-sm text-gray-600">Conectando saúde e tecnologia</p>
                </div>

                <!-- Indicador de passos -->
                <div class="mb-8">
                    <div class="flex items-center justify-center space-x-4 mb-6">
                        <div class="step-indicator active w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">1</div>
                        <div class="w-12 h-0.5 bg-gray-300"></div>
                        <div class="step-indicator w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-sm font-medium">2</div>
                    </div>
                    <h2 class="text-2xl font-bold text-center text-gray-900 mb-2">Criar Conta</h2>
                    <p class="text-center text-gray-600">Escolha o tipo de conta que deseja criar</p>
                </div>

                <!-- Seleção de tipo de usuário primeiro -->
                <div id="user-type-selection" class="space-y-6">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Que tipo de conta você quer criar?</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <button type="button" onclick="selectUserType('customer')" class="user-type-btn w-full p-6 border-2 border-gray-200 rounded-xl hover:border-teal-500 hover:bg-teal-50 transition-all duration-200 text-left">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-user text-teal-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Cliente</h4>
                                    <p class="text-sm text-gray-500">Comprar medicamentos de farmácias parceiras</p>
                                    <p class="text-xs text-teal-600 mt-1">Acesso a mais de 200 farmácias</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                            </div>
                        </button>
                        
                        <button type="button" onclick="selectUserType('pharmacy')" class="user-type-btn w-full p-6 border-2 border-gray-200 rounded-xl hover:border-teal-500 hover:bg-teal-50 transition-all duration-200 text-left">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-store text-orange-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Farmácia</h4>
                                    <p class="text-sm text-gray-500">Vender medicamentos para milhares de clientes</p>
                                    <p class="text-xs text-orange-600 mt-1">* Sujeito a aprovação administrativa</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                            </div>
                        </button>
                    </div>
                    
                    <div class="text-center mt-8">
                        <p class="text-sm text-gray-500">Já tem uma conta? 
                            <a href="{{ route('login') }}" class="text-teal-600 hover:text-teal-500 font-medium">Fazer login</a>
                        </p>
                    </div>
                </div>

                <!-- Formulário de registro (inicialmente oculto) -->
                <div id="registration-form" class="hidden">
                    <form method="POST" action="{{ route('register.process') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" id="selected-role" name="role" value="">

                        <!-- Basic User Information -->
                        <div class="space-y-4">
                            <div id="customer-name-field">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nome Completo <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name') }}"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                    placeholder="Digite seu nome completo"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        value="{{ old('email') }}"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                        placeholder="seu@email.com"
                                        required
                                    >
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Telefone <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="phone" 
                                        name="phone" 
                                        value="{{ old('phone') }}"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                        placeholder="9xxxxxxxx"
                                        required
                                    >
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Senha <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                        placeholder="Mínimo 8 caracteres"
                                        required
                                    >
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Confirmar Senha <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="password" 
                                        id="password_confirmation" 
                                        name="password_confirmation" 
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                        placeholder="Confirme sua senha"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Campos de farmácia otimizados -->
                        <div id="pharmacy-fields" class="hidden space-y-4">
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Farmácia</h3>
                                
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="pharmacy_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                Nome da Farmácia <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="pharmacy_name" 
                                                name="pharmacy_name" 
                                                value="{{ old('pharmacy_name') }}"
                                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('pharmacy_name') border-red-500 @enderror"
                                                placeholder="Nome da sua farmácia"
                                            >
                                            @error('pharmacy_name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="nif" class="block text-sm font-medium text-gray-700 mb-2">
                                                NIF <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="nif" 
                                                name="nif" 
                                                value="{{ old('nif') }}"
                                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 @error('nif') border-red-500 @enderror"
                                                placeholder="Número de Identificação Fiscal"
                                            >
                                            @error('nif')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                            Endereço <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            id="address" 
                                            name="address" 
                                            value="{{ old('address') }}"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                            placeholder="Endereço completo da farmácia"
                                        >
                                        @error('address')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="municipality" class="block text-sm font-medium text-gray-700 mb-2">
                                                Município <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="municipality" 
                                                name="municipality" 
                                                value="{{ old('municipality') }}"
                                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 @error('municipality') border-red-500 @enderror"
                                                placeholder="Município"
                                            >
                                            @error('municipality')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="province" class="block text-sm font-medium text-gray-700 mb-2">
                                                Província <span class="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                id="province" 
                                                name="province" 
                                                value="{{ old('province') }}"
                                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 @error('province') border-red-500 @enderror"
                                                placeholder="Província"
                                            >
                                            @error('province')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                                            WhatsApp
                                        </label>
                                        <input 
                                            type="text" 
                                            id="whatsapp" 
                                            name="whatsapp" 
                                            value="{{ old('whatsapp') }}"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 @error('whatsapp') border-red-500 @enderror"
                                            placeholder="Número do WhatsApp"
                                        >
                                        @error('whatsapp')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Documents Upload -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Documentos <span class="text-red-500">*</span></h3>
                                
                                <div id="documents-container">
                                    <div class="document-upload-item border border-gray-300 rounded-lg p-4 mb-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                                                <select name="document_types[]" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" required>
                                                    <option value="license">Licença</option>
                                                    <option value="certificate">Certificado</option>
                                                    <option value="other">Outro</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Arquivo</label>
                                                <input 
                                                    type="file" 
                                                    name="documents[]" 
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                                    required
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button 
                                    type="button" 
                                    onclick="addDocumentField()" 
                                    class="inline-flex items-center px-4 py-2 border border-teal-600 text-teal-600 rounded-lg hover:bg-teal-50 transition duration-200"
                                >
                                    <i class="fas fa-plus mr-2"></i>
                                    Adicionar Documento
                                </button>

                                @error('documents.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Seção de termos e políticas -->
                        <div class="border-t pt-6">
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <input 
                                        type="checkbox" 
                                        id="accept_terms" 
                                        name="accept_terms" 
                                        value="1"
                                        class="mt-1 h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded @error('accept_terms') border-red-500 @enderror"
                                        required
                                    >
                                    <label for="accept_terms" class="ml-3 text-sm text-gray-700">
                                        Eu li e aceito os 
                                        <button type="button" onclick="showLegalDocument('terms')" class="text-teal-600 hover:text-teal-500 underline">
                                            Termos de Uso
                                        </button>
                                        <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                @error('accept_terms')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <div class="flex items-start">
                                    <input 
                                        type="checkbox" 
                                        id="accept_privacy" 
                                        name="accept_privacy" 
                                        value="1"
                                        class="mt-1 h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded @error('accept_privacy') border-red-500 @enderror"
                                        required
                                    >
                                    <label for="accept_privacy" class="ml-3 text-sm text-gray-700">
                                        Eu li e aceito a 
                                        <button type="button" onclick="showLegalDocument('privacy')" class="text-teal-600 hover:text-teal-500 underline">
                                            Política de Privacidade
                                        </button>
                                        <span class="text-red-500">*</span>
                                    </label>
                                </div>
                                @error('accept_privacy')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Error Messages -->
                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex">
                                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                                    <div>
                                        <h3 class="text-sm font-medium text-red-800">Corrija os seguintes erros:</h3>
                                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="flex space-x-4">
                            <button 
                                type="button" 
                                onclick="goBackToUserSelection()"
                                class="flex-1 py-3 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200"
                            >
                                <i class="fas fa-arrow-left mr-2"></i>
                                Voltar
                            </button>
                            <button 
                                type="submit" 
                                id="submit-btn"
                                class="flex-1 py-3 px-4 bg-teal-600 text-white rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-200"
                            >
                                <i class="fas fa-user-plus mr-2"></i>
                                Criar Conta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para documentos legais -->
    <div id="legal-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b">
                <div>
                    <h3 id="legal-modal-title" class="text-lg font-semibold text-gray-900">Carregando...</h3>
                    <p class="text-sm text-gray-500">
                        Versão <span id="legal-modal-version">-</span> • 
                        Vigente desde <span id="legal-modal-date">-</span>
                    </p>
                </div>
                <button onclick="closeLegalModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh] custom-scrollbar">
                <div id="legal-modal-content" class="prose max-w-none">
                    <div class="flex items-center justify-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end p-6 border-t bg-gray-50">
                <button onclick="closeLegalModal()" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition duration-200">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <script>
        function showLegalDocument(type) {
            const modal = document.getElementById('legal-modal');
            const title = document.getElementById('legal-modal-title');
            const content = document.getElementById('legal-modal-content');
            const version = document.getElementById('legal-modal-version');
            const date = document.getElementById('legal-modal-date');
            
            title.textContent = 'Carregando...';
            content.innerHTML = '<div class="flex items-center justify-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
            version.textContent = '-';
            date.textContent = '-';
            
            modal.classList.remove('hidden');
            
            const documentType = type === 'terms' ? 'terms_of_use' : 'privacy_policy';
            
            fetch(`/api/legal-documents/${documentType}`)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.title && data.content) {
                        title.textContent = data.title;
                        content.innerHTML = data.content.replace(/\n/g, '<br>');
                        version.textContent = data.version || '1.0';
                        date.textContent = data.created_at ? new Date(data.created_at).toLocaleDateString('pt-AO') : 'N/A';
                    } else {
                        throw new Error('Dados do documento inválidos');
                    }
                })
                .catch(error => {
                    title.textContent = 'Documento Indisponível';
                    content.innerHTML = `
                        <div class="text-center py-8 text-amber-600">
                            <i class="fas fa-exclamation-triangle text-3xl mb-4"></i>
                            <p class="text-lg font-medium mb-2">Erro: ${error.message}</p>
                            <p class="text-sm text-gray-600">Tente novamente mais tarde</p>
                        </div>
                    `;
                    version.textContent = '-';
                    date.textContent = '-';
                });
        }

        function closeLegalModal() {
            document.getElementById('legal-modal').classList.add('hidden');
        }

        function selectUserType(type) {
            document.getElementById('selected-role').value = type;
            document.getElementById('user-type-selection').classList.add('hidden');
            document.getElementById('registration-form').classList.remove('hidden');
            
            const formContainer = document.querySelector('.form-container');
            formContainer.scrollTo(0, 0);
            
            document.querySelectorAll('.step-indicator')[0].classList.remove('active');
            document.querySelectorAll('.step-indicator')[0].classList.add('completed');
            document.querySelectorAll('.step-indicator')[1].classList.add('active');
            
            const nameField = document.getElementById('name');
            const pharmacyFields = document.getElementById('pharmacy-fields');
            const customerNameField = document.getElementById('customer-name-field');
            
            if (type === 'pharmacy') {
                pharmacyFields.classList.remove('hidden');
                customerNameField.classList.add('hidden');
                nameField.removeAttribute('required');
                
                // Adicionar required aos campos de farmácia
                document.getElementById('pharmacy_name').setAttribute('required', 'required');
                document.getElementById('nif').setAttribute('required', 'required');
                document.getElementById('address').setAttribute('required', 'required');
                document.getElementById('municipality').setAttribute('required', 'required');
                document.getElementById('province').setAttribute('required', 'required');
            } else {
                pharmacyFields.classList.add('hidden');
                customerNameField.classList.remove('hidden');
                nameField.setAttribute('required', 'required');
                
                // Remover required dos campos de farmácia
                document.getElementById('pharmacy_name').removeAttribute('required');
                document.getElementById('nif').removeAttribute('required');
                document.getElementById('address').removeAttribute('required');
                document.getElementById('municipality').removeAttribute('required');
                document.getElementById('province').removeAttribute('required');
            }
        }
        
        function goBackToUserSelection() {
            document.getElementById('user-type-selection').classList.remove('hidden');
            document.getElementById('registration-form').classList.add('hidden');
            
            const formContainer = document.querySelector('.form-container');
            formContainer.scrollTo(0, 0);
            
            document.querySelectorAll('.step-indicator')[0].classList.add('active');
            document.querySelectorAll('.step-indicator')[0].classList.remove('completed');
            document.querySelectorAll('.step-indicator')[1].classList.remove('active');
        }

        function addDocumentField() {
            const container = document.getElementById('documents-container');
            const newField = document.createElement('div');
            newField.className = 'document-upload-item border border-gray-300 rounded-lg p-4 mb-4';
            newField.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select name="document_types[]" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500" required>
                            <option value="license">Licença</option>
                            <option value="certificate">Certificado</option>
                            <option value="other">Outro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Arquivo</label>
                        <div class="flex">
                            <input 
                                type="file" 
                                name="documents[]" 
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                                required
                            >
                            <button 
                                type="button" 
                                onclick="this.closest('.document-upload-item').remove()"
                                class="px-3 py-2 bg-red-500 text-white rounded-r-lg hover:bg-red-600 transition duration-200"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(newField);
        }

        function createFloatingPills() {
            const container = document.querySelector('.floating-pills');
            const pillCount = 15;
            
            for (let i = 0; i < pillCount; i++) {
                const pill = document.createElement('div');
                pill.className = 'pill';
                
                const width = Math.random() * 30 + 20;
                const height = Math.random() * 15 + 10;
                
                pill.style.width = width + 'px';
                pill.style.height = height + 'px';
                pill.style.left = Math.random() * 100 + '%';
                pill.style.top = Math.random() * 100 + '%';
                pill.style.animationDelay = Math.random() * 6 + 's';
                pill.style.animationDuration = (Math.random() * 4 + 4) + 's';
                
                container.appendChild(pill);
            }
        }

        function startAdvancedTyping() {
            const typewriterElement = document.getElementById('typewriter-text');
            if (!typewriterElement) return;
            
            const text = 'RammesPharm';
            let currentText = '';
            let isDeleting = false;
            let charIndex = 0;
            
            function getRandomTypingSpeed() {
                return Math.random() * 400 + 500;
            }
            
            function getRandomDeletingSpeed() {
                return Math.random() * 200 + 250;
            }
            
            function type() {
                if (!isDeleting && charIndex < text.length) {
                    currentText += text.charAt(charIndex);
                    charIndex++;
                    typewriterElement.textContent = currentText;
                    setTimeout(type, getRandomTypingSpeed());
                } else if (isDeleting && charIndex > 0) {
                    currentText = currentText.slice(0, -1);
                    charIndex--;
                    typewriterElement.textContent = currentText;
                    setTimeout(type, getRandomDeletingSpeed());
                } else if (!isDeleting && charIndex === text.length) {
                    setTimeout(() => {
                        isDeleting = true;
                        type();
                    }, 8000);
                } else if (isDeleting && charIndex === 0) {
                    typewriterElement.textContent = '';
                    isDeleting = false;
                    setTimeout(() => {
                        type();
                    }, 4000);
                }
            }
            
            type();
        }

        document.addEventListener('DOMContentLoaded', function() {
            createFloatingPills();
            startAdvancedTyping();
        });
    </script>
</body>
</html>
