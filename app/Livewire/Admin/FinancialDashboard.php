<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\HasResponsivePagination;
use App\Models\Reservation;
use Illuminate\Support\Carbon;

class FinancialDashboard extends Component
{
    use WithPagination, HasResponsivePagination;

    protected $paginationTheme = 'simple-tailwind';
    public string $period = 'year';
    public string $search = '';

    public ?string $selectedMonth = 'all';
    public ?string $selectedYear = null;
    public ?string $customStart = null;
    public ?string $customEnd = null;

    public ?int $selectedFlightId = null;

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

                // Búsqueda por ID numérico (soporta 0011 -> 11)
                $cleanSearch = ltrim($this->search, '0');
                if (is_numeric($cleanSearch) && $cleanSearch !== '') {
                    $q->orWhere('id', (int) $cleanSearch);
                }
            });
        }

        // Income Metrics based on FLIGHT departure date (Operational View)
        $paidReservations = (clone $query)
            ->where('payment_status', 'paid')
            ->whereHas('spaceFlight', function($q) use ($start, $end) {
                $q->whereBetween('departure_date', [$start, $end]);
            })
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

        // Subtract actual refunds from net income
        $refundsQuery = \App\Models\Expense::where('category', 'Reembolso')
            ->whereBetween('expense_date', [$start, $end]);
        $totalRefunds = $refundsQuery->sum('amount');
        $netIncome -= $totalRefunds;

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

        $selectedYearVal = $this->selectedYear ?? now()->year;
        $annualPaidReservations = Reservation::where('payment_status', 'paid')
            ->whereHas('spaceFlight', function($q) use ($selectedYearVal) {
                $q->whereYear('departure_date', $selectedYearVal);
            })
            ->get();

        $annualNetIncome = $annualPaidReservations->sum('total_price');
        $annualGrossIncome = 0;
        foreach ($annualPaidReservations as $res) {
            $snap = is_string($res->price_snapshot) ? json_decode($res->price_snapshot, true) : $res->price_snapshot;
            if ($snap && isset($snap['subtotal'])) {
                $annualGrossIncome += $snap['subtotal'];
            } else {
                $annualGrossIncome += $res->total_price;
            }
        }

        $annualRefunds = \App\Models\Expense::where('category', 'Reembolso')
            ->whereYear('expense_date', $selectedYearVal)
            ->sum('amount');
        $annualNetIncome -= $annualRefunds;

        $annualExpenses = \App\Models\Flight::where('status', '!=', 'cancelled')
            ->whereYear('departure_date', $selectedYearVal)
            ->sum('operational_cost');

        $annualProjectedIncome = \App\Models\Flight::where('status', '!=', 'cancelled')
            ->whereYear('departure_date', $selectedYearVal)
            ->get()
            ->sum('max_income');

        $annualProfit = $annualNetIncome - $annualExpenses;

        $transactions = (clone $query)
            ->whereIn('payment_status', ['paid', 'pending', 'refunded'])
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->paginate($this->getPerPage(), pageName: 'txPage');

        $flightsWithExpensesQuery = \App\Models\Flight::with(['starship', 'destination'])
            ->whereBetween('departure_date', [$start, $end])
            ->where('status', '!=', 'cancelled');

        if ($this->search) {
            $flightsWithExpensesQuery->where(function ($q) {
                $q->where('flight_code', 'like', '%' . $this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%');
                $cleanSearch = ltrim($this->search, '0');
                if (is_numeric($cleanSearch) && $cleanSearch !== '') {
                    $q->orWhere('id', (int) $cleanSearch);
                }
            });
        }

        $flightsWithExpenses = $flightsWithExpensesQuery->orderByDesc('departure_date')
            ->paginate($this->getPerPage(), pageName: 'expPage');

        $this->chartData = $this->buildChartData($start, $end);

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
            'totalRefunds' => $totalRefunds,
            'annualNetIncome' => $annualNetIncome,
            'annualGrossIncome' => $annualGrossIncome,
            'annualExpenses' => $annualExpenses,
            'annualProfit' => $annualProfit,
            'annualProjectedIncome' => $annualProjectedIncome,
            'transactions' => $transactions,
            'flightsWithExpenses' => $flightsWithExpenses,
            'criticalFlights' => $this->getAnalysisFlights(),
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

    private function getAnalysisFlights()
    {
        $q = \App\Models\Flight::with('starship')
            ->whereNotIn('status', ['cancelled', 'landed'])
            ->where('departure_date', '>', now());

        if ($this->search) {
            $q->where(function ($sub) {
                $sub->where('flight_code', 'like', '%' . $this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%');
                $cleanSearch = ltrim($this->search, '0');
                if (is_numeric($cleanSearch) && $cleanSearch !== '') {
                    $sub->orWhere('id', (int) $cleanSearch);
                }
            });
            return $q->orderBy('departure_date')->paginate($this->getPerPage(), pageName: 'anaPage');
        }

        // Si no hay búsqueda, mostramos solo los críticos (< 80%)
        return \App\Models\Flight::with('starship')
            ->whereNotIn('status', ['cancelled', 'landed'])
            ->where('departure_date', '>', now())
            ->whereRaw('(SELECT COUNT(*) FROM reservations WHERE reservations.space_flight_id = flights.id AND reservations.payment_status = \'paid\') / CAST(NULLIF(flights.total_capacity, 0) AS FLOAT) < 0.8')
            ->orderBy('departure_date')
            ->paginate($this->getPerPage(), pageName: 'anaPage');
    }

    private function buildChartData($start, $end)
    {
        $labels = [];
        $net = [];
        $exp = [];
        $proj = [];
        $year = $this->selectedYear ?? now()->year;

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create()->month($m)->locale('es')->translatedFormat('M');

            $n = Reservation::where('payment_status', 'paid')
                ->whereHas('spaceFlight', function($q) use ($m, $year) {
                    $q->whereMonth('departure_date', $m)
                      ->whereYear('departure_date', $year);
                })
                ->sum('total_price');

            $eQuery = \App\Models\Flight::where('status', '!=', 'cancelled')
                ->whereMonth('departure_date', $m)
                ->whereYear('departure_date', $year);

            $e = $eQuery->sum('operational_cost');

            $pSum = 0;
            $flights = (clone $eQuery)->get();
            foreach ($flights as $f) {
                $pSum += $f->max_income;
            }

            $net[] = $n;
            $exp[] = $e;
            $proj[] = $pSum;
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

    public function resetFilters()
    {
        $this->search = '';
        $this->period = 'year';
        $this->selectedMonth = now()->format('m');
        $this->selectedYear = now()->format('Y');
        $this->customStart = null;
        $this->customEnd = null;
        $this->selectedFlightId = null;
        $this->dispatch('chart-refreshed');
    }
}
