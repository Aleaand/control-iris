<div class="p-6 md:p-8 space-y-6 relative obsidian-bg min-h-screen text-[var(--text-primary)]" x-data="{ activeTab: 'income', showScrollTop: false }" @scroll.window="showScrollTop = window.pageYOffset > 300">
    
    {{-- ══ HEADER ══ --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-[var(--neon-violet)]/30 pb-4">
        <div>
            <h2 class="text-3xl font-bold text-[var(--neon-violet)] tracking-tight uppercase flex items-center gap-3">
                Finanzas & Rentabilidad
            </h2>
            <p class="text-[var(--text-secondary)] text-sm mt-1 uppercase tracking-widest">
                Dashboards Estratégicos: Ingresos, Gastos y Ocupación
            </p>
        </div>

        @if (session()->has('message'))
            <div
                class="mt-4 md:mt-0 bg-green-900/40 border border-green-700/50 text-green-400 px-4 py-2 text-sm font-medium uppercase tracking-wider rounded-[10px] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('message') }}
            </div>
        @endif
    </div>

    {{-- Sub-Navigation & Search --}}
    <div class="flex flex-col lg:flex-row gap-6 items-center">
        <div class="flex bg-[var(--tech-input-bg)] p-1 rounded-[12px] border border-[var(--border-glass)] w-full lg:w-auto">
            <button @click="activeTab = 'income'"
                :class="activeTab === 'income' ? 'bg-[var(--neon-violet)] text-black shadow-lg' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]'"
                class="flex-1 lg:flex-none px-6 py-2 rounded-[10px] text-[11px] font-black uppercase tracking-widest transition-all duration-300">
                Ingresos
            </button>
            <button @click="activeTab = 'expenses'"
                :class="activeTab === 'expenses' ? 'bg-[var(--neon-violet)] text-black shadow-lg' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]'"
                class="flex-1 lg:flex-none px-6 py-2 rounded-[10px] text-[11px] font-black uppercase tracking-widest transition-all duration-300">
                Gastos
            </button>
            <button @click="activeTab = 'profitability'"
                :class="activeTab === 'profitability' ? 'bg-[var(--neon-violet)] text-black shadow-lg' : 'text-[var(--text-secondary)] hover:text-[var(--text-primary)]'"
                class="flex-1 lg:flex-none px-6 py-2 rounded-[10px] text-[11px] font-black uppercase tracking-widest transition-all duration-300">
                Análisis
            </button>
        </div>

        <div class="flex-1 flex flex-col md:flex-row gap-3 w-full">
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-secondary)]" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por ID de reserva o localizador..."
                    class="tech-input w-full py-2.5 pl-10 pr-4 text-xs focus:outline-none transition-all rounded-[12px]">
            </div>

            {{-- Period Filters --}}
            <div
                class="flex items-center gap-3 bg-[var(--tech-input-bg)] p-1 rounded-[12px] border border-[var(--border-glass)]">
                <div class="flex gap-1">
                    @foreach(['today' => 'Hoy', 'all' => 'Todo'] as $key => $label)
                        <button wire:click="setPeriod('{{ $key }}')"
                            class="px-4 py-2 rounded-[10px] text-[10px] font-black uppercase tracking-widest transition-all
                                    {{ $period === $key ? 'bg-[var(--neon-violet)] text-black shadow-lg shadow-[var(--neon-violet)]/20' : 'text-[var(--text-secondary)] hover:text-[var(--neon-violet)]' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="h-6 w-[1px] bg-[var(--border-glass)] mx-1"></div>

                {{-- Specific Selectors (Always active for context) --}}
                <div class="flex items-center gap-2 px-2">
                    <select wire:model.live="selectedMonth" wire:click="setPeriod('month')"
                        class="bg-transparent border-none text-[var(--text-secondary)] text-[10px] rounded-[5px] px-2 py-1 outline-none focus:ring-0 cursor-pointer appearance-none">
                        <option value="all">Todo el año</option>
                        @foreach(range(1, 12) as $m)
                            @php $date = Carbon\Carbon::create()->month($m)->locale('es'); @endphp
                            <option value="{{ sprintf('%02d', $m) }}">{{ ucfirst($date->translatedFormat('F')) }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedYear" wire:click="setPeriod('year')"
                        class="bg-transparent border-none text-[var(--text-secondary)] text-[10px] rounded-[5px] px-2 py-1 outline-none focus:ring-0 cursor-pointer appearance-none">
                        @foreach(range($maxAvailableYear, 2024) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Global Context Bar (Manual Range Entry) --}}
    <div
        class="flex flex-wrap items-center justify-between gap-4 px-4 py-2 bg-[var(--neon-violet)]/5 border border-[var(--neon-violet)]/10 rounded-[12px]">
        <div class="flex items-center gap-3">
            <span
                class="w-1.5 h-1.5 rounded-full bg-[var(--neon-violet)] {{ $period === 'custom' ? 'animate-pulse' : '' }}"></span>
            <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--text-secondary)] mr-2">Periodo:</p>
            <div class="flex items-center gap-2" x-data="{ editing: false }">
                <div class="flex items-center gap-2" x-show="!editing">
                    <span class="text-[var(--neon-violet)] text-[10px] font-bold">
                        {{ $startDate->locale('es')->translatedFormat('d M Y') }} —
                        {{ $endDate->locale('es')->translatedFormat('d M Y') }}
                    </span>
                    <button @click="editing = true; $wire.set('period', 'custom')"
                        class="text-[var(--text-secondary)] hover:text-[var(--neon-violet)] transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                </div>
                <div class="flex items-center gap-2" x-show="editing" x-cloak>
                    <input type="date" wire:model.live="customStart"
                        class="tech-input text-[9px] rounded px-1.5 py-0.5 outline-none">
                    <span class="text-[var(--text-secondary)]">—</span>
                    <input type="date" wire:model.live="customEnd"
                        class="tech-input text-[9px] rounded px-1.5 py-0.5 outline-none">
                    <button @click="editing = false"
                        class="bg-[var(--neon-violet)] text-black px-2 py-0.5 rounded text-[9px] font-bold">OK</button>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--text-secondary)]">Transacciones: <span
                    class="text-[var(--text-primary)]">{{ $transactions->count() }}</span></p>
            <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--text-secondary)]">Liquidez: <span
                    class="text-[var(--neon-cyan)]">€{{ number_format($netIncome, 0, ',', '.') }}</span></p>
        </div>
    </div>

    {{-- PILLAR 1: CENTRO DE INGRESOS --}}
    <div class="space-y-6" x-show="activeTab === 'income'" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <h3
            class="text-xs font-black uppercase tracking-[0.3em] text-[var(--neon-cyan)] flex items-center gap-2 border-b border-[var(--neon-cyan)]/30 pb-2">
            Pilar 1: Centro de Ingresos
        </h3>
            
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- KPI Liquidez --}}
            <div class="flex flex-col gap-4">
                <div class="bg-[var(--neon-cyan)]/5 border border-[var(--neon-cyan)]/20 rounded-[15px] p-5 shadow-lg">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--neon-cyan)] mb-1 opacity-80">
                        Liquidez Inmediata</p>
                    <span class="text-3xl font-bold text-[var(--neon-cyan)]">{{ number_format($netIncome, 0, ',', '.') }}
                        €</span>
                    <div class="mt-4 pt-4 border-t border-[var(--border-glass)] flex justify-between">
                        <div>
                            <p class="text-[9px] uppercase text-[var(--text-secondary)] font-bold">Ticket Medio</p>
                            <p class="text-sm font-mono text-[var(--text-primary)]">
                                {{ number_format($avgTicket, 0, ',', '.') }} €</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] uppercase text-[var(--text-secondary)] font-bold">Descuentos</p>
                            <p class="text-sm font-mono text-[var(--neon-rose)]">
                                {{ number_format($totalDiscounts, 0, ',', '.') }} €</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Interactive Chart --}}
            <div class="md:col-span-2 tech-card p-4 relative" x-data="incomeChartData()" x-init="initChart()"
                @chart-refreshed.window="refreshChart($wire.chartData)">

                <div class="flex justify-between items-start mb-2">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--text-secondary)]">Dinámica de
                        Ingresos</p>
                    @if($period !== 'all')
                        <button wire:click="toggleUpcoming"
                            class="flex items-center gap-2 px-3 py-1 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded-full text-[9px] font-black uppercase transition-all {{ $upcomingOnly ? 'text-[var(--neon-violet)] border-[var(--neon-violet)]/50 bg-[var(--neon-violet)]/10' : 'text-[var(--text-secondary)]' }}">
                            <div
                                class="w-2 h-2 rounded-full {{ $upcomingOnly ? 'bg-[var(--neon-violet)] animate-pulse' : 'bg-zinc-700' }}">
                            </div>
                            Solo Próximos
                        </button>
                    @endif
                </div>

                <div class="relative w-full h-[220px]" wire:ignore>
                    <div wire:loading
                        wire:target="setPeriod, selectedMonth, selectedYear, toggleUpcoming, customStart, customEnd"
                        class="absolute inset-0 bg-black/60 backdrop-blur-[2px] flex items-center justify-center z-30 rounded-[10px]">
                        <div class="flex flex-col items-center gap-2">
                            <div
                                class="w-5 h-5 border-2 border-[var(--neon-violet)] border-t-transparent rounded-full animate-spin">
                            </div>
                            <span class="text-[8px] font-bold text-[var(--neon-violet)] uppercase tracking-widest">Actualizando
                                Ingresos...</span>
                        </div>
                    </div>
                    <canvas x-ref="chartCanvas"></canvas>
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="tech-card overflow-hidden">
            <div class="px-5 py-3 border-b border-[var(--border-glass)] flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--neon-cyan)]/80 flex items-center gap-2">
                    Registro de Transacciones
                    <span class="bg-[var(--tech-input-bg)] text-[var(--neon-cyan)] px-2 py-0.5 rounded-full text-[8px] border border-[var(--border-glass)]">{{ $transactions->count() }}
                        recientes</span>
                </p>
                <div class="flex items-center gap-4 text-[9px] font-bold uppercase tracking-widest">
                    <span class="text-[var(--text-secondary)]">Pagados: <strong
                            class="text-[var(--neon-emerald)]">{{ $totalCount }}</strong></span>
                    <span class="text-[var(--text-secondary)]">Pendientes: <strong
                            class="text-[var(--neon-amber)]">{{ $pending }}</strong></span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left bg-transparent text-[11px]">
                    <thead
                        class="bg-[var(--tech-input-bg)] border-b border-[var(--border-glass)] text-[var(--text-secondary)] uppercase tracking-widest">
                        <tr>
                            <th class="px-4 py-3 font-bold">Reserva / Factura</th>
                            <th class="px-4 py-3 font-bold">Cliente</th>
                            <th class="px-4 py-3 font-bold">Vuelo Ref.</th>
                            <th class="px-4 py-3 font-bold">Importe</th>
                            <th class="px-4 py-3 font-bold text-center">Estado</th>
                            <th class="px-4 py-3 font-bold text-right">Justificante</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-glass)]">
                        @forelse($transactions as $tx)
                            <tr
                                class="hover:bg-[var(--tech-hover-bg)] transition-colors {{ $tx->payment_status === 'pending' ? 'bg-[var(--neon-amber)]/5' : '' }}">
                                <td class="px-4 py-3 font-mono text-[var(--text-secondary)]">
                                    {{ str_pad($tx->id, 4, '0', STR_PAD_LEFT) }}<br>
                                    <span class="text-[9px] text-[var(--text-secondary)] opacity-70">{{ $tx->id_locator }}</span>
                                </td>
                                <td class="px-4 py-3 text-[var(--text-secondary)] flex items-center gap-2 mt-1">
                                    <div
                                        class="w-5 h-5 rounded-full bg-[var(--tech-input-bg)] border border-[var(--border-glass)] flex items-center justify-center text-[9px] font-bold text-[var(--text-primary)]">
                                        {{ substr(optional($tx->user)->name ?? '?', 0, 1) }}</div>
                                    {{ optional($tx->user)->name ?? 'Desconocido' }}
                                </td>
                                <td class="px-4 py-3 font-mono">
                                    @if($tx->spaceFlight)
                                        <a href="{{ route('admin.flights', ['search' => $tx->spaceFlight->flight_code]) }}"
                                            class="group flex items-center gap-1.5 text-[var(--neon-cyan)] hover:text-[var(--text-primary)] underline decoration-[var(--neon-cyan)]/30 decoration-dashed transition-all">
                                            {{ $tx->spaceFlight->flight_code }}
                                            <svg class="w-3 h-3 translate-y-[1px] opacity-0 group-hover:opacity-100 transition-all"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-[var(--text-secondary)] opacity-50">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-[var(--neon-cyan)] font-bold">
                                    €{{ number_format($tx->total_price, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($tx->payment_status === 'paid')
                                        <span
                                            class="bg-[var(--neon-emerald)]/10 text-[var(--neon-emerald)] border border-[var(--neon-emerald)]/30 px-2 py-0.5 rounded-[5px] text-[9px] uppercase tracking-widest font-black">Pagado</span>
                                    @else
                                        <span
                                            class="bg-[var(--neon-amber)]/10 text-[var(--neon-amber)] border border-[var(--neon-amber)]/30 px-2 py-0.5 rounded-[5px] text-[9px] uppercase tracking-widest font-black flex items-center gap-1 justify-center">
                                            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($tx->stripe_receipt_url)
                                        <a href="{{ $tx->stripe_receipt_url }}" target="_blank"
                                            class="inline-flex items-center gap-1 text-[9px] uppercase font-bold text-[var(--neon-cyan)] hover:text-[var(--text-primary)] transition-colors bg-[var(--neon-cyan)]/10 px-2 py-1 rounded-[5px] border border-[var(--neon-cyan)]/20 shadow-sm">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Ticket
                                        </a>
                                    @else
                                        <span class="text-[9px] text-[var(--text-secondary)] italic opacity-50">No disp.</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-[var(--text-secondary)] text-xs uppercase tracking-widest opacity-60 leading-relaxed">
                                    Sin transacciones registradas.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    {{-- PILLAR 2: CONTROL DE GASTOS OPERATIVOS --}}
    <div class="space-y-6" x-show="activeTab === 'expenses'" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <h3
            class="text-xs font-black uppercase tracking-[0.3em] text-[var(--neon-rose)] flex items-center gap-2 border-b border-[var(--neon-rose)]/30 pb-2">
            Pilar 2: Control de Gastos Operativos
        </h3>

        {{-- Expenses Summary Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 bg-[var(--neon-rose)]/5 border border-[var(--neon-rose)]/20 rounded-[15px] p-5 shadow-lg">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--neon-rose)] mb-1 opacity-80">Métricas
                    de Operación</p>
                <p class="text-3xl font-bold text-[var(--neon-rose)]">
                    {{ number_format($totalExpenses, 0, ',', '.') }} €</p>

                <div class="mt-4 pt-4 border-t border-[var(--border-glass)] space-y-4">
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="text-[var(--text-secondary)] font-bold uppercase">Misiones Listadas</span>
                        <span class="text-[var(--text-primary)] font-mono">{{ $flightsWithExpenses->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="text-[var(--text-secondary)] font-bold uppercase">Coste Medio</span>
                        <span class="text-[var(--text-primary)] font-mono">{{ number_format($flightsWithExpenses->count() > 0 ? $totalExpenses / $flightsWithExpenses->count() : 0, 0, ',', '.') }}
                            €</span>
                    </div>
                </div>
            </div>

            {{-- Mini Expense Chart --}}
            <div class="md:col-span-2 tech-card p-5" x-data="expenseChartData()" x-init="initChart()"
                @chart-refreshed.window="refreshChart($wire.chartData)">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--text-secondary)]">Histórico de
                        Gastos</p>
                </div>
                <div class="h-[220px] w-full relative" wire:ignore>
                    <div wire:loading
                        wire:target="setPeriod, selectedMonth, selectedYear, toggleUpcoming, customStart, customEnd"
                        class="absolute inset-0 bg-black/60 backdrop-blur-[2px] flex items-center justify-center z-30 rounded-[10px]">
                        <div class="flex flex-col items-center gap-2">
                            <div
                                class="w-5 h-5 border-2 border-[var(--neon-rose)] border-t-transparent rounded-full animate-spin">
                            </div>
                            <span class="text-[8px] font-bold text-[var(--neon-rose)] uppercase tracking-widest">Sincronizando
                                Gastos...</span>
                        </div>
                    </div>
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </div>

        {{-- Grouped Expenses Table --}}
        <div class="tech-card overflow-hidden">
            <div class="px-5 py-3 border-b border-[var(--border-glass)] flex justify-between items-center bg-[var(--tech-input-bg)]">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--text-secondary)]">Misiones
                    Operativas (Coste Consolidado)</p>
                <span class="text-[9px] text-[var(--text-secondary)] font-bold uppercase">{{ $flightsWithExpenses->count() }}
                    misiones en sistema</span>
            </div>
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                <table class="w-full text-left bg-transparent text-[11px]">
                    <thead
                        class="bg-[var(--tech-input-bg)] sticky top-0 text-[var(--text-secondary)] uppercase tracking-widest border-b border-[var(--border-glass)] z-20">
                        <tr>
                            <th class="px-4 py-3 font-bold">Misión / Nave</th>
                            <th class="px-4 py-3 font-bold">Fecha / Destino</th>
                            <th class="px-4 py-3 font-bold">Estado</th>
                            <th class="px-4 py-3 font-bold">Coste Total</th>
                            <th class="px-4 py-3 font-bold text-right">Auditoría</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-glass)]">
                        @forelse($flightsWithExpenses as $f)
                            <tr class="hover:bg-[var(--tech-hover-bg)] transition-colors" x-data="{ expanded: false }">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="font-mono font-bold text-[var(--text-primary)]">
                                            {{ $f->flight_code }}</div>
                                        <a href="{{ route('admin.flights', ['search' => $f->flight_code]) }}"
                                            class="text-[var(--text-secondary)] hover:text-[var(--text-primary)]">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="text-[9px] text-[var(--text-secondary)] uppercase opacity-70">
                                        {{ optional($f->starship)->name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-[var(--text-primary)] font-mono">
                                        {{ $f->departure_date->locale('es')->format('d M Y') }}</div>
                                    <div class="text-[9px] text-[var(--text-secondary)] uppercase font-bold opacity-70">
                                        {{ optional($f->destination)->name }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest 
                                            {{ $f->status === 'landed' ? 'bg-[var(--neon-emerald)]/10 text-[var(--neon-emerald)] border border-[var(--neon-emerald)]/30' : 'bg-[var(--neon-cyan)]/10 text-[var(--neon-cyan)] border border-[var(--neon-cyan)]/30' }}">
                                        {{ $f->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-[var(--neon-rose)] font-black text-sm">
                                    {{ number_format($f->operational_cost, 2, ',', '.') }} €
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button @click="expanded = !expanded"
                                        class="px-3 py-1.5 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded-[8px] text-[9px] font-bold text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-all uppercase tracking-widest shadow-sm">
                                        Detalles
                                    </button>
                                        
                                    {{-- Advanced Breakdown Modal --}}
                                    <template x-if="expanded">
                                        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-md "
                                            @click.self="expanded = false">
                                            <div
                                                class="bg-[var(--bg-obsidian)] border border-[var(--border-glass)] w-full max-w-xl rounded-[25px] shadow-2xl overflow-hidden animate-slide-in text-left">
                                                <div
                                                    class="px-8 py-5 border-b border-[var(--border-glass)] flex justify-between items-center bg-black/20">
                                                    <div>
                                                        <h4 class="text-xs font-black uppercase tracking-[0.3em] text-[var(--neon-rose)]">
                                                            Misión: {{ $f->flight_code }}</h4>
                                                        <p class="text-[9px] text-[var(--text-secondary)] uppercase mt-0.5">Auditoría
                                                            Detallada de Costes Operativos</p>
                                                    </div>
                                                    <button @click="expanded = false"
                                                        class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] p-2 transition-colors">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="p-8 space-y-6">
                                                    {{-- Logistic Fees --}}
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div
                                                            class="p-4 bg-black/20 rounded-xl border border-[var(--border-glass)]">
                                                            <p class="text-[8px] font-black uppercase text-[var(--text-secondary)] mb-1">
                                                                Tasas de Despegue (Tierra)</p>
                                                            <p class="text-md font-mono text-[var(--text-primary)]">
                                                                €{{ number_format($f->launch_cost_earth, 2, ',', '.') }}</p>
                                                        </div>
                                                        <div
                                                            class="p-4 bg-black/20 rounded-xl border border-[var(--border-glass)]">
                                                            <p class="text-[8px] font-black uppercase text-[var(--text-secondary)] mb-1">
                                                                Tasas de Aterrizaje (Tierra)</p>
                                                            <p class="text-md font-mono text-[var(--text-primary)]">
                                                                €{{ number_format($f->landing_cost_earth, 2, ',', '.') }}</p>
                                                        </div>
                                                        <div
                                                            class="p-4 bg-black/20 rounded-xl border border-[var(--border-glass)]">
                                                            <p class="text-[8px] font-black uppercase text-[var(--text-secondary)] mb-1">
                                                                Tasas de Lanzamiento ({{ optional($f->destination)->name }})</p>
                                                            <p class="text-md font-mono text-[var(--text-primary)]">
                                                                €{{ number_format($f->launch_cost_planet, 2, ',', '.') }}</p>
                                                        </div>
                                                        <div
                                                            class="p-4 bg-black/20 rounded-xl border border-[var(--border-glass)]">
                                                            <p class="text-[8px] font-black uppercase text-[var(--text-secondary)] mb-1">
                                                                Tasas de Aterrizaje ({{ optional($f->destination)->name }})</p>
                                                            <p class="text-md font-mono text-[var(--text-primary)]">
                                                                €{{ number_format($f->landing_cost_planet, 2, ',', '.') }}</p>
                                                        </div>
                                                    </div>

                                                    {{-- Mission Stats --}}
                                                    <div
                                                        class="p-5 bg-black/10 rounded-xl border border-[var(--border-glass)] space-y-3">
                                                        <div class="flex justify-between items-center text-[10px]">
                                                            <span class="text-[var(--text-secondary)] uppercase font-bold text-[8px]">Precio
                                                                por AU (Consolidado)</span>
                                                            <span class="text-[var(--text-primary)] font-mono italic">€{{ number_format($f->au_distance > 0 ? ($f->operational_cost / $f->au_distance) : 0, 4, ',', '.') }}
                                                                / AU</span>
                                                        </div>
                                                        <div class="h-[1px] bg-[var(--border-glass)]"></div>
                                                        <div class="flex justify-between items-center text-[10px]">
                                                            <span class="text-[var(--text-secondary)] uppercase font-bold text-[8px]">Costes
                                                                de Personal / Empleados</span>
                                                            <span class="text-[var(--text-primary)] font-mono">€{{ number_format($f->crew_daily_rate, 2, ',', '.') }} x día</span>
                                                        </div>
                                                        <div class="h-[1px] bg-[var(--border-glass)]"></div>
                                                        <div class="flex justify-between items-center text-[10px]">
                                                            <span class="text-[var(--text-secondary)] uppercase font-bold text-[8px]">Distancia de
                                                                Misión</span>
                                                            <span class="text-[var(--text-primary)] font-mono">{{ number_format($f->au_distance, 2, ',', '.') }}
                                                                AU</span>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="flex justify-between items-end pt-4 border-t border-[var(--border-glass)]">
                                                        <div>
                                                            <p class="text-[9px] font-black uppercase text-[var(--text-secondary)]">
                                                                Referencia de Fecha</p>
                                                            <p class="text-xs text-[var(--text-secondary)]">
                                                                {{ $f->departure_date->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-[10px] font-black uppercase text-[var(--text-secondary)]">
                                                                Gasto Total de Misión</p>
                                                            <p class="text-3xl font-black text-[var(--neon-rose)]">
                                                                €{{ number_format($f->operational_cost, 2, ',', '.') }}</p>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-20 text-center text-[var(--text-secondary)] opacity-60">
                                    <p class="text-sm font-black uppercase tracking-widest">Sin registros operacionales</p>
                                    <p class="text-[10px] opacity-50 mt-1">No se detectaron misiones activas en este periodo.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PILLAR 3: ANÁLISIS DE RENTABILIDAD --}}
    <div class="space-y-4 pt-4 pb-12" x-show="activeTab === 'profitability'"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0">
        <h3
            class="text-xs font-black uppercase tracking-[0.3em] text-[var(--neon-emerald)] flex items-center gap-2 border-b border-[var(--neon-emerald)]/30 pb-2">
            Pilar 3: Análisis de Rentabilidad
        </h3>
            
        {{-- Profitability Chart --}}
        <div class="tech-card p-5 mb-6" x-data="profitabilityChartData()" x-init="initChart()"
            @chart-refreshed.window="refreshChart($wire.chartData)">
            <div class="flex justify-between items-center mb-4">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--text-secondary)]">Proyección y
                    Realidad Financiera</p>
            </div>
            <div class="h-[250px] w-full relative" wire:ignore>
                <div wire:loading
                    wire:target="setPeriod, selectedMonth, selectedYear, toggleUpcoming, customStart, customEnd"
                    class="absolute inset-0 bg-black/60 backdrop-blur-[2px] flex items-center justify-center z-30 rounded-[10px]">
                    <div class="flex flex-col items-center gap-2">
                        <div
                            class="w-5 h-5 border-2 border-[var(--neon-emerald)] border-t-transparent rounded-full animate-spin">
                        </div>
                        <span class="text-[8px] font-bold text-[var(--neon-emerald)] uppercase tracking-widest">Sincronizando
                            Análisis...</span>
                    </div>
                </div>
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KPI Rentabilidad Proyectada vs Real --}}
            <div
                class="lg:col-span-1 bg-[var(--neon-emerald)]/5 border border-[var(--neon-emerald)]/20 rounded-[15px] p-5 flex flex-col justify-between shadow-lg">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--neon-emerald)] mb-1 opacity-80">
                        Resultado Neto (Global)</p>
                    <span
                        class="text-3xl font-bold {{ $totalProfit >= 0 ? 'text-[var(--neon-emerald)]' : 'text-[var(--neon-rose)]' }}">{{ number_format($totalProfit, 0, ',', '.') }}
                        €</span>

                    <div class="space-y-4 mt-6">
                        <div
                            class="bg-black/20 p-3 rounded-lg border border-[var(--border-glass)]">
                            <p class="text-[8px] uppercase tracking-widest text-[var(--text-secondary)]">Gastos Globales</p>
                            <p class="text-sm font-mono text-[var(--neon-rose)]/80 mt-1">
                                {{ number_format($totalExpenses, 0, ',', '.') }} €</p>
                        </div>
                        <div
                            class="bg-black/20 p-3 rounded-lg border border-[var(--border-glass)]">
                            <p class="text-[8px] uppercase tracking-widest text-[var(--text-secondary)]">Ingresos (Netos)</p>
                            <p class="text-sm font-mono text-[var(--neon-cyan)]/80 mt-1">
                                {{ number_format($netIncome, 0, ',', '.') }} €</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Algoritmo Optimización de Ocupación --}}
            <div
                class="md:col-span-2 tech-card overflow-hidden flex flex-col h-full relative">
                <div
                    class="px-5 py-3 border-b border-[var(--border-glass)] flex justify-between items-center bg-[var(--tech-input-bg)]">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--neon-amber)]/80">Listado de
                            Vuelos Críticos (&lt;80%)</p>
                        <p class="text-[8px] text-[var(--text-secondary)] uppercase">Acción requerida para optimizar
                            rentabilidad</p>
                    </div>
                    <span
                        class="text-[9px] font-bold bg-[var(--neon-amber)]/10 text-[var(--neon-amber)] border border-[var(--neon-amber)]/30 px-2 py-0.5 rounded-full">{{ $criticalFlights->count() }}
                        Alertas</span>
                </div>
                    
                <div class="flex-1 overflow-y-auto max-h-[300px]">
                    <ul class="divide-y divide-[var(--border-glass)]">
                        @forelse($criticalFlights as $fc)
                            <li wire:click="selectFlight({{ $fc->id }})"
                                class="p-4 flex items-center justify-between hover:bg-[var(--neon-amber)]/5 cursor-pointer transition-colors {{ $selectedFlightId == $fc->id ? 'bg-[var(--neon-amber)]/10' : '' }}">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-[10px] bg-[var(--neon-amber)]/10 border border-[var(--neon-amber)]/20 flex items-center justify-center text-[var(--neon-amber)] shadow-sm">
                                        <svg class="w-5 h-5 shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <span
                                            class="text-xs font-mono font-bold text-[var(--neon-amber)]">{{ $fc->flight_code }}</span>
                                        <p class="text-[10px] text-[var(--text-secondary)]">Este vuelo no está saliendo rentable
                                            ({{ $fc->occupancy_percentage }}% ocupación)</p>
                                    </div>
                                </div>
                                <div class="text-right flex items-center gap-4">
                                    <div class="text-right">
                                        <p class="text-[8px] uppercase tracking-widest text-[var(--text-secondary)]">Ocupación</p>
                                        <p
                                            class="font-mono text-sm font-black {{ $fc->occupancy_percentage < 50 ? 'text-[var(--neon-rose)]' : 'text-[var(--neon-amber)]' }}">
                                            {{ $fc->occupancy_percentage }}%</p>
                                    </div>
                                    <svg class="w-4 h-4 text-[var(--text-secondary)] opacity-50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </li>
                        @empty
                            <div class="p-12 text-center text-[var(--text-secondary)] text-xs uppercase tracking-widest opacity-60 leading-relaxed">
                                Excelente. Ningún vuelo programado se encuentra por debajo del ratio de optimización operativa.
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Selected Flight Projections --}}
            @if($flightDetails)
                <div
                    class="md:col-span-3 bg-[var(--tech-input-bg)] border border-[var(--border-glass)] rounded-[20px] p-6 animate-slide-in">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                        <div>
                            <h4
                                class="text-xl font-black text-[var(--text-primary)] italic uppercase tracking-tighter flex items-center gap-3">
                                <span
                                    class="text-[var(--neon-amber)] border-r border-[var(--border-glass)] pr-3">{{ $flightDetails->flight_code }}</span>
                                Análisis de Proyección Detallado
                            </h4>
                            <p class="text-[10px] text-[var(--text-secondary)] uppercase tracking-widest mt-1">Simulación basada en
                                ocupación real vs potencial de nave {{ $flightDetails->starship->name }}</p>
                        </div>
                        <button wire:click="$set('selectedFlightId', null)"
                            class="text-[10px] text-[var(--text-secondary)] hover:text-[var(--text-primary)] uppercase font-bold transition-colors">Cerrar
                            Análisis</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        {{-- Real Income --}}
                        <div class="bg-black/20 p-5 rounded-[15px] border border-[var(--border-glass)]">
                            <p class="text-[10px] font-bold text-[var(--neon-cyan)] uppercase tracking-widest mb-2">Ingresos
                                Reales (Actual)</p>
                            <span
                                class="text-2xl font-black text-[var(--text-primary)]">€{{ number_format($flightDetails->real_income, 0, ',', '.') }}</span>
                            <p class="text-[9px] text-[var(--text-secondary)] mt-2">Reservas confirmadas y pagadas.</p>
                        </div>

                        {{-- Projected 80% --}}
                        <div class="bg-black/20 p-5 rounded-[15px] border border-[var(--border-glass)]">
                            <p class="text-[10px] font-bold text-[var(--neon-amber)] uppercase tracking-widest mb-2">Proyección
                                (80% Cap)</p>
                            <span
                                class="text-2xl font-black text-[var(--text-primary)]">€{{ number_format($flightDetails->projected_income_80, 0, ',', '.') }}</span>
                            <p class="text-[9px] text-[var(--text-secondary)] mt-2">Objetivo mínimo de rentabilidad operativa.
                            </p>
                        </div>

                        {{-- Possible Income (Max) --}}
                        <div class="bg-black/20 p-5 rounded-[15px] border border-[var(--border-glass)]">
                            <p class="text-[10px] font-bold text-[var(--neon-emerald)] uppercase tracking-widest mb-2">Ingresos
                                Posibles (100%)</p>
                            <span
                                class="text-2xl font-black text-[var(--text-primary)]">€{{ number_format($flightDetails->max_income, 0, ',', '.') }}</span>
                            <p class="text-[9px] text-[var(--text-secondary)] mt-2">Capacidad máxima facturable del vuelo.</p>
                        </div>

                        {{-- Expenses --}}
                        <div class="bg-black/20 p-5 rounded-[15px] border border-[var(--neon-rose)]/20 shadow-sm shadow-[var(--neon-rose)]/5">
                            <p class="text-[10px] font-bold text-[var(--neon-rose)] uppercase tracking-widest mb-2">Gastos
                                Operativos</p>
                            <span
                                class="text-2xl font-black text-[var(--text-primary)]">€{{ number_format($flightDetails->operational_cost, 0, ',', '.') }}</span>
                            <p class="text-[9px] text-[var(--text-secondary)] mt-2">Costes fijos + variables de misión.</p>
                        </div>
                    </div>

                    {{-- Visual Bar Comparison --}}
                    <div class="mt-8 pt-6 border-t border-[var(--border-glass)]">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--text-secondary)] mb-4 text-center">
                            Comparativa de Margen</p>
                        <div
                            class="h-6 w-full bg-black/40 rounded-full flex overflow-hidden border border-[var(--border-glass)] p-1">
                            @php
                                $maxVal = max($flightDetails->max_income, $flightDetails->operational_cost, 1);
                                $wReal = ($flightDetails->real_income / $maxVal) * 100;
                                $wExp = ($flightDetails->operational_cost / $maxVal) * 100;
                            @endphp
                            <div class="h-full bg-[var(--neon-rose)]/40 border-r border-[var(--neon-rose)]/50"
                                style="width: {{ $wExp }}%"></div>
                            <div class="h-full bg-[var(--neon-cyan)] shadow-[0_0_10px_rgba(6,182,212,0.3)]"
                                style="width: {{ $wReal }}%"></div>
                        </div>
                        <div class="flex justify-between mt-2 px-1">
                            <span class="text-[8px] font-bold uppercase text-[var(--neon-rose)] opacity-80">Punto de Equilibrio
                                (Break-even)</span>
                            <span class="text-[8px] font-bold uppercase text-[var(--neon-cyan)]">Progreso de Ingresos Reales</span>
                        </div>

                        <div class="mt-6 p-4 bg-[var(--neon-amber)]/5 border border-[var(--neon-amber)]/10 rounded-[10px]">
                            @if($flightDetails->real_income < $flightDetails->operational_cost)
                                <p class="text-xs text-[var(--neon-amber)] font-bold flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    Atención: El vuelo actual está en pérdidas. Faltan
                                    {{ number_format($flightDetails->operational_cost - $flightDetails->real_income, 0, ',', '.') }} € para
                                    alcanzar el punto de equilibrio.
                                </p>
                            @else
                                <p class="text-xs text-[var(--neon-emerald)] font-bold flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Vuelo Rentable: Has superado el umbral de gastos operativos. Margen actual:
                                    {{ number_format($flightDetails->real_income - $flightDetails->operational_cost, 0, ',', '.') }} €.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
        
    {{-- Stripe Reminder --}}
    <div
        class="border border-dashed border-[var(--border-glass)] rounded-[10px] p-4 flex items-center gap-3 bg-black/10 opacity-70">
        <svg class="w-4 h-4 text-[var(--neon-violet)] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-[9px] text-[var(--text-secondary)] leading-relaxed uppercase tracking-widest">
            Stripe Test Mode Activo. Los pagos en la tabla principal son generados desde la tarjeta de prueba de la API en
            el Sandbox de Iris Aerospace.
        </p>
    </div>

    <!-- Cargar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function incomeChartData() {
            return {
                chart: null,
                refreshChart(newData) {
                    if(!newData || !newData.labels || !this.chart) return;
                    this.chart.data.labels = newData.labels || [];
                    this.chart.data.datasets[0].data = newData.net || [];
                    this.chart.update();
                },
                initChart() {
                    if (!this.$refs.chartCanvas) return;
                    const ctx = this.$refs.chartCanvas.getContext('2d');
                    Chart.defaults.color = '#71717a';
                    Chart.defaults.font.family = "'Helvetica Neue', Helvetica, Arial, sans-serif";
                    
                    if (this.chart) this.chart.destroy();

                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.$wire.chartData.labels || [],
                            datasets: [
                                {
                                    label: 'Ingresos Netos',
                                    data: this.$wire.chartData.net || [],
                                    backgroundColor: 'rgba(139, 92, 246, 0.4)',
                                    borderColor: 'rgba(139, 92, 246, 1)',
                                    borderWidth: 1,
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: { boxWidth: 8, usePointStyle: true, font: {size: 10} }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    titleFont: { size: 11 },
                                    bodyFont: { size: 11, weight: 'bold' },
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            if (context.parsed.y !== null) {
                                                let val = context.parsed.y;
                                                if (val >= 1000000) {
                                                    label += (val / 1000000).toFixed(2).replace(/\.00$/, '') + ' M €';
                                                } else if (val >= 1000) {
                                                    label += (val / 1000).toFixed(1).replace(/\.0$/, '') + ' k €';
                                                } else {
                                                    label += val + ' €';
                                                }
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255,255,255,0.05)' },
                                    ticks: {
                                        callback: function(val) { 
                                            if (val >= 1000000) return (val / 1000000).toFixed(1).replace(/\.0$/, '') + ' M €';
                                            if (val >= 1000) return (val / 1000).toFixed(1).replace(/\.0$/, '') + ' k €';
                                            return val + ' €';
                                        },
                                        font: { size: 9 }
                                    }
                                },
                                x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                            }
                        }
                    });

                    this.$watch('$wire.chartData', (newData) => {
                        this.refreshChart(newData);
                    });
                }
            }
        }
        function expenseChartData() {
            return {
                chart: null,
                refreshChart(newData) {
                    if(!newData || !newData.labels || !this.chart) return;
                    this.chart.data.labels = newData.labels || [];
                    this.chart.data.datasets[0].data = newData.expenses || [];
                    this.chart.update();
                },
                initChart() {
                    if (!this.$refs.canvas) return;
                    const ctx = this.$refs.canvas.getContext('2d');
                    if (this.chart) this.chart.destroy();

                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.$wire.chartData.labels || [],
                            datasets: [{
                                label: 'Gastos',
                                data: this.$wire.chartData.expenses || [],
                                backgroundColor: 'rgba(239, 68, 68, 0.4)',
                                borderColor: '#ef4444',
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    titleFont: { size: 11 },
                                    bodyFont: { size: 11, weight: 'bold' },
                                    callbacks: {
                                        label: (ctx) => {
                                            let val = ctx.parsed.y;
                                            if (val >= 1000000) return (val / 1000000).toFixed(2).replace(/\.00$/, '') + ' M €';
                                            if (val >= 1000) return (val / 1000).toFixed(1).replace(/\.0$/, '') + ' k €';
                                            return val + ' €';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true, 
                                    grid: { color: 'rgba(255,255,255,0.05)' }, 
                                    border: { display: false }, 
                                    ticks: { 
                                        color: '#71717a', 
                                        font: { size: 9 },
                                        callback: (val) => {
                                            if (val >= 1000000) return (val / 1000000).toFixed(1).replace(/\.0$/, '') + ' M €';
                                            if (val >= 1000) return (val / 1000).toFixed(1).replace(/\.0$/, '') + ' k €';
                                            return val + ' €';
                                        }
                                    } 
                                },
                                x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 9 } } }
                            }
                        }
                    });
                }
            }
        }
        function profitabilityChartData() {
            return {
                chart: null,
                refreshChart(newData) {
                    if(!newData || !newData.labels || !this.chart) return;
                    this.chart.data.labels = newData.labels || [];
                    this.chart.data.datasets[0].data = newData.projected || [];
                    this.chart.data.datasets[1].data = newData.net || [];
                    this.chart.data.datasets[2].data = newData.expenses || [];
                    this.chart.update();
                },
                initChart() {
                    if (!this.$refs.canvas) return;
                    const ctx = this.$refs.canvas.getContext('2d');
                    if (this.chart) this.chart.destroy();

                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.$wire.chartData.labels || [],
                            datasets: [
                                {
                                    label: 'Ingresos Previstos (Potencial)',
                                    data: this.$wire.chartData.projected || [],
                                    backgroundColor: 'rgba(52, 211, 153, 0.15)',
                                    borderColor: 'rgba(52, 211, 153, 0.3)',
                                    borderWidth: 1,
                                    borderRadius: 4
                                },
                                {
                                    label: 'Ingresos Reales',
                                    data: this.$wire.chartData.net || [],
                                    backgroundColor: 'rgba(139, 92, 246, 0.5)',
                                    borderColor: 'rgba(139, 92, 246, 1)',
                                    borderWidth: 1,
                                    borderRadius: 4
                                },
                                {
                                    label: 'Gastos Operativos',
                                    data: this.$wire.chartData.expenses || [],
                                    backgroundColor: 'rgba(239, 68, 68, 0.5)',
                                    borderColor: 'rgba(239, 68, 68, 1)',
                                    borderWidth: 1,
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: { 
                                legend: { 
                                    position: 'top',
                                    labels: { boxWidth: 8, usePointStyle: true, font: {size: 10} }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0,0,0,0.8)',
                                    titleFont: { size: 11 },
                                    bodyFont: { size: 11, weight: 'bold' },
                                    callbacks: {
                                        label: (context) => {
                                            let label = context.dataset.label || '';
                                            if (label) label += ': ';
                                            if (context.parsed.y !== null) {
                                                let val = context.parsed.y;
                                                if (val >= 1000000) {
                                                    label += (val / 1000000).toFixed(2).replace(/\.00$/, '') + ' M €';
                                                } else if (val >= 1000) {
                                                    label += (val / 1000).toFixed(1).replace(/\.0$/, '') + ' k €';
                                                } else {
                                                    label += val + ' €';
                                                }
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true, 
                                    grid: { color: 'rgba(255,255,255,0.05)' }, 
                                    border: { display: false }, 
                                    ticks: { 
                                        color: '#71717a', 
                                        font: { size: 9 },
                                        callback: (val) => {
                                            if (val >= 1000000) return (val / 1000000).toFixed(1).replace(/\.0$/, '') + ' M €';
                                            if (val >= 1000) return (val / 1000).toFixed(1).replace(/\.0$/, '') + ' k €';
                                            return val + ' €';
                                        }
                                    } 
                                },
                                x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 9 } } }
                            }
                        }
                    });

                    this.$watch('$wire.chartData', (newData) => {
                        this.refreshChart(newData);
                    });
                }
            }
        }
    </script>

    <!-- Botón Subir Mobile -->
    <button x-show="showScrollTop" x-transition @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="md:hidden fixed bottom-6 right-6 z-[90] w-12 h-12 rounded-full bg-[var(--neon-violet)] text-black flex items-center justify-center shadow-[0_0_20px_rgba(139,92,246,0.5)] border border-[var(--neon-violet)]/50 transition-transform active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
</div>
