<div class="min-h-screen bg-gradient-to-b from-[#050505] to-[#19191c] text-zinc-300 p-4 md:p-8" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div class="max-w-[1400px] mx-auto space-y-6">
        
        <!-- Header & Flash Message -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-orange-600 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-orange-500 tracking-tight uppercase flex items-center gap-3">
                    Pasajeros
                </h2>
                <p class="text-zinc-400 text-sm mt-1 uppercase tracking-widest">
                    @if($filterUserName)
                        Pasajeros del cliente: <span class="text-orange-400 font-bold">{{ $filterUserName }}</span>
                    @else
                        Registro de identidad física y documentación Iris
                    @endif
                </p>
            </div>
            
            <div class="flex items-center gap-3 mt-4 md:mt-0">
                @if($filterUserId)
                    <a href="{{ route('admin.users.role', 'cliente') }}" 
                        class="bg-zinc-800/80 hover:bg-zinc-700 border border-zinc-700/50 text-zinc-300 px-4 py-2 text-[10px] font-bold uppercase tracking-widest rounded-[10px] flex items-center gap-2 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Volver a Clientes
                    </a>
                @endif

                @if (session()->has('message'))
                    <div class="bg-orange-900/40 border border-orange-700/50 text-orange-400 px-4 py-2 text-sm font-medium uppercase tracking-wider rounded-[10px] flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('message') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Columna Izquierda: Lista -->
            <div class="lg:col-span-7 flex flex-col space-y-4 order-2 lg:order-1">
                
                <div class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md p-4 flex flex-col sm:flex-row gap-4 justify-between items-center rounded-[10px] shadow-lg">
                    <div class="relative w-full sm:w-2/3">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.live="search" placeholder="Buscar por Nombre o Documento..."
                            class="block w-full pl-10 bg-[#050505] border border-zinc-700/50 text-white placeholder-zinc-600 py-2 focus:outline-none focus:border-orange-500 sm:text-sm transition-colors rounded-[10px]">
                    </div>
                    
                    <div class="w-full sm:w-1/3 flex justify-end">
                        <button wire:click="toggleSort" class="bg-zinc-800/80 hover:bg-zinc-700 border border-zinc-700/50 text-white px-4 py-2 sm:text-sm font-medium flex items-center gap-2 transition-colors w-full sm:w-auto justify-center rounded-[10px] tracking-widest uppercase text-xs">
                             <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($sortDir === 'asc')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                @endif
                            </svg>
                            Orden: {{ $sortDir === 'asc' ? 'A-Z' : 'Z-A' }}
                        </button>
                    </div>
                </div>

                <div class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md rounded-[10px] shadow-lg overflow-hidden">
                    <ul class="divide-y divide-zinc-800/80">
                        @forelse($passengers as $pax)
                            <li class="p-4 hover:bg-zinc-800/50 transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4 group">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-xs font-mono text-orange-400 bg-orange-950/30 px-2 py-0.5 rounded-[5px] border border-orange-900/50 uppercase">
                                            {{ $pax->document_country }}: {{ $pax->document_number }}
                                        </span>
                                        <h4 class="text-lg font-bold text-white uppercase tracking-wide">
                                            {{ $pax->full_name }}
                                        </h4>
                                    </div>

                                    @if(!$filterUserId && $pax->client)
                                        <div class="text-[10px] text-zinc-500 font-mono mb-2">
                                            TITULAR: <span class="text-amber-400">{{ $pax->client->name }}</span> ({{ $pax->client->email }})
                                        </div>
                                    @endif

                                    <div class="flex flex-wrap gap-2">
                                        {{-- Physical fitness --}}
                                        @php
                                            $physClass = match ($pax->physical_fitness) {
                                                'Excelente' => 'text-emerald-400 bg-emerald-950/30 border-emerald-900/50',
                                                'En entrenamiento' => 'text-amber-400 bg-amber-950/30 border-amber-900/50',
                                                default => 'text-zinc-500 bg-zinc-900 border-zinc-700/50'
                                            };
                                        @endphp
                                        <div class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] {{ $physClass }}">
                                            ESTADO: {{ $pax->physical_fitness === 'No apto' ? 'NO APTO' : $pax->physical_fitness }}
                                        </div>

                                        {{-- Training --}}
                                        <div class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] {{ $pax->hasValidTraining() ? 'text-emerald-400 bg-emerald-950/30 border-emerald-900/50' : 'text-red-500 bg-red-950/30 border-red-900/50' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            TRAINING: {{ $pax->hasValidTraining() ? 'VIGENTE' : 'NO' }}
                                        </div>

                                        {{-- Pasaporte --}}
                                        <div class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] {{ $pax->hasValidPassport() ? 'text-blue-400 bg-blue-950/30 border-blue-900/50' : 'text-red-500 bg-red-950/30 border-red-900/50' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                            PASAPORTE: {{ $pax->hasValidPassport() ? 'OK' : 'NO' }}
                                        </div>

                                        {{-- Flight Ready --}}
                                        @if($pax->isFlightReady())
                                            <div class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] text-green-400 bg-green-950/30 border-green-900/50 animate-pulse">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                FLIGHT READY
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex sm:flex-col gap-2 shrink-0 border-t border-zinc-800/80 sm:border-0 pt-3 sm:pt-0">
                                    <button type="button" wire:click="edit({{ $pax->id }})"
                                        class="flex-1 sm:flex-none px-4 py-1.5 bg-zinc-800 hover:bg-amber-900/50 hover:text-amber-400 text-zinc-300 text-xs font-bold uppercase tracking-wider transition-colors border border-zinc-700/50 hover:border-amber-400 rounded-[10px] flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Editar
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $pax->id }})"
                                        class="flex-1 sm:flex-none px-4 py-1.5 bg-black/50 hover:bg-red-950/50 text-red-500/80 hover:text-red-400 text-xs font-bold uppercase tracking-wider transition-colors border border-red-900/30 hover:border-red-900/80 rounded-[10px] flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Eliminar
                                    </button>
                                </div>
                            </li>
                        @empty
                            <div class="p-12 text-center text-zinc-500">
                                <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <p class="font-medium uppercase tracking-widest text-sm">No hay Pasajeros registrados</p>
                                @if($filterUserName)
                                    <p class="text-[10px] mt-2 text-zinc-600">Este cliente todavía no tiene pasajeros vinculados.</p>
                                @endif
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Columna Derecha: Formulario Tabulado -->
            <div class="lg:col-span-5 sticky top-6 order-1 lg:order-2">
                <div class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md rounded-[10px] shadow-lg overflow-hidden transition-colors duration-500 {{ $isEditing ? 'border-2 border-amber-500/80' : 'border-2 border-zinc-500' }}" x-data="{ tab: 'identity' }">
                    
                    <div class="p-4 border-b border-zinc-800 flex justify-between items-center bg-black/40">
                        <h3 class="text-sm font-bold uppercase tracking-widest flex items-center gap-2 {{ $isEditing ? 'text-amber-400' : 'text-white' }}">
                            @if($isEditing)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Editando Pasajero
                            @else
                               <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Nuevo Pasajero
                            @endif
                        </h3>
                        
                        @if($isEditing)
                            <button type="button" wire:click="setCreateMode"
                                class="text-[10px] uppercase font-bold tracking-widest bg-zinc-800/80 hover:bg-white hover:text-black text-zinc-300 px-2.5 py-1.5 transition-colors border border-zinc-700/50 rounded-[5px] flex items-center gap-1.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Crear Nuevo
                            </button>
                        @endif
                    </div>

                    <!-- Tabs -->
                    <div class="flex bg-black">
                        <button type="button" @click="tab = 'identity'"
                            :class="tab === 'identity' ? 'bg-[#0f0f0f] border-t-2 border-orange-500 text-white' : 'bg-black text-zinc-500 hover:bg-zinc-900 hover:text-zinc-300 border-t-2 border-transparent'"
                            class="flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-colors">
                            Identidad
                        </button>
                        <button type="button" @click="tab = 'medical'"
                            :class="tab === 'medical' ? 'bg-[#0f0f0f] border-t-2 border-emerald-500 text-white' : 'bg-black text-zinc-500 hover:bg-zinc-900 hover:text-zinc-300 border-t-2 border-transparent'"
                            class="flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-colors border-l border-zinc-800">
                            Médico
                        </button>
                        <button type="button" @click="tab = 'iris'"
                            :class="tab === 'iris' ? 'bg-[#0f0f0f] border-t-2 border-purple-500 text-white' : 'bg-black text-zinc-500 hover:bg-zinc-900 hover:text-zinc-300 border-t-2 border-transparent'"
                            class="flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-colors border-l border-zinc-800">
                            Documentos
                        </button>
                    </div>

                    <form wire:submit.prevent="confirmSave" class="p-6 space-y-4">

                        <!-- TAB IDENTIDAD -->
                        <div x-show="tab === 'identity'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            
                            {{-- Titular / Cliente --}}
                            <div>
                                <label class="block text-[10px] font-bold text-amber-500 mb-1 uppercase tracking-widest flex justify-between">
                                    <span>Titular de la Cuenta (Cliente)</span>
                                </label>
                                @if($user_id && $selectedClientName)
                                    <div class="flex items-center justify-between bg-[#050505] border border-amber-900/40 px-3 py-2 rounded-[10px]">
                                        <span class="text-sm text-amber-400 font-bold">{{ $selectedClientName }}</span>
                                        @if(!$filterUserId)
                                            <button type="button" wire:click="clearSelectedClient" class="text-zinc-500 hover:text-red-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                            <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                        <input type="text" wire:model.live.debounce.300ms="clientSearch" placeholder="Buscar cliente por email o nombre..."
                                            class="w-full pl-10 bg-[#050505] border border-amber-900/40 focus:border-amber-500 text-white px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                        
                                        @if(!empty($clientSearchResults))
                                            <div class="absolute z-10 w-full mt-1 bg-[#0f0f0f] border border-zinc-700/50 rounded-[10px] shadow-lg max-h-48 overflow-y-auto">
                                                @foreach($clientSearchResults as $c)
                                                    <button type="button" wire:click="selectClient({{ $c['id'] }}, '{{ addslashes($c['name']) }}')" class="w-full text-left px-4 py-2 text-sm text-zinc-300 hover:bg-amber-900/40 hover:text-white transition-colors border-b border-zinc-800/50 last:border-0">
                                                        <div class="font-bold">{{ $c['name'] }}</div>
                                                        <div class="text-[10px] text-zinc-500 font-mono">{{ $c['email'] }}</div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @elseif(strlen($clientSearch) > 1)
                                            <div class="absolute z-10 w-full mt-1 bg-[#0f0f0f] border border-zinc-700/50 rounded-[10px] shadow-lg p-3 text-center text-xs text-zinc-500">
                                                No se ha encontrado el cliente.
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @error('user_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <hr class="border-zinc-800">

                            <div>
                                <label class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">Nombre</label>
                                <input type="text" wire:model="name"
                                    class="w-full bg-[#050505] border border-zinc-700/50 focus:border-orange-500 text-white px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px]"
                                    placeholder="Nombre del pasajero">
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">Primer Apellido</label>
                                    <input type="text" wire:model="primarylastname"
                                        class="w-full bg-[#050505] border border-zinc-700/50 focus:border-orange-500 text-white px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px]"
                                        placeholder="Apellido 1">
                                    @error('primarylastname') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">Segundo Apellido</label>
                                    <input type="text" wire:model="secondarylastname"
                                        class="w-full bg-[#050505] border border-zinc-700/50 focus:border-orange-500 text-white px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px]"
                                        placeholder="Apellido 2">
                                    @error('secondarylastname') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <hr class="border-zinc-800">

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-orange-400 mb-1 uppercase tracking-widest">Nº Identidad</label>
                                    <input type="text" wire:model="document_number"
                                        class="w-full bg-[#050505] border border-orange-900/40 focus:border-orange-500 text-orange-100 font-mono px-3 py-2 placeholder-orange-900/40 focus:outline-none transition-colors text-sm rounded-[10px]"
                                        placeholder="12345678A">
                                    @error('document_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold text-orange-400 mb-1 uppercase tracking-widest flex justify-between">
                                        <span>País</span>
                                    </label>
                                    <select wire:model.live="document_country"
                                        class="w-full bg-[#050505] border border-zinc-700/50 focus:border-orange-500 text-white px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                        <option value="" selected>-- SELECCIONAR --</option>
                                        @foreach($uniqueCountries as $c)
                                            <option value="{{ $c->country_code }}">{{ $c->country_code }}</option>
                                        @endforeach
                                    </select>
                                     @error('document_country') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">Fecha de Nacimiento</label>
                                <input type="date" wire:model="birth_date"
                                    class="w-full bg-[#050505] border border-zinc-700/50 focus:border-orange-500 text-white font-mono px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                @error('birth_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- TAB MÉDICO -->
                        <div x-show="tab === 'medical'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            
                            <div class="bg-[#050505] border border-zinc-800 rounded-[10px] p-4 text-center">
                                <label class="block text-xs font-bold text-emerald-400 mb-2 uppercase tracking-widest">
                                    Estado Físico / Entrenamiento
                                </label>
                                <select wire:model="physical_fitness"
                                    class="w-full bg-[#090909] border border-zinc-700 focus:border-emerald-500 text-white text-center font-bold px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px] tracking-wider uppercase">
                                    <option value="No apto">NO REALIZADO / NO APTO</option>
                                    <option value="En entrenamiento">EN ENTRENAMIENTO ACTIVO</option>
                                    <option value="Excelente">ACREDITADO / EXCELENTE</option>
                                </select>
                                @error('physical_fitness') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                <p class="text-[10px] text-zinc-500 mt-2">
                                    Solo pasajeros "Acreditados" califican a vuelos interplanetarios.
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">
                                        Alérgenos <span class="text-[9px] font-normal text-zinc-600">(Opcional)</span>
                                    </label>
                                    <textarea wire:model="allergies" rows="3"
                                        class="w-full bg-[#050505] border border-zinc-700/50 focus:border-emerald-500 text-white px-3 py-2 placeholder-zinc-700 focus:outline-none transition-colors text-sm rounded-[10px]"
                                        placeholder="Ej: Penicilina..."></textarea>
                                    @error('allergies') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">
                                        Grupo Sanguíneo
                                    </label>
                                    <select wire:model="blood_type"
                                        class="w-full bg-[#050505] border border-zinc-700/50 text-white px-3 py-2 focus:outline-none focus:border-emerald-500 transition-colors text-sm rounded-[10px]"
                                       >
                                        <option value="">-- SELECCIONAR --</option>
                                        <option value="A+">A Positivo (A+)</option>
                                        <option value="A-">A Negativo (A-)</option>
                                        <option value="B+">B Positivo (B+)</option>
                                        <option value="B-">B Negativo (B-)</option>
                                        <option value="AB+">AB Positivo (AB+)</option>
                                        <option value="AB-">AB Negativo (AB-)</option>
                                        <option value="O+">O Positivo (O+)</option>
                                        <option value="O-">O Negativo (O-)</option>
                                    </select>
                                    @error('blood_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- TAB DOCUMENTOS IRIS -->
                        <div x-show="tab === 'iris'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            
                            {{-- Certificado Training --}}
                            <div class="bg-[#050505] border border-emerald-900/40 rounded-[10px] p-4 space-y-3">
                                <h4 class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Certificado Iris Training
                                </h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-emerald-400 mb-1 uppercase tracking-widest">Fecha de Emisión</label>
                                        <input type="date" wire:model="training_certificate_date"
                                            class="w-full bg-[#090909] border border-emerald-900/50 focus:border-emerald-500 text-emerald-100 font-mono px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                        @error('training_certificate_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-emerald-400 mb-1 uppercase tracking-widest">Estado</label>
                                        <select wire:model="training_certificate_status"
                                            class="w-full bg-[#090909] border border-emerald-900/50 focus:border-emerald-500 text-emerald-100 px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                            <option value="">-- Sin certificar --</option>
                                            <option value="Apto">APTO</option>
                                            <option value="No Apto">NO APTO</option>
                                        </select>
                                        @error('training_certificate_status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <p class="text-[9px] text-emerald-700 mt-1 uppercase tracking-widest font-mono">Válido por 10 años. Descuento del 10% si &lt; 3 años.</p>
                            </div>

                            <hr class="border-zinc-800">

                            {{-- Pasaporte Espacial --}}
                            <div class="bg-[#050505] border border-blue-900/40 rounded-[10px] p-4 space-y-3">
                                <h4 class="text-[10px] font-bold text-blue-400 uppercase tracking-widest flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    Pasaporte Espacial
                                </h4>
                                <div>
                                    <label class="block text-[10px] font-bold text-blue-400 mb-1 uppercase tracking-widest">Identificador Alfanumérico</label>
                                    <input type="text" wire:model="iris_passport_number"
                                        class="w-full bg-[#090909] border border-blue-900/50 focus:border-blue-500 text-blue-100 font-mono px-3 py-2 placeholder-blue-900/40 focus:outline-none transition-colors text-sm rounded-[10px]"
                                        placeholder="XP-002934-F">
                                    @error('iris_passport_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-blue-400 mb-1 uppercase tracking-widest">Fecha de Expiración</label>
                                    <input type="date" wire:model="iris_passport_expiration"
                                        class="w-full bg-[#090909] border border-blue-900/50 focus:border-blue-500 text-blue-100 font-mono px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                    @error('iris_passport_expiration') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Footer Acción Guardar -->
                        <div class="pt-6 mt-4 border-t border-zinc-800">
                            <button type="submit" class="w-full {{ $isEditing ? 'bg-amber-600 hover:bg-amber-500 text-white border-amber-500' : 'bg-white hover:bg-zinc-200 text-black border-white' }} font-bold uppercase tracking-widest py-3 px-4 transition-colors text-xs rounded-[10px] border">
                                @if($isEditing)
                                    Actualizar Pasajero
                                @else
                                    Registrar Pasajero
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Confirmación --}}
    @if($showSaveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-data x-init="document.body.style.overflow='hidden'" @destroyed="document.body.style.overflow=''">
        <div class="bg-[#0f0f0f] border border-zinc-700/50 rounded-[15px] max-w-sm w-full overflow-hidden shadow-[0_0_40px_rgba(6,182,212,0.1)]" @click.away="$wire.set('showSaveModal', false)">
            <div class="p-6 border-b border-zinc-800 flex items-start gap-4">
                <div class="w-10 h-10 rounded-full {{ $isEditing ? 'bg-amber-500/10 border-amber-500/30 text-amber-400' : 'bg-orange-900/30 border-orange-700 text-orange-400' }} flex items-center justify-center border shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-1">Confirmar Pasajero</h3>
                    <p class="text-zinc-500 text-xs leading-relaxed">
                        {{ $isEditing ? '¿Guardar los cambios en este pasajero?' : '¿Registrar este nuevo pasajero?' }}
                    </p>
                </div>
            </div>
            <div class="flex bg-[#050505] p-3 gap-3">
                <button type="button" wire:click="$set('showSaveModal', false)" class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors">
                    Cancelar
                </button>
                <button type="button" wire:click="executeSave" class="flex-1 py-2.5 px-4 text-xs font-bold text-white {{ $isEditing ? 'bg-amber-600 hover:bg-amber-500' : 'bg-black border hover:bg-white hover:text-black' }} rounded-[10px] transition-colors">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Eliminar --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-data x-init="document.body.style.overflow='hidden'" @destroyed="document.body.style.overflow=''">
        <div class="bg-[#0f0f0f] border border-red-900/50 rounded-[15px] max-w-sm w-full overflow-hidden shadow-[0_0_30px_rgba(220,38,38,0.1)]" @click.away="$wire.set('showDeleteModal', false)">
            <div class="p-6 border-b border-red-900/30 flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-red-950/50 border border-red-900/50 text-red-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-red-500 uppercase tracking-widest mb-1">Eliminar Pasajero</h3>
                    <p class="text-zinc-500 text-xs leading-relaxed">
                        Se eliminará a <strong class="text-white">{{ $deleteImpactInfo['name'] ?? '' }}</strong>.
                        @if(($deleteImpactInfo['active_reservations'] ?? 0) > 0)
                            <span class="text-red-400 font-bold">{{ $deleteImpactInfo['active_reservations'] }} reservas activas serán canceladas.</span>
                        @endif
                    </p>
                    @if(($deleteImpactInfo['reservation_count'] ?? 0) > 0)
                        <div class="mt-2">
                            <span class="text-[9px] bg-red-950/50 border border-red-900/50 text-red-400 px-2 py-0.5 rounded font-bold uppercase">{{ $deleteImpactInfo['reservation_count'] }} reservas totales</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex bg-[#050505] p-3 gap-3">
                <button type="button" wire:click="$set('showDeleteModal', false)" class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors">
                    Cancelar
                </button>
                <button type="button" wire:click="executeDelete" class="flex-1 py-2.5 px-4 text-xs font-bold text-white bg-red-900 hover:bg-red-800 rounded-[10px] transition-colors border border-red-900/50">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
