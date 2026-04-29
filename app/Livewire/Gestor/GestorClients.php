<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\User;
use App\Models\Passenger;

class GestorClients extends Component
{
    public string $search = '';
    public string $sortDir = 'asc';

    // — Ficha cliente seleccionado
    public ?int $selectedClientId = null;
    public ?User $selectedClient = null;

    // — Formulario Pasajero
    public bool $showPassengerForm = false;
    public ?int $editingPassengerId = null;
    public string $pax_document_number = '';
    public string $pax_document_country = 'ESP';
    public string $pax_name = '';
    public string $pax_primarylastname = '';
    public string $pax_secondarylastname = '';
    public string $pax_birth_date = '';
    public string $pax_blood_type = '';
    public string $pax_allergies = '';
    public string $pax_physical_fitness = 'No apto';

    // — Modales
    public bool $showDeleteModal = false;
    public ?int $deletePassengerId = null;
    public bool $showSaveModal = false;

    protected function rules(): array
    {
        return [
            'pax_document_number'    => ['required', 'string', 'max:30',
                \Illuminate\Validation\Rule::unique('passengers', 'document_number')
                    ->where('document_country', $this->pax_document_country)
                    ->ignore($this->editingPassengerId)
            ],
            'pax_document_country'   => 'required|string|size:3',
            'pax_name'               => 'required|string|max:100',
            'pax_primarylastname'    => 'nullable|string|max:100',
            'pax_secondarylastname'  => 'nullable|string|max:100',
            'pax_birth_date'         => 'required|date|before_or_equal:-18 years',
            'pax_blood_type'         => 'nullable|string|max:5',
            'pax_allergies'          => 'nullable|string|max:500',
            'pax_physical_fitness'   => 'required|in:Excelente,En entrenamiento,No apto',
        ];
    }

    protected function messages(): array
    {
        return [
            'pax_birth_date.before_or_equal' => 'El pasajero debe ser mayor de 18 años.',
            'pax_document_number.unique'      => 'Ya existe un pasajero con este documento en ese país.',
        ];
    }

    // ── Selección de Cliente ─────────────────────────────────────────────────

    public function selectClient(int $id): void
    {
        $this->selectedClientId = $id;
        $this->selectedClient   = User::where('assigned_manager_id', auth()->id())->find($id);
        $this->resetPassengerForm();
    }

    public function clearSelection(): void
    {
        $this->selectedClientId = null;
        $this->selectedClient   = null;
        $this->resetPassengerForm();
    }

    // ── Pasajeros ────────────────────────────────────────────────────────────

    public function createPassenger(): void
    {
        $this->resetPassengerForm();
        $this->showPassengerForm = true;
    }

    public function editPassenger(int $id): void
    {
        $pax = Passenger::where('user_id', $this->selectedClientId)->findOrFail($id);
        $this->editingPassengerId   = $pax->id;
        $this->pax_document_number  = $pax->document_number;
        $this->pax_document_country = $pax->document_country;
        $this->pax_name             = $pax->name;
        $this->pax_primarylastname  = $pax->primarylastname ?? '';
        $this->pax_secondarylastname = $pax->secondarylastname ?? '';
        $this->pax_birth_date       = $pax->birth_date?->format('Y-m-d') ?? '';
        $this->pax_blood_type       = $pax->blood_type ?? '';
        $this->pax_allergies        = $pax->allergies ?? '';
        $this->pax_physical_fitness = $pax->physical_fitness;
        $this->showPassengerForm    = true;
    }

    public function confirmSavePassenger(): void
    {
        $this->validate();
        $this->showSaveModal = true;
    }

    public function savePassenger(): void
    {
        $this->validate();
        $this->showSaveModal = false;

        $data = [
            'user_id'             => $this->selectedClientId,
            'document_number'     => strtoupper(trim($this->pax_document_number)),
            'document_country'    => strtoupper($this->pax_document_country),
            'name'                => $this->pax_name,
            'primarylastname'     => $this->pax_primarylastname ?: null,
            'secondarylastname'   => $this->pax_secondarylastname ?: null,
            'birth_date'          => $this->pax_birth_date,
            'blood_type'          => $this->pax_blood_type ?: null,
            'allergies'           => $this->pax_allergies ?: null,
            'physical_fitness'    => $this->pax_physical_fitness,
        ];

        if ($this->editingPassengerId) {
            Passenger::where('user_id', $this->selectedClientId)->findOrFail($this->editingPassengerId)->update($data);
            session()->flash('message', 'Pasajero actualizado.');
        } else {
            Passenger::create($data);
            session()->flash('message', 'Pasajero añadido correctamente.');
        }

        $this->resetPassengerForm();
    }

    public function confirmDeletePassenger(int $id): void
    {
        $this->deletePassengerId = $id;
        $this->showDeleteModal   = true;
    }

    public function deletePassenger(): void
    {
        if ($this->deletePassengerId) {
            Passenger::where('user_id', $this->selectedClientId)
                ->findOrFail($this->deletePassengerId)
                ->delete();
            session()->flash('message', 'Pasajero eliminado.');
        }
        $this->showDeleteModal   = false;
        $this->deletePassengerId = null;
    }

    private function resetPassengerForm(): void
    {
        $this->showPassengerForm       = false;
        $this->editingPassengerId      = null;
        $this->pax_document_number     = '';
        $this->pax_document_country    = 'ESP';
        $this->pax_name                = '';
        $this->pax_primarylastname     = '';
        $this->pax_secondarylastname   = '';
        $this->pax_birth_date          = '';
        $this->pax_blood_type          = '';
        $this->pax_allergies           = '';
        $this->pax_physical_fitness    = 'No apto';
        $this->resetValidation();
    }

    // ── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $clients = User::where('assigned_manager_id', auth()->id())
            ->where('role', 'cliente')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            }))
            ->withCount('passengers')
            ->orderBy('name', $this->sortDir)
            ->get();

        $passengers = $this->selectedClientId
            ? Passenger::where('user_id', $this->selectedClientId)->get()
            : collect();

        return view('livewire.gestor.clients', compact('clients', 'passengers'))
            ->layout('layouts.gestor');
    }
}
