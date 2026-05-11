<div class="p-6 space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Documentación & Seguridad</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Control de documentación de pasaporte y de iris training</p>
        </div>
        <div class="flex mx-2">
            <button wire:click="identifyPassportNeeds" class="px-4 py-2 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-[10px] font-bold uppercase tracking-widest hover:bg-cyan-500/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Trámites Pasaporte
            </button>
        </div>
    </div>

    @if(session('message'))
        <div class="px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">{{ session('message') }}</div>
    @endif

    @if(session('passport_request_message'))
        <div class="px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('passport_request_message') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="px-4 py-3 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-400 text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <input wire:model.live="search" type="text" placeholder="Buscar pasajero..."
            class="flex-1 min-w-[200px] px-3 py-2 rounded-lg text-sm"
            style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
        <select wire:model.live="filterStatus" class="px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
            <option value="all">Todos</option>
            <option value="ready">Listos para volar (OK)</option>
            <option value="issues">Con incidencias</option>
            <option value="urgent">Urgente (< 72h)</option>
        </select>
    </div>

    {{-- Card de Tareas con Pestañas + OFAC Tooltip --}}
    <div
        x-data="{ activeTab: 'passport' }"
        class="tech-card rounded-xl mb-8 overflow-hidden"
    >
        {{-- Tab Header --}}
        <div class="flex items-center justify-between border-b border-white/10 px-5 pt-4 pb-0">
            <div class="flex items-center gap-1">
                {{-- Tab Pasaportes --}}
                <button
                    @click="activeTab = 'passport'"
                    :class="activeTab === 'passport'
                        ? 'border-b-2 border-emerald-400 text-emerald-400 pb-3'
                        : 'text-zinc-500 hover:text-zinc-300 pb-3 border-b-2 border-transparent'"
                    class="flex items-center gap-2 px-4 text-[10px] font-black uppercase tracking-widest transition-all"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Pasaportes
                    @if($passportTasks->count() > 0)
                        <span class="ml-1 px-1.5 py-0.5 bg-emerald-500/20 text-emerald-400 rounded-full text-[8px] font-bold">{{ $passportTasks->count() }}</span>
                    @endif
                </button>

                {{-- Tab Training --}}
                <button
                    @click="activeTab = 'training'"
                    :class="activeTab === 'training'
                        ? 'border-b-2 border-cyan-400 text-cyan-400 pb-3'
                        : 'text-zinc-500 hover:text-zinc-300 pb-3 border-b-2 border-transparent'"
                    class="flex items-center gap-2 px-4 text-[10px] font-black uppercase tracking-widest transition-all"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    IRIS Training
                    @if($trainingTasks->count() > 0)
                        <span class="ml-1 px-1.5 py-0.5 bg-cyan-500/20 text-cyan-400 rounded-full text-[8px] font-bold">{{ $trainingTasks->count() }}</span>
                    @endif
                </button>
            </div>

            {{-- OFAC Info Icon --}}
            <div class="relative pb-3" x-data="{ ofacOpen: false }" @mouseenter="ofacOpen = true" @mouseleave="ofacOpen = false">
                <button class="w-7 h-7 flex items-center justify-center rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/20 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </button>
                {{-- Tooltip OFAC --}}
                <div
                    x-show="ofacOpen"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute right-0 top-10 z-50 w-72 p-4 rounded-xl border border-emerald-500/20 shadow-2xl"
                    style="background: var(--bg-panel);"
                >
                    <p class="font-black text-[10px] uppercase tracking-widest text-emerald-400 mb-2 flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Motor OFAC Activo
                    </p>
                    <p class="text-[9px] text-zinc-400 leading-relaxed mb-3">Analiza DNI, Fecha de Nacimiento y Nombre para detectar riesgos antes de tramitar el Pasaporte Estelar.</p>
                    <div class="grid grid-cols-3 gap-1.5 text-center">
                        <div class="p-1.5 rounded bg-emerald-500/10 border border-emerald-500/20">
                            <span class="block text-emerald-400 font-bold text-[10px]">< 40%</span>
                            <span class="text-[7px] text-zinc-500 uppercase">Limpio</span>
                        </div>
                        <div class="p-1.5 rounded bg-amber-500/10 border border-amber-500/20">
                            <span class="block text-amber-400 font-bold text-[10px]">40-75%</span>
                            <span class="text-[7px] text-zinc-500 uppercase">Revisión</span>
                        </div>
                        <div class="p-1.5 rounded bg-rose-500/10 border border-rose-500/20">
                            <span class="block text-rose-400 font-bold text-[10px]">> 75%</span>
                            <span class="text-[7px] text-zinc-500 uppercase">Bloqueo</span>
                        </div>
                    </div>
                    @if(session('passport_request_message'))
                        <div class="mt-3 px-2 py-1.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px]">
                            {{ session('passport_request_message') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="p-5">
            {{-- Panel Pasaportes --}}
            <div x-show="activeTab === 'passport'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-3 max-h-[340px] overflow-y-auto pr-2">
                    @forelse($passportTasks as $task)
                        <div class="p-3 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 transition-colors group">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex flex-col">
                                    <span class="font-bold text-xs" style="color:var(--text-primary)">{{ $task->title }}</span>
                                    @if(isset($task->flight_date))
                                        <span class="text-[8px] text-cyan-400 font-mono-tech uppercase">Vuelo: {{ \Carbon\Carbon::parse($task->flight_date)->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                                <span class="font-mono-tech text-[9px] px-2 py-0.5 rounded text-white bg-{{ $task->priorityColor() }}-500">
                                    {{ strtoupper($task->priority) }}
                                </span>
                            </div>
                            <p class="text-[10px] text-zinc-400 leading-relaxed mb-3">{{ $task->description }}</p>
                            <div class="flex items-center gap-1.5 border-t border-white/5 pt-2 mt-1">
                                @foreach(['Pendiente', 'Aceptada', 'En progreso', 'Completada'] as $st)
                                    <button
                                        wire:click="updateTaskStatus({{ $task->id }}, '{{ $st }}')"
                                        class="px-2 py-1 rounded text-[8px] font-bold uppercase transition-all {{ $task->status === $st ? 'bg-white/20 text-white border border-white/30' : 'bg-white/5 text-zinc-500 border border-transparent hover:bg-white/10' }}"
                                    >{{ $st }}</button>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 gap-3">
                            <svg class="w-10 h-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-zinc-500">No hay tareas de pasaporte pendientes.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Panel Training --}}
            <div x-show="activeTab === 'training'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="space-y-3 max-h-[340px] overflow-y-auto pr-2">
                    @forelse($trainingTasks as $task)
                        <div class="p-3 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 transition-colors group">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex flex-col">
                                    <span class="font-bold text-xs" style="color:var(--text-primary)">{{ $task->title }}</span>
                                    @if(isset($task->flight_date))
                                        <span class="text-[8px] text-cyan-400 font-mono-tech uppercase">Vuelo: {{ \Carbon\Carbon::parse($task->flight_date)->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                                <span class="font-mono-tech text-[9px] px-2 py-0.5 rounded text-white bg-{{ $task->priorityColor() }}-500">
                                    {{ strtoupper($task->priority) }}
                                </span>
                            </div>
                            <p class="text-[10px] text-zinc-400 leading-relaxed mb-3">{{ $task->description }}</p>
                            <div class="flex items-center gap-1.5 border-t border-white/5 pt-2 mt-1">
                                @foreach(['Pendiente', 'Aceptada', 'En progreso', 'Completada'] as $st)
                                    <button
                                        wire:click="updateTaskStatus({{ $task->id }}, '{{ $st }}')"
                                        class="px-2 py-1 rounded text-[8px] font-bold uppercase transition-all {{ $task->status === $st ? 'bg-white/20 text-white border border-white/30' : 'bg-white/5 text-zinc-500 border border-transparent hover:bg-white/10' }}"
                                    >{{ $st }}</button>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 gap-3">
                            <svg class="w-10 h-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-zinc-500">No hay tareas de entrenamiento pendientes.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <h2 class="font-black uppercase tracking-[0.1em] text-zinc-400 text-xs border-b border-white/10 pb-2 mb-4">Estado General de Pasajeros</h2>

    {{-- Pasajeros Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($passengers as $pax)
            <div class="tech-card rounded-xl p-5 relative overflow-hidden {{ $pax->is_72h_alert && !$pax->fully_ready ? 'border-rose-500/30 bg-rose-500/5' : '' }}">
                @if($pax->is_72h_alert)
                    <div class="absolute top-0 right-0 px-2 py-1 bg-rose-500 text-white font-black text-[9px] uppercase tracking-widest rounded-bl-lg z-10">
                        72H VUELO
                    </div>
                @endif
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center font-black text-zinc-400 overflow-hidden border border-white/10">
                        @if($pax->passport_photo)
                            <img src="{{ asset('storage/' . $pax->passport_photo) }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($pax->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm truncate" style="color: var(--text-primary)">{{ $pax->full_name }}</p>
                        <p class="font-mono-tech text-[9px] text-zinc-500 truncate">Cliente: {{ $pax->client?->name }}</p>
                    </div>
                    <button wire:click="openEdit({{ $pax->id }})" class="p-2 rounded-lg text-zinc-400 hover:text-cyan-400 hover:bg-cyan-500/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                </div>

                <div class="space-y-3">
                    {{-- Pasaporte --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="{{ $pax->passport_ok ? 'text-emerald-400' : 'text-rose-400' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </span>
                            <span class="text-xs text-zinc-400">Pasaporte Estelar</span>
                        </div>
                        @if($pax->passport_ok)
                            <span class="font-mono-tech text-[9px] text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">VÁLIDO</span>
                        @else
                            <span class="font-mono-tech text-[9px] text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-full">PENDIENTE</span>
                        @endif
                    </div>
                    {{-- Training --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="{{ $pax->training_ok ? 'text-emerald-400' : 'text-rose-400' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                            </span>
                            <span class="text-xs text-zinc-400">Iris Training</span>
                        </div>
                        @if($pax->training_ok)
                            <span class="font-mono-tech text-[9px] text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">APTO</span>
                        @else
                            <span class="font-mono-tech text-[9px] text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-full">NO APTO</span>
                        @endif
                    </div>
                    {{-- Aptitud Médica --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="{{ $pax->medical_ok ? 'text-emerald-400' : 'text-rose-400' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </span>
                            <span class="text-xs text-zinc-400">Aptitud Física</span>
                        </div>
                        <span class="font-mono-tech text-[9px] px-2 py-0.5 rounded-full {{ $pax->medical_ok ? 'text-emerald-400 bg-emerald-500/10' : 'text-rose-400 bg-rose-500/10' }}">
                            {{ strtoupper($pax->physical_fitness) }}
                        </span>
                    </div>
                </div>

                @if($pax->next_flight)
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Próximo Vuelo</p>
                        <p class="text-xs" style="color: var(--text-primary)">{{ $pax->next_flight->spaceFlight?->destination?->name }} — {{ $pax->next_flight->spaceFlight?->departure_date?->format('d/m/Y') }}</p>
                    </div>
                @endif
                
                <div class="mt-4 pt-4 border-t border-white/5 flex flex-col gap-2">
                    <button wire:click="analyzePassengerForPassport({{ $pax->id }})" 
                            wire:loading.attr="disabled"
                            wire:target="analyzePassengerForPassport({{ $pax->id }})"
                            class="w-full py-2 rounded-lg text-[10px] font-bold uppercase bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="analyzePassengerForPassport({{ $pax->id }})">Analizar Pasajero</span>
                        <span wire:loading wire:target="analyzePassengerForPassport({{ $pax->id }})">Analizando en Trade.gov...</span>
                    </button>

                    @if($pax->passport_status === 'pending')
                        <div class="mt-2 p-2 bg-amber-500/10 border border-amber-500/20 rounded-lg">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></div>
                                <span class="text-[8px] font-bold text-amber-400 uppercase">Esperando respuesta</span>
                            </div>
                            <button wire:click="openFinalizarPasaporte({{ $pax->id }})" class="w-full py-1.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded text-[9px] font-black uppercase hover:bg-emerald-500/30 transition-all">
                                Cargar Aprobación
                            </button>
                        </div>
                    @elseif($pax->passport_status === 'active')
                        <div class="mt-2 p-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded flex items-center justify-between">
                            <span class="text-[8px] font-bold text-emerald-400 uppercase tracking-widest">Activo</span>
                            <a href="{{ asset('storage/' . $pax->passport_pdf) }}" target="_blank" class="text-[8px] text-zinc-500 hover:text-white underline uppercase">PDF Oficial</a>
                        </div>
                    @elseif(!$pax->passport_ok)
                        <button wire:click="openTramitarPasaporte({{ $pax->id }})"
                                class="w-full py-2 rounded-lg text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-colors">
                             Tramitar Pasaporte
                        </button>
                    @else
                        <button wire:click="openRevisarPasaporte({{ $pax->id }})"
                                class="w-full py-2 rounded-lg text-[10px] font-bold uppercase bg-purple-500/10 text-purple-400 border border-purple-500/20 hover:bg-purple-500/20 transition-colors">
                              Revisar Pasaporte
                        </button>
                    @endif

                    {{-- Botón IRIS Training --}}
                    @if(!$pax->training_ok)
                        <button wire:click="openTrainingForPassenger({{ $pax->id }})"
                                class="w-full py-2 rounded-lg text-[10px] font-bold uppercase border transition-colors
                                {{ $pax->training_certificate_date ? 'bg-amber-500/10 text-amber-400 border-amber-500/20 hover:bg-amber-500/20' : 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20 hover:bg-cyan-500/20' }}">
                            {{ $pax->training_certificate_date ? 'Renovar IRIS Training' : 'Tramitar IRIS Training' }}
                        </button>
                    @else
                        <button wire:click="openTrainingForPassenger({{ $pax->id }})"
                                class="w-full py-2 rounded-lg text-[10px] font-bold uppercase bg-white/5 text-zinc-400 border border-white/10 hover:bg-white/10 transition-colors">
                            Ver IRIS Training
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-zinc-600 text-sm">
                No hay pasajeros que coincidan con los filtros.
            </div>
        @endforelse
    </div>

    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-md relative overflow-hidden border-cyan-500/30" style="background: var(--bg-panel); color: var(--text-primary);">
                <div class="flex items-center justify-between mb-6 border-b border-white/10 pb-4">
                    <h3 class="font-black uppercase tracking-widest text-cyan-400 text-sm">Editar Documentación</h3>
                    <button wire:click="$set('showEditModal', false)" style="color: var(--text-secondary)" class="hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveCompliance" class="space-y-6">
                    <div class="space-y-2">
                        <h4 class="font-mono-tech text-[10px] uppercase border-b border-white/5 pb-1" style="color: var(--text-secondary)">Pasaporte Estelar</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Número</label>
                                <input wire:model="iris_passport_number" type="text" class="tech-input w-full px-3 py-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Caducidad</label>
                                <input wire:model="iris_passport_expiration" type="date" class="tech-input w-full px-3 py-2 text-xs">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="font-mono-tech text-[10px] uppercase border-b border-white/5 pb-1" style="color: var(--text-secondary)">Iris Training</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Fecha Certificado</label>
                                <input wire:model="training_date" type="date" class="tech-input w-full px-3 py-2 text-xs">
                            </div>
                            <div>
                                <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Estado</label>
                                <select wire:model="training_status" class="tech-input w-full px-3 py-2 text-xs">
                                    <option value="No Apto">No Apto</option>
                                    <option value="Apto">Apto</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="font-mono-tech text-[10px] uppercase border-b border-white/5 pb-1" style="color: var(--text-secondary)">Aptitud Médica</h4>
                        <select wire:model="physical_fitness" class="tech-input w-full px-3 py-2 text-xs">
                            <option value="No apto">No apto</option>
                            <option value="En entrenamiento">En entrenamiento</option>
                            <option value="Apto">Apto</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-white/10">
                        <button type="submit" class="flex-1 py-2.5 rounded-lg text-xs font-bold bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 hover:bg-cyan-500/30 transition-colors">Guardar Cambios</button>
                        <button type="button" wire:click="$set('showEditModal', false)" class="flex-1 py-2.5 rounded-lg text-xs font-bold border border-white/10 hover:bg-white/5 transition-colors" style="color: var(--text-secondary)">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Motor OFAC Analysis Modal --}}
    @if($showAnalysisModal && $analysisResults)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            {{-- Loading Overlay Interno --}}
            <div wire:loading wire:target="analyzePassengerForPassport" class="absolute inset-0 z-[70] flex flex-col items-center justify-center bg-black/60 backdrop-blur-sm rounded-xl">
                <div class="w-12 h-12 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin mb-4"></div>
                <p class="text-xs font-black uppercase tracking-widest text-emerald-400 animate-pulse">Consultando Motor de Seguridad...</p>
            </div>

            <div class="tech-card p-6 rounded-xl w-full max-w-lg relative overflow-hidden border-zinc-500/30" style="background: var(--bg-panel); color: var(--text-primary);">
                
                @if($analysisResults['status'] === 'missing_data')
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 mx-auto bg-amber-500/20 rounded-full flex items-center justify-center mb-4 text-amber-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="font-black uppercase tracking-widest text-amber-400 mb-2 text-lg">Datos Incompletos</h3>
                        <p class="text-xs mb-4" style="color: var(--text-secondary)">El pasajero <strong>{{ $analysisResults['passenger_name'] }}</strong> no tiene la información mínima requerida para ejecutar el análisis OFAC y tramitar el pasaporte.</p>
                        
                        <div class="bg-black/20 p-3 rounded-lg border border-white/5 text-left mb-6">
                            <p class="text-[9px] uppercase tracking-widest mb-2" style="color: var(--text-secondary)">Faltan los siguientes datos:</p>
                            <ul class="list-disc list-inside text-xs" style="color: var(--text-primary)">
                                @foreach($analysisResults['missing'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="flex gap-3">
                            <button wire:click="requestMissingData" class="flex-1 py-3 rounded-lg text-xs font-bold uppercase tracking-widest bg-amber-500/20 text-amber-400 border border-amber-500/30 hover:bg-amber-500/30 transition-colors">Solicitar al Cliente</button>
                            <button wire:click="$set('showAnalysisModal', false)" class="px-6 py-3 rounded-lg text-xs font-bold border border-white/5 hover:bg-white/5 transition-colors" style="color: var(--text-secondary)">Cerrar</button>
                        </div>
                    </div>
                @else
                    @php
                        $colorClass = $analysisResults['color'];
                        $textColor = $colorClass === 'green' ? 'text-emerald-400' : ($colorClass === 'amber' ? 'text-amber-400' : 'text-rose-400');
                        $bgColor = $colorClass === 'green' ? 'bg-emerald-500/10' : ($colorClass === 'amber' ? 'bg-amber-500/10' : 'bg-rose-500/10');
                        $borderColor = $colorClass === 'green' ? 'border-emerald-500/20' : ($colorClass === 'amber' ? 'border-amber-500/20' : 'border-rose-500/20');
                    @endphp

                    <div class="mb-2">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-black uppercase tracking-widest {{ $textColor }} text-lg">
                                Resultados OFAC
                            </h3>
                            <span class="text-2xl font-black {{ $textColor }}">
                                {{ $analysisResults['score'] }}%
                            </span>
                        </div>
                        
                        <div class="bg-black/20 rounded-lg p-4 border border-white/5 mb-4">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-[9px] uppercase tracking-widest mb-1" style="color: var(--text-secondary)">Pasajero</p>
                                    <p class="text-xs font-bold" style="color: var(--text-primary)">{{ $analysisResults['passenger_name'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] uppercase tracking-widest mb-1" style="color: var(--text-secondary)">DNI / Pasaporte Terrestre</p>
                                    <p class="text-xs font-bold" style="color: var(--text-primary)">{{ $analysisResults['dni'] }}</p>
                                    <p class="text-[9px] text-emerald-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Válido en fecha de vuelo
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[9px] uppercase tracking-widest mb-1" style="color: var(--text-secondary)">Fecha Nacimiento</p>
                                    <p class="text-xs font-bold" style="color: var(--text-primary)">{{ $analysisResults['dob'] }}</p>
                                </div>
                            </div>
                            
                            <div class="p-3 rounded {{ $bgColor }} border {{ $borderColor }}">
                                <p class="text-xs {{ $textColor }} font-bold">
                                    {{ $analysisResults['message'] }}
                                </p>
                            </div>

                            <div class="mt-4 pt-2 border-t border-white/5">
                                <p class="text-[8px] font-mono-tech uppercase" style="color: var(--text-secondary)">{{ $analysisResults['debug_query'] }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3">
                            <button wire:click="$set('showJsonModal', true)" type="button" class="w-full py-3 rounded-lg text-xs font-bold uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                Ver Respuesta Técnica (GET)
                            </button>
                            
                            <a href="https://sanctionssearch.ofac.treas.gov/" target="_blank" class="w-full block text-center py-3 rounded-lg text-xs font-bold uppercase tracking-widest bg-blue-500/20 text-blue-400 border border-blue-500/30 hover:bg-blue-500/30 transition-colors">
                                Verificar Manualmente (Búsqueda)
                            </a>
                            
                            <button wire:click="$set('showAnalysisModal', false)" class="w-full py-3 rounded-lg text-xs font-bold uppercase tracking-widest border border-white/5 hover:bg-white/5 transition-colors" style="color: var(--text-secondary)">
                                Cerrar
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
    @if($showBulkOfacModal && $bulkOfacSummary)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-2xl relative overflow-hidden border-emerald-500/30" style="background: var(--bg-panel); color: var(--text-primary);">
                <div class="flex items-center justify-between mb-6 border-b border-white/10 pb-4">
                    <h3 class="font-black uppercase tracking-widest text-emerald-400 text-lg">Reporte Global de Seguridad OFAC</h3>
                    <button wire:click="$set('showBulkOfacModal', false)" style="color: var(--text-secondary)" class="hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-4 gap-4 mb-8">
                    <div class="p-4 rounded-xl bg-black/20 border border-white/5 text-center">
                        <p class="text-[10px] uppercase tracking-widest mb-1" style="color: var(--text-secondary)">Total</p>
                        <p class="text-2xl font-black">{{ $bulkOfacSummary['total'] }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-center text-emerald-400">
                        <p class="text-[10px] uppercase tracking-widest mb-1">Limpios</p>
                        <p class="text-2xl font-black">{{ $bulkOfacSummary['clean'] }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-center text-amber-400">
                        <p class="text-[10px] uppercase tracking-widest mb-1">Riesgo</p>
                        <p class="text-2xl font-black">{{ $bulkOfacSummary['warning'] }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-center text-rose-400">
                        <p class="text-[10px] uppercase tracking-widest mb-1">Alertas</p>
                        <p class="text-2xl font-black">{{ $bulkOfacSummary['alert'] }}</p>
                    </div>
                </div>

                @if(count($bulkOfacSummary['details']) > 0)
                    <h4 class="font-bold text-xs uppercase tracking-widest mb-3" style="color: var(--text-primary)">Pasajeros con Incidencias</h4>
                    <div class="space-y-2 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($bulkOfacSummary['details'] as $item)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-black/20 border border-white/5">
                                <div>
                                    <p class="text-xs font-bold" style="color: var(--text-primary)">{{ $item['name'] }}</p>
                                    <p class="text-[10px]" style="color: var(--text-secondary)">{{ $item['msg'] }}</p>
                                </div>
                                <span class="font-mono-tech text-[10px] px-2 py-1 rounded {{ $item['status'] === 'RED' ? 'bg-rose-500/20 text-rose-400' : 'bg-amber-500/20 text-amber-400' }}">
                                    {{ $item['status'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center">
                        <div class="w-12 h-12 mx-auto bg-emerald-500/20 rounded-full flex items-center justify-center mb-4 text-emerald-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-sm italic" style="color: var(--text-secondary)">No se han detectado coincidencias críticas en el grupo.</p>
                    </div>
                @endif
                
                <div class="mt-8 pt-6 border-t border-white/10 flex justify-end">
                    <button wire:click="$set('showBulkOfacModal', false)" class="px-6 py-2 rounded-lg border border-white/10 text-xs font-bold hover:bg-white/5 transition-colors" style="color: var(--text-secondary)">Cerrar Reporte</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Passport Due Modal --}}
    @if($showPassportDueModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-4xl relative overflow-hidden border-cyan-500/30" style="background: var(--bg-panel); color: var(--text-primary);">
                <div class="flex items-center justify-between mb-6 border-b border-white/10 pb-4">
                    <div>
                        <h3 class="font-black uppercase tracking-widest text-cyan-400 text-lg">Pasajeros pendientes de Pasaporte</h3>
                        <p class="text-[10px] uppercase font-mono-tech mt-1" style="color: var(--text-secondary)">Incluye expirados y próximos a expirar</p>
                    </div>
                    <button wire:click="$set('showPassportDueModal', false)" style="color: var(--text-secondary)" class="hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="max-h-[450px] overflow-y-auto pr-2 custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="py-3 px-2 text-[10px] font-black uppercase tracking-widest" style="color: var(--text-secondary)">Pasajero</th>
                                <th class="py-3 px-2 text-[10px] font-black uppercase tracking-widest" style="color: var(--text-secondary)">Estado Pasaporte</th>
                                <th class="py-3 px-2 text-[10px] font-black uppercase tracking-widest" style="color: var(--text-secondary)">Próximo Vuelo</th>
                                <th class="py-3 px-2 text-[10px] font-black uppercase tracking-widest text-right" style="color: var(--text-secondary)">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($passportDuePassengers as $paxData)
                                <tr>
                                    <td class="py-4 px-2">
                                        <p class="text-xs font-bold" style="color: var(--text-primary)">{{ $paxData['name'] }}</p>
                                        <p class="text-[9px]" style="color: var(--text-secondary)">Cliente: {{ $paxData['client']['name'] }}</p>
                                    </td>
                                    <td class="py-4 px-2">
                                        @if(!$paxData['iris_passport_number'])
                                            <span class="text-[9px] font-mono-tech px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 border border-rose-500/30 uppercase">Sin Registro</span>
                                        @else
                                            <span class="text-[9px] font-mono-tech px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 border border-amber-500/30 uppercase">Expira: {{ \Carbon\Carbon::parse($paxData['iris_passport_expiration'])->format('d/m/Y') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-2">
                                        @if($paxData['next_flight_date'])
                                            <p class="text-[10px] font-mono-tech" style="color: var(--text-primary)">{{ \Carbon\Carbon::parse($paxData['next_flight_date'])->format('d/m/Y') }}</p>
                                            <p class="text-[8px] uppercase" style="color: var(--text-secondary)">En {{ \Carbon\Carbon::parse($paxData['next_flight_date'])->diffForHumans() }}</p>
                                        @else
                                            <span class="text-[9px] italic" style="color: var(--text-secondary)">Sin vuelos</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-2 text-right flex gap-1 justify-end">
                                        <button wire:click="analyzePassengerForPassport({{ $paxData['id'] }})" class="p-2 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 transition-all" title="Analizar OFAC">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </button>
                                        @if(!$paxData['iris_passport_number'])
                                            <button wire:click="openTramitarPasaporte({{ $paxData['id'] }})" class="p-2 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-all" title="Tramitar Pasaporte">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </button>
                                        @else
                                            <button wire:click="openRevisarPasaporte({{ $paxData['id'] }})" class="p-2 rounded bg-purple-500/10 text-purple-400 border border-purple-500/20 hover:bg-purple-500/20 transition-all" title="Revisar Pasaporte">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center" style="color: var(--text-secondary)">Todos los pasajeros tienen su documentación al día.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 pt-6 border-t border-white/10 flex justify-end">
                    <button wire:click="$set('showPassportDueModal', false)" class="px-6 py-2 rounded-lg border border-white/10 text-xs font-bold hover:bg-white/5 transition-colors" style="color: var(--text-secondary)">Cerrar Lista</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Technical JSON Modal --}}
    @if($showJsonModal && $analysisResults)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-md p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-4xl relative overflow-hidden border-cyan-500/30" style="background: var(--bg-panel); color: var(--text-primary);">
                <div class="flex items-center justify-between mb-6 border-b border-white/10 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-cyan-500/20 rounded-lg flex items-center justify-center text-cyan-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </div>
                        <div>
                            <h3 class="font-black uppercase tracking-widest text-cyan-400 text-sm">Respuesta API de Trade.gov</h3>
                            <p class="text-[9px] font-mono-tech" style="color: var(--text-secondary)">GET: https://data.trade.gov/consolidated_screening_list/v1/search</p>
                        </div>
                    </div>
                    <button wire:click="$set('showJsonModal', false)" style="color: var(--text-secondary)" class="p-2 hover:bg-white/5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="rounded-xl border border-white/10 p-4 overflow-y-auto overflow-x-auto max-h-[300px] custom-scrollbar" style="background: rgba(0,0,0,0.3)">
                    <pre class="text-[11px] font-mono text-emerald-400/90 leading-relaxed">{{ $analysisResults['raw_json'] }}</pre>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button @click="navigator.clipboard.writeText(atob('{{ base64_encode($analysisResults['raw_json']) }}')).then(() => alert('Contenido copiado al portapapeles'))" 
                            class="px-4 py-2 rounded-lg border border-white/10 text-[10px] font-bold hover:bg-white/5 transition-colors uppercase tracking-widest" 
                            style="color: var(--text-secondary)">
                        Copiar JSON
                    </button>
                    <button wire:click="downloadJson" 
                            class="px-4 py-2 rounded-lg border border-white/10 text-[10px] font-bold hover:bg-white/5 transition-colors uppercase tracking-widest flex items-center gap-2" 
                            style="color: var(--text-secondary)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Descargar
                    </button>
                    <button wire:click="$set('showJsonModal', false)" class="px-6 py-2 rounded-lg bg-cyan-500 text-black text-[10px] font-black uppercase tracking-widest hover:bg-cyan-400 transition-all shadow-lg shadow-cyan-900/40">Cerrar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Tramitar Pasaporte --}}
    @if($showTramitarModal && $tramitarPax)
        <div class="fixed inset-0 z-[80] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-2xl relative overflow-hidden border-emerald-500/30 flex flex-col max-h-[90vh]" style="background: var(--bg-panel); color: var(--text-primary);">
                <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4 flex-shrink-0">
                    <h3 class="font-black uppercase tracking-widest text-emerald-400 text-lg">Tramitar Pasaporte Estelar</h3>
                    <button wire:click="cancelarTramitePasaporte" style="color: var(--text-secondary)" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Nombre Completo</label>
                            <input type="text" value="{{ $tramitarPax['nombre'] }}" readonly class="tech-input w-full px-3 py-2 text-xs opacity-60">
                        </div>
                        <div>
                            <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">DNI / Pasaporte Terrestre</label>
                            <input type="text" value="{{ $tramitarPax['dni'] }}" readonly class="tech-input w-full px-3 py-2 text-xs opacity-60">
                        </div>
                        <div>
                            <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Fecha de Nacimiento</label>
                            <input type="date" value="{{ $tramitarPax['fecha_nacimiento'] }}" readonly class="tech-input w-full px-3 py-2 text-xs opacity-60">
                        </div>
                        <div>
                            <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Caducidad del DNI</label>
                            <input wire:model="tramite_caducidad_dni" type="date" class="tech-input w-full px-3 py-2 text-xs @error('tramite_caducidad_dni') border-rose-500 @enderror">
                            @error('tramite_caducidad_dni') <span class="text-[8px] text-rose-400 uppercase mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Nacionalidad</label>
                            <input wire:model="tramite_nacionalidad" type="text" class="tech-input w-full px-3 py-2 text-xs @error('tramite_nacionalidad') border-rose-500 @enderror">
                            @error('tramite_nacionalidad') <span class="text-[8px] text-rose-400 uppercase mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Tipo de Trámite</label>
                            <select wire:model="tramite_tipo" class="tech-input w-full px-3 py-2 text-xs">
                                <option value="Nuevo pasaporte">Nuevo pasaporte</option>
                                <option value="Renovar Pasaporte">Renovar Pasaporte</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Correo Electrónico</label>
                            <input wire:model="tramite_correo" type="email" class="tech-input w-full px-3 py-2 text-xs @error('tramite_correo') border-rose-500 @enderror">
                            @error('tramite_correo') <span class="text-[8px] text-rose-400 uppercase mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Teléfono</label>
                            <input wire:model="tramite_telefono" type="text" class="tech-input w-full px-3 py-2 text-xs @error('tramite_telefono') border-rose-500 @enderror">
                            @error('tramite_telefono') <span class="text-[8px] text-rose-400 uppercase mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Dirección de Envío</label>
                            <input wire:model="tramite_direccion" type="text" class="tech-input w-full px-3 py-2 text-xs @error('tramite_direccion') border-rose-500 @enderror">
                            @error('tramite_direccion') <span class="text-[8px] text-rose-400 uppercase mt-1 text-center block">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[9px] uppercase mb-1" style="color: var(--text-secondary)">Fotografía del Pasajero</label>
                            <div 
                                x-data="{ isDragging: false }"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="isDragging = false; $refs.photoInput.files = $event.dataTransfer.files; $refs.photoInput.dispatchEvent(new Event('change'))"
                                class="relative w-full p-4 border border-dashed rounded-lg text-center transition-all cursor-pointer @error('tramite_foto') border-rose-500 bg-rose-500/5 @else border-white/20 hover:bg-white/5 @enderror"
                                :class="isDragging ? 'border-emerald-500 bg-emerald-500/10' : ''"
                                @click="$refs.photoInput.click()"
                            >
                                <input type="file" wire:model.live="tramite_foto" x-ref="photoInput" class="hidden" accept="image/png, image/jpeg" wire:key="passport-photo-input-{{ $tramite_foto ? 'loaded' : 'empty' }}">
                                
                                @if($tramite_foto && !$errors->has('tramite_foto'))
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-24 h-32 rounded-lg border-2 border-emerald-500/50 overflow-hidden shadow-xl shadow-emerald-900/20 bg-black">
                                            <img src="{{ $tramite_foto->temporaryUrl() }}" class="w-full h-full object-cover">
                                        </div>
                                        <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">Foto Procesada con Éxito</p>
                                        <button type="button" wire:click="$set('tramite_foto', null)" class="text-[9px] underline" style="color: var(--text-secondary)">Cambiar imagen</button>
                                    </div>
                                @else
                                    <div class="py-4 flex flex-col items-center gap-2">
                                        <svg class="w-8 h-8" style="color: var(--text-secondary)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-xs" style="color: var(--text-secondary)">Haz clic o arrastra la imagen aquí</p>
                                        @error('tramite_foto')
                                            <p class="text-[10px] text-rose-400 font-bold uppercase">{{ $message }}</p>
                                        @else
                                            <p class="text-[9px] uppercase" style="color: var(--text-secondary)">PNG o JPG (Formatos espaciales admitidos)</p>
                                        @enderror
                                    </div>
                                @endif
    
                                {{-- Loading state --}}
                                <div wire:loading wire:target="tramite_foto" class="absolute inset-0 bg-black/60 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-5 h-5 border-2 border-emerald-500 border-t-transparent animate-spin rounded-full"></div>
                                        <p class="text-[9px] text-emerald-500 font-mono-tech">Procesando...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($showMissingDocSection)
                    <div class="mt-4 p-4 rounded-xl bg-amber-500/5 border border-amber-500/20 animate-in fade-in slide-in-from-top-2 duration-300">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-amber-400 mb-3 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Requerimiento de Documentación
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[9px] uppercase mb-2 text-zinc-500">Documentos que faltan (Selección múltiple)</label>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-2 bg-black/20 p-4 rounded-xl border border-white/5">
                                    @php
                                        $options = [
                                            'Nombre Completo',
                                            'DNI / Pasaporte Terrestre',
                                            'Fecha de Nacimiento',
                                            'Nacionalidad',
                                            'Caducidad del DNI',
                                            'Teléfono',
                                            'Fotografía para el pasaporte'
                                        ];
                                    @endphp
                                    @foreach($options as $option)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <div class="relative flex items-center justify-center">
                                                <input type="checkbox" wire:model="selectedMissingDocs" value="{{ $option }}" class="peer h-4 w-4 opacity-0 absolute cursor-pointer">
                                                <div class="h-4 w-4 border border-white/20 rounded bg-white/5 peer-checked:bg-amber-500 peer-checked:border-amber-500 transition-all"></div>
                                                <svg class="absolute w-3 h-3 text-black opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <span class="text-[10px] text-zinc-400 group-hover:text-amber-400 transition-colors uppercase">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label class="block text-[9px] uppercase mb-1 text-zinc-500">Mensaje para el cliente</label>
                                <textarea wire:model="missingDocNotes" rows="3" placeholder="Escribe aquí las instrucciones adicionales..." class="tech-input w-full px-3 py-2 text-xs resize-none"></textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button wire:click="enviarSolicitudDocumento" class="px-4 py-2 rounded-lg bg-amber-500 text-black text-[10px] font-black uppercase tracking-widest hover:bg-amber-400 transition-all shadow-lg shadow-amber-900/20">
                                    Enviar Requerimiento
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex flex-wrap gap-3 pt-4 border-t border-white/10 mt-6 flex-shrink-0">
                    <button wire:click="solicitarDocumentoFaltante" class="flex-1 min-w-[150px] py-2.5 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30 hover:bg-amber-500/20 transition-colors">Solicitar Doc. Faltante</button>
                    <button wire:click="cancelarTramitePasaporte" class="flex-1 min-w-[100px] py-2.5 rounded-lg text-xs font-bold border border-white/10 hover:bg-white/5 transition-colors" style="color: var(--text-secondary)">Limpiar y Cerrar</button>
                    <button wire:click="enviarTramitePasaporte" 
                            wire:loading.attr="disabled"
                            wire:target="enviarTramitePasaporte, tramite_foto"
                            class="flex-[2] min-w-[200px] py-2.5 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/30 transition-colors disabled:opacity-50 disabled:cursor-wait uppercase tracking-widest">
                        <span wire:loading.remove wire:target="enviarTramitePasaporte">Enviar Trámite</span>
                        <span wire:loading wire:target="enviarTramitePasaporte">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showRevisarModal && $tramitarPax)
        <div class="fixed inset-0 z-[80] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-md relative overflow-hidden border-purple-500/30 text-center" style="background: var(--bg-panel); color: var(--text-primary);">
                <div class="w-16 h-16 mx-auto bg-purple-500/20 rounded-full flex items-center justify-center mb-4 text-purple-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-black uppercase tracking-widest text-purple-400 mb-2 text-lg">Pasaporte Estelar Aprobado</h3>
                <p class="text-xs mb-6" style="color: var(--text-secondary)">El pasaporte para <strong>{{ $tramitarPax['nombre'] }}</strong> se encuentra activo y validado.</p>
                
                <div class="p-4 rounded-lg border border-white/5 bg-black/20 text-left mb-6 space-y-3">
                    <div>
                        <p class="text-[9px] uppercase tracking-widest mb-1" style="color: var(--text-secondary)">Nº Pasaporte Estelar</p>
                        <p class="text-sm font-mono-tech">{{ $tramitarPax['numero_pasaporte'] }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-widest mb-1" style="color: var(--text-secondary)">Válido Hasta</p>
                        <p class="text-sm font-mono-tech">{{ $tramitarPax['validez_pasaporte'] }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-widest mb-1" style="color: var(--text-secondary)">DNI Terrestre Asociado</p>
                        <p class="text-sm font-mono-tech">{{ $tramitarPax['dni'] }}</p>
                    </div>
                </div>
                
                <div class="flex flex-col gap-2">
                    @if($tramitarPax['pdf_path'])
                        <a href="{{ asset('storage/' . $tramitarPax['pdf_path']) }}" 
                           download="Pasaporte_Estelar_{{ str_replace(' ', '_', $tramitarPax['nombre']) }}.pdf"
                           class="w-full py-2 text-[9px] font-bold uppercase tracking-widest text-zinc-600 hover:text-zinc-400 transition-colors text-center block">
                            Descargar Pasaporte (Oficial)
                        </a>
                    @endif
                    <button wire:click="$set('showRevisarModal', false)" class="w-full py-3 rounded-lg text-xs font-bold uppercase tracking-widest border border-white/5 hover:bg-white/5 transition-colors" style="color: var(--text-secondary)">Cerrar Ficha</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Finalizar Pasaporte (Paso 2) --}}
    @if($showFinalizarModal && $finalizarPax)
        <div class="fixed inset-0 z-[80] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-lg relative overflow-hidden border-emerald-500/30 flex flex-col max-h-[90vh]" style="background: var(--bg-panel); color: var(--text-primary);">
                <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4 flex-shrink-0">
                    <div class="flex flex-col">
                        <h3 class="font-black uppercase tracking-widest text-emerald-400 text-lg">Emitir Pasaporte Estelar</h3>
                        <p class="text-[9px] text-zinc-500 uppercase">Pasajero: {{ $finalizarPax['name'] }}</p>
                    </div>
                    <button wire:click="$set('showFinalizarModal', false)" style="color: var(--text-secondary)" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar space-y-6">
                    <div class="p-4 bg-emerald-500/5 border border-emerald-500/20 rounded-xl">
                        <p class="text-[10px] text-emerald-400 uppercase font-black leading-relaxed mb-2">Acción Requerida del Gestor:</p>
                        <p class="text-[9px] text-zinc-400 uppercase leading-relaxed">Revise el correo electrónico del gestor que ha enviado el documento ({{ auth()->user()->email }}). Allí habrá llegado el informe de aprobación junto con el Pasaporte Estelar en formato PDF. Descárguelo y adjúntelo a continuación.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-[9px] uppercase mb-1 text-zinc-500">Número de Pasaporte Estelar</label>
                            <input wire:model="final_passport_number" type="text" class="tech-input w-full px-3 py-2 text-xs font-mono tracking-widest uppercase">
                            @error('final_passport_number') <span class="text-[8px] text-rose-400 uppercase">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[9px] uppercase mb-1 text-zinc-500">Fecha de Expiración (10 años estándar)</label>
                            <input wire:model="final_passport_expiration" type="date" class="tech-input w-full px-3 py-2 text-xs">
                            @error('final_passport_expiration') <span class="text-[8px] text-rose-400 uppercase">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-span-2">
                            <label class="block text-[9px] uppercase mb-1 text-zinc-500">Cargar Pasaporte PDF (Oficial)</label>
                            <div 
                                x-data="{ isDragging: false }"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="isDragging = false; $refs.pdfInput.files = $event.dataTransfer.files; $refs.pdfInput.dispatchEvent(new Event('change'))"
                                class="relative w-full p-6 border-2 border-dashed rounded-xl text-center transition-all cursor-pointer @error('final_passport_pdf') border-rose-500 bg-rose-500/5 @else border-white/10 hover:bg-white/5 @enderror"
                                :class="isDragging ? 'border-emerald-500 bg-emerald-500/10' : ''"
                                @click="$refs.pdfInput.click()"
                            >
                                <input type="file" wire:model="final_passport_pdf" x-ref="pdfInput" class="hidden" accept="application/pdf">
                                
                                @if($final_passport_pdf)
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-10 h-10 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/><path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                                        <p class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">PDF Cargado: {{ $final_passport_pdf->getClientOriginalName() }}</p>
                                    </div>
                                @else
                                    <div class="py-4 flex flex-col items-center gap-2">
                                        <svg class="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <p class="text-xs text-zinc-500">Haz clic o arrastra el PDF del pasaporte</p>
                                        @error('final_passport_pdf')
                                            <p class="text-[10px] text-rose-400 font-bold uppercase">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif

                                <div wire:loading wire:target="final_passport_pdf" class="absolute inset-0 bg-black/60 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-5 h-5 border-2 border-emerald-500 border-t-transparent animate-spin rounded-full"></div>
                                        <p class="text-[9px] text-emerald-500 font-mono-tech">Subiendo documento...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-white/10 mt-6 flex-shrink-0">
                    <button wire:click="$set('showFinalizarModal', false)" class="flex-1 py-2.5 rounded-lg text-xs font-bold border border-white/10 hover:bg-white/5 transition-colors text-zinc-400">Cancelar</button>
                    <button wire:click="finalizarTramitePasaporte" class="flex-[2] py-2.5 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/30 transition-colors uppercase tracking-widest">Activar Pasaporte y Enviar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal IRIS Training (Pasajero) --}}
    @if($showTrainingModal)
        <div class="fixed inset-0 z-[90] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="tech-card rounded-xl w-full max-w-lg border-cyan-500/30 flex flex-col max-h-[92vh]" style="background: var(--bg-panel); color: var(--text-primary);">
                
                {{-- Header --}}
                <div class="flex justify-between items-center px-6 pt-5 pb-4 border-b border-white/10 flex-shrink-0">
                    <div>
                        <h3 class="font-black uppercase tracking-widest text-cyan-400 text-base">IRIS Training</h3>
                        @if($manageTrainingPax)
                            <p class="text-[9px] text-zinc-500 uppercase mt-0.5">Pasajero: {{ $manageTrainingPax['name'] }}</p>
                        @endif
                    </div>
                    <button wire:click="$set('showTrainingModal', false)" style="color: var(--text-secondary)" class="hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

                    {{-- Sección Certificado (solo si viene de pasajero) --}}
                    @if($manageTrainingPax)
                        <div>
                            <h4 class="font-mono-tech text-[9px] uppercase text-zinc-500 mb-3 border-b border-white/5 pb-1">Certificado de Training</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[9px] uppercase mb-1 text-zinc-500">Fecha Certificado</label>
                                    <input wire:model="trainingCertDate" type="date" class="tech-input w-full px-3 py-2 text-xs">
                                    @error('trainingCertDate') <p class="text-[8px] text-rose-400 mt-0.5">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[9px] uppercase mb-1 text-zinc-500">Estado Training</label>
                                    <select wire:model="trainingCertStatus" class="tech-input w-full px-3 py-2 text-xs">
                                        <option value="Apto">Apto</option>
                                        <option value="No Apto">No Apto</option>
                                    </select>
                                    @error('trainingCertStatus') <p class="text-[8px] text-rose-400 mt-0.5">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[9px] uppercase mb-1 text-zinc-500">Aptitud Física</label>
                                    <select wire:model="trainingPhysicalFitness" class="tech-input w-full px-3 py-2 text-xs">
                                        <option value="Apto">Apto</option>
                                        <option value="No apto">No apto</option>
                                    </select>
                                    @error('trainingPhysicalFitness') <p class="text-[8px] text-rose-400 mt-0.5">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <button wire:click="saveTrainingCert" class="w-full mt-3 py-2 bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 rounded text-[10px] font-bold uppercase tracking-widest hover:bg-cyan-500/30 transition-all">
                                Guardar Certificado
                            </button>
                            @if(session('training_saved'))
                                <p class="text-[8px] text-emerald-400 font-bold uppercase mt-2 text-center animate-pulse">✓ {{ session('training_saved') }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Info duración --}}
                    <div class="p-3 rounded-lg border flex items-start gap-3 {{ $trainingIsRenewal ? 'bg-amber-500/5 border-amber-500/20' : 'bg-cyan-500/5 border-cyan-500/20' }}">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 {{ $trainingIsRenewal ? 'text-amber-400' : 'text-cyan-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-[9px] font-bold uppercase {{ $trainingIsRenewal ? 'text-amber-400' : 'text-cyan-400' }} mb-0.5">
                                {{ $trainingIsRenewal ? 'Renovación de Entrenamiento' : 'Entrenamiento Inicial' }}
                            </p>
                            <p class="text-[9px] text-zinc-400 leading-relaxed">
                                @if($trainingCertExpired)
                                    <span class="text-rose-400 font-bold uppercase tracking-tighter">[CERTIFICADO VENCIDO]</span><br>
                                    El entrenamiento previo ha expirado (más de 10 años). Es obligatorio realizar una <strong class="text-amber-400">Renovación de 1 hora</strong> para habilitar el vuelo.
                                @elseif($trainingIsRenewal)
                                    El pasajero ya dispone de entrenamiento previo. Para la renovación <strong class="text-amber-400">basta con 1 hora</strong> de actualización.
                                @else
                                    El pasajero no tiene entrenamiento previo. Se requieren <strong class="text-cyan-400">3 horas</strong> de formación completa (pueden distribuirse en varias sesiones de 1h).
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($trainingCertExpired)
                        <div class="mb-4 p-3 bg-rose-500/10 border border-rose-500/20 rounded-lg">
                            <p class="text-[9px] font-bold text-rose-400 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Documentación Vencida
                            </p>
                            <p class="text-[8px] text-zinc-500 mt-1 uppercase">El estado se ha forzado a NO APTO automáticamente hasta completar la renovación.</p>
                        </div>
                    @endif

                    {{-- Programar Entrenamiento --}}
                    <div>
                        <h4 class="font-mono-tech text-[9px] uppercase text-zinc-500 mb-3 border-b border-white/5 pb-1">Programar Entrenamiento</h4>

                        @if(session('training_error'))
                            <div class="mb-4 p-3 bg-rose-500/10 border border-rose-500/20 rounded-lg text-[10px] text-rose-400 font-bold uppercase animate-pulse">
                                {{ session('training_error') }}
                            </div>
                        @endif

                        {{-- Sesiones Programadas --}}
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[9px] font-bold uppercase text-zinc-400">Sesiones Registradas</p>
                            @php 
                                $totalComp = collect($trainingSessions)->where('status', 'Completada')->sum('hours');
                                $req = $trainingIsRenewal ? 1 : 3;
                            @endphp
                            <span class="text-[9px] font-mono-tech {{ $totalComp >= $req ? 'text-emerald-400' : 'text-amber-400' }}">
                                Total: {{ $totalComp }}/{{ $req }}h
                            </span>
                        </div>
                        <div class="space-y-2 max-h-[160px] overflow-y-auto pr-1 mb-4">
                            @forelse($trainingSessions as $index => $session)
                                @php $sessionPassed = \Carbon\Carbon::parse($session['date'])->isPast(); @endphp
                                <div class="flex items-center justify-between p-2.5 rounded-lg bg-white/5 border {{ $session['status'] === 'Completada' ? 'border-emerald-500/30 bg-emerald-500/5' : ($sessionPassed ? 'border-amber-500/20' : 'border-white/10') }}">
                                    <div>
                                        <p class="text-xs font-bold" style="color: var(--text-primary)">{{ \Carbon\Carbon::parse($session['date'])->format('d/m/Y H:i') }}</p>
                                        <p class="text-[8px] uppercase tracking-widest mt-0.5 {{ $session['status'] === 'Completada' ? 'text-emerald-400' : ($sessionPassed ? 'text-amber-400' : 'text-zinc-500') }}">
                                            {{ $session['hours'] }}h · {{ $session['status'] }}
                                            @if($sessionPassed && $session['status'] !== 'Completada') · Fecha superada @endif
                                        </p>
                                    </div>
                                    <div class="flex gap-1">
                                        @if($session['status'] === 'Programada')
                                            @if($sessionPassed)
                                                {{-- La fecha ya pasó: mostrar Aprobar --}}
                                                <button wire:click="approveTrainingSession({{ $index }})" class="px-2.5 py-1 rounded text-[8px] font-bold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/40 transition-colors">
                                                    ✓ Aprobar
                                                </button>
                                                <button wire:click="updateTrainingSessionStatus({{ $index }}, 'Ausente')" class="px-2 py-1 rounded text-[7px] font-bold uppercase bg-white/5 text-zinc-500 hover:bg-white/10">Falta</button>
                                            @else
                                                {{-- Fecha futura: Programada o cancelar --}}
                                                <span class="px-2 py-1 text-[7px] font-bold uppercase text-amber-400">Programada</span>
                                                <button wire:click="updateTrainingSessionStatus({{ $index }}, 'Ausente')" class="px-2 py-1 rounded text-[7px] font-bold uppercase bg-white/5 text-zinc-500 hover:bg-white/10">Cancelar</button>
                                            @endif
                                        @elseif($session['status'] === 'Completada')
                                            <span class="px-2 py-1 text-[7px] font-bold uppercase text-emerald-400">✓ Completada</span>
                                            <button wire:click="deleteScheduledSession({{ $index }})" class="px-2 py-1 rounded text-[7px] font-bold uppercase bg-rose-500/10 text-rose-500 border border-rose-500/20 hover:bg-rose-500/20 transition-all">Eliminar</button>
                                        @else
                                            {{-- Ausente u otros --}}
                                            <span class="px-2 py-1 text-[7px] font-bold uppercase text-rose-400">{{ $session['status'] }}</span>
                                            <button wire:click="updateTrainingSessionStatus({{ $index }}, 'Programada')" class="px-2 py-1 rounded text-[7px] font-bold uppercase bg-white/5 text-zinc-500 hover:bg-white/10">Reprogramar</button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-[10px] text-zinc-600 text-center py-3">No hay sesiones programadas aún.</p>
                            @endforelse
                        </div>

                        {{-- Añadir Nuevas Sesiones --}}
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[9px] font-bold uppercase text-zinc-400">Programar Sesiones</p>
                            <button wire:click="addNewSessionRow" class="text-[9px] font-black uppercase text-cyan-400 hover:text-cyan-300 transition-colors flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Añadir otra
                            </button>
                        </div>
                        <div class="space-y-3 bg-black/20 p-3 rounded-lg border border-white/5">
                            @foreach($newSessions as $i => $sess)
                                <div class="grid grid-cols-5 gap-2 items-center">
                                    <div class="col-span-3">
                                        <input wire:model="newSessions.{{ $i }}.date" type="datetime-local" class="tech-input w-full px-2 py-1.5 text-[10px]">
                                    </div>
                                    <div class="col-span-1">
                                        <select wire:model="newSessions.{{ $i }}.hours" class="tech-input w-full px-2 py-1.5 text-[10px]">
                                            <option value="1">1h</option>
                                            <option value="2">2h</option>
                                            <option value="3">3h</option>
                                        </select>
                                    </div>
                                    <div class="col-span-1 text-right">
                                        @if(count($newSessions) > 1)
                                            <button wire:click="removeSessionRow({{ $i }})" class="text-rose-500 hover:text-rose-400 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button wire:click="addTrainingSession" class="w-full mt-3 py-2 bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 rounded text-[10px] font-bold uppercase tracking-widest hover:bg-cyan-500/30 transition-all">
                            Programar Sesiones
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-white/10 flex-shrink-0">
                    <button wire:click="$set('showTrainingModal', false)" class="w-full py-2.5 rounded-lg text-xs font-bold border border-white/10 hover:bg-white/5 transition-colors text-zinc-400">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>
