<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $semana = $request->get('semana');
        $anio   = $request->get('anio');
        $query = DB::table('finance_payments');

        if ($semana && $anio) {
            $query->where('semana', $semana)
                ->where('anio', $anio);
        }

        $payments = $query
            ->orderBy('anio', 'desc')
            ->orderBy('semana', 'desc')
            ->get();

        return view('finance', compact('payments', 'semana', 'anio'));
    }

    public function datatable()
    {
        $payments = DB::table('finance_payments as p')
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

        return response()->json([
            'data' => $payments
        ]);
    }
    
    public function updateStatus(Request $request, $id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Identificador inválido'], 400);
        }

        $request->validate([
            'estatus' => 'required|in:PENDIENTE,PAGADO,CANCELADO',
        ]);

        DB::table('finance_payments')
            ->where('id', $id)
            ->update([
                'estatus' => $request->estatus,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa'       => 'required|string|max:150',
            'cantidad'      => 'required|numeric|min:0',
            'motivo'        => 'required|string',
            'banco'         => 'nullable|string|max:100',
            'factura'       => 'required|exists:facturas,factura_id',
            'fecha_factura' => 'nullable|date',
            'fecha_pago'    => 'nullable|date',
            'semana'        => 'required|integer|min:1|max:53',
            'anio'          => 'required|integer|min:2000|max:2100',
            'comentarios'   => 'nullable|string|max:255',
            'estatus'       => 'required|in:PENDIENTE,PAGADO,CANCELADO',
            'terminacion'   => 'nullable|string|max:4',
            'efectivo'      => 'nullable|string|max:2',
        ]);

        $id = DB::table('finance_payments')->insertGetId([
            'empresa'       => $data['empresa'],
            'cantidad'      => $data['cantidad'],
            'motivo'        => $data['motivo'],
            'banco'         => $data['banco'] ?? null,
            'factura'       => $data['factura'],
            'fecha_factura' => $data['fecha_factura'] ?? null,
            'fecha_pago'    => $data['fecha_pago'] ?? null,
            'semana'        => $data['semana'],
            'anio'          => $data['anio'],
            'comentarios'   => $data['comentarios'] ?? null,
            'estatus'       => $data['estatus'],
            'terminacion'   => $data['terminacion'] ?? null,
            'efectivo'      => $data['efectivo'] ?? null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json([
            'success' => true,
            'id'      => $id,
            'message' => 'Pago creado correctamente'
        ]);
    }

    public function show($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Identificador inválido'], 400);
        }

        $row = DB::table('finance_payments')->where('id', $id)->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Registro no encontrado'], 404);
        }

        return response()->json(['success' => true, 'data' => $row]);
    }

    public function destroy($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Identificador inválido'], 400);
        }

        $deleted = DB::table('finance_payments')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'No se pudo eliminar'], 404);
        }

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Identificador inválido'], 400);
        }

        $data = $request->validate([
            'empresa'       => 'required|string|max:150',
            'cantidad'      => 'required|numeric|min:0',
            'banco'         => 'nullable|string|max:100',
            'factura'       => 'nullable|string|max:50',
            'motivo'        => 'required|string',
            'fecha_factura' => 'nullable|date',
            'fecha_pago'    => 'nullable|date',
            'semana'        => 'required|integer|min:1|max:53',
            'anio'          => 'required|integer|min:2000|max:2100',
            'estatus'       => 'required|in:PENDIENTE,PAGADO,CANCELADO',
            'comentarios'   => 'nullable|in:Efectivo,Transferencia,Tarjeta',
            'terminacion'   => 'nullable|string|max:4',
            'efectivo'      => 'nullable|string|max:2',
        ]);

        $terminacion = ($data['comentarios'] === 'Tarjeta') ? ($data['terminacion'] ?? null) : null;
        $efectivo = ($data['comentarios'] === 'Efectivo') ? ($data['efectivo'] ?? null) : null;

        $updated = DB::table('finance_payments')
            ->where('id', $id)
            ->update([
                'empresa'       => $data['empresa'],
                'cantidad'      => $data['cantidad'],
                'banco'         => $data['banco'] ?? null,
                'factura'       => $data['factura'] ?? null,
                'motivo'        => $data['motivo'],
                'fecha_factura' => $data['fecha_factura'] ?? null,
                'fecha_pago'    => $data['fecha_pago'] ?? null,
                'semana'        => $data['semana'],
                'anio'          => $data['anio'],
                'estatus'       => $data['estatus'],
                'comentarios'   => $data['comentarios'] ?? null,
                'terminacion'   => $terminacion,
                'efectivo'      => $efectivo,
                'updated_at'    => now(),
            ]);

        if ($updated === 0) {
            $exists = DB::table('finance_payments')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json(['success' => false, 'message' => 'Registro no encontrado'], 404);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Historial de Compras – Vista principal
     */
    public function purchaseHistory()
    {
        $customers = DB::table('customers')
            ->select('customer_id', 'name')
            ->orderBy('name')
            ->get();

        return view('finance.historial-compras.index', compact('customers'));
    }

    /**
     * Historial de Compras – Datos AJAX para un cliente
     */
    public function getPurchaseHistoryData($customerId)
    {
        $customer = DB::table('customers')->where('customer_id', $customerId)->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
        }

        // IDs de estatus cancelados
        $cancelledIds = DB::table('sales_status')
            ->whereIn(DB::raw('LOWER(name)'), ['cancelado', 'cancelada'])
            ->pluck('sales_status_id')
            ->toArray();

        $year = request()->query('year');

        // Ventas válidas del cliente
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
            return response()->json([
                'success' => true,
                'data' => [
                    'customer_name' => $customer->name,
                    'contact_name' => $customer->contact ?? 'Sin especificar',
                    'contact_phone' => $customer->phone ?? 'N/A',
                    'contact_email' => $customer->email ?? 'N/A',
                    'total_purchased' => 0,
                    'last_purchase' => null,
                    'purchase_frequency' => null,
                    'top_product' => null,
                    'days_without_purchase' => null,
                    'assigned_seller' => $customer->vendedor ?? 'Sin asignar',
                    'charts' => [
                        'monthly' => [],
                        'top_products' => [],
                        'avg_ticket' => [],
                        'seller_distribution' => [],
                    ]
                ]
            ]);
        }

        // 1. Total comprado (con IVA cuando aplica)
        $details = DB::table('sale_detail')
            ->whereIn('sale_id', $saleIds)
            ->select('sale_detail.*')
            ->get();

        $totalPurchased = $details->sum(function ($d) {
            $subtotal = (float) $d->quantity * (float) $d->cost;
            return $d->has_tax == 1 ? $subtotal * 1.16 : $subtotal;
        });

        // 2. Última compra
        $lastPurchaseDate = (clone $salesQuery)->max('date');

        // 3. Frecuencia de compra (promedio de días entre compras)
        $saleDates = (clone $salesQuery)
            ->orderBy('date')
            ->pluck('date')
            ->unique()
            ->values();

        $purchaseFrequency = null;
        if ($saleDates->count() > 1) {
            $diffs = [];
            for ($i = 1; $i < $saleDates->count(); $i++) {
                $d1 = \Carbon\Carbon::parse($saleDates[$i - 1]);
                $d2 = \Carbon\Carbon::parse($saleDates[$i]);
                $diffs[] = $d1->diffInDays($d2);
            }
            $purchaseFrequency = round(array_sum($diffs) / count($diffs), 1);
        }

        // 4. Producto más comprado
        $topProduct = DB::table('sale_detail')
            ->whereIn('sale_id', $saleIds)
            ->select('public_product_name', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('public_product_name')
            ->orderByDesc('total_qty')
            ->first();

        // 5. Días sin comprar (desde la última compra en el filtro)
        $daysSinceLastPurchase = $lastPurchaseDate
            ? floor(\Carbon\Carbon::parse($lastPurchaseDate)->diffInDays(\Carbon\Carbon::now()))
            : null;

        // 6. Vendedor asignado
        $assignedSeller = $customer->vendedor;
        if (empty($assignedSeller)) {
            $assignedSeller = (clone $salesQuery)->orderByDesc('date')->value('seller') ?? 'Sin asignar';
        }

        // === DATOS PARA GRÁFICAS ===

        // A. Compras mensuales
        $monthlyQuery = DB::table('sales')
            ->join('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id')
            ->where('sales.customer_id', $customerId)
            ->where('sales.is_customer', 1)
            ->when(!empty($cancelledIds), fn($q) => $q->whereNotIn('sales.sales_status_id', $cancelledIds));

        if (!empty($year)) {
            $monthlyQuery->whereYear('sales.date', $year);
        } else {
            $monthlyQuery->where('sales.date', '>=', \Carbon\Carbon::now()->subMonths(12)->startOfMonth());
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

        // B. Top 5 productos
        $topProducts = DB::table('sale_detail')
            ->whereIn('sale_id', $saleIds)
            ->select('public_product_name', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('public_product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // C. Ticket promedio mensual
        $ticketQuery = DB::table('sales')
            ->join('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id')
            ->where('sales.customer_id', $customerId)
            ->where('sales.is_customer', 1)
            ->when(!empty($cancelledIds), fn($q) => $q->whereNotIn('sales.sales_status_id', $cancelledIds));

        if (!empty($year)) {
            $ticketQuery->whereYear('sales.date', $year);
        } else {
            $ticketQuery->where('sales.date', '>=', \Carbon\Carbon::now()->subMonths(12)->startOfMonth());
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

        // D. Distribución por vendedor
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

        return response()->json([
            'success' => true,
            'data' => [
                'customer_name' => $customer->name,
                'contact_name' => $customer->contact ?? 'Sin especificar',
                'contact_phone' => $customer->phone ?? 'N/A',
                'contact_email' => $customer->email ?? 'N/A',
                'total_purchased' => round($totalPurchased, 2),
                'last_purchase' => $lastPurchaseDate,
                'purchase_frequency' => $purchaseFrequency,
                'top_product' => $topProduct->public_product_name ?? null,
                'days_without_purchase' => $daysSinceLastPurchase,
                'assigned_seller' => $assignedSeller ?? 'Sin asignar',
                'charts' => [
                    'monthly' => $monthlyData,
                    'top_products' => $topProducts,
                    'avg_ticket' => $avgTicket,
                    'seller_distribution' => $sellerDistribution,
                ]
            ]
        ]);
    }

    public function getDashboardData()
    {
        // IDs de estatus cancelados
        $cancelledIds = DB::table('sales_status')
            ->whereIn(DB::raw('LOWER(name)'), ['cancelado', 'cancelada'])
            ->pluck('sales_status_id')
            ->toArray();

        $cancelFilter = function ($q) use ($cancelledIds) {
            if (!empty($cancelledIds)) {
                $q->whereNotIn('sales.sales_status_id', $cancelledIds);
            }
        };

        // 1. CLIENTES FRECUENTES (Top 10 por número de compras)
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

        // Agregar monto total para cada cliente frecuente
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

        // 2. CLIENTES INACTIVOS (100+ días sin comprar)
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

        // 3. RANKING DE MEJORES CLIENTES (Top 10 por monto)
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

        // 4. PRODUCTOS MÁS VENDIDOS (Top 10)
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

        return response()->json([
            'success' => true,
            'data' => [
                'frequent_customers'  => $frequentCustomers,
                'inactive_customers'  => $inactiveCustomers,
                'best_customers'      => $bestCustomers,
                'max_amount'          => $maxAmount,
                'top_products'        => $topProducts,
                'max_qty'             => $maxQty,
            ]
        ]);
    }

}
