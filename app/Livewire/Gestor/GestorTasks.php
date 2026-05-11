<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination;
use App\Traits\HasResponsivePagination;

class GestorTasks extends Component
{
    use WithPagination, HasResponsivePagination;

    public string $activeSection = 'system';
    public string $viewMode = 'kanban';
    
    public string $filterStatus = '';
    public string $filterPriority = '';
    public bool $showCompleted = false;

    // Sorting
    public string $sortBy = 'created_at'; // 'created_at', 'status', 'priority'
    public string $sortDir = 'desc';

    // Modal states
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDetailModal = false;
    
    // Form data
    public ?int $editingTaskId = null;
    public string $taskTitle = '';
    public string $taskDescription = '';
    public string $taskPriority = 'media';

    public ?Task $selectedTask = null;

    protected $queryString = [
        'activeSection', 'viewMode', 'filterStatus', 
        'showCompleted', 'sortBy', 'sortDir'
    ];

    public function setSection(string $section): void
    {
        $this->activeSection = $section;
        $this->resetPage();
    }

    public function toggleView(): void
    {
        $this->viewMode = ($this->viewMode === 'list') ? 'kanban' : 'list';
    }

    public function setSort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDir = ($this->sortDir === 'desc') ? 'asc' : 'desc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'desc';
        }
    }

    public function updateStatus(int $id, string $status): void
    {
        $task = Task::where('assigned_gestor_id', auth()->id())->findOrFail($id);
        $update = ['status' => $status];
        if ($status === 'Completada') {
            $update['completed_at'] = now();
        }
        $task->update($update);
    }

    public function markAsRead(int $id): void
    {
        $task = Task::where('assigned_gestor_id', auth()->id())->findOrFail($id);
        $payload = $task->payload ?? [];
        $payload['is_read'] = true;
        $task->update(['payload' => $payload]);
    }

    public function createTask(): void
    {
        $this->validate(['taskTitle' => 'required|min:3']);

        Task::create([
            'assigned_gestor_id' => auth()->id(),
            'created_by' => auth()->id(),
            'title' => $this->taskTitle,
            'description' => $this->taskDescription,
            'priority' => $this->taskPriority,
            'type' => 'general',
            'status' => 'Pendiente',
            'payload' => ['is_personal' => true]
        ]);

        $this->reset(['taskTitle', 'taskDescription', 'taskPriority', 'showCreateModal']);
    }

    public function editTask(int $id): void
    {
        $task = Task::where('assigned_gestor_id', auth()->id())
                    ->where('type', 'general')
                    ->where('payload->is_personal', true)
                    ->findOrFail($id);
        
        $this->editingTaskId = $id;
        $this->taskTitle = $task->title;
        $this->taskDescription = $task->description;
        $this->taskPriority = $task->priority;
        $this->showEditModal = true;
    }

    public function updateTask(): void
    {
        $this->validate(['taskTitle' => 'required|min:3']);

        $task = Task::where('assigned_gestor_id', auth()->id())
                    ->where('type', 'general')
                    ->where('payload->is_personal', true)
                    ->findOrFail($this->editingTaskId);

        $task->update([
            'title' => $this->taskTitle,
            'description' => $this->taskDescription,
            'priority' => $this->taskPriority,
        ]);

        $this->reset(['editingTaskId', 'taskTitle', 'taskDescription', 'taskPriority', 'showEditModal']);
    }

    public function deleteTask(int $id): void
    {
        Task::where('assigned_gestor_id', auth()->id())
            ->where('type', 'general')
            ->where('payload->is_personal', true)
            ->findOrFail($id)
            ->delete();
    }

    public function markAllAsSeen(): void
    {
        $tasks = Task::where('assigned_gestor_id', auth()->id())
            ->when($this->activeSection === 'system', fn($q) => $q->where('type', '!=', 'personal'))
            ->when($this->activeSection === 'personal', fn($q) => $q->where('type', 'personal'))
            ->get();

        foreach ($tasks as $task) {
            $payload = $task->payload ?? [];
            if (!($payload['is_seen'] ?? false)) {
                $payload['is_seen'] = true;
                $task->update(['payload' => $payload]);
            }
        }
        
        session()->flash('message', 'Todas las tareas marcadas como vistas.');
    }

    public function markAsSeen(int $id): void
    {
        $task = Task::where('assigned_gestor_id', auth()->id())->findOrFail($id);
        $payload = $task->payload ?? [];
        $payload['is_seen'] = true;
        $task->update(['payload' => $payload]);
    }

    public function viewDetail(int $id): void
    {
        $task = Task::where('assigned_gestor_id', auth()->id())->findOrFail($id);
        
        // Marcar como vista automáticamente al ver
        if (!($task->payload['is_seen'] ?? false)) {
            $this->markAsSeen($id);
        }

        $this->selectedTask = $task;
        $this->showDetailModal = true;
    }

    public function render()
    {
        $query = Task::where('assigned_gestor_id', auth()->id())
            ->when($this->activeSection === 'system', function($q) {
                $q->where(function($sq) {
                    $sq->where('type', '!=', 'general')
                       ->orWhere(function($ssq) {
                           $ssq->where('type', 'general')
                               ->where(function($finalq) {
                                   $finalq->whereNull('payload->is_personal')
                                          ->orWhere('payload->is_personal', false);
                               });
                       });
                });
            })
            ->when($this->activeSection === 'personal', fn($q) => $q->where('type', 'general')->where('payload->is_personal', true))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when(!$this->showCompleted, fn($q) => $q->where('status', '!=', 'Completada'));

        // Advanced Sorting Logic
        if ($this->sortBy === 'priority') {
            $query->orderByRaw("CASE 
                WHEN priority = 'urgente' THEN 1 
                WHEN priority = 'alta' THEN 2 
                WHEN priority = 'media' THEN 3 
                WHEN priority = 'baja' THEN 4 
                ELSE 5 END " . $this->sortDir);
        } elseif ($this->sortBy === 'status') {
            $query->orderByRaw("CASE 
                WHEN status = 'Pendiente' THEN 1 
                WHEN status = 'Aceptada' THEN 2 
                WHEN status = 'En progreso' THEN 3 
                WHEN status = 'Completada' THEN 4 
                ELSE 5 END " . $this->sortDir);
        } else {
            $query->orderBy('created_at', $this->sortDir);
        }

        $tasks = ($this->viewMode === 'list') 
            ? (clone $query)->paginate(15) // Compact list needs more items per page
            : (clone $query)->get();

        $systemPending = Task::where('assigned_gestor_id', auth()->id())->where('type', '!=', 'personal')->where('status', 'Pendiente')->count();
        $personalPending = Task::where('assigned_gestor_id', auth()->id())->where('type', 'personal')->where('status', 'Pendiente')->count();

        return view('livewire.gestor.tasks', [
            'tasks' => $tasks,
            'systemPending' => $systemPending,
            'personalPending' => $personalPending
        ])->layout('layouts.gestor');
    }
}
