<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\ReservationLogistic;
use App\Models\User;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Passenger;
use App\Models\TerrestrialFlight;
use App\Models\PriceLog;
use App\Models\PaymentLink;

class GestorReservations extends Component
{
    // ── Búsqueda / listado ─────────────────────────────────────────────
    public string $search  = '';
    public string $sortDir = 'desc';
    public string $filterStatus = '';

    // ── FINAL GO ──────────────────────────────────────────────────────
    public bool   $showFinalGoModal    = false;
    public ?int   $finalGoReservationId = null;
    public array  $finalGoChecklist    = [];

    // ── Link de Pago ──────────────────────────────────────────────────
    public bool    $showPayLinkModal  = false;
    public ?string $generatedPayLink  = null;
    public ?float  $generatedPayAmount = null;

    // ── Delete ────────────────────────────────────────────────────────
    public bool $showDeleteModal = false;
    public ?int $deleteId        = null;

    // ── Ver detalle ───────────────────────────────────────────────────
    public bool   $showDetailModal      = false;
    public ?Reservation $detailReservation = null;

    public function render()
    {
        $reservations = Reservation::with([
                'user', 'passenger', 'spaceFlight.destination', 'logistics',
            ])
            ->whereHas('user', fn($q) => $q->where('assigned_manager_id', auth()->id()))
            ->where('is_adenda', false)
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('passenger', fn($p) => $p->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhere('id_locator', 'like', '%'.$this->search.'%');
            }))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('created_at', $this->sortDir)
            ->get()
            ->map(function ($res) {
                $dep   = $res->spaceFlight?->departure_date;
                $hours = $dep ? now()->diffInHours($dep, false) : null;
                $res->hours_to_flight = $hours;
                $res->is_72h_window   = $hours !== null && $hours >= 0 && $hours <= 72;

                // Final GO eligibility
                $paid    = $res->payment_status === 'paid';
                $pax     = $res->passenger;
                $docs    = $pax?->hasValidPassport() ?? false;
                $health  = in_array($pax?->physical_fitness, ['Excelente', 'En entrenamiento']);
                $res->go_eligible = $res->is_72h_window && $paid && $docs && $health;
                $res->go_paid     = $paid;
                $res->go_docs     = $docs;
                $res->go_health   = $health;

                return $res;
            });

        return view('livewire.gestor.reservations', compact('reservations'))
            ->layout('layouts.gestor');
    }

    // ── FINAL GO ──────────────────────────────────────────────────────

    public function openFinalGo(int $id): void
    {
        $res = $this->getMyReservation($id);
        $pax = $res->passenger;

        $this->finalGoReservationId = $id;
        $this->finalGoChecklist = [
            'paid'    => $res->payment_status === 'paid',
            'docs'    => $pax?->hasValidPassport() ?? false,
            'health'  => in_array($pax?->physical_fitness, ['Excelente', 'En entrenamiento']),
            'training'=> $pax?->hasValidTraining() ?? false,
        ];
        $this->showFinalGoModal = true;
    }

    public function executeFinalGo(): void
    {
        $res = $this->getMyReservation($this->finalGoReservationId);
        $all = collect($this->finalGoChecklist)->every(fn($v) => $v);

        if (!$all) {
            session()->flash('error', 'No se puede emitir el GO. Faltan requisitos.');
            return;
        }

        $res->update(['status' => 'GO']);
        $this->showFinalGoModal = false;

        // Redirect to PDF
        session()->flash('message', 'FINAL GO emitido. Generando ticket...');
    }

    public function downloadTicket(int $id): mixed
    {
        $this->getMyReservation($id); // auth check
        return redirect()->route('gestor.reservations.ticket-pdf', ['reservation' => $id]);
    }

    // ── Link de Pago ──────────────────────────────────────────────────

    public function generatePayLink(int $id): void
    {
        $res    = $this->getMyReservation($id);
        $token  = \Illuminate\Support\Str::random(48);
        $amount = (float) $res->total_price;

        PaymentLink::create([
            'token'            => $token,
            'booking_group_id' => $res->booking_group_id,
            'created_by'       => auth()->id(),
            'client_id'        => $res->user_id,
            'amount'           => $amount,
            'expires_at'       => now()->addDays(7),
        ]);

        $this->generatedPayLink   = url('/pay/'.$token);
        $this->generatedPayAmount = $amount;
        $this->showPayLinkModal   = true;
    }

    public function closePayLinkModal(): void
    {
        $this->showPayLinkModal  = false;
        $this->generatedPayLink  = null;
        $this->generatedPayAmount = null;
    }

    // ── Delete ────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $this->getMyReservation($id);
        $this->deleteId        = $id;
        $this->showDeleteModal = true;
    }

    public function executeDelete(): void
    {
        if (!$this->deleteId) return;
        $res = $this->getMyReservation($this->deleteId);
        $res->update(['status' => 'Cancelada']);
        session()->flash('message', 'Reserva cancelada.');
        $this->showDeleteModal = false;
        $this->deleteId        = null;
    }

    // ── Detalle ───────────────────────────────────────────────────────

    public function viewDetail(int $id): void
    {
        $this->detailReservation = $this->getMyReservation($id)
            ->load(['user', 'passenger', 'spaceFlight.destination', 'logistics.hotel', 'logistics.terrestrialFlight']);
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal   = false;
        $this->detailReservation = null;
    }

    // ── Helper ────────────────────────────────────────────────────────

    private function getMyReservation(int $id): Reservation
    {
        return Reservation::whereHas('user', fn($q) =>
            $q->where('assigned_manager_id', auth()->id())
        )->with(['passenger', 'spaceFlight', 'logistics'])->findOrFail($id);
    }
}
