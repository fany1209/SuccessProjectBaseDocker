<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderUpdatedNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $changes;
    protected $user;

    public function __construct($order, array $changes = [], $user = null)
    {
        $this->order = $order;
        $this->changes = $changes;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }

    public function toArray($notifiable)
    {
        $userName = $this->user ? $this->user->name : 'Usuario';
        return [
            'order_id' => $this->order->id,
            'po'       => $this->order->po ?? 'S/N',
            'type'     => 'order_edited',
            'title'    => 'Pedido PO: ' . ($this->order->po ?? 'S/N') . ' Modificado',
            'message'  => 'El pedido de ' . $this->order->empresa . ' (PO: ' . ($this->order->po ?? 'S/N') . ') fue editado por ' . $userName . '.',
            'changes'  => $this->changes,
            'url'      => '/orders',
        ];
    }
}
