<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Starship extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'operational_cost_per_au' => 'decimal:2',
        'cruise_speed_au'         => 'decimal:4',
        'crew_hourly_rate'        => 'decimal:2',
        'crew_daily_rate'         => 'decimal:2',
        'depreciation_per_au'     => 'decimal:2',
        'maintenance_start_date'  => 'date',
        'maintenance_end_date'    => 'date',
    ];

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    public function currentLocation()
    {
        return $this->belongsTo(Destination::class, 'current_location_id');
    }

    public function operationalCostLogs()
    {
        return $this->hasMany(OperationalCost::class);
    }
}
