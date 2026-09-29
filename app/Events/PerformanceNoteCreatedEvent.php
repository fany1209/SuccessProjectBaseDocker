<?php

namespace App\Events;

use App\Models\PerformanceNote;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PerformanceNoteCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $note;

    public function __construct(PerformanceNote $note)
    {
        $this->note = $note;
    }

    public function broadcastOn()
    {
        return new Channel('user-tasks.' . $this->note->user_id);
    }

    public function broadcastAs()
    {
        return 'performance-note-created';
    }

    public function broadcastWith()
    {
        return [
            'type' => $this->note->type,
            'message' => 'Has recibido una nueva nota de desempeño.'
        ];
    }
}
