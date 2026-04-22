<?php

namespace App\Livewire\Admin;

use App\Models\Passenger;
use App\Models\User;
use App\Models\Location;
use Livewire\Component;

class ManagePassengers extends Component
{
    // Filtro por cliente
    public $filterUserId = null;
    public $filterUserName = null;

    // Búsqueda y orden
    public $search = '';
    public $sortDir = 'asc';

    // Datos de identidad
    public $name, $primarylastname, $secondarylastname;
    public $document_number, $document_country;
    public $birth_date;

    // Datos médicos
    public $blood_type = '';
    public $allergies = '';
    public $physical_fitness = 'No apto';

    // Documentación Iris
    public $iris_passport_number = '';
    public $iris_passport_expiration = '';
    public $training_certificate_date = '';
    public $training_certificate_status = '';

    // Vinculo a cliente
    public $user_id = null;
    public $clientSearch = '';
    public $clientSearchResults = [];
    public $selectedClientName = null;
    public $selectedCountry = null;

    // Control CRUD
    public $isEditing = false;
    public $passengerId = null;
    public $activeTab = 'identity';

    // Modales
    public $showSaveModal = false;
    public $showDeleteModal = false;
    public $deleteId = null;
    public $deleteImpactInfo = [];

    public function mount($userId = null)
    {
        if ($userId) {
            $user = User::find($userId);
            if ($user && $user->role === 'cliente') {
                $this->filterUserId = $userId;
                $this->filterUserName = $user->name;
                $this->user_id = $userId;
                $this->selectedClientName = $user->name;
            }
        }
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|min:2|max:100',
            'primarylastname' => 'nullable|string|max:100',
            'secondarylastname' => 'nullable|string|max:100',
            'document_number' => 'required|string|max:50',
            'document_country' => 'required|string|size:3',
            'birth_date' => 'required|date|before_or_equal:-18 years',
            'user_id' => 'required|exists:users,id',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:1000',
            'physical_fitness' => 'required|in:Excelente,En entrenamiento,No apto',
            'iris_passport_number' => 'nullable|string|max:100',
            'iris_passport_expiration' => 'nullable|date',
            'training_certificate_date' => 'nullable|date',
            'training_certificate_status' => 'nullable|in:Apto,No Apto',
        ];

        return $rules;
    }

    protected function messages()
    {
        return [
            'birth_date.before_or_equal' => 'El pasajero debe ser mayor de 18 años por motivos de seguridad.',
        ];
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->primarylastname = '';
        $this->secondarylastname = '';
        $this->document_number = '';
        $this->document_country = 'ESP';
        $this->birth_date = '';
        $this->blood_type = '';
        $this->allergies = '';
        $this->physical_fitness = 'No apto';
        $this->iris_passport_number = '';
        $this->iris_passport_expiration = '';
        $this->training_certificate_date = '';
        $this->training_certificate_status = '';

        // Mantener el vínculo al cliente si estamos filtrando
        if ($this->filterUserId) {
            $this->user_id = $this->filterUserId;
            $this->selectedClientName = $this->filterUserName;
        } else {
            $this->user_id = null;
            $this->selectedClientName = null;
            $this->clientSearch = '';
            $this->clientSearchResults = [];
        }

        $this->selectedCountry = null;

        $this->isEditing = false;
        $this->passengerId = null;
        $this->activeTab = 'identity';
        $this->resetValidation();
        $this->showSaveModal = false;
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deleteImpactInfo = [];
    }

    public function updatedClientSearch()
    {
        if (strlen($this->clientSearch) > 1) {
            $this->clientSearchResults = User::where('role', 'cliente')
                ->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->clientSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->clientSearch . '%');
                })
                ->limit(8)
                ->get()
                ->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])
                ->toArray();
        } else {
            $this->clientSearchResults = [];
        }
    }

    public function selectClient($id, $name)
    {
        $this->user_id = $id;
        $this->selectedClientName = $name;
        $this->clientSearch = '';
        $this->clientSearchResults = [];
    }

    public function clearSelectedClient()
    {
        if (!$this->filterUserId) {
            $this->user_id = null;
            $this->selectedClientName = null;
        }
    }

    public function updatedSelectedCountry($value)
    {
        if ($value) {
            $this->document_country = $value;
        }
    }

    public function toggleSort()
    {
        $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
    }

    public function setCreateMode()
    {
        $this->resetInputFields();
    }

    public function render()
    {
        $query = Passenger::with(['client', 'reservations']);

        if ($this->filterUserId) {
            $query->where('user_id', $this->filterUserId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('primarylastname', 'like', '%' . $this->search . '%')
                    ->orWhere('document_number', 'like', '%' . $this->search . '%');
            });
        }

        $passengers = $query->orderBy('name', $this->sortDir)->get();
        $uniqueCountries = Location::whereNotNull('country_code')
            ->select('country_code')
            ->distinct()
            ->orderBy('country_code', 'asc')
            ->get();

        return view('livewire.admin.manage-passengers', [
            'passengers' => $passengers,
            'uniqueCountries' => $uniqueCountries,
        ])->layout('layouts.app');
    }

    public function edit($id)
    {
        $this->resetInputFields();
        $this->isEditing = true;

        $passenger = Passenger::find($id);
        if ($passenger) {
            $this->passengerId = $id;
            $this->name = $passenger->name;
            $this->primarylastname = $passenger->primarylastname;
            $this->secondarylastname = $passenger->secondarylastname;
            $this->document_number = $passenger->document_number;
            $this->document_country = $passenger->document_country;
            $this->birth_date = $passenger->birth_date?->format('Y-m-d');
            $this->blood_type = $passenger->blood_type;
            $this->allergies = $passenger->allergies;
            $this->physical_fitness = $passenger->physical_fitness ?? 'No apto';
            $this->iris_passport_number = $passenger->iris_passport_number;
            $this->iris_passport_expiration = $passenger->iris_passport_expiration?->format('Y-m-d');
            $this->training_certificate_date = $passenger->training_certificate_date?->format('Y-m-d');
            $this->training_certificate_status = $passenger->training_certificate_status;
            $this->user_id = $passenger->user_id;
            $this->selectedClientName = $passenger->client?->name;
        }
    }

    public function confirmSave()
    {
        $this->validate();
        $this->showSaveModal = true;
    }

    public function executeSave()
    {
        $this->showSaveModal = false;

        $data = [
            'user_id' => $this->user_id,
            'document_number' => $this->document_number,
            'document_country' => strtoupper($this->document_country),
            'name' => $this->name,
            'primarylastname' => $this->primarylastname,
            'secondarylastname' => $this->secondarylastname,
            'birth_date' => $this->birth_date,
            'blood_type' => $this->blood_type ?: null,
            'allergies' => $this->allergies ?: null,
            'physical_fitness' => $this->physical_fitness,
            'iris_passport_number' => $this->iris_passport_number ?: null,
            'iris_passport_expiration' => $this->iris_passport_expiration ?: null,
            'training_certificate_date' => $this->training_certificate_date ?: null,
            'training_certificate_status' => $this->training_certificate_status ?: null,
        ];

        if ($this->isEditing && $this->passengerId) {
            $passenger = Passenger::find($this->passengerId);
            if ($passenger) {
                $passenger->update($data);
                session()->flash('message', 'Pasajero actualizado correctamente.');
            }
        } else {
            Passenger::create($data);
            session()->flash('message', 'Pasajero registrado con éxito.');
        }

        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $passenger = Passenger::withCount('reservations')->find($id);

        if (!$passenger)
            return;

        $activeReservations = $passenger->reservations()
            ->where('status', '!=', 'Cancelada')
            ->count();

        $this->deleteImpactInfo = [
            'name' => $passenger->full_name,
            'reservation_count' => $passenger->reservations_count,
            'active_reservations' => $activeReservations,
        ];

        $this->showDeleteModal = true;
    }

    public function executeDelete()
    {
        if (!$this->deleteId)
            return;

        $passenger = Passenger::find($this->deleteId);
        if (!$passenger) {
            $this->resetInputFields();
            return;
        }

        // Cancelar reservas activas del pasajero
        $passenger->reservations()
            ->where('status', '!=', 'Cancelada')
            ->update(['status' => 'Cancelada', 'seat_number' => null]);

        // Soft Delete del pasajero
        $passenger->delete();

        session()->flash('message', 'Pasajero eliminado. Reservas activas canceladas.');
        $this->resetInputFields();
    }
}
