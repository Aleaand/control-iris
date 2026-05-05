<div class="p-6 md:p-8 space-y-6 relative obsidian-bg min-h-screen text-[var(--text-primary)]"
    x-data="{ showScrollTop: false, showForm: window.innerWidth >= 1280 }"
    @resize.window="if(window.innerWidth >= 1280) showForm = true"
    @scroll.window="showScrollTop = window.pageYOffset > 300">

    <div class="w-full max-w-[1700px] mx-auto space-y-6">

        {{-- ══ HEADER ══ --}}
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-emerald-500 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-emerald-400 tracking-tight uppercase flex items-center gap-3">
                    Gestión de Reservas
                </h2>
                <p class="text-[var(--text-secondary)] text-sm mt-1 uppercase tracking-widest font-medium">
                    Centro de control de reservas · {{ $reservations->total() }} Registradas
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 mt-4 md:mt-0">
                @if (session()->has('message'))
                    <div
                        class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div
                        class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.1)]">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ══ SEARCH & FILTERS ══ --}}
        <div class="space-y-4">
            <div class="flex flex-col lg:flex-row gap-4 items-center">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Buscar..."
                        class="tech-input block w-full pl-10 pr-4 py-3 text-xs focus:outline-none transition-all rounded-[12px]">
                </div>

                <div class="flex gap-3 w-full lg:w-auto">
                    <button wire:click="toggleSort"
                        class="flex-1 lg:flex-none bg-[var(--tech-input-bg)] border border-[var(--border-glass)] text-[var(--text-primary)] px-6 py-3 rounded-[12px] text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:bg-[var(--tech-hover-bg)] transition-all justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($sortDir === 'asc')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                            @endif
                        </svg>
                        <span>Orden: {{ $sortDir === 'asc' ? 'Recientes' : 'Antiguos' }}</span>
                    </button>
                </div>
            </div>

            {{-- Mobile Toggle --}}
            <div class="xl:hidden">
                <button @click="showForm = !showForm"
                    class="w-full py-3 bg-[var(--tech-input-bg)] border transition-all duration-300 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] flex items-center justify-center gap-3 active:scale-[0.98]
                    {{ ($isEditing || $isAdendaMode) ? 'border-emerald-500 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.15)]' : 'border-[var(--border-glass)] text-[var(--text-secondary)] hover:text-[var(--text-primary)]' }}">
                    <svg class="w-4 h-4 transition-transform duration-300" :class="showForm ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span
                        x-text="showForm ? 'Ocultar Formulario' : '{{ $isEditing ? 'Continuar Edición' : ($isAdendaMode ? 'Continuar Upgrade' : 'Nueva Reserva') }}'"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8 items-start relative">

            {{-- ══ LISTING (60% on XL) ══ --}}
            <div class="xl:col-span-3 order-2 xl:order-1 space-y-6">
                <div class="tech-card overflow-hidden border border-[var(--border-glass)] shadow-2xl">
                    <div
                        class="px-6 py-4 bg-[var(--tech-input-bg)] border-b border-[var(--border-glass)] flex justify-between items-center">
                        <h4 class="text-[10px] font-black text-[var(--text-secondary)] uppercase tracking-[0.3em]">
                            Listado de Reservas</h4>
                        <span
                            class="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                            TOTAL: {{ $reservations->total() }}
                        </span>
                    </div>

                    <div class="divide-y divide-[var(--border-glass)]">
                        @forelse($reservations as $res)
                            @php
                                $allReservationsInGroup = \App\Models\Reservation::where('booking_group_id', $res->booking_group_id)
                                    ->whereNull('deleted_at')
                                    ->get();

                                $groupCount = $allReservationsInGroup->where('is_adenda', false)
                                    ->whereNotIn('status', ['Cancelada', 'Cancelled'])
                                    ->count();

                                $adendaCount = $allReservationsInGroup->where('is_adenda', true)->count();
                                $isGroup = $groupCount > 1;

                                // Global price: sum of all active reservations (original + adendas)
                                $totalGroupPrice = $allReservationsInGroup->whereNotIn('status', ['Cancelada', 'Cancelled'])->sum('total_price');

                                // Global payment status: 'paid' only if ALL active items are paid
                                $anyPendingPayment = $allReservationsInGroup->whereNotIn('status', ['Cancelada', 'Cancelled'])
                                    ->where('payment_status', '!=', 'paid')
                                    ->count() > 0;
                                $payStatus = $anyPendingPayment ? 'pending' : 'paid';

                                // Global operation status: 'Pendiente' if any is 'Pendiente'
                                $anyPendingStatus = $allReservationsInGroup->whereNotIn('status', ['Cancelada', 'Cancelled'])
                                    ->where('status', 'Pendiente')
                                    ->count() > 0;
                                $globalStatus = $anyPendingStatus ? 'Pendiente' : $res->status;

                                $hasHotelAlert = str_contains($res->discount_note ?? '', 'ACCIÓN REQUERIDA');
                            @endphp

                            <div class="p-6 hover:bg-[var(--tech-hover-bg)] transition-all group relative overflow-hidden">
                                <div
                                    class="absolute inset-y-0 left-0 w-1 bg-emerald-500 transform scale-y-0 group-hover:scale-y-100 transition-transform duration-300">
                                </div>

                                <div class="flex flex-col md:flex-row justify-between gap-6 relative z-10">
                                    <div class="space-y-4 flex-1">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span
                                                class="text-[10px] font-mono font-black text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded border border-emerald-500/20 uppercase tracking-widest">
                                                LOC: {{ $res->id_locator }}
                                            </span>
                                            @if($isGroup)
                                                <span
                                                    class="text-[9px] font-black bg-purple-500/10 text-purple-400 border border-purple-500/20 px-2 py-1 rounded-full flex items-center gap-1.5 uppercase">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    GRUPO: {{ $groupCount }}
                                                    {{ $adendaCount > 0 ? '+' . $adendaCount . ' UP' : '' }}
                                                </span>
                                            @endif
                                            @if($res->is_adenda)
                                                <span
                                                    class="text-[9px] font-black bg-violet-500 text-white px-2 py-1 rounded-full uppercase tracking-tighter shadow-lg shadow-violet-500/30">ADENDA</span>
                                            @endif
                                        </div>

                                        <div class="space-y-1">
                                            <h4
                                                class="text-sm font-black text-[var(--text-primary)] uppercase tracking-tight group-hover:text-emerald-400 transition-colors">
                                                {{ $res->user?->name ?? 'USUARIO DESCONOCIDO' }}
                                            </h4>
                                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                                <span
                                                    class="text-[9px] text-[var(--text-secondary)] uppercase font-bold">Viajeros:</span>
                                                 @foreach($res->group->unique('passenger_id') as $member)
                                                     <span class="text-[9px] text-[var(--text-primary)] font-medium italic">
                                                         {{ $member->passenger?->full_name }}{{ !$loop->last ? ',' : '' }}
                                                     </span>
                                                 @endforeach
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div
                                                class="flex items-center gap-2 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] p-2 rounded-lg">
                                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[8px] font-black text-cyan-500 uppercase tracking-widest">Vuelo
                                                        Principal</span>
                                                    <span
                                                        class="text-[10px] font-mono text-[var(--text-primary)]">#{{ $res->spaceFlight?->flight_code ?? 'N/A' }}
                                                        → {{ $res->spaceFlight?->destination?->name ?? '???' }}</span>
                                                </div>
                                            </div>
                                            <div
                                                class="flex items-center gap-2 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] p-2 rounded-lg">
                                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[8px] font-black text-amber-500 uppercase tracking-widest">Despegue
                                                        Estimado</span>
                                                    <span
                                                        class="text-[10px] font-mono text-[var(--text-primary)]">{{ $res->spaceFlight?->departure_date?->format('d/m/Y H:i') ?? 'PDTE' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if($hasHotelAlert)
                                            <div
                                                class="bg-amber-500/10 border border-amber-500/30 text-amber-400 p-2 rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-2 animate-pulse">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z" />
                                                </svg>
                                                Acción Requerida: Reasignar Habitación
                                            </div>
                                        @endif

                                        <div class="flex flex-wrap gap-2">
                                            <span
                                                class="px-2 py-1 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded text-[8px] font-black text-[var(--text-secondary)] uppercase tracking-widest">
                                                {{ $res->seat_type }} ({{ $res->seat_number ?: 'TBD' }})
                                            </span>
                                            @if($res->logistics?->hotel)
                                                <span
                                                    class="px-2 py-1 bg-pink-500/10 border border-pink-500/30 rounded text-[8px] font-black text-pink-400 uppercase tracking-widest">
                                                    ESTANCIA: {{ $res->logistics->hotel_nights }}N
                                                </span>
                                            @endif
                                            @if($res->logistics?->terrestrialFlight)
                                                <span
                                                    class="px-2 py-1 bg-orange-500/10 border border-orange-500/30 rounded text-[8px] font-black text-orange-400 uppercase tracking-widest">
                                                    CONEXIÓN T.
                                                </span>
                                            @endif
                                             <span
                                                 class="px-2 py-1 bg-emerald-500/10 border border-emerald-500/30 rounded text-[8px] font-black text-emerald-400 uppercase tracking-widest">
                                                 STATUS: {{ $globalStatus }}
                                             </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col md:w-48 gap-3 shrink-0">
                                        {{-- Payment Widget --}}
                                        <div
                                            class="tech-card p-3 bg-[var(--tech-input-bg)] border-[var(--border-glass)] flex flex-col items-center gap-2 text-center">
                                            <span
                                                class="text-[8px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em]">Facturación
                                                Total</span>
                                            <span
                                                class="text-lg font-black text-emerald-400">{{ number_format($totalGroupPrice, 2, ',', '.') }}€</span>

                                            <div class="w-full h-px bg-[var(--border-glass)] my-1"></div>

                                            @if($payStatus === 'paid')
                                                <div
                                                    class="flex items-center gap-1.5 text-emerald-400 text-[8px] font-black uppercase tracking-widest">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    PAGO VERIFICADO
                                                </div>
                                                @if($res->stripe_receipt_url || $res->adendas->where('payment_status', 'paid')->count() > 0)
                                                    <button type="button" wire:click="openReceiptsModal({{ $res->id }})"
                                                        class="text-[8px] font-bold text-cyan-400 hover:underline uppercase tracking-widest">
                                                        GESTIONAR FACTURAS
                                                    </button>
                                                @endif
                                            @else
                                                <button wire:click="initiatePayment({{ $res->id }})"
                                                    class="w-full py-2 bg-emerald-600 text-black text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-emerald-500 transition-all shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                                                    PROCESAR PAGO
                                                </button>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <a href="{{ route('admin.reservations.ticket', $res) }}" target="_blank"
                                                class="p-2.5 flex items-center justify-center rounded-lg border border-emerald-500/30 text-emerald-600 dark:text-emerald-500 hover:bg-emerald-500 hover:text-white transition-colors"
                                                title="Ver Ticket">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                                </svg>
                                            </a>
                                            @if($payStatus === 'paid')
                                                <button type="button" wire:click="prepareAdendaMode({{ $res->id }})"
                                                    @click="showForm = true; window.scrollTo({top: 0, behavior: 'smooth'})"
                                                    class="p-2.5 flex items-center justify-center rounded-lg border border-violet-500/30 text-violet-600 dark:text-violet-500 hover:bg-violet-500 hover:text-white transition-colors"
                                                    title="Añadir Upgrade">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                            d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-9-4h18c1.104 0 2 .896 2 2v8c0 1.104-.896 2-2 2H3c-1.104 0-2-.896-2-2v-8c0-1.104.896-2 2-2z" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="button" disabled
                                                    class="p-2.5 flex items-center justify-center rounded-lg border border-zinc-500/10 text-zinc-600 opacity-20 cursor-not-allowed"
                                                    title="Upgrade Bloqueado (Requiere Pago Verificado)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                            d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-9-4h18c1.104 0 2 .896 2 2v8c0 1.104-.896 2-2 2H3c-1.104 0-2-.896-2-2v-8c0-1.104.896-2 2-2z" />
                                                    </svg>
                                                </button>
                                            @endif
                                            <button type="button" wire:click="openEditOrModal({{ $res->id }})"
                                                @click="showForm = true; window.scrollTo({top: 0, behavior: 'smooth'})"
                                                class="p-2.5 flex items-center justify-center rounded-lg border border-amber-500/30 text-amber-600 dark:text-amber-500 hover:bg-amber-500 hover:text-black transition-colors"
                                                title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="confirmDelete({{ $res->id }})"
                                                class="p-2.5 flex items-center justify-center rounded-lg border border-red-500/30 text-red-600 dark:text-red-500 hover:bg-red-500 hover:text-white transition-colors"
                                                title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-20 text-center text-[var(--text-secondary)] opacity-50">
                                <svg class="w-16 h-16 mx-auto mb-6 opacity-20" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm uppercase font-black tracking-[0.3em]">No se han detectado reservas
                                    activas</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="px-6 py-4 bg-[var(--tech-input-bg)] border-t border-[var(--border-glass)] flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] opacity-70">
                            Mostrando {{ $reservations->firstItem() ?? 0 }} - {{ $reservations->lastItem() ?? 0 }} de {{ $reservations->total() }} Registros
                        </div>
                        @if($reservations->hasPages())
                            <div class="w-full md:w-auto">
                                {{ $reservations->links('vendor.livewire.simple-tailwind') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="xl:col-span-2 order-1 xl:order-2 space-y-6" x-show="showForm"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

                <div
                    class="tech-card p-6 rounded-xl transition-all duration-500 relative overflow-hidden {{ ($isEditing || $isAdendaMode) ? 'border-emerald-500/50 shadow-[0_0_30px_rgba(16,185,129,0.1)]' : '' }}">
                    @if($isEditing || $isAdendaMode)
                        <div
                            class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500/0 via-emerald-500 to-emerald-500/0">
                        </div>
                    @endif
                    <div
                        class="flex items-center justify-between mb-6 border-b border-zinc-200 dark:border-zinc-800/50 pb-4">
                        <div class="flex items-center gap-3">
                            <h3
                                class="text-sm font-black uppercase tracking-[0.1em] flex items-center gap-2 {{ $isAdendaMode ? 'text-violet-400' : ($isEditing ? 'text-amber-400' : 'text-emerald-400') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ $isAdendaMode ? 'Upgrade' : ($isEditing ? 'Editando Reserva' : 'Nueva Reserva') }}
                            </h3>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!$isEditing && !$isAdendaMode)
                                <button type="button" wire:click="toggleGroupMode"
                                    class="text-[10px] uppercase font-mono-tech tracking-widest px-2 py-1 transition-colors border rounded-lg {{ $groupMode ? 'bg-purple-600 text-white border-purple-500' : 'text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white border-zinc-300 dark:border-zinc-700/50 hover:border-white/20' }}"
                                    style="{{ !$groupMode ? 'background: var(--tech-hover-bg)' : '' }}">
                                    {{ $groupMode ? 'MODO: Res.Grupo' : 'MODO: Res.Indiv' }}
                                </button>
                            @endif
                            @if($isEditing || $isAdendaMode)
                                <button type="button" wire:click="setCreateMode"
                                    class="text-[10px] uppercase font-mono-tech tracking-widest text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white px-2 py-1 transition-colors border border-zinc-300 dark:border-zinc-700/50 hover:border-white/20 rounded-lg"
                                    style="background: var(--tech-hover-bg)">
                                    Nueva Reserva
                                </button>
                            @endif
                        </div>
                    </div>

                    <div x-data="{ tab: @entangle('activeTab') }">
                        <div class="flex bg-[var(--tech-input-bg)] border-b border-[var(--border-glass)]">
                            <button @click="tab = 'space'"
                                :class="tab === 'space' ? 'border-b-2 border-emerald-500 text-emerald-400 bg-emerald-500/5' : 'text-[var(--text-secondary)]'"
                                class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all">
                                Logística Espacial
                            </button>
                            @if(!$groupMode)
                                <button @click="tab = 'logistic'"
                                    :class="tab === 'logistic' ? 'border-b-2 border-amber-500 text-amber-400 bg-amber-500/5' : 'text-[var(--text-secondary)]'"
                                    class="flex-1 py-4 text-[9px] font-black uppercase tracking-widest transition-all border-l border-[var(--border-glass)]">
                                    Logística Terrestre
                                </button>
                            @endif
                        </div>

                        <form wire:submit.prevent="confirmSave" class="p-6 space-y-6">

                            @if($isAdendaMode)
                                <div
                                    class="bg-violet-500/10 border border-violet-500/30 p-4 rounded-xl flex items-start gap-3">
                                    <svg class="w-5 h-5 text-violet-400 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p
                                        class="text-[9px] text-violet-300 font-bold uppercase tracking-widest leading-relaxed">
                                        Upgrade Detectado: Se generará un pago adicional por la diferencia de servicios.</p>
                                </div>
                            @endif

                            {{-- Tab: Space --}}
                            <div x-show="tab === 'space'" x-transition class="space-y-6">

                                {{-- Flight Selection --}}
                                @if(!$isAdendaMode)
                                    <div class="space-y-3">
                                        <label
                                            class="block text-[10px] font-black text-cyan-400 uppercase tracking-widest pl-1">Vuelo
                                            de Salida (Ida)</label>
                                        @if($selectedFlightLabel)
                                            <div
                                                class="flex items-center justify-between bg-cyan-500/10 border border-cyan-500/30 px-4 py-3 rounded-xl">
                                                <span
                                                    class="text-xs text-cyan-400 font-mono font-bold">{{ $selectedFlightLabel }}</span>
                                                <button type="button" wire:click="clearSelectedFlight"
                                                    class="text-cyan-600 hover:text-red-500 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                                    <svg class="h-4 w-4 text-cyan-700" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </div>
                                                <input type="text" wire:model.live.debounce.300ms="flightSearch"
                                                    placeholder="Buscar por código de vuelo o destino..."
                                                    class="tech-input w-full pl-10 pr-4 py-3 text-xs focus:outline-none transition-all rounded-xl border-cyan-500/30">

                                                @if(!empty($flightSearchResults))
                                                    <div
                                                        class="absolute z-30 w-full mt-2 bg-[var(--bg-obsidian)] border border-[var(--border-glass)] rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto no-scrollbar">
                                                        @foreach($flightSearchResults as $fr)
                                                            <button type="button"
                                                                wire:click="selectFlight({{ $fr['id'] }}, '{{ addslashes($fr['label']) }}')"
                                                                class="w-full text-left px-4 py-3 text-[10px] text-[var(--text-secondary)] hover:bg-cyan-500/10 hover:text-cyan-400 transition-all border-b border-[var(--border-glass)] last:border-0 uppercase font-black tracking-widest font-mono">
                                                                {{ $fr['label'] }} <span
                                                                    class="block opacity-50 text-[8px]">{{ number_format($fr['price'], 0, ',', '.') }}€
                                                                    base</span>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        @error('space_flight_id') <span
                                            class="text-red-500 text-[8px] font-black uppercase mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                {{-- Client & Passenger Selection --}}
                                <div class="space-y-5 border-t border-[var(--border-glass)] pt-5">
                                    <div>
                                        <label
                                            class="block text-[10px] font-black text-amber-400 uppercase tracking-widest mb-2 pl-1">Titular
                                            Responsable (Pagador)</label>
                                        @if($user_id && $selectedClientName)
                                            <div
                                                class="flex items-center justify-between bg-amber-500/10 border border-amber-500/30 px-4 py-3 rounded-xl">
                                                <span
                                                    class="text-xs text-amber-400 font-bold uppercase tracking-widest">{{ $selectedClientName }}</span>
                                                <button type="button" wire:click="clearSelectedClient"
                                                    class="text-amber-600 hover:text-red-500 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                                    <svg class="h-4 w-4 text-amber-700" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </div>
                                                <input type="text" wire:model.live.debounce.300ms="clientSearch"
                                                    placeholder="Buscar cliente..."
                                                    class="tech-input w-full pl-10 pr-4 py-3 text-xs focus:outline-none transition-all rounded-xl border-amber-500/30">

                                                @if(!empty($clientSearchResults))
                                                    <div
                                                        class="absolute z-20 w-full mt-2 bg-[var(--bg-obsidian)] border border-[var(--border-glass)] rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto no-scrollbar">
                                                        @foreach($clientSearchResults as $resClient)
                                                            <button type="button"
                                                                wire:click="selectClient({{ $resClient['id'] }}, '{{ addslashes($resClient['name']) }}', '{{ addslashes($resClient['email']) }}')"
                                                                class="w-full text-left px-4 py-3 text-[10px] text-[var(--text-secondary)] hover:bg-amber-500/10 hover:text-amber-400 transition-all border-b border-[var(--border-glass)] last:border-0 uppercase font-black tracking-widest">
                                                                {{ $resClient['name'] }} <span
                                                                    class="block opacity-50 font-mono text-[8px]">{{ $resClient['email'] }}</span>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        @error('user_id')
                                            <span class="text-red-500 text-[8px] font-black uppercase mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    @if($user_id)
                                        <div class="space-y-4">
                                            <label
                                                class="block text-[10px] font-black text-purple-400 uppercase tracking-widest pl-1">Selección
                                                de Viajeros</label>

                                            @if($groupMode)
                                                {{-- Group Selection Cards --}}
                                                <div class="space-y-2">
                                                    @foreach($clientPassengers as $cp)
                                                        @php $alreadyAdded = collect($selectedPassengers)->pluck('passenger_id')->contains($cp->id); @endphp
                                                        <button type="button"
                                                            wire:click="{{ $alreadyAdded ? 'removePassengerFromGroup(' . collect($selectedPassengers)->search(fn($p) => $p['passenger_id'] === $cp->id) . ')' : 'addPassengerToGroup(' . $cp->id . ')' }}"
                                                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl border transition-all {{ $alreadyAdded ? 'bg-purple-500/10 border-purple-500/60 shadow-lg' : 'bg-[var(--tech-input-bg)] border-[var(--border-glass)] hover:border-purple-500/30' }}">
                                                            <div class="flex items-center gap-3">
                                                                <div
                                                                    class="w-4 h-4 rounded border flex items-center justify-center {{ $alreadyAdded ? 'bg-purple-600 border-purple-500' : 'border-zinc-700 bg-black' }}">
                                                                    @if($alreadyAdded) <svg class="w-2.5 h-2.5 text-white"
                                                                        fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                            clip-rule="evenodd" />
                                                                    </svg> @endif
                                                                </div>
                                                                <div class="text-left">
                                                                    <div
                                                                        class="text-[10px] font-black text-[var(--text-primary)] uppercase">
                                                                        {{ $cp->full_name }}</div>
                                                                    <div class="text-[8px] font-mono text-[var(--text-secondary)]">
                                                                        {{ $cp->document_number }}</div>
                                                                </div>
                                                            </div>
                                                            @if($cp->isFlightReady()) <span
                                                                class="text-[8px] font-black text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20">LISTO</span>
                                                            @endif
                                                        </button>
                                                    @endforeach
                                                </div>

                                                {{-- Individual Configs --}}
                                                @foreach($selectedPassengers as $pIdx => $pData)
                                                    <div class="tech-card p-0 overflow-hidden border-purple-500/30"
                                                        x-data="{ open: false }">
                                                        <button type="button" @click="open = !open"
                                                            class="w-full flex items-center justify-between px-4 py-3 bg-purple-500/5 hover:bg-purple-500/10 transition-all text-left">
                                                            <span
                                                                class="text-[10px] font-black uppercase text-purple-300">{{ $pData['name'] }}</span>
                                                            <div class="flex items-center gap-3">
                                                                <span
                                                                    class="text-[10px] font-black text-white">{{ number_format($pData['total_price'], 0, ',', '.') }}€</span>
                                                                <svg class="w-4 h-4 text-purple-500 transition-transform"
                                                                    :class="open ? 'rotate-180' : ''" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                            </div>
                                                        </button>
                                                        <div x-show="open" x-transition
                                                            class="p-4 space-y-4 border-t border-purple-500/20">
                                                            <div class="grid grid-cols-2 gap-2">
                                                                @foreach(['nova' => 'NOVA', 'supernova' => 'SUPERNOVA'] as $val => $lbl)
                                                                    <button type="button"
                                                                        wire:click="updatePassengerService({{ $pIdx }}, 'seat_type', '{{ $val }}')"
                                                                        class="py-2 text-[8px] font-black uppercase rounded-lg border transition-all {{ $pData['seat_type'] === $val ? 'bg-cyan-600 text-white border-cyan-500' : 'bg-[var(--tech-input-bg)] border-[var(--border-glass)] text-[var(--text-secondary)]' }}">
                                                                        {{ $lbl }}
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                            <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                                                                @foreach(['training_included' => 'Training', 'passport_management_included' => 'Pasaporte', 'refund_insurance_included' => 'Seguro', 'vip_transfer_included' => 'Traslado'] as $key => $lbl)
                                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                                        <input type="checkbox"
                                                                            wire:change="updatePassengerService({{ $pIdx }}, '{{ $key }}', $event.target.checked)"
                                                                            {{ $pData[$key] ? 'checked' : '' }}
                                                                            class="w-3.5 h-3.5 bg-black border-zinc-700 text-emerald-500 rounded">
                                                                        <span
                                                                            class="text-[9px] font-black text-[var(--text-secondary)] uppercase">{{ $lbl }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>

                                                            {{-- Hotel Selection Individual --}}
                                                            <div class="space-y-2">
                                                                <label
                                                                    class="text-[8px] font-black text-pink-500 uppercase tracking-widest pl-1">Hotel
                                                                    Destino</label>
                                                                <select
                                                                    wire:change="selectPassengerHotel({{ $pIdx }}, $event.target.value)"
                                                                    class="tech-input w-full px-3 py-2 text-[10px] rounded-lg bg-black">
                                                                    <option value="">SIN ALOJAMIENTO</option>
                                                                    @foreach($hotels as $h)
                                                                        <option value="{{ $h->id }}" {{ ($pData['hotel_id'] ?? null) == $h->id ? 'selected' : '' }}>{{ $h->name }}
                                                                            ({{ number_format($h->price_per_night, 0, ',', '.') }}€/N)</option>
                                                                    @endforeach
                                                                </select>
                                                                @if($pData['hotel_id'] ?? false)
                                                                    <div class="flex items-center gap-3">
                                                                        <span
                                                                            class="text-[8px] font-black text-[var(--text-secondary)] uppercase">Noches:</span>
                                                                        <input type="number" min="1" max="30"
                                                                            wire:change="updatePassengerHotelNights({{ $pIdx }}, $event.target.value)"
                                                                            value="{{ $pData['hotel_nights'] ?? 0 }}"
                                                                            class="tech-input w-20 px-2 py-1 text-[10px] rounded-lg">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @error('selectedPassengers')
                                                    <span class="text-red-500 text-[8px] font-black uppercase mt-2 block text-center">{{ $message }}</span>
                                                @enderror
                                            @else
                                                {{-- Individual Passenger Select --}}
                                                @if($passenger_id && $selectedPassengerName)
                                                        <div class="flex items-center justify-between bg-cyan-500/10 border border-cyan-500/30 px-4 py-3 rounded-xl">
                                                            <div class="flex flex-col">
                                                                <span class="text-xs text-cyan-400 font-black uppercase tracking-widest">{{ $selectedPassengerName }}</span>
                                                                <span class="text-[8px] font-mono text-cyan-600 uppercase">{{ \App\Models\Passenger::find($passenger_id)?->document_number }}</span>
                                                            </div>
                                                            @if(!$isAdendaMode)
                                                                <button type="button" wire:click="clearSelectedPassenger" class="text-cyan-600 hover:text-red-500 transition-all">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                @else
                                                    <select wire:model.live="passenger_id"
                                                        class="tech-input w-full px-4 py-3 text-xs focus:outline-none rounded-xl bg-black border-cyan-500/30">
                                                        <option value="">-- SELECCIONAR VIAJERO --</option>
                                                        @foreach($clientPassengers as $cp)
                                                            <option value="{{ $cp->id }}">{{ $cp->full_name }}
                                                                ({{ $cp->document_number }})</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            @endif
                                            @error('passenger_id') <span
                                                class="text-red-500 text-[8px] font-black uppercase mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>

                                {{-- Return Flight Toggle --}}
                                <div class="pt-6 border-t border-[var(--border-glass)]">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <div class="relative">
                                            <input type="checkbox" wire:model.live="hasReturnFlight"
                                                class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded-full peer peer-checked:bg-emerald-600 transition-all">
                                            </div>
                                            <div
                                                class="absolute left-1 top-1 w-4 h-4 bg-zinc-600 rounded-full peer-checked:translate-x-5 peer-checked:bg-white transition-all shadow-lg">
                                            </div>
                                        </div>
                                        <span
                                            class="text-[10px] font-black text-[var(--text-secondary)] group-hover:text-emerald-400 uppercase tracking-widest transition-colors">¿Incluir
                                            Vuelo de Regreso?</span>
                                    </label>

                                    @if($hasReturnFlight)
                                        <div class="mt-5 space-y-4 animate-slide-in">
                                            <label
                                                class="block text-[9px] font-black text-purple-400 uppercase tracking-widest pl-1">Vuelo
                                                de Retorno</label>
                                            @if($selectedReturnFlightLabel)
                                                <div
                                                    class="flex items-center justify-between bg-purple-500/10 border border-purple-500/30 px-4 py-3 rounded-xl">
                                                    <span
                                                        class="text-[10px] text-purple-400 font-mono font-bold">{{ $selectedReturnFlightLabel }}</span>
                                                    <button type="button" wire:click="clearSelectedReturnFlight"
                                                        class="text-purple-600 hover:text-red-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @else
                                                <div class="relative">
                                                    <input type="text" wire:model.live.debounce.300ms="returnFlightSearch"
                                                        placeholder="Buscar vuelo de regreso..."
                                                        class="tech-input w-full px-4 py-3 text-xs focus:outline-none rounded-xl border-purple-500/30">
                                                    @if(!empty($returnFlightSearchResults))
                                                        <div
                                                            class="absolute z-30 w-full mt-2 bg-[var(--bg-obsidian)] border border-[var(--border-glass)] rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto no-scrollbar">
                                                            @foreach($returnFlightSearchResults as $rf)
                                                                <button type="button"
                                                                    wire:click="selectReturnFlight({{ $rf['id'] }}, '{{ addslashes($rf['label']) }}')"
                                                                    class="w-full text-left px-4 py-3 text-[10px] text-purple-300 hover:bg-purple-500/10 transition-all border-b border-[var(--border-glass)] last:border-0 font-mono uppercase font-black">
                                                                    {{ $rf['label'] }} <span
                                                                        class="block opacity-50 text-[8px]">{{ number_format($rf['price'], 0, ',', '.') }}€
                                                                        base</span>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Individual Global Config (Mode NOT Group) --}}
                                @if(!$groupMode)
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label
                                                class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest">Estado</label>
                                            <select wire:model="status"
                                                class="tech-input w-full px-3 py-2.5 text-[10px] font-black uppercase rounded-xl bg-black">
                                                <option value="Pendiente">PENDIENTE</option>
                                                <option value="Confirmada">CONFIRMADA</option>
                                                <option value="Cancelada">CANCELADA</option>
                                            </select>
                                        </div>
                                        <div class="space-y-2">
                                            <label
                                                class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest">Clase
                                                Asiento</label>
                                            <select wire:model.live="seat_type"
                                                class="tech-input w-full px-3 py-2.5 text-[10px] font-black uppercase rounded-xl bg-black">
                                                <option value="nova">NOVA</option>
                                                <option value="supernova">SUPERNOVA</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest">Asiento
                                            (Opcional)</label>
                                        <input type="number" wire:model="seat_number"
                                            class="tech-input w-full px-4 py-2.5 text-xs font-mono rounded-xl">
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <label
                                            class="block text-[9px] font-black text-[var(--text-secondary)] uppercase tracking-widest">Estado
                                            Global Grupo</label>
                                        <select wire:model="status"
                                            class="tech-input w-full px-3 py-2.5 text-[10px] font-black uppercase rounded-xl bg-black">
                                            <option value="Pendiente">PENDIENTE</option>
                                            <option value="Cancelada">CANCELADA</option>
                                        </select>
                                    </div>
                                @endif
                            </div>

                            {{-- Tab: Logistics (Individual Mode Only) --}}
                            <div x-show="tab === 'logistic'" x-transition class="space-y-6">
                                <div class="space-y-4">
                                    <label
                                        class="block text-[10px] font-black text-pink-400 uppercase tracking-widest pl-1">Vuelo
                                        Terrestre de Conexión</label>
                                    @if($selectedTerrestrialLabel)
                                        <div
                                            class="flex items-center justify-between bg-pink-500/10 border border-pink-500/30 px-4 py-3 rounded-xl">
                                            <span
                                                class="text-xs text-pink-400 font-mono font-bold">{{ $selectedTerrestrialLabel }}</span>
                                            <button type="button" wire:click="clearSelectedTerrestrialFlight"
                                                class="text-pink-600 hover:text-red-500 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @else
                                        <div class="relative">
                                            <input type="text" wire:model.live.debounce.300ms="terrestrialSearch"
                                                placeholder="Buscar por ciudad..."
                                                class="tech-input w-full px-4 py-3 text-xs focus:outline-none rounded-xl border-pink-500/30">
                                            @if(!empty($terrestrialSearchResults))
                                                <div
                                                    class="absolute z-20 w-full mt-2 bg-[var(--bg-obsidian)] border border-[var(--border-glass)] rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto no-scrollbar">
                                                    @foreach($terrestrialSearchResults as $tr)
                                                        <button type="button"
                                                            wire:click="selectTerrestrialFlight({{ $tr['id'] }}, '{{ addslashes($tr['label']) }}')"
                                                            class="w-full text-left px-4 py-3 text-[10px] text-pink-300 hover:bg-pink-500/10 transition-all border-b border-[var(--border-glass)] last:border-0 uppercase font-black font-mono">
                                                            {{ $tr['label'] }} <span
                                                                class="block opacity-50 text-[8px]">{{ number_format($tr['price'], 0, ',', '.') }}€</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Hotel Selection Individual --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-[var(--border-glass)]">
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-pink-400 uppercase tracking-widest pl-1">Hotel Destino</label>
                                        <select wire:model.live="hotel_id" class="tech-input w-full px-3 py-2.5 text-[10px] font-black uppercase rounded-xl bg-black">
                                            <option value="">SIN ALOJAMIENTO</option>
                                            @foreach($hotels as $h)
                                                <option value="{{ $h->id }}">{{ $h->name }} ({{ $h->location?->name ?? '?' }}) — {{ number_format($h->price_per_night, 0, ',', '.') }}€/noche</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-pink-400 uppercase tracking-widest pl-1">Noches Estancia</label>
                                        <input type="number" wire:model.live="hotel_nights" min="0" class="tech-input w-full px-4 py-2 text-[10px] rounded-xl bg-black">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 pt-4 border-t border-[var(--border-glass)]">
                                    @foreach(['training_included' => ['Iris Training', 'emerald'], 'passport_management_included' => ['Gestión Pasaporte', 'blue'], 'refund_insurance_included' => ['Seguro Reembolso', 'indigo'], 'vip_transfer_included' => ['VIP Transfer', 'amber']] as $key => $meta)
                                        <label
                                            class="flex items-center justify-between p-3 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded-xl cursor-pointer hover:border-{{ $meta[1] }}-500/30 transition-all">
                                            <span
                                                class="text-[10px] font-black text-[var(--text-secondary)] uppercase tracking-widest">{{ $meta[0] }}</span>
                                            <input type="checkbox" wire:model.live="{{ $key }}"
                                                class="w-5 h-5 bg-black border-zinc-700 text-{{ $meta[1] }}-500 rounded">
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Price Panel --}}
                            <div
                                class="tech-card p-0 overflow-hidden border-emerald-500/40 bg-[var(--bg-panel)] shadow-2xl animate-fade-in">
                                <div
                                    class="px-5 py-3 border-b border-[var(--border-glass)] flex items-center justify-between bg-emerald-500/10">
                                    <span
                                        class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.3em]">Resumen
                                        de Costes</span>
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <div class="p-5 space-y-3">
                                    @if(!empty($priceBreakdown) && $priceBreakdown['space'] > 0)
                                        <div class="space-y-2 text-[10px] font-mono">
                                            <div class="flex justify-between text-cyan-400">
                                                <span>Vuelo Espacial</span>
                                                <span>{{ number_format($priceBreakdown['space'], 2, ',', '.') }}€</span>
                                            </div>
                                            @foreach(['hotel' => 'Alojamiento Destino', 'terrestrial' => 'Conexión Terrestre', 'training' => 'Certificación Iris', 'passport' => 'Protocolo Pasaporte', 'vip' => 'Logística VIP', 'insurance' => 'Protección Reembolso'] as $key => $lbl)
                                                @if(($priceBreakdown[$key] ?? 0) > 0)
                                                    <div class="flex justify-between text-[var(--text-secondary)]">
                                                        <span>{{ $lbl }}</span>
                                                        <span>{{ number_format($priceBreakdown[$key], 2, ',', '.') }}€</span>
                                                    </div>
                                                @endif
                                            @endforeach

                                            <div class="border-t border-[var(--border-glass)] pt-2 mt-2">
                                                <div class="flex justify-between text-[var(--text-secondary)] opacity-60">
                                                    <span>Subtotal Neto</span>
                                                    <span>{{ number_format($priceBreakdown['subtotal'], 2, ',', '.') }}€</span>
                                                </div>
                                                @if($priceBreakdown['discount_pct'] > 0)
                                                    <div class="flex justify-between text-emerald-500 font-black">
                                                        <span>Descuento Iris Training
                                                            (-{{ $priceBreakdown['discount_pct'] }}%)</span>
                                                        <span>−{{ number_format($priceBreakdown['discount_amt'], 2, ',', '.') }}€</span>
                                                    </div>
                                                @endif

                                                @if($isAdendaMode && ($priceBreakdown['paid_amount'] ?? 0) > 0)
                                                    <div class="flex justify-between text-violet-400 font-black">
                                                        <span>Importe ya Pagado</span>
                                                        <span>−{{ number_format($priceBreakdown['paid_amount'], 2, ',', '.') }}€</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="pt-4 border-t border-emerald-500/30 flex justify-between items-end">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[8px] font-black text-emerald-600 uppercase tracking-widest">Total
                                                    a Pagar</span>
                                                <span
                                                    class="text-2xl font-black text-emerald-400 leading-none">{{ number_format($groupMode ? $groupTotal : ($total_price + $return_total_price), 2, ',', '.') }}€</span>
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="py-8 text-center text-[var(--text-secondary)] opacity-40 uppercase font-black text-[9px] tracking-widest">
                                            Esperando...</div>
                                    @endif
                                </div>
                            </div>

                            @if(!empty($priceBreakdown) && $priceBreakdown['space'] > 0 && $priceBreakdown['insurance'] <= 0)
                                <div
                                    class="bg-red-500/10 border border-red-500/30 p-4 rounded-xl flex items-start gap-3 animate-pulse">
                                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-[8px] text-red-400 font-black uppercase tracking-widest leading-relaxed">
                                        ALERTA: Sin Seguro de Reembolso. En caso de cancelación por causas ajenas a Iris
                                        Aerospace, no se procederá a la devolución de fondos.</p>
                                </div>
                            @endif

                            {{-- Footer Buttons --}}
                            <div class="pt-6 border-t border-[var(--border-glass)] flex flex-col gap-3">
                                <button type="submit"
                                    class="w-full py-4 text-[10px] font-black uppercase tracking-[0.3em] transition-all rounded-xl border-2 shadow-2xl
                                    {{ $isAdendaMode ? 'bg-violet-600 border-violet-500 text-white shadow-violet-500/20' : ($isEditing ? 'bg-amber-600 border-amber-500 text-black shadow-amber-500/20' : 'bg-emerald-600 border-emerald-500 text-black shadow-emerald-500/20') }}">
                                    {{ $isAdendaMode ? 'Registrar Upgrade' : ($isEditing ? "Actualizar Reserva" : "Registrar Nueva Reserva") }}
                                </button>
                                <button type="button" @click="showForm = false"
                                    class="xl:hidden w-full py-3 text-[10px] font-black uppercase tracking-widest text-[var(--text-secondary)] border border-[var(--border-glass)] rounded-xl">
                                    Cerrar Centro de Control
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div x-data="{ 
        lockScroll: @entangle('showSaveModal') || @entangle('showDeleteModal')
    }"
        x-effect="lockScroll ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">

        {{-- Modal Guardar Reserva (Rediseñado) --}}
        @if($showSaveModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                    wire:click="$set('showSaveModal', false)"></div>

                <div class="relative border border-emerald-500/30 rounded-[32px] max-w-lg w-full overflow-hidden shadow-[0_0_80px_rgba(16,185,129,0.15)] bg-[#050505] animate-tech">
                    {{-- Glow effect --}}
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-[80px]"></div>
                    
                    <div class="p-8 border-b border-[var(--border-glass)] relative">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 rounded-2xl {{ $isEditing ? 'bg-amber-500/10 border border-amber-500/30 text-amber-500' : 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400' }} flex items-center justify-center shrink-0 shadow-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white uppercase tracking-[0.2em]">
                                    {{ $isEditing ? 'Validar Cambios' : 'Confirmar Misión' }}
                                </h3>
                                <p class="text-[10px] text-emerald-500/70 font-mono uppercase tracking-widest mt-1">
                                    Protocolo de Verificación de Datos
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="space-y-1">
                                    <span class="text-[8px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em]">Responsable</span>
                                    <p class="text-xs font-bold text-white uppercase">{{ $selectedClientName ?: 'No especificado' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[8px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em]">Destino Operativo</span>
                                    <p class="text-xs font-bold text-cyan-400 uppercase tracking-tight">{{ $selectedFlightLabel ?: 'Sin asignar' }}</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="space-y-1">
                                    <span class="text-[8px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em]">Viajeros Registrados</span>
                                    <div class="flex flex-wrap gap-1">
                                        @if($groupMode)
                                            @foreach($selectedPassengers as $p)
                                                <span class="px-2 py-0.5 bg-white/5 border border-white/10 rounded text-[9px] font-medium text-zinc-300">{{ $p['name'] }}</span>
                                            @endforeach
                                        @else
                                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 rounded text-[9px] font-medium text-zinc-300">{{ $selectedPassengerName }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[8px] font-black text-[var(--text-secondary)] uppercase tracking-[0.2em]">Configuración</span>
                                    <p class="text-xs font-bold text-amber-500 uppercase">{{ $seat_type }} ({{ $seat_number ?: 'TBD' }})</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-[var(--tech-input-bg)] border border-[var(--border-glass)] p-5 rounded-2xl flex justify-between items-end">
                            <div class="space-y-1">
                                <span class="text-[8px] font-black text-emerald-500 uppercase tracking-widest">Facturación Total</span>
                                <p class="text-xs text-[var(--text-secondary)] font-medium uppercase italic">Impuestos e Iris Fees incluidos</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black text-emerald-400 font-mono">{{ number_format($groupMode ? $groupTotal : ($total_price + $return_total_price), 2, ',', '.') }}€</span>
                            </div>
                        </div>

                        <p class="text-[9px] text-[var(--text-secondary)] leading-relaxed italic opacity-70">
                            * Al confirmar, se emitirá el localizador de reserva y se bloquearán los activos logísticos seleccionados. Esta operación quedará registrada en el dossier administrativo.
                        </p>
                    </div>

                    <div class="p-6 bg-white/5 border-t border-[var(--border-glass)] flex gap-4">
                        <button type="button" wire:click="$set('showSaveModal', false)"
                            class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest rounded-2xl border border-[var(--border-glass)] text-zinc-400 hover:bg-white/5 hover:text-white transition-all">
                            Cancelar
                        </button>
                        <button type="button" wire:click="executeSave"
                            class="flex-1 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-black {{ $isEditing ? 'bg-amber-500 hover:bg-amber-400 shadow-[0_0_30px_rgba(245,158,11,0.2)]' : 'bg-emerald-600 hover:bg-emerald-500 shadow-[0_0_30px_rgba(16,185,129,0.2)]' }} rounded-2xl transition-all active:scale-[0.98]">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Eliminar Reserva --}}
        @if($showDeleteModal)
            <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/80 backdrop-blur-md"
                    wire:click="$set('showDeleteModal', false)"></div>

                <div class="relative border border-rose-500/30 rounded-[32px] max-w-sm w-full overflow-hidden shadow-[0_0_80px_rgba(244,63,94,0.15)] bg-[#050505] animate-tech">
                    {{-- Glow effect --}}
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-rose-500/10 rounded-full blur-[80px]"></div>

                    <div class="p-8 border-b border-[var(--border-glass)] relative flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-500 flex items-center justify-center shrink-0 mb-6 shadow-lg shadow-rose-500/10">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-white uppercase tracking-[0.2em] mb-2">
                            Alerta Crítica
                        </h3>
                        <p class="text-[10px] text-rose-400 font-mono uppercase tracking-widest">
                            Protocolo de Baja en Curso
                        </p>
                    </div>

                    <div class="p-8 space-y-6 text-center">
                        <p class="text-[var(--text-secondary)] text-xs leading-relaxed font-medium">
                            Se procederá a la <span class="text-white font-black">CANCELACIÓN IRREVERSIBLE</span> de la reserva asignada a <span class="text-rose-400 font-bold uppercase">{{ $deleteImpactInfo['name'] ?? 'el sistema' }}</span>.
                        </p>
                        
                        <div class="bg-rose-500/5 border border-rose-500/20 p-4 rounded-xl">
                            <span class="text-[8px] font-black text-rose-500 uppercase tracking-[0.2em] block mb-2 text-center">Impacto de la Acción</span>
                            <p class="text-[9px] text-rose-400/80 leading-relaxed uppercase font-bold tracking-tighter">
                                Los asientos quedarán liberados inmediatamente. El estado de la transacción pasará a "REFUNDED" o será eliminada según el estado del pago.
                            </p>
                        </div>
                    </div>

                    <div class="p-6 bg-white/5 border-t border-[var(--border-glass)] flex flex-col gap-3">
                        <button type="button" wire:click="executeDelete"
                            class="w-full py-4 text-[10px] font-black uppercase tracking-[0.2em] text-white bg-rose-600 hover:bg-rose-500 shadow-[0_0_30px_rgba(225,29,72,0.2)] rounded-2xl transition-all active:scale-[0.98]">
                            Confirmar Cancelación
                        </button>
                        <button type="button" wire:click="$set('showDeleteModal', false)"
                            class="w-full py-3 text-[10px] font-black uppercase tracking-widest rounded-xl text-zinc-500 hover:text-white transition-all">
                            Abortar Proceso
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Facturas (Recibos Stripe) --}}
    @if($showReceiptsModal)
        <div class="fixed inset-0 z-[500] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-md"
                wire:click="$set('showReceiptsModal', false)"></div>

            <div class="relative border border-emerald-500/30 rounded-[32px] max-w-md w-full overflow-hidden shadow-[0_0_80px_rgba(16,185,129,0.15)] bg-[#050505] animate-tech">
                <div class="p-8 border-b border-[var(--border-glass)]">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white uppercase tracking-widest">Historial de Facturación</h3>
                            <p class="text-[9px] text-emerald-500/60 font-mono uppercase">Documentos fiscales verificados</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-3 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    @forelse($receiptsList as $receipt)
                        <div class="bg-white/5 border border-[var(--border-glass)] p-4 rounded-2xl flex items-center justify-between group hover:border-emerald-500/30 transition-all">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-black {{ $receipt['type'] === 'Original' ? 'text-emerald-400' : 'text-violet-400' }} uppercase tracking-widest">{{ $receipt['type'] }}</span>
                                    <span class="text-[8px] text-zinc-500 font-mono">{{ $receipt['date'] }}</span>
                                </div>
                                <p class="text-xs font-bold text-white">{{ number_format($receipt['amount'], 2, ',', '.') }}€</p>
                            </div>
                            <a href="{{ $receipt['url'] }}" target="_blank"
                                class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl hover:bg-emerald-500 transition-all hover:text-black">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <p class="text-[10px] text-zinc-500 uppercase font-black tracking-widest">No se han encontrado recibos pagados</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-6 border-t border-[var(--border-glass)]">
                    <button type="button" wire:click="$set('showReceiptsModal', false)"
                        class="w-full py-3 text-[10px] font-black uppercase tracking-widest rounded-xl text-zinc-400 hover:text-white transition-all">
                        Cerrar Historial
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Scroll to Top --}}
    <button x-show="showScrollTop" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="fixed bottom-6 right-6 z-[90] w-12 h-12 rounded-full bg-emerald-500 text-black flex items-center justify-center shadow-[0_0_20px_rgba(16,185,129,0.5)] border border-emerald-400/50 transition-transform active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
</div>