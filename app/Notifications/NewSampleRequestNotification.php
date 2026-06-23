<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSampleRequestNotification extends Notification
{
    use Queueable;

    protected $sample;

    
    public function __construct($sample)
    {
        $this->sample = $sample;
    }

   
    public function via($notifiable)
    {
        return ['database'];
    }

   
    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->sample->id,
            'folio'      => $this->sample->folio,
            'title'      => 'Nueva Solicitud de Muestra',
            'message'    => 'Se ha generado la solicitud con folio: ' . $this->sample->folio,
            'url'        => '/laboratory/customer-samples', 
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'request_id' => $this->sample->id,
            'folio'      => $this->sample->folio,
        ];
    }
}