<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ReservationRefundService
{
    /**
     * Cancela la reserva de un pasajero específico dentro de un grupo y gestiona el reembolso.
     */
    public function cancelPassengerAndRefund(Reservation $reservation, string $reason = 'Cancelación solicitada por el cliente'): array
    {
        return DB::transaction(function () use ($reservation, $reason) {
            // 1. Calcular el monto a devolver
            // Esto incluye el precio original + cualquier adenda pagada vinculada a esta reserva
            $originalPrice = $reservation->payment_status === 'paid' ? $reservation->total_price : 0;
            
            $adendasPaid = $reservation->adendas()->where('payment_status', 'paid')->sum('total_price');
            
            $totalRefundAmount = $originalPrice + $adendasPaid;
            
            // 2. Cancelar la reserva original y sus adendas
            $reservation->status = 'Cancelled';
            // Dejamos el payment_status como 'refunded' para el registro si hubo dinero implicado
            if ($totalRefundAmount > 0) {
                $reservation->payment_status = 'refunded';
            }
            $reservation->save();
            
            foreach ($reservation->adendas as $adenda) {
                $adenda->status = 'Cancelled';
                if ($adenda->payment_status === 'paid') {
                    $adenda->payment_status = 'refunded';
                }
                $adenda->save();
            }
            
            // TODO: Integración con Stripe para ejecutar el reembolso ($totalRefundAmount) a $reservation->user
            // StripeService::refund($reservation->stripe_session_id, $totalRefundAmount);
            
            return [
                'success' => true,
                'refund_amount' => $totalRefundAmount,
                'message' => 'Reserva cancelada y reembolso calculado (Falta integración real con Stripe).',
            ];
        });
    }

    /**
     * Revisa si esta cancelación deja un Shared Resource (ej. Hotel) huérfano para aplicar suplemento.
     */
    public function checkSharedResourceImpact(Reservation $cancelledReservation): ?array
    {
        // En un futuro: consultar ReservationLogistic de esta reserva
        // Si compartía room_token con otra reserva, avisar que el acompañante de esa otra reserva 
        // ahora necesita un cargo por 'suplemento individual'.
        return null; // Placeholder para la lógica de Shared Tokens
    }
}
