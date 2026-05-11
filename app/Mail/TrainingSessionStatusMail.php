<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainingSessionStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $passengerName,
        public string $sessionDate,
        public string $newStatus,
        public string $reason = ''
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->newStatus === 'Ausente' 
            ? 'Iris Aerospace — Sesión de Entrenamiento Cancelada' 
            : 'Iris Aerospace — Actualización de Sesión de Entrenamiento';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.training-session-status',
        );
    }
}
