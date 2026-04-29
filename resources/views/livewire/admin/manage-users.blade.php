<div class="p-6 md:p-8 space-y-6 relative obsidian-bg min-h-screen text-[var(--text-primary)]" 
    x-data="{ 
        showScrollTop: false, 
        showForm: window.innerWidth >= 1280,
        role: @js($roleFilter)
    }" 
    @resize.window="if(window.innerWidth >= 1280) showForm = true"
    @scroll.window="showScrollTop = window.pageYOffset > 300"
    :style="role === 'gestor' ? '--theme-accent: var(--neon-violet); --theme-accent-soft: rgba(139, 92, 246, 0.1); --theme-accent-border: rgba(139, 92, 246, 0.3);' : '--theme-accent: var(--neon-amber); --theme-accent-soft: rgba(245, 158, 11, 0.1); --theme-accent-border: rgba(245, 158, 11, 0.3);'">
    
    <div class="w-full max-w-[1700px] mx-auto space-y-6">
        
        {{-- ══ HEADER ══ --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-[var(--theme-accent-border)] pb-4">
            <div>
                <h2 class="text-3xl font-bold text-[var(--theme-accent)] tracking-tight uppercase flex items-center gap-3">
                    <span class="p-2 bg-[var(--theme-accent-soft)] rounded-lg">
                        @if($roleFilter === 'gestor')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @else
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        @endif
                    </span>
                    Directorio de {{ ucfirst($roleFilter) }}s
                </h2>
                <p class="text-[var(--text-secondary)] text-sm mt-1 uppercase tracking-widest font-medium">
                    @if($filterManagerName)
                        Asignados al Gestor: <span class="text-[var(--theme-accent)] font-bold">{{ $filterManagerName }}</span>
                    @else
                        Módulo de control de identidades y privilegios críticos
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3 mt-4 md:mt-0">
                @if (session()->has('message'))
                    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl flex items-center gap-2 shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                        {{ session('message') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ══ TOP CONTROLS ══ --}}
        <div class="space-y-4">
            <div class="flex flex-col lg:flex-row gap-4 items-center">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Filtrar por nombre, credencial o identificación..."
                        class="tech-input block w-full pl-10 pr-4 py-3 text-xs focus:outline-none transition-all rounded-xl">
                </div>

                <div class="flex gap-3 w-full lg:w-auto">
                    <button wire:click="toggleSort"
                        class="flex-1 lg:flex-none bg-[var(--tech-input-bg)] border border-[var(--border-glass)] text-[var(--text-primary)] px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:bg-[var(--tech-hover-bg)] transition-all justify-center">
                        <svg class="w-4 h-4 text-[var(--theme-accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($sortDir === 'asc')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                            @endif
                        </svg>
                        Orden: {{ $sortDir === 'asc' ? 'A-Z' : 'Z-A' }}
                    </button>
                    
                    @if($filterManagerId)
                        <a href="{{ route('admin.users.role', 'cliente') }}"
                            class="flex-1 lg:flex-none px-6 py-3 bg-[var(--theme-accent-soft)] border border-[var(--theme-accent-border)] text-[var(--theme-accent)] rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-[var(--theme-accent)] hover:text-black transition-all justify-center">
                            Ver Todos
                        </a>
                    @endif
                </div>
            </div>

            <div class="xl:hidden">
                <button @click="showForm = !showForm" 
                    class="w-full py-4 bg-[var(--tech-input-bg)] border transition-all duration-300 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] flex items-center justify-center gap-3 active:scale-[0.98]
                    :class="showForm ? 'border-[var(--theme-accent)] text-[var(--theme-accent)] shadow-[0_0_15px_var(--theme-accent-soft)]' : 'border-[var(--border-glass)] text-[var(--text-secondary)]'">
                    <svg class="w-4 h-4 transition-transform duration-300" :class="showForm ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span x-text="showForm ? 'Ocultar Formulario' : '{{ $isEditing ? 'Continuar Edición' : 'Nueva Identidad' }}'"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8 items-start relative">
            
            {{-- ══ LISTING (60% on XL) ══ --}}
            <div class="xl:col-span-3 order-2 xl:order-1 space-y-6">
                <div class="tech-card overflow-hidden border border-[var(--border-glass)] shadow-2xl">
                    <div class="px-6 py-4 bg-black/20 border-b border-[var(--border-glass)] flex justify-between items-center">
                        <h4 class="text-[10px] font-black text-[var(--text-secondary)] uppercase tracking-[0.3em]">Censo de Personal Autorizado</h4>
                        <span class="text-[10px] font-mono text-[var(--theme-accent)] bg-[var(--theme-accent-soft)] px-2 py-0.5 rounded border border-[var(--theme-accent-border)]">
                            IDENTIDADES: {{ $users->total() }}
                        </span>
                    </div>

                    <div class="divide-y divide-[var(--border-glass)]">
                        @forelse($users as $u)
                            <div class="p-6 hover:bg-[var(--tech-hover-bg)] transition-all group relative overflow-hidden">
                                <div class="absolute inset-y-0 left-0 w-1 bg-[var(--theme-accent)] transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300"></div>
                                
                                <div class="flex flex-col md:flex-row justify-between gap-6 relative z-10">
                                    <div class="space-y-4 flex-1">
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                <div class="w-14 h-14 rounded-2xl border border-[var(--border-glass)] bg-black/40 flex items-center justify-center text-xl font-black text-[var(--theme-accent)] group-hover:border-[var(--theme-accent-border)] transition-all overflow-hidden shadow-inner">
                                                    {{ substr($u->name, 0, 1) }}
                                                    <div class="absolute inset-0 bg-gradient-to-t from-[var(--theme-accent)]/10 to-transparent"></div>
                                                </div>
                                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-[var(--bg-obsidian)] rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                                            </div>
                                            <div>
                                                <h4 class="text-base font-black text-[var(--text-primary)] uppercase tracking-tight group-hover:text-[var(--theme-accent)] transition-colors">
                                                    {{ $u->name }}
                                                </h4>
                                                <div class="flex flex-wrap items-center gap-3 mt-1">
                                                    <span class="text-[9px] font-black font-mono text-[var(--theme-accent)] bg-[var(--theme-accent-soft)] px-2 py-0.5 rounded border border-[var(--theme-accent-border)] uppercase">
                                                        IRIS-ID-{{ str_pad($u->id, 5, '0', STR_PAD_LEFT) }}
                                                    </span>
                                                    <span class="text-[10px] font-mono text-[var(--text-secondary)] uppercase tracking-tighter opacity-70">
                                                        {{ $u->email }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 pl-0 md:pl-18">
                                            <div class="flex items-center gap-3 bg-black/20 p-2 rounded-lg border border-[var(--border-glass)]">
                                                <div class="p-1.5 bg-[var(--theme-accent-soft)] rounded text-[var(--theme-accent)]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                </div>
                                                <span class="text-[10px] font-mono text-[var(--text-secondary)] uppercase">{{ $u->phone ?: 'SIN REGISTRO' }}</span>
                                            </div>
                                            
                                            <div class="flex items-center gap-3 bg-black/20 p-2 rounded-lg border border-[var(--border-glass)]">
                                                <div class="p-1.5 bg-[var(--theme-accent-soft)] rounded text-[var(--theme-accent)]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <span class="text-[10px] font-mono text-[var(--text-secondary)] uppercase">{{ $u->birth_date ? $u->birth_date->format('d/m/Y') : 'PDTE. VALIDACIÓN' }}</span>
                                            </div>

                                            @if($roleFilter === 'cliente' && $u->manager)
                                                <div class="sm:col-span-2 flex items-center gap-3 bg-[var(--neon-violet)]/5 p-2 rounded-lg border border-[var(--neon-violet)]/20">
                                                    <span class="text-[8px] font-black text-[var(--neon-violet)] uppercase tracking-[0.2em]">Gestor Asignado:</span>
                                                    <span class="text-[10px] font-bold text-[var(--text-primary)] uppercase">{{ $u->manager->name }}</span>
                                                </div>
                                            @endif
                                            
                                            @if($roleFilter === 'gestor')
                                                <div class="sm:col-span-2 flex items-center gap-3 bg-[var(--neon-violet)]/5 p-2 rounded-lg border border-[var(--neon-violet)]/20">
                                                    <span class="text-[8px] font-black text-[var(--neon-violet)] uppercase tracking-[0.2em]">Cartera Activa:</span>
                                                    <span class="text-[10px] font-bold text-[var(--text-primary)] uppercase">{{ $u->clients_count }} Clientes Designados</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-row md:flex-col gap-3 justify-end items-end shrink-0">
                                        <div class="flex gap-2">
                                            <button type="button" wire:click="edit({{ $u->id }})" @click="showForm = true; window.scrollTo({top: 0, behavior: 'smooth'})"
                                                class="p-2.5 rounded-lg border border-[var(--theme-accent-border)] text-[var(--theme-accent)] hover:bg-[var(--theme-accent)] hover:text-black transition-colors" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="confirmDelete({{ $u->id }})"
                                                class="p-2.5 rounded-lg border border-red-500/30 text-red-600 dark:text-red-500 hover:bg-red-500 hover:text-white transition-colors" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        @if($roleFilter === 'gestor')
                                            <button wire:click="regenerateUserPassword({{ $u->id }})"
                                                class="w-full sm:w-auto px-4 py-2 text-[9px] font-black uppercase tracking-widest text-[var(--neon-violet)] border border-[var(--neon-violet)]/40 hover:bg-[var(--neon-violet)] hover:text-black rounded-xl transition-all text-center shadow-lg shadow-[var(--neon-violet)]/10">
                                                Acceso de Emergencia
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-20 text-center text-[var(--text-secondary)] opacity-50">
                                <svg class="w-16 h-16 mx-auto mb-6 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.123-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-sm uppercase font-black tracking-[0.3em]">Nula detección de identidades en el sector</p>
                            </div>
                        @endforelse
                    </div>
                    
                    @if($users->hasPages())
                        <div class="px-6 py-4 bg-black/20 border-t border-[var(--border-glass)]">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ══ FORM (40% on XL) ══ --}}
            <div class="xl:col-span-2 order-1 xl:order-2 space-y-6 xl:sticky xl:top-8" 
                 x-show="showForm" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0">
                
                <div class="tech-card p-6 rounded-xl transition-all duration-500 relative overflow-hidden"
                    :class="isEditing ? 'border-[var(--theme-accent-border)] shadow-[0_0_30px_var(--theme-accent-soft)]' : ''">
                    <template x-if="isEditing">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-[var(--theme-accent)] to-transparent"></div>
                    </template>
                    
                    {{-- Form Header --}}
                    <div class="flex items-center justify-between mb-6 border-b border-zinc-200 dark:border-zinc-800/50 pb-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-black uppercase tracking-[0.1em] flex items-center gap-2" :class="isEditing ? 'text-[var(--theme-accent)]' : 'text-[var(--text-primary)]'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span x-text="isEditing ? 'Editando Expediente' : 'Nueva Identidad'"></span>
                            </h3>
                        </div>
                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode" class="text-[10px] uppercase font-mono-tech tracking-widest text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white px-2 py-1 transition-colors border border-zinc-300 dark:border-zinc-700/50 hover:border-white/20 rounded-lg" style="background: var(--tech-hover-bg)">
                                Nuevo Registro
                            </button>
                        @endif
                    </div>

                    <div x-data="{ tab: 'general' }">
                        <div class="flex bg-black/20 border-b border-[var(--border-glass)]">
                            <button @click="tab = 'general'"
                                :class="tab === 'general' ? 'border-b-2 border-[var(--theme-accent)] text-[var(--theme-accent)] bg-[var(--theme-accent-soft)]' : 'text-[var(--text-secondary)]'"
                                class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all">
                                Perfil de Usuario
                            </button>
                            @if($roleFilter === 'gestor')
                                <button @click="tab = 'clients'"
                                    :class="tab === 'clients' ? 'border-b-2 border-[var(--theme-accent)] text-[var(--theme-accent)] bg-[var(--theme-accent-soft)]' : 'text-[var(--text-secondary)]'"
                                    class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all border-l border-[var(--border-glass)]">
                                    Cartera de Clientes
                                </button>
                            @endif
                        </div>

                        <form wire:submit.prevent="confirmSave" class="p-6 space-y-6">
                            
                            {{-- Tab: General --}}
                            <div x-show="tab === 'general'" x-transition class="space-y-5">
                                @if($isEditing)
                                    <div class="bg-black/40 px-4 py-3 rounded-xl border border-[var(--theme-accent-border)] flex items-center justify-between">
                                        <div>
                                            <label class="block text-[8px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em] mb-1">ID DE EXPEDIENTE</label>
                                            <p class="text-xs font-mono text-[var(--theme-accent)] font-black">IRIS-{{ str_pad($userId, 4, '0', STR_PAD_LEFT) }}-{{ strtoupper($roleFilter) }}</p>
                                        </div>
                                        <div class="w-1.5 h-1.5 rounded-full bg-[var(--theme-accent)] animate-pulse"></div>
                                    </div>
                                @endif

                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Nombre y Apellidos</label>
                                    <input type="text" wire:model.live.debounce.300ms="name"
                                        class="tech-input w-full px-4 py-3 text-xs focus:outline-none transition-all rounded-xl shadow-inner border-[var(--border-glass)]">
                                    @error('name') <span class="text-[var(--neon-rose)] text-[8px] font-bold uppercase tracking-widest mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Correo Electrónico Oficial</label>
                                    <input type="email" wire:model="email" {{ $roleFilter === 'gestor' && $isEditing ? 'readonly' : '' }}
                                        class="tech-input w-full px-4 py-3 text-xs focus:outline-none transition-all rounded-xl border-[var(--border-glass)] {{ $roleFilter === 'gestor' && $isEditing ? 'opacity-50 bg-black/20' : '' }}">
                                    @error('email') <span class="text-[var(--neon-rose)] text-[8px] font-bold uppercase tracking-widest mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Contacto Telefónico</label>
                                        <input type="text" wire:model="phone"
                                            class="tech-input w-full px-4 py-3 text-xs focus:outline-none transition-all rounded-xl border-[var(--border-glass)]">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Fecha Nacimiento</label>
                                        <input type="date" wire:model="birth_date"
                                            class="tech-input w-full px-4 py-3 text-xs font-mono focus:outline-none transition-all rounded-xl border-[var(--border-glass)]">
                                    </div>
                                </div>

                                @if($roleFilter === 'cliente')
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-[var(--neon-violet)] uppercase tracking-widest pl-1">Gestor de Cuenta Designado</label>
                                        <div class="relative">
                                            <select wire:model="assigned_manager_id"
                                                class="tech-input w-full px-4 py-3 text-xs focus:outline-none transition-all rounded-xl appearance-none bg-[var(--tech-input-bg)] border-[var(--neon-violet)]/20 text-[var(--text-primary)]">
                                                <option value="">-- No asignado (Nivel 1) --</option>
                                                @foreach($managers as $manager)
                                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none opacity-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Tab: Clients --}}
                            @if($roleFilter === 'gestor')
                                <div x-show="tab === 'clients'" x-transition class="space-y-6">
                                    <div class="bg-black/30 p-5 rounded-xl border border-[var(--border-glass)] space-y-4">
                                        <h4 class="text-[9px] font-black text-[var(--theme-accent)] uppercase tracking-widest flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Vinculación de Clientes
                                        </h4>
                                        <div class="relative">
                                            <input type="text" wire:model.live.debounce.300ms="clientSearch" placeholder="Escriba nombre o email..."
                                                class="tech-input w-full px-4 py-3 text-xs focus:outline-none transition-all rounded-xl border-[var(--theme-accent-border)] bg-black/40">
                                        </div>

                                        @if(!empty($clientSearchResults))
                                            <div class="space-y-2 max-h-48 overflow-y-auto no-scrollbar pt-2">
                                                @foreach($clientSearchResults as $res)
                                                    <div class="flex justify-between items-center p-3 bg-white/5 border border-[var(--border-glass)] rounded-xl hover:bg-[var(--theme-accent-soft)] transition-all group">
                                                        <div class="overflow-hidden">
                                                            <p class="text-[10px] font-black text-[var(--text-primary)] truncate uppercase">{{ $res['name'] }}</p>
                                                            <p class="text-[8px] font-mono text-[var(--text-secondary)] truncate">{{ $res['email'] }}</p>
                                                        </div>
                                                        <button type="button" wire:click="requestAddClient({{ $res['id'] }})"
                                                            class="px-3 py-1.5 text-[8px] font-black bg-[var(--theme-accent)] text-black rounded-lg uppercase shadow-lg shadow-[var(--theme-accent-soft)]">
                                                            Añadir
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="space-y-3">
                                        <h4 class="text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">LISTA DE ASIGNACIÓN ({{ count($assignedClients) }})</h4>
                                        <div class="space-y-2 max-h-64 overflow-y-auto no-scrollbar pr-1">
                                            @forelse($assignedClients as $ac)
                                                <div class="flex justify-between items-center p-3 bg-black/40 border border-[var(--border-glass)] rounded-xl group hover:border-[var(--theme-accent-border)] transition-all shadow-lg">
                                                    <div class="overflow-hidden">
                                                        <p class="text-[10px] font-black text-[var(--text-primary)] uppercase truncate">{{ $ac['name'] }}</p>
                                                        <p class="text-[8px] font-mono text-[var(--theme-accent)] uppercase opacity-60">{{ $ac['old_manager'] ? 'TRANSACCIÓN: ' . $ac['old_manager'] : 'NUEVA ASIGNACIÓN' }}</p>
                                                    </div>
                                                    <button type="button" wire:click="removeClient({{ $ac['id'] }})"
                                                        class="p-2 text-red-500/50 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-all">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            @empty
                                                <div class="py-8 text-center border-2 border-dashed border-[var(--border-glass)] rounded-xl opacity-30">
                                                    <p class="text-[9px] text-[var(--text-secondary)] uppercase tracking-[0.2em]">Sin clientes vinculados</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Footer --}}
                            <div class="pt-6 border-t border-[var(--border-glass)] flex flex-col gap-3">
                                <button type="submit"
                                    class="w-full py-4 text-[11px] font-black uppercase tracking-[0.3em] transition-all rounded-xl border shadow-2xl active:scale-[0.98]"
                                    :class="isEditing 
                                        ? 'bg-[var(--theme-accent)] text-black border-[var(--theme-accent)] shadow-[0_10px_20px_var(--theme-accent-soft)]' 
                                        : 'bg-[var(--neon-violet)] text-black border-[var(--neon-violet)] shadow-[0_10px_20px_rgba(139,92,246,0.3)]'">
                                    <span x-text="isEditing ? 'Actualizar Expediente' : 'Registrar Nueva Identidad'"></span>
                                </button>
                                <button type="button" @click="showForm = false"
                                    class="xl:hidden w-full py-3 text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] border border-[var(--border-glass)] rounded-xl hover:bg-white/5 transition-all">
                                    Cerrar Formulario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Integrity Widget --}}
                <div class="tech-card p-6 bg-black/40 border-[var(--border-glass)] overflow-hidden relative group">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-[var(--theme-accent)] opacity-[0.03] rounded-full blur-3xl group-hover:opacity-[0.07] transition-all"></div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-[var(--theme-accent-soft)] rounded-lg text-[var(--theme-accent)]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h4 class="text-[10px] font-black text-[var(--theme-accent)] uppercase tracking-[0.2em]">Protocolo de Integridad</h4>
                    </div>
                    <p class="text-[10px] text-[var(--text-secondary)] leading-relaxed uppercase tracking-widest opacity-80">
                        La creación de identidades de clase <strong class="text-[var(--text-primary)]">{{ strtoupper($roleFilter) }}</strong> implica la aceptación de los protocolos de seguridad IRIS. Todo cambio será auditado y almacenado de forma permanente en el registro inmutable del sistema central.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}
    @if($showSaveModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/20 dark:bg-black/40 backdrop-blur-sm p-4">
            <div class="border border-black/10 dark:border-white/10 rounded-[15px] max-w-sm w-full overflow-hidden shadow-2xl backdrop-blur-xl bg-white/80 dark:bg-zinc-950/60"
                @click.away="$wire.set('showSaveModal', false)">
                <div class="p-6 border-b border-black/5 dark:border-white/5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-[var(--theme-accent-soft)] border border-[var(--theme-accent-border)] text-[var(--theme-accent)] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-widest mb-1">Validación Requerida</h3>
                        <p class="text-zinc-600 dark:text-zinc-300 text-xs leading-relaxed">
                            {{ $isEditing ? "Se procederá a la actualización inmutable de los datos de este expediente." : "Se creará una nueva identidad única en el sector." }}
                        </p>
                    </div>
                </div>
                <div class="flex p-3 gap-3 bg-zinc-100/50 dark:bg-black/30 border-t border-black/5 dark:border-white/5">
                    <button type="button" wire:click="$set('showSaveModal', false)"
                        class="flex-1 py-2.5 px-4 text-xs font-bold uppercase rounded-[10px] border border-black/10 dark:border-white/10 text-zinc-700 dark:text-zinc-300 hover:bg-black/5 dark:hover:bg-white/5 hover:text-black dark:hover:text-white transition-colors backdrop-blur-md">
                        Abortar
                    </button>
                    <button type="button" wire:click="executeSave"
                        class="flex-1 py-2.5 px-4 text-xs font-bold uppercase text-black bg-[var(--theme-accent)] rounded-[10px] shadow-lg transition-colors border border-[var(--theme-accent-border)]">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    @if($showDeleteModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/20 dark:bg-black/40 backdrop-blur-sm p-4">
            <div class="border border-red-500/30 dark:border-red-900/30 rounded-[15px] max-w-sm w-full overflow-hidden shadow-2xl backdrop-blur-xl bg-white/80 dark:bg-zinc-950/60">
                <div class="p-6 border-b border-red-500/10 dark:border-red-900/20 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-600 dark:text-red-500 uppercase tracking-widest mb-1">Eliminación Crítica</h3>
                        <p class="text-zinc-600 dark:text-zinc-300 text-xs leading-relaxed">
                            {{ ($deleteImpactInfo['type'] ?? '') === 'client_soft' ? 'La identidad será desactivada y archivada por motivos legales.' : 'Se procederá a la purga total de los datos de este registro.' }}
                        </p>
                    </div>
                </div>
                <div class="flex p-3 gap-3 bg-red-50/50 dark:bg-black/30 border-t border-red-500/10 dark:border-red-900/10">
                    <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="flex-1 py-2 px-4 text-xs font-bold uppercase rounded-[10px] border border-black/10 dark:border-white/10 text-zinc-700 dark:text-zinc-300 hover:bg-black/5 dark:hover:bg-white/5 hover:text-black dark:hover:text-white transition-colors backdrop-blur-md">Abortar</button>
                    <button type="button" wire:click="executeDelete"
                        class="flex-1 py-2 px-4 text-xs font-bold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600/90 dark:hover:bg-red-500 rounded-[10px] transition-all border border-red-600 dark:border-red-500/50 shadow-lg dark:shadow-[0_0_15px_rgba(220,38,38,0.3)]">Purgar</button>
                </div>
            </div>
        </div>
    @endif

    @if($showMigrationModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-md bg-black/40 animate-fade-in">
            <div class="relative z-10 w-full max-w-md bg-[var(--bg-obsidian)] border border-[var(--theme-accent-border)] rounded-[30px] shadow-2xl overflow-hidden animate-slide-in">
                <div class="p-10 space-y-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-[var(--theme-accent-soft)] border border-[var(--theme-accent-border)] text-[var(--theme-accent)] flex items-center justify-center shrink-0 shadow-[0_0_30px_var(--theme-accent-soft)]">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-[var(--theme-accent)] uppercase tracking-widest mb-1">Migración de Cartera</h3>
                            <p class="text-[11px] text-[var(--text-secondary)] uppercase tracking-wider leading-relaxed">
                                El gestor <strong class="text-[var(--text-primary)]">{{ $deleteImpactInfo['name'] ?? '' }}</strong> tiene <strong class="text-[var(--theme-accent)]">{{ $deleteImpactInfo['client_count'] ?? 0 }} clientes</strong> vinculados.
                            </p>
                        </div>
                    </div>
                    <div class="space-y-4 bg-black/40 p-5 rounded-2xl border border-[var(--border-glass)] shadow-inner">
                        <label class="block text-[9px] font-black text-[var(--theme-accent)] uppercase tracking-widest">Designar Gestor Substituto:</label>
                        <div class="relative">
                            <select wire:model.live="migrationTargetGestorId" class="tech-input w-full px-4 py-3 text-xs focus:outline-none transition-all rounded-xl bg-black border-[var(--theme-accent-border)] text-white appearance-none">
                                <option value="">-- Seleccionar Gestor Destino --</option>
                                @foreach($availableGestors as $g) 
                                    @if($g->id !== $deleteId) <option value="{{ $g->id }}">{{ $g->name }}</option> @endif 
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none opacity-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <button wire:click="$set('showMigrationModal', false)" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest border border-[var(--border-glass)] text-[var(--text-secondary)] rounded-xl hover:bg-white/5 transition-all">Cancelar</button>
                        <button wire:click="executeMigrationAndDelete" {{ !$migrationTargetGestorId ? 'disabled' : '' }} class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest bg-[var(--theme-accent)] text-black rounded-xl shadow-xl disabled:opacity-30">Confirmar Migración</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showPasswordModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-md bg-black/60 animate-fade-in">
            <div class="relative z-10 w-full max-w-sm bg-[#0b0b0b] border-2 border-[var(--neon-violet)]/40 rounded-[35px] shadow-[0_0_60px_rgba(139,92,246,0.3)] overflow-hidden animate-zoom-in">
                <div class="p-12 text-center space-y-10">
                    <div class="w-24 h-24 rounded-full bg-[var(--neon-violet)]/10 border border-[var(--neon-violet)]/30 text-[var(--neon-violet)] flex items-center justify-center mx-auto shadow-[0_0_40px_rgba(139,92,246,0.4)] relative">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        <div class="absolute inset-0 rounded-full border-2 border-[var(--neon-violet)] animate-ping opacity-20"></div>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-2xl font-black text-white uppercase tracking-[0.3em]">Protocolo Iris</h3>
                        <p class="text-[10px] text-zinc-500 uppercase font-black tracking-widest">Credenciales generadas con éxito.</p>
                    </div>
                    <div class="bg-black border-2 border-zinc-900 rounded-3xl p-8 space-y-3 shadow-inner">
                        <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest">Master Access Key</label>
                        <div class="text-3xl font-mono text-[var(--neon-violet)] font-black tracking-[0.4em]">{{ $tempPassword }}</div>
                    </div>
                    <button wire:click="$set('showPasswordModal', false)" class="w-full py-5 text-[11px] font-black uppercase tracking-[0.4em] bg-[var(--neon-violet)] text-black rounded-2xl shadow-[0_15px_30px_rgba(139,92,246,0.4)] active:scale-[0.97] transition-all">Cerrar Sesión</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Scroll to Top --}}
    <button x-show="showScrollTop" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="fixed bottom-8 right-8 z-[90] w-14 h-14 rounded-2xl bg-[var(--theme-accent)] text-black flex items-center justify-center shadow-[0_10px_20px_var(--theme-accent-soft)] border border-[var(--theme-accent-border)] transition-all active:scale-[0.9] hover:-translate-y-1">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
    </button>
</div>
