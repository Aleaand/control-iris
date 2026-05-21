<div class="p-4 md:p-10 relative" x-data="{ 
    formatCurrency(val) {
        return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(val);
    },
    get saludo() {
        const hora = new Date().getHours();
        if (hora >= 6 && hora < 12) return 'Buenos días';
        if (hora >= 12 && hora < 20) return 'Buenas tardes';
        return 'Buenas noches';
    }
}">
    <div class="animate-tech">
        <!-- Header -->
        <header class="max-w-7xl mx-auto mb-12">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-black tech-text-primary tracking-tighter mb-2">
                        <span x-text="saludo"></span>, <span class="text-gradient-cyan uppercase">Administrador</span>
                    </h1>
                    <div
                        class="flex items-center gap-4 font-mono-tech text-[10px] tech-text-secondary uppercase tracking-[0.2em]">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 {{ $dbStatus['color'] }} rounded-full animate-pulse"></span>
                            {{ $dbStatus['label'] }}
                        </span>
                        <span class="w-1 h-1 bg-zinc-800 rounded-full"></span>
                        <span>Fecha: {{ now()->format('Y-m-d') }}</span>
                        <span class="w-1 h-1 bg-zinc-800 rounded-full"></span>
                        <span>Nombre: {{ Auth::user()->name }}</span>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="tech-card px-6 py-3 flex flex-col items-end">
                        <a href="{{ route('admin.flights', ['status' => 'landed']) }}">
                            <span class="font-mono-tech text-[9px] tech-text-secondary uppercase">Recorrido Total</span>
                            <span class="text-xl font-bold tech-text-primary italic">
                                {{$totalAURecorridas >= 1000000000
    ? number_format($totalAURecorridas / 1000000000, 1) . 'B'
    : ($totalAURecorridas >= 1000000
        ? number_format($totalAURecorridas / 1000000, 1) . 'M'
        : ($totalAURecorridas >= 1000
            ? number_format($totalAURecorridas / 1000, 1) . 'k'
            : number_format($totalAURecorridas, 2)))
                            }}
                                <span class="text-xs text-zinc-600">AU</span>
                            </span>
                        </a>
                    </div>

                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">

            <div class="lg:col-span-8 space-y-8">

                <!-- Finanzas -->
                <section>
                    <div class="flex items-center gap-4 mb-6">
                        <h2 class="font-mono-tech text-xs font-bold text-zinc-400 uppercase tracking-[0.3em]">Finanzas
                        </h2>
                        <div class="h-px flex-1 bg-zinc-800/50"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="tech-card p-8 group">
                            <a href="{{ route('admin.finances') }}" class="block">
                                <div class="flex justify-between items-start mb-10">
                                    <div>
                                        <p
                                            class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest mb-2">
                                            Ingresos Reales</p>
                                        <h3 class="text-4xl font-black tech-text-primary"
                                            x-text="formatCurrency({{ $ingresosReales }})">
                                        </h3>
                                    </div>
                                    <div
                                        class="w-12 h-12 flex items-center justify-center bg-emerald-500/5 border border-emerald-500/20 rounded-2xl text-emerald-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                stroke-width="1.5" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div class="relative group cursor-help">
                                        <div class="flex justify-between font-mono-tech text-[9px] uppercase mb-3">
                                            <span class="tech-text-secondary">Pagos Pendientes</span>
                                            <span class="text-amber-400">
                                                {{ number_format($projectedIncome > 0 ? ($projectedIncome / max(1, $ingresosReales + $projectedIncome)) * 100 : 0, 1) }}%
                                            </span>
                                        </div>
                                        <div
                                            class="h-1.5 bg-zinc-900/50 rounded-full overflow-hidden border border-white/5">
                                            <div class="h-full bg-gradient-to-r from-amber-600 to-amber-400 shadow-[0_0_15px_rgba(245,158,11,0.3)]"
                                                style="width: {{ $projectedIncome > 0 ? ($projectedIncome / max(1, $ingresosReales + $projectedIncome)) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center pt-4 border-t border-white/5">
                                        <span class="font-mono-tech text-[9px] tech-text-secondary uppercase">Capital
                                            Proyectado</span>
                                        <span class="text-sm font-bold tech-text-primary"
                                            x-text="formatCurrency({{ $projectedIncome }})"></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Alerta de ocupación -->
                        <div class="tech-card overflow-hidden flex flex-col group/card">
                            <a href="{{ route('admin.flights', ['status' => 'scheduled'])}}"
                                class="p-6 border-b border-white/5 flex justify-between items-center bg-white/[0.01] hover:bg-[var(--tech-hover-bg)] transition-colors">
                                <h3
                                    class="tech-text-primary text-[10px] font-black font-mono-tech uppercase tracking-[0.2em] flex items-center gap-3">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span> Alertas
                                    Críticas
                                </h3>
                                <span
                                    class="font-mono-tech text-[9px] text-red-500 border border-red-500/20 px-2 py-0.5 rounded">OCUPACIÓN
                                    < 70%</span>
                            </a>
                            <div class="flex-1 divide-y divide-white/5 custom-scrollbar overflow-y-auto max-h-[220px]">
                                @forelse($criticalFlights as $f)
                                    <a href="{{ route('admin.flights', ['search' => $f->flight_code])}}"
                                        class="p-4 hover:bg-[var(--tech-hover-bg)] transition-colors flex justify-between items-center group/item">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center text-[9px] font-mono-tech text-zinc-500">
                                                {{ substr($f->destination->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <h4
                                                    class="text-[10px] font-bold tech-text-primary uppercase tracking-wider">
                                                    #{{ $f->flight_code }} </h4>
                                                <p class="font-mono-tech text-[8px] tech-text-secondary uppercase">
                                                    {{ $f->destination->name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-mono-tech text-[10px] text-red-400 font-bold">
                                                {{ $f->occupancy_percentage }}%
                                            </p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-12 text-center">
                                        <p class="font-mono-tech text-[9px] text-zinc-600 uppercase tracking-widest">No hay
                                            alertas críticas</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="flex items-center gap-4 mb-6">
                        <h2 class="font-mono-tech text-xs font-bold tech-text-secondary uppercase tracking-[0.3em]">
                            Estado
                            de
                            Naves Espaciales</h2>
                        <div class="h-px flex-1 bg-zinc-800/50"></div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        @php
                            $fleetStats = [
                                ['label' => 'En Vuelo', 'val' => $starshipsStatus['in_flight'], 'color' => 'var(--neon-cyan)', 'bg' => 'rgba(14, 165, 233, 0.05)', 'link' => route('admin.starships', ['status' => 'in_flight'])],
                                ['label' => 'Mantenimiento', 'val' => $starshipsStatus['maintenance'], 'color' => 'var(--neon-amber)', 'bg' => 'rgba(245, 158, 11, 0.05)', 'link' => route('admin.starships', ['status' => 'maintenance'])],
                                ['label' => 'Programadas', 'val' => $starshipsStatus['ready'], 'color' => 'var(--neon-emerald)', 'bg' => 'rgba(16, 185, 129, 0.05)', 'link' => route('admin.starships', ['status' => 'ready'])],
                                ['label' => 'Sin programar', 'val' => $starshipsStatus['idle'], 'color' => 'var(--neon-rose)', 'bg' => 'rgba(244, 63, 94, 0.05)', 'link' => route('admin.starships', ['status' => 'idle'])]
                            ];
                        @endphp

                        @foreach($fleetStats as $stat)
                            <a href="{{ $stat['link'] }}"
                                class="tech-card p-6 text-center border-white/5 hover:border-white/10 group/stat">
                                <div class="w-12 h-12 mx-auto mb-4 rounded-2xl flex items-center justify-center border border-white/5 transition-transform group-hover/stat:scale-110"
                                    style="background: {{ $stat['bg'] }}">
                                    <span class="text-2xl font-black italic"
                                        style="color: {{ $stat['color'] }}">{{ $stat['val'] }}</span>
                                </div>
                                <p class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-[0.2em]">
                                    {{ $stat['label'] }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </section>


                <section id="gestores-tasks">
                    <div class="flex items-center gap-4 mb-6">
                        <h2 class="font-mono-tech text-xs font-bold tech-text-secondary uppercase tracking-[0.3em]">
                            Tareas
                            de
                            Gestores</h2>
                        <div class="h-px flex-1 bg-zinc-800/50"></div>
                        <button wire:click="$toggle('showTaskForm')"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600/20 border border-violet-500/40 text-var(--neon-violet) font-mono-tech text-[9px] uppercase tracking-widest hover:bg-violet-600/30 transition-all">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $showTaskForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}" />
                            </svg>
                            {{ $showTaskForm ? 'Cerrar' : 'Nueva Tarea' }}
                        </button>
                    </div>

                    {{-- Flash --}}
                    @if(session()->has('task_created'))
                        <div
                            class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 font-mono-tech text-[9px] uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse flex-shrink-0"></span>
                            {{ session('task_created') }}
                        </div>
                    @endif

                    {{-- ── Formulario nueva misión ────────────────────────────────── --}}
                    @if($showTaskForm)
                        <div class="tech-card p-6 mb-6 border-violet-500/20 bg-violet-500/[0.03]"
                            x-data="{ type: @entangle('taskType'), scope: @entangle('passengerScope') }">
                            <form wire:submit.prevent="createTask" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Gestor --}}
                                <div class="md:col-span-2">
                                    <label
                                        class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Gestor
                                        Asignado <span class="text-rose-400">*</span></label>
                                    <select wire:model.live="taskGestorId" class="tech-input w-full p-3 text-xs">
                                        <option value="">— Seleccionar gestor —</option>
                                        @foreach($gestores as $g)
                                            <option value="{{ $g->id }}">{{ $g->name }}
                                                {{ $g->pending_tasks_count > 0 ? '(' . $g->pending_tasks_count . ' pendientes)' : '(libre)' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('taskGestorId') <p class="text-rose-400 text-[9px] mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Título --}}
                                <div class="md:col-span-2">
                                    <label
                                        class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Título
                                        <span class="text-rose-400">*</span></label>
                                    <input wire:model="taskTitle" type="text"
                                        placeholder="Ej: Reasignar pasajero del vuelo IRS-042..."
                                        class="tech-input w-full p-3 text-xs" />
                                    @error('taskTitle') <p class="text-rose-400 text-[9px] mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Descripción --}}
                                <div class="md:col-span-2">
                                    <label
                                        class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Descripción</label>
                                    <textarea wire:model="taskDesc" rows="3"
                                        placeholder="Instrucciones detalladas para el gestor..."
                                        class="tech-input w-full p-3 text-xs resize-none"></textarea>
                                </div>

                                {{-- Tipo --}}
                                <div>
                                    <label
                                        class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Tipo</label>
                                    <select wire:model.live="taskType" x-model="type" class="tech-input w-full p-3 text-xs">
                                        <option value="general">General</option>
                                        <option value="passenger_issue">Pasajero</option>
                                        <option value="policy_change">Política</option>
                                        <option value="flight_cancelled">Vuelo Cancelado</option>
                                        <option value="other">Otro</option>
                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Prioridad</label>
                                    <select wire:model="taskPriority"
                                        class="tech-input w-full p-3 text-xs uppercase tracking-wider font-bold p-1">
                                        <option value="baja" class="text-sky-400 bg-[#1c1c28]">Baja</option>
                                        <option value="media" class="text-amber-400 bg-[#1c1c28]">Media</option>
                                        <option value="alta" class="text-orange-400 bg-[#1c1c28]">Alta</option>
                                        <option value="urgente" class="text-rose-400 bg-[#1c1c28]">Urgente</option>
                                    </select>
                                    @error('taskPriority') <p class="text-rose-400 text-[9px] mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- ── Selector de pasajero (solo cuando tipo = passenger_issue) ── --}}
                                <div class="md:col-span-2" x-show="type === 'passenger_issue'" x-transition>
                                    <div class="bg-black/30 border border-violet-500/20 rounded-xl p-4 space-y-4">
                                        <p class="font-mono-tech text-[9px] text-violet-400 uppercase tracking-widest">
                                            Ámbito
                                            del Pasajero</p>

                                        {{-- Scope selector (3 opciones) --}}
                                        <div class="grid grid-cols-3 gap-2">
                                            @php
                                                $scopes = [
                                                    'passenger' => ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Un pasajero'],
                                                    'flight_passengers' => ['icon' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8', 'label' => 'Vuelo completo'],
                                                    'client_passengers' => ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'label' => 'Cliente completo'],
                                                ];
                                            @endphp
                                            @foreach($scopes as $sv => $sopt)
                                                <label class="cursor-pointer">
                                                    <input type="radio" wire:model.live="passengerScope" value="{{ $sv }}"
                                                        class="sr-only peer">
                                                    <div
                                                        class="flex flex-col items-center gap-1.5 px-2 py-3 rounded-xl border transition-all text-center
                                                                                                                                                                                                                                            peer-checked:bg-violet-500/10 peer-checked:border-violet-500/40 peer-checked:text-violet-300
                                                                                                                                                                                                                                            border-white/5 text-zinc-500 hover:border-white/10 hover:text-zinc-300">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5" d="{{ $sopt['icon'] }}" />
                                                        </svg>
                                                        <span
                                                            class="font-mono-tech text-[8px] uppercase tracking-wide leading-tight">{{ $sopt['label'] }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>

                                        {{-- Pasajero individual --}}
                                        <div x-show="scope === 'passenger'" x-transition>
                                            <label
                                                class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Seleccionar
                                                Pasajero</label>
                                            <select wire:model="taskPassengerId" class="tech-input w-full p-3 text-xs">
                                                @if(!$taskGestorId)
                                                    <option value="">— Selecciona un gestor primero —</option>
                                                @else
                                                    <option value="">— Seleccionar pasajero —</option>
                                                    @foreach($allPassengers as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }} {{ $p->primarylastname }} —
                                                            {{ $p->client?->name ?? 'Sin cliente' }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        {{-- Todos los pasajeros de un vuelo --}}
                                        <div x-show="scope === 'flight_passengers'" x-transition>
                                            <label
                                                class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Seleccionar
                                                Vuelo</label>
                                            <select wire:model="taskFlightId" class="tech-input w-full p-3 text-xs">
                                                @if(!$taskGestorId)
                                                    <option value="">— Selecciona un gestor primero —</option>
                                                @else
                                                    <option value="">— Seleccionar vuelo —</option>
                                                    @foreach($allFlights as $f)
                                                        <option value="{{ $f->id }}">#{{ $f->flight_code }} —
                                                            {{ $f->destination?->name }} ({{ strtoupper($f->status) }}) —
                                                            {{ $f->departure_date->format('d/m/Y') }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        {{-- Todos los pasajeros de un cliente --}}
                                        <div x-show="scope === 'client_passengers'" x-transition>
                                            <label
                                                class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Seleccionar
                                                Cliente</label>
                                            <select wire:model="taskClientId" class="tech-input w-full p-3 text-xs">
                                                @if(!$taskGestorId)
                                                    <option value="">— Selecciona un gestor primero —</option>
                                                @else
                                                    <option value="">— Seleccionar cliente —</option>
                                                    @foreach($allClients as $c)
                                                        <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->email }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Selector de vuelo (solo cuando tipo = flight_cancelled) ── --}}
                                <div class="md:col-span-2" x-show="type === 'flight_cancelled'" x-transition>
                                    <div class="bg-black/30 border border-violet-500/20 rounded-xl p-4">
                                        <label
                                            class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Seleccionar
                                            Vuelo Cancelado</label>
                                        <select wire:model="taskFlightId" class="tech-input w-full p-3 text-xs">
                                            @if(!$taskGestorId)
                                                <option value="">— Selecciona un gestor primero —</option>
                                            @else
                                                <option value="">— Seleccionar vuelo —</option>
                                                @foreach($allFlights as $f)
                                                    <option value="{{ $f->id }}">#{{ $f->flight_code }} —
                                                        {{ $f->destination?->name }} ({{ strtoupper($f->status) }}) —
                                                        {{ $f->departure_date->format('d/m/Y') }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <button type="submit"
                                        class="w-full py-3 bg-violet-600 hover:bg-violet-500 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-[0_0_20px_rgba(139,92,246,0.25)]">
                                        Asignar Tarea al Gestor
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- ── Filtros de búsqueda ──────────────────────────────────────── --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                        <div>
                            <select wire:model.live="filterGestorId" class="tech-input w-full p-2.5 text-xs">
                                <option value="">Todos los gestores</option>
                                @foreach($gestores as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select wire:model.live="filterTaskType" class="tech-input w-full p-2.5 text-xs">
                                <option value="">Todos los tipos</option>
                                <option value="general">General</option>
                                <option value="passenger_issue">Pasajero</option>
                                <option value="policy_change">Política</option>
                                <option value="flight_cancelled">Vuelo Cancelado</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        <div>
                            <select wire:model.live="filterTaskStatus" class="tech-input w-full p-2.5 text-xs">
                                <option value="">Todos los estados</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Aceptada">Aceptada</option>
                                <option value="En progreso">En progreso</option>
                                <option value="Completada">Completada</option>
                            </select>
                        </div>
                        <div>
                            <select wire:model.live="filterTaskPriority" class="tech-input w-full p-2.5 text-xs">
                                <option value="">Todas las prioridades</option>
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tabla --}}
                    <div class="tech-card overflow-hidden">
                        <div
                            class="hidden md:grid grid-cols-12 gap-3 px-5 py-3 bg-white/[0.02] border-b border-white/5 font-mono-tech text-[8px] tech-text-secondary uppercase tracking-widest">
                            <div class="col-span-4">Título / Gestor</div>
                            <div class="col-span-2">Tipo</div>
                            <div class="col-span-2">Estado Gestor</div>
                            <div class="col-span-2">Prioridad</div>
                            <div class="col-span-2 text-right">Acciones</div>
                        </div>

                        @php
                            $statusColors = [
                                'Pendiente' => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                                'Aceptada' => 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20',
                                'En progreso' => 'text-violet-400 bg-violet-500/10 border-violet-500/20',
                                'Completada' => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                            ];
                            $typeLabels = [
                                'general' => 'General',
                                'passenger_issue' => 'Pasajero',
                                'policy_change' => 'Política',
                                'flight_cancelled' => 'Vuelo',
                                'other' => 'Otro',
                            ];
                            $priorityColors = [
                                'baja' => ['text' => 'text-sky-400', 'dot' => 'bg-sky-400', 'glow' => 'shadow-sky-400/50'],
                                'media' => ['text' => 'text-amber-400', 'dot' => 'bg-amber-400', 'glow' => 'shadow-amber-400/50'],
                                'alta' => ['text' => 'text-orange-400', 'dot' => 'bg-orange-400', 'glow' => 'shadow-orange-400/50'],
                                'urgente' => ['text' => 'text-rose-400', 'dot' => 'bg-rose-500', 'glow' => 'shadow-rose-500/50'],
                            ];
                        @endphp

                        <div class="divide-y divide-white/5 max-h-[520px] overflow-y-auto custom-scrollbar">
                            @forelse($missions as $m)
                                <div wire:key="mission-row-{{ $m->id }}"
                                    class="grid grid-cols-1 md:grid-cols-12 gap-4 px-5 py-5 hover:bg-[var(--tech-hover-bg)] transition-all items-start md:items-center group border-b border-white/5 last:border-0">
                                    {{-- Título / Gestor --}}
                                    <div class="md:col-span-4">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="w-8 h-8 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[11px] font-black tech-text-primary leading-tight line-clamp-2 uppercase tracking-wide">
                                                    {{ $m->title }}
                                                </p>
                                                <div class="flex items-center gap-1.5 mt-1.5">
                                                    <span
                                                        class="font-mono-tech text-[8px] var(--text-secondary) uppercase tracking-widest">Asignado
                                                        a:</span>
                                                    <span
                                                        class="font-mono-tech text-[8px] text-zinc-400 font-bold underline decoration-violet-500/30 underline-offset-2">{{ $m->gestor?->name ?? 'Sin gestor' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if($m->description)
                                            <div class="mt-2 pl-11">
                                                <p
                                                    class="font-mono-tech text-[8px] text-zinc-500 line-clamp-1 italic border-l border-white/10 pl-2">
                                                    {{ $m->description }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Tipo --}}
                                    <div class="md:col-span-2">
                                        <span
                                            class="md:hidden text-[7px] text-zinc-600 uppercase tracking-[0.2em] mb-1 block">Clasificación</span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-md bg-white/[0.03] border border-white/5 font-mono-tech text-[8px] text-zinc-400 uppercase tracking-wider">
                                            {{ $typeLabels[$m->type] ?? $m->type }}
                                        </span>
                                        <p class="font-mono-tech text-[7px] text-zinc-700 mt-1 pl-1">
                                            {{ $m->created_at->format('d/m H:i') }}
                                        </p>
                                    </div>

                                    {{-- Estado del Gestor --}}
                                    <div class="md:col-span-2">
                                        <span
                                            class="md:hidden text-[7px] text-zinc-600 uppercase tracking-[0.2em] mb-1 block">Progreso
                                            Operativo</span>
                                        @php $sc = $statusColors[$m->status] ?? 'text-zinc-400 bg-zinc-500/10 border-zinc-500/20'; @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[8px] font-black border uppercase tracking-widest {{ $sc }}">
                                            {{ $m->status }}
                                        </span>
                                        @if($m->accepted_at)
                                            <p class="font-mono-tech text-[7px] text-zinc-700 mt-1 pl-1">
                                                ACT: {{ $m->accepted_at->diffForHumans(null, true) }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Prioridad editable --}}
                                    <div class="md:col-span-2">
                                        <span
                                            class="md:hidden text-[7px] text-zinc-600 uppercase tracking-[0.2em] mb-1 block">Nivel
                                            de Prioridad</span>
                                        <div class="relative group/sel">
                                            @php $pc = $priorityColors[$m->priority] ?? ['text' => 'text-zinc-400', 'dot' => 'bg-zinc-400']; @endphp
                                            <div
                                                class="absolute left-3 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full {{ $pc['dot'] }} {{ $pc['glow'] ?? '' }}">
                                            </div>
                                            <select wire:change="confirmUpdatePriority({{ $m->id }}, $event.target.value)"
                                                class="w-full bg-white/[0.03] border border-white/5 rounded-xl pl-7 pr-3 py-2 text-[9px] font-black uppercase tracking-widest {{ $pc['text'] }} focus:ring-1 focus:ring-violet-500/50 focus:border-violet-500/50 transition-all cursor-pointer appearance-none">
                                                <option value="baja" {{ $m->priority === 'baja' ? 'selected' : '' }}
                                                    class="bg-[#1c1c28] text-sky-400">Baja</option>
                                                <option value="media" {{ $m->priority === 'media' ? 'selected' : '' }}
                                                    class="bg-[#1c1c28] text-amber-400">Media</option>
                                                <option value="alta" {{ $m->priority === 'alta' ? 'selected' : '' }}
                                                    class="bg-[#1c1c28] text-orange-400">Alta</option>
                                                <option value="urgente" {{ $m->priority === 'urgente' ? 'selected' : '' }}
                                                    class="bg-[#1c1c28] text-rose-400">Urgente</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Acciones --}}
                                    <div
                                        class="md:col-span-2 flex justify-end items-center gap-2 pt-2 md:pt-0 border-t md:border-0 border-white/5 mt-2 md:mt-0">
                                        <button wire:click="confirmCancelTask({{ $m->id }})"
                                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-rose-500/20 text-rose-500/60 hover:text-rose-400 hover:bg-rose-500/10 hover:border-rose-500/40 transition-all group/btn"
                                            title="Cancelar tarea">
                                            <span
                                                class="md:hidden font-mono-tech text-[8px] uppercase tracking-widest font-bold">Cancelar</span>
                                            <svg class="w-3 h-3 group-hover/btn:rotate-90 transition-transform" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="py-16 text-center">
                                    <p class="font-mono-tech text-[9px] text-zinc-600 uppercase tracking-widest">No hay
                                        tareas
                                        activas</p>
                                    <p class="font-mono-tech text-[8px] text-zinc-700 mt-1">Ajusta los filtros o crea una
                                        nueva
                                        tarea</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Footer resumen --}}
                        <div
                            class="px-5 py-3 border-t border-white/5 bg-white/[0.01] flex items-center justify-between">
                            <span
                                class="font-mono-tech text-[8px] text-zinc-600 uppercase tracking-widest">{{ $missions->count() }}
                                tarea(s)</span>
                            <span class="font-mono-tech text-[8px] text-zinc-700">CONTROL DE TAREAS</span>
                        </div>
                    </div>
                </section>
            </div>

            <div class="lg:col-span-4 space-y-8">

                <div class="tech-card p-6 border-cyan-500/10 hidden md:block">
                    <h3
                        class="font-mono-tech text-[10px] text-secondary uppercase tracking-widest mb-6 border-b border-white/5 pb-4">
                        Accesos Directo</h3>
                    <div class="space-y-3">
                        @php
                            $navs = [
                                ['label' => 'Finanzas', 'route' => 'admin.finances', 'color' => 'purple'],
                                ['label' => 'Vuelos', 'route' => 'admin.flights', 'color' => 'blue'],
                                ['label' => 'Reservas', 'route' => 'admin.reservations', 'color' => 'emerald'],
                            ];
                        @endphp
                        @foreach($navs as $n)
                            <a href="{{ route($n['route']) }}"
                                class="flex items-center justify-between p-4 bg-white/[0.02] border border-white/5 rounded-xl hover:bg-[var(--tech-hover-bg)] transition-all group">
                                <span
                                    class="text-[11px] font-bold text-secondary group-hover:text-{{ $n['color'] }}-400 uppercase tracking-wider transition-colors">{{ $n['label'] }}</span>
                                <svg class="w-4 h-4 text-zinc-600 group-hover:text-{{ $n['color'] }}-400 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 5l7 7-7 7" stroke-width="2.5" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Logs de precios -->
                <div class="tech-card overflow-hidden">
                    <div class="p-5 border-b border-white/5 bg-white/[0.01] flex justify-between items-center">
                        <a href="{{ route('admin.tariffs') }}">
                            <h3
                                class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest hover:text-zinc-300">
                                Logs de Precios
                            </h3>
                        </a>
                        <button wire:click="exportLogs"
                            class="flex items-center gap-2 px-3 py-1 rounded-lg bg-cyan-500/5 border border-cyan-500/20 text-cyan-400 font-mono-tech text-[9px] uppercase tracking-tighter hover:bg-cyan-500/10 hover:border-cyan-500/40 transition-all group">
                            <svg class="w-3 h-3 transition-transform group-hover:translate-y-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                            </svg>
                            <span>Exportar</span>
                        </button>
                    </div>
                    <div class="p-2 space-y-1 max-h-[400px] overflow-y-auto custom-scrollbar">
                        @foreach($recentPriceLogs as $log)
                            <div
                                class="p-4 hover:bg-[var(--tech-hover-bg)] rounded-xl transition-all cursor-default border border-transparent hover:border-white/5">
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest">{{ $log->item_label }}</span>
                                    <span
                                        class="font-mono-tech text-[8px] text-zinc-600">{{ $log->created_at->format('H:i') }}</span>
                                </div>
                                <div class="flex items-center gap-3 font-mono-tech text-[11px]">
                                    <span
                                        class="text-zinc-600 line-through">${{ number_format((float) $log->old_price, 0) }}</span>
                                    <svg class="w-3 h-3 text-zinc-800" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" />
                                    </svg>
                                    <span
                                        class="text-emerald-400 font-bold">${{ number_format((float) $log->new_price, 0) }}</span>
                                </div>
                                @if($log->reason)
                                    <p class="text-[9px] text-zinc-500 mt-2 italic leading-relaxed">"{{ $log->reason }}"</p>
                                @endif
                                <div class="mt-3 pt-3 border-t border-white/5 flex justify-between items-center">
                                    <span
                                        class="font-mono-tech text-[8px] text-violet-400 uppercase font-bold">{{ $log->admin->name ?? 'SYSTEM' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SECCIÓN DE MODALES ── --}}
    <div x-data="{ 
        lockScroll: @entangle('showCancelModal') || @entangle('showUpdateModal') 
    }"
        x-effect="lockScroll ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">

        {{-- Modal de Cancelación --}}
        @if($showCancelModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                {{-- Backdrop con desenfoque profundo --}}
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md" wire:click="$set('showCancelModal', false)">
                </div>

                <div class="relative border border-[var(--border-glass)] rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/90 backdrop-blur-xl animate-tech"
                    @click.away="$wire.set('showCancelModal', false)">
                    <div class="p-8 border-b border-[var(--border-glass)] flex flex-col items-center text-center gap-4">
                        <div
                            class="w-14 h-14 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-500 flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(244,63,94,0.1)]">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">Eliminar Tarea</h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                                ¿Confirmas la eliminación permanente de esta tarea operativa? <br>
                                <span class="text-rose-500/80 mt-1 block">Esta acción no se puede revertir.</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="$set('showCancelModal', false)"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="cancelTask"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-[0_0_20px_rgba(225,29,72,0.3)] transition-all">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal de Actualización --}}
        @if($showUpdateModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                {{-- Backdrop con desenfoque profundo --}}
                <div class="absolute inset-0 bg-[var(--bg-obsidian)]/80 backdrop-blur-md" wire:click="cancelUpdatePriority"></div>

                <div class="relative border border-[var(--border-glass)] rounded-[24px] max-w-sm w-full overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] bg-[var(--bg-panel)]/90 backdrop-blur-xl animate-tech"
                    @click.away="cancelUpdatePriority">
                    <div class="p-8 border-b border-[var(--border-glass)] flex flex-col items-center text-center gap-4">
                        <div
                            class="w-14 h-14 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-500 flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(245,158,11,0.1)]">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[var(--text-primary)] uppercase tracking-[0.1em] mb-2">Modificar Tarea</h3>
                            <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                                ¿Confirmas el cambio de nivel operativo a <strong
                                    class="text-amber-500">{{ strtoupper($updateNewPriority) }}</strong>?
                                @php
                                    $weights = ['baja' => 1, 'media' => 2, 'alta' => 3, 'urgente' => 4];
                                    $task = $updateTaskId ? \App\Models\Task::find($updateTaskId) : null;
                                    $oldWeight = $task ? $weights[$task->priority] : 0;
                                    $newWeight = $weights[$updateNewPriority] ?? 0;
                                @endphp
                                @if($newWeight > $oldWeight)
                                    <br><span class="text-cyan-400 font-bold block mt-2">● Se enviará alerta al gestor.</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]">
                        <button type="button" wire:click="cancelUpdatePriority"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest rounded-xl border border-[var(--border-glass)] text-[var(--text-secondary)] hover:bg-[var(--tech-hover-bg)] transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="processUpdatePriority"
                            class="flex-1 py-3 px-4 text-[10px] font-black uppercase tracking-widest text-black bg-amber-500 hover:bg-amber-400 rounded-xl shadow-[0_0_20px_rgba(245,158,11,0.3)] transition-all">
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>