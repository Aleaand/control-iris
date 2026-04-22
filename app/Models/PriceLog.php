<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PriceLog extends Model
{
    protected $fillable = [
        'item_type',
        'item_id',
        'old_price',
        'new_price',
        'reason',
        'admin_id',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /**
     * Record a price change for any priceable item.
     * Call this BEFORE saving the new price.
     *
     * @param  string  $itemType  'flight' | 'hotel' | 'terrestrial_flight'
     * @param  int     $itemId
     * @param  float   $oldPrice
     * @param  float   $newPrice
     * @param  string|null $reason
     */
    public static function record(
        string $itemType,
        int $itemId,
        float $oldPrice,
        float $newPrice,
        ?string $reason = null
    ): void {
        if ((float) $oldPrice === (float) $newPrice) {
            return; // No real change — skip
        }

        static::create([
            'item_type' => $itemType,
            'item_id' => $itemId,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'reason' => $reason,
            'admin_id' => Auth::id(),
        ]);
    }

    /**
     * Get the current price for a global item type.
     * Fallbacks to sensible defaults if no record exists.
     */
    public static function getCurrentPrice(string $itemType): float
    {
        $log = static::where('item_type', $itemType)->latest()->first();
        if ($log) {
            return (float) $log->new_price;
        }

        return match ($itemType) {
            'training' => 50000.0,
            'vip_transfer' => 1000.0,
            'passport_management' => 2500.0,
            'refund_insurance' => 10.0, // 10% as default
            'crew_expense_per_au' => 12.0, // Gastos por tripulante por AU
            'hours_per_au' => 48.0, // Equivalente a 2 días por AU
            default => 0.0,
        };
    }

    public function getItemLabelAttribute(): string
    {
        return match ($this->item_type) {
            'flight' => 'Vuelo',
            'hotel' => 'Hotel',
            'terrestrial_flight' => 'Vuelo Terrestre',
            'training' => 'Iris Training',
            'refund_insurance' => 'Seguro Reembolso',
            'passport_management' => 'Pasaporte Espacial',
            'vip_transfer' => 'Transfer VIP',
            'crew_expense_per_au' => 'Gasto Tripulación/AU',
            'hours_per_au' => 'Horas de Vuelo/AU',
            default => $this->item_type,
        };
    }

}
