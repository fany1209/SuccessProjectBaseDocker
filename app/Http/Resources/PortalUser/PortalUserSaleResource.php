<?php

namespace App\Http\Resources\PortalUser;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalUserSaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'sale_id'  => $this->sale_id,
            'folio'    => $this->folio,
            'date'     => $this->date ? Carbon::parse($this->date)->format('d-m-Y') : null,
            'has_pdf'  => (bool) $this->has_pdf,
            'pdf_path' => $this->pdf_path,
            'pdf_name' => $this->pdf_name,
            'has_xml'  => (bool) $this->has_xml,
            'xml_path' => $this->xml_path,
            'xml_name' => $this->xml_name,
            'has_coa'  => (bool) $this->has_coa,
            'coa_path' => $this->coa_path,
            'coa_name' => $this->coa_name,
        ];
    }
}
