<div class="p-6 md:p-8 space-y-6 relative obsidian-bg min-h-screen text-[var(--text-primary)]" x-data="{ 
        showScrollTop: false, 
        showForm: window.innerWidth >= 1280,
        role: @js($roleFilter)
    }" @resize.window="if(window.innerWidth >= 1280) showForm = true"
    @scroll.window="showScrollTop = window.pageYOffset > 300"
    :style="role === 'gestor' ? '--theme-accent: var(--neon-amber); --theme-accent-soft: rgba(245, 158, 11, 0.1); --theme-accent-border: rgba(245, 158, 11, 0.3);' : '--theme-accent: #f97316; --theme-accent-soft: rgba(249, 115, 22, 0.1); --theme-accent-border: rgba(249, 115, 22, 0.3);'">
    <div class="w-full max-w-[1700px] mx-auto space-y-6">
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-orange-400/30 pb-4">
            <div>
                <h2
                    class="text-3xl font-bold text-[var(--theme-accent)] tracking-tight uppercase flex items-center gap-3">
                    <span class="p-2 bg-[var(--theme-accent-soft)] rounded-lg">
                        {{ ucfirst($roleFilter) }}s
                </h2>
                <p class="text-[var(--text-secondary)] text-sm mt-1 uppercase tracking-widest font-medium">
                    @if($filterManagerName)
                        Asignados al Gestor: <span
                            class="text-[var(--theme-accent)] font-bold">{{ $filterManagerName }}</span>
                    @else
                        Gestión de Usuarios {{ ucfirst($roleFilter) }}s
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3 mt-4 md:mt-0">
                @if (session()->has('message'))
                    <div
                        class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl flex items-center gap-2 shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                        {{ session('message') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex flex-col lg:flex-row gap-4 items-center">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Buscar usuario..."
                        class="tech-input block w-full pl-10 pr-4 py-3 text-xs focus:outline-none transition-all rounded-xl">
                </div>

                <div class="flex gap-3 w-full lg:w-auto">
                    <button wire:click="toggleSort"
                        class="flex-1 lg:flex-none bg-[var(--tech-input-bg)] border border-[var(--border-glass)] text-[var(--text-primary)] px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:bg-[var(--tech-hover-bg)] transition-all justify-center">
                        <svg class="w-4 h-4 text-[var(--theme-accent)]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            @if($sortDir === 'asc')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
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
                <button @click="showForm = !showForm" type="button"
                    class="w-full py-4 bg-[var(--tech-input-bg)] border transition-all duration-300 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] flex items-center justify-center gap-3 active:scale-[0.98] border-[var(--border-glass)] text-[var(--text-secondary)]"
                    :class="showForm ? 'border-[var(--theme-accent)] text-[var(--theme-accent)] shadow-[0_0_15px_var(--theme-accent-soft)]' : ''">
                    <span
                        x-text="showForm ? 'Ocultar Formulario' : '{{ $isEditing ? 'Continuar Edición' : 'Nuevo Usuario' }}'"></span>
                    <svg :class="showForm ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8 items-start relative">

            {{-- ══ LISTING (60% on XL) ══ --}}
            <div class="xl:col-span-3 order-2 xl:order-1 space-y-6">
                <div class="tech-card overflow-hidden border border-[var(--border-glass)] shadow-2xl">
                    <div
                        class="px-6 py-4 bg-black/20 border-b border-[var(--border-glass)] flex justify-between items-center">
                        <h4 class="text-[10px] font-black text-[var(--text-secondary)] uppercase tracking-[0.3em]">
                            Directorio de {{$roleFilter}}s</h4>
                        <span
                            class="uppercase text-[10px] font-mono text-[var(--theme-accent)] bg-[var(--theme-accent-soft)] px-2 py-0.5 rounded border border-[var(--theme-accent-border)]">
                            {{$roleFilter}}s: {{ $users->total() }}
                        </span>
                    </div>

                    <div class="divide-y divide-[var(--border-glass)]">
                        @forelse($users as $u)
                            <div class="p-6 hover:bg-[var(--tech-hover-bg)] transition-all group relative overflow-hidden">
                                <div
                                    class="absolute inset-y-0 left-0 w-1 bg-[var(--theme-accent)] transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300">
                                </div>

                                <div class="flex flex-col md:flex-row justify-between gap-6 relative z-10">
                                    <div class="space-y-4 flex-1">
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                <div
                                                    class="w-14 h-14 rounded-2xl border border-[var(--border-glass)] bg-black/40 flex items-center justify-center text-xl font-black text-[var(--theme-accent)] group-hover:border-[var(--theme-accent-border)] transition-all overflow-hidden shadow-inner">
                                                    {{ substr($u->name, 0, 1) }}
                                                    <div
                                                        class="absolute inset-0 bg-gradient-to-t from-[var(--theme-accent)]/10 to-transparent">
                                                    </div>
                                                </div>
                                                <div
                                                    class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-[var(--bg-obsidian)] rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]">
                                                </div>
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-base font-black text-[var(--text-primary)] uppercase tracking-tight group-hover:text-[var(--theme-accent)] transition-colors">
                                                    {{ $u->name }} {{ $u->primarylastname }} {{ $u->secondarylastname }}
                                                </h4>
                                                <div class="flex flex-wrap items-center gap-3 mt-1">
                                                    <span
                                                        class="text-[9px] font-black font-mono text-[var(--theme-accent)] bg-[var(--theme-accent-soft)] px-2 py-0.5 rounded border border-[var(--theme-accent-border)] uppercase">
                                                        IRIS-ID-{{ str_pad($u->id, 5, '0', STR_PAD_LEFT) }}
                                                    </span>
                                                    <span
                                                        class="text-[10px] font-mono text-[var(--text-secondary)] uppercase tracking-tighter opacity-70">
                                                        {{ $u->email }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 pl-0 md:pl-18">
                                            <div
                                                class="flex items-center gap-3 bg-black/20 p-2 rounded-lg border border-[var(--border-glass)]">
                                                <div
                                                    class="p-1.5 bg-[var(--theme-accent-soft)] rounded text-[var(--theme-accent)]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                </div>
                                                <span
                                                    class="text-[10px] font-mono text-[var(--text-secondary)] uppercase">{{ $u->phone ?: 'SIN REGISTRO' }}</span>
                                            </div>

                                            <div
                                                class="flex items-center gap-3 bg-black/20 p-2 rounded-lg border border-[var(--border-glass)]">
                                                <div
                                                    class="p-1.5 bg-[var(--theme-accent-soft)] rounded text-[var(--theme-accent)]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <span
                                                    class="text-[10px] font-mono text-[var(--text-secondary)] uppercase">{{ $u->birth_date ? $u->birth_date->format('d/m/Y') : 'PDTE. VALIDACIÓN' }}</span>
                                            </div>

                                            @if($roleFilter === 'cliente' && $u->manager)
                                                <div
                                                    class="sm:col-span-2 flex items-center gap-3 bg-orange-500/5 p-2 rounded-lg border border-orange-500/30">
                                                    <span
                                                        class="text-[8px] font-black text-orange-500 uppercase tracking-[0.2em]">Gestor
                                                        Asignado:</span>
                                                    <span
                                                        class="text-[10px] font-bold text-[var(--text-primary)] uppercase">{{ $u->manager->name }}</span>
                                                </div>
                                            @endif

                                            @if($roleFilter === 'gestor')
                                                <a href="{{ route('admin.passengers') }}?manager={{ $u->id }}" target="_blank"
                                                    class="flex items-center justify-between gap-2 bg-orange-500/10 px-2 py-1.5 rounded-lg border border-orange-500/40 hover:border-orange-500 hover:bg-orange-500/20 transition-all group/clients shadow-lg shadow-orange-900/10">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse">
                                                        </div>
                                                        <span
                                                            class="text-[9px] font-bold text-[var(--text-primary)] uppercase tracking-wider">{{ $u->clients_count }}
                                                            Clientes</span>
                                                    </div>
                                                    <svg class="w-3 h-3 text-orange-500 opacity-50" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>

                                                <!-- Tarea Link -->
                                                <a href="{{ route('admin.dashboard') }}?assign_to={{ $u->id }}#gestores-tasks"
                                                    wire:navigate
                                                    class="flex items-center justify-between gap-2 bg-emerald-500/10 px-2 py-1.5 rounded-lg border border-emerald-500/30 hover:border-emerald-500 hover:bg-emerald-500/20 transition-all group/task shadow-lg shadow-emerald-900/10">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                        </svg>
                                                        <span
                                                            class="text-[9px] font-bold text-[var(--text-primary)] uppercase tracking-wider">Asignar
                                                            Tarea</span>
                                                    </div>
                                                    <svg class="w-3 h-3 text-emerald-500 opacity-50" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-row md:flex-col gap-3 justify-end items-end shrink-0">
                                        <div class="flex gap-2">
                                            <button type="button" wire:click="edit({{ $u->id }})"
                                                @click="showForm = true; window.scrollTo({top: 0, behavior: 'smooth'})"
                                                class="p-2.5 rounded-lg border border-[var(--theme-accent-border)] text-[var(--theme-accent)] hover:bg-[var(--theme-accent)] hover:text-black transition-colors"
                                                title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="confirmDelete({{ $u->id }})"
                                                class="p-2.5 rounded-lg border border-red-500/30 text-red-600 dark:text-red-500 hover:bg-red-500 hover:text-white transition-colors"
                                                title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>

                                        @if($roleFilter === 'gestor')
                                            <button wire:click="regenerateUserPassword({{ $u->id }})"
                                                class="w-full sm:w-auto px-4 py-2 text-[9px] font-black uppercase tracking-widest text-[var(--neon-cyan)] border border-[var(--neon-cyan)] hover:bg-[var(--neon-cyan)] hover:text-black rounded-lg transition-all text-center shadow-lg shadow-[var(--neon-cyan)]/10 flex items-center gap-2 justify-center">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                Enviar Enlace de Acceso
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-20 text-center text-[var(--text-secondary)] opacity-50">
                                <svg class="w-16 h-16 mx-auto mb-6 opacity-20" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.123-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-sm uppercase font-black tracking-[0.3em]">No se han encontrado usuarios</p>
                            </div>
                        @endforelse
                    </div>

                    @if($users->hasPages())
                        <div class="px-6 py-4 bg-black/20 border-t border-[var(--border-glass)]">
                            {{ $users->links('vendor.livewire.simple-tailwind') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ══ FORM (40% on XL) ══ --}}
            <div class="xl:col-span-2 order-1 xl:order-2 space-y-6 xl:sticky xl:top-8" x-show="showForm"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

                <div
                    class="tech-card p-6 rounded-xl transition-all duration-500 relative overflow-hidden {{ $isEditing ? 'border-amber-500/50 shadow-[0_0_30px_rgba(245,158,11,0.1)]' : '' }}">
                    @if($isEditing)
                        <div
                            class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500/0 via-amber-500 to-amber-500/0">
                        </div>
                    @endif

                    <div
                        class="flex justify-between items-center mb-6 border-b border-zinc-200 dark:border-zinc-800/50 pb-4 hidden xl:flex">
                        <h3
                            class="text-sm font-black uppercase tracking-[0.1em] flex items-center gap-2 {{ $isEditing ? 'text-amber-400' : 'text-blue-400' }}">
                            @if($isEditing)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Editando Usuario
                            @else
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Nuevo Usuario
                            @endif
                        </h3>
                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode"
                                class="text-[10px] uppercase font-mono-tech tracking-widest text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white px-2 py-1 transition-colors border border-zinc-300 dark:border-zinc-700/50 hover:border-black/20 dark:hover:border-white/20 rounded-lg flex items-center gap-1.5"
                                style="background: var(--tech-hover-bg)">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Nuevo Usuario
                            </button>
                        @endif
                    </div>

                    <div x-data="{ tab: 'general' }">
                        <div class="flex bg-black/20 border-b border-[var(--border-glass)]">
                            <button @click="tab = 'general'"
                                :class="tab === 'general' ? 'border-b-2 {{ $isEditing ? 'border-amber-500 text-amber-500 bg-amber-500/10' : 'border-orange-500 text-orange-400 bg-orange-500/10' }}' : 'text-[var(--text-secondary)]'"
                                class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all">
                                Perfil de Usuario
                            </button>
                            @if($roleFilter === 'gestor')
                                <button @click="tab = 'clients'"
                                    :class="tab === 'clients' ? 'border-b-2 {{ $isEditing ? 'border-amber-500 text-amber-500 bg-amber-500/10' : 'border-orange-500 text-orange-400 bg-orange-500/10' }}' : 'text-[var(--text-secondary)]'"
                                    class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all border-l border-[var(--border-glass)]">
                                    Clientes Asignados
                                </button>
                            @endif
                        </div>

                        <form wire:submit.prevent="confirmSave" class="space-y-5 pt-6">

                            {{-- Tab: General --}}
                            <div x-show="tab === 'general'" x-transition class="space-y-5">
                                @if($isEditing)
                                    <div>
                                        <label
                                            class="block text-[10px] font-mono-tech text-zinc-500 mb-1 uppercase tracking-widest pl-1">ID
                                            DE EXPEDIENTE</label>
                                        <input type="text"
                                            value="IRIS-{{ str_pad($userId, 4, '0', STR_PAD_LEFT) }}-{{ strtoupper($roleFilter) }}"
                                            readonly
                                            class="w-full border border-zinc-200 dark:border-zinc-800 px-3 py-2 text-zinc-500 font-mono text-sm cursor-not-allowed outline-none rounded-lg"
                                            style="background: var(--tech-input-bg)">
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1"
                                            style="color: var(--text-secondary)">Nombre <span
                                                class="text-rose-500">*</span></label>
                                        <input type="text" wire:model.live.debounce.300ms="name"
                                            class="tech-input w-full border px-3 py-2 focus:outline-none transition-colors text-sm rounded-lg {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }}"
                                            style="background: var(--tech-input-bg); color: var(--text-primary)">
                                        @error('name') <span
                                            class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1"
                                            style="color: var(--text-secondary)">Primer Apellido <span
                                                class="text-rose-500">*</span></label>
                                        <input type="text" wire:model.live.debounce.300ms="primarylastname"
                                            class="tech-input w-full border px-3 py-2 focus:outline-none transition-colors text-sm rounded-lg {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }}"
                                            style="background: var(--tech-input-bg); color: var(--text-primary)">
                                        @error('primarylastname') <span
                                            class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1"
                                            style="color: var(--text-secondary)">Segundo Apellido (Opcional)</label>
                                        <input type="text" wire:model.live.debounce.300ms="secondarylastname"
                                            class="tech-input w-full border px-3 py-2 focus:outline-none transition-colors text-sm rounded-lg {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }}"
                                            style="background: var(--tech-input-bg); color: var(--text-primary)">
                                        @error('secondarylastname') <span
                                            class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1"
                                        style="color: var(--text-secondary)">Correo Electrónico <span
                                            class="text-rose-500">*</span></label>
                                    <input type="email" wire:model="email" {{ $roleFilter === 'gestor' && $isEditing ? 'readonly' : '' }}
                                        class="tech-input w-full border px-3 py-2 focus:outline-none transition-colors text-sm rounded-lg {{ $roleFilter === 'gestor' && $isEditing ? 'opacity-50 cursor-not-allowed' : '' }} {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }}"
                                        style="background: var(--tech-input-bg); color: var(--text-primary)">
                                    @error('email') <span
                                        class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1"
                                            style="color: var(--text-secondary)">Contacto Telefónico</label>
                                        <input type="text" wire:model="phone"
                                            class="tech-input w-full border px-3 py-2 focus:outline-none transition-colors text-sm rounded-lg {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }}"
                                            style="background: var(--tech-input-bg); color: var(--text-primary)">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1"
                                            style="color: var(--text-secondary)">Fecha Nacimiento</label>
                                        <input type="date" wire:model="birth_date"
                                            class="tech-input w-full border px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }}"
                                            style="background: var(--tech-input-bg); color: var(--text-primary)">
                                    </div>
                                </div>

                                @if($roleFilter === 'cliente')
                                    <div>
                                        <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1"
                                            style="color: var(--text-secondary)">Gestor de Cuenta Designado</label>
                                        <div class="relative">
                                            <select wire:model="assigned_manager_id"
                                                class="tech-input w-full border px-3 py-2 focus:outline-none transition-colors text-sm rounded-lg appearance-none {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-orange-500/30 focus:border-orange-400' }}"
                                                style="background: var(--tech-input-bg);color: var(--text-primary)">
                                                <option value="" disabled>-- Seleccionar Gestor --</option>
                                                @foreach($managers as $manager)
                                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                                @endforeach
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-3 flex items-center pointer-events-none opacity-50 {{ $isEditing ? 'text-amber-500' : 'text-orange-400' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Tab: Clients --}}
                            @if($roleFilter === 'gestor')
                                <div x-show="tab === 'clients'" x-transition class="space-y-6">
                                    <div
                                        class="bg-[var(--tech-card-bg)] p-5 rounded-xl border border-[var(--border-glass)] space-y-4">
                                        <h4
                                            class="text-[9px] font-black {{ $isEditing ? 'text-amber-500' : 'text-blue-400' }} uppercase tracking-widest flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Vinculación de Clientes
                                        </h4>
                                        <div class="relative">
                                            <input type="text" wire:model.live.debounce.300ms="clientSearch"
                                                placeholder="Escriba nombre o email..."
                                                class="w-full px-4 py-3 text-xs focus:outline-none transition-all rounded-xl border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }} bg-black/40"
                                                style="background: var(--tech-input-bg); color: var(--text-primary)">
                                        </div>

                                        @if(!empty($clientSearchResults))
                                            <div class="space-y-2 max-h-48 overflow-y-auto no-scrollbar pt-2">
                                                @foreach($clientSearchResults as $res)
                                                    <div
                                                        class="flex justify-between items-center p-3 bg-white/5 border border-[var(--border-glass)] rounded-xl hover:bg-[var(--tech-hover-bg)] transition-all group">
                                                        <div class="overflow-hidden">
                                                            <div class="flex items-center gap-2">
                                                                <p
                                                                    class="text-[10px] font-black text-[var(--text-primary)] truncate uppercase">
                                                                    {{ $res['name'] }}
                                                                </p>
                                                                @if(empty($res['manager']))
                                                                    <span
                                                                        class="text-[7px] bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded border border-emerald-500/30 font-black uppercase tracking-tighter">Disponible</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-[8px] font-mono text-[var(--text-secondary)] truncate">
                                                                ID: {{ $res['id'] }} • {{ $res['email'] }}</p>
                                                        </div>
                                                        <button type="button" wire:click="requestAddClient({{ $res['id'] }})"
                                                            class="px-3 py-1.5 text-[8px] font-black {{ $isEditing ? 'bg-amber-500' : 'bg-orange-500' }} text-black rounded-lg uppercase shadow-lg">
                                                            Añadir
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="space-y-3">
                                        <h4
                                            class="text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">
                                            LISTA DE ASIGNACIÓN ({{ count($assignedClients) }})</h4>
                                        <div class="space-y-2 max-h-64 overflow-y-auto no-scrollbar pr-1">
                                            @forelse($assignedClients as $ac)
                                                <div
                                                    class="flex justify-between items-center p-3 bg-white/5 border border-zinc-200/10 dark:border-zinc-800/50 rounded-lg group hover:border-[var(--theme-accent)] transition-all shadow-lg">
                                                    <div class="overflow-hidden">
                                                        <p
                                                            class="text-[10px] font-mono-tech font-bold text-[var(--text-primary)] uppercase truncate">
                                                            {{ $ac['name'] }}
                                                        </p>
                                                        <p
                                                            class="text-[8px] font-mono {{ $isEditing ? 'text-amber-500' : 'text-orange-400' }} uppercase opacity-60">
                                                            {{ $ac['old_manager'] ? 'TRANSACCIÓN: ' . $ac['old_manager'] : 'NUEVA ASIGNACIÓN' }}
                                                        </p>
                                                    </div>
                                                    <button type="button" wire:click="removeClient({{ $ac['id'] }})"
                                                        class="p-2 text-rose-500/50 hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition-all">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @empty
                                                <div
                                                    class="py-8 text-center border-2 border-dashed border-[var(--border-glass)] rounded-xl opacity-30">
                                                    <p
                                                        class="text-[9px] text-[var(--text-secondary)] uppercase tracking-[0.2em]">
                                                        Sin clientes vinculados</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Footer --}}
                            <div class="pt-4 mt-2 border-t border-zinc-200 dark:border-zinc-800/50 flex flex-col gap-3">
                                <button type="submit"
                                    class="w-full font-mono-tech font-bold uppercase tracking-widest py-3 px-4 transition-colors text-[11px] rounded-lg border flex items-center justify-center gap-2 {{ $isEditing ? 'bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-black border-amber-500/50' : 'bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 dark:text-emerald-400 hover:text-black border-emerald-500/50' }}">
                                    {{ $isEditing ? 'Actualizar Usuario' : 'Registrar Nuevo Usuario' }}
                                </button>
                                <button type="button" @click="expanded = false"
                                    class="xl:hidden w-full py-3 text-[10px] font-mono-tech uppercase tracking-widest text-[var(--text-secondary)] border border-[var(--border-glass)] rounded-lg hover:bg-white/5 transition-all">
                                    Cerrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Integrity Widget --}}
                <div
                    class="tech-card p-6 bg-black/40 border border-[var(--border-glass)] overflow-hidden relative group rounded-xl">
                    <div
                        class="absolute -right-10 -bottom-10 w-32 h-32 {{ $isEditing ? 'bg-amber-500' : 'bg-blue-500' }} opacity-[0.03] rounded-full blur-3xl group-hover:opacity-[0.07] transition-all">
                    </div>
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="p-2 {{ $isEditing ? 'bg-amber-500/10 text-amber-500' : 'bg-orange-500/10 text-orange-400' }} rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h4
                            class="text-[10px] font-black {{ $isEditing ? 'text-amber-500' : 'text-orange-400' }} uppercase tracking-[0.2em]">
                            Protocolo de Integridad</h4>
                    </div>
                    <p
                        class="text-[10px] text-[var(--text-secondary)] leading-relaxed uppercase tracking-widest opacity-80">
                        La creación de usuarios de clase <strong
                            class="text-[var(--text-primary)]">{{ strtoupper($roleFilter) }}</strong> implica la
                        aceptación de los protocolos de seguridad IRIS. Todo cambio será auditado y almacenado de forma
                        permanente en el registro inmutable del sistema central.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bloque de modales --}}
    <div x-data="{ 
        lockScroll: @entangle('showSaveModal') || @entangle('showDeleteModal') || @entangle('showMigrationModal') || @entangle('showOverrideModal')
    }"
        x-effect="lockScroll ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">

        {{-- Modal Guardar --}}
        @if($showSaveModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md"
                    wire:click="$set('showSaveModal', false)"></div>

                <div
                    class="relative border border-[var(--border-glass)] rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/90 backdrop-blur-xl animate-tech">
                    <div class="p-8 border-b border-[var(--border-glass)] flex flex-col items-center text-center gap-4">
                        <div
                            class="w-14 h-14 rounded-full {{ $isEditing ? 'bg-amber-500/10 border-amber-500/30 text-amber-500 shadow-[0_0_20px_rgba(245,158,11,0.1)]' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500 shadow-[0_0_20px_rgba(16,185,129,0.1)]' }} flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">
                                {{ $isEditing ? 'Confirmar Edición' : 'Confirmar Registro' }}
                            </h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                                {{ $isEditing ? "Se procederá a la actualización inmutable de los datos de este usuario." : "Se creará un nuevo usuario en el sistema." }}
                            </p>
                        </div>
                    </div>
                    <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="$set('showSaveModal', false)"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="executeSave"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest text-black {{ $isEditing ? 'bg-amber-500 hover:bg-amber-400 shadow-[0_0_20px_rgba(245,158,11,0.3)]' : 'bg-emerald-500 hover:bg-emerald-400 shadow-[0_0_20px_rgba(16,185,129,0.3)]' }} rounded-xl transition-all">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Eliminar --}}
        @if($showDeleteModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md"
                    wire:click="$set('showDeleteModal', false)"></div>

                <div
                    class="relative border border-[var(--border-glass)] rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/90 backdrop-blur-xl animate-tech">
                    <div class="p-8 border-b border-[var(--border-glass)] flex flex-col items-center text-center gap-4">
                        <div
                            class="w-14 h-14 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-500 flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(244,63,94,0.1)]">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">
                                Eliminación</h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                                {{ ($deleteImpactInfo['type'] ?? '') === 'client_soft' ? 'El usuario será desactivado y archivado por motivos legales.' : 'Se procederá a la eliminación total de los datos de este registro.' }}
                                <br>
                                <span
                                    class="text-rose-500/80 mt-1 block font-bold uppercase tracking-widest text-[9px]">Esta
                                    acción es irreversible</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="$set('showDeleteModal', false)"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="executeDelete"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-[0_0_20px_rgba(225,29,72,0.3)] transition-all">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if($showMigrationModal)
            <div class="fixed inset-0 z-[600] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/90 backdrop-blur-lg"
                    wire:click="$set('showMigrationModal', false)"></div>

                <div
                    class="relative border border-[var(--neon-rose)]/30 rounded-[24px] max-w-md w-full overflow-hidden shadow-[0_0_60px_rgba(220,38,38,0.4)] bg-[var(--bg-panel)]/95 backdrop-blur-2xl animate-tech">
                    <div class="p-8 border-b border-[var(--neon-rose)]/10 flex flex-col items-center text-center gap-5">
                        <div
                            class="w-16 h-16 rounded-full bg-[var(--neon-rose)] text-white flex items-center justify-center shrink-0 shadow-[0_0_30px_rgba(220,38,38,0.4)]">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-[var(--neon-rose)] uppercase tracking-widest mb-2">Migración
                                de Clientes</h3>
                            <div class="space-y-3">
                                <p class="text-[var(--text-primary)] text-sm font-bold uppercase tracking-wider">
                                    El gestor <span
                                        class="text-[var(--neon-rose)] underline">{{ $deleteImpactInfo['name'] ?? '' }}</span>
                                    posee
                                    <span class="text-[var(--neon-rose)]">{{ $deleteImpactInfo['client_count'] ?? 0 }}
                                        clientes</span> activos.
                                </p>
                                <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                                    Para proceder con la eliminación, debe designar un gestor que asuma la
                                    responsabilidad de estos clientes.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="space-y-2">
                            <label
                                class="block text-[10px] font-mono-tech text-[var(--neon-rose)] uppercase tracking-widest pl-1">Designar
                                Gestor Substituto</label>
                            <div class="relative">
                                <select wire:model.live="migrationTargetGestorId"
                                    class="w-full bg-black/40 border border-[var(--neon-rose)]/30 focus:border-[var(--neon-rose)] px-4 py-3 text-xs focus:outline-none transition-all rounded-xl text-white appearance-none">
                                    <option value="">-- Seleccionar Gestor Destino --</option>
                                    @foreach($availableGestors as $g)
                                        @if($g->id !== $deleteId)
                                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[var(--neon-rose)]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex p-5 gap-3 bg-[var(--neon-rose)]/5">
                        <button type="button" wire:click="$set('showMigrationModal', false)"
                            class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="executeMigrationAndDelete" {{ !$migrationTargetGestorId ? 'disabled' : '' }}
                            class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest text-white bg-red-600 hover:bg-red-500 rounded-xl shadow-[0_0_30px_rgba(220,38,38,0.5)] transition-all disabled:opacity-30 disabled:cursor-not-allowed">
                            Confirmar Migración
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Transferencia de Cliente (Override) --}}
        @if($showOverrideModal)
            <div class="fixed inset-0 z-[600] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md" wire:click="cancelOverrideClient">
                </div>

                <div
                    class="relative border border-[var(--neon-amber)]/30 rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/95 backdrop-blur-xl animate-tech">
                    <div class="p-8 border-b border-[var(--border-glass)] flex flex-col items-center text-center gap-4">
                        <div
                            class="w-14 h-14 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-500 shadow-[0_0_20px_rgba(245,158,11,0.1)] flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">
                                Transferencia de Cartera</h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                                El cliente <span
                                    class="text-amber-500 font-bold">{{ $pendingClientOverride ? $pendingClientOverride['name'] : '' }}</span>
                                ya está asignado al gestor
                                <span
                                    class="text-amber-500 font-bold">{{ $pendingClientOverride && isset($pendingClientOverride['manager']) ? $pendingClientOverride['manager']['name'] : 'N/A' }}</span>.
                                <br><br>
                                ¿Deseas reasignarlo a tu jurisdicción actual?
                            </p>
                        </div>
                    </div>
                    <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="cancelOverrideClient"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="confirmOverrideClient"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest text-black bg-amber-500 hover:bg-amber-400 shadow-[0_0_20px_rgba(245,158,11,0.3)] rounded-xl transition-all">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Scroll to Top --}}
    <button x-show="showScrollTop" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="fixed bottom-8 right-8 z-[90] w-14 h-14 rounded-2xl bg-[var(--theme-accent)] text-black flex items-center justify-center shadow-[0_10px_20px_var(--theme-accent-soft)] border border-[var(--theme-accent-border)] transition-all active:scale-[0.9] hover:-translate-y-1">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18">
            </path>
        </svg>
    </button>
</div>