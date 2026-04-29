<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Task;
use App\Models\Flight;
use Carbon\Carbon;

class GestorDashboard extends Component
{
    public function render()
    {
        $gestorId = auth()->id();

        // — Clientes bajo mi gestión
        $totalClients = User::where('assigned_manager_id', $gestorId)->count();

        // — Pasajeros totales de mis clientes
        $totalPassengers = \App\Models\Passenger::whereHas('client', fn($q) =>
            $q->where('assigned_manager_id', $gestorId)
        )->count();

        // — Reservas activas de mis clientes
        $activeReservations = Reservation::whereHas('user', fn($q) =>
            $q->where('assigned_manager_id', $gestorId)
        )->whereNotIn('status', ['Cancelada'])->count();

        // — Pagos pendientes
        $pendingPayments = Reservation::whereHas('user', fn($q) =>
            $q->where('assigned_manager_id', $gestorId)
        )->where('payment_status', '!=', 'paid')->whereNotIn('status', ['Cancelada'])->count();

        // — Vuelos próximos en < 72 horas (necesitan FINAL GO)
        $urgentFlights = Reservation::whereHas('user', fn($q) =>
            $q->where('assigned_manager_id', $gestorId)
        )->whereHas('spaceFlight', fn($q) =>
            $q->whereBetween('departure_date', [now(), now()->addHours(72)])
        )->whereNotIn('status', ['Cancelada'])->count();

        // — Tareas pendientes
        $pendingTasks = Task::where('assigned_gestor_id', $gestorId)
            ->where('status', 'Pendiente')->count();

        // — Reservas recientes
        $recentReservations = Reservation::with(['user', 'passenger', 'spaceFlight.destination'])
            ->whereHas('user', fn($q) => $q->where('assigned_manager_id', $gestorId))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // — Próximos vuelos de mis clientes
        $upcomingFlights = Reservation::with(['user', 'passenger', 'spaceFlight.destination'])
            ->join('flights', 'reservations.space_flight_id', '=', 'flights.id')
            ->whereHas('user', fn($q) => $q->where('assigned_manager_id', $gestorId))
            ->where('flights.departure_date', '>', now())
            ->whereNotIn('reservations.status', ['Cancelada'])
            ->orderBy('flights.departure_date', 'asc')
            ->select('reservations.*')
            ->take(5)
            ->get();

        // — Misiones urgentes
        $urgentTasks = Task::where('assigned_gestor_id', $gestorId)
            ->whereIn('status', ['Pendiente', 'Aceptada'])
            ->where('priority', 'urgente')
            ->take(3)
            ->get();

        return view('livewire.gestor.dashboard', compact(
            'totalClients', 'totalPassengers', 'activeReservations',
            'pendingPayments', 'urgentFlights', 'pendingTasks',
            'recentReservations', 'upcomingFlights', 'urgentTasks'
        ))->layout('layouts.gestor');
    }
}
