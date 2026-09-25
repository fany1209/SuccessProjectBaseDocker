<?php
/*
Inventario
Inventario Controlador
Fecha de creación: 13-08-2025
Creado por: Jacob
Actualizado por: Stefany
Fecha de actualización: 15-01-2026
*/
namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Http\Controllers\Controller;
use App\Models\Cli;
use App\Models\Concept;
use App\Models\Input;
use App\Models\Inventory;
use App\Models\Operator;
use App\Models\Output;
use App\Models\Product;
use App\Models\ProductInputs;
use App\Models\ProductOutputs;
use App\Models\Quarantine;
use App\Models\Trailer;
use App\Models\TransportLine;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(){
        $available_locations = DB::table('inventory')
        ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
        ->join('locations', 'locations.location_id', '=', 'cli.location_id')
        ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
        ->join('products', 'products.product_id', '=', 'inventory.product_id')
        ->select(
            'locations.name'
        )->get();
        $quarantine = Quarantine::all();
        $transport_lines = TransportLine::all();
        $products_all = DB::table('products')
            ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
            ->select('products.product_id', 'products.name', 'products.category_id', 'categories.name as category_name')
            ->get();
        $products = DB::table('inventory')->leftJoin('products','products.product_id','=','inventory.product_id')->select('products.product_id','products.name')->orderBy('products.product_id')->distinct()->get();
        $suppliers = DB::table('suppliers')->select('supplier_id','name')->get();
        $customers = DB::table('customers')->select('customer_id','name')->get();
        $products_warehouse = Inventory::select('inventory.product_id','products.name')->join('products','products.product_id','=','inventory.product_id')->distinct()->get();
        $batchs = Inventory::select('inventory_id','batch','stock','inventory.product_id','products.name','products.unit','products.batch_code')->join('products','products.product_id','=','inventory.product_id')->get();
        $inputs_years = DB::table('inputs')->selectRaw('year(updated_at) as year')->orderByDesc('year')->distinct()->get();
        $inputs_months = DB::table('inputs')->selectRaw('year(updated_at) as year, month(updated_at) as month')->groupByRaw('year(updated_at), month(updated_at)')->orderByDesc('year')->orderByDesc('month')->get();
        $inputs_dates = DB::table('inputs')->selectRaw("input_id, comments, year(updated_at) as year, month(updated_at) as month,day(updated_at) as day, DATE_FORMAT(updated_at, '%W, %e %M %H:%i') as date")->groupByRaw('input_id, comments, year(updated_at), month(updated_at), day(updated_at), date')->orderByDesc('input_id')->orderByDesc('year')->orderByDesc('month')->orderByDesc('day')->orderByDesc('date')->get();
        $inputs_products = DB::table('product_inputs')->select('inputs.input_id','products.name','product_inputs.quantity','products.unit')->join('products', 'products.product_id', '=', 'product_inputs.product_id')->join('inputs', 'inputs.input_id', '=', 'product_inputs.input_id')->get();
        $outputs_years = DB::table('outputs')->selectRaw('year(updated_at) as year')->orderByDesc('year')->distinct()->get();
        $outputs_months = DB::table('outputs')->selectRaw('year(updated_at) as year, month(updated_at) as month')->groupByRaw('year(updated_at), month(updated_at)')->orderByDesc('year')->orderByDesc('month')->get();
        $outputs_dates = DB::table('outputs')->selectRaw("output_id, comments, year(updated_at) as year, month(updated_at) as month,day(updated_at) as day, DATE_FORMAT(updated_at, '%W, %e %M %H:%i') as date")->groupByRaw('output_id, comments, year(updated_at), month(updated_at), day(updated_at), date')->orderByDesc('output_id')->orderByDesc('year')->orderByDesc('month')->orderByDesc('day')->orderByDesc('date')->get();
        $outputs_products = DB::table('product_outputs')->select('outputs.output_id','products.name','product_outputs.quantity','products.unit')->join('products', 'products.product_id', '=', 'product_outputs.product_id')->join('outputs', 'outputs.output_id', '=', 'product_outputs.output_id')->get();
        $operators = Operator::select('operator_id','name','license')->get();
        $vehicles = Vehicle::select('plate','type')->get();
        $trailers = Trailer::select('plate','type')->get();
        $concepts = Concept::select('concept_id','name')->get();
        $warehouses = Warehouse::select('warehouse_id','name')->get();
        $locations = \App\Models\Location::select('location_id','name','warehouse_id')->get();

        $almacen_sales = \App\Models\Sale::with(['customer', 'prospect', 'user'])
            ->whereNotNull('almacen_status')
            ->orderBy('sale_id', 'desc')
            ->get();

        $product_locations = DB::table('cli')->select('inventory_id', 'location_id', 'bag_number', 'protein', 'weight_per_unit', 'quantity')->get();

        return view('inventory',compact('warehouses','locations','available_locations','concepts','trailers','vehicles','operators','batchs','outputs_products','inputs_products','outputs_dates','inputs_dates','outputs_months','outputs_years','inputs_months','inputs_years','transport_lines','suppliers','customers','products','products_all','quarantine','products_warehouse', 'almacen_sales', 'product_locations'));
    }

    //Pal inicio de Inventario
    public function getInventoryAvailable(Request $request)
    {
        $search = $request->input('search');
        $query = DB::table('products')
            ->leftJoin('inventory', 'inventory.product_id', '=', 'products.product_id')
            ->select(
                'products.product_id',
                'products.name',
                DB::raw('COALESCE(SUM(inventory.stock), 0) as stock'),
                'products.unit'
            )
            ->groupBy('products.product_id', 'products.name', 'products.unit');

        if (!empty($search)) {
            $query->where('products.name', 'like', '%' . $search . '%');
        }

        $products = $query->get();
        $quarantine = DB::table('quarantine')
            ->join('inventory', 'inventory.inventory_id', '=', 'quarantine.inventory_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'quarantine.quarantine_id',
                'products.name',
                'inventory.batch',
                'quarantine.quantity',
                'products.unit',
                'quarantine.notes',
                'inventory.inventory_id'
            )
            ->get();

       $productsHigh = Product::leftJoin('inventory', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'products.product_id',
                'products.name',
                'products.unit',
                'products.stock_max',
                DB::raw('COALESCE(SUM(inventory.stock), 0) as stock')
            )
            ->where('products.category_id', '=', 17) 
            ->groupBy('products.product_id', 'products.name', 'products.unit', 'products.stock_max')
            ->orderBy('stock', 'desc')
            ->get();

        $productsLow = Product::leftJoin('inventory', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'products.product_id',
                'products.name',
                'products.unit',
                'products.stock_min',
                DB::raw('COALESCE(SUM(inventory.stock), 0) as stock')
            )
            ->havingRaw('COALESCE(SUM(inventory.stock), 0) <= products.stock_min')
            ->groupBy('products.product_id', 'products.name', 'products.unit', 'products.stock_min')
            ->orderBy('stock')
            ->get();

        return response()->json([
            'products' => $products,
            'productsLow' => $productsLow,
            'productsHight' => $productsHigh,
            'quarantine' => $quarantine
        ]);
    }

    //Pa' lo de Quarantine
    public function addQuarantine(Request $request){
        $request -> validate([
            'inventory_id' => 'required|integer',
            'quantity' => 'required|numeric|min:0',
            'notes' => 'required|string|max:350',
        ]);
        Quarantine::create($request->only(['quantity','inventory_id','notes']));
        Inventory::where('inventory_id',$request->inventory_id)->update(['stock' => DB::raw("stock-$request->quantity")]);
        return response()->json(['message' => 'Operation successfuly make it'], 201);
    }

    public function updateQuarantine(Request $request){
        $request -> validate([
            'quarantine_id' => 'required|integer',
            'quantity' => 'required|numeric|min:0'
        ]);
        $query = Quarantine::where('quarantine_id',$request->quarantine_id)->first();
        if(($query->quantity-$request->quantity)<=0){
            $quarantine = Quarantine::find($request->quarantine_id);
            $quarantine->delete();
        }
        Quarantine::where('quarantine_id',$request->quarantine_id)->update(['quantity'=>DB::raw("quantity-$request->quantity")]);
        Inventory::where('inventory_id',$query->inventory_id)->update(['stock' => DB::raw("stock+$request->quantity")]);
        return response()->json(['message' => 'Operation successfuly make it'], 201);
    }

    //Pa' Transaction
    public function makeTransaction(Request $request)
    {
        try {
            if ($request->has('quantity') && is_array($request->quantity)) {
                $cleanedQuantities = array_map(function ($q) {
                    return is_string($q) ? str_replace(',', '.', trim($q)) : $q;
                }, $request->quantity);
                $request->merge(['quantity' => $cleanedQuantities]);
            }

            $request->validate([
                'type'                 => 'required|string|in:Input,Output,InternalOutput',
                'transport_line'       => 'nullable|integer',
                'operator'             => 'nullable|string|max:200',
                'license_number'       => 'nullable|string|max:50',
                'security_seal'        => 'required|integer',
                'security_seal_number' => 'nullable|required_if:security_seal,1|string|max:50',
                'unit_plates'          => 'nullable|string|max:20',
                'trailer_plates'       => 'nullable|string|max:20',
                'comments'             => 'nullable|string|max:300',
                'supplier'             => 'nullable|required_if:type,Input|integer',
                'customer'             => 'nullable|required_if:type,Output|integer',
                'vendedor'             => 'nullable|required_if:type,Output|string|max:200',
                'product_id'           => 'required|array|min:1',
                'product_id.*'         => 'required|integer',
                'quantity'             => 'required|array|min:1',
                'quantity.*'           => 'required|numeric|min:0.001',
                'warehouse_batch'      => 'required|array|min:1',
                'warehouse_batch.*'    => 'required|string|max:50',
                'location_id'          => 'nullable|array',
                'location_id.*'        => 'nullable|integer',
                'concept_id'           => 'nullable|array',
                'concept_id.*'         => 'nullable|integer',
                'weight_per_unit'      => 'nullable|array',
                'weight_per_unit.*'    => 'nullable|numeric',
                'bag_number'           => 'nullable|array',
                'protein'              => 'nullable|array',
                'bag_weight'           => 'nullable|array',
                'bag_location_id'      => 'nullable|array',
            ]);

            return DB::transaction(function () use ($request) {
                if ($request->type === 'Output' && auth()->user()->hasRole('Warehouse')) {
                    $invalidProducts = DB::table('products')
                        ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
                        ->whereIn('products.product_id', $request->product_id)
                        ->where(function($query) {
                            $query->whereNull('categories.name')
                                  ->orWhere('categories.name', 'not like', '%insumo%');
                        })
                        ->exists();

                    if ($invalidProducts) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'product_id' => 'El almacenista solo puede dar salida a insumos.'
                        ]);
                    }
                }

                $data = $request->only([
                    'operator', 'license_number', 'security_seal', 
                    'security_seal_number', 'unit_plates', 'trailer_plates', 'comments'
                ]);
                $data['transport_line_id'] = $request->transport_line;

                if ($request->type === 'Input') {
                    $data['supplier_id'] = $request->supplier;
                    $movement = Input::create($data);
                    $fkField  = 'input_id';
                    $fkValue  = $movement->input_id;
                } else {
                    if ($request->type === 'InternalOutput') {
                        $internalCustomer = \App\Models\Customer::firstOrCreate(
                            ['name' => 'Consumo Interno Producción'],
                            ['customer_code' => 'INT-PROD']
                        );
                        $data['customer_id'] = $internalCustomer->customer_id;
                        $data['vendedor'] = 'Producción';
                    } else {
                        $data['customer_id'] = $request->customer;
                        $data['vendedor'] = $request->vendedor;
                    }
                    $movement = Output::create($data);
                    $fkField  = 'output_id';
                    $fkValue  = $movement->output_id;
                }

                $details = [];
                foreach ($request->product_id as $index => $productId) {
                    $qty = (float) $request->quantity[$index];
                    
                    $row = [
                        'product_id' => $productId,
                        'quantity'   => $qty,
                        $fkField     => $fkValue,
                    ];

                    if ($request->type === 'Output' || $request->type === 'InternalOutput') {
                        $inventory = Inventory::where('inventory_id', $request->warehouse_batch[$index])->firstOrFail();
                        $row['warehouse_batch'] = $inventory->batch;
                        $row['label_batch']     = $request->label_batch[$index] ?? null;
                        $inventory->decrement('stock', $qty);
                        
                        if (isset($request->bag_number[$index]) && is_array($request->bag_number[$index]) && count($request->bag_number[$index]) > 0) {
                            foreach ($request->bag_number[$index] as $bag_num) {
                                $query = Cli::where('inventory_id', $inventory->inventory_id)
                                   ->where('bag_number', $bag_num);
                                if (isset($request->output_location_id[$index])) {
                                    $query->where('location_id', $request->output_location_id[$index]);
                                } else if (isset($request->location_id[$index])) {
                                    $query->where('location_id', $request->location_id[$index]);
                                }
                                
                                $cliRecord = $query->first();
                                $internalWeight = $cliRecord ? $cliRecord->weight_per_unit : null;
                                
                                $query->delete();
                                
                                if ($request->type === 'InternalOutput') {
                                    \App\Models\YeastProduction::create([
                                        'output_id' => $fkValue,
                                        'date' => now(),
                                        'bag_number' => $bag_num,
                                        'internal_weight' => $internalWeight,
                                    ]);
                                }
                            }
                        } else {
                            $loc = $request->output_location_id[$index] ?? ($request->location_id[$index] ?? null);
                            if ($loc) {
                                $cli = Cli::where('inventory_id', $inventory->inventory_id)
                                   ->where('location_id', $loc)
                                   ->first();
                                if ($cli) {
                                    if ($cli->quantity <= $qty) {
                                        $cli->delete();
                                    } else {
                                        $cli->quantity -= $qty;
                                        $cli->net_weight = $cli->quantity * $cli->weight_per_unit;
                                        $cli->save();
                                    }
                                }
                            }
                            
                            if ($request->type === 'InternalOutput') {
                                \App\Models\YeastProduction::create([
                                    'output_id' => $fkValue,
                                    'date' => now(),
                                    'bag_number' => null,
                                ]);
                            }
                        }
                    } else {
                        $row['warehouse_batch'] = $request->warehouse_batch[$index];
                        $inventory = Inventory::create([
                            'product_id' => $productId,
                            'stock'      => $qty,
                            'batch'      => $row['warehouse_batch'],
                        ]);

                        $locId = $request->location_id[$index] ?? null;
                        
                        $hasBagLocation = false;
                        if (isset($request->bag_location_id[$index]) && is_array($request->bag_location_id[$index])) {
                            foreach ($request->bag_location_id[$index] as $bLoc) {
                                if (!empty($bLoc)) {
                                    $hasBagLocation = true;
                                    break;
                                }
                            }
                        }

                        if (($locId || $hasBagLocation) && !empty($request->concept_id[$index])) {
                            $conceptId = $request->concept_id[$index];
                            $weight = $request->weight_per_unit[$index] ?? 0;
                            
                            if (isset($request->bag_number[$index]) && is_array($request->bag_number[$index]) && count($request->bag_number[$index]) > 0) {
                                foreach ($request->bag_number[$index] as $b_idx => $bag_num) {
                                    $bagLocId = $request->bag_location_id[$index][$b_idx] ?? $locId;
                                    $realWeight = $request->bag_weight[$index][$b_idx] ?? $weight;
                                    
                                    if ($bagLocId) {
                                        Cli::create([
                                            'location_id' => $bagLocId,
                                            'inventory_id' => $inventory->inventory_id,
                                            'concept_id' => $conceptId,
                                            'quantity' => 1,
                                            'weight_per_unit' => $realWeight,
                                            'net_weight' => 1 * $realWeight,
                                            'bag_number' => $bag_num,
                                            'protein' => $request->protein[$index][$b_idx] ?? null,
                                        ]);
                                    }
                                }
                            } else {
                                if ($locId) {
                                    Cli::create([
                                        'location_id' => $locId,
                                        'inventory_id' => $inventory->inventory_id,
                                        'concept_id' => $conceptId,
                                        'quantity' => $qty,
                                        'weight_per_unit' => $weight,
                                        'net_weight' => $qty * $weight,
                                    ]);
                                }
                            }
                        }
                    }
                    $details[] = $row;
                }

                if ($request->type === 'Input') {
                    ProductInputs::insert($details);
                } else {
                    ProductOutputs::insert($details);
                }

                if ($request->has('req_id') && $request->type === 'InternalOutput') {
                    $matReq = \App\Models\ProductionMaterialRequest::find($request->req_id);
                    if ($matReq) {
                        $matReq->status = 'Surtido';
                        $matReq->save();
                    }
                }

                return response()->json(['message' => 'Success'], 201);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateDate(Request $request){
        try{
            DB::transaction(function () use ($request){
                $type = $request->input('type');
                $id = $request->input('id');
                $new_date = $request->input('updated_at');
                $new_date = Carbon::parse($new_date)->setTimeFrom(Carbon::now());
                if($type == 'input'){
                    Input::where('input_id',$id)->update([
                        'updated_at' => $new_date
                    ]);
                }else{
                    Output::where('output_id',$id)->update([
                        'updated_at' => $new_date
                    ]);
                }
                return response()->json(['message' => 'Operation successfuly make it'], 201);
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }

    public function updateComment(Request $request, $type, $id) {
        $request->validate(['comments' => 'nullable|string|max:1000']);
        if ($type === 'input') {
            DB::table('inputs')->where('input_id', $id)->update(['comments' => $request->comments]);
        } else {
            DB::table('outputs')->where('output_id', $id)->update(['comments' => $request->comments]);
        }
        return response()->json(['message' => 'Updated']);
    }

    public function pendingPallets()
    {
        $pallets = \App\Models\Pallet::with('yeastProductions')->where('inventory_status', 'Enviada')->orderBy('pallet_id', 'desc')->get();
        
        // Calculate total final weight for each pallet based on yeast productions or sacks * 25kg
        foreach ($pallets as $pallet) {
            $calculatedWeight = 0;
            foreach ($pallet->yeastProductions as $yp) {
                if ($yp->bags_quantity > 0 && $yp->finished_product_kg > 0) {
                    $calculatedWeight += ($yp->finished_product_kg / $yp->bags_quantity) * ($yp->pivot->sacks_contributed ?? 0);
                }
            }
            $pallet->total_weight = $calculatedWeight > 0 ? round($calculatedWeight, 2) : round(($pallet->current_sacks ?? 0) * 25, 2);
        }

        $warehouses = \App\Models\Warehouse::all();
        $locations = \App\Models\Location::with('warehouse')->get();
        $suppliers = \App\Models\Supplier::select('supplier_id', 'name', 'supplier_code')->get();
        $concepts = \App\Models\Concept::select('concept_id', 'name')->get();
        $transport_lines = \App\Models\TransportLine::select('transport_line_id', 'name')->get();
        $products = \App\Models\Product::select('product_id', 'name', 'unit')->get();

        // Ensure internal production supplier exists safely
        $internalSupplier = \App\Models\Supplier::where('name', 'Producción Interna')
            ->orWhere('supplier_code', 'PROD-INT')
            ->first();

        if (!$internalSupplier) {
            $internalSupplier = \App\Models\Supplier::create([
                'name'          => 'Producción Interna',
                'supplier_code' => 'PROD-INT',
                'contact'       => 'Planta Producción',
                'phone'         => 'N/A',
                'email'         => 'produccion@yeacali.com',
                'rfc'           => 'XAXX010101000',
                'address'       => 'Planta',
                'city'          => 'Local',
                'state'         => 'Local',
                'sector_id'     => 1,
            ]);
            $suppliers = \App\Models\Supplier::select('supplier_id', 'name', 'supplier_code')->get();
        }

        // Use "Producto Terminado" as default concept
        $internalConcept = \App\Models\Concept::where('name', 'Producto Terminado')->first()
            ?? \App\Models\Concept::where('name', 'like', '%Terminado%')->first()
            ?? \App\Models\Concept::firstOrCreate(['name' => 'Producto Terminado']);

        if (!$concepts->contains('concept_id', $internalConcept->concept_id)) {
            $concepts = \App\Models\Concept::select('concept_id', 'name')->get();
        }

        // Default to Yeacali sacos (Product ID 623 with unit Kg, or fallback to 622)
        $defaultProduct = \App\Models\Product::where('product_id', 623)
            ->orWhere('name', 'like', '%Yeacali%')
            ->first();

        return view('inventory.pallets.pending', compact(
            'pallets', 'warehouses', 'locations', 'suppliers', 'concepts', 
            'transport_lines', 'products', 'internalSupplier', 'internalConcept', 'defaultProduct'
        ));
    }

    public function acceptPallet(Request $request, $id)
    {
        $request->validate([
            'location_id'          => 'required|exists:locations,location_id',
            'supplier_id'          => 'nullable|exists:suppliers,supplier_id',
            'concept_id'           => 'nullable|exists:concepts,concept_id',
            'product_id'           => 'nullable|exists:products,product_id',
            'quantity'             => 'nullable|numeric|min:0.01',
            'weight_per_unit'      => 'nullable|numeric|min:0.01',
            'final_weight'         => 'nullable|numeric|min:0.01',
            'warehouse_batch'      => 'nullable|string|max:50',
            'transport_line'       => 'nullable|integer',
            'operator'             => 'nullable|string|max:200',
            'license_number'       => 'nullable|string|max:50',
            'security_seal'        => 'nullable|integer',
            'security_seal_number' => 'nullable|string|max:50',
            'unit_plates'          => 'nullable|string|max:20',
            'trailer_plates'       => 'nullable|string|max:20',
            'comments'             => 'nullable|string|max:300',
        ]);

        $pallet = \App\Models\Pallet::with('yeastProductions')->findOrFail($id);

        if ($pallet->inventory_status !== 'Enviada') {
            return response()->json(['message' => 'La tarima no está pendiente de ser recibida.'], 400);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($pallet, $request) {
            // Resolve supplier
            $supplierId = $request->supplier_id;
            if (!$supplierId) {
                $supplier = \App\Models\Supplier::where('name', 'Producción Interna')
                    ->orWhere('supplier_code', 'PROD-INT')
                    ->first();
                if (!$supplier) {
                    $supplier = \App\Models\Supplier::create([
                        'name'          => 'Producción Interna',
                        'supplier_code' => 'PROD-INT',
                        'contact'       => 'Planta Producción',
                        'sector_id'     => 1
                    ]);
                }
                $supplierId = $supplier->supplier_id;
            }

            // Resolve concept (default to "Producto Terminado")
            $conceptId = $request->concept_id;
            if (!$conceptId) {
                $concept = \App\Models\Concept::where('name', 'Producto Terminado')->first()
                    ?? \App\Models\Concept::where('name', 'like', '%Terminado%')->first();
                $conceptId = $concept ? $concept->concept_id : 2;
            }

            // Resolve product
            $defaultProd = \App\Models\Product::where('product_id', 623)->orWhere('name', 'like', '%Yeacali%')->first();
            $productId = $request->product_id ?: ($defaultProd->product_id ?? 623);

            // Resolve quantities and weights:
            // Sacks count:
            $sacks = (float) ($request->quantity ?: $pallet->current_sacks);
            $weightPerUnit = (float) ($request->weight_per_unit ?: 25);

            // Final weight in Kg (this is the actual stock amount that enters inventory):
            $finalWeight = (float) ($request->final_weight ?: ($sacks * $weightPerUnit));
            $batch = $request->warehouse_batch ?: $pallet->pallet_number;

            // 1. Create Input movement record
            $input = \App\Models\Input::create([
                'supplier_id'          => $supplierId,
                'transport_line_id'    => $request->transport_line ?: null,
                'operator'             => $request->operator,
                'license_number'       => $request->license_number,
                'security_seal'        => $request->security_seal ?? 0,
                'security_seal_number' => $request->security_seal_number,
                'unit_plates'          => $request->unit_plates,
                'trailer_plates'       => $request->trailer_plates,
                'comments'             => $request->comments,
            ]);

            // 2. Create Inventory record with final weight as stock (in Kg)
            $inventory = \App\Models\Inventory::create([
                'stock'      => $finalWeight,
                'batch'      => $batch,
                'product_id' => $productId,
            ]);

            // 3. Create ProductInputs record with final weight (in Kg)
            \App\Models\ProductInputs::insert([
                'product_id'      => $productId,
                'input_id'        => $input->input_id,
                'quantity'        => $finalWeight,
                'warehouse_batch' => $batch,
            ]);

            // 4. Create CLI record (location, concept, sacks and net weight in Kg)
            \App\Models\Cli::create([
                'inventory_id'    => $inventory->inventory_id,
                'concept_id'      => $conceptId,
                'location_id'     => $request->location_id,
                'quantity'        => $sacks,
                'weight_per_unit' => $weightPerUnit,
                'net_weight'      => $finalWeight,
            ]);

            // 5. Mark pallet as Ingresada
            $pallet->inventory_status = 'Ingresada';
            $pallet->save();

            return response()->json(['message' => '¡Entrada registrada y tarima ingresada al inventario correctamente con ' . number_format($finalWeight, 2) . ' kg!']);
        });
    }
}