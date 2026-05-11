<?php

namespace App\Livewire\Gestor;

use App\Models\Destination;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Location;
use App\Models\Passenger;
use App\Models\PriceLog;
use App\Models\Reservation;
use App\Models\ReservationLogistic;
use App\Models\Task;
use App\Models\TerrestrialFlight;
use App\Models\User;
use App\Traits\HasResponsivePagination;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentLinkMail;

class GestorReservations extends Component
{
    use WithPagination, HasResponsivePagination;

    public $search = '';
    public $filterStatus = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public ?Reservation $detailReservation = null;
    public bool $showDetailModal = false;
    public bool $showModifyModal = false;
    public ?Reservation $modifyingReservation = null;
    public ?int $modify_flight_id = null;
    public string $modifySearchCode = '';
    public string $modifySearchDest = '';
    public bool $showModifyResults = false;
    public ?string $selectedModifyFlightLabel = null;

    public bool $showDeleteModal = false;
    public ?int $deleteId = null;
    public bool $showPayLinkModal = false;
    public ?int $payLinkReservationId = null;
    public string $payLinkUrl = '';
    public float $payLinkAmount = 0;
    public bool $showReceiptsModal = false;
    public array $receiptsList = [];

    protected $queryString = ['search', 'filterStatus', 'sortField', 'sortDirection'];

    public function render()
    {
        $reservations = Reservation::whereHas('user', function ($q) {
            $q->where('assigned_manager_id', auth()->id());
        })
            ->with(['user', 'passenger', 'spaceFlight.destination', 'logistics'])
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('id_locator', 'like', '%' . $this->search . '%')
                        ->orWhereHas('passenger', function ($pq) {
                            $pq->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('lastname', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('user', function ($uq) {
                            $uq->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->getPerPage());

        return view('livewire.gestor.reservations', [
            'reservations' => $reservations
        ])->layout('layouts.gestor');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteReservation(): void
    {
        $res = $this->getMyReservation($this->deleteId);
        $res->delete();

        session()->flash('message', 'Reserva eliminada.');
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function viewDetail(int $id): void
    {
        $this->detailReservation = $this->getMyReservation($id)
            ->load(['user', 'passenger', 'spaceFlight.destination', 'spaceFlight.origin', 'logistics.hotel', 'logistics.terrestrialFlight', 'adendas']);
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailReservation = null;
    }

    public function viewReceipts(int $id): void
    {
        $res = $this->getMyReservation($id);
        $this->receiptsList = $res->stripe_receipts ?? [];

        if ($res->stripe_receipt_url && empty($this->receiptsList)) {
            $this->receiptsList[] = [
                'type' => 'payment',
                'amount' => $res->total_price,
                'date' => $res->paid_at ? $res->paid_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                'description' => 'Pago Original (Legacy)',
                'url' => $res->stripe_receipt_url
            ];
        }

        $this->showReceiptsModal = true;
    }

    public function closeReceiptsModal(): void
    {
        $this->showReceiptsModal = false;
        $this->receiptsList = [];
    }

    public function sendReminderEmail(): void
    {
        if (!$this->detailReservation)
            return;
        session()->flash('detail_message', 'Recordatorio enviado con éxito al cliente.');
    }

    public function processManualPayment(): void
    {
        if (!$this->detailReservation)
            return;

        $this->detailReservation->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'status' => 'Confirmada'
        ]);

        session()->flash('detail_message', 'Pago procesado manualmente.');
        $this->viewDetail($this->detailReservation->id);
    }

    public function openModifyModal(int $id): void
    {
        $this->modifyingReservation = $this->getMyReservation($id);
        $this->modify_flight_id = $this->modifyingReservation->space_flight_id;
        $this->selectedModifyFlightLabel = "#{$this->modifyingReservation->spaceFlight?->flight_code} | {$this->modifyingReservation->spaceFlight?->destination?->name}";
        $this->showModifyModal = true;
    }

    public function selectModifyFlight(int $id, string $label): void
    {
        $this->modify_flight_id = $id;
        $this->selectedModifyFlightLabel = $label;
        $this->showModifyResults = false;
    }

    public function executeModification(): void
    {
        $this->validate(['modify_flight_id' => 'required|exists:flights,id']);

        $this->modifyingReservation->update([
            'space_flight_id' => $this->modify_flight_id
        ]);

        session()->flash('message', 'Vuelo de misión modificado con éxito.');
        $this->showModifyModal = false;
    }

    public function getFilteredModifyFlightsProperty()
    {
        return Flight::with(['destination', 'origin'])
            ->where('departure_date', '>', now())
            ->when($this->modifySearchCode, fn($q) => $q->where('flight_code', 'like', '%' . $this->modifySearchCode . '%'))
            ->when($this->modifySearchDest, function ($q) {
                $q->whereHas('destination', fn($sq) => $sq->where('name', 'like', '%' . $this->modifySearchDest . '%'));
            })
            ->limit(5)
            ->get();
    }

    // ── Crear Reserva ─────────────────────────────────────────────────
    public bool $showCreateModal = false;
    public ?int $create_client_id = null;
    public ?int $create_flight_id = null;

    // Búsqueda IDA (Radar Completo)
    public string $flightSearchCode = '';
    public string $flightSearchOrigin = '';
    public string $flightSearchDest = '';
    public string $flightSearchDep = '';
    public string $flightSearchArr = '';
    public bool $showFlightResults = false;
    public ?string $selectedFlightLabel = null;

    // Búsqueda VUELTA (Radar Completo)
    public bool $create_has_return_flight = false;
    public ?int $create_return_flight_id = null;
    public string $returnSearchCode = '';
    public string $returnSearchOrigin = '';
    public string $returnSearchDest = '';
    public string $returnSearchDep = '';
    public string $returnSearchArr = '';
    public bool $showReturnFlightResults = false;
    public ?string $selectedReturnFlightLabel = null;

    // Búsqueda Conexión Terrestre (Creación)
    public string $tFlightSearchID = '';
    public string $tFlightSearchOrigin = '';
    public string $tFlightSearchDest = '';
    public string $tFlightSearchDate = '';

    // Advertencias Temporales
    public array $temporalConflicts = []; // [pax_id => 'mensaje']

    public float $create_total_price = 0;

    public array $create_selected_passengers = [];
    public $clientPassengers = [];

    // Confirmación Final
    public bool $showConfirmModal = false;

    public function openCreateModal(): void
    {
        $this->resetCreateFields();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->showConfirmModal = false;
        $this->resetCreateFields();
    }

    public function openConfirmModal(): void
    {
        $this->checkTemporalConflicts();

        if (!empty($this->temporalConflicts)) {
            session()->flash('error', 'No se puede proceder: Hay conflictos temporales detectados en la agenda de los pasajeros.');
            return;
        }

        $this->validate([
            'create_client_id' => 'required|exists:users,id',
            'create_flight_id' => 'nullable|exists:flights,id',
            'create_selected_passengers' => 'required|array|min:1',
            'create_return_flight_id' => $this->create_has_return_flight ? 'required|exists:flights,id' : 'nullable',
        ]);

        $this->showConfirmModal = true;
    }

    public function resetCreateFields(): void
    {
        $this->create_client_id = null;
        $this->create_flight_id = null;
        $this->flightSearchCode = '';
        $this->flightSearchDest = '';
        $this->flightSearchDate = '';
        $this->showFlightResults = false;
        $this->selectedFlightLabel = null;

        $this->create_has_return_flight = false;
        $this->create_return_flight_id = null;
        $this->returnSearchCode = '';
        $this->returnSearchDest = '';
        $this->returnSearchDate = '';
        $this->showReturnFlightResults = false;
        $this->selectedReturnFlightLabel = null;

        $this->create_selected_passengers = [];
        $this->create_total_price = 0;
        $this->clientPassengers = [];
    }

    public function updated($property): void
    {
        if (str_starts_with($property, 'flightSearch') || str_starts_with($property, 'returnSearch')) {
            if (str_contains($property, 'flight'))
                $this->showFlightResults = true;
            if (str_contains($property, 'return'))
                $this->showReturnFlightResults = true;
        }

        if (str_starts_with($property, 'create_')) {
            if (str_contains($property, 'hotel_checkin') || str_contains($property, 'hotel_checkout')) {
                foreach ($this->create_selected_passengers as $idx => $p) {
                    if (!empty($p['hotel_checkin']) && !empty($p['hotel_checkout'])) {
                        $start = \Carbon\Carbon::parse($p['hotel_checkin']);
                        $end = \Carbon\Carbon::parse($p['hotel_checkout']);
                        $this->create_selected_passengers[$idx]['hotel_nights'] = max(1, $start->diffInDays($end));
                    }
                }
            }
            $this->calculateCreatePrice();
            $this->checkTemporalConflicts();
        }

        if (str_starts_with($property, 'upgrade_hotel_')) {
            if ($this->upgrade_hotel_checkin && $this->upgrade_hotel_checkout) {
                $start = \Carbon\Carbon::parse($this->upgrade_hotel_checkin);
                $end = \Carbon\Carbon::parse($this->upgrade_hotel_checkout);
                $this->upgrade_hotel_nights = max(1, $start->diffInDays($end));
            }
            $this->calculateUpgradePrice();
        }
    }


    public function updatedCreateClientId($value): void
    {
        if ($value) {
            $this->clientPassengers = Passenger::where('user_id', $value)->get();
            $this->create_selected_passengers = [];
        } else {
            $this->clientPassengers = [];
        }
    }

    public function addPassengerToGroup(int $passengerId): void
    {
        foreach ($this->create_selected_passengers as $p) {
            if ($p['passenger_id'] === $passengerId)
                return;
        }

        $pax = Passenger::find($passengerId);
        if (!$pax)
            return;

        $this->create_selected_passengers[] = [
            'passenger_id' => $passengerId,
            'name' => $pax->full_name,
            'seat_type' => null,
            'hotel_id' => null,
            'hotel_nights' => 0,
            'hotel_checkin' => null,
            'hotel_checkout' => null,
            'terrestrial_flight_id' => null,
            'training_included' => false,
            'vip_transfer_included' => false,
            'refund_insurance_included' => false,
            'passport_management_included' => false,
            'total' => 0,
            'return_total' => 0
        ];

        $this->calculateCreatePrice();
    }

    public function removePassengerFromGroup(int $index): void
    {
        unset($this->create_selected_passengers[$index]);
        $this->create_selected_passengers = array_values($this->create_selected_passengers);
        $this->calculateCreatePrice();
    }


    public function calculateCreatePrice(): void
    {
        if (empty($this->create_selected_passengers)) {
            $this->create_total_price = 0;
            return;
        }

        $flight = $this->create_flight_id ? Flight::find($this->create_flight_id) : null;
        $returnFlight = ($this->create_has_return_flight && $this->create_return_flight_id)
            ? Flight::find($this->create_return_flight_id)
            : null;

        $grandTotal = 0;

        foreach ($this->create_selected_passengers as $index => $p) {
            $subtotalIda = 0;
            if ($flight && $p['seat_type']) {
                $mult = $p['seat_type'] === 'supernova' ? 2.5 : 1;
                $subtotalIda = round($flight->base_price * $mult, 2);
            }

            if ($p['hotel_id'] && $p['hotel_nights'] > 0) {
                $hotel = Hotel::find($p['hotel_id']);
                if ($hotel)
                    $subtotalIda += round($hotel->price_per_night * $p['hotel_nights'], 2);
            }

            if ($p['terrestrial_flight_id']) {
                $tf = TerrestrialFlight::find($p['terrestrial_flight_id']);
                if ($tf)
                    $subtotalIda += round($tf->price, 2);
            }

            if ($p['training_included'])
                $subtotalIda += PriceLog::getCurrentPrice('training');
            if ($p['passport_management_included'])
                $subtotalIda += PriceLog::getCurrentPrice('passport_management');
            if ($p['vip_transfer_included']) {
                $base = Location::find(1);
                $subtotalIda += $base ? $base->transport_price : 0;
            }

            if ($p['refund_insurance_included']) {
                $pct = PriceLog::getCurrentPrice('refund_insurance');
                $subtotalIda += round($subtotalIda * ($pct / 100), 2);
            }

            $subtotalVuelta = 0;
            if ($returnFlight) {
                $subtotalVuelta = round($returnFlight->base_price * $mult, 2);
                if ($p['refund_insurance_included']) {
                    $pct = PriceLog::getCurrentPrice('refund_insurance');
                    $subtotalVuelta += round($subtotalVuelta * ($pct / 100), 2);
                }
            }

            $this->create_selected_passengers[$index]['total'] = $subtotalIda;
            $this->create_selected_passengers[$index]['return_total'] = $subtotalVuelta;
            $grandTotal += ($subtotalIda + $subtotalVuelta);
        }

        $this->create_total_price = $grandTotal;
    }

    public function saveNewReservation(): void
    {
        $this->validate([
            'create_client_id' => 'required|exists:users,id',
            'create_flight_id' => 'nullable|exists:flights,id',
            'create_selected_passengers' => 'required|array|min:1',
            'create_return_flight_id' => $this->create_has_return_flight ? 'required|exists:flights,id' : 'nullable',
        ]);

        $this->calculateCreatePrice();
        $bookingGroupId = (string) \Illuminate\Support\Str::uuid();

        foreach ($this->create_selected_passengers as $p) {
            $resOut = Reservation::create([
                'user_id' => $this->create_client_id,
                'passenger_id' => $p['passenger_id'],
                'space_flight_id' => $this->create_flight_id ?: null,
                'seat_type' => $p['seat_type'] ?: 'none',
                'total_price' => $p['total'],
                'status' => 'Pendiente',
                'payment_status' => 'pending',
                'booking_group_id' => $bookingGroupId,
            ]);

            ReservationLogistic::create([
                'reservation_id' => $resOut->id,
                'hotel_id' => $p['hotel_id'],
                'hotel_nights' => $p['hotel_nights'],
                'hotel_check_in' => $p['hotel_checkin'],
                'hotel_check_out' => $p['hotel_checkout'],
                'terrestrial_flight_id' => $p['terrestrial_flight_id'],
                'training_included' => $p['training_included'],
                'vip_transfer_included' => $p['vip_transfer_included'],
                'refund_insurance_included' => $p['refund_insurance_included'],
                'passport_management_included' => $p['passport_management_included'],
            ]);

            if ($this->create_has_return_flight && $this->create_return_flight_id) {
                $resIn = Reservation::create([
                    'user_id' => $this->create_client_id,
                    'passenger_id' => $p['passenger_id'],
                    'space_flight_id' => $this->create_return_flight_id,
                    'seat_type' => $p['seat_type'],
                    'total_price' => $p['return_total'],
                    'status' => 'Pendiente',
                    'payment_status' => 'pending',
                    'booking_group_id' => $bookingGroupId,
                ]);
                ReservationLogistic::create([
                    'reservation_id' => $resIn->id,
                    'refund_insurance_included' => $p['refund_insurance_included'],
                    'vip_transfer_included' => $p['vip_transfer_included'],
                ]);
            }

            // AUTO-TASKS
            if ($p['passport_management_included']) {
                Task::create([
                    'assigned_gestor_id' => auth()->id(),
                    'created_by' => auth()->id(),
                    'title' => "Gestionar Pasaporte: {$p['name']}",
                    'description' => "Trámite de documentación para reserva {$resOut->id_locator}",
                    'type' => 'general',
                    'status' => 'Pendiente',
                    'priority' => 'media',
                    'payload' => ['reservation_id' => $resOut->id]
                ]);
            }
            if ($p['training_included']) {
                Task::create([
                    'assigned_gestor_id' => auth()->id(),
                    'created_by' => auth()->id(),
                    'title' => "Coordinar Entrenamiento: {$p['name']}",
                    'description' => "Misión {$resOut->spaceFlight?->flight_code}",
                    'type' => 'general',
                    'status' => 'Pendiente',
                    'priority' => 'media',
                    'payload' => ['reservation_id' => $resOut->id]
                ]);
            }
            if ($p['vip_transfer_included']) {
                Task::create([
                    'assigned_gestor_id' => auth()->id(),
                    'created_by' => auth()->id(),
                    'title' => "Logística VIP: {$p['name']}",
                    'description' => "Transfer terrestre para vuelo {$resOut->id_locator}",
                    'type' => 'general',
                    'status' => 'Pendiente',
                    'priority' => 'baja',
                ]);
            }
        }

        session()->flash('message', 'Reserva grupal completada (Ida/Vuelta).');
        $this->closeCreateModal();
    }

    // ── Upgrade Reserva ───────────────────────────────────────────────
    public bool $showUpgradeModal = false;
    public ?int $upgradeReservationId = null;
    public float $upgradePriceDifference = 0;

    // Extras para el upgrade
    public bool $upgrade_to_supernova = false;
    public ?int $upgrade_hotel_id = null;
    public int $upgrade_hotel_nights = 1;
    public ?int $upgrade_terrestrial_flight_id = null;
    public bool $upgrade_vip_transfer = false;
    public bool $upgrade_training = false;
    public bool $upgrade_passport = false;
    public bool $upgrade_insurance = false;

    // Búsqueda en upgrade
    public string $upgradeHotelSearch = '';
    public ?string $upgrade_hotel_checkin = null;
    public ?string $upgrade_hotel_checkout = null;

    // Búsqueda Conexión Terrestre Detallada
    public string $upgradeT_ID = '';
    public string $upgradeT_Origin = '';
    public string $upgradeT_Dest = '';
    public string $upgradeT_Date = '';

    public ?string $selectedUpgradeHotelLabel = null;
    public ?string $selectedUpgradeTerrestrialLabel = null;
    public bool $showUpgradeHotelResults = false;
    public bool $showUpgradeTerrestrialResults = false;

    public function openUpgradeModal(int $id): void
    {
        $res = $this->getMyReservation($id);
        if ($res->status === 'Cancelada' || $res->status === 'GO') {
            session()->flash('error', 'Esta reserva no es elegible para modificaciones.');
            return;
        }

        $this->upgradeReservationId = $id;

        // Reset extras
        $this->upgrade_to_supernova = false;
        $this->upgrade_hotel_id = null;
        $this->upgrade_hotel_nights = 1;
        $this->upgrade_hotel_checkin = null;
        $this->upgrade_hotel_checkout = null;
        $this->upgrade_terrestrial_flight_id = null;
        $this->upgrade_vip_transfer = false;
        $this->upgrade_training = false;
        $this->upgrade_passport = false;
        $this->upgrade_insurance = false;
        $this->selectedUpgradeHotelLabel = null;
        $this->selectedUpgradeTerrestrialLabel = null;
        $this->upgradeHotelSearch = '';
        $this->upgradeT_ID = '';
        $this->upgradeT_Origin = '';
        $this->upgradeT_Dest = '';
        $this->upgradeT_Date = '';

        $this->calculateUpgradePrice();
        $this->showUpgradeModal = true;
    }

    public function selectUpgradeHotel(int $id, string $label): void
    {
        $this->upgrade_hotel_id = $id;
        $this->selectedUpgradeHotelLabel = $label;
        $this->showUpgradeHotelResults = false;
        $this->calculateUpgradePrice();
    }

    public function selectUpgradeTerrestrial(int $id, string $label): void
    {
        $this->upgrade_terrestrial_flight_id = $id;
        $this->selectedUpgradeTerrestrialLabel = $label;
        $this->showUpgradeTerrestrialResults = false;
        $this->calculateUpgradePrice();
    }

    public function calculateUpgradePrice(): void
    {
        $res = $this->getMyReservation($this->upgradeReservationId);
        $totalExtras = 0;

        // 1. Upgrade de asiento
        if ($this->upgrade_to_supernova && $res->seat_type !== 'supernova') {
            $flight = $res->spaceFlight;
            $supernovaPrice = round($flight->base_price * 2.5, 2);
            $totalExtras += max(0, $supernovaPrice - $res->total_price);
        }

        // 2. Hotel
        if ($this->upgrade_hotel_id) {
            $hotel = Hotel::find($this->upgrade_hotel_id);
            if ($hotel) {
                $totalExtras += $hotel->price_per_night * $this->upgrade_hotel_nights;
            }
        }

        // 3. Vuelo Terrestre
        if ($this->upgrade_terrestrial_flight_id) {
            $tf = TerrestrialFlight::find($this->upgrade_terrestrial_flight_id);
            if ($tf) {
                $totalExtras += $tf->price;
            }
        }

        // 4. Servicios Booleano
        if ($this->upgrade_vip_transfer) {
            // Buscamos precio de transporte en la localización del vuelo terrestre o base
            $tf = TerrestrialFlight::find($this->upgrade_terrestrial_flight_id);
            $loc = $tf ? $tf->destinationLocation : Location::find(1);
            $totalExtras += $loc?->transport_price ?? 0;
        }

        if ($this->upgrade_training)
            $totalExtras += PriceLog::getCurrentPrice('training');
        if ($this->upgrade_passport)
            $totalExtras += PriceLog::getCurrentPrice('passport_management');

        // 5. Seguro (sobre los nuevos extras)
        if ($this->upgrade_insurance) {
            $pct = PriceLog::getCurrentPrice('refund_insurance');
            $totalExtras += $totalExtras * ($pct / 100);
        }

        $this->upgradePriceDifference = round($totalExtras, 2);
    }

    public function getFilteredUpgradeHotelsProperty()
    {
        if (strlen($this->upgradeHotelSearch) < 2)
            return collect();
        return Hotel::where('name', 'like', '%' . $this->upgradeHotelSearch . '%')
            ->limit(5)->get();
    }

    public function getFilteredUpgradeTerrestrialsProperty()
    {
        if (!$this->upgradeT_ID && !$this->upgradeT_Origin && !$this->upgradeT_Dest && !$this->upgradeT_Date)
            return collect();

        return TerrestrialFlight::with(['originLocation', 'destinationLocation'])
            ->when($this->upgradeT_ID, fn($q) => $q->where('flight_number', 'like', '%' . $this->upgradeT_ID . '%'))
            ->when($this->upgradeT_Origin, function ($q) {
                $q->whereHas('originLocation', fn($sq) => $sq->where('name', 'like', '%' . $this->upgradeT_Origin . '%'));
            })
            ->when($this->upgradeT_Dest, function ($q) {
                $q->whereHas('destinationLocation', fn($sq) => $sq->where('name', 'like', '%' . $this->upgradeT_Dest . '%'));
            })
            ->when($this->upgradeT_Date, function ($q) {
                $q->whereRaw("DATE(departure_datetime) = ?", [$this->upgradeT_Date]);
            })
            ->limit(10)->get();
    }

    public function executeUpgrade(): void
    {
        $res = $this->getMyReservation($this->upgradeReservationId);
        $difference = $this->upgradePriceDifference;

        if ($difference <= 0) {
            session()->flash('error', 'No se han seleccionado cambios o servicios adicionales.');
            return;
        }

        if ($res->payment_status === 'paid') {
            $adenda = Reservation::create([
                'user_id' => $res->user_id,
                'passenger_id' => $res->passenger_id,
                'space_flight_id' => $res->space_flight_id,
                'booking_group_id' => $res->booking_group_id,
                'is_adenda' => true,
                'parent_reservation_id' => $res->id,
                'seat_type' => $this->upgrade_to_supernova ? 'supernova' : $res->seat_type,
                'total_price' => $difference,
                'payment_status' => 'pending',
                'status' => 'Confirmada',
            ]);

            ReservationLogistic::create([
                'reservation_id' => $adenda->id,
                'hotel_id' => $this->upgrade_hotel_id,
                'hotel_nights' => $this->upgrade_hotel_nights,
                'hotel_check_in' => $this->upgrade_hotel_checkin,
                'hotel_check_out' => $this->upgrade_hotel_checkout,
                'terrestrial_flight_id' => $this->upgrade_terrestrial_flight_id,
                'vip_transfer_included' => $this->upgrade_vip_transfer,
                'training_included' => $this->upgrade_training,
                'passport_management_included' => $this->upgrade_passport,
                'refund_insurance_included' => $this->upgrade_insurance,
            ]);

            if ($this->upgrade_to_supernova) {
                $res->update(['seat_type' => 'supernova']);
            }

            $this->showUpgradeModal = false;
            $this->generatePayLink($adenda->id);
            session()->flash('pay_message', 'Adenda generada por ' . number_format($difference, 2) . ' € incluyendo los nuevos servicios.');
        } else {
            $newTotal = $res->total_price + $difference;
            $res->update([
                'seat_type' => $this->upgrade_to_supernova ? 'supernova' : $res->seat_type,
                'total_price' => $newTotal,
            ]);

            $logistic = $res->logistics ?: new ReservationLogistic(['reservation_id' => $res->id]);

            if ($this->upgrade_hotel_id) {
                $logistic->hotel_id = $this->upgrade_hotel_id;
                $logistic->hotel_nights = $this->upgrade_hotel_nights;
                $logistic->hotel_check_in = $this->upgrade_hotel_checkin;
                $logistic->hotel_check_out = $this->upgrade_hotel_checkout;
            }
            if ($this->upgrade_terrestrial_flight_id)
                $logistic->terrestrial_flight_id = $this->upgrade_terrestrial_flight_id;
            if ($this->upgrade_vip_transfer)
                $logistic->vip_transfer_included = true;
            if ($this->upgrade_training)
                $logistic->training_included = true;
            if ($this->upgrade_passport)
                $logistic->passport_management_included = true;
            if ($this->upgrade_insurance)
                $logistic->refund_insurance_included = true;

            $logistic->save();

            $this->showUpgradeModal = false;
            $this->generatePayLink($res->id);
        }

        session()->flash('message', 'Upgrade y servicios extra procesados correctamente.');
        $this->upgradeReservationId = null;
    }

    public function checkTemporalConflicts(): void
    {
        $this->temporalConflicts = [];
        if (!$this->create_flight_id)
            return;

        $flight = Flight::find($this->create_flight_id);
        $returnFlight = $this->create_has_return_flight ? Flight::find($this->create_return_flight_id) : null;
        if (!$flight)
            return;

        foreach ($this->create_selected_passengers as $p) {
            $pax = Passenger::find($p['passenger_id']);
            if (!$pax)
                continue;

            if ($pax->hasConflictOnDate($flight->departure_date)) {
                $this->temporalConflicts[$p['passenger_id']] = "Conflicto en IDA: {$flight->departure_date->format('d/m/Y')}";
            }
            if ($returnFlight && $pax->hasConflictOnDate($returnFlight->departure_date)) {
                $this->temporalConflicts[$p['passenger_id']] = "Conflicto en VUELTA: {$returnFlight->departure_date->format('d/m/Y')}";
            }
        }
    }

    public function selectFlight(int $id, string $label): void
    {
        $this->create_flight_id = $id;
        $this->selectedFlightLabel = $label;
        $this->showFlightResults = false;
        $this->calculateCreatePrice();
        $this->checkTemporalConflicts();
    }

    public function selectReturnFlight(int $id, string $label): void
    {
        $this->create_return_flight_id = $id;
        $this->selectedReturnFlightLabel = $label;
        $this->showReturnFlightResults = false;
        $this->calculateCreatePrice();
        $this->checkTemporalConflicts();
    }

    public function getFilteredFlightsProperty()
    {
        if (!$this->flightSearchCode && !$this->flightSearchOrigin && !$this->flightSearchDest && !$this->flightSearchDep && !$this->flightSearchArr)
            return collect();

        return Flight::with(['destination', 'origin'])
            ->where('departure_date', '>', now())
            ->when($this->flightSearchCode, fn($q) => $q->where('flight_code', 'like', '%' . $this->flightSearchCode . '%'))
            ->when($this->flightSearchOrigin, function ($q) {
                $q->whereHas('origin', fn($sq) => $sq->where('name', 'like', '%' . $this->flightSearchOrigin . '%'));
            })
            ->when($this->flightSearchDest, function ($q) {
                $q->whereHas('destination', fn($sq) => $sq->where('name', 'like', '%' . $this->flightSearchDest . '%'));
            })
            ->when($this->flightSearchDep, function ($q) {
                $q->whereRaw("DATE_FORMAT(departure_date, '%d/%m/%Y') LIKE ?", ["%{$this->flightSearchDep}%"]);
            })
            ->when($this->flightSearchArr, function ($q) {
                $q->whereRaw("DATE_FORMAT(arrival_date, '%d/%m/%Y') LIKE ?", ["%{$this->flightSearchArr}%"]);
            })
            ->take(10)
            ->get();
    }

    public function getFilteredReturnFlightsProperty()
    {
        if (!$this->returnSearchCode && !$this->returnSearchOrigin && !$this->returnSearchDest && !$this->returnSearchDep && !$this->returnSearchArr)
            return collect();

        $q = Flight::with(['destination', 'origin'])
            ->where('departure_date', '>', now());

        if ($this->create_flight_id) {
            $ida = Flight::find($this->create_flight_id);
            if ($ida && $ida->destination_id) {
                $q->where('origin_id', $ida->destination_id);
                $minDate = $ida->arrival_date ?? $ida->departure_date;
                $q->where('departure_date', '>', $minDate);
            }
        }

        return $q->when($this->returnSearchCode, fn($q) => $q->where('flight_code', 'like', '%' . $this->returnSearchCode . '%'))
            ->when($this->returnSearchOrigin, function ($q) {
                $q->whereHas('origin', fn($sq) => $sq->where('name', 'like', '%' . $this->returnSearchOrigin . '%'));
            })
            ->when($this->returnSearchDest, function ($q) {
                $q->whereHas('destination', fn($sq) => $sq->where('name', 'like', '%' . $this->returnSearchDest . '%'));
            })
            ->when($this->returnSearchDep, function ($q) {
                $q->whereRaw("DATE_FORMAT(departure_date, '%d/%m/%Y') LIKE ?", ["%{$this->returnSearchDep}%"]);
            })
            ->when($this->returnSearchArr, function ($q) {
                $q->whereRaw("DATE_FORMAT(arrival_date, '%d/%m/%Y') LIKE ?", ["%{$this->returnSearchArr}%"]);
            })
            ->take(10)
            ->get();
    }

    private function getMyReservation(int $id): Reservation
    {
        return Reservation::where('id', $id)
            ->whereHas('user', function ($q) {
                $q->where('assigned_manager_id', auth()->id());
            })->with(['passenger', 'spaceFlight', 'logistics'])->firstOrFail();
    }

    public function getFilteredTerrestrialFlightsProperty()
    {
        $q = TerrestrialFlight::with(['originLocation', 'destinationLocation']);

        if (!$this->tFlightSearchID && !$this->tFlightSearchOrigin && !$this->tFlightSearchDest && !$this->tFlightSearchDate) {
            if ($this->create_flight_id) {
                $ida = Flight::find($this->create_flight_id);
                if ($ida) {
                    $q->where('departure_datetime', '<', $ida->departure_date)
                        ->where('departure_datetime', '>', $ida->departure_date->copy()->subDays(3));
                }
            }
        } else {
            $q->when($this->tFlightSearchID, fn($sq) => $sq->where('flight_number', 'like', '%' . $this->tFlightSearchID . '%'))
                ->when($this->tFlightSearchOrigin, function ($sq) {
                    $sq->whereHas('originLocation', fn($l) => $l->where('name', 'like', '%' . $this->tFlightSearchOrigin . '%'));
                })
                ->when($this->tFlightSearchDest, function ($sq) {
                    $sq->whereHas('destinationLocation', fn($l) => $l->where('name', 'like', '%' . $this->tFlightSearchDest . '%'));
                })
                ->when($this->tFlightSearchDate, function ($sq) {
                    $sq->whereRaw("DATE(departure_datetime) = ?", [$this->tFlightSearchDate]);
                });
        }

        return $q->take(15)->get();
    }



    public function openPayLinkModal(int $id): void
    {
        $this->payLinkReservationId = $id;
        $this->payLinkUrl = '';
        $this->showPayLinkModal = true;
    }

    public function generatePayLink(int $id): void
    {
        $this->payLinkReservationId = $id;
        $res = $this->getMyReservation($id);

        // Simulación o integración real con Stripe
        $link = \App\Models\PaymentLink::create([
            'booking_group_id' => $res->booking_group_id,
            'client_id' => $res->user_id,
            'amount' => $res->total_price,
            'status' => 'activo',
            'created_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        $this->payLinkUrl = route('payment.pay', ['token' => $link->token]);
        $this->payLinkAmount = (float) $res->total_price;
        $this->showPayLinkModal = true;
        session()->flash('pay_message', 'Enlace de pago generado correctamente.');
    }

    public function sendPaymentEmail(): void
    {
        $res = $this->getMyReservation($this->payLinkReservationId);
        $client = $res->user;

        if ($client && $client->email) {
            Mail::to($client->email)->send(new PaymentLinkMail(
                paymentUrl: $this->payLinkUrl,
                amount: $this->payLinkAmount,
                locator: $res->id_locator
            ));

            session()->flash('pay_message', 'Correo oficial enviado a ' . $client->email);
        } else {
            session()->flash('pay_message', 'Error: El cliente no tiene un correo válido.');
        }
    }

    public function closePayLinkModal(): void
    {
        $this->showPayLinkModal = false;
        $this->payLinkUrl = '';
        $this->payLinkAmount = 0;
    }

}
