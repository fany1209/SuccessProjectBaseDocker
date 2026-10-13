<?php

namespace App\Http\Resources\ProductInf;

use F9WebLtd\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductQrResource extends JsonResource
{
    /**
     * Transforma el recurso en un array serializado.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $scanUrl = url('/rack/' . $this->uuid);
        $qrSvg = null;

        try {
            $qrSvg = (string) QrCode::size(280)->generate($scanUrl);
        } catch (\Throwable $e) {
            $qrSvg = null;
        }

        // Si cli_id está activo, tomar datos en tiempo real
        $liveCli = $this->cli;
        $liveQuantity = $liveCli ? (float) $liveCli->quantity : (float) $this->quantity;
        $liveNetWeight = $liveCli ? (float) $liveCli->net_weight : (float) $this->net_weight;
        $liveWpu = $liveCli ? (float) $liveCli->weight_per_unit : (float) $this->weight_per_unit;
        $currentLocation = $liveCli && $liveCli->location ? $liveCli->location->name : ($this->location ? $this->location->name : 'N/A');
        $warehouseName = $liveCli && $liveCli->location && $liveCli->location->warehouse 
            ? $liveCli->location->warehouse->name 
            : ($this->location && $this->location->warehouse ? $this->location->warehouse->name : 'N/A');

        return [
            'id'                   => $this->id,
            'uuid'                 => $this->uuid,
            'scan_url'             => $scanUrl,
            'qr_svg'               => $qrSvg,
            'product_id'           => $this->product_id,
            'product_name'         => $this->product ? $this->product->name : ($this->inventory && $this->inventory->product ? $this->inventory->product->name : 'N/A'),
            'product_description'  => $this->product ? $this->product->description : '',
            'concept_name'         => $this->concept ? $this->concept->name : ($liveCli && $liveCli->concept ? $liveCli->concept->name : 'N/A'),
            'batch'                => $this->batch ?: ($this->inventory ? $this->inventory->batch : 'N/A'),
            'bag_number'           => $this->bag_number ?: ($liveCli ? $liveCli->bag_number : null),
            'warehouse_name'       => $warehouseName,
            'location_name'        => $currentLocation,
            'quantity'             => $liveQuantity,
            'weight_per_unit'      => $liveWpu,
            'net_weight'           => $liveNetWeight,
            'unit'                 => $this->unit ?: ($this->product ? $this->product->unit : 'kg'),
            'description'          => $this->description,
            'is_active_in_rack'    => !is_null($liveCli),
            'created_by_name'      => $this->creator ? $this->creator->name : 'Sistema',
            'created_at'           => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'           => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
