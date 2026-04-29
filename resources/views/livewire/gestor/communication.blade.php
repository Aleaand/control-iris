<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Centro de Comunicación</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Registro de interacciones con clientes</p>
        </div>
    </div>

    @if(session('message'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">{{ session('message') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Lista de Clientes (Left) --}}
        <div class="tech-card rounded-xl p-5">
            <input wire:model.live="search" type="text" placeholder="Buscar cliente..." class="w-full px-3 py-2 mb-4 rounded-lg text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
            
            <div class="space-y-2 max-h-[600px] overflow-y-auto custom-scrollbar pr-1">
                @forelse($clients as $c)
                    <button wire:click="selectClient({{ $c->id }})"
                        class="w-full text-left px-3 py-2.5 rounded-xl border transition-colors flex items-center gap-3
                        {{ $selectedClientId === $c->id ? 'bg-blue-500/10 border-blue-500/30' : 'border-transparent hover:bg-white/5' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs {{ $selectedClientId === $c->id ? 'bg-blue-500 text-white' : 'bg-white/10 text-zinc-400' }}">
                            {{ substr($c->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold truncate" style="color: var(--text-primary)">{{ $c->name }}</p>
                            <p class="font-mono-tech text-[9px] text-zinc-500 truncate">{{ $c->email }}</p>
                        </div>
                    </button>
                @empty
                    <p class="text-zinc-500 text-sm text-center py-4">No hay clientes en tu cartera.</p>
                @endforelse
            </div>
        </div>

        {{-- Timeline e interacciones (Right) --}}
        <div class="lg:col-span-2">
            @if(!$selectedClientId)
                <div class="tech-card rounded-xl p-12 text-center text-zinc-500 h-full flex flex-col justify-center items-center">
                    <svg class="w-12 h-12 mb-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    <p class="text-sm">Selecciona un cliente para ver o añadir interacciones.</p>
                </div>
            @else
                <div class="tech-card rounded-xl p-5 mb-4 border-t-2" style="border-top-color: rgba(59,130,246,0.5)">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-lg font-bold" style="color: var(--text-primary)">{{ $selectedClient->name }}</h2>
                            <p class="text-xs text-zinc-400 mt-1 flex gap-4">
                                <span>📧 {{ $selectedClient->email }}</span>
                                <span>📞 {{ $selectedClient->phone ?? 'Sin teléfono' }}</span>
                            </p>
                        </div>
                        <button wire:click="openLogForm" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 transition-colors">
                            + Nueva Interacción
                        </button>
                    </div>
                </div>

                @if($showLogForm)
                    <div class="tech-card rounded-xl p-5 mb-4 border border-blue-500/20 bg-blue-500/5">
                        <form wire:submit="saveLog" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Tipo de Contacto</label>
                                    <select wire:model.live="log_type" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                        <option value="nota">📝 Nota Interna</option>
                                        <option value="llamada">📞 Llamada Telefónica</option>
                                        <option value="email">📧 Correo Electrónico</option>
                                        <option value="videollamada">🎥 Videollamada (Zoom/Meet)</option>
                                        <option value="otro">📋 Otro</option>
                                    </select>
                                </div>
                                @if($log_type === 'videollamada')
                                    <div>
                                        <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Link (Opcional)</label>
                                        <input wire:model="log_zoom_link" type="url" placeholder="https://zoom.us/j/..." class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Detalles / Notas</label>
                                <textarea wire:model="log_notes" rows="3" class="w-full px-3 py-2 rounded-lg text-xs resize-none" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)"></textarea>
                                @error('log_notes') <span class="text-[9px] text-rose-400">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 transition-colors">Guardar Registro</button>
                                <button type="button" wire:click="$set('showLogForm', false)" class="px-4 py-2 rounded-lg text-xs text-zinc-400 hover:bg-white/5 transition-colors">Cancelar</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="space-y-4">
                    @forelse($logs as $log)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-sm">
                                    {{ mb_substr($log->typeLabel(), 0, 1) }}
                                </div>
                                <div class="w-px h-full bg-white/5 my-2"></div>
                            </div>
                            <div class="flex-1 pb-4">
                                <div class="tech-card rounded-xl p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-mono-tech text-[10px] text-zinc-400 uppercase">{{ $log->typeLabel() }}</span>
                                        <span class="font-mono-tech text-[9px] text-zinc-500">{{ $log->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    <p class="text-sm" style="color: var(--text-primary); white-space: pre-wrap;">{{ $log->notes }}</p>
                                    @if($log->zoom_link)
                                        <a href="{{ $log->zoom_link }}" target="_blank" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            Abrir Sala
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-zinc-500 text-sm text-center py-8">No hay registros de contacto previos.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</div>
