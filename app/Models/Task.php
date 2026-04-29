<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'assigned_gestor_id',
        'created_by',
        'title',
        'description',
        'type',
        'status',
        'priority',
        'payload',
        'accepted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload'      => 'array',
            'accepted_at'  => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'assigned_gestor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'Pendiente';
    }

    public function priorityColor(): string
    {
        return match($this->priority) {
            'urgente' => 'rose',
            'alta'    => 'orange',
            'media'   => 'amber',
            'baja'    => 'zinc',
            default   => 'zinc',
        };
    }
}
