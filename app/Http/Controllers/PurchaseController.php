<?php
/*
purchases
06/08/25
stefany 
Actualizado por: Stefany
Fecha de actualización: 11-02-2026
*/
namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use App\Models\Sector;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;

class PurchaseController extends Controller{


    public function index() 
    {
        $requisitions_count    = PurchaseRequisition::count();
        $requisitions_no_check = PurchaseRequisition::whereNull('consecutive')
            ->whereNull('purchase_order')
            ->count();

        $your_requisitions = PurchaseRequisition::where('applicant', auth()->user()->name)->count();

        $suppliers = Supplier::select('supplier_id','name')->get();
        $sectors   = Sector::select('sector_id','name')->get();
        
        $warehouseQuery = DB::table('insumos_entradas')
            ->select('id','fecha_llegada','proveedor','descripcion','cantidad','unidad','insumo','costo','moneda','categoria')
            ->orderByDesc('id');

        if (!auth()->user()->can('purchases.admin')) {
            $warehouseQuery->where('categoria', 'warehouse');
        }

        $warehouse_entries = $warehouseQuery->get();

        $payment_method = [
            (object)[ "type" => "PUE", "description" => "Pago en una sola exhibición" ],
            (object)[ "type" => "PPD", "description" => "Pago en parcialidades o diferido" ]
        ];

        $method_payment = [
            (object)[ "code" => "01", "description" => "Efectivo" ],
            (object)[ "code" => "02", "description" => "Cheque nominativo" ],
            (object)[ "code" => "03", "description" => "Transferencia electrónica de fondos" ],
            (object)[ "code" => "04", "description" => "Tarjeta de crédito" ],
            (object)[ "code" => "05", "description" => "Monedero electrónico" ],
            (object)[ "code" => "06", "description" => "Dinero electrónico" ],
            (object)[ "code" => "08", "description" => "Vales de despensa" ],
            (object)[ "code" => "12", "description" => "Dación en pago" ],
            (object)[ "code" => "13", "description" => "Pago por subrogación" ],
            (object)[ "code" => "14", "description" => "Pago por consignación" ],
            (object)[ "code" => "15", "description" => "Condonación" ],
            (object)[ "code" => "17", "description" => "Compensación" ],
            (object)[ "code" => "23", "description" => "Novación" ],
            (object)[ "code" => "24", "description" => "Confusión" ],
            (object)[ "code" => "25", "description" => "Remisión de deuda" ],
            (object)[ "code" => "26", "description" => "Prescripción o caducidad" ],
            (object)[ "code" => "27", "description" => "A satisfacción del acreedor" ],
            (object)[ "code" => "28", "description" => "Tarjeta de débito" ],
            (object)[ "code" => "29", "description" => "Tarjeta de servicios" ],
            (object)[ "code" => "30", "description" => "Aplicación de anticipos" ],
            (object)[ "code" => "99", "description" => "Por definir" ]
        ];

        $cfdi = [
            (object)[ "cfdi" => "G01", "description" => "Adquisición de mercancías" ],
            (object)[ "cfdi" => "G02", "description" => "Devoluciones, descuentos o bonificaciones" ],
            (object)[ "cfdi" => "G03", "description" => "Gastos en general" ],
            (object)[ "cfdi" => "I01", "description" => "Construcciones" ],
            (object)[ "cfdi" => "I02", "description" => "Mobiliario y equipo de oficina por inversiones" ],
            (object)[ "cfdi" => "I03", "description" => "Equipo de transporte" ],
            (object)[ "cfdi" => "I04", "description" => "Equipo de computo y accesorios" ],
            (object)[ "cfdi" => "I05", "description" => "Dados, troqueles, moldes, matrices y herramental" ],
            (object)[ "cfdi" => "I06", "description" => "Comunicaciones telefónicas" ],
            (object)[ "cfdi" => "I07", "description" => "Comunicaciones satelitales" ],
            (object)[ "cfdi" => "I08", "description" => "Otra maquinaria y equipo" ],
            (object)[ "cfdi" => "D01", "description" => "Honorarios médicos, dentales y gastos hospitalarios" ],
            (object)[ "cfdi" => "D02", "description" => "Gastos médicos por incapacidad o discapacidad" ],
            (object)[ "cfdi" => "D03", "description" => "Gastos funerales" ],
            (object)[ "cfdi" => "D04", "description" => "Donativos" ],
            (object)[ "cfdi" => "D05", "description" => "Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)" ],
            (object)[ "cfdi" => "D06", "description" => "Aportaciones voluntarias al SAR" ],
            (object)[ "cfdi" => "D07", "description" => "Primas por seguros de gastos médicos" ],
            (object)[ "cfdi" => "D08", "description" => "Gastos de transportación escolar obligatoria" ],
            (object)[ "cfdi" => "D09", "description" => "Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones" ],
            (object)[ "cfdi" => "D10", "description" => "Pagos por servicios educativos (colegiaturas)" ],
            (object)[ "cfdi" => "S01", "description" => "Sin efectos fiscales" ],
            (object)[ "cfdi" => "CP01", "description" => "Pagos" ],
            (object)[ "cfdi" => "CN01", "description" => "Nómina" ]
        ];

        return view('purchases', compact(
            'requisitions_count',
            'requisitions_no_check',
            'your_requisitions',
            'suppliers',
            'sectors',
            'warehouse_entries',
            'payment_method', 
            'method_payment', 
            'cfdi'          
        ));
    }

    public function getPurchasesCharts()
    {
        $requisitions_per_department = PurchaseRequisition::select(DB::raw('DISTINCT(department)'),DB::raw('count(*) as quantity'))->groupBy('department')->get();
        $requisitions_per_employee = PurchaseRequisition::select(DB::raw('DISTINCT(applicant)'),DB::raw('count(*) as quantity'))->groupBy('applicant')->orderBy('quantity','desc')->limit(6)->get();
        return response()->json(['requisitions_per_department'=>$requisitions_per_department,'requisitions_per_employee'=>$requisitions_per_employee]);
    }

    public function gPurchaseOrder(Request $request)
    {
        $products = $request->input('product_name');
        $items = [];
        if($products){
            foreach($products as $index => $product){
                if(!empty($product)){
                    $items[] = [
                        'description' => $product,
                        'quantity'    => $request->quantity[$index] ?? 0,
                        'iva'         => $request->iva[$index] ?? 0,
                        'unit_price'  => $request->unit_price[$index] ?? 0
                    ];
                }
            }
        }

        try {
            return DB::transaction(function () use ($request, $items) {
                
                $supplier_id = $request->supplier_id;
                $supplier_obj = null;

                if (empty($supplier_id)) {
                    if (Supplier::where('name', $request->name)->exists()) {
                        throw new \Exception("El nombre del proveedor '{$request->name}' ya existe.");
                    }

                    $supplier_obj = Supplier::create([
                        'supplier_code' => 'SP' . (Supplier::count() + 1),
                        'name'          => $request->name, 
                        'phone'         => $request->phone,
                        'rfc'           => $request->rfc,
                        'address'       => $request->address,
                        'city'          => $request->city,
                        'state'         => $request->state,
                        'district'      => $request->district,
                    ]);
                    
                    $supplier_id = $supplier_obj->supplier_id; 
                } else {
                    $supplier_obj = Supplier::findOrFail($supplier_id);
                }

                if (PurchaseOrder::where('id', $request->id)->exists()) {
                    throw new \Exception("El folio {$request->id} ya está registrado.");
                }

                $purchaseOrder = PurchaseOrder::create([
                    'id'               => $request->id,
                    'supplier_id'      => $supplier_id, 
                    'contact'          => $request->contact,
                    'delivery_time'    => $request->delivery_time,
                    'delivery_date'    => $request->delivery_date,
                    'guia'             => $request->guia,
                    'cfdi'             => $request->cfdi,
                    'payment_method'   => $request->payment_method,
                    'method_payment'   => $request->method_payment,
                    'application_date' => $request->application_date,
                    'applicant'        => $request->applicant,
                    'price'            => $request->price, 
                ]);

                foreach ($items as $item) {
                    PurchaseOrderDetail::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_name'      => $item['description'],
                        'has_iva'           => $item['iva'],
                        'unit_price'        => $item['unit_price'],
                        'quantity'          => $item['quantity'],
                    ]);
                }

                $data = [
                    'supplier_name'    => $supplier_obj->name,
                    'email'            => $supplier_obj->email,
                    'phone'            => $supplier_obj->phone,
                    'rfc'              => $supplier_obj->rfc,
                    'address'          => $supplier_obj->address . ', ' . $supplier_obj->city,
                    'cc_code'          => $purchaseOrder->id,
                    'contact'          => $request->contact,
                    'method_payment'   => $request->method_payment,
                    'payment_method'   => $request->payment_method,
                    'cfdi'             => $request->cfdi,
                    'application_date' => $request->application_date,
                    'delivery_time'    => $request->delivery_time,
                    'delivery_date'    => $request->delivery_date,
                    'numero_guia'      => $request->guia,
                    'items'            => $items,
                    'total'            => $request->price
                ];

                return Pdf::loadView('formats.purchases.01', $data)
                        ->setPaper('letter')
                        ->stream('OrdenCompra_' . $purchaseOrder->id . '.pdf');
            });

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function gSupSelectCrit(Request $request)
    {
        $data = [
            'supplier' => $request->supplier,
            'address' => $request->address,
            'date' => $request->date,
            'products' => $request->products
        ];
        $count = 0;
        $items = $request->all();
        foreach ($items as $key => $value) {
            if (preg_match('/^(e|q|o)-\d+$/', $key)) {
                $data['checks'][] = $value;
                if($value == 1){
                    $count += 1;
                }
            }
        }
        $data['result'] = round(($count/26)*100,2);
        return Pdf::loadView('formats.purchases.05', $data)->setPaper('letter')->stream('CriteriosSeleccion_DEMO.pdf');
    }

    public function gSupplierEvaluation(Request $request)
    {
        $answers = $request->input('answers');
        $qualifications = $request->input('qualification');
        $questions_answers = [];
        foreach($answers as $index => $answer){
            $questions_answers [] = [
                'answer' => $answer,
                'qualification' => $qualifications[$index]
            ];
        }
        $data = [
            'supplier' => $request->supplier_name,
            'rfc' => $request->rfc,
            'address' => $request->address,
            'evaluation_date' => $request->evaluation_date,
            'evaluator' => $request->evaluator,
            'products' => $request->products,
            'observations' => $request->observations,
        ];
        $data['questions_answers'] = $questions_answers;
        return Pdf::loadView('formats.purchases.06', $data)->setPaper('letter')->stream('EvaluacionProveedores_DEMO.pdf');
    }

    public function orders()
    {
        $suppliers = Supplier::all(); 

        $payment_method = [
            (object)[ "type" => "PUE", "description" => "Pago en una sola exhibición" ],
            (object)[ "type" => "PPD", "description" => "Pago en parcialidades o diferido" ]
        ];

        $method_payment = [
            (object)[ "code" => "01", "description" => "Efectivo" ],
            (object)[ "code" => "02", "description" => "Cheque nominativo" ],
            (object)[ "code" => "03", "description" => "Transferencia electrónica de fondos" ],
            (object)[ "code" => "04", "description" => "Tarjeta de crédito" ],
            (object)[ "code" => "05", "description" => "Monedero electrónico" ],
            (object)[ "code" => "06", "description" => "Dinero electrónico" ],
            (object)[ "code" => "08", "description" => "Vales de despensa" ],
            (object)[ "code" => "12", "description" => "Dación en pago" ],
            (object)[ "code" => "13", "description" => "Pago por subrogación" ],
            (object)[ "code" => "14", "description" => "Pago por consignación" ],
            (object)[ "code" => "15", "description" => "Condonación" ],
            (object)[ "code" => "17", "description" => "Compensación" ],
            (object)[ "code" => "23", "description" => "Novación" ],
            (object)[ "code" => "24", "description" => "Confusión" ],
            (object)[ "code" => "25", "description" => "Remisión de deuda" ],
            (object)[ "code" => "26", "description" => "Prescripción o caducidad" ],
            (object)[ "code" => "27", "description" => "A satisfacción del acreedor" ],
            (object)[ "code" => "28", "description" => "Tarjeta de débito" ],
            (object)[ "code" => "29", "description" => "Tarjeta de servicios" ],
            (object)[ "code" => "30", "description" => "Aplicación de anticipos" ],
            (object)[ "code" => "99", "description" => "Por definir" ]
        ];

        $cfdi = [
            (object)[ "cfdi" => "G01", "description" => "Adquisición de mercancías" ],
            (object)[ "cfdi" => "G02", "description" => "Devoluciones, descuentos o bonificaciones" ],
            (object)[ "cfdi" => "G03", "description" => "Gastos en general" ],
            (object)[ "cfdi" => "I01", "description" => "Construcciones" ],
            (object)[ "cfdi" => "I02", "description" => "Mobiliario y equipo de oficina por inversiones" ],
            (object)[ "cfdi" => "I03", "description" => "Equipo de transporte" ],
            (object)[ "cfdi" => "I04", "description" => "Equipo de computo y accesorios" ],
            (object)[ "cfdi" => "I05", "description" => "Dados, troqueles, moldes, matrices y herramental" ],
            (object)[ "cfdi" => "I06", "description" => "Comunicaciones telefónicas" ],
            (object)[ "cfdi" => "I07", "description" => "Comunicaciones satelitales" ],
            (object)[ "cfdi" => "I08", "description" => "Otra maquinaria y equipo" ],
            (object)[ "cfdi" => "D01", "description" => "Honorarios médicos, dentales y gastos hospitalarios" ],
            (object)[ "cfdi" => "D02", "description" => "Gastos médicos por incapacidad o discapacidad" ],
            (object)[ "cfdi" => "D03", "description" => "Gastos funerales" ],
            (object)[ "cfdi" => "D04", "description" => "Donativos" ],
            (object)[ "cfdi" => "D05", "description" => "Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)" ],
            (object)[ "cfdi" => "D06", "description" => "Aportaciones voluntarias al SAR" ],
            (object)[ "cfdi" => "D07", "description" => "Primas por seguros de gastos médicos" ],
            (object)[ "cfdi" => "D08", "description" => "Gastos de transportación escolar obligatoria" ],
            (object)[ "cfdi" => "D09", "description" => "Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones" ],
            (object)[ "cfdi" => "D10", "description" => "Pagos por servicios educativos (colegiaturas)" ],
            (object)[ "cfdi" => "S01", "description" => "Sin efectos fiscales" ],
            (object)[ "cfdi" => "CP01", "description" => "Pagos" ],
            (object)[ "cfdi" => "CN01", "description" => "Nómina" ]
        ];

        return view('purchases.order', compact('suppliers', 'payment_method', 'method_payment', 'cfdi'));
    }

    public function show($id)
    {
        if ($id === 'get-orders') {
            return $this->getPurchaseOrders(request());
        }

        return $this->streamPdf($id);
    }

    public function getPurchaseOrders(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\PurchaseOrder::with('supplier');

            if ($request->search_orders) {
                $search = $request->search_orders;
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%$search%")           
                      ->orWhere('applicant', 'like', "%$search%")  
                      ->orWhereHas('supplier', function($subQ) use ($search){
                          $subQ->where('name', 'like', "%$search%"); 
                      });
                });
            }
            
            if ($request->date_filter) {
                $query->whereDate('application_date', $request->date_filter);
            }

            $orders = $query->orderBy('id', 'desc')->get();

            return response()->json(['orders' => $orders]);
        }
        
        return abort(404);
    }

    public function streamPdf($id)
    {
        $order = PurchaseOrder::with(['details', 'supplier'])->findOrFail($id);

        $data = [
            'supplier_name'    => $order->supplier->name,
            'email'            => $order->supplier->email,
            'phone'            => $order->supplier->phone,
            'rfc'              => $order->supplier->rfc,
            'address'          => $order->supplier->address, 
            'cc_code'          => $order->id, 
            'contact'          => $order->contact,
            'method_payment'   => $order->method_payment,
            'payment_method'   => $order->payment_method,
            'cfdi'             => $order->cfdi,
            'application_date' => $order->application_date,
            'delivery_time'    => $order->delivery_time,
            'delivery_date'    => $order->delivery_date,
            'guia'      => $order->guia, 
            'items'            => $order->details->map(function($item) {
                return [
                    'description' => $item->product_name,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unit_price,
                    'iva'         => $item->has_iva
                ];
            })
        ];

        $subtotal = 0;
        $iva = 0;
        foreach ($data['items'] as $item) {
            $importe = $item['quantity'] * $item['unit_price'];
            $subtotal += round($importe, 2);
            if ($item['iva'] == 1) {
                $iva += round($importe * 0.16, 2);
            }
        }

        $data['subtotal'] = $subtotal;
        $data['iva']      = $iva;
        $data['total']    = round($subtotal + $iva, 2);

        return Pdf::loadView('formats.purchases.01', $data)
                ->setPaper('letter')
                ->stream('OrdenCompra_' . $order->id . '.pdf');
    }

    public function edit($id)
    {
        $order = \App\Models\PurchaseOrder::with(['details', 'supplier'])->findOrFail($id);
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                $order = PurchaseOrder::findOrFail($id);

                if ($request->id != $id && PurchaseOrder::where('id', $request->id)->exists()) {
                    throw new \Exception("El folio número {$request->id} ya está en uso por otra orden.");
                }

                $order->update([
                    'id'               => $request->id, 
                    'supplier_id'      => $request->supplier_id,
                    'contact'          => $request->contact,
                    'delivery_time'    => $request->delivery_time,
                    'delivery_date'    => $request->delivery_date,
                    'guia'             => $request->guia,
                    'cfdi'             => $request->cfdi,
                    'payment_method'   => $request->payment_method,
                    'method_payment'   => $request->method_payment,
                    'application_date' => $request->application_date,
                    'applicant'        => $request->applicant,
                    'price'            => $request->price,
                ]);

                $order->details()->delete();

                $products = $request->input('product_name');
                if ($products) {
                    foreach ($products as $index => $product) {
                        if (!empty($product)) {
                            $order->details()->create([
                                'product_name' => $product,
                                'quantity'     => $request->quantity[$index] ?? 0,
                                'unit_price'   => $request->unit_price[$index] ?? 0,
                                'has_iva'      => $request->iva[$index] ?? 0,
                            ]);
                        }
                    }
                }

                return response()->json([
                    'success' => true, 
                    'message' => 'Order updated successfully',
                    'new_id'  => $order->id 
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $order = PurchaseOrder::findOrFail($id);

                $order->details()->delete();

                $order->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Order deleted successfully'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

}