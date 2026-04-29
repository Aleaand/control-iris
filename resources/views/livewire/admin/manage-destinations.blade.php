<div class="p-6 md:p-8 space-y-6 relative" x-data="{ showScrollTop: false }" @scroll.window="showScrollTop = window.pageYOffset > 300">

    {{-- ══ HEADER ══ --}}
    <div class="flex items-start justify-between border-b border-blue-400/30 pb-4">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-[0.15em] text-blue-400 flex items-center gap-3">
                Destinos
            </h1>
            <p class="font-mono-tech text-[11px] uppercase tracking-widest mt-1" style="color: var(--text-secondary)">
                Gestión planetaria · {{ count($destinations) }} Registrados
            </p>
        </div>
        <div class="flex items-center gap-4">
            @if (session()->has('message'))
                <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                    <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                    <span
                        class="font-mono-tech text-[10px] text-emerald-400 uppercase tracking-widest">{{ session('message') }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-y-4 md:gap-8 items-start md:grid-rows-[auto_auto_1fr]">
        <!-- Buscador y Filtro -->
        <div class="md:col-span-3 md:col-start-1 md:row-start-1">
            <div class="tech-card p-4 flex flex-col sm:flex-row gap-4 justify-between items-center rounded-xl">
                <div class="relative w-full sm:w-2/3">
                    <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Búsqueda de planeta..."
                        class="block w-full pl-10 border border-zinc-700/50 placeholder-zinc-600 py-2 focus:outline-none focus:border-blue-400 text-sm transition-colors rounded-lg"
                        style="background: var(--tech-input-bg); color: var(--text-primary)">
                </div>

                <div class="w-full sm:w-1/3 flex justify-end">
                    <button type="button" wire:click="toggleSort" wire:key="btn-sort-destinations"
                        class="border border-zinc-700/50 px-4 py-2 text-xs font-mono-tech uppercase tracking-widest flex items-center gap-2 transition-colors w-full sm:w-auto justify-center rounded-lg hover:bg-zinc-700/20"
                        style="background: var(--tech-hover-bg); color: var(--text-primary)">
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
                        <span wire:key="text-sort-dir">Orden: {{ $sortDir === 'asc' ? 'A-Z' : 'Z-A' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Formulario -->
        <div class="md:col-span-2 md:col-start-4 md:row-start-1 md:row-span-3 mt-4 md:mt-0" x-data="{ expanded: window.innerWidth >= 768 }" @resize.window="if(window.innerWidth >= 768) expanded = true">
            <div class="tech-card p-6 rounded-xl transition-all duration-500 relative overflow-hidden {{ $isEditing ? 'border-amber-500/50 shadow-[0_0_30px_rgba(245,158,11,0.1)]' : '' }}">
                @if($isEditing)
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500/0 via-amber-500 to-amber-500/0"></div>
                @endif
                <!-- Mobile Toggle -->
                <button @click="expanded = !expanded" type="button" class="w-full md:hidden flex justify-between items-center pb-4 mb-4 border-b border-zinc-200 dark:border-zinc-800/50 font-black uppercase tracking-widest text-sm transition-colors {{ $isEditing ? 'text-amber-400' : 'text-blue-400' }}">
                    <span x-text="expanded ? 'Ocultar Formulario' : '{{ $isEditing ? 'Continuar Edición' : 'Nuevo Destino' }}'"></span>
                    <svg :class="expanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="expanded" x-transition>
                    <div class="flex justify-between items-center mb-6 border-b border-zinc-200 dark:border-zinc-800/50 pb-4 hidden md:flex">
                        <h3 class="text-sm font-black uppercase tracking-[0.1em] flex items-center gap-2 {{ $isEditing ? 'text-amber-400' : 'text-blue-400' }}">
                            @if($isEditing)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Editando Destino
                            @else
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Nuevo Destino
                            @endif
                        </h3>
                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode" class="text-[10px] uppercase font-mono-tech tracking-widest text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white px-2 py-1 transition-colors border border-zinc-300 dark:border-zinc-700/50 hover:border-black/20 dark:hover:border-white/20 rounded-lg flex items-center gap-1.5" style="background: var(--tech-hover-bg)">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path></svg>
                                Nuevo
                            </button>
                        @endif
                    </div>
                    <form wire:submit.prevent="confirmSave" class="space-y-4">
                        @if($isEditing)
                            <div>
                                <label class="block text-[10px] font-mono-tech text-zinc-500 mb-1 uppercase tracking-widest pl-1">ID Planetario</label>
                                <input type="text" value="{{ str_pad($destinationId, 4, '0', STR_PAD_LEFT) }}" readonly class="w-full border border-zinc-200 dark:border-zinc-800 px-3 py-2 text-zinc-500 font-mono text-sm cursor-not-allowed outline-none rounded-lg" style="background: var(--tech-input-bg)">
                            </div>
                        @endif
                        <div>
                            <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">Nombre del Planeta</label>
                            <input type="text" wire:model="name" class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-zinc-300 dark:border-zinc-700/50 focus:border-blue-400' }} px-3 py-2 focus:outline-none transition-colors text-sm rounded-lg" style="background: var(--tech-input-bg); color: var(--text-primary)">
                            @error('name') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">Descripción</label>
                            <textarea wire:model="description" rows="4" class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-zinc-300 dark:border-zinc-700/50 focus:border-blue-400' }} px-3 py-2 focus:outline-none transition-colors text-sm rounded-lg resize-y" style="background: var(--tech-input-bg); color: var(--text-primary)"></textarea>
                            @error('description') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">Distancia Mínima (AU)</label>
                                <input type="number" step="0.01" min="0.01" wire:model="distance_au" class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-cyan-500/30 focus:border-cyan-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg" style="background: var(--tech-input-bg); color: var(--text-primary)">
                                @error('distance_au') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">Distancia Máxima (AU)</label>
                                <input type="number" step="0.01" min="0.01" wire:model="max_distance_au" class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-cyan-500/30 focus:border-cyan-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg" style="background: var(--tech-input-bg); color: var(--text-primary)">
                                @error('max_distance_au') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">Tasa Despegue (€)</label>
                                <input type="number" step="0.01" min="0" wire:model="launch_fee" class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-zinc-300 dark:border-zinc-700/50 focus:border-blue-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg" style="background: var(--tech-input-bg); color: var(--text-primary)">
                                @error('launch_fee') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-mono-tech mb-1 uppercase tracking-widest pl-1" style="color: var(--text-secondary)">Tasa Aterrizaje (€)</label>
                                <input type="number" step="0.01" min="0" wire:model="landing_fee" class="w-full border {{ $isEditing ? 'border-amber-500/30 focus:border-amber-500' : 'border-zinc-300 dark:border-zinc-700/50 focus:border-blue-400' }} px-3 py-2 font-mono focus:outline-none transition-colors text-sm rounded-lg" style="background: var(--tech-input-bg); color: var(--text-primary)">
                                @error('landing_fee') <span class="text-rose-500 text-[10px] font-mono-tech mt-1 block uppercase">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="pt-4 mt-2 border-t border-zinc-200 dark:border-zinc-800/50">
                            <button type="submit" class="w-full font-mono-tech font-bold uppercase tracking-widest py-3 px-4 transition-colors text-[11px] rounded-lg border flex items-center justify-center gap-2 {{ $isEditing ? 'bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-black border-amber-500/50' : 'bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 dark:text-emerald-400 hover:text-black border-emerald-500/50' }}">
                                {{ $isEditing ? 'Actualizar Destino' : 'Registrar Nuevo Destino' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista de Resultados -->
        <div class="md:col-span-3 md:col-start-1 md:row-start-2 mt-4 md:mt-0">
            <div class="tech-card rounded-xl overflow-hidden">
                <ul class="divide-y divide-zinc-200 dark:divide-zinc-800/80">
                    @forelse($destinations as $dest)
                        <li wire:key="dest-{{ $dest->id }}"
                            class="p-5 transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4 group hover:bg-zinc-100 dark:hover:bg-zinc-800/10">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <span
                                        class="text-[10px] font-mono-tech text-blue-500 dark:text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded border border-blue-500/20">ID:{{ str_pad($dest->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <h4 class="text-lg font-black uppercase tracking-wide flex items-center gap-2 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors"
                                        style="color: var(--text-primary)">
                                        {{ $dest->name }}
                                    </h4>
                                </div>
                                <p class="text-sm line-clamp-2 mb-3 leading-relaxed" style="color: var(--text-secondary)">
                                    {{ $dest->description }}
                                </p>
                                <div class="inline-flex items-center gap-2 text-[10px] font-mono-tech px-3 py-1.5 border border-zinc-200 dark:border-zinc-700/50 rounded-lg"
                                    style="background: var(--tech-input-bg); color: var(--text-primary)">
                                    <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span class="text-zinc-500 uppercase">Distancia:</span>
                                    <span class="text-cyan-600 dark:text-cyan-400">{{ number_format($dest->distance_au, 2) }} AU</span>
                                    @if($dest->max_distance_au)
                                        <span class="text-zinc-400 dark:text-zinc-600 mx-1">—</span>
                                        <span class="text-zinc-500 uppercase">Máx:</span>
                                        <span class="text-cyan-600/70 dark:text-cyan-500/70">{{ number_format($dest->max_distance_au, 2) }} AU</span>
                                    @endif
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span
                                        class="text-[9px] font-mono-tech text-amber-600 dark:text-amber-500 bg-amber-500/10 px-2 py-1 rounded border border-amber-500/20 uppercase tracking-widest">
                                        Despegue: {{ number_format($dest->launch_fee, 2) }} €
                                    </span>
                                    <span
                                        class="text-[9px] font-mono-tech text-purple-600 dark:text-purple-500 bg-purple-500/10 px-2 py-1 rounded border border-purple-500/20 uppercase tracking-widest">
                                        Aterrizaje: {{ number_format($landing_fee, 2) ?? number_format($dest->landing_fee, 2) }} €
                                    </span>
                                </div>
                            </div>

                            <div
                                class="flex sm:flex-col gap-2 shrink-0 border-t border-zinc-200 dark:border-zinc-800/80 sm:border-0 pt-4 sm:pt-0">
                                <button type="button" wire:click="edit({{ $dest->id }})" @click="expanded = true; window.scrollTo({top: 0, behavior: 'smooth'})"
                                    class="p-2.5 rounded-lg border border-amber-500/30 text-amber-500 hover:bg-amber-500 hover:text-black transition-colors" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                                <button type="button" wire:click="confirmDelete({{ $dest->id }})"
                                    class="p-2.5 rounded-lg border border-red-500/30 text-red-600 dark:text-red-500 hover:bg-red-500 hover:text-white transition-colors" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </li>
                    @empty
                        <div class="p-12 text-center text-zinc-500">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-50 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                </path>
                            </svg>
                            <p class="font-mono-tech uppercase tracking-widest text-xs">No se han encontrado destinos</p>
                        </div>
                    @endforelse
                </ul>
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

    {{-- Modal Guardar --}}
    @if($showSaveModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/20 dark:bg-black/40 backdrop-blur-sm p-4">
            <div class="border border-black/10 dark:border-white/10 rounded-[15px] max-w-sm w-full overflow-hidden shadow-2xl backdrop-blur-xl bg-white/80 dark:bg-zinc-950/60">
                <div class="p-6 border-b border-black/5 dark:border-white/5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full {{ $isEditing ? 'bg-amber-100 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/30 text-amber-600 dark:text-amber-500' : 'bg-blue-100 dark:bg-blue-700/10 border-blue-200 dark:border-blue-700/30 text-blue-600 dark:text-blue-700' }} flex items-center justify-center border shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-widest mb-1 text-zinc-900 dark:text-white">
                            {{ $isEditing ? 'Confirmar Actualización' : 'Confirmar Registro' }}
                        </h3>
                        <p class="text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
                            {{ $isEditing ? '¿Deseas guardar los cambios realizados en este destino?' : '¿Confirmas la creación de este nuevo destino?' }}
                        </p>
                    </div>
                </div>
                <div class="flex p-3 gap-3 bg-zinc-100/50 dark:bg-black/30 border-t border-black/5 dark:border-white/5">
                    <button type="button" wire:click="$set('showSaveModal', false)"
                        class="flex-1 py-2.5 px-4 text-xs font-bold uppercase rounded-[10px] border border-black/10 dark:border-white/10 text-zinc-700 dark:text-zinc-300 hover:bg-black/5 dark:hover:bg-white/5 hover:text-black dark:hover:text-white transition-colors backdrop-blur-md">
                        Cancelar
                    </button>
                    <button type="button" wire:click="executeSave"
                        class="flex-1 py-2.5 px-4 text-xs font-bold uppercase text-white dark:text-black {{ $isEditing ? 'bg-amber-500 hover:bg-amber-600 dark:hover:bg-amber-400' : 'bg-blue-500 hover:bg-blue-600 dark:hover:bg-blue-400' }} rounded-[10px] shadow-lg transition-colors">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Eliminar --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/20 dark:bg-black/40 backdrop-blur-sm p-4">
            <div class="border border-red-500/30 dark:border-red-900/30 rounded-[15px] max-w-sm w-full overflow-hidden shadow-2xl backdrop-blur-xl bg-white/80 dark:bg-zinc-950/60">
                <div class="p-6 border-b border-red-500/10 dark:border-red-900/20 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-600 dark:text-red-500 uppercase tracking-widest mb-1">Confirmar Eliminación</h3>
                        <p class="text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
                            ¿Estás seguro de borrar este destino <span class="font-bold text-zinc-900 dark:text-white">definitivamente</span>?
                        </p>
                    </div>
                </div>
                <div class="flex p-3 gap-3 bg-red-50/50 dark:bg-black/30 border-t border-red-500/10 dark:border-red-900/10">
                    <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="flex-1 py-2 px-4 text-xs font-bold uppercase rounded-[10px] border border-black/10 dark:border-white/10 text-zinc-700 dark:text-zinc-300 hover:bg-black/5 dark:hover:bg-white/5 hover:text-black dark:hover:text-white transition-colors backdrop-blur-md">
                        Cancelar
                    </button>
                    <button type="button" wire:click="executeDelete"
                        class="flex-1 py-2 px-4 text-xs font-bold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600/90 dark:hover:bg-red-500 rounded-[10px] transition-all border border-red-600 dark:border-red-500/50 shadow-lg dark:shadow-[0_0_15px_rgba(220,38,38,0.3)]">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Cascada --}}
    @if($showCascadeDeleteModal)
        <div class="fixed inset-0 z-[110] flex items-center justify-center bg-zinc-900/40 dark:bg-black/60 backdrop-blur-md p-4">
            <div class="border-2 border-red-500 dark:border-red-600/50 rounded-[15px] max-w-md w-full overflow-hidden shadow-2xl backdrop-blur-2xl bg-white/90 dark:bg-zinc-950/70">
                <div class="p-6 border-b border-red-200 dark:border-red-900/30 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-600 dark:bg-red-600/90 text-white flex items-center justify-center shrink-0 shadow-lg dark:shadow-[0_0_20px_rgba(220,38,38,0.4)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-red-600 dark:text-red-500 uppercase tracking-tighter mb-1">
                            Eliminación Crítica
                        </h3>
                        <p class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-200">
                            Se han detectado <span class="font-bold underline text-red-600 dark:text-red-400">{{ $flightsCount }} vuelos
                                activos</span> vinculados.
                        </p>
                        <p class="text-xs mt-2 italic text-red-500 dark:text-red-300/80">
                            Esta acción eliminará permanentemente todos los vuelos y reservas asociados.
                        </p>
                    </div>
                </div>
                <div class="flex p-4 gap-3 bg-red-50/50 dark:bg-black/40 border-t border-red-200 dark:border-red-900/20">
                    <button type="button" wire:click="$set('showCascadeDeleteModal', false)"
                        class="flex-1 py-3 px-4 text-xs font-bold uppercase tracking-widest rounded-[10px] border border-black/10 dark:border-white/10 text-zinc-700 dark:text-zinc-300 hover:bg-black/5 dark:hover:bg-white/5 hover:text-black dark:hover:text-white transition-colors backdrop-blur-md">
                        Cancelar
                    </button>
                    <button type="button" wire:click="executeDelete"
                        class="flex-1 py-3 px-4 text-xs font-bold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600/90 dark:hover:bg-red-500 rounded-[10px] uppercase tracking-widest shadow-lg dark:shadow-[0_0_20px_rgba(220,38,38,0.4)] border border-red-600 dark:border-red-500/50 transition-all">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>