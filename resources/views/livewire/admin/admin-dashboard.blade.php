<div class="p-4 md:p-10 animate-tech" x-data="{ 
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
                <div
                    class="tech-card px-6 py-3 flex flex-col items-end {{ $porcentajeGanancias < 0 ? 'border-rose-500/30' : 'border-cyan-500/20' }}">
                    <a href="{{ route('admin.finances') }}">
                        <span
                            class="font-mono-tech text-[9px] {{ $porcentajeGanancias < 0 ? 'text-rose-500' : 'text-cyan-500' }} uppercase tracking-widest">Ganancias</span>
                        <span
                            class="text-xl font-bold {{ $porcentajeGanancias < 0 ? 'text-rose-400' : 'text-cyan-400' }}">
                            {{ number_format($porcentajeGanancias, 1) }}%
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
                                            <h4 class="text-[10px] font-bold tech-text-primary uppercase tracking-wider">
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
                    <h2 class="font-mono-tech text-xs font-bold tech-text-secondary uppercase tracking-[0.3em]">Estado
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

            {{-- ══ Centro de Misiones ═══════════════════════════════════════════ --}}
            <section>
                <div class="flex items-center gap-4 mb-6">
                    <h2 class="font-mono-tech text-xs font-bold tech-text-secondary uppercase tracking-[0.3em]">🛰️ Centro de Misiones</h2>
                    <div class="h-px flex-1 bg-zinc-800/50"></div>
                    <button wire:click="$toggle('showTaskForm')"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600/20 border border-violet-500/40 text-violet-400 font-mono-tech text-[9px] uppercase tracking-widest hover:bg-violet-600/30 transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $showTaskForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}" />
                        </svg>
                        {{ $showTaskForm ? 'Cerrar formulario' : 'Nueva Misión' }}
                    </button>
                </div>

                {{-- Flash --}}
                @if(session()->has('task_created'))
                    <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 font-mono-tech text-[9px] uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse flex-shrink-0"></span>
                        {{ session('task_created') }}
                    </div>
                @endif

                {{-- ── Formulario nueva misión ────────────────────────────────── --}}
                @if($showTaskForm)
                    <div class="tech-card p-6 mb-6 border-violet-500/20 bg-violet-500/[0.03]">
                        <form wire:submit.prevent="createTask" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Gestor --}}
                            <div class="md:col-span-2">
                                <label class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Gestor Asignado <span class="text-rose-400">*</span></label>
                                <select wire:model="taskGestorId" class="tech-input w-full p-3 text-xs">
                                    <option value="">— Seleccionar gestor —</option>
                                    @foreach($gestores as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }} {{ $g->pending_tasks_count > 0 ? '('.$g->pending_tasks_count.' pendientes)' : '(libre)' }}</option>
                                    @endforeach
                                </select>
                                @error('taskGestorId') <p class="text-rose-400 text-[9px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            {{-- Título --}}
                            <div class="md:col-span-2">
                                <label class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Título <span class="text-rose-400">*</span></label>
                                <input wire:model="taskTitle" type="text" placeholder="Ej: Reasignar pasajero del vuelo IRS-042..." class="tech-input w-full p-3 text-xs" />
                                @error('taskTitle') <p class="text-rose-400 text-[9px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            {{-- Descripción --}}
                            <div class="md:col-span-2">
                                <label class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Descripción</label>
                                <textarea wire:model="taskDesc" rows="3" placeholder="Instrucciones detalladas para el gestor..." class="tech-input w-full p-3 text-xs resize-none"></textarea>
                            </div>
                            {{-- Tipo --}}
                            <div>
                                <label class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Tipo</label>
                                <select wire:model="taskType" class="tech-input w-full p-3 text-xs">
                                    <option value="general">📋 General</option>
                                    <option value="passenger_issue">👤 Pasajero</option>
                                    <option value="policy_change">⚙️ Política</option>
                                    <option value="flight_cancelled">✈️ Vuelo Cancelado</option>
                                </select>
                            </div>
                            {{-- Prioridad --}}
                            <div>
                                <label class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest block mb-1.5">Prioridad</label>
                                <select wire:model="taskPriority" class="tech-input w-full p-3 text-xs">
                                    <option value="baja">🔵 Baja</option>
                                    <option value="media">🟡 Media</option>
                                    <option value="alta">🟠 Alta</option>
                                    <option value="urgente">🔴 Urgente</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit" class="w-full py-3 bg-violet-600 hover:bg-violet-500 text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-[0_0_20px_rgba(139,92,246,0.25)]">
                                    Asignar Misión al Gestor
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- ── Filtros de búsqueda ──────────────────────────────────────── --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
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
                            <option value="general">📋 General</option>
                            <option value="passenger_issue">👤 Pasajero</option>
                            <option value="policy_change">⚙️ Política</option>
                            <option value="flight_cancelled">✈️ Vuelo Cancelado</option>
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="filterTaskStatus" class="tech-input w-full p-2.5 text-xs">
                            <option value="">Todos los estados</option>
                            <option value="Pendiente">⏳ Pendiente</option>
                            <option value="Aceptada">✅ Aceptada</option>
                            <option value="En progreso">🔄 En progreso</option>
                            <option value="Completada">🏁 Completada</option>
                        </select>
                    </div>
                </div>

                {{-- ── Tabla de misiones ────────────────────────────────────────── --}}
                <div class="tech-card overflow-hidden">
                    {{-- Header de tabla --}}
                    <div class="hidden md:grid grid-cols-12 gap-3 px-5 py-3 bg-white/[0.02] border-b border-white/5 font-mono-tech text-[8px] tech-text-secondary uppercase tracking-widest">
                        <div class="col-span-4">Título / Gestor</div>
                        <div class="col-span-2">Tipo</div>
                        <div class="col-span-2">Estado Gestor</div>
                        <div class="col-span-2">Prioridad</div>
                        <div class="col-span-2 text-right">Acciones</div>
                    </div>

                    @php
                        $statusColors = [
                            'Pendiente'   => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                            'Aceptada'    => 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20',
                            'En progreso' => 'text-violet-400 bg-violet-500/10 border-violet-500/20',
                            'Completada'  => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                        ];
                        $typeLabels = [
                            'general'          => '📋 General',
                            'passenger_issue'  => '👤 Pasajero',
                            'policy_change'    => '⚙️ Política',
                            'flight_cancelled' => '✈️ Vuelo',
                        ];
                        $priorityColors = [
                            'baja'    => 'text-sky-400',
                            'media'   => 'text-amber-400',
                            'alta'    => 'text-orange-400',
                            'urgente' => 'text-rose-400',
                        ];
                    @endphp

                    <div class="divide-y divide-white/5 max-h-[520px] overflow-y-auto custom-scrollbar">
                        @forelse($missions as $m)
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 px-5 py-4 hover:bg-[var(--tech-hover-bg)] transition-all items-center group">
                                {{-- Título / Gestor --}}
                                <div class="md:col-span-4">
                                    <p class="text-[11px] font-bold tech-text-primary leading-snug line-clamp-2">{{ $m->title }}</p>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <div class="w-4 h-4 rounded-full bg-violet-500/20 flex items-center justify-center text-[7px] font-bold text-violet-400">
                                            {{ strtoupper(substr($m->gestor?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="font-mono-tech text-[8px] text-zinc-500">{{ $m->gestor?->name ?? 'Sin gestor' }}</span>
                                    </div>
                                    @if($m->description)
                                        <p class="font-mono-tech text-[8px] text-zinc-600 mt-1 line-clamp-1 italic">{{ $m->description }}</p>
                                    @endif
                                </div>
                                {{-- Tipo --}}
                                <div class="md:col-span-2">
                                    <span class="font-mono-tech text-[8px] text-zinc-400 uppercase tracking-wider">
                                        {{ $typeLabels[$m->type] ?? $m->type }}
                                    </span>
                                    <p class="font-mono-tech text-[7px] text-zinc-700 mt-0.5">{{ $m->created_at->format('d/m H:i') }}</p>
                                </div>
                                {{-- Estado del Gestor --}}
                                <div class="md:col-span-2">
                                    @php $sc = $statusColors[$m->status] ?? 'text-zinc-400 bg-zinc-500/10 border-zinc-500/20'; @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-bold border {{ $sc }}">
                                        {{ $m->status }}
                                    </span>
                                    @if($m->accepted_at)
                                        <p class="font-mono-tech text-[7px] text-zinc-700 mt-0.5">Aceptada {{ $m->accepted_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                                {{-- Prioridad editable --}}
                                <div class="md:col-span-2">
                                    <select
                                        wire:change="updateTaskPriority({{ $m->id }}, $event.target.value)"
                                        class="tech-input text-[9px] py-1 px-2 w-full {{ $priorityColors[$m->priority] ?? '' }}">
                                        <option value="baja"    {{ $m->priority === 'baja'    ? 'selected' : '' }}>🔵 Baja</option>
                                        <option value="media"   {{ $m->priority === 'media'   ? 'selected' : '' }}>🟡 Media</option>
                                        <option value="alta"    {{ $m->priority === 'alta'    ? 'selected' : '' }}>🟠 Alta</option>
                                        <option value="urgente" {{ $m->priority === 'urgente' ? 'selected' : '' }}>🔴 Urgente</option>
                                    </select>
                                </div>
                                {{-- Acciones --}}
                                <div class="md:col-span-2 flex justify-end">
                                    <button wire:click="confirmCancelTask({{ $m->id }})"
                                        class="p-2 rounded-lg border border-rose-500/20 text-rose-500/60 hover:text-rose-400 hover:bg-rose-500/10 hover:border-rose-500/40 transition-all"
                                        title="Cancelar misión">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-16 text-center">
                                <p class="font-mono-tech text-[9px] text-zinc-600 uppercase tracking-widest">No hay misiones activas</p>
                                <p class="font-mono-tech text-[8px] text-zinc-700 mt-1">Ajusta los filtros o crea una nueva misión</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Footer resumen --}}
                    <div class="px-5 py-3 border-t border-white/5 bg-white/[0.01] flex items-center justify-between">
                        <span class="font-mono-tech text-[8px] text-zinc-600 uppercase tracking-widest">{{ $missions->count() }} misión(es)</span>
                        <span class="font-mono-tech text-[8px] text-zinc-700">IRIS_MISSION_CTRL_v2</span>
                    </div>
                </div>
            </section>
        </div>

        {{-- ── Sidebar derecho (40%) ──────────────────────────────────────── --}}
        <div class="lg:col-span-4 space-y-8">

            {{-- Accesos rápidos --}}
            <div class="tech-card p-6 border-cyan-500/10 hidden md:block">
                <h3 class="font-mono-tech text-[10px] text-secondary uppercase tracking-widest mb-6 border-b border-white/5 pb-4">
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
                            <span class="text-[11px] font-bold text-secondary group-hover:text-{{ $n['color'] }}-400 uppercase tracking-wider transition-colors">{{ $n['label'] }}</span>
                            <svg class="w-4 h-4 text-zinc-600 group-hover:text-{{ $n['color'] }}-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 5l7 7-7 7" stroke-width="2.5" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Carga por gestor --}}
            <div class="tech-card overflow-hidden border-violet-500/10">
                <div class="p-5 border-b border-white/5 bg-violet-500/5">
                    <h3 class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest">Carga de Gestores</h3>
                    <p class="font-mono-tech text-[8px] text-zinc-600 mt-0.5">Tareas pendientes activas</p>
                </div>
                <div class="p-2 space-y-1 max-h-64 overflow-y-auto custom-scrollbar">
                    @forelse($gestores as $g)
                        <div class="flex items-center justify-between px-4 py-3 hover:bg-[var(--tech-hover-bg)] rounded-xl transition-all">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-violet-500/10 border border-violet-500/20 flex items-center justify-center text-[10px] font-bold text-violet-400">
                                    {{ strtoupper(substr($g->name, 0, 1)) }}
                                </div>
                                <span class="font-mono-tech text-[10px] tech-text-primary font-bold">{{ $g->name }}</span>
                            </div>
                            @if($g->pending_tasks_count > 0)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $g->pending_tasks_count >= 5 ? 'bg-rose-500/20 text-rose-400' : ($g->pending_tasks_count >= 3 ? 'bg-amber-500/20 text-amber-400' : 'bg-violet-500/20 text-violet-400') }}">
                                    {{ $g->pending_tasks_count }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] bg-emerald-500/10 text-emerald-400">✓ Libre</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-center font-mono-tech text-[9px] text-zinc-600 py-6 uppercase">Sin gestores</p>
                    @endforelse
                </div>
            </div>

            <!-- Logs de precios -->
            <div class="tech-card overflow-hidden">
                <div class="p-5 border-b border-white/5 bg-white/[0.01] flex justify-between items-center">
                    <a href="{{ route('admin.tariffs') }}">
                        <h3 class="font-mono-tech text-[10px] tech-text-secondary uppercase tracking-widest hover:text-zinc-300">
                            Logs de Precios
                        </h3>
                    </a>
                    <button wire:click="exportLogs"
                        class="flex items-center gap-2 px-3 py-1 rounded-lg bg-cyan-500/5 border border-cyan-500/20 text-cyan-400 font-mono-tech text-[9px] uppercase tracking-tighter hover:bg-cyan-500/10 hover:border-cyan-500/40 transition-all group">
                        <svg class="w-3 h-3 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                        </svg>
                        <span>Exportar</span>
                    </button>
                </div>
                <div class="p-2 space-y-1 max-h-[400px] overflow-y-auto custom-scrollbar">
                    @foreach($recentPriceLogs as $log)
                        <div class="p-4 hover:bg-[var(--tech-hover-bg)] rounded-xl transition-all cursor-default border border-transparent hover:border-white/5">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-mono-tech text-[9px] tech-text-secondary uppercase tracking-widest">{{ $log->item_label }}</span>
                                <span class="font-mono-tech text-[8px] text-zinc-600">{{ $log->created_at->format('H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-3 font-mono-tech text-[11px]">
                                <span class="text-zinc-600 line-through">${{ number_format((float) $log->old_price, 0) }}</span>
                                <svg class="w-3 h-3 text-zinc-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" />
                                </svg>
                                <span class="text-emerald-400 font-bold">${{ number_format((float) $log->new_price, 0) }}</span>
                            </div>
                            @if($log->reason)
                                <p class="text-[9px] text-zinc-500 mt-2 italic leading-relaxed">"{{ $log->reason }}"</p>
                            @endif
                            <div class="mt-3 pt-3 border-t border-white/5 flex justify-between items-center">
                                <span class="font-mono-tech text-[8px] text-violet-400 uppercase font-bold">{{ $log->admin->name ?? 'SYSTEM' }}</span>
                                <span class="text-[8px] text-zinc-700">AUTH_SIG_VALID</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-6 border border-[var(--border-glass)] rounded-3xl bg-[var(--tech-card-bg)]">
                <p class="font-mono-tech text-[9px] tech-text-secondary leading-relaxed uppercase tracking-widest">
                    Iris Aerospace Control Administrativo de reservas<br>
                    Base Control: Tierra<br>
                    Estado Global: Operacional
                </p>
            </div>
        </div>
    </div>

    {{-- ══ Modal de cancelación de tarea ═══════════════════════════════════════ --}}
    @if($showCancelModal)
        <div class="fixed inset-0 z-[110] flex items-center justify-center bg-black/70 backdrop-blur-md p-4">
            <div class="tech-card border-2 border-[var(--neon-rose)] rounded-[15px] max-w-md w-full overflow-hidden shadow-[0_0_50px_rgba(244,63,94,0.3)] bg-[var(--bg-panel)]/90 backdrop-blur-2xl animate-tech">
                <div class="p-6 border-b border-[var(--neon-rose)]/30 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-[var(--neon-rose)] text-white flex items-center justify-center shrink-0 shadow-lg shadow-[rgba(244,63,94,0.5)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-[var(--neon-rose)] uppercase tracking-tighter mb-1">Cancelar Misión</h3>
                        <p class="text-[var(--text-secondary)] text-sm leading-relaxed">
                            Esta acción es <strong class="text-[var(--text-primary)]">irreversible</strong>. La misión será eliminada permanentemente y el gestor ya no la verá en sus asignaciones.
                        </p>
                    </div>
                </div>
                <div class="flex p-4 gap-3 bg-[var(--tech-input-bg)]/50 border-t border-[var(--border-glass)]">
                    <button wire:click="$set('showCancelModal', false)"
                        class="flex-1 py-3 text-xs font-bold text-[var(--text-secondary)] bg-[var(--bg-panel)] border border-[var(--border-glass)] rounded-[10px] uppercase tracking-widest hover:bg-[var(--tech-hover-bg)] transition-all">
                        Conservar
                    </button>
                    <button wire:click="cancelTask"
                        class="flex-1 py-3 text-xs font-bold text-white bg-[var(--neon-rose)] hover:bg-[var(--neon-rose)]/90 rounded-[10px] uppercase tracking-widest shadow-lg shadow-[rgba(244,63,94,0.4)] border border-[var(--neon-rose)] transition-all">
                        Sí, Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
