<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transforma el recurso de inventario en un arreglo estructurado.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'inventory_id' => (int) $this->inventory_id,
            'id'           => (int) $this->inventory_id,
            'product_id'   => (int) $this->product_id,
            'stock'        => (float) $this->stock,
            'batch'        => (string) $this->batch,
            'bar_code'     => $this->bar_code !== null ? (string) $this->bar_code : null,
            'product'      => $this->whenLoaded('product', function () {
                return [
                    'product_id' => (int) $this->product->product_id,
                    'name'       => (string) $this->product->name,
                    'unit'       => (string) $this->product->unit,
                    'sku'        => $this->product->sku ? (string) $this->product->sku : null,
                ];
            }),
            'created_at'   => optional($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'   => optional($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
