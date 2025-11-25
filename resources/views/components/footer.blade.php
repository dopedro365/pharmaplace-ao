<footer class="bg-gray-800 text-white py-8 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Sobre a Empresa -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-teal-400">RammesPharm</h3>
                <p class="text-sm text-gray-400 mb-4">
                    Sua saúde ao seu alcance. Encontre medicamentos e produtos farmacêuticos com facilidade e segurança em Angola.
                </p>
                <div class="flex items-center text-sm text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    Luanda, Angola
                </div>
            </div>

            <!-- Links Rápidos -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-teal-400">Links Rápidos</h3>
                <ul class="space-y-2 text-sm">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('explore') }}" class="text-gray-400 hover:text-white transition-colors">
                            Explorar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pharmacy.location') }}" class="text-gray-400 hover:text-white transition-colors">
                            Farmácias
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            Sobre Nós
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            Contato
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Atendimento -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-teal-400">Atendimento</h3>
                <ul class="space-y-2 text-sm">
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            Central de Ajuda
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            Política de Privacidade
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            Termos de Uso
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            FAQ
                        </a>
                    </li>
                </ul>
                <div class="mt-4">
                    <p class="text-sm text-gray-400 mb-1">Horário de Atendimento:</p>
                    <p class="text-xs text-gray-500">Segunda a Sexta: 8h às 18h</p>
                    <p class="text-xs text-gray-500">Sábado: 8h às 14h</p>
                </div>
            </div>

            <!-- Redes Sociais e Newsletter -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-teal-400">Conecte-se</h3>
                <div class="flex space-x-4 mb-6">
                    <a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>

                <!-- Newsletter -->
                <div>
                    <h4 class="text-sm font-semibold mb-2 text-gray-300">Newsletter</h4>
                    <p class="text-xs text-gray-400 mb-3">Receba ofertas e novidades</p>
                    <div class="flex">
                        <input 
                            type="email" 
                            placeholder="Seu e-mail" 
                            class="flex-1 px-3 py-2 bg-gray-700 text-white text-sm rounded-l-lg border border-gray-600 focus:outline-none focus:border-teal-400"
                        >
                        <button class="bg-teal-600 text-white px-4 py-2 text-sm rounded-r-lg hover:bg-teal-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m22 2-7 20-4-9-9-4Z"/>
                                <path d="M22 2 11 13"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Linha de Separação -->
        <div class="border-t border-gray-700 mt-8 pt-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left text-sm text-gray-400 mb-4 md:mb-0">
                    &copy; {{ date('Y') }} RammesPharm. Todos os direitos reservados.
                </div>
                <div class="flex items-center space-x-4 text-xs text-gray-500">
                    <span>Desenvolvido com</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="text-red-500">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span>em Angola</span>
                </div>
            </div>
        </div>
    </div>
</footer>
