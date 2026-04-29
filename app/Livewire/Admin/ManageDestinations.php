<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Destination;

class ManageDestinations extends Component
{
    public $name, $description, $distance_au, $max_distance_au, $launch_fee, $landing_fee;
    public $isEditing = false, $destinationId;
    public $search = '';
    public $sortDir = 'asc';
    public $showSaveModal = false;
    public $showDeleteModal = false;
    public $deleteId = null;
    public $showCascadeDeleteModal = false;
    public $flightsCount = 0;
    protected $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.max' => 'El nombre no puede exceder los 255 caracteres.',
        'description.required' => 'La descripción es obligatoria.',
        'description.min' => 'La descripción debe tener al menos 5 caracteres.',
        'distance_au.required' => 'La distancia minima es obligatoria.',
        'distance_au.numeric' => 'La distancia debe ser un valor numérico válido.',
        'distance_au.min' => 'La distancia debe ser superior a 0 AU.',
    ];
    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->distance_au = '';
        $this->max_distance_au = '';
        $this->launch_fee = 0;
        $this->landing_fee = 0;
        $this->isEditing = false;
        $this->destinationId = null;
        $this->resetValidation();
        $this->showSaveModal = false;
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $query = Destination::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('id', 'like', $this->search . '%');
        }

        $destinations = $query->orderBy('name', $this->sortDir)->get();

        return view('livewire.admin.manage-destinations', [
            'destinations' => $destinations
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
        $dest = Destination::find($id);
        if ($dest) {
            $this->destinationId = $id;
            $this->name = $dest->name;
            $this->description = $dest->description;
            $this->distance_au = $dest->distance_au;
            $this->max_distance_au = $dest->max_distance_au;
            $this->launch_fee = $dest->launch_fee;
            $this->landing_fee = $dest->landing_fee;
        }
        $this->resetValidation();
        $this->showSaveModal = false;
        $this->showDeleteModal = false;
    }

    public function confirmSave()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:5',
            'distance_au' => 'required|numeric|min:0.01',
        ]);

        $this->showSaveModal = true;
    }

    public function executeSave()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:5',
            'distance_au' => 'required|numeric|min:0.01',
            'max_distance_au' => 'nullable|numeric|min:0.01',
            'launch_fee' => 'required|numeric|min:0',
            'landing_fee' => 'required|numeric|min:0',
        ]);
        if ($this->isEditing && $this->destinationId) {
            $dest = Destination::find($this->destinationId);
            if ($dest) {
                $dest->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'distance_au' => $this->distance_au,
                    'max_distance_au' => $this->max_distance_au ?: null,
                    'launch_fee' => $this->launch_fee,
                    'landing_fee' => $this->landing_fee,
                ]);
                session()->flash('message', 'Destino actualizado con exito.');
            }
        } else {
            Destination::create([
                'name' => $this->name,
                'description' => $this->description,
                'distance_au' => $this->distance_au,
                'max_distance_au' => $this->max_distance_au ?: null,
                'launch_fee' => $this->launch_fee,
                'landing_fee' => $this->landing_fee,
            ]);
            session()->flash('message', 'Nuevo destino registrado con éxito.');
        }

        $this->showSaveModal = false;
        $this->resetInputFields();
    }
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $dest = Destination::find($id);

        if ($dest) {
            $this->flightsCount = $dest->flights()->count();
            if ($this->flightsCount > 0) {
                $this->showCascadeDeleteModal = true;
                $this->showDeleteModal = false;
            } else {
                $this->showDeleteModal = true;
                $this->showCascadeDeleteModal = false;
            }
        }
    }

    public function executeDelete()
    {
        if ($this->deleteId) {
            $dest = Destination::find($this->deleteId);

            if ($dest) {
                if ($this->flightsCount > 0) {
                    $flightIds = $dest->flights()->pluck('id');
                    \App\Models\Reservation::whereIn('space_flight_id', $flightIds)->delete();
                    $dest->flights()->delete();
                    $dest->delete();

                    session()->flash('message', 'Eliminado con exito en cascada: Planeta, rutas y reservas.');
                } else {
                    $dest->forceDelete();

                    session()->flash('message', 'Destino eliminado con exito.');
                }
            }
        }

        $this->showDeleteModal = false;
        $this->showCascadeDeleteModal = false;

        $this->resetInputFields();
    }

}
