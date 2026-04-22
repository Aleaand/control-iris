<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Starship;
use App\Models\Flight;
use App\Models\Reservation;

class ManageStarships extends Component
{
    public $name, $general_capacity, $vip_capacity, $crew_capacity, $status = 'active';
    public $operational_cost_per_au = 0;
    public $cruise_speed_au = 0;
    public $crew_hourly_rate = 0;
    public $crew_daily_rate = 0;
    public $isEditing = false, $starshipId;
    public $search = '';
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
        'name.required'                        => 'El nombre de la nave obligatoriob.',
        'name.max'                             => 'El nombre de la nave no puede exceder los 255 caracteres.',
        'general_capacity.required'            => 'La configuración de capacidad Nova es necesaria.',
        'general_capacity.integer'             => 'La capacidad Nova debe ser un número entero.',
        'general_capacity.min'                 => 'No se permiten valores negativos en la capacidad Nova.',
        'vip_capacity.required'                => 'La configuración de capacidad Supernova es necesaria (use 0 si no aplica).',
        'vip_capacity.integer'                 => 'La capacidad Supernova debe ser un número entero.',
        'vip_capacity.min'                     => 'No se permiten valores negativos en la capacidad Supernova.',
        'status.required'                      => 'El estado operativo de la nave es obligatorio.',
        'cruise_speed_au.required'             => 'La velocidad de crucero es obligatoria.',
        'cruise_speed_au.min'                  => 'El rendimiento debe ser mayor que 0 Horas/AU.',
        'maintenance_start_date.after_or_equal' => 'El inicio del mantenimiento no puede ser en el pasado.',
        'maintenance_start_date.required_if'   => 'Debe indicar la fecha de inicio del mantenimiento.',
        'maintenance_end_date.after'           => 'La fecha final debe ser posterior al inicio.',
        'maintenance_end_date.required_if'     => 'Debe indicar la fecha de fin de mantenimiento.',
    ];

    protected function rules()
    {
        return [
            'name'                    => 'required|string|max:255',
            'general_capacity'        => 'required|integer|min:0',
            'vip_capacity'            => 'required|integer|min:0',
            'crew_capacity'           => 'required|integer|min:0',
            'status'                  => 'required|string|max:50',
            'operational_cost_per_au' => 'required|numeric|min:0',
            'cruise_speed_au'         => 'required|numeric|min:0.0001',
            'crew_hourly_rate'        => 'required|numeric|min:0',
            'crew_daily_rate'         => 'required|numeric|min:0',
            'maintenance_start_date'  => 'required_if:status,maintenance|nullable|date|after_or_equal:today',
            'maintenance_end_date'    => 'required_if:status,maintenance|nullable|date|after:maintenance_start_date',
        ];
    }

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->general_capacity = '';
        $this->vip_capacity = 0;
        $this->crew_capacity = 0;
        $this->operational_cost_per_au = 0;
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
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('id', 'like', $this->search . '%');
        }

        $starships = $query->orderBy('name', $this->sortDir)->get();

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
            'name'                    => $this->name,
            'general_capacity'        => $this->general_capacity,
            'vip_capacity'            => $this->vip_capacity,
            'crew_capacity'           => $this->crew_capacity,
            'operational_cost_per_au' => $this->operational_cost_per_au,
            'cruise_speed_au'         => $this->cruise_speed_au,
            'crew_hourly_rate'        => $this->crew_hourly_rate,
            'crew_daily_rate'         => $this->crew_daily_rate,
            'status'                  => $dbStatus,
            'maintenance_start_date'  => $this->maintenance_start_date,
            'maintenance_end_date'    => $this->maintenance_end_date,
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
                $starship->flights()
                    ->whereIn('status', ['scheduled', 'in_orbit'])
                    ->update(['status' => 'cancelled']);
                $this->showConflictModal = false;
                $this->executeSave();

                // TODO: Desarrollar un sistema de notificaciones push para que el Gestor reciba el aviso 
                // de 'Vuelo Cancelado' en tiempo real. En la siguiente fase, el sistema debería 
                // sugerir automáticamente naves alternativas con capacidad suficiente (Nova/SuperNova).

                session()->flash('message', 'Se ha notificado a los Gestores para la reubicación de los clientes.');
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
                    $starship->flights()
                        ->whereIn('status', ['scheduled', 'in_orbit'])
                        ->update(['status' => 'cancelled']);

                    $starship->delete();
                    session()->flash('message', 'Nave eliminada. Vuelos cancelados y Gestores notificados para reubicación.');
                } else {
                    $starship->forceDelete();
                    session()->flash('message', 'Nave eliminada con exito');
                }
            }
        }
        $this->resetInputFields();
    }
}

