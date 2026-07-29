<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SaleAlmacenNotification extends Notification
{
    use Queueable;

    public $sale;
    public $type;
    public $message;
    public $reason;
    public $date;

    /**
     * Create a new notification instance.
     */
    public function __construct($sale, $type, $message, $reason = null, $date = null)
    {
        $this->sale = $sale;
        $this->type = $type; // 'new_sale', 'confirmed', 'postponed', 'cancelled'
        $this->message = $message;
        $this->reason = $reason;
        $this->date = $date;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sale_id' => $this->sale->sale_id,
            'folio' => $this->sale->folio,
            'type' => $this->type,
            'message' => $this->message,
            'reason' => $this->reason,
            'date' => $this->date,
            'url' => route('sales.almacen_detail', $this->sale->sale_id)
        ];
    }
}
