<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'code', 'transport_price', 'country_code'];

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function departingFlights()
    {
        return $this->hasMany(TerrestrialFlight::class, 'origin_id');
    }

    public function arrivingFlights()
    {
        return $this->hasMany(TerrestrialFlight::class, 'destination_id');
    }
}
