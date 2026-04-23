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
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tighter mb-2">
                    <span x-text="saludo"></span>, <span class="text-gradient-cyan uppercase">Administrador</span>
                </h1>
                <div
                    class="flex items-center gap-4 font-mono-tech text-[10px] text-zinc-500 uppercase tracking-[0.2em]">
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
                        <span class="font-mono-tech text-[9px] text-zinc-500 uppercase">Recorrido Total</span>
                        <span class="text-xl font-bold text-white italic">
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
                    <h2 class="font-mono-tech text-xs font-bold text-zinc-400 uppercase tracking-[0.3em]"><a
                            href="{{ route('admin.finances') }}">Finanzas</a></h2>
                    <div class="h-px flex-1 bg-zinc-800/50"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="tech-card p-8 group">
                        <a href="{{ route('admin.finances') }}" class="block">
                            <div class="flex justify-between items-start mb-10">
                                <div>
                                    <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mb-2">
                                        Ingresos</p>
                                    <h3 class="text-4xl font-black text-white"
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
                                    <div
                                        class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 w-48 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-50 px-3 py-2 bg-zinc-900 border border-white/10 rounded-lg shadow-xl">
                                        <p
                                            class="font-mono-tech text-[8px] leading-tight text-zinc-400 uppercase tracking-tighter text-center">
                                            Peso del capital pendiente sobre el total esperado del año
                                        </p>
                                        <div
                                            class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-zinc-900">
                                        </div>
                                    </div>

                                    <div class="flex justify-between font-mono-tech text-[9px] uppercase mb-3">
                                        <span class="text-zinc-500">Pagos Pendientes</span>
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
                                    <span class="font-mono-tech text-[9px] text-zinc-500 uppercase">Capital
                                        Proyectado</span>
                                    <span class="text-sm font-bold text-zinc-300"
                                        x-text="formatCurrency({{ $projectedIncome }})"></span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Alerta de ocupación -->
                    <div class="tech-card overflow-hidden flex flex-col group/card">
                        <a href="{{ route('admin.flights', ['status' => 'scheduled'])}}"
                            class="p-6 border-b border-white/5 flex justify-between items-center bg-white/[0.01] hover:bg-white/[0.03] transition-colors">
                            <h3
                                class="text-white text-[10px] font-black font-mono-tech uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span> Alertas
                                Críticas
                            </h3>
                            <span
                                class="font-mono-tech text-[9px] text-red-500 border border-red-500/20 px-2 py-0.5 rounded">OCUPACIÓN
                                < 70%</span>
                        </a>
                        <div class="flex-1 divide-y divide-white/5 custom-scrollbar overflow-y-auto max-h-[280px]">
                            @forelse($criticalFlights as $f)
                                <a href="{{ route('admin.flights', ['search' => $f->flight_code])}}"
                                    class="p-5 hover:bg-white/[0.02] transition-colors flex justify-between items-center group/item">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-[10px] font-mono-tech text-zinc-500">
                                            {{ substr($f->destination->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-white uppercase tracking-wider">
                                                #{{ $f->flight_code }} </h4>
                                            <p class="font-bold text-[10px] text-white uppercase mt-1">
                                                {{ $f->origin->name }} <span class="text-zinc-700 mx-1">→</span>
                                                {{ $f->destination->name }}
                                            </p>
                                            <p class="font-mono-tech text-[8px] text-zinc-500 uppercase mt-1">Despegue:
                                                {{ $f->departure_date}}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-mono-tech text-[10px] text-red-400 font-bold">
                                            {{ $f->occupancy_percentage }}%
                                        </p>
                                        <div class="w-16 h-1 bg-zinc-900 rounded-full mt-1 overflow-hidden">
                                            <div class="h-full bg-red-500" style="width: {{ $f->occupancy_percentage }}%">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-12 text-center">
                                    <p class="font-mono-tech text-[10px] text-zinc-600 uppercase tracking-widest">
                                        Sistemas
                                        nominales. No hay alertas.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-4 mb-6">
                    <h2 class="font-mono-tech text-xs font-bold text-zinc-400 uppercase tracking-[0.3em]">Estado de
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
                            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase tracking-[0.2em]">
                                {{ $stat['label'] }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="lg:col-span-4 space-y-8">

            <div class="tech-card p-6 border-cyan-500/10 hidden md:block">
                <h3
                    class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mb-6 border-b border-white/5 pb-4">
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
                            class="flex items-center justify-between p-4 bg-white/[0.02] border border-white/5 rounded-xl hover:bg-white/[0.05] transition-all group">
                            <span
                                class="text-[11px] font-bold text-zinc-400 group-hover:text-{{ $n['color'] }}-400 uppercase tracking-wider transition-colors">{{ $n['label'] }}</span>
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
                            class="font-mono-tech text-[10px] text-zinc-400 uppercase tracking-widest hover:text-zinc-300">
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
                            class="p-4 hover:bg-white/[0.03] rounded-xl transition-all cursor-default border border-transparent hover:border-white/5">
                            <div class="flex justify-between items-start mb-2">
                                <span
                                    class="font-mono-tech text-[9px] text-zinc-300 uppercase tracking-widest">{{ $log->item_label }}</span>
                                <span
                                    class="font-mono-tech text-[8px] text-zinc-600">{{ $log->created_at->format('H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-3 font-mono-tech text-[11px]">
                                <span
                                    class="text-zinc-600 line-through">${{ number_format((float) $log->old_price, 0) }}</span>
                                <svg class="w-3 h-3 text-zinc-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <span class="text-[8px] text-zinc-700">AUTH_SIG_VALID</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-6 border border-white/5 rounded-3xl bg-gradient-to-br from-zinc-900/40 to-transparent">
                <p class="font-mono-tech text-[9px] text-zinc-600 leading-relaxed uppercase tracking-widest">
                    Iris Aerospace Control Administrativo de reservas<br>
                    Base Control: Tierrs<br>
                    Estado Global: Operacional
                </p>
            </div>
        </div>
    </div>
</div>