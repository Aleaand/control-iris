<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $paymentUrl,
        public float $amount,
        public string $locator
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Iris Aerospace — Enlace de Pago para su Misión #' . substr($this->locator, 0, 8),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-link',
        );
    }
}
