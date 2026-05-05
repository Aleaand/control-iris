<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\HasResponsivePagination;
use App\Models\Flight;
use App\Models\Starship;
use App\Models\Destination;
use App\Models\PriceLog;
use App\Models\Task;
use App\Models\User;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ManageFlights extends Component
{
    use WithPagination, HasResponsivePagination;
    #[\Livewire\Attributes\Url]
    public $search = '';

    #[\Livewire\Attributes\Url(as: 'period')]
    public $periodFilter = 'all';

    #[\Livewire\Attributes\Url(as: 'status')]
    public $statusFilter = 'all';

    #[\Livewire\Attributes\Url(as: 'date')]
    public $dateFilter = '';

    public $sortDir = 'desc'; // Por defecto los más recientes primero (fecha más lejana o última creada)
    public $flightId;
    public $flight_code;
    public $starship_id = '';
    public $origin_id = '';
    public $destination_id = '';
    public $departure_date;
    public $arrival_date;
    public $base_price;
    public $all_base_price = 0;
    public $booked_passengers = 0;
    public $status = 'scheduled';
    public $au_distance = 0;
    public $total_capacity = 0;
    public $operational_cost = 0;
    public $shipLocationName = null;
    public $shipStatus = '';
    public $formattedShipInfo = '';
    public $shipMaintenanceEnd = null;
    public $shipCostPerAu = 0;
    public $suggested_arrival_date;
    public $suggested_price;
    public $profit_margin = 0;
    public $profit_percentage = 0;
    public $statusMessage = 'Esperando...';
    public $isEditing = false;
    public $showSaveModal = false;
    public $showDeleteModal = false;
    public $showDetailsModal = false;
    public $showConflictDeleteModal = false;
    public $reservationsCount = 0;
    public $deleteId = null;
    public $selectedFlight = null;

    public $flightToDeleteIsReturn = false;
    public $flightToDeleteCode = '';
    public $siblingCodeToDelete = '';
    public $missionHasReservations = false;
    public $cancelReason = 'voluntary'; // technical | weather | voluntary


    public $mission_speed_au = 0;
    public $crew_hourly_rate = 0;
    public $crew_daily_rate = 0;
    public $launch_cost_earth = 0;
    public $landing_cost_earth = 0;
    public $launch_cost_planet = 0;
    public $landing_cost_planet = 0;
    public $crew_members = 0;

    public $outbound_total_cost = 0;
    public $return_total_cost = 0;
    public $showReturnForm = false;
    public $return_departure_date = null;
    public $return_arrival_date = null;
    public $return_base_price = 0;

    public $return_au_distance = 0;
    public $flight_hours_return = 0;
    public $suggested_return_price = 0;

    public $user_modified_base_price = false;
    public $user_modified_return_base_price = false;
    public $user_modified_arrival_date = false;
    public $user_modified_return_arrival_date = false;

    // ── Cálculos de Vuelo ────────────────────────────────────────────────────────
    public $flight_hours_outbound = 0;
    public $waiting_days = 0;
    public $crew_cost_outbound = 0;
    public $crew_cost_waiting = 0;

    public $ship_outbound_cost = 0;
    public $ship_return_cost = 0;
    public $crew_cost_return = 0;

    public $revenue_outbound = 0;
    public $nova_price = 0;
    public $supernova_price = 0;

    public $return_revenue_total = 0;
    public $return_nova_price = 0;
    public $return_supernova_price = 0;
    public $rrhh_alert_needed = false;

    public $mission_total_revenue = 0;
    public $mission_total_cost = 0;
    public $mission_profitability = 0;
    public $mission_profit_pct = 0;
    public $mission_status_msg = '';

    // ── Edit context ──────────────────────────────────────────────────────────────
    public $isReturnFlight = false;
    public $siblingFlightId = null;
    public $siblingArrivalDate = null;
    public $hasLinkedReturn = false;
    public $siblingOperationalCost = 0;
    public $siblingBasePrice = 0;
    public $showDateConflictModal = false;
    public $conflictingReturnDate = null;
    public $isEditingFromReturn = false;

    protected $rules = [
        'starship_id' => 'required|exists:starships,id',
        'origin_id' => 'required|exists:destinations,id',
        'destination_id' => 'required|exists:destinations,id|different:origin_id',
        'departure_date' => 'required|date',
        'arrival_date' => 'required|date|after_or_equal:departure_date',
        'base_price' => 'required|numeric|min:0',
        'booked_passengers' => 'required|integer|min:0',
        'au_distance' => 'required|integer|min:0',
        'status' => 'required|in:scheduled,in_orbit,landed,cancelled',
        'mission_speed_au' => 'required|numeric|min:0.0001',
        'crew_hourly_rate' => 'required|numeric|min:0',
        'crew_daily_rate' => 'required|numeric|min:0',
        'launch_cost_earth' => 'required|numeric|min:0',
        'landing_cost_earth' => 'required|numeric|min:0',
        'launch_cost_planet' => 'required|numeric|min:0',
        'landing_cost_planet' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function addReturnFlightForExisting()
    {
        $this->showReturnForm = true;
        $this->return_au_distance = $this->au_distance;
        if ($this->arrival_date) {
            $this->return_departure_date = Carbon::parse($this->arrival_date)->addHours(24)->format('Y-m-d\TH:i');
        }
        $this->recalculateAll();
    }

    public function updatedDestinationId()
    {
        if ($this->destination_id) {
            $destination = Destination::find($this->destination_id);
            if ($destination) {
                $earth = Destination::where('name', 'Tierra')->first();
                $earthId = $earth ? $earth->id : null;

                if ($destination->id != $earthId) {
                    $this->au_distance = (int) $destination->getEffectiveDistanceAu();
                    $this->launch_cost_planet = (float) $destination->launch_fee;
                    $this->landing_cost_planet = (float) $destination->landing_fee;
                    if ($earth) {
                        $this->launch_cost_earth = (float) $earth->launch_fee;
                        $this->landing_cost_earth = (float) $earth->landing_fee;
                    }
                } elseif ($this->starship_id) {
                    $starship = Starship::find($this->starship_id);
                    if ($starship) {
                        $lastFlight = $starship->flights()
                            ->whereIn('status', ['landed', 'in_orbit', 'scheduled'])
                            ->latest('arrival_date')->first();
                        if ($lastFlight && $lastFlight->destination) {
                            $this->au_distance = (int) $lastFlight->destination->getEffectiveDistanceAu();
                        }
                    }
                } else {
                    $this->au_distance = (int) $destination->getEffectiveDistanceAu();
                }
                $this->return_au_distance = $this->au_distance;
            }
        } else {
            $this->au_distance = 0;
            $this->return_au_distance = 0;
        }

        $this->user_modified_base_price = false;
        $this->user_modified_return_base_price = false;

        $this->recalculateAll();
    }

    public function updatedAuDistance()
    {
        $this->au_distance = (int) $this->au_distance;
        $this->arrival_date = null;
        $this->user_modified_arrival_date = false;
        if (!$this->showReturnForm) {
            $this->return_au_distance = (int) $this->au_distance;
        }
        $this->recalculateAll();
    }

    public function updatedReturnAuDistance()
    {
        $this->return_au_distance = (int) $this->return_au_distance;
        $this->recalculateAll();
    }

    public function updatedDepartureDate()
    {
        $this->resetValidation(['departure_date', 'starship_id']);
        $this->arrival_date = null;
        $this->user_modified_arrival_date = false;
        $this->recalculateAll();
    }

    public function updatedArrivalDate()
    {
        $this->resetValidation(['arrival_date', 'starship_id']);
        $this->user_modified_arrival_date = true;

        // Detección de conflicto con vuelo de retorno vinculado
        if ($this->isEditing && !$this->isReturnFlight && $this->siblingFlightId && $this->arrival_date) {
            $outboundArrival = Carbon::parse($this->arrival_date);
            $sibling = Flight::find($this->siblingFlightId);

            if ($sibling) {
                $returnDep = Carbon::parse($sibling->departure_date);
                if ($outboundArrival->copy()->addHours(24)->gt($returnDep)) {
                    $this->showDateConflictModal = true;
                    $this->conflictingReturnDate = $outboundArrival->copy()->addHours(24)->format('Y-m-d\TH:i');
                }
            }
        }

        if ($this->showReturnForm && $this->arrival_date) {
            $this->return_departure_date = Carbon::parse($this->arrival_date)->addHours(24)->format('Y-m-d\TH:i');
            $this->recalculateAll();
        }
    }

    public function adjustReturnFlightDate()
    {
        if ($this->conflictingReturnDate) {
            $this->return_departure_date = $this->conflictingReturnDate;
            $this->showDateConflictModal = false;
            $this->recalculateAll();
        }
    }

    public function updatedBasePrice()
    {
        $this->user_modified_base_price = true;
        $this->all_base_price = round((float) $this->base_price * $this->getEffectiveCapacity(), 2);
        $this->calculateMissionProfit();
    }

    public function updatedBookedPassengers()
    {
        $this->resetValidation('booked_passengers');
        $this->calculateMissionProfit();
    }

    public function updatedStarshipId()
    {
        $this->resetValidation(['starship_id', 'departure_date', 'booked_passengers']);
        $this->shipLocationName = 'Tierra';
        $this->shipMaintenanceEnd = null;
        $this->shipCostPerAu = 0;
        $this->total_capacity = 0;
        $this->crew_members = 0;
        $this->mission_speed_au = 0;
        $this->crew_hourly_rate = 0;
        $this->crew_daily_rate = 0;
        $this->launch_cost_earth = 0;
        $this->landing_cost_earth = 0;
        $this->launch_cost_planet = 0;
        $this->landing_cost_planet = 0;

        if ($this->starship_id) {
            $starship = Starship::with('currentLocation')->find($this->starship_id);
            if ($starship) {
                $this->total_capacity = (int) $starship->general_capacity + (int) $starship->vip_capacity + (int) $starship->crew_capacity;
                $this->crew_members = (int) $starship->crew_capacity;
                $this->shipCostPerAu = (float) $starship->operational_cost_per_au;
                $this->mission_speed_au = (float) ($starship->cruise_speed_au ?? 0);
                $this->crew_hourly_rate = (float) ($starship->crew_hourly_rate ?? 0);
                $this->crew_daily_rate = (float) ($starship->crew_daily_rate ?? 0);
                $this->shipStatus = $starship->status;

                $this->shipMaintenanceEnd = $starship->maintenance_end_date
                    ? Carbon::parse($starship->maintenance_end_date)->format('d M Y')
                    : null;

                $earth = Destination::where('name', 'Tierra')->first();
                $earthId = $earth ? $earth->id : null;

                if ($starship->current_location_id) {
                    $this->origin_id = $starship->current_location_id;
                    $this->shipLocationName = $starship->currentLocation->name;
                } else {
                    $lastFlight = $starship->flights()
                        ->whereIn('status', ['landed'])
                        ->latest('arrival_date')->first();
                    $this->shipLocationName = $lastFlight
                        ? optional($lastFlight->destination)->name ?? 'Tierra'
                        : 'Tierra';
                    $this->origin_id = $lastFlight ? $lastFlight->destination_id : $earthId;
                }

                $statusLabel = match ($this->shipStatus) {
                    'active' => 'Activa',
                    'maintenance' => 'En Mantenimiento',
                    'retired' => 'Retirada',
                    default => ucfirst($this->shipStatus)
                };

                $this->formattedShipInfo = "{$starship->name} - {$statusLabel} - Aterrizada en {$this->shipLocationName}";
            }
        }

        $this->recalculateAll();
        // Re-apply destination fees if a destination is already selected
        if ($this->destination_id) {
            $destination = Destination::find($this->destination_id);
            if ($destination) {
                $earth = Destination::where('name', 'Tierra')->first();
                $earthId = $earth ? $earth->id : null;
                if ($destination->id != $earthId) {
                    $this->launch_cost_planet = (float) $destination->launch_fee;
                    $this->landing_cost_planet = (float) $destination->landing_fee;
                    if ($earth) {
                        $this->launch_cost_earth = (float) $earth->launch_fee;
                        $this->landing_cost_earth = (float) $earth->landing_fee;
                    }
                }
            }
            $this->recalculateAll();
        }
    }

    // Watchers para recalcular al editar parámetros de misión
    public function updatedMissionSpeedAu()
    {
        $this->recalculateAll();
    }
    public function updatedCrewMembers()
    {
        $this->calculateMissionProfit();
    }
    public function updatedCrewHourlyRate()
    {
        $this->calculateMissionProfit();
    }
    public function updatedCrewDailyRate()
    {
        $this->calculateMissionProfit();
    }
    public function updatedLaunchCostEarth()
    {
        $this->calculateMissionProfit();
    }
    public function updatedLandingCostEarth()
    {
        $this->calculateMissionProfit();
    }
    public function updatedLaunchCostPlanet()
    {
        $this->calculateMissionProfit();
    }
    public function updatedLandingCostPlanet()
    {
        $this->calculateMissionProfit();
    }
    public function updatedReturnDepartureDate()
    {
        $this->user_modified_return_arrival_date = false;
        $this->recalculateAll();
    }
    public function updatedReturnArrivalDate()
    {
        $this->user_modified_return_arrival_date = true;
        $this->calculateMissionProfit();
    }
    public function updatedReturnBasePrice()
    {
        $this->user_modified_return_base_price = true;
        $this->calculateMissionProfit();
    }

    public function toggleReturnForm()
    {
        $this->showReturnForm = !$this->showReturnForm;
        if (!$this->showReturnForm) {
            $this->return_departure_date = null;
            $this->return_arrival_date = null;
            $this->return_base_price = 0;
            $this->user_modified_return_base_price = false;
            $this->return_au_distance = $this->au_distance;
        } else {
            if (!$this->return_au_distance) {
                $this->return_au_distance = $this->au_distance;
            }
            if ($this->arrival_date && empty($this->return_departure_date)) {
                $this->return_departure_date = Carbon::parse($this->arrival_date)->addHours(24)->format('Y-m-d\TH:i');
            }
        }
        $this->recalculateAll();
    }

    private function getEffectiveCapacity()
    {
        $effCap = 1;
        if ($this->starship_id) {
            $starship = Starship::find($this->starship_id);
            if ($starship) {
                $novaMult = PriceLog::getCurrentPrice('multiplier_nova') ?: 1.0;
                $supernovaMult = PriceLog::getCurrentPrice('multiplier_supernova') ?: 2.5;
                $effCap = ($starship->general_capacity * $novaMult) + ($starship->vip_capacity * $supernovaMult);
                if ($effCap <= 0) {
                    $effCap = 1;
                }
            }
        }
        return $effCap;
    }

    private function recalculateAll()
    {
        if ($this->starship_id) {
            if ($this->total_capacity == 0 || $this->mission_speed_au <= 0 || $this->shipCostPerAu <= 0) {
                $starship = Starship::find($this->starship_id);
                if ($starship) {
                    if ($this->total_capacity == 0) {
                        $this->total_capacity = (int) $starship->general_capacity + (int) $starship->vip_capacity + (int) $starship->crew_capacity;
                        $this->crew_members = (int) $starship->crew_capacity;
                    }
                    if ($this->shipCostPerAu <= 0) {
                        $this->shipCostPerAu = (float) $starship->operational_cost_per_au;
                    }
                    if ($this->mission_speed_au <= 0) {
                        $this->mission_speed_au = (float) ($starship->cruise_speed_au ?? 0);
                    }
                }
            }
        }

        $this->flight_hours_outbound = 0;
        if ($this->au_distance > 0 && $this->mission_speed_au > 0) {
            $this->flight_hours_outbound = ceil($this->au_distance * $this->mission_speed_au);
        }

        if ($this->flight_hours_outbound > 0 && $this->departure_date) {
            try {
                $this->suggested_arrival_date = Carbon::parse($this->departure_date)
                    ->addHours($this->flight_hours_outbound)
                    ->format('Y-m-d\TH:i');

                if (!$this->user_modified_arrival_date) {
                    $this->arrival_date = $this->suggested_arrival_date;
                }
            } catch (\Exception $e) {
            }
        }

        $this->flight_hours_return = 0;
        if ($this->showReturnForm && $this->return_au_distance > 0 && $this->mission_speed_au > 0) {
            $this->flight_hours_return = ceil($this->return_au_distance * $this->mission_speed_au);

            if ($this->flight_hours_return > 0 && $this->return_departure_date) {
                try {
                    $sugg = Carbon::parse($this->return_departure_date)
                        ->addHours($this->flight_hours_return)
                        ->format('Y-m-d\TH:i');

                    if (!$this->user_modified_return_arrival_date) {
                        $this->return_arrival_date = $sugg;
                    }
                } catch (\Exception $e) {
                }
            }
        }

        $this->calculateMissionProfit();
    }

    private function calculateMissionProfit()
    {
        $effCap = $this->getEffectiveCapacity();
        if ($effCap <= 0)
            $effCap = 1;

        $novaMult = PriceLog::getCurrentPrice('multiplier_nova') ?: 1.0;
        $supernovaMult = PriceLog::getCurrentPrice('multiplier_supernova') ?: 2.5;

        // Número real de tripulantes facturables (0 = sin coste de empleados)
        $crewCount = (int) $this->booked_passengers;

        // Días de espera: 0 si no hay tripulación
        $this->waiting_days = 0;
        $this->rrhh_alert_needed = false;

        if ($crewCount > 0) {
            if ($this->showReturnForm && $this->arrival_date && $this->return_departure_date) {
                try {
                    $arrival = Carbon::parse($this->arrival_date);
                    $returnDep = Carbon::parse($this->return_departure_date);
                    $this->waiting_days = max(1, (int) $arrival->diffInDays($returnDep));
                } catch (\Exception $e) {
                    $this->waiting_days = 1;
                }
            } elseif (!$this->showReturnForm && !$this->isReturnFlight) {
                // Sin vuelo de vuelta definido: 7 días de espera + alerta RRHH
                // (Solo si NO es un vuelo de retorno per se)
                $this->waiting_days = 7;
                $this->rrhh_alert_needed = true;
            }
        }

        $this->crew_cost_outbound = round($crewCount * $this->crew_hourly_rate * $this->flight_hours_outbound, 2);
        $this->crew_cost_waiting = round($crewCount * $this->crew_daily_rate * $this->waiting_days, 2);
        $this->ship_outbound_cost = round($this->au_distance * $this->shipCostPerAu, 2);

        $this->outbound_total_cost = round(
            (float) $this->launch_cost_earth +
            (float) $this->landing_cost_planet +
            $this->crew_cost_outbound +
            $this->ship_outbound_cost,
            2
        );

        // Tarifa base calculado el 70% ocupacio
        $this->suggested_price = round($this->outbound_total_cost / (0.49 * $effCap), 2);
        if (!$this->user_modified_base_price || !$this->base_price) {
            $this->base_price = $this->suggested_price;
        }

        $this->nova_price = round($this->base_price * $novaMult, 2);
        $this->supernova_price = round($this->base_price * $supernovaMult, 2);
        $this->revenue_outbound = round($effCap * 0.80 * $this->base_price, 2); // Proyeccion 80%

        if ($this->showReturnForm) {
            $this->ship_return_cost = round($this->return_au_distance * $this->shipCostPerAu, 2);
            $this->crew_cost_return = round($crewCount * $this->crew_hourly_rate * $this->flight_hours_return, 2);

            $this->return_total_cost = round(
                (float) $this->launch_cost_planet +
                (float) $this->landing_cost_earth +
                $this->crew_cost_return +
                $this->crew_cost_waiting +
                $this->ship_return_cost,
                2
            );

            $this->suggested_return_price = round($this->return_total_cost / (0.49 * $effCap), 2);
            if (!$this->user_modified_return_base_price || !$this->return_base_price) {
                $this->return_base_price = $this->suggested_return_price;
            }
            $this->return_revenue_total = round($effCap * 0.80 * $this->return_base_price, 2);
            $this->return_nova_price = round($this->return_base_price * $novaMult, 2);
            $this->return_supernova_price = round($this->return_base_price * $supernovaMult, 2);
        } else {
            $this->return_total_cost = 0;
            $this->return_base_price = 0;
            $this->return_revenue_total = 0;
            $this->return_nova_price = 0;
            $this->return_supernova_price = 0;
            $this->ship_return_cost = 0;
            $this->crew_cost_return = 0;
        }

        // Totales
        $this->mission_total_cost = $this->outbound_total_cost + $this->return_total_cost;
        $this->mission_total_revenue = $this->revenue_outbound + $this->return_revenue_total;
        $this->operational_cost = $this->outbound_total_cost;

        // Rentabilidad
        $this->mission_profitability = $this->mission_total_revenue - $this->mission_total_cost;
        $this->mission_profit_pct = $this->mission_total_revenue > 0
            ? round(($this->mission_profitability / $this->mission_total_revenue) * 100, 1)
            : ($this->mission_total_cost > 0 ? -100.0 : 0.0);

        // Compatibilidad 
        $this->all_base_price = round((float) $this->base_price * $effCap, 2);
        $this->profit_margin = $this->mission_profitability;
        $this->profit_percentage = $this->mission_profit_pct;
        $this->suggested_price = $this->base_price;

        // Mensaje de Estado
        if (!$this->showReturnForm && !$this->isEditing) {
            $this->mission_status_msg = "one_way_alert";
        } elseif ($this->mission_profitability < 0) {
            $this->mission_status_msg = "Vuelo no rentable";
        } elseif ($this->mission_profit_pct < 20) {
            $this->mission_status_msg = "Vuelo poco rentable";
        } else {
            $this->mission_status_msg = "Vuelo rentable";
        }

        $this->statusMessage = $this->mission_status_msg;
    }

    public function resetInputFields()
    {
        $this->flight_code = 'IRIS-' . date('Y') . '-' . strtoupper(Str::random(4));
        $this->starship_id = '';
        $this->destination_id = '';
        $this->origin_id = '';
        $this->departure_date = '';
        $this->arrival_date = '';
        $this->base_price = '';
        $this->all_base_price = 0;
        $this->booked_passengers = 0;
        $this->status = 'scheduled';
        $this->au_distance = 0;
        $this->total_capacity = 0;
        $this->operational_cost = 0;
        $this->shipLocationName = null;
        $this->shipStatus = '';
        $this->formattedShipInfo = '';
        $this->shipMaintenanceEnd = null;
        $this->shipCostPerAu = 0;
        $this->suggested_arrival_date = null;
        $this->suggested_price = null;
        $this->profit_margin = 0;
        $this->profit_percentage = 0;
        $this->statusMessage = 'Esperando...';
        $this->isEditing = false;
        $this->flightId = null;
        $this->mission_speed_au = 0;
        $this->crew_hourly_rate = 0;
        $this->crew_daily_rate = 0;
        $this->launch_cost_earth = 0;
        $this->landing_cost_earth = 0;
        $this->launch_cost_planet = 0;
        $this->landing_cost_planet = 0;
        $this->crew_members = 0;
        $this->isReturnFlight = false;
        $this->siblingFlightId = null;
        $this->siblingArrivalDate = null;
        $this->hasLinkedReturn = false;
        $this->siblingOperationalCost = 0;
        $this->siblingBasePrice = 0;
        $this->showDateConflictModal = false;
        $this->conflictingReturnDate = null;
        $this->user_modified_arrival_date = false;

        // Formulario retorno
        $this->showReturnForm = false;
        $this->return_departure_date = null;
        $this->return_arrival_date = null;
        $this->return_au_distance = 0;
        $this->return_base_price = 0;

        // Cálculos misión
        $this->flight_hours_outbound = 0;
        $this->waiting_days = 0;
        $this->crew_cost_outbound = 0;
        $this->crew_cost_waiting = 0;
        $this->return_revenue_total = 0;
        $this->mission_total_revenue = 0;
        $this->mission_total_cost = 0;
        $this->mission_profitability = 0;
        $this->mission_profit_pct = 0;
        $this->mission_status_msg = '';
        $this->isEditingFromReturn = false;
        $this->resetValidation();
        $this->showSaveModal = false;
        $this->showDeleteModal = false;
        $this->showDetailsModal = false;
        $this->showConflictDeleteModal = false;
        $this->reservationsCount = 0;
        $this->reservationsCount = 0;
        $this->deleteId = null;
        $this->selectedFlight = null;
        $this->flightToDeleteIsReturn = false;
        $this->flightToDeleteCode = '';
        $this->siblingCodeToDelete = '';
        $this->missionHasReservations = false;
    }

    private function updateFlightStatuses()
    {
        $now = Carbon::now();
        Flight::where('status', 'scheduled')
            ->where('departure_date', '<=', $now)
            ->update(['status' => 'in_orbit']);

        $landedFlights = Flight::where('status', 'in_orbit')
            ->where('arrival_date', '<=', $now)->get();

        foreach ($landedFlights as $f) {
            $f->update(['status' => 'landed']);
            if ($f->starship) {
                $f->starship->update(['current_location_id' => $f->destination_id]);
            }
        }
    }

    public function render()
    {
        $this->updateFlightStatuses();

        $query = Flight::with(['starship', 'destination', 'origin'])
            ->withCount([
                'reservations',
                'reservations as nova_booked' => function ($q) {
                    $q->where('seat_type', 'LIKE', 'Nova')->whereNotIn('status', ['Cancelada', 'Cancelled']);
                },
                'reservations as supernova_booked' => function ($q) {
                    $q->where('seat_type', 'LIKE', 'Supernova')->whereNotIn('status', ['Cancelada', 'Cancelled']);
                }
            ]);
        if ($this->search) {
            $query->where(function (\Illuminate\Database\Eloquent\Builder $q) {
                $searchTerm = '%' . $this->search . '%';
                $q->where('flight_code', 'LIKE', $searchTerm)
                    ->orWhereHas('destination', function ($subQ) use ($searchTerm) {
                        $subQ->where('name', 'LIKE', $searchTerm);
                    })
                    ->orWhereHas('starship', function ($subQ) use ($searchTerm) {
                        $subQ->where('name', 'LIKE', $searchTerm);
                    })
                    ->orWhereRaw('id::text LIKE ?', ["{$this->search}%"])
                    ->orWhereRaw("LPAD(id::text, 4, '0') LIKE ?", ["{$this->search}%"]);

                // Búsqueda por fecha (intenta parsear si el search parece una fecha)
                try {
                    if (preg_match('/^\d{1,4}[-\/\.]\d{1,2}[-\/\.]\d{1,4}$/', $this->search)) {
                        $date = \Carbon\Carbon::parse($this->search)->format('Y-m-d');
                        $q->orWhereDate('departure_date', $date);
                    }
                } catch (\Exception $e) {
                    // Si falla el parseo no añadimos el filtro de fecha
                }
            });
        }

        if ($this->periodFilter === 'today') {
            $query->whereDate('departure_date', Carbon::today());
        } elseif ($this->periodFilter === 'this_month') {
            $query->whereMonth('departure_date', Carbon::now()->month)
                ->whereYear('departure_date', Carbon::now()->year);
        } elseif ($this->periodFilter === 'this_year') {
            $query->whereYear('departure_date', Carbon::now()->year);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFilter) {
            $query->whereDate('departure_date', $this->dateFilter);
        }

        $flights = $query->orderBy('departure_date', $this->sortDir)
            ->orderBy('id', 'desc')
            ->paginate($this->getPerPage());

        $widgets = [
            'today' => Flight::whereDate('departure_date', Carbon::today())->count(),
            'landed_today' => Flight::where('status', 'landed')->whereDate('arrival_date', Carbon::today())->count(),
            'in_orbit' => Flight::where('status', 'in_orbit')->count(),
            'incidents' => Flight::where('status', 'cancelled')->count()
        ];

        $starships = Starship::where('status', '!=', 'retired')->get();

        return view('livewire.admin.manage-flights', [
            'flights' => $flights,
            'widgets' => $widgets,
            'starships' => $starships,
            'destinations' => Destination::all(),
        ])->layout('layouts.app');
    }

    public function toggleSort()
    {
        $this->sortDir = $this->sortDir === 'desc' ? 'asc' : 'desc';
    }

    public function setCreateMode()
    {
        $this->resetInputFields();
    }

    public function setPresetFilter($type)
    {
        $this->periodFilter = 'all';
        $this->statusFilter = 'all';
        $this->search = '';

        if ($type === 'today') {
            $this->periodFilter = 'today';
        } elseif ($type === 'in_orbit') {
            $this->statusFilter = 'in_orbit';
        } elseif ($type === 'landed_year') {
            $this->periodFilter = 'this_year';
            $this->statusFilter = 'landed';
        } elseif ($type === 'incidents') {
            $this->statusFilter = 'cancelled';
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->periodFilter = 'all';
        $this->statusFilter = 'all';
        $this->dateFilter = '';
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function viewDetails($id)
    {
        $this->selectedFlight = Flight::with(['starship', 'destination', 'origin'])
            ->withCount([
                'reservations',
                'reservations as nova_booked' => function ($q) {
                    $q->where('seat_type', 'LIKE', 'Nova')->whereNotIn('status', ['Cancelada', 'Cancelled']);
                },
                'reservations as supernova_booked' => function ($q) {
                    $q->where('seat_type', 'LIKE', 'Supernova')->whereNotIn('status', ['Cancelada', 'Cancelled']);
                }
            ])->find($id);

        if ($this->selectedFlight) {
            $this->showDetailsModal = true;
        }
    }

    public function edit($id)
    {
        $this->resetInputFields();
        $this->isEditing = true;
        $clickedFlight = Flight::findOrFail($id);

        $outbound = null;
        $return = null;

        if (str_ends_with($clickedFlight->flight_code, '-RET')) {
            $this->isEditingFromReturn = true;
            $outboundCode = Str::beforeLast($clickedFlight->flight_code, '-RET');
            $outbound = Flight::where('flight_code', $outboundCode)->first();
            $return = $clickedFlight;
        } else {
            $this->isEditingFromReturn = false;
            $outbound = $clickedFlight;
            $return = Flight::where('flight_code', $outbound->flight_code . '-RET')->first();
        }

        if (!$outbound) {
            $outbound = $clickedFlight;
        }

        $this->flightId = $outbound->id;
        $this->flight_code = $outbound->flight_code;
        $this->starship_id = $outbound->starship_id;
        $this->origin_id = $outbound->origin_id;
        $this->destination_id = $outbound->destination_id;
        $this->departure_date = $outbound->departure_date ? Carbon::parse($outbound->departure_date)->format('Y-m-d\TH:i') : null;
        $this->arrival_date = $outbound->arrival_date ? Carbon::parse($outbound->arrival_date)->format('Y-m-d\TH:i') : null;
        $this->base_price = (float) $outbound->base_price;
        $this->booked_passengers = (int) $outbound->booked_passengers;
        $this->status = $outbound->status;
        $this->au_distance = (int) $outbound->au_distance;
        $this->total_capacity = (int) $outbound->total_capacity;
        $this->mission_speed_au = (float) ($outbound->mission_speed_au ?? 0);
        $this->crew_hourly_rate = (float) ($outbound->crew_hourly_rate ?? 0);
        $this->crew_daily_rate = (float) ($outbound->crew_daily_rate ?? 0);
        $this->launch_cost_earth = (float) ($outbound->launch_cost_earth ?? 0);
        $this->landing_cost_planet = (float) ($outbound->landing_cost_planet ?? 0);
        $this->landing_cost_earth = 0;
        $this->launch_cost_planet = 0;
        $this->suggested_arrival_date = $this->arrival_date;
        $this->isReturnFlight = false;
        if ($return) {
            $this->siblingFlightId = $return->id;
            $this->showReturnForm = true;
            $this->return_departure_date = $return->departure_date ? Carbon::parse($return->departure_date)->format('Y-m-d\TH:i') : null;
            $this->return_arrival_date = $return->arrival_date ? Carbon::parse($return->arrival_date)->format('Y-m-d\TH:i') : null;
            $this->return_base_price = (float) $return->base_price;
            $this->return_au_distance = (int) ($return->au_distance ?? $outbound->au_distance);
            $this->landing_cost_earth = (float) ($return->landing_cost_earth ?? 0);
            $this->launch_cost_planet = (float) ($return->launch_cost_planet ?? 0);
        } else {
            $this->showReturnForm = false;
        }
        if ($this->starship_id) {
            $starship = Starship::with('currentLocation')->find($this->starship_id);
            if ($starship) {
                $this->shipCostPerAu = (float) $starship->operational_cost_per_au;
                $this->crew_members = (int) $starship->crew_capacity;
                $this->shipStatus = $starship->status;
                $this->shipMaintenanceEnd = $starship->maintenance_end_date
                    ? Carbon::parse($starship->maintenance_end_date)->format('d M Y')
                    : null;
                if ($starship->currentLocation) {
                    $this->shipLocationName = $starship->currentLocation->name;
                } else {
                    $lastFlight = $starship->flights()
                        ->whereIn('status', ['landed'])
                        ->latest('arrival_date')->first();
                    $this->shipLocationName = $lastFlight
                        ? optional($lastFlight->destination)->name ?? 'Tierra'
                        : 'Tierra';
                }

                $statusLabel = match ($this->shipStatus) {
                    'active' => 'Activa',
                    'maintenance' => 'En Mantenimiento',
                    'retired' => 'Retirada',
                    default => ucfirst($this->shipStatus)
                };
                $this->formattedShipInfo = "{$starship->name} - {$statusLabel} - Aterrizada en {$this->shipLocationName}";
            }
        }

        $this->user_modified_base_price = true;
        $this->user_modified_return_base_price = true;
        $this->recalculateAll();

    }

    public function confirmSave()
    {
        $this->validate();

        if ($this->showReturnForm && $this->return_departure_date && $this->arrival_date) {
            $arr = \Carbon\Carbon::parse($this->arrival_date);
            $retDep = \Carbon\Carbon::parse($this->return_departure_date);

            if ($retDep->lt($arr) || $arr->diffInHours($retDep) < 24) {
                $this->addError('return_departure_date', 'Debe haber un margen mínimo de 24 horas desde el aterrizaje para programar el próximo vuelo.');
                return;
            }
        }

        $starship = Starship::find($this->starship_id);
        $earth = Destination::where('name', 'Tierra')->first();
        $earthId = $earth ? $earth->id : null;

        if ($starship && $this->status !== 'cancelled' && $this->status !== 'landed' && !$this->isEditing) {
            $currentLocId = $starship->current_location_id ?? $earthId;

            if ($this->destination_id != $earthId && $currentLocId != $earthId) {
                $locName = Destination::find($currentLocId)?->name ?? 'desconocida';
                $this->addError('starship_id', "La nave está en {$locName}. Debe volver a la Tierra antes de este vuelo.");
                return;
            }
            if ($this->destination_id == $earthId && $currentLocId == $earthId) {
                $this->addError('destination_id', "La nave ya está en la Tierra. No puede hacer un vuelo hacia su propia ubicación.");
                return;
            }
        }

        if ($starship && $this->status !== 'cancelled' && $this->status !== 'landed') {
            if ($starship->status === 'maintenance' || ($starship->maintenance_end_date && Carbon::parse($this->departure_date)->lte($starship->maintenance_end_date))) {
                $maintEndStr = $starship->maintenance_end_date ? Carbon::parse($starship->maintenance_end_date)->format('d M Y') : 'indefinida';
                $this->addError('departure_date', "La unidad está en mantenimiento hasta el " . $maintEndStr . ". Programa la salida después de esa fecha o selecciona otra nave disponible.");
                return;
            }
        }
        if ($this->status !== 'cancelled' && $this->status !== 'landed') {
            $collisionOutbound = Flight::where('starship_id', $this->starship_id)
                ->whereNotIn('status', ['cancelled', 'landed'])
                ->where(function ($q) {
                    $q->where('departure_date', '<', $this->arrival_date)
                        ->where('arrival_date', '>', $this->departure_date);
                });

            if ($this->isEditing) {
                $collisionOutbound->where('id', '!=', $this->flightId);
                if ($this->siblingFlightId) {
                    $collisionOutbound->where('id', '!=', $this->siblingFlightId);
                }
            }

            if ($collisionOutbound->exists()) {
                $this->addError('starship_id', 'Esta nave ya tiene un vuelo que se superpone con estas fechas.');
                return;
            }
        }

        if ($this->showReturnForm && $this->return_departure_date && $this->return_arrival_date) {
            $collisionReturn = Flight::where('starship_id', $this->starship_id)
                ->whereNotIn('status', ['cancelled', 'landed'])
                ->where(function ($q) {
                    $q->where('departure_date', '<', $this->return_arrival_date)
                        ->where('arrival_date', '>', $this->return_departure_date);
                });

            if ($this->isEditing) {
                $collisionReturn->where('id', '!=', $this->flightId);
                if ($this->siblingFlightId) {
                    $collisionReturn->where('id', '!=', $this->siblingFlightId);
                }
            }

            if ($collisionReturn->exists()) {
                $this->addError('return_departure_date', 'Esta nave ya tiene un vuelo que se superpone con estas fechas.');
                return;
            }
        }

        if ($starship && $this->booked_passengers > $starship->crew_capacity) {
            $this->addError('booked_passengers', 'CAPACIDAD EXCEDIDA: La nave soporta un máximo de ' . $starship->crew_capacity . ' plazas de tripulación (Empleados).');
            return;
        }

        $this->showSaveModal = true;
    }

    public function executeSave()
    {
        $this->validate();

        $data = [
            'flight_code' => $this->flight_code,
            'starship_id' => $this->starship_id,
            'origin_id' => $this->origin_id,
            'destination_id' => $this->destination_id,
            'departure_date' => $this->departure_date,
            'arrival_date' => $this->arrival_date,
            'base_price' => $this->base_price,
            'booked_passengers' => $this->booked_passengers,
            'status' => $this->status,
            'au_distance' => $this->au_distance,
            'total_capacity' => $this->total_capacity,
            'operational_cost' => $this->outbound_total_cost,
            'mission_speed_au' => $this->mission_speed_au,
            'crew_hourly_rate' => $this->crew_hourly_rate,
            'crew_daily_rate' => $this->crew_daily_rate,
            'launch_cost_earth' => $this->launch_cost_earth,
            'landing_cost_earth' => 0,
            'launch_cost_planet' => 0,
            'landing_cost_planet' => $this->landing_cost_planet,
            'return_departure_date' => null,
            'return_base_price' => null,
            'mission_profitability' => $this->mission_profitability,
        ];

        if ($this->isEditing && $this->flightId) {
            $flight = Flight::findOrFail($this->flightId);
            if ((float) $this->base_price !== (float) $flight->base_price) {
                PriceLog::record(
                    itemType: 'flight',
                    itemId: $flight->id,
                    oldPrice: (float) $flight->base_price,
                    newPrice: (float) $this->base_price,
                    reason: 'Actualización manual desde Administrador'
                );
            }

            // ── Detectar retraso de salida ─────────────────────────────────
            $oldDeparture = $flight->departure_date ? Carbon::parse($flight->departure_date) : null;
            $newDeparture = $this->departure_date ? Carbon::parse($this->departure_date) : null;
            $isDelayed = $oldDeparture && $newDeparture && $newDeparture->gt($oldDeparture);

            if ($isDelayed && $flight->reservations()->whereNotIn('status', ['Cancelada', 'Cancelled'])->exists()) {
                $affectedGestors = $this->getAffectedGestors($flight->id);
                $oldStr = $oldDeparture->format('d/m/Y H:i');
                $newStr = $newDeparture->format('d/m/Y H:i');

                foreach ($affectedGestors as $gestor) {
                    $passengerNames = $flight->reservations()
                        ->whereNotIn('status', ['Cancelada', 'Cancelled'])
                        ->whereHas('user', fn($q) => $q->where('assigned_manager_id', $gestor->id))
                        ->with('passenger')
                        ->get()
                        ->pluck('passenger.full_name')
                        ->unique()
                        ->implode(', ');

                    Task::create([
                        'assigned_gestor_id' => $gestor->id,
                        'created_by' => auth()->id(),
                        'title' => "Retraso en Vuelo {$flight->flight_code} — Notifica a tus pasajeros",
                        'description' => "El vuelo {$flight->flight_code} ha cambiado su fecha de salida.\n\nFecha anterior: {$oldStr}\nNueva fecha: {$newStr}\n\nNotifica a tus pasajeros afectados: {$passengerNames}",
                        'type' => 'policy_change',
                        'status' => 'Pendiente',
                        'priority' => 'alta',
                        'payload' => [
                            'flight_id' => $flight->id,
                            'flight_code' => $flight->flight_code,
                            'old_departure' => $oldDeparture->toDateTimeString(),
                            'new_departure' => $newDeparture->toDateTimeString(),
                        ],
                    ]);
                }
            }
            // ─────────────────────────────────────────────────────────────

            $flight->update($data);

            if ($this->status === 'landed') {
                Starship::where('id', $this->starship_id)
                    ->update(['current_location_id' => $this->destination_id]);
            }

            if ($this->showReturnForm && $this->return_departure_date) {
                $returnArrDate = $this->return_arrival_date
                    ?: Carbon::parse($this->return_departure_date)->addHours($this->flight_hours_return)->format('Y-m-d\TH:i');

                $returnData = [
                    'flight_code' => $this->flight_code . '-RET',
                    'starship_id' => $this->starship_id,
                    'origin_id' => $this->destination_id,
                    'destination_id' => $this->origin_id,
                    'departure_date' => $this->return_departure_date,
                    'arrival_date' => $returnArrDate,
                    'base_price' => $this->return_base_price,
                    'booked_passengers' => $this->booked_passengers,
                    'status' => $this->status,
                    'au_distance' => $this->return_au_distance ?: $this->au_distance,
                    'total_capacity' => $this->total_capacity,
                    'operational_cost' => $this->return_total_cost,
                    'mission_speed_au' => $this->mission_speed_au,
                    'crew_hourly_rate' => $this->crew_hourly_rate,
                    'crew_daily_rate' => $this->crew_daily_rate,
                    'launch_cost_earth' => 0,
                    'landing_cost_earth' => $this->landing_cost_earth,
                    'launch_cost_planet' => $this->launch_cost_planet,
                    'landing_cost_planet' => 0,
                    'return_departure_date' => null,
                    'return_base_price' => null,
                    'mission_profitability' => 0,
                ];

                if ($this->siblingFlightId) {
                    $returnFlight = Flight::find($this->siblingFlightId);
                    if ($returnFlight) {
                        if ((float) $this->return_base_price !== (float) $returnFlight->base_price) {
                            PriceLog::record('flight', $returnFlight->id, (float) $returnFlight->base_price, (float) $this->return_base_price, 'Actualización manual misión unificada');
                        }
                        $returnFlight->update($returnData);
                    }
                } else {
                    Flight::create($returnData);
                }
            }
            if ($this->siblingFlightId) {
                $sibling = Flight::find($this->siblingFlightId);
                if ($sibling) {
                    $siblingParams = [
                        'starship_id' => $this->starship_id,
                        'crew_hourly_rate' => (float) $this->crew_hourly_rate,
                        'crew_daily_rate' => (float) $this->crew_daily_rate,
                        'mission_speed_au' => (float) $this->mission_speed_au,
                        'booked_passengers' => (int) $this->booked_passengers,
                    ];

                    if (!$this->isReturnFlight && $this->return_departure_date && $this->return_arrival_date) {
                        $siblingParams['departure_date'] = $this->return_departure_date;
                        $siblingParams['arrival_date'] = $this->return_arrival_date;
                    }

                    $sibling->update($siblingParams);
                }
            }

            session()->flash('message', 'Vuelo ' . $flight->flight_code . ' actualizado exitosamente.');
        } else {
            $flight = Flight::create($data);
            $this->generateExpensesForFlight($flight, $this->launch_cost_earth, $this->landing_cost_planet, $this->crew_cost_outbound, $this->ship_outbound_cost, 0);

            if ($this->showReturnForm && $this->return_departure_date) {
                $returnArrDate = $this->return_arrival_date ?: Carbon::parse($this->return_departure_date)->addHours($this->flight_hours_return)->format('Y-m-d\TH:i');

                $returnFlight = Flight::create([
                    'flight_code' => $this->flight_code . '-RET',
                    'starship_id' => $this->starship_id,
                    'origin_id' => $this->destination_id,
                    'destination_id' => $this->origin_id,
                    'departure_date' => $this->return_departure_date,
                    'arrival_date' => $returnArrDate,
                    'base_price' => $this->return_base_price,
                    'booked_passengers' => $this->booked_passengers,
                    'status' => $this->status,
                    'au_distance' => $this->return_au_distance ?: $this->au_distance,
                    'total_capacity' => $this->total_capacity,
                    'operational_cost' => $this->return_total_cost,
                    'mission_speed_au' => $this->mission_speed_au,
                    'crew_hourly_rate' => $this->crew_hourly_rate,
                    'crew_daily_rate' => $this->crew_daily_rate,
                    'launch_cost_earth' => 0,
                    'landing_cost_earth' => $this->landing_cost_earth,
                    'launch_cost_planet' => $this->launch_cost_planet,
                    'landing_cost_planet' => 0,
                    'return_departure_date' => null,
                    'return_base_price' => null,
                    'mission_profitability' => 0,
                ]);

                $this->generateExpensesForFlight($returnFlight, $this->launch_cost_planet, $this->landing_cost_earth, $this->crew_cost_return, $this->ship_return_cost, $this->crew_cost_waiting);
            }

            session()->flash('message', 'Nuevo Vuelo programado con éxito.');
        }

        $this->setCreateMode();
        $this->showSaveModal = false;
    }

    private function generateExpensesForFlight($flight, $launchCost, $landingCost, $crewCost, $shipCost, $waitingCost)
    {
        $expenses = [];
        $date = $flight->departure_date ?? now();

        if ($launchCost > 0) {
            $expenses[] = [
                'flight_id' => $flight->id,
                'reference' => 'LNC-' . $flight->flight_code,
                'category' => 'operational_flight',
                'description' => 'Tasa de lanzamiento',
                'amount' => $launchCost,
                'expense_date' => $date,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($landingCost > 0) {
            $expenses[] = [
                'flight_id' => $flight->id,
                'reference' => 'LND-' . $flight->flight_code,
                'category' => 'operational_flight',
                'description' => 'Tasa de aterrizaje',
                'amount' => $landingCost,
                'expense_date' => $flight->arrival_date ?? $date,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($crewCost > 0) {
            $expenses[] = [
                'flight_id' => $flight->id,
                'reference' => 'CRW-' . $flight->flight_code,
                'category' => 'operational_flight',
                'description' => 'Salario de tripulación (Vuelo)',
                'amount' => $crewCost,
                'expense_date' => $date,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($waitingCost > 0) {
            $expenses[] = [
                'flight_id' => $flight->id,
                'reference' => 'CRWK-' . $flight->flight_code,
                'category' => 'operational_flight',
                'description' => 'Salario de tripulación (Espera)',
                'amount' => $waitingCost,
                'expense_date' => $date,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($shipCost > 0) {
            $expenses[] = [
                'flight_id' => $flight->id,
                'reference' => 'SHP-' . $flight->flight_code,
                'category' => 'operational_flight',
                'description' => 'Coste operativo de la nave (AU)',
                'amount' => $shipCost,
                'expense_date' => $date,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($expenses)) {
            \App\Models\Expense::insert($expenses);
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $flight = Flight::find($id);

        if ($flight) {
            $this->flightToDeleteCode = $flight->flight_code;
            $this->flightToDeleteIsReturn = str_ends_with($flight->flight_code, '-RET');
            if ($this->flightToDeleteIsReturn) {
                $outboundCode = Str::beforeLast($flight->flight_code, '-RET');
                $siblingFlight = Flight::where('flight_code', $outboundCode)->first();
            } else {
                $siblingFlight = Flight::where('flight_code', $flight->flight_code . '-RET')->first();
            }

            $this->siblingCodeToDelete = $siblingFlight ? $siblingFlight->flight_code : null;
            $reservationsCount = $flight->reservations()->count();
            $siblingReservationsCount = $siblingFlight ? $siblingFlight->reservations()->count() : 0;

            $this->reservationsCount = $reservationsCount + $siblingReservationsCount;
            $this->missionHasReservations = $this->reservationsCount > 0;

            if ($this->missionHasReservations) {
                $this->showConflictDeleteModal = true;
            } else {
                $this->showDeleteModal = true;
            }
        }
    }

    /**
     * Returns the unique gestors who have clients with active reservations on a flight.
     */
    private function getAffectedGestors(int $flightId): \Illuminate\Support\Collection
    {
        $gestorIds = Reservation::where('space_flight_id', $flightId)
            ->whereNotIn('status', ['Cancelada', 'Cancelled'])
            ->whereHas('user', fn($q) => $q->whereNotNull('assigned_manager_id'))
            ->with('user')
            ->get()
            ->pluck('user.assigned_manager_id')
            ->unique();

        if ($gestorIds->isEmpty()) {
            return collect();
        }

        return User::whereIn('id', $gestorIds)->where('role', 'gestor')->get();
    }

    public function cancelFlightAndNotify()
    {
        $flight = Flight::find($this->deleteId);

        if ($flight) {
            $isReturn = str_ends_with($flight->flight_code, '-RET');
            if ($isReturn) {
                $outboundCode = Str::beforeLast($flight->flight_code, '-RET');
                $siblingFlight = Flight::where('flight_code', $outboundCode)->first();
            } else {
                $siblingFlight = Flight::where('flight_code', $flight->flight_code . '-RET')->first();
            }

            $outbound = $isReturn ? $siblingFlight : $flight;
            $return = $isReturn ? $flight : $siblingFlight;

            // Collect all affected flight IDs
            $flightIds = collect([$outbound?->id, $return?->id])->filter()->values();

            // ── Crear tareas automáticas a gestores afectados ──────────────
            $affectedGestors = collect();
            foreach ($flightIds as $fid) {
                $affectedGestors = $affectedGestors->merge($this->getAffectedGestors($fid));
            }
            $affectedGestors = $affectedGestors->unique('id');

            $cancelReasonLabel = match ($this->cancelReason) {
                'technical' => 'causa técnica de la nave/sistemas',
                'weather' => 'condiciones meteorológicas espaciales adversas',
                default => 'decisión administrativa',
            };

            $refundNote = match ($this->cancelReason) {
                'technical', 'weather' => 'Los clientes CON seguro de reembolso tienen derecho a reembolso 100% o reubicación gratuita. Gestiona las opciones con cada pasajero.',
                default => 'Aplica política estándar. Los clientes CON seguro recibirán reembolso según las condiciones de su póliza.',
            };

            $priority = ($this->cancelReason === 'technical' || $this->cancelReason === 'weather') ? 'urgente' : 'alta';
            $flightCode = $outbound?->flight_code ?? $flight->flight_code;

            foreach ($affectedGestors as $gestor) {
                // Obtener nombres de pasajeros afectados (antes de cancelar la reserva)
                $passengerNames = Reservation::whereIn('space_flight_id', $flightIds->toArray())
                    ->whereNotIn('status', ['Cancelada', 'Cancelled'])
                    ->whereHas('user', fn($q) => $q->where('assigned_manager_id', $gestor->id))
                    ->with('passenger')
                    ->get()
                    ->pluck('passenger.full_name')
                    ->unique()
                    ->implode(', ');

                Task::create([
                    'assigned_gestor_id' => $gestor->id,
                    'created_by' => auth()->id(),
                    'title' => "Vuelo {$flightCode} Cancelado — Gestiona a tus pasajeros",
                    'description' => "El vuelo {$flightCode} ha sido cancelado por {$cancelReasonLabel}.\n\n{$refundNote}\n\nPasajeros afectados de tu cartera: {$passengerNames}",
                    'type' => 'flight_cancelled',
                    'status' => 'Pendiente',
                    'priority' => $priority,
                    'payload' => [
                        'flight_id' => $outbound?->id,
                        'flight_code' => $flightCode,
                        'cancel_reason' => $this->cancelReason,
                    ],
                ]);
            }

            // ── AHORA SÍ: Actualizar estados de reservas y vuelos ──────────
            if ($outbound) {
                $outbound->reservations()->update(['status' => 'Cancelada']);
                $outbound->update(['status' => 'cancelled']);
            }

            if ($return) {
                $return->reservations()->update(['status' => 'Cancelada']);
                $return->update(['status' => 'cancelled']);
            }

            $tasksCreated = $affectedGestors->count();
            $msg = "Misión cancelada por {$cancelReasonLabel}.";
            if ($tasksCreated > 0) {
                $msg .= " Se han enviado {$tasksCreated} misión(es) a los gestores afectados.";
            } else {
                $msg .= " No hay gestores asignados a los pasajeros de este vuelo.";
            }

            session()->flash('message', $msg);
        }

        $this->cancelReason = 'voluntary';
        $this->resetInputFields();
        $this->showConflictDeleteModal = false;
    }

    public function redirectToEdit()
    {
        $this->edit($this->deleteId);
        $this->showConflictDeleteModal = false;
        $this->showDeleteModal = false;
    }

    public function executeDelete()
    {
        if ($this->deleteId) {
            $flight = Flight::find($this->deleteId);
            if ($flight) {
                $isReturn = str_ends_with($flight->flight_code, '-RET');
                if ($isReturn) {
                    $outboundCode = Str::beforeLast($flight->flight_code, '-RET');
                    $siblingFlight = Flight::where('flight_code', $outboundCode)->first();
                } else {
                    $siblingFlight = Flight::where('flight_code', $flight->flight_code . '-RET')->first();
                }

                if ($siblingFlight) {
                    $siblingFlight->forceDelete();
                }
                $flight->forceDelete();

                session()->flash('message', 'Eliminación en cascada realizada con éxito.');
            }
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;

        if ($this->isEditing && $this->flightId === $this->deleteId) {
            $this->setCreateMode();
        }
        $this->resetInputFields();
    }
}
