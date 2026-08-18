<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MaintenanceEquipment;

class UpcomingMaintenanceNotification extends Notification implements ShouldQueue
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
            'title' => 'Mantenimiento Próximo: ' . $this->equipment->name,
            'message' => 'Faltan 3 días o menos para el mantenimiento del equipo ' . $this->equipment->name . ' (Modelo: ' . $this->equipment->model . ').',
            'equipment_id' => $this->equipment->id,
            'url' => route('maintenance.show', $this->equipment->id),
        ];
    }
}
