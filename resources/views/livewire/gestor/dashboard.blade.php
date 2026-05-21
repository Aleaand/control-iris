<x-slot name="title">Dashboard — Iris Gestor</x-slot>

<div class="p-6 space-y-6">

    {{-- ══ HEADER ══ --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">
                Centro de Mando
            </h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">
                Bienvenido/a, {{ auth()->user()->name }} · {{ now()->format('d M Y') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($urgentFlights > 0)
                <div
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-500/10 border border-rose-500/30 animate-pulse">
                    <div class="w-1.5 h-1.5 rounded-full bg-rose-400"></div>
                    <span class="font-mono-tech text-[9px] text-rose-400 uppercase">{{ $urgentFlights }} vuelo(s) en
                        72h</span>
                </div>
            @endif
            @if($pendingTasks > 0)
                <a href="{{ route('gestor.tasks') }}" wire:navigate
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-orange-500/10 border border-orange-500/30 hover:bg-orange-500/20 transition-colors">
                    <div class="w-1.5 h-1.5 rounded-full bg-orange-400"></div>
                    <span class="font-mono-tech text-[9px] text-orange-400 uppercase">{{ $pendingTasks }} Tarea(s)</span>
                </a>
            @endif
        </div>
    </div>

    {{-- ══ KPI GRID ══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

        {{-- Clientes --}}
        <a href="{{ route('gestor.clients') }}" wire:navigate
            class="tech-card p-4 rounded-xl hover:border-cyan-500/30 transition-all duration-200 group">
            <div class="flex items-center justify-between mb-3">
                <div
                    class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-400 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black" style="color: var(--text-primary)">{{ $totalClients }}</p>
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mt-0.5">Clientes</p>
        </a>

        {{-- Pasajeros --}}
        <a href="{{ route('gestor.clients') }}" wire:navigate
            class="tech-card p-4 rounded-xl hover:border-emerald-500/30 transition-all duration-200 group">
            <div class="flex items-center justify-between mb-3">
                <div
                    class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4" />
                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black" style="color: var(--text-primary)">{{ $totalPassengers }}</p>
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mt-0.5">Pasajeros</p>
        </a>

        {{-- Reservas Activas --}}
        <a href="{{ route('gestor.reservations') }}" wire:navigate
            class="tech-card p-4 rounded-xl hover:border-violet-500/30 transition-all duration-200 group">
            <div class="flex items-center justify-between mb-3">
                <div
                    class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center text-violet-400 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                        </path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black" style="color: var(--text-primary)">{{ $activeReservations }}</p>
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mt-0.5">Reservas Activas</p>
        </a>

        {{-- Pagos Pendientes --}}
        <a href="{{ route('gestor.payments') }}" wire:navigate
            class="tech-card p-4 rounded-xl transition-all duration-200 group {{ $pendingPayments > 0 ? 'border-amber-500/30 hover:border-amber-500/50' : 'hover:border-white/10' }}">
            <div class="flex items-center justify-between mb-3">
                <div
                    class="w-8 h-8 rounded-lg {{ $pendingPayments > 0 ? 'bg-amber-500/10 text-amber-400' : 'bg-zinc-700/20 text-zinc-500' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black {{ $pendingPayments > 0 ? 'text-amber-400' : '' }}"
                style="{{ $pendingPayments === 0 ? 'color: var(--text-primary)' : '' }}">{{ $pendingPayments }}</p>
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mt-0.5">Pagos Pendientes</p>
        </a>

        {{-- Vuelos en 72h --}}
        <a href="{{ route('gestor.documentation') }}" wire:navigate
            class="tech-card p-4 rounded-xl transition-all duration-200 group {{ $urgentFlights > 0 ? 'border-rose-500/30' : 'hover:border-white/10' }}">
            <div class="flex items-center justify-between mb-3">
                <div
                    class="w-8 h-8 rounded-lg {{ $urgentFlights > 0 ? 'bg-rose-500/10 text-rose-400' : 'bg-zinc-700/20 text-zinc-500' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black {{ $urgentFlights > 0 ? 'text-rose-400' : '' }}"
                style="{{ $urgentFlights === 0 ? 'color: var(--text-primary)' : '' }}">{{ $urgentFlights }}</p>
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mt-0.5">Vuelos en 72h</p>
        </a>

        {{-- Tareas --}}
        <a href="{{ route('gestor.tasks') }}" wire:navigate
            class="tech-card p-4 rounded-xl transition-all duration-200 group {{ $pendingTasks > 0 ? 'border-orange-500/30' : 'hover:border-white/10' }}">
            <div class="flex items-center justify-between mb-3">
                <div
                    class="w-8 h-8 rounded-lg {{ $pendingTasks > 0 ? 'bg-orange-500/10 text-orange-400' : 'bg-zinc-700/20 text-zinc-500' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black {{ $pendingTasks > 0 ? 'text-orange-400' : '' }}"
                style="{{ $pendingTasks === 0 ? 'color: var(--text-primary)' : '' }}">{{ $pendingTasks }}</p>
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mt-0.5">Tareas</p>
        </a>

    </div>

    {{-- ══ MAIN CONTENT GRID ══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Reservas Recientes --}}
        <div class="xl:col-span-2 tech-card rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-mono-tech text-[11px] uppercase tracking-widest text-zinc-400">Últimas Reservas</h2>
                <a href="{{ route('gestor.reservations') }}" wire:navigate
                    class="font-mono-tech text-[9px] text-emerald-400 hover:text-emerald-300 uppercase tracking-wider transition-colors">
                    Ver todas →
                </a>
            </div>
            @if($recentReservations->isEmpty())
                <div class="flex items-center justify-center h-24 text-zinc-600 text-sm">Sin reservas recientes</div>
            @else
                <div class="space-y-2">
                    @foreach($recentReservations as $res)
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-white/3 transition-colors">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold
                                                {{ $res->seat_type === 'supernova' ? 'bg-violet-500/10 text-violet-400' : 'bg-cyan-500/10 text-cyan-400' }}">
                                {{ $res->seat_type === 'supernova' ? 'SN' : 'NV' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold truncate" style="color: var(--text-primary)">
                                    {{ $res->passenger?->full_name ?? $res->user?->name ?? 'N/A' }}
                                </p>
                                <p class="font-mono-tech text-[9px] text-zinc-500">
                                    {{ $res->spaceFlight?->destination?->name ?? '—' }} ·
                                    {{ $res->spaceFlight?->departure_date?->format('d/m/Y') ?? '—' }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span
                                    class="font-mono-tech text-[9px] px-2 py-0.5 rounded-full
                                                    {{ $res->payment_status === 'paid' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                    {{ $res->payment_status === 'paid' ? 'Pagado' : 'Pendiente' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-6">

            @if($urgentTasks->isNotEmpty())
                <div class="tech-card rounded-xl p-5" style="border-color: rgba(249,115,22,0.2)">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></div>
                        <h2 class="font-mono-tech text-[11px] uppercase tracking-widest text-orange-400">Tareas Urgentes
                        </h2>
                    </div>
                    <div class="space-y-2">
                        @foreach($urgentTasks as $task)
                            <div class="px-3 py-2.5 rounded-lg bg-orange-500/5 border border-orange-500/10">
                                <p class="text-xs font-semibold" style="color: var(--text-primary)">{{ $task->title }}</p>
                                <p class="font-mono-tech text-[9px] text-orange-400/70 mt-0.5 uppercase">{{ $task->status }}</p>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('gestor.tasks') }}" wire:navigate
                        class="mt-3 block text-center font-mono-tech text-[9px] text-orange-400 hover:text-orange-300 uppercase tracking-wider transition-colors">
                        Ver bandeja →
                    </a>
                </div>
            @endif

            {{-- Próximos vuelos --}}
            <div class="tech-card rounded-xl p-5">
                <h2 class="font-mono-tech text-[11px] uppercase tracking-widest text-zinc-400 mb-4">Próximos Vuelos</h2>
                @if($upcomingFlights->isEmpty())
                    <div class="flex items-center justify-center h-16 text-zinc-600 text-xs">Sin vuelos próximos</div>
                @else
                    <div class="space-y-2">
                        @foreach($upcomingFlights as $res)
                            @php
                                $daysLeft = (int) now()->diffInDays($res->spaceFlight?->departure_date, false);
                                $isUrgent = $daysLeft <= 3;
                            @endphp
                            <div
                                class="flex items-center gap-2 px-2 py-2 rounded-lg {{ $isUrgent ? 'bg-rose-500/5 border border-rose-500/10' : '' }}">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold truncate" style="color: var(--text-primary)">
                                        {{ $res->passenger?->full_name ?? $res->user?->name ?? 'N/A' }}
                                    </p>
                                    <p class="font-mono-tech text-[9px] {{ $isUrgent ? 'text-rose-400' : 'text-zinc-500' }}">
                                        {{ $res->spaceFlight?->destination?->name ?? '—' }} ·
                                        {{ $res->spaceFlight?->departure_date?->format('d/m/Y') ?? '—' }}
                                    </p>
                                </div>
                                @if($isUrgent)
                                    <span
                                        class="font-mono-tech text-[8px] text-rose-400 bg-rose-500/10 px-1.5 py-0.5 rounded">{{ $daysLeft }}d</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>