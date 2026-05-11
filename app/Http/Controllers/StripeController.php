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
     * Start Stripe Checkout from a PaymentLink.
     */
    public function pay(string $token)
    {
        $link = \App\Models\PaymentLink::where('token', $token)
            ->where('status', 'activo')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $reservations = Reservation::where('booking_group_id', $link->booking_group_id)->get();
        if ($reservations->isEmpty()) {
            abort(404, 'No se encontraron reservas para este enlace.');
        }

        $totalCents = 0;
        $description = "";
        $resCount = $reservations->count();
        
        foreach ($reservations as $res) {
            $totalCents += (int) ($res->total_price * 100);
            $description .= "#{$res->id_locator} (" . ($res->passenger?->full_name ?? 'Pasajero') . ") ";
        }

        // Stripe Checkout Limit: 999,999.99 (99,999,999 cents)
        $isCapped = false;
        $originalAmount = $totalCents / 100;
        if ($totalCents > 99999999) {
            $totalCents = 99999999;
            $isCapped = true;
        }

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [
                [
                    'price_data' => [
                        'currency'     => 'eur',
                        'product_data' => [
                            'name'        => "Misión Iris Aerospace" . ($isCapped ? " (Límite Stripe)" : ""),
                            'description' => ($isCapped ? "[PRECIO REAL: " . number_format($originalAmount, 2, ',', '.') . "€] " : "") . "Reserva de grupo: " . trim($description),
                        ],
                        'unit_amount'  => $totalCents,
                    ],
                    'quantity'   => 1,
                ]
            ],
            'mode'                 => 'payment',
            'success_url'          => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('stripe.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => [
                'booking_group_id' => $link->booking_group_id,
                'is_capped'        => $isCapped ? '1' : '0',
                'source'           => $link->creator?->role === 'gestor' ? 'gestor' : 'admin',
            ],
        ]);

        // Guardar session_id en todas las reservas del grupo
        foreach ($reservations as $res) {
            $res->update(['stripe_session_id' => $session->id]);
        }

        return redirect($session->url);
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

        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        $isGestor = ($session->metadata->source ?? 'admin') === 'gestor';

        // Buscar la primera reserva con esta sesión
        $reservation = Reservation::where('stripe_session_id', $sessionId)->first();

        if ($reservation) {
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

                // Confirmar TODAS las filas del grupo
                $toUpdate = Reservation::with('logistics', 'passenger')
                    ->where(function($q) use ($groupId, $sessionId) {
                        $q->where('booking_group_id', $groupId)
                          ->orWhere('stripe_session_id', $sessionId);
                    })
                    ->where('payment_status', '!=', 'paid')
                    ->get();

                foreach ($toUpdate as $res) {
                    $res->update([
                        'payment_status'     => 'paid',
                        'status'             => 'Confirmada',
                        'paid_at'            => now(),
                        'stripe_receipt_url' => $receiptUrl,
                        'stripe_receipts'    => [
                            [
                                'type'        => 'payment',
                                'amount'      => $res->total_price,
                                'date'        => now()->format('Y-m-d H:i:s'),
                                'description' => 'Pago Original (Checkout)',
                                'url'         => $receiptUrl
                            ]
                        ],
                    ]);

                    $this->automateTasks($res);
                }
            }
        }

        if ($isGestor) {
            return redirect()->route('gestor.reservations')
                ->with('message', 'Pago confirmado. Su reserva ha sido marcada como PAGADA con éxito.');
        }

        return redirect()->route('admin.finances')
            ->with('message', 'Pago confirmado por Stripe. Reserva ' . ($reservation?->id_locator ? substr($reservation->id_locator, 0, 8) : '') . '... marcada como PAGADA.');
    }

    public function cancel(Request $request)
    {
        $sessionId = $request->query('session_id');
        
        $session = $sessionId ? \Stripe\Checkout\Session::retrieve($sessionId) : null;
        $isGestor = ($session?->metadata->source ?? 'admin') === 'gestor';

        if ($sessionId) {
            Reservation::where('stripe_session_id', $sessionId)
                ->update(['payment_status' => 'failed']);
        }

        if ($isGestor) {
            return redirect()->route('gestor.reservations')
                ->with('error', 'El proceso de pago fue cancelado.');
        }

        return redirect()->route('admin.reservations')
            ->with('error', 'El proceso de pago fue cancelado en Stripe.');
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
                // Usar booking_group_id del metadata para actualizar todo el grupo
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
                            'stripe_receipts' => [
                                [
                                    'type'        => 'payment',
                                    'amount'      => $res->total_price,
                                    'date'        => now()->format('Y-m-d H:i:s'),
                                    'description' => 'Pago Original (Webhook)',
                                    'url'         => '#'
                                ]
                            ],
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
