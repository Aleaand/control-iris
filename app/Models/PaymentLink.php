<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentLink extends Model
{
    protected $fillable = [
        'token',
        'booking_group_id',
        'created_by',
        'client_id',
        'amount',
        'status',
        'expires_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at'    => 'datetime',
            'amount'     => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($link) {
            if (empty($link->token)) {
                $link->token = Str::random(48);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'activo' && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast() && $this->status === 'activo';
    }
}
