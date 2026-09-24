<?php

namespace App\Http\Resources\Concept;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConceptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'concept_id' => $this->concept_id,
            'name' => $this->name,
            'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
