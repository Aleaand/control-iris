<div class="p-6 md:p-8 space-y-6 relative obsidian-bg min-h-screen text-[var(--text-primary)]" x-data="{ showScrollTop: false }" @scroll.window="showScrollTop = window.pageYOffset > 300">
    <div class="w-full">
    
    {{-- ══ HEADER ══ --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-[var(--neon-violet)]/30 pb-4">
        <div>
            <h2 class="text-3xl font-bold text-[var(--neon-violet)] tracking-tight uppercase flex items-center gap-3">
                Tarifario Maestro
            </h2>
            <p class="text-[var(--text-secondary)] text-sm mt-1 uppercase tracking-widest">
                Control de Precios, Tasas y Logs de Auditoría
            </p>
        </div>

        @if (session()->has('tariff_message'))
            <div class="mt-4 md:mt-0 bg-green-900/40 border border-green-700/50 text-green-400 px-4 py-2 text-sm font-medium uppercase tracking-wider rounded-[10px] flex items-center gap-2 shadow-[0_0_20px_rgba(16,185,129,0.1)]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('tariff_message') }}
            </div>
        @endif
    </div>

    {{-- Sub-Navigation & Search --}}
    <div class="flex flex-col lg:flex-row gap-6 items-center">
        <div class="flex-1 flex flex-col md:flex-row gap-3 w-full">
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-secondary)]" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Filtrar por nombre de servicio o ID..."
                    class="tech-input w-full py-2.5 pl-10 pr-4 text-xs focus:outline-none transition-all rounded-[12px]">
            </div>
        </div>
    </div>

    <div class="py-6 grid grid-cols-1 xl:grid-cols-5 gap-6">
        <div class="xl:col-span-3 space-y-6">

            {{-- Tab Navigation --}}
            <div class="flex bg-[var(--tech-input-bg)] p-1 rounded-[12px] border border-[var(--border-glass)] w-full lg:w-fit overflow-x-auto no-scrollbar scroll-smooth">
                @foreach([
                    'flights'      => ['label' => 'Vuelos Esp.', 'count' => count($flights)],
                    'hotels'       => ['label' => 'Hoteles',     'count' => count($hotels)],
                    'terrestrial'  => ['label' => 'Vuelos Terr.', 'count' => count($terrestrialFlights)],
                    'transfers'    => ['label' => 'Traslados',         'count' => count($locations)],
                    'services'     => ['label' => 'Servicios',   'count' => count($globalServices)],
                    'operational'  => ['label' => 'Costes Naves',      'count' => count($operationalRates) + count($starships)],
                ] as $tab => $meta)
                    <button wire:click="$set('activeTab', '{{ $tab }}')"
                        :class="activeTab === '{{ $tab }}' ? 'bg-[var(--neon-violet)] text-black shadow-lg' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]'"
                        class="px-3 md:px-4 py-2 rounded-[10px] text-[10px] md:text-[11px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2 whitespace-nowrap
                            {{ $activeTab === $tab ? 'bg-[var(--neon-violet)] text-black shadow-lg' : '' }}">
                        {{ $meta['label'] }}
                        <span class="text-[9px] {{ $activeTab === $tab ? 'bg-black/20 text-black' : 'bg-[var(--tech-hover-bg)] text-[var(--text-secondary)]' }} px-1.5 py-0.5 rounded-full font-mono font-bold">
                            {{ $meta['count'] }}
                        </span>
                    </button>
                @endforeach
            </div>

            {{-- ── VUELOS ESPACIALES ── --}}
            @if($activeTab === 'flights')
                <div class="tech-card overflow-hidden">
                    <div class="px-5 py-3 border-b border-[var(--border-glass)] flex items-center gap-2 bg-[var(--tech-input-bg)]">
                        <div class="w-1.5 h-1.5 rounded-full bg-[var(--neon-cyan)] animate-pulse"></div>
                        <span class="text-[10px] font-bold text-[var(--text-secondary)] uppercase tracking-[0.2em]">Vuelos Espaciales · Tasa Base</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[11px] text-left">
                            <thead class="bg-[var(--tech-input-bg)]/50 border-b border-[var(--border-glass)] text-[var(--text-secondary)] uppercase tracking-widest">
                                <tr>
                                    <th class="px-5 py-3 font-bold">Misión / Destino</th>
                                    <th class="px-5 py-3 font-bold">Lanzamiento</th>
                                    <th class="px-5 py-3 text-right font-bold">Clase Nova</th>
                                    <th class="px-5 py-3 text-right font-bold">Supernova (x2.5)</th>
                                    <th class="px-3 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border-glass)]">
                                @forelse($flights as $flight)
                                    <tr class="hover:bg-[var(--tech-hover-bg)] transition-colors group">
                                        <td class="px-5 py-4">
                                            <div class="font-bold text-[var(--neon-cyan)] font-mono text-xs">#{{ $flight->flight_code }}</div>
                                            <div class="text-[var(--text-secondary)] text-[10px] mt-0.5 uppercase tracking-tighter">→ {{ $flight->destination?->name ?? 'Tierra' }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-[var(--text-secondary)] font-mono">
                                            {{ $flight->departure_date?->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <span class="text-[var(--text-primary)] font-bold font-mono">€{{ number_format($flight->base_price, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-5 py-4 text-right text-[var(--neon-rose)]/70 font-mono">
                                            €{{ number_format($flight->base_price * 2.5, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-4 text-right">
                                            <button type="button"
                                                wire:click="openUpdateModal('flight', {{ $flight->id }}, '#{{ $flight->flight_code }} → {{ $flight->destination?->name }}', {{ $flight->base_price }}, '€')"
                                                class="opacity-0 group-hover:opacity-100 p-2.5 rounded-lg border border-[var(--neon-violet)]/30 text-[var(--neon-violet)] hover:bg-[var(--neon-violet)] hover:text-black transition-colors" title="Actualizar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-12 text-center text-[var(--text-secondary)] text-[10px] uppercase tracking-widest opacity-60">Sin vuelos próximos en el sistema</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── HOTELES ── --}}
            @if($activeTab === 'hotels')
                <div class="tech-card overflow-hidden">
                    <div class="px-5 py-3 border-b border-[var(--border-glass)] flex items-center gap-2 bg-[var(--tech-input-bg)]">
                        <div class="w-1.5 h-1.5 rounded-full bg-[var(--neon-rose)] animate-pulse"></div>
                        <span class="text-[10px] font-bold text-[var(--text-secondary)] uppercase tracking-[0.2em]">Alojamiento · Tarifa por Pernoctación</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[11px] text-left">
                            <thead class="bg-[var(--tech-input-bg)]/50 border-b border-[var(--border-glass)] text-[var(--text-secondary)] uppercase tracking-widest">
                                <tr>
                                    <th class="px-5 py-3 font-bold">Establecimiento / Destino</th>
                                    <th class="px-5 py-3 font-bold">Clasificación</th>
                                    <th class="px-5 py-3 text-right font-bold">Coste / Noche</th>
                                    <th class="px-3 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border-glass)]">
                                @forelse($hotels as $hotel)
                                    <tr class="hover:bg-[var(--tech-hover-bg)] transition-colors group">
                                        <td class="px-5 py-4">
                                            <div class="font-bold text-[var(--neon-rose)] text-sm">{{ $hotel->name }}</div>
                                            <div class="text-[var(--text-secondary)] text-[10px] mt-0.5 uppercase tracking-tighter">{{ $hotel->location?->name ?? '—' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex gap-0.5">
                                                @for($i = 0; $i < 5; $i++)
                                                    <span class="{{ $i < $hotel->galactic_stars ? 'text-[var(--neon-amber)]' : 'text-[var(--text-secondary)] opacity-20' }} text-[10px]">★</span>
                                                @endfor
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <span class="text-[var(--text-primary)] font-bold font-mono">€{{ number_format($hotel->price_per_night, 0, ',', '.') }}</span>
                                            <div class="text-[9px] text-[var(--text-secondary)] opacity-50 uppercase tracking-widest">/ noche</div>
                                        </td>
                                        <td class="px-3 py-4 text-right">
                                            <button type="button"
                                                wire:click="openUpdateModal('hotel', {{ $hotel->id }}, '{{ addslashes($hotel->name) }}', {{ $hotel->price_per_night }}, '€')"
                                                class="opacity-0 group-hover:opacity-100 p-2.5 rounded-lg border border-[var(--neon-violet)]/30 text-[var(--neon-violet)] hover:bg-[var(--neon-violet)] hover:text-black transition-colors" title="Actualizar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-12 text-center text-[var(--text-secondary)] text-[10px] uppercase tracking-widest opacity-60">Sin hoteles registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── VUELOS TERRESTRES ── --}}
            @if($activeTab === 'terrestrial')
                <div class="tech-card overflow-hidden">
                    <div class="px-5 py-3 border-b border-[var(--border-glass)] flex items-center gap-2 bg-[var(--tech-input-bg)]">
                        <div class="w-1.5 h-1.5 rounded-full bg-[var(--neon-amber)] animate-pulse"></div>
                        <span class="text-[10px] font-bold text-[var(--text-secondary)] uppercase tracking-[0.2em]">Conectividad · Vuelos de Aproximación</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[11px] text-left">
                            <thead class="bg-[var(--tech-input-bg)]/50 border-b border-[var(--border-glass)] text-[var(--text-secondary)] uppercase tracking-widest">
                                <tr>
                                    <th class="px-5 py-3 font-bold">Ruta / Código</th>
                                    <th class="px-5 py-3 font-bold">Operador · Itinerario</th>
                                    <th class="px-5 py-3 text-right font-bold">Precio</th>
                                    <th class="px-3 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border-glass)]">
                                @forelse($terrestrialFlights as $tf)
                                    <tr class="hover:bg-[var(--tech-hover-bg)] transition-colors group">
                                        <td class="px-5 py-4">
                                            <div class="font-bold text-[var(--neon-amber)] font-mono text-xs">#{{ $tf->flight_number }}</div>
                                            <div class="text-[var(--text-secondary)] text-[10px] mt-0.5 uppercase tracking-tighter">{{ $tf->originLocation?->name ?? '?' }} → {{ $tf->destinationLocation?->name ?? '?' }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="text-[var(--text-primary)] font-bold">{{ $tf->airline }}</div>
                                            <div class="text-[var(--text-secondary)] text-[10px] mt-0.5 font-mono">
                                                {{ \Carbon\Carbon::parse($tf->departure_datetime)->format('d/m/Y H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <span class="text-[var(--text-primary)] font-bold font-mono">€{{ number_format($tf->price, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-3 py-4 text-right">
                                            <button type="button"
                                                wire:click="openUpdateModal('terrestrial_flight', {{ $tf->id }}, '{{ addslashes($tf->originLocation?->name) }} → {{ addslashes($tf->destinationLocation?->name) }}', {{ $tf->price }}, '€')"
                                                class="opacity-0 group-hover:opacity-100 p-2.5 rounded-lg border border-[var(--neon-violet)]/30 text-[var(--neon-violet)] hover:bg-[var(--neon-violet)] hover:text-black transition-colors" title="Actualizar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-12 text-center text-[var(--text-secondary)] text-[10px] uppercase tracking-widest opacity-60">Sin vuelos terrestres registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── TRASLADOS VIP POR LOCALIZACIÓN ── --}}
            @if($activeTab === 'transfers')
                <div class="tech-card overflow-hidden">
                    <div class="px-5 py-3 border-b border-[var(--border-glass)] flex items-center gap-2 bg-[var(--tech-input-bg)]">
                        <div class="w-1.5 h-1.5 rounded-full bg-[var(--neon-violet)] animate-pulse"></div>
                        <span class="text-[10px] font-bold text-[var(--text-secondary)] uppercase tracking-[0.2em]">Traslados · Protocolo VIP a Spaceport</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[11px] text-left">
                            <thead class="bg-[var(--tech-input-bg)]/50 border-b border-[var(--border-glass)] text-[var(--text-secondary)] uppercase tracking-widest">
                                <tr>
                                    <th class="px-5 py-3 font-bold">Ubicación / Código de Área</th>
                                    <th class="px-5 py-3 text-right font-bold">Tarifa Traslado</th>
                                    <th class="px-3 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border-glass)]">
                                @forelse($locations as $loc)
                                    <tr class="hover:bg-[var(--tech-hover-bg)] transition-colors group">
                                        <td class="px-5 py-4">
                                            <div class="font-bold text-[var(--neon-violet)] text-sm">{{ $loc->name }}</div>
                                            <div class="text-[var(--text-secondary)] text-[10px] mt-0.5 font-mono uppercase tracking-widest">{{ $loc->code }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <span class="text-[var(--text-primary)] font-bold font-mono">€{{ number_format($loc->transport_price ?? 0, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-3 py-4 text-right">
                                            <button type="button"
                                                wire:click="openUpdateModal('vip_transfer_location', {{ $loc->id }}, '{{ addslashes($loc->name) }}', {{ $loc->transport_price ?? 0 }}, '€')"
                                                class="opacity-0 group-hover:opacity-100 p-2.5 rounded-lg border border-[var(--neon-violet)]/30 text-[var(--neon-violet)] hover:bg-[var(--neon-violet)] hover:text-black transition-colors" title="Actualizar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-12 text-center text-[var(--text-secondary)] text-[10px] uppercase tracking-widest opacity-60">Sin ubicaciones configuradas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── SERVICIOS FIJOS ── --}}
            @if($activeTab === 'services')
                <div class="space-y-4">
                    <div class="px-1 border-l-2 border-[var(--neon-emerald)] pl-4 py-1 bg-[var(--neon-emerald)]/5">
                        <p class="text-[10px] text-[var(--neon-emerald)] uppercase tracking-widest font-black">Servicios Adicionales · Tasas Globales</p>
                        <p class="text-[10px] text-[var(--text-secondary)] mt-0.5 uppercase">Ajuste de parámetros financieros transversales a todas las misiones.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($globalServices as $service)
                            <div class="group tech-card p-5 hover:border-[var(--neon-emerald)]/30 transition-all relative overflow-hidden">
                                <div class="absolute inset-0 bg-[var(--neon-emerald)] opacity-0 group-hover:opacity-[0.03] transition-opacity"></div>
                                <div class="flex items-start justify-between mb-4 relative z-10">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <p class="text-[var(--text-primary)] font-bold text-sm uppercase tracking-tight">{{ $service['label'] }}</p>
                                            <p class="text-[var(--text-secondary)] text-[10px] mt-0.5 uppercase tracking-tighter opacity-70">{{ $service['desc'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between relative z-10">
                                    <div>
                                        <p class="text-[9px] text-[var(--text-secondary)] uppercase tracking-widest opacity-60">Tarifa Vigente</p>
                                        <p class="text-[var(--neon-emerald)] font-mono font-black text-xl">
                                            {{ $service['unit'] === '%' ? '' : $service['unit'] }}{{ number_format($service['price'], 2, ',', '.') }}{{ $service['unit'] === '%' ? '%' : '' }}
                                        </p>
                                    </div>
                                    <button type="button"
                                        wire:click="openUpdateModal('{{ $service['type'] }}', 0, '{{ $service['label'] }}', {{ $service['price'] }}, '{{ $service['unit'] }}')"
                                        class="p-2.5 rounded-lg border border-[var(--neon-violet)]/30 text-[var(--neon-violet)] hover:bg-[var(--neon-violet)] hover:text-black transition-colors" title="Actualizar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── OPERATIVO AU ── --}}
            @if($activeTab === 'operational')
                <div class="space-y-6">
                    <div class="px-1 border-l-2 border-[var(--neon-cyan)] pl-4 py-1 bg-[var(--neon-cyan)]/5">
                        <p class="text-[10px] text-[var(--neon-cyan)] uppercase tracking-widest font-black">Métricas AU · Desempeño Logístico</p>
                        <p class="text-[10px] text-[var(--text-secondary)] mt-0.5 uppercase">Costes de propulsión, tripulación y mantenimiento por Unidad Astronómica.</p>
                    </div>

                    @forelse($operationalRates as $shipRates)
                        <div class="tech-card overflow-hidden">
                            <div class="px-5 py-3 border-b border-[var(--border-glass)] flex items-center justify-between bg-[var(--tech-input-bg)]">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-[var(--neon-cyan)] animate-pulse"></div>
                                    <span class="text-[11px] font-black text-[var(--text-primary)] uppercase tracking-widest">{{ $shipRates['label'] }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-[var(--border-glass)]">
                                @foreach($shipRates['items'] as $rate)
                                    <div class="bg-[var(--bg-obsidian)] p-5 group hover:bg-[var(--tech-hover-bg)] transition-colors relative">
                                        <p class="text-[10px] text-[var(--text-secondary)] font-bold uppercase tracking-widest mb-1">{{ $rate['label'] }}</p>
                                        <p class="text-[9px] text-[var(--text-secondary)] mb-4 h-6 uppercase tracking-tighter opacity-60 leading-tight">{{ $rate['desc'] }}</p>
                                        
                                        <div class="flex items-end justify-between">
                                            <div>
                                                <p class="text-[var(--neon-cyan)] font-mono font-black text-lg">
                                                    {{ $rate['unit'] === '€/h' || $rate['unit'] === '€/AU' ? '€' : '' }}{{ number_format($rate['price'], 2, ',', '.') }}<span class="text-[10px] text-[var(--text-secondary)] font-normal ml-0.5 lowercase">{{ str_replace('€', '', $rate['unit']) }}</span>
                                                </p>
                                            </div>
                                            <button type="button"
                                                wire:click="openUpdateModal('{{ $rate['type'] }}', {{ $shipRates['starship_id'] }}, '{{ addslashes($shipRates['label']) }} - {{ addslashes($rate['label']) }}', {{ $rate['price'] }}, '{{ $rate['unit'] }}')"
                                                class="opacity-0 group-hover:opacity-100 p-2.5 rounded-lg border border-[var(--neon-violet)]/30 text-[var(--neon-violet)] hover:bg-[var(--neon-violet)] hover:text-black transition-colors" title="Actualizar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-20 text-center tech-card opacity-60">
                            <p class="text-[var(--text-secondary)] text-[10px] uppercase tracking-widest">Sin naves operativas detectadas</p>
                        </div>
                    @endforelse
                </div>
            @endif

        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- RIGHT: PRICE LOG (col-span-1) --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="xl:col-span-2 space-y-4 lg:sticky lg:top-8 self-start">
            <div class="tech-card overflow-hidden">
                <div class="px-5 py-3 border-b border-[var(--border-glass)] flex items-center justify-between bg-[var(--tech-input-bg)]">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--neon-violet)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="text-[10px] font-bold text-[var(--neon-violet)] uppercase tracking-[0.2em]">Registro de Auditoría</span>
                    </div>
                    <button wire:click="exportLogs" class="text-[9px] font-black uppercase tracking-widest bg-[var(--neon-violet)] text-black px-3 py-1.5 rounded-[8px] hover:bg-violet-400 transition-all shadow-[0_0_15px_rgba(139,92,246,0.2)]">
                        Exportar CSV
                    </button>
                </div>

                @if($priceLogs->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <p class="text-[var(--text-secondary)] text-[10px] uppercase tracking-widest opacity-60 leading-relaxed">Sin cambios de tarifa registrados.<br>Las actualizaciones aparecerán en tiempo real.</p>
                    </div>
                @else
                    <div class="divide-y divide-[var(--border-glass)] max-h-[500px] overflow-y-auto no-scrollbar">
                        @foreach($priceLogs as $log)
                            <div class="px-5 py-4 hover:bg-[var(--tech-hover-bg)] transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-[5px] border
                                        {{ in_array($log->item_type, ['flight']) ? 'border-[var(--neon-cyan)]/30 text-[var(--neon-cyan)] bg-[var(--neon-cyan)]/10' : '' }}
                                        {{ in_array($log->item_type, ['hotel']) ? 'border-[var(--neon-rose)]/30 text-[var(--neon-rose)] bg-[var(--neon-rose)]/10' : '' }}
                                        {{ in_array($log->item_type, ['terrestrial_flight']) ? 'border-[var(--neon-amber)]/30 text-[var(--neon-amber)] bg-[var(--neon-amber)]/10' : '' }}
                                        {{ in_array($log->item_type, ['vip_transfer_location']) ? 'border-[var(--neon-violet)]/30 text-[var(--neon-violet)] bg-[var(--neon-violet)]/10' : '' }}
                                        {{ in_array($log->item_type, ['training','passport_management','refund_insurance']) ? 'border-[var(--neon-emerald)]/30 text-[var(--neon-emerald)] bg-[var(--neon-emerald)]/10' : '' }}
                                        {{ in_array($log->item_type, ['crew_expense_per_au','hours_per_au','starship_cost_per_au']) ? 'border-[var(--neon-cyan)]/30 text-[var(--neon-cyan)] bg-[var(--neon-cyan)]/10' : '' }}
                                    ">
                                        {{ $log->item_label }}
                                    </span>
                                    <span class="text-[9px] text-[var(--text-secondary)] opacity-50 uppercase font-bold">{{ $log->created_at->diffForHumans() }}</span>
                                </div>

                                <div class="flex items-center gap-3 font-mono text-xs mb-1.5">
                                    <span class="text-[var(--text-secondary)] line-through opacity-50">{{ number_format($log->old_price, 2, ',', '.') }}</span>
                                    <svg class="w-3 h-3 text-[var(--text-secondary)] opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                    <span class="{{ $log->new_price > $log->old_price ? 'text-[var(--neon-rose)]' : 'text-[var(--neon-emerald)]' }} font-black">
                                        {{ number_format($log->new_price, 2, ',', '.') }}
                                    </span>
                                    @php $diff = $log->new_price - $log->old_price; @endphp
                                    <span class="text-[9px] {{ $diff > 0 ? 'text-[var(--neon-rose)]' : 'text-[var(--neon-emerald)]' }} opacity-80">
                                        ({{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 2, ',', '.') }})
                                    </span>
                                </div>

                                @if($log->reason)
                                    <p class="text-[10px] text-[var(--text-secondary)] italic opacity-80 leading-tight">"{{ $log->reason }}"</p>
                                @endif

                                @if($log->admin)
                                    <p class="text-[8px] text-[var(--neon-violet)] mt-2 uppercase font-black tracking-widest opacity-60">Operador: {{ $log->admin->name }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Security Policy --}}
            <div class="tech-card p-5 bg-[var(--neon-violet)]/5 border-[var(--neon-violet)]/20 shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-[var(--neon-violet)]/10 rounded-full blur-3xl group-hover:bg-[var(--neon-violet)]/20 transition-all"></div>
                <div class="flex items-center gap-3 mb-4 relative z-10">
                    <div class="p-2 bg-[var(--neon-violet)]/10 rounded-lg">
                        <svg class="w-5 h-5 text-[var(--neon-violet)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-black text-[var(--neon-violet)] uppercase tracking-[0.2em]">Políticas de Integridad</span>
                </div>
                <p class="text-[10px] text-[var(--text-secondary)] leading-relaxed uppercase tracking-widest opacity-80 mb-4">
                    Las reservas <strong class="text-[var(--text-primary)]">confirmadas</strong> están blindadas ante cambios de tarifa. El precio se congela mediante <code class="bg-[var(--neon-violet)]/20 text-[var(--neon-violet)] px-1.5 py-0.5 rounded font-mono">price_snapshot</code> al momento del pago.
                </p>
                <div class="space-y-2 border-t border-[var(--border-glass)] pt-4">
                    <div class="flex items-center justify-between text-[9px] uppercase tracking-widest font-bold">
                        <span class="text-[var(--text-secondary)]">Retroactividad</span>
                        <span class="text-[var(--neon-rose)]">Bloqueada ✗</span>
                    </div>
                    <div class="flex items-center justify-between text-[9px] uppercase tracking-widest font-bold">
                        <span class="text-[var(--text-secondary)]">Inmutabilidad de Logs</span>
                        <span class="text-[var(--neon-emerald)]">Activa ✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: ACTUALIZAR TARIFA --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showUpdateModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/20 dark:bg-black/40 backdrop-blur-sm p-4" x-data x-on:keydown.escape.window="$wire.set('showUpdateModal', false)">
            <div class="border border-black/10 dark:border-white/10 rounded-[15px] max-w-sm w-full overflow-hidden shadow-2xl backdrop-blur-xl bg-white/80 dark:bg-zinc-950/60"
                @click.away="$wire.set('showUpdateModal', false)">
                <div class="p-6 border-b border-black/5 dark:border-white/5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-[var(--neon-violet)]/10 border border-[var(--neon-violet)]/30 text-[var(--neon-violet)] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-widest mb-1">Ajuste de Parámetros</h3>
                        <p class="text-zinc-600 dark:text-zinc-300 text-xs leading-relaxed">
                            Actualizar tarifa de <strong class="text-[var(--neon-violet)]">{{ $itemName }}</strong>. Este registro es inmutable.
                        </p>
                    </div>
                </div>
                
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-black/5 dark:bg-white/5 rounded-xl px-4 py-3 text-center">
                            <p class="text-[8px] text-[var(--text-secondary)] uppercase tracking-[0.2em] mb-1">Valor Actual</p>
                            <p class="text-[var(--text-secondary)] font-mono font-bold text-lg line-through opacity-50">{{ $unit === '%' ? '' : $unit }}{{ number_format($currentPrice, 2, ',', '.') }}{{ $unit === '%' ? '%' : '' }}</p>
                        </div>
                        <div class="bg-[var(--neon-violet)]/5 border border-[var(--neon-violet)]/20 rounded-xl px-4 py-3 text-center">
                            <p class="text-[8px] text-[var(--neon-violet)] uppercase tracking-[0.2em] mb-1">Nueva Tasa</p>
                            <p class="text-[var(--neon-violet)] font-mono font-black text-lg">
                                @if($newPrice && is_numeric($newPrice))
                                    {{ $unit === '%' ? '' : $unit }}{{ number_format((float)$newPrice, 2, ',', '.') }}{{ $unit === '%' ? '%' : '' }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em]">Magnitud de Cambio ({{ $unit }})</label>
                        <input type="number" wire:model.live="newPrice" step="0.01" min="0"
                            class="tech-input w-full px-4 py-3 text-sm font-mono focus:outline-none transition-all rounded-xl"
                            placeholder="0.00">
                        @error('newPrice') <span class="text-[var(--neon-rose)] text-[10px] font-bold uppercase tracking-widest mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em]">
                            Justificación Técnica
                            <span class="text-[var(--neon-rose)] ml-1">*</span>
                        </label>
                        <input type="text" wire:model.live="updateReason"
                            class="tech-input w-full px-4 py-3 text-xs focus:outline-none transition-all rounded-xl"
                            placeholder="Ej. Ajuste por inflación combustible...">
                        @error('updateReason') <span class="text-[var(--neon-rose)] text-[10px] font-bold uppercase tracking-widest mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex p-3 gap-3 bg-zinc-100/50 dark:bg-black/30 border-t border-black/5 dark:border-white/5">
                    <button type="button" wire:click="$set('showUpdateModal', false)"
                        class="flex-1 py-2.5 px-4 text-xs font-bold uppercase rounded-[10px] border border-black/10 dark:border-white/10 text-zinc-700 dark:text-zinc-300 hover:bg-black/5 dark:hover:bg-white/5 hover:text-black dark:hover:text-white transition-colors backdrop-blur-md">
                        Abortar
                    </button>
                    <button type="button" wire:click="applyUpdate" wire:loading.attr="disabled"
                        class="flex-1 py-2.5 px-4 text-xs font-bold uppercase text-black bg-[var(--neon-violet)] rounded-[10px] shadow-lg transition-colors border border-[var(--neon-violet)]/50 disabled:opacity-50">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif


    <!-- Botón Subir Mobile -->
    <button x-show="showScrollTop" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="md:hidden fixed bottom-6 right-6 z-[90] w-12 h-12 rounded-full bg-[var(--neon-violet)] text-black flex items-center justify-center shadow-[0_0_20px_rgba(139,92,246,0.5)] border border-[var(--neon-violet)]/50 transition-transform active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
    </div>
</div>
