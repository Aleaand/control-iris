<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerrestrialFlight extends Model
{
    protected $fillable = [
        'flight_number',
        'airline',
        'origin_id',
        'destination_id',
        'departure_datetime',
        'arrival_datetime',
        'price',
        'baggage_price',
        'executive_capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_datetime' => 'datetime',
            'arrival_datetime' => 'datetime',
        ];
    }

    public function originLocation()
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destinationLocation()
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function reservationLogistics()
    {
        return $this->hasMany(ReservationLogistic::class, 'terrestrial_flight_id');
    }
}
