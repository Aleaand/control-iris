<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Reservation;
use App\Models\Flight;
use App\Models\Starship;
use App\Models\PriceLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AdminDashboard extends Component
{
    public function render()
    {
        $currentYear = now()->year;

        // Finanzas anuales
        $projectedIncome = Reservation::where('payment_status', 'pending')
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');

        $ingresosReales = Reservation::where('payment_status', 'paid')
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');

        $totalGastos = Flight::whereYear('departure_date', $currentYear)
            ->where('status', '!=', 'cancelled')
            ->sum('operational_cost');

        $gananciasNetas = $ingresosReales - $totalGastos;
        $porcentajeGanancias = $ingresosReales > 0 ? ($gananciasNetas / $ingresosReales) * 100 : 0;

        // vuelos con ocupación inferior al 70% y próximos
        $criticalFlights = Flight::where('departure_date', '>', now())
            ->with(['destination', 'starship'])
            ->get()
            ->filter(fn($f) => $f->occupancy_percentage < 70)
            ->take(3);

        // Control de naves
        $starshipsStatus = [
            'in_flight' => Starship::whereHas('flights', function ($q) {
                $q->where('status', 'in_orbit');
            })->count(),
            'maintenance' => Starship::where('status', 'maintenance')->count(),
            'ready' => Starship::where('status', 'active')->whereHas('flights', function ($q) {
                $q->where('status', 'scheduled');
            })->count(),
            'idle' => Starship::where('status', 'active')->whereDoesntHave('flights', function ($q) {
                $q->whereIn('status', ['in_orbit', 'scheduled']);
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

        // 5. Database Status Monitoring
        try {
            DB::connection()->getPdo();
            $dbStatus = [
                'label' => 'Base de datos Activa',
                'color' => 'bg-emerald-500',
                'status' => 'online'
            ];
        } catch (\Exception $e) {
            $dbStatus = [
                'label' => 'Base de datos Desactivada',
                'color' => 'bg-red-500',
                'status' => 'offline'
            ];
        }

        return view('livewire.admin.admin-dashboard', [
            'projectedIncome' => $projectedIncome,
            'ingresosReales' => $ingresosReales,
            'porcentajeGanancias' => $porcentajeGanancias,
            'criticalFlights' => $criticalFlights,
            'starshipsStatus' => $starshipsStatus,
            'nextLaunches' => $nextLaunches,
            'totalAURecorridas' => $totalAURecorridas,
            'recentPriceLogs' => $recentPriceLogs,
            'dbStatus' => $dbStatus,
        ])->layout('layouts.app');
    }

    public function exportLogs()
    {
        $logs = PriceLog::with('admin')->orderBy('created_at', 'desc')->get();
        $filename = "logs_modificacion_precios_iris_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Admin', 'Tipo/Entidad', 'Item ID', 'Precio Anterior', 'Precio Nuevo', 'Motivo', 'Fecha']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->admin ? $log->admin->name : 'Sistema',
                    $log->item_type,
                    $log->item_id ?: 'Global',
                    $log->old_price,
                    $log->new_price,
                    $log->reason,
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return Response::streamDownload($callback, $filename, $headers);
    }
}
