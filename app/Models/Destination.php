<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destination extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'distance_au'     => 'decimal:2',
        'max_distance_au' => 'decimal:2',
        'launch_fee'      => 'decimal:2',
        'landing_fee'     => 'decimal:2',
    ];

    /**
     * Devuelve la distancia efectiva para presupuesto (Afelio).
     * Si max_distance_au está configurada la usa; si no, cae al distance_au mínimo.
     */
    public function getEffectiveDistanceAu(): float
    {
        return $this->max_distance_au
            ? (float) $this->max_distance_au
            : (float) $this->distance_au;
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }
}
