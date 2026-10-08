<?php

namespace App\Http\Repositories\Pago;

use App\Models\Factura;
use Illuminate\Support\Collection;

class PagoRepository
{
    protected Factura $facturaModel;

    public function __construct(Factura $facturaModel)
    {
        $this->facturaModel = $facturaModel;
    }

    public function getFacturasForPagos(): Collection
    {
        return $this->facturaModel->newQuery()
            ->select('factura_id', 'folio_factura', 'empresa', 'total')
            ->orderByDesc('factura_id')
            ->get();
    }
}
