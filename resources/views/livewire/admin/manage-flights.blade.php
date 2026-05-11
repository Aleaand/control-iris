<div class="p-6 md:p-8 space-y-6 relative obsidian-bg min-h-screen text-[var(--text-primary)]"
    x-data="{ showScrollTop: false }" @scroll.window="showScrollTop = window.pageYOffset > 300">

    {{-- ══ HEADER ══ --}}
    <div class="flex items-start justify-between border-b border-blue-400/30 pb-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-[0.15em] text-[var(--neon-cyan)] flex items-center gap-3">
                Vuelos
            </h1>
            <p class="font-mono-tech text-[11px] uppercase tracking-widest mt-1 text-[var(--text-secondary)]">
                Gestión de Vuelos Espaciales
            </p>
        </div>
        <div class="flex items-center gap-4">
            @if (session()->has('message'))
                <div
                    class="flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--neon-emerald)]/10 border border-[var(--neon-emerald)]/30">
                    <div class="w-2 h-2 rounded-full bg-[var(--neon-emerald)]"></div>
                    <span
                        class="font-mono-tech text-[10px] text-[var(--neon-emerald)] uppercase tracking-widest">{{ session('message') }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div wire:click="setPresetFilter('today')"
            class="cursor-pointer bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded-[10px] p-4 flex flex-col justify-center items-center shadow-[0_0_15px_rgba(168,85,247,0.05)] relative overflow-hidden group hover:border-[var(--neon-violet)] transition-colors">
            <div
                class="absolute inset-0 bg-[var(--neon-violet)]/5 opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
            <h4
                class="text-[10px] uppercase text-[var(--neon-violet)] font-bold tracking-widest mb-1 pointer-events-none flex items-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full bg-[var(--neon-violet)] animate-pulse"></div> Despegues (Hoy)
            </h4>
            <p class="text-3xl font-bold text-[var(--text-primary)] pointer-events-none">{{ $widgets['today'] ?? 0 }}
            </p>
        </div>

        <div wire:click="setPresetFilter('in_orbit')"
            class="cursor-pointer bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded-[10px] p-4 flex flex-col justify-center items-center shadow-[0_0_15px_rgba(6,182,212,0.05)] relative overflow-hidden group hover:border-[var(--neon-cyan)] transition-colors">
            <div class="absolute inset-0 bg-[var(--neon-cyan)]/5 opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
            <h4
                class="text-[10px] uppercase text-[var(--neon-cyan)] font-bold tracking-widest mb-1 pointer-events-none flex items-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full bg-[var(--neon-cyan)] animate-pulse"></div> En órbita
            </h4>
            <p
                class="text-3xl font-bold text-[var(--neon-cyan)] pointer-events-none drop-shadow-[0_0_8px_rgba(6,182,212,0.5)]">
                {{ $widgets['in_orbit'] ?? 0 }}
            </p>
        </div>

        <div wire:click="setPresetFilter('landed_today')"
            class="cursor-pointer bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded-[10px] p-4 flex flex-col justify-center items-center shadow-[0_0_15px_rgba(16,185,129,0.05)] relative overflow-hidden group hover:border-[var(--neon-emerald)] transition-colors">
            <div
                class="absolute inset-0 bg-[var(--neon-emerald)]/5 opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
            <h4
                class="text-[10px] uppercase text-[var(--neon-emerald)] font-bold tracking-widest mb-1 pointer-events-none flex items-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full bg-[var(--neon-emerald)] animate-pulse"></div>Aterrizajes (Hoy)
            </h4>
            <div class="flex items-center gap-3">
                <p class="text-3xl font-bold text-[var(--neon-emerald)] pointer-events-none">
                    {{ $widgets['landed_today'] ?? 0 }}
                </p>
            </div>
        </div>

        <div wire:click="setPresetFilter('incidents')"
            class="cursor-pointer bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded-[10px] p-4 flex flex-col justify-center items-center shadow-[0_0_15px_rgba(244,63,94,0.05)] relative overflow-hidden group hover:border-[var(--neon-rose)] transition-colors">
            <div class="absolute inset-0 bg-[var(--neon-rose)]/5 opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
            <h4
                class="text-[10px] uppercase text-[var(--neon-rose)] font-bold tracking-widest mb-1 pointer-events-none flex items-center gap-1.5">
                Cancelados</h4>
            <p class="text-3xl font-bold text-[var(--neon-rose)] pointer-events-none">{{ $widgets['incidents'] ?? 0 }}
            </p>
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-y-4 md:gap-8 items-start md:grid-rows-[auto_auto_1fr]">
        <!-- Buscador y Filtro -->
        <div class="md:col-span-3 md:col-start-1 md:row-start-1">
            <div class="tech-card p-4 rounded-[10px] shadow-lg flex flex-col gap-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative w-full md:w-2/3">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live="search" placeholder="Buscar vuelo..."
                            class="tech-input block w-full pl-10 py-3 focus:outline-none text-sm transition-colors rounded-[10px]">
                    </div>

                    <div class="relative w-full md:w-1/3">
                        <input type="date" wire:model.live="dateFilter"
                            class="tech-input block w-full px-3 py-2.5 focus:outline-none sm:text-sm transition-colors rounded-[10px] font-mono text-xs uppercase tracking-widest">
                    </div>

                    <button wire:click="toggleSort"
                        class="border border-[var(--border-glass)] px-4 py-2 text-xs font-mono-tech uppercase tracking-widest flex items-center gap-2 transition-colors w-full sm:w-auto justify-center rounded-lg hover:bg-[var(--tech-hover-bg)]"
                        style="background: var(--tech-input-bg); color: var(--text-primary)">
                        @if($sortDir === 'asc')
                            <svg wire:key="icon-sort-asc" class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                            </svg>
                        @else
                            <svg wire:key="icon-sort-desc" class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                            </svg>
                        @endif
                        <span wire:loading.remove
                            wire:target="toggleSort">{{ $sortDir === 'desc' ? 'Recientes' : 'Antiguos' }}</span>
                        <span wire:loading wire:target="toggleSort">Ordenando...</span>
                    </button>

                    <select wire:model.live="statusFilter"
                        class="tech-input w-full md:w-auto flex-1 py-3 px-3 text-sm focus:outline-none transition-colors rounded-[10px] cursor-pointer appearance-none">
                        <option value="all">Todos los Estados</option>
                        <option value="scheduled">Programados</option>
                        <option value="in_orbit">En Órbita</option>
                        <option value="landed">Aterrizados</option>
                        <option value="cancelled">Cancelados</option>
                    </select>
                </div>

                @if ($search !== '' || $periodFilter !== 'all' || $statusFilter !== 'all' || $dateFilter !== '')
                    <div class="flex justify-start px-2">
                        <button wire:click="resetFilters"
                            class="text-[10px] uppercase font-bold tracking-widest text-[var(--text-secondary)] hover:text-[var(--neon-rose)] transition-colors flex items-center gap-1.5 group">
                            Ver Todos
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Columna Derecha: Formulario -->
        <div class="md:col-span-2 md:col-start-4 md:row-start-1 md:row-span-3 mt-4 md:mt-0"
            x-data="{ expanded: window.innerWidth >= 768 }"
            @resize.window="if(window.innerWidth >= 768) expanded = true">

            @if($errors->has('starship_id'))
                <div
                    class="bg-[var(--neon-rose)]/10 border border-[var(--neon-rose)]/30 text-[var(--neon-rose)] px-4 py-3 rounded-[10px] mb-4 text-xs font-bold uppercase tracking-wide flex shadow-[0_0_15px_rgba(244,63,94,0.1)]">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    {{ current($errors->get('starship_id')) }}
                </div>
            @endif

            @if($errors->has('booked_passengers'))
                <div
                    class="bg-[var(--neon-amber)]/10 border border-[var(--neon-amber)]/30 text-[var(--neon-amber)] px-4 py-3 rounded-[10px] mb-4 text-xs font-bold uppercase tracking-wide flex shadow-[0_0_15px_rgba(245,158,11,0.1)]">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    {{ current($errors->get('booked_passengers')) }}
                </div>
            @endif

            <div
                class="tech-card p-6 rounded-xl transition-all duration-500 relative overflow-hidden {{ $isEditing ? 'border-2 border-[var(--neon-amber)] shadow-[0_0_30px_rgba(245,158,11,0.1)]' : 'border-2 border-[var(--border-glass)]' }}">
                @if($isEditing)
                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[var(--neon-amber)]/0 via-[var(--neon-amber)] to-[var(--neon-amber)]/0">
                    </div>
                @endif

                <!-- Mobile Toggle -->
                <button @click="expanded = !expanded" type="button"
                    class="w-full md:hidden flex justify-between items-center pb-4 mb-4 border-b border-[var(--border-glass)] font-black uppercase tracking-widest text-sm transition-colors {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--neon-cyan)]' }}">
                    <span x-text="expanded ? 'Ocultar' : 'Nuevo Vuelo'"></span>
                    <svg :class="expanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="expanded" x-transition>
                    <div
                        class="flex justify-between items-center mb-6 border-b border-[var(--border-glass)] pb-4 hidden md:flex">
                        <h3
                            class="text-sm font-black uppercase tracking-[0.1em] flex items-center gap-2 {{ $isEditing ? 'text-amber-400' : 'text-blue-400' }}">
                            @if($isEditing)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Editando Vuelo @if($isReturnFlight) <br><span class="text-xs">Vuelo de Retorno</span>
                                @endif
                            @else
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Nuevo Vuelo
                            @endif
                        </h3>

                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode"
                                class="text-[10px] uppercase font-bold tracking-widest bg-[var(--tech-input-bg)] hover:bg-[var(--text-primary)] hover:text-[var(--bg-obsidian)] text-[var(--text-secondary)] px-2.5 py-1.5 transition-colors border border-[var(--border-glass)] rounded-[5px] flex items-center gap-1.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Nuevo Vuelo
                            </button>
                        @endif
                    </div>

                    {{-- ── Edit context: sibling flight badge ──────────────────── --}}
                    @if($isEditing && $siblingFlightId)
                        <div
                            class="mb-4 px-3 py-2 rounded-[8px] flex items-center justify-between {{ $isReturnFlight ? 'bg-[var(--neon-violet)]/10 border border-[var(--neon-violet)]/30' : 'bg-[var(--neon-cyan)]/10 border border-[var(--neon-cyan)]/30' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 {{ $isReturnFlight ? 'text-[var(--neon-violet)]' : 'text-[var(--neon-cyan)]' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                    </path>
                                </svg>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest {{ $isReturnFlight ? 'text-[var(--neon-violet)]' : 'text-[var(--neon-cyan)]' }}">
                                    {{ $isReturnFlight ? 'Ida asociada:' : 'Retorno asociado:' }}
                                </span>
                            </div>
                            <button type="button" wire:click="edit({{ $siblingFlightId }})"
                                class="text-[9px] font-mono font-bold px-2 py-1 rounded-[5px] border transition-colors {{ $isReturnFlight ? 'text-[var(--neon-violet)] border-[var(--neon-violet)]/30 hover:bg-[var(--neon-violet)]/20' : 'text-[var(--neon-cyan)] border-[var(--neon-cyan)]/30 hover:bg-[var(--neon-cyan)]/20' }}">
                                {{ $isReturnFlight ? \Illuminate\Support\Str::beforeLast($flight_code, '-RET') : $flight_code . '-RET' }}
                            </button>
                        </div>
                    @endif

                    @if($isEditing && $isReturnFlight && $siblingArrivalDate)
                        <div
                            class="mb-4 px-3 py-2 bg-[var(--neon-amber)]/10 border border-[var(--neon-amber)]/30 rounded-[8px] flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 text-[var(--neon-amber)] shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                            <p class="text-[9px] text-[var(--neon-amber)] font-medium leading-relaxed">
                                Fechas deben ser posteriores al aterrizaje del vuelo de ida
                                <strong
                                    class="text-[var(--neon-amber)]">{{ \Carbon\Carbon::parse($siblingArrivalDate)->format('d M Y, H:i') }}</strong>
                                con mínimo 24h de margen.
                            </p>
                        </div>
                    @endif

                    <form wire:submit.prevent="confirmSave" class="space-y-4">
                        @if($isEditingFromReturn)
                            <div
                                class="mb-4 p-3 bg-[var(--neon-amber)]/10 border border-[var(--neon-amber)]/30 rounded-[12px] flex items-start gap-3 animate-tech">
                                <div class="p-1.5 bg-[var(--neon-amber)]/20 rounded-lg">
                                    <svg class="w-4 h-4 text-[var(--neon-amber)]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p
                                        class="text-[10px] font-black text-[var(--neon-amber)] uppercase tracking-widest text-left">
                                        Estás editando un Viaje de Retorno</p>
                                    <p class="text-[9px] text-[var(--neon-amber)]/70 mt-0.5 leading-relaxed text-left">
                                        Has seleccionado un vuelo de vuelta. <span
                                            class="font-bold text-[var(--neon-amber)] italic">Dato clave:</span> desde este
                                        panel
                                        puedes modificar tanto el vuelo de <span
                                            class="text-[var(--neon-amber)] underline">Ida</span>
                                        como el de <span class="text-[var(--neon-amber)] underline">Vuelta</span>.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label
                                class="block text-[10px] font-bold text-[var(--text-secondary)] mb-1 uppercase tracking-widest flex items-center gap-1.5 pl-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                ID DE VUELO
                            </label>
                            <input type="text" value="{{ $flight_code }}" readonly
                                class="tech-input w-full px-3 py-2 text-[var(--text-secondary)] font-mono text-sm cursor-not-allowed outline-none rounded-[10px]">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                Asignar Nave <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model.live="starship_id" required
                                class="tech-input w-full px-3 py-2.5 focus:outline-none transition-colors text-sm rounded-[10px] appearance-none cursor-pointer {{ $isEditing ? 'border-[var(--neon-amber)]' : '' }}">
                                <option value="">--- Seleccionar Nave ---</option>
                                @foreach($starships as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} (Pasj.:
                                        {{ $s->general_capacity + $s->vip_capacity }} | Trip.: {{ $s->crew_capacity }})
                                    </option>
                                @endforeach
                            </select>

                            @if($formattedShipInfo)
                                <div
                                    class="mt-2 px-3 py-1.5 bg-[var(--neon-cyan)]/10 border border-[var(--neon-cyan)]/30 rounded-[8px] flex items-center gap-2">
                                    <div
                                        class="w-1.5 h-1.5 rounded-full {{ $shipStatus === 'active' ? 'bg-[var(--neon-emerald)] shadow-[0_0_8px_rgba(10,185,129,0.5)]' : 'bg-[var(--neon-amber)] animate-pulse' }}">
                                    </div>
                                    <span class="text-[10px] font-bold text-[var(--neon-cyan)] uppercase tracking-widest">
                                        {{ $formattedShipInfo }}
                                    </span>
                                </div>
                            @endif

                            @error('starship_id') <span
                                class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Origen <span class="text-rose-500">*</span>
                                </label>
                                <select wire:model.live="origin_id" required
                                    class="tech-input w-full px-3 py-2.5 focus:outline-none transition-colors text-sm rounded-[10px] appearance-none cursor-pointer {{ $isEditing ? 'border-[var(--neon-amber)]' : '' }}">
                                    <option value="">--- Origen ---</option>
                                    @foreach($destinations as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                                @error('origin_id') <span
                                    class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Destino <span class="text-rose-500">*</span>
                                </label>
                                <select wire:model.live="destination_id" required
                                    class="tech-input w-full px-3 py-2.5 focus:outline-none transition-colors text-sm rounded-[10px] appearance-none cursor-pointer {{ $isEditing ? 'border-[var(--neon-amber)]' : '' }}">
                                    <option value="">--- Destino ---</option>
                                    @foreach($destinations as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }} ({{ number_format($d->distance_au, 2) }}
                                            AU)
                                        </option>
                                    @endforeach
                                </select>
                                @error('destination_id') <span
                                    class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Distancia AU (editable) --}}
                        <div>
                            <label
                                class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                Distancia <span class="text-rose-500">*</span>
                                <div x-data="{ open: false }" class="relative flex items-center">
                                    <span @mouseenter="open = true" @mouseleave="open = false"
                                        class="cursor-help border-b border-dotted border-[var(--text-secondary)] pb-0.5 text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
                                        (AU)
                                    </span>

                                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-40 p-2 bg-[var(--bg-panel)] border border-[var(--border-glass)] text-[var(--text-primary)] text-[9px] leading-tight text-center rounded-md shadow-2xl backdrop-blur-sm z-50 pointer-events-none">
                                        <span class="font-bold text-[var(--neon-cyan)]">UNIDADES ASTRONÓMICAS</span><br>
                                        1 AU ≈ 149.6 millones de km
                                        <div
                                            class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-[var(--border-glass)]">
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <input type="number" step="any" min="0.01" required
                                wire:model.live="au_distance"
                                class="tech-input w-full px-3 py-2.5 font-mono focus:outline-none transition-colors text-sm rounded-[10px] h-[42px] {{ $isEditing ? 'border-[var(--neon-amber)]' : 'text-[var(--neon-cyan)]' }}">
                            @error('au_distance') <span
                                class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Despegue <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" wire:model.live="departure_date" required
                                    class="tech-input w-full h-[42px] px-3 font-mono focus:outline-none transition-colors text-xs rounded-[10px] {{ $isEditing ? 'border-[var(--neon-amber)]' : '' }}">
                                @error('departure_date') <span
                                    class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                                @enderror
                            </div>

                            @php
                                $arrivalDeviates = $suggested_arrival_date && $arrival_date && $arrival_date !== $suggested_arrival_date;
                            @endphp
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $arrivalDeviates ? 'text-[var(--neon-amber)]' : 'text-[var(--neon-emerald)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Aterrizaje <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" wire:model.live="arrival_date" required
                                    class="tech-input w-full h-[42px] px-3 font-mono focus:outline-none transition-colors text-xs rounded-[10px] {{ $arrivalDeviates ? 'border-[var(--neon-amber)] text-[var(--neon-amber)]' : 'text-[var(--neon-emerald)]' }}">

                                @if($arrivalDeviates)
                                    <span
                                        class="text-[var(--neon-amber)] text-[9px] font-bold mt-1 block uppercase italic">Modificar</span>
                                @endif
                                @error('arrival_date') <span
                                    class="text-red-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- ── Tasa Nova + Tripulación ────────────────────────────── --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-[var(--neon-cyan)] mb-1 uppercase tracking-widest flex items-center gap-1.5 pl-2">
                                    Tasa Nova <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" wire:model.live="base_price" required
                                    class="tech-input w-full border-[var(--neon-cyan)]/50 focus:border-[var(--neon-cyan)] text-[var(--neon-cyan)] px-3 py-2.5 font-mono focus:outline-none transition-colors text-sm rounded-[10px] h-[42px]">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text(--text-secondary)]' }} mb-1 uppercase tracking-widest pl-2">
                                    Tripulación
                                </label>
                                <input type="number" min="0" wire:model.live="booked_passengers"
                                    class="tech-input w-full px-3 py-2.5 font-mono focus:outline-none transition-colors text-sm rounded-[10px] h-[42px] {{ $isEditing ? 'border-[var(--neon-amber)]' : '' }}">
                            </div>
                        </div>
                        @if($shipLocationName)
                            <div
                                class="border border-[var(--border-glass)] rounded-[10px] p-3 space-y-3 bg-[var(--bg-panel)]/50">
                                <p
                                    class="text-[9px] font-bold text-[var(--neon-cyan)]/70 uppercase tracking-[0.2em] flex items-center gap-1.5">
                                    Costos de Tripulación
                                </p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label
                                            class="block text-[9px] font-bold text-[var(--text-secondary)] mb-1 uppercase tracking-widest pl-1">Trip.
                                            €/hora</label>
                                        <input type="number" step="0.01" min="0" wire:model.live="crew_hourly_rate"
                                            class="tech-input w-full px-2 py-1.5 font-mono focus:outline-none text-xs rounded-[8px]">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[9px] font-bold text-[var(--text-secondary)] mb-1 uppercase tracking-widest pl-1">Espera
                                            €/día</label>
                                        <input type="number" step="0.01" min="0" wire:model.live="crew_daily_rate"
                                            class="tech-input w-full px-2 py-1.5 font-mono focus:outline-none text-xs rounded-[8px]">
                                    </div>
                                </div>
                            </div>

                            <div
                                class="border border-[var(--border-glass)] rounded-[10px] p-3 space-y-3 bg-[var(--bg-panel)]/50">
                                <p
                                    class="text-[9px] font-bold text-[var(--neon-amber)]/70 uppercase tracking-[0.2em] flex items-center gap-1.5">
                                    Tasas de Puerto Espacial (Ida)
                                </p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label
                                            class="block text-[9px] font-bold text-[var(--text-secondary)] mb-1 uppercase tracking-widest pl-1">Despegue (€)</label>
                                        <input type="number" step="1" min="0" wire:model.live="launch_cost_earth"
                                            class="tech-input w-full px-2 py-1.5 font-mono focus:outline-none text-xs rounded-[8px]">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[9px] font-bold text-[var(--text-secondary)] mb-1 uppercase tracking-widest pl-1">Aterrizaje (€)</label>
                                        <input type="number" step="1" min="0" wire:model.live="landing_cost_planet"
                                            class="tech-input w-full px-2 py-1.5 font-mono focus:outline-none text-xs rounded-[8px]">
                                    </div>
                                </div>
                            </div>

                            @if($showReturnForm)
                            <div
                                class="border border-[var(--border-glass)] rounded-[10px] p-3 space-y-3 bg-[var(--bg-panel)]/50">
                                <p
                                    class="text-[9px] font-bold text-[var(--neon-violet)]/70 uppercase tracking-[0.2em] flex items-center gap-1.5">
                                    Tasas de Puerto Espacial (Vuelta)
                                </p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label
                                            class="block text-[9px] font-bold text-[var(--text-secondary)] mb-1 uppercase tracking-widest pl-1">Despegue (€)</label>
                                        <input type="number" step="1" min="0" wire:model.live="launch_cost_planet"
                                            class="tech-input w-full px-2 py-1.5 font-mono focus:outline-none text-xs rounded-[8px]">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[9px] font-bold text-[var(--text-secondary)] mb-1 uppercase tracking-widest pl-1">Aterrizaje (€)</label>
                                        <input type="number" step="1" min="0" wire:model.live="landing_cost_earth"
                                            class="tech-input w-full px-2 py-1.5 font-mono focus:outline-none text-xs rounded-[8px]">
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endif

                        <div>
                            <label
                                class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                Estado actual
                            </label>
                            <select wire:model.live="status"
                                class="tech-input w-full px-3 py-2.5 focus:outline-none transition-colors text-sm rounded-[10px] appearance-none cursor-pointer h-[42px] {{ $isEditing ? 'border-[var(--neon-amber)]' : '' }}">
                                <option value="scheduled">Programado - En Base</option>
                                <option value="in_orbit">En Órbita</option>
                                <option value="landed">Aterrizado</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                            @error('status') <span
                            class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        @if($rrhh_alert_needed && $booked_passengers > 0)
                            <div
                                class="flex items-start gap-2 px-3 py-2.5 rounded-[10px] bg-[var(--neon-amber)]/10 border border-[var(--neon-amber)]/30">
                                <svg class="w-3.5 h-3.5 text-[var(--neon-amber)] mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                </svg>
                                <div>
                                    <p class="text-[9px] font-black text-[var(--neon-amber)] uppercase tracking-widest">
                                        Alerta RRHH
                                    </p>
                                    <p class="text-[9px] text-[var(--neon-amber)]/80 mt-0.5">
                                        {{ $booked_passengers }} tripulante(s) sin vuelo de retorno programado. Se
                                        notificará a RRHH para gestionar el vuelo de vuelta. Se calculan <span
                                            class="font-bold">7 días de espera</span> por defecto.
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($shipLocationName && $au_distance > 0)
                            <div class="border-t border-[var(--border-glass)] pt-3">

                                <button type="button" wire:click="toggleReturnForm"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-[10px] border transition-all text-xs font-bold uppercase tracking-widest @if($showReturnForm) bg-[var(--neon-violet)]/10 border-[var(--neon-violet)]/30 text-[var(--neon-violet)] @else bg-[var(--tech-input-bg)] border-[var(--border-glass)] text-[var(--text-secondary)] hover:border-[var(--neon-violet)]/50 hover:text-[var(--neon-violet)] @endif">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
                                        {{ $showReturnForm ? 'Vuelo de Retorno Activo' : 'Añadir Vuelo de Retorno' }}
                                    </span>
                                    <svg class="w-3.5 h-3.5 transition-transform @if($showReturnForm) rotate-180 @endif"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                @if($showReturnForm)
                                    <div
                                        class="mt-2 p-3 bg-[var(--neon-violet)]/5 border border-[var(--neon-violet)]/20 rounded-[10px] space-y-2 animate-tech">
                                        <p class="text-[9px] text-[var(--neon-violet)]/70 uppercase tracking-widest font-bold">
                                            Datos Vuelo de Retorno</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-[9px] font-bold text-[var(--neon-violet)]/70 mb-1 uppercase tracking-widest pl-1">Fecha
                                                    Despegue Retorno <span class="text-rose-500">*</span></label>
                                                <input type="datetime-local" wire:model.live="return_departure_date" required
                                                    class="tech-input w-full h-[36px] px-2 font-mono focus:outline-none text-xs rounded-[8px]"
                                                    @if($siblingArrivalDate) min="{{ $siblingArrivalDate }}" @endif>
                                                @error('return_departure_date') <span
                                                class="text-rose-500 text-[10px] font-bold mt-1 block uppercase italic">{{ $message }}</span>@enderror
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[9px] font-bold text-[var(--neon-violet)]/70 mb-1 uppercase tracking-widest pl-1">Fecha
                                                    Aterrizaje Retorno <span class="text-rose-500">*</span></label>
                                                <input type="datetime-local" wire:model.live="return_arrival_date" required
                                                    class="tech-input w-full h-[36px] px-2 font-mono focus:outline-none text-xs rounded-[8px]">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[9px] font-bold text-[var(--neon-violet)]/70 mb-1 uppercase tracking-widest pl-1">Distancia
                                                    (AU) <span class="text-rose-500">*</span></label>
                                                <input type="number" step="any" min="0.01" required
                                                    wire:model.live="return_au_distance"
                                                    class="tech-input w-full px-2 py-1.5 font-mono h-[36px] focus:outline-none text-xs rounded-[8px]"
                                                    placeholder="0">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[9px] font-bold text-[var(--neon-violet)]/70 mb-1 uppercase tracking-widest pl-1">Tasa
                                                    Nova (Vuelta) <span class="text-rose-500">*</span></label>
                                                <input type="number" step="0.01" min="0" wire:model.live="return_base_price"
                                                    required
                                                    class="tech-input w-full px-2 py-1.5 font-mono h-[36px] focus:outline-none text-xs rounded-[8px]"
                                                    placeholder="{{ number_format($suggested_return_price, 2) }}">
                                            </div>
                                        </div>
                                        @if($waiting_days > 0)
                                            <div
                                                class="flex items-center justify-between text-[10px] bg-[var(--neon-violet)]/10 rounded-[6px] px-2 py-1">
                                                <span class="text-[var(--neon-violet)]/60 uppercase font-bold tracking-widest">Días
                                                    en planeta:</span>
                                                <span
                                                    class="text-[var(--neon-violet)] font-mono font-bold">{{ $waiting_days }}d</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif


                        {{-- Rentabilidad de Vuelos --}}
                        @if($shipLocationName && $au_distance > 0 && $base_price > 0)
                            <div
                                class="rounded-[10px] overflow-hidden border {{ $mission_profitability < 0 ? 'border-[var(--neon-rose)]/50' : 'border-[var(--neon-emerald)]/30' }} bg-[var(--bg-panel)]/50">
                                <div
                                    class="px-3 py-2 border-b {{ $mission_profitability < 0 ? 'border-[var(--neon-rose)]/30 bg-[var(--neon-rose)]/5' : 'border-[var(--neon-emerald)]/20 bg-[var(--neon-emerald)]/5' }} flex items-center justify-between">
                                    <span
                                        class="text-[9px] font-bold {{ $mission_profitability < 0 ? 'text-[var(--neon-rose)]' : 'text-[var(--neon-emerald)]' }} uppercase tracking-[0.2em] flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                        Rentabilidad de Vuelo
                                    </span>
                                    <span
                                        class="text-[10px] font-mono font-black {{ $mission_profitability < 0 ? 'text-[var(--neon-rose)]' : 'text-[var(--neon-emerald)]' }}">
                                        {{ number_format($mission_profitability, 0) }} €
                                    </span>
                                </div>

                                <div class="p-3 space-y-1 text-[10px]">
                                    {{-- Ingresos --}}
                                    <div
                                        class="flex justify-between text-[var(--text-secondary)] border-b border-[var(--border-glass)] pb-1 mb-1">
                                        <span
                                            class="font-bold uppercase tracking-widest text-[var(--neon-cyan)]/70">Ingresos</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-[var(--text-secondary)]">Ingresos al 80% de capacidad vuelo de
                                            ida</span>
                                        <span
                                            class="font-mono text-[var(--neon-cyan)]">{{ number_format($revenue_outbound, 0) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-[var(--text-secondary)] pl-2 text-[9px]">- Ingreso de un puesto
                                            nova</span>
                                        <span
                                            class="font-mono text-[var(--neon-cyan)]/70 text-[9px]">{{ number_format($nova_price, 2) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-[var(--text-secondary)] pl-2 text-[9px]">- Ingreso de un super
                                            nova</span>
                                        <span
                                            class="font-mono text-[var(--neon-cyan)]/70 text-[9px]">{{ number_format($supernova_price, 2) }}
                                            €</span>
                                    </div>
                                    @if($showReturnForm && $return_revenue_total > 0)
                                        <div class="flex justify-between mt-1">
                                            <span class="text-[var(--text-secondary)]">Ingresos al 80% de capacidad vuelo de
                                                vuelta</span>
                                            <span
                                                class="font-mono text-[var(--neon-violet)]">{{ number_format($return_revenue_total, 0) }}
                                                €</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-[var(--text-secondary)] pl-2 text-[9px]">- Ingreso de un puesto
                                                nova</span>
                                            <span
                                                class="font-mono text-[var(--neon-violet)]/70 text-[9px]">{{ number_format($return_nova_price, 2) }}
                                                €</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-[var(--text-secondary)] pl-2 text-[9px]">- Ingreso de un super
                                                nova</span>
                                            <span
                                                class="font-mono text-[var(--neon-violet)]/70 text-[9px]">{{ number_format($return_supernova_price, 2) }}
                                                €</span>
                                        </div>
                                    @endif
                                    <div
                                        class="flex justify-between font-bold border-t border-[var(--border-glass)] pt-1 mt-1">
                                        <span class="text-[var(--text-primary)] uppercase">Total Ingresos</span>
                                        <span
                                            class="font-mono text-[var(--neon-cyan)]">{{ number_format($mission_total_revenue, 0) }}
                                            €</span>
                                    </div>

                                    {{-- Costes --}}
                                    <div
                                        class="flex justify-between text-[var(--text-secondary)] border-t border-[var(--border-glass)] pt-2 mt-2 mb-1">
                                        <span
                                            class="font-bold uppercase tracking-widest text-[var(--neon-rose)]/70">Costes</span>
                                    </div>

                                    {{-- Gastos Ida --}}
                                    <div
                                        class="text-[9px] text-[var(--text-secondary)] font-bold mb-1 border-b border-[var(--border-glass)]">
                                        VUELO DE IDA</div>
                                    <div class="flex justify-between">
                                        <span class="text-[var(--text-secondary)] pl-2">Gastos despegue</span>
                                        <span
                                            class="font-mono text-[var(--neon-rose)]/70">-{{ number_format((float) $launch_cost_earth, 0) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-[var(--text-secondary)] pl-2">Gastos aterrizaje</span>
                                        <span
                                            class="font-mono text-[var(--neon-rose)]/70">-{{ number_format((float) $landing_cost_planet, 0) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-[var(--text-secondary)] pl-2">Gastos de empleados (horas)</span>
                                        <span
                                            class="font-mono text-[var(--neon-rose)]/70">-{{ number_format($crew_cost_outbound, 0) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-[var(--text-secondary)] pl-2">Gastos de AU</span>
                                        <span
                                            class="font-mono text-[var(--neon-rose)]/70">-{{ number_format($ship_outbound_cost, 0) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between font-bold mt-1">
                                        <span class="text-[var(--text-primary)] pl-1">Gastos Totales ida</span>
                                        <span
                                            class="font-mono text-[var(--neon-rose)]">-{{ number_format($outbound_total_cost, 0) }}
                                            €</span>
                                    </div>

                                    @if($showReturnForm)
                                        {{-- Gastos Vuelta --}}
                                        <div
                                            class="text-[9px] text-[var(--text-secondary)] font-bold mt-2 mb-1 border-b border-[var(--border-glass)]">
                                            VUELO DE VUELTA</div>
                                        <div class="flex justify-between">
                                            <span class="text-[var(--text-secondary)] pl-2">Gastos despegue</span>
                                            <span
                                                class="font-mono text-[var(--neon-rose)]/70">-{{ number_format((float) $launch_cost_planet, 0) }}
                                                €</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-[var(--text-secondary)] pl-2">Gastos aterrizaje</span>
                                            <span
                                                class="font-mono text-[var(--neon-rose)]/70">-{{ number_format((float) $landing_cost_earth, 0) }}
                                                €</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-[var(--text-secondary)] pl-2">Gastos de empleados</span>
                                            <span
                                                class="font-mono text-[var(--neon-rose)]/70">-{{ number_format($crew_cost_return + $crew_cost_waiting, 0) }}
                                                €</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-[var(--text-secondary)] pl-2">Gastos de AU</span>
                                            <span
                                                class="font-mono text-[var(--neon-rose)]/70">-{{ number_format($ship_return_cost, 0) }}
                                                €</span>
                                        </div>
                                        <div class="flex justify-between font-bold mt-1">
                                            <span class="text-[var(--text-primary)] pl-1">Gastos Totales vuelta</span>
                                            <span
                                                class="font-mono text-[var(--neon-rose)]">-{{ number_format($return_total_cost, 0) }}
                                                €</span>
                                        </div>
                                    @endif

                                    <div
                                        class="flex justify-between font-bold border-t border-[var(--border-glass)] pt-2 mt-2">
                                        <span class="text-[var(--text-primary)]">Gastos Totales</span>
                                        <span
                                            class="font-mono text-[var(--neon-rose)]">-{{ number_format($mission_total_cost, 0) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between font-bold">
                                        <span class="text-[var(--text-primary)]">Ingresos Totales</span>
                                        <span
                                            class="font-mono text-[var(--neon-cyan)]">{{ number_format($mission_total_revenue, 0) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between font-bold">
                                        <span class="text-[var(--text-primary)]">Ganancias Reales</span>
                                        <span
                                            class="font-mono {{ $mission_profitability < 0 ? 'text-[var(--neon-rose)]' : 'text-[var(--neon-emerald)]' }}">{{ number_format($mission_profitability, 0) }}
                                            €</span>
                                    </div>

                                    {{-- Resultado Rentabilidad --}}
                                    <div
                                        class="mt-2 pt-2 border-t-2 {{ $mission_profitability < 0 ? 'border-[var(--neon-rose)]/40' : 'border-[var(--neon-emerald)]/30' }}">
                                        <div class="flex justify-between items-center">
                                            <span
                                                class="text-[var(--text-secondary)] uppercase font-bold tracking-widest">ROI
                                                Vuelo:</span>
                                            <span
                                                class="font-mono-tech text-xs font-black {{ $mission_profitability < 0 ? 'text-[var(--neon-rose)]' : 'text-[var(--neon-emerald)]' }}">
                                                {{ number_format(($mission_total_revenue > 0 && $mission_total_cost > 0) ? ($mission_profitability / $mission_total_cost) * 100 : 0, 1) }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="pt-4 mt-2 border-t border-[var(--border-glass)]">
                            <button type="submit"
                                class="w-full font-mono-tech font-bold uppercase tracking-widest py-3 px-4 transition-colors text-[11px] rounded-lg border flex items-center justify-center gap-2 {{ $isEditing ? 'bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-black border-amber-500/50' : 'bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 dark:text-emerald-400 hover:text-black border-emerald-500/50' }}">
                                {{ $isEditing ? 'Actualizar Vuelo' : 'Registrar Nuevo Vuelo' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista de Resultados -->
        <div class="md:col-span-3 md:col-start-1 md:row-start-2 mt-4 md:mt-0">
            <div class="tech-card rounded-[10px] shadow-lg overflow-hidden relative">
                <ul class="divide-y divide-[var(--border-glass)]">
                    @forelse($flights as $flight)
                        @php
                            $badgeColor = 'bg-[var(--tech-input-bg)] text-[var(--text-secondary)] border-[var(--border-glass)]';
                            $statusLabel = 'Desconocido';

                            if ($flight->status === 'in_orbit') {
                                $borderColor = 'border-[var(--neon-cyan)]';
                                $badgeColor = 'bg-[var(--neon-cyan)]/10 text-[var(--neon-cyan)] border-[var(--neon-cyan)]/30';
                                $statusLabel = 'En Órbita';
                            } elseif ($flight->status === 'scheduled') {
                                $borderColor = 'border-[var(--neon-violet)]';
                                $badgeColor = 'bg-[var(--neon-violet)]/10 text-[var(--neon-violet)] border-[var(--neon-violet)]/30';
                                $statusLabel = 'Programado';
                            } elseif ($flight->status === 'cancelled') {
                                $borderColor = 'border-[var(--neon-rose)]';
                                $borderAccent = 'bg-[var(--neon-rose)]';
                                $badgeColor = 'bg-[var(--neon-rose)]/10 text-[var(--neon-rose)] border-[var(--neon-rose)]/30';
                                $statusLabel = 'Cancelado';
                            } elseif ($flight->status === 'landed') {
                                $borderColor = 'border-[var(--neon-emerald)]';
                                $borderAccent = 'bg-[var(--neon-emerald)]';
                                $badgeColor = 'bg-[var(--neon-emerald)]/10 text-[var(--neon-emerald)] border-[var(--neon-emerald)]/30';
                                $statusLabel = 'Aterrizado';
                            } 
                        @endphp

                        <li wire:key="flight-{{ $flight->id }}"
                            class="p-5 hover:bg-[var(--tech-hover-bg)] transition-all flex flex-col md:flex-row justify-between md:items-center gap-6 group relative bg-[var(--bg-panel)]/40 overflow-hidden">

                            <div
                                class="absolute inset-y-0 left-0 w-1 bg-[var(--neon-cyan)] transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300">
                            </div>

                            <div class="flex-1 cursor-pointer space-y-4" wire:click="viewDetails({{ $flight->id }})">
                                <!-- Cabecera: ID y Estado -->
                                <div class="flex items-center gap-3">
                                    <span
                                        class="text-[10px] font-mono-tech text-[var(--neon-cyan)] bg-[var(--neon-cyan)]/10 px-2 py-0.5 rounded border border-[var(--border-glass)]">
                                        VUELO: {{ $flight->flight_code }} | NAVE:
                                        {{ str_pad($flight->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span
                                        class="text-[10px] font-mono-tech px-2 py-0.5 rounded border {{ $badgeColor }} {{ $borderColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <!-- Cuerpo: Ruta y Horarios -->
                                <div class="flex flex-col lg:flex-row lg:items-center gap-4 lg:gap-12">
                                    <div class="space-y-1">
                                        <h4
                                            class="text-lg font-black text-[var(--text-primary)] tracking-wide uppercase flex items-center gap-3 group-hover:text-[var(--neon-cyan)] transition-colors duration-300">
                                            {{ optional($flight->origin)->name ?? '---' }}
                                            <svg class="w-5 h-5 text-[var(--neon-cyan)]/40 group-hover:translate-x-1 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                            {{ optional($flight->destination)->name ?? 'DESTINO BORRADO' }}
                                        </h4>
                                        <div
                                            class="flex flex-wrap items-center gap-4 text-[11px] font-mono-tech uppercase tracking-tighter text-[var(--text-secondary)]">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[var(--neon-cyan)]/60 font-bold">Despegue:</span>
                                                <span
                                                    class="text-[var(--text-primary)]">{{ \Carbon\Carbon::parse($flight->departure_date)->format('d M, H:i') }}
                                                    h</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[var(--neon-emerald)]/60 font-bold">Aterrizaje:</span>
                                                <span
                                                    class="text-[var(--text-primary)]">{{ \Carbon\Carbon::parse($flight->arrival_date)->format('d M, H:i') }}
                                                    h</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex-col gap-2 shrink-0 border-t border-[var(--border-glass)] sm:border-0 pt-4 sm:pt-0">
                                <button type="button" wire:click="edit({{ $flight->id }})"
                                    @click="expanded = true; window.scrollTo({top: 0, behavior: 'smooth'})"
                                    class="p-2.5 rounded-lg border border-amber-500/30 text-amber-500 hover:bg-amber-500 hover:text-black transition-colors"
                                    title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button type="button" wire:click="confirmDelete({{ $flight->id }})"
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
                        <div class="p-16 text-center text-[var(--text-secondary)]">
                            <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <p class="font-bold uppercase tracking-widest text-sm mb-1 text-[var(--text-secondary)]">Sin
                                Vuelos</p>
                            <p class="text-[10px] uppercase tracking-tighter opacity-60">No se encontraron expedientes con
                                los parámetros actuales.</p>
                        </div>
                    @endforelse
                </ul>

                @if($flights->hasPages())
                    <div class="px-6 py-4 bg-black/20 border-t border-[var(--border-glass)]">
                        {{ $flights->links('vendor.livewire.simple-tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div> {{-- Cierre del Grid Principal (83) --}}

    @if($showDetailsModal && $selectedFlight)
        @php
            $start = \Carbon\Carbon::parse($selectedFlight->departure_date);
            $end = \Carbon\Carbon::parse($selectedFlight->arrival_date);
            $diff = $start->diff($end);
            $duration = ($diff->days > 0 ? $diff->days . 'd ' : '') . $diff->h . 'h ' . $diff->i . 'm';

            $st = $selectedFlight->status;
            $stColor = match ($st) {
                'in_orbit' => 'var(--neon-cyan)',
                'scheduled' => 'var(--neon-violet)',
                'landed' => 'var(--neon-emerald)',
                'cancelled' => 'var(--neon-rose)',
                default => 'var(--text-secondary)'
            };
            $stLabel = match ($st) {
                'in_orbit' => 'Misión Activa',
                'scheduled' => 'Programado',
                'landed' => 'Completado',
                'cancelled' => 'Cancelado',
                default => $st
            };

            $novaMult = \App\Models\PriceLog::getCurrentPrice('multiplier_nova') ?: 1.0;
            $supernovaMult = \App\Models\PriceLog::getCurrentPrice('multiplier_supernova') ?: 2.5;
            $novaPrice = $selectedFlight->base_price * $novaMult;
            $supernovaPrice = $selectedFlight->base_price * $supernovaMult;

            $launchCost = (float) $selectedFlight->launch_cost_earth;
            $landingCost = (float) $selectedFlight->landing_cost_planet;
            $flightHours = ceil((float) $selectedFlight->au_distance * (float) $selectedFlight->mission_speed_au);
            $crewCost = (int) $selectedFlight->booked_passengers * (float) $selectedFlight->crew_hourly_rate * $flightHours;
            $auCost = max(0, (float) $selectedFlight->operational_cost - ($launchCost + $landingCost + $crewCost));
        @endphp

        <div class="fixed inset-0 z-[600] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-md" wire:click="$set('showDetailsModal', false)"></div>

            <div
                class="relative w-full max-w-2xl bg-[var(--bg-obsidian)] border border-white/10 rounded-2xl overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] animate-tech max-h-[90vh] flex flex-col">

                <div
                    class="p-6 border-b border-white/5 flex justify-between items-center bg-gradient-to-r from-[var(--neon-cyan)]/5 to-transparent shrink-0">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-[var(--neon-cyan)]/10 border border-[var(--neon-cyan)]/30 flex items-center justify-center text-[var(--neon-cyan)] shadow-[0_0_15px_rgba(6,182,212,0.2)]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 2L9 7V17L12 22L15 17V7L12 2Z M9 13H5L3 17H9 M15 13H19L21 17H15 M12 7V11" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[var(--neon-cyan)] mb-0.5">
                                Información de Vuelo</p>
                            <h2 class="text-2xl font-black text-[var(--text-primary)] tracking-tighter uppercase italic">
                                {{ $selectedFlight->flight_code }}
                            </h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <span
                            class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full border border-current/20"
                            style="color: {{ $stColor }}; background: {{ $stColor }}10;">
                            {{ $stLabel }}
                        </span>
                        <p class="text-[9px] font-mono text-[var(--text-secondary)] mt-2 uppercase tracking-tighter">ID:
                            {{ str_pad($selectedFlight->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto space-y-8 custom-scrollbar">
                    <div class="relative py-4">
                        <div
                            class="absolute top-1/2 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-[var(--border-glass)] to-transparent -translate-y-1/2">
                        </div>
                        <div
                            class="absolute top-1/2 left-[15%] right-[15%] h-[2px] bg-gradient-to-r from-[var(--neon-cyan)]/0 via-[var(--neon-cyan)] to-[var(--neon-cyan)]/0 -translate-y-1/2 shadow-[0_0_15px_var(--neon-cyan)]">
                        </div>

                        <div class="flex justify-between items-center relative z-10">
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-12 h-12 rounded-full bg-[var(--bg-panel)] border-2 border-[var(--neon-cyan)] shadow-[0_0_20px_rgba(6,182,212,0.3)] flex items-center justify-center">
                                    <div class="w-3 h-3 rounded-full bg-[var(--neon-cyan)] animate-pulse"></div>
                                </div>
                                <div class="text-center mt-3">
                                    <p class="text-[8px] font-black text-[var(--text-secondary)] uppercase tracking-widest">
                                        Origen</p>
                                    <p class="text-xs font-black text-[var(--text-primary)] uppercase">
                                        {{ optional($selectedFlight->origin)->name ?? 'Tierra' }}
                                    </p>
                                    <p class="text-[9px] font-mono text-[var(--neon-cyan)] font-bold mt-1">
                                        {{ $start->format('d M Y') }}
                                    </p>
                                    <p class="text-[11px] font-mono text-[var(--neon-cyan)] font-black">
                                        {{ $start->format('H:i') }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="bg-[var(--bg-obsidian)] p-2 rounded-full border border-[var(--border-glass)] relative group">
                                <svg class="w-8 h-8 text-[var(--neon-cyan)] rotate-90 drop-shadow-[0_0_10px_var(--neon-cyan)]"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M21,16.5C21,16.88 20.79,17.21 20.47,17.38L12.57,21.82C12.41,21.94 12.21,22 12,22C11.79,22 11.59,21.94 11.43,21.82L3.53,17.38C3.21,17.21 3,16.88 3,16.5V7.5C3,7.12 3.21,6.79 3.53,6.62L11.43,2.18C11.59,2.06 11.79,2 12,2C12.21,2 12.41,2.06 12.57,2.18L20.47,6.62C20.79,6.79 21,7.12 21,7.5V16.5Z" />
                                </svg>
                                <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 whitespace-nowrap">
                                    <span
                                        class="text-[9px] font-black text-[var(--text-primary)] bg-[var(--neon-cyan)]/10 px-3 py-1 rounded-full uppercase tracking-tighter border border-[var(--neon-cyan)]/20">{{ $duration }}</span>
                                </div>
                            </div>

                            <div class="flex flex-col items-center">
                                <div
                                    class="w-12 h-12 rounded-full bg-[var(--bg-panel)] border-2 border-[var(--border-glass)] flex items-center justify-center transition-colors">
                                    <div class="w-3 h-3 rounded-full bg-[var(--text-secondary)]/20"></div>
                                </div>
                                <div class="text-center mt-3">
                                    <p class="text-[8px] font-black text-[var(--text-secondary)] uppercase tracking-widest">
                                        Destino</p>
                                    <p class="text-xs font-black text-[var(--text-primary)] uppercase">
                                        {{ optional($selectedFlight->destination)->name ?? '---' }}
                                    </p>
                                    <p class="text-[9px] font-mono text-[var(--text-secondary)] font-bold mt-1">
                                        {{ $end->format('d M Y') }}
                                    </p>
                                    <p class="text-[11px] font-mono text-[var(--text-primary)] font-black">
                                        {{ $end->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div
                                class="p-4 rounded-2xl bg-[var(--tech-input-bg)] border border-[var(--border-glass)] space-y-4">
                                <h4
                                    class="text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em] flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--neon-cyan)]"></span>
                                    Logística de Misión
                                </h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <p
                                            class="text-[8px] font-bold text-[var(--text-secondary)] uppercase tracking-widest">
                                            Nave
                                            Asignada</p>
                                        <p class="text-xs font-black text-[var(--text-primary)] uppercase">
                                            {{ optional($selectedFlight->starship)->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="space-y-1 text-right">
                                        <p
                                            class="text-[8px] font-bold text-[var(--text-secondary)] uppercase tracking-widest">
                                            Distancia AU
                                        </p>
                                        <p class="text-xs font-mono font-black text-[var(--neon-cyan)]">
                                            {{ number_format($selectedFlight->au_distance, 2) }} AU
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <p
                                            class="text-[8px] font-bold text-[var(--text-secondary)] uppercase tracking-widest">
                                            Velocidad
                                            Crucero</p>
                                        <p class="text-xs font-mono font-black text-[var(--text-primary)]">
                                            {{ number_format($selectedFlight->mission_speed_au, 4) }} h/AU
                                        </p>
                                    </div>
                                    <div class="space-y-1 text-right">
                                        <p
                                            class="text-[8px] font-bold text-[var(--text-secondary)] uppercase tracking-widest">
                                            Tiempo de
                                            Vuelo</p>
                                        <p class="text-xs font-mono font-black text-[var(--text-primary)]">
                                            {{ $flightHours }} Horas
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="p-4 rounded-2xl bg-[var(--tech-input-bg)] border border-[var(--border-glass)] space-y-4">
                                <h4
                                    class="text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em] flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--neon-amber)]"></span>
                                    Tarifas y Pasaje
                                </h4>
                                <div class="space-y-3">
                                    <div
                                        class="flex justify-between items-center p-2 rounded-xl bg-amber-500/5 border border-amber-500/10">
                                        <div>
                                            <p class="text-[7px] font-black text-amber-500/60 uppercase">Puesto Nova</p>
                                            <p class="text-sm font-black text-amber-500">{{ number_format($novaPrice, 2) }}
                                                €</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[7px] font-black text-[var(--text-secondary)] uppercase">
                                                Ocupación</p>
                                            <p class="text-xs font-black text-[var(--text-primary)]">
                                                {{ $selectedFlight->nova_booked ?? 0 }} pax
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex justify-between items-center p-2 rounded-xl bg-purple-500/5 border border-purple-500/10">
                                        <div>
                                            <p class="text-[7px] font-black text-purple-400/60 uppercase">Puesto SuperNova
                                            </p>
                                            <p class="text-sm font-black text-purple-400">
                                                {{ number_format($supernovaPrice, 2) }} €
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[7px] font-black text-[var(--text-secondary)] uppercase">
                                                Ocupación</p>
                                            <p class="text-xs font-black text-[var(--text-primary)]">
                                                {{ $selectedFlight->supernova_booked ?? 0 }} pax
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div
                                class="p-4 rounded-2xl bg-[var(--tech-input-bg)] border border-[var(--border-glass)] space-y-4">
                                <h4
                                    class="text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em] flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--neon-rose)]"></span>
                                    Desglose de Gastos
                                </h4>
                                <div class="space-y-2 text-[10px]">
                                    <div class="flex justify-between border-b border-[var(--border-glass)] pb-1">
                                        <span class="text-[var(--text-secondary)]">Tasas Portuarias (Tierra/Planet)</span>
                                        <span
                                            class="text-[var(--text-primary)] font-mono">{{ number_format($launchCost + $landingCost, 2) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between border-b border-[var(--border-glass)] pb-1">
                                        <span class="text-[var(--text-secondary)]">Salarios Tripulación
                                            ({{ $selectedFlight->booked_passengers }} pers.)</span>
                                        <span class="text-[var(--text-primary)] font-mono">{{ number_format($crewCost, 2) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between border-b border-[var(--border-glass)] pb-1">
                                        <span class="text-[var(--text-secondary)]">Consumo Antimateria
                                            ({{ $selectedFlight->au_distance }}
                                            AU)</span>
                                        <span class="text-[var(--text-primary)] font-mono">{{ number_format($auCost, 2) }}
                                            €</span>
                                    </div>
                                    <div class="flex justify-between font-black pt-1 text-[var(--neon-rose)]">
                                        <span class="text-[var(--text-secondary)]">Total Gastos</span>
                                        <span class="font-mono">{{ number_format($selectedFlight->operational_cost, 2) }}
                                            €</span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="p-6 rounded-2xl bg-gradient-to-br from-[var(--neon-emerald)]/10 to-transparent border border-[var(--neon-emerald)]/20 text-center space-y-4">
                                <div>
                                    <p
                                        class="text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-[0.3em] mb-2">
                                        Rentabilidad Neta</p>
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="flex items-baseline gap-2">
                                            <span
                                                class="text-4xl font-black {{ $selectedFlight->mission_profitability < 0 ? 'text-[var(--neon-rose)]' : 'text-[var(--neon-emerald)]' }} tracking-tighter drop-shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                                                {{ number_format($selectedFlight->mission_profitability, 0, ',', '.') }}
                                            </span>
                                            <span
                                                class="text-xl font-bold {{ $selectedFlight->mission_profitability < 0 ? 'text-[var(--neon-rose)]' : 'text-[var(--neon-emerald)]' }} opacity-50">€</span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span
                                                class="text-[8px] font-black uppercase text-[var(--text-secondary)] tracking-widest">ROI
                                                Estimado:</span>
                                            <span
                                                class="text-xs font-mono font-black {{ $selectedFlight->mission_profitability < 0 ? 'text-[var(--neon-rose)]' : 'text-[var(--neon-emerald)]' }}">
                                                {{ number_format(($selectedFlight->operational_cost > 0) ? ($selectedFlight->mission_profitability / $selectedFlight->operational_cost) * 100 : 0, 1) }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-[var(--tech-input-bg)] border-t border-[var(--border-glass)] flex gap-3">
                    <button type="button" wire:click="edit({{ $selectedFlight->id }})"
                        @click="$wire.set('showDetailsModal', false)"
                        class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] hover:text-[var(--text-primary)] bg-[var(--tech-hover-bg)] hover:bg-[var(--tech-hover-bg)]/80 rounded-xl transition-all border border-[var(--border-glass)]">
                        Editar Vuelo
                    </button>
                    <button type="button" wire:click="$set('showDetailsModal', false)"
                        class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-black bg-[var(--neon-cyan)] hover:bg-[var(--neon-cyan)]/90 rounded-xl transition-all shadow-[0_0_20px_rgba(6,182,212,0.3)]">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modales --}}
    <div x-data="{ 
        lockScroll: @entangle('showSaveModal') || @entangle('showDeleteModal') || @entangle('showConflictDeleteModal') || @entangle('showDateConflictModal') || @entangle('showDetailsModal')
    }"
        x-effect="lockScroll ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">

        {{-- Modal Conflicto de Fechas --}}
        @if($showDateConflictModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md"
                    wire:click="$set('showDateConflictModal', false)"></div>
                <div
                    class="relative border border-[var(--border-glass)] rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/90 backdrop-blur-xl animate-tech">
                    <div class="p-8 border-b border-[var(--border-glass)] flex flex-col items-center text-center gap-4">
                        <div
                            class="w-14 h-14 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-500 shadow-[0_0_20px_rgba(245,158,11,0.1)] flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">
                                Conflicto de Fechas
                            </h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">La nueva fecha de
                                llegada colisiona
                                con el despegue del vuelo de retorno. ¿Deseas ajustar automáticamente el vuelo de retorno
                                (margen 24h)?</p>
                        </div>
                    </div>
                    <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="$set('showDateConflictModal', false)"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">Ignorar</button>
                        <button type="button" wire:click="adjustReturnFlightDate"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest text-black bg-amber-500 hover:bg-amber-400 shadow-[0_0_20px_rgba(245,158,11,0.3)] rounded-xl transition-all">Ajustar</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Guardar/Editar --}}
        @if($showSaveModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md"
                    wire:click="$set('showSaveModal', false)"></div>
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
                                {{ $isEditing ? '¿Confirmas los cambios realizados en los parámetros de esta misión?' : '¿Confirmas el registro y activación de este nuevo vuelo?' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="$set('showSaveModal', false)"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">Cancelar</button>
                        <button type="button" wire:click="executeSave"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest text-black {{ $isEditing ? 'bg-amber-500 hover:bg-amber-400 shadow-[0_0_20px_rgba(245,158,11,0.3)]' : 'bg-emerald-500 hover:bg-emerald-400 shadow-[0_0_20px_rgba(14,165,233,0.3)]' }} rounded-xl transition-all">Confirmar</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Eliminar --}}
        @if($showDeleteModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md"
                    wire:click="$set('showDeleteModal', false)">
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
                            <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">
                                Eliminar Vuelo</h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">¿Confirmas la
                                eliminación
                                permanente del vuelo <span
                                    class="text-[var(--text-primary)] font-bold">{{ $flightToDeleteCode }}</span>?
                            </p>
                        </div>
                    </div>
                    <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="$set('showDeleteModal', false)"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">Cancelar</button>
                        <button type="button" wire:click="executeDelete"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-[0_0_20px_rgba(225,29,72,0.3)] transition-all">Confirmar
                            Borrado</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Crítico: Cancelación (Con Reservas) --}}
        @if($showConflictDeleteModal)
            <div class="fixed inset-0 z-[600] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/90 backdrop-blur-lg"
                    wire:click="$set('showConflictDeleteModal', false)"></div>
                <div
                    class="relative border border-red-500/30 rounded-[24px] max-w-md w-full overflow-hidden shadow-[0_0_60px_rgba(220,38,38,0.4)] bg-[var(--bg-panel)]/95 backdrop-blur-2xl animate-tech">
                    <div class="p-8 border-b border-red-500/10 flex flex-col items-center text-center gap-5">
                        <div
                            class="w-16 h-16 rounded-full bg-red-600 text-white flex items-center justify-center shrink-0 shadow-[0_0_30px_rgba(220,38,38,0.4)]">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="w-full">
                            <h3 class="text-xl font-black text-red-500 uppercase tracking-widest mb-2">Cancelación Crítica
                            </h3>
                            <div class="space-y-4">
                                <div class="bg-red-500/10 border border-[var(--border-glass)] rounded-xl p-4 text-left">
                                    <p class="text-[var(--text-primary)] text-xs font-bold mb-1 uppercase tracking-tight">
                                        Vuelo:
                                        {{ $flightToDeleteCode }}
                                    </p>
                                    <p class="text-red-400 text-[10px] leading-tight font-medium">Se han detectado <span
                                            class="underline font-black">{{ $reservationsCount }} reservas activas</span>.
                                        La cancelación requiere un motivo formal.</p>
                                </div>
                                <div class="space-y-2 text-left">
                                    <p
                                        class="text-[10px] font-black text-[var(--text-secondary)] uppercase tracking-widest ml-1">
                                        Motivo de
                                        Cancelación</p>
                                    <div class="grid grid-cols-1 gap-2">
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-xl border border-[var(--border-glass)] bg-[var(--tech-hover-bg)] cursor-pointer hover:bg-[var(--tech-hover-bg)]/80 transition-all">
                                            <input type="radio" wire:model="cancelReason" value="technical"
                                                class="accent-red-500">
                                            <span
                                                class="text-[10px] font-bold text-[var(--text-primary)] uppercase tracking-wider">Causa
                                                Técnica</span>
                                        </label>
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-xl border border-[var(--border-glass)] bg-[var(--tech-hover-bg)] cursor-pointer hover:bg-[var(--tech-hover-bg)]/80 transition-all">
                                            <input type="radio" wire:model="cancelReason" value="weather"
                                                class="accent-amber-500">
                                            <span
                                                class="text-[10px] font-bold text-[var(--text-primary)] uppercase tracking-wider">Meteorología</span>
                                        </label>
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-xl border border-[var(--border-glass)] bg-[var(--tech-hover-bg)] cursor-pointer hover:bg-[var(--tech-hover-bg)]/80 transition-all">
                                            <input type="radio" wire:model="cancelReason" value="voluntary"
                                                class="accent-zinc-400">
                                            <span
                                                class="text-[10px] font-bold text-[var(--text-primary)] uppercase tracking-wider">Administrativo</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex p-5 gap-3 bg-red-600/5">
                        <button type="button" wire:click="$set('showConflictDeleteModal', false)"
                            class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">Cancelar</button>
                        <button type="button" wire:click="cancelFlightAndNotify"
                            class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest text-white bg-red-600 hover:bg-red-500 rounded-xl shadow-[0_0_30px_rgba(220,38,38,0.5)] transition-all">Confirmar
                            y Notificar</button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Botón Subir Mobile -->
    <button x-show="showScrollTop" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="md:hidden fixed bottom-6 right-6 z-[90] w-12 h-12 rounded-full bg-[var(--neon-cyan)] text-black flex items-center justify-center shadow-[0_0_20px_rgba(14,165,233,0.5)] border border-[var(--neon-cyan)]/50 transition-transform active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
</div> {{-- Cierre de min-h-screen (1) --}}