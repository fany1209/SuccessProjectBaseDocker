<?php

namespace App\Http\Resources\Reception;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceptionResource extends JsonResource
{
    /**
     * Transforma el recurso de recepción de muestra en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                        => (int) $this->id,
            'folio_muestra'             => $this->folio_muestra,
            'product_id'                => $this->product_id,
            'product_name'              => optional($this->product)->name ?? ($this->nombre_comercial ?? ''),
            'sku'                       => $this->sku ?? optional($this->product)->sku,
            'batch'                     => $this->batch,
            'nombre_comercial'          => $this->nombre_comercial,
            'fecha_entrada'             => $this->fecha_entrada ? Carbon::parse($this->fecha_entrada)->format('Y-m-d') : '',
            'fecha_caducidad'           => $this->fecha_caducidad ? Carbon::parse($this->fecha_caducidad)->format('Y-m-d') : '',
            'descripcion'               => $this->descripcion,
            'supplier_id'               => $this->supplier_id,
            'supplier_name'             => optional($this->supplier)->name,
            'origen_muestra'            => $this->origen_muestra,
            'origen_otro'               => $this->origen_otro,
            'objetivo_muestra'          => $this->objetivo_muestra ?? '',
            'objetivo_otro'             => $this->objetivo_otro,
            'cantidad'                  => $this->cantidad !== null ? (float) $this->cantidad : null,
            'um'                        => $this->um,
            'um_otro'                   => $this->um_otro,
            'docs_ccf'                  => (bool) $this->docs_ccf,
            'docs_ft'                   => (bool) $this->docs_ft,
            'docs_hs'                   => (bool) $this->docs_hs,
            'docs_otro'                 => (bool) $this->docs_otro,
            'docs_otro_txt'             => $this->docs_otro_txt,
            'observaciones_laboratorio' => $this->observaciones_laboratorio,
            'firma_entrega_nombre'      => $this->firma_entrega_nombre,
            'firma_recepcion_nombre'    => $this->firma_recepcion_nombre,
            'estatus'                   => (int) ($this->estatus ?? 0),
            'status'                    => (int) ($this->estatus ?? 0) === 1 ? 'TERMINADO' : 'PENDIENTE',
            'created_at'                => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'                => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
