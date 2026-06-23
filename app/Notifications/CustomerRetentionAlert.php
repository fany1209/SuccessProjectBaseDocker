<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Customer;

class CustomerRetentionAlert extends Notification
{
    use Queueable;

    protected $customer;
    protected $lastPurchaseDate;

    public function __construct(Customer $customer, $lastPurchaseDate)
    {
        $this->customer = $customer;
        $this->lastPurchaseDate = $lastPurchaseDate;
    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'CustomerRetentionAlert',
            'customer_id' => $this->customer->customer_id,
            'customer_name' => $this->customer->name,
            'message' => 'El cliente frecuente ' . $this->customer->name . ' ha superado los 30 días sin realizar una compra. Su última compra fue el ' . $this->lastPurchaseDate . '. Sería un buen momento para contactarlo.',
            'url' => '/customers' 
        ];
    }
}
