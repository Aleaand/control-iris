<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\PriceLog;
use Carbon\Carbon;
use Livewire\WithPagination;

class ManageHotels extends Component
{
    use WithPagination;

    // Propiedades del Modelo
    public $hotelId, $name, $location_id, $galactic_stars = 5, $price_per_night, $total_rooms;

    // Estados de UI
    public $isEditing = false;
    public $search = '';
    public $sortDir = 'asc';

    // Modales y Control de Daños
    public $showSaveModal = false;
    public $showDeleteModal = false;
    public $showConflictDeleteModal = false;
    public $deleteId = null;
    public $reservationsCount = 0;

    #[\Livewire\Attributes\Url]
    public $searchUrl = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'galactic_stars' => 'required|integer|min:1|max:5',
            'price_per_night' => 'required|numeric|min:0.1',
            'total_rooms' => 'required|integer|min:1',
        ];
    }

    protected $messages = [
        'price_per_night.min' => 'La tarifa estelar debe ser superior a 0.',
        'total_rooms.min' => 'El Manor debe tener al menos una unidad habitacional.',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->location_id = '';
        $this->galactic_stars = 5;
        $this->price_per_night = '';
        $this->total_rooms = '';
        $this->isEditing = false;
        $this->hotelId = null;
        $this->resetValidation();
        $this->showSaveModal = false;
        $this->showDeleteModal = false;
        $this->showConflictDeleteModal = false;
        $this->deleteId = null;
        $this->reservationsCount = 0;
    }

    public function render()
    {
        $query = Hotel::query()->with('location');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('location', function ($sub) {
                        $sub->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('code', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return view('livewire.admin.manage-hotels', [
            'hotels' => $query->orderBy('name', $this->sortDir)->paginate(10),
            'locations' => Location::orderBy('name', 'asc')->get()
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
        $hotel = Hotel::find($id);
        if ($hotel) {
            $this->hotelId = $id;
            $this->name = $hotel->name;
            $this->location_id = $hotel->location_id;
            $this->galactic_stars = $hotel->galactic_stars;
            $this->price_per_night = $hotel->price_per_night;
            $this->total_rooms = $hotel->total_rooms;
        }
        $this->resetValidation();
    }

    public function confirmSave()
    {
        $this->validate();
        $this->showSaveModal = true;
    }

    public function executeSave()
    {
        $this->validate();

        if ($this->isEditing && $this->hotelId) {
            $hotel = Hotel::find($this->hotelId);
            if ($hotel) {

                $ocupacionActual = $hotel->logistics()
                    ->whereHas('reservation', function ($q) {
                        $q->where('status', '!=', 'cancelled')
                            ->whereHas('flight', function ($f) {
                                $f->where('arrival_date', '>=', now());
                            });
                    })->count();

                if ($this->total_rooms < $ocupacionActual) {
                    session()->flash('warning', "La nueva capacidad ({$this->total_rooms}) es inferior a las reservas activas ({$ocupacionActual}). Los gestores han sido notificados.");
                }
                $oldPrice = (float) $hotel->price_per_night;
                $newPrice = (float) $this->price_per_night;

                if ($newPrice !== $oldPrice) {
                    PriceLog::record(
                        itemType: 'hotel',
                        itemId: $hotel->id,
                        oldPrice: $oldPrice,
                        newPrice: $newPrice,
                        reason: 'Actualización de tarifas',
                    );

                    $hotel->previous_price_per_night = $oldPrice;
                    $hotel->price_updated_at = now();
                    $hotel->price_updated_by = auth()->id();
                }

                $hotel->update([
                    'name' => $this->name,
                    'location_id' => $this->location_id,
                    'galactic_stars' => $this->galactic_stars,
                    'price_per_night' => $this->price_per_night,
                    'total_rooms' => $this->total_rooms,
                    'previous_price_per_night' => $hotel->previous_price_per_night,
                    'price_updated_at' => $hotel->price_updated_at,
                    'price_updated_by' => $hotel->price_updated_by,
                ]);

                session()->flash('message', 'Hotel actualizado con éxito.');
            }
        } else {
            Hotel::create([
                'name' => $this->name,
                'location_id' => $this->location_id,
                'galactic_stars' => $this->galactic_stars,
                'price_per_night' => $this->price_per_night,
                'total_rooms' => $this->total_rooms,
            ]);
            session()->flash('message', 'Nuevo hotel registrado con éxito.');
        }

        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $hotel = Hotel::find($id);

        if ($hotel) {
            $this->reservationsCount = $hotel->logistics()
                ->whereHas('reservation', function ($q) {
                    $q->where('status', '!=', 'cancelled')
                        ->whereHas('flight', function ($f) {
                            $f->where('arrival_date', '>=', now());
                        });
                })->count();

            if ($this->reservationsCount > 0) {
                $this->showConflictDeleteModal = true;
            } else {
                $this->showDeleteModal = true;
            }
        }
    }

    public function executeDelete()
    {
        if ($this->deleteId) {
            $hotel = Hotel::find($this->deleteId);
            if ($hotel) {
                $hotel->forceDelete();
                session()->flash('message', 'Hotel eliminado con éxito.');
            }
        }
        $this->resetInputFields();
    }

    public function deleteAndNotify()
    {
        $hotel = Hotel::find($this->deleteId);
        if ($hotel) {
            $hotel->logistics()->whereHas('reservation', function ($q) {
                $q->where('check_out', '>=', now());
            })->get()->each(function ($logistica) {
                $logistica->reservation()->update(['status' => 'cancelled']);
            });

            $hotel->delete();
            session()->flash('warning', "Protocolo de evacuación: Hotel cerrado y {$this->reservationsCount} reservas canceladas.");
        }
        $this->resetInputFields();
    }
}
