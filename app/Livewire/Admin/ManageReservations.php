<?php

namespace App\Livewire\Admin;

use App\Traits\HasResponsivePagination;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Reservation;
use App\Models\ReservationLogistic;
use App\Models\User;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\PriceLog;
use App\Models\Task;
use App\Models\TerrestrialFlight;
use App\Models\Location;
use App\Models\Passenger;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class ManageReservations extends Component
{
    use WithPagination, HasResponsivePagination;

    protected $paginationTheme = 'simple-tailwind';

    // ── Reserva Base ──────────────────────────────────────
    public $user_id;
    public $passenger_id;
    public $space_flight_id;
    public $seat_type = 'Nova';
    public $seat_number = '';
    public $total_price = 0;
    public $discount_applied = false;
    public $status = 'Pendiente';

    // ── Búsqueda de Pasajero ──────────────────────────────
    public $passengerSearch = '';
    public $passengerSearchResults = [];
    public $selectedPassengerName = '';
    public $clientPassengers = [];

    // ── Búsqueda de Cliente ───────────────────────────────
    public $clientSearch = '';
    public $clientSearchResults = [];
    public $selectedClientName = '';

    // ── Búsqueda de Vuelo Espacial ───────────────────────
    public $flightSearch = '';
    public $flightSearchResults = [];
    public $selectedFlightLabel = '';

    // ── Búsqueda de Hotel ────────────────────────────────
    public $hotelSearch = '';
    public $hotelSearchResults = [];
    public $selectedHotelLabel = '';

    // ── Búsqueda de Vuelo Terrestre ──────────────────────
    public $terrestrialSearch = '';
    public $terrestrialSearchResults = [];
    public $selectedTerrestrialLabel = '';

    // ── Logística ─────────────────────────────────────────
    public $terrestrial_flight_id = null;
    public $hotel_id = null;
    public $hotel_nights = 0;
    public $training_included = false;
    public $vip_transfer_included = false;
    public $refund_insurance_included = false;
    public $passport_management_included = false;

    // ── Ajuste Manual / Cortesía ──────────────────────────
    public $manual_adjustment_type = 'none';  // none | pct | fixed
    public $manual_adjustment_value = 0;
    public $discount_note = '';

    // ── Desglose en Tiempo Real ───────────────────────────
    public array $priceBreakdown = [];
    public bool $priceOverrideEnabled = false;
    public array $readinessCheck = [];

    // Price Protection Properties
    public ?array $originalSnapshot = null;
    public ?int $originalSpaceFlightId = null;
    public ?int $originalHotelId = null;
    public ?int $originalHotelNights = null;
    public ?int $originalTerrestrialFlightId = null;
    public ?bool $originalTraining = null;
    public ?bool $originalVip = null;
    public ?bool $originalPassport = null;
    public ?bool $originalInsurance = null;
    public ?string $originalSeatType = null;

    // ── Control UI ────────────────────────────────────────
    public $isEditing = false;
    public $reservationId = null;
    public $locatorId = null;
    public $search = '';
    public $sortDir = 'desc';
    public $activeTab = 'space';
    public $showSaveModal = false;
    public $showDeleteModal = false;
    public $showReceiptsModal = false;
    public $paid_amount = 0;
    public $receiptsList = [];
    public $deleteId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ── Flujo Maestro-Detalle de Edición/Eliminación de Grupo ──
    public bool $showGroupEditModal = false;
    public array $groupEditMembers = [];    // [{id, passenger_name, flight_code, seat_type, total_price, payment_status}]
    public ?string $currentGroupId = null;  // booking_group_id del grupo en foco

    // ── Modo Grupo ────────────────────────────────────────
    public bool $groupMode = true;
    public string $bookingGroupId = '';
    public string $expedition_title = '';
    public bool $isAdendaMode = false;
    public ?int $adendaParentId = null;
    // Cada ítem: ['passenger_id', 'name', 'seat_type', 'seat_number',
    //   'training_included', 'passport_management_included',
    //   'refund_insurance_included', 'vip_transfer_included',
    //   'hotel_id', 'hotel_nights', 'shared_hotel_key',
    //   'is_hotel_payer', 'total_price', 'priceBreakdown']
    public array $selectedPassengers = [];
    public float $groupTotal = 0.0;
    // Alerta de hotel huérfano pendiente de reasignación
    public ?string $hotelAlertMessage = null;

    // ── Motor de Coherencia Temporal ──────────────────────
    public array $temporalWarnings = []; // [index => ['msg']] o [0 => ['msg']] para indiv
    public array $smartSuggestions = []; // [index => nights] o [0 => nights] para indiv
    // ── Vuelo de Vuelta (Round Trip) ─────────────────────
    public bool $hasReturnFlight = false;
    public $return_flight_id = null;
    public $selectedReturnFlightLabel = '';
    public $returnFlightSearch = '';
    public $returnFlightSearchResults = [];

    // Logística de Vuelta (Indiv + Global Default para Grupo)
    public $return_terrestrial_flight_id = null;
    public $return_hotel_id = null;
    public $return_hotel_nights = 0;
    public $return_vip_transfer_included = false;

    public array $returnPriceBreakdown = [];
    public float $return_total_price = 0.0;

    protected function rules()
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'space_flight_id' => [
                'nullable',
                'exists:flights,id',
                function ($attribute, $value, $fail) {
                    if (!$value)
                        return;
                    // Solo validar capacidad aquí si NO estamos en modo grupo.
                    // En modo grupo, executeSave() tiene su propia validación iterativa.
                    if ($this->groupMode)
                        return;

                    $flight = Flight::with('starship')->find($value);
                    if (!$flight || !$flight->starship)
                        return;

                    $capacity = ($this->seat_type === 'supernova')
                        ? $flight->starship->vip_capacity
                        : $flight->starship->general_capacity;

                    $occupiedCount = Reservation::where('space_flight_id', $value)
                        ->where('seat_type', $this->seat_type)
                        ->when($this->isEditing, fn($q) => $q->where('id', '!=', $this->reservationId))
                        ->count();

                    if ($occupiedCount >= $capacity) {
                        $fail("No hay cupo disponible en clase " . strtoupper($this->seat_type) . " para este vuelo espacial. (Capacidad: {$capacity}).");
                    }
                }
            ],
            'status' => 'required|string',
            'total_price' => 'nullable|numeric',
            'discount_applied' => 'boolean',
            'manual_adjustment_type' => 'in:none,pct,fixed',
            'manual_adjustment_value' => 'nullable|numeric|min:0',
            'discount_note' => 'nullable|string|max:255',
            'selectedPassengers' => $this->groupMode && !$this->isEditing ? 'required|array|min:1' : 'nullable',
        ];

        if (!$this->groupMode) {
            $rules['passenger_id'] = 'required|exists:passengers,id';
            $rules['seat_type'] = 'nullable|string';
            $rules['seat_number'] = [
                'nullable',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (!$this->space_flight_id || !$value)
                        return;

                    $flight = Flight::with('starship')->find($this->space_flight_id);
                    if (!$flight || !$flight->starship)
                        return;

                    $capacity = ($this->seat_type === 'supernova')
                        ? $flight->starship->vip_capacity
                        : $flight->starship->general_capacity;

                    if ($value > $capacity) {
                        $fail("El asiento {$value} excede la capacidad de la clase " . strtoupper($this->seat_type) . " ({$capacity} max).");
                    }

                    // Check if occupied (ONLY active reservations)
                    $exists = Reservation::where('space_flight_id', $this->space_flight_id)
                        ->where('seat_type', $this->seat_type)
                        ->where('seat_number', $value)
                        ->where('status', '!=', 'Cancelada')
                        ->when($this->isEditing, fn($q) => $q->where('id', '!=', $this->reservationId))
                        ->exists();

                    if ($exists) {
                        $fail("El asiento {$value} ya está ocupado en este vuelo.");
                    }
                }
            ];
            $rules['terrestrial_flight_id'] = [
                'nullable',
                'exists:terrestrial_flights,id',
                function ($attribute, $value, $fail) {
                    if (!$value)
                        return;
                    $tf = TerrestrialFlight::find($value);
                    if (!$tf)
                        return;

                    $totalCapacity = $tf->tourist_capacity + $tf->executive_capacity;
                    $occupiedCount = ReservationLogistic::where('terrestrial_flight_id', $value)
                        ->whereHas('reservation', fn($r) => $r->where('status', '!=', 'Cancelada'))
                        ->when($this->isEditing, fn($q) => $q->whereHas('reservation', fn($r) => $r->where('id', '!=', $this->reservationId)))
                        ->count();

                    if ($occupiedCount >= $totalCapacity) {
                        $fail("El vuelo terrestre seleccionado está completo. Por favor, escoja otro.");
                    }
                }
            ];
            $rules['hotel_id'] = [
                'nullable',
                'exists:hotels,id',
                function ($attribute, $value, $fail) {
                    if (!$value)
                        return;
                    $hotel = Hotel::find($value);
                    if (!$hotel)
                        return;

                    $occupiedCount = ReservationLogistic::where('hotel_id', $value)
                        ->whereHas('reservation', fn($r) => $r->where('status', '!=', 'Cancelada'))
                        ->when($this->isEditing, fn($q) => $q->whereHas('reservation', fn($r) => $r->where('id', '!=', $this->reservationId)))
                        ->count();

                    if ($occupiedCount >= $hotel->total_rooms) {
                        $fail("El hotel no tiene habitaciones disponibles para estas fechas. Escoja otro hotel.");
                    }
                }
            ];
            $rules['hotel_nights'] = 'nullable|integer|min:0|max:30';
            $rules['training_included'] = 'boolean';
            $rules['vip_transfer_included'] = 'boolean';
            $rules['refund_insurance_included'] = 'boolean';
            $rules['passport_management_included'] = 'boolean';
        }

        return $rules;
    }

    public function resetInputFields()
    {
        $this->user_id = null;
        $this->passenger_id = null;
        $this->space_flight_id = null;
        $this->seat_type = 'nova';
        $this->seat_number = '';
        $this->total_price = 0;
        $this->discount_applied = false;
        $this->status = 'Pendiente';

        // Client search
        $this->clientSearch = '';
        $this->clientSearchResults = [];
        $this->selectedClientName = '';

        // Passenger selection
        $this->passengerSearch = '';
        $this->passengerSearchResults = [];
        $this->selectedPassengerName = '';
        $this->clientPassengers = [];

        // Flight live search
        $this->flightSearch = '';
        $this->flightSearchResults = [];
        $this->selectedFlightLabel = '';

        // Hotel live search
        $this->hotelSearch = '';
        $this->hotelSearchResults = [];
        $this->selectedHotelLabel = '';

        // Terrestrial live search
        $this->terrestrialSearch = '';
        $this->terrestrialSearchResults = [];
        $this->selectedTerrestrialLabel = '';

        // Logistics
        $this->terrestrial_flight_id = null;
        $this->hotel_id = null;
        $this->hotel_nights = 0;
        $this->training_included = false;
        $this->vip_transfer_included = false;
        $this->refund_insurance_included = false;
        $this->passport_management_included = false;

        // Manual adjustment
        $this->manual_adjustment_type = 'none';
        $this->manual_adjustment_value = 0;
        $this->discount_note = '';

        $this->isEditing = false;
        $this->reservationId = null;
        $this->locatorId = null;
        $this->activeTab = 'space';
        $this->priceBreakdown = [];
        $this->priceOverrideEnabled = false;

        // Reset Protection
        $this->originalSnapshot = null;
        $this->originalSpaceFlightId = null;
        $this->originalHotelId = null;
        $this->originalHotelNights = null;
        $this->originalTerrestrialFlightId = null;
        $this->originalTraining = null;
        $this->originalVip = null;
        $this->originalPassport = null;
        $this->originalInsurance = null;
        $this->originalSeatType = null;

        // Reset Grupo
        $this->groupMode = true;
        $this->bookingGroupId = '';
        $this->expedition_title = '';
        $this->selectedPassengers = [];
        $this->groupTotal = 0.0;
        $this->isAdendaMode = false;
        $this->adendaParentId = null;
        $this->hotelAlertMessage = null;

        $this->resetValidation();
        $this->showSaveModal = false;
        $this->showDeleteModal = false;
        $this->deleteId = null;

        // Reset Vuelta
        $this->hasReturnFlight = false;
        $this->return_flight_id = null;
        $this->selectedReturnFlightLabel = '';
        $this->returnFlightSearch = '';
        $this->returnFlightSearchResults = [];
        $this->return_terrestrial_flight_id = null;
        $this->return_hotel_id = null;
        $this->return_hotel_nights = 0;
        $this->return_vip_transfer_included = false;
        $this->returnPriceBreakdown = [];
        $this->return_total_price = 0.0;
    }

    public function mount()
    {
        $this->resetInputFields();
    }

    public function updatedClientSearch()
    {
        if (strlen($this->clientSearch) > 1) {
            $this->clientSearchResults = User::query()
                ->where('role', 'cliente')
                ->where(function ($q) {
                    $q->where('email', 'LIKE', '%' . $this->clientSearch . '%')
                        ->orWhere('name', 'LIKE', '%' . $this->clientSearch . '%');

                    if (is_numeric($this->clientSearch)) {
                        $q->orWhere('id', (int) $this->clientSearch);
                    }
                })
                ->take(5)
                ->get()
                ->toArray();
        } else {
            $this->clientSearchResults = [];
        }
    }

    public function updated($property)
    {
        $triggerProperties = [
            'space_flight_id',
            'seat_type',
            'terrestrial_flight_id',
            'hotel_id',
            'hotel_nights',
            'training_included',
            'vip_transfer_included',
            'refund_insurance_included',
            'passport_management_included',
            'manual_adjustment_value',
            // Return properties
            'hasReturnFlight',
            'return_flight_id',
            'return_terrestrial_flight_id',
            'return_hotel_id',
            'return_hotel_nights',
            'return_vip_transfer_included',
        ];

        if (in_array($property, $triggerProperties)) {
            if ($property === 'hasReturnFlight' && $this->hasReturnFlight) {
                $this->suggestReturnFlight();
            }
            $this->applyTemporalCoherence();

            if ($this->groupMode) {
                foreach ($this->selectedPassengers as $index => $p) {
                    $this->recalculatePassengerPrice($index);
                }
                $this->calculateGroupTotal();
            } else {
                $this->calculateTotalPrice();
            }
        }
    }

    // ── Búsqueda de Vuelo Espacial (Ida) ──────────────────────────────────
    public function updatedFlightSearch()
    {
        if (strlen($this->flightSearch) > 1) {
            $this->flightSearchResults = Flight::with('destination')
                ->where(function ($q) {
                    $q->where('flight_code', 'LIKE', '%' . $this->flightSearch . '%')
                        ->orWhereHas('destination', fn($d) => $d->where('name', 'LIKE', '%' . $this->flightSearch . '%'));
                })
                ->where('departure_date', '>', now())
                ->take(5)
                ->get()
                ->map(fn($f) => [
                    'id' => $f->id,
                    'label' => '#' . $f->flight_code . ' → ' . ($f->destination?->name ?? '?') . ' (' . $f->departure_date?->format('d/m/Y') . ')',
                    'price' => $f->base_price,
                ])
                ->toArray();
        } else {
            $this->flightSearchResults = [];
        }
    }

    public function selectFlight($id, $label)
    {
        $this->space_flight_id = $id;
        $this->selectedFlightLabel = $label;
        $this->flightSearch = '';
        $this->flightSearchResults = [];

        if ($this->hasReturnFlight) {
            $this->suggestReturnFlight();
        }

        $this->calculateTotalPrice();
    }

    /**
     * Sugiere automáticamente el próximo vuelo de vuelta disponible.
     */
    public function suggestReturnFlight(): void
    {
        if (!$this->space_flight_id || $this->return_flight_id)
            return;

        $outbound = Flight::find($this->space_flight_id);
        if (!$outbound)
            return;

        // "el proximo vuelo a su llegada"
        // Si hay fecha de llegada registrada, la usamos. Si no, usamos la de salida.
        $refDate = $outbound->arrival_date ?? $outbound->departure_date;

        $suggestion = Flight::with('destination')
            ->where('origin_id', $outbound->destination_id)
            ->where('destination_id', $outbound->origin_id)
            ->where('departure_date', '>', $refDate)
            ->orderBy('departure_date', 'asc')
            ->first();

        if ($suggestion) {
            $this->return_flight_id = $suggestion->id;
            $this->selectedReturnFlightLabel = '#' . $suggestion->flight_code . ' → ' . ($suggestion->destination?->name ?? '?') . ' (' . $suggestion->departure_date?->format('d/m/Y') . ')';
        }
    }

    // ── Búsqueda de Vuelo de Vuelta (Regreso) ─────────────────────────────
    public function updatedReturnFlightSearch()
    {
        if (strlen($this->returnFlightSearch) > 1) {
            $query = Flight::with('destination');

            // Si hay un vuelo de ida seleccionado, el origen de la vuelta debe ser el destino de la ida
            if ($this->space_flight_id) {
                $outboundFlight = Flight::find($this->space_flight_id);
                if ($outboundFlight) {
                    $query->where('origin_id', $outboundFlight->destination_id);
                }
            }

            $this->returnFlightSearchResults = $query
                ->where(function ($q) {
                    $q->where('flight_code', 'LIKE', '%' . $this->returnFlightSearch . '%')
                        ->orWhereHas('destination', fn($d) => $d->where('name', 'LIKE', '%' . $this->returnFlightSearch . '%'));
                })
                ->where('departure_date', '>', now())
                ->take(5)
                ->get()
                ->map(fn($f) => [
                    'id' => $f->id,
                    'label' => '#' . $f->flight_code . ' → ' . ($f->destination?->name ?? '?') . ' (' . $f->departure_date?->format('d/m/Y') . ')',
                    'price' => $f->base_price,
                ])
                ->toArray();
        } else {
            $this->returnFlightSearchResults = [];
        }
    }

    public function selectReturnFlight($id, $label)
    {
        $this->return_flight_id = $id;
        $this->selectedReturnFlightLabel = $label;
        $this->returnFlightSearch = '';
        $this->returnFlightSearchResults = [];
        $this->calculateTotalPrice();
    }

    public function clearSelectedReturnFlight()
    {
        $this->return_flight_id = null;
        $this->selectedReturnFlightLabel = '';
        $this->returnFlightSearch = '';
        $this->calculateTotalPrice();
    }

    public function clearSelectedFlight()
    {
        $this->space_flight_id = null;
        $this->selectedFlightLabel = '';
        $this->flightSearch = '';
        $this->calculateTotalPrice();
    }

    // ── Búsqueda de Hotel ────────────────────────────────────────────────
    public function updatedHotelSearch()
    {
        if (strlen($this->hotelSearch) > 1) {
            $this->hotelSearchResults = Hotel::with('location')
                ->where(function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->hotelSearch . '%')
                        ->orWhereHas('location', fn($l) => $l->where('name', 'LIKE', '%' . $this->hotelSearch . '%'));
                })
                ->take(5)
                ->get()
                ->map(fn($h) => [
                    'id' => $h->id,
                    'label' => $h->name . ' — ' . ($h->location?->name ?? '?') . ' (' . $h->galactic_stars . '★)',
                    'price' => $h->price_per_night,
                ])
                ->toArray();
        } else {
            $this->hotelSearchResults = [];
        }
    }

    public function selectHotel($id, $label)
    {
        $this->hotel_id = $id;
        $this->selectedHotelLabel = $label;
        $this->hotelSearch = '';
        $this->hotelSearchResults = [];
        $this->calculateTotalPrice();
    }

    public function clearSelectedHotel()
    {
        $this->hotel_id = null;
        $this->selectedHotelLabel = '';
        $this->hotelSearch = '';
        $this->calculateTotalPrice();
    }

    // ── Búsqueda de Vuelo Terrestre ──────────────────────────────────────
    public function updatedTerrestrialSearch()
    {
        if (strlen($this->terrestrialSearch) > 1) {
            $this->terrestrialSearchResults = TerrestrialFlight::with(['originLocation', 'destinationLocation'])
                ->where(function ($q) {
                    $q->whereHas('originLocation', fn($l) => $l->where('name', 'LIKE', '%' . $this->terrestrialSearch . '%'))
                        ->orWhereHas('destinationLocation', fn($l) => $l->where('name', 'LIKE', '%' . $this->terrestrialSearch . '%'));
                })
                ->where('departure_datetime', '>', now())
                ->take(5)
                ->get()
                ->map(fn($tf) => [
                    'id' => $tf->id,
                    'label' => ($tf->originLocation?->name ?? '?') . ' → ' . ($tf->destinationLocation?->name ?? '?')
                        . ' (' . \Carbon\Carbon::parse($tf->departure_datetime)->format('d/m H:i') . ')',
                    'price' => $tf->price,
                ])
                ->toArray();
        } else {
            $this->terrestrialSearchResults = [];
        }
    }

    public function selectTerrestrialFlight($id, $label)
    {
        $this->terrestrial_flight_id = $id;
        $this->selectedTerrestrialLabel = $label;
        $this->terrestrialSearch = '';
        $this->terrestrialSearchResults = [];
        $this->calculateTotalPrice();
    }

    public function clearSelectedTerrestrialFlight()
    {
        $this->terrestrial_flight_id = null;
        $this->selectedTerrestrialLabel = '';
        $this->terrestrialSearch = '';
        $this->calculateTotalPrice();
    }

    public function selectClient($id, $name, $email)
    {
        $this->user_id = $id;
        $this->selectedClientName = $name . ' (' . $email . ')';
        $this->clientSearch = '';
        $this->clientSearchResults = [];

        $this->clientPassengers = \App\Models\Passenger::where('user_id', $id)->get();

        if (!$this->groupMode) {
            if ($this->clientPassengers->count() === 1) {
                $p = $this->clientPassengers->first();
                $this->selectPassenger($p->id, $p->full_name);
            } else {
                $this->passenger_id = null;
                $this->selectedPassengerName = '';
            }
        }

        $this->calculateTotalPrice();
    }

    public function selectPassenger($id, $name)
    {
        $this->passenger_id = $id;
        $this->selectedPassengerName = $name;
        $this->passengerSearchResults = [];

        $hasPrevious = Reservation::where('passenger_id', $id)->exists();
        if (!$hasPrevious) {
            $this->training_included = true;
        }

        $this->calculateTotalPrice();
    }


    public function toggleGroupMode(): void
    {
        $this->groupMode = !$this->groupMode;
        $this->selectedPassengers = [];
        $this->groupTotal = 0.0;
        $this->bookingGroupId = '';
        $this->activeTab = 'space';
    }
    public function addPassengerToGroup(int $passengerId): void
    {
        // Evitar duplicados en el array
        foreach ($this->selectedPassengers as $p) {
            if ($p['passenger_id'] === $passengerId)
                return;
        }

        $passenger = \App\Models\Passenger::find($passengerId);
        if (!$passenger)
            return;

        if (!$passenger->isAdult()) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Menor de edad',
                'text' => 'No se pueden añadir pasajeros menores de 18 años por seguridad.'
            ]);
            return;
        }

        $this->selectedPassengers[] = [
            'passenger_id' => $passengerId,
            'name' => $passenger->full_name,
            'has_valid_passport' => $passenger->hasValidPassport(),
            'has_valid_training' => $passenger->hasValidTraining(),
            'has_training_discount' => $passenger->hasTrainingDiscount(),
            'seat_type' => null,
            'seat_number' => '',
            'training_included' => false,
            'passport_management_included' => false,
            'refund_insurance_included' => false,
            'vip_transfer_included' => false,
            'hotel_id' => null,
            'hotel_label' => '',
            'hotel_nights' => 0,
            'shared_hotel_key' => null,
            'is_hotel_payer' => true,
            'terrestrial_flight_id' => null,
            'terrestrial_flight_label' => '',
            'return_hotel_id' => null,
            'return_hotel_nights' => 0,
            'return_terrestrial_flight_id' => null,
            'return_vip_transfer_included' => false,
            'return_total_price' => 0.0,
            'returnPriceBreakdown' => [],
            'total_price' => 0.0,
            'priceBreakdown' => [],
        ];

        $this->recalculatePassengerPrice(count($this->selectedPassengers) - 1);
        $this->calculateGroupTotal();
    }

    public function removePassengerFromGroup(int $index): void
    {
        array_splice($this->selectedPassengers, $index, 1);
        $this->calculateGroupTotal();
    }

    public function updatePassengerReturnService(int $index, string $field, $value): void
    {
        $this->selectedPassengers[$index][$field] = $value;
        $this->recalculatePassengerPrice($index);
        $this->calculateGroupTotal();
    }

    public function selectPassengerReturnHotel(int $index, $hotelId): void
    {
        $this->selectedPassengers[$index]['return_hotel_id'] = $hotelId ?: null;
        $this->recalculatePassengerPrice($index);
        $this->calculateGroupTotal();
    }

    public function updatePassengerReturnHotelNights(int $index, $nights): void
    {
        $this->selectedPassengers[$index]['return_hotel_nights'] = (int) $nights;
        $this->recalculatePassengerPrice($index);
        $this->calculateGroupTotal();
    }

    public function selectPassengerReturnTerrestrialFlight(int $index, $flightId): void
    {
        $this->selectedPassengers[$index]['return_terrestrial_flight_id'] = $flightId ?: null;
        $this->recalculatePassengerPrice($index);
        $this->calculateGroupTotal();
    }

    public function updatePassengerService(int $index, string $field, $value): void
    {
        if (!isset($this->selectedPassengers[$index]))
            return;
        if (in_array($field, ['training_included', 'passport_management_included', 'refund_insurance_included', 'vip_transfer_included'])) {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        $this->selectedPassengers[$index][$field] = $value;
        $this->recalculatePassengerPrice($index);
        $this->calculateGroupTotal();
    }

    public function selectPassengerHotel(int $index, $hotelId): void
    {
        if (!isset($this->selectedPassengers[$index]))
            return;
        $hotelId = $hotelId ? (int) $hotelId : null;
        $label = '';
        if ($hotelId) {
            $hotel = Hotel::find($hotelId);
            $label = $hotel ? $hotel->name : '';
        }
        $this->selectedPassengers[$index]['hotel_id'] = $hotelId;
        $this->selectedPassengers[$index]['hotel_label'] = $label;
        $this->recalculatePassengerPrice($index);
        $this->calculateGroupTotal();
    }

    public function updatePassengerHotelNights(int $index, $nights): void
    {
        if (!isset($this->selectedPassengers[$index]))
            return;
        $this->selectedPassengers[$index]['hotel_nights'] = max(0, (int) $nights);
        $this->recalculatePassengerPrice($index);
        $this->calculateGroupTotal();
    }

    public function selectPassengerTerrestrialFlight(int $index, $flightId): void
    {
        if (!isset($this->selectedPassengers[$index]))
            return;
        $flightId = $flightId ? (int) $flightId : null;
        $label = '';
        if ($flightId) {
            $tf = TerrestrialFlight::with(['originLocation', 'destinationLocation'])->find($flightId);
            if ($tf) {
                $label = ($tf->originLocation?->name ?? '?') . ' → ' . ($tf->destinationLocation?->name ?? '?');
            }
        }
        $this->selectedPassengers[$index]['terrestrial_flight_id'] = $flightId;
        $this->selectedPassengers[$index]['terrestrial_flight_label'] = $label;
        $this->recalculatePassengerPrice($index);
        $this->calculateGroupTotal();
    }

    public function linkPassengersHotel(int $indexA, int $indexB): void
    {
        $token = 'HOTEL-' . strtoupper(substr(md5($indexA . $indexB . now()), 0, 8));

        $this->selectedPassengers[$indexA]['shared_hotel_key'] = $token;
        $this->selectedPassengers[$indexA]['is_hotel_payer'] = true;

        $this->selectedPassengers[$indexB]['shared_hotel_key'] = $token;
        $this->selectedPassengers[$indexB]['is_hotel_payer'] = false;

        $this->recalculatePassengerPrice($indexA);
        $this->recalculatePassengerPrice($indexB);
        $this->calculateGroupTotal();
    }

    public function prepareAdendaMode(int $reservationId): void
    {
        $res = Reservation::with(['logistics', 'spaceFlight', 'passenger', 'user'])->find($reservationId);
        if (!$res)
            return;

        // Si la reserva NO está pagada, el "upgrade" es simplemente una edición
        // para que se sume al total de la reserva original y lo pague todo junto.
        if ($res->payment_status !== 'paid') {
            $this->edit($reservationId);
            return;
        }

        $this->resetInputFields();
        $this->groupMode = false;

        $this->isAdendaMode = true;
        $this->adendaParentId = $res->id;
        $this->isEditing = false;

        $this->originalSnapshot = $res->price_snapshot;
        $this->originalSpaceFlightId = $res->space_flight_id;
        $this->originalSeatType = $res->seat_type;
        $this->originalHotelId = $res->logistics?->hotel_id;
        $this->originalHotelNights = $res->logistics?->hotel_nights;
        $this->originalTerrestrialFlightId = $res->logistics?->terrestrial_flight_id;
        $this->originalTraining = $res->logistics?->training_included;
        $this->originalVip = $res->logistics?->vip_transfer_included;
        $this->originalPassport = $res->logistics?->passport_management_included;
        $this->originalInsurance = $res->logistics?->refund_insurance_included;

        // Heredar datos del pasajero original
        $this->user_id = $res->user_id;
        $this->passenger_id = $res->passenger_id;
        $this->space_flight_id = $res->space_flight_id;
        $this->seat_type = $res->seat_type;
        $this->selectedClientName = $res->user ? $res->user->name . ' (' . $res->user->email . ')' : '';
        $this->selectedPassengerName = $res->passenger?->full_name ?? '';
        $this->bookingGroupId = $res->booking_group_id;

        if ($res->spaceFlight) {
            $sf = $res->spaceFlight;
            $this->selectedFlightLabel = '#' . $sf->flight_code . ' → ' . ($sf->destination?->name ?? '?')
                . ' (' . $sf->departure_date?->format('d/m/Y') . ')';
        }

        // Marcar servicios YA contratados para que el Blade los deshabilite
        $this->training_included = $res->logistics?->training_included ?? false;
        $this->passport_management_included = $res->logistics?->passport_management_included ?? false;
        $this->refund_insurance_included = $res->logistics?->refund_insurance_included ?? false;
        $this->vip_transfer_included = $res->logistics?->vip_transfer_included ?? false;

        $this->calculateTotalPrice();
    }

    /**
     * Recalcula el precio de un pasajero concreto dentro del grupo.
     * Respeta la lógica de shared_hotel_key.
     */
    protected function recalculatePassengerPrice(int $index): void
    {
        $p = $this->selectedPassengers[$index] ?? null;
        if (!$p)
            return;

        $spacePrice = 0;
        if ($this->space_flight_id) {
            $flight = Flight::find($this->space_flight_id);
            if ($flight) {
                $mult = strtolower($p['seat_type'] ?? 'nova') === 'supernova' ? 2.5 : 1;
                if (!$p['seat_type'])
                    $mult = 0; // Si el pasajero no tiene asiento en este vuelo
                $spacePrice = round($flight->base_price * $mult, 2);
            }
        }

        $hotelPrice = 0;
        if (($p['hotel_id'] ?? null) && ($p['hotel_nights'] ?? 0) > 0) {
            if (!($p['shared_hotel_key'] ?? null) || ($p['is_hotel_payer'] ?? true)) {
                $hotel = Hotel::find($p['hotel_id']);
                if ($hotel) {
                    $hotelPrice = round($hotel->price_per_night * $p['hotel_nights'], 2);
                }
            }
        }

        $terrestrialPrice = 0;
        if ($p['terrestrial_flight_id'] ?? null) {
            $tf = TerrestrialFlight::find($p['terrestrial_flight_id']);
            if ($tf)
                $terrestrialPrice = round($tf->price ?? 0, 2);
        }

        $trainingFee = $p['training_included'] ? PriceLog::getCurrentPrice('training') : 0;
        $passportFee = $p['passport_management_included'] ? PriceLog::getCurrentPrice('passport_management') : 0;
        $vipFee = $p['vip_transfer_included'] ? PriceLog::getCurrentPrice('vip_transfer') : 0;

        $base = $spacePrice + $hotelPrice + $terrestrialPrice + $trainingFee + $passportFee + $vipFee;

        $insuranceFee = 0;
        if ($p['refund_insurance_included']) {
            $pct = PriceLog::getCurrentPrice('refund_insurance');
            $insuranceFee = round($base * ($pct / 100), 2);
        }

        $passenger = \App\Models\Passenger::find($p['passenger_id']);
        $discountAmt = 0;
        if ($passenger && $passenger->hasTrainingDiscount()) {
            $discountAmt = round(($base + $insuranceFee) * 0.10, 2);
        }

        $total = max(0, round($base + $insuranceFee - $discountAmt, 2));

        $this->selectedPassengers[$index]['total_price'] = $total;
        $this->selectedPassengers[$index]['priceBreakdown'] = [
            'space' => $spacePrice,
            'hotel' => $hotelPrice,
            'terrestrial' => $terrestrialPrice,
            'training' => $trainingFee,
            'passport' => $passportFee,
            'vip' => $vipFee,
            'insurance' => $insuranceFee,
            'discount' => $discountAmt,
            'total' => $total,
        ];

        if ($this->hasReturnFlight) {
            $this->calculateReturnPriceForPassenger($index);
        } else {
            $this->selectedPassengers[$index]['return_total_price'] = 0;
            $this->selectedPassengers[$index]['returnPriceBreakdown'] = [];
        }

        $this->applyTemporalCoherence();
    }

    public function calculateReturnPriceForPassenger(int $index): void
    {
        $p = &$this->selectedPassengers[$index];
        $passenger = \App\Models\Passenger::find($p['passenger_id']);

        $spacePrice = 0;
        if ($this->return_flight_id) {
            $flight = Flight::find($this->return_flight_id);
            if ($flight) {
                $mult = $p['seat_type'] === 'supernova' ? 2.5 : 1;
                $spacePrice = round($flight->base_price * $mult, 2);
            }
        }

        $hotelPrice = 0;
        if (($p['return_hotel_id'] ?? null) && ($p['return_hotel_nights'] ?? 0) > 0) {
            $hotel = Hotel::find($p['return_hotel_id']);
            if ($hotel) {
                $hotelPrice = round($hotel->price_per_night * $p['return_hotel_nights'], 2);
            }
        }

        $terrestrialPrice = 0;
        if ($p['return_terrestrial_flight_id'] ?? null) {
            $tf = TerrestrialFlight::find($p['return_terrestrial_flight_id']);
            if ($tf) {
                $terrestrialPrice = round($tf->price, 2);
            }
        }

        $vipFee = ($p['return_vip_transfer_included'] ?? false) ? PriceLog::getCurrentPrice('vip_transfer') : 0;

        $trainingFee = 0;
        $passportFee = 0;

        $base = $spacePrice + $hotelPrice + $terrestrialPrice + $trainingFee + $passportFee + $vipFee;

        $insuranceFee = 0;
        if ($p['refund_insurance_included']) {
            $pct = PriceLog::getCurrentPrice('refund_insurance');
            $insuranceFee = round($base * ($pct / 100), 2);
        }

        $discountAmt = 0;
        if ($passenger && $passenger->hasTrainingDiscount()) {
            $discountAmt = round(($base + $insuranceFee) * 0.10, 2);
        }

        $totalReturn = max(0, round($base + $insuranceFee - $discountAmt, 2));

        $p['return_total_price'] = $totalReturn;
        $p['returnPriceBreakdown'] = [
            'space' => $spacePrice,
            'hotel' => $hotelPrice,
            'terrestrial' => $terrestrialPrice,
            'vip' => $vipFee,
            'insurance' => $insuranceFee,
            'discount' => $discountAmt,
            'total' => $totalReturn,
        ];
    }

    /**
     * Suma el total de todos los pasajeros del grupo.
     */
    public function calculateGroupTotal(): void
    {
        $this->groupTotal = collect($this->selectedPassengers)->sum(function ($p) {
            return ($p['total_price'] ?? 0) + ($p['return_total_price'] ?? 0);
        });

        // Generar un breakdown acumulado para la UI
        $groupBreakdown = [
            'space' => 0,
            'hotel' => 0,
            'terrestrial' => 0,
            'training' => 0,
            'passport' => 0,
            'vip' => 0,
            'insurance' => 0,
            'subtotal' => 0,
            'discount_amt' => 0,
            'discount_pct' => 0,
            'adj_type' => $this->manual_adjustment_type,
            'adj_value' => (float) $this->manual_adjustment_value,
            'adj_amount' => 0,
            'total' => $this->groupTotal,
        ];

        foreach ($this->selectedPassengers as $p) {
            $pb = $p['priceBreakdown'] ?? [];
            $rb = $p['returnPriceBreakdown'] ?? [];

            $groupBreakdown['space'] += ($pb['space'] ?? 0) + ($rb['space'] ?? 0);
            $groupBreakdown['hotel'] += ($pb['hotel'] ?? 0) + ($rb['hotel'] ?? 0);
            $groupBreakdown['terrestrial'] += ($pb['terrestrial'] ?? 0) + ($rb['terrestrial'] ?? 0);
            $groupBreakdown['training'] += ($pb['training'] ?? 0);
            $groupBreakdown['passport'] += ($pb['passport'] ?? 0);
            $groupBreakdown['vip'] += ($pb['vip'] ?? 0) + ($rb['vip'] ?? 0);
            $groupBreakdown['insurance'] += ($pb['insurance'] ?? 0) + ($rb['insurance'] ?? 0);
            $groupBreakdown['discount_amt'] += ($pb['discount'] ?? 0) + ($rb['discount'] ?? 0);
        }

        $subtotal = $groupBreakdown['space'] + $groupBreakdown['hotel'] + $groupBreakdown['terrestrial'] +
            $groupBreakdown['training'] + $groupBreakdown['passport'] + $groupBreakdown['vip'] + $groupBreakdown['insurance'];

        $groupBreakdown['subtotal'] = $subtotal;
        $afterCert = $subtotal - $groupBreakdown['discount_amt'];

        // Aplicar ajuste manual al TOTAL del grupo
        $manualAdj = 0;
        if ($groupBreakdown['adj_type'] === 'pct' && $groupBreakdown['adj_value'] > 0) {
            $manualAdj = round($afterCert * ($groupBreakdown['adj_value'] / 100), 2);
        } elseif ($groupBreakdown['adj_type'] === 'fixed' && $groupBreakdown['adj_value'] > 0) {
            $manualAdj = min($afterCert, $groupBreakdown['adj_value']);
        }

        $groupBreakdown['adj_amount'] = $manualAdj;
        $groupBreakdown['total'] = round($afterCert - $manualAdj, 2);
        $this->groupTotal = $groupBreakdown['total'];

        // Distribuir el ajuste manual entre los pasajeros para que el guardado sea correcto
        if ($manualAdj > 0 && $afterCert > 0) {
            foreach ($this->selectedPassengers as $idx => $p) {
                $pSubtotal = ($p['total_price'] ?? 0) + ($p['return_total_price'] ?? 0);
                $pShare = $pSubtotal / $afterCert;
                $pAdj = round($manualAdj * $pShare, 2);

                // Actualizamos el total del pasajero restando su parte del ajuste
                $this->selectedPassengers[$idx]['total_price'] -= $pAdj;
                // También lo reflejamos en su breakdown individual para que el Snapshot sea real
                $this->selectedPassengers[$idx]['priceBreakdown']['adj_amount'] = $pAdj;
                $this->selectedPassengers[$idx]['priceBreakdown']['total'] -= $pAdj;
            }
        }

        $this->priceBreakdown = $groupBreakdown;
    }

    /**
     * Calcula el precio del trayecto de vuelta para MODO INDIVIDUAL.
     */
    public function calculateIndividualReturnPrice(): void
    {
        if (!$this->hasReturnFlight || !$this->return_flight_id) {
            $this->return_total_price = 0;
            $this->returnPriceBreakdown = [];
            return;
        }

        $passenger = $this->passenger_id ? \App\Models\Passenger::find($this->passenger_id) : null;
        $flight = Flight::find($this->return_flight_id);
        if (!$flight)
            return;

        $mult = $this->seat_type === 'supernova' ? 2.5 : 1;
        $spacePrice = round($flight->base_price * $mult, 2);

        $hotelPrice = 0;
        if ($this->return_hotel_id && $this->return_hotel_nights > 0) {
            $hotel = Hotel::find($this->return_hotel_id);
            if ($hotel)
                $hotelPrice = round($hotel->price_per_night * $this->return_hotel_nights, 2);
        }

        $terrestrialPrice = 0;
        if ($this->return_terrestrial_flight_id) {
            $tf = TerrestrialFlight::find($this->return_terrestrial_flight_id);
            if ($tf)
                $terrestrialPrice = round($tf->price, 2);
        }

        $vipFee = $this->return_vip_transfer_included ? PriceLog::getCurrentPrice('vip_transfer') : 0;

        $base = $spacePrice + $hotelPrice + $terrestrialPrice + $vipFee;

        $insuranceFee = 0;
        if ($this->refund_insurance_included) {
            $pct = PriceLog::getCurrentPrice('refund_insurance');
            $insuranceFee = round($base * ($pct / 100), 2);
        }

        $discountAmt = ($passenger && $passenger->hasTrainingDiscount()) ? round(($base + $insuranceFee) * 0.10, 2) : 0;

        $totalReturn = max(0, round($base + $insuranceFee - $discountAmt, 2));

        $this->return_total_price = $totalReturn;
        $this->returnPriceBreakdown = [
            'space' => $spacePrice,
            'hotel' => $hotelPrice,
            'terrestrial' => $terrestrialPrice,
            'vip' => $vipFee,
            'insurance' => $insuranceFee,
            'discount' => $discountAmt,
            'total' => $totalReturn,
            'snapshot_at' => now()->toIso8601String(),
        ];
    }

    public function clearSelectedPassenger()
    {
        $this->passenger_id = null;
        $this->selectedPassengerName = '';
        $this->calculateTotalPrice();
    }

    public function clearSelectedClient()
    {
        $this->user_id = null;
        $this->passenger_id = null;
        $this->selectedClientName = '';
        $this->selectedPassengerName = '';
        $this->clientSearch = '';
        $this->clientPassengers = [];
        $this->calculateTotalPrice();
    }

    public function calculateTotalPrice()
    {
        $price = 0;
        $snap = $this->originalSnapshot;
        $isProtecting = ($this->isEditing || $this->isAdendaMode);
        $spacePrice = 0;
        $mult = 1;
        if ($this->space_flight_id) {
            if ($snap && $isProtecting && $this->space_flight_id == $this->originalSpaceFlightId && $this->seat_type == $this->originalSeatType && isset($snap['space'])) {
                $spacePrice = (float) $snap['space'];
                $mult = $snap['mult'] ?? 1;
            } else {
                $flight = Flight::find($this->space_flight_id);
                if ($flight) {
                    if ($this->seat_type === 'supernova')
                        $mult = 2.5;
                    $spacePrice = round($flight->base_price * $mult, 2);
                }
            }
            $price += $spacePrice;
        }

        $hotelPrice = 0;
        if ($this->hotel_id && $this->hotel_nights > 0) {
            if ($snap && $isProtecting && $this->hotel_id == $this->originalHotelId && $this->hotel_nights == $this->originalHotelNights && isset($snap['hotel'])) {
                $hotelPrice = (float) $snap['hotel'];
            } else {
                $hotel = Hotel::find($this->hotel_id);
                if ($hotel) {
                    $hotelPrice = round($hotel->price_per_night * $this->hotel_nights, 2);
                }
            }
            $price += $hotelPrice;
        }

        $terrestrialPrice = 0;
        if ($this->terrestrial_flight_id) {
            if ($snap && $isProtecting && $this->terrestrial_flight_id == $this->originalTerrestrialFlightId && isset($snap['terrestrial'])) {
                $terrestrialPrice = (float) $snap['terrestrial'];
            } else {
                $tFlight = TerrestrialFlight::find($this->terrestrial_flight_id);
                if ($tFlight) {
                    $terrestrialPrice = round($tFlight->price, 2);
                }
            }
            $price += $terrestrialPrice;
        }

        $trainingFee = 0;
        if ($this->training_included) {
            if ($snap && $isProtecting && $this->training_included == $this->originalTraining && isset($snap['training'])) {
                $trainingFee = (float) $snap['training'];
            } else {
                $trainingFee = PriceLog::getCurrentPrice('training');
            }
        }

        $passportFee = 0;
        if ($this->passport_management_included) {
            if ($snap && $isProtecting && $this->passport_management_included == $this->originalPassport && isset($snap['passport'])) {
                $passportFee = (float) $snap['passport'];
            } else {
                $passportFee = PriceLog::getCurrentPrice('passport_management');
            }
        }

        $vipFee = 0;
        if ($this->vip_transfer_included) {
            if ($snap && $isProtecting && $this->vip_transfer_included == $this->originalVip && isset($snap['vip'])) {
                $vipFee = (float) $snap['vip'];
            } else {
                $vipFee = PriceLog::getCurrentPrice('vip_transfer');
            }
        }

        $priceBeforeInsurance = $price + $trainingFee + $passportFee + $vipFee;

        $insuranceFee = 0;
        if ($this->refund_insurance_included) {
            if ($snap && $isProtecting && $this->refund_insurance_included == $this->originalInsurance && isset($snap['insurance'])) {
                $insuranceFee = (float) $snap['insurance'];
            } else {
                $insurancePct = PriceLog::getCurrentPrice('refund_insurance');
                $insuranceFee = round($priceBeforeInsurance * ($insurancePct / 100), 2);
            }
        }

        $price = $priceBeforeInsurance + $insuranceFee;
        $this->discount_applied = false;
        if ($this->passenger_id) {
            $passenger = \App\Models\Passenger::find($this->passenger_id);
            if ($passenger && $passenger->training_certificate_date) {
                $years = \Carbon\Carbon::parse($passenger->training_certificate_date)->diffInYears(now());
                if ($years < 3 && $passenger->training_certificate_status === 'Apto') {
                    $this->discount_applied = true;
                }
            }
        }

        $subtotal = $price;
        $discountAmt = $this->discount_applied ? round($subtotal * 0.10, 2) : 0;
        $afterCertDiscount = round($subtotal - $discountAmt, 2);

        $manualAdj = 0;
        $adjType = $this->manual_adjustment_type;
        $adjValue = (float) $this->manual_adjustment_value;

        if ($adjType === 'pct' && $adjValue > 0) {
            $manualAdj = round($afterCertDiscount * ($adjValue / 100), 2);
        } elseif ($adjType === 'fixed' && $adjValue > 0) {
            $manualAdj = min($afterCertDiscount, $adjValue);
        }

        $total = round($afterCertDiscount - $manualAdj, 2);

        // Si es una Adenda/Upgrade, solo cobramos la DIFERENCIA
        $this->paid_amount = 0;
        if ($this->isAdendaMode && $this->adendaParentId) {
            $parent = \App\Models\Reservation::find($this->adendaParentId);
            if ($parent) {
                $this->paid_amount = (float) $parent->total_price;
                $total = max(0, $total - $this->paid_amount);
            }
        }

        if ($total < 0)
            $total = 0;

        if (!$this->priceOverrideEnabled) {
            $this->total_price = $total;
        }

        $this->calculateIndividualReturnPrice();

        $this->priceBreakdown = [
            'space' => $spacePrice,
            'mult' => $mult,
            'hotel' => $hotelPrice,
            'hotel_nights' => (int) $this->hotel_nights,
            'terrestrial' => $terrestrialPrice,
            'training' => $trainingFee,
            'passport' => $passportFee,
            'vip' => $vipFee,
            'insurance' => $insuranceFee,
            'subtotal' => $subtotal,
            'discount_pct' => $this->discount_applied ? 10 : 0,
            'discount_amt' => $discountAmt,
            'after_cert' => $afterCertDiscount,
            'adj_type' => $adjType,
            'adj_value' => $adjValue,
            'adj_amount' => $manualAdj,
            'paid_amount' => $this->paid_amount,
            'total' => $total,
        ];
        $this->calculateReadiness();
    }

    protected function calculateReadiness()
    {
        if (!$this->passenger_id) {
            $this->readinessCheck = [];
            return;
        }

        $p = \App\Models\Passenger::find($this->passenger_id);
        if (!$p)
            return;

        $hasPassport = $p->hasValidPassport() || $this->passport_management_included;
        $hasTraining = $p->hasValidTraining() || $this->training_included;
        $isPhysicalFit = $p->physical_fitness === 'Excelente';
        $isAdult = $p->isAdult();

        $this->readinessCheck = [
            'passenger_name' => $p->full_name,
            'has_passport' => $hasPassport,
            'has_training' => $hasTraining,
            'is_physical_fit' => $isPhysicalFit,
            'is_adult' => $isAdult,
            'is_ready' => $hasPassport && $hasTraining && $isPhysicalFit && $isAdult,
            'missing' => collect([
                !$hasPassport ? 'Pasaporte' : null,
                !$hasTraining ? 'Entrenamiento' : null,
                !$isPhysicalFit ? 'Estado Físico' : null,
                !$isAdult ? 'Mayoría de edad (18+)' : null,
            ])->filter()->values()->toArray()
        ];
    }

    public function render()
    {
        $query = Reservation::with([
            'user.passports',
            'passenger',
            'spaceFlight.destination',
            'logistics.hotel',
            'logistics.terrestrialFlight.originLocation',
            'logistics.terrestrialFlight.destinationLocation',
        ]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id_locator', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('passenger', fn($p) => $p->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('primarylastname', 'like', '%' . $this->search . '%')
                        ->orWhere('secondarylastname', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('spaceFlight', fn($f) => $f->where('flight_code', 'like', '%' . $this->search . '%'));
            });
        } else {
            // Agrupamos por expedición para el Hangar de Grupo
            $query->where(function ($q) {
                $q->whereIn('id', function ($sub) {
                    $sub->selectRaw('MIN(id)')
                        ->from('reservations')
                        ->where('is_adenda', false)
                        ->whereNotNull('booking_group_id')
                        ->whereNull('deleted_at')
                        ->groupBy('booking_group_id');
                })->orWhereNull('booking_group_id');
            });
        }
        $reservations = $query->orderBy('created_at', $this->sortDir)
            ->paginate($this->getPerPage());

        $spaceFlights = Flight::with('destination')->where('departure_date', '>', now())->orderBy('departure_date')->get();
        $hotels = Hotel::with('location')->orderBy('name')->get();
        $terrestrialFlights = TerrestrialFlight::with(['originLocation', 'destinationLocation'])->where('departure_datetime', '>', now())->orderBy('departure_datetime')->get();

        return view('livewire.admin.manage-reservations', [
            'reservations' => $reservations,
            'spaceFlights' => $spaceFlights,
            'hotels' => $hotels,
            'terrestrialFlights' => $terrestrialFlights,
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

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function edit($id)
    {
        $this->resetInputFields();
        $this->groupMode = false;
        $res = Reservation::with('logistics')->find($id);

        if ($res) {
            $this->isEditing = true;
            $this->reservationId = $res->id;
            $this->locatorId = $res->id_locator;

            $this->user_id = $res->user_id;
            $this->selectedClientName = $res->user ? $res->user->name . ' (' . $res->user->email . ')' : '';
            $this->passenger_id = $res->passenger_id;
            $this->selectedPassengerName = $res->passenger ? $res->passenger->full_name : '';
            $this->clientPassengers = \App\Models\Passenger::where('user_id', $res->user_id)->get();
            $this->space_flight_id = $res->space_flight_id;
            $this->seat_type = $res->seat_type;
            $this->seat_number = $res->seat_number;

            $this->originalSnapshot = $res->price_snapshot;
            $this->originalSpaceFlightId = $res->space_flight_id;
            $this->originalSeatType = $res->seat_type;

            if ($res->logistics) {
                $this->terrestrial_flight_id = $res->logistics->terrestrial_flight_id;
                $this->hotel_id = $res->logistics->hotel_id;
                $this->hotel_nights = $res->logistics->hotel_nights;
                $this->training_included = $res->logistics->training_included;
                $this->vip_transfer_included = $res->logistics->vip_transfer_included;
                $this->refund_insurance_included = $res->logistics->refund_insurance_included;
                $this->passport_management_included = $res->logistics->passport_management_included;
                $this->originalTerrestrialFlightId = $res->logistics->terrestrial_flight_id;
                $this->originalHotelId = $res->logistics->hotel_id;
                $this->originalHotelNights = $res->logistics->hotel_nights;
                $this->originalTraining = $res->logistics->training_included;
                $this->originalVip = $res->logistics->vip_transfer_included;
                $this->originalPassport = $res->logistics->passport_management_included;
                $this->originalInsurance = $res->logistics->refund_insurance_included;
            }

            if ($res->spaceFlight) {
                $sf = $res->spaceFlight;
                $this->selectedFlightLabel = '#' . $sf->flight_code . ' → ' . ($sf->destination?->name ?? '?')
                    . ' (' . $sf->departure_date?->format('d/m/Y') . ')';
            }

            if ($res->logistics?->hotel) {
                $h = $res->logistics->hotel;
                $this->selectedHotelLabel = $h->name . ' — ' . ($h->location?->name ?? '?');
            }

            if ($res->logistics?->terrestrialFlight) {
                $tf = $res->logistics->terrestrialFlight;
                $this->selectedTerrestrialLabel = ($tf->originLocation?->name ?? '?') . ' → ' . ($tf->destinationLocation?->name ?? '?');
            }

            $this->manual_adjustment_type = $res->manual_adjustment_type ?? 'none';
            $this->manual_adjustment_value = $res->manual_adjustment_value ?? 0;
            $this->discount_note = $res->discount_note ?? '';

            $this->calculateTotalPrice();
            if ($res->total_price && $this->total_price != $res->total_price) {
                $this->total_price = $res->total_price;
            }

            $this->status = $res->status;
        }
    }

    private function notifyGestorOfRequiredActions(\App\Models\Reservation $res): void
    {
        $res->load(['user', 'passenger', 'logistics']);
        $gestorId = $res->user?->assigned_manager_id;
        if (!$gestorId)
            return;

        $clientName = $res->user?->name ?? 'Cliente';
        $passengerName = $res->passenger?->full_name ?? $clientName;
        $locator = strtoupper(substr($res->id_locator, 0, 8));

        if ($res->logistics?->training_included) {
            $exists = \App\Models\Task::where('assigned_gestor_id', $gestorId)
                ->where('type', 'training_scheduled')
                ->whereJsonContains('payload->reservation_id', $res->id)
                ->exists();

            if (!$exists) {
                \App\Models\Task::create([
                    'assigned_gestor_id' => $gestorId,
                    'created_by' => auth()->id(),
                    'title' => "Agendar Iris Training — {$passengerName}",
                    'description' => "El pasajero ha solicitado Iris Training en la reserva #{$locator}.\n\nContactar con el cliente para agendar las sesiones de entrenamiento y validación física.",
                    'type' => 'training_scheduled',
                    'status' => 'Pendiente',
                    'priority' => 'alta',
                    'payload' => ['reservation_id' => $res->id, 'passenger_name' => $passengerName],
                ]);
            }
        }

        if ($res->logistics?->passport_management_included) {
            $exists = \App\Models\Task::where('assigned_gestor_id', $gestorId)
                ->where('type', 'passport_request')
                ->whereJsonContains('payload->reservation_id', $res->id)
                ->exists();

            if (!$exists) {
                \App\Models\Task::create([
                    'assigned_gestor_id' => $gestorId,
                    'created_by' => auth()->id(),
                    'title' => "Gestión de Pasaporte — {$passengerName}",
                    'description' => "Se requiere gestión de pasaporte/visado para el pasajero en la reserva #{$locator}.\n\nIniciar trámites consulares y solicitar documentación necesaria al cliente.",
                    'type' => 'passport_request',
                    'status' => 'Pendiente',
                    'priority' => 'urgente',
                    'payload' => ['reservation_id' => $res->id, 'passenger_name' => $passengerName],
                ]);
            }
        }
    }

    public function confirmSave()
    {
        $this->validate();
        $this->showSaveModal = true;
    }

    public function executeSave()
    {
        // ── RAMA: MODO ADENDA ───────────────────────────────────
        if ($this->isAdendaMode && $this->adendaParentId) {
            $parent = Reservation::find($this->adendaParentId);
            if (!$parent) {
                session()->flash('error', 'No se encontró la reserva original para vincular la adenda.');
                return;
            }

            $adendaData = [
                'user_id' => $this->user_id,
                'passenger_id' => $this->passenger_id,
                'space_flight_id' => $this->space_flight_id,
                'id_locator' => $parent->id_locator,
                'booking_group_id' => $parent->booking_group_id,
                'parent_reservation_id' => $parent->id,
                'is_adenda' => true,
                'seat_type' => $this->seat_type,
                'total_price' => $this->total_price,
                'discount_applied' => $this->discount_applied,
                'status' => 'Pendiente',
                'price_snapshot' => $this->buildPriceSnapshot(),
            ];

            $logisticsData = [
                'training_included' => $this->training_included && !($parent->logistics?->training_included),
                'passport_management_included' => $this->passport_management_included && !($parent->logistics?->passport_management_included),
                'refund_insurance_included' => $this->refund_insurance_included && !($parent->logistics?->refund_insurance_included),
                'vip_transfer_included' => $this->vip_transfer_included && !($parent->logistics?->vip_transfer_included),
                'hotel_id' => $this->hotel_id ?: null,
                'hotel_nights' => $this->hotel_nights ?: 0,
                'terrestrial_flight_id' => $this->terrestrial_flight_id ?: null,
            ];

            $existingAdenda = Reservation::where('parent_reservation_id', $parent->id)
                ->where('payment_status', '!=', 'paid')
                ->where('is_adenda', true)
                ->first();

            if ($existingAdenda) {
                $existingAdenda->update($adendaData);
                $existingAdenda->logistics()->update($logisticsData);
                $this->notifyGestorOfRequiredActions($existingAdenda);
                session()->flash('message', 'Upgrade actualizado (pendiente de pago).');
            } else {
                $adenda = Reservation::create($adendaData);
                $adenda->logistics()->create($logisticsData);
                $this->notifyGestorOfRequiredActions($adenda);
                session()->flash('message', 'Nueva Adenda registrada correctamente.');
            }

            $this->resetInputFields();
            return;
        }

        if ($this->groupMode && count($this->selectedPassengers) > 0) {
            if (!$this->user_id) {
                session()->flash('error', 'Seleccione cliente.');
                return;
            }

            // Validación Capacidad IDA
            if ($this->space_flight_id) {
                $seatTypeGroups = collect($this->selectedPassengers)->groupBy('seat_type');
                foreach ($seatTypeGroups as $st => $g) {
                    if (Flight::availableSeats($this->space_flight_id, $st) < $g->count()) {
                        $msg = "Sin cupo en IDA para clase " . strtoupper($st);
                        session()->flash('error', $msg);
                        $this->dispatch('swal:alert', ['icon' => 'error', 'title' => 'Error de Cupo', 'text' => $msg]);
                        return;
                    }
                    if ($this->hasReturnFlight && $this->return_flight_id) {
                        if (Flight::availableSeats($this->return_flight_id, $st) < $g->count()) {
                            session()->flash('error', "Sin cupo en VUELTA para clase " . strtoupper($st));
                            return;
                        }
                    }
                }
            }
            if ($this->space_flight_id) {
                $flight = Flight::find($this->space_flight_id);
                foreach ($this->selectedPassengers as $pData) {
                    $pId = $pData['passenger_id'] ?? null;
                    if (!$pId)
                        continue;

                    $passenger = Passenger::find($pId);
                    if ($passenger && $passenger->hasConflictOnDate(\Carbon\Carbon::parse($flight->departure_date))) {
                        $msg = "ERROR DE UBICUIDAD: El pasajero {$passenger->name} ya tiene una misión para esta fecha.";
                        session()->flash('error', $msg);
                        $this->dispatch('swal:alert', ['icon' => 'error', 'title' => 'Conflicto Logístico', 'text' => $msg]);
                        return;
                    }
                }
            }

            try {
                \Illuminate\Support\Facades\DB::transaction(function () {
                    $groupId = (string) \Illuminate\Support\Str::uuid();
                    foreach ($this->selectedPassengers as $pIdx => $pData) {

                        // IDA
                        $resOut = Reservation::create([
                            'user_id' => $this->user_id,
                            'passenger_id' => $pData['passenger_id'],
                            'space_flight_id' => $this->space_flight_id,
                            'booking_group_id' => $groupId,
                            'group_name' => null,
                            'seat_type' => $pData['seat_type'] ?? 'nova',
                            'seat_number' => $pData['seat_number'] ?? null,
                            'total_price' => $pData['total_price'] ?? 0,
                            'status' => 'Pendiente',
                            'price_snapshot' => $pData['priceBreakdown'] ?? [],
                        ]);
                        $resOut->logistics()->create([
                            'terrestrial_flight_id' => $pData['terrestrial_flight_id'] ?? null,
                            'hotel_id' => $pData['hotel_id'] ?? null,
                            'hotel_nights' => $pData['hotel_nights'] ?? 0,
                            'training_included' => $pData['training_included'] ?? false,
                            'vip_transfer_included' => $pData['vip_transfer_included'] ?? false,
                            'refund_insurance_included' => $pData['refund_insurance_included'] ?? false,
                            'passport_management_included' => $pData['passport_management_included'] ?? false,
                        ]);
                        $this->notifyGestorOfRequiredActions($resOut);

                        // VUELTA
                        if ($this->hasReturnFlight && $this->return_flight_id) {
                            $resRet = Reservation::create([
                                'user_id' => $this->user_id,
                                'passenger_id' => $pData['passenger_id'],
                                'space_flight_id' => $this->return_flight_id,
                                'booking_group_id' => $groupId,
                                'group_name' => null,
                                'seat_type' => $pData['seat_type'] ?? 'nova',
                                'total_price' => $pData['return_total_price'] ?? 0,
                                'status' => 'Pendiente',
                                'price_snapshot' => array_merge($pData['returnPriceBreakdown'] ?? [], ['snapshot_at' => now()->toIso8601String()]),
                            ]);
                            $resRet->logistics()->create([
                                'terrestrial_flight_id' => $pData['return_terrestrial_flight_id'] ?? null,
                                'hotel_id' => $pData['return_hotel_id'] ?? null,
                                'hotel_nights' => $pData['return_hotel_nights'] ?? 0,
                                'training_included' => false,
                                'passport_management_included' => false,
                                'refund_insurance_included' => $pData['refund_insurance_included'],
                                'vip_transfer_included' => $pData['return_vip_transfer_included'] ?? false,
                            ]);
                            $this->notifyGestorOfRequiredActions($resRet);
                        }
                    }
                });
                session()->flash('message', 'Expedición grupal (Ida/Vuelta) creada exitosamente.');
                $this->dispatch('swal:alert', ['icon' => 'success', 'title' => 'Éxito', 'text' => 'Expedición grupal creada correctamente.']);
            } catch (\Throwable $e) {
                session()->flash('error', $e->getMessage());
                $this->dispatch('swal:alert', ['icon' => 'error', 'title' => 'Error en Grupo', 'text' => $e->getMessage()]);
            }
            $this->resetInputFields();
            return;
        }

        $this->validate();

        if ($this->isEditing && $this->reservationId) {
            $res = Reservation::find($this->reservationId);
            if ($res) {
                $res->update([
                    'user_id' => $this->user_id,
                    'passenger_id' => $this->passenger_id,
                    'space_flight_id' => $this->space_flight_id,
                    'seat_type' => $this->seat_type,
                    'seat_number' => $this->seat_number,
                    'total_price' => $this->total_price,
                    'status' => $this->status,
                    'price_snapshot' => $this->buildPriceSnapshot(),
                ]);
                $res->logistics()->update([
                    'terrestrial_flight_id' => $this->terrestrial_flight_id ?: null,
                    'hotel_id' => $this->hotel_id ?: null,
                    'hotel_nights' => $this->hotel_nights ?: 0,
                    'training_included' => $this->training_included,
                    'vip_transfer_included' => $this->vip_transfer_included,
                    'refund_insurance_included' => $this->refund_insurance_included,
                    'passport_management_included' => $this->passport_management_included,
                ]);
                $this->notifyGestorOfRequiredActions($res);
                session()->flash('message', 'Reserva actualizada.');
                $this->dispatch('swal:alert', ['icon' => 'success', 'title' => 'Actualizado', 'text' => 'Los cambios se han guardado correctamente.']);
            }
        } else {
            try {
                \Illuminate\Support\Facades\DB::transaction(function () {
                    $groupId = (string) \Illuminate\Support\Str::uuid();
                    // IDA
                    $resOut = Reservation::create([
                        'user_id' => $this->user_id,
                        'passenger_id' => $this->passenger_id,
                        'space_flight_id' => $this->space_flight_id,
                        'booking_group_id' => $groupId,
                        'group_name' => $this->expedition_title ?: null,
                        'seat_type' => $this->seat_type,
                        'seat_number' => $this->seat_number,
                        'total_price' => $this->total_price,
                        'status' => $this->status,
                        'price_snapshot' => $this->buildPriceSnapshot(),
                    ]);
                    $resOut->logistics()->create([
                        'terrestrial_flight_id' => $this->terrestrial_flight_id ?: null,
                        'hotel_id' => $this->hotel_id ?: null,
                        'hotel_nights' => $this->hotel_nights ?: 0,
                        'training_included' => $this->training_included,
                        'vip_transfer_included' => $this->vip_transfer_included,
                        'refund_insurance_included' => $this->refund_insurance_included,
                        'passport_management_included' => $this->passport_management_included,
                    ]);
                    $this->notifyGestorOfRequiredActions($resOut);

                    // VUELTA
                    if ($this->hasReturnFlight && $this->return_flight_id) {
                        $resRet = Reservation::create([
                            'user_id' => $this->user_id,
                            'passenger_id' => $this->passenger_id,
                            'space_flight_id' => $this->return_flight_id,
                            'booking_group_id' => $groupId,
                            'group_name' => $this->expedition_title ?: null,
                            'seat_type' => $this->seat_type,
                            'total_price' => $this->return_total_price,
                            'status' => $this->status,
                            'price_snapshot' => $this->returnPriceBreakdown,
                        ]);
                        $resRet->logistics()->create([
                            'terrestrial_flight_id' => $this->return_terrestrial_flight_id ?: null,
                            'hotel_id' => $this->return_hotel_id ?: null,
                            'hotel_nights' => $this->return_hotel_nights ?: 0,
                            'training_included' => false,
                            'passport_management_included' => false,
                            'refund_insurance_included' => $this->refund_insurance_included,
                            'vip_transfer_included' => $this->return_vip_transfer_included,
                        ]);
                        $this->notifyGestorOfRequiredActions($resRet);
                    }
                });
                session()->flash('message', 'Reserva(s) creada(s) correctamente.');
                $this->dispatch('swal:alert', ['icon' => 'success', 'title' => 'Éxito', 'text' => 'Reserva(s) guardada(s) correctamente.']);
            } catch (\Throwable $e) {
                session()->flash('error', $e->getMessage());
                $this->dispatch('swal:alert', ['icon' => 'error', 'title' => 'Error Crítico', 'text' => $e->getMessage()]);
            }
        }
        $this->resetInputFields();
    }

    /**
     * Punto de entrada al editar desde la lista.
     * - Si la reserva pertenece a un grupo real → abre el modal maestro-detalle.
     * - Si es individual → carga directamente el formulario de edición.
     */
    public function openEditOrModal($reservationId): void
    {
        $res = Reservation::with(['passenger', 'spaceFlight'])->find($reservationId);
        if (!$res)
            return;

        $groupId = $res->booking_group_id;
        $members = Reservation::with(['passenger', 'spaceFlight'])
            ->where('booking_group_id', $groupId)
            ->where('is_adenda', false)
            ->whereNull('deleted_at')
            ->orderBy('created_at')
            ->get();

        if ($members->count() > 1) {
            $this->currentGroupId = $groupId;
            $this->groupEditMembers = $members->map(fn($m) => [
                'id' => $m->id,
                'passenger_name' => $m->passenger?->full_name ?? 'Sin nombre',
                'flight_code' => $m->spaceFlight?->flight_code ?? '—',
                'seat_type' => strtoupper($m->seat_type ?? 'NOVA'),
                'total_price' => $m->total_price,
                'payment_status' => $m->payment_status ?? 'pending',
                'status' => $m->status,
            ])->values()->toArray();
            $this->showGroupEditModal = true;
        } else {
            $this->groupMode = false;
            $this->edit($reservationId);
        }
    }

    /**
     * Carga en el formulario la reserva de un miembro específico del grupo.
     */
    public function editGroupMember(int $reservationId): void
    {
        $this->showGroupEditModal = false;
        $this->groupMode = false;
        $this->edit($reservationId);
    }

    /**
     * Ejecuta la eliminación según el scope.
     * Llamado desde el Centro de Mando (expedición).
     */
    public function executeGroupDelete(?int $reservationId = null, string $scope = 'single'): void
    {
        if ($scope === 'group' && $this->currentGroupId) {
            // Eliminar todo el grupo
            $groupRes = Reservation::where('booking_group_id', $this->currentGroupId)
                ->whereNull('deleted_at')
                ->get();

            foreach ($groupRes as $r) {
                if ($r->payment_status === 'paid') {
                    $r->update(['status' => 'Cancelada', 'payment_status' => 'refunded']);
                } else {
                    Reservation::where('parent_reservation_id', $r->id)->delete();
                    $r->delete();
                }
            }
            session()->flash('message', 'Expedición completa cancelada. Todos los pasajeros han sido dados de baja.');
        } elseif ($scope === 'single' && $reservationId) {
            // Reutilizar lógica individual existente
            $this->deleteId = $reservationId;
            $this->executeDelete();
            $this->deleteId = null;
        }

        $this->showGroupEditModal = false;
        $this->currentGroupId = null;
        $this->groupEditMembers = [];
        $this->resetInputFields();
    }

    public function openReceiptsModal($id): void
    {
        $res = Reservation::find($id);
        if (!$res)
            return;
        $parentId = $res->is_adenda ? $res->parent_reservation_id : $res->id;
        $chain = Reservation::where('id', $parentId)
            ->orWhere('parent_reservation_id', $parentId)
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'asc')
            ->get();

        $receipts = [];
        foreach ($chain as $idx => $r) {
            $type = $r->is_adenda ? 'Upgrade #' . $idx : 'Original';
            $receipts[] = [
                'type' => $type,
                'date' => $r->paid_at ? $r->paid_at->format('d/m/Y H:i') : ($r->updated_at->format('d/m/Y H:i')),
                'url' => $r->stripe_receipt_url,
                'amount' => $r->total_price
            ];
        }

        $this->receiptsList = $receipts;
        $this->showReceiptsModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function executeDelete()
    {
        if ($this->deleteId) {
            $res = Reservation::with('logistics', 'group')->find($this->deleteId);
            if ($res) {
                $groupSiblings = Reservation::where('booking_group_id', $res->booking_group_id)
                    ->where('id', '!=', $res->id)
                    ->where('is_adenda', false)
                    ->whereNotIn('status', ['Cancelada', 'Cancelled'])
                    ->get();

                $isMemberOfGroup = $groupSiblings->count() > 0;

                if ($res->payment_status === 'paid') {
                    $res->update([
                        'status' => 'Cancelada',
                        'seat_number' => null,
                        'payment_status' => 'refunded',
                    ]);

                    Reservation::where('parent_reservation_id', $res->id)
                        ->where('payment_status', 'paid')
                        ->update(['status' => 'Cancelada', 'payment_status' => 'refunded']);

                    $msg = $isMemberOfGroup
                        ? 'Pasajero cancelado del grupo. El resto de la expedición sigue activa.'
                        : 'Reserva PAGADA cancelada. El asiento ha sido liberado.';

                    $cliente = $res->user;
                    $gestorId = $cliente?->assigned_manager_id;
                    $clientName = $cliente?->name ?? 'Cliente';
                    $amount = number_format((float) $res->total_price, 2) . ' €';

                    if ($gestorId) {
                        $hasInsurance = $res->logistics?->refund_insurance_included ?? false;

                        if ($hasInsurance) {
                            Task::create([
                                'assigned_gestor_id' => $gestorId,
                                'created_by' => auth()->id(),
                                'title' => "Gestionar Reembolso — {$clientName}",
                                'description' => "La reserva de {$clientName} (importe: {$amount}) ha sido cancelada.\n\nEl cliente TIENE seguro de reembolso. Contacta con el cliente para tramitar el reembolso según las condiciones de su póliza.\n\nReserva ID: #{$res->id}",
                                'type' => 'passenger_issue',
                                'status' => 'Pendiente',
                                'priority' => 'urgente',
                                'payload' => ['reservation_id' => $res->id, 'client_name' => $clientName, 'amount' => $res->total_price, 'has_insurance' => true],
                            ]);
                            $msg .= ' Misión de reembolso asignada al gestor.';
                        } else {
                            Task::create([
                                'assigned_gestor_id' => $gestorId,
                                'created_by' => auth()->id(),
                                'title' => "Reserva Cancelada (sin seguro) — {$clientName}",
                                'description' => "La reserva de {$clientName} (importe: {$amount}) ha sido cancelada.\n\nEl cliente NO tiene seguro de reembolso. Comunica la baja al cliente según la política estándar.\n\nReserva ID: #{$res->id}",
                                'type' => 'passenger_issue',
                                'status' => 'Pendiente',
                                'priority' => 'media',
                                'payload' => ['reservation_id' => $res->id, 'client_name' => $clientName, 'amount' => $res->total_price, 'has_insurance' => false],
                            ]);
                            $msg .= ' Gestor notificado de la cancelación.';
                        }
                    }
                    $snap = $res->price_snapshot;
                    if (is_array($snap) && !empty($snap['shared_hotel_key']) && ($snap['is_hotel_payer'] ?? false)) {
                        $hotelToken = $snap['shared_hotel_key'];
                        $orphan = Reservation::where('booking_group_id', $res->booking_group_id)
                            ->where('id', '!=', $res->id)
                            ->whereJsonContains('price_snapshot->shared_hotel_key', $hotelToken)
                            ->where('is_adenda', false)
                            ->orderBy('created_at')
                            ->first();

                        if ($orphan) {
                            $orphan->update([
                                'discount_note' => 'ACCIÓN REQUERIDA: Reasignar coste de habitación compartida (el otro ocupante canceló).'
                            ]);
                            $msg .= 'Se ha marcado la reserva del compañero de habitación para reasignación de hotel.';

                            $companionGestorId = $orphan->user?->assigned_manager_id;
                            if ($companionGestorId) {
                                $companionName = $orphan->user?->name ?? 'Pasajero';
                                Task::create([
                                    'assigned_gestor_id' => $companionGestorId,
                                    'created_by' => auth()->id(),
                                    'title' => "Reasignar Hotel — {$companionName} queda sin habitación compartida",
                                    'description' => "El compañero de habitación de {$companionName} ha cancelado su reserva. Debes reasignar el coste del hotel o buscar una nueva opción de alojamiento para {$companionName}.\n\nReserva afectada ID: #{$orphan->id}",
                                    'type' => 'passenger_issue',
                                    'status' => 'Pendiente',
                                    'priority' => 'alta',
                                    'payload' => ['reservation_id' => $orphan->id, 'client_name' => $companionName],
                                ]);
                            }
                        }
                    }

                    session()->flash('message', $msg);
                } else {
                    Reservation::where('parent_reservation_id', $res->id)->delete();
                    $res->delete();
                    session()->flash('message', $isMemberOfGroup
                        ? 'Pasajero eliminado del grupo.'
                        : 'Reserva cancelada.');
                }
            }
        }
        $this->resetInputFields();
    }

    private function buildPriceSnapshot(): array
    {
        $spaceFlight = Flight::find($this->space_flight_id);
        $hotel = $this->hotel_id ? Hotel::find($this->hotel_id) : null;
        $tFlight = $this->terrestrial_flight_id ? TerrestrialFlight::find($this->terrestrial_flight_id) : null;

        $mult = 1;
        if ($this->seat_type === 'supernova')
            $mult = 2.5;

        $spacePrice = $spaceFlight ? round($spaceFlight->base_price * $mult, 2) : 0;
        $hotelPrice = $hotel && $this->hotel_nights > 0 ? round($hotel->price_per_night * $this->hotel_nights, 2) : 0;
        $terrestrialPrice = $tFlight ? round($tFlight->price, 2) : 0;
        $trainingFee = $this->training_included ? PriceLog::getCurrentPrice('training') : 0;
        $passportFee = $this->passport_management_included ? PriceLog::getCurrentPrice('passport_management') : 0;

        $vipFee = 0;
        if ($this->vip_transfer_included) {
            if ($this->terrestrial_flight_id) {
                if ($tFlight && $tFlight->destinationLocation) {
                    $vipFee = (float) ($tFlight->destinationLocation->transport_price ?? 0);
                }
            } else {
                $baseLocation = \App\Models\Location::find(1);
                if ($baseLocation) {
                    $vipFee = (float) ($baseLocation->transport_price ?? 0);
                }
            }
        }

        $baseAmount = $spacePrice + $hotelPrice + $terrestrialPrice + $trainingFee + $passportFee + $vipFee;

        $insuranceFee = 0;
        if ($this->refund_insurance_included) {
            $insurancePct = PriceLog::getCurrentPrice('refund_insurance');
            $insuranceFee = round($baseAmount * ($insurancePct / 100), 2);
        }

        $subtotal = $baseAmount + $insuranceFee;
        $discountAmt = $this->discount_applied ? round($subtotal * 0.10, 2) : 0;
        $afterCertDiscount = round($subtotal - $discountAmt, 2);

        $adjType = $this->manual_adjustment_type;
        $adjValue = (float) $this->manual_adjustment_value;
        $manualAdj = 0;
        if ($adjType === 'pct' && $adjValue > 0) {
            $manualAdj = round($afterCertDiscount * ($adjValue / 100), 2);
        } elseif ($adjType === 'fixed' && $adjValue > 0) {
            $manualAdj = min($afterCertDiscount, $adjValue);
        }
        $total = max(0, round($afterCertDiscount - $manualAdj, 2));

        return [
            'seat_type' => $this->seat_type,
            'seat_multiplier' => $mult,
            'space_flight_price' => $spacePrice,
            'hotel_price' => $hotelPrice,
            'hotel_nights' => (int) $this->hotel_nights,
            'terrestrial_price' => $terrestrialPrice,
            'training_fee' => $trainingFee,
            'passport_fee' => $passportFee,
            'vip_transfer_fee' => $vipFee,
            'insurance_fee' => $insuranceFee,
            'subtotal' => $subtotal,
            'discount_pct' => $this->discount_applied ? 10 : 0,
            'discount_amount' => $discountAmt,
            'after_cert_discount' => $afterCertDiscount,
            'manual_adjustment_applied' => $manualAdj > 0,
            'manual_adjustment_type' => $adjType,
            'manual_adjustment_value' => $adjValue,
            'manual_adjustment_amount' => $manualAdj,
            'discount_note' => $this->discount_note ?: null,
            'total' => $total,
            'snapshot_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Launch a Stripe Checkout Session.
     * En modo grupo: cobra el total agregado del grupo en una sola sesión.
     * En modo individual: comportamiento original.
     */
    public function initiatePayment($reservationId)
    {
        $res = Reservation::with('spaceFlight')->find($reservationId);
        if (!$res)
            return;

        Stripe::setApiKey(config('stripe.secret'));

        $groupId = $res->booking_group_id;
        $isGroup = false;
        $amountCents = 0;
        $description = '';

        $groupMembers = Reservation::where('booking_group_id', $groupId)
            ->whereNotIn('payment_status', ['paid'])
            ->whereNotIn('status', ['Cancelada', 'Cancelled'])
            ->get();

        if ($groupMembers->count() === 0) {
            session()->flash('error', 'No hay pagos pendientes para este grupo de reserva.');
            return;
        }

        $groupTotal = $groupMembers->sum('total_price');
        $amountCents = (int) round($groupTotal * 100);

        if ($groupMembers->count() > 1) {
            $isGroup = true;
            $adendaCount = $groupMembers->where('is_adenda', true)->count();
            $description = 'Expedición Grupal — ' . $groupMembers->where('is_adenda', false)->count() . ' pasajeros';
            if ($adendaCount > 0) {
                $description .= " + {$adendaCount} Upgrades";
            }
            $description .= ' | Vuelo ' . ($res->spaceFlight?->flight_code ?? 'N/A');
        } else {
            $pendingItem = $groupMembers->first();
            $isGroup = false;
            $description = ($pendingItem->is_adenda ? '[UPGRADE] ' : '') . 'Vuelo ' . ($pendingItem->spaceFlight?->flight_code ?? 'N/A')
                . ' | Clase ' . ($pendingItem->seat_type ?? 'Estándar')
                . ($pendingItem->discount_applied ? ' | Descuento 10% Certificado' : '');
        }

        if ($amountCents < 50)
            $amountCents = 50;

        // Limite de Stripe
        $isCapped = false;
        $originalAmount = $amountCents / 100;
        if ($amountCents > 99999999) {
            $amountCents = 99999999;
            $isCapped = true;
        }

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $amountCents,
                        'product_data' => [
                            'name' => 'Reserva IRIS — ' . strtoupper(substr($res->id_locator, 0, 8)) . ($isCapped ? ' (Límite Stripe)' : ''),
                            'description' => ($isCapped ? '[PRECIO REAL: ' . number_format($originalAmount, 2, ',', '.') . '€] ' : '') . $description,
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => [
                'reservation_id' => $res->id,
                'reservation_locator' => $res->id_locator,
                'booking_group_id' => $groupId,
                'is_group_payment' => $isGroup ? '1' : '0',
            ],
        ]);
        Reservation::where('booking_group_id', $groupId)
            ->where('payment_status', '!=', 'paid')
            ->update([
                'stripe_session_id' => $session->id,
                'payment_status' => 'pending',
            ]);

        $this->redirect($session->url);
    }

    public function applyTemporalCoherence(): void
    {
        $this->temporalWarnings = [];
        $this->smartSuggestions = [];

        $outboundLaunch = $this->space_flight_id ? Flight::find($this->space_flight_id) : null;
        if (!$outboundLaunch)
            return;

        $launchDate = \Carbon\Carbon::parse($outboundLaunch->departure_date);

        if ($this->groupMode) {
            foreach ($this->selectedPassengers as $idx => $pData) {
                if ($pData['terrestrial_flight_id']) {
                    $tf = TerrestrialFlight::find($pData['terrestrial_flight_id']);
                    if ($tf) {
                        $this->validatePassengerTimeline($idx, $tf, $launchDate, $pData['training_included']);
                    }
                }
            }
        } else {
            if ($this->terrestrial_flight_id) {
                $tf = TerrestrialFlight::find($this->terrestrial_flight_id);
                if ($tf) {
                    $this->validatePassengerTimeline(0, $tf, $launchDate, $this->training_included);
                }
            }
        }
    }

    private function validatePassengerTimeline(int $idx, $terrestrialFlight, $launchDate, bool $trainingIncluded): void
    {
        $arrival = \Carbon\Carbon::parse($terrestrialFlight->arrival_datetime);
        $diffDays = $arrival->diffInDays($launchDate, false);
        $warnings = [];

        if ($diffDays > 90) {
            $warnings[] = "La llegada a base ocurre con demasiada antelación (" . $diffDays . " días). No debería superar los 3 meses.";
        }
        if ($diffDays < 0) {
            $warnings[] = "Error crítico: El vuelo terrestre llega después del lanzamiento espacial.";
        }

        if ($trainingIncluded) {
            if ($diffDays < 4) {
                $warnings[] = "Si incluye Iris Training, requiere al menos 4 días de antelación para descanso y validación. Tienes " . $diffDays . " días.";
            }
        }

        if ($diffDays > 0) {
            $this->smartSuggestions[$idx] = (int) ceil($diffDays);
        }

        if (!empty($warnings)) {
            $this->temporalWarnings[$idx] = $warnings;
        }
    }

    public function applySmartSuggestion(int $idx): void
    {
        $nights = $this->smartSuggestions[$idx] ?? 0;
        if ($nights <= 0)
            return;

        if ($this->groupMode && isset($this->selectedPassengers[$idx])) {
            $this->selectedPassengers[$idx]['hotel_nights'] = $nights;
            $this->recalculatePassengerPrice($idx);
            $this->calculateGroupTotal();
        } else {
            $this->hotel_nights = $nights;
            $this->calculateTotalPrice();
        }
    }
}
