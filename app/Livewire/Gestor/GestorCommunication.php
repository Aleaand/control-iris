<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\User;
use App\Models\ContactLog;

class GestorCommunication extends Component
{
    public ?int $selectedClientId = null;
    public string $search = '';

    // — Nuevo log
    public string $log_type      = 'nota';
    public string $log_notes     = '';
    public string $log_zoom_link = '';
    public bool   $showLogForm   = false;

    protected function rules(): array
    {
        return [
            'log_type'      => 'required|in:nota,llamada,email,videollamada,otro',
            'log_notes'     => 'required|string|max:1000',
            'log_zoom_link' => 'nullable|url|max:500',
        ];
    }

    public function selectClient(int $id): void
    {
        $this->selectedClientId = $id;
        $this->resetLogForm();
    }

    public function openLogForm(): void
    {
        $this->resetLogForm();
        $this->showLogForm = true;
    }

    public function saveLog(): void
    {
        $this->validate();

        ContactLog::create([
            'client_id'  => $this->selectedClientId,
            'gestor_id'  => auth()->id(),
            'type'       => $this->log_type,
            'zoom_link'  => $this->log_type === 'videollamada' ? ($this->log_zoom_link ?: null) : null,
            'notes'      => $this->log_notes,
        ]);

        session()->flash('message', 'Interacción registrada.');
        $this->resetLogForm();
    }

    private function resetLogForm(): void
    {
        $this->showLogForm   = false;
        $this->log_type      = 'nota';
        $this->log_notes     = '';
        $this->log_zoom_link = '';
        $this->resetValidation();
    }

    public function render()
    {
        $clients = User::where('assigned_manager_id', auth()->id())
            ->where('role', 'cliente')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            }))
            ->orderBy('name')
            ->get();

        $logs = $this->selectedClientId
            ? ContactLog::where('client_id', $this->selectedClientId)
                ->where('gestor_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        $selectedClient = $this->selectedClientId
            ? User::where('assigned_manager_id', auth()->id())->find($this->selectedClientId)
            : null;

        return view('livewire.gestor.communication', compact('clients', 'logs', 'selectedClient'))
            ->layout('layouts.gestor');
    }
}
