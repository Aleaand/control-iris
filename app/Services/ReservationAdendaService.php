<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Str;

class ReservationAdendaService
{
    /**
     * Crea una adenda (un upgrade o servicio complementario) vinculado a un pasajero
     * y a la reserva original, para mantener la traza financiera sin mezclar.
     */
    public function createAdenda(Reservation $parentReservation, array $serviceDetails, float $priceDifference): Reservation
    {
        $adenda = new Reservation();
        $adenda->user_id = $parentReservation->user_id;
        $adenda->passenger_id = $parentReservation->passenger_id;
        $adenda->space_flight_id = $parentReservation->space_flight_id;
        
        $adenda->id_locator = $parentReservation->id_locator; // Comparten el localizador
        $adenda->booking_group_id = $parentReservation->booking_group_id;
        
        $adenda->parent_reservation_id = $parentReservation->id;
        $adenda->is_adenda = true;
        
        // Detalles específicos de la adenda
        $adenda->seat_type = $serviceDetails['seat_type'] ?? $parentReservation->seat_type;
        $adenda->seat_number = $serviceDetails['seat_number'] ?? $parentReservation->seat_number;
        $adenda->total_price = $priceDifference;
        $adenda->status = 'Pending';
        $adenda->payment_status = 'pending';
        
        // Guardamos el qué se está cobrando en price_snapshot
        $adenda->price_snapshot = [
            'type' => 'adenda_upgrade',
            'details' => $serviceDetails,
            'original_price' => $parentReservation->total_price,
            'new_total_expected' => $parentReservation->total_price + $priceDifference,
        ];
        
        $adenda->save();
        
        return $adenda;
    }
}
