<x-app-layout>
    <div class="p-6 md:p-10 max-w-7xl mx-auto animate-tech">
        <!-- Header Técnico -->
        <header class="mb-10">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black tech-text-primary uppercase tracking-[0.2em]">Dossier de Usuario</h1>
                    <p class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest mt-1">Configuración y Protocolos de Acceso</p>
                </div>
            </div>
            <div class="h-px w-full bg-gradient-to-r from-cyan-500/50 via-transparent to-transparent"></div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Columna Izquierda: Información Principal -->
            <div class="lg:col-span-7 space-y-10">
                <livewire:profile.update-profile-information-form />
                
                <livewire:profile.update-password-form />
            </div>

            <!-- Columna Derecha: Estado y Seguridad -->
            <div class="lg:col-span-5 space-y-10">
                <div class="tech-card p-8 border-rose-500/10">
                    <livewire:profile.delete-user-form />
                </div>

                <div class="p-8 border border-[var(--border-glass)] rounded-3xl bg-[var(--tech-card-bg)]">
                    <h3 class="font-mono-tech text-[10px] tech-text-primary uppercase tracking-widest mb-4 border-b border-white/5 pb-4">Protocolo de Identidad</h3>
                    <p class="font-mono-tech text-[9px] tech-text-secondary leading-relaxed uppercase">
                        La modificación de datos en este dossier quedará registrada en el log de auditoría central de IRIS Aerospace.<br><br>
                        ID_SESSION: {{ substr(session()->getId(), 0, 12) }}<br>
                        IP_ADDR: {{ request()->ip() }}<br>
                        STATUS: SECURE_AUTH
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
