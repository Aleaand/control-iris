<?php
namespace App\Livewire\Admin;
use App\Traits\HasResponsivePagination;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Starship;
use App\Models\Flight;
use App\Models\Reservation;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskStatusNotification;

class ManageStarships extends Component
{
    use WithPagination, HasResponsivePagination;
    public $name, $general_capacity, $vip_capacity, $crew_capacity, $status = 'active';
    public $operational_cost_per_au = 0;
    public $depreciation_per_au = 0;
    public $cruise_speed_au = 0;
    public $crew_hourly_rate = 0;
    public $crew_daily_rate = 0;
    public $isEditing = false, $starshipId;
    public $search = '';

    #[\Livewire\Attributes\Url(as: 'status')]
    public $statusFilter = 'all';

    public $sortDir = 'asc';
    public $showSaveModal = false;
    public $showDeleteModal = false;
    public $showCascadeDeleteModal = false;
    public $deleteId = null;
    public $flightsCount = 0;
    public $maintenance_start_date;
    public $maintenance_end_date;
    public $showConflictModal = false;

    protected $messages = [
        'name.required' => 'El nombre de la nave obligatoriob.',
        'name.max' => 'El nombre de la nave no puede exceder los 255 caracteres.',
        'general_capacity.required' => 'La configuración de capacidad Nova es necesaria.',
        'general_capacity.integer' => 'La capacidad Nova debe ser un número entero.',
        'general_capacity.min' => 'No se permiten valores negativos en la capacidad Nova.',
        'vip_capacity.required' => 'La configuración de capacidad Supernova es necesaria (use 0 si no aplica).',
        'vip_capacity.integer' => 'La capacidad Supernova debe ser un número entero.',
        'vip_capacity.min' => 'No se permiten valores negativos en la capacidad Supernova.',
        'status.required' => 'El estado operativo de la nave es obligatorio.',
        'cruise_speed_au.required' => 'La velocidad de crucero es obligatoria.',
        'cruise_speed_au.min' => 'El rendimiento debe ser mayor que 0 Horas/AU.',
        'maintenance_start_date.after_or_equal' => 'El inicio del mantenimiento no puede ser en el pasado.',
        'maintenance_start_date.required_if' => 'Debe indicar la fecha de inicio del mantenimiento.',
        'maintenance_end_date.after' => 'La fecha final debe ser posterior al inicio.',
        'maintenance_end_date.required_if' => 'Debe indicar la fecha de fin de mantenimiento.',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'general_capacity' => 'required|integer|min:0',
            'vip_capacity' => 'required|integer|min:0',
            'crew_capacity' => 'required|integer|min:0',
            'status' => 'required|string|max:50',
            'operational_cost_per_au' => 'required|numeric|min:0',
            'depreciation_per_au' => 'required|numeric|min:0',
            'cruise_speed_au' => 'required|numeric|min:0.0001',
            'crew_hourly_rate' => 'nullable|numeric|min:0',
            'crew_daily_rate' => 'nullable|numeric|min:0',
            'maintenance_start_date' => 'required_if:status,maintenance|nullable|date|after_or_equal:today',
            'maintenance_end_date' => 'required_if:status,maintenance|nullable|date|after:maintenance_start_date',
        ];
    }

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->general_capacity = 0;
        $this->vip_capacity = 0;
        $this->crew_capacity = 0;
        $this->operational_cost_per_au = 0;
        $this->depreciation_per_au = 0;
        $this->cruise_speed_au = 0;
        $this->crew_hourly_rate = 0;
        $this->crew_daily_rate = 0;
        $this->status = 'active';
        $this->isEditing = false;
        $this->starshipId = null;

        $this->maintenance_start_date = null;
        $this->maintenance_end_date = null;

        $this->resetValidation();
        $this->showSaveModal = false;
        $this->showDeleteModal = false;
        $this->showCascadeDeleteModal = false;

        $this->deleteId = null;
        $this->flightsCount = 0;
    }

    private function updateStarshipStatuses()
    {
        $today = \Carbon\Carbon::today()->toDateString();

        // 1. Finalizar mantenimientos caducados
        Starship::where('status', 'maintenance')
            ->whereNotNull('maintenance_end_date')
            ->where('maintenance_end_date', '<', $today)
            ->update([
                'status' => 'active',
                'maintenance_start_date' => null,
                'maintenance_end_date' => null
            ]);

        // 2. Iniciar mantenimientos programados
        Starship::where('status', 'active')
            ->whereNotNull('maintenance_start_date')
            ->where('maintenance_start_date', '<=', $today)
            ->where('maintenance_end_date', '>=', $today)
            ->update([
                'status' => 'maintenance'
            ]);

        // 3. Limpieza de fechas huérfanas en naves activas
        Starship::where('status', 'active')
            ->whereNotNull('maintenance_end_date')
            ->where('maintenance_end_date', '<', $today)
            ->update([
                'maintenance_start_date' => null,
                'maintenance_end_date' => null
            ]);
    }

    public function render()
    {
        $this->updateStarshipStatuses();

        $query = Starship::query();

        if ($this->search) {
            $searchTerm = '%' . strtolower($this->search) . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                  ->orWhereRaw("LPAD(id::text, 4, '0') LIKE ?", ["{$this->search}%"])
                  ->orWhereRaw('id::text LIKE ?', ["{$this->search}%"]);
            });
        }

        if ($this->statusFilter === 'in_flight') {
            $query->where('status', 'active')->whereHas('flights', function ($q) {
                $q->where('status', 'in_orbit');
            });
        } elseif ($this->statusFilter === 'maintenance') {
            $query->where('status', 'maintenance');
        } elseif ($this->statusFilter === 'ready') {
            $query->where('status', 'active')->whereHas('flights', function ($q) {
                $q->where('status', 'scheduled');
            });
        } elseif ($this->statusFilter === 'idle') {
            $query->where('status', 'active')->whereDoesntHave('flights', function ($q) {
                $q->whereIn('status', ['in_orbit', 'scheduled']);
            });
        }

        $starships = $query->orderBy('name', $this->sortDir)->paginate($this->getPerPage());

        return view('livewire.admin.manage-starships', [
            'starships' => $starships
        ])->layout('layouts.app');
    }

    public function toggleSort()
    {
        $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
    }

    public function setCreateMode()
    {
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $this->isEditing = true;
        $starship = Starship::find($id);
        if ($starship) {
            $this->starshipId = $id;
            $this->name = $starship->name;
            $this->general_capacity = $starship->general_capacity;
            $this->vip_capacity = $starship->vip_capacity;
            $this->crew_capacity = $starship->crew_capacity;
            $this->operational_cost_per_au = $starship->operational_cost_per_au;
            $this->depreciation_per_au = $starship->depreciation_per_au ?? 0;
            $this->cruise_speed_au = $starship->cruise_speed_au ?? 0;
            $this->crew_hourly_rate = $starship->crew_hourly_rate ?? 0;
            $this->crew_daily_rate = $starship->crew_daily_rate ?? 0;
            $this->status = $starship->maintenance_start_date ? 'maintenance' : $starship->status;
            $this->maintenance_start_date = $starship->maintenance_start_date ? \Carbon\Carbon::parse($starship->maintenance_start_date)->format('Y-m-d') : null;
            $this->maintenance_end_date = $starship->maintenance_end_date ? \Carbon\Carbon::parse($starship->maintenance_end_date)->format('Y-m-d') : null;
        }
        $this->resetValidation();
    }

    public function confirmSave()
    {
        $this->validate();

        $starship = $this->starshipId ? Starship::find($this->starshipId) : new Starship();

        $conflictos = 0;
        if (($this->status === 'maintenance' || $this->status === 'retired') && $starship && $starship->exists) {
            $query = $starship->flights()->whereIn('status', ['scheduled', 'in_orbit']);

            if ($this->status === 'maintenance') {
                $start = $this->maintenance_start_date . ' 00:00:00';
                $end = $this->maintenance_end_date . ' 23:59:59';

                $query->where(function ($q) use ($start, $end) {
                    $q->where('departure_date', '<=', $end)
                        ->where('arrival_date', '>=', $start);
                });
            }

            $conflictos = $query->count();
        }

        if ($conflictos > 0) {
            $this->flightsCount = $conflictos;
            $this->showConflictModal = true;
            $this->showSaveModal = false;
        } else {
            $this->showSaveModal = true;
            $this->showConflictModal = false;
        }
    }

    public function executeSave()
    {
        $this->validate();

        $starship = $this->starshipId ? Starship::find($this->starshipId) : new Starship();

        $dbStatus = $this->status;
        if ($this->status === 'maintenance') {
            if ($this->maintenance_start_date > \Carbon\Carbon::today()->toDateString()) {
                $dbStatus = 'active';
            }
        } else {
            $this->maintenance_start_date = null;
            $this->maintenance_end_date = null;
        }

        $starship->fill([
            'name' => $this->name,
            'general_capacity' => $this->general_capacity,
            'vip_capacity' => $this->vip_capacity,
            'crew_capacity' => $this->crew_capacity,
            'operational_cost_per_au' => $this->operational_cost_per_au,
            'depreciation_per_au' => $this->depreciation_per_au,
            'cruise_speed_au' => $this->cruise_speed_au,
            'crew_hourly_rate' => $this->crew_hourly_rate,
            'crew_daily_rate' => $this->crew_daily_rate,
            'status' => $dbStatus,
            'maintenance_start_date' => $this->maintenance_start_date,
            'maintenance_end_date' => $this->maintenance_end_date,
        ]);

        $starship->save();

        session()->flash('message', 'Nave actualizada correctamente.');
        $this->resetInputFields();
    }

    public function handleRedirectToFlights()
    {
        $this->executeSave();
        return redirect()->route('admin.flights', ['search' => $this->name]);
    }

    public function handleDelegateToGestor()
    {
        if ($this->starshipId) {
            $starship = Starship::find($this->starshipId);

            if ($starship) {
                $flightsToCancel = $starship->flights()
                    ->whereIn('status', ['scheduled', 'in_orbit'])
                    ->get();

                foreach ($flightsToCancel as $flight) {
                    $flight->update(['status' => 'cancelled']);
                }

                $this->notifyGestoresAboutCancellation($flightsToCancel, $starship);

                $this->showConflictModal = false;
                $this->executeSave();

                session()->flash('message', 'Vuelos cancelados y tareas de reubicación asignadas a los gestores.');
            }
        }
        $this->showConflictModal = false;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $starship = Starship::find($id);

        if ($starship) {
            $this->flightsCount = $starship->flights()->count();
            if ($this->flightsCount > 0) {
                $this->showCascadeDeleteModal = true;
            } else {
                $this->showDeleteModal = true;
            }
        }
    }

    public function executeDelete()
    {
        if ($this->deleteId) {
            $starship = Starship::find($this->deleteId);

            if ($starship) {
                if ($this->flightsCount > 0) {
                    $flightsToCancel = $starship->flights()
                        ->whereIn('status', ['scheduled', 'in_orbit'])
                        ->get();

                    foreach ($flightsToCancel as $flight) {
                        $flight->update(['status' => 'cancelled']);
                    }

                    $this->notifyGestoresAboutCancellation($flightsToCancel, $starship);

                    $starship->delete();
                    session()->flash('message', 'Nave eliminada. Vuelos cancelados y tareas de reubicación asignadas a gestores.');
                } else {
                    $starship->forceDelete();
                    session()->flash('message', 'Nave eliminada con exito');
                }
            }
        }
        $this->resetInputFields();
    }

    private function notifyGestoresAboutCancellation($flights, $starship = null)
    {
        $currentStarshipId = $starship ? $starship->id : $this->starshipId;
        $starshipName = $starship ? $starship->name : (Starship::find($this->starshipId)?->name ?? 'Nave Desconocida');
        $adminId = auth()->id();
        $gestorMap = [];

        foreach ($flights as $flight) {
            $gestoresAffected = User::whereHas('clients.reservations', function ($query) use ($flight) {
                $query->where('space_flight_id', $flight->id)
                    ->whereNull('deleted_at');
            })->where('role', 'gestor')->get();

            foreach ($gestoresAffected as $gestor) {
                if (!isset($gestorMap[$gestor->id])) {
                    $gestorMap[$gestor->id] = [
                        'user' => $gestor,
                        'flights' => []
                    ];
                }
                $gestorMap[$gestor->id]['flights'][] = $flight;
            }
        }

        foreach ($gestorMap as $data) {
            $gestor = $data['user'];
            $affectedFlights = $data['flights'];
            $flightIds = collect($affectedFlights)->pluck('id');
            $flightCount = count($affectedFlights);
            $flightCodes = collect($affectedFlights)->pluck('flight_code')->implode(', ');

            $affectedReservations = Reservation::whereIn('space_flight_id', $flightIds)
                ->whereHas('user', function ($q) use ($gestor) {
                    $q->where('assigned_manager_id', $gestor->id);
                })
                ->with(['passenger', 'spaceFlight'])
                ->get();

            $passengersPayload = $affectedReservations->map(fn($res) => [
                'passenger_id' => $res->passenger->id,
                'passenger_name' => $res->passenger->name . ' ' . $res->passenger->primarylastname,
                'flight_code' => $res->spaceFlight->flight_code,
                'reservation_id' => $res->id
            ])->toArray();

            $passengerCount = $affectedReservations->count();
            $task = Task::create([
                'assigned_gestor_id' => $gestor->id,
                'created_by' => $adminId,
                'title' => 'Relocación Crítica: ' . ($flightCount > 1 ? 'Múltiples Vuelos Cancelados' : 'Vuelo Cancelado'),
                'description' => "Se ha(n) cancelado {$flightCount} vuelo(s) [{$flightCodes}] de la nave {$starshipName}. Hay {$passengerCount} pasajero(s) bajo su gestión afectados que requieren reubicación inmediata.",
                'type' => 'flight_cancelled',
                'priority' => 'urgente',
                'status' => 'Pendiente',
                'payload' => [
                    'starship_id' => $currentStarshipId,
                    'flights' => collect($affectedFlights)->map(fn($f) => [
                        'id' => $f->id,
                        'code' => $f->flight_code,
                        'date' => $f->departure_date->format('Y-m-d H:i')
                    ])->toArray(),
                    'affected_passengers' => $passengersPayload
                ],
            ]);
            try {
                \Illuminate\Support\Facades\Log::info("Enviando notificación de tarea #{$task->id} al gestor: {$gestor->email}");
                $gestor->notify(new TaskStatusNotification($task, 'created'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error al enviar notificación al gestor {$gestor->email}: " . $e->getMessage());
            }
        }
    }
}

