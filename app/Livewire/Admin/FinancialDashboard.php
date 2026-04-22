<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Reservation;
use Illuminate\Support\Carbon;

class FinancialDashboard extends Component
{
    public string $period = 'year';
    public string $search = '';

    public ?string $selectedMonth = 'all';
    public ?string $selectedYear = null;
    public ?string $customStart = null;
    public ?string $customEnd = null;

    public ?int $selectedFlightId = null;
    public bool $upcomingOnly = false;

    public array $chartData = [];
    public int $maxAvailableYear = 2024;

    public function mount()
    {
        // Calculate the maximum year dynamically
        $maxDataDate = max(
            Reservation::where('payment_status', 'paid')->max('paid_at') ?? now(),
            \App\Models\Flight::where('status', '!=', 'cancelled')->max('departure_date') ?? now()
        );
        $foundMaxYear = Carbon::parse($maxDataDate)->year;
        $this->maxAvailableYear = max(now()->year, min($foundMaxYear, now()->year + 10));

        $this->selectedMonth = now()->format('m');
        $this->selectedYear = now()->format('Y');
    }

    public function render()
    {
        $now = Carbon::now();

        // Simplified Range Logic
        [$start, $end] = $this->getRange();

        $query = Reservation::with(['user', 'spaceFlight']);
        $flightQuery = \App\Models\Flight::query();

        // Search Filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id_locator', 'like', '%' . $this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%');
            });
        }

        // Income Metrics
        $paidReservations = (clone $query)
            ->where('payment_status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->get();

        $grossIncome = 0;
        $totalDiscounts = 0;
        $netIncome = 0;

        foreach ($paidReservations as $res) {
            $snap = is_string($res->price_snapshot) ? json_decode($res->price_snapshot, true) : $res->price_snapshot;
            if ($snap && isset($snap['subtotal'])) {
                $grossIncome += $snap['subtotal'];
                $totalDiscounts += ($snap['discount_amount'] ?? 0) + ($snap['adj_amount'] ?? 0);
            }
            $netIncome += $res->total_price;
        }

        // Consolidated Expenses
        $flightsInPeriod = (clone $flightQuery)
            ->whereBetween('departure_date', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->get();

        $totalExpenses = $flightsInPeriod->sum('operational_cost');
        $totalProfit = $netIncome - $totalExpenses;

        $totalCount = $paidReservations->count();
        $avgTicket = $totalCount > 0 ? round($netIncome / $totalCount, 2) : 0;

        $pending = Reservation::where('payment_status', 'pending')->count();
        $failed = Reservation::where('payment_status', 'failed')->count();

        // transactions Table
        $transactions = (clone $query)
            ->whereIn('payment_status', ['paid', 'pending'])
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->take(30)
            ->get();

        // Expenses Table
        $flightsWithExpenses = \App\Models\Flight::with(['starship', 'destination'])
            ->whereBetween('departure_date', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('departure_date')
            ->get();

        // Chart Data
        $this->chartData = $this->buildChartData($start, $end);

        // Selection Detail
        $flightDetails = null;
        if ($this->selectedFlightId) {
            $flightDetails = \App\Models\Flight::with(['starship', 'destination'])->find($this->selectedFlightId);
        }

        return view('livewire.admin.financial-dashboard', [
            'grossIncome' => $grossIncome,
            'totalDiscounts' => $totalDiscounts,
            'netIncome' => $netIncome,
            'totalExpenses' => $totalExpenses,
            'totalProfit' => $totalProfit,
            'totalCount' => $totalCount,
            'avgTicket' => $avgTicket,
            'pending' => $pending,
            'failed' => $failed,
            'transactions' => $transactions,
            'flightsWithExpenses' => $flightsWithExpenses,
            'criticalFlights' => $this->getCriticalFlights(),
            'startDate' => $start,
            'endDate' => $end,
            'flightDetails' => $flightDetails,
            'chartData' => $this->chartData,
        ])->layout('layouts.app');
    }

    private function getRange()
    {
        $now = now();

        if ($this->period === 'custom' && $this->customStart && $this->customEnd) {
            return [Carbon::parse($this->customStart)->startOfDay(), Carbon::parse($this->customEnd)->endOfDay()];
        }

        if ($this->period === 'month' && $this->selectedMonth && $this->selectedYear) {
            if ($this->selectedMonth === 'all') {
                $d = Carbon::createFromDate($this->selectedYear, 1, 1);
                return [$d->copy()->startOfYear(), $d->copy()->endOfYear()];
            }
            $d = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1);
            return [$d->copy()->startOfMonth(), $d->copy()->endOfMonth()];
        }

        if ($this->period === 'year' && $this->selectedYear) {
            $d = Carbon::createFromDate($this->selectedYear, 1, 1);
            return [$d->copy()->startOfYear(), $d->copy()->endOfYear()];
        }

        return match ($this->period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            default => [Carbon::create(2020, 1, 1), $now->copy()->addYears(10)],
        };
    }

    private function getCriticalFlights()
    {
        return \App\Models\Flight::with('starship')
            ->whereNotIn('status', ['cancelled', 'landed'])
            ->where('departure_date', '>', now())
            ->get()
            ->filter(fn($f) => $f->occupancy_percentage < 80)
            ->sortBy('occupancy_percentage')
            ->take(8);
    }

    private function buildChartData($start, $end)
    {
        $labels = [];
        $net = [];
        $exp = [];
        $proj = [];

        // MODE 1: ALL TIME (By Year)
        if ($this->period === 'all') {
            $minYear = 2024;
            $maxDataDate = max(
                Reservation::where('payment_status', 'paid')->max('paid_at') ?? now(),
                \App\Models\Flight::where('status', '!=', 'cancelled')->max('departure_date') ?? now()
            );
            $foundMaxYear = Carbon::parse($maxDataDate)->year;
            // Cap at a reasonable future (e.g. next 10 years) to avoid DB inconsistencies
            $maxYear = min($foundMaxYear, now()->year + 10);

            for ($y = $minYear; $y <= $maxYear; $y++) {
                $labels[] = $y;
                
                $n = Reservation::where('payment_status', 'paid')
                    ->whereYear('paid_at', $y)
                    ->sum('total_price');

                $eQuery = \App\Models\Flight::where('status', '!=', 'cancelled')
                    ->whereYear('departure_date', $y);
                
                if ($this->upcomingOnly) {
                    $eQuery->where('departure_date', '>=', now());
                }
                
                $e = $eQuery->sum('operational_cost');
                
                // Projected Income
                $pSum = 0;
                $flights = (clone $eQuery)->get();
                foreach($flights as $f) { $pSum += $f->max_income; }

                $net[] = $n;
                $exp[] = $e;
                $proj[] = $pSum;
            }
        } 
        // MODE 2: BY YEAR (12 Months)
        else {
            // Determine which year to show months for
            $year = ($this->period === 'year' || $this->period === 'month') 
                ? ($this->selectedYear ?? $start->year) 
                : $start->year;
            
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = Carbon::create()->month($m)->locale('es')->translatedFormat('M');
                
                $n = Reservation::where('payment_status', 'paid')
                    ->whereMonth('paid_at', $m)
                    ->whereYear('paid_at', $year)
                    ->sum('total_price');

                $eQuery = \App\Models\Flight::where('status', '!=', 'cancelled')
                    ->whereMonth('departure_date', $m)
                    ->whereYear('departure_date', $year);

                if ($this->upcomingOnly) {
                    $eQuery->where('departure_date', '>=', now());
                }

                $e = $eQuery->sum('operational_cost');

                // Projected Income
                $pSum = 0;
                $flights = (clone $eQuery)->get();
                foreach($flights as $f) { $pSum += $f->max_income; }

                $net[] = $n;
                $exp[] = $e;
                $proj[] = $pSum;
            }
        }

        return [
            'labels' => $labels,
            'net' => $net,
            'expenses' => $exp,
            'projected' => $proj
        ];
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        $this->search = '';
        if ($period !== 'custom') {
            $this->customStart = null;
            $this->customEnd = null;
        }

        if ($period === 'all') {
            $this->upcomingOnly = false;
        }

        $this->dispatch('chart-refreshed');
    }

    public function updatedSelectedYear($value)
    {
        $this->period = 'year';
        $this->customStart = null;
        $this->customEnd = null;
        $this->dispatch('chart-refreshed');
    }

    public function updatedSelectedMonth($value)
    {
        $this->period = $value === 'all' ? 'year' : 'month';
        $this->customStart = null;
        $this->customEnd = null;
        $this->dispatch('chart-refreshed');
    }

    public function selectFlight(int $id)
    {
        $this->selectedFlightId = $id;
    }

    public function toggleUpcoming()
    {
        $this->upcomingOnly = !$this->upcomingOnly;
    }
}
