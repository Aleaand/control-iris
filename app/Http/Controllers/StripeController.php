<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Stripe redirects here after a successful payment.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('admin.reservations')
                ->with('error', 'Sesión de pago no encontrada.');
        }

        // Buscar la primera reserva con esta sesión
        $reservation = Reservation::where('stripe_session_id', $sessionId)->first();

        if ($reservation) {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $groupId     = $reservation->booking_group_id;
                $receiptUrl  = $session->url;

                // Obtener receipt de Stripe
                if ($session->payment_intent) {
                    try {
                        $intent = \Stripe\PaymentIntent::retrieve($session->payment_intent);
                        $charge = $intent->latest_charge ? \Stripe\Charge::retrieve($intent->latest_charge) : null;
                        if ($charge?->receipt_url) $receiptUrl = $charge->receipt_url;
                    } catch (\Exception $e) { /* non-critical */ }
                }

                // ⚠️ Confirmar TODAS las filas del grupo (no solo la primera)
                // Anti-paradoja: solo actualizamos filas que AÚN NO están pagadas
                $toUpdate = Reservation::with('logistics', 'passenger')
                    ->where('booking_group_id', $groupId)
                    ->where('payment_status', '!=', 'paid')
                    ->get();

                foreach ($toUpdate as $res) {
                    $res->update([
                        'payment_status'     => 'paid',
                        'status'             => 'Confirmada',
                        'paid_at'            => now(),
                        'stripe_receipt_url' => $receiptUrl,
                    ]);

                    $this->automateTasks($res);
                }
            }
        }

        return redirect()->route('admin.finances')
            ->with('message', '💳 Pago confirmado por Stripe. Reserva ' . ($reservation?->id_locator ? substr($reservation->id_locator, 0, 8) : '') . '... marcada como PAGADA.');
    }

    /**
     * Stripe redirects here if the user cancels payment.
     */
    public function cancel(Request $request)
    {
        $sessionId = $request->query('session_id');
        
        if ($sessionId) {
            Reservation::where('stripe_session_id', $sessionId)
                ->update(['payment_status' => 'failed']);
        }

        return redirect()->route('admin.reservations')
            ->with('error', '⚠️ El proceso de pago fue cancelado en Stripe.');
    }

    /**
     * Webhook endpoint — Stripe calls this server-to-server.
     * Guaranteed delivery even if user closes the browser.
     */
    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        // Handle specific events
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            if ($session->payment_status === 'paid') {
                // ⚠️ Usar booking_group_id del metadata para actualizar todo el grupo
                $groupId = $session->metadata->booking_group_id ?? null;

                if ($groupId) {
                    // Anti-paradoja adenda: solo actualizamos filas AÚN NO pagadas
                    $toUpdate = Reservation::with('logistics', 'passenger')
                        ->where('booking_group_id', $groupId)
                        ->where('payment_status', '!=', 'paid')
                        ->get();

                    foreach ($toUpdate as $res) {
                        $res->update([
                            'payment_status' => 'paid',
                            'status'         => 'Confirmada',
                            'paid_at'        => now(),
                        ]);
                        $this->automateTasks($res);
                    }
                } else {
                    // Fallback individual (reservas antiguas sin group_id en metadata)
                    $reservation = Reservation::with('logistics', 'passenger')
                        ->where('stripe_session_id', $session->id)
                        ->first();
                    if ($reservation && $reservation->payment_status !== 'paid') {
                        $reservation->update([
                            'payment_status' => 'paid',
                            'status'         => 'Confirmada',
                            'paid_at'        => now(),
                        ]);
                        $this->automateTasks($reservation);
                    }
                }
            }
        }

        return response('', 200);
    }

    private function automateTasks(Reservation $reservation)
    {
        // 1. Gestión de Pasaporte
        if ($reservation->logistics?->passport_management_included) {
            $gestor = $reservation->user?->assignedManager ?? User::where('role', 'gestor')->first();
            if ($gestor) {
                Task::create([
                    'assigned_gestor_id' => $gestor->id,
                    'created_by'         => auth()->id() ?? $gestor->id,
                    'title'              => 'Gestión de Pasaporte: ' . ($reservation->passenger?->full_name ?? 'Pasajero'),
                    'description'        => "Trámite de pasaporte requerido para la reserva {$reservation->id_locator}. Pago confirmado.",
                    'type'               => 'general',
                    'priority'           => 'alta',
                    'payload'            => [
                        'reservation_id' => $reservation->id,
                        'passenger_id'   => $reservation->passenger_id,
                        'locator'        => $reservation->id_locator,
                    ]
                ]);
            }
        }
    }
}
