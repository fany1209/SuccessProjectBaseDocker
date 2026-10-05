<?php

namespace App\Http\Resources\Requisition;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequisitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'consecutive'    => $this->consecutive,
            'purchase_order' => $this->purchase_order,
            'applicant'      => $this->applicant,
            'department'     => $this->department,
            'data_sheet'     => (bool) $this->data_sheet,
            'safety_sheet'   => (bool) $this->safety_sheet,
            'comparative_id' => $this->comparative_id,
            'products'       => $this->products ? $this->products->map(function ($p) {
                return [
                    'id'          => $p->id,
                    'description' => $p->description,
                    'supplier'    => $p->supplier,
                    'insumo'      => $p->insumo,
                    'url'         => $p->url,
                    'use'         => $p->use,
                    'quantity'    => $p->quantity,
                    'image_url'   => $p->image_url,
                ];
            }) : [],
            'created_at'     => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at'     => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}