<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PriceLog;
class ReservationLogistic extends Model
{
    protected $fillable = [
        'reservation_id',
        'terrestrial_flight_id',
        'hotel_id',
        'hotel_nights',
        'hotel_check_in',
        'hotel_check_out',
        'training_included',
        'vip_transfer_included',
        'refund_insurance_included',
        'passport_management_included'
    ];

    protected function casts(): array
    {
        return [
            'training_included' => 'boolean',
            'vip_transfer_included' => 'boolean',
            'refund_insurance_included' => 'boolean',
            'passport_management_included' => 'boolean',
            'hotel_check_in' => 'date',
            'hotel_check_out' => 'date',
        ];
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function terrestrialFlight()
    {
        return $this->belongsTo(TerrestrialFlight::class);
    }

    /**
     * Helper to compute total price of the logistic block
     */
    public function computeTotalPrice()
    {
        $total = 0;

        if ($this->hotel && $this->hotel_nights > 0) {
            $total += $this->hotel->price_per_night * $this->hotel_nights;
        }

        if ($this->terrestrialFlight) {
            $total += $this->terrestrialFlight->price;
        }
        if ($this->vip_transfer_included) {
            $precioTransporte = $this->terrestrialFlight?->destinationLocation?->transport_price;

            if (!$precioTransporte) {
                $baseLanzamiento = Location::find(1);//DESDE LA BASE DE LANZAMIENTO
                $precioTransporte = $baseLanzamiento ? $baseLanzamiento->transport_price : 0;
            }

            $total += $precioTransporte;
        }
        if ($this->training_included) {
            $precioTraining = PriceLog::getCurrentPrice('training');
            $total += $precioTraining;
        }

        if ($this->passport_management_included) {
            $precioPasaporte = PriceLog::getCurrentPrice('passport_management');
            $total += $precioPasaporte;
        }
        if ($this->refund_insurance_included) {
            $insurancePct = PriceLog::getCurrentPrice('refund_insurance');
            $insuranceAmt = $total * ($insurancePct / 100);
            $total += $insuranceAmt;
        }
        return $total;
    }
}
