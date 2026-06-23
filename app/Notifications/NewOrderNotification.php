<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'po'       => $this->order->po ?? 'S/N',
            'title'    => 'Nuevo Pedido Registrado',
            'message'  => 'Se ha ingresado un pedido de ' . $this->order->empresa . ' (PO: ' . ($this->order->po ?? 'S/N') . ')',
            'url'      => '/orders', 
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'po'       => $this->order->po,
        ];
    }
}