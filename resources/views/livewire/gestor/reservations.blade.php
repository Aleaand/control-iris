<div class="p-6 space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Central de Reservas</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Reservas de tu cartera de clientes</p>
        </div>
    </div>

    @if(session('message'))
        <div class="px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">{{ session('message') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs">{{ session('error') }}</div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <input wire:model.live="search" type="text" placeholder="Buscar por cliente, pasajero o localizador..."
            class="flex-1 min-w-[200px] px-3 py-2 rounded-lg text-sm"
            style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
        <select wire:model.live="filterStatus" class="px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
            <option value="">Todos los estados</option>
            <option value="Pendiente">Pendiente</option>
            <option value="GO">GO</option>
            <option value="Cancelada">Cancelada</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="tech-card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.06)">
                        <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Localizador</th>
                        <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Pasajero / Cliente</th>
                        <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Vuelo</th>
                        <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Clase</th>
                        <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Importe</th>
                        <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Pago</th>
                        <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Estado</th>
                        <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase tracking-widest text-zinc-500">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $res)
                        <tr class="border-b border-white/3 hover:bg-white/2 transition-colors {{ $res->is_72h_window ? 'bg-rose-500/3' : '' }}">
                            <td class="px-4 py-3">
                                <span class="font-mono-tech text-[9px] text-cyan-400">{{ strtoupper(substr($res->id_locator, 0, 8)) }}</span>
                                @if($res->is_72h_window)
                                    <span class="ml-1 text-[8px] text-rose-400 bg-rose-500/10 px-1 py-0.5 rounded">72H</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold" style="color: var(--text-primary)">{{ $res->passenger?->full_name ?? '—' }}</p>
                                <p class="text-zinc-500 text-[9px]">{{ $res->user?->name }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p style="color: var(--text-primary)">{{ $res->spaceFlight?->destination?->name ?? '—' }}</p>
                                <p class="text-zinc-500 text-[9px]">{{ $res->spaceFlight?->departure_date?->format('d/m/Y') ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full font-mono-tech text-[9px]
                                    {{ $res->seat_type === 'supernova' ? 'bg-violet-500/10 text-violet-400' : 'bg-cyan-500/10 text-cyan-400' }}">
                                    {{ strtoupper($res->seat_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono-tech text-[10px]" style="color: var(--text-primary)">
                                {{ number_format($res->total_price, 2) }} €
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full font-mono-tech text-[9px]
                                    {{ $res->payment_status === 'paid' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                    {{ $res->payment_status === 'paid' ? 'Pagado' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full font-mono-tech text-[9px]
                                    {{ $res->status === 'GO' ? 'bg-emerald-500/20 text-emerald-300' : ($res->status === 'Cancelada' ? 'bg-rose-500/10 text-rose-400' : 'bg-zinc-700/30 text-zinc-400') }}">
                                    {{ $res->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    {{-- Ver detalle --}}
                                    <button wire:click="viewDetail({{ $res->id }})" title="Ver detalle"
                                        class="p-1.5 rounded-lg text-zinc-500 hover:text-cyan-400 hover:bg-cyan-500/10 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    </button>
                                    {{-- Link de pago --}}
                                    @if($res->payment_status !== 'paid' && $res->status !== 'Cancelada')
                                        <button wire:click="generatePayLink({{ $res->id }})" title="Generar link de pago"
                                            class="p-1.5 rounded-lg text-zinc-500 hover:text-amber-400 hover:bg-amber-500/10 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                        </button>
                                    @endif
                                    {{-- Final GO --}}
                                    @if($res->is_72h_window && $res->status !== 'GO' && $res->status !== 'Cancelada')
                                        <button wire:click="openFinalGo({{ $res->id }})" title="FINAL GO"
                                            class="p-1.5 rounded-lg font-bold text-[9px] uppercase tracking-wider
                                            {{ $res->go_eligible ? 'bg-emerald-500 text-white hover:bg-emerald-400' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }} transition-all">
                                            GO
                                        </button>
                                    @endif
                                    {{-- PDF --}}
                                    @if($res->status === 'GO')
                                        <a href="{{ route('gestor.reservations.ticket-pdf', ['reservation' => $res->id]) }}"
                                            target="_blank" title="Descargar ticket PDF"
                                            class="p-1.5 rounded-lg text-zinc-500 hover:text-violet-400 hover:bg-violet-500/10 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                        </a>
                                    @endif
                                    {{-- Cancelar --}}
                                    @if($res->status !== 'Cancelada')
                                        <button wire:click="confirmDelete({{ $res->id }})" title="Cancelar"
                                            class="p-1.5 rounded-lg text-zinc-500 hover:text-rose-400 hover:bg-rose-500/10 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-zinc-600 text-sm">Sin reservas encontradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FINAL GO Modal --}}
    @if($showFinalGoModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="tech-card p-6 rounded-xl w-full max-w-md relative overflow-hidden" style="border-color:rgba(16,185,129,0.3)">
                <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
                <h3 class="font-black uppercase tracking-[0.2em] text-emerald-400 mb-1">⚡ FINAL GO</h3>
                <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mb-5">Verificación pre-vuelo — Ventana 72h</p>

                <div class="space-y-3 mb-6">
                    @foreach([
                        'paid'     => ['label' => 'Pago Confirmado', 'icon' => '💳'],
                        'docs'     => ['label' => 'Pasaporte Espacial Válido', 'icon' => '🛂'],
                        'health'   => ['label' => 'Aptitud Física Confirmada', 'icon' => '🏥'],
                        'training' => ['label' => 'Certificado Iris Training', 'icon' => '🎓'],
                    ] as $key => $item)
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $finalGoChecklist[$key] ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-rose-500/10 border border-rose-500/20' }}">
                            <span>{{ $item['icon'] }}</span>
                            <span class="flex-1 text-xs" style="color: var(--text-primary)">{{ $item['label'] }}</span>
                            @if($finalGoChecklist[$key])
                                <span class="text-emerald-400 font-bold text-xs">✓ OK</span>
                            @else
                                <span class="text-rose-400 font-bold text-xs">✗ FALLO</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @php $allOk = collect($finalGoChecklist)->every(fn($v) => $v); @endphp

                @if($allOk)
                    <p class="text-xs text-emerald-400 text-center mb-4">✅ Todo en verde. Puedes emitir el GO.</p>
                @else
                    <p class="text-xs text-rose-400 text-center mb-4">⚠️ Hay requisitos pendientes. No se puede emitir GO.</p>
                @endif

                <div class="flex gap-3">
                    @if($allOk)
                        <button wire:click="executeFinalGo"
                            class="flex-1 py-3 rounded-xl font-black text-sm uppercase tracking-widest text-white transition-all"
                            style="background:linear-gradient(135deg,#059669,#10b981);box-shadow:0 0 20px rgba(16,185,129,0.4)">
                            🚀 EMITIR GO
                        </button>
                    @endif
                    <button wire:click="$set('showFinalGoModal', false)"
                        class="flex-1 py-3 rounded-xl text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Pay Link Modal --}}
    @if($showPayLinkModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="tech-card p-6 rounded-xl w-full max-w-md" style="border-color:rgba(245,158,11,0.3)">
                <h3 class="font-black uppercase tracking-widest text-amber-400 mb-1 text-sm">Link de Pago Generado</h3>
                <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mb-4">Válido 7 días · Importe: {{ number_format($generatedPayAmount ?? 0, 2) }} €</p>
                <div class="flex items-center gap-2 px-3 py-3 rounded-lg bg-white/5 border border-white/8 mb-4">
                    <input type="text" readonly value="{{ $generatedPayLink }}" class="flex-1 bg-transparent text-xs text-zinc-300 outline-none truncate">
                    <button onclick="navigator.clipboard.writeText('{{ $generatedPayLink }}')"
                        class="text-amber-400 hover:text-amber-300 transition-colors font-mono-tech text-[9px] uppercase">Copiar</button>
                </div>
                <button wire:click="closePayLinkModal" class="w-full py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Cerrar</button>
            </div>
        </div>
    @endif

    {{-- Delete Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="tech-card p-6 rounded-xl w-full max-w-sm" style="border-color:rgba(244,63,94,0.2)">
                <h3 class="font-black uppercase tracking-widest text-rose-400 mb-3 text-sm">¿Cancelar reserva?</h3>
                <p class="text-xs text-zinc-400 mb-5">El estado cambiará a "Cancelada". Si el cliente pagó, podrás solicitar un reembolso desde Pagos.</p>
                <div class="flex gap-3">
                    <button wire:click="executeDelete" class="flex-1 py-2.5 rounded-lg text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30 hover:bg-rose-500/30 transition-colors">Cancelar Reserva</button>
                    <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Atrás</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $detailReservation)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-lg max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black uppercase tracking-widest text-cyan-400 text-sm">Detalle Reserva</h3>
                    <button wire:click="closeDetail" class="text-zinc-500 hover:text-zinc-300 transition-colors">✕</button>
                </div>
                <dl class="space-y-2 text-xs">
                    <div class="flex justify-between"><dt class="text-zinc-500">Localizador</dt><dd class="font-mono-tech text-cyan-400">{{ strtoupper(substr($detailReservation->id_locator,0,8)) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">Cliente</dt><dd style="color:var(--text-primary)">{{ $detailReservation->user?->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">Pasajero</dt><dd style="color:var(--text-primary)">{{ $detailReservation->passenger?->full_name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">Destino</dt><dd style="color:var(--text-primary)">{{ $detailReservation->spaceFlight?->destination?->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">Fecha</dt><dd style="color:var(--text-primary)">{{ $detailReservation->spaceFlight?->departure_date?->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">Clase</dt><dd>{{ strtoupper($detailReservation->seat_type) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">Hotel</dt><dd style="color:var(--text-primary)">{{ $detailReservation->logistics?->hotel?->name ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">Importe</dt><dd class="font-mono-tech text-emerald-400">{{ number_format($detailReservation->total_price, 2) }} €</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">Pago</dt><dd>{{ $detailReservation->payment_status === 'paid' ? '✅ Pagado' : '⏳ Pendiente' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-500">Estado</dt><dd>{{ $detailReservation->status }}</dd></div>
                </dl>
            </div>
        </div>
    @endif

</div>
