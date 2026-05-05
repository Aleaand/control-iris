<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\Flight;
use App\Models\Starship;
use App\Models\PriceLog;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskStatusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Url;

class AdminDashboard extends Component
{
    #[Url(as: 'assign_to')]
    public $taskGestorId = '';
    public $taskTitle = '';
    public $taskDesc = '';
    public $taskType = 'general';
    public $taskPriority = 'media';
    public $showTaskForm = false;
    public $passengerScope = 'passenger';
    public $taskPassengerId = '';
    public $taskFlightId = '';
    public $taskClientId = '';
    public $filterGestorId = '';
    public $filterTaskType = '';
    public $filterTaskStatus = '';
    public $filterTaskPriority = '';
    public $cancelTaskId = null;
    public $showCancelModal = false;
    public $updateTaskId = null;
    public $updateNewPriority = '';
    public $showUpdateModal = false;

    public function mount()
    {
        if ($this->taskGestorId) {
            $this->showTaskForm = true;
        }
    }

    public function render()
    {
        $currentYear = now()->year;

        $projectedIncome = Reservation::where('payment_status', 'pending')
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');

        $ingresosReales = Reservation::where('payment_status', 'paid')
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');

        $totalGastos = Flight::whereYear('departure_date', $currentYear)
            ->where('status', '!=', 'cancelled')
            ->sum('operational_cost');

        $gananciasNetas = $ingresosReales - $totalGastos;
        $porcentajeGanancias = $ingresosReales > 0 ? ($gananciasNetas / $ingresosReales) * 100 : 0;

        $criticalFlights = Flight::where('departure_date', '>', now())
            ->with(['destination', 'starship'])
            ->get()
            ->filter(fn($f) => $f->occupancy_percentage < 70)
            ->take(3);

        $starshipsStatus = [
            'in_flight' => Starship::whereHas('flights', fn($q) => $q->where('status', 'in_orbit'))->count(),
            'maintenance' => Starship::where('status', 'maintenance')->count(),
            'ready' => Starship::where('status', 'active')->whereHas('flights', fn($q) => $q->where('status', 'scheduled'))->count(),
            'idle' => Starship::where('status', 'active')->whereDoesntHave('flights', fn($q) => $q->whereIn('status', ['in_orbit', 'scheduled']))->count(),
        ];

        $nextLaunches = Flight::where('departure_date', '>', now())
            ->where('status', 'scheduled')
            ->where('departure_date', '<', now()->addHours(48))
            ->with(['destination', 'starship'])
            ->orderBy('departure_date', 'asc')
            ->get();

        $totalAURecorridas = Flight::where('status', 'landed')->sum('au_distance');

        $recentPriceLogs = PriceLog::with('admin')->latest()->take(6)->get();

        try {
            DB::connection()->getPdo();
            $dbStatus = ['label' => 'Base de datos Activa', 'color' => 'bg-emerald-500', 'status' => 'online'];
        } catch (\Exception $e) {
            $dbStatus = ['label' => 'Base de datos Desactivada', 'color' => 'bg-red-500', 'status' => 'offline'];
        }

        $gestores = User::where('role', 'gestor')
            ->withCount(['tasks as pending_tasks_count' => fn($q) => $q->whereIn('status', ['Pendiente', 'Aceptada', 'En progreso'])])
            ->orderByDesc('pending_tasks_count')
            ->get();

        $missions = Task::with(['gestor', 'creator'])
            ->when($this->filterGestorId, fn($q) => $q->where('assigned_gestor_id', $this->filterGestorId))
            ->when($this->filterTaskType, fn($q) => $q->where('type', $this->filterTaskType))
            ->when($this->filterTaskStatus, fn($q) => $q->where('status', $this->filterTaskStatus))
            ->when($this->filterTaskPriority, fn($q) => $q->where('priority', $this->filterTaskPriority))
            ->orderByRaw("CASE WHEN priority='urgente' THEN 1 WHEN priority='alta' THEN 2 WHEN priority='media' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->get();

        if ($this->taskGestorId) {
            $gestorClientIds = User::where('assigned_manager_id', $this->taskGestorId)->pluck('id');

            $allClients = User::whereIn('id', $gestorClientIds)->orderBy('name')->get();
            $allPassengers = \App\Models\Passenger::whereIn('user_id', $gestorClientIds)
                ->with(['client'])
                ->orderBy('name')
                ->get();

            $allFlights = Flight::whereHas('reservations', function ($q) use ($gestorClientIds) {
                $q->whereIn('user_id', $gestorClientIds);
            })
                ->when($this->taskType === 'flight_cancelled', fn($q) => $q->where('status', 'cancelled'))
                ->with('destination')->orderBy('departure_date')->get();
        } else {
            $allPassengers = collect();
            $allFlights = collect();
            $allClients = collect();
        }

        return view('livewire.admin.admin-dashboard', compact(
            'projectedIncome',
            'ingresosReales',
            'porcentajeGanancias',
            'criticalFlights',
            'starshipsStatus',
            'nextLaunches',
            'totalAURecorridas',
            'recentPriceLogs',
            'dbStatus',
            'totalGastos',
            'gestores',
            'missions',
            'allPassengers',
            'allFlights',
            'allClients'
        ))->layout('layouts.app');
    }

    public function updatedTaskGestorId(): void
    {
        $this->reset('taskPassengerId', 'taskFlightId', 'taskClientId');
    }

    public function updatedTaskType(): void
    {
        $this->reset('taskPassengerId', 'taskFlightId', 'taskClientId');
    }

    public function createTask(): void
    {
        $this->validate([
            'taskGestorId' => 'required|exists:users,id',
            'taskTitle' => 'required|min:5|max:255',
            'taskDesc' => 'nullable|max:1000',
            'taskType' => 'required|in:flight_cancelled,policy_change,passenger_issue,general,other',
            'taskPriority' => 'required|in:baja,media,alta,urgente',
        ]);

        $payload = [];
        if ($this->taskType === 'passenger_issue') {
            $payload['passenger_scope'] = $this->passengerScope;
            if ($this->passengerScope === 'passenger' && $this->taskPassengerId) {
                $payload['passenger_id'] = $this->taskPassengerId;
            } elseif ($this->passengerScope === 'flight_passengers' && $this->taskFlightId) {
                $payload['flight_id'] = $this->taskFlightId;
            } elseif ($this->passengerScope === 'client_passengers' && $this->taskClientId) {
                $payload['client_id'] = $this->taskClientId;
            }
        } elseif ($this->taskType === 'flight_cancelled' && $this->taskFlightId) {
            $payload['flight_id'] = $this->taskFlightId;
        }

        $task = Task::create([
            'assigned_gestor_id' => $this->taskGestorId,
            'created_by' => auth()->id(),
            'title' => $this->taskTitle,
            'description' => $this->taskDesc,
            'type' => $this->taskType,
            'status' => 'Pendiente',
            'priority' => $this->taskPriority,
            'payload' => $payload ?: null,
        ]);

        if ($task->gestor) {
            $task->gestor->notify(new TaskStatusNotification($task, 'created'));
        }

        $this->reset('taskGestorId', 'taskTitle', 'taskDesc', 'showTaskForm', 'taskPassengerId', 'taskFlightId', 'taskClientId');
        $this->taskType = 'general';
        $this->taskPriority = 'media';
        $this->passengerScope = 'passenger';
        session()->flash('task_created', '´Tarea asignada correctamente al gestor.');
    }

    public function confirmCancelTask(int $id): void
    {
        $this->cancelTaskId = $id;
        $this->showCancelModal = true;
    }

    public function cancelTask(): void
    {
        if ($this->cancelTaskId) {
            Task::findOrFail($this->cancelTaskId)->delete();
            session()->flash('task_created', 'Tarea cancelada y eliminada de las asignaciones del gestor.');
        }
        $this->cancelTaskId = null;
        $this->showCancelModal = false;
    }

    public function confirmUpdatePriority(int $id, string $priority): void
    {
        $this->updateTaskId = $id;
        $this->updateNewPriority = $priority;
        $this->showUpdateModal = true;
    }

    public function cancelUpdatePriority(): void
    {
        $this->updateTaskId = null;
        $this->updateNewPriority = '';
        $this->showUpdateModal = false;
    }

    public function processUpdatePriority(): void
    {
        if ($this->updateTaskId && in_array($this->updateNewPriority, ['baja', 'media', 'alta', 'urgente'])) {
            $task = Task::findOrFail($this->updateTaskId);
            $oldPriority = $task->priority;

            $weights = ['baja' => 1, 'media' => 2, 'alta' => 3, 'urgente' => 4];
            $shouldNotify = ($weights[$this->updateNewPriority] > $weights[$oldPriority]);

            $task->update(['priority' => $this->updateNewPriority]);

            if ($task->gestor && $shouldNotify) {
                $task->gestor->notify(new TaskStatusNotification($task, 'modified'));
            }

            session()->flash('task_created', $shouldNotify
                ? 'Prioridad elevada y notificada al gestor.'
                : 'Prioridad actualizada correctamente.');
        }
        $this->updateTaskId = null;
        $this->showUpdateModal = false;
    }
    public function updateTaskPriority(int $id, string $priority): void
    {
        $this->confirmUpdatePriority($id, $priority);
    }

    public function exportLogs()
    {
        $logs = PriceLog::with('admin')->orderBy('created_at', 'desc')->get();
        $filename = "logs_modificacion_precios_iris_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Admin', 'Tipo/Entidad', 'Item ID', 'Precio Anterior', 'Precio Nuevo', 'Motivo', 'Fecha']);
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->admin ? $log->admin->name : 'Sistema',
                    $log->item_type,
                    $log->item_id ?: 'Global',
                    $log->old_price,
                    $log->new_price,
                    $log->reason,
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return Response::streamDownload($callback, $filename, $headers);
    }
}
