<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainingScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $passengerName,
        public array  $sessions,
        public bool   $isRenewal,
        public bool   $hasTransfer = false
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Iris Aerospace — Recordatorio de Sesión IRIS Training',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.training-scheduled',
        );
    }
}
