<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\Passenger;
use App\Models\Reservation;

class GestorCompliance extends Component
{
    public string $filterStatus = 'all';
    public string $search = '';

    // — Edición rápida de Iris Training / Pasaporte
    public bool $showEditModal    = false;
    public ?int $editPassengerId  = null;
    public string $iris_passport_number     = '';
    public string $iris_passport_expiration = '';
    public string $training_date            = '';
    public string $training_status          = 'No Apto';
    public string $physical_fitness         = 'No apto';

    protected function rules(): array
    {
        return [
            'iris_passport_number'     => 'nullable|string|max:50',
            'iris_passport_expiration' => 'nullable|date',
            'training_date'            => 'nullable|date',
            'training_status'          => 'required|in:Apto,No Apto',
            'physical_fitness'         => 'required|in:Excelente,En entrenamiento,No apto',
        ];
    }

    public function openEdit(int $passengerId): void
    {
        $pax = $this->getMyPassenger($passengerId);
        $this->editPassengerId          = $pax->id;
        $this->iris_passport_number     = $pax->iris_passport_number ?? '';
        $this->iris_passport_expiration = $pax->iris_passport_expiration?->format('Y-m-d') ?? '';
        $this->training_date            = $pax->training_certificate_date?->format('Y-m-d') ?? '';
        $this->training_status          = $pax->training_certificate_status ?? 'No Apto';
        $this->physical_fitness         = $pax->physical_fitness;
        $this->showEditModal            = true;
    }

    public function saveCompliance(): void
    {
        $this->validate();

        $pax = $this->getMyPassenger($this->editPassengerId);
        $pax->update([
            'iris_passport_number'       => $this->iris_passport_number ?: null,
            'iris_passport_expiration'   => $this->iris_passport_expiration ?: null,
            'training_certificate_date'  => $this->training_date ?: null,
            'training_certificate_status'=> $this->training_status,
            'physical_fitness'           => $this->physical_fitness,
        ]);

        session()->flash('message', 'Documentación actualizada.');
        $this->showEditModal   = false;
        $this->editPassengerId = null;
        $this->resetValidation();
    }

    private function getMyPassenger(int $id): Passenger
    {
        return Passenger::whereHas('client', fn($q) =>
            $q->where('assigned_manager_id', auth()->id())
        )->findOrFail($id);
    }

    public function render()
    {
        $passengersQuery = Passenger::with(['client', 'reservations.spaceFlight'])
            ->whereHas('client', fn($q) => $q->where('assigned_manager_id', auth()->id()))
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('document_number', 'like', '%'.$this->search.'%');
            }));

        $passengers = $passengersQuery->get()->map(function ($pax) {
            $nextFlight = $pax->reservations
                ->whereNotIn('status', ['Cancelada'])
                ->sortBy(fn($r) => $r->spaceFlight?->departure_date)
                ->first();

            $hoursToFlight = $nextFlight?->spaceFlight?->departure_date
                ? now()->diffInHours($nextFlight->spaceFlight->departure_date, false)
                : null;

            $pax->next_flight        = $nextFlight;
            $pax->hours_to_flight    = $hoursToFlight;
            $pax->is_72h_alert       = $hoursToFlight !== null && $hoursToFlight <= 72 && $hoursToFlight >= 0;
            $pax->passport_ok        = $pax->hasValidPassport();
            $pax->training_ok        = $pax->hasValidTraining();
            $pax->medical_ok         = in_array($pax->physical_fitness, ['Excelente', 'En entrenamiento']);
            $pax->fully_ready        = $pax->passport_ok && $pax->training_ok && $pax->medical_ok;

            return $pax;
        });

        // Filtrar
        if ($this->filterStatus === 'ready') {
            $passengers = $passengers->filter(fn($p) => $p->fully_ready);
        } elseif ($this->filterStatus === 'issues') {
            $passengers = $passengers->filter(fn($p) => !$p->fully_ready);
        } elseif ($this->filterStatus === 'urgent') {
            $passengers = $passengers->filter(fn($p) => $p->is_72h_alert);
        }

        // Ordenar: primero los urgentes
        $passengers = $passengers->sortByDesc(fn($p) => $p->is_72h_alert ? 1 : 0)->values();

        return view('livewire.gestor.compliance', compact('passengers'))
            ->layout('layouts.gestor');
    }
}
