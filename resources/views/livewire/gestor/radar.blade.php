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
        <button wire:click="$set('activeTab', 'tflights')" class="px-4 py-2 font-mono-tech text-[10px] uppercase tracking-widest border-b-2 transition-colors {{ $activeTab === 'tflights' ? 'border-violet-400 text-violet-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">Vuelos Terrestres</button>
        <button wire:click="$set('activeTab', 'hotels')" class="px-4 py-2 font-mono-tech text-[10px] uppercase tracking-widest border-b-2 transition-colors {{ $activeTab === 'hotels' ? 'border-emerald-400 text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">Hoteles Terrestres</button>
        <button wire:click="$set('activeTab', 'tariffs')" class="px-4 py-2 font-mono-tech text-[10px] uppercase tracking-widest border-b-2 transition-colors {{ $activeTab === 'tariffs' ? 'border-amber-400 text-amber-400' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">Servicios Extras</button>
    </div>

    @if($activeTab === 'flights')
        <div class="space-y-6">
            {{-- Compact Search Bar --}}
            <div class="tech-card rounded-2xl p-2 border border-white/5 bg-black/40 backdrop-blur-xl">
                <div class="flex flex-wrap md:flex-nowrap items-center gap-1">
                    <div class="flex-1 min-w-[140px]">
                        <select wire:model.live="filterOrigin" class="w-full bg-transparent border-0 text-xs font-bold text-zinc-300 focus:ring-0 cursor-pointer hover:bg-white/5 py-3 px-4 rounded-xl transition-all">
                            <option value="">Cualquier Origen</option>
                            @foreach($destinations as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-px h-6 bg-white/10 hidden md:block"></div>
                    <div class="flex-1 min-w-[140px]">
                        <select wire:model.live="filterDestination" class="w-full bg-transparent border-0 text-xs font-bold text-cyan-400 focus:ring-0 cursor-pointer hover:bg-white/5 py-3 px-4 rounded-xl transition-all">
                            <option value="">Cualquier Destino</option>
                            @foreach($destinations as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-px h-6 bg-white/10 hidden md:block"></div>
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
                        </div>
                    </button>
                    <div class="w-px h-6 bg-white/10 hidden md:block"></div>
                    <div class="w-[120px]">
                        <input wire:model.live="filterMaxPrice" type="number" placeholder="Max €" class="w-full bg-transparent border-0 text-xs font-bold text-emerald-400 focus:ring-0 py-3 px-4 rounded-xl transition-all">
                    </div>
                </div>
            </div>

            @if($showDateModal)
                <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" wire:click="$set('showDateModal', false)"></div>
                    <div class="relative bg-[#0a0a0a] border border-white/10 rounded-3xl w-full max-w-md overflow-hidden shadow-2xl">
                        <div class="p-6 border-b border-white/5 flex justify-between items-center bg-white/2">
                            <h3 class="text-sm font-black uppercase tracking-widest text-zinc-300">Seleccionar Temporalidad</h3>
                            <div class="flex p-1 bg-black rounded-xl border border-white/5">
                                <button type="button" wire:click="$set('dateSearchMode', 'exact')" class="px-3 py-1.5 text-[9px] font-bold uppercase rounded-lg {{ $dateSearchMode === 'exact' ? 'bg-cyan-500 text-black' : 'text-zinc-500' }}">Exacta</button>
                                <button type="button" wire:click="$set('dateSearchMode', 'flexible')" class="px-3 py-1.5 text-[9px] font-bold uppercase rounded-lg {{ $dateSearchMode === 'flexible' ? 'bg-cyan-500 text-black' : 'text-zinc-500' }}">Flexible</button>
                            </div>
                        </div>
                        <div class="p-8">
                            @if($dateSearchMode === 'exact')
                                <input type="date" wire:model.live="filterDate" class="w-full bg-black border border-white/10 rounded-2xl py-4 text-center text-cyan-400 font-bold">
                            @else
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach(['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'] as $idx => $m)
                                        <button wire:click="$set('filterMonth', {{ $idx+1 }})" class="py-2 rounded-lg text-[10px] font-bold border {{ $filterMonth == ($idx+1) ? 'bg-cyan-500 text-black' : 'border-white/5 text-zinc-500' }}">{{ $m }}</button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="p-4 bg-white/2 border-t border-white/5">
                            <button wire:click="$set('showDateModal', false)" class="w-full py-3 bg-zinc-800 text-white rounded-xl uppercase text-[10px] font-bold tracking-widest">Cerrar</button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach($flights as $f)
                    <div class="tech-card rounded-xl p-5 border-l-2 border-cyan-500/50">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-[10px] text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded">{{ $f->flight_code }}</span>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-sm font-bold text-zinc-300">{{ $f->origin?->name }}</span>
                                    <svg class="w-4 h-4 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    <span class="text-sm font-bold text-cyan-400">{{ $f->destination?->name }}</span>
                                </div>
                                <p class="text-[10px] text-zinc-500 mt-1">{{ $f->departure_date->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-white">{{ number_format($f->base_price, 2) }} €</p>
                                <p class="text-[9px] text-zinc-500">{{ $f->starship?->name }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                            <div>
                                <div class="flex justify-between text-[9px] mb-1"><span class="text-cyan-400 uppercase">Nova</span><span class="text-zinc-500">{{ $f->nova_free }} libres</span></div>
                                <div class="h-1 bg-white/5 rounded-full overflow-hidden"><div class="h-full bg-cyan-500" style="width:{{ $f->nova_pct }}%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-[9px] mb-1"><span class="text-violet-400 uppercase">Supernova</span><span class="text-zinc-500">{{ $f->sn_free }} libres</span></div>
                                <div class="h-1 bg-white/5 rounded-full overflow-hidden"><div class="h-full bg-violet-500" style="width:{{ $f->sn_pct }}%"></div></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $flights->links('vendor.livewire.simple-tailwind') }}
        </div>
    @elseif($activeTab === 'tflights')
        <div class="space-y-6">
            <div class="tech-card rounded-2xl p-2 border border-white/5 bg-black/40 backdrop-blur-xl">
                <div class="flex flex-wrap md:flex-nowrap items-center gap-1">
                    <div class="flex-1 min-w-[140px]">
                        <select wire:model.live="tFlightOrigin" class="w-full bg-transparent border-0 text-xs font-bold text-zinc-300 focus:ring-0 py-3 px-4 rounded-xl">
                            <option value="">Cualquier Origen</option>
                            @foreach($locations as $l)
                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-px h-6 bg-white/10"></div>
                    <div class="flex-1 min-w-[140px]">
                        <select wire:model.live="tFlightDest" class="w-full bg-transparent border-0 text-xs font-bold text-violet-400 focus:ring-0 py-3 px-4 rounded-xl">
                            <option value="">Cualquier Destino</option>
                            @foreach($locations as $l)
                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-px h-6 bg-white/10"></div>
                    <div class="flex-1 min-w-[140px]">
                        <input type="date" wire:model.live="tFlightDate" class="w-full bg-transparent border-0 text-xs font-bold text-zinc-300 focus:ring-0 py-3 px-4 rounded-xl">
                    </div>
                    <div class="w-px h-6 bg-white/10"></div>
                    <div class="w-[120px]">
                        <input wire:model.live="tFlightMaxPrice" type="number" placeholder="Max €" class="w-full bg-transparent border-0 text-xs font-bold text-emerald-400 focus:ring-0 py-3 px-4 rounded-xl">
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($tflights as $tf)
                    <div class="tech-card rounded-xl p-5 border-l-2 border-violet-500/50">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-[10px] text-violet-400 bg-violet-500/10 px-2 py-0.5 rounded">{{ $tf->flight_number }}</span>
                            <span class="text-xs font-black text-emerald-400">{{ number_format($tf->price, 2) }} €</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-zinc-300">{{ $tf->originLocation?->name }}</span>
                            <svg class="w-3 h-3 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            <span class="text-xs font-bold text-violet-400">{{ $tf->destinationLocation?->name }}</span>
                        </div>
                        <p class="text-[9px] text-zinc-500 mt-3 italic">{{ $tf->departure_datetime?->format('d/m/y H:i') }}</p>
                    </div>
                @endforeach
            </div>
            {{ $tflights->links('vendor.livewire.simple-tailwind') }}
        </div>
    @elseif($activeTab === 'hotels')
        <div class="space-y-4">
            <input wire:model.live="searchHotel" type="text" placeholder="Buscar hotel..." class="w-full md:w-1/3 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-zinc-300 focus:ring-0 focus:border-emerald-500">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($hotels as $h)
                    <div class="tech-card rounded-xl p-5 border-l-2 border-emerald-500/50">
                        <h3 class="font-bold mb-1 text-sm text-white">{{ $h->name }}</h3>
                        <p class="text-[10px] text-zinc-500 mb-4 uppercase tracking-widest">{{ $h->location?->name }}</p>
                        <div class="flex justify-between items-center mb-1"><span class="text-[9px] text-zinc-400 uppercase">Ocupación</span><span class="text-[9px] text-emerald-400">{{ $h->available }} disp.</span></div>
                        <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden"><div class="h-full bg-emerald-500" style="width:{{ $h->pct }}%"></div></div>
                    </div>
                @endforeach
            </div>
            {{ $hotels->links('vendor.livewire.simple-tailwind') }}
        </div>
    @elseif($activeTab === 'tariffs')
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="tech-card rounded-2xl p-6 border-l-4 border-cyan-500/50 bg-black/40">
                    <div class="flex items-center gap-3 mb-4"><div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></div><h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Pasaporte Espacial</h4></div>
                    <p class="text-3xl font-mono-tech font-black text-white">{{ number_format($extraPrices['passport'], 2) }} €</p>
                </div>
                <div class="tech-card rounded-2xl p-6 border-l-4 border-violet-500/50 bg-black/40">
                    <div class="flex items-center gap-3 mb-4"><div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center text-violet-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div><h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Iris Training</h4></div>
                    <p class="text-3xl font-mono-tech font-black text-white">{{ number_format($extraPrices['training'], 2) }} €</p>
                </div>
                <div class="tech-card rounded-2xl p-6 border-l-4 border-emerald-500/50 bg-black/40">
                    <div class="flex items-center gap-3 mb-4"><div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div><h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Seguro Reembolso</h4></div>
                    <p class="text-3xl font-mono-tech font-black text-emerald-400">{{ number_format($extraPrices['insurance'], 1) }}%</p>
                </div>
                <div class="tech-card rounded-2xl p-6 border-l-4 border-amber-500/50 bg-black/40">
                    <div class="flex items-center gap-3 mb-4"><div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div><h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">VIP Transfer</h4></div>
                    <p class="text-3xl font-mono-tech font-black text-white">{{ number_format($extraPrices['vip_transfer'], 2) }} €</p>
                </div>
            </div>

            <div class="tech-card rounded-2xl p-0 overflow-hidden border border-white/5 bg-black/20">
                <div class="p-4 border-b border-white/5 bg-white/2"><h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-300">Tarifas de Traslado por Zona</h4></div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 divide-x divide-y divide-white/5">
                    @foreach($locations as $loc)
                        <div class="p-4 hover:bg-white/2 transition-colors flex justify-between items-center">
                            <div><p class="text-[10px] font-bold text-white uppercase">{{ $loc->name }}</p><p class="text-[8px] text-zinc-500 font-mono-tech">{{ $loc->code }}</p></div>
                            <span class="text-sm font-black text-amber-400">{{ number_format($loc->transport_price, 2) }} €</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
