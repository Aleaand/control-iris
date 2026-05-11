<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Pagos y
                Reembolsos</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Gestión financiera de
                tus clientes</p>
        </div>
    </div>

    @if(session('message'))
        <div class="px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs animate-tech">
            {{ session('message') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs animate-tech">
            {{ session('error') }}
        </div>
    @endif

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="tech-card rounded-xl p-4">
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase">Total Reservas</p>
            <p class="text-xl font-black mt-1" style="color: var(--text-primary)">{{ $stats['total'] }}</p>
        </div>
        <div class="tech-card rounded-xl p-4">
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase">Pagadas</p>
            <p class="text-xl font-black text-emerald-400 mt-1">{{ $stats['paid'] }}</p>
        </div>
        <div class="tech-card rounded-xl p-4">
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase">Pendientes</p>
            <p class="text-xl font-black text-amber-400 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="tech-card rounded-xl p-4">
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase">Ingresos Confirmados</p>
            <p class="text-xl font-black mt-1" style="color: var(--text-primary)">
                {{ number_format($stats['revenue'], 2) }} €</p>
        </div>
        <div class="tech-card rounded-xl p-4 border-rose-500/10">
            <p class="font-mono-tech text-[9px] text-zinc-500 uppercase">Total Reembolsado</p>
            <p class="text-xl font-black text-rose-400 mt-1">
                {{ number_format($stats['refunded'], 2) }} €</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Reservas / Pagos --}}
        <div class="xl:col-span-2 space-y-4">
            <div class="flex items-center gap-3">
                <input wire:model.live="search" type="text" placeholder="Buscar reserva..."
                    class="flex-1 px-3 py-2 rounded-lg text-sm"
                    style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                <select wire:model.live="filterPayment" class="px-3 py-2 rounded-lg text-xs"
                    style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                    <option value="">Todos los estados</option>
                    <option value="paid">Pagado</option>
                    <option value="pending">Pendiente</option>
                </select>
            </div>

            <div class="tech-card rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06)">
                                <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase text-zinc-500">
                                    Localizador / Vuelo</th>
                                <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase text-zinc-500">
                                    Cliente</th>
                                <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase text-zinc-500">
                                    Importe</th>
                                <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase text-zinc-500">Estado
                                    Pago</th>
                                <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase text-zinc-500">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $res)
                                <tr wire:key="res-{{ $res->id }}" class="border-b border-white/3 hover:bg-white/2 transition-colors">
                                    <td class="px-4 py-3">
                                        <p class="font-mono-tech text-cyan-400">
                                            {{ strtoupper(substr($res->id_locator, 0, 8)) }}</p>
                                        <p class="text-[9px] text-zinc-500">{{ $res->spaceFlight?->destination?->name }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p style="color: var(--text-primary)">{{ $res->user?->name }}</p>
                                        <p class="text-[9px] text-zinc-500">{{ $res->passenger?->full_name }}</p>
                                    </td>
                                    <td class="px-4 py-3 font-mono-tech text-[10px]" style="color: var(--text-primary)">
                                        {{ number_format($res->total_price, 2) }} €
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $payStatusClass = match($res->payment_status) {
                                                'paid' => 'bg-emerald-500/10 text-emerald-400',
                                                'refunded' => 'bg-rose-500/10 text-rose-400',
                                                default => 'bg-amber-500/10 text-amber-400',
                                            };
                                            $payStatusLabel = match($res->payment_status) {
                                                'paid' => 'PAGADO',
                                                'refunded' => 'REEMBOLSADO',
                                                default => 'PENDIENTE',
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full font-mono-tech text-[9px] {{ $payStatusClass }}">
                                            {{ $payStatusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                         <div class="flex gap-2">
                                             @if($res->payment_status !== 'paid' && $res->status !== 'Cancelada')
                                                 <button
                                                     wire:click="generatePaymentLink('{{ $res->booking_group_id }}', {{ $res->total_price }}, {{ $res->user_id }})"
                                                     class="p-1.5 rounded text-amber-400 hover:bg-amber-500/10"
                                                     title="Generar link de pago">
                                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                         <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                                         <path
                                                             d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                                                     </svg>
                                                 </button>
                                             @endif
                                             @if($res->payment_status === 'paid' && $res->status !== 'Cancelada' && $res->status !== 'Reembolsada')
                                                 <button wire:click="requestRefund({{ $res->id }})"
                                                     class="p-1.5 rounded text-rose-400 hover:bg-rose-500/10"
                                                     title="Solicitar Reembolso (Cancelar Reserva)">
                                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                             d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                                     </svg>
                                                 </button>
                                             @endif
                                             @if(!empty($res->stripe_receipt_url) || !empty($res->stripe_receipts))
                                                 <button wire:click="viewReceipts({{ $res->id }})" class="p-1.5 rounded text-cyan-400 hover:bg-cyan-500/10" title="Ver Videos y Facturas">
                                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                 </button>
                                             @endif
                                         </div>
                                     </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-zinc-500 text-sm">No hay reservas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="tech-card rounded-xl p-5">
                <h2 class="font-black uppercase tracking-widest text-emerald-400 mb-4 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tareas de Reembolso
                </h2>
                <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($refundTasks ?? [] as $task)
                        <div wire:key="task-{{ $task->id }}" class="p-3 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 transition-colors group">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-[11px]" style="color:var(--text-primary)">{{ $task->title }}</span>
                                <span class="font-mono-tech text-[8px] px-1.5 py-0.5 rounded text-white bg-{{ $task->priorityColor() }}-500/80">
                                    {{ strtoupper($task->priority) }}
                                </span>
                            </div>
                            <p class="text-[10px] text-zinc-400 leading-relaxed mb-3">{{ $task->description }}</p>
                            
                            <div class="flex items-center gap-2">
                                <select 
                                    wire:change="updateTaskStatus({{ $task->id }}, $event.target.value)"
                                    class="flex-1 bg-black/40 border border-white/10 rounded px-2 py-1 text-[9px] text-zinc-300 outline-none focus:border-emerald-500/50 transition-colors"
                                >
                                    <option value="Pendiente" {{ $task->status === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="Aceptada" {{ $task->status === 'Aceptada' ? 'selected' : '' }}>Aceptada</option>
                                    <option value="En progreso" {{ $task->status === 'En progreso' ? 'selected' : '' }}>En progreso</option>
                                    <option value="Completada" {{ $task->status === 'Completada' ? 'selected' : '' }}>Completada</option>
                                </select>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-500 text-center py-8">No hay tareas de reembolso pendientes.</p>
                    @endforelse
                </div>
            </div>

            <div class="tech-card rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-mono-tech text-[11px] uppercase tracking-widest text-zinc-400">Reembolsos
                        Solicitados</h2>
                    <span
                        class="text-[9px] px-2 py-0.5 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-full font-black">AUDIT</span>
                </div>
                @if($refundRequests->isEmpty())
                    <p class="text-zinc-600 text-sm text-center py-4">No has solicitado reembolsos</p>
                @else
                    <div class="space-y-3">
                        @foreach($refundRequests as $req)
                            <div wire:key="refund-req-{{ $req->id }}" class="p-3 rounded-lg border border-white/5 bg-white/2 hover:bg-white/3 transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-mono-tech text-[9px] text-cyan-400">{{ strtoupper(substr($req->reservation?->id_locator ?? 'N/A', 0, 8)) }}</span>
                                    @php
                                        $statusClass = match($req->status) {
                                            'Pendiente' => 'bg-amber-500/10 text-amber-400',
                                            'Aprobado'  => 'bg-emerald-500/10 text-emerald-400',
                                            'Rechazado' => 'bg-rose-500/10 text-rose-400',
                                            default     => 'bg-zinc-500/10 text-zinc-400',
                                        };
                                    @endphp
                                    <span class="font-mono-tech text-[8px] px-2 py-0.5 rounded {{ $statusClass }}">
                                        {{ strtoupper($req->status) }}
                                    </span>
                                </div>
                                <p class="text-xs font-semibold" style="color: var(--text-primary)">{{ $req->reservation?->user?->name ?? '—' }}</p>

                                <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-white/5">
                                    <div>
                                        <span class="text-[8px] text-zinc-500 uppercase block">Devolución</span>
                                        <span class="font-mono-tech text-[11px] text-emerald-400 font-bold">{{ number_format($req->refund_amount, 2) }} €</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[8px] text-zinc-500 uppercase block">Retención</span>
                                        <span class="font-mono-tech text-[11px] text-rose-400 font-bold">{{ number_format($req->penalty_amount, 2) }} €</span>
                                    </div>
                                </div>

                                @if($req->status === 'Pendiente')
                                    <div class="flex gap-2 mt-3">
                                        <button 
                                            wire:click="confirmExecuteRefund({{ $req->id }})"
                                            wire:loading.attr="disabled"
                                            class="flex-[3] py-2 rounded bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-tighter border border-emerald-500/20 hover:bg-emerald-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span wire:loading.remove wire:target="confirmExecuteRefund({{ $req->id }})">Ejecutar</span>
                                            <span wire:loading wire:target="confirmExecuteRefund({{ $req->id }})">...</span>
                                        </button>
                                        <button 
                                            wire:click="rejectRefund({{ $req->id }})"
                                            wire:loading.attr="disabled"
                                            class="flex-1 py-2 rounded bg-rose-500/10 text-rose-400 text-[10px] font-black uppercase tracking-tighter border border-rose-500/20 hover:bg-rose-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                            title="Rechazar solicitud">
                                            <span wire:loading.remove wire:target="rejectRefund({{ $req->id }})">X</span>
                                            <span wire:loading wire:target="rejectRefund({{ $req->id }})">...</span>
                                        </button>
                                    </div>
                                @endif

                                @if($req->admin_notes)
                                    <div class="mt-2 text-[10px] text-zinc-400 bg-black/20 p-2 rounded italic border-l-2 border-amber-500/50">
                                        <span class="text-amber-400 font-bold not-italic">Admin:</span> {{ $req->admin_notes }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="tech-card rounded-xl p-5 border-cyan-500/10 bg-cyan-500/5">
                <h2 class="font-black uppercase tracking-widest text-cyan-400 mb-4 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Resumen de Política
                </h2>
                <div class="space-y-4 text-[10px] text-zinc-400">
                    <div>
                        <p class="font-bold text-zinc-300 uppercase mb-1">Escala de Reembolso (Con Seguro)</p>
                        <ul class="space-y-1">
                            <li class="flex justify-between"><span>> 30 días</span> <span
                                    class="text-emerald-400 font-bold">90%</span></li>
                            <li class="flex justify-between"><span>30 - 7 días</span> <span
                                    class="text-amber-400 font-bold">50%</span></li>
                            <li class="flex justify-between"><span>7d - 72h</span> <span
                                    class="text-orange-400 font-bold">10%</span></li>
                            <li class="flex justify-between"><span>&lt; 72h</span> <span class="text-rose-400 font-bold">0%</span></li>
                        </ul>
                    </div>
                    <div class="pt-2 border-t border-white/5">
                        <p class="font-bold text-zinc-300 uppercase mb-1">No Reembolsables</p>
                        <p>Hoteles, Vuelos Terrestres, Training aprobado/realizado.</p>
                    </div>
                    <button wire:click="$set('showPolicyModal', true)"
                        class="w-full py-2 bg-white/5 hover:bg-white/10 rounded text-[9px] uppercase font-black transition-colors">Ver
                        Política Completa</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Link Pago --}}
    @if($showPaymentLinkModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="tech-card p-6 rounded-xl w-full max-w-md" style="border-color:rgba(245,158,11,0.3)">
                <h3 class="font-black uppercase tracking-widest text-amber-400 mb-1 text-sm">Link de Pago Generado</h3>
                <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mb-4">Válido 7 días · Importe:
                    {{ number_format($generatedLinkAmount ?? 0, 2) }} €</p>
                <div class="flex items-center gap-2 px-3 py-3 rounded-lg bg-white/5 border border-white/8 mb-4">
                    <input type="text" readonly value="{{ $generatedLink }}"
                        class="flex-1 bg-transparent text-xs text-zinc-300 outline-none truncate">
                    <button onclick="navigator.clipboard.writeText('{{ $generatedLink }}')"
                        class="text-amber-400 hover:text-amber-300 transition-colors font-mono-tech text-[9px] uppercase">Copiar</button>
                </div>
                <button wire:click="closePaymentLinkModal"
                    class="w-full py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Cerrar</button>
            </div>
        </div>
    @endif

    {{-- Modal Reembolso --}}
    @if($showRefundModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-md" style="border-color:rgba(244,63,94,0.3)">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-black uppercase tracking-widest text-rose-400 text-sm">Solicitar Reembolso</h3>
                    <button wire:click="$set('showPolicyModal', true)"
                        class="text-[9px] font-bold uppercase text-cyan-400 bg-cyan-500/10 px-2 py-1 rounded hover:bg-cyan-500/20 transition-colors">
                        Ver Política
                    </button>
                </div>
                <p class="text-[10px] text-zinc-400 mb-4 leading-relaxed">Calcula la devolución según la política. La
                    reserva será cancelada tras confirmar.</p>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Porcentaje (%)</label>
                        <input wire:model.live.debounce.300ms="refundPercentage" type="number" step="0.01" min="0" max="100"
                            class="w-full px-3 py-2 rounded-lg text-xs"
                            style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                    </div>
                    <div>
                        <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Cantidad a devolver
                            (€)</label>
                        <input wire:model.live.debounce.300ms="refundAmount" type="number" step="0.01" min="0"
                            class="w-full px-3 py-2 rounded-lg text-xs"
                            style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                    </div>
                </div>

                <form wire:submit="submitRefund">
                    <div class="mb-4">
                        <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Notas para
                            Administración</label>
                        <textarea wire:model="refundNotes" rows="3" class="w-full px-3 py-2 rounded-lg text-xs resize-none"
                            style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)"
                            placeholder="Explica el motivo de la cancelación y reembolso..."></textarea>
                        @error('refundNotes') <span class="text-[9px] text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 py-2.5 rounded-lg text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30 hover:bg-rose-500/30 transition-colors">Confirmar
                            y Cancelar Reserva</button>
                        <button type="button" wire:click="$set('showRefundModal', false)"
                            class="flex-1 py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Atrás</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Política de Reembolso --}}
    @if($showPolicyModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-2xl max-h-[85vh] overflow-y-auto"
                style="border-color:rgba(6,182,212,0.3)">
                <div class="flex justify-between items-center mb-6 pb-3 border-b border-white/10">
                    <h3 class="font-black uppercase tracking-[0.1em] text-cyan-400 text-lg">Política de Cancelación y
                        Reembolso</h3>
                    <button wire:click="$set('showPolicyModal', false)"
                        class="text-zinc-500 hover:text-white text-xl">&times;</button>
                </div>

                <div class="space-y-6 text-sm leading-relaxed" style="color:var(--text-secondary)">
                    <div>
                        <h4 class="font-bold text-emerald-400 mb-2 text-xs uppercase tracking-widest">1. Baremo de Reembolso
                        </h4>
                        <p class="mb-2">Si la reserva incluye el Seguro de Reembolso, se aplicará la siguiente escala de
                            devolución sobre los servicios propios de Iris Aerospace (vuelo espacial):</p>
                        <ul class="list-disc pl-5 space-y-1 text-xs">
                            <li><strong style="color:var(--text-primary)">Más de 30 días:</strong> Reembolso del 90%.</li>
                            <li><strong style="color:var(--text-primary)">Entre 30 y 7 días:</strong> Reembolso del 50%.
                            </li>
                            <li><strong style="color:var(--text-primary)">Entre 7 días y 72 horas:</strong> Reembolso del
                                10%.</li>
                            <li><strong style="color:var(--text-primary)">Menos de 72 horas:</strong> Sin derecho a
                                reembolso (0%).</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-emerald-400 mb-2 text-xs uppercase tracking-widest">2. Cancelación por
                            Incumplimiento</h4>
                        <p>El pasajero debe disponer de Pasaporte e Iris Training 72h antes del despegue. Si no se cumplen
                            estos requisitos, el sistema anulará la plaza automáticamente.</p>
                        <p class="mt-1 text-rose-400 text-xs font-bold">Penalización: Retención del 100% del importe total
                            por bloqueo de recursos y ventana de lanzamiento.</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-emerald-400 mb-2 text-xs uppercase tracking-widest">3. Ejecución y Plazos
                        </h4>
                        <ul class="list-disc pl-5 space-y-1 text-xs">
                            <li><strong style="color:var(--text-primary)">Método:</strong> Devolución automática al método
                                original de pago.</li>
                            <li><strong style="color:var(--text-primary)">Plazo:</strong> Hasta 30 días naturales para el
                                procesamiento bancario.</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-emerald-400 mb-2 text-xs uppercase tracking-widest">4. Servicios Externos
                            (No Reembolsables)</h4>
                        <p class="mb-2">Los siguientes servicios no admiten devolución bajo ninguna circunstancia una vez
                            confirmada la reserva:</p>
                        <ul class="list-disc pl-5 space-y-1 text-xs">
                            <li>Vuelo Terrestre (Conexión): Billetes de avión.</li>
                            <li>Alojamiento (Hotel): Reservas de estancia externa.</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-emerald-400 mb-2 text-xs uppercase tracking-widest">5. Casos Especiales de
                            Gestión</h4>
                        <ul class="list-disc pl-5 space-y-2 text-xs">
                            <li><strong style="color:var(--text-primary)">Iris Training:</strong> No se reembolsará si el
                                día de entrenamiento ya ha sido aprobado, si el proceso está en curso o si ya ha sido
                                realizado.</li>
                            <li><strong style="color:var(--text-primary)">Gestión de Pasaporte Espacial:</strong> No se
                                reembolsará una vez iniciado el proceso de gestión administrativa.</li>
                        </ul>
                    </div>

                    <div class="p-4 bg-cyan-500/10 border border-cyan-500/20 rounded-lg">
                        <h4 class="font-bold text-cyan-400 mb-1 text-xs uppercase tracking-widest">6. Garantía Iris
                            Aerospace</h4>
                        <p class="text-xs">Se emitirá un reembolso del 100% o reubicación gratuita solo si la cancelación es
                            por motivos técnicos de la Iris Aerospace o condiciones meteorológicas espaciales adversas.</p>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-white/10 text-right">
                    <button wire:click="$set('showPolicyModal', false)"
                        class="px-6 py-2 rounded-lg text-xs font-bold bg-white/5 hover:bg-white/10 text-white transition-colors">Entendido</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Confirmación Ejecución Reembolso --}}
    @if($showExecuteConfirmModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-sm border-emerald-500/30 text-center"
                style="background: var(--bg-panel); color: var(--text-primary);">
                <div
                    class="w-16 h-16 mx-auto bg-emerald-500/20 rounded-full flex items-center justify-center mb-4 text-emerald-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-black uppercase tracking-widest text-emerald-400 mb-2 text-lg">Confirmar Liquidación</h3>
                <p class="text-xs mb-6 leading-relaxed" style="color: var(--text-secondary)">
                    ¿Estás seguro de ejecutar este reembolso? <br>
                    Se generarán los <span class="text-emerald-400 font-bold">registros contables</span> en finanzas y se
                    notificará al cliente la resolución de su solicitud.
                </p>

                <div class="flex gap-3">
                    <button wire:click="$set('showExecuteConfirmModal', false)"
                        class="flex-1 py-2.5 rounded-lg text-xs font-bold border border-white/10 hover:bg-white/5 transition-colors text-zinc-400">
                        Cancelar
                    </button>
                    <button wire:click="executeRefund" wire:loading.attr="disabled"
                        wire:loading.attr="disabled"
                        class="flex-[2] py-2.5 rounded-lg text-xs font-bold bg-emerald-500 text-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="executeRefund">Sí, Ejecutar Reembolso</span>
                        <span wire:loading wire:target="executeRefund">Liquidando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showReceiptsModal && $receiptsReservation)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="tech-card p-0 rounded-2xl w-full max-w-lg border-cyan-500/30 overflow-hidden shadow-2xl shadow-cyan-500/10" style="background: var(--bg-panel);">
                <div class="p-5 border-b border-white/5 bg-gradient-to-r from-cyan-500/10 to-transparent flex justify-between items-center">
                    <div>
                        <h3 class="font-black uppercase tracking-widest text-cyan-400 text-sm">Historial Financiero Stripe</h3>
                        <p class="text-[9px] font-mono-tech text-zinc-500 uppercase mt-0.5">Reserva: {{ strtoupper(substr($receiptsReservation->id_locator, 0, 8)) }}</p>
                    </div>
                    <button wire:click="closeReceiptsModal" class="text-zinc-500 hover:text-white transition-colors">✕</button>
                </div>
                <div class="p-5 space-y-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    @forelse($receiptsList as $receipt)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5 hover:bg-white/8 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ ($receipt['type'] ?? '') === 'refund' ? 'bg-rose-500/20 text-rose-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                                    @if(($receipt['type'] ?? '') === 'refund')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs font-bold" style="color: var(--text-primary)">{{ $receipt['description'] ?? 'Documento Stripe' }}</p>
                                    <p class="text-[9px] text-zinc-500">{{ \Carbon\Carbon::parse($receipt['date'] ?? now())->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right flex flex-col items-end gap-1">
                                <span class="font-mono-tech text-[11px] font-black {{ ($receipt['type'] ?? '') === 'refund' ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ ($receipt['type'] ?? '') === 'refund' ? '-' : '+' }} {{ number_format($receipt['amount'] ?? 0, 2) }} €
                                </span>
                                @if(!empty($receipt['url']) && $receipt['url'] !== '#')
                                    <a href="{{ $receipt['url'] }}" target="_blank" 
                                       class="px-3 py-1.5 rounded-lg bg-cyan-500/10 text-cyan-400 text-[9px] font-black uppercase hover:bg-cyan-500 hover:text-black transition-all border border-cyan-500/20">
                                        Abrir Factura
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-zinc-500 text-xs italic text-center py-10">No hay documentos financieros registrados.</p>
                    @endforelse
                </div>
                <div class="p-5 border-t border-white/5">
                    <button wire:click="closeReceiptsModal" class="w-full py-2.5 rounded-xl text-[10px] font-black uppercase border border-white/10 hover:bg-white/5 transition-all text-zinc-400">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>