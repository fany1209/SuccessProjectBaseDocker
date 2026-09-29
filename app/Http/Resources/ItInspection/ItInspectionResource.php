<?php

namespace App\Http\Resources\ItInspection;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ItInspectionResource extends JsonResource
{
    /**
     * Transforma el recurso de inspección de TI en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'             => (int) $this->id,
            'folio'          => (string) $this->folio,
            'date'           => $this->date ? Carbon::parse($this->date)->format('Y-m-d') : null,
            'brand'          => $this->brand !== null ? (string) $this->brand : null,
            'model'          => $this->model !== null ? (string) $this->model : null,
            'serial_number'  => $this->serial_number !== null ? (string) $this->serial_number : null,
            'location'       => $this->location !== null ? (string) $this->location : null,
            'area'           => $this->area !== null ? (string) $this->area : null,
            'req1'           => (string) $this->req1,
            'req2'           => (string) $this->req2,
            'req3'           => (string) $this->req3,
            'req4'           => (string) $this->req4,
            'req5'           => (string) $this->req5,
            'observations'   => $this->observations !== null ? (string) $this->observations : null,
            'inspector_name' => $this->inspector_name !== null ? (string) $this->inspector_name : null,
            'created_at'     => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'     => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
