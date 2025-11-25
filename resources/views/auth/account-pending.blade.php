<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conta Pendente - RammesPharm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-teal-50 to-orange-50 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full mx-4">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-full mb-4">
                <i class="fas fa-clock text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">RammesPharm</h1>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-100 rounded-full mb-4">
                    <i class="fas fa-hourglass-half text-orange-500 text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Conta em Análise</h2>
                <p class="text-gray-600">Sua conta está sendo analisada pela nossa equipe</p>
            </div>

            @if($user->role === 'pharmacy')
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-orange-800 mb-3">Status da Farmácia</h3>
                    <div class="text-left space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Cadastro realizado:</span>
                            <span class="text-sm font-medium text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                Concluído
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Documentos enviados:</span>
                            <span class="text-sm font-medium text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ $user->pharmacy->documents->count() }} documento(s)
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Análise da documentação:</span>
                            <span class="text-sm font-medium text-orange-600">
                                <i class="fas fa-clock mr-1"></i>
                                Em andamento
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Aprovação final:</span>
                            <span class="text-sm font-medium text-gray-400">
                                <i class="fas fa-minus-circle mr-1"></i>
                                Aguardando
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div class="text-left">
                            <h4 class="font-medium text-blue-800 mb-1">Processo de Validação</h4>
                            <p class="text-sm text-blue-700">
                                Nossa equipe está analisando seus documentos e informações. Este processo pode levar de 1 a 3 dias úteis. 
                                Você receberá uma notificação por email assim que sua conta for aprovada.
                            </p>
                        </div>
                    </div>
                </div>

                @if($user->pharmacy->documents->count() > 0)
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-medium text-gray-800 mb-3">Documentos Enviados</h4>
                        <div class="space-y-2">
                            @foreach($user->pharmacy->documents as $document)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ ucfirst($document->type) }} - {{ $document->original_name }}</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($document->status === 'pending') bg-orange-100 text-orange-800
                                        @elseif($document->status === 'approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif
                                    ">
                                        {{ ucfirst($document->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div class="text-left">
                            <h4 class="font-medium text-blue-800 mb-1">Conta de Cliente</h4>
                            <p class="text-sm text-blue-700">
                                Sua conta de cliente está sendo processada. Normalmente este processo é automático, 
                                mas pode haver uma verificação adicional em alguns casos.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Contact Information -->
            <div class="border-t pt-6">
                <h3 class="font-semibold text-gray-800 mb-3">Precisa de Ajuda?</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Se tiver dúvidas sobre o processo de aprovação, entre em contato conosco:
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="mailto:suporte@pharmaplace.ao" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition duration-200">
                        <i class="fas fa-envelope mr-2"></i>
                        Email
                    </a>
                    <a href="tel:+244923456789" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-phone mr-2"></i>
                        Telefone
                    </a>
                </div>
            </div>

            <!-- Logout Button -->
            <div class="mt-6 pt-6 border-t">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Sair da Conta
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} RammesPharm. Todos os direitos reservados.</p>
        </div>
    </div>

    <!-- Auto-refresh every 30 seconds to check for status updates -->
    <script>
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>
