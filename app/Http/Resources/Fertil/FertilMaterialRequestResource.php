<?php

namespace App\Http\Resources\Fertil;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FertilMaterialRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => (int) $this->id,
            'area'           => $this->area,
            'applicant_name' => $this->applicant_name,
            'status'         => $this->status,
            'comments'       => $this->comments,
            'items'          => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id'                  => (int) $item->id,
                        'request_id'          => (int) $item->request_id,
                        'product_name'        => $item->product_name,
                        'quantity'            => (float) $item->quantity,
                        'dispatched_quantity' => (float) $item->dispatched_quantity,
                    ];
                });
            }),
            'created_at'     => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'     => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
