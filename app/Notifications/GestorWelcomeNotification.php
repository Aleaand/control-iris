<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class GestorWelcomeNotification extends ResetPasswordNotification
{
    /**
     * Build the mail representation of the notification.
     * This is used for new gestor onboarding — they need to set their password for the first time.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Iris Aerospace - Bienvenido, configura tu acceso')
            ->view('emails.gestor-welcome', [
                'url' => url(route('password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false)),
                'user' => $notifiable,
            ]);
    }
}
