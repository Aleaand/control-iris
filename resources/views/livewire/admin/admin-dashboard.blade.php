<div class="min-h-screen bg-[#050505] p-4 md:p-8" x-data="{ 
    formatCurrency(val) {
        return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(val);
    }
}">
    <!-- Header: Welcome Section -->
    <div class="max-w-7xl mx-auto mb-10">
        <div class="relative overflow-hidden bg-gradient-to-br from-violet-900/40 to-indigo-900/10 border border-violet-500/30 rounded-[24px] p-8 md:p-12 shadow-2xl">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 w-64 h-64 bg-violet-600/20 blur-[100px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 translate-y-12 -translate-x-12 w-48 h-48 bg-cyan-600/20 blur-[80px] rounded-full"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tighter mb-4">
                        Bienvenido al <span class="bg-gradient-to-r from-violet-400 to-cyan-400 bg-clip-text text-transparent">Super Admin</span>
                    </h1>
                    <p class="text-violet-200/70 text-lg max-w-2xl leading-relaxed">
                        Sistema de Control de Misión Iris Aerospace. <span class="text-amber-400 font-bold">⚠️ Recordatorio:</span> Maneje con extrema precaución, posee acceso total a la logística de reservas y finanzas corporativas.
                    </p>
                </div>
                
                <!-- Quick Access -->
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('admin.finances') }}" class="group relative px-6 py-3 bg-zinc-900/80 border border-zinc-700/50 hover:border-violet-500/50 rounded-xl transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-r from-violet-600/10 to-indigo-600/10 opacity-0 group-hover:opacity-100 rounded-xl transition-opacity"></div>
                        <span class="relative text-sm font-bold text-zinc-300 group-hover:text-white uppercase tracking-widest">Finanzas</span>
                    </a>
                    <a href="{{ route('admin.flights') }}" class="group relative px-6 py-3 bg-zinc-900/80 border border-zinc-700/50 hover:border-cyan-500/50 rounded-xl transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/10 to-blue-600/10 opacity-0 group-hover:opacity-100 rounded-xl transition-opacity"></div>
                        <span class="relative text-sm font-bold text-zinc-300 group-hover:text-white uppercase tracking-widest">Vuelos</span>
                    </a>
                    <a href="{{ route('admin.reservations') }}" class="group relative px-6 py-3 bg-zinc-900/80 border border-zinc-700/50 hover:border-amber-500/50 rounded-xl transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-r from-amber-600/10 to-orange-600/10 opacity-0 group-hover:opacity-100 rounded-xl transition-opacity"></div>
                        <span class="relative text-sm font-bold text-zinc-300 group-hover:text-white uppercase tracking-widest">Reservas</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LEFT COLUMN: Financials -->
        <div class="lg:col-span-2 space-y-8">
            <h2 class="text-xs font-black text-zinc-500 uppercase tracking-[0.3em] flex items-center gap-3">
                <span class="w-8 h-px bg-zinc-800"></span> 01. Estado Financiero
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Projected vs Real -->
                <div class="bg-[#0c0c0e] border border-zinc-800/60 rounded-3xl p-6 hover:border-zinc-700/80 transition-all group">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h3 class="text-zinc-400 text-xs font-bold uppercase tracking-widest mb-1">Ingresos de Misión</h3>
                            <p class="text-2xl font-black text-white" x-text="formatCurrency({{ $realIncome }})"></p>
                        </div>
                        <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-[10px] uppercase font-bold tracking-widest mb-2">
                                <span class="text-zinc-500">Cobrado (Real)</span>
                                <span class="text-emerald-400">{{ number_format($realIncome > 0 ? ($realIncome / max(1, $realIncome + $projectedIncome)) * 100 : 0, 1) }}%</span>
                            </div>
                            <div class="h-2 bg-zinc-900 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: {{ $realIncome > 0 ? ($realIncome / max(1, $realIncome + $projectedIncome)) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="pt-2 flex justify-between items-center text-xs">
                            <span class="text-zinc-500 font-bold uppercase tracking-wide">Proyectado (Pendiente)</span>
                            <span class="text-zinc-300 font-black" x-text="formatCurrency({{ $projectedIncome }})"></span>
                        </div>
                    </div>
                </div>

                <!-- Margin & Rentability -->
                <div class="bg-[#0c0c0e] border border-zinc-800/60 rounded-3xl p-6 hover:border-zinc-700/80 transition-all">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h3 class="text-zinc-400 text-xs font-bold uppercase tracking-widest mb-1">Margen Operativo</h3>
                            <p class="text-3xl font-black text-white">{{ number_format($avgProfitability, 1) }}%</p>
                        </div>
                        <div class="p-3 bg-violet-500/10 border border-violet-500/20 rounded-xl text-violet-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>
                    <div class="p-4 bg-violet-950/20 border border-violet-500/20 rounded-2xl">
                        <p class="text-[10px] text-violet-300 font-bold uppercase tracking-widest leading-relaxed">
                            Rendimiento promedio de las misiones espaciales activas tras descontar costes operativos de naves y tripulación.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Critical Occupancy -->
            <div class="bg-[#0c0c0e] border border-zinc-800/60 rounded-3xl overflow-hidden">
                <div class="p-6 border-b border-zinc-800/40 flex justify-between items-center">
                    <h3 class="text-white text-sm font-black uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span> Alerta de Ocupación Crítica
                    </h3>
                    <span class="text-[10px] font-bold text-red-500 bg-red-500/10 px-2 py-1 rounded border border-red-500/20 uppercase tracking-widest">Bajo 70%</span>
                </div>
                <div class="divide-y divide-zinc-800/40">
                    @forelse($criticalFlights as $f)
                        <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4 hover:bg-zinc-900/20 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 font-black">
                                    {{ substr($f->destination->name, 0, 2) }}
                                </div>
                                <div>
                                    <h4 class="text-white font-bold uppercase tracking-wide">#{{ $f->flight_code }} <span class="mx-2 text-zinc-700 text-xs">→</span> {{ $f->destination->name }}</h4>
                                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">Sale en: {{ $f->departure_date->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6 w-full md:w-auto">
                                <div class="flex-1 md:w-48">
                                    <div class="flex justify-between text-[9px] uppercase font-black text-zinc-500 mb-1.5 tracking-widest">
                                        <span>Ocupación</span>
                                        <span class="text-red-400">{{ $f->occupancy_percentage }}%</span>
                                    </div>
                                    <div class="h-1.5 bg-zinc-900 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-600 shadow-[0_0_8px_rgba(220,38,38,0.5)]" style="width: {{ $f->occupancy_percentage }}%"></div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.flights') }}" class="p-2 text-zinc-500 hover:text-white hover:bg-zinc-800 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <p class="text-zinc-600 font-bold uppercase tracking-widest text-xs">No hay vuelos en alerta crítica actualmente ✨</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Mission Control & Audit -->
        <div class="space-y-8">
            <h2 class="text-xs font-black text-zinc-500 uppercase tracking-[0.3em] flex items-center gap-3">
                <span class="w-8 h-px bg-zinc-800"></span> 02. Mission Control
            </h2>

            <!-- Starships Status -->
            <div class="bg-[#0c0c0e] border border-zinc-800/60 rounded-3xl p-6">
                <h3 class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.2em] mb-6">Estado de la Flota</h3>
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-4 bg-zinc-900/50 rounded-2xl text-center border border-zinc-800/40">
                        <p class="text-xl font-black text-cyan-400 mb-1">{{ $starshipsStatus['in_flight'] }}</p>
                        <p class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest">En Vuelo</p>
                    </div>
                    <div class="p-4 bg-zinc-900/50 rounded-2xl text-center border border-zinc-800/40">
                        <p class="text-xl font-black text-amber-500 mb-1">{{ $starshipsStatus['maintenance'] }}</p>
                        <p class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest">Mantenim.</p>
                    </div>
                    <div class="p-4 bg-zinc-900/50 rounded-2xl text-center border border-zinc-800/40">
                        <p class="text-xl font-black text-emerald-400 mb-1">{{ $starshipsStatus['ready'] }}</p>
                        <p class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest">Listas</p>
                    </div>
                </div>
            </div>

            <!-- AU Traveled -->
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-900 to-blue-900 rounded-3xl p-6 shadow-xl border border-white/5">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 blur-[50px] rounded-full"></div>
                <h3 class="text-white/60 text-[10px] font-black uppercase tracking-[0.2em] mb-2 relative z-10">Total Recorrido</h3>
                <div class="flex items-baseline gap-2 relative z-10">
                    <span class="text-4xl font-black text-white italic tracking-tighter">{{ number_format($totalAURecorridas, 2) }}</span>
                    <span class="text-white/50 text-xs font-black uppercase italic">AU</span>
                </div>
                <p class="text-indigo-200/50 text-[9px] font-bold uppercase tracking-widest mt-4 leading-relaxed">
                    Unidades Astronómicas exploradas por la flota de Iris Aerospace junto a nuestros distinguidos clientes.
                </p>
            </div>

            <!-- Next Launches -->
            <div class="bg-[#0c0c0e] border border-zinc-800/60 rounded-3xl p-6">
                <h3 class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.2em] mb-6">Próximos lanzamientos (48h)</h3>
                <div class="space-y-4">
                    @forelse($nextLaunches as $launch)
                        <div class="flex items-center gap-3 p-3 bg-zinc-900/40 rounded-2xl border border-zinc-800/40">
                            <div class="w-10 h-10 rounded-xl bg-violet-600/20 border border-violet-500/20 flex items-center justify-center text-violet-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-black text-white uppercase tracking-wider truncate">#{{ $launch->flight_code }} - {{ $launch->destination->name }}</p>
                                <p class="text-[8px] font-bold text-cyan-500 uppercase tracking-widest mt-0.5">T-Minus: {{ $launch->departure_date->diffForHumans(null, true) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest text-center py-4">No hay lanzamientos inminentes</p>
                    @endforelse
                </div>
            </div>

            <!-- Price Audit Feed -->
            <div class="bg-[#0c0c0e] border border-zinc-800/60 rounded-3xl overflow-hidden">
                <div class="p-4 bg-zinc-900/30 border-b border-zinc-800/40">
                    <h3 class="text-zinc-400 text-[10px] font-black uppercase tracking-[0.2em]">Registro de Gobernanza (Price Logs)</h3>
                </div>
                <div class="p-2 space-y-1">
                    @foreach($recentPriceLogs as $log)
                        <div class="p-3 hover:bg-white/5 rounded-xl transition-all cursor-default group">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-[9px] font-black text-zinc-300 uppercase tracking-widest">{{ $log->item_label }}</span>
                                <span class="text-[8px] text-zinc-600 font-mono">{{ $log->created_at->format('H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-zinc-500 line-through text-[10px]">${{ number_format((float)$log->old_price, 0) }}</span>
                                <svg class="w-3 h-3 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                <span class="text-emerald-400 font-black text-[10px]">${{ number_format((float)$log->new_price, 0) }}</span>
                            </div>
                            @if($log->reason)
                                <p class="text-[8px] text-zinc-500 mt-1 italic group-hover:text-zinc-400 transition-colors">"{{ $log->reason }}"</p>
                            @endif
                            <p class="text-[8px] text-violet-400 font-bold uppercase tracking-widest mt-2">Por: {{ $log->admin->name ?? 'System' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
