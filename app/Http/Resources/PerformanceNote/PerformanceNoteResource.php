<?php

namespace App\Http\Resources\PerformanceNote;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'admin_id' => $this->admin_id,
            'type' => $this->type,
            'comments' => $this->comments,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'admin' => $this->whenLoaded('admin', function () {
                return [
                    'id' => $this->admin->id,
                    'name' => $this->admin->name,
                    'email' => $this->admin->email,
                ];
            }),
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
