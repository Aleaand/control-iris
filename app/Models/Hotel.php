<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'location_id',
        'galactic_stars',
        'price_per_night',
        'total_rooms',
    ];

    public function logistics()
    {
        return $this->hasMany(ReservationLogistic::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
