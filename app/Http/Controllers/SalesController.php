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
use App\Models\Inventory;
use Illuminate\Database\QueryException;
use App\Notifications\SaleAlmacenNotification;
use Illuminate\Support\Facades\Notification;

class SalesController extends Controller
{
    public function index()
    {
        $total_sales = Sale::max('folio') ?? 0;
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

                            Sale::where('prospect_id', $prospect->prospect_id)->update([
                                'customer_id' => $customer->customer_id,
                                'prospect_id' => null,
                                'is_customer' => 1
                            ]);

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

                $ultimo_folio = Sale::max('folio') ?? 0;
                $data['folio'] = $ultimo_folio + 1;

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

                $almacenUsers = User::role(['Warehouse', 'Admin'])->get();
                if($almacenUsers->count() > 0) {
                    Notification::send($almacenUsers, new SaleAlmacenNotification($sale, 'new_sale', "Nueva venta registrada: Folio " . $sale->folio . " esperando confirmación."));
                }
                
                // Notificar también al creador de la venta
                if ($sale->user) {
                    $sale->user->notify(new SaleAlmacenNotification($sale, 'pending', "Tu venta Folio " . $sale->folio . " ha sido enviada a almacén para confirmación."));
                }

                return response()->json(['message' => 'Venta registrada y enviada a almacén para confirmación'], 201);
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

    public function almacenDetail($id)
    {
        $sale = Sale::with(['customer', 'prospect', 'user', 'products'])->findOrFail($id);
        
        $user = Auth::user();
        if (!$user->hasRole(['Warehouse', 'Production', 'Admin', 'Sales'])) {
            abort(403, 'No tienes permiso para ver esta vista.');
        }

        return view('sales.almacen_detail', compact('sale'));
    }

    public function almacenAction(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                $sale = Sale::with(['user', 'products'])->findOrFail($id);
                $action = $request->input('action');
                $userVentas = $sale->user; 
                $almacenUsers = User::role(['Warehouse', 'Admin'])->get();
                
                if ($action === 'confirm') {
                    $lotAssignments = $request->input('lot_assignments', []);
                    if (empty($lotAssignments)) {
                        return response()->json(['success' => false, 'message' => 'Debes seleccionar un lote para cada producto.'], 422);
                    }

                    $sale->almacen_status = 'confirmed';
                    $sale->save();

                    // Generar la salida (Output) automáticamente
                    $output = \App\Models\Output::create([
                        'customer_id' => $sale->customer_id ?? 1,
                        'vendedor' => $sale->seller ?? ($sale->user ? $sale->user->name : 'N/A'),
                        'operator' => null,
                        'license_number' => null,
                        'security_seal' => 0,
                        'security_seal_number' => null,
                        'unit_plates' => null,
                        'trailer_plates' => null,
                        'comments' => 'Salida automática de Venta Folio ' . $sale->folio,
                        'transport_line_id' => null,
                    ]);

                    foreach ($lotAssignments as $assignment) {
                        $cliId = $assignment['cli_id'];
                        $quantity = floatval($assignment['quantity']);

                        // Buscar el registro CLI seleccionado
                        $cliRecord = \App\Models\Cli::where('cli_id', $cliId)->lockForUpdate()->first();
                        if (!$cliRecord) {
                            throw new \Exception("Registro de ubicación no encontrado (CLI ID: {$cliId}).");
                        }
                        if ($cliRecord->net_weight < $quantity) {
                            throw new \Exception("Stock insuficiente en la ubicación seleccionada. Disponible: {$cliRecord->net_weight}, Solicitado: {$quantity}");
                        }

                        // Decrementar el registro CLI (net_weight y quantity proporcional)
                        $cliRecord->net_weight -= $quantity;
                        if ($cliRecord->weight_per_unit > 0) {
                            $cliRecord->quantity = $cliRecord->net_weight / $cliRecord->weight_per_unit;
                        }
                        $cliRecord->save();

                        // Decrementar también inventory.stock para la vista general
                        $inventory = Inventory::where('inventory_id', $cliRecord->inventory_id)->lockForUpdate()->first();
                        if ($inventory) {
                            $inventory->stock -= $quantity;
                            $inventory->save();

                            // Insertar el producto en la salida generada
                            \Illuminate\Support\Facades\DB::table('product_outputs')->insert([
                                'output_id' => $output->output_id,
                                'product_id' => $inventory->product_id,
                                'quantity' => $quantity,
                                'warehouse_batch' => $inventory->batch,
                                'label_batch' => ''
                            ]);
                        }
                    }

                    if ($userVentas) {
                        $userVentas->notify(new SaleAlmacenNotification($sale, 'confirmed', "Tu venta Folio " . $sale->folio . " ha sido confirmada por almacén."));
                    }
                    if ($almacenUsers->count() > 0) {
                        Notification::send($almacenUsers, new SaleAlmacenNotification($sale, 'inventory_updated', "El inventario ha sido reducido para la venta Folio " . $sale->folio));
                    }
                    
                    return response()->json(['success' => true, 'message' => 'Venta confirmada e inventario actualizado. Se ha generado la salida automática (ID: ' . $output->output_id . ').']);
                } 
                elseif ($action === 'postpone') {
                    $request->validate(['reason' => 'required', 'date' => 'required|date']);
                    $sale->almacen_status = 'postponed';
                    $sale->almacen_comment = $request->reason;
                    $sale->almacen_postponed_date = $request->date;
                    $sale->save();
                    
                    if ($userVentas) {
                        $userVentas->notify(new SaleAlmacenNotification($sale, 'postponed', "Tu venta Folio " . $sale->folio . " fue pospuesta por almacén. Recordatorio: " . $request->date, $request->reason, $request->date));
                    }
                    return response()->json(['success' => true, 'message' => 'Venta pospuesta. Se te recordará el ' . $request->date]);
                }
                elseif ($action === 'mark_ready') {
                    // Cambia de postponed a pending para que pueda confirmar la salida
                    $sale->almacen_status = 'pending';
                    $sale->almacen_comment = null;
                    $sale->almacen_postponed_date = null;
                    $sale->save();
                    return response()->json(['success' => true, 'message' => 'Venta marcada como lista. Ahora puedes confirmar la salida.']);
                }
                elseif ($action === 'cancel') {
                    $request->validate(['reason' => 'required']);
                    $sale->almacen_status = 'cancelled';
                    $sale->almacen_comment = $request->reason;
                    $sale->sales_status_id = 5; // Cancelada
                    $sale->save();
                    
                    // Cancelar en Cuentas por Cobrar
                    \App\Models\CxcDetail::where('sale_id', $sale->sale_id)->update(['is_canceled' => 1]);
                    
                    if ($userVentas) {
                        $userVentas->notify(new SaleAlmacenNotification($sale, 'cancelled', "Tu venta Folio " . $sale->folio . " fue cancelada por almacén.", $request->reason));
                    }
                    return response()->json(['success' => true, 'message' => 'Venta cancelada.']);
                }

                return response()->json(['success' => false, 'message' => 'Acción inválida.'], 400);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function unreadNotifications()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['notifications' => []]);

        $notifications = $user->unreadNotifications->where('type', 'App\Notifications\SaleAlmacenNotification');
        return response()->json(['notifications' => $notifications]);
    }

    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false], 401);

        $notification = $user->unreadNotifications->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function postponedReminders()
    {
        $user = Auth::user();
        if (!$user || !$user->hasAnyRole(['Warehouse', 'Admin'])) {
            return response()->json(['reminders' => []]);
        }

        $today = now()->toDateString();
        $sales = Sale::where('almacen_status', 'postponed')
            ->whereNotNull('almacen_postponed_date')
            ->whereDate('almacen_postponed_date', '<=', $today)
            ->select('sale_id', 'folio', 'almacen_comment', 'almacen_postponed_date')
            ->get();

        return response()->json(['reminders' => $sales]);
    }

    public function getSaleLots($id)
    {
        $saleDetails = SaleDetail::where('sale_id', $id)->get();
        $result = [];

        foreach ($saleDetails as $detail) {
            $productName = $detail->public_product_name;
            if (empty($productName)) {
                $product = \App\Models\Product::find($detail->product_id);
                $productName = $product ? $product->name : 'Producto ID: ' . $detail->product_id;
            }

            // Traer registros CLI agrupados por lote+ubicación con stock disponible
            $cliRecords = DB::table('cli')
                ->join('inventory', 'inventory.inventory_id', '=', 'cli.inventory_id')
                ->join('locations', 'locations.location_id', '=', 'cli.location_id')
                ->where('inventory.product_id', $detail->product_id)
                ->where('cli.net_weight', '>', 0)
                ->select(
                    'cli.cli_id',
                    'inventory.inventory_id',
                    'inventory.batch',
                    'locations.name as location_name',
                    'cli.location_id',
                    'cli.net_weight',
                    'cli.quantity',
                    'cli.weight_per_unit'
                )
                ->get();

            $result[] = [
                'detail_id' => $detail->sale_detail_id,
                'product_id' => $detail->product_id,
                'product_name' => $productName,
                'quantity_required' => $detail->quantity,
                'lots' => $cliRecords,
            ];
        }

        return response()->json(['products' => $result]);
    }
}