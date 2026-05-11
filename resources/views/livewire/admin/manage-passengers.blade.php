<div class="p-6 md:p-8 space-y-6 relative obsidian-bg min-h-screen text-[var(--text-primary)]" 
    x-data="{ 
        showScrollTop: false, 
        showForm: window.innerWidth >= 1280 
    }" 
    @resize.window="if(window.innerWidth >= 1280) showForm = true"
    @scroll.window="showScrollTop = window.pageYOffset > 300"
    :style="'--theme-accent: var(--neon-amber); --theme-accent-soft: rgba(245, 158, 11, 0.1); --theme-accent-border: rgba(245, 158, 11, 0.3);'">
    
    <div class="w-full max-w-[1600px] mx-auto space-y-6">
        
        {{-- ══ HEADER ══ --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-orange-400/30 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-[var(--theme-accent)] tracking-tight uppercase flex items-center gap-3">
                    Pasajeros
                </h2>
                <p class="text-[var(--text-secondary)] text-sm mt-1 uppercase tracking-widest font-medium">
                    @if($filterUserName)
                        Pasajeros vinculados a: <span class="text-[var(--theme-accent)] font-bold">{{ $filterUserName }}</span>
                    @else
                        Gestión de Pasajeros
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
                    <input type="text" wire:model.live="search" placeholder="Buscar pasajero..."
                        class="tech-input block w-full pl-10 pr-4 py-3 text-xs focus:outline-none transition-all rounded-[12px]">
                </div>

                <button wire:click="toggleSort"
                    class="bg-[var(--tech-input-bg)] border border-[var(--border-glass)] text-[var(--text-primary)] px-6 py-3 rounded-[12px] text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:bg-[var(--tech-hover-bg)] transition-all w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 text-[var(--theme-accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    class="w-full py-4 bg-[var(--tech-input-bg)] border transition-all duration-300 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] flex items-center justify-center gap-3 active:scale-[0.98]
                    {{ $isEditing ? 'border-[var(--theme-accent)] text-[var(--theme-accent)] shadow-[0_0_15px_var(--theme-accent-soft)]' : 'border-[var(--border-glass)] text-[var(--text-secondary)] hover:text-[var(--text-primary)]' }}">
                    <span x-text="showForm ? 'Ocultar Formulario' : '{{ $isEditing ? 'Continuar Edición' : 'Nuevo Pasajero' }}'"></span>
                    <svg class="w-5 h-5 transition-transform duration-300" :class="showForm ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8 items-start relative">
            <div class="xl:col-span-3 order-2 xl:order-1 space-y-6">
                <div class="tech-card overflow-hidden border border-[var(--border-glass)] shadow-2xl">
                    <div class="px-6 py-4 bg-black/20 border-b border-[var(--border-glass)] flex justify-between items-center">
                        <h4 class="text-[10px] font-black text-[var(--text-secondary)] uppercase tracking-[0.3em]">Directorio de Pasajeros</h4>
                        <span class="text-[10px] font-mono text-[var(--theme-accent)] bg-[var(--theme-accent-soft)] px-2 py-0.5 rounded border border-[var(--theme-accent-border)]">
                            Pasajeros: {{ $passengers->total() }}
                        </span>
                    </div>

                    <div class="divide-y divide-[var(--border-glass)]">
                        @forelse($passengers as $pax)
                            <div class="p-6 hover:bg-[var(--tech-hover-bg)] transition-all group relative overflow-hidden">
                                <div
                                    class="absolute inset-y-0 left-0 w-1 bg-[var(--theme-accent)] transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300">
                                </div>
                                
                                <div class="flex flex-col md:flex-row justify-between gap-6 relative z-10">
                                    <div class="space-y-4 flex-1">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl border border-[var(--border-glass)] bg-black/40 flex items-center justify-center text-lg font-black text-[var(--theme-accent)] group-hover:border-[var(--theme-accent-border)] transition-all">
                                                {{ substr($pax->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="text-base font-black text-[var(--text-primary)] uppercase tracking-tight group-hover:text-[var(--theme-accent)] transition-colors">
                                                    {{ $pax->full_name }}
                                                </h4>
                                                <div class="flex items-center gap-3 mt-1">
                                                    <span class="text-[9px] font-black font-mono text-[var(--theme-accent)] bg-[var(--theme-accent-soft)] px-2 py-0.5 rounded uppercase border border-[var(--theme-accent-border)]">
                                                        {{ $pax->document_country }}: {{ $pax->document_number }}
                                                    </span>
                                                    @if(!$filterUserId && $pax->client)
                                                        <span class="text-[9px] font-mono text-[var(--text-secondary)] uppercase tracking-tighter italic">
                                                            Cliente Titular: <span class="text-[var(--text-primary)]">{{ $pax->client->name }}</span>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap gap-3 pl-16">
                                            {{-- Status Badges --}}
                                            @php
                                                $physStatus = match ($pax->physical_fitness) {
                                                    'Apto' => ['color' => 'emerald', 'label' => 'APTO'],
                                                    'En entrenamiento' => ['color' => 'amber', 'label' => 'TRAINING'],
                                                    default => ['color' => 'red', 'label' => 'NO APTO']
                                                };
                                            @endphp
                                            <div class="px-2 py-1 bg-{{ $physStatus['color'] }}-500/10 border border-{{ $physStatus['color'] }}-500/30 rounded text-[8px] font-black text-{{ $physStatus['color'] }}-400 uppercase tracking-widest">
                                                FÍSICO: {{ $physStatus['label'] }}
                                            </div>

                                            <div class="px-2 py-1 {{ $pax->hasValidTraining() ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }} border rounded text-[8px] font-black uppercase tracking-widest">
                                                ENTRENAMIENTO: {{ $pax->hasValidTraining() ? 'VIGENTE' : 'FALTA' }}
                                            </div>

                                            <div class="px-2 py-1 {{ $pax->hasValidPassport() ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }} border rounded text-[8px] font-black uppercase tracking-widest">
                                                PASAPORTE: {{ $pax->hasValidPassport() ? 'VIGENTE' : 'FALTA' }}
                                            </div>

                                            @if($pax->isFlightReady())
                                                <div class="px-2 py-1 bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 rounded text-[8px] font-black uppercase tracking-[0.2em] shadow-[0_0_10px_var(--theme-accent-soft)] animate-pulse">
                                                    ¡LISTO PARA VUELO!
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-row md:flex-col gap-3 justify-end items-end shrink-0">
                                        <div class="flex gap-2">
                                            <button type="button" wire:click="edit({{ $pax->id }})" @click="showForm = true; window.scrollTo({top: 0, behavior: 'smooth'})"
                                                class="p-2.5 rounded-lg border border-[var(--theme-accent-border)] text-[var(--theme-accent)] hover:bg-[var(--theme-accent)] hover:text-black transition-colors" title="Editar">
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
                            {{ $passengers->links('vendor.livewire.simple-tailwind') }}
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
                    @if($isEditing)
                        <div
                            class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500/0 via-amber-500 to-amber-500/0">
                        </div>
                    @endif
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6 border-b border-zinc-200 dark:border-zinc-800/50 pb-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-black uppercase tracking-[0.1em] flex items-center gap-2 {{ $isEditing ? 'text-[var(--theme-accent)]' : 'text-blue-400' }}">
                                @if($isEditing)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editando Pasajero
                                @else
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Nuevo Pasajero
                                @endif
                            </h3>
                        </div>
                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode" class="text-[10px] uppercase font-mono-tech tracking-widest text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white px-2 py-1 transition-colors border border-zinc-300 dark:border-zinc-700/50 hover:border-black/20 dark:hover:border-white/20 rounded-lg flex items-center gap-1.5" style="background: var(--tech-hover-bg)">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Nuevo
                            </button>
                        @endif
                    </div>

                    <div x-data="{ tab: 'identity' }">
                        <div class="flex bg-black/20 border-b border-[var(--border-glass)]">
                            <button @click="tab = 'identity'"
                                :class="tab === 'identity' ? 'border-b-2 border-[var(--theme-accent)] text-[var(--theme-accent)] bg-[var(--theme-accent-soft)]' : 'text-[var(--text-secondary)]'"
                                class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all">
                                Bio-Identidad
                            </button>
                            <button @click="tab = 'medical'"
                                :class="tab === 'medical' ? 'border-b-2 border-emerald-500 text-emerald-400 bg-emerald-500/5' : 'text-[var(--text-secondary)]'"
                                class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all border-l border-[var(--border-glass)]">
                                Médico
                            </button>
                            <button @click="tab = 'docs'"
                                :class="tab === 'docs' ? 'border-b-2 border-cyan-500 text-cyan-400 bg-cyan-500/5' : 'text-[var(--text-secondary)]'"
                                class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all border-l border-[var(--border-glass)]">
                                Docs
                            </button>
                        </div>

                        <form wire:submit.prevent="confirmSave" class="p-6 space-y-6">
                            
                            {{-- Tab: Identity --}}
                            <div x-show="tab === 'identity'" x-transition class="space-y-5">
                                <div>
                                    <label class="block text-[10px] font-black text-[var(--theme-accent)] mb-2 uppercase tracking-widest">Cliente Titular (Responsable) <span class="text-rose-500">*</span></label>
                                    @if($user_id && $selectedClientName)
                                        <div class="flex items-center justify-between bg-black/40 border border-[var(--theme-accent-border)] px-4 py-3 rounded-xl">
                                            <span class="text-xs text-[var(--theme-accent)] font-bold uppercase tracking-widest">{{ $selectedClientName }}</span>
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
                                                class="tech-input w-full pl-10 pr-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl border-[var(--theme-accent-border)]">
                                            
                                            @if(!empty($clientSearchResults))
                                                <div class="absolute z-20 w-full mt-2 bg-[var(--bg-obsidian)] border border-[var(--border-glass)] rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto no-scrollbar">
                                                    @foreach($clientSearchResults as $c)
                                                        <button type="button" wire:click="selectClient({{ $c['id'] }}, '{{ addslashes($c['name']) }}')" class="w-full text-left px-4 py-3 text-[10px] text-[var(--text-secondary)] hover:bg-[var(--theme-accent-soft)] hover:text-[var(--theme-accent)] transition-all border-b border-[var(--border-glass)] last:border-0 uppercase font-black tracking-widest">
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
                                        <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Nombre <span class="text-rose-500">*</span></label>
                                        <input type="text" wire:model="name" required class="tech-input w-full px-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl">
                                        @error('name') <span class="text-red-500 text-[8px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Primer Apellido <span class="text-rose-500">*</span></label>
                                            <input type="text" wire:model="primarylastname" required class="tech-input w-full px-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl">
                                            @error('primarylastname') <span class="text-red-500 text-[8px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Segundo Apellido</label>
                                            <input type="text" wire:model="secondarylastname" class="tech-input w-full px-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-[9px] font-black text-[var(--theme-accent)] uppercase tracking-widest pl-1">Nº Documento <span class="text-rose-500">*</span></label>
                                            <input type="text" wire:model="document_number" required class="tech-input w-full px-4 py-2.5 text-xs font-mono focus:outline-none transition-all rounded-xl border-[var(--theme-accent-border)]">
                                            @error('document_number') <span class="text-red-500 text-[8px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Origen / País <span class="text-rose-500">*</span></label>
                                            <select wire:model.live="document_country" required class="tech-input w-full px-4 py-2.5 text-xs focus:outline-none transition-all rounded-xl bg-[var(--tech-input-bg)]">
                                                <option value="">-- SELECCIONAR --</option>
                                                @foreach($uniqueCountries as $c)
                                                    <option value="{{ $c->country_code }}">{{ $c->country_code }}</option>
                                                @endforeach
                                            </select>
                                            @error('document_country') <span class="text-red-500 text-[8px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest pl-1">Fecha de Nacimiento <span class="text-rose-500">*</span></label>
                                        <input type="date" wire:model="birth_date" required class="tech-input w-full px-4 py-2.5 text-xs font-mono focus:outline-none transition-all rounded-xl">
                                        @error('birth_date') <span class="text-red-500 text-[8px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Tab: Medical --}}
                            <div x-show="tab === 'medical'" x-transition class="space-y-6">
                                <div class="bg-emerald-500/5 border border-emerald-500/20 p-5 rounded-xl text-center space-y-4">
                                    <h4 class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Estado Médico</h4>
                                    <select wire:model="physical_fitness"
                                        class="tech-input w-full px-4 py-3 text-xs text-center font-black uppercase tracking-widest border-emerald-500/40 focus:border-emerald-500 rounded-xl bg-black">
                                        <option value="No apto">SIN CERTIFICACIÓN / NO APTO</option>
                                        <option value="En entrenamiento">EN ENTRENAMIENTO</option>
                                        <option value="Apto">APTO</option>
                                    </select>
                                    <p class="text-[8px] text-emerald-700 font-black uppercase tracking-widest">Solo niveles "Apto" están habilitados para despegue inmediato.</p>
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
                                        Pasaporte Espacial
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
                                        Certificación Iris Training Program
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="block text-[8px] font-black text-purple-700 uppercase tracking-widest">Fecha Emisión</label>
                                            <input type="date" wire:model="training_certificate_date" class="tech-input w-full px-4 py-2.5 text-xs font-mono focus:outline-none transition-all rounded-lg border-purple-500/30">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-[8px] font-black text-purple-700 uppercase tracking-widest">Estado</label>
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
                                    class="w-full font-mono-tech font-bold uppercase tracking-widest py-3 px-4 transition-colors text-[11px] rounded-lg border flex items-center justify-center gap-2 {{ $isEditing ? 'bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-black border-amber-500/50' : 'bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 dark:text-emerald-400 hover:text-black border-emerald-500/50' }}">
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
            </div>
        </div>
    </div>

    <div x-data="{ 
        lockScroll: @entangle('showSaveModal') || @entangle('showDeleteModal')
    }"
        x-effect="lockScroll ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">

        {{-- Modal Guardar Pasajero --}}
        @if($showSaveModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md"
                    wire:click="$set('showSaveModal', false)"></div>

                <div class="relative border border-[var(--border-glass)] rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/90 backdrop-blur-xl animate-tech">
                    <div class="p-8 border-b border-[var(--border-glass)] flex flex-col items-center text-center gap-4">
                        <div class="w-14 h-14 rounded-full {{ $isEditing ? 'bg-amber-500/10 border-amber-500/30 text-amber-500 shadow-[0_0_20px_rgba(245,158,11,0.1)]' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500 shadow-[0_0_20px_rgba(16,185,129,0.1)]' }} flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">
                                {{ $isEditing ? 'Confirmar Edición' : 'Confirmar Registro' }}
                            </h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                                {{ $isEditing ? "Se procederá a la actualización de los datos de este pasajero." : "Se creará un nuevo registro de pasajero." }}
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

        {{-- Modal Eliminar Pasajero --}}
        @if($showDeleteModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md"
                    wire:click="$set('showDeleteModal', false)"></div>

                <div class="relative border border-[var(--border-glass)] rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/90 backdrop-blur-xl animate-tech">
                    <div class="p-8 border-b border-[var(--border-glass)] flex flex-col items-center text-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-500 flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(244,63,94,0.1)]">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">
                                Confirmar Eliminación</h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                                Se procederá a la eliminación del pasajero <span class="text-rose-500 font-bold uppercase">{{ $deleteImpactInfo['name'] ?? '' }}</span>.
                                <br>
                                @if(($deleteImpactInfo['active_reservations'] ?? 0) > 0)
                                    <span class="text-rose-500/80 mt-1 block font-bold uppercase tracking-widest text-[9px]">
                                        AVISO: {{ $deleteImpactInfo['active_reservations'] }} RESERVAS ACTIVAS SERÁN CANCELADAS.
                                    </span>
                                @endif
                                <span class="text-rose-500/60 mt-1 block font-bold uppercase tracking-widest text-[8px]">Esta acción es irreversible</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="$set('showDeleteModal', false)"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="executeDelete"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-[0_0_20px_rgba(225,29,72,0.3)] transition-all">
                            Eliminar
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
