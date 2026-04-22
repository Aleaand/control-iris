<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passport extends Model
{
    protected $fillable = [
        'user_id',
        'number',
        'expiration_date',
        'is_valid',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'is_valid' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpiredForFlight(): bool
    {
        return $this->expiration_date->isPast();
    }
}
