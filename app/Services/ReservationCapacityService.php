<?php

namespace App\Services;

use App\Models\Flight;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\ReservationLogistic;

class ReservationCapacityService
{
    /**
     * Valida si un vuelo tiene capacidad para los pasajeros solicitados.
     * Retorna true si hay espacio, false en caso contrario.
     */
    public function checkFlightCapacity(int $flightId, int $requestedSeats = 1, ?int $excludeReservationGroupId = null): bool
    {
        $flight = Flight::findOrFail($flightId);
        
        $query = Reservation::where('space_flight_id', $flightId)
            ->whereIn('status', ['Confirmed', 'Pending', 'Paid']); // omitimos cancelados

        if ($excludeReservationGroupId) {
            $query->where('booking_group_id', '!=', $excludeReservationGroupId);
        }

        $currentOccupancy = $query->count();
        
        return ($currentOccupancy + $requestedSeats) <= $flight->total_capacity;
    }

    /**
     * Valida si un hotel tiene habitaciones disponibles para los recursos compartidos solicitados.
     */
    public function checkHotelCapacity(int $hotelId, int $requestedRooms = 1, ?string $excludeRoomToken = null): bool
    {
        $hotel = Hotel::findOrFail($hotelId);

        // Agrupamos por room_token para contar habitacion = 1
        $query = ReservationLogistic::where('hotel_id', $hotelId)
            ->whereNotNull('room_token')
            ->distinct('room_token');

        if ($excludeRoomToken) {
            $query->where('room_token', '!=', $excludeRoomToken);
        }

        $currentRoomsBooked = $query->count();

        return ($currentRoomsBooked + $requestedRooms) <= $hotel->total_rooms;
    }
}
