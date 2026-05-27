<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Establecer Contraseña — Iris Aerospace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script>
        function applyTheme() {
            const theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'dark') {
                document.documentElement.classList.remove('light-mode');
                document.documentElement.style.backgroundColor = '#0a0a0f';
            } else {
                document.documentElement.classList.add('light-mode');
                document.documentElement.style.backgroundColor = '#f4f4f7';
            }
        }
        applyTheme();
        document.addEventListener('livewire:navigated', applyTheme);
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased obsidian-bg" style="color: var(--text-primary)">
    <div class="min-h-screen flex items-center justify-center p-4">
        <!-- Background grid -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(var(--text-primary) 1px, transparent 1px), linear-gradient(90deg, var(--text-primary) 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="relative w-full max-w-md">
            <!-- Glow effect -->
            <div class="absolute -inset-1 bg-gradient-to-r from-emerald-600/20 via-cyan-600/20 to-emerald-600/20 rounded-2xl blur-xl"></div>

            <div class="tech-card relative p-8 rounded-2xl" style="border-color: rgba(16, 185, 129, 0.2);">
                <!-- Top accent bar -->
                <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-emerald-500 to-transparent rounded-t-2xl"></div>

                <!-- Logo & heading -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-xl font-black uppercase tracking-[0.2em] text-emerald-400 mb-1">Activar Acceso</h1>
                    <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest">Iris Aerospace — Protocolo de Seguridad</p>
                    <p class="text-xs text-zinc-400 mt-3 leading-relaxed">
                        Has recibido una contraseña temporal. Establece tu contraseña personal para continuar.
                    </p>
                </div>

                @if (session()->has('message'))
                    <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">
                        {{ session('message') }}
                    </div>
                @endif

                {{ $slot }}

                <div class="mt-6 text-center">
                    <p class="font-mono-tech text-[9px] text-zinc-600 uppercase">
                        Iris Aerospace · Acceso Seguro
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
