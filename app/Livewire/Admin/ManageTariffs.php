<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\TerrestrialFlight;
use App\Models\Location;
use App\Models\Starship;
use App\Models\PriceLog;

class ManageTariffs extends Component
{
    // ── Tab activo ────────────────────────────────────────────────────────────
    public string $activeTab = 'services'; // flights | hotels | terrestrial | services | operational

    public string $search = '';

    // ── Modal "Actualizar Tarifa" ─────────────────────────────────────────────
    public bool $showUpdateModal = false;
    public string $itemType = '';
    public int $itemId = 0;
    public string $itemName = '';
    public float $currentPrice = 0;
    public string $newPrice = '';
    public string $updateReason = '';
    public string $unit = '€'; // unit label for display

    protected function rules(): array
    {
        return [
            'newPrice' => 'required|numeric|min:0',
            'updateReason' => 'required|string|min:5|max:255',
        ];
    }

    protected $messages = [
        'newPrice.required' => 'Introduce el nuevo valor.',
        'newPrice.numeric' => 'Debe ser un valor numérico.',
        'updateReason.required' => 'El motivo del cambio es obligatorio.',
        'updateReason.min' => 'Describe el motivo con al menos 5 caracteres.',
    ];

    // ── Open modal ────────────────────────────────────────────────────────────
    public function openUpdateModal(string $type, int $id, string $name, float $price, string $unit = '€'): void
    {
        $this->reset(['newPrice', 'updateReason']);
        $this->resetValidation();

        $this->itemType = $type;
        $this->itemId = $id;
        $this->itemName = $name;
        $this->currentPrice = $price;
        $this->newPrice = (string) $price;
        $this->unit = $unit;
        $this->showUpdateModal = true;
    }

    // ── Apply tariff update ───────────────────────────────────────────────────
    public function applyUpdate(): void
    {
        $this->validate();

        $newPriceFloat = (float) $this->newPrice;

        // Write the price log BEFORE updating
        PriceLog::record(
            itemType: $this->itemType,
            itemId: $this->itemId,
            oldPrice: $this->currentPrice,
            newPrice: $newPriceFloat,
            reason: $this->updateReason,
        );

        // Update the actual record depending on the type
        match (true) {
            $this->itemType === 'flight' => Flight::findOrFail($this->itemId)->update(['base_price' => $newPriceFloat]),
            $this->itemType === 'hotel' => Hotel::findOrFail($this->itemId)->update(['price_per_night' => $newPriceFloat]),
            $this->itemType === 'terrestrial_flight' => TerrestrialFlight::findOrFail($this->itemId)->update(['price' => $newPriceFloat]),
            $this->itemType === 'vip_transfer_location' => Location::findOrFail($this->itemId)->update(['transport_price' => $newPriceFloat]),
            $this->itemType === 'starship_cost_per_au' => Starship::findOrFail($this->itemId)->update(['operational_cost_per_au' => $newPriceFloat]),
            $this->itemType === 'starship_cruise_speed' => Starship::findOrFail($this->itemId)->update(['cruise_speed_au' => $newPriceFloat]),
            $this->itemType === 'starship_crew_hourly' => Starship::findOrFail($this->itemId)->update(['crew_hourly_rate' => $newPriceFloat]),
            // Global PriceLog-only entries (no DB row to update, just insert the log)
            default => null,
        };

        session()->flash('tariff_message', "Tarifa de \"{$this->itemName}\" actualizada de {$this->unit}" .
            number_format($this->currentPrice, 2, ',', '.') . " a {$this->unit}" .
            number_format($newPriceFloat, 2, ',', '.') . '.');

        $this->showUpdateModal = false;
        $this->reset(['itemType', 'itemId', 'itemName', 'currentPrice', 'newPrice', 'updateReason', 'unit']);
    }

    public function render()
    {
        $search = $this->search;

        $flights = Flight::with(['destination', 'starship'])
            ->when(
                $search,
                fn($q) => $q
                    ->where('flight_code', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%")
                    ->orWhereHas('destination', fn($d) => $d->where('name', 'like', "%$search%"))
            )
            ->where('departure_date', '>', now())
            ->orderBy('departure_date')
            ->get();

        $hotels = Hotel::with('location')
            ->when(
                $search,
                fn($q) => $q
                    ->where('name', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%")
                    ->orWhereHas('location', fn($l) => $l->where('name', 'like', "%$search%"))
            )
            ->orderBy('name')
            ->get();

        $terrestrialFlights = TerrestrialFlight::with(['originLocation', 'destinationLocation'])
            ->when(
                $search,
                fn($q) => $q
                    ->where('id', 'like', "%$search%")
                    ->orWhere('flight_number', 'like', "%$search%")
                    ->orWhere('airline', 'like', "%$search%")
                    ->orWhereHas('originLocation', fn($l) => $l->where('name', 'like', "%$search%")->orWhere('code', 'like', "%$search%"))
                    ->orWhereHas('destinationLocation', fn($l) => $l->where('name', 'like', "%$search%")->orWhere('code', 'like', "%$search%"))
            )
            ->where('departure_datetime', '>', now())
            ->orderBy('departure_datetime')
            ->get();

        $locations = Location::orderBy('name')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%")->orWhere('code', 'like', "%$search%"))
            ->get();

        $starships = Starship::where('status', '!=', 'retired')->orderBy('name')->get();

        // Global service rates from PriceLog
        $globalServices = [
            [
                'type' => 'training',
                'label' => 'Iris Training',
                'price' => PriceLog::getCurrentPrice('training'),
                'unit' => '$',
                'desc' => 'Tasa fija por pasajero — Cualificación espacial',
            ],
            [
                'type' => 'passport_management',
                'label' => 'Gestión Pasaporte Espacial',
                'price' => PriceLog::getCurrentPrice('passport_management'),
                'unit' => '$',
                'desc' => 'Tasa fija — Visados y tasas gubernamentales',
            ],
            [
                'type' => 'refund_insurance',
                'label' => 'Seguro de Reembolso',
                'price' => PriceLog::getCurrentPrice('refund_insurance'),
                'unit' => '%',
                'desc' => 'Porcentaje sobre el total de la reserva',
            ],
        ];

        if ($search) {
            $globalServices = array_filter($globalServices, function ($s) use ($search) {
                return stripos($s['label'], $search) !== false || stripos($s['type'], $search) !== false;
            });
        }

        $operationalRates = [];
        foreach ($starships as $ship) {
            if ($search && stripos($ship->name, $search) === false && stripos($ship->id, $search) === false) {
                continue;
            }
            $operationalRates[] = [
                'starship_id' => $ship->id,
                'label' => $ship->name,
                'items' => [
                    [
                        'type' => 'starship_cost_per_au',
                        'label' => 'Coste Base por AU',
                        'icon' => '🚀',
                        'price' => $ship->operational_cost_per_au,
                        'unit' => '€/AU',
                        'desc' => 'Gasto de combustible y mantenimiento por Unidad Astronómica',
                    ],
                    [
                        'type' => 'starship_cruise_speed',
                        'label' => 'Velocidad de Crucero',
                        'icon' => '⏱️',
                        'price' => $ship->cruise_speed_au,
                        'unit' => 'h/AU',
                        'desc' => 'Duración del viaje estimada por Unidad Astronómica',
                    ],
                    [
                        'type' => 'starship_crew_hourly',
                        'label' => 'Tarifa Tripulación (Hora)',
                        'icon' => '👨‍✈️',
                        'price' => $ship->crew_hourly_rate,
                        'unit' => '€/h',
                        'desc' => 'Coste monetario de la tripulación por cada hora de vuelo',
                    ],
                ]
            ];
        }

        // Price log (last 40 entries)
        $priceLogs = PriceLog::with('admin')
            ->latest()
            ->take(40)
            ->get();

        return view('livewire.admin.manage-tariffs', [
            'flights' => $flights,
            'hotels' => $hotels,
            'terrestrialFlights' => $terrestrialFlights,
            'locations' => $locations,
            'starships' => $starships,
            'globalServices' => $globalServices,
            'operationalRates' => $operationalRates,
            'priceLogs' => $priceLogs,
        ])->layout('layouts.app');
    }

    public function exportLogs()
    {
        $logs = PriceLog::with('admin')->orderBy('created_at', 'desc')->get();
        $filename = "audit_logs_" . date('Ymd_His') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $callback = function() use($logs) {
            $file = fopen('php://output', 'w');
            // Adding BOM for Excel UTF-8 compatibility
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
