<div class="p-6 space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">
                {{ $activeSection === 'system' ? 'Gestión de Tareas' : 'Gestión de Agenda Personal' }}
            </h1>
            <div class="flex items-center gap-2 mt-0.5">
                <div class="w-2 h-2 rounded-full {{ $activeSection === 'system' ? 'bg-cyan-500' : 'bg-violet-500' }} animate-pulse"></div>
                <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest">
                    {{ $activeSection === 'system' ? 'Flujo de tareas automatizadas' : 'Gestión de agenda y notas personales' }}
                </p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($activeSection === 'personal')
                <button wire:click="$set('showCreateModal', true)" class="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-violet-600/20 flex items-center gap-2 group">
                    <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nueva Tarea
                </button>
            @endif
            <div class="flex p-1 bg-zinc-200/50 dark:bg-black/40 rounded-xl border border-zinc-200 dark:border-white/5 backdrop-blur-xl">
                <button wire:click="$set('viewMode', 'list')" 
                    class="px-4 py-2 rounded-lg transition-all {{ $viewMode === 'list' ? 'bg-white dark:bg-white/10 text-zinc-900 dark:text-white shadow-sm dark:shadow-inner' : 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-500 dark:hover:text-zinc-300' }}" 
                    title="Vista de Lista">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <button wire:click="$set('viewMode', 'kanban')" 
                    class="px-4 py-2 rounded-lg transition-all {{ $viewMode === 'kanban' ? 'bg-white dark:bg-white/10 text-zinc-900 dark:text-white shadow-sm dark:shadow-inner' : 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-500 dark:hover:text-zinc-300' }}" 
                    title="Vista Kanban">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-white/5">
        <button wire:click="setSection('system')" class="group relative px-8 py-4 transition-all overflow-hidden">
            <span class="relative z-10 font-mono-tech text-[10px] uppercase tracking-[0.2em] {{ $activeSection === 'system' ? 'text-cyan-400 font-black' : 'text-zinc-500' }}">Sistema IRIS</span>
            @if($systemPending > 0)
                <span class="relative z-10 ml-2 bg-cyan-500 text-black px-1.5 py-0.5 rounded-full text-[8px] font-black">{{ $systemPending }}</span>
            @endif
            @if($activeSection === 'system')
                <div class="absolute bottom-0 left-0 w-full h-1 bg-cyan-500 shadow-[0_-4px_12px_rgba(6,182,212,0.5)]"></div>
            @endif
        </button>
        <button wire:click="setSection('personal')" class="group relative px-8 py-4 transition-all overflow-hidden">
            <span class="relative z-10 font-mono-tech text-[10px] uppercase tracking-[0.2em] {{ $activeSection === 'personal' ? 'text-violet-400 font-black' : 'text-zinc-500' }}">Agenda Personal</span>
            @if($personalPending > 0)
                <span class="relative z-10 ml-2 bg-violet-500 text-white px-1.5 py-0.5 rounded-full text-[8px] font-black">{{ $personalPending }}</span>
            @endif
            @if($activeSection === 'personal')
                <div class="absolute bottom-0 left-0 w-full h-1 bg-violet-500 shadow-[0_-4px_12px_rgba(139,92,246,0.5)]"></div>
            @endif
        </button>
    </div>

    {{-- Controls --}}
    <div class="flex flex-wrap items-center justify-between gap-4 p-3 tech-card border-zinc-200 dark:border-white/5 bg-zinc-50/50 dark:bg-black/40 backdrop-blur-md">
        <div class="flex items-center gap-6">
            @if($viewMode === 'list')
                <div class="flex items-center gap-1 bg-zinc-200/50 dark:bg-white/5 rounded-xl p-1 border border-zinc-200 dark:border-white/5">
                    <button wire:click="setSort('created_at')" 
                        class="px-4 py-1.5 text-[9px] font-mono-tech uppercase tracking-widest rounded-lg transition-all {{ $sortBy === 'created_at' ? 'bg-white dark:bg-white/10 text-zinc-900 dark:text-white shadow-sm dark:shadow-lg' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-600 dark:hover:text-zinc-400' }}">
                        Recientes {!! $sortBy === 'created_at' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                    </button>
                    <button wire:click="setSort('priority')" 
                        class="px-4 py-1.5 text-[9px] font-mono-tech uppercase tracking-widest rounded-lg transition-all {{ $sortBy === 'priority' ? 'bg-white dark:bg-white/10 text-zinc-900 dark:text-white shadow-sm dark:shadow-lg' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-600 dark:hover:text-zinc-400' }}">
                        Urgencia {!! $sortBy === 'priority' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                    </button>
                    <button wire:click="setSort('status')" 
                        class="px-4 py-1.5 text-[9px] font-mono-tech uppercase tracking-widest rounded-lg transition-all {{ $sortBy === 'status' ? 'bg-white dark:bg-white/10 text-zinc-900 dark:text-white shadow-sm dark:shadow-lg' : 'text-zinc-500 hover:text-zinc-700 dark:text-zinc-600 dark:hover:text-zinc-400' }}">
                        Fase {!! $sortBy === 'status' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                    </button>
                </div>
            @endif

            <div class="flex items-center gap-3">
                <span class="text-[9px] font-mono-tech text-zinc-500 dark:text-zinc-600 uppercase tracking-widest">Filtrar:</span>
                <select wire:model.live="filterStatus" class="bg-zinc-100 dark:bg-white/5 border-zinc-200 dark:border-white/10 text-[10px] font-bold text-zinc-700 dark:text-zinc-400 rounded-xl focus:ring-0 py-1.5 px-4 uppercase tracking-tighter">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente">Pendientes</option>
                    <option value="Aceptada">Aceptadas</option>
                    <option value="En progreso">En Progreso</option>
                    <option value="Completada">Completadas</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-4 ml-auto">
            <button wire:click="markAllAsSeen" class="flex items-center gap-2 px-4 py-1.5 rounded-xl border border-orange-500/30 bg-orange-500/5 text-orange-600 dark:text-orange-400 text-[9px] font-mono-tech uppercase tracking-widest hover:bg-orange-500/10 transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Marcar todo como visto
            </button>
            <label class="flex items-center gap-3 cursor-pointer group px-4 py-1.5 rounded-xl border border-zinc-200 dark:border-white/5 hover:bg-zinc-100 dark:hover:bg-white/5 transition-all">
                <input type="checkbox" wire:model.live="showCompleted" class="w-4 h-4 rounded bg-zinc-100 dark:bg-white/5 border-zinc-300 dark:border-white/10 text-{{ $activeSection === 'system' ? 'cyan' : 'violet' }}-500 focus:ring-0 transition-all">
                <span class="text-[9px] font-mono-tech text-zinc-500 uppercase tracking-widest group-hover:text-zinc-900 dark:group-hover:text-zinc-300">Mostrar completadas</span>
            </label>
        </div>
    </div>

    @if($viewMode === 'list')
        {{-- Modern List View --}}
        <div class="grid grid-cols-1 gap-2">
            @forelse($tasks as $task)
                @php 
                    $priorityColor = match($task->priority) {
                        'urgente' => 'rose',
                        'alta'    => 'amber',
                        'media'   => 'emerald',
                        'baja'    => 'zinc',
                        default   => 'zinc'
                    };
                    $statusColor = match($task->status) {
                        'Pendiente'   => 'rose',
                        'Aceptada'    => 'amber',
                        'En progreso' => 'emerald',
                        'Completada'  => 'zinc',
                        default       => 'zinc'
                    };
                    $isRead = ($task->payload['is_read'] ?? false);
                    $isSeen = ($task->payload['is_seen'] ?? false);
                @endphp
                <div class="tech-card group p-3 px-5 border transition-all duration-300 bg-black/30 hover:bg-black/50 {{ !$isSeen ? 'border-orange-500/50 shadow-[0_0_15px_rgba(249,115,22,0.1)]' : 'border-white/5 hover:border-white/20' }} {{ $isRead ? 'opacity-50' : '' }}">
                    <div class="flex items-center gap-5">
                        {{-- Indicador y Acción "Visto" --}}
                        <button wire:click="markAsSeen({{ $task->id }})" class="relative group/seen" title="Marcar como visto">
                            @if(!$isSeen)
                                <div class="w-3 h-3 rounded-full bg-orange-500 shadow-[0_0_8px_#f97316] group-hover/seen:scale-125 transition-transform"></div>
                            @else
                                <div class="w-3 h-3 rounded-full bg-zinc-800 flex items-center justify-center">
                                    <svg class="w-2 h-2 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                            @endif
                        </button>

                        {{-- Semáforo de Prioridad --}}
                        <div class="flex flex-col gap-0.5" title="Prioridad: {{ $task->priority }}">
                            <div class="w-1 h-3 rounded-t-full bg-{{ $priorityColor }}-500 shadow-[0_0_10px_rgba(var(--{{ $priorityColor }}-500),0.3)]"></div>
                            <div class="w-1 h-3 rounded-b-full bg-{{ $priorityColor }}-500/20"></div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <h3 class="text-sm font-bold text-zinc-200 uppercase tracking-tight group-hover:text-white transition-colors">{{ $task->title }}</h3>
                                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded bg-white/5 border border-white/5">
                                    <div class="w-1 h-1 rounded-full bg-{{ $statusColor }}-500"></div>
                                    <span class="text-[8px] font-mono-tech text-{{ $statusColor }}-400 uppercase font-black">{{ $task->status }}</span>
                                </div>
                            </div>
                            <p class="text-[10px] text-zinc-500 mt-1 truncate font-medium">{{ $task->description }}</p>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="hidden lg:flex flex-col items-end">
                                <span class="text-[9px] font-mono-tech text-zinc-600 uppercase">{{ $task->created_at->format('d/m/Y') }}</span>
                                <span class="text-[8px] font-mono-tech text-zinc-700">{{ $task->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                @if($activeSection === 'system' && !$isRead)
                                    <button wire:click="markAsRead({{ $task->id }})" class="w-8 h-8 flex items-center justify-center bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-black rounded-lg transition-all" title="Resolver">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                @endif
                                
                                @if($activeSection === 'personal')
                                    <button wire:click="editTask({{ $task->id }})" class="w-8 h-8 flex items-center justify-center bg-violet-500/10 hover:bg-violet-500 text-violet-500 hover:text-white rounded-lg transition-all" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="deleteTask({{ $task->id }})" wire:confirm="¿Deseas eliminar esta tarea personal?" class="w-8 h-8 flex items-center justify-center bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-lg transition-all" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif

                                <button wire:click="viewDetail({{ $task->id }})" class="w-10 h-10 flex items-center justify-center bg-white/5 text-zinc-400 hover:bg-white/10 hover:text-white rounded-xl transition-all" title="Ver Expediente">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="tech-card p-20 text-center border-white/5">
                    <p class="font-mono-tech text-[10px] text-zinc-600 uppercase tracking-[0.3em]">Bandeja de entrada vacía</p>
                </div>
            @endforelse
            
            <div class="mt-6">
                @if($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $tasks->links('vendor.livewire.simple-tailwind') }}
                @endif
            </div>
        </div>
    @else
        {{-- Advanced Kanban View --}}
        <div class="flex gap-6 overflow-x-auto pb-8 custom-scrollbar" x-data="{ 
            draggingTaskId: null,
            handleDrop(status) {
                if (this.draggingTaskId) {
                    $wire.updateStatus(this.draggingTaskId, status);
                    this.draggingTaskId = null;
                }
            }
        }">
            @php 
                $columns = ['Pendiente', 'Aceptada', 'En progreso'];
                if($showCompleted) $columns[] = 'Completada';
            @endphp
            @foreach($columns as $col)
                <div class="flex-shrink-0 w-[350px] flex flex-col gap-5" x-on:dragover.prevent="" x-on:drop="handleDrop('{{ $col }}')">
                    <div class="flex justify-between items-center px-3 border-l-2 {{ $col === 'Pendiente' ? 'border-rose-500' : ($col === 'Aceptada' ? 'border-amber-500' : 'border-emerald-500') }} bg-white/2 py-2 rounded-r-lg">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.25em] text-zinc-300">{{ $col }}</h3>
                        <span class="bg-black/40 px-2 py-0.5 rounded text-[9px] font-mono-tech text-zinc-500">{{ $tasks->where('status', $col)->count() }}</span>
                    </div>
                    <div class="flex flex-col gap-4 min-h-[600px] p-4 rounded-3xl bg-black/40 border border-white/5 transition-all duration-300" :class="draggingTaskId ? 'bg-white/5 border-dashed border-white/20' : ''">
                        @foreach($tasks->where('status', $col) as $task)
                            @php 
                                $priorityColor = match($task->priority) {
                                    'urgente' => 'rose',
                                    'alta'    => 'amber',
                                    'media'   => 'emerald',
                                    'baja'    => 'zinc',
                                    default   => 'zinc'
                                };
                                $isSeen = ($task->payload['is_seen'] ?? false);
                            @endphp
                            <div class="tech-card p-5 border {{ !$isSeen ? 'border-orange-500 shadow-[0_0_15px_rgba(249,115,22,0.15)]' : 'border-white/5' }} bg-white/2 hover:border-white/20 transition-all cursor-grab active:cursor-grabbing group relative overflow-hidden"
                                 draggable="true" x-on:dragstart="draggingTaskId = {{ $task->id }}" x-on:dragend="draggingTaskId = null" wire:key="task-kanban-{{ $task->id }}">
                                
                                <div class="absolute top-0 right-0 w-20 h-20 -mr-10 -mt-10 bg-{{ $priorityColor }}-500/10 blur-3xl rounded-full"></div>
                                
                                @if(!$isSeen)
                                    <button wire:click.stop="markAsSeen({{ $task->id }})" class="absolute top-2 left-2 w-3 h-3 rounded-full bg-orange-500 shadow-[0_0_8px_#f97316] hover:scale-125 transition-transform z-20"></button>
                                @endif

                                <div class="flex justify-between items-start mb-4 relative z-10">
                                    <span class="text-[8px] font-black uppercase tracking-widest text-{{ $priorityColor }}-500 px-2 py-0.5 rounded-full bg-{{ $priorityColor }}-500/10 border border-{{ $priorityColor }}-500/20">
                                        {{ $task->priority }}
                                    </span>
                                    <div class="flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if($activeSection === 'personal')
                                            <button wire:click.stop="editTask({{ $task->id }})" class="p-1.5 bg-white/5 hover:bg-violet-500/20 text-zinc-500 hover:text-violet-400 rounded-lg"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                        @endif
                                        <button wire:click.stop="viewDetail({{ $task->id }})" class="p-1.5 bg-white/5 hover:bg-white/10 text-zinc-400 hover:text-white rounded-lg"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></button>
                                    </div>
                                </div>
                                <h4 class="text-xs font-black text-zinc-200 mb-3 leading-tight uppercase tracking-tight group-hover:text-white transition-colors">{{ $task->title }}</h4>
                                <div class="flex justify-between items-center mt-6 relative z-10">
                                    <span class="text-[8px] px-2 py-1 rounded-lg bg-black/40 text-zinc-500 font-bold uppercase tracking-widest">{{ $task->type }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[7px] font-mono-tech text-zinc-600">{{ $task->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Create/Edit Modal (Personal) --}}
    @if($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/90 backdrop-blur-md" wire:click="$set('{{ $showCreateModal ? 'showCreateModal' : 'showEditModal' }}', false)"></div>
            <div class="relative bg-[#050505] border border-white/10 rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] animate-in zoom-in-95 duration-300">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-violet-500 to-transparent"></div>
                <div class="p-8 border-b border-white/5 bg-white/2 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-violet-400">{{ $showCreateModal ? 'Nueva Tarea' : 'Editar Tarea' }}</h3>
                        <p class="text-[9px] font-mono-tech text-zinc-600 uppercase mt-1">Sincronización con agenda personal</p>
                    </div>
                    <button wire:click="$set('{{ $showCreateModal ? 'showCreateModal' : 'showEditModal' }}', false)" class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-white/5 rounded-2xl transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-10 space-y-8">
                    <div class="space-y-2">
                        <label class="block text-[9px] font-mono-tech text-zinc-600 uppercase tracking-widest">Definición de la tarea</label>
                        <input type="text" wire:model="taskTitle" class="w-full bg-white/2 border border-white/10 rounded-2xl py-4 px-5 text-sm font-bold text-white focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 transition-all placeholder:text-zinc-700" placeholder="¿Qué necesitas recordar?">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[9px] font-mono-tech text-zinc-600 uppercase tracking-widest">Contexto y detalles</label>
                        <textarea wire:model="taskDescription" rows="4" class="w-full bg-white/2 border border-white/10 rounded-2xl py-4 px-5 text-sm font-medium text-zinc-300 focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 transition-all placeholder:text-zinc-700" placeholder="Escribe aquí los detalles..."></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[9px] font-mono-tech text-zinc-600 uppercase tracking-widest">Nivel de Urgencia</label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(['baja' => 'zinc', 'media' => 'emerald', 'alta' => 'amber', 'urgente' => 'rose'] as $p => $c)
                                <button wire:click="$set('taskPriority', '{{ $p }}')" class="py-2.5 rounded-xl border transition-all text-[8px] font-black uppercase tracking-widest {{ $taskPriority === $p ? 'bg-'.$c.'-500 text-black border-'.$c.'-500 shadow-lg shadow-'.$c.'-500/20' : 'bg-white/2 border-white/5 text-zinc-500 hover:border-white/20' }}">
                                    {{ $p }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="p-8 bg-white/2 border-t border-white/5 flex gap-4">
                    <button wire:click="$set('{{ $showCreateModal ? 'showCreateModal' : 'showEditModal' }}', false)" class="flex-1 py-4 text-[10px] font-black uppercase text-zinc-600 hover:text-zinc-400 tracking-widest transition-all">Cancelar</button>
                    @if($showCreateModal)
                        <button wire:click="createTask" class="flex-2 px-10 py-4 bg-violet-600 hover:bg-violet-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-violet-600/30">Desplegar Tarea</button>
                    @else
                        <button wire:click="updateTask" class="flex-2 px-10 py-4 bg-violet-600 hover:bg-violet-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-violet-600/30">Guardar Cambios</button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedTask)
        <div class="fixed inset-0 z-[120] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/95 backdrop-blur-xl" wire:click="$set('showDetailModal', false)"></div>
            <div class="relative bg-[#050505] border border-white/10 rounded-[3rem] w-full max-w-2xl overflow-hidden shadow-2xl animate-in fade-in zoom-in-95 duration-300">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-transparent via-{{ $activeSection === 'system' ? 'cyan' : 'violet' }}-500 to-transparent"></div>
                <div class="p-12">
                    <div class="flex justify-between items-start mb-10">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[8px] font-black px-2 py-1 rounded bg-white/5 text-zinc-500 uppercase tracking-[0.2em]">CÓDIGO #{{ $selectedTask->id }}</span>
                                <span class="text-[8px] font-black px-2 py-1 rounded bg-{{ $statusColor }}-500/10 text-{{ $statusColor }}-500 uppercase tracking-[0.2em]">{{ $selectedTask->status }}</span>
                            </div>
                            <h2 class="text-3xl font-black text-white uppercase tracking-tight leading-tight max-w-md">{{ $selectedTask->title }}</h2>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-mono-tech text-zinc-600 uppercase tracking-widest mb-2">Prioridad Técnica</p>
                            <span class="text-sm font-black uppercase p-3 rounded-2xl bg-{{ $priorityColor }}-500/10 border border-{{ $priorityColor }}-500/30 text-{{ $priorityColor }}-500 shadow-lg shadow-{{ $priorityColor }}-500/10">
                                {{ $selectedTask->priority }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-6 mb-12">
                        <div class="flex items-center gap-3 border-b border-white/5 pb-3">
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                            <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Expediente de la Tarea</h4>
                        </div>
                        <div class="text-base text-zinc-400 leading-relaxed max-h-[250px] overflow-y-auto custom-scrollbar pr-4 font-medium italic">
                            "{{ $selectedTask->description ?? 'Sin descripción adicional proporcionada por el sistema.' }}"
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @if($selectedTask->status === 'Pendiente')
                            <button wire:click="updateStatus({{ $selectedTask->id }}, 'Aceptada')" class="md:col-span-2 px-6 py-5 bg-cyan-600 hover:bg-cyan-500 text-white font-black text-[10px] uppercase tracking-[0.3em] rounded-3xl transition-all shadow-xl shadow-cyan-600/20">Aceptar Misión</button>
                        @elseif($selectedTask->status === 'Aceptada')
                            <button wire:click="updateStatus({{ $selectedTask->id }}, 'En progreso')" class="md:col-span-2 px-6 py-5 bg-amber-600 hover:bg-amber-500 text-white font-black text-[10px] uppercase tracking-[0.3em] rounded-3xl transition-all shadow-xl shadow-amber-600/20">Iniciar Ejecución</button>
                        @elseif($selectedTask->status === 'En progreso')
                            <button wire:click="updateStatus({{ $selectedTask->id }}, 'Completada')" class="md:col-span-2 px-6 py-5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-[10px] uppercase tracking-[0.3em] rounded-3xl transition-all shadow-xl shadow-emerald-600/20">Finalizar Misión</button>
                        @endif
                        <button wire:click="$set('showDetailModal', false)" class="md:col-span-{{ $selectedTask->status === 'Completada' ? '4' : '2' }} px-6 py-5 bg-zinc-900 hover:bg-zinc-800 text-zinc-500 font-black text-[10px] uppercase tracking-[0.3em] rounded-3xl transition-all">Cerrar Expediente</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
