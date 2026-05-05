<div class="p-6 md:p-8 space-y-6 relative" x-data="{ showScrollTop: false }" @scroll.window="showScrollTop = window.pageYOffset > 300">

    {{-- ══ HEADER ══ --}}
    <div class="flex items-start justify-between border-b border-blue-400/30 pb-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-[0.15em] text-[var(--neon-cyan)] flex items-center gap-3">
                Naves
            </h1>
            <p class="font-mono-tech text-[11px] uppercase tracking-widest mt-1 text-[var(--text-secondary)]">
                Gestión de Naves Espaciales  · {{ $starships->total() }} Registradas
            </p>
        </div>
        <div class="flex items-center gap-4">
            @if (session()->has('message'))
                <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--neon-emerald)]/10 border border-[var(--neon-emerald)]/30">
                    <div class="w-2 h-2 rounded-full bg-[var(--neon-emerald)]"></div>
                    <span class="font-mono-tech text-[10px] text-[var(--neon-emerald)] uppercase tracking-widest">{{ session('message') }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-y-4 md:gap-8 items-start md:grid-rows-[auto_1fr]">

        <!-- 1. Buscador y Filtro -->
        <div class="md:col-span-3 md:col-start-1 md:row-start-1">
            <div class="tech-card p-4 flex flex-col sm:flex-row gap-4 justify-between items-center rounded-xl">
                <div class="relative w-full sm:w-2/3">
                    <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Buscar..."
                        class="block w-full pl-10 border border-[var(--border-glass)] placeholder-zinc-600 py-2 focus:outline-none focus:border-blue-400 text-sm transition-colors rounded-lg"
                        style="background: var(--tech-input-bg); color: var(--text-primary)">
                </div>

                <div class="w-full sm:w-1/3 flex justify-end">
                    <button type="button" wire:click="toggleSort" wire:key="btn-sort-starships"
                        class="border border-[var(--border-glass)] px-4 py-2 text-xs font-mono-tech uppercase tracking-widest flex items-center gap-2 transition-colors w-full sm:w-auto justify-center rounded-lg hover:bg-[var(--tech-hover-bg)]"
                        style="background: var(--tech-input-bg); color: var(--text-primary)">
                        @if($sortDir === 'asc')
                            <svg wire:key="icon-sort-asc" class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                            </svg>
                        @else
                            <svg wire:key="icon-sort-desc" class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l-4-4"></path>
                            </svg>
                        @endif
                        <span wire:key="text-sort-dir">Orden: {{ $sortDir === 'asc' ? 'A-Z' : 'Z-A' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. Formulario (Entre Buscador y Lista en Mobile, Derecha en Desktop) -->
        <div class="md:col-span-2 md:col-start-4 md:row-start-1 md:row-span-3 mt-4 md:mt-0"
            x-data="{ expanded: window.innerWidth >= 768 }" @resize.window="if(window.innerWidth >= 768) expanded = true">
                <div class="tech-card p-6 rounded-xl transition-all duration-500 relative overflow-hidden {{ $isEditing ? 'border-amber-500/50 shadow-[0_0_30px_rgba(245,158,11,0.1)]' : '' }}">
                    @if($isEditing)
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500/0 via-amber-500 to-amber-500/0"></div>
                    @endif

                    <!-- Mobile Toggle -->
                    <button @click="expanded = !expanded" type="button" class="w-full md:hidden flex justify-between items-center pb-4 mb-4 border-b border-[var(--border-glass)] font-black uppercase tracking-widest text-sm transition-colors {{ $isEditing ? 'text-amber-400' : 'text-blue-400' }}">
                        <span x-text="expanded ? 'Ocultar Formulario' : 'Mostrar Formulario'"></span>
                        <svg :class="expanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="expanded" x-transition>
                        <div class="flex justify-between items-center mb-6 border-b border-[var(--border-glass)] pb-4 hidden md:flex">
                            <h3 class="text-sm font-black uppercase tracking-[0.1em] flex items-center gap-2 {{ $isEditing ? 'text-amber-400' : 'text-blue-400' }}">
                            @if($isEditing)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Editando Nave
                            @else
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Nueva Nave
                            @endif
                        </h3>

                        </div>

                        <div class="md:hidden flex justify-between items-center mb-4">
                            <h3 class="text-xs font-black uppercase tracking-[0.1em] {{ $isEditing ? 'text-amber-400' : 'text-blue-400' }}">
                                {{ $isEditing ? 'Editando Nave' : 'Nueva Nave' }}
                            </h3>
                            @if($isEditing)
                                <button type="button" wire:click="setCreateMode" class="text-[10px] uppercase font-mono-tech tracking-widest text-[var(--text-secondary)] hover:text-[var(--text-primary)] px-2 py-1 transition-colors border border-[var(--border-glass)] hover:border-white/20 rounded-lg" style="background: var(--tech-hover-bg)">
                                    Crear
                                </button>
                            @endif
                        </div>

                        <form wire:submit.prevent="confirmSave" class="space-y-4" x-data="{ status: @entangle('status') }">
                        @if($isEditing)
                            <div>
                                <label class="block text-[10px] font-mono-tech text-[var(--text-secondary)] mb-1 uppercase tracking-widest flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    ID
                                </label>
                                <input type="text" value="{{ str_pad($starshipId, 4, '0', STR_PAD_LEFT) }}" readonly
                                    class="w-full border border-[var(--border-glass)] px-3 py-2 text-[var(--text-secondary)] font-mono text-sm cursor-not-allowed outline-none rounded-lg"
                                    style="background: rgba(0,0,0,0.2);">
                            </div>
                        @endif

                        <div>
                            <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1"
                                style="color: var(--text-secondary)">
                                Nombre de la Nave <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model="name" required
                                class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg"
                                style="background: var(--tech-input-bg); color: var(--text-primary)">
                            @error('name') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest flex items-center gap-1.5 pl-1" style="color: var(--text-secondary)">
                                    <span>Cap.<br>Nova</span>
                                </label>
                                <input type="number" min="0" wire:model="general_capacity"
                                    class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg"
                                    style="background: var(--tech-input-bg); color: var(--text-primary)">
                                @error('general_capacity') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest flex items-center gap-1.5 pl-1" style="color: var(--text-secondary)">
                                    <span>Cap.<br> SuperNova</span>
                                </label>
                                <input type="number" min="0" wire:model="vip_capacity"
                                    class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg"
                                    style="background: var(--tech-input-bg); color: var(--text-primary)">
                                @error('vip_capacity') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest flex items-center gap-1.5 pl-1" style="color: var(--text-secondary)">
                                    <span>Cap.<br> Tripulación</span>
                                </label>
                                <input type="number" min="0" wire:model="crew_capacity"
                                    class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg"
                                    style="background: var(--tech-input-bg); color: var(--text-primary)">
                                @error('crew_capacity') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">
                                    Coste Base*AU(€)
                                </label>
                                <input type="number" min="0" step="0.01" wire:model="operational_cost_per_au"
                                    class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg"
                                    style="background: var(--tech-input-bg); color: var(--text-primary)">
                                @error('operational_cost_per_au') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">
                                    Estado Operativo
                                </label>
                                <select wire:model.live="status"
                                    class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg appearance-none cursor-pointer"
                                    style="background: var(--tech-input-bg); color: var(--text-primary)">
                                    <option value="active">Activa</option>
                                    <option value="maintenance">Mantenimiento</option>
                                    <option value="retired">Retirada</option>
                                </select>
                            </div>
                        </div>

                        <div class="border-t border-[var(--border-glass)] pt-4 mt-2">
                            <p class="text-[9px] font-mono-tech text-blue-400 uppercase tracking-widest mb-3 pl-1">
                                Parámetros de Vuelos
                            </p>
                            <div class="mb-3">
                                <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">
                                    Velocidad (Horas/AU)
                                </label>
                                <input type="number" min="0.0001" step="0.0001" wire:model="cruise_speed_au"
                                    class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-cyan-500/30 focus:border-cyan-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg"
                                    style="background: var(--tech-input-bg); color: var(--text-primary)">
                                @error('cruise_speed_au') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">
                                        Tripulación €/hora
                                    </label>
                                    <input type="number" min="0" step="0.01" wire:model="crew_hourly_rate"
                                        class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg"
                                        style="background: var(--tech-input-bg); color: var(--text-primary)">
                                    @error('crew_hourly_rate') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">
                                        Espera tripulación €/día
                                    </label>
                                    <input type="number" min="0" step="0.01" wire:model="crew_daily_rate"
                                        class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-blue-500/30 focus:border-blue-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg"
                                        style="background: var(--tech-input-bg); color: var(--text-primary)">
                                </div>

                                <div x-show="status === 'maintenance'" x-transition class="col-span-2 grid grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <label class="block text-[10px] font-mono-tech text-amber-500/80 mb-1 uppercase tracking-widest pl-1 flex items-center gap-1.5">
                                            Inicio Mantenimiento
                                        </label>
                                        <input type="date" wire:model="maintenance_start_date"
                                            class="w-full border border-amber-500/30 focus:border-amber-500 px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg [color-scheme:dark]"
                                            style="background: var(--tech-input-bg); color: var(--text-primary)">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-mono-tech text-rose-500/80 mb-1 uppercase tracking-widest pl-1 flex items-center gap-1.5">
                                            Fin Mantenimiento
                                        </label>
                                        <input type="date" wire:model="maintenance_end_date"
                                            class="w-full border border-rose-500/30 focus:border-rose-500 px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg [color-scheme:dark]"
                                            style="background: var(--tech-input-bg); color: var(--text-primary)">
                                    </div>
                                </div>

                                <div class="col-span-2 pt-6 border-t border-[var(--border-glass)] mt-2">
                                    <button type="submit"
                                        class="w-full font-mono-tech font-bold uppercase tracking-widest py-3 px-4 transition-colors text-[11px] rounded-lg border flex items-center justify-center gap-2 {{ $isEditing ? 'bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-black border-amber-500/50' : 'bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 dark:text-emerald-400 hover:text-black border-emerald-500/50' }}">
                                        {{ $isEditing ? 'Actualizar Nave' : 'Registrar Nueva Nave' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
        </div>

        <!-- 3. Lista de Resultados -->
        <div class="md:col-span-3 md:col-start-1 md:row-start-2 mt-4 md:mt-0">
            <div class="tech-card rounded-xl overflow-hidden">
                <ul class="divide-y divide-[var(--border-glass)]">
                        @forelse($starships as $ship)
                            <li wire:key="ship-{{ $ship->id }}"
                                class="p-5 transition-all flex flex-col sm:flex-row justify-between sm:items-center gap-4 group hover:bg-[var(--tech-hover-bg)] bg-[var(--bg-panel)]/40 relative overflow-hidden">
                                <div
                                    class="absolute inset-y-0 left-0 w-1 bg-[var(--neon-cyan)] transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3">
                                        <span class="text-[10px] font-mono-tech text-[var(--neon-cyan)] bg-[var(--neon-cyan)]/10 px-2 py-0.5 rounded border border-[var(--border-glass)]">ID:{{ str_pad($ship->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        <h4 class="text-lg font-black uppercase tracking-wide flex items-center gap-2 group-hover:text-[var(--neon-cyan)] transition-colors text-[var(--text-primary)]">
                                            {{ $ship->name }}
                                        </h4>
                                        @if($ship->status === 'active')
                                            <span class="px-2 py-0.5 text-[9px] uppercase font-mono-tech tracking-widest bg-[var(--neon-emerald)]/10 text-[var(--neon-emerald)] border border-[var(--border-glass)]  rounded">Active</span>
                                        @elseif($ship->status === 'maintenance')
                                            <span class="px-2 py-0.5 text-[9px] uppercase font-mono-tech tracking-widest bg-[var(--neon-amber)]/10 text-[var(--neon-amber)] border border-[var(--border-glass)]  rounded">Maintenance</span>
                                        @else
                                            <span class="px-2 py-0.5 text-[9px] uppercase font-mono-tech tracking-widest bg-[var(--neon-rose)]/10 text-[var(--neon-rose)] border border-[var(--border-glass)]  rounded">Retired</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <div class="inline-flex items-center gap-1.5 text-[10px] font-mono-tech px-2 py-1 border border-[var(--border-glass)] rounded-lg bg-[var(--tech-input-bg)] text-[var(--text-primary)]">
                                            <svg class="w-3.5 h-3.5 text-[var(--neon-cyan)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span class="text-[var(--text-secondary)] uppercase">Nova:</span> <span class="text-[var(--neon-cyan)]">{{ $ship->general_capacity }}</span>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 text-[10px] font-mono-tech px-2 py-1 border border-[var(--border-glass)]  rounded-lg bg-[var(--neon-amber)]/10 text-[var(--neon-amber)]">
                                            <svg class="w-3.5 h-3.5 text-[var(--neon-amber)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                            </svg>
                                            <span class="uppercase">SuperNova:</span> <span class="text-[var(--neon-amber)]">{{ $ship->vip_capacity }}</span>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 text-[10px] font-mono-tech px-2 py-1 border border-[var(--border-glass)]  rounded-lg bg-[var(--neon-cyan)]/10 text-[var(--neon-cyan)]">
                                            <svg class="w-3.5 h-3.5 text-[var(--neon-cyan)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span class="uppercase">Tripulación:</span> <span class="text-[var(--neon-cyan)]">{{ $ship->crew_capacity }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex-col gap-2 shrink-0 border-t border-[var(--border-glass)] sm:border-0 pt-4 sm:pt-0">
                                    <button type="button" wire:click="edit({{ $ship->id }})"
                                        @click="expanded = true; window.scrollTo({top: 0, behavior: 'smooth'})"
                                        class="p-2.5 rounded-lg border border-amber-500/30 text-amber-500 hover:bg-amber-500 hover:text-black transition-colors"
                                        title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $ship->id }})"
                                        class="p-2.5 rounded-lg border border-red-500/30 text-red-600 dark:text-red-500 hover:bg-red-500 hover:text-white transition-colors"
                                        title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        @empty
                            <div class="p-12 text-center flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-[var(--tech-input-bg)] flex items-center justify-center mb-4 border border-[var(--border-glass)]">
                                    <svg class="w-8 h-8 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <p class="text-[11px] font-mono-tech uppercase tracking-widest text-[var(--text-secondary)]">No hay naves espaciales registradas</p>
                            </div>
                        @endforelse
                </ul>

                @if($starships->hasPages())
                    <div class="px-6 py-4 bg-black/20 border-t border-[var(--border-glass)]">
                        {{ $starships->links('vendor.livewire.simple-tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>


    <!-- Botón Subir Mobile -->
    <button x-show="showScrollTop" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="md:hidden fixed bottom-6 right-6 z-[90] w-12 h-12 rounded-full bg-blue-500 text-black flex items-center justify-center shadow-[0_0_20px_rgba(59,130,246,0.5)] border border-blue-400/50 transition-transform active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>

    {{-- Bloque de modales --}}
    <div x-data="{ 
        lockScroll: @entangle('showSaveModal') || @entangle('showDeleteModal') || @entangle('showCascadeDeleteModal') || @entangle('showConflictModal')
    }"
        x-effect="lockScroll ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">
        
        {{-- Modal Guardar/Editar --}}
        @if($showSaveModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md" wire:click="$set('showSaveModal', false)"></div>

                <div
                    class="relative border border-[var(--border-glass)] rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/90 backdrop-blur-xl animate-tech">
                    <div class="p-8 border-b border-[var(--border-glass)] flex flex-col items-center text-center gap-4">
                        <div
                            class="w-14 h-14 rounded-full {{ $isEditing ? 'bg-amber-500/10 border-amber-500/30 text-amber-500 shadow-[0_0_20px_rgba(245,158,11,0.1)]' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500 shadow-[0_0_20px_rgba(14,165,233,0.1)]' }} flex items-center justify-center shrink-0">
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
                                {{ $isEditing ? '¿Confirmas los cambios en los sistemas de esta nave?' : '¿Confirmas la integración de esta nueva nave?' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="$set('showSaveModal', false)"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="executeSave"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest text-black {{ $isEditing ? 'bg-amber-500 hover:bg-amber-400 shadow-[0_0_20px_rgba(245,158,11,0.3)]' : 'bg-emerald-500 hover:bg-emerald-400 shadow-[0_0_20px_rgba(14,165,233,0.3)]' }} rounded-xl transition-all">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modal Eliminar --}}
        @if($showDeleteModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md" wire:click="$set('showDeleteModal', false)">
                </div>

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
                            <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">Eliminación de Nave</h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                                ¿Confirmas la eliminación permanente de esta nave? <br>
                                <span class="text-rose-500/80 mt-1 block font-bold">Esta acción retirará la nave de todos los registros activos.</span>
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
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modal Cascada --}}
        @if($showCascadeDeleteModal)
            <div class="fixed inset-0 z-[600] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/90 backdrop-blur-lg"
                    wire:click="$set('showCascadeDeleteModal', false)"></div>

                <div
                    class="relative border border-rose-500/30 rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_60px_rgba(225,29,72,0.4)] bg-[var(--bg-panel)]/95 backdrop-blur-2xl animate-tech">
                    <div class="p-8 border-b border-rose-500/10 flex flex-col items-center text-center gap-5">
                        <div
                            class="w-16 h-16 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-[0_0_30px_rgba(225,29,72,0.4)]">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-rose-500 uppercase tracking-widest mb-2">Eliminación en Cascada</h3>
                            <p class="text-[var(--text-primary)] text-[11px] leading-relaxed font-bold">
                                Se han detectado <span class="underline">{{ $flightsCount }} vuelos</span> programados.
                            </p>
                            <p class="text-[var(--text-secondary)] text-[10px] leading-tight mt-2">
                                La eliminación de la nave cancelará todos sus vuelos asociados y notificará a los pasajeros.
                            </p>
                        </div>
                    </div>
                    <div class="flex p-5 gap-3 bg-rose-600/5">
                        <button type="button" wire:click="$set('showCascadeDeleteModal', false)"
                            class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="executeDelete"
                            class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-[0_0_30px_rgba(225,29,72,0.5)] transition-all">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Modal Conflicto (Mantenimiento) --}}
        @if($showConflictModal)
            <div class="fixed inset-0 z-[600] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/90 backdrop-blur-lg"
                    wire:click="$set('showConflictModal', false)"></div>

                <div
                    class="relative border border-amber-500/30 rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_60px_rgba(245,158,11,0.4)] bg-[var(--bg-panel)]/95 backdrop-blur-2xl animate-tech">
                    <div class="p-8 border-b border-amber-500/10 flex flex-col items-center text-center gap-5">
                        <div
                            class="w-16 h-16 rounded-full bg-amber-500 text-black flex items-center justify-center shrink-0 shadow-[0_0_30px_rgba(245,158,11,0.4)]">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-amber-500 uppercase tracking-widest mb-2">Conflicto Logístico</h3>
                            <p class="text-[var(--text-primary)] text-[11px] leading-relaxed font-bold">
                                {{ $flightsCount }} vuelos programados colisionan con este cambio de estado.
                            </p>
                        </div>

                        <div class="w-full space-y-2">
                            <button wire:click="handleRedirectToFlights"
                                class="w-full group flex items-center gap-4 p-4 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] hover:border-[var(--neon-amber)] rounded-[15px] transition-all text-left">
                                <div class="w-10 h-10 rounded-lg bg-[var(--neon-amber)]/10 text-[var(--neon-amber)] flex items-center justify-center group-hover:bg-[var(--neon-amber)] group-hover:text-black transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-[var(--text-primary)] uppercase tracking-widest">Reasignar Ahora</p>
                                    <p class="text-[9px] text-[var(--text-secondary)]">Ir al panel de vuelos</p>
                                </div>
                            </button>

                            <button wire:click="handleDelegateToGestor"
                                class="w-full group flex items-center gap-4 p-4 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] hover:border-[var(--neon-emerald)] rounded-[15px] transition-all text-left">
                                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-black transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-[var(--text-primary)] uppercase tracking-widest">Delegar a Gestores</p>
                                    <p class="text-[9px] text-[var(--text-secondary)]">Notificar incidencias</p>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="$set('showConflictModal', false)"
                            class="w-full py-3 text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-all">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
</div>
