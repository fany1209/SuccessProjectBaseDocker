<?php

namespace App\Http\Resources\Sales;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'sale_id'                => $this->sale_id,
            'folio'                  => $this->folio,
            'seller'                 => $this->seller,
            'first_time'             => (int) $this->first_time,
            'is_customer'            => (int) $this->is_customer,
            'purchase_order'         => $this->purchase_order,
            'invoice'                => $this->invoice,
            'sale_type'              => $this->sale_type,
            'term'                   => $this->term,
            'date'                   => $this->date ? Carbon::parse($this->date)->format('Y-m-d') : null,
            'payment_status'         => $this->payment_status,
            'almacen_status'         => $this->almacen_status,
            'almacen_comment'        => $this->almacen_comment,
            'almacen_postponed_date' => $this->almacen_postponed_date ? Carbon::parse($this->almacen_postponed_date)->format('Y-m-d') : null,
            'sector_id'              => $this->sector_id,
            'sector'                 => $this->sector?->name ?? null,
            'customer_id'            => $this->customer_id,
            'customer'               => $this->customer?->name ?? null,
            'prospect_id'            => $this->prospect_id,
            'prospect'               => $this->prospect?->name ?? null,
            'user_id'                => $this->user_id,
            'user'                   => $this->user?->name ?? null,
            'sales_status_id'        => $this->sales_status_id,
            'status'                 => $this->status?->name ?? null,
            'products_count'         => $this->details ? $this->details->count() : 0,
            'details'                => $this->details ? $this->details->map(function ($d) {
                $qty = (float) $d->quantity;
                $cost = (float) $d->cost;
                $subtotal = $qty * $cost;
                $tax = ($d->has_tax == 1) ? ($subtotal * 0.16) : 0;

                return [
                    'sale_detail_id'      => $d->sale_detail_id,
                    'product_id'          => $d->product_id,
                    'product_name'        => $d->product?->name ?? $d->public_product_name,
                    'public_product_name' => $d->public_product_name,
                    'quantity'            => $qty,
                    'cost'                => $cost,
                    'has_tax'             => (int) $d->has_tax,
                    'invoice_val'         => (int) $d->invoice_val,
                    'warehouse_batch'     => $d->warehouse_batch,
                    'public_batch'        => $d->public_batch,
                    'subtotal'            => round($subtotal, 2),
                    'tax'                 => round($tax, 2),
                    'total'               => round($subtotal + $tax, 2),
                ];
            }) : [],
            'created_at'             => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at'             => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
