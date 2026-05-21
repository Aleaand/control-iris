<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iris Aerospace - Control</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script>
        document.documentElement.classList.remove('light-mode');
        
        let mouseX = window.innerWidth / 2;
        let mouseY = window.innerHeight / 2;

        const layers = [
            { el: null, id: 'cloud-1-el', tx: 0, ty: 0, speed: 0.018, range: 55 },
            { el: null, id: 'cloud-2-el', tx: 0, ty: 0, speed: 0.012, range: 35 },
            { el: null, id: 'cloud-3-el', tx: 0, ty: 0, speed: 0.025, range: 70 },
            { el: null, id: 'cursor-light', tx: 0, ty: 0, speed: 0.09, range: 0 },
        ];

        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        function animateNebula() {
            const cx = window.innerWidth / 2;
            const cy = window.innerHeight / 2;
            const dx = (mouseX - cx) / cx;
            const dy = (mouseY - cy) / cy;

            layers.forEach(layer => {
                if (!layer.el) {
                    layer.el = layer.id === 'cursor-light'
                        ? document.getElementById('cursor-light')
                        : document.querySelector('.' + layer.id.replace('-el', ''));
                }
                if (!layer.el) return;

                if (layer.id === 'cursor-light') {
                    layer.tx += (mouseX - layer.tx) * layer.speed;
                    layer.ty += (mouseY - layer.ty) * layer.speed;
                    layer.el.style.left = layer.tx + 'px';
                    layer.el.style.top = layer.ty + 'px';
                } else {
                    const targetX = dx * layer.range;
                    const targetY = dy * layer.range;
                    layer.tx += (targetX - layer.tx) * layer.speed;
                    layer.ty += (targetY - layer.ty) * layer.speed;
                    layer.el.style.setProperty('--px', layer.tx + 'px');
                    layer.el.style.setProperty('--py', layer.ty + 'px');
                }
            });

            requestAnimationFrame(animateNebula);
        }

        document.addEventListener('DOMContentLoaded', animateNebula);
    </script>
    <style>
        /* Estilo crítico para evitar el destello blanco y forzar el modo oscuro en la zona pública */
        html, body { background-color: #0a0a0f !important; }
        [x-cloak] { display: none !important; }

        /* Anular variables en modo claro para que siempre renderice en modo oscuro */
        .light-mode {
            color-scheme: dark !important;
            --bg-obsidian: #0a0a0f !important;
            --bg-panel: #111119 !important;
            --text-primary: #ffffff !important;
            --text-secondary: #94a3b8 !important;
            --border-glass: rgba(255, 255, 255, 0.08) !important;
            --gradient-blue: #ffffff !important;
            --tech-card-bg: rgba(28, 28, 40, 0.6) !important;
            --tech-input-bg: rgba(255, 255, 255, 0.03) !important;
            --tech-hover-bg: rgba(255, 255, 255, 0.05) !important;
            --tech-dark-hover-bg: rgba(32, 32, 45, 0.8) !important;
            --tech-hover-bg-card: rgba(25, 25, 35, 0.9) !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased obsidian-bg text-[var(--text-primary)]" style="color: var(--text-primary); background-color: #0a0a0f;">
    <div class="min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">

        <div class="nebula-container">
            <div class="nebula-cloud cloud-1"></div>
            <div class="nebula-cloud cloud-2"></div>
            <div class="nebula-cloud cloud-3"></div>
            <div id="cursor-light"></div>
        </div>

        <div class="relative z-10 w-full max-w-4xl flex flex-col items-center text-center space-y-10 animate-tech" style="opacity: 0;">
            <div class="relative group">
                <div class="relative w-42 h-42 md:w-40 md:h-40 rounded-3xl flex items-center justify-center p-3">
                    <img src="{{ asset('assets/logo_iris.png') }}" alt="Iris Aerospace" class="w-full">
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
                    <span>ESTADO: OPERATIVO</span>
                    <span class="w-1.5 h-1.5 bg-[var(--neon-emerald)] rounded-full animate-pulse"></span>
                    <span>SISTEMA: CONTROL PRIVADO</span>
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