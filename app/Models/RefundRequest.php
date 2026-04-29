<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'reservation_id',
        'gestor_id',
        'status',
        'refund_amount',
        'penalty_amount',
        'gestor_notes',
        'admin_notes',
        'has_insurance',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'has_insurance' => 'boolean',
            'resolved_at'   => 'datetime',
            'refund_amount' => 'decimal:2',
            'penalty_amount' => 'decimal:2',
        ];
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'Pendiente';
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['Aprobado', 'Rechazado']);
    }
}
