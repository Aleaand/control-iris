<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\RefundRequest;
use App\Models\PaymentLink;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\RefundInvoiceMail;

class GestorPayments extends Component
{
    public string $filterPayment = '';
    public string $search = '';
    public bool $showRefundModal = false;
    public ?int $refundReservationId = null;
    public string $refundNotes = '';
    public $refundAmount = 0;
    public $refundPercentage = 0;
    public bool $showExecuteConfirmModal = false;
    public ?int $executingRequestId = null;
    public bool $showPolicyModal = false;
    public bool $showReceiptsModal = false;
    public ?Reservation $receiptsReservation = null;
    public array $receiptsList = [];
    public bool $showPaymentLinkModal = false;
    public ?string $generatedLink = null;
    public ?string $generatedLinkGroupId = null;
    public ?float $generatedLinkAmount = null;

    public function requestRefund(int $reservationId): void
    {
        $res = Reservation::with(['logistics', 'spaceFlight'])->findOrFail($reservationId);
        $this->refundReservationId = $reservationId;

        $total = (float) $res->total_price;
        $hasInsurance = $res->logistics?->refund_insurance_included ?? false;

        $nonRefundable = 0;
        if ($res->logistics) {
            if ($res->logistics->hotel_id) {
                $hotel = \App\Models\Hotel::find($res->logistics->hotel_id);
                if ($hotel)
                    $nonRefundable += $hotel->price_per_night * $res->logistics->hotel_nights;
            }
            if ($res->logistics->terrestrial_flight_id) {
                $tf = \App\Models\TerrestrialFlight::find($res->logistics->terrestrial_flight_id);
                if ($tf)
                    $nonRefundable += $tf->price;
            }
        }

        $refundableBase = max(0, $total - $nonRefundable);

        if ($hasInsurance) {
            $daysToFlight = now()->diffInDays($res->spaceFlight->departure_date, false);
            $hoursToFlight = now()->diffInHours($res->spaceFlight->departure_date, false);

            if ($daysToFlight > 30) {
                $this->refundPercentage = 90;
            } elseif ($daysToFlight >= 7) {
                $this->refundPercentage = 50;
            } elseif ($hoursToFlight >= 72) {
                $this->refundPercentage = 10;
            } else {
                $this->refundPercentage = 0;
            }
        } else {
            $this->refundPercentage = 0;
        }

        $this->refundAmount = round($refundableBase * ($this->refundPercentage / 100), 2);
        $this->showRefundModal = true;
    }

    public function updatedRefundPercentage(): void
    {
        if ($this->refundReservationId) {
            $res = Reservation::findOrFail($this->refundReservationId);
            $total = (float) $res->total_price;
            $percentage = (float) $this->refundPercentage;
            $this->refundAmount = round($total * ($percentage / 100), 2);
        }
    }

    public function updatedRefundAmount(): void
    {
        if ($this->refundReservationId) {
            $res = Reservation::findOrFail($this->refundReservationId);
            $total = (float) $res->total_price;
            $amount = (float) $this->refundAmount;
            if ($total > 0) {
                $this->refundPercentage = round(($amount / $total) * 100, 2);
            }
        }
    }

    public function submitRefund(): void
    {
        $this->validate([
            'refundNotes' => 'nullable|string|max:500',
            'refundAmount' => 'required|numeric|min:0',
        ]);

        $res = Reservation::with(['passenger', 'user', 'logistics'])->findOrFail($this->refundReservationId);

        $exists = RefundRequest::where('reservation_id', $res->id)->where('status', 'Pendiente')->exists();
        if ($exists) {
            session()->flash('error', 'Ya existe una solicitud de reembolso pendiente para esta reserva.');
            $this->showRefundModal = false;
            return;
        }

        $originalAmount = (float) $res->total_price;
        $penalty = $originalAmount - $this->refundAmount;

        $request = RefundRequest::create([
            'reservation_id' => $this->refundReservationId,
            'gestor_id' => auth()->id(),
            'status' => 'Pendiente',
            'refund_amount' => $this->refundAmount,
            'penalty_amount' => $penalty,
            'gestor_notes' => $this->refundNotes,
            'has_insurance' => $res->logistics?->refund_insurance_included ?? false,
        ]);

        \App\Models\Task::create([
            'assigned_gestor_id' => auth()->id(),
            'created_by' => auth()->id(),
            'title' => 'Procesar Reembolso: ' . $res->id_locator,
            'description' => "Reembolso de {$this->refundAmount}€ para " . ($res->passenger->full_name ?? $res->user->name ?? 'Pasajero') . ".",
            'type' => 'reembolso',
            'status' => 'Pendiente',
            'priority' => 'alta',
            'payload' => ['refund_request_id' => $request->id, 'reservation_id' => $res->id],
        ]);

        // Registrar en LOG de contacto
        \App\Models\ContactLog::create([
            'client_id' => $res->user_id,
            'gestor_id' => auth()->id(),
            'type' => 'email',
            'notes' => "REEMBOLSO: Se ha solicitado un reembolso de {$this->refundAmount}€ para la reserva {$res->id_locator}. Motivo: {$this->refundNotes}",
        ]);

        session()->flash('message', 'Solicitud de reembolso enviada para aprobación.');
        $this->showRefundModal = false;
        $this->reset(['refundNotes', 'refundAmount', 'refundPercentage', 'refundReservationId']);
    }

    public function updateTaskStatus(int $taskId, string $status): void
    {
        $task = \App\Models\Task::findOrFail($taskId);
        $task->update(['status' => $status]);
        session()->flash('message', 'Estado de la tarea actualizado.');
    }

    public function confirmExecuteRefund(int $requestId): void
    {
        $this->executingRequestId = $requestId;
        $this->showExecuteConfirmModal = true;
    }

    private function cleanAmount($value): float
    {
        if (is_numeric($value)) return (float)$value;
        $value = (string)$value;
        // Si tiene puntos y comas, asumimos formato europeo (1.234,56)
        if (str_contains($value, '.') && str_contains($value, ',')) {
            return (float) str_replace(',', '.', str_replace('.', '', $value));
        }
        // Si solo tiene coma, la convertimos a punto
        if (str_contains($value, ',')) {
            return (float) str_replace(',', '.', $value);
        }
        return (float)$value;
    }

    public function executeRefund(): void
    {
        if (!$this->executingRequestId) return;

        $req = RefundRequest::with('reservation.user')->findOrFail($this->executingRequestId);

        if ($req->status !== 'Pendiente') {
            $this->showExecuteConfirmModal = false;
            $this->executingRequestId = null;
            session()->flash('error', 'Esta solicitud ya ha sido procesada.');
            return;
        }

        // 1. Proceso de Reembolso en Stripe (con soporte para importes > 1M€)
        if ($req->reservation->stripe_session_id) {
            try {
                $stripe = new \Stripe\StripeClient(config('stripe.secret'));
                $session = $stripe->checkout->sessions->retrieve($req->reservation->stripe_session_id);

                if ($session->payment_intent) {
                    $totalAmountCents = (int) round($this->cleanAmount($req->refund_amount) * 100);
                    $maxStripeLimitCents = 99999999; // Límite de Stripe: 999.999,99€
                    
                    $remainingCents = $totalAmountCents;
                    $tramosProcesados = 0;

                    while ($remainingCents > 0) {
                        $currentTramoCents = min($remainingCents, $maxStripeLimitCents);
                        
                        \Log::info("Procesando tramo de reembolso", [
                            'locator' => $req->reservation->id_locator,
                            'tramo_cents' => $currentTramoCents,
                            'restante_cents' => $remainingCents - $currentTramoCents
                        ]);

                        $stripe->refunds->create([
                            'payment_intent' => $session->payment_intent,
                            'amount' => $currentTramoCents,
                            'reason' => 'requested_by_customer',
                        ]);

                        $remainingCents -= $currentTramoCents;
                        $tramosProcesados++;

                        // Si hay muchos tramos, damos un pequeño respiro al API
                        if ($remainingCents > 0) usleep(100000); 
                    }
                    
                    \Log::info("Reembolso total de Stripe completado", ['tramos' => $tramosProcesados]);
                } else {
                    \Log::warning("No hay PaymentIntent en la sesión: " . $req->reservation->id_locator);
                }
            } catch (\Exception $e) {
                \Log::error("Fallo Reembolso Stripe: " . $e->getMessage());
                session()->flash('error', 'Error en Stripe: ' . $e->getMessage());
                return;
            }
        }

        // 2. Registro Contable (Gasto)
        \App\Models\Expense::create([
            'flight_id' => $req->reservation->space_flight_id,
            'reference' => 'REFUND-' . $req->reservation->id_locator,
            'category' => 'Reembolso',
            'description' => "Devolución a {$req->reservation->user->name} - Res: {$req->reservation->id_locator}",
            'amount' => $req->refund_amount,
            'expense_date' => now(),
        ]);

        // 3. Actualizar Reserva y Recibos
        $receipts = $req->reservation->stripe_receipts ?? [];
        $receipts[] = [
            'type' => 'refund',
            'amount' => $req->refund_amount,
            'date' => now()->format('Y-m-d H:i:s'),
            'description' => 'Factura de Devolución',
            'url' => '#'
        ];

        $req->reservation->update([
            'status' => 'Reembolsada',
            'stripe_receipts' => $receipts,
            'payment_status' => 'refunded'
        ]);

        // 4. Finalizar Solicitud
        $req->update([
            'status' => 'Aprobado',
            'resolved_at' => now(),
        ]);

        // 5. Notificación por Correo
        try {
            Mail::to($req->reservation->user->email)->send(new RefundInvoiceMail($req->reservation, $req));
        } catch (\Exception $e) {
            \Log::error("Error Mail Reembolso: " . $e->getMessage());
        }

        session()->flash('message', 'Reembolso completado con éxito.');
        $this->showExecuteConfirmModal = false;
        $this->executingRequestId = null;
    }

    public function generatePaymentLink(string $bookingGroupId, float $amount, int $clientId): void
    {
        $link = PaymentLink::create([
            'booking_group_id' => $bookingGroupId,
            'created_by' => auth()->id(),
            'client_id' => $clientId,
            'amount' => $amount,
            'expires_at' => now()->addDays(7),
        ]);

        $this->generatedLink = route('payment.pay', ['token' => $link->token]);
        $this->generatedLinkGroupId = $bookingGroupId;
        $this->generatedLinkAmount = $amount;
        $this->showPaymentLinkModal = true;

        session()->flash('message', 'Link de pago generado. Válido 7 días.');
    }

    public function closePaymentLinkModal(): void
    {
        $this->showPaymentLinkModal = false;
        $this->generatedLink = null;
    }

    public function rejectRefund(int $requestId): void
    {
        $req = RefundRequest::findOrFail($requestId);
        $req->update(['status' => 'Rechazado', 'resolved_at' => now()]);
        session()->flash('message', 'Solicitud de reembolso rechazada.');
    }

    public function viewReceipts(int $reservationId): void
    {
        $res = Reservation::with('logistics', 'passenger')->findOrFail($reservationId);
        $this->receiptsReservation = $res;
        $this->receiptsList = $res->stripe_receipts ?? [];
        
        // Si hay una factura principal de Stripe y no está en la lista, la añadimos al principio
        if ($res->stripe_receipt_url && empty($this->receiptsList)) {
             // Intentar no duplicar si ya existe una de tipo 'payment'
             $hasPayment = collect($this->receiptsList)->contains('type', 'payment');
             if (!$hasPayment) {
                 array_unshift($this->receiptsList, [
                     'type' => 'payment',
                     'amount' => $res->total_price,
                     'date' => $res->paid_at ? $res->paid_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                     'description' => 'Recibo de Pago Stripe',
                     'url' => $res->stripe_receipt_url
                 ]);
             }
        }

        $this->showReceiptsModal = true;
    }

    public function closeReceiptsModal(): void
    {
        $this->showReceiptsModal = false;
        $this->receiptsReservation = null;
        $this->receiptsList = [];
    }

    public function render()
    {

        static $taskConstraintFixed = false;
        if (!$taskConstraintFixed) {
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE tasks DROP CONSTRAINT IF EXISTS tasks_type_check");
                \Illuminate\Support\Facades\DB::statement(
                    "ALTER TABLE tasks ADD CONSTRAINT tasks_type_check CHECK (type IN ('flight_cancelled','policy_change','passenger_issue','general','passport','iris_training','iris-training','reembolso','refund'))"
                );
                $taskConstraintFixed = true;
            } catch (\Exception $e) {
            }
        }

        $reservations = Reservation::with(['user', 'passenger', 'spaceFlight.destination', 'logistics'])
            ->whereHas('user', fn($q) => $q->where('assigned_manager_id', auth()->id()))
            ->where('is_adenda', false)
            ->when($this->filterPayment, fn($q) => $q->where('payment_status', $this->filterPayment))
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('passenger', fn($p) => $p->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhere('id_locator', 'like', '%' . $this->search . '%');
            }))
            ->orderBy('created_at', 'desc')
            ->get();

        $refundRequests = RefundRequest::where('gestor_id', auth()->id())
            ->where('status', 'Pendiente')
            ->with('reservation.user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $stats = [
            'total' => $reservations->count(),
            'paid' => $reservations->where('payment_status', 'paid')->count(),
            'pending' => $reservations->where('payment_status', '!=', 'paid')->count(),
            'revenue' => $reservations->where('payment_status', 'paid')->sum('total_price'),
            'refunded' => $reservations->where('payment_status', 'refunded')->sum('total_price'),
        ];

        $refundTasks = \App\Models\Task::where('assigned_gestor_id', auth()->id())
            ->where('status', '!=', 'Completada')
            ->where(function ($q) {
                $q->where('type', 'like', '%rembolso%')
                    ->orWhere('type', 'like', '%reembolso%')
                    ->orWhere('type', 'like', '%refund%')
                    ->orWhere('title', 'like', '%rembolso%')
                    ->orWhere('title', 'like', '%reembolso%');
            })
            ->where('status', 'Pendiente')
            ->orderBy('priority', 'asc')
            ->get();

        return view('livewire.gestor.payments', compact('reservations', 'refundRequests', 'stats', 'refundTasks'))
            ->layout('layouts.gestor');
    }
}
