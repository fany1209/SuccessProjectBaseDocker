<?php

namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Http\Requests\StoreSaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\Sector;
use App\Models\Customer;
use App\Models\Prospect;
use App\Models\Product;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Database\QueryException;

class SalesController extends Controller
{
    public function index()
    {
        $total_sales = Sale::count();
        $sectors = Sector::all();
        $products = Product::select('product_id', 'name')->get();
        $prospects = Prospect::select('prospect_id', 'name', 'sector_id')->get();
        $customers = Customer::select('customer_id', 'name', 'sector_id')->get();
        $statuses = \DB::table('sales_status')->get();
        $user = Auth::user();
        $user_admin = $user->hasRole('Admin');
        $user_id = $user->id;
        $user_name = $user->name;

        $sellers = User::select('users.id as seller_number', 'users.name')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->whereIn('roles.name', ['Sales', 'Admin'])
            ->get();

        return view('sales', compact('sellers', 'user_admin', 'user_name', 'user_id', 'total_sales', 'sectors', 'products', 'prospects', 'customers', 'statuses'));
    }

    public function deleteDetail(Request $request)
    {
        $id = $request->input('id');
        $detail = SaleDetail::find($id);
        if ($detail) {
            $detail->delete();
            return response()->json(['success' => true, 'message' => 'Detail deleted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Detail not found'], 404);
        }
    }

    public function getSales(Request $request)
    {
        $sector = $request->input('sector');
        $search = $request->input('search');
        $clients = $request->input('clients');
        $seller = $request->input('seller');
        $sale_type = $request->input('sale_type');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = DB::table('sales')
            ->leftJoin('sectors', 'sectors.sector_id', '=', 'sales.sector_id')
            ->leftJoin('sale_detail', 'sale_detail.sale_id', '=', 'sales.sale_id')
            ->select(
                'sales.sale_id',
                'sales.folio',
                'sales.date',
                'sales.seller',
                'sales.sales_status_id',
                'sectors.name as sName',
                'sales.sale_type',
                'sales.purchase_order',
                'sales.invoice',
                DB::raw('count(sale_detail.sale_detail_id) as products')
            )
            ->groupBy(
                'sales.sale_id',
                'sales.folio',
                'sales.date',
                'sales.seller',
                'sales.sales_status_id',
                'sectors.name',
                'sales.sale_type',
                'sales.purchase_order',
                'sales.invoice'
            );

        if ($clients == 1) {
            $query->leftJoin('customers', 'customers.customer_id', '=', 'sales.customer_id')
                ->addSelect('customers.name as cName')
                ->where('sales.is_customer', 1)
                ->groupBy('customers.name');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('sectors.name', 'like', '%' . $search . '%')
                        ->orWhere('customers.name', 'like', '%' . $search . '%')
                        ->orWhere('sales.seller', 'like', '%' . $search . '%')
                        ->orWhere('sales.folio', 'like', '%' . $search . '%');
                });
            }
        } else {
            $query->leftJoin('prospects', 'prospects.prospect_id', '=', 'sales.prospect_id')
                ->addSelect('prospects.name as cName')
                ->where('sales.is_customer', 0)
                ->groupBy('prospects.name');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('sectors.name', 'like', '%' . $search . '%')
                        ->orWhere('prospects.name', 'like', '%' . $search . '%')
                        ->orWhere('sales.seller', 'like', '%' . $search . '%')
                        ->orWhere('sales.folio', 'like', '%' . $search . '%');
                });
            }
        }

        if (!empty($sector)) {
            $query->where('sales.sector_id', $sector);
        }

        if (!empty($seller)) {
            $query->where('sales.seller', 'like', '%' . $seller . '%');
        }

        if (!empty($sale_type)) {
            $query->where('sales.sale_type', 'like', '%' . $sale_type . '%');
        }

        if (!empty($date_from)) {
            $query->whereDate('sales.date', '>=', $date_from);
        }

        if (!empty($date_to)) {
            $query->whereDate('sales.date', '<=', $date_to);
        }

        if (!auth()->user()->hasRole('Admin')) {
            $query->where('sales.user_id', auth()->id());
        }

        $sales = $query->get();

        return response()->json(['sales' => $sales]);
    }

    public function store(StoreSaleRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->only(['seller', 'first_time', 'is_customer', 'purchase_order', 'invoice', 'sale_type', 'term', 'date', 'customer_id', 'prospect_id', 'user_id', 'sales_status_id', 'sector_id']);
                $data['payment_status'] = ($request->sale_type == 'Cash') ? 'PAID' : 'PENDING';

                if ($request->first_time == 0 && $request->is_customer == 0) {
                    if (!$request->has('name') || empty($request->name)) {
                        throw new \Exception("El nombre del cliente es obligatorio para registros manuales.");
                    }
                    $customerData = $request->only(['sector_id', 'name', 'phone', 'email', 'rfc', 'state', 'city', 'district', 'address']);

                    $customerData['customer_code'] = $this->generateCustomerCode($customerData['sector_id']);

                    $customer = Customer::create($customerData);
                    $data['customer_id'] = $customer->customer_id;
                    $data['prospect_id'] = null;
                    $data['is_customer'] = 1;
                } else {
                    if ($request->is_customer == 0 && !empty($data['prospect_id'])) {
                        $prospect = Prospect::find($data['prospect_id']);
                        if ($prospect) {
                            $customerCode = $this->generateCustomerCode($data['sector_id']);

                            $customer = Customer::create([
                                'name' => $prospect->name,
                                'phone' => $prospect->phone,
                                'email' => $prospect->email,
                                'rfc' => $prospect->rfc,
                                'district' => $prospect->district,
                                'city' => $prospect->city,
                                'state' => $prospect->state,
                                'address' => $prospect->address,
                                'sector_id' => $data['sector_id'],
                                'customer_code' => $customerCode,
                            ]);

                            $data['customer_id'] = $customer->customer_id;
                            $data['prospect_id'] = null;
                            $data['is_customer'] = 1;

                            // Actualizar ventas previas (si las hubiera, aunque esta sería la primera compra)
                            Sale::where('prospect_id', $prospect->prospect_id)->update([
                                'customer_id' => $customer->customer_id,
                                'prospect_id' => null,
                                'is_customer' => 1
                            ]);

                            // Borrar el prospecto ya convertido a cliente
                            $prospect->delete();
                        } else {
                            $data['customer_id'] = null;
                        }
                    } else {
                        if ($request->is_customer == 1) {
                            $data['prospect_id'] = null;
                        } else {
                            $data['customer_id'] = null;
                        }
                    }
                }

                $count = Sale::count();
                $data['folio'] = $count + 1;

                $sale = Sale::create($data);
                $sale_id = $sale->sale_id;

                $products_id = $request->input('product_id', []);
                $public_product_names = $request->input('public_product_name', []);
                $public_batchs = $request->input('public_batch', []);
                $quantities = $request->input('quantity', []);
                $invoice_values = $request->input('invoice_val', []);
                $costs = $request->input('cost', []);
                $has_taxs = $request->input('has_tax', []);

                foreach ($products_id as $i => $product_id) {
                    $invoice_val = $invoice_values[$i] ?? 0;
                    $cost = $costs[$i] ?? 0;

                    if ($invoice_val == 1) {
                        $cost = 0;
                    }

                    SaleDetail::create([
                        'sale_id' => $sale_id,
                        'product_id' => $product_id,
                        'public_product_name' => $public_product_names[$i] ?? '',
                        'quantity' => $quantities[$i] ?? 0,
                        'cost' => $cost,
                        'invoice_val' => $invoice_val,
                        'public_batch' => $public_batchs[$i] ?? null,
                        'has_tax' => $has_taxs[$i] ?? 0
                    ]);
                }

                return response()->json(['message' => 'Operation successfully completed'], 201);
            });
        } catch (\Exception $e) {
            if ($e instanceof QueryException) {
                return DatabaseErrors::handle($e);
            }
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(StoreSaleRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $fields = ['seller', 'purchase_order', 'invoice', 'sale_type', 'term', 'date', 'folio', 'user_id', 'sales_status_id', 'sector_id', 'is_customer', 'payment_status'];

                $data = $request->only($fields);

                if ($request->is_customer == 1) {
                    $data['customer_id'] = $request->customer_id;
                    $data['prospect_id'] = null;
                } else {
                    if (!empty($request->prospect_id)) {
                        $prospect = Prospect::find($request->prospect_id);
                        if ($prospect) {
                            $customerCode = $this->generateCustomerCode($data['sector_id']);

                            $customer = Customer::create([
                                'name' => $prospect->name,
                                'phone' => $prospect->phone,
                                'email' => $prospect->email,
                                'rfc' => $prospect->rfc,
                                'district' => $prospect->district,
                                'city' => $prospect->city,
                                'state' => $prospect->state,
                                'address' => $prospect->address,
                                'sector_id' => $data['sector_id'],
                                'customer_code' => $customerCode,
                            ]);

                            $data['customer_id'] = $customer->customer_id;
                            $data['prospect_id'] = null;
                            $data['is_customer'] = 1;

                            Sale::where('prospect_id', $prospect->prospect_id)->update([
                                'customer_id' => $customer->customer_id,
                                'prospect_id' => null,
                                'is_customer' => 1
                            ]);

                            $prospect->delete();
                        } else {
                            $data['prospect_id'] = $request->prospect_id;
                            $data['customer_id'] = null;
                        }
                    } else {
                        $data['prospect_id'] = $request->prospect_id;
                        $data['customer_id'] = null;
                    }
                }

                Sale::where('sale_id', $request->sale_id)->update($data);

                $sale_details_ids = $request->input('sale_detail', []);
                $products_id = $request->input('product_id', []);
                $public_product_names = $request->input('public_product_name', []);
                $public_batchs = $request->input('public_batch', []);
                $quantities = $request->input('quantity', []);
                $invoice_values = $request->input('invoice_val', []);
                $costs = $request->input('cost', []);
                $has_taxs = $request->input('has_tax', []);

                $existing_ids = array_filter($sale_details_ids, function ($id) {
                    return $id != 0;
                });
                SaleDetail::where('sale_id', $request->sale_id)
                    ->whereNotIn('sale_detail_id', $existing_ids)
                    ->delete();

                foreach ($sale_details_ids as $i => $sale_detail_id) {
                    $invoice_val = $invoice_values[$i] ?? 0;
                    $cost = $costs[$i] ?? 0;

                    if ($invoice_val == 1) {
                        $cost = 0;
                    }

                    $detailData = [
                        'product_id' => $products_id[$i] ?? null,
                        'public_product_name' => $public_product_names[$i] ?? '',
                        'quantity' => $quantities[$i] ?? 0,
                        'cost' => $cost,
                        'invoice_val' => $invoice_val,
                        'public_batch' => $public_batchs[$i] ?? null,
                        'has_tax' => $has_taxs[$i] ?? 0
                    ];

                    if ($sale_detail_id != 0) {
                        SaleDetail::where('sale_detail_id', $sale_detail_id)->update($detailData);
                    } else {
                        $detailData['sale_id'] = $request->sale_id;
                        SaleDetail::create($detailData);
                    }
                }

                return response()->json(['message' => 'Operation successfully completed'], 201);
            });
        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        }
    }

    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $sale = Sale::findOrFail($id);
                SaleDetail::where('sale_id', $id)->delete();
                $sale->delete();
                return response()->json(['success' => true]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function generateCustomerCode($sector_id)
    {
        $sector = \App\Models\Sector::where('sector_id', $sector_id)
            ->lockForUpdate()
            ->firstOrFail();

        $prefix = 'SC' . $sector->code;
        $prefixLen = strlen($prefix);

        $last = Customer::where('sector_id', $sector->sector_id)
            ->where('customer_code', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(SUBSTRING(customer_code, " . ($prefixLen + 1) . ") AS UNSIGNED)) AS max_num")
            ->value('max_num');

        $next = ((int) $last) + 1;
        return $prefix . $next;
    }

    public function show($id)
    {
        $saleBase = Sale::select('is_customer')->where('sale_id', $id)->first();

        if (!$saleBase) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        $sectors = Sector::all();

        $query = Sale::select('sales.*', 'sectors.name as sector_name')
            ->leftJoin('sectors', 'sectors.sector_id', '=', 'sales.sector_id')
            ->where('sales.sale_id', $id);

        if ($saleBase->is_customer == 1) {
            $query->join('customers', 'customers.customer_id', '=', 'sales.customer_id')
                ->addSelect('customers.name as client_name', 'customers.email', 'customers.phone', 'customers.rfc', 'customers.address', 'customers.city', 'customers.state', 'customers.district');
        } else {
            $query->join('prospects', 'prospects.prospect_id', '=', 'sales.prospect_id')
                ->addSelect('prospects.name as client_name', 'prospects.email', 'prospects.phone', 'prospects.rfc', 'prospects.address', 'prospects.city', 'prospects.state', 'prospects.district');
        }

        $sale = $query->first();

        $sale_detail = SaleDetail::select('sale_detail.*', 'products.name as original_product_name', 'products.sku')
            ->join('products', 'products.product_id', '=', 'sale_detail.product_id')
            ->where('sale_id', $id)
            ->get();

        return response()->json(['sale' => $sale, 'sale_detail' => $sale_detail, 'sectors' => $sectors]);
    }

    public function getRemisionesChartData()
    {
        try {
            $data = \DB::table('sales')
                ->join('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id')
                ->join('sales_status', 'sales.sales_status_id', '=', 'sales_status.sales_status_id')
                ->select(
                    'sales.date',
                    'sales.seller',
                    'sales.folio',
                    'sales_status.name as status_name',
                    'sale_detail.quantity',
                    'sale_detail.cost',
                    'sale_detail.has_tax',
                    'sale_detail.public_product_name'
                )
                ->whereNotNull('sales.seller')
                ->where('sales.seller', '!=', '')
                ->get();

            $aliasVendedores = [
                'Flor de Maria Gutierrez Sanchez' => 'Flor de María Gutiérrez Sánchez',
                'Manola Ramirez Perez' => 'Manola Ramirez'
            ];

            $data->transform(function ($item) use ($aliasVendedores) {

                $nombreVendedor = trim($item->seller);

                if (array_key_exists($nombreVendedor, $aliasVendedores)) {
                    $item->seller = $aliasVendedores[$nombreVendedor];
                }

                $esCancelado = (strtolower($item->status_name) === 'cancelado' || strtolower($item->status_name) === 'cancelada');

                if ($esCancelado) {
                    $item->total_money = 0;
                } else {
                    $subtotal = (float) ($item->quantity ?? 0) * (float) ($item->cost ?? 0);
                    $item->total_money = $item->has_tax == 1 ? ($subtotal * 1.16) : $subtotal;
                }

                return $item;
            });

            return response()->json([
                'datos' => $data->groupBy('date')
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        DB::table('sales')->where('sale_id', $id)->update([
            'sales_status_id' => $request->sales_status_id,
            'updated_at' => now()
        ]);
        return response()->json(['success' => true]);
    }

    public function getClienteData(Request $request)
    {
        try {
            $busqueda = $request->get('q');
            if (!$busqueda)
                return response()->json([]);

            $data = \DB::table('sales')
                ->join('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id')
                ->join('customers', 'sales.customer_id', '=', 'customers.customer_id')
                ->where('customers.name', 'LIKE', "%{$busqueda}%")
                ->select(
                    'sales.date',
                    'sale_detail.public_product_name as producto',
                    'sale_detail.quantity',
                    'sale_detail.cost',
                    \DB::raw('(sale_detail.quantity * sale_detail.cost) as subtotal'),
                    \DB::raw('MONTH(sales.date) as mes'),
                    \DB::raw('YEAR(sales.date) as anio')
                )
                ->orderBy('sales.date', 'asc')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}