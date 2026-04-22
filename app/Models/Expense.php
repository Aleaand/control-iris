<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'flight_id',
        'reference',
        'category',
        'description',
        'amount',
        'expense_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'datetime'
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}
