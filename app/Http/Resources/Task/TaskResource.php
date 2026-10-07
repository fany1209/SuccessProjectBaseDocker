<?php

namespace App\Http\Resources\Task;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'admin_id' => $this->admin_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'completed_at' => $this->completed_at ? Carbon::parse($this->completed_at)->format('d/m/Y H:i') : null,
            'responsable' => $this->whenLoaded('responsable', function () {
                return [
                    'id' => $this->responsable->id,
                    'name' => $this->responsable->name,
                    'email' => $this->responsable->email,
                ];
            }),
            'creador' => $this->whenLoaded('creador', function () {
                return [
                    'id' => $this->creador->id,
                    'name' => $this->creador->name,
                    'email' => $this->creador->email,
                ];
            }),
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
