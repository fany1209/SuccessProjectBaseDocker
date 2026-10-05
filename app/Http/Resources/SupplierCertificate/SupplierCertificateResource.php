<?php

namespace App\Http\Resources\SupplierCertificate;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierCertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'certificate_id' => $this->certificate_id ?? $this->id,
            'supplier_name'  => $this->supplier_name,
            'product_name'   => $this->product_name,
            'file_path'      => $this->file_path ?? $this->path,
            'fecha_emision'  => $this->fecha_emision ? Carbon::parse($this->fecha_emision)->format('d/m/Y') : null,
            'created_at'     => $this->created_at ? Carbon::parse($this->created_at)->format('d-m-Y H:i:s') : null,
        ];
    }
}
