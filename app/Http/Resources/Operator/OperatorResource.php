<?php

namespace App\Http\Resources\Operator;

use Illuminate\Http\Resources\Json\JsonResource;

class OperatorResource extends JsonResource
{
    /**
     * Transforma el recurso de operador en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'operator_id' => (int) $this->operator_id,
            'id'          => (int) $this->operator_id,
            'name'        => (string) $this->name,
            'license'     => (string) $this->license,
            'created_at'  => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'  => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
