<?php

namespace App\Http\Repositories\Finance;

use App\Models\Customer;
use App\Models\FinancePayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class FinanceRepository
{
    protected FinancePayment $model;

    public function __construct(FinancePayment $model)
    {
        $this->model = $model;
    }

    public function getFilteredPayments(?int $semana = null, ?int $anio = null): Collection
    {
        $query = $this->model->newQuery();

        if ($semana && $anio) {
            $query->where('semana', $semana)
                ->where('anio', $anio);
        }

        return $query->orderBy('anio', 'desc')
            ->orderBy('semana', 'desc')
            ->get();
    }

    public function getDatatableData(): SupportCollection
    {
        return DB::table('finance_payments as p')
            ->leftJoin('facturas as f', 'f.factura_id', '=', 'p.factura')
            ->select(
                'p.id',
                'p.empresa',
                'p.cantidad',
                'p.motivo',
                'p.banco',
                'f.folio_factura as factura',
                'p.fecha_factura',
                'p.fecha_pago',
                'p.estatus',
                'p.comentarios',
                'p.semana',
                'p.anio',
                'p.terminacion',
                'p.efectivo'
            )
            ->orderBy('p.anio', 'desc')
            ->orderBy('p.semana', 'desc')
            ->get();
    }

    public function findById(int $id): ?FinancePayment
    {
        return $this->model->find($id);
    }

    public function create(array $data): FinancePayment
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update(int $id, array $data): ?FinancePayment
    {
        return DB::transaction(function () use ($id, $data) {
            $record = $this->model->where('id', $id)->lockForUpdate()->first();
            if (!$record) {
                return null;
            }

            $record->update($data);

            return $record;
        });
    }

    public function updateStatus(int $id, string $status): bool
    {
        return DB::transaction(function () use ($id, $status) {
            $record = $this->model->where('id', $id)->lockForUpdate()->first();
            if (!$record) {
                return false;
            }

            $record->estatus = $status;

            return $record->save();
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->model->where('id', $id)->lockForUpdate()->first();
            if (!$record) {
                return false;
            }

            return (bool) $record->delete();
        });
    }

    public function getPurchaseHistoryCustomers(): SupportCollection
    {
        return DB::table('customers')
            ->select('customer_id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function getCustomerPurchaseHistory(int $customerId, ?int $year = null): ?array
    {
        $customer = DB::table('customers')->where('customer_id', $customerId)->first();
        if (!$customer) {
            return null;
        }

        $cancelledIds = DB::table('sales_status')
            ->whereIn(DB::raw('LOWER(name)'), ['cancelado', 'cancelada'])
            ->pluck('sales_status_id')
            ->toArray();

        $salesQuery = DB::table('sales')
            ->where('customer_id', $customerId)
            ->where('is_customer', 1);

        if (!empty($cancelledIds)) {
            $salesQuery->whereNotIn('sales_status_id', $cancelledIds);
        }

        if (!empty($year)) {
            $salesQuery->whereYear('date', $year);
        }

        $saleIds = (clone $salesQuery)->pluck('sale_id');

        if ($saleIds->isEmpty()) {
            return [
                'customer_name'         => $customer->name,
                'contact_name'          => $customer->contact ?? 'Sin especificar',
                'contact_phone'         => $customer->phone ?? 'N/A',
                'contact_email'         => $customer->email ?? 'N/A',
                'total_purchased'       => 0,
                'last_purchase'         => null,
                'purchase_frequency'    => null,
                'top_product'           => null,
                'days_without_purchase' => null,
                'assigned_seller'       => $customer->vendedor ?? 'Sin asignar',
                'charts'                => [
                    'monthly'             => [],
                    'top_products'        => [],
                    'avg_ticket'          => [],
                    'seller_distribution' => [],
                ],
            ];
        }

        $details = DB::table('sale_detail')
            ->whereIn('sale_id', $saleIds)
            ->select('sale_detail.*')
            ->get();

        $totalPurchased = $details->sum(function ($d) {
            $subtotal = (float) $d->quantity * (float) $d->cost;
            return $d->has_tax == 1 ? $subtotal * 1.16 : $subtotal;
        });

        $lastPurchaseDate = (clone $salesQuery)->max('date');

        $saleDates = (clone $salesQuery)
            ->orderBy('date')
            ->pluck('date')
            ->unique()
            ->values();

        $purchaseFrequency = null;
        if ($saleDates->count() > 1) {
            $diffs = [];
            for ($i = 1; $i < $saleDates->count(); $i++) {
                $d1 = Carbon::parse($saleDates[$i - 1]);
                $d2 = Carbon::parse($saleDates[$i]);
                $diffs[] = $d1->diffInDays($d2);
            }
            $purchaseFrequency = round(array_sum($diffs) / count($diffs), 1);
        }

        $topProduct = DB::table('sale_detail')
            ->whereIn('sale_id', $saleIds)
            ->select('public_product_name', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('public_product_name')
            ->orderByDesc('total_qty')
            ->first();

        $daysSinceLastPurchase = $lastPurchaseDate
            ? (int) floor(Carbon::parse($lastPurchaseDate)->diffInDays(Carbon::now()))
            : null;

        $assignedSeller = $customer->vendedor;
        if (empty($assignedSeller)) {
            $assignedSeller = (clone $salesQuery)->orderByDesc('date')->value('seller') ?? 'Sin asignar';
        }

        $monthlyQuery = DB::table('sales')
            ->join('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id')
            ->where('sales.customer_id', $customerId)
            ->where('sales.is_customer', 1)
            ->when(!empty($cancelledIds), fn($q) => $q->whereNotIn('sales.sales_status_id', $cancelledIds));

        if (!empty($year)) {
            $monthlyQuery->whereYear('sales.date', $year);
        } else {
            $monthlyQuery->where('sales.date', '>=', Carbon::now()->subMonths(12)->startOfMonth());
        }

        $monthlyData = $monthlyQuery->select(
            DB::raw('YEAR(sales.date) as year'),
            DB::raw('MONTH(sales.date) as month'),
            DB::raw('SUM(CASE WHEN sale_detail.has_tax = 1 THEN sale_detail.quantity * sale_detail.cost * 1.16 ELSE sale_detail.quantity * sale_detail.cost END) as total')
        )
            ->groupBy(DB::raw('YEAR(sales.date)'), DB::raw('MONTH(sales.date)'))
            ->orderBy(DB::raw('YEAR(sales.date)'))
            ->orderBy(DB::raw('MONTH(sales.date)'))
            ->get();

        $topProducts = DB::table('sale_detail')
            ->whereIn('sale_id', $saleIds)
            ->select('public_product_name', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('public_product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $ticketQuery = DB::table('sales')
            ->join('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id')
            ->where('sales.customer_id', $customerId)
            ->where('sales.is_customer', 1)
            ->when(!empty($cancelledIds), fn($q) => $q->whereNotIn('sales.sales_status_id', $cancelledIds));

        if (!empty($year)) {
            $ticketQuery->whereYear('sales.date', $year);
        } else {
            $ticketQuery->where('sales.date', '>=', Carbon::now()->subMonths(12)->startOfMonth());
        }

        $avgTicket = $ticketQuery->select(
            DB::raw('YEAR(sales.date) as year'),
            DB::raw('MONTH(sales.date) as month'),
            DB::raw('COUNT(DISTINCT sales.sale_id) as num_sales'),
            DB::raw('SUM(CASE WHEN sale_detail.has_tax = 1 THEN sale_detail.quantity * sale_detail.cost * 1.16 ELSE sale_detail.quantity * sale_detail.cost END) as total')
        )
            ->groupBy(DB::raw('YEAR(sales.date)'), DB::raw('MONTH(sales.date)'))
            ->orderBy(DB::raw('YEAR(sales.date)'))
            ->orderBy(DB::raw('MONTH(sales.date)'))
            ->get()
            ->map(function ($row) {
                $row->avg_ticket = $row->num_sales > 0 ? round($row->total / $row->num_sales, 2) : 0;
                return $row;
            });

        $sellerQuery = DB::table('sales')
            ->join('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id')
            ->where('sales.customer_id', $customerId)
            ->where('sales.is_customer', 1)
            ->when(!empty($cancelledIds), fn($q) => $q->whereNotIn('sales.sales_status_id', $cancelledIds))
            ->whereNotNull('sales.seller')
            ->where('sales.seller', '!=', '');

        if (!empty($year)) {
            $sellerQuery->whereYear('sales.date', $year);
        }

        $sellerDistribution = $sellerQuery->select(
            'sales.seller',
            DB::raw('SUM(CASE WHEN sale_detail.has_tax = 1 THEN sale_detail.quantity * sale_detail.cost * 1.16 ELSE sale_detail.quantity * sale_detail.cost END) as total')
        )
            ->groupBy('sales.seller')
            ->orderByDesc('total')
            ->get();

        return [
            'customer_name'         => $customer->name,
            'contact_name'          => $customer->contact ?? 'Sin especificar',
            'contact_phone'         => $customer->phone ?? 'N/A',
            'contact_email'         => $customer->email ?? 'N/A',
            'total_purchased'       => round($totalPurchased, 2),
            'last_purchase'         => $lastPurchaseDate,
            'purchase_frequency'    => $purchaseFrequency,
            'top_product'           => $topProduct->public_product_name ?? null,
            'days_without_purchase' => $daysSinceLastPurchase,
            'assigned_seller'       => $assignedSeller ?? 'Sin asignar',
            'charts'                => [
                'monthly'             => $monthlyData,
                'top_products'        => $topProducts,
                'avg_ticket'          => $avgTicket,
                'seller_distribution' => $sellerDistribution,
            ],
        ];
    }

    public function getPurchaseHistoryDashboard(): array
    {
        $cancelledIds = DB::table('sales_status')
            ->whereIn(DB::raw('LOWER(name)'), ['cancelado', 'cancelada'])
            ->pluck('sales_status_id')
            ->toArray();

        $frequentCustomers = DB::table('sales')
            ->join('customers', 'customers.customer_id', '=', 'sales.customer_id')
            ->where('sales.is_customer', 1)
            ->whereNotNull('sales.customer_id')
            ->when(!empty($cancelledIds), fn($q) => $q->whereNotIn('sales.sales_status_id', $cancelledIds))
            ->select(
                'customers.customer_id',
                'customers.name',
                DB::raw('COUNT(DISTINCT sales.sale_id) as total_purchases'),
                DB::raw('MAX(sales.date) as last_purchase')
            )
            ->groupBy('customers.customer_id', 'customers.name')
            ->orderByDesc('total_purchases')
            ->limit(10)
            ->get();

        foreach ($frequentCustomers as $fc) {
            $saleIds = DB::table('sales')
                ->where('customer_id', $fc->customer_id)
                ->where('is_customer', 1)
                ->when(!empty($cancelledIds), fn($q) => $q->whereNotIn('sales_status_id', $cancelledIds))
                ->pluck('sale_id');

            $total = DB::table('sale_detail')
                ->whereIn('sale_id', $saleIds)
                ->get()
                ->sum(function ($d) {
                    $subtotal = (float) $d->quantity * (float) $d->cost;
                    return $d->has_tax == 1 ? $subtotal * 1.16 : $subtotal;
                });

            $fc->total_amount = round($total, 2);
        }

        $inactiveCustomers = DB::table('customers')
            ->leftJoin('sales', function ($join) use ($cancelledIds) {
                $join->on('customers.customer_id', '=', 'sales.customer_id')
                    ->where('sales.is_customer', 1);
                if (!empty($cancelledIds)) {
                    $join->whereNotIn('sales.sales_status_id', $cancelledIds);
                }
            })
            ->select(
                'customers.customer_id',
                'customers.name',
                'customers.vendedor',
                'customers.phone',
                DB::raw('MAX(sales.date) as last_purchase'),
                DB::raw('DATEDIFF(CURDATE(), MAX(sales.date)) as days_without_purchase')
            )
            ->groupBy('customers.customer_id', 'customers.name', 'customers.vendedor', 'customers.phone')
            ->havingRaw('MAX(sales.date) IS NOT NULL AND DATEDIFF(CURDATE(), MAX(sales.date)) > 100')
            ->orderByDesc('days_without_purchase')
            ->limit(15)
            ->get();

        $bestCustomers = DB::table('sales')
            ->join('customers', 'customers.customer_id', '=', 'sales.customer_id')
            ->join('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id')
            ->where('sales.is_customer', 1)
            ->whereNotNull('sales.customer_id')
            ->when(!empty($cancelledIds), fn($q) => $q->whereNotIn('sales.sales_status_id', $cancelledIds))
            ->select(
                'customers.customer_id',
                'customers.name',
                DB::raw('COUNT(DISTINCT sales.sale_id) as total_purchases'),
                DB::raw('SUM(CASE WHEN sale_detail.has_tax = 1 THEN sale_detail.quantity * sale_detail.cost * 1.16 ELSE sale_detail.quantity * sale_detail.cost END) as total_amount'),
                DB::raw('MAX(sales.date) as last_purchase')
            )
            ->groupBy('customers.customer_id', 'customers.name')
            ->orderByDesc('total_amount')
            ->limit(10)
            ->get();

        $maxAmount = $bestCustomers->isNotEmpty() ? $bestCustomers->first()->total_amount : 1;

        $topProducts = DB::table('sale_detail')
            ->join('sales', 'sales.sale_id', '=', 'sale_detail.sale_id')
            ->where('sales.is_customer', 1)
            ->when(!empty($cancelledIds), fn($q) => $q->whereNotIn('sales.sales_status_id', $cancelledIds))
            ->whereNotNull('sale_detail.public_product_name')
            ->where('sale_detail.public_product_name', '!=', '')
            ->select(
                'sale_detail.public_product_name',
                DB::raw('SUM(sale_detail.quantity) as total_qty'),
                DB::raw('SUM(CASE WHEN sale_detail.has_tax = 1 THEN sale_detail.quantity * sale_detail.cost * 1.16 ELSE sale_detail.quantity * sale_detail.cost END) as total_revenue'),
                DB::raw('COUNT(DISTINCT sales.customer_id) as unique_customers')
            )
            ->groupBy('sale_detail.public_product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $maxQty = $topProducts->isNotEmpty() ? $topProducts->first()->total_qty : 1;

        return [
            'frequent_customers' => $frequentCustomers,
            'inactive_customers' => $inactiveCustomers,
            'best_customers'     => $bestCustomers,
            'max_amount'         => $maxAmount,
            'top_products'       => $topProducts,
            'max_qty'            => $maxQty,
        ];
    }
}
