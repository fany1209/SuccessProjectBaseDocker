<?php

namespace App\Http\Resources\Quote;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'quote_id'                => $this->quote_id,
            'folio'                   => $this->folio,
            'company'                 => $this->company,
            'date'                    => $this->date ? Carbon::parse($this->date)->format('Y-m-d') : null,
            'currency'                => $this->currency ?? 'MXN',
            'attention'               => $this->attention,
            'department'              => $this->department,
            'phone'                   => $this->phone,
            'place_of_delivery'       => $this->place_of_delivery,
            'transport_specification' => $this->transport_specification,
            'deadline'                => $this->deadline,
            'terms'                   => $this->terms,
            'notes'                   => $this->notes,
            'quotes_status_id'        => $this->quotes_status_id,
            'status'                  => $this->status?->name ?? null,
            'user_id'                 => $this->user_id,
            'user'                    => $this->user?->name ?? null,
            'details'                 => $this->details ? $this->details->map(function ($d) {
                $qty = (float) $d->quantity;
                $cost = (float) $d->cost;
                $ivaRate = (float) $d->iva;
                $subtotal = $qty * $cost;
                $ivaAmount = $subtotal * $ivaRate;

                return [
                    'quote_detail_id'    => $d->quote_detail_id,
                    'product_id'         => $d->product_id,
                    'quote_product_name' => $d->quote_product_name,
                    'presentation'       => $d->presentation,
                    'unit'               => $d->unit,
                    'quantity'           => $qty,
                    'cost'               => $cost,
                    'iva'                => $ivaRate,
                    'subtotal'           => round($subtotal, 2),
                    'iva_amount'         => round($ivaAmount, 2),
                    'total'              => round($subtotal + $ivaAmount, 2),
                ];
            }) : [],
            'created_at'              => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at'              => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
