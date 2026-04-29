<div class="p-6 space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Compliance & Seguridad</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Control de documentación y aptitud médica</p>
        </div>
    </div>

    @if(session('message'))
        <div class="px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">{{ session('message') }}</div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
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
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center font-black text-zinc-400">
                        {{ substr($pax->name, 0, 1) }}
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
                            <span class="text-xs text-zinc-400">Pasaporte Iris</span>
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
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-zinc-600 text-sm">
                No hay pasajeros que coincidan con los filtros.
            </div>
        @endforelse
    </div>

    {{-- Edit Modal --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-md" style="border-color:rgba(6,182,212,0.3)">
                <h3 class="font-black uppercase tracking-widest text-cyan-400 mb-4 text-sm">Actualizar Compliance</h3>

                <form wire:submit="saveCompliance" class="space-y-4">
                    {{-- Pasaporte --}}
                    <div class="space-y-2">
                        <h4 class="font-mono-tech text-[10px] text-zinc-400 uppercase border-b border-white/5 pb-1">Pasaporte Iris</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[9px] text-zinc-500 uppercase mb-1">Número</label>
                                <input wire:model="iris_passport_number" type="text" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                            </div>
                            <div>
                                <label class="block text-[9px] text-zinc-500 uppercase mb-1">Caducidad</label>
                                <input wire:model="iris_passport_expiration" type="date" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                            </div>
                        </div>
                    </div>

                    {{-- Training --}}
                    <div class="space-y-2">
                        <h4 class="font-mono-tech text-[10px] text-zinc-400 uppercase border-b border-white/5 pb-1">Iris Training</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[9px] text-zinc-500 uppercase mb-1">Fecha Certificado</label>
                                <input wire:model="training_date" type="date" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                            </div>
                            <div>
                                <label class="block text-[9px] text-zinc-500 uppercase mb-1">Estado</label>
                                <select wire:model="training_status" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                    <option value="No Apto">No Apto</option>
                                    <option value="Apto">Apto</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Médica --}}
                    <div class="space-y-2">
                        <h4 class="font-mono-tech text-[10px] text-zinc-400 uppercase border-b border-white/5 pb-1">Aptitud Física</h4>
                        <select wire:model="physical_fitness" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                            <option value="No apto">No apto</option>
                            <option value="En entrenamiento">En entrenamiento</option>
                            <option value="Excelente">Excelente</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-white/5">
                        <button type="submit" class="flex-1 py-2.5 rounded-lg text-xs font-bold bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 hover:bg-cyan-500/30 transition-colors">Guardar Cambios</button>
                        <button type="button" wire:click="$set('showEditModal', false)" class="flex-1 py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
