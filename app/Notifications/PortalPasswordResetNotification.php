<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PortalPasswordResetNotification extends Notification
{
    use Queueable;

    protected string $token;
    protected string $email;

    public function __construct(string $token, string $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $resetUrl = route('portal.password.reset', [
            'token' => $this->token,
            'email' => $this->email,
        ]);

        return (new MailMessage)
            ->from(config('mail.from.address'), 'Portal SUCCESS Suministros Sustentables')
            ->subject('Restablecimiento de contraseña — Portal de Clientes')
            ->view('emails.portal-reset-password', [
                'resetUrl'      => $resetUrl,
                'nombreContacto' => $notifiable->nombre_contacto,
                'expiresIn'     => 30,
            ]);
    }
}
