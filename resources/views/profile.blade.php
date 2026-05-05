<x-app-layout>
    <div class="p-6 md:p-10 max-w-7xl mx-auto animate-tech">
        <header class="mb-10">
            <div class="flex items-center gap-4 mb-4">
                <div>
                    <h1 class="text-2xl font-black tech-text-primary uppercase tracking-[0.2em]">Tu Perfil</h1>
                    <p class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest mt-1">Ajustes de
                        Usuario</p>
                </div>
            </div>
            <div class="h-px w-full bg-gradient-to-r from-cyan-500/50 via-transparent to-transparent"></div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-7 space-y-10">
                <livewire:profile.update-profile-information-form />

                <livewire:profile.update-password-form />
            </div>

            <div class="lg:col-span-5 space-y-10">
                <div class="tech-card p-8 border-rose-500/10">
                    <livewire:profile.delete-user-form />
                </div>

                <div class="p-8 border border-[var(--border-glass)] rounded-3xl bg-[var(--tech-card-bg)]">
                    <h3
                        class="font-mono-tech text-[10px] tech-text-primary uppercase tracking-widest mb-4 border-b border-white/5 pb-4">
                        Protocolo de Identidad</h3>
                    <p class="font-mono-tech text-[9px] tech-text-secondary leading-relaxed uppercase">
                        La modificación de datos en este perfil quedará registrada en el log de auditoría
                        central.<br><br>
                        ID_SESSION: {{ substr(session()->getId(), 0, 12) }}<br>
                        IP_ADDR: {{ request()->ip() }}<br>
                        STATUS: SECURE_AUTH
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>