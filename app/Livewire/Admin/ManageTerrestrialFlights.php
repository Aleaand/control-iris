<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\HasResponsivePagination;
use App\Models\TerrestrialFlight;
use App\Models\Location;
use App\Models\PriceLog;

class ManageTerrestrialFlights extends Component
{
    use WithPagination, HasResponsivePagination;
    public $origin_id, $destination_id, $departure_datetime, $arrival_datetime, $price;
    public $baggage_price;
    public $executive_capacity = 20;
    public $status = 'Programado';
    public $isEditing = false, $flightId;
    public $search = '';
    public $searchDate = '';
    public $searchOriginId = '';
    public $searchDestinationId = '';
    public $flightDurationHours = 0;
    public $airline = '';
    public $sortDir = 'asc';

    public $showSaveModal = false;
    public $showDeleteModal = false;
    public $deleteId = null;
    public $showConflictDeleteModal = false;
    public $reservationsCount = 0;

    protected function rules()
    {
        return [
            'airline' => 'required|string|max:255',
            'origin_id' => 'required|exists:locations,id',
            'destination_id' => 'required|exists:locations,id|different:origin_id',
            'departure_datetime' => 'required|date',
            'arrival_datetime' => 'nullable|date|after:departure_datetime',
            'price' => 'required|numeric|min:0.01',
            'baggage_price' => 'required|numeric|min:0.01',
            'executive_capacity' => 'required|integer|min:1',
            'status' => 'nullable|string|max:50',
        ];
    }

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->airline = '';
        $this->origin_id = '';
        $this->destination_id = '';
        $this->departure_datetime = '';
        $this->arrival_datetime = '';
        $this->price = '';
        $this->baggage_price = '';
        $this->executive_capacity = 20;
        $this->status = 'Programado';
        $this->isEditing = false;
        $this->flightId = null;
        $this->resetValidation();
        $this->showSaveModal = false;
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $query = TerrestrialFlight::query()->with(['originLocation', 'destinationLocation']);

        if ($this->search) {
            $searchTerm = '%' . strtolower($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(airline) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(flight_number) LIKE ?', [$searchTerm])
                    ->orWhereRaw("LPAD(id::text, 4, '0') LIKE ?", ["%{$this->search}%"])
                    ->orWhereRaw('id::text LIKE ?', ["%{$this->search}%"])
                    ->orWhereHas('originLocation', function ($sub) use ($searchTerm) {
                        $sub->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('LOWER(code) LIKE ?', [$searchTerm]);
                    })
                    ->orWhereHas('destinationLocation', function ($sub) use ($searchTerm) {
                        $sub->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('LOWER(code) LIKE ?', [$searchTerm]);
                    });
            });
        }

        if ($this->searchDate) {
            $query->whereDate('departure_datetime', $this->searchDate);
        }

        if ($this->searchOriginId) {
            $query->where('origin_id', $this->searchOriginId);
        }

        if ($this->searchDestinationId) {
            $query->where('destination_id', $this->searchDestinationId);
        }

        $flights = $query->orderBy('departure_datetime', $this->sortDir)->paginate($this->getPerPage());
        $locations = Location::orderBy('name', 'asc')->get();

        return view('livewire.admin.manage-terrestrial-flights', [
            'flights' => $flights,
            'terrestrialFlights' => $flights,
            'locations' => $locations
        ])->layout('layouts.app');
    }

    public function toggleSort()
    {
        $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
    }

    public function swapSearchLocations()
    {
        $temp = $this->searchOriginId;
        $this->searchOriginId = $this->searchDestinationId;
        $this->searchDestinationId = $temp;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->searchDate = '';
        $this->searchOriginId = '';
        $this->searchDestinationId = '';
    }

    public function setCreateMode()
    {
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $this->isEditing = true;
        $flight = TerrestrialFlight::find($id);
        if ($flight) {
            $this->flightId = $id;
            $this->airline = $flight->airline;
            $this->origin_id = $flight->origin_id;
            $this->destination_id = $flight->destination_id;
            $this->departure_datetime = $flight->departure_datetime->format('Y-m-d\TH:i');
            $this->arrival_datetime = $flight->arrival_datetime ? $flight->arrival_datetime->format('Y-m-d\TH:i') : '';
            $this->price = $flight->price;
            $this->baggage_price = $flight->baggage_price;
            $this->executive_capacity = $flight->executive_capacity;
            $this->status = $flight->status;
        }
        $this->resetValidation();
        $this->showSaveModal = false;
        $this->showDeleteModal = false;
    }

    public function confirmSave()
    {
        $this->validate();

        if ($this->arrival_datetime && $this->departure_datetime) {
            $start = \Carbon\Carbon::parse($this->departure_datetime);
            $end = \Carbon\Carbon::parse($this->arrival_datetime);
            $this->flightDurationHours = $start->diffInHours($end);
        } else {
            $this->flightDurationHours = 0;
        }

        $this->showSaveModal = true;
    }

    public function executeSave()
    {
        $this->validate();

        if ($this->isEditing && $this->flightId) {
            $flight = TerrestrialFlight::find($this->flightId);
            if ($flight) {
                // Audit price change
                if ((float) $this->price !== (float) $flight->price) {
                    PriceLog::record(
                        itemType: 'terrestrial_flight',
                        itemId: $flight->id,
                        oldPrice: (float) $flight->price,
                        newPrice: (float) $this->price,
                        reason: 'Actualización manual desde Gestión Terrestre',
                    );
                }
                $flight->update([
                    'airline' => $this->airline,
                    'origin_id' => $this->origin_id,
                    'destination_id' => $this->destination_id,
                    'departure_datetime' => $this->departure_datetime,
                    'arrival_datetime' => $this->arrival_datetime ?: null,
                    'price' => $this->price,
                    'baggage_price' => $this->baggage_price,
                    'executive_capacity' => $this->executive_capacity,
                    'status' => $this->status,
                ]);
                session()->flash('message', 'Vuelo Terrestre actualizado correctamente.');
            }
        } else {
            // Generate a realistic flight number based on airline
            $airlineCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $this->airline) . 'XX', 0, 2));
            $flightNumber = $airlineCode . '-' . mt_rand(1000, 9999);

            TerrestrialFlight::create([
                'flight_number' => $flightNumber,
                'airline' => $this->airline,
                'origin_id' => $this->origin_id,
                'destination_id' => $this->destination_id,
                'departure_datetime' => $this->departure_datetime,
                'arrival_datetime' => $this->arrival_datetime ?: null,
                'price' => $this->price,
                'baggage_price' => $this->baggage_price,
                'executive_capacity' => $this->executive_capacity,
                'status' => $this->status ?? 'Programado',
            ]);
            session()->flash('message', 'Nuevo Vuelo Terrestre registrado con éxito.');
        }

        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $flight = TerrestrialFlight::withCount('reservationLogistics')->find($id);

        if ($flight) {
            $this->reservationsCount = $flight->reservation_logistics_count;

            if ($this->reservationsCount > 0) {
                $this->showConflictDeleteModal = true;
            } else {
                $this->showDeleteModal = true;
            }
        }
    }

    public function cancelTerrestrialFlightAndNotify()
    {
        if ($this->deleteId) {
            $flight = TerrestrialFlight::find($this->deleteId);

            if ($flight) {
                $flight->reservationLogistics()->each(function ($logistica) {
                    if ($logistica->reservation) {
                        $logistica->reservation->update(['status' => 'cancelled']);
                    }
                });

                $flight->update(['status' => 'Cancelado']);
                $flight->delete();

                session()->flash('message', "Protocolo de cancelación ejecutado. {$this->reservationsCount} traslados cancelados y gestores notificados.");
            }
        }

        $this->resetInputFields();
        $this->showConflictDeleteModal = false;
    }

    public function executeDelete()
    {
        if ($this->deleteId) {
            $flight = TerrestrialFlight::find($this->deleteId);

            if ($flight) {
                $flight->forceDelete();
                session()->flash('message', 'Registro de vuelo terrestre purgado con éxito.');
            }
        }

        $this->resetInputFields();
        $this->showDeleteModal = false;
    }

    public function redirectToEdit()
    {
        $this->edit($this->deleteId);
        $this->showConflictDeleteModal = false;
    }
}
