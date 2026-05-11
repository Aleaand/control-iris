<div class="p-6">
    <style>
        select option {
            background-color: #18181b !important; /* zinc-900 */
            color: #ffffff !important;
        }
    </style>
    <div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.15em]" style="color: var(--text-primary)">Central de Reservas</h1>
            <p class="font-mono-tech text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Reservas de tus clientes</p>
        </div>
        <button wire:click="openCreateModal" class="px-4 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-500/20 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva Reserva
        </button>
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
            <option value="Confirmada">Confirmada</option>
            <option value="Reembolsada">Reembolsada</option>
            <option value="Pendiente">Pendiente</option>
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
                                                                                                                                    {{ $res->status === 'GO' ? 'bg-emerald-500/20 text-emerald-300' :
                        ($res->status === 'Cancelada' ? 'bg-rose-500/10 text-rose-400' :
                            ($res->status === 'Confirmada' ? 'bg-cyan-500/10 text-cyan-400' :
                                ($res->status === 'Pendiente' ? 'bg-rose-500/10 text-rose-400' : 'bg-zinc-700/30 text-zinc-400'))) }}">
                                                                                                                                    {{ $res->status }}
                                                                                                                                </span>
                                                                                                                            </td>
                                                                                                                            <td class="px-4 py-3">
                                                                                                                                <div class="flex items-center gap-1">
                                                                                                                                    <button wire:click="viewDetail({{ $res->id }})" title="Ver detalle"
                                                                                                                                        class="p-1.5 rounded-lg text-cyan-400 hover:bg-cyan-500/10 transition-all">
                                                                                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                                                                                                                    </button>

                                                                                                                                    @php $canPay = ($res->payment_status !== 'paid' && $res->status !== 'Cancelada'); @endphp
                                                                                                                                    <button wire:click="generatePayLink({{ $res->id }})" title="Generar link de pago"
                                                                                                                                        @if(!$canPay) disabled @endif
                                                                                                                                        class="p-1.5 rounded-lg transition-all {{ $canPay ? 'text-amber-400 hover:bg-amber-500/10' : 'text-zinc-700 opacity-30 cursor-not-allowed' }}">
                                                                                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                                                                                                                    </button>

                                                                                                                                    @if(!empty($res->stripe_receipt_url) || !empty($res->stripe_receipts))
                                                                                                                                        <button wire:click="viewReceipts({{ $res->id }})" title="VerFacturas"
                                                                                                                                            class="p-1.5 rounded-lg text-cyan-400 hover:bg-cyan-500/10 transition-all">
                                                                                                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                                                                                                        </button>
                                                                                                                                    @endif
                                                                                                                                    @php $canUpgrade = ($res->status !== 'GO' && $res->status !== 'Cancelada'); @endphp
                                                                                                                                    <button wire:click="openUpgradeModal({{ $res->id }})" title="Upgrade & Servicios Extra"
                                                                                                                                        @if(!$canUpgrade) disabled @endif
                                                                                                                                        class="p-1.5 rounded-lg transition-all {{ $canUpgrade ? 'text-emerald-400 hover:bg-emerald-500/10' : 'text-zinc-700 opacity-30 cursor-not-allowed' }}">
                                                                                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                                                                                                    </button>

                                                                                                                                    {{-- 6. Modificar --}}
                                                                                                                                    @php $canModify = ($res->status !== 'GO' && $res->status !== 'Cancelada'); @endphp
                                                                                                                                    <button wire:click="openModifyModal({{ $res->id }})" title="Modificar Vuelo"
                                                                                                                                        @if(!$canModify) disabled @endif
                                                                                                                                        class="p-1.5 rounded-lg transition-all {{ $canModify ? 'text-cyan-400 hover:bg-cyan-500/10' : 'text-zinc-700 opacity-30 cursor-not-allowed' }}">
                                                                                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                                                                                    </button>

                                                                                                                                    {{-- 7. Cancelar --}}
                                                                                                                                    @php $canDelete = ($res->status !== 'Cancelada'); @endphp
                                                                                                                                    <button wire:click="confirmDelete({{ $res->id }})" title="Cancelar"
                                                                                                                                        @if(!$canDelete) disabled @endif
                                                                                                                                        class="p-1.5 rounded-lg transition-all {{ $canDelete ? 'text-rose-400 hover:bg-rose-500/10' : 'text-zinc-700 opacity-30 cursor-not-allowed' }}">
                                                                                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                                                                                                                    </button>
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

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $reservations->links('vendor.livewire.simple-tailwind') }}
        </div>
    </div>



    {{-- Pay Link Modal --}}
    @if($showPayLinkModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="tech-card p-6 rounded-xl w-full max-w-md" style="border-color:rgba(245,158,11,0.3)">
                <h3 class="font-black uppercase tracking-widest text-amber-400 mb-1 text-sm">Link de Pago Generado</h3>
                <p class="font-mono-tech text-[9px] text-zinc-500 uppercase mb-4">Válido 7 días · Importe: {{ number_format($payLinkAmount, 2) }} €</p>
                <div class="flex items-center gap-2 px-3 py-3 rounded-lg bg-white/5 border border-white/8 mb-4" x-data="{ copied: false }">
                    <input type="text" readonly id="pay-link-input" value="{{ $payLinkUrl }}" class="flex-1 bg-transparent text-xs text-zinc-300 outline-none truncate">
                    <button @click="const input = document.getElementById('pay-link-input'); input.select(); navigator.clipboard.writeText('{{ $payLinkUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="transition-colors font-mono-tech text-[9px] uppercase"
                        :class="copied ? 'text-emerald-400' : 'text-amber-400 hover:text-amber-300'">
                        <span x-text="copied ? '¡Copiado!' : 'Copiar'"></span>
                    </button>
                </div>

                @if(session('pay_message'))
                    <div class="mb-4 px-3 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs text-center font-semibold">
                        {{ session('pay_message') }}
                    </div>
                @endif

                <div class="flex gap-2">
                    <button wire:click="sendPaymentEmail" class="flex-1 py-2.5 rounded-lg text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30 hover:bg-amber-500/30 transition-colors">
                        Enviar al Cliente
                    </button>
                    <button wire:click="closePayLinkModal" class="flex-1 py-2.5 rounded-lg text-xs font-bold text-zinc-400 border border-white/5 hover:bg-white/5 transition-colors">
                        Cerrar
                    </button>
                </div>
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
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div class="tech-card p-0 rounded-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden border-cyan-500/30">
                {{-- Header Detalle --}}
                <div class="p-6 border-b border-white/5 bg-gradient-to-r from-cyan-500/10 to-transparent flex justify-between items-center">
                    <div>
                        <h3 class="font-black uppercase tracking-widest text-cyan-400 text-lg">Expediente de Vuelo</h3>
                        <p class="text-[9px] font-mono-tech text-zinc-500 uppercase">Localizador: {{ strtoupper(substr($detailReservation->id_locator, 0, 8)) }}</p>
                    </div>
                    <button wire:click="closeDetail" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/5 text-zinc-500 transition-colors">✕</button>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">
                    @if(session('detail_message'))
                        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] p-3 rounded-lg text-center uppercase font-bold animate-pulse">
                            {{ session('detail_message') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <section>
                                <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-3 border-b border-white/5 pb-1">Identidad</h4>
                                <div class="space-y-2">
                                    <div>
                                        <p class="text-[8px] text-zinc-600 uppercase">Titular de Cuenta</p>
                                        <p class="text-xs font-bold text-white uppercase">{{ $detailReservation->user?->name }} {{ $detailReservation->user?->primarylastname }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[8px] text-zinc-600 uppercase">Pasajero Asignado</p>
                                        <p class="text-xs font-bold text-cyan-400 uppercase">{{ $detailReservation->passenger?->full_name }}</p>
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-3 border-b border-white/5 pb-1">Itinerario</h4>
                                <div class="space-y-2">
                                    <div>
                                        <p class="text-[8px] text-zinc-600 uppercase">Destino de Vuelo</p>
                                        <p class="text-xs font-bold text-white uppercase">{{ $detailReservation->spaceFlight?->destination?->name }}</p>
                                    </div>
                                    <div class="flex justify-between">
                                        <div>
                                            <p class="text-[8px] text-zinc-600 uppercase">Fecha</p>
                                            <p class="text-xs font-mono-tech text-white">{{ $detailReservation->spaceFlight?->departure_date?->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[8px] text-zinc-600 uppercase">Clase</p>
                                            <span class="text-[9px] font-black px-2 py-0.5 rounded {{ $detailReservation->seat_type === 'supernova' ? 'bg-violet-500/20 text-violet-400' : 'bg-cyan-500/20 text-cyan-400' }}">
                                                {{ strtoupper($detailReservation->seat_type) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                        {{-- Col Derecha: Logística y Extras --}}
                        <div class="space-y-6">
                            <section>
                                <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-3 border-b border-white/5 pb-1">Logística</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <p class="text-[8px] text-zinc-600 uppercase leading-none mb-1">Hotel</p>
                                            <p class="text-[10px] text-white font-bold">{{ $detailReservation->logistics?->hotel?->name ?? 'No contratado' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <p class="text-[8px] text-zinc-600 uppercase leading-none mb-1">Traslado / Enlace</p>
                                            <p class="text-[10px] text-white font-bold">
                                                {{ $detailReservation->logistics?->terrestrialFlight ? 'Confirmado' : ($detailReservation->logistics?->vip_transfer_included ? 'VIP Transfer' : 'Estándar') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-3 border-b border-white/5 pb-1">Servicios Extra</h4>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2 py-1 rounded text-[8px] font-bold {{ $detailReservation->logistics?->training_included ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-white/3 text-zinc-600 grayscale' }}">IRIS TRAINING</span>
                                    <span class="px-2 py-1 rounded text-[8px] font-bold {{ $detailReservation->logistics?->passport_management_included ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-white/3 text-zinc-600 grayscale' }}">PASAPORTE</span>
                                </div>
                            </section>

                            <section>
                                <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-3 border-b border-white/5 pb-1">Auditoría</h4>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-[9px]">
                                        <span class="text-zinc-500">Upgrades:</span>
                                        <span class="text-white font-bold">{{ $detailReservation->adendas->count() }}</span>
                                    </div>
                                    <div class="flex justify-between text-[9px]">
                                        <span class="text-zinc-500">Facturas:</span>
                                        <span class="text-cyan-400 font-mono-tech">{{ strtoupper(substr($detailReservation->id_locator, 0, 5)) }}@foreach($detailReservation->adendas as $ad), {{ strtoupper(substr($ad->id_locator, 0, 5)) }}@endforeach</span>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    {{-- Footer de Detalles --}}
                    <div class="bg-black/40 border border-white/5 rounded-xl p-5 flex items-center justify-between">
                        <div>
                            <p class="text-[8px] uppercase text-zinc-600 mb-1">Importe Total Facturado</p>
                            <p class="text-2xl font-mono-tech font-black text-emerald-400">{{ number_format($detailReservation->total_price, 2) }} €</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full font-mono-tech text-[10px] font-bold
                                {{ $detailReservation->payment_status === 'paid' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400 animate-pulse' }}">
                                {{ strtoupper($detailReservation->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Acciones Finales --}}
                <div class="p-6 border-t border-white/5 bg-white/2 flex gap-3">
                    <a href="{{ route('gestor.reservations.ticket', $detailReservation->id) }}" target="_blank"
                        class="flex-1 py-3 rounded-xl bg-violet-600 text-white font-bold text-[10px] uppercase tracking-widest hover:bg-violet-500 transition-all text-center flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Ver Ticket de Reserva
                    </a>
                    @if($detailReservation->payment_status !== 'paid')
                        <button wire:click="sendReminderEmail" class="flex-1 py-3 rounded-xl border border-amber-500/30 text-amber-400 font-bold text-[10px] uppercase tracking-widest hover:bg-amber-500/10 transition-all">
                            Recordatorio Email
                        </button>
                        <button wire:click="processManualPayment" class="flex-1 py-3 rounded-xl bg-emerald-600 text-white font-bold text-[10px] uppercase tracking-widest hover:bg-emerald-500 transition-all">
                            Procesar Pago
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modify Modal --}}
    @if($showModifyModal && $modifyingReservation)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div class="tech-card p-6 rounded-2xl w-full max-w-lg border-amber-500/30 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-black uppercase tracking-widest text-amber-400 text-lg">Modificar Reserva</h3>
                        <p class="text-[9px] text-zinc-500 uppercase font-mono-tech">Ajuste de vuelo para pax: {{ $modifyingReservation->passenger?->name }}</p>
                    </div>
                    <button wire:click="$set('showModifyModal', false)" class="text-zinc-500 hover:text-zinc-300">✕</button>
                </div>

                <div class="space-y-6">
                    <div class="bg-white/5 p-4 rounded-xl border border-white/5">
                        <p class="text-[10px] uppercase text-zinc-500 mb-2 tracking-widest font-black">Vuelo Actual</p>
                        <p class="text-xs text-white font-bold uppercase">#{{ $modifyingReservation->spaceFlight?->flight_code }} | {{ $modifyingReservation->spaceFlight?->destination?->name }}</p>
                    </div>

                    <div class="space-y-2 relative">
                        <label class="text-[10px] uppercase text-zinc-500 tracking-widest font-black">Nuevo Vuelo de Reserva</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" wire:model.live="modifySearchCode" placeholder="Código Vuelo..." class="bg-black/40 border border-white/10 rounded-lg px-3 py-2.5 text-xs text-white outline-none focus:border-amber-500/50">
                            <input type="text" wire:model.live="modifySearchDest" placeholder="Destino..." class="bg-black/40 border border-white/10 rounded-lg px-3 py-2.5 text-xs text-white outline-none focus:border-amber-500/50">
                        </div>

                        @if($modifySearchCode || $modifySearchDest)
                            <div class="absolute z-10 left-0 right-0 mt-1 bg-zinc-900 border border-white/10 rounded-xl shadow-2xl overflow-hidden">
                                @forelse($this->filteredModifyFlights as $f)
                                    <button wire:click="selectModifyFlight({{ $f->id }}, '#{{ $f->flight_code }} | {{ $f->destination?->name }}')" 
                                        class="w-full text-left px-4 py-3 hover:bg-amber-500/10 transition-colors border-b border-white/5 last:border-0">
                                        <div class="flex justify-between items-center">
                                            <p class="text-[10px] font-black text-amber-400 uppercase">#{{ $f->flight_code }} | {{ $f->destination?->name }}</p>
                                            <p class="text-[9px] text-zinc-500">{{ $f->departure_date?->format('d/m/Y') }}</p>
                                        </div>
                                    </button>
                                @empty
                                    <div class="p-4 text-center text-[9px] text-zinc-500 uppercase">Sin vuelos disponibles</div>
                                @endforelse
                            </div>
                        @endif

                        @if($modify_flight_id)
                            <div class="mt-4 p-3 rounded-lg bg-amber-500/5 border border-amber-500/20 flex items-center justify-between">
                                <span class="text-[10px] font-bold text-amber-400 uppercase">Seleccionado: {{ $selectedModifyFlightLabel }}</span>
                                <button wire:click="$set('modify_flight_id', null)" class="text-amber-400 hover:text-rose-400 transition-colors">✕</button>
                            </div>
                        @endif
                    </div>

                    <div class="p-4 bg-rose-500/5 border border-rose-500/20 rounded-xl">
                        <p class="text-[9px] text-rose-400 leading-relaxed uppercase font-bold">
                             Nota: Esta modificación reasigna el vuelo sin generar cargos adicionales. Los servicios logísticos (hotel, transfer) podrían requerir ajustes manuales tras el cambio.
                        </p>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button wire:click="executeModification" class="flex-1 py-4 bg-amber-500 text-black font-black uppercase tracking-[0.2em] rounded-xl hover:bg-amber-400 transition-all shadow-xl shadow-amber-500/20 text-xs">
                            Confirmar Cambio de Vuelo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Create Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-md p-4">
            <div class="tech-card p-6 rounded-xl w-full max-w-4xl max-h-[85vh] flex flex-col relative overflow-hidden border-emerald-500/30" style="background: var(--bg-panel); color: var(--text-primary);">

                {{-- Header Modal --}}
                <div class="flex items-center justify-between mb-6 border-b border-white/10 pb-4">
                    <div>
                        <h3 class="font-black uppercase tracking-widest text-emerald-400 text-lg">Nueva Reserva Grupal</h3>
                        <p class="text-[9px] font-mono-tech text-zinc-500 uppercase">Configuración de reserva</p>
                    </div>
                    <button wire:click="closeCreateModal" class="p-2 hover:bg-white/5 rounded-lg text-zinc-500 transition-colors">✕</button>
                </div>

                {{-- Scrollable Content --}}
                <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar space-y-8">

                    <div class="grid grid-cols-2 gap-6 bg-white/3 p-4 rounded-xl border border-white/5">
                        <div class="col-span-2 md:col-span-1 space-y-1">
                            <label class="block text-[10px] uppercase tracking-widest text-zinc-500 font-mono-tech">Cliente Titular</label>
                            <select wire:model.live="create_client_id" class="w-full bg-black/40 border border-white/10 rounded-lg px-3 py-2 text-xs text-white outline-none focus:border-emerald-500/50 transition-colors">
                                <option value="">Seleccione titular de cuenta...</option>
                                @foreach(\App\Models\User::where('assigned_manager_id', auth()->id())->get() as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->name }} {{ $c->primarylastname }} {{ $c->secondarylastname }} ({{ $c->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2 space-y-1 relative" x-data="{ open: @entangle('showFlightResults') }">
                            <label class="block text-[10px] uppercase tracking-widest text-zinc-500 font-mono-tech">Radar de Vuelos (IDA)</label>

                            @if($create_flight_id)
                                <div class="flex items-center justify-between bg-emerald-500/10 border border-emerald-500/30 rounded-lg px-3 py-2">
                                    <span class="text-xs text-emerald-400 font-bold uppercase tracking-tight">{{ $selectedFlightLabel }}</span>
                                    <button wire:click="$set('create_flight_id', null)" class="text-emerald-400 hover:text-rose-400 transition-colors">✕</button>
                                </div>
                            @else
                                <div class="grid grid-cols-5 gap-2">
                                    <input type="text" wire:model.live="flightSearchCode" @focus="open = true" placeholder="ID" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[9px] text-white outline-none focus:border-cyan-500/50">
                                    <input type="text" wire:model.live="flightSearchOrigin" @focus="open = true" placeholder="Origen" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[9px] text-white outline-none focus:border-cyan-500/50">
                                    <input type="text" wire:model.live="flightSearchDest" @focus="open = true" placeholder="Destino" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[9px] text-white outline-none focus:border-cyan-500/50">
                                    <input type="text" wire:model.live="flightSearchDep" @focus="open = true" placeholder="Despegue" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[9px] text-white outline-none focus:border-cyan-500/50">
                                    <input type="text" wire:model.live="flightSearchArr" @focus="open = true" placeholder="Aterrizaje" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[9px] text-white outline-none focus:border-cyan-500/50">
                                </div>
                                @if($showFlightResults && ($flightSearchCode || $flightSearchOrigin || $flightSearchDest || $flightSearchDep || $flightSearchArr))
                                    <div class="absolute z-[60] left-0 right-0 mt-1 bg-zinc-900 border border-white/10 rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                        @forelse($this->filteredFlights as $f)
                                            <button wire:click="selectFlight({{ $f->id }}, '#{{ $f->flight_code }} | {{ $f->origin?->name }} → {{ $f->destination?->name }}')" class="w-full text-left px-4 py-3 hover:bg-white/5 transition-colors border-b border-white/5 last:border-0">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="text-[10px] font-black text-cyan-400 uppercase">#{{ $f->flight_code }}</p>
                                                        <p class="text-[9px] text-zinc-400 uppercase">{{ $f->origin?->name }} → {{ $f->destination?->name }}</p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-[9px] text-zinc-500 uppercase">{{ $f->departure_date?->format('d/m/Y') }}</p>
                                                        <p class="text-[8px] text-zinc-600 uppercase">Llegada: {{ $f->arrival_date?->format('d/m/Y') }}</p>
                                                    </div>
                                                </div>
                                            </button>
                                        @empty
                                            <div class="p-4 text-center text-zinc-500 text-[10px] uppercase tracking-widest">Sin resultados en el radar</div>
                                        @endforelse
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- Vuelta --}}
                        <div class="col-span-2 border-t border-white/5 pt-4 mt-2 space-y-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="create_has_return_flight" class="w-4 h-4 rounded bg-black/40 border-white/10 text-cyan-500">
                                <span class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Añadir Vuelo de Retorno</span>
                            </label>

                            @if($create_has_return_flight)
                                <div class="relative" x-data="{ open: @entangle('showReturnFlightResults') }">
                                    @if($create_return_flight_id)
                                        <div class="flex items-center justify-between bg-cyan-500/10 border border-cyan-500/30 rounded-lg px-3 py-2">
                                            <span class="text-xs text-cyan-400 font-bold uppercase tracking-tight">{{ $selectedReturnFlightLabel }}</span>
                                            <button wire:click="$set('create_return_flight_id', null)" class="text-cyan-400 hover:text-rose-400 transition-colors">✕</button>
                                        </div>
                                    @else
                                        <div class="grid grid-cols-5 gap-2">
                                            <input type="text" wire:model.live="returnSearchCode" @focus="open = true" placeholder="ID" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[9px] text-white outline-none focus:border-cyan-500/50">
                                            <input type="text" wire:model.live="returnSearchOrigin" @focus="open = true" placeholder="Origen" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[9px] text-white outline-none focus:border-cyan-500/50">
                                            <input type="text" wire:model.live="returnSearchDest" @focus="open = true" placeholder="Destino" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[9px] text-white outline-none focus:border-cyan-500/50">
                                            <input type="text" wire:model.live="returnSearchDep" @focus="open = true" placeholder="Despegue" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[9px] text-white outline-none focus:border-cyan-500/50">
                                            <input type="text" wire:model.live="returnSearchArr" @focus="open = true" placeholder="Aterrizaje" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[9px] text-white outline-none focus:border-cyan-500/50">
                                        </div>
                                        @if($showReturnFlightResults && ($returnSearchCode || $returnSearchOrigin || $returnSearchDest || $returnSearchDep || $returnSearchArr))
                                            <div class="absolute z-[60] left-0 right-0 mt-1 bg-zinc-900 border border-white/10 rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                                @forelse($this->filteredReturnFlights as $f)
                                                    <button wire:click="selectReturnFlight({{ $f->id }}, '#{{ $f->flight_code }} | {{ $f->origin?->name }} → {{ $f->destination?->name }}')" class="w-full text-left px-4 py-3 hover:bg-white/5 transition-colors border-b border-white/5 last:border-0">
                                                        <div class="flex justify-between items-center">
                                                            <div>
                                                                <p class="text-[10px] font-black text-cyan-400 uppercase">#{{ $f->flight_code }}</p>
                                                                <p class="text-[9px] text-zinc-400 uppercase">{{ $f->origin?->name }} → {{ $f->destination?->name }}</p>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-[9px] text-zinc-500 uppercase">{{ $f->departure_date?->format('d/m/Y') }}</p>
                                                            </div>
                                                        </div>
                                                    </button>
                                                @empty
                                                    <div class="p-4 text-center text-zinc-500 text-[10px] uppercase tracking-widest">Sin opciones coherentes</div>
                                                @endforelse
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($create_client_id)
                        {{-- Paso 2: Selección de Pasajeros y Logística Individual --}}
                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b border-white/10 pb-2">
                                <h4 class="font-black uppercase tracking-widest text-zinc-400 text-xs flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Detalle de Pasajeros
                                </h4>
                                <div class="flex gap-2">
                                    @foreach($clientPassengers as $pax)
                                        @php $isSelected = collect($create_selected_passengers)->contains('passenger_id', $pax->id); @endphp
                                        <button wire:click="addPassengerToGroup({{ $pax->id }})" 
                                            class="px-3 py-1 rounded-full text-[10px] font-bold uppercase transition-all border 
                                            {{ $isSelected ? 'bg-emerald-500/20 border-emerald-500/50 text-emerald-400 opacity-50 cursor-not-allowed' : 'bg-white/5 border-white/10 text-zinc-400 hover:bg-white/10' }}">
                                            + {{ $pax->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="space-y-4">
                                @forelse($create_selected_passengers as $idx => $pax)
                                    <div wire:key="pax-{{ $idx }}-{{ $pax['passenger_id'] }}" class="bg-white/3 border border-white/10 rounded-xl p-4 relative overflow-hidden group">
                                        {{-- Warning Conflict --}}
                                        @if(isset($temporalConflicts[$pax['passenger_id']]))
                                            <div class="absolute top-0 right-0 bg-rose-500 text-white text-[8px] font-black px-3 py-1 uppercase tracking-tighter animate-pulse z-10">
                                                 {{ $temporalConflicts[$pax['passenger_id']] }}
                                            </div>
                                        @endif

                                        <button wire:click="removePassengerFromGroup({{ $idx }})" class="absolute top-4 right-4 text-rose-500/50 hover:text-rose-500 transition-colors">✕</button>

                                        <div class="grid grid-cols-12 gap-6">
                                                {{-- Info básica pax --}}
                                                <div class="col-span-12 md:col-span-3">
                                                    <p class="text-[10px] font-black uppercase text-emerald-400 mb-1">{{ $pax['name'] }}</p>
                                                    <select wire:model.live="create_selected_passengers.{{ $idx }}.seat_type" class="w-full bg-black/20 border border-white/10 rounded px-2 py-1 text-[10px] text-white">
                                                        <option value="">Sin Vuelo / Solo Extras</option>
                                                        <option value="nova">Clase Nova</option>
                                                        <option value="supernova">Clase Supernova</option>
                                                    </select>
                                                </div>

                                                {{-- Logística --}}
                                                <div class="col-span-12 md:col-span-6 grid grid-cols-2 gap-4 border-l border-r border-white/5 px-6">
                                                    <div class="space-y-3">
                                                        <label class="text-[8px] uppercase tracking-widest text-zinc-500 font-mono-tech">Alojamiento (Hotel)</label>
                                                        <select wire:model.live="create_selected_passengers.{{ $idx }}.hotel_id" class="w-full bg-black/20 border border-white/10 rounded px-2 py-1 text-[10px] text-white">
                                                            <option value="">Sin Hotel</option>
                                                            @foreach(\App\Models\Hotel::all() as $h)
                                                                <option value="{{ $h->id }}">{{ $h->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="grid grid-cols-2 gap-2 mt-1">
                                                            <div>
                                                                <span class="text-[7px] text-zinc-600 uppercase block mb-1">Entrada</span>
                                                                <input type="date" wire:model.live="create_selected_passengers.{{ $idx }}.hotel_checkin" class="w-full bg-black/40 border border-white/10 rounded text-[8px] text-white p-1">
                                                            </div>
                                                            <div>
                                                                <span class="text-[7px] text-zinc-600 uppercase block mb-1">Salida</span>
                                                                <input type="date" wire:model.live="create_selected_passengers.{{ $idx }}.hotel_checkout" class="w-full bg-black/40 border border-white/10 rounded text-[8px] text-white p-1">
                                                            </div>
                                                        </div>
                                                        @if($pax['hotel_nights'] > 0)
                                                            <p class="text-[7px] text-emerald-500/70 text-right uppercase font-bold">{{ $pax['hotel_nights'] }} noches</p>
                                                        @endif
                                                    </div>
                                                    <div class="space-y-3">
                                                        <label class="text-[8px] uppercase tracking-widest text-zinc-500 font-mono-tech">Conexión Terrestre</label>
                                                        <div class="grid grid-cols-2 gap-1 mb-1">
                                                            <input type="text" wire:model.live="tFlightSearchID" placeholder="ID" class="bg-black/40 border border-white/5 rounded text-[7px] px-1 py-0.5 text-white" title="Filtrar por ID">
                                                            <input type="date" wire:model.live="tFlightSearchDate" class="bg-black/40 border border-white/5 rounded text-[7px] px-1 py-0.5 text-white">
                                                        </div>
                                                        <select wire:model.live="create_selected_passengers.{{ $idx }}.terrestrial_flight_id" class="w-full bg-black/20 border border-white/10 rounded px-2 py-1 text-[10px] text-white">
                                                            <option value="">Sin enlace</option>
                                                            @foreach($this->filteredTerrestrialFlights as $tf)
                                                                <option value="{{ $tf->id }}">#{{ $tf->flight_number }} | {{ $tf->originLocation?->name }} → {{ $tf->destinationLocation?->name }} ({{ $tf->departure_datetime?->format('d/m H:i') }})</option>
                                                            @endforeach
                                                        </select>
                                                        <p class="text-[7px] text-zinc-600 italic">Búsqueda avanzada activa</p>
                                                    </div>
                                                </div>

                                                {{-- Servicios --}}
                                                <div class="col-span-12 md:col-span-3 space-y-2">
                                                    <label class="text-[8px] uppercase tracking-widest text-zinc-500 font-mono-tech">Servicios Adicionales</label>
                                                    <div class="grid grid-cols-2 gap-1">
                                                        @foreach([
                                                                'training_included' => 'ENTRENAMIENTO',
                                                                'passport_management_included' => 'PASAPORTE',
                                                                'vip_transfer_included' => 'TRASLADO',
                                                                'refund_insurance_included' => 'REEMBOLSO'
                                                            ] as $key => $label)
                                                                                    <label class="flex items-center gap-1.5 cursor-pointer select-none" wire:key="svc-{{ $idx }}-{{ $key }}">
                                                                                        <input type="checkbox" 
                                                                                            wire:model.live="create_selected_passengers.{{ $idx }}.{{ $key }}"
                                                                                            class="w-3.5 h-3.5 rounded bg-black/60 border-white/10 text-emerald-500 transition-all focus:ring-0 cursor-pointer">
                                                                                        <span class="text-[8px] font-bold transition-colors {{ $pax[$key] ? 'text-emerald-400' : 'text-zinc-500' }}">{{ $label }}</span>
                                                                                    </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Subtotal pax --}}
                                            <div class="mt-3 pt-3 border-t border-white/5 flex justify-end gap-6 items-center">
                                                @if($create_has_return_flight && $create_return_flight_id)
                                                    <div class="text-[8px] font-mono-tech text-zinc-500 uppercase">
                                                        IDA: {{ number_format($pax['total'], 2) }} € | 
                                                        VUELTA: {{ number_format($pax['return_total'], 2) }} €
                                                    </div>
                                                @endif
                                                <p class="text-[10px] font-mono-tech text-emerald-400/70 uppercase">
                                                    Total PAX: <span class="font-bold text-emerald-400">{{ number_format($pax['total'] + $pax['return_total'], 2) }} €</span>
                                                </p>
                                            </div>
                                        </div>
                                @empty
                                    <div class="py-12 border-2 border-dashed border-white/5 rounded-xl text-center">
                                        <p class="text-xs text-zinc-600 uppercase tracking-widest">Añada pasajeros a la expedición para comenzar</p>
                                    </div>
                                @endforelse
                                </div>
                            </div>
                    @else
                        <div class="py-20 text-center bg-black/10 rounded-xl border border-white/5">
                            <p class="text-xs text-zinc-500 uppercase tracking-[0.2em]">Seleccione un Cliente para configurar la expedición</p>
                        </div>
                    @endif
                </div>

                {{-- Footer Modal --}}
                <div class="mt-8 p-5 bg-emerald-500/5 border border-emerald-500/20 rounded-xl flex items-center justify-between shadow-xl">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[9px] uppercase font-mono-tech text-emerald-400/60 leading-none mb-1">Presupuesto Total ({{ count($create_selected_passengers) }} PAX)</p>
                            <p class="text-2xl font-mono-tech font-black text-emerald-400 leading-none">{{ number_format($create_total_price, 2) }} €</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if(!empty($temporalConflicts))
                            <span class="text-[9px] font-black text-rose-400 uppercase tracking-widest animate-pulse"> Resuelva conflictos de agenda</span>
                        @endif
                        <button wire:click="openConfirmModal" class="px-8 py-3 rounded-lg bg-emerald-500 text-black font-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20 text-xs {{ (empty($create_selected_passengers) || !empty($temporalConflicts)) ? 'opacity-30 cursor-not-allowed grayscale' : '' }}">
                            Revisar Reserva
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de Confirmación Resumen --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-xl p-4">
            <div class="tech-card p-8 rounded-2xl w-full max-w-3xl border-emerald-500/50 shadow-[0_0_50px_rgba(16,185,129,0.1)] flex flex-col max-h-[90vh]">
                <div class="text-center mb-6">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-emerald-500/30">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-black uppercase tracking-[0.3em] text-emerald-400">Resumen de Reserva</h3>
                    <p class="text-[9px] text-zinc-500 uppercase font-mono-tech mt-1 tracking-widest">Verificación detallada de conceptos facturables</p>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar space-y-4 mb-6 pr-2">
                    {{-- Bloque Itinerario --}}
                    <div class="bg-white/3 border border-white/5 p-4 rounded-xl">
                        <h4 class="text-[10px] font-black uppercase text-emerald-500/70 mb-3 tracking-widest border-b border-white/5 pb-2">Itinerario de Vuelo</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[8px] uppercase text-zinc-500 font-mono-tech mb-1">Salida / Ida</p>
                                <p class="text-xs font-bold text-white uppercase">{{ $selectedFlightLabel ?: 'No Incluido' }}</p>
                            </div>
                            @if($create_has_return_flight)
                                <div>
                                    <p class="text-[8px] uppercase text-zinc-500 font-mono-tech mb-1">Regreso / Vuelta</p>
                                    <p class="text-xs font-bold text-cyan-400 uppercase">{{ $selectedReturnFlightLabel ?: 'No Incluido' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Desglose por Pasajero --}}
                    <div class="space-y-3">
                        @foreach($create_selected_passengers as $p)
                            <div class="bg-black/40 border border-white/10 rounded-xl p-4">
                                <div class="flex justify-between items-center border-b border-white/5 pb-2 mb-3">
                                    <span class="text-[11px] font-black text-white uppercase tracking-wider">{{ $p['name'] }}</span>
                                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-tighter">{{ $p['seat_type'] ? strtoupper($p['seat_type']) : 'SIN VUELO' }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-x-8 gap-y-2">
                                    {{-- Desglose de servicios --}}
                                    <div class="space-y-1">
                                        <p class="text-[9px] flex justify-between text-zinc-400">
                                            <span>Vuelo Espacial:</span>
                                            <span class="text-white">{{ $p['seat_type'] ? 'Incluido' : 'No Incluido' }}</span>
                                        </p>
                                        @if($p['hotel_id'])
                                            <p class="text-[9px] flex justify-between text-zinc-400">
                                                <span>Alojamiento ({{ $p['hotel_nights'] }}n):</span>
                                                <span class="text-white">{{ \App\Models\Hotel::find($p['hotel_id'])?->name }}</span>
                                            </p>
                                        @endif
                                        @if($p['terrestrial_flight_id'])
                                            <p class="text-[9px] flex justify-between text-zinc-400">
                                                <span>Vuelo Terrestre:</span>
                                                <span class="text-white">Activado</span>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="space-y-1 border-l border-white/5 pl-4">
                                        <p class="text-[8px] font-black text-zinc-500 uppercase mb-1">Servicios Extra</p>
                                        <div class="flex flex-wrap gap-2">
                                            @if($p['training_included']) <span class="bg-emerald-500/10 text-emerald-400 px-1.5 py-0.5 rounded text-[7px] font-bold">ENTRENAMIENTO</span> @endif
                                            @if($p['passport_management_included']) <span class="bg-cyan-500/10 text-cyan-400 px-1.5 py-0.5 rounded text-[7px] font-bold">PASAPORTE</span> @endif
                                        @if($p['vip_transfer_included']) <span class="bg-amber-500/10 text-amber-400 px-1.5 py-0.5 rounded text-[7px] font-bold">TRANSPORTE</span> @endif
                                            @if($p['refund_insurance_included']) <span class="bg-indigo-500/10 text-indigo-400 px-1.5 py-0.5 rounded text-[7px] font-bold">SEGURO REEMBOLSO</span> @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 text-right">
                                    <span class="text-[9px] text-zinc-500 uppercase mr-2">Subtotal:</span>
                                    <span class="text-xs font-mono-tech font-bold text-emerald-400">{{ number_format($p['total'] + $p['return_total'], 2) }} €</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-6 border-t border-white/10 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase text-zinc-500 mb-1">Total a Facturar</p>
                        <p class="text-3xl font-mono-tech font-black text-emerald-400 tracking-tighter">{{ number_format($create_total_price, 2) }} €</p>
                    </div>
                    <div class="flex gap-4">
                        <button wire:click="saveNewReservation" class="px-10 py-4 bg-emerald-500 text-black font-black uppercase tracking-[0.2em] rounded-xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/20 text-xs">
                            Confirmar y Procesar
                        </button>
                        <button wire:click="$set('showConfirmModal', false)" class="px-8 py-4 border border-white/10 text-zinc-500 font-black uppercase tracking-widest rounded-xl hover:bg-white/5 transition-colors text-xs">
                            Volver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Upgrade & Extras Modal --}}
    @if($showUpgradeModal)
        <div class="fixed inset-0 z-[120] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div class="tech-card p-0 rounded-2xl w-full max-w-2xl border-emerald-500/30 shadow-[0_0_50px_rgba(16,185,129,0.1)] flex flex-col max-h-[95vh] overflow-hidden">

                {{-- Header --}}
                <div class="p-6 border-b border-white/5 bg-gradient-to-r from-emerald-500/10 to-transparent flex justify-between items-center">
                    <div>
                        <h3 class="font-black uppercase tracking-[0.2em] text-emerald-400 text-lg">Upgrade & Servicios Extra</h3>
                        <p class="text-[9px] font-mono-tech text-zinc-500 uppercase">Añadir valor a la experiencia del cliente</p>
                    </div>
                    <button wire:click="$set('showUpgradeModal', false)" class="text-zinc-500 hover:text-white transition-colors">✕</button>
                </div>

                {{-- Content Scrollable --}}
                <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-8">

                    {{-- 1. Upgrade de Clase --}}
                    <section class="space-y-3">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-6 h-6 rounded bg-violet-500/20 flex items-center justify-center border border-violet-500/30">
                                <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 11l7-7 7 7M5 19l7-7 7 7"/></svg>
                            </div>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Upgrade de Clase Espacial</h4>
                        </div>
                        <label class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/2 cursor-pointer hover:bg-white/5 transition-all group">
                            <div class="flex items-center gap-4">
                                <input type="checkbox" wire:model.live="upgrade_to_supernova" wire:change="calculateUpgradePrice" class="w-5 h-5 rounded bg-black/40 border-white/10 text-violet-500">
                                <div>
                                    <p class="text-xs font-bold text-white uppercase tracking-tight">Ascender a Clase SUPERNOVA</p>
                                    <p class="text-[9px] text-zinc-500 uppercase">Acceso a suite orbital y servicios premium</p>
                                </div>
                            </div>
                            <span class="text-xs font-mono-tech font-bold text-violet-400">+ {{ number_format(\App\Models\PriceLog::getCurrentPrice('supernova_upgrade') ?: 0, 0) }} €</span>
                        </label>
                    </section>

                    {{-- 2. Alojamiento --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded bg-emerald-500/20 flex items-center justify-center border border-emerald-500/30">
                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Alojamiento Adicional</h4>
                        </div>

                        <div class="relative" x-data="{ open: @entangle('showUpgradeHotelResults') }">
                            @if($upgrade_hotel_id)
                                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="text-emerald-400 text-xs">✓</div>
                                            <p class="text-xs font-bold text-white uppercase">{{ $selectedUpgradeHotelLabel }}</p>
                                        </div>
                                        <button wire:click="$set('upgrade_hotel_id', null); calculateUpgradePrice()" class="text-rose-400 hover:text-rose-300 text-[10px] uppercase font-bold">Quitar</button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-white/5">
                                        <div>
                                            <label class="text-[7px] text-zinc-500 uppercase block mb-1">Entrada</label>
                                            <input type="date" wire:model.live="upgrade_hotel_checkin" class="w-full bg-black/40 border border-white/10 rounded px-2 py-1 text-[10px] text-white">
                                        </div>
                                        <div>
                                            <label class="text-[7px] text-zinc-500 uppercase block mb-1">Salida</label>
                                            <input type="date" wire:model.live="upgrade_hotel_checkout" class="w-full bg-black/40 border border-white/10 rounded px-2 py-1 text-[10px] text-white">
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-zinc-500 uppercase text-right">Estancia: <span class="text-emerald-400 font-bold">{{ $upgrade_hotel_nights }}</span> noches</p>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <input type="text" wire:model.live="upgradeHotelSearch" @focus="open = true" placeholder="Buscar hotel por nombre..." 
                                            class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-emerald-500/50">
                                    </div>
                                </div>
                                @if($showUpgradeHotelResults && strlen($upgradeHotelSearch) >= 2)
                                    <div class="absolute z-20 left-0 right-0 mt-1 bg-zinc-900 border border-white/10 rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto">
                                        @forelse($this->filteredUpgradeHotels as $h)
                                            <button wire:click="selectUpgradeHotel({{ $h->id }}, '{{ $h->name }}')" 
                                                class="w-full text-left px-4 py-3 hover:bg-emerald-500/10 transition-colors border-b border-white/5 last:border-0">
                                                <div class="flex justify-between items-center">
                                                    <p class="text-[10px] font-black text-white uppercase">{{ $h->name }}</p>
                                                    <p class="text-[9px] text-emerald-400 font-mono-tech">{{ number_format($h->price_per_night, 0) }} €/n</p>
                                                </div>
                                            </button>
                                        @empty
                                            <div class="p-4 text-center text-[9px] text-zinc-500 uppercase tracking-widest">Sin hoteles encontrados</div>
                                        @endforelse
                                    </div>
                                @endif
                            @endif
                        </div>
                    </section>

                    {{-- 3. Conexión Terrestre --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded bg-cyan-500/20 flex items-center justify-center border border-cyan-500/30">
                                <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </div>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Conexión & Transporte</h4>
                        </div>

                        <div class="relative" x-data="{ open: @entangle('showUpgradeTerrestrialResults') }">
                            @if($upgrade_terrestrial_flight_id)
                                <div class="p-3 bg-cyan-500/10 border border-cyan-500/20 rounded-xl flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="text-cyan-400">✓</div>
                                        <div>
                                            <p class="text-xs font-bold text-white uppercase">{{ $selectedUpgradeTerrestrialLabel }}</p>
                                            <p class="text-[9px] text-zinc-500 uppercase mt-0.5">Enlace terrestre confirmado</p>
                                        </div>
                                    </div>
                                    <button wire:click="$set('upgrade_terrestrial_flight_id', null); calculateUpgradePrice()" class="text-rose-400 hover:text-rose-300 px-2">✕</button>
                                </div>
                            @else
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-2">
                                    <input type="text" wire:model.live="upgradeT_ID" placeholder="ID Vuelo" class="bg-black/40 border border-white/10 rounded-lg px-3 py-2 text-[10px] text-white outline-none focus:border-cyan-500/50">
                                    <input type="text" wire:model.live="upgradeT_Origin" placeholder="Origen" class="bg-black/40 border border-white/10 rounded-lg px-3 py-2 text-[10px] text-white outline-none focus:border-cyan-500/50">
                                    <input type="text" wire:model.live="upgradeT_Dest" placeholder="Destino" class="bg-black/40 border border-white/10 rounded-lg px-3 py-2 text-[10px] text-white outline-none focus:border-cyan-500/50">
                                    <input type="date" wire:model.live="upgradeT_Date" class="bg-black/40 border border-white/10 rounded-lg px-2 py-2 text-[10px] text-white outline-none focus:border-cyan-500/50">
                                </div>

                                @if($this->filteredUpgradeTerrestrials->isNotEmpty())
                                    <div class="bg-zinc-900/50 border border-white/5 rounded-xl overflow-hidden max-h-48 overflow-y-auto">
                                        @foreach($this->filteredUpgradeTerrestrials as $tf)
                                            <button wire:click="selectUpgradeTerrestrial({{ $tf->id }}, '{{ $tf->originLocation?->name }} → {{ $tf->destinationLocation?->name }}')" 
                                                class="w-full text-left px-4 py-3 hover:bg-cyan-500/10 transition-colors border-b border-white/5 last:border-0 group">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="text-[10px] font-black text-white uppercase group-hover:text-cyan-400 transition-colors">#{{ $tf->flight_number }} | {{ $tf->originLocation?->name }} → {{ $tf->destinationLocation?->name }}</p>
                                                        <p class="text-[8px] text-zinc-500 uppercase mt-0.5">{{ $tf->departure_datetime?->format('d/m/Y H:i') }}</p>
                                                    </div>
                                                    <p class="text-[9px] text-cyan-400 font-mono-tech font-bold">{{ number_format($tf->price, 0) }} €</p>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-white/2 cursor-pointer hover:bg-white/5 transition-all">
                                <input type="checkbox" wire:model.live="upgrade_vip_transfer" wire:change="calculateUpgradePrice" class="w-4 h-4 rounded bg-black/40 border-white/10 text-amber-500">
                                <div>
                                    <p class="text-[10px] font-bold text-white uppercase tracking-tight">VIP Transfer</p>
                                    <p class="text-[8px] text-zinc-500 uppercase">Transporte privado</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-white/2 cursor-pointer hover:bg-white/5 transition-all">
                                <input type="checkbox" wire:model.live="upgrade_training" wire:change="calculateUpgradePrice" class="w-4 h-4 rounded bg-black/40 border-white/10 text-emerald-500">
                                <div>
                                    <p class="text-[10px] font-bold text-white uppercase tracking-tight">IRIS Training</p>
                                    <p class="text-[8px] text-zinc-500 uppercase">Aptitud física</p>
                                </div>
                            </label>
                        </div>
                    </section>

                    {{-- 4. Otros Servicios --}}
                    <section class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-white/2 cursor-pointer hover:bg-white/5 transition-all">
                            <input type="checkbox" wire:model.live="upgrade_passport" wire:change="calculateUpgradePrice" class="w-4 h-4 rounded bg-black/40 border-white/10 text-cyan-500">
                            <div>
                                <p class="text-[10px] font-bold text-white uppercase tracking-tight">Gestión Pasaporte</p>
                                <p class="text-[8px] text-zinc-500 uppercase">Trámite IRIS</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-white/2 cursor-pointer hover:bg-white/5 transition-all">
                            <input type="checkbox" wire:model.live="upgrade_insurance" wire:change="calculateUpgradePrice" class="w-4 h-4 rounded bg-black/40 border-white/10 text-indigo-500">
                            <div>
                                <p class="text-[10px] font-bold text-white uppercase tracking-tight">Seguro Reembolso</p>
                                <p class="text-[8px] text-zinc-500 uppercase">Protección 100%</p>
                            </div>
                        </label>
                    </section>
                </div>

                {{-- Footer Summary --}}
                <div class="p-6 border-t border-white/10 bg-black/40 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase text-zinc-500 mb-1 font-black tracking-widest">Diferencia a Liquidar</p>
                        <p class="text-3xl font-mono-tech font-black text-emerald-400">{{ number_format($upgradePriceDifference, 2) }} €</p>
                    </div>
                    <div class="flex gap-3">
                        <button wire:click="executeUpgrade" 
                            class="px-8 py-3 rounded-xl bg-emerald-500 text-black font-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20 text-xs {{ $upgradePriceDifference <= 0 ? 'opacity-30 cursor-not-allowed grayscale' : '' }}">
                            Confirmar & Generar Pago
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Facturas y Recibos --}}
    @if($showReceiptsModal)
        <div class="fixed inset-0 z-[110] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
            <div class="tech-card p-0 rounded-2xl w-full max-w-lg border-cyan-500/30 overflow-hidden shadow-2xl" style="background: var(--bg-panel);">
                <div class="p-6 border-b border-white/5 bg-gradient-to-r from-cyan-500/10 to-transparent flex justify-between items-center">
                    <h3 class="font-black uppercase tracking-widest text-cyan-400">Expediente Financiero</h3>
                    <button wire:click="closeReceiptsModal" class="text-zinc-500 hover:text-white transition-colors">✕</button>
                </div>
                <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    @forelse($receiptsList as $receipt)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ ($receipt['type'] ?? '') === 'refund' ? 'bg-rose-500/20 text-rose-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                                    @if(($receipt['type'] ?? '') === 'refund')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold" style="color: var(--text-primary)">{{ $receipt['description'] ?? 'Factura Stripe' }}</p>
                                    <p class="text-[10px] text-zinc-500 font-mono-tech">{{ \Carbon\Carbon::parse($receipt['date'] ?? now())->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black {{ ($receipt['type'] ?? '') === 'refund' ? 'text-rose-400' : 'text-emerald-400' }}">
                                    {{ ($receipt['type'] ?? '') === 'refund' ? '-' : '+' }} {{ number_format($receipt['amount'] ?? 0, 2) }} €
                                </p>
                                @if(!empty($receipt['url']) && $receipt['url'] !== '#')
                                    <a href="{{ $receipt['url'] }}" target="_blank" 
                                       class="px-3 py-1.5 rounded-lg bg-cyan-500/10 text-cyan-400 text-[9px] font-black uppercase hover:bg-cyan-500 hover:text-black transition-all border border-cyan-500/20">
                                        Abrir Factura
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-zinc-500 italic py-10">No hay registros financieros asociados.</p>
                    @endforelse
                </div>
                <div class="p-6 border-t border-white/5">
                    <button wire:click="closeReceiptsModal" class="w-full py-3 rounded-xl text-xs font-black uppercase tracking-widest border border-white/10 hover:bg-white/5 transition-all text-zinc-400">Cerrar Historial</button>
                </div>
            </div>
        </div>
    @endif
</div>
