<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iris Aerospace</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
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

<body class="font-sans antialiased obsidian-bg text-[var(--text-primary)]" style="color: var(--text-primary)">
    <div class="min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">

        <!-- Background grid -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
            style="background-image: linear-gradient(var(--text-primary) 1px, transparent 1px), linear-gradient(90deg, var(--text-primary) 1px, transparent 1px); background-size: 60px 60px;">
        </div>

        <!-- Atmospheric Glows -->
        <div
            class="absolute top-1/4 left-1/4 w-[500px] h-[500px] bg-[var(--neon-cyan)]/5 rounded-full blur-[120px] pointer-events-none">
        </div>
        <div
            class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-[var(--neon-emerald)]/5 rounded-full blur-[120px] pointer-events-none">
        </div>

        <div class="relative z-10 w-full max-w-4xl flex flex-col items-center text-center space-y-12 animate-tech">
            <div class="relative group">
                <div
                    class="relative w-32 h-32 md:w-40 md:h-40 rounded-3xl flex items-center justify-center p-6 shadow-2xl backdrop-blur-xl">
                    <script>
                        document.write('<img src="' + (localStorage.getItem('theme') === 'light' ? '{{ asset('assets/logo_iris_black.png') }}' : '{{ asset('assets/logo_iris.png') }}') + '" alt="Iris Aerospace" class="w-full">');
                    </script>
                    <noscript>
                        <img src="{{ asset('assets/logo_iris.png') }}" alt="Iris Aerospace" class="w-full">
                    </noscript>
                </div>
            </div>

            <div class="space-y-4">
                <h1 class="text-4xl md:text-6xl font-black uppercase tracking-tighter text-gradient-cyan">
                    Bienvenido a Iris Aerospace
                </h1>
                <p
                    class="font-mono-tech text-xs md:text-sm text-[var(--text-secondary)] uppercase tracking-[0.4em] max-w-2xl mx-auto leading-relaxed opacity-80">
                    Vive con Iris una experiencia de tránsito interplanetario diseñada para el confort absoluto en la
                    inmensidad del espacio.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-6 w-full max-w-md">
                <a href="{{ route('login') }}"
                    class="w-full py-4 bg-[var(--neon-cyan)] hover:bg-[var(--neon-cyan)]/90 text-black font-black text-xs uppercase tracking-[0.3em] rounded-xl transition-all duration-300 hover:scale-[1.03] active:scale-[0.98] shadow-[0_0_20px_rgba(14,165,233,0.3)] hover:shadow-[0_0_40px_rgba(14,165,233,0.6)] border border-[var(--neon-cyan)] flex justify-center items-center gap-3 group">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Iniciar Sesión
                </a>

                <a href="https://iris.com" target="_blank"
                    class="w-full py-4 bg-transparent hover:bg-white/5 text-[var(--text-primary)] font-bold text-xs uppercase tracking-[0.3em] rounded-xl transition-all duration-300 hover:scale-[1.03] active:scale-[0.98] border border-[var(--border-glass)] hover:border-[var(--neon-cyan)]/40 hover:shadow-[0_0_30px_rgba(14,165,233,0.2)] flex justify-center items-center gap-3 group">
                    <svg class="w-4 h-4 text-[var(--text-secondary)] group-hover:text-[var(--neon-cyan)] transition-colors"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    Visita Iris
                </a>
            </div>

            <div class="pt-12 border-t border-[var(--border-glass)] w-full max-w-lg">
                <div
                    class="flex justify-between items-center font-mono-tech text-[10px] text-[var(--text-secondary)] uppercase tracking-widest opacity-40">
                    <span>ESTADO: OPERANDO</span>
                    <span class="w-1.5 h-1.5 bg-[var(--neon-emerald)] rounded-full animate-pulse"></span>
                    <span>SISTEMA: PRIVADO</span>
                </div>
            </div>
        </div>
        <div class="absolute top-0 left-0 w-32 h-32 border-t border-l border-[var(--border-glass)] opacity-20"></div>
        <div class="absolute top-0 right-0 w-32 h-32 border-t border-r border-[var(--border-glass)] opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 border-b border-l border-[var(--border-glass)] opacity-20"></div>
        <div class="absolute bottom-0 right-0 w-32 h-32 border-b border-r border-[var(--border-glass)] opacity-20">
        </div>
    </div>
</body>

</html>