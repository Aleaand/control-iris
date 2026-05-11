<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PassportApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $passengerName,
        public string $pdfPath
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡ENHORABUENA! Su Pasaporte Estelar IRIS ha sido Aprobado',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.passport-approved',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('Pasaporte_Estelar_IRIS.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
