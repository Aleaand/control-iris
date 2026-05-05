<div class="p-6 md:p-8 space-y-6 relative obsidian-bg min-h-screen text-[var(--text-primary)]"
    x-data="{ showScrollTop: false }" @scroll.window="showScrollTop = window.pageYOffset > 300">

    {{-- ══ HEADER ══ --}}
    <div class="flex items-start justify-between border-b border-rose-500/30 pb-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-[0.15em] text-[var(--neon-rose)] flex items-center gap-3">
                Hoteles
            </h1>
            <p class="font-mono-tech text-[11px] uppercase tracking-widest mt-1 text-[var(--text-secondary)]">
                Gestión de Hoteles terrestres · {{ $hotels->total() }} Registrados
            </p>
        </div>
        <div class="flex items-center gap-4">
            @if (session()->has('message'))
                <div
                    class="flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--neon-emerald)]/10 border border-[var(--border-glass)]">
                    <div class="w-2 h-2 rounded-full bg-[var(--neon-emerald)]"></div>
                    <span
                        class="font-mono-tech text-[10px] text-[var(--neon-emerald)] uppercase tracking-widest">{{ session('message') }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-y-4 md:gap-8 items-start md:grid-rows-[auto_auto_1fr]">
        <div class="md:col-span-3 md:col-start-1 md:row-start-1">
            <div class="tech-card p-4 flex flex-col sm:flex-row gap-4 justify-between items-center rounded-xl">
                <div class="relative w-full sm:w-2/3">
                    <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Búsqueda de hotel..."
                        class="tech-input block w-full pl-10 py-2 focus:outline-none focus:border-[var(--neon-rose)] text-sm transition-colors rounded-lg">
                </div>

                <div class="w-full sm:w-1/3 flex justify-end">
                    <button type="button" wire:click="toggleSort"
                        class="tech-input border border-[var(--border-glass)] px-4 py-2 text-xs font-mono-tech uppercase tracking-widest flex items-center gap-2 transition-colors w-full sm:w-auto justify-center rounded-lg hover:bg-[var(--tech-hover-bg)]">
                        @if($sortDir === 'asc')
                            <svg class="w-4 h-4 text-[var(--neon-rose)]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-[var(--neon-rose)]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                            </svg>
                        @endif
                        <span>Orden: {{ $sortDir === 'asc' ? 'A-Z' : 'Z-A' }}</span>
                    </button>
                </div>
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
                <button @click="expanded = !expanded" type="button"
                    class="w-full md:hidden flex justify-between items-center pb-4 mb-4 border-b border-zinc-200 dark:border-zinc-800/50 font-black uppercase tracking-widest text-sm transition-colors {{ $isEditing ? 'text-amber-400' : 'text-blue-400' }}">
                    <span
                        x-text="expanded ? 'Ocultar' : '{{ $isEditing ? 'Continuar Edición' : 'Nuevo Destino' }}'"></span>
                    <svg :class="expanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="expanded" x-transition>
                    <div
                        class="flex justify-between items-center mb-6 border-b border-zinc-200 dark:border-zinc-800/50 pb-4 hidden md:flex">
                        <h3
                            class="text-sm font-black uppercase tracking-[0.1em] flex items-center gap-2 {{ $isEditing ? 'text-amber-400' : 'text-blue-400' }}">
                            @if($isEditing)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Editando Hotel
                            @else
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Nuevo Hotel
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
                                Nuevo Hotel
                            </button>
                        @endif
                    </div>

                    <form wire:submit.prevent="confirmSave" class="space-y-4">
                        @if($isEditing)
                            <div>
                                <label
                                    class="block text-[10px] font-mono-tech text-[var(--text-secondary)] mb-1 uppercase tracking-widest pl-1">
                                    ID de Registro
                                </label>
                                <input type="text" value="{{ str_pad($hotelId, 4, '0', STR_PAD_LEFT) }}" readonly
                                    class="tech-input w-full px-3 py-2 text-[var(--text-secondary)] font-mono text-sm cursor-not-allowed outline-none rounded-lg opacity-60">
                            </div>
                        @endif

                        <div>
                            <label
                                class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1 text-[var(--text-secondary)]">
                                Nombre del Hotel <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model="name" required
                                class="tech-input w-full px-3 py-2 focus:outline-none transition-colors text-sm rounded-lg {{ $isEditing ? 'border-[var(--neon-amber)]/50 focus:border-[var(--neon-amber)]' : 'focus:border-[var(--neon-rose)]' }}">
                            @error('name') <span
                                class="text-[var(--neon-rose)] text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1 text-[var(--text-secondary)]">
                                Ubicación Destino <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model="location_id" required
                                class="tech-input w-full px-3 py-2 focus:outline-none transition-colors text-sm rounded-lg cursor-pointer appearance-none {{ $isEditing ? 'border-[var(--neon-amber)]/50 focus:border-[var(--neon-amber)]' : 'focus:border-[var(--neon-rose)]' }}">
                                <option value="">-- SELECCIONAR UBICACIÓN --</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                                @endforeach
                            </select>
                            @error('location_id') <span
                                class="text-[var(--neon-rose)] text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1 text-[var(--text-secondary)]">
                                    Estrellas <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" wire:model="galactic_stars" min="1" max="5" required
                                    class="tech-input w-full px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg {{ $isEditing ? 'border-[var(--neon-amber)]/50 focus:border-[var(--neon-amber)]' : 'focus:border-[var(--neon-rose)]' }}">
                                @error('galactic_stars') <span
                                    class="text-[var(--neon-rose)] text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1 text-[var(--text-secondary)]">
                                    Tarifa / Noche (€) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" wire:model="price_per_night" step="0.01" required
                                    class="tech-input w-full px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg {{ $isEditing ? 'border-[var(--neon-amber)]/50 focus:border-[var(--neon-amber)]' : 'focus:border-[var(--neon-rose)]' }}">
                                @error('price_per_night') <span
                                    class="text-[var(--neon-rose)] text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1 text-[var(--text-secondary)]">
                                Capacidad de Habitaciones <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" wire:model="total_rooms" required
                                class="tech-input w-full px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg {{ $isEditing ? 'border-[var(--neon-amber)]/50 focus:border-[var(--neon-amber)]' : 'focus:border-[var(--neon-rose)]' }}">
                            @error('total_rooms') <span
                                class="text-[var(--neon-rose)] text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="pt-4 mt-2 border-t border-zinc-200 dark:border-zinc-800/50">
                            <button type="submit"
                                class="w-full font-mono-tech font-bold uppercase tracking-widest py-3 px-4 transition-colors text-[11px] rounded-lg border flex items-center justify-center gap-2 {{ $isEditing ? 'bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-black border-amber-500/50' : 'bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 dark:text-emerald-400 hover:text-black border-emerald-500/50' }}">
                                {{ $isEditing ? 'Actualizar Hotel' : 'Registrar Nuevo Hotel' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="md:col-span-3 md:col-start-1 md:row-start-2 mt-4 md:mt-0">
            <div class="tech-card rounded-xl overflow-hidden relative">
                <ul class="divide-y divide-[var(--border-glass)]">
                    @forelse($hotels as $hotel)
                        <li wire:key="hotel-{{ $hotel->id }}"
                            class="p-5 hover:bg-[var(--tech-hover-bg)] transition-all flex flex-col md:flex-row justify-between md:items-center gap-6 group relative bg-[var(--bg-panel)]/40 overflow-hidden">
                            <div
                                class="absolute inset-y-0 left-0 w-1 bg-[var(--neon-emerald)] transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <span
                                        class="text-[10px] font-mono-tech text-rose-500 dark:text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/20">
                                        ID:{{ str_pad($hotel->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <h4
                                        class="text-lg font-black uppercase tracking-wide flex items-center gap-2 group-hover:text-[var(--neon-rose)] transition-colors text-[var(--text-primary)]">
                                        {{ $hotel->name }}
                                    </h4>
                                    <span class="flex gap-0.5 ml-1">
                                        @for($i = 0; $i < $hotel->galactic_stars; $i++)
                                            <svg class="w-3.5 h-3.5 text-[var(--neon-rose)]" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                </path>
                                            </svg>
                                        @endfor
                                    </span>
                                </div>
                                <p
                                    class="text-sm mb-3 text-[var(--text-secondary)] uppercase tracking-widest font-mono-tech flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[var(--neon-rose)]/60" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ optional($hotel->location)->name ?? 'DESCONOCIDO' }}
                                </p>

                                <div class="flex flex-wrap gap-2">
                                    <div
                                        class="inline-flex items-center gap-1.5 text-[10px] font-mono-tech px-3 py-1.5 border border-[var(--border-glass)] rounded-lg bg-[var(--tech-input-bg)] text-[var(--text-primary)]">
                                        <svg class="w-3.5 h-3.5 text-[var(--neon-rose)]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        <span class="text-[var(--text-secondary)] uppercase">Tarifa:</span>
                                        <span
                                            class="text-[var(--neon-rose)] font-bold">${{ number_format($hotel->price_per_night, 2) }}</span>
                                    </div>

                                    <div
                                        class="inline-flex items-center gap-1.5 text-[10px] font-mono-tech px-3 py-1.5 border border-[var(--border-glass)] rounded-lg bg-[var(--tech-input-bg)] text-[var(--text-primary)]">
                                        <svg class="w-3.5 h-3.5 text-[var(--neon-emerald)]" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                            </path>
                                        </svg>
                                        <span class="text-[var(--text-secondary)] uppercase">Disponibilidad:</span>
                                        <span
                                            class="{{ ($hotel->total_rooms - $hotel->occupied_rooms) > 5 ? 'text-[var(--neon-emerald)]' : 'text-[var(--neon-amber)]' }} font-bold">
                                            {{ max(0, $hotel->total_rooms - $hotel->occupied_rooms) }} Libres
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex-col gap-2 shrink-0 border-t border-[var(--border-glass)] sm:border-0 pt-4 sm:pt-0">
                                <button type="button" wire:click="edit({{ $hotel->id }})"
                                    @click="expanded = true; window.scrollTo({top: 0, behavior: 'smooth'})"
                                    class="p-2.5 rounded-lg border border-amber-500/30 text-amber-500 hover:bg-amber-500 hover:text-black transition-colors"
                                    title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button type="button" wire:click="confirmDelete({{ $hotel->id }})"
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
                            <svg class="w-12 h-12 mx-auto mb-4 opacity-50 text-[var(--neon-rose)]" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                </path>
                            </svg>
                            <p class="font-mono-tech uppercase tracking-widest text-sm">No se han encontrado hoteles
                                registrados</p>
                        </div>
                    @endforelse
                </ul>

                <div class="px-6 py-4 bg-[var(--tech-input-bg)] border-t border-[var(--border-glass)] flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] opacity-70">
                        Mostrando {{ $hotels->firstItem() ?? 0 }} - {{ $hotels->lastItem() ?? 0 }} de {{ $hotels->total() }} Registros
                    </div>
                    @if($hotels->hasPages())
                        <div class="w-full md:w-auto">
                            {{ $hotels->links('vendor.livewire.simple-tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <button x-show="showScrollTop" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="md:hidden fixed bottom-6 right-6 z-[90] w-12 h-12 rounded-full bg-[var(--neon-rose)] text-black flex items-center justify-center shadow-[0_0_20px_rgba(244,63,94,0.5)] border border-[var(--neon-rose)]/50 transition-transform active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>

    <div x-data="{ 
        lockScroll: @entangle('showSaveModal') || @entangle('showDeleteModal') || @entangle('showConflictDeleteModal')
    }"
        x-effect="lockScroll ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">

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
                                {{ $isEditing ? '¿Confirmas los cambios en los parámetros de este hotel?' : '¿Confirmas la activación de este nuevo establecimiento?' }}
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
                                Eliminar Hotel</h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                                ¿Confirmas la eliminación permanente de este hotel? <br>
                                <span class="text-rose-500/80 mt-1 block font-bold">Esta acción es irreversible.</span>
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
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/90 backdrop-blur-lg"
                    wire:click="$set('showConflictDeleteModal', false)"></div>

                <div
                    class="relative border border-[var(--neon-rose)]/30 rounded-[24px] max-w-md w-full overflow-hidden shadow-[0_0_60px_rgba(225,29,72,0.4)] bg-[var(--bg-panel)]/95 backdrop-blur-2xl animate-tech">
                    <div class="p-8 border-b border-rose-500/10 flex flex-col items-center text-center gap-5">
                        <div
                            class="w-16 h-16 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-[0_0_30px_rgba(225,29,72,0.4)]">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-[var(--neon-rose)] uppercase tracking-widest mb-2">
                                Eliminación Crítica</h3>
                            <p class="text-[var(--text-primary)] text-[11px] leading-relaxed font-bold">
                                Se han detectado <span class="underline">{{ $reservationsCount }} reservas activas</span>
                                vinculadas.
                            </p>
                            <p class="text-[var(--text-secondary)] text-[10px] leading-tight mt-2">
                                Esta acción cancelará todas las estancias asociadas y notificará a los gestores de tierra
                                para la reubicación inmediata.
                            </p>
                        </div>
                    </div>
                    <div class="flex p-5 gap-3 bg-[var(--neon-rose)]/5">
                        <button type="button" wire:click="$set('showConflictDeleteModal', false)"
                            class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="deleteAndNotify"
                            class="flex-1 py-3.5 px-4 text-[10px] font-black uppercase tracking-widest text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-[0_0_30px_rgba(225,29,72,0.5)] transition-all">
                            Confirmar y Notificar
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>