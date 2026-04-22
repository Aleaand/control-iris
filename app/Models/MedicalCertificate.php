<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MedicalCertificate extends Model
{
    protected $fillable = [
        'user_id',
        'issue_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Checks if the certificate is expired for Space Flight (10 years)
     */
    public function isExpiredForFlight(): bool
    {
        return $this->issue_date->copy()->addYears(10)->isPast();
    }

    /**
     * Checks if the certificate still applies for the 10% discount (3 years)
     */
    public function hasDiscount(): bool
    {
        return !$this->issue_date->copy()->addYears(3)->isPast();
    }
}
