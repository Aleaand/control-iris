<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'passenger_id',
        'space_flight_id',
        'id_locator',
        'booking_group_id',
        'group_name',
        'is_adenda',
        'parent_reservation_id',
        'seat_type',
        'seat_number',
        'total_price',
        'discount_applied',
        'status',
        'stripe_session_id',
        'stripe_receipt_url',
        'payment_status',
        'paid_at',
        'price_snapshot',
        'manual_adjustment_type',
        'manual_adjustment_value',
        'discount_note',
        'stripe_receipts',
    ];

    protected function casts(): array
    {
        return [
            'price_snapshot' => 'array',
            'paid_at' => 'datetime',
            'discount_applied' => 'boolean',
            'manual_adjustment_value' => 'decimal:2',
            'is_adenda' => 'boolean',
            'stripe_receipts' => 'array',
        ];
    }

    public function getRouteKeyName()
    {
        return 'id_locator';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function spaceFlight()
    {
        return $this->belongsTo(Flight::class, 'space_flight_id');
    }

    public function logistics()
    {
        return $this->hasOne(ReservationLogistic::class);
    }

    protected static function booted()
    {
        static::creating(function ($reservation) {
            if (empty($reservation->id_locator)) {
                $reservation->id_locator = (string) \Illuminate\Support\Str::uuid();
            }
            if (empty($reservation->booking_group_id)) {
                $reservation->booking_group_id = $reservation->id_locator; // default fallback
            }
        });
    }

    public function group()
    {
        return $this->hasMany(Reservation::class, 'booking_group_id', 'booking_group_id');
    }

    public function parentReservation()
    {
        return $this->belongsTo(Reservation::class, 'parent_reservation_id');
    }

    public function adendas()
    {
        return $this->hasMany(Reservation::class, 'parent_reservation_id');
    }

    public function isGroup()
    {
        return $this->group()->count() > 1;
    }
    public function flight()
    {
        return $this->belongsTo(Flight::class, 'space_flight_id');
    }
}
