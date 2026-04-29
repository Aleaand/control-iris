<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Bandeja de Misiones</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Asignaciones directas del Centro de Mando</p>
        </div>
    </div>

    @if(session('message'))
        <div class="px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">{{ session('message') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- Side Filters & Stats --}}
        <div class="space-y-4">
            <div class="tech-card rounded-xl p-4">
                <h3 class="font-mono-tech text-[10px] uppercase tracking-widest text-zinc-500 mb-3 border-b border-white/10 pb-2">Estado</h3>
                <div class="space-y-1">
                    <button wire:click="$set('filterStatus', '')" class="w-full text-left px-2 py-1.5 rounded text-xs transition-colors {{ $filterStatus === '' ? 'bg-white/10 text-white' : 'text-zinc-400 hover:bg-white/5' }}">
                        Todas <span class="float-right text-[10px] text-zinc-600">{{ array_sum($counts) }}</span>
                    </button>
                    @foreach(['Pendiente', 'Aceptada', 'En progreso', 'Completada'] as $status)
                        <button wire:click="$set('filterStatus', '{{ $status }}')" class="w-full text-left px-2 py-1.5 rounded text-xs transition-colors {{ $filterStatus === $status ? 'bg-white/10 text-white' : 'text-zinc-400 hover:bg-white/5' }}">
                            {{ $status }} <span class="float-right text-[10px] text-zinc-600">{{ $counts[$status] ?? 0 }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="tech-card rounded-xl p-4">
                <h3 class="font-mono-tech text-[10px] uppercase tracking-widest text-zinc-500 mb-3 border-b border-white/10 pb-2">Prioridad</h3>
                <select wire:model.live="filterPriority" class="w-full px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                    <option value="">Todas</option>
                    <option value="urgente">Urgente</option>
                    <option value="alta">Alta</option>
                    <option value="media">Media</option>
                    <option value="baja">Baja</option>
                </select>
            </div>
        </div>

        {{-- Tasks List --}}
        <div class="lg:col-span-3 space-y-3">
            @forelse($tasks as $task)
                @php $color = $task->priorityColor(); @endphp
                <div class="tech-card rounded-xl p-4 flex gap-4 transition-all hover:bg-white/2 border-l-2" style="border-left-color: var(--tw-colors-{{ $color }}-500)">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono-tech text-[8px] uppercase px-1.5 py-0.5 rounded bg-{{ $color }}-500/10 text-{{ $color }}-400">{{ $task->priority }}</span>
                            <span class="font-mono-tech text-[8px] uppercase text-zinc-500">{{ $task->status }}</span>
                            <span class="text-[9px] text-zinc-600">&bull;</span>
                            <span class="text-[9px] text-zinc-500">{{ $task->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="font-bold text-sm mb-1 truncate" style="color: var(--text-primary)">{{ $task->title }}</h3>
                        <p class="text-xs text-zinc-400 truncate">{{ $task->description }}</p>
                    </div>
                    <div class="flex flex-col items-end justify-between">
                        <button wire:click="viewDetail({{ $task->id }})" class="text-zinc-500 hover:text-cyan-400 transition-colors p-1" title="Ver detalle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        
                        @if($task->status === 'Pendiente')
                            <button wire:click="acceptMission({{ $task->id }})" class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-3 py-1.5 rounded hover:bg-emerald-500/20 transition-colors">Aceptar</button>
                        @elseif($task->status === 'Aceptada')
                            <button wire:click="progressMission({{ $task->id }})" class="text-[10px] font-bold text-cyan-400 bg-cyan-500/10 px-3 py-1.5 rounded hover:bg-cyan-500/20 transition-colors">Iniciar</button>
                        @elseif($task->status === 'En progreso')
                            <button wire:click="completeMission({{ $task->id }})" class="text-[10px] font-bold text-emerald-400 bg-emerald-500/20 px-3 py-1.5 rounded hover:bg-emerald-500/30 border border-emerald-500/30 transition-colors">✔ Completar</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="tech-card rounded-xl p-12 text-center text-zinc-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <p class="text-sm">Bandeja de misiones limpia. Buen trabajo.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedTask)
        @php $tc = $selectedTask->priorityColor(); @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-lg border-t-2" style="border-top-color: var(--tw-colors-{{ $tc }}-500)">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="flex gap-2 mb-2">
                            <span class="font-mono-tech text-[9px] uppercase px-2 py-0.5 rounded bg-{{ $tc }}-500/10 text-{{ $tc }}-400">{{ $selectedTask->priority }}</span>
                            <span class="font-mono-tech text-[9px] uppercase px-2 py-0.5 rounded bg-white/10 text-white">{{ $selectedTask->status }}</span>
                            <span class="font-mono-tech text-[9px] uppercase text-zinc-500 mt-0.5">{{ $selectedTask->type }}</span>
                        </div>
                        <h2 class="text-lg font-bold" style="color: var(--text-primary)">{{ $selectedTask->title }}</h2>
                        <p class="text-[10px] text-zinc-500 mt-1">Asignada por: <span class="text-zinc-400">{{ $selectedTask->creator?->name }}</span> el {{ $selectedTask->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <button wire:click="closeDetail" class="text-zinc-500 hover:text-zinc-300">✕</button>
                </div>
                
                <div class="bg-black/30 rounded-lg p-4 mb-6 border border-white/5 text-sm" style="color: var(--text-primary); white-space: pre-wrap;">{{ $selectedTask->description ?? 'Sin descripción adicional.' }}</div>

                <div class="flex justify-end gap-3">
                    @if($selectedTask->status === 'Pendiente')
                        <button wire:click="acceptMission({{ $selectedTask->id }})" class="px-4 py-2 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 transition-colors">Aceptar Misión</button>
                    @elseif($selectedTask->status === 'Aceptada')
                        <button wire:click="progressMission({{ $selectedTask->id }})" class="px-4 py-2 rounded-lg text-xs font-bold bg-cyan-500/20 text-cyan-400 hover:bg-cyan-500/30 transition-colors">Iniciar Trabajo</button>
                    @elseif($selectedTask->status === 'En progreso')
                        <button wire:click="completeMission({{ $selectedTask->id }})" class="px-4 py-2 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/30 transition-colors">✔ Marcar Completada</button>
                    @endif
                    <button wire:click="closeDetail" class="px-4 py-2 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>
