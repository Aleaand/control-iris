<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Iris Aerospace') }} — Autenticación</title>

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
    @vite(['resources/css/app.css', 'resources/css/obsidian-design.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased obsidian-bg" style="color: var(--text-primary)">
    <div class="min-h-screen flex items-center justify-center p-4">
        <!-- Background grid -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(var(--text-primary) 1px, transparent 1px), linear-gradient(90deg, var(--text-primary) 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="relative w-full max-w-md">
            <!-- Glow effect -->
            <div class="absolute -inset-1 bg-gradient-to-r from-[var(--neon-cyan)]/20 via-[var(--neon-emerald)]/20 to-[var(--neon-cyan)]/20 rounded-2xl blur-xl"></div>

            <div class="tech-card relative p-8 rounded-2xl" style="border-color: rgba(14, 165, 233, 0.2);">
                <!-- Top accent bar -->
                <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-[var(--neon-cyan)] to-transparent rounded-t-2xl"></div>

                <!-- Logo & heading -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <a href="/" wire:navigate class="block w-20 h-20 rounded-2xl bg-[var(--neon-cyan)]/5 border border-[var(--neon-cyan)]/20 flex items-center justify-center hover:bg-[var(--neon-cyan)]/10 transition-colors shadow-[0_0_15px_rgba(14,165,233,0.1)]">
                            <script>
                                document.write('<img src="' + (localStorage.getItem('theme') === 'light' ? '{{ asset('assets/logo_iris_black.png') }}' : '{{ asset('assets/logo_iris.png') }}') + '" alt="Iris Aerospace" class="h-10">');
                            </script>
                            <noscript>
                                <img src="{{ asset('assets/logo_iris.png') }}" alt="Iris Aerospace" class="h-10">
                            </noscript>
                        </a>
                    </div>
                    <h1 class="text-xl font-black uppercase tracking-[0.2em] text-[var(--neon-cyan)] mb-1">Terminal de Acceso</h1>
                    <p class="font-mono-tech text-[10px] text-[var(--text-secondary)] uppercase tracking-widest">Iris Aerospace — Protocolo de Identificación</p>
                </div>

                {{ $slot }}

                <div class="mt-6 text-center">
                    <p class="font-mono-tech text-[9px] text-zinc-600 uppercase tracking-widest">
                        Iris Aerospace · Red Privada
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>