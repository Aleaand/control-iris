<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Mis Clientes</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Cartera de clientes bajo tu gestión</p>
        </div>
    </div>

    @if(session('message'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">{{ session('message') }}</div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Lista de Clientes --}}
        <div class="tech-card rounded-xl p-5">
            <div class="flex items-center gap-3 mb-4">
                <input wire:model.live="search" type="text" placeholder="Buscar cliente..." class="flex-1 px-3 py-2 rounded-lg text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
            </div>

            @if($clients->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-zinc-600">
                    <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                    <p class="text-sm">Sin clientes asignados</p>
                </div>
            @else
                <div class="space-y-2 max-h-[600px] overflow-y-auto custom-scrollbar pr-1">
                    @foreach($clients as $client)
                        <button wire:click="selectClient({{ $client->id }})"
                            class="w-full text-left flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-150 border
                            {{ $selectedClientId === $client->id
                                ? 'bg-emerald-500/10 border-emerald-500/30'
                                : 'border-transparent hover:bg-white/3 hover:border-white/5' }}">
                            <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center font-black text-sm
                                {{ $selectedClientId === $client->id ? 'bg-emerald-500 text-white' : 'bg-white/5 text-zinc-400' }}">
                                {{ substr($client->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate" style="color: var(--text-primary)">{{ $client->name }}</p>
                                <p class="font-mono-tech text-[9px] text-zinc-500 truncate">{{ $client->email }}</p>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <span class="font-mono-tech text-[9px] text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded-full">
                                    {{ $client->passengers_count }} pax
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Panel de Pasajeros --}}
        <div class="tech-card rounded-xl p-5 {{ !$selectedClientId ? 'opacity-50 pointer-events-none' : '' }}">
            @if(!$selectedClientId)
                <div class="flex flex-col items-center justify-center h-40 text-zinc-600">
                    <p class="text-sm">Selecciona un cliente</p>
                </div>
            @else
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-semibold" style="color: var(--text-primary)">{{ $selectedClient?->name }}</h2>
                        <p class="font-mono-tech text-[9px] text-zinc-500">{{ $selectedClient?->email }} · {{ $selectedClient?->phone }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="createPassenger"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-colors">
                            + Pasajero
                        </button>
                        <button wire:click="clearSelection" class="px-2 py-1.5 rounded-lg text-xs text-zinc-500 hover:text-zinc-300 transition-colors">✕</button>
                    </div>
                </div>

                {{-- Formulario Pasajero --}}
                @if($showPassengerForm)
                    <div class="mb-4 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5 space-y-3">
                        <h3 class="font-mono-tech text-[10px] uppercase tracking-widest text-emerald-400 mb-3">
                            {{ $editingPassengerId ? 'Editar Pasajero' : 'Nuevo Pasajero' }}
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Nº Documento *</label>
                                <input wire:model="pax_document_number" type="text" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                @error('pax_document_number') <p class="text-rose-400 text-[9px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">País (ISO3) *</label>
                                <input wire:model="pax_document_country" type="text" maxlength="3" placeholder="ESP" class="w-full px-3 py-2 rounded-lg text-xs uppercase" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                @error('pax_document_country') <p class="text-rose-400 text-[9px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Nombre *</label>
                                <input wire:model="pax_name" type="text" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                @error('pax_name') <p class="text-rose-400 text-[9px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Apellido 1</label>
                                <input wire:model="pax_primarylastname" type="text" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                            </div>
                            <div>
                                <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Fecha Nacimiento *</label>
                                <input wire:model="pax_birth_date" type="date" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                @error('pax_birth_date') <p class="text-rose-400 text-[9px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Grupo Sanguíneo</label>
                                <select wire:model="pax_blood_type" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                    <option value="">— Sin especificar —</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                                        <option value="{{ $bt }}">{{ $bt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Alergias</label>
                                <textarea wire:model="pax_allergies" rows="2" class="w-full px-3 py-2 rounded-lg text-xs resize-none" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)"></textarea>
                            </div>
                            <div class="col-span-2">
                                <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Aptitud Física</label>
                                <select wire:model="pax_physical_fitness" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                    <option>No apto</option>
                                    <option>En entrenamiento</option>
                                    <option>Excelente</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button wire:click="confirmSavePassenger" class="flex-1 py-2 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 border border-emerald-500/20 transition-colors">Guardar</button>
                            <button wire:click="$set('showPassengerForm', false)" class="px-4 py-2 rounded-lg text-xs text-zinc-500 hover:text-zinc-300 transition-colors">Cancelar</button>
                        </div>
                    </div>
                @endif

                {{-- Lista de Pasajeros --}}
                @if($passengers->isEmpty())
                    <div class="flex flex-col items-center justify-center py-8 text-zinc-600">
                        <p class="text-sm">Sin pasajeros. Añade el primero.</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($passengers as $pax)
                            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-white/5 hover:bg-white/3 transition-colors">
                                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center text-xs font-bold text-cyan-400">
                                    {{ substr($pax->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold" style="color: var(--text-primary)">{{ $pax->full_name }}</p>
                                    <p class="font-mono-tech text-[9px] text-zinc-500">{{ $pax->document_country }} · {{ $pax->document_number }}</p>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span title="Pasaporte" class="{{ $pax->hasValidPassport() ? 'text-emerald-400' : 'text-zinc-600' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    </span>
                                    <span title="Training" class="{{ $pax->hasValidTraining() ? 'text-emerald-400' : 'text-zinc-600' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                                    </span>
                                    <button wire:click="editPassenger({{ $pax->id }})" class="text-zinc-500 hover:text-cyan-400 transition-colors p-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <button wire:click="confirmDeletePassenger({{ $pax->id }})" class="text-zinc-500 hover:text-rose-400 transition-colors p-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Save Modal --}}
    @if($showSaveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="tech-card p-6 rounded-xl w-full max-w-sm" style="border-color:rgba(16,185,129,0.2)">
                <h3 class="font-black uppercase tracking-widest text-emerald-400 mb-3 text-sm">¿Guardar pasajero?</h3>
                <p class="text-xs text-zinc-400 mb-5">Se registrarán los datos en el sistema.</p>
                <div class="flex gap-3">
                    <button wire:click="savePassenger" class="flex-1 py-2.5 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/30 transition-colors">Confirmar</button>
                    <button wire:click="$set('showSaveModal', false)" class="flex-1 py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Cancelar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="tech-card p-6 rounded-xl w-full max-w-sm" style="border-color:rgba(244,63,94,0.2)">
                <h3 class="font-black uppercase tracking-widest text-rose-400 mb-3 text-sm">¿Eliminar pasajero?</h3>
                <p class="text-xs text-zinc-400 mb-5">Esta acción es irreversible.</p>
                <div class="flex gap-3">
                    <button wire:click="deletePassenger" class="flex-1 py-2.5 rounded-lg text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30 hover:bg-rose-500/30 transition-colors">Eliminar</button>
                    <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Cancelar</button>
                </div>
            </div>
        </div>
    @endif
</div>
