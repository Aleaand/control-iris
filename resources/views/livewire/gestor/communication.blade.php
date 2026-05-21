<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Centro de
                Comunicación</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Registro de
                interacciones con clientes</p>
        </div>
    </div>

    @if(session('message'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">
            {{ session('message') }}</div>
    @endif

    @php
        $firstDayOfMonth = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $startDayOfWeek = $firstDayOfMonth->dayOfWeekIso;   
        $monthName = $firstDayOfMonth->translatedFormat('F');
    @endphp

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">        <div class="xl:col-span-1">
            @if(count($globalRequests ?? []) > 0)
                <div class="tech-card rounded-xl p-5 border border-cyan-500/20 bg-cyan-500/5 h-full">
                    <h2 class="font-black uppercase tracking-widest text-cyan-400 mb-4 text-sm flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Solicitudes y Citas
                    </h2>
                    <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($globalRequests as $request)
                            <div class="p-3 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-xs truncate mr-2" style="color:var(--text-primary)">{{ $request->title }}</span>
                                    <span class="font-mono-tech text-[9px] px-2 py-0.5 rounded text-white bg-{{ $request->priorityColor }}-500 flex-shrink-0">
                                        {{ strtoupper($request->priority) }}
                                    </span>
                                </div>
                                <p class="text-[9px] text-zinc-400 leading-relaxed mb-2">{{ Str::limit($request->description, 80) }}</p>
                                
                                @if($request->type === 'task')
                                    <button wire:click="openMeetingForm({{ $request->id }})" class="w-full py-1.5 rounded text-[9px] font-bold uppercase bg-cyan-500/20 text-cyan-400 hover:bg-cyan-500/30 transition-colors">
                                        Programar
                                    </button>
                                @else
                                    <div class="flex gap-2">
                                        @if($request->link)
                                            <a href="{{ $request->link }}" target="_blank" class="flex-1 text-center py-1.5 rounded text-[9px] font-bold uppercase bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 transition-colors">
                                                Unirse
                                            </a>
                                        @endif
                                        @if($request->client_id)
                                            <button wire:click="selectClient({{ $request->client_id }})" class="flex-1 py-1.5 rounded text-[9px] font-bold uppercase bg-white/10 text-white hover:bg-white/20 transition-colors">
                                                Ver Cliente
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="tech-card rounded-xl p-5 h-full flex flex-col items-center justify-center text-zinc-500">
                    <p class="text-xs uppercase tracking-widest font-bold">Sin solicitudes de contacto</p>
                </div>
            @endif
        </div>


        {{-- Calendario Visual --}}
        <div class="xl:col-span-2 tech-card rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-black uppercase tracking-widest text-emerald-400 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Calendario de Reuniones
                </h2>
                <div class="flex items-center gap-4">
                    <button wire:click="prevMonth" class="text-zinc-400 hover:text-white transition-colors">&larr;</button>
                    <span class="font-mono-tech text-xs uppercase tracking-widest" style="color:var(--text-primary)">{{ ucfirst($monthName) }} {{ $currentYear }}</span>
                    <button wire:click="nextMonth" class="text-zinc-400 hover:text-white transition-colors">&rarr;</button>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar pb-2">
                <div class="min-w-[500px] md:min-w-0">
                    <div class="grid grid-cols-7 gap-1 text-center font-mono-tech text-[8px] md:text-[9px] uppercase text-zinc-500 mb-2">
                        <div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div><div>Dom</div>
                    </div>
                    
                    <div class="grid grid-cols-7 gap-1">
                        @for ($i = 1; $i < $startDayOfWeek; $i++)
                            <div class="p-2 rounded bg-black/10 border border-transparent"></div>
                        @endfor

                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                                $hasEvents = isset($calendarEvents[$dateStr]);
                                $isToday = $dateStr === date('Y-m-d');
                            @endphp
                            <button wire:click="selectDate('{{ $dateStr }}')" class="p-1 min-h-[50px] md:min-h-[60px] w-full text-left rounded border flex flex-col items-start
                                {{ $isToday ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-white/5 border-white/5 hover:bg-white/10 transition-colors' }}
                                {{ $hasEvents ? 'ring-1 ring-cyan-500/50' : '' }}">
                                <span class="text-[9px] font-bold mb-1 {{ $isToday ? 'text-emerald-400' : 'text-zinc-400' }}">{{ $day }}</span>
                                
                                @if($hasEvents)
                                    <div class="space-y-1 w-full">
                                        @foreach($calendarEvents[$dateStr] as $ev)
                                            <div class="text-[7px] md:text-[8px] leading-tight px-1 py-0.5 rounded truncate text-left
                                                {{ $ev['type'] === 'videollamada' ? 'bg-blue-500/20 text-blue-300' : 'bg-amber-500/20 text-amber-300' }}"
                                                title="{{ $ev['time'] }} - {{ $ev['client'] }}">
                                                {{ $ev['time'] }} {{ $ev['client'] }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </button>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Lista de Clientes (Left) --}}
        <div class="tech-card rounded-xl p-5">
            <input wire:model.live="search" type="text" placeholder="Buscar cliente..."
                class="w-full px-3 py-2 mb-4 rounded-lg text-sm"
                style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">

            <div class="space-y-2 max-h-[600px] overflow-y-auto custom-scrollbar pr-1">
                @forelse($clients as $c)
                    <button wire:click="selectClient({{ $c->id }})"
                        class="w-full text-left px-3 py-2.5 rounded-xl border transition-colors flex items-center gap-3
                            {{ $selectedClientId === $c->id ? 'bg-blue-500/10 border-blue-500/30' : 'border-transparent hover:bg-white/5' }}">
                        <div
                            class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs {{ $selectedClientId === $c->id ? 'bg-blue-500 text-white' : 'bg-white/10 text-zinc-400' }}">
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
                <div
                    class="tech-card rounded-xl p-12 text-center text-zinc-500 h-full flex flex-col justify-center items-center">
                    <svg class="w-12 h-12 mb-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                    <p class="text-sm">Selecciona un cliente para ver o añadir interacciones.</p>
                </div>
            @else
                <div class="tech-card rounded-xl p-5 mb-4 border-t-2" style="border-top-color: rgba(59,130,246,0.5)">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-lg font-bold" style="color: var(--text-primary)">{{ $selectedClient->name }}
                            </h2>
                            <p class="text-xs text-zinc-400 mt-1 flex gap-4">
                                <span> {{ $selectedClient->email }}</span>
                                <span> {{ $selectedClient->phone ?? 'Sin teléfono' }}</span>
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="openMeetingForm"
                                class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-colors">
                                Agendar Reunión
                            </button>
                            <button wire:click="openLogForm"
                                class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 transition-colors">
                                + Nueva Interacción
                            </button>
                        </div>
                    </div>
                </div>

                @if(count($clientTasks ?? []) > 0)
                    <div class="tech-card rounded-xl p-4 mb-4 border border-amber-500/20 bg-amber-500/5 transition-all">
                        <button wire:click="$toggle('showClientTasks')" class="w-full flex items-center justify-between group">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-amber-400 text-[10px] uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Tareas pendientes para este cliente
                                </h3>
                                <span class="px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 text-[8px] font-bold">{{ count($clientTasks) }}</span>
                            </div>
                            <svg class="w-4 h-4 text-amber-500/40 group-hover:text-amber-400 transition-transform {{ $showClientTasks ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        @if($showClientTasks)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4 animate-fadeIn">
                                @foreach($clientTasks as $task)
                                    <div class="p-3 rounded border border-white/10 bg-white/5">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold text-[10px] text-zinc-200">{{ $task->title }}</span>
                                            <span class="font-mono-tech text-[8px] px-1.5 py-0.5 rounded text-white bg-{{ $task->priorityColor() }}-500">{{ strtoupper($task->priority) }}</span>
                                        </div>
                                        <p class="text-[9px] text-zinc-400 mb-2">{{ Str::limit($task->description, 60) }}</p>
                                        <button wire:click="openMeetingForm({{ $task->id }})" class="w-full py-1 rounded text-[9px] font-bold uppercase bg-amber-500/20 text-amber-400 hover:bg-amber-500/30 transition-colors">
                                            Resolver
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                @if($showLogForm)
                    <div class="tech-card rounded-xl p-5 mb-4 border border-blue-500/20 bg-blue-500/5">
                        <form wire:submit="saveLog" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Tipo de
                                        Contacto</label>
                                    <select wire:model.live="log_type" class="w-full px-3 py-2 rounded-lg text-xs"
                                        style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                        <option value="llamada">Llamada Telefónica</option>
                                        <option value="email">Correo Electrónico</option>
                                        <option value="videollamada">Videollamada (Jitsi Meet)</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                                @if(in_array($log_type, ['llamada', 'videollamada']))
                                    <div>
                                        <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Fecha y Hora de la Cita</label>
                                        <input wire:model="log_date" type="datetime-local" class="w-full px-3 py-2 rounded-lg text-xs"
                                            style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                        @error('log_date') <span class="text-rose-400 text-[9px] mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                                @if($log_type === 'videollamada')
                                    <div class="col-span-2">
                                        <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Link de la Sala (Opcional)</label>
                                        <input wire:model="log_zoom_link" type="url" placeholder="https://meet.jit.si/... (Se autogenerará si lo dejas vacío)"
                                            class="w-full px-3 py-2 rounded-lg text-xs"
                                            style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Mensaje para el Cliente (Cuerpo del Correo)</label>
                                <textarea wire:model="log_notes" rows="4"
                                    class="w-full px-3 py-2 rounded-lg text-xs resize-none"
                                    placeholder="Escribe el mensaje corporativo..."
                                    style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)"></textarea>
                                @error('log_notes') <span class="text-[9px] text-rose-400">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="px-4 py-2 rounded-lg text-xs font-bold bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 transition-colors">Enviar Correo y Guardar</button>
                                <button type="button" wire:click="$set('showLogForm', false)"
                                    class="px-4 py-2 rounded-lg text-xs text-zinc-400 hover:bg-white/5 transition-colors">Cancelar</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="space-y-4">
                    @forelse($logs as $log)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-sm">
                                    {{ mb_substr($log->typeLabel(), 0, 1) }}
                                </div>
                                <div class="w-px h-full bg-white/5 my-2"></div>
                            </div>
                            <div class="flex-1 pb-4">
                                <div class="tech-card rounded-xl p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span
                                            class="font-mono-tech text-[10px] text-zinc-400 uppercase">{{ $log->typeLabel() }}</span>
                                        <span
                                            class="font-mono-tech text-[9px] text-zinc-500">{{ $log->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    <p class="text-sm" style="color: var(--text-primary); white-space: pre-wrap;">
                                        {{ $log->notes }}</p>
                                    @if($log->zoom_link)
                                        <a href="{{ $log->zoom_link }}" target="_blank"
                                            class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
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

    {{-- Modal Programar Reunión --}}
    @if($showMeetingForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-md" style="border-color:rgba(16,185,129,0.3)">
                <h3 class="font-black uppercase tracking-widest text-emerald-400 mb-1 text-sm">Programar Reunión</h3>
                <p class="text-[10px] text-zinc-400 mb-4 leading-relaxed">Configura la fecha y el tipo de reunión. El enlace
                    de sala Jitsi Meet se generará automáticamente si seleccionas videollamada.</p>

                <form wire:submit="scheduleMeeting" class="space-y-4">
                    @if(!$selectedClientId)
                        <div class="p-3 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs rounded-lg mb-4">
                            ⚠️ Por favor, selecciona un cliente en la lista lateral antes de agendar la reunión para poder
                            vincular la sala a su expediente.
                        </div>
                    @else
                        <div>
                            <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Fecha y Hora</label>
                            <input wire:model="meetingDate" type="datetime-local" class="w-full px-3 py-2 rounded-lg text-xs"
                                style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                            @error('meetingDate') <span class="text-rose-400 text-[9px] mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Tipo de Contacto</label>
                            <select wire:model="meetingType" class="w-full px-3 py-2 rounded-lg text-xs"
                                style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                                <option value="videollamada">Videollamada (Sala Jitsi Meet)</option>
                                <option value="telefono">Llamada Telefónica</option>
                            </select>
                            @error('meetingType') <span class="text-rose-400 text-[9px] mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Asunto /
                                Descripción</label>
                            <textarea wire:model="meetingDescription" rows="3"
                                placeholder="Preparación para el vuelo, dudas sobre el pasaporte..."
                                class="w-full px-3 py-2 rounded-lg text-xs resize-none"
                                style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)"></textarea>
                            @error('meetingDescription') <span class="text-rose-400 text-[9px] mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        @if($selectedClientId)
                            <button type="submit"
                                class="flex-1 py-2.5 rounded-lg text-xs font-bold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/30 transition-colors">Confirmar
                                y Generar Sala</button>
                        @endif
                        <button type="button" wire:click="$set('showMeetingForm', false)"
                            class="flex-1 py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Eventos del Día Seleccionado --}}
    @if($selectedDate)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-md" style="border-color:rgba(16,185,129,0.3)">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-black uppercase tracking-widest text-emerald-400 text-sm">Eventos del {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</h3>
                    <button wire:click="closeDateModal" class="text-zinc-400 hover:text-white">&times;</button>
                </div>
                
                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                    @if(isset($calendarEvents[$selectedDate]) && count($calendarEvents[$selectedDate]) > 0)
                        @foreach($calendarEvents[$selectedDate] as $ev)
                            <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-sm" style="color:var(--text-primary)">{{ $ev['time'] }} - {{ $ev['client'] }}</span>
                                    <span class="font-mono-tech text-[9px] px-2 py-0.5 rounded text-white {{ $ev['type'] === 'videollamada' ? 'bg-blue-500' : 'bg-amber-500' }}">
                                        {{ strtoupper($ev['type']) }}
                                    </span>
                                </div>
                                @if(isset($ev['description']) && $ev['description'])
                                    <p class="text-[10px] text-zinc-400 leading-relaxed mb-3 whitespace-pre-wrap">{{ $ev['description'] }}</p>
                                @endif
                                @if(isset($ev['link']) && $ev['link'])
                                    <a href="{{ $ev['link'] }}" target="_blank" class="block w-full text-center py-1.5 rounded text-[10px] font-bold uppercase bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 transition-colors">
                                        Entrar a la Sala Jitsi
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-zinc-500 text-sm text-center py-4">No hay eventos programados para este día.</p>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-white/10 text-right">
                    <button type="button" wire:click="closeDateModal" class="px-4 py-2 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>