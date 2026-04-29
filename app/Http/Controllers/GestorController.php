<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;

class GestorController extends Controller
{
    /**
     * Generate and download reservation ticket PDF after FINAL GO.
     */
    public function downloadTicket(Reservation $reservation)
    {
        // Security: gestor can only access their own clients' reservations
        abort_unless(
            $reservation->user?->assigned_manager_id === auth()->id(),
            403,
            'Acceso denegado'
        );

        $reservation->load([
            'user',
            'passenger',
            'spaceFlight.destination',
            'spaceFlight.starship',
            'logistics.hotel',
            'logistics.terrestrialFlight.originLocation',
            'logistics.terrestrialFlight.destinationLocation',
        ]);

        // Generate a deterministic "QR code" placeholder string
        $qrData = 'IRIS-' . strtoupper(substr($reservation->id_locator, 0, 8)) . '-' . $reservation->id;

        $pdf = Pdf::loadView('gestor.ticket-pdf', [
            'res'    => $reservation,
            'qrData' => $qrData,
        ])->setPaper('a4', 'portrait');

        $filename = 'IRIS-TICKET-' . strtoupper(substr($reservation->id_locator, 0, 8)) . '.pdf';

        return $pdf->download($filename);
    }
}
