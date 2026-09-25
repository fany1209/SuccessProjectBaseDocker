<?php

namespace App\Http\Resources\Directory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierDirectoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'Code_supplier' => (int) $this->Code_supplier,
            'Name' => $this->Name,
            'Product' => $this->Product,
            'Address' => $this->Address,
            'Phone' => $this->Phone,
            'Email' => $this->Email,
            'RFC' => $this->RFC,
            'Contact' => $this->Contact,
            'created_at' => $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }
}
