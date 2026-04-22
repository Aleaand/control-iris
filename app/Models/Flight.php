<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flight extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'departure_date'        => 'datetime',
        'arrival_date'          => 'datetime',
        'base_price'            => 'decimal:2',
        'previous_base_price'   => 'decimal:2',
        'price_updated_at'      => 'datetime',
        'au_distance'           => 'decimal:2',
        'operational_cost'      => 'decimal:2',
        'mission_speed_au'      => 'decimal:4',
        'crew_hourly_rate'      => 'decimal:2',
        'crew_daily_rate'       => 'decimal:2',
        'launch_cost_earth'     => 'decimal:2',
        'landing_cost_earth'    => 'decimal:2',
        'launch_cost_planet'    => 'decimal:2',
        'landing_cost_planet'   => 'decimal:2',
        'return_departure_date' => 'datetime',
        'return_base_price'     => 'decimal:2',
        'mission_profitability' => 'decimal:2',
    ];

    /**
     * Call this before saving when base_price changes.
     * Records the previous price and who changed it for the audit trail.
     */
    public function recordPriceChange(int $adminId): void
    {
        $this->previous_base_price = $this->getOriginal('base_price');
        $this->price_updated_by    = $adminId;
        $this->price_updated_at    = now();
    }

    public function starship()
    {
        return $this->belongsTo(Starship::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function origin()
    {
        return $this->belongsTo(Destination::class, 'origin_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'space_flight_id');
    }

    public function getOccupancyPercentageAttribute()
    {
        if ($this->total_capacity <= 0) {
            return 0;
        }
        return round(($this->booked_passengers / $this->total_capacity) * 100, 1);
    }

    public function getRealIncomeAttribute()
    {
        return $this->reservations()->where('payment_status', 'paid')->sum('total_price');
    }

    public function getMaxIncomeAttribute()
    {
        return $this->total_capacity * $this->base_price;
    }

    public function getProjectedIncome80Attribute()
    {
        return $this->max_income * 0.8;
    }

    /**
     * Devuelve cuántos asientos libres hay en una clase concreta.
     * Usado por executeGroupSave() para la validación atómica.
     */
    public static function availableSeats(int $flightId, string $seatType): int
    {
        $flight = static::with('starship')->find($flightId);
        if (!$flight || !$flight->starship) return 0;

        $capacity = strtolower($seatType) === 'supernova'
            ? $flight->starship->vip_capacity
            : $flight->starship->general_capacity;

        $occupied = Reservation::where('space_flight_id', $flightId)
            ->where('seat_type', $seatType)
            ->whereNotIn('status', ['Cancelada', 'Cancelled'])
            ->where('is_adenda', false)
            ->count();

        return max(0, $capacity - $occupied);
    }
}
