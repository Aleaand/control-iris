<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iris Aerospace - Control</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script>
        function applyTheme() {
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.add('light-mode');
            } else {
                document.documentElement.classList.remove('light-mode');
            }
        }
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    </script>
    <style>
        html,
        body {
            background-color: #0a0a0f !important;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/css/obsidian-design.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased obsidian-bg" style="color: var(--text-primary); background-color: #0a0a0f;">
    <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
        <!-- Nebula Background -->
        <div class="nebula-container">
            <div class="nebula-cloud cloud-1"></div>
            <div class="nebula-cloud cloud-2"></div>
            <div class="nebula-cloud cloud-3"></div>
        </div>

        <div class="relative w-full max-w-md">
            <!-- Subtle accent glow -->
            <div class="absolute -inset-2 bg-[var(--neon-cyan)]/5 rounded-2xl blur-2xl"></div>

            <div class="tech-card relative p-8 rounded-2xl" style="border-color: rgba(14, 165, 233, 0.2);">
                <!-- Top accent bar -->
                <div
                    class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-[var(--neon-cyan)] to-transparent rounded-t-2xl">
                </div>

                <!-- Logo & heading -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <a href="/" wire:navigate
                            class="block w-24 h-24 flex items-center justify-center transition-transform hover:scale-110">
                            <img src="{{ asset('assets/logo_iris.png') }}" alt="Iris Aerospace" class="h-14 logo-dark">
                        </a>
                    </div>
                    <h1 class="text-2xl font-black text-white mb-1">¡Bienvenido a Iris Aerospace!</h1>
                    <p class="font-mono-tech text-[10px] text-[var(--text-secondary)] uppercase tracking-widest">Control
                        de Acceso Privado</p>
                </div>

                {{ $slot }}

                <div class="mt-6 text-center">
                    <p class="font-mono-tech text-[9px] text-zinc-600 uppercase tracking-widest">
                        Iris Aerospace · Control Privado
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>