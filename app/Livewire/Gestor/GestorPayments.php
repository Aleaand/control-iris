<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\RefundRequest;
use App\Models\PaymentLink;
use App\Models\User;
use Illuminate\Support\Str;

class GestorPayments extends Component
{
    public string $filterPayment = '';
    public string $search = '';

    // — Reembolso
    public bool $showRefundModal = false;
    public ?int $refundReservationId = null;
    public string $refundNotes = '';
    public float $refundAmount = 0;

    // — Link de pago
    public bool $showPaymentLinkModal = false;
    public ?string $generatedLink = null;
    public ?string $generatedLinkGroupId = null;
    public ?float  $generatedLinkAmount = null;

    public function requestRefund(int $reservationId): void
    {
        $res = Reservation::whereHas('user', fn($q) => $q->where('assigned_manager_id', auth()->id()))
            ->findOrFail($reservationId);

        $this->refundReservationId = $reservationId;
        $this->refundAmount = (float) $res->total_price;

        // Penalización: 20% si tiene seguro, 50% si no
        $hasInsurance = $res->logistics?->refund_insurance_included ?? false;
        if ($hasInsurance) {
            $penalty = $this->refundAmount * 0.20;
        } else {
            $penalty = $this->refundAmount * 0.50;
        }
        $this->refundAmount = $this->refundAmount - $penalty;
        $this->showRefundModal = true;
    }

    public function submitRefund(): void
    {
        $this->validate([
            'refundNotes'  => 'nullable|string|max:500',
            'refundAmount' => 'required|numeric|min:0',
        ]);

        $res = Reservation::findOrFail($this->refundReservationId);
        $hasInsurance = $res->logistics?->refund_insurance_included ?? false;
        $originalAmount = (float) $res->total_price;
        $penalty = $hasInsurance ? $originalAmount * 0.20 : $originalAmount * 0.50;

        RefundRequest::create([
            'reservation_id' => $this->refundReservationId,
            'gestor_id'      => auth()->id(),
            'status'         => 'Pendiente',
            'refund_amount'  => $this->refundAmount,
            'penalty_amount' => $penalty,
            'gestor_notes'   => $this->refundNotes,
            'has_insurance'  => $hasInsurance,
        ]);

        // Cambiar estado de la reserva
        $res->update(['status' => 'Cancelada']);

        session()->flash('message', 'Solicitud de reembolso enviada al Super Admin.');
        $this->showRefundModal = false;
        $this->refundNotes = '';
    }

    public function generatePaymentLink(string $bookingGroupId, float $amount, int $clientId): void
    {
        $link = PaymentLink::create([
            'booking_group_id' => $bookingGroupId,
            'created_by'       => auth()->id(),
            'client_id'        => $clientId,
            'amount'           => $amount,
            'expires_at'       => now()->addDays(7),
        ]);

        $this->generatedLink        = route('payment.pay', ['token' => $link->token]);
        $this->generatedLinkGroupId = $bookingGroupId;
        $this->generatedLinkAmount  = $amount;
        $this->showPaymentLinkModal = true;

        session()->flash('message', 'Link de pago generado. Válido 7 días.');
    }

    public function closePaymentLinkModal(): void
    {
        $this->showPaymentLinkModal = false;
        $this->generatedLink        = null;
    }

    public function render()
    {
        $reservations = Reservation::with(['user', 'passenger', 'spaceFlight.destination', 'logistics'])
            ->whereHas('user', fn($q) => $q->where('assigned_manager_id', auth()->id()))
            ->where('is_adenda', false)
            ->when($this->filterPayment, fn($q) => $q->where('payment_status', $this->filterPayment))
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('passenger', fn($p) => $p->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhere('id_locator', 'like', '%'.$this->search.'%');
            }))
            ->orderBy('created_at', 'desc')
            ->get();

        $refundRequests = RefundRequest::where('gestor_id', auth()->id())
            ->with('reservation.user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $stats = [
            'total'   => $reservations->count(),
            'paid'    => $reservations->where('payment_status', 'paid')->count(),
            'pending' => $reservations->where('payment_status', '!=', 'paid')->count(),
            'revenue' => $reservations->where('payment_status', 'paid')->sum('total_price'),
        ];

        return view('livewire.gestor.payments', compact('reservations', 'refundRequests', 'stats'))
            ->layout('layouts.gestor');
    }
}
