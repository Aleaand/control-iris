<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Radar de Recursos</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Visor logístico global (Solo Lectura)</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-white/10 mb-6">
        <button wire:click="$set('activeTab', 'flights')" class="px-4 py-2 font-mono-tech text-[10px] uppercase tracking-widest border-b-2 transition-colors {{ $activeTab === 'flights' ? 'border-cyan-400 text-cyan-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">Vuelos Espaciales</button>
        <button wire:click="$set('activeTab', 'hotels')" class="px-4 py-2 font-mono-tech text-[10px] uppercase tracking-widest border-b-2 transition-colors {{ $activeTab === 'hotels' ? 'border-emerald-400 text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">Hoteles Terrestres</button>
        <button wire:click="$set('activeTab', 'tariffs')" class="px-4 py-2 font-mono-tech text-[10px] uppercase tracking-widest border-b-2 transition-colors {{ $activeTab === 'tariffs' ? 'border-violet-400 text-violet-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">Tarifas Vigentes</button>
    </div>

    @if($activeTab === 'flights')
        <div class="space-y-6">
            {{-- Compact Search Bar --}}
            <div class="tech-card rounded-2xl p-2 border border-white/5 bg-black/40 backdrop-blur-xl">
                <div class="flex flex-wrap md:flex-nowrap items-center gap-1">
                    {{-- Origen --}}
                    <div class="flex-1 min-w-[140px]">
                        <select wire:model.live="filterOrigin" class="w-full bg-transparent border-0 text-xs font-bold text-zinc-300 focus:ring-0 cursor-pointer hover:bg-white/5 py-3 px-4 rounded-xl transition-all">
                            <option value="">Cualquier Origen</option>
                            @foreach($destinations as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="w-px h-6 bg-white/10 hidden md:block"></div>

                    {{-- Destino --}}
                    <div class="flex-1 min-w-[140px]">
                        <select wire:model.live="filterDestination" class="w-full bg-transparent border-0 text-xs font-bold text-cyan-400 focus:ring-0 cursor-pointer hover:bg-white/5 py-3 px-4 rounded-xl transition-all">
                            <option value="">Cualquier Destino</option>
                            @foreach($destinations as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-px h-6 bg-white/10 hidden md:block"></div>

                    {{-- Fecha (Modal Trigger) --}}
                    <button type="button" wire:click="$set('showDateModal', true)" class="flex-1 min-w-[180px] text-left hover:bg-white/5 py-3 px-4 rounded-xl transition-all group">
                        <p class="text-[9px] font-mono-tech text-zinc-500 uppercase tracking-widest leading-none mb-1">Temporalidad</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-300 group-hover:text-white">
                                @if($dateSearchMode === 'exact')
                                    {{ $filterDate ? \Carbon\Carbon::parse($filterDate)->format('d M Y') : 'Seleccionar Fecha' }}
                                @else
                                    {{ $filterMonth ? ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][$filterMonth-1] : 'Cualquier Mes' }} {{ $filterYear ?: '' }}
                                @endif
                            </span>
                            <svg class="w-4 h-4 text-zinc-600 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </button>

                    <div class="w-px h-6 bg-white/10 hidden md:block"></div>

                    {{-- Precio --}}
                    <div class="w-[120px]">
                        <div class="relative group">
                            <input wire:model.live="filterMaxPrice" type="number" placeholder="Max. €" class="w-full bg-transparent border-0 text-xs font-bold text-emerald-400 placeholder-zinc-600 focus:ring-0 py-3 px-4 rounded-xl group-hover:bg-white/5 transition-all">
                        </div>
                    </div>

                    {{-- Buscar --}}
                    <div class="w-full md:w-auto p-1">
                        <button class="w-full md:w-12 h-10 flex items-center justify-center bg-cyan-500 hover:bg-cyan-400 text-black rounded-xl transition-all shadow-lg shadow-cyan-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Date Modal --}}
            @if($showDateModal)
                <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" wire:click="$set('showDateModal', false)"></div>
                    <div class="relative bg-[#0a0a0a] border border-white/10 rounded-3xl w-full max-w-md overflow-hidden shadow-2xl animate-in zoom-in-95 duration-200">
                        <div class="p-6 border-b border-white/5 flex justify-between items-center bg-white/2">
                            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-300">Seleccionar Temporalidad</h3>
                            <div class="flex p-1 bg-black rounded-xl border border-white/5">
                                <button type="button" wire:click="$set('dateSearchMode', 'exact')" class="px-3 py-1.5 text-[9px] font-bold uppercase rounded-lg transition-all {{ $dateSearchMode === 'exact' ? 'bg-cyan-500 text-black shadow-lg shadow-cyan-500/20' : 'text-zinc-500 hover:text-zinc-300' }}">Exacta</button>
                                <button type="button" wire:click="$set('dateSearchMode', 'flexible')" class="px-3 py-1.5 text-[9px] font-bold uppercase rounded-lg transition-all {{ $dateSearchMode === 'flexible' ? 'bg-cyan-500 text-black shadow-lg shadow-cyan-500/20' : 'text-zinc-500 hover:text-zinc-300' }}">Flexible</button>
                            </div>
                        </div>

                        <div class="p-8">
                            @if($dateSearchMode === 'exact')
                                <div class="space-y-4">
                                    <label class="block font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest text-center mb-4">Selecciona un día específico</label>
                                    <input type="date" wire:model.live="filterDate" class="w-full bg-black border border-white/10 rounded-2xl py-4 px-6 text-xl font-bold text-cyan-400 focus:border-cyan-500 focus:ring-0 text-center transition-all">
                                </div>
                            @else
                                <div class="space-y-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <button type="button" wire:click="$set('filterYear', {{ ($filterYear ?: date('Y')) - 1 }})" class="p-2 hover:bg-white/5 rounded-lg text-zinc-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                                        <span class="text-lg font-black text-white tracking-widest">{{ $filterYear ?: date('Y') }}</span>
                                        <button type="button" wire:click="$set('filterYear', {{ ($filterYear ?: date('Y')) + 1 }})" class="p-2 hover:bg-white/5 rounded-lg text-zinc-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                                    </div>

                                    <div class="grid grid-cols-3 gap-3">
                                        @foreach(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $idx => $m)
                                            <button type="button" 
                                                wire:click="$set('filterMonth', {{ $idx + 1 }})"
                                                class="py-3 px-2 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all border {{ $filterMonth == ($idx + 1) ? 'bg-cyan-500 border-cyan-400 text-black shadow-lg shadow-cyan-500/20' : 'bg-white/2 border-white/5 text-zinc-500 hover:border-white/20 hover:text-white' }}">
                                                {{ $m }}
                                            </button>
                                        @endforeach
                                    </div>
                                    
                                    <button type="button" wire:click="$set('filterMonth', null)" class="w-full py-2 text-[9px] font-bold text-zinc-600 uppercase tracking-widest hover:text-zinc-400 transition-colors">Limpiar Selección de Mes</button>
                                </div>
                            @endif
                        </div>

                        <div class="p-4 bg-white/2 border-t border-white/5">
                            <button type="button" wire:click="$set('showDateModal', false)" class="w-full py-4 bg-zinc-800 hover:bg-zinc-700 text-white font-bold uppercase tracking-[0.2em] text-xs rounded-2xl transition-all">Confirmar Selección</button>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @forelse($flights as $f)
                    <div class="tech-card rounded-xl p-5 border-l-2" style="border-left-color: rgba(6,182,212,0.5)">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono-tech text-[10px] text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded uppercase">{{ $f->flight_code }}</span>
                                    <span class="font-mono-tech text-[9px] text-zinc-600 uppercase tracking-widest">Base: {{ number_format($f->base_price, 2) }} €</span>
                                </div>
                                <div class="flex items-center gap-3 mt-3">
                                    <div class="text-left">
                                        <p class="font-mono-tech text-[8px] text-zinc-500 uppercase">Origen</p>
                                        <h4 class="font-bold text-sm text-zinc-300">{{ $f->origin?->name ?? 'Earth' }}</h4>
                                    </div>
                                    <svg class="w-4 h-4 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    <div class="text-left">
                                        <p class="font-mono-tech text-[8px] text-zinc-500 uppercase">Destino</p>
                                        <h4 class="font-bold text-sm text-cyan-400">{{ $f->destination?->name }}</h4>
                                    </div>
                                </div>
                                <p class="text-[10px] text-zinc-500 mt-2">Salida Programada: {{ $f->departure_date?->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-zinc-400">{{ $f->starship?->name }}</p>
                                <p class="font-mono-tech text-[9px] text-zinc-600 mt-0.5">{{ $f->starship?->model }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-white/5">
                            {{-- Nova --}}
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-mono-tech text-[9px] text-cyan-400 uppercase">Clase Nova</span>
                                    <span class="font-mono-tech text-[9px] {{ $f->nova_free === 0 ? 'text-rose-400' : 'text-zinc-400' }}">{{ $f->nova_free }} libres</span>
                                </div>
                                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full bg-cyan-500 transition-all" style="width: {{ $f->nova_pct }}%"></div>
                                </div>
                            </div>
                            {{-- Supernova --}}
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-mono-tech text-[9px] text-violet-400 uppercase">Clase Supernova</span>
                                    <span class="font-mono-tech text-[9px] {{ $f->sn_free === 0 ? 'text-rose-400' : 'text-zinc-400' }}">{{ $f->sn_free }} libres</span>
                                </div>
                                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full bg-violet-500 transition-all" style="width: {{ $f->sn_pct }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-sm">No hay vuelos programados.</p>
                @endforelse
            </div>
        </div>
    @elseif($activeTab === 'hotels')
        <div class="space-y-4">
            <input wire:model.live="searchHotel" type="text" placeholder="Buscar hotel o ubicación..." class="w-full md:w-1/3 px-3 py-2 rounded-lg text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($hotels as $h)
                    <div class="tech-card rounded-xl p-5 border-l-2" style="border-left-color: rgba(16,185,129,0.5)">
                        <h3 class="font-bold mb-1" style="color: var(--text-primary)">{{ $h->name }}</h3>
                        <p class="text-[10px] text-zinc-500 mb-4">{{ $h->location?->name }}</p>
                        
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-mono-tech text-[9px] text-zinc-400 uppercase">Ocupación</span>
                            <span class="font-mono-tech text-[9px] {{ $h->available === 0 ? 'text-rose-400' : 'text-emerald-400' }}">{{ $h->available }} disp.</span>
                        </div>
                        <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full {{ $h->pct > 90 ? 'bg-rose-500' : 'bg-emerald-500' }} transition-all" style="width: {{ $h->pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-sm">No hay hoteles disponibles.</p>
                @endforelse
            </div>
        </div>
    @elseif($activeTab === 'tariffs')
        <div class="tech-card rounded-xl p-5 overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left py-3 px-4 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">ID</th>
                        <th class="text-left py-3 px-4 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Servicio</th>
                        <th class="text-left py-3 px-4 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Tipo</th>
                        <th class="text-left py-3 px-4 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Precio Actual</th>
                        <th class="text-left py-3 px-4 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Última Mod.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($priceLogs->unique('service_id')->unique('service_type') as $log)
                        <tr class="border-b border-white/5 hover:bg-white/2">
                            <td class="py-3 px-4 font-mono-tech text-[9px] text-violet-400">{{ $log->service_type === 'flight' ? 'VUELO' : ($log->service_type === 'hotel' ? 'HOTEL' : 'TERR') }}-{{ $log->service_id }}</td>
                            <td class="py-3 px-4" style="color: var(--text-primary)">
                                @if($log->service_type === 'flight')
                                    Vuelo a {{ App\Models\Flight::find($log->service_id)?->destination?->name ?? 'N/A' }}
                                @elseif($log->service_type === 'hotel')
                                    Hotel {{ App\Models\Hotel::find($log->service_id)?->name ?? 'N/A' }}
                                @else
                                    Vuelo Terr. {{ App\Models\TerrestrialFlight::find($log->service_id)?->originLocation?->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="py-3 px-4 text-zinc-400 text-[10px]">{{ ucfirst($log->service_type) }}</td>
                            <td class="py-3 px-4 font-mono-tech text-emerald-400 font-bold">{{ number_format($log->new_price, 2) }} €</td>
                            <td class="py-3 px-4 text-[10px] text-zinc-500">{{ $log->created_at->format('d/m/y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-zinc-500 text-sm">No hay tarifas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
