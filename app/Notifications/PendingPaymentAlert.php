<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PendingPaymentAlert extends Notification
{
    use Queueable;

    protected $pendingSales;

    public function __construct($pendingSales)
    {
        $this->pendingSales = $pendingSales;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'pending_payments_alert',
            'title' => 'Pagos Pendientes',
            'message' => 'Tienes ' . count($this->pendingSales) . ' cliente(s) con ventas a crédito vencidas.',
            'pending_sales' => $this->pendingSales
        ];
    }
}
