<div class="p-6 md:p-8 space-y-6 relative obsidian-bg min-h-screen text-[var(--text-primary)]"
    x-data="{ showScrollTop: false }" @scroll.window="showScrollTop = window.pageYOffset > 300">
    <!-- Header & Flash Message -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-rose-500/30 pb-4">
        <div>
            <h2 class="text-3xl font-bold text-[var(--neon-rose)] tracking-tight uppercase flex items-center gap-3">
                Vuelos Terrestres
            </h2>
            <p class="text-[var(--text-secondary)] text-sm mt-1 uppercase tracking-widest">
                Gestión de Vuelos Terrestres · {{ $flights->total() }} Registrados
            </p>
        </div>

        @if (session()->has('message'))
            <div
                class="mt-4 md:mt-0 bg-green-900/40 border border-green-700/50 text-green-400 px-4 py-2 text-sm font-medium uppercase tracking-wider rounded-[10px] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('message') }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-y-4 md:gap-8 items-start md:grid-rows-[auto_auto_1fr]">
        <div class="md:col-span-3 md:col-start-1 md:row-start-1">
            <div class="tech-card p-6 flex flex-col gap-6 rounded-[10px] shadow-lg">
                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    <div class="relative w-full sm:flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live="search" placeholder="Buscar vuelo terrestre..."
                            class="tech-input block w-full pl-10 py-2.5 focus:outline-none sm:text-sm transition-colors rounded-[10px]">
                    </div>
                    <div class="relative w-full sm:w-48">
                        <input type="date" wire:model.live="searchDate"
                            class="tech-input block w-full px-3 py-2.5 focus:outline-none sm:text-sm transition-colors rounded-[10px] font-mono text-xs uppercase tracking-widest">
                    </div>
                    <div class="w-full sm:w-auto flex justify-end gap-2">
                        <button wire:click="toggleSort"
                            class="bg-[var(--tech-input-bg)] hover:bg-[var(--tech-hover-bg)] border border-[var(--border-glass)] text-[var(--text-primary)] px-4 py-2.5 sm:text-sm font-medium flex items-center gap-2 transition-colors w-full sm:w-auto justify-center rounded-[10px] tracking-widest uppercase text-xs">
                            <svg class="w-4 h-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                @if($sortDir === 'asc')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                @endif
                            </svg>
                            Orden
                        </button>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 items-center border-t border-[var(--border-glass)] pt-4">
                    <div class="w-full sm:flex-1">
                        <select wire:model.live="searchOriginId"
                            class="tech-input w-full px-3 py-2.5 focus:outline-none text-xs rounded-[10px] appearance-none cursor-pointer font-mono uppercase tracking-widest">
                            <option value="">-- ORIGEN (TODOS) --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" wire:click="swapSearchLocations"
                        class="p-2.5 rounded-full bg-[var(--tech-input-bg)] border border-[var(--border-glass)] text-[var(--neon-rose)] hover:bg-[var(--neon-rose)] hover:text-black transition-all shadow-[0_0_10px_rgba(244,63,94,0.1)] active:scale-95"
                        title="Intercambiar Origen/Destino">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </button>

                    <div class="w-full sm:flex-1">
                        <select wire:model.live="searchDestinationId"
                            class="tech-input w-full px-3 py-2.5 focus:outline-none text-xs rounded-[10px] appearance-none cursor-pointer font-mono uppercase tracking-widest">
                            <option value="">-- DESTINO (TODOS) --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if ($search !== '' || $searchOriginId !== '' || $searchDestinationId !== '' || $searchDate !== '')
                    <div class="flex justify-start px-2">
                        <button wire:click="resetFilters"
                            class="text-[10px] uppercase font-bold tracking-widest text-[var(--text-secondary)] hover:text-[var(--neon-rose)] transition-colors flex items-center gap-1.5 group">
                            Ver Todos
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="md:col-span-2 md:col-start-4 md:row-start-1 md:row-span-3 mt-4 md:mt-0"
            x-data="{ expanded: window.innerWidth >= 768 }"
            @resize.window="if(window.innerWidth >= 768) expanded = true">
            <div
                class="tech-card p-6 rounded-xl transition-all duration-500 relative overflow-hidden {{ $isEditing ? 'border-amber-500/50 shadow-[0_0_30px_rgba(245,158,11,0.1)]' : '' }}">
                @if($isEditing)
                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500/0 via-amber-500 to-amber-500/0">
                    </div>
                @endif

                <!-- Mobile Toggle -->
                <button @click="expanded = !expanded" type="button"
                    class="w-full md:hidden flex justify-between items-center pb-4 mb-4 border-b border-[var(--border-glass)] font-black uppercase tracking-widest text-sm transition-colors text-[var(--neon-rose)]">
                    <span
                        x-text="expanded ? 'Ocultar Formulario' : '{{ $isEditing ? 'Continuar Edición' : 'Nuevo Vuelo Terrestre' }}'"></span>
                    <svg :class="expanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="expanded" x-transition>

                    <div class="flex justify-between items-center mb-6 border-b border-[var(--border-glass)] pb-4">
                        <h3
                            class="text-sm font-black uppercase tracking-[0.1em] flex items-center gap-2 {{ $isEditing ? 'text-amber-400' : 'text-blue-400' }}">
                            @if($isEditing)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Editando Vuelo
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
                                class="text-[10px] uppercase font-mono-tech tracking-widest text-[var(--text-secondary)] hover:text-[var(--text-primary)] px-2 py-1 transition-colors border border-[var(--border-glass)] hover:border-[var(--text-primary)]/20 rounded-lg flex items-center gap-1.5 bg-[var(--tech-hover-bg)]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Nuevo Vuelo
                            </button>
                        @endif
                    </div>

                    <form wire:submit.prevent="confirmSave" class="space-y-4">
                        @if($isEditing)
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-[var(--text-secondary)] mb-1 uppercase tracking-widest flex items-center gap-1.5 pl-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                    ID
                                </label>
                                <input type="text" value="{{ str_pad($flightId, 4, '0', STR_PAD_LEFT) }}" readonly
                                    class="tech-input w-full px-3 py-2 text-[var(--text-secondary)] font-mono text-sm cursor-not-allowed outline-none rounded-[10px]">
                            </div>
                        @endif

                        <div>
                            <label
                                class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                Aerolínea
                            </label>
                            <input type="text" wire:model="airline"
                                class="tech-input w-full px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px] {{ $isEditing ? 'border-[var(--neon-amber)] focus:border-[var(--neon-amber)]' : 'focus:border-[var(--neon-rose)]' }}">
                            @error('airline') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Base Origen
                                </label>
                                <select wire:model="origin_id"
                                    class="tech-input w-full px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px] {{ $isEditing ? 'border-[var(--neon-amber)]' : '' }}">
                                    <option value="">-- SELECCIONAR --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                                    @endforeach
                                </select>
                                @error('origin_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Base Destino
                                </label>
                                <select wire:model="destination_id"
                                    class="tech-input w-full px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px] {{ $isEditing ? 'border-[var(--neon-amber)]' : '' }}">
                                    <option value="">-- SELECCIONAR --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                                    @endforeach
                                </select>
                                @error('destination_id') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Salida
                                </label>
                                <input type="datetime-local" wire:model="departure_datetime"
                                    class="tech-input w-full px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('departure_datetime') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Llegada
                                </label>
                                <input type="datetime-local" wire:model="arrival_datetime"
                                    class="tech-input w-full px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('arrival_datetime') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Precio P.P(€)
                                </label>
                                <input type="number" step="0.01" min="0.01" wire:model="price"
                                    class="tech-input w-full px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Precio por Equipaje (€)
                                </label>
                                <input type="number" step="0.01" min="0.01" wire:model="baggage_price"
                                    class="tech-input w-full px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('baggage_price') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold {{ $isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)]' }} mb-1 uppercase tracking-widest flex items-center gap-1.5 transition-colors pl-2">
                                    Capacidad Ejecutiva
                                </label>
                                <input type="number" min="1" wire:model="executive_capacity"
                                    class="tech-input w-full px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('executive_capacity') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

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
            <div class="tech-card rounded-[10px] shadow-lg overflow-hidden">
                <ul class="divide-y divide-[var(--border-glass)]">
                    @forelse($flights as $flight)
                        <li wire:key="tflight-{{ $flight->id }}"
                            class="p-5 hover:bg-[var(--tech-hover-bg)] transition-all flex flex-col md:flex-row justify-between md:items-center gap-6 group relative bg-[var(--bg-panel)]/40 overflow-hidden">
                            <div
                                class="absolute inset-y-0 left-0 w-1 bg-[var(--neon-amber)] transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span
                                        class="text-[10px] font-mono-tech text-rose-500 dark:text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/20">
                                        ID: {{ str_pad($flight->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <h4
                                        class="text-lg font-bold text-[var(--text-primary)] uppercase tracking-wide flex items-center gap-2">
                                        {{ $flight->airline }}
                                    </h4>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <div
                                        class="inline-flex items-center gap-1.5 text-xs font-mono text-[var(--neon-rose)] bg-[var(--tech-input-bg)] px-2 py-1 border border-[var(--border-glass)] rounded-[5px]">
                                        {{ optional($flight->originLocation)->code ?? '???' }} →
                                        {{ optional($flight->destinationLocation)->code ?? '???' }}
                                    </div>
                                    <div
                                        class="inline-flex items-center gap-1.5 text-xs font-mono text-[var(--text-secondary)] bg-[var(--tech-input-bg)] px-2 py-1 border border-[var(--border-glass)] rounded-[5px]">
                                        <svg class="w-3.5 h-3.5 text-[var(--text-secondary)]" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ $flight->departure_datetime->format('d/M H:i') }} -
                                        {{ $flight->arrival_datetime ? $flight->arrival_datetime->format('H:i') : '' }}
                                    </div>
                                    <div
                                        class="inline-flex items-center gap-1.5 text-xs font-mono text-[var(--text-secondary)] bg-[var(--tech-input-bg)] px-2 py-1 border border-[var(--border-glass)] rounded-[5px]">
                                        TARIFA: ${{ number_format($flight->price, 2) }}
                                    </div>

                                    <!-- Disponibilidades -->
                                    <div
                                        class="inline-flex items-center gap-1.5 text-xs font-mono {{ $flight->status === 'Cancelado' ? 'text-[var(--neon-rose)] bg-[var(--tech-input-bg)] border-[var(--neon-rose)]' : 'text-[var(--text-secondary)] bg-[var(--tech-input-bg)] border-[var(--border-glass)]' }} px-2 py-1 border rounded-[5px]">
                                        ESTADO: {{ strtoupper($flight->status ?? 'PROGRAMADO') }}
                                    </div>
                                    <div
                                        class="inline-flex items-center gap-1.5 text-xs font-mono text-[var(--text-secondary)] bg-[var(--tech-input-bg)] px-2 py-1 border border-[var(--border-glass)] rounded-[5px]">
                                        EQUIPAJE: ${{ number_format($flight->baggage_price, 2) }}
                                    </div>
                                    <div
                                        class="inline-flex items-center gap-1.5 text-xs font-mono text-[var(--text-secondary)] bg-[var(--tech-input-bg)] px-2 py-1 border border-[var(--border-glass)] rounded-[5px]">
                                        CAPACIDAD: {{ $flight->executive_capacity }}
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
                        <div class="p-12 text-center text-[var(--text-secondary)]">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                </path>
                            </svg>
                            <p class="font-medium uppercase tracking-widest text-sm">No se han encontrado registros</p>
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


        <!-- Botón Subir Mobile -->
        <button x-show="showScrollTop" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})"
            class="md:hidden fixed bottom-6 right-6 z-[90] w-12 h-12 rounded-full bg-[var(--neon-cyan)] text-black flex items-center justify-center shadow-[0_0_20px_rgba(14,165,233,0.5)] border border-[var(--neon-cyan)]/50 transition-transform active:scale-95">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18">
                </path>
            </svg>
        </button>

        {{-- Bloque de modales --}}
        <div x-data="{ 
        lockScroll: @entangle('showSaveModal') || @entangle('showDeleteModal') || @entangle('showConflictDeleteModal')
    }" x-effect="lockScroll ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">

            {{-- Modal Guardar/Editar --}}
            @if($showSaveModal)
                <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md" wire:click="$set('showSaveModal', false)">
                    </div>

                    <div
                        class="relative border border-[var(--border-glass)] rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/95 backdrop-blur-xl animate-tech">
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
                                <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium text-pretty px-2">
                                    @if($isEditing)
                                        ¿Confirmas los cambios realizados en este trayecto terrestre?
                                    @else
                                        ¿Confirmas el registro y activación de este nuevo servicio de transporte?
                                    @endif

                                    @if($flightDurationHours > 0 && $airline)
                                        <span
                                            class="text-amber-500/80 font-bold block mt-3 p-2 bg-amber-500/5 border border-amber-500/20 rounded-lg">
                                            Trayecto: {{ $flightDurationHours }}h de trayecto estimado.
                                        </span>
                                    @endif
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
            @endif

            {{-- Modal Eliminar (Sin Reservas) --}}
            @if($showDeleteModal)
                <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md" wire:click="$set('showDeleteModal', false)">
                    </div>

                    <div
                        class="relative border border-[var(--border-glass)] rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/95 backdrop-blur-xl animate-tech">
                        <div class="p-8 border-b border-[var(--border-glass)] flex flex-col items-center text-center gap-4">
                            <div
                                class="w-14 h-14 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-500 flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(244,63,94,0.1)]">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">Eliminar Vuelo
                                </h3>
                                <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium px-4">
                                    ¿Deseas retirar permanentemente este servicio de la red terrestre? <br>
                                    <span class="text-rose-500/80 mt-1 block font-bold">Esta operación es
                                        irreversible.</span>
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
            @endif

            {{-- Modal Crítico: Eliminación (Con Reservas) --}}
            @if($showConflictDeleteModal)
                <div class="fixed inset-0 z-[600] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-[var(--bg-obsidian)]/95 backdrop-blur-xl"
                        wire:click="$set('showConflictDeleteModal', false)"></div>

                    <div
                        class="relative border border-[var(--neon-rose)]/30 rounded-[24px] max-w-md w-full overflow-hidden shadow-[0_0_60px_rgba(225,29,72,0.4)] bg-[var(--bg-panel)]/95 backdrop-blur-3xl animate-tech">
                        <div class="p-8 border-b border-rose-500/10 flex flex-col items-center text-center gap-5">
                            <div
                                class="w-16 h-16 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-[0_0_30px_rgba(225,29,72,0.4)]">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-[var(--neon-rose)] uppercase tracking-widest mb-2 px-2">Conflicto
                                    de Traslado</h3>
                                <p class="text-[var(--text-primary)] text-[11px] leading-relaxed font-bold">
                                    Se han detectado <span class="underline">{{ $reservationsCount }} pasajeros
                                        ejecutivos</span> en este trayecto.
                                </p>
                                <p class="text-[var(--text-secondary)] text-[10px] leading-tight mt-2 px-6">
                                    La eliminación del servicio cancelará los traslados y notificará a los gestores para la
                                    reubicación de personal.
                                </p>
                            </div>
                        </div>
                        <div class="flex p-5 gap-3 bg-[var(--neon-rose)]/5 items-center flex-wrap sm:flex-nowrap">
                            <button type="button" wire:click="$set('showConflictDeleteModal', false)"
                                class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                                Cancelar
                            </button>

                            <button type="button" wire:click="redirectToEdit"
                                class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest text-amber-500 border border-amber-500/30 hover:bg-amber-500/10 rounded-xl transition-all">
                                Editar
                            </button>

                            <button type="button" wire:click="cancelTerrestrialFlightAndNotify"
                                class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-[0_0_30px_rgba(225,29,72,0.5)] transition-all">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>