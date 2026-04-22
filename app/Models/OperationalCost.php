<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalCost extends Model
{
    protected $guarded = [];

    protected $casts = [
        'old_cost_per_au' => 'decimal:2',
        'new_cost_per_au' => 'decimal:2',
    ];

    public function starship()
    {
        return $this->belongsTo(Starship::class);
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }

    /**
     * Registra un cambio de coste operativo en el log de auditoría.
     */
    public static function record(int $starshipId, float $oldCost, float $newCost, ?string $reason = null): self
    {
        return self::create([
            'starship_id'    => $starshipId,
            'old_cost_per_au' => $oldCost,
            'new_cost_per_au' => $newCost,
            'reason'         => $reason,
            'admin_id'       => auth()->id(),
        ]);
    }
}
