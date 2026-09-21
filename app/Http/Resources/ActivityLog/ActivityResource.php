<?php

namespace App\Http\Resources\ActivityLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'log_name'     => $this->log_name,
            'description'  => $this->description,
            'event'        => $this->event,
            'subject_type' => $this->subject_type,
            'subject_id'   => $this->subject_id,
            'causer_type'  => $this->causer_type,
            'causer_id'    => $this->causer_id,
            'causer_name'  => $this->causer ? $this->causer->name : null,
            'properties'   => $this->properties,
            'created_at'   => $this->created_at?->format('d-m-Y H:i:s'),
            'updated_at'   => $this->updated_at?->format('d-m-Y H:i:s'),
        ];
    }
}
