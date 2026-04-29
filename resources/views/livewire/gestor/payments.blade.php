<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Pagos y Reembolsos</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Gestión financiera de tus clientes</p>
        </div>
    </div>

    @if(session('message'))
        <div class="px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">{{ session('message') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
            <p class="text-xl font-black mt-1" style="color: var(--text-primary)">{{ number_format($stats['revenue'], 2) }} €</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Reservas / Pagos --}}
        <div class="xl:col-span-2 space-y-4">
            <div class="flex items-center gap-3">
                <input wire:model.live="search" type="text" placeholder="Buscar reserva..."
                    class="flex-1 px-3 py-2 rounded-lg text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
                <select wire:model.live="filterPayment" class="px-3 py-2 rounded-lg text-xs" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)">
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
                                <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase text-zinc-500">Localizador / Vuelo</th>
                                <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase text-zinc-500">Cliente</th>
                                <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase text-zinc-500">Importe</th>
                                <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase text-zinc-500">Estado Pago</th>
                                <th class="text-left px-4 py-3 font-mono-tech text-[9px] uppercase text-zinc-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $res)
                                <tr class="border-b border-white/3 hover:bg-white/2 transition-colors">
                                    <td class="px-4 py-3">
                                        <p class="font-mono-tech text-cyan-400">{{ strtoupper(substr($res->id_locator, 0, 8)) }}</p>
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
                                        <span class="px-2 py-0.5 rounded-full font-mono-tech text-[9px] {{ $res->payment_status === 'paid' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                            {{ $res->payment_status === 'paid' ? 'PAGADO' : 'PENDIENTE' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            @if($res->payment_status !== 'paid' && $res->status !== 'Cancelada')
                                                <button wire:click="generatePaymentLink('{{ $res->booking_group_id }}', {{ $res->total_price }}, {{ $res->user_id }})" class="p-1.5 rounded text-amber-400 hover:bg-amber-500/10" title="Generar link de pago">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                                </button>
                                            @endif
                                            @if($res->payment_status === 'paid' && $res->status !== 'Cancelada')
                                                <button wire:click="requestRefund({{ $res->id }})" class="p-1.5 rounded text-rose-400 hover:bg-rose-500/10" title="Solicitar Reembolso (Cancelar Reserva)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-zinc-500 text-sm">No hay reservas</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Solicitudes de Reembolso --}}
        <div class="tech-card rounded-xl p-5 self-start">
            <h2 class="font-mono-tech text-[11px] uppercase tracking-widest text-zinc-400 mb-4">Reembolsos Solicitados</h2>
            @if($refundRequests->isEmpty())
                <p class="text-zinc-600 text-sm text-center py-4">No has solicitado reembolsos</p>
            @else
                <div class="space-y-3">
                    @foreach($refundRequests as $req)
                        <div class="p-3 rounded-lg border border-white/5 bg-white/2">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono-tech text-[9px] text-cyan-400">{{ strtoupper(substr($req->reservation?->id_locator ?? 'N/A', 0, 8)) }}</span>
                                <span class="font-mono-tech text-[8px] px-2 py-0.5 rounded
                                    {{ $req->status === 'Pendiente' ? 'bg-amber-500/10 text-amber-400' :
                                       ($req->status === 'Aprobado' ? 'bg-emerald-500/10 text-emerald-400' :
                                       ($req->status === 'Rechazado' ? 'bg-rose-500/10 text-rose-400' : 'bg-zinc-500/10 text-zinc-400')) }}">
                                    {{ strtoupper($req->status) }}
                                </span>
                            </div>
                            <p class="text-xs font-semibold" style="color: var(--text-primary)">{{ $req->reservation?->user?->name ?? '—' }}</p>
                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-white/5">
                                <span class="text-[9px] text-zinc-500">Devolución:</span>
                                <span class="font-mono-tech text-[10px] text-emerald-400">{{ number_format($req->refund_amount, 2) }} €</span>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-[9px] text-zinc-500">Penalización:</span>
                                <span class="font-mono-tech text-[10px] text-rose-400">{{ number_format($req->penalty_amount, 2) }} €</span>
                            </div>
                            @if($req->admin_notes)
                                <div class="mt-2 text-[10px] text-zinc-400 bg-black/20 p-2 rounded">
                                    <span class="text-amber-400 font-bold">Admin:</span> {{ $req->admin_notes }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Link Pago --}}
    @if($showPaymentLinkModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="tech-card p-6 rounded-xl w-full max-w-md" style="border-color:rgba(245,158,11,0.3)">
                <h3 class="font-black uppercase tracking-widest text-amber-400 mb-1 text-sm">Link de Pago Generado</h3>
                <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mb-4">Válido 7 días · Importe: {{ number_format($generatedLinkAmount ?? 0, 2) }} €</p>
                <div class="flex items-center gap-2 px-3 py-3 rounded-lg bg-white/5 border border-white/8 mb-4">
                    <input type="text" readonly value="{{ $generatedLink }}" class="flex-1 bg-transparent text-xs text-zinc-300 outline-none truncate">
                    <button onclick="navigator.clipboard.writeText('{{ $generatedLink }}')" class="text-amber-400 hover:text-amber-300 transition-colors font-mono-tech text-[9px] uppercase">Copiar</button>
                </div>
                <button wire:click="closePaymentLinkModal" class="w-full py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Cerrar</button>
            </div>
        </div>
    @endif

    {{-- Modal Reembolso --}}
    @if($showRefundModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-md" style="border-color:rgba(244,63,94,0.3)">
                <h3 class="font-black uppercase tracking-widest text-rose-400 mb-2 text-sm">Solicitar Reembolso</h3>
                <p class="text-xs text-zinc-400 mb-4">Al enviar esta solicitud, la reserva será cancelada y el Super Admin decidirá sobre la devolución económica.</p>
                
                <div class="bg-black/30 p-3 rounded-lg border border-white/5 mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] text-zinc-500 uppercase font-mono-tech">Importe a devolver estimado:</span>
                        <span class="font-mono-tech text-emerald-400 font-bold">{{ number_format($refundAmount, 2) }} €</span>
                    </div>
                    <p class="text-[9px] text-zinc-500 italic">La penalización ya ha sido descontada según la política de seguro del cliente.</p>
                </div>

                <form wire:submit="submitRefund">
                    <div class="mb-4">
                        <label class="block font-mono-tech text-[9px] text-zinc-500 uppercase mb-1">Notas para Administración</label>
                        <textarea wire:model="refundNotes" rows="3" class="w-full px-3 py-2 rounded-lg text-xs resize-none" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:var(--text-primary)" placeholder="Explica el motivo de la cancelación y reembolso..."></textarea>
                        @error('refundNotes') <span class="text-[9px] text-rose-400">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 py-2.5 rounded-lg text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30 hover:bg-rose-500/30 transition-colors">Confirmar y Cancelar Reserva</button>
                        <button type="button" wire:click="$set('showRefundModal', false)" class="flex-1 py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">Atrás</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
