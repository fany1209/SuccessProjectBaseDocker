<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PerformanceNoteNotification extends Notification
{
    use Queueable;

    protected $note;

    public function __construct($note)
    {
        $this->note = $note;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $message = 'Tienes un nuevo comentario de desempeño.';
        if ($this->note->type === 'positive') $message = '¡Buen Trabajo! Has recibido una evaluación positiva.';
        if ($this->note->type === 'improvement') $message = 'Atención: Has recibido un área de mejora.';

        return [
            'note_id' => $this->note->id,
            'type' => $this->note->type,
            'message' => $message,
            'url' => route('performance_notes.index'),
        ];
    }
}
