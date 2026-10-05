<?php

namespace App\Http\Resources\Purchase;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'supplier_id'      => $this->supplier_id,
            'supplier_name'    => $this->supplier?->name,
            'contact'          => $this->contact,
            'delivery_time'    => $this->delivery_time,
            'delivery_date'    => $this->delivery_date ? Carbon::parse($this->delivery_date)->format('d/m/Y') : null,
            'guia'             => $this->guia,
            'cfdi'             => $this->cfdi,
            'payment_method'   => $this->payment_method,
            'method_payment'   => $this->method_payment,
            'application_date' => $this->application_date ? Carbon::parse($this->application_date)->format('d/m/Y') : null,
            'applicant'        => $this->applicant,
            'price'            => (float) $this->price,
            'details'          => $this->details ? $this->details->map(function ($d) {
                return [
                    'id'           => $d->id,
                    'product_name' => $d->product_name,
                    'quantity'     => (float) $d->quantity,
                    'unit_price'   => (float) $d->unit_price,
                    'has_iva'      => (bool) $d->has_iva,
                ];
            }) : [],
            'created_at'       => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at'       => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
