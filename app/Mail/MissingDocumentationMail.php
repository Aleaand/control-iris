<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MissingDocumentationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $passengerName,
        public array $missingDocs,
        public string $notes = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Iris Aerospace — Acción Requerida: Documentación para su Pasaporte Estelar',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.missing-documentation',
        );
    }
}
