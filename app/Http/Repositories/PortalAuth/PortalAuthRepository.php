<?php

namespace App\Http\Repositories\PortalAuth;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PortalAuthRepository
{
    public function getCustomerSales(int $customerId): Collection
    {
        return DB::table('sales')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getSaleDetail(int $saleId, int $customerId): ?array
    {
        $venta = DB::table('sales')
            ->where('sale_id', $saleId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$venta) {
            return null;
        }

        $articulos = DB::table('sale_detail')
            ->where('sale_id', $saleId)
            ->get();

        return [
            'venta'     => $venta,
            'articulos' => $articulos,
        ];
    }

    public function isSaleOwnedByCustomer(int $saleId, int $customerId): bool
    {
        return DB::table('sales')
            ->where('sale_id', $saleId)
            ->where('customer_id', $customerId)
            ->exists();
    }
}
