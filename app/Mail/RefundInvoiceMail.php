<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $refundRequest;

    /**
     * Create a new message instance.
     */
    public function __construct($reservation, $refundRequest)
    {
        $this->reservation = $reservation;
        $this->refundRequest = $refundRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Confirmación de Reembolso - Reserva {$this->reservation->id_locator}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.refund-invoice',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
