<?php

namespace App\Http\Resources\Product;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id'   => $this->product_id,
            'category_id'  => $this->category_id,
            'category'     => $this->category?->name ?? null,
            'name'         => $this->name,
            'sat_code'     => $this->sat_code,
            'sku'          => $this->sku,
            'presentation' => $this->presentation,
            'unit'         => $this->unit,
            'batch_code'   => $this->batch_code,
            'stock_min'    => $this->stock_min,
            'stock_max'    => $this->stock_max,
            'images'       => $this->images ? $this->images->map(function ($img) {
                return [
                    'image_id' => $img->image_id,
                    'path'     => $img->path,
                ];
            }) : [],
            'files'        => $this->files ? $this->files->map(function ($f) {
                return [
                    'file_id' => $f->file_id,
                    'path'    => $f->path,
                    'sector'  => $f->sector,
                ];
            }) : [],
            'created_at'   => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
            'updated_at'   => $this->updated_at ? Carbon::parse($this->updated_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
