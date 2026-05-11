<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PassportSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $passengerName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Iris Aerospace — Trámite de Pasaporte Estelar Iniciado',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.passport-submitted',
        );
    }
}
