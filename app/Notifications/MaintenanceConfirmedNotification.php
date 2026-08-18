<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MaintenanceEquipment;

class MaintenanceConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $equipment;

    public function __construct(MaintenanceEquipment $equipment)
    {
        $this->equipment = $equipment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Mantenimiento Confirmado (Dpto): ' . $this->equipment->name,
            'message' => 'El departamento ha confirmado el mantenimiento del equipo ' . $this->equipment->name . '. Requiere tu confirmación final.',
            'equipment_id' => $this->equipment->id,
            'url' => route('maintenance.show', $this->equipment->id),
        ];
    }
}
