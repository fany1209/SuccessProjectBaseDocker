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
    public $changes;
    public $title;

    public function __construct($sale, $type, $message, $reason = null, $date = null, array $changes = [], ?string $title = null)
    {
        $this->sale = $sale;
        $this->type = $type; // 'new_sale', 'confirmed', 'postponed', 'cancelled', 'sale_edited'
        $this->message = $message;
        $this->reason = $reason;
        $this->date = $date;
        $this->changes = $changes;
        $this->title = $title;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'sale_id' => $this->sale->sale_id,
            'folio'   => $this->sale->folio,
            'type'    => $this->type,
            'title'   => $this->title ?? ($this->type === 'sale_edited' ? 'Venta Folio ' . $this->sale->folio . ' Modificada' : 'Notificación de Venta'),
            'message' => $this->message,
            'reason'  => $this->reason,
            'date'    => $this->date,
            'changes' => $this->changes,
            'url'     => route('sales.almacen_detail', $this->sale->sale_id),
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
