<?php

namespace App\Livewire\Gestor;

use Livewire\Component;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\PriceLog;
use App\Models\Reservation;
use App\Models\ReservationLogistic;

class GestorRadar extends Component
{
    public string $activeTab      = 'flights';
    public string $searchFlight   = '';
    public string $searchHotel    = '';

    // Filtros de Radar
    public bool $showDateModal = false;
    public $filterDate;
    public $dateSearchMode = 'exact'; // 'exact' o 'flexible'
    public $filterMonth;
    public $filterYear;
    public $filterDestination;
    public $filterOrigin;
    public $filterMaxPrice;

    public function render()
    {
        $flights = Flight::with(['starship', 'destination', 'origin'])
            ->where('departure_date', '>', now())
            ->when($this->searchFlight, fn($q) => $q->where(function ($q) {
                $q->where('flight_code', 'like', '%'.$this->searchFlight.'%')
                  ->orWhereHas('destination', fn($d) => $d->where('name', 'like', '%'.$this->searchFlight.'%'));
            }))
            // Lógica de Fecha (Exacta vs Flexible)
            ->when($this->dateSearchMode === 'exact' && $this->filterDate, fn($q) => $q->whereDate('departure_date', $this->filterDate))
            ->when($this->dateSearchMode === 'flexible', function($q) {
                if ($this->filterMonth) $q->whereMonth('departure_date', $this->filterMonth);
                if ($this->filterYear) $q->whereYear('departure_date', $this->filterYear);
            })
            ->when($this->filterDestination, fn($q) => $q->where('destination_id', $this->filterDestination))
            ->when($this->filterOrigin, fn($q) => $q->where('origin_id', $this->filterOrigin))
            ->when($this->filterMaxPrice, fn($q) => $q->where('base_price', '<=', $this->filterMaxPrice))
            ->orderBy('departure_date')
            ->get()
            ->map(function ($f) {
                $novaOcc = Reservation::where('space_flight_id', $f->id)->where('seat_type', 'nova')->whereNotIn('status', ['Cancelada'])->count();
                $snOcc   = Reservation::where('space_flight_id', $f->id)->where('seat_type', 'supernova')->whereNotIn('status', ['Cancelada'])->count();
                $f->nova_cap   = $f->starship?->general_capacity ?? 0;
                $f->sn_cap     = $f->starship?->vip_capacity ?? 0;
                $f->nova_occ   = $novaOcc;
                $f->sn_occ     = $snOcc;
                $f->nova_free  = max(0, $f->nova_cap - $novaOcc);
                $f->sn_free    = max(0, $f->sn_cap - $snOcc);
                $f->nova_pct   = $f->nova_cap > 0 ? round($novaOcc / $f->nova_cap * 100) : 0;
                $f->sn_pct     = $f->sn_cap > 0 ? round($snOcc / $f->sn_cap * 100) : 0;
                return $f;
            });

        $hotels = Hotel::with('location')
            ->when($this->searchHotel, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->searchHotel.'%')
                  ->orWhereHas('location', fn($l) => $l->where('name', 'like', '%'.$this->searchHotel.'%'));
            }))
            ->get()
            ->map(function ($h) {
                $occ = ReservationLogistic::where('hotel_id', $h->id)
                    ->whereHas('reservation', fn($r) => $r->whereNotIn('status', ['Cancelada']))
                    ->count();
                $h->occupied  = $occ;
                $h->available = max(0, $h->total_rooms - $occ);
                $h->pct       = $h->total_rooms > 0 ? round($occ / $h->total_rooms * 100) : 0;
                return $h;
            });

        $priceLogs = PriceLog::orderBy('created_at', 'desc')->take(20)->get();
        $destinations = \App\Models\Destination::orderBy('name')->get();

        return view('livewire.gestor.radar', compact('flights', 'hotels', 'priceLogs', 'destinations'))
            ->layout('layouts.gestor');
    }
}
