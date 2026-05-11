<div class="p-6 md:p-10 space-y-10 animate-tech">
    <header>
        <h1 class="text-2xl font-black tech-text-primary uppercase tracking-[0.2em]">Configuración de Identidad</h1>
        <p class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest mt-1">Gestión de credenciales y datos biográficos</p>
        <div class="h-px w-full bg-gradient-to-r from-cyan-500/50 via-transparent to-transparent mt-4"></div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {{-- Profile Info --}}
        <div class="lg:col-span-7 space-y-10">
            <div class="tech-card p-8 border-white/5 bg-black/40 backdrop-blur-xl">
                <div class="flex items-center gap-4 mb-8 border-b border-white/5 pb-6">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 flex items-center justify-center text-cyan-400 border border-cyan-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Información del Perfil</h3>
                        <p class="text-[9px] font-mono-tech text-zinc-500 uppercase">Actualiza tu nombre de enlace y correo de despacho</p>
                    </div>
                </div>

                <form wire:submit="updateProfile" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-mono-tech text-zinc-600 uppercase tracking-widest">Nombre Operativo</label>
                            <input type="text" wire:model="name" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-xs font-bold text-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all">
                            @error('name') <span class="text-[9px] text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[9px] font-mono-tech text-zinc-600 uppercase tracking-widest">Correo Electrónico</label>
                            <input type="email" wire:model="email" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-xs font-bold text-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all">
                            @error('email') <span class="text-[9px] text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="px-8 py-3 bg-cyan-600 hover:bg-cyan-500 text-black font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-cyan-600/20">
                            Actualizar Datos
                        </button>
                        <x-action-message on="profile-updated">
                            <span class="text-[9px] font-mono-tech text-emerald-500 uppercase tracking-widest">Cambios guardados con éxito</span>
                        </x-action-message>
                    </div>
                </form>
            </div>

            {{-- Password --}}
            <div class="tech-card p-8 border-white/5 bg-black/40 backdrop-blur-xl">
                <div class="flex items-center gap-4 mb-8 border-b border-white/5 pb-6">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-400 border border-amber-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Seguridad de Acceso</h3>
                        <p class="text-[9px] font-mono-tech text-zinc-500 uppercase">Asegúrate de usar una clave de alta entropía</p>
                    </div>
                </div>

                <form wire:submit="updatePassword" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[9px] font-mono-tech text-zinc-600 uppercase tracking-widest">Contraseña Actual</label>
                        <input type="password" wire:model="current_password" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-xs font-bold text-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                        @error('current_password') <span class="text-[9px] text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-mono-tech text-zinc-600 uppercase tracking-widest">Nueva Contraseña</label>
                            <input type="password" wire:model="new_password" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-xs font-bold text-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                            @error('new_password') <span class="text-[9px] text-rose-500 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[9px] font-mono-tech text-zinc-600 uppercase tracking-widest">Confirmar Contraseña</label>
                            <input type="password" wire:model="new_password_confirmation" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-xs font-bold text-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="px-8 py-3 bg-amber-600 hover:bg-amber-500 text-black font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-amber-600/20">
                            Rotar Contraseña
                        </button>
                        <x-action-message on="password-updated">
                            <span class="text-[9px] font-mono-tech text-emerald-500 uppercase tracking-widest">Clave actualizada correctamente</span>
                        </x-action-message>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="tech-card p-8 border-white/5 bg-black/40 backdrop-blur-xl">
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 mb-6 border-b border-white/5 pb-4">Protocolo de Seguridad</h4>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-zinc-300 uppercase">Cuenta Protegida</p>
                            <p class="text-[9px] text-zinc-500 mt-1 uppercase leading-relaxed">Tu cuenta está vinculada al nodo operativo Gestor. La eliminación de cuenta está restringida por política de integridad de datos.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-400 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-zinc-300 uppercase">Registro de Auditoría</p>
                            <p class="text-[9px] text-zinc-500 mt-1 uppercase leading-relaxed">Cualquier cambio en tus credenciales será notificado a la terminal de administración central.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 border border-white/5 rounded-[2.5rem] bg-gradient-to-br from-black/40 to-transparent">
                <h3 class="font-mono-tech text-[10px] text-zinc-400 uppercase tracking-widest mb-4">Estado del Nodo</h3>
                <div class="space-y-2 font-mono-tech text-[9px] text-zinc-500 uppercase">
                    <div class="flex justify-between"><span>SESIÓN_ID:</span> <span class="text-zinc-300">{{ substr(session()->getId(), 0, 10) }}...</span></div>
                    <div class="flex justify-between"><span>IP_ACCESO:</span> <span class="text-zinc-300">{{ request()->ip() }}</span></div>
                    <div class="flex justify-between"><span>ROL_NODO:</span> <span class="text-cyan-400 font-black">GESTOR_AUTH</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
