<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <meta name="description" content="@yield('description', 'Sua plataforma de saúde e bem-estar.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        @livewire('header')

        <!-- Page Content -->
        <main class="flex-1">
            @yield('content')
        </main>

        @include('includes.footer')
    </div>

    @livewireScripts
    @stack('scripts')
        <script script  src = " https://cdn.lordicon.com/lordicon.js " > </script>
    
    <!-- Script para melhorar navegação -->
    <script>
        // Prevenir conflitos de navegação do Livewire
        document.addEventListener('DOMContentLoaded', function() {
            // Forçar navegação normal em links específicos
            const normalLinks = document.querySelectorAll('a[href^="/"]');
            normalLinks.forEach(link => {
                if (!link.hasAttribute('wire:navigate') && !link.closest('[wire\\:navigate]')) {
                    link.addEventListener('click', function(e) {
                        // Permitir navegação normal sem interferência do Livewire
                        if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
                            window.location.href = this.href;
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
