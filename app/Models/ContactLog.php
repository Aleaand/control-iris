<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactLog extends Model
{
    protected $fillable = [
        'client_id',
        'gestor_id',
        'type',
        'zoom_link',
        'notes',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function typeLabel(): string
    {
        return match($this->type) {
            'nota'        => 'Nota',
            'llamada'     => 'Llamada',
            'email'       => 'Email',
            'videollamada' => 'Videollamada',
            default       => 'Otro',
        };
    }
}
