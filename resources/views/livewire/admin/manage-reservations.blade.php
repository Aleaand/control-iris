<div class="min-h-screen bg-gradient-to-b from-[#050505] to-[#19191c] text-zinc-300 p-4 md:p-8"
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <div class="max-w-[1400px] mx-auto space-y-6">

        <!-- Header & Flash Message -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-purple-600 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-purple-600 tracking-tight uppercase flex items-center gap-3">
                    Reservas y Logística
                </h2>
                <p class="text-zinc-400 text-sm mt-1 uppercase tracking-widest">
                    Gestión de reservas espaciales y terrestres
                </p>
            </div>

            @if (session()->has('message'))
                <div
                    class="mt-4 md:mt-0 bg-purple-900/40 border border-purple-700/50 text-purple-400 px-4 py-2 text-sm font-medium uppercase tracking-wider rounded-[10px] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div
                    class="mt-4 md:mt-0 bg-red-900/40 border border-red-700/50 text-red-400 px-4 py-2 text-sm font-medium uppercase tracking-wider rounded-[10px] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Columna Izquierda: Lista y Filtros -->
            <div class="lg:col-span-8 flex flex-col space-y-4">

                <div
                    class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md p-4 flex flex-col sm:flex-row gap-4 justify-between items-center rounded-[10px] shadow-lg">
                    <div class="relative w-full sm:w-2/3">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live="search"
                            placeholder="Buscar por Nombre, Email o Localizador..."
                            class="block w-full pl-10 bg-[#050505] border border-zinc-700/50 text-white placeholder-zinc-600 py-2 focus:outline-none focus:border-purple-500 sm:text-sm transition-colors rounded-[10px]">
                    </div>

                    <div class="w-full sm:w-1/3 flex justify-end">
                        <button wire:click="toggleSort"
                            class="bg-zinc-800/80 hover:bg-zinc-700 border border-zinc-700/50 text-white px-4 py-2 sm:text-sm font-medium flex items-center gap-2 transition-colors w-full sm:w-auto justify-center rounded-[10px] tracking-widest uppercase text-xs">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($sortDir === 'asc')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"></path>
                                @endif
                            </svg>
                            Orden: {{ $sortDir === 'asc' ? 'A-Z' : 'Z-A' }}
                        </button>
                    </div>
                </div>

                <div
                    class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md rounded-[10px] shadow-lg overflow-hidden">
                    <ul class="divide-y divide-zinc-800/80">
                        @forelse($reservations as $res)
                            @php
                                // Contar miembros reales del grupo (excluye adendas)
                                $groupCount = \App\Models\Reservation::where('booking_group_id', $res->booking_group_id)
                                    ->where('is_adenda', false)
                                    ->whereNotIn('status', ['Cancelada', 'Cancelled'])
                                    ->count();
                                $adendaCount = \App\Models\Reservation::where('booking_group_id', $res->booking_group_id)
                                    ->where('is_adenda', true)
                                    ->count();
                                $isGroupReservation = $groupCount > 1;
                                $hasHotelAlert = str_contains($res->discount_note ?? '', 'ACCIÓN REQUERIDA');
                            @endphp
                            <li
                                class="p-4 hover:bg-zinc-800/50 transition-colors flex flex-col sm:flex-row justify-between sm:items-center gap-4 group">
                                <div class="flex-1">
                                    {{-- Alerta de hotel huérfano --}}
                                    @if($hasHotelAlert)
                                        <div
                                            class="mb-2 bg-amber-950/30 border border-amber-700/50 text-amber-400 px-3 py-1.5 text-[9px] font-bold uppercase tracking-widest rounded-[5px] flex items-center gap-2">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z" />
                                            </svg>
                                            Acción Requerida: Reasignar coste de habitación compartida
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-3 mb-2">
                                        <span
                                            class="text-xs font-mono text-purple-400 bg-purple-950/30 px-2 py-0.5 rounded-[5px] border border-purple-900/50 tooltip"
                                            title="{{ $res->id_locator }}">
                                            LOC: {{ substr($res->id_locator, 0, 8) }}...
                                        </span>
                                        @if($isGroupReservation)
                                            <span
                                                class="text-[9px] font-bold bg-violet-900/40 text-violet-300 border border-violet-700/50 px-2 py-0.5 rounded-full flex items-center gap-1">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $groupCount }}
                                                Pasajeros{{ $adendaCount > 0 ? ' · +' . $adendaCount . ' upgrade' : '' }}
                                            </span>
                                        @endif
                                        <h4
                                            class="text-lg font-bold text-white uppercase tracking-wide flex flex-col gap-0.5">
                                            <span
                                                class="text-zinc-400 text-[10px] uppercase tracking-[0.2em] font-medium italic mb-0.5">Titular
                                                de Cuenta</span>
                                            {{ $res->user?->name ?? 'DESCONOCIDO' }}

                                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1">
                                                <span
                                                    class="text-[9px] text-violet-400 font-bold uppercase tracking-widest border-r border-zinc-800 pr-2">Pasajeros:</span>
                                                @foreach($res->group as $member)
                                                    <span class="text-[10px] text-zinc-300 font-normal italic">
                                                        {{ $member->passenger?->full_name }}{{ !$loop->last ? ',' : '' }}
                                                    </span>
                                                @endforeach
                                            </div>

                                        </h4>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-xs font-mono text-zinc-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-cyan-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            VUELO: #{{ $res->spaceFlight?->flight_code ?? 'N/A' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-zinc-500">
                                            ({{ $res->spaceFlight?->departure_date?->format('d/m/Y') ?? '??/??/????' }} →
                                            {{ $res->spaceFlight?->destination?->name ?? 'Destino Desconocido' }})
                                        </span>
                                    </div>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <!-- User Validation Warnings -->
                                        @php
                                            $phys = $res->user?->physical_fitness ?? 'No apto';
                                            $ppt = $res->user?->passports?->first();
                                            $pptValid = $ppt && !$ppt->isExpiredForFlight();

                                            $ready = ($phys === 'Excelente') && $pptValid;
                                        @endphp

                                        @if($ready)
                                            <div
                                                class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] text-emerald-400 bg-emerald-950/30 border-emerald-900/50">
                                                PASAJERO
                                            </div>
                                        @else
                                            <div
                                                class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] text-red-500 bg-red-950/30 border-red-900/50">
                                                REQUIERE Training/PASAPORTE
                                            </div>
                                        @endif

                                        @if($res->logistics)
                                            <div class="w-full mt-1.5 flex flex-wrap gap-2">
                                                @if($res->status === 'Confirmada')
                                                    <span
                                                        class="text-[9px] font-bold text-white bg-green-500/20 border border-green-500/50 px-1.5 py-0.5 rounded">CONFIRMADA</span>
                                                @else
                                                    <span
                                                        class="text-[9px] font-bold text-white bg-zinc-500/20 border border-zinc-500/50 px-1.5 py-0.5 rounded uppercase">{{ $res->status }}</span>
                                                @endif
                                                <span
                                                    class="text-[9px] font-bold text-zinc-400 bg-black border border-zinc-800 px-1.5 py-0.5 rounded">{{ $res->seat_type }}
                                                    ({{ $res->seat_number ?: 'Sin asignar' }})</span>
                                                <span
                                                    class="text-[9px] font-bold text-purple-400 bg-purple-900/20 border border-purple-500/30 px-1.5 py-0.5 rounded">${{ number_format($res->total_price, 2) }}
                                                    K</span>
                                            </div>
                                            @if($res->logistics->hotel)
                                                <div
                                                    class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] text-zinc-400 bg-zinc-900 border-zinc-700/50">
                                                    {{ $res->logistics->hotel_nights }} Noches Hotel
                                                </div>
                                            @endif
                                            @if($res->logistics->terrestrialFlight)
                                                <div
                                                    class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] text-zinc-400 bg-zinc-900 border-zinc-700/50">
                                                    Vuelo Terrestre
                                                </div>
                                            @endif
                                            @if($res->logistics->training_included)
                                                <div
                                                    class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-2 py-1 border rounded-[5px] text-amber-500 bg-amber-950/30 border-amber-900/50">
                                                    Iris Training
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div
                                    class="flex sm:flex-col gap-2 shrink-0 border-t border-zinc-800/80 sm:border-0 pt-3 sm:pt-0">
                                    {{-- Payment Status Badge --}}
                                    @php
                                        $payStatus = $res->payment_status ?? 'pending';
                                    @endphp
                                    <div class="text-center mb-1">
                                        @if($payStatus === 'paid')
                                            <span
                                                class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-400 bg-emerald-950/30 border border-emerald-900/50 px-2 py-0.5 rounded-full uppercase tracking-widest">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Pagado
                                            </span>
                                        @elseif($payStatus === 'failed')
                                            <span
                                                class="inline-flex items-center gap-1 text-[9px] font-bold text-red-400 bg-red-950/30 border border-red-900/50 px-2 py-0.5 rounded-full uppercase tracking-widest">
                                                Fallido
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 text-[9px] font-bold text-amber-400 bg-amber-950/30 border border-amber-900/50 px-2 py-0.5 rounded-full uppercase tracking-widest">
                                                Pendiente a pago
                                            </span>
                                        @endif
                                    </div>

                                    @if($payStatus !== 'paid')
                                        <button type="button" wire:click="initiatePayment({{ $res->id }})"
                                            wire:loading.attr="disabled"
                                            class="flex-1 sm:flex-none px-3 py-1.5 bg-emerald-900/50 hover:bg-emerald-600 text-emerald-400 hover:text-white text-[10px] font-bold uppercase tracking-wider transition-all border border-emerald-900/50 hover:border-emerald-500 rounded-[10px] flex items-center justify-center gap-1.5 shadow-[0_0_10px_rgba(52,211,153,0.1)] hover:shadow-[0_0_15px_rgba(52,211,153,0.3)]">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                                </path>
                                            </svg>
                                            <span wire:loading.remove wire:target="initiatePayment({{ $res->id }})">Procesar
                                                Pago</span>
                                            <span wire:loading
                                                wire:target="initiatePayment({{ $res->id }})">Conectando...</span>
                                        </button>
                                    @endif

                                    @if($payStatus === 'paid' && $res->stripe_receipt_url)
                                        <a href="{{ $res->stripe_receipt_url }}" target="_blank"
                                            class="flex-1 sm:flex-none px-3 py-1.5 bg-zinc-900 text-zinc-400 hover:text-emerald-400 text-[10px] font-bold uppercase tracking-wider transition-colors border border-zinc-700/50 rounded-[10px] flex items-center justify-center gap-1.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            Ver Factura
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.reservations.ticket', $res) }}" target="_blank"
                                        class="flex-1 sm:flex-none px-3 py-1.5 bg-purple-950/30 text-purple-400 hover:text-purple-400 text-[10px] font-bold uppercase tracking-wider transition-all border border-zinc-800 hover:border-purple-500/50 rounded-[10px] flex items-center justify-center gap-1.5 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                            </path>
                                        </svg>
                                        Ticket de Reserva
                                    </a>

                                    {{-- Botón Upgrade / Adenda --}}
                                    <button type="button" wire:click="prepareAdendaMode({{ $res->id }})"
                                        class="flex-1 sm:flex-none px-3 py-1.5 bg-violet-950/30 hover:bg-violet-900/50 text-violet-400 hover:text-violet-200 text-[10px] font-bold uppercase tracking-wider transition-all border border-violet-900/40 hover:border-violet-600 rounded-[10px] flex items-center justify-center gap-1.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Upgrade
                                    </button>
                                    <button type="button" wire:click="openEditOrModal({{ $res->id }})"
                                        class="flex-1 sm:flex-none px-4 py-1.5 bg-zinc-800 hover:bg-amber-900/50 hover:text-amber-400 text-zinc-300 text-xs font-bold uppercase tracking-wider transition-colors border border-zinc-700/50 hover:border-amber-400 rounded-[10px] flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                        Editar
                                    </button>
                                    @if($isGroupReservation)
                                        <button type="button" wire:click="openEditOrModal({{ $res->id }})"
                                            class="flex-1 sm:flex-none px-4 py-1.5 bg-black/50 hover:bg-red-950/50 text-red-500/80 hover:text-red-400 text-xs font-bold uppercase tracking-wider transition-colors border border-red-900/30 hover:border-red-900/80 rounded-[10px] flex items-center justify-center gap-2">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Eliminar
                                        </button>
                                    @else
                                        <button type="button" wire:click="confirmDelete({{ $res->id }})"
                                            class="flex-1 sm:flex-none px-4 py-1.5 bg-black/50 hover:bg-red-950/50 text-red-500/80 hover:text-red-400 text-xs font-bold uppercase tracking-wider transition-colors border border-red-900/30 hover:border-red-900/80 rounded-[10px] flex items-center justify-center gap-2">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Eliminar
                                        </button>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <div class="p-12 text-center text-zinc-500">
                                <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <p class="font-medium uppercase tracking-widest text-sm">No hay Reservas</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Columna Derecha: Formulario Tabulado -->
            <div class="lg:col-span-4 sticky top-6">
                <div class="border border-zinc-700/50 bg-[#0f0f0f]/80 backdrop-blur-md rounded-[10px] shadow-lg overflow-hidden transition-colors duration-500 {{ $isEditing ? 'border-2 border-amber-500/80 shadow-[0_0_20px_rgba(168,85,247,0.05)]' : 'border-2 border-zinc-500' }}"
                    x-data="{ tab: @entangle('activeTab') }">

                    <div class="p-4 border-b border-zinc-800 flex justify-between items-center bg-black/40">
                        <h3 class="text-sm font-bold uppercase tracking-widest flex items-center gap-2
                            {{ $isAdendaMode ? 'text-violet-400' : ($isEditing ? 'text-amber-400' : 'text-white') }}">
                            @if($isAdendaMode)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Añadir Upgrade / Adenda
                            @elseif($isEditing)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Modo Edición
                            @else
                                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Nueva reserva
                            @endif
                        </h3>
                        <div class="flex items-center gap-2">
                            {{-- Toggle Modo Grupo --}}
                            @if(!$isEditing && !$isAdendaMode)
                                                    <button type="button" wire:click="toggleGroupMode"
                                                        class="text-[10px] uppercase font-bold tracking-widest px-2.5 py-1.5 transition-all border rounded-[5px] flex items-center gap-1.5
                                                                                                                                                                                                                {{ $groupMode
                                ? 'bg-zinc-800/80 border-zinc-700/50 text-zinc-400 hover:border-violet-600 hover:text-violet-400'
                                : 'bg-zinc-800/80 border-zinc-700/50 text-zinc-400 hover:border-violet-600 hover:text-cyan-400' }}">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        {{ $groupMode ? 'Modo Individual' : 'Modo Grupo' }}
                                                    </button>
                            @endif
                            @if($isEditing || $isAdendaMode)
                                <button type="button" wire:click="setCreateMode"
                                    class="text-[10px] uppercase font-bold tracking-widest bg-zinc-800/80 hover:bg-white hover:text-black text-zinc-300 px-2.5 py-1.5 transition-colors border border-zinc-700/50 rounded-[5px] flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Nueva Reserva
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Botones de Navegación Tabs -->
                    <div class="flex bg-black">
                        <button type="button" @click="tab = 'space'"
                            :class="tab === 'space' ? 'bg-[#0f0f0f] border-t-2 border-cyan-500 text-white' : 'bg-black text-zinc-500 hover:bg-zinc-900 hover:text-zinc-300 border-t-2 border-transparent'"
                            class="flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-colors">
                            Logistica Espacial
                        </button>
                        @if(!$groupMode)
                            <button type="button" @click="tab = 'logistic'"
                                :class="tab === 'logistic' ? 'bg-[#0f0f0f] border-t-2 border-amber-500 text-white' : 'bg-black text-zinc-500 hover:bg-zinc-900 hover:text-zinc-300 border-t-2 border-transparent'"
                                class="flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-colors border-l border-zinc-800">
                                Logística Terrestre
                            </button>
                        @endif
                    </div>

                    <form wire:submit.prevent="confirmSave" class="p-6 space-y-4">

                        @if($isAdendaMode)
                            <div class="bg-violet-950/30 border border-violet-500/30 p-3 rounded-[10px] mb-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-violet-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-[10px] font-black text-violet-300 uppercase tracking-widest mb-1">Upgrade / Adenda Detectada</p>
                                        <p class="text-[9px] text-zinc-400 leading-relaxed uppercase font-bold">
                                            Estás añadiendo servicios a una reserva existente. <span class="text-violet-400">Se generará un nuevo pago y una segunda factura</span> por la diferencia o el nuevo servicio.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($isEditing)
                            <div class="bg-amber-950/30 border border-amber-500/30 p-3 rounded-[10px] mb-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <div>
                                        <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest mb-1">Modo Edición Administrativa</p>
                                        <p class="text-[9px] text-zinc-400 leading-relaxed uppercase font-bold">
                                            Modificando datos maestros. Los cambios de precio en este modo <span class="text-amber-500 font-black">NO generarán cobros adicionales automáticos</span>. Úselo solo para correcciones.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- TAB RESERVA ESPACIAL -->
                        <div x-show="tab === 'space'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            @if(!$isAdendaMode)

                                {{-- 1. CORE DE MISIÓN (Vuelo y Destino) --}}
                                <div class="space-y-3">
                                    <h6
                                        class="block text-[11px] font-bold text-cyan-400 mb-2 uppercase tracking-[0.2em] pl-1 border-b border-cyan-900/30 pb-1 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        1. Core de Misión (Trayecto Principal)
                                    </h6>

                                    <div>
                                        <label
                                            class="block text-[9px] font-bold text-zinc-500 mb-1 uppercase tracking-widest pl-2">
                                            Vuelo Espacial (Ida)
                                        </label>
                                        @if($selectedFlightLabel)
                                            <div
                                                class="flex items-center gap-2 bg-cyan-950/30 border border-cyan-900/50 px-3 py-2 rounded-[10px]">
                                                <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <span
                                                    class="text-cyan-400 text-xs font-mono flex-1 truncate">{{ $selectedFlightLabel }}</span>
                                                <button type="button" wire:click="clearSelectedFlight"
                                                    class="text-zinc-600 hover:text-red-400 transition-colors ml-auto shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <div class="relative">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                                    <svg class="h-4 w-4 text-cyan-800" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </div>
                                                <input type="text" wire:model.live.debounce.300ms="flightSearch"
                                                    placeholder="Buscar por código o destino..."
                                                    class="w-full pl-10 bg-[#050505] border border-cyan-900/40 focus:border-cyan-500 text-cyan-100 px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                                @if(!empty($flightSearchResults))
                                                    <div
                                                        class="absolute z-20 w-full mt-1 bg-[#0f0f0f] border border-cyan-900/40 rounded-[10px] shadow-lg max-h-48 overflow-y-auto">
                                                        @foreach($flightSearchResults as $fr)
                                                            <button type="button"
                                                                wire:click="selectFlight({{ $fr['id'] }}, '{{ addslashes($fr['label']) }}')"
                                                                class="w-full text-left px-4 py-2.5 text-sm text-zinc-300 hover:bg-cyan-900/30 hover:text-white transition-colors border-b border-zinc-800/50 last:border-0">
                                                                <div class="font-bold text-cyan-400 font-mono">{{ $fr['label'] }}</div>
                                                                <div class="text-[10px] text-zinc-500 mt-0.5">Precio base:
                                                                    ${{ number_format($fr['price'], 0, ',', '.') }}</div>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div>
                                <h6
                                    class="block text-[11px] font-bold text-amber-500 mb-2 mt-4 uppercase tracking-[0.2em] pl-1 border-b border-amber-900/30 pb-1 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    @if($groupMode) 2. Titular & Selección @else Titular / Pagador @endif
                                </h6>

                                @if($user_id && $selectedClientName)
                                    <div
                                        class="flex items-center justify-between bg-[#050505] border {{ $isEditing ? 'border-amber-900/40' : 'border-zinc-700/50' }} px-3 py-2 rounded-[10px]">
                                        <span class="text-sm text-amber-400 font-bold">{{ $selectedClientName }}</span>
                                        <button type="button" wire:click="clearSelectedClient"
                                            class="text-zinc-500 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                            <svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" wire:model.live.debounce.300ms="clientSearch"
                                            placeholder="Buscar por email o nombre..."
                                            class="w-full pl-10 bg-[#050505] border border-amber-900/40 focus:border-amber-500 text-white px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">

                                        @if(!empty($clientSearchResults))
                                            <div
                                                class="absolute z-10 w-full mt-1 bg-[#0f0f0f] border border-zinc-700/50 rounded-[10px] shadow-lg max-h-48 overflow-y-auto">
                                                @foreach($clientSearchResults as $resClient)
                                                    <button type="button"
                                                        wire:click="selectClient({{ $resClient['id'] }}, '{{ addslashes($resClient['name']) }}', '{{ addslashes($resClient['email']) }}')"
                                                        class="w-full text-left px-4 py-2 text-sm text-zinc-300 hover:bg-amber-900/40 hover:text-white transition-colors border-b border-zinc-800/50 last:border-0">
                                                        <div class="font-bold">{{ $resClient['name'] }}</div>
                                                        <div class="text-[10px] text-zinc-500 font-mono">{{ $resClient['email'] }}
                                                        </div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            @if($user_id)
                                    @if($groupMode)
                                            {{-- ═══ MODO GRUPO: Checklist de pasajeros ═══ --}}
                                            <div>
                                                <label
                                                    class="block text-[10px] font-bold text-violet-400 mb-2 uppercase tracking-widest pl-2 flex items-center gap-2">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    Seleccionar Expedicionarios
                                                </label>

                                                @if($clientPassengers->isEmpty())
                                                    <div class="bg-amber-950/20 border border-amber-900/30 p-3 rounded-[10px] text-center">
                                                        <p class="text-[9px] text-amber-500 uppercase font-bold">
                                                            Este cliente no tiene pasajeros registrados.
                                                            <a href="{{ route('admin.passengers', ['userId' => $user_id]) }}"
                                                                class="underline hover:text-amber-400">Registrar aquí →</a>
                                                        </p>
                                                    </div>
                                                @else
                                                        <div class="space-y-2 mb-3">
                                                            {{-- Se muestra un item por pasajero del cliente --}}
                                                            @foreach($clientPassengers as $cp)
                                                                                @php
                                                                                    $alreadyAdded = collect($selectedPassengers)->pluck('passenger_id')->contains($cp->id);
                                                                                    $cpHasPassport = $cp->hasValidPassport();
                                                                                    $cpHasTraining = $cp->hasValidTraining();
                                                                                    $cpHasDiscount = $cp->hasTrainingDiscount();
                                                                                @endphp
                                                                                <button type="button"
                                                                                    wire:click="{{ $alreadyAdded ? 'removePassengerFromGroup(' . collect($selectedPassengers)->search(fn($p) => $p['passenger_id'] === $cp->id) . ')' : 'addPassengerToGroup(' . $cp->id . ')' }}"
                                                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-[8px] border text-left transition-all
                                                                                                                                                                                                                                                                                                                                                                            {{ $alreadyAdded
                                                                ? 'bg-violet-900/30 border-violet-500/60 shadow-[0_0_8px_rgba(139,92,246,0.2)]'
                                                                : 'bg-zinc-900/50 border-zinc-700/40 hover:border-violet-700/50 hover:bg-violet-950/20' }}">
                                                                                    {{-- Checkbox visual --}}
                                                                                    <div
                                                                                        class="w-4 h-4 rounded border flex items-center justify-center flex-shrink-0
                                                                                                                                                                                                                                                                                                                                                                            {{ $alreadyAdded ? 'bg-violet-600 border-violet-500' : 'border-zinc-600 bg-zinc-900' }}">
                                                                                        @if($alreadyAdded)
                                                                                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                                                <path fill-rule="evenodd"
                                                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                                                    clip-rule="evenodd" />
                                                                                            </svg>
                                                                                        @endif
                                                                                    </div>
                                                                                    {{-- Nombre y doc --}}
                                                                                    <div class="flex-1 min-w-0">
                                                                                        <div class="text-[11px] font-bold uppercase tracking-wide truncate">
                                                                                            {{ $cp->full_name }}
                                                                                        </div>
                                                                                        <div class="text-[9px] font-mono text-zinc-500">{{ $cp->document_country }}:
                                                                                            {{ $cp->document_number }}
                                                                                        </div>
                                                                                    </div>
                                                                                    {{-- Badges de validez --}}
                                                                                    <div class="flex gap-1 flex-shrink-0">
                                                                                        @if($cpHasPassport)
                                                                                            <span
                                                                                                class="text-[8px] font-bold text-emerald-400 bg-emerald-950/40 border border-emerald-800/50 px-1.5 py-0.5 rounded">PASAPORTE
                                                                                                ✓</span>
                                                                                        @endif
                                                                                        @if($cpHasTraining)
                                                                                            <span
                                                                                                class="text-[8px] font-bold text-cyan-400 bg-cyan-950/40 border border-cyan-800/50 px-1.5 py-0.5 rounded">TRAINING
                                                                                                ✓</span>
                                                                                        @endif
                                                                                        @if($cpHasDiscount)
                                                                                            <span
                                                                                                class="text-[8px] font-bold text-amber-400 bg-amber-950/40 border border-amber-800/50 px-1.5 py-0.5 rounded">10%
                                                                                                DESC.</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </button>
                                                            @endforeach
                                                        </div>

                                                        {{-- ══ Tarjetas colapsables de configuración por pasajero ══ --}}
                                                        @foreach($selectedPassengers as $pIdx => $pData)
                                                                <div class="border border-violet-900/40 bg-violet-950/10 rounded-[10px] mb-2 overflow-hidden"
                                                                    x-data="{ open: false }">
                                                                    {{-- Cabecera colapsable --}}
                                                                    <button type="button" @click="open = !open"
                                                                        class="w-full flex items-center justify-between px-3 py-2.5 text-left hover:bg-violet-900/20 transition-colors">
                                                                        <div class="flex items-center gap-2">
                                                                            <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                            </svg>
                                                                            <span
                                                                                class="text-[11px] font-bold text-violet-200 uppercase tracking-wide">{{ $pData['name'] }}</span>
                                                                            <span
                                                                                class="text-[9px] font-mono text-zinc-500">{{ strtoupper($pData['seat_type']) }}</span>
                                                                            {{-- Badges de validez en la cabecera --}}
                                                                            @if($pData['has_valid_passport'] ?? false)
                                                                                <span
                                                                                    class="text-[8px] font-bold text-emerald-400 bg-emerald-950/30 border border-emerald-800/40 px-1 py-0.5 rounded">PAS
                                                                                    ✓</span>
                                                                            @endif
                                                                            @if($pData['has_valid_training'] ?? false)
                                                                                <span
                                                                                    class="text-[8px] font-bold text-cyan-400 bg-cyan-950/30 border border-cyan-800/40 px-1 py-0.5 rounded">TRAIN
                                                                                    ✓</span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="flex items-center gap-2">
                                                                            <span class="text-[11px] font-bold text-violet-300">
                                                                                ${{ number_format($pData['total_price'], 0, ',', '.') }}
                                                                            </span>
                                                                            <svg class="w-4 h-4 text-zinc-500 transition-transform"
                                                                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                    d="M19 9l-7 7-7-7" />
                                                                            </svg>
                                                                        </div>
                                                                    </button>

                                                                    {{-- Contenido colapsable --}}
                                                                    <div x-show="open" x-transition
                                                                        class="px-3 pb-3 space-y-3 border-t border-violet-900/30">

                                                                        {{-- Clase de asiento --}}
                                                                        <div class="pt-2">
                                                                            <label
                                                                                class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Clase</label>
                                                                            <div class="flex gap-2 mt-1">
                                                                                <button type="button"
                                                                                    wire:click="updatePassengerService({{ $pIdx }}, 'seat_type', 'nova')"
                                                                                    class="flex-1 py-1.5 text-[9px] font-bold uppercase rounded-[6px] border transition-all
                                                                                                                                                                                                                                                                                                        {{ $pData['seat_type'] === 'nova' ? 'bg-cyan-700 border-cyan-500 text-white' : 'bg-zinc-900 border-zinc-700/50 text-zinc-500 hover:border-cyan-700' }}">
                                                                                    Nova
                                                                                </button>
                                                                                <button type="button"
                                                                                    wire:click="updatePassengerService({{ $pIdx }}, 'seat_type', 'supernova')"
                                                                                    class="flex-1 py-1.5 text-[9px] font-bold uppercase rounded-[6px] border transition-all
                                                                                                                                                                                                                                                                                                        {{ $pData['seat_type'] === 'supernova' ? 'bg-amber-600 border-amber-500 text-white' : 'bg-zinc-900 border-zinc-700/50 text-zinc-500 hover:border-amber-600' }}">
                                                                                    Supernova
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                        {{-- Alertas de documentos ya válidos --}}
                                                                        @if($pData['has_valid_training'] ?? false)
                                                                            <div
                                                                                class="flex items-center gap-2 text-[9px] font-bold text-cyan-400 bg-cyan-950/20 border border-cyan-900/30 px-2 py-1.5 rounded-[6px]">
                                                                                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                                    <path fill-rule="evenodd"
                                                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                                        clip-rule="evenodd" />
                                                                                </svg>
                                                                                Ya tiene Cert. Iris Training vigente
                                                                                @if($pData['has_training_discount'] ?? false)
                                                                                    <span
                                                                                        class="ml-auto text-amber-400 bg-amber-950/30 border border-amber-800/40 px-1 rounded">DESC.
                                                                                        10% AUTO</span>
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                        @if($pData['has_valid_passport'] ?? false)
                                                                            <div
                                                                                class="flex items-center gap-2 text-[9px] font-bold text-emerald-400 bg-emerald-950/20 border border-emerald-900/30 px-2 py-1.5 rounded-[6px]">
                                                                                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                                    <path fill-rule="evenodd"
                                                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                                        clip-rule="evenodd" />
                                                                                </svg>
                                                                                Ya tiene Pasaporte Espacial vigente
                                                                            </div>
                                                                        @endif

                                                                        {{-- Servicios individuales --}}
                                                                        <div>
                                                                            <label
                                                                                class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest mb-1.5 block">Servicios
                                                                                Adicionales</label>
                                                                            <div class="grid grid-cols-2 gap-1.5">
                                                                                {{-- Training: deshabilitado si ya tiene cert. válido --}}
                                                                                <label
                                                                                    class="flex items-center gap-2 {{ ($pData['has_valid_training'] ?? false) ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer' }}">
                                                                                    <input type="checkbox"
                                                                                        wire:change="updatePassengerService({{ $pIdx }}, 'training_included', $event.target.checked)"
                                                                                        {{ $pData['training_included'] ? 'checked' : '' }} {{ ($pData['has_valid_training'] ?? false) ? 'disabled' : '' }}
                                                                                        class="w-3.5 h-3.5 bg-black border-zinc-700 text-emerald-500 rounded">
                                                                                    <span
                                                                                        class="text-[9px] text-zinc-300 uppercase font-bold">Training</span>
                                                                                </label>
                                                                                {{-- Pasaporte: deshabilitado si ya tiene pasaporte válido --}}
                                                                                <label
                                                                                    class="flex items-center gap-2 {{ ($pData['has_valid_passport'] ?? false) ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer' }}">
                                                                                    <input type="checkbox"
                                                                                        wire:change="updatePassengerService({{ $pIdx }}, 'passport_management_included', $event.target.checked)"
                                                                                        {{ $pData['passport_management_included'] ? 'checked' : '' }} {{ ($pData['has_valid_passport'] ?? false) ? 'disabled' : '' }}
                                                                                        class="w-3.5 h-3.5 bg-black border-zinc-700 text-blue-500 rounded">
                                                                                    <span
                                                                                        class="text-[9px] text-zinc-300 uppercase font-bold">Pasaporte</span>
                                                                                </label>
                                                                                <label class="flex items-center gap-2 cursor-pointer">
                                                                                    <input type="checkbox"
                                                                                        wire:change="updatePassengerService({{ $pIdx }}, 'refund_insurance_included', $event.target.checked)"
                                                                                        {{ $pData['refund_insurance_included'] ? 'checked' : '' }}
                                                                                        class="w-3.5 h-3.5 bg-black border-zinc-700 text-pink-500 rounded">
                                                                                    <span class="text-[9px] text-zinc-300 uppercase font-bold">Seguro
                                                                                        Reembolso</span>
                                                                                </label>
                                                                                <label class="flex items-center gap-2 cursor-pointer">
                                                                                    <input type="checkbox"
                                                                                        wire:change="updatePassengerService({{ $pIdx }}, 'vip_transfer_included', $event.target.checked)"
                                                                                        {{ $pData['vip_transfer_included'] ? 'checked' : '' }}
                                                                                        class="w-3.5 h-3.5 bg-black border-zinc-700 text-amber-500 rounded">
                                                                                    <span class="text-[9px] text-zinc-300 uppercase font-bold">VIP
                                                                                        Transfer</span>
                                                                                </label>
                                                                            </div>
                                                                        </div>

                                                                        {{-- Motor de Coherencia Temporal: Alertas y Sugerencias --}}
                                                                        @if(!empty($temporalWarnings[$pIdx]) || isset($smartSuggestions[$pIdx]))
                                                                            <div
                                                                                class="p-2.5 rounded-[12px] border border-orange-900/30 bg-orange-950/10 space-y-2">
                                                                                @if(!empty($temporalWarnings[$pIdx]))
                                                                                    <div class="space-y-1">
                                                                                        @foreach($temporalWarnings[$pIdx] as $warning)
                                                                                            <p class="text-[9px] font-bold text-orange-400 leading-tight">
                                                                                                {{ $warning }}
                                                                                            </p>
                                                                                        @endforeach
                                                                                    </div>
                                                                                @endif

                                                                                @if(isset($smartSuggestions[$pIdx]))
                                                                                    <div
                                                                                        class="flex items-center justify-between gap-3 pt-1 border-t border-orange-900/20">
                                                                                        <div class="flex items-center gap-2">
                                                                                            <div class="bg-orange-500/20 p-1 rounded-full">
                                                                                                <svg class="w-3 h-3 text-orange-400" fill="none"
                                                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                        stroke-width="2"
                                                                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.247 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                                                                </svg>
                                                                                            </div>
                                                                                            <span class="text-[9px] text-zinc-300 font-medium">Sugerencia
                                                                                                Estancia: <b class="text-white">{{ $smartSuggestions[$pIdx] }}
                                                                                                    noches</b></span>
                                                                                        </div>
                                                                                        <button type="button" wire:click="applySmartSuggestion({{ $pIdx }})"
                                                                                            class="text-[8px] font-black uppercase tracking-widest bg-orange-600 hover:bg-orange-500 text-white px-2 py-1 rounded-[4px] transition-all">
                                                                                            Aplicar
                                                                                        </button>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endif

                                                                        {{-- Logística individual: Hotel --}}
                                                                        <div class="space-y-1.5">
                                                                            <label
                                                                                class="text-[9px] font-bold text-pink-400 uppercase tracking-widest block">Hotel</label>
                                                                            <select wire:change="selectPassengerHotel({{ $pIdx }}, $event.target.value)"
                                                                                class="w-full bg-[#050505] border border-pink-900/40 focus:border-pink-600 text-white px-2 py-1.5 text-[10px] rounded-[8px] focus:outline-none transition-colors">
                                                                                <option value="">Sin hotel</option>
                                                                                @foreach($hotels as $h)
                                                                                    <option value="{{ $h->id }}" {{ ($pData['hotel_id'] ?? null) == $h->id ? 'selected' : '' }}>
                                                                                        {{ $h->name }} ({{ $h->galactic_stars }}&#9733; ·
                                                                                        ${{ number_format($h->price_per_night, 0) }}/noche)
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            @if($pData['hotel_id'] ?? false)
                                                                                <div class="flex items-center gap-2">
                                                                                    <label
                                                                                        class="text-[9px] text-zinc-500 uppercase tracking-widest whitespace-nowrap">Noches</label>
                                                                                    <input type="number" min="0" max="30"
                                                                                        wire:change="updatePassengerHotelNights({{ $pIdx }}, $event.target.value)"
                                                                                        value="{{ $pData['hotel_nights'] ?? 0 }}"
                                                                                        class="w-full bg-[#050505] border border-pink-900/40 text-white px-2 py-1 text-[10px] rounded-[8px] focus:outline-none">
                                                                                </div>
                                                                            @endif
                                                                        </div>

                                                                    </div>

                                                                    {{-- Logística individual: Regreso (Subpanel) --}}
                                                                    @if($hasReturnFlight)
                                                                        <div
                                                                            class="mt-2 p-2.5 bg-purple-950/20 border border-purple-900/30 rounded-[8px] space-y-2.5">
                                                                            <h6
                                                                                class="text-[8px] font-bold text-purple-400 uppercase tracking-widest flex items-center gap-1.5">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                        d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                                                                </svg>
                                                                                Regreso: Logística
                                                                            </h6>

                                                                            <div class="grid grid-cols-1 gap-2">
                                                                                <div>
                                                                                    <label
                                                                                        class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest block mb-1">Hotel</label>
                                                                                    <select
                                                                                        wire:change="selectPassengerReturnHotel({{ $pIdx }}, $event.target.value)"
                                                                                        class="w-full bg-[#050505] border border-purple-900/40 text-white px-2 py-1 text-[9px] rounded-[4px] focus:outline-none">
                                                                                        <option value="">Sin hotel</option>
                                                                                        @foreach($hotels as $h)
                                                                                            <option value="{{ $h->id }}" {{ ($pData['return_hotel_id'] ?? null) == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <div class="flex gap-2">
                                                                                    <div class="flex-1">
                                                                                        <label
                                                                                            class="text-[8px] font-bold text-zinc-500 uppercase tracking-widest block mb-1">V.
                                                                                            Terrestre</label>
                                                                                        <select
                                                                                            wire:change="selectPassengerReturnTerrestrialFlight({{ $pIdx }}, $event.target.value)"
                                                                                            class="w-full bg-[#050505] border border-purple-900/40 text-white px-2 py-1 text-[9px] rounded-[4px] focus:outline-none">
                                                                                            <option value="">Ninguno</option>
                                                                                            @foreach($terrestrialFlights as $tf)
                                                                                                <option value="{{ $tf->id }}" {{ ($pData['return_terrestrial_flight_id'] ?? null) == $tf->id ? 'selected' : '' }}>{{ $tf->originLocation?->name }} →
                                                                                                    {{ $tf->destinationLocation?->name }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>
                                                                                    <label class="flex items-center gap-1.5 mt-4 cursor-pointer">
                                                                                        <input type="checkbox"
                                                                                            wire:change="updatePassengerReturnService({{ $pIdx }}, 'return_vip_transfer_included', $event.target.checked)"
                                                                                            {{ ($pData['return_vip_transfer_included'] ?? false) ? 'checked' : '' }}
                                                                                            class="w-3 h-3 bg-black border-zinc-700 text-purple-500 rounded">
                                                                                        <span
                                                                                            class="text-[8px] text-zinc-400 uppercase font-bold">VIP</span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>

                                                                            @if(($pData['return_total_price'] ?? 0) > 0)
                                                                                <div
                                                                                    class="flex justify-between items-center text-[9px] pt-1 border-t border-purple-900/20 font-bold">
                                                                                    <span class="text-purple-400 italic font-normal">Subtotal Regreso:</span>
                                                                                    <span
                                                                                        class="text-white">${{ number_format($pData['return_total_price'], 0, ',', '.') }}</span>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif

                                                                    {{-- Desglose de precio --}}
                                                                    @if(!empty($pData['priceBreakdown']))
                                                                        <div
                                                                            class="bg-black/40 rounded-[6px] p-2 text-[9px] font-mono space-y-1 border border-zinc-800/50">
                                                                            @foreach(['space' => ['Vuelo Espacial', 'text-cyan-400'], 'hotel' => ['Hotel', 'text-pink-400'], 'terrestrial' => ['V. Terrestre', 'text-orange-400'], 'training' => ['Training', 'text-emerald-400'], 'passport' => ['Pasaporte', 'text-blue-400'], 'vip' => ['VIP Transfer', 'text-amber-400'], 'insurance' => ['Seguro', 'text-indigo-400'], 'discount' => ['Descuento −', 'text-emerald-500']] as $k => [$bLabel, $color])
                                                                                @if(($pData['priceBreakdown'][$k] ?? 0) > 0)
                                                                                    <div class="flex justify-between">
                                                                                        <span class="{{ $color }}">{{ $bLabel }}</span>
                                                                                        <span
                                                                                            class="text-zinc-300">{{ $k === 'discount' ? '-' : '' }}${{ number_format($pData['priceBreakdown'][$k], 0, ',', '.') }}</span>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                            <div class="flex justify-between border-t border-zinc-700/50 pt-1 font-bold">
                                                                                <span class="text-white">Total</span>
                                                                                <span
                                                                                    class="text-violet-300">${{ number_format($pData['total_price'], 0, ',', '.') }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    {{-- Botón de eliminar del grupo --}}
                                                                    <button type="button" wire:click="removePassengerFromGroup({{ $pIdx }})"
                                                                        class="w-full flex items-center justify-center gap-1.5 py-1.5 text-[9px] font-bold uppercase text-red-500 hover:text-red-400 border border-red-900/40 hover:border-red-700/60 rounded-[6px] transition-colors">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                        </svg>
                                                                        Quitar del grupo
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                    {{-- Barra de total grupal sticky --}}
                                                    @if(count($selectedPassengers) > 0)
                                                        <div
                                                            class="sticky bottom-0 bg-gradient-to-t from-black to-zinc-900/90 border border-violet-900/50 rounded-[10px] px-4 py-3 flex items-center justify-between shadow-[0_0_20px_rgba(139,92,246,0.2)]">
                                                            <div>
                                                                <div class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Total
                                                                    Expedición</div>
                                                                <div class="text-[10px] text-violet-400">{{ count($selectedPassengers) }}
                                                                    pasajero(s) · {{ $space_flight_id ? 'Vuelo seleccionado' : 'Sin vuelo' }}</div>
                                                            </div>
                                                            <div class="text-xl font-black text-violet-300 shadow-[0_0_15px_rgba(139,92,246,0.4)]">
                                                                ${{ number_format($groupTotal, 0, ',', '.') }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                        </div>
                                    @else
                                    {{-- ═══ MODO INDIVIDUAL: selector original ═══ --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-cyan-400 mb-1 uppercase tracking-widest pl-2">
                                            Pasajero (Viajero)
                                        </label>
                                        @if($passenger_id && $selectedPassengerName)
                                            <div
                                                class="flex items-center justify-between bg-cyan-950/20 border border-cyan-900/40 px-3 py-2 rounded-[10px]">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm text-cyan-400 font-bold uppercase tracking-wider">{{ $selectedPassengerName }}</span>
                                                    @php $p = \App\Models\Passenger::find($passenger_id); @endphp
                                                    @if($p)
                                                        <span
                                                            class="text-[9px] font-mono text-cyan-600 uppercase">{{ $p->document_country }}:
                                                            {{ $p->document_number }}</span>
                                                    @endif
                                                </div>
                                                <button type="button" wire:click="clearSelectedPassenger"
                                                    class="text-zinc-500 hover:text-red-500 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <div class="space-y-2">
                                                <select wire:model.live="passenger_id"
                                                    class="w-full bg-[#050505] border border-cyan-900/40 focus:border-cyan-500 text-white px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                                    <option value="">-- Seleccionar Pasajero --</option>
                                                    @foreach($clientPassengers as $cp)
                                                        <option value="{{ $cp->id }}">{{ $cp->full_name }} ({{ $cp->document_number }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if($clientPassengers->isEmpty())
                                                    <div class="bg-amber-950/20 border border-amber-900/30 p-2 rounded-[5px]">
                                                        <p class="text-[9px] text-amber-500 uppercase font-bold text-center">
                                                            Este cliente no tiene pasajeros registrados.
                                                            <a href="{{ route('admin.passengers', ['userId' => $user_id]) }}"
                                                                class="underline hover:text-amber-400">Registrar aquí →</a>
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @error('passenger_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            @endif
                        @error('space_flight_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror

                        {{-- TOGGLE VUELO DE REGRESO --}}
                        <div class="mt-4 pt-4 border-t border-zinc-800/50">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model.live="hasReturnFlight" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-zinc-800 rounded-full peer peer-checked:bg-purple-600 transition-colors border border-zinc-700">
                                    </div>
                                    <div
                                        class="absolute left-1 top-1 w-3 h-3 bg-zinc-400 rounded-full peer-checked:translate-x-5 peer-checked:bg-white transition-transform">
                                    </div>
                                </div>
                                <span
                                    class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest group-hover:text-purple-400 transition-colors">
                                    ¿Incluye Vuelo de Regreso?
                                </span>
                            </label>

                            @if($hasReturnFlight)
                                <div class="mt-4 space-y-4 animate-fadeIn">
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-purple-400 mb-1 uppercase tracking-widest pl-2 flex items-center gap-2">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                            </svg>
                                            Vuelo Espacial de Regreso
                                        </label>
                                        @if($selectedReturnFlightLabel)
                                            <div
                                                class="flex items-center gap-2 bg-purple-950/30 border border-purple-900/50 px-3 py-2 rounded-[10px]">
                                                <span
                                                    class="text-purple-400 text-xs font-mono flex-1 truncate">{{ $selectedReturnFlightLabel }}</span>
                                                <button type="button" wire:click="clearSelectedReturnFlight"
                                                    class="text-zinc-600 hover:text-red-400 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <div class="relative">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                                    <svg class="h-4 w-4 text-purple-800" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </div>
                                                <input type="text" wire:model.live.debounce.300ms="returnFlightSearch"
                                                    placeholder="Buscar vuelo de vuelta..."
                                                    class="w-full pl-10 bg-[#050505] border border-purple-900/40 focus:border-purple-500 text-purple-100 px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                                @if(!empty($returnFlightSearchResults))
                                                    <div
                                                        class="absolute z-30 w-full mt-1 bg-[#0f0f0f] border border-purple-900/40 rounded-[10px] shadow-lg max-h-48 overflow-y-auto">
                                                        @foreach($returnFlightSearchResults as $rf)
                                                            <button type="button"
                                                                wire:click="selectReturnFlight({{ $rf['id'] }}, '{{ addslashes($rf['label']) }}')"
                                                                class="w-full text-left px-4 py-2.5 text-sm text-zinc-300 hover:bg-purple-900/30 hover:text-white transition-colors border-b border-zinc-800/50 last:border-0">
                                                                <div class="font-bold text-purple-400 font-mono">{{ $rf['label'] }}
                                                                </div>
                                                                <div class="text-[10px] text-zinc-500 mt-0.5">Precio base:
                                                                    ${{ number_format($rf['price'], 0, ',', '.') }}</div>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Logística de Regreso: Modo Individual --}}
                                    @if(!$groupMode)
                                        <div class="p-3 bg-purple-950/10 border border-purple-900/20 rounded-[10px] space-y-3">
                                            <h5 class="text-[9px] font-bold text-purple-300 uppercase tracking-[0.2em] mb-2">
                                                Logística Individual Regreso</h5>

                                            <div>
                                                <label
                                                    class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest block mb-1">Hotel
                                                    Regreso</label>
                                                <select wire:model.live="return_hotel_id"
                                                    class="w-full bg-black border border-purple-900/40 text-white px-2 py-1.5 text-[10px] rounded-[6px]">
                                                    <option value="">Sin hotel</option>
                                                    @foreach($hotels as $h)
                                                        <option value="{{ $h->id }}">{{ $h->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label
                                                        class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest block mb-1">Vuelo
                                                        Terrestre</label>
                                                    <select wire:model.live="return_terrestrial_flight_id"
                                                        class="w-full bg-black border border-purple-900/40 text-white px-2 py-1.5 text-[10px] rounded-[6px]">
                                                        <option value="">Ninguno</option>
                                                        @foreach($terrestrialFlights as $tf)
                                                            <option value="{{ $tf->id }}">{{ $tf->originLocation?->name }} →
                                                                {{ $tf->destinationLocation?->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <label class="flex items-center gap-2 mt-4 cursor-pointer">
                                                    <input type="checkbox" wire:model.live="return_vip_transfer_included"
                                                        class="w-3.5 h-3.5 bg-black border-zinc-700 text-purple-500 rounded">
                                                    <span class="text-[9px] text-zinc-300 uppercase font-bold">Transfer</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <hr class="border-zinc-800 my-2">

                            {{-- Clase asiento + Estado: en modo grupo, solo Estado --}}
                            @if($groupMode)
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">Estado
                                        Reserva</label>
                                    <select wire:model="status"
                                        class="w-full bg-[#050505] border border-zinc-700/50 focus:border-zinc-400 text-white px-3 py-2 text-sm rounded-[10px]">
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Cancelada">Cancelada</option>
                                    </select>
                                </div>
                            @else
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">Estado
                                            Reserva</label>
                                        <select wire:model="status"
                                            class="w-full bg-[#050505] border border-zinc-700/50 focus:border-zinc-400 text-white px-3 py-2 text-sm rounded-[10px]">
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="Cancelada">Cancelada</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">Clase
                                            Asiento</label>
                                        <select wire:model.live="seat_type"
                                            class="w-full bg-[#050505] border border-zinc-700/50 focus:border-zinc-400 text-white px-3 py-2 text-sm rounded-[10px]">
                                            <option value="nova">Nova</option>
                                            <option value="supernova">Supernova</option>
                                        </select>
                                    </div>
                                </div>
                            @endif

                            {{-- Asiento individual: solo visible en modo NO grupo --}}
                            @if(!$groupMode)
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-zinc-400 mb-1 uppercase tracking-widest">Asiento
                                        (Opcional)</label>
                                    @php
                                        $maxSeat = 999;
                                        if ($space_flight_id) {
                                            $f = \App\Models\Flight::with('starship')->find($space_flight_id);
                                            if ($f && $f->starship) {
                                                $maxSeat = ($seat_type === 'supernova') ? $f->starship->vip_capacity : $f->starship->general_capacity;
                                            }
                                        }
                                    @endphp
                                    <input type="number" min="1" max="{{ $maxSeat }}" wire:model="seat_number"
                                        class="w-full bg-[#050505] border border-zinc-700/50 focus:border-zinc-400 text-white px-3 py-2 text-sm rounded-[10px]">
                                </div>
                            @endif
                            {{-- Desglose de precio en tiempo real --}}
                            <div class="mt-3 rounded-[12px] border overflow-hidden
                                {{ !empty($priceBreakdown) ? 'border-purple-900/60 shadow-[0_0_20px_rgba(168,85,247,0.08)]' : 'border-zinc-800/50' }}
                                bg-gradient-to-b from-zinc-900/80 to-black/60 backdrop-blur-sm">

                                <div class="px-4 py-2.5 border-b border-zinc-800/50 flex items-center justify-between">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-400 flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Factura
                                    </span>
                                    @if(empty($priceBreakdown))
                                        <span class="text-[9px] text-zinc-600 uppercase tracking-widest">Esperando
                                            datos...</span>
                                    @endif
                                </div>

                                @if(!empty($priceBreakdown) && $priceBreakdown['space'] > 0)
                                    <div class="px-4 py-3 space-y-2 text-[11px] font-mono">

                                        {{-- Línea: Vuelo Espacial --}}
                                        <div class="flex justify-between items-center">
                                            <span class="text-cyan-400/80 flex items-center gap-1.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                Vuelo Espacial
                                                @if($priceBreakdown['mult'] > 1)
                                                    <span
                                                        class="text-[9px] text-cyan-700 bg-cyan-950/40 px-1.5 py-0.5 rounded-full">×
                                                        SuperNova ({{ $priceBreakdown['mult'] }})</span>
                                                @endif
                                            </span>
                                            <span
                                                class="text-cyan-300 font-bold">${{ number_format($priceBreakdown['space'], 2, ',', '.') }}</span>
                                        </div>

                                        {{-- Línea: Hotel --}}
                                        @if($priceBreakdown['hotel'] > 0)
                                            <div class="flex justify-between items-center">
                                                <span class="text-pink-400/80 flex items-center gap-1.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                    </svg>
                                                    Hotel ({{ $priceBreakdown['hotel_nights'] }} noches)
                                                </span>
                                                <span
                                                    class="text-pink-300 font-bold">${{ number_format($priceBreakdown['hotel'], 2, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        {{-- Línea: Vuelo Terrestre --}}
                                        @if($priceBreakdown['terrestrial'] > 0)
                                            <div class="flex justify-between items-center">
                                                <span class="text-amber-400/80 flex items-center gap-1.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Vuelo Terrestre
                                                </span>
                                                <span
                                                    class="text-amber-300 font-bold">${{ number_format($priceBreakdown['terrestrial'], 2, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        {{-- Línea: Entrenamiento Iris Training --}}
                                        @if($priceBreakdown['training'] > 0)
                                            <div class="flex justify-between items-center">
                                                <span class="text-emerald-400/80 flex items-center gap-1.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                    </svg>
                                                    Iris Training
                                                </span>
                                                <span
                                                    class="text-emerald-300 font-bold">${{ number_format($priceBreakdown['training'], 2, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        {{-- Línea: VIP Transfer --}}
                                        @if($priceBreakdown['vip'] > 0)
                                            <div class="flex justify-between items-center">
                                                <span class="text-amber-400/80 flex items-center gap-1.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Traslados
                                                </span>
                                                <span
                                                    class="text-amber-300 font-bold">${{ number_format($priceBreakdown['vip'], 2, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        {{-- Línea: Gestión de Pasaporte --}}
                                        @if($priceBreakdown['passport'] > 0)
                                            <div class="flex justify-between items-center">
                                                <span class="text-blue-400/80 flex items-center gap-1.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                    </svg>
                                                    Gestión Pasaporte
                                                </span>
                                                <span
                                                    class="text-blue-300 font-bold">${{ number_format($priceBreakdown['passport'], 2, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        {{-- Línea: Seguro de Reembolso --}}
                                        @if($priceBreakdown['insurance'] > 0)
                                            <div class="flex justify-between items-center">
                                                <span class="text-indigo-400/80 flex items-center gap-1.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                    </svg>
                                                    Seguro Reembolso
                                                </span>
                                                <span
                                                    class="text-indigo-300 font-bold">${{ number_format($priceBreakdown['insurance'], 2, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        {{-- TRAYECTO VUELTA RESUMEN --}}
                                        @if($hasReturnFlight && $return_total_price > 0)
                                            <div class="pt-2 border-t border-purple-900/20">
                                                <div class="flex justify-between items-center">
                                                    <span
                                                        class="text-purple-400 flex items-center gap-1.5 font-bold italic font-mono text-[9px] uppercase tracking-wider">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                                        </svg>
                                                        Coste Regreso Estimado
                                                    </span>
                                                    <span
                                                        class="text-purple-300 font-bold font-mono">${{ number_format($return_total_price, 2, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Separador --}}
                                        <div class="border-t border-zinc-700/50 pt-2 mt-2 space-y-1">
                                            {{-- Subtotal --}}
                                            <div class="flex justify-between items-center text-zinc-500">
                                                <span>Subtotal</span>
                                                <span>${{ number_format($priceBreakdown['subtotal'], 2, ',', '.') }}</span>
                                            </div>

                                            {{-- Descuento --}}
                                            @if($priceBreakdown['discount_pct'] > 0)
                                                <div class="flex justify-between items-center text-emerald-500">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                        </svg>
                                                        Desc. Certificado Iris Training
                                                        (−{{ $priceBreakdown['discount_pct'] }}%)
                                                    </span>
                                                    <span>−${{ number_format($priceBreakdown['discount_amt'], 2, ',', '.') }}</span>
                                                </div>
                                            @endif

                                            {{-- TOTAL --}}
                                            <div
                                                class="flex justify-between items-center pt-1.5 border-t border-purple-900/40">
                                                <span
                                                    class="text-[11px] font-bold text-white uppercase tracking-widest">TOTAL
                                                    EXPEDICIÓN</span>
                                                <span
                                                    class="text-[16px] font-black text-purple-400 shadow-[0_0_10px_rgba(168,85,247,0.3)]">
                                                    ${{ number_format($groupMode ? $groupTotal : ($total_price + $return_total_price), 2, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Panel de Ajuste precio(solo super_admin) --}}
                                        @if(auth()->user()?->role === 'super_admin')
                                            <div class="pt-3 mt-1 border-t border-zinc-800/50">
                                                <p
                                                    class="text-[9px] font-bold text-zinc-500 uppercase tracking-[0.2em] mb-2 flex items-center gap-1.5">
                                                    <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    </svg>
                                                    Modificar precio (Admin)
                                                </p>

                                                {{-- Tipo de ajuste --}}
                                                <div class="flex gap-2 mb-2">
                                                    @foreach(['none' => 'Sin ajuste', 'pct' => '% Descuento', 'fixed' => 'Precio Final'] as $val => $lbl)
                                                                                    <button type="button"
                                                                                        wire:click="$set('manual_adjustment_type', '{{ $val }}')"
                                                                                        class="flex-1 py-1 text-[9px] font-bold uppercase tracking-widest rounded-[6px] border transition-all
                                                                                                                                                                                                                                                                                                                                                        {{ $manual_adjustment_type === $val
                                                        ? 'bg-purple-700 border-purple-500 text-white'
                                                        : 'bg-zinc-900 border-zinc-700/50 text-zinc-500 hover:border-purple-700' }}">
                                                                                        {{ $lbl }}
                                                                                    </button>
                                                    @endforeach
                                                </div>

                                                @if($manual_adjustment_type !== 'none')
                                                    <div class="space-y-2">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-[10px] text-zinc-500 w-20 shrink-0">
                                                                {{ $manual_adjustment_type === 'pct' ? 'Porcentaje %' : 'Precio Final $' }}
                                                            </span>
                                                            <input type="number" wire:model.live="manual_adjustment_value"
                                                                step="{{ $manual_adjustment_type === 'pct' ? '0.1' : '100' }}"
                                                                min="0"
                                                                max="{{ $manual_adjustment_type === 'pct' ? '100' : '9999999' }}"
                                                                class="flex-1 bg-purple-950/20 border border-purple-900/60 focus:border-purple-500 text-purple-300 px-2 py-1 text-sm rounded-[6px] font-mono text-right"
                                                                placeholder="{{ $manual_adjustment_type === 'pct' ? '15' : '200000' }}">
                                                            <span class="text-[10px] text-purple-600 font-mono shrink-0">
                                                                {{ $manual_adjustment_type === 'pct' ? '%' : '$' }}
                                                            </span>
                                                        </div>
                                                        @if(!empty($priceBreakdown) && ($priceBreakdown['adj_amount'] ?? 0) > 0)
                                                            <div class="text-[10px] text-purple-400 font-mono text-right">
                                                                Ahorro:
                                                                −${{ number_format($priceBreakdown['adj_amount'], 0, ',', '.') }}
                                                            </div>
                                                        @endif
                                                        <input type="text" wire:model.live="discount_note"
                                                            placeholder="Motivo del descuento espacial"
                                                            class="w-full bg-zinc-900/50 border border-zinc-700/50 focus:border-purple-700 text-zinc-300 px-2 py-1.5 text-[10px] rounded-[6px] italic">
                                                        <p class="text-[9px] text-zinc-700 uppercase tracking-widest">El motivo
                                                            queda registrado.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="px-4 py-5 text-center">
                                        <p class="text-[10px] text-zinc-700 uppercase tracking-widest">Esperando datos...
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- Aviso de Reembolso --}}
                            @if(!empty($priceBreakdown) && $priceBreakdown['space'] > 0 && $priceBreakdown['insurance'] <= 0)
                                <div
                                    class="bg-red-950/20 border border-red-900/40 p-3 rounded-[10px] mt-2 flex items-start gap-3 animate-pulse">
                                    <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-[9px] text-red-400 uppercase tracking-widest leading-relaxed font-bold">
                                        Este billete no cuenta con seguro de reembolso por lo que no será reembolsado bajo
                                        ninguna circunstancia.
                                    </p>
                                </div>
                            @endif

                            <!-- Mensaje aqui de la politica de rembolso <div class="bg-zinc-900/50 p-3 rounded-[10px] border border-zinc-800 mt-1">
                                <p class="text-[10px] text-zinc-400 uppercase tracking-widest">Se re-comprueba la validación de los certificados en el momento del embarque. En caso de inconsistencia con el pasaporte/curso, el billete quedará incautado sin reembolso.</p>
                            </div> -->
                        </div>
                </div>

                <!-- TAB LOGÍSTICA TERRESTRE -->
                <div x-show="tab === 'logistic'" style="display: none;"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                    @if($groupMode)
                        <div class="p-8 text-center border-2 border-dashed border-zinc-800 rounded-[15px] bg-black/20">
                            <div
                                class="bg-violet-950/20 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 border border-violet-900/40 text-violet-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <h4 class="text-xs font-bold text-white uppercase tracking-widest mb-2">Logística
                                Individualizada</h4>
                            <p class="text-[10px] text-zinc-500 leading-relaxed max-w-[200px] mx-auto">
                                La logística de cada expedicionario se gestiona ahora de forma granular dentro de su tarjeta
                                dedicada en la pestaña de <b class="text-zinc-400">Pasajeros</b> para evitar errores de
                                sincronización.
                            </p>
                        </div>
                    @else
                        {{-- MODO INDIVIDUAL: bloque original --}}
                        <div>
                            <label class="block text-[10px] font-bold text-pink-400 mb-1 uppercase tracking-widest">
                                Vuelo Terrestre
                            </label>
                            @if($selectedTerrestrialLabel)
                                <div
                                    class="flex items-center gap-2 bg-pink-950/20 border border-pink-900/50 px-3 py-2 rounded-[10px]">
                                    <svg class="w-3.5 h-3.5 text-pink-400 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span
                                        class="text-pink-300 text-xs font-mono flex-1 truncate">{{ $selectedTerrestrialLabel }}</span>
                                    <button type="button" wire:click="clearSelectedTerrestrialFlight"
                                        class="text-zinc-600 hover:text-red-400 transition-colors ml-auto shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                        <svg class="h-4 w-4 text-amber-900" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" wire:model.live.debounce.300ms="terrestrialSearch"
                                        placeholder="Buscar por ciudad origen o destino..."
                                        class="w-full pl-10 bg-[#050505] border border-pink-900/40 focus:border-pink-500 text-pink-100 px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">
                                    @if(!empty($terrestrialSearchResults))
                                        <div
                                            class="absolute z-20 w-full mt-1 bg-[#0f0f0f] border border-amber-900/40 rounded-[10px] shadow-lg max-h-48 overflow-y-auto">
                                            @foreach($terrestrialSearchResults as $tr)
                                                <button type="button"
                                                    wire:click="selectTerrestrialFlight({{ $tr['id'] }}, '{{ addslashes($tr['label']) }}')"
                                                    class="w-full text-left px-4 py-2.5 text-sm text-zinc-300 hover:bg-amber-900/20 hover:text-white transition-colors border-b border-zinc-800/50 last:border-0">
                                                    <div class="font-bold text-pink-400 font-mono text-xs">{{ $tr['label'] }}</div>
                                                    <div class="text-[10px] text-zinc-500 mt-0.5">
                                                        ${{ number_format($tr['price'], 0, ',', '.') }}</div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @elseif(strlen($terrestrialSearch) > 1)
                                        <div
                                            class="absolute z-20 w-full mt-1 bg-[#0f0f0f] border border-amber-900/40 rounded-[10px] p-3 text-center text-xs text-zinc-500">
                                            Sin vuelos terrestres disponibles.
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @error('terrestrial_flight_id') <span
                            class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Motor de Coherencia Temporal: Individual --}}
                        @if(!empty($temporalWarnings[0]) || isset($smartSuggestions[0]))
                            <div
                                class="mt-4 p-4 rounded-[16px] border border-orange-900/40 bg-gradient-to-br from-orange-950/20 to-transparent space-y-3">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h6 class="text-[10px] font-black text-orange-400 uppercase tracking-[0.2em]">Sincronización
                                        Logística</h6>
                                </div>

                                @if(!empty($temporalWarnings[0]))
                                    <div class="space-y-2">
                                        @foreach($temporalWarnings[0] as $warning)
                                            <div class="flex gap-2 items-start">
                                                <span class="text-orange-500 mt-0.5">•</span>
                                                <p class="text-[10px] text-orange-200 leading-relaxed font-medium">
                                                    {{ $warning }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if(isset($smartSuggestions[0]))
                                    <div
                                        class="flex items-center justify-between bg-orange-600/10 border border-orange-900/30 p-2.5 rounded-[12px] mt-2">
                                        <div class="flex flex-col">
                                            <span class="text-[8px] text-zinc-500 uppercase font-bold tracking-widest mb-0.5">Noches
                                                Sugeridas</span>
                                            <span class="text-xs text-white font-black">{{ $smartSuggestions[0] }} <span
                                                    class="text-[10px] font-normal text-zinc-400">noches de espera
                                                    base</span></span>
                                        </div>
                                        <button type="button" wire:click="applySmartSuggestion(0)"
                                            class="bg-orange-600 hover:bg-orange-500 text-white px-3 py-1.5 rounded-full text-[9px] font-bold uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-lg shadow-orange-900/20">
                                            Sincronizar
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <hr class="border-zinc-800 my-2">

                        <div class="space-y-3">
                            <div class="grid grid-cols-12 gap-3 items-start">
                                <div class="col-span-9">
                                    <label class="block text-[10px] font-bold text-pink-400 mb-1 uppercase tracking-widest">
                                        Estancia en hotel
                                    </label>

                                    @if($selectedHotelLabel)
                                        <div
                                            class="flex items-center gap-2 bg-pink-950/20 border border-pink-900/50 px-3 py-2 rounded-[10px] h-[38px]">
                                            <svg class="w-3.5 h-3.5 text-pink-400 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                            <span
                                                class="text-pink-300 text-xs font-mono flex-1 truncate">{{ $selectedHotelLabel }}</span>
                                            <button type="button" wire:click="clearSelectedHotel"
                                                class="text-zinc-600 hover:text-red-400 transition-colors ml-auto shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @else
                                        <div class="relative h-[38px]">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                                <svg class="h-4 w-4 text-pink-900" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                            <input type="text" wire:model.live.debounce.300ms="hotelSearch"
                                                placeholder="Buscar hotel..."
                                                class="w-full pl-10 bg-[#050505] border border-pink-900/40 focus:border-pink-500 text-pink-100 px-3 py-2 focus:outline-none transition-colors text-sm rounded-[10px]">

                                            @if(!empty($hotelSearchResults))
                                                <div
                                                    class="absolute z-20 w-full mt-1 bg-[#0f0f0f] border border-pink-900/40 rounded-[10px] shadow-lg max-h-48 overflow-y-auto">
                                                    @foreach($hotelSearchResults as $hr)
                                                        <button type="button"
                                                            wire:click="selectHotel({{ $hr['id'] }}, '{{ addslashes($hr['label']) }}')"
                                                            class="w-full text-left px-4 py-2.5 text-sm text-zinc-300 hover:bg-pink-900/20 hover:text-white transition-colors border-b border-zinc-800/50 last:border-0">
                                                            <div class="font-bold text-pink-400 text-xs">{{ $hr['label'] }}</div>
                                                            <div class="text-[10px] text-zinc-500 mt-0.5">
                                                                ${{ number_format($hr['price'], 0, ',', '.') }}/noche</div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    @error('hotel_id') <span
                                    class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-3">
                                    <label
                                        class="block text-[10px] font-bold text-pink-400 mb-1 uppercase tracking-widest text-center truncate">
                                        Noches
                                    </label>
                                    <input type="number" wire:model.live="hotel_nights"
                                        class="w-full bg-[#050505] border border-pink-900/40 focus:border-pink-500 text-pink-100 px-2 py-2 text-sm rounded-[10px] text-center h-[38px] appearance-none"
                                        min="0" max="30" placeholder="0">
                                    @error('hotel_nights') <span
                                        class="text-red-500 text-[10px] mt-1 block text-center">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="border-zinc-800 my-2">

                        <div class="space-y-3">
                            @php
                                $pModel = $passenger_id ? \App\Models\Passenger::find($passenger_id) : null;
                                $hasPassport = $pModel && $pModel->hasValidPassport();
                                $hasTraining = $pModel && $pModel->hasValidTraining();

                                // Protocolo Compliance: Warning Crítico
                                $showSafetyWarning = $pModel && !$hasPassport && !$hasTraining && !$this->passport_management_included && !$this->training_included;
                            @endphp

                            {{-- AVISO DE SEGURIDAD CRÍTICO --}}
                            @if($showSafetyWarning)
                                <div
                                    class="p-3 bg-red-950/40 border border-red-500/50 rounded-[12px] shadow-[0_0_15px_rgba(239,68,68,0.2)]">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <p class="text-[10px] text-red-200 uppercase font-black tracking-widest leading-tight">
                                            ⚠️ AVISO DE SEGURIDAD: <span class="text-white font-normal lowercase">El pasajero no
                                                dispone de Pasaporte ni Iris Training y no se han seleccionado como servicios
                                                adicionales. De no regularizarse antes del viaje, este se cancelará
                                                automáticamente <b class="uppercase">sin derecho a reembolso</b>.</span>
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" wire:model.live="training_included"
                                    class="w-4 h-4 bg-black border-zinc-700 text-emerald-500 rounded focus:ring-emerald-500 focus:ring-offset-black">
                                <span
                                    class="text-[11px] font-bold text-white uppercase tracking-widest group-hover:text-emerald-400 transition-colors flex items-center gap-2">
                                    Incluir Iris training.
                                    @if($pModel && !$hasTraining)
                                        <span
                                            class="text-[8px] bg-emerald-950 text-emerald-500 px-1.5 py-0.5 rounded italic">Certificación
                                            requerida</span>
                                    @endif
                                </span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" wire:model.live="vip_transfer_included"
                                    class="w-4 h-4 bg-black border-zinc-700 text-amber-500 rounded focus:ring-amber-500 focus:ring-offset-black">
                                <span
                                    class="text-[11px] font-bold text-white uppercase tracking-widest group-hover:text-amber-400 transition-colors">
                                    Incluir servicio de traslados.
                                </span>
                            </label>

                            <label
                                class="flex items-center gap-3 cursor-pointer group p-2 rounded-[10px] transition-colors {{ ($pModel && !$hasPassport) ? 'bg-red-500/10 border border-red-500/30' : '' }}">
                                <input type="checkbox" wire:model.live="passport_management_included"
                                    class="w-4 h-4 bg-black border-zinc-700 text-blue-500 rounded focus:ring-blue-500 focus:ring-offset-black">
                                <span
                                    class="text-[11px] font-bold {{ ($pModel && !$hasPassport) ? 'text-red-400' : 'text-white' }} uppercase tracking-widest group-hover:text-blue-400 transition-colors">
                                    Incluir Gestión de pasaporte espacial.
                                    @if($pModel && !$hasPassport)
                                        <span
                                            class="block text-[8px] mt-0.5 font-black text-red-500 uppercase tracking-[0.15em]">•
                                            ATENCIÓN: Passport Missing</span>
                                    @endif
                                </span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" wire:model.live="refund_insurance_included"
                                    class="w-4 h-4 bg-black border-zinc-700 text-pink-500 rounded focus:ring-pink-500 focus:ring-offset-black">
                                <span
                                    class="text-[11px] font-bold text-white uppercase tracking-widest group-hover:text-pink-400 transition-colors">
                                    Incluir seguro de rembolso.
                                </span>
                            </label>
                        </div>
                    @endif
                </div>

                <!-- Footer Acción Guardar -->
                <div class="pt-6 mt-4 border-t border-zinc-800">
                    <button type="submit" @if($groupMode && count($selectedPassengers) === 0) disabled @endif class="w-full font-bold uppercase tracking-widest py-3 px-4 transition-all text-xs rounded-[10px] border
                                    {{ $isAdendaMode
    ? 'bg-violet-700 hover:bg-violet-600 text-white border-violet-600'
    : ($groupMode
        ? (count($selectedPassengers) > 0
            ? 'bg-violet-700 hover:bg-violet-600 text-white border-violet-600 shadow-[0_0_15px_rgba(139,92,246,0.3)]'
            : 'bg-zinc-800 text-zinc-600 border-zinc-700 cursor-not-allowed')
        : ($isEditing
            ? 'bg-amber-600 hover:bg-amber-500 text-white border-amber-500'
            : 'bg-white hover:bg-zinc-200 text-black border-white')) }}">
                        @if($isAdendaMode)
                            Guardar Upgrade / Adenda
                        @elseif($groupMode)
                            🚀 Crear Expedición ({{ count($selectedPassengers) }} pasajeros)
                        @elseif($isEditing)
                            Actualizar Reserva
                        @else
                            Generar Reserva
                        @endif
                    </button>
                </div>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- Modals de Livewire -->
@if($showSaveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-data
        x-init="document.body.style.overflow='hidden'" @destroyed="document.body.style.overflow=''">
        <div class="bg-[#0f0f0f] border border-zinc-700/50 rounded-[15px] max-w-sm w-full overflow-hidden shadow-[0_0_40px_rgba(168,85,247,0.1)]"
            @click.away="$wire.set('showSaveModal', false)">
            <div class="p-6 border-b border-zinc-800 flex items-start gap-4">
                <div
                    class="w-10 h-10 rounded-full {{ $isEditing ? 'bg-amber-500/10 border-amber-500/30 text-amber-400' : 'bg-zinc-800 border-zinc-600 text-white' }} flex items-center justify-center border shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-1">Confirmación de Reserva</h3>
                    <p class="text-zinc-500 text-xs leading-relaxed">
                        ¿Esta seguro de que deseas generar esta reserva?
                    </p>
                </div>
            </div>
            <div class="flex bg-[#050505] p-3 gap-3">
                <button type="button" wire:click="$set('showSaveModal', false)"
                    class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors">
                    Cancelar
                </button>
                <button type="button" wire:click="executeSave"
                    class="flex-1 py-2.5 px-4 text-xs font-bold text-white {{ $isEditing ? 'bg-amber-600 hover:bg-amber-500' : 'bg-black border hover:bg-white hover:text-black' }} rounded-[10px] transition-colors">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
@endif

@if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4" x-data
        x-init="document.body.style.overflow='hidden'" @destroyed="document.body.style.overflow=''">
        <div class="bg-[#0f0f0f] border border-red-900/50 rounded-[15px] max-w-sm w-full overflow-hidden shadow-[0_0_30px_rgba(220,38,38,0.1)]"
            @click.away="$wire.set('showDeleteModal', false)">
            <div class="p-6 border-b border-red-900/30 flex items-start gap-4">
                <div
                    class="w-10 h-10 rounded-full bg-red-950/50 border border-red-900/50 text-red-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-red-500 uppercase tracking-widest mb-1">Eliminar Reserva</h3>
                    <p class="text-zinc-500 text-xs leading-relaxed">
                        ¿Estás seguro de que deseas eliminar esta reserva? Esta acción es irreversible.
                    </p>
                </div>
            </div>
            <div class="flex bg-[#050505] p-3 gap-3">
                <button type="button" wire:click="$set('showDeleteModal', false)"
                    class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors">
                    Cancelar
                </button>
                <button type="button" wire:click="executeDelete"
                    class="flex-1 py-2.5 px-4 text-xs font-bold text-white bg-red-900 hover:bg-red-800 rounded-[10px] transition-colors border border-red-900/50">
                    Eliminar Reserva
                </button>
            </div>
        </div>
    </div>
@endif
{{-- MODAL: Centro de Mando de Expedición (Edición + Eliminación) --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@if($showGroupEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md p-4" x-data="{
                        view: 'list',
                        deleteTarget: null,
                        deleteName: '',
                        deleteScope: 'single',
                        startDelete(id, name, scope) {
                            this.deleteTarget = id;
                            this.deleteName = name;
                            this.deleteScope = scope;
                            this.view = 'confirm';
                        },
                        goBack() { this.view = 'list'; this.deleteTarget = null; }
                    }" x-init="document.body.style.overflow='hidden'" @destroyed="document.body.style.overflow=''">

        <div class="bg-[#0c0c0e] border border-violet-900/50 rounded-[18px] w-full max-w-lg overflow-hidden shadow-[0_0_60px_rgba(139,92,246,0.15)] transition-all duration-300"
            :class="view === 'confirm' ? 'border-red-900/60 shadow-[0_0_50px_rgba(220,38,38,0.15)]' : 'border-violet-900/50 shadow-[0_0_60px_rgba(139,92,246,0.15)]'"
            @click.away="$wire.set('showGroupEditModal', false)">

            {{-- ── VISTA LISTA ── --}}
            <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">

                {{-- Header --}}
                <div
                    class="p-5 border-b border-violet-900/30 flex items-center gap-3 bg-gradient-to-r from-violet-950/40 to-transparent">
                    <div
                        class="w-9 h-9 rounded-full bg-violet-900/40 border border-violet-700/50 flex items-center justify-center text-violet-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-black text-white uppercase tracking-[0.15em]">Centro de Mando de Expedición
                        </h3>
                        <p class="text-[10px] text-violet-400 uppercase tracking-widest mt-0.5">
                            {{ count($groupEditMembers) }} pasajeros en esta expedición
                        </p>
                    </div>
                    <button wire:click="$set('showGroupEditModal', false)"
                        class="text-zinc-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Lista de miembros --}}
                <div class="divide-y divide-zinc-800/60 max-h-[60vh] overflow-y-auto">
                    @foreach($groupEditMembers as $member)
                        <div class="p-4 flex items-center gap-3 hover:bg-zinc-900/30 transition-colors">
                            {{-- Avatar inicial --}}
                            <div
                                class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-800 to-indigo-900 flex items-center justify-center text-[12px] font-black text-white shrink-0 border border-violet-700/40">
                                {{ strtoupper(substr($member['passenger_name'], 0, 1)) }}
                            </div>

                            {{-- Info pasajero --}}
                            <div class="flex-1 min-w-0">
                                <div class="text-[12px] font-bold text-white uppercase tracking-wide truncate">
                                    {{ $member['passenger_name'] }}
                                </div>
                                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                    <span class="text-[9px] font-mono text-cyan-500">#{{ $member['flight_code'] }}</span>
                                    <span class="text-[9px] font-bold text-zinc-500 uppercase">{{ $member['seat_type'] }}</span>
                                    @if($member['payment_status'] === 'paid')
                                        <span
                                            class="text-[8px] font-bold text-emerald-400 bg-emerald-950/40 border border-emerald-800/50 px-1 py-0.5 rounded">PAGADO</span>
                                    @else
                                        <span
                                            class="text-[8px] font-bold text-amber-400 bg-amber-950/40 border border-amber-800/50 px-1 py-0.5 rounded">PENDIENTE</span>
                                    @endif
                                    @if($member['status'] === 'Cancelada')
                                        <span
                                            class="text-[8px] font-bold text-red-400 bg-red-950/40 border border-red-800/50 px-1 py-0.5 rounded">CANCELADA</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Precio --}}
                            <div class="text-[11px] font-black text-violet-300 shrink-0 mr-1">
                                ${{ number_format($member['total_price'], 0, ',', '.') }}
                            </div>

                            {{-- Acciones --}}
                            <div class="flex gap-1.5 shrink-0">
                                <button type="button" wire:click="editGroupMember({{ $member['id'] }})"
                                    class="px-2.5 py-1.5 bg-amber-900/30 hover:bg-amber-800/60 text-amber-400 hover:text-amber-200 text-[9px] font-bold uppercase tracking-widest rounded-[7px] border border-amber-900/50 hover:border-amber-600 transition-all flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Editar
                                </button>
                                <button type="button"
                                    @click="startDelete({{ $member['id'] }}, '{{ addslashes($member['passenger_name']) }}', 'single')"
                                    class="px-2.5 py-1.5 bg-red-950/20 hover:bg-red-900/40 text-red-500 hover:text-red-300 text-[9px] font-bold uppercase tracking-widest rounded-[7px] border border-red-900/40 hover:border-red-700 transition-all flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="p-4 border-t border-zinc-800/80 bg-black/40 flex items-center justify-between gap-3">
                    <button wire:click="$set('showGroupEditModal', false)"
                        class="px-4 py-2 text-[10px] font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[8px] border border-zinc-800 transition-colors uppercase tracking-widest">
                        Cerrar
                    </button>
                    <button @click="startDelete({{ $groupEditMembers[0]['id'] ?? 0 }}, 'la expedición completa', 'group')"
                        class="px-4 py-2 text-[10px] font-bold text-red-400 hover:text-red-200 bg-red-950/20 hover:bg-red-950/40 rounded-[8px] border border-red-900/40 hover:border-red-700 transition-all uppercase tracking-widest flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Cancelar Expedición Completa
                    </button>
                </div>
            </div>

            {{-- ── VISTA CONFIRMACIÓN ── --}}
            <div x-show="view === 'confirm'" style="display:none" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">

                {{-- Header confirmación --}}
                <div
                    class="p-5 border-b border-red-900/40 flex items-center gap-3 bg-gradient-to-r from-red-950/30 to-transparent">
                    <div
                        class="w-9 h-9 rounded-full bg-red-950/60 border border-red-900/60 flex items-center justify-center text-red-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-black text-red-400 uppercase tracking-[0.15em]">Confirmar Cancelación</h3>
                        <p class="text-[10px] text-red-600 uppercase tracking-widest mt-0.5"
                            x-text="deleteScope === 'group' ? 'Acción sobre toda la expedición' : 'Acción sobre un pasajero'">
                        </p>
                    </div>
                    <button @click="goBack()" class="text-zinc-600 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                </div>

                {{-- Cuerpo de confirmación --}}
                <div class="p-6 space-y-4">
                    {{-- Mensaje contextual --}}
                    <div x-show="deleteScope === 'single'">
                        <p class="text-zinc-300 text-sm leading-relaxed">
                            ¿Estás seguro de que deseas <span class="text-red-400 font-bold">cancelar la reserva</span> del
                            pasajero
                            <span class="text-white font-black" x-text="'«' + deleteName + '»'"></span>?
                        </p>
                        <p class="text-zinc-500 text-xs mt-2 leading-relaxed">
                            El resto de pasajeros de la expedición <span class="text-zinc-300 font-semibold">permanecerán
                                activos</span>. Si la reserva ya está pagada, quedará marcada como <span
                                class="text-amber-400 font-semibold">cancelada con reembolso pendiente</span>.
                        </p>
                    </div>
                    <div x-show="deleteScope === 'group'" style="display:none">
                        <p class="text-zinc-300 text-sm leading-relaxed">
                            ¿Estás seguro de que deseas <span class="text-red-400 font-bold">cancelar la expedición
                                completa</span>?
                        </p>
                        <div class="mt-3 bg-red-950/20 border border-red-900/40 rounded-[10px] p-3">
                            <p class="text-red-400 text-xs font-bold uppercase tracking-widest mb-1">⚠️ Esta acción afecta a
                                {{ count($groupEditMembers) }} pasajeros
                            </p>
                            <p class="text-zinc-500 text-xs leading-relaxed">
                                Todos los registros vinculados a esta expedición serán cancelados. Las reservas pagadas
                                quedarán marcadas como <span class="text-amber-400 font-semibold">reembolso
                                    pendiente</span>, y las pendientes de pago serán eliminadas.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Acciones confirmación --}}
                <div class="flex bg-[#050505] border-t border-zinc-800/60 p-4 gap-3">
                    <button type="button" @click="goBack()"
                        class="flex-1 py-2.5 px-4 text-xs font-bold text-zinc-400 hover:text-white bg-zinc-900 hover:bg-zinc-800 rounded-[10px] border border-zinc-800 transition-colors uppercase tracking-widest flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Volver
                    </button>
                    <button type="button" x-on:click="$wire.call('executeGroupDelete', deleteTarget, deleteScope)" :class="deleteScope === 'group'
                                        ? 'bg-gradient-to-r from-red-900 to-red-800 hover:from-red-800 hover:to-red-700 shadow-[0_0_15px_rgba(220,38,38,0.25)] font-black'
                                        : 'bg-red-900 hover:bg-red-800 font-bold'"
                        class="flex-1 py-2.5 px-4 text-xs text-white rounded-[10px] transition-all border border-red-800/50 uppercase tracking-widest">
                        <span
                            x-text="deleteScope === 'group' ? '⚠️ Cancelar Expedición Completa' : 'Sí, Cancelar Pasajero'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>
@endif
</div>