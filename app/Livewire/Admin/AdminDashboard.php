<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\Flight;
use App\Models\Starship;
use App\Models\PriceLog;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public function render()
    {
        // 1. Financial Widgets
        $projectedIncome = Reservation::where('payment_status', 'pending')->sum('total_price');
        $realIncome = Reservation::where('payment_status', 'paid')->sum('total_price');
        
        // Profitability: Average of mission_profitability from Flights
        $avgProfitability = Flight::avg('mission_profitability') ?? 0;

        // Critical Occupancy: Flights below 70% and upcoming
        $criticalFlights = Flight::where('departure_date', '>', now())
            ->with(['destination', 'starship'])
            ->get()
            ->filter(fn($f) => $f->occupancy_percentage < 70)
            ->take(3);

        // 2. Mission Control
        $starshipsStatus = [
            'in_flight' => Starship::whereHas('flights', function($q) {
                $q->where('status', 'in_orbit');
            })->count(),
            'maintenance' => Starship::where('status', 'maintenance')->count(),
            'ready' => Starship::where('status', 'active')->whereDoesntHave('flights', function($q) {
                $q->where('status', 'in_orbit');
            })->count(),
        ];

        $nextLaunches = Flight::where('departure_date', '>', now())
            ->where('status', 'scheduled')
            ->where('departure_date', '<', now()->addHours(48))
            ->with(['destination', 'starship'])
            ->orderBy('departure_date', 'asc')
            ->get();

        $totalAURecorridas = Flight::where('status', 'landed')
            ->sum('au_distance');

        // 4. Alerts & Auditing
        $recentPriceLogs = PriceLog::with('admin')
            ->latest()
            ->take(6)
            ->get();

        return view('livewire.admin.admin-dashboard', [
            'projectedIncome' => $projectedIncome,
            'realIncome' => $realIncome,
            'avgProfitability' => $avgProfitability,
            'criticalFlights' => $criticalFlights,
            'starshipsStatus' => $starshipsStatus,
            'nextLaunches' => $nextLaunches,
            'totalAURecorridas' => $totalAURecorridas,
            'recentPriceLogs' => $recentPriceLogs,
        ])->layout('layouts.app');
    }
}
