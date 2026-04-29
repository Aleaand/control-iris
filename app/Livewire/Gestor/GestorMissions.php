<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\Task;

class GestorMissions extends Component
{
    public string $filterStatus = '';
    public string $filterPriority = '';

    public bool $showDetailModal = false;
    public ?Task $selectedTask   = null;

    public function acceptMission(int $id): void
    {
        $task = Task::where('assigned_gestor_id', auth()->id())->findOrFail($id);
        $task->update([
            'status'      => 'Aceptada',
            'accepted_at' => now(),
        ]);
        session()->flash('message', "Misión #{$id} aceptada.");
    }

    public function progressMission(int $id): void
    {
        $task = Task::where('assigned_gestor_id', auth()->id())->findOrFail($id);
        $task->update(['status' => 'En progreso']);
        session()->flash('message', "Misión marcada en progreso.");
    }

    public function completeMission(int $id): void
    {
        $task = Task::where('assigned_gestor_id', auth()->id())->findOrFail($id);
        $task->update([
            'status'       => 'Completada',
            'completed_at' => now(),
        ]);
        session()->flash('message', "Misión completada.");
    }

    public function viewDetail(int $id): void
    {
        $this->selectedTask   = Task::where('assigned_gestor_id', auth()->id())->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedTask    = null;
    }

    public function render()
    {
        $tasks = Task::where('assigned_gestor_id', auth()->id())
            ->when($this->filterStatus,   fn($q) => $q->where('status',   $this->filterStatus))
            ->when($this->filterPriority, fn($q) => $q->where('priority', $this->filterPriority))
            ->with('creator')
            ->orderByRaw("CASE 
                WHEN priority = 'urgente' THEN 1 
                WHEN priority = 'alta' THEN 2 
                WHEN priority = 'media' THEN 3 
                WHEN priority = 'baja' THEN 4 
                ELSE 5 
            END")
            ->orderByRaw("CASE 
                WHEN status = 'Pendiente' THEN 1 
                WHEN status = 'Aceptada' THEN 2 
                WHEN status = 'En progreso' THEN 3 
                WHEN status = 'Completada' THEN 4 
                ELSE 5 
            END")
            ->orderBy('created_at', 'desc')
            ->get();

        $counts = [
            'Pendiente'   => Task::where('assigned_gestor_id', auth()->id())->where('status', 'Pendiente')->count(),
            'Aceptada'    => Task::where('assigned_gestor_id', auth()->id())->where('status', 'Aceptada')->count(),
            'En progreso' => Task::where('assigned_gestor_id', auth()->id())->where('status', 'En progreso')->count(),
            'Completada'  => Task::where('assigned_gestor_id', auth()->id())->where('status', 'Completada')->count(),
        ];

        return view('livewire.gestor.missions', compact('tasks', 'counts'))
            ->layout('layouts.gestor');
    }
}
