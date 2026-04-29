<div class="p-6 md:p-8 space-y-6 relative obsidian-bg min-h-screen text-[var(--text-primary)]" 
    x-data="{ showScrollTop: false, showForm: window.innerWidth >= 1280 }" 
    @resize.window="if(window.innerWidth >= 1280) showForm = true"
    @scroll.window="showScrollTop = window.pageYOffset > 300">
    
    <div class="w-full max-w-[1600px] mx-auto space-y-6">
        
        {{-- ══ HEADER ══ --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-[var(--neon-amber)]/30 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-[var(--neon-amber)] tracking-tight uppercase flex items-center gap-3">
                    Registro de Pasajeros
                </h2>
                <p class="text-[var(--text-secondary)] text-sm mt-1 uppercase tracking-widest">
                    @if($filterUserName)
                        Expedientes vinculados a: <span class="text-[var(--neon-amber)] font-bold">{{ $filterUserName }}</span>
                    @else
                        Administración de identidades físicas y biometría Iris
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3 mt-4 md:mt-0">
                @if($filterUserId)
                    <a href="{{ route('admin.users.role', 'cliente') }}"
                        class="px-4 py-2 text-[10px] font-black uppercase tracking-widest border border-[var(--border-glass)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:border-[var(--text-primary)] rounded-[10px] flex items-center gap-2 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver a Clientes
                    </a>
                @endif

                @if (session()->has('message'))
                    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-[10px] flex items-center gap-2 shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('message') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ══ SEARCH & CONTROLS ══ --}}
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 items-center">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Búsqueda por nombre, documento o ID..."
                        class="tech-input block w-full pl-10 pr-4 py-3 text-xs focus:outline-none transition-all rounded-[12px]">
                </div>

                <button wire:click="toggleSort"
                    class="bg-[var(--tech-input-bg)] border border-[var(--border-glass)] text-[var(--text-primary)] px-6 py-3 rounded-[12px] text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:bg-[var(--tech-hover-bg)] transition-all w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 text-[var(--neon-amber)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($sortDir === 'asc')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                        @endif
                    </svg>
                    Orden: {{ $sortDir === 'asc' ? 'A-Z' : 'Z-A' }}
                </button>
            </div>

            {{-- Mobile-only Toggle --}}
            <div class="xl:hidden">
                <button @click="showForm = !showForm" 
                    class="w-full py-3 bg-[var(--tech-input-bg)] border transition-all duration-300 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] flex items-center justify-center gap-3 active:scale-[0.98]
                    {{ $isEditing ? 'border-[var(--neon-amber)] text-[var(--neon-amber)] shadow-[0_0_15px_rgba(245,158,11,0.15)]' : 'border-[var(--border-glass)] text-[var(--text-secondary)] hover:text-[var(--text-primary)]' }}">
                    <svg class="w-4 h-4 transition-transform duration-300" :class="showForm ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span x-text="showForm ? 'Ocultar Formulario' : '{{ $isEditing ? 'Continuar Edición' : 'Nuevo Pasajero' }}'"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8 items-start relative">
            
            {{-- ══ LISTING (60% on XL) ══ --}}
            <div class="xl:col-span-3 order-2 xl:order-1 space-y-6">
                <div class="tech-card overflow-hidden border border-[var(--border-glass)] shadow-2xl">
                    <div class="px-6 py-4 bg-black/20 border-b border-[var(--border-glass)] flex justify-between items-center">
                        <h4 class="text-[10px] font-black text-[var(--text-secondary)] uppercase tracking-[0.3em]">Censo de Tripulación y Pasajeros</h4>
                        <span class="text-[10px] font-mono text-[var(--neon-amber)] bg-[var(--neon-amber)]/10 px-2 py-0.5 rounded border border-[var(--neon-amber)]/20">
                            EXPEDIENTES: {{ $passengers->total() }}
                        </span>
                    </div>

                    <div class="divide-y divide-[var(--border-glass)]">
                        @forelse($passengers as $pax)
                            <div class="p-6 hover:bg-[var(--tech-hover-bg)] transition-all group relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-[var(--neon-amber)]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                
                                <div class="flex flex-col md:flex-row justify-between gap-6 relative z-10">
                                    <div class="space-y-4 flex-1">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl border border-[var(--border-glass)] bg-black/40 flex items-center justify-center text-lg font-black text-[var(--neon-amber)] group-hover:border-[var(--neon-amber)]/50 transition-all">
                                                {{ substr($pax->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="text-base font-black text-[var(--text-primary)] uppercase tracking-tight group-hover:text-[var(--neon-amber)] transition-colors">
                                                    {{ $pax->full_name }}
                                                </h4>
                                                <div class="flex items-center gap-3 mt-1">
                                                    <span class="text-[9px] font-black font-mono text-[var(--neon-amber)] bg-[var(--neon-amber)]/10 px-2 py-0.5 rounded uppercase">
                                                        {{ $pax->document_country }}: {{ $pax->document_number }}
                                                    </span>
                                                    @if(!$filterUserId && $pax->client)
                                                        <span class="text-[9px] font-mono text-[var(--text-secondary)] uppercase tracking-tighter italic">
                                                            Titular: <span class="text-[var(--text-primary)]">{{ $pax->client->name }}</span>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap gap-3 pl-16">
                                            {{-- Status Badges --}}
                                            @php
                                                $physStatus = match ($pax->physical_fitness) {
                                                    'Excelente' => ['color' => 'emerald', 'label' => 'APTO'],
                                                    'En entrenamiento' => ['color' => 'amber', 'label' => 'TRAINING'],
                                                    default => ['color' => 'red', 'label' => 'NO APTO']
                                                };
                                            @endphp
                                            <div class="px-2 py-1 bg-{{ $physStatus['color'] }}-500/10 border border-{{ $physStatus['color'] }}-500/30 rounded text-[8px] font-black text-{{ $physStatus['color'] }}-400 uppercase tracking-widest">
                                                FÍSICO: {{ $physStatus['label'] }}
                                            </div>

                                            <div class="px-2 py-1 {{ $pax->hasValidTraining() ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }} border rounded text-[8px] font-black uppercase tracking-widest">
                                                ENTRENAMIENTO: {{ $pax->hasValidTraining() ? 'VIGENTE' : 'CADUCADO' }}
                                            </div>

                                            <div class="px-2 py-1 {{ $pax->hasValidPassport() ? 'bg-cyan-500/10 border-cyan-500/30 text-cyan-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }} border rounded text-[8px] font-black uppercase tracking-widest">
                                                PASAPORTE: {{ $pax->hasValidPassport() ? 'IRIS-READY' : 'FALTA' }}
                                            </div>

                                            @if($pax->isFlightReady())
                                                <div class="px-2 py-1 bg-[var(--neon-amber)]/20 border border-[var(--neon-amber)] text-[var(--neon-amber)] rounded text-[8px] font-black uppercase tracking-[0.2em] shadow-[0_0_10px_rgba(245,158,11,0.3)] animate-pulse">
                                                    READY FOR LAUNCH
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-row md:flex-col gap-3 justify-end items-end shrink-0">
                                        <div class="flex gap-2">
                                            <button type="button" wire:click="edit({{ $pax->id }})" @click="showForm = true; window.scrollTo({top: 0, behavior: 'smooth'})"
                                                class="p-2.5 rounded-lg border border-[var(--neon-amber)]/30 text-[var(--neon-amber)] hover:bg-[var(--neon-amber)] hover:text-black transition-colors" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="confirmDelete({{ $pax->id }})"
                                                class="p-2.5 rounded-lg border border-red-500/30 text-red-600 dark:text-red-500 hover:bg-red-500 hover:text-white transition-colors" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-20 text-center text-[var(--text-secondary)] opacity-50">
                                <svg class="w-16 h-16 mx-auto mb-6 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-sm uppercase font-black tracking-[0.3em]">Censo Vacío en este Sector</p>
                            </div>
                        @endforelse
                    </div>
                    
                    @if($passengers->hasPages())
                        <div class="px-6 py-4 bg-black/20 border-t border-[var(--border-glass)]">
                            {{ $passengers->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ══ FORM (40% on XL, Sticky) ══ --}}
            <div class="xl:col-span-2 order-1 xl:order-2 space-y-6 xl:sticky xl:top-8" 
                 x-show="showForm" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4">
                
                <div class="tech-card p-6 rounded-xl transition-all duration-500 relative overflow-hidden {{ $isEditing ? 'border-[var(--neon-amber)]/50 shadow-[0_0_30px_rgba(245,158,11,0.1)]' : '' }}">
                    <template x-if="isEditing">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-[var(--neon-amber)] to-transparent"></div>
                    </template>
                    
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6 border-b border-zinc-200 dark:border-zinc-800/50 pb-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-black uppercase tracking-[0.1em] flex items-center gap-2" :class="isEditing ? 'text-[var(--neon-amber)]' : 'text-[var(--text-primary)]'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span x-text="isEditing ? 'Editando Pasajero' : 'Nuevo Pasajero'"></span>
                            </h3>
                        </div>
                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode" class="text-[10px] uppercase font-mono-tech tracking-widest text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white px-2 py-1 transition-colors border border-zinc-300 dark:border-zinc-700/50 hover:border-white/20 rounded-lg" style="background: var(--tech-hover-bg)">
                                Nuevo
                            </button>
                        @endif
                    </div>

                    <div x-data="{ tab: 'identity' }">
                        <div class="flex bg-black/20 border-b border-[var(--border-glass)]">
                            <button @click="tab = 'identity'"
                                :class="tab === 'identity' ? 'border-b-2 border-[var(--neon-amber)] text-[var(--neon-amber)] bg-[var(--neon-amber)]/5' : 'text-[var(--text-secondary)]'"
                                class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all">
                                Bio-Identidad
                            </button>
                            <button @click="tab = 'medical'"
                                :class="tab === 'medical' ? 'border-b-2 border-emerald-500 text-emerald-400 bg-emerald-500/5' : 'text-[var(--text-secondary)]'"
                                class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all border-l border-[var(--border-glass)]">
                                Apta Médica
                            </button>
                            <button @click="tab = 'docs'"
                                :class="tab === 'docs' ? 'border-b-2 border-cyan-500 text-cyan-400 bg-cyan-500/5' : 'text-[var(--text-secondary)]'"
                                class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all border-l border-[var(--border-glass)]">
                                Protocolos
                            </button>
                        </div>

                        <form wire:submit.prevent="confirmSave" class="p-6 space-y-6">
                            
                            {{-- Tab: Identity --}}
                            <div x-show="tab === 'identity'" x-transition class="space-y-5">
                                <div>
                                    <label class="block text-[10px] font-black text-[var(--neon-amber)] mb-2 uppercase tracking-widest">Titular de la Cuenta (Responsable)</label>
                                    @if($user_id && $selectedClientName)
                                        <div class="flex items-center justify-between bg-black/40 border border-[var(--neon-amber)]/40 px-4 py-3 rounded-xl">
                                            <span class="text-xs text-[var(--neon-amber)] font-bold uppercase tracking-widest">{{ $selectedClientName }}</span>
                                            @if(!$filterUserId)
                                                <button type="button" wire:click="clearSelectedClient" class="text-[var(--text-secondary)] hover:text-red-500 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                                <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </div>
                                            <input type="text" wire:model.live.debounce.300ms="clientSearch" placeholder="Buscar cliente por email o nombre..."
                                                class="tech-input w-full pl-10 pr-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl border-[var(--neon-amber)]/30">
                                            
                                            @if(!empty($clientSearchResults))
                                                <div class="absolute z-20 w-full mt-2 bg-[var(--bg-obsidian)] border border-[var(--border-glass)] rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto no-scrollbar">
                                                    @foreach($clientSearchResults as $c)
                                                        <button type="button" wire:click="selectClient({{ $c['id'] }}, '{{ addslashes($c['name']) }}')" class="w-full text-left px-4 py-3 text-[10px] text-[var(--text-secondary)] hover:bg-[var(--neon-amber)]/10 hover:text-[var(--neon-amber)] transition-all border-b border-[var(--border-glass)] last:border-0 uppercase font-black tracking-widest">
                                                            {{ $c['name'] }} <span class="block opacity-50 font-mono text-[8px]">{{ $c['email'] }}</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    @error('user_id') <span class="text-red-500 text-[8px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-4 border-t border-[var(--border-glass)] pt-5">
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Nombre de Pila</label>
                                        <input type="text" wire:model="name" class="tech-input w-full px-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Primer Apellido</label>
                                            <input type="text" wire:model="primarylastname" class="tech-input w-full px-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Segundo Apellido</label>
                                            <input type="text" wire:model="secondarylastname" class="tech-input w-full px-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-[9px] font-black text-[var(--neon-amber)] uppercase tracking-widest pl-1">Nº Documento</label>
                                            <input type="text" wire:model="document_number" class="tech-input w-full px-4 py-2.5 text-xs font-mono focus:outline-none transition-all rounded-xl border-[var(--neon-amber)]/30">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Origen / País</label>
                                            <select wire:model.live="document_country" class="tech-input w-full px-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl bg-[var(--tech-input-bg)]">
                                                <option value="">-- SELECCIONAR --</option>
                                                @foreach($uniqueCountries as $c)
                                                    <option value="{{ $c->country_code }}">{{ $c->country_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Nacimiento Estelar</label>
                                        <input type="date" wire:model="birth_date" class="tech-input w-full px-4 py-2.5 text-xs font-mono focus:outline-none transition-all rounded-xl">
                                    </div>
                                </div>
                            </div>

                            {{-- Tab: Medical --}}
                            <div x-show="tab === 'medical'" x-transition class="space-y-6">
                                <div class="bg-emerald-500/5 border border-emerald-500/20 p-5 rounded-xl text-center space-y-4">
                                    <h4 class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Estado Biomecánico y Entrenamiento</h4>
                                    <select wire:model="physical_fitness"
                                        class="tech-input w-full px-4 py-3 text-xs text-center font-black uppercase tracking-widest border-emerald-500/40 focus:border-emerald-500 rounded-xl bg-black">
                                        <option value="No apto">SIN CERTIFICACIÓN / NO APTO</option>
                                        <option value="En entrenamiento">EN ENTRENAMIENTO (PROGRESO)</option>
                                        <option value="Excelente">ACREDITADO (NIVEL EXCELENTE)</option>
                                    </select>
                                    <p class="text-[8px] text-emerald-700 font-black uppercase tracking-widest">Solo niveles "Acreditado" están habilitados para despegue inmediato.</p>
                                </div>

                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Tipo de Sangre / Grupo Rh</label>
                                        <select wire:model="blood_type" class="tech-input w-full px-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl bg-[var(--tech-input-bg)]">
                                            <option value="">-- SELECCIONAR --</option>
                                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                                <option value="{{ $bt }}">{{ $bt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Contraindicaciones / Alergias</label>
                                        <textarea wire:model="allergies" rows="4" placeholder="Especificar alérgenos detectados..."
                                            class="tech-input w-full px-4 py-3 text-xs focus:outline-none transition-all rounded-xl resize-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab: Docs --}}
                            <div x-show="tab === 'docs'" x-transition class="space-y-6">
                                <div class="bg-cyan-500/5 border border-cyan-500/20 p-5 rounded-xl space-y-4 relative overflow-hidden">
                                    <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-cyan-500/10 rounded-full blur-xl"></div>
                                    <h4 class="text-[10px] font-black text-cyan-400 uppercase tracking-widest flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        Pasaporte Espacial Iris
                                    </h4>
                                    <div class="space-y-4">
                                        <div class="space-y-2">
                                            <label class="block text-[8px] font-black text-cyan-700 uppercase tracking-widest">Nº Identificador Alfanumérico</label>
                                            <input type="text" wire:model="iris_passport_number" class="tech-input w-full px-4 py-2.5 text-xs font-mono focus:outline-none transition-all rounded-lg border-cyan-500/30">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-[8px] font-black text-cyan-700 uppercase tracking-widest">Fecha de Expiración</label>
                                            <input type="date" wire:model="iris_passport_expiration" class="tech-input w-full px-4 py-2.5 text-xs font-mono focus:outline-none transition-all rounded-lg border-cyan-500/30">
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-purple-500/5 border border-purple-500/20 p-5 rounded-xl space-y-4">
                                    <h4 class="text-[10px] font-black text-purple-400 uppercase tracking-widest flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Certificación de Entrenamiento
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-[8px] font-black text-purple-700 uppercase tracking-widest">Emisión</label>
                                            <input type="date" wire:model="training_certificate_date" class="tech-input w-full px-4 py-2.5 text-xs font-mono focus:outline-none transition-all rounded-lg border-purple-500/30">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-[8px] font-black text-purple-700 uppercase tracking-widest">Calificación</label>
                                            <select wire:model="training_certificate_status" class="tech-input w-full px-4 py-2.5 text-xs font-black uppercase tracking-widest border-purple-500/30 rounded-lg bg-black">
                                                <option value="">N/A</option>
                                                <option value="Apto">APTO</option>
                                                <option value="No Apto">NO APTO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Buttons --}}
                            <div class="pt-6 border-t border-[var(--border-glass)] flex flex-col gap-3">
                                <button type="submit"
                                    class="w-full py-4 text-[10px] font-black uppercase tracking-[0.3em] transition-all rounded-xl border-2 shadow-2xl
                                    {{ $isEditing 
                                        ? 'bg-[var(--neon-amber)] text-black border-[var(--neon-amber)] shadow-[0_0_20px_rgba(245,158,11,0.2)]' 
                                        : 'bg-emerald-600 text-black border-emerald-600 shadow-[0_0_20px_rgba(16,185,129,0.2)]' }}">
                                    {{ $isEditing ? 'Actualizar Pasajero' : 'Registrar Nuevo Pasajero' }}
                                </button>
                                <button type="button" @click="showForm = false"
                                    class="xl:hidden w-full py-3 text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] border border-[var(--border-glass)] rounded-xl">
                                    Cerrar Formulario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Status Widget --}}
                <div class="tech-card p-6 bg-black/40 border-[var(--border-glass)] shadow-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-[0.3em]">Protocolo de Auditoría</h4>
                        <div class="w-2 h-2 rounded-full bg-[var(--neon-amber)] animate-ping"></div>
                    </div>
                    <p class="text-[9px] text-[var(--text-secondary)] leading-relaxed uppercase tracking-widest opacity-60">
                        Cada registro físico se encripta mediante biometría de 256 bits. Cualquier modificación requiere privilegios de Administrador de Nivel 4.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODALS --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}

    {{-- Save Confirmation --}}
    @if($showSaveModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/20 dark:bg-black/40 backdrop-blur-sm p-4">
            <div class="border border-black/10 dark:border-white/10 rounded-[15px] max-w-sm w-full overflow-hidden shadow-2xl backdrop-blur-xl bg-white/80 dark:bg-zinc-950/60"
                @click.away="$wire.set('showSaveModal', false)">
                <div class="p-6 border-b border-black/5 dark:border-white/5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-[var(--neon-amber)]/10 border border-[var(--neon-amber)]/30 text-[var(--neon-amber)] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-widest mb-1">Validación de Biometría</h3>
                        <p class="text-zinc-600 dark:text-zinc-300 text-xs leading-relaxed">
                            Se procederá a la firma digital del expediente. Esta acción es inmutable y quedará registrada en el núcleo de datos central.
                        </p>
                    </div>
                </div>
                <div class="flex p-3 gap-3 bg-zinc-100/50 dark:bg-black/30 border-t border-black/5 dark:border-white/5">
                    <button type="button" wire:click="$set('showSaveModal', false)"
                        class="flex-1 py-2.5 px-4 text-xs font-bold uppercase rounded-[10px] border border-black/10 dark:border-white/10 text-zinc-700 dark:text-zinc-300 hover:bg-black/5 dark:hover:bg-white/5 hover:text-black dark:hover:text-white transition-colors backdrop-blur-md">
                        Abortar
                    </button>
                    <button type="button" wire:click="executeSave"
                        class="flex-1 py-2.5 px-4 text-xs font-bold uppercase text-black bg-[var(--neon-amber)] rounded-[10px] shadow-lg transition-colors border border-[var(--neon-amber)]/50">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/20 dark:bg-black/40 backdrop-blur-sm p-4">
            <div class="border border-red-500/30 dark:border-red-900/30 rounded-[15px] max-w-sm w-full overflow-hidden shadow-2xl backdrop-blur-xl bg-white/80 dark:bg-zinc-950/60">
                <div class="p-6 border-b border-red-500/10 dark:border-red-900/20 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-600 dark:text-red-500 uppercase tracking-widest mb-1">Purga de Expediente</h3>
                        <p class="text-zinc-600 dark:text-zinc-300 text-xs leading-relaxed">
                            Se eliminará permanentemente a <strong class="text-zinc-900 dark:text-white font-mono">{{ $deleteImpactInfo['name'] ?? '' }}</strong>.
                            @if(($deleteImpactInfo['active_reservations'] ?? 0) > 0)
                                <span class="block mt-2 text-red-500 font-bold">AVISO: {{ $deleteImpactInfo['active_reservations'] }} RESERVAS ACTIVAS SERÁN CANCELADAS.</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex p-3 gap-3 bg-red-50/50 dark:bg-black/30 border-t border-red-500/10 dark:border-red-900/10">
                    <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="flex-1 py-2 px-4 text-xs font-bold uppercase rounded-[10px] border border-black/10 dark:border-white/10 text-zinc-700 dark:text-zinc-300 hover:bg-black/5 dark:hover:bg-white/5 hover:text-black dark:hover:text-white transition-colors backdrop-blur-md">Cancelar</button>
                    <button type="button" wire:click="executeDelete"
                        class="flex-1 py-2 px-4 text-xs font-bold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600/90 dark:hover:bg-red-500 rounded-[10px] transition-all border border-red-600 dark:border-red-500/50 shadow-lg dark:shadow-[0_0_15px_rgba(220,38,38,0.3)]">Purgar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Scroll to Top --}}
    <button x-show="showScrollTop" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="fixed bottom-6 right-6 z-[90] w-12 h-12 rounded-full bg-[var(--neon-amber)] text-black flex items-center justify-center shadow-[0_0_20px_rgba(245,158,11,0.5)] border border-[var(--neon-amber)]/50 transition-transform active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
    </button>
</div>
