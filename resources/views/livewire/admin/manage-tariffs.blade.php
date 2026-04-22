<div class="min-h-screen bg-[#020202] text-white pb-20" x-data>

    <div class="relative border-b border-zinc-900 bg-gradient-to-b from-emerald-950/20 to-transparent px-8 py-8">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-3xl font-bold text-emerald-400 tracking-tight uppercase flex items-center gap-3">
                    Tarifas
                </h2>
                <p class="text-zinc-400 text-sm mt-1 uppercase tracking-widest">
                    Gestión de tarifas globales
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative group">
                    <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-4 w-4 text-zinc-600 group-focus-within:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Buscar servicio..."
                        class="pl-10 pr-4 py-2 bg-zinc-900/70 border border-zinc-800 focus:border-emerald-700 focus:outline-none text-white text-sm rounded-[10px] w-64 transition-colors">
                </div>
            </div>
        </div>

        {{-- Flash --}}
        @if(session()->has('tariff_message'))
            <div class="mt-4 px-4 py-2.5 bg-emerald-950/60 border border-emerald-800/50 rounded-[10px] flex items-center gap-2 shadow-[0_0_20px_rgba(16,185,129,0.1)]">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-emerald-300 text-sm">{{ session('tariff_message') }}</span>
            </div>
        @endif
    </div>

    <div class="px-8 py-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">

            {{-- Tab Pills --}}
            <div class="flex flex-wrap gap-2 p-1 bg-zinc-900/60 border border-zinc-800 rounded-[12px] w-fit">
                @foreach([
                    'flights'      => ['label' => 'Vuelos Espaciales', 'count' => count($flights)],
                    'hotels'       => ['label' => 'Hoteles',     'count' => count($hotels)],
                    'terrestrial'  => ['label' => 'Vuelos Terrestres', 'count' => count($terrestrialFlights)],
                    'transfers'    => ['label' => 'Traslados',         'count' => count($locations)],
                    'services'     => ['label' => 'Servicios Fijos',   'count' => count($globalServices)],
                    'operational'  => ['label' => 'Operativo AU',      'count' => count($operationalRates) + count($starships)],
                ] as $tab => $meta)
                    <button wire:click="$set('activeTab', '{{ $tab }}')"
                        class="px-4 py-2 text-xs font-bold uppercase tracking-widest rounded-[8px] transition-all flex items-center gap-2
                            {{ $activeTab === $tab
                                ? 'bg-emerald-700 text-white shadow-[0_0_15px_rgba(16,185,129,0.3)]'
                                : 'text-zinc-500 hover:text-white hover:bg-zinc-800/60' }}">
                        {{ $meta['label'] }}
                        <span class="text-[9px] {{ $activeTab === $tab ? 'bg-emerald-900 text-emerald-200' : 'bg-zinc-800 text-zinc-600' }} px-1.5 py-0.5 rounded-full font-mono">
                            {{ $meta['count'] }}
                        </span>
                    </button>
                @endforeach
            </div>

            {{-- ── VUELOS ESPACIALES ── --}}
            @if($activeTab === 'flights')
                <div class="bg-zinc-900/40 border border-zinc-800/60 rounded-[14px] overflow-hidden">
                    <div class="px-5 py-3 border-b border-zinc-800/60 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em]">Vuelos Espaciales · Tasa (Precio Base/Asiento)</span>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-800/40">
                                <th class="px-5 py-3 text-left text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Vuelo / Destino</th>
                                <th class="px-5 py-3 text-left text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Salida</th>
                                <th class="px-5 py-3 text-right text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Nova</th>
                                <th class="px-5 py-3 text-right text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Supernova ×2.5</th>
                                <th class="px-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/30">
                            @forelse($flights as $flight)
                                <tr class="hover:bg-emerald-950/10 transition-colors group">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-cyan-400 font-mono text-xs">#{{ $flight->flight_code }}</div>
                                        <div class="text-zinc-400 text-xs mt-0.5">→ {{ $flight->destination?->name ?? '—' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-zinc-400 text-xs">
                                        {{ $flight->departure_date?->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-white font-bold font-mono">€{{ number_format($flight->base_price, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-emerald-400/70 text-xs font-mono">
                                        €{{ number_format($flight->base_price * 2.5, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3.5 text-right">
                                        <button
                                            wire:click="openUpdateModal('flight', {{ $flight->id }}, '#{{ $flight->flight_code }} → {{ $flight->destination?->name }}', {{ $flight->base_price }}, '€')"
                                            class="opacity-0 group-hover:opacity-100 transition-all px-3 py-1.5 text-[9px] font-black uppercase tracking-widest border border-emerald-700/60 text-emerald-400 hover:bg-emerald-700 hover:text-white rounded-[6px] whitespace-nowrap">
                                            Actualizar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-zinc-600 text-xs uppercase tracking-widest">Sin vuelos próximos en el sistema</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- ── HOTELES ── --}}
            @if($activeTab === 'hotels')
                <div class="bg-zinc-900/40 border border-zinc-800/60 rounded-[14px] overflow-hidden">
                    <div class="px-5 py-3 border-b border-zinc-800/60 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-pink-500 animate-pulse"></div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em]">Pre-Launch Manors · Precio por Noche</span>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-800/40">
                                <th class="px-5 py-3 text-left text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Hotel / Ubicación</th>
                                <th class="px-5 py-3 text-left text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Categoría</th>
                                <th class="px-5 py-3 text-right text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Precio/Noche</th>
                                <th class="px-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/30">
                            @forelse($hotels as $hotel)
                                <tr class="hover:bg-emerald-950/10 transition-colors group">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-pink-300 text-sm">{{ $hotel->name }}</div>
                                        <div class="text-zinc-500 text-xs mt-0.5">{{ $hotel->location?->name ?? '—' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex gap-0.5">
                                            @for($i = 0; $i < 5; $i++)
                                                <span class="{{ $i < $hotel->galactic_stars ? 'text-amber-400' : 'text-zinc-700' }} text-xs">★</span>
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-white font-bold font-mono">€{{ number_format($hotel->price_per_night, 0, ',', '.') }}</span>
                                        <div class="text-[10px] text-zinc-500">/ noche</div>
                                    </td>
                                    <td class="px-3 py-3.5 text-right">
                                        <button
                                            wire:click="openUpdateModal('hotel', {{ $hotel->id }}, '{{ addslashes($hotel->name) }}', {{ $hotel->price_per_night }}, '€')"
                                            class="opacity-0 group-hover:opacity-100 transition-all px-3 py-1.5 text-[9px] font-black uppercase tracking-widest border border-emerald-700/60 text-emerald-400 hover:bg-emerald-700 hover:text-white rounded-[6px] whitespace-nowrap">
                                            Actualizar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-zinc-600 text-xs uppercase tracking-widest">Sin hoteles registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- ── VUELOS TERRESTRES ── --}}
            @if($activeTab === 'terrestrial')
                <div class="bg-zinc-900/40 border border-zinc-800/60 rounded-[14px] overflow-hidden">
                    <div class="px-5 py-3 border-b border-zinc-800/60 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em]">Vuelos de Aproximación · Precio por Pasajero</span>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-800/40">
                                <th class="px-5 py-3 text-left text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Ruta</th>
                                <th class="px-5 py-3 text-left text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Aerolínea · Salida</th>
                                <th class="px-5 py-3 text-right text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Precio</th>
                                <th class="px-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/30">
                            @forelse($terrestrialFlights as $tf)
                                <tr class="hover:bg-emerald-950/10 transition-colors group">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-amber-300 font-mono text-xs">
                                            #{{ $tf->flight_number }} <span class="text-zinc-500 ml-1 truncate">({{ $tf->originLocation?->name ?? '?' }} → {{ $tf->destinationLocation?->name ?? '?' }})</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="text-zinc-300 text-sm">{{ $tf->airline }}</div>
                                        <div class="text-zinc-500 text-xs mt-0.5">
                                            {{ \Carbon\Carbon::parse($tf->departure_datetime)->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-white font-bold font-mono">€{{ number_format($tf->price, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-3 py-3.5 text-right">
                                        <button
                                            wire:click="openUpdateModal('terrestrial_flight', {{ $tf->id }}, '{{ addslashes($tf->originLocation?->name) }} → {{ addslashes($tf->destinationLocation?->name) }}', {{ $tf->price }}, '€')"
                                            class="opacity-0 group-hover:opacity-100 transition-all px-3 py-1.5 text-[9px] font-black uppercase tracking-widest border border-emerald-700/60 text-emerald-400 hover:bg-emerald-700 hover:text-white rounded-[6px] whitespace-nowrap">
                                            Actualizar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-zinc-600 text-xs uppercase tracking-widest">Sin vuelos terrestres próximos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- ── TRASLADOS VIP POR LOCALIZACIÓN ── --}}
            @if($activeTab === 'transfers')
                <div class="bg-zinc-900/40 border border-zinc-800/60 rounded-[14px] overflow-hidden">
                    <div class="px-5 py-3 border-b border-zinc-800/60 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-violet-500 animate-pulse"></div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em]">Transfer VIP a Spaceport · Precio por Ubicación</span>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-800/40">
                                <th class="px-5 py-3 text-left text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Ubicación / Código</th>
                                <th class="px-5 py-3 text-right text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Precio Traslado</th>
                                <th class="px-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/30">
                            @forelse($locations as $loc)
                                <tr class="hover:bg-emerald-950/10 transition-colors group">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-violet-300 text-sm">{{ $loc->name }}</div>
                                        <div class="text-zinc-500 text-xs mt-0.5 font-mono">{{ $loc->code }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="text-white font-bold font-mono">€{{ number_format($loc->transport_price ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-3 py-3.5 text-right">
                                        <button
                                            wire:click="openUpdateModal('vip_transfer_location', {{ $loc->id }}, '{{ addslashes($loc->name) }}', {{ $loc->transport_price ?? 0 }}, '€')"
                                            class="opacity-0 group-hover:opacity-100 transition-all px-3 py-1.5 text-[9px] font-black uppercase tracking-widest border border-emerald-700/60 text-emerald-400 hover:bg-emerald-700 hover:text-white rounded-[6px] whitespace-nowrap">
                                            Actualizar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-zinc-600 text-xs uppercase tracking-widest">Sin ubicaciones registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- ── SERVICIOS FIJOS ── --}}
            @if($activeTab === 'services')
                <div class="space-y-3">
                    <div class="px-1">
                        <p class="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Servicios adicionales · Tasas globales del sistema</p>
                        <p class="text-[10px] text-zinc-600 mt-0.5">Estos precios aplican a todas las reservas futuras. Los precios históricos quedan protegidos.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($globalServices as $service)
                            <div class="group bg-zinc-900/40 border border-zinc-800/60 hover:border-emerald-800/60 rounded-[14px] p-5 transition-colors relative overflow-hidden">
                                <div class="absolute inset-0 bg-emerald-500/3 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="flex items-start justify-between mb-3 relative z-10">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <p class="text-white font-bold text-sm">{{ $service['label'] }}</p>
                                            <p class="text-zinc-500 text-[10px] mt-0.5">{{ $service['desc'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between relative z-10">
                                    <div>
                                        <p class="text-[9px] text-zinc-600 uppercase tracking-widest">Tarifa Vigente</p>
                                        <p class="text-emerald-400 font-mono font-black text-xl">
                                            {{ $service['unit'] === '%' ? '' : $service['unit'] }}{{ number_format($service['price'], 2, ',', '.') }}{{ $service['unit'] === '%' ? '%' : '' }}
                                        </p>
                                    </div>
                                    <button
                                        wire:click="openUpdateModal('{{ $service['type'] }}', 0, '{{ $service['label'] }}', {{ $service['price'] }}, '{{ $service['unit'] }}')"
                                        class="px-4 py-2 text-[9px] font-black uppercase tracking-widest border border-emerald-700/60 text-emerald-400 hover:bg-emerald-700 hover:text-white rounded-[8px] transition-all whitespace-nowrap">
                                        Actualizar Tarifa
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
                    <div class="px-1">
                        <p class="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Costes y Desempeño Operativo por Unidad Astronómica (AU)</p>
                        <p class="text-[10px] text-zinc-600 mt-0.5">Define los costes de combustible, horas estimadas de vuelo y gastos de tripulación específicos de cada nave.</p>
                    </div>

                    @forelse($operationalRates as $shipRates)
                        <div class="bg-zinc-900/40 border border-teal-900/40 rounded-[14px] overflow-hidden shadow-[0_0_20px_rgba(20,184,166,0.05)]">
                            <div class="px-4 py-3 border-b border-teal-900/30 flex items-center justify-between bg-teal-950/20">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></div>
                                    <span class="text-sm font-bold text-white uppercase tracking-widest">{{ $shipRates['label'] }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-zinc-800/50">
                                @foreach($shipRates['items'] as $rate)
                                    <div class="bg-[#020202] p-5 group hover:bg-teal-950/20 transition-colors relative">
                                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">{{ $rate['label'] }}</p>
                                        <p class="text-[9px] text-zinc-600 mb-4 h-6">{{ $rate['desc'] }}</p>
                                        
                                        <div class="flex items-end justify-between">
                                            <div>
                                                <p class="text-teal-400 font-mono font-black text-lg">
                                                    {{ $rate['unit'] === '€/h' || $rate['unit'] === '€/AU' ? '€' : '' }}{{ number_format($rate['price'], 2, ',', '.') }}<span class="text-[10px] text-teal-600 font-normal ml-0.5">{{ str_replace('€', '', $rate['unit']) }}</span>
                                                </p>
                                            </div>
                                            <button
                                                wire:click="openUpdateModal('{{ $rate['type'] }}', {{ $shipRates['starship_id'] }}, '{{ addslashes($shipRates['label']) }} - {{ addslashes($rate['label']) }}', {{ $rate['price'] }}, '{{ $rate['unit'] }}')"
                                                class="opacity-0 group-hover:opacity-100 px-3 py-1.5 text-[9px] font-black uppercase tracking-widest border border-teal-700/60 text-teal-400 hover:bg-teal-700 hover:text-white rounded-[6px] transition-all whitespace-nowrap">
                                                Actualizar
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center bg-zinc-900/40 rounded-[14px]">
                            <p class="text-zinc-500 text-xs uppercase tracking-widest">No hay naves operativas</p>
                        </div>
                    @endforelse
                </div>
            @endif

        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- RIGHT: PRICE LOG (col-span-1) --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="space-y-4">
            <div class="bg-zinc-900/40 border border-emerald-900/30 rounded-[14px] overflow-hidden shadow-[0_0_30px_rgba(16,185,129,0.05)]">
                <div class="px-5 py-3 border-b border-emerald-900/30 flex items-center gap-2 bg-emerald-950/20">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-[0.2em]">Log de Auditoría</span>
                    
                    <div class="ml-auto flex items-center gap-3">
                        <button wire:click="exportLogs" class="text-[9px] border border-purple-800/60 bg-purple-950/30 text-purple-400 hover:bg-purple-800/80 hover:text-white px-2 py-1 rounded transition-colors uppercase tracking-widest whitespace-nowrap">
                            Descargar CSV
                        </button>
                        <span class="text-[9px] text-zinc-600 uppercase hidden sm:inline">Inmutable · Últimos 40</span>
                    </div>
                </div>

                @if($priceLogs->isEmpty())
                    <div class="px-5 py-8 text-center">
                        <p class="text-zinc-600 text-xs uppercase tracking-widest">Sin cambios de tarifa registrados</p>
                        <p class="text-zinc-700 text-[10px] mt-1">Los cambios aparecerán aquí en tiempo real.</p>
                    </div>
                @else
                    <div class="divide-y divide-zinc-800/30 max-h-[75vh] overflow-y-auto">
                        @foreach($priceLogs as $log)
                            <div class="px-4 py-3 hover:bg-emerald-950/10 transition-colors">
                                {{-- Header: type badge + timestamp --}}
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full border
                                        {{ in_array($log->item_type, ['flight']) ? 'border-cyan-900/60 text-cyan-500 bg-cyan-950/30' : '' }}
                                        {{ in_array($log->item_type, ['hotel']) ? 'border-pink-900/60 text-pink-500 bg-pink-950/30' : '' }}
                                        {{ in_array($log->item_type, ['terrestrial_flight']) ? 'border-amber-900/60 text-amber-500 bg-amber-950/30' : '' }}
                                        {{ in_array($log->item_type, ['vip_transfer_location']) ? 'border-violet-900/60 text-violet-500 bg-violet-950/30' : '' }}
                                        {{ in_array($log->item_type, ['training','passport_management','refund_insurance']) ? 'border-emerald-900/60 text-emerald-500 bg-emerald-950/30' : '' }}
                                        {{ in_array($log->item_type, ['crew_expense_per_au','hours_per_au','starship_cost_per_au']) ? 'border-teal-900/60 text-teal-500 bg-teal-950/30' : '' }}
                                    ">
                                        {{ $log->item_label }}
                                    </span>
                                    <span class="text-[9px] text-zinc-600">{{ $log->created_at->diffForHumans() }}</span>
                                </div>

                                {{-- Price change --}}
                                <div class="flex items-center gap-2 font-mono text-xs mb-1">
                                    <span class="text-zinc-500 line-through">{{ number_format($log->old_price, 2, ',', '.') }}</span>
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span class="{{ $log->new_price > $log->old_price ? 'text-red-400' : 'text-emerald-400' }} font-bold">
                                        {{ number_format($log->new_price, 2, ',', '.') }}
                                    </span>
                                    @php $diff = $log->new_price - $log->old_price; @endphp
                                    <span class="text-[9px] {{ $diff > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                        ({{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 2, ',', '.') }})
                                    </span>
                                </div>

                                {{-- Reason --}}
                                @if($log->reason)
                                    <p class="text-[10px] text-zinc-500 italic truncate" title="{{ $log->reason }}">
                                        "{{ $log->reason }}"
                                    </p>
                                @endif

                                {{-- Admin --}}
                                @if($log->admin)
                                    <p class="text-[9px] text-emerald-900 mt-0.5 uppercase tracking-wider">por {{ $log->admin->name }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Price Snapshot Info --}}
            <div class="bg-emerald-950/20 border border-emerald-900/30 rounded-[14px] p-4 shadow-[0_0_20px_rgba(16,185,129,0.05)]">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Price Snapshot Activo</span>
                </div>
                <p class="text-[10px] text-zinc-500 leading-relaxed">
                    Las reservas <strong class="text-zinc-300">confirmadas y pagadas</strong> quedan inmunes a cambios de tarifa. El precio vigente al momento del pago se congela en el <code class="text-emerald-400 bg-emerald-950/40 px-1 rounded">price_snapshot</code> de la reserva.
                </p>
                <div class="mt-3 pt-3 border-t border-emerald-900/20 space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] text-zinc-600 uppercase">Protección desde</span>
                        <span class="text-[9px] text-emerald-500 font-mono">CONFIRMACIÓN DE PAGO</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] text-zinc-600 uppercase">Retroactividad</span>
                        <span class="text-[9px] text-red-500 font-mono">BLOQUEADA ✗</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: ACTUALIZAR TARIFA --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showUpdateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-on:keydown.escape.window="$wire.set('showUpdateModal', false)">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" wire:click="$set('showUpdateModal', false)"></div>

            {{-- Panel --}}
            <div class="relative z-10 w-full max-w-md bg-[#0a0a0a] border border-emerald-900/40 rounded-[16px] shadow-[0_0_60px_rgba(16,185,129,0.15)] overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-4 border-b border-emerald-900/30 flex items-center justify-between bg-emerald-950/20">
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-emerald-500">Cambio de Tarifas</p>
                        <h2 class="text-base font-black text-white uppercase tracking-widest mt-0.5">Actualizar Tarifa</h2>
                    </div>
                    <button wire:click="$set('showUpdateModal', false)" class="text-zinc-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    {{-- Item name --}}
                    <div class="bg-emerald-950/20 border border-emerald-900/30 rounded-[10px] px-4 py-3">
                        <p class="text-[9px] text-zinc-500 uppercase tracking-widest mb-1">Servicio / Activo</p>
                        <p class="text-white font-bold text-sm">{{ $itemName }}</p>
                    </div>

                    {{-- Price comparison --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-zinc-900/50 border border-zinc-800 rounded-[10px] px-4 py-3 text-center">
                            <p class="text-[9px] text-zinc-500 uppercase tracking-widest mb-1">Precio Actual</p>
                            <p class="text-zinc-400 font-mono font-bold text-lg line-through">{{ $unit === '%' ? '' : $unit }}{{ number_format($currentPrice, 2, ',', '.') }}{{ $unit === '%' ? '%' : '' }}</p>
                        </div>
                        <div class="bg-emerald-950/20 border border-emerald-900/40 rounded-[10px] px-4 py-3 text-center">
                            <p class="text-[9px] text-zinc-500 uppercase tracking-widest mb-1">Nuevo Valor</p>
                            <p class="text-emerald-300 font-mono font-bold text-lg">
                                @if($newPrice && is_numeric($newPrice))
                                    {{ $unit === '%' ? '' : $unit }}{{ number_format((float)$newPrice, 2, ',', '.') }}{{ $unit === '%' ? '%' : '' }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- New price input --}}
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">Nuevo Valor ({{ $unit }})</label>
                        <input type="number" wire:model.live="newPrice" step="0.01" min="0"
                            class="w-full bg-[#050505] border {{ $errors->has('newPrice') ? 'border-red-700' : 'border-zinc-700/50' }} focus:border-emerald-500 focus:outline-none text-white px-3 py-2 text-sm rounded-[10px] font-mono"
                            placeholder="0.00">
                        @error('newPrice') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Reason input --}}
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">
                            Motivo del Cambio
                            <span class="text-red-600 ml-1">*</span>
                        </label>
                        <input type="text" wire:model.live="updateReason"
                            class="w-full bg-[#050505] border {{ $errors->has('updateReason') ? 'border-red-700' : 'border-zinc-700/50' }} focus:border-emerald-500 focus:outline-none text-white px-3 py-2 text-sm rounded-[10px]"
                            >
                        @error('updateReason') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        <p class="text-[9px] text-zinc-700 mt-1 uppercase tracking-widest">Quedará registrado en el log de auditoría inmutable.</p>
                    </div>

                    {{-- Price protection warning --}}
                    <div class="flex items-start gap-2 bg-emerald-950/10 border border-emerald-900/20 rounded-[8px] px-3 py-2">
                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p class="text-[9px] text-emerald-700 leading-relaxed">
                            <strong class="text-emerald-600">Price Snapshot activo.</strong> Las reservas ya pagadas no se ven afectadas por este cambio.
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-zinc-800/60 flex gap-3">
                    <button wire:click="$set('showUpdateModal', false)"
                        class="flex-1 py-2 text-xs font-bold uppercase tracking-widest border border-zinc-700/50 text-zinc-400 hover:text-white hover:border-zinc-500 rounded-[10px] transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="applyUpdate" wire:loading.attr="disabled"
                        class="flex-1 py-2 text-xs font-black uppercase tracking-widest bg-emerald-700 hover:bg-emerald-600 text-white rounded-[10px] transition-colors shadow-[0_0_20px_rgba(16,185,129,0.3)] disabled:opacity-50">
                        <span wire:loading.remove wire:target="applyUpdate">✦ Confirmar Cambio</span>
                        <span wire:loading wire:target="applyUpdate">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
