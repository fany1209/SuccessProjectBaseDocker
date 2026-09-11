<?php

namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Http\Controllers\Controller;
use App\Models\Cli;
use App\Models\Input;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Operator;
use App\Models\ProductInputs;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InputController extends Controller{
    public function show($id){
        $input = Input::select('inputs.supplier_id','security_seal','security_seal_number','inputs.transport_line_id','operator','license_number','unit_plates','trailer_plates','comments','suppliers.name as sName','transport_lines.name as tName')
            ->where('input_id',$id)
            ->leftJoin('suppliers','suppliers.supplier_id','=','inputs.supplier_id')
            ->leftJoin('transport_lines','transport_lines.transport_line_id','=','inputs.transport_line_id')
            ->first();
        $products = ProductInputs::where('input_id',$id)
            ->join('products','products.product_id','=','product_inputs.product_id')
            ->get();
        return response()->json(['input'=> $input,'products'=> $products]);
    }

    public function update(Request $request)
{
    try {

        $quanties = $request->input('quantity', []);
        if (is_array($quanties)) {
            $quanties = array_map(function ($q) {
                if (is_string($q)) {
                    $q = trim($q);
                    $q = str_replace(',', '.', $q);
                }
                return $q;
            }, $quanties);

            $request->merge(['quantity' => $quanties]);
        }

        $request->merge([
            'security_seal' => $request->filled('security_seal_number') ? 1 : 0
        ]);

        $request->validate([
            'operator' => 'nullable|string|max:200',
            'license_number' => 'nullable|string|max:50',
            'security_seal' => 'nullable|integer|in:0,1',
            'security_seal_number' => 'nullable|string|max:50',
            'unit_plates' => 'nullable|string|max:20',
            'trailer_plates' => 'nullable|string|max:20',
            'comments' => 'nullable|string|max:300',
            'supplier_id' => 'required|integer',
            'transport_line_id' => 'nullable|integer',
            'warehouse_batch' => 'required|array|min:1',
            'warehouse_batch.*' => 'required|string|max:50',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric|min:0.001', 
        ]);

        $products = [];
        $quantities = $request->input('quantity', []);

        foreach ($request->input('warehouse_batch', []) as $index => $warehouse_batch) {

            $warehouse_batch = is_string($warehouse_batch) ? trim($warehouse_batch) : $warehouse_batch;
            $qty = isset($quantities[$index]) ? (float) $quantities[$index] : 0;

            $product_id = Inventory::where('batch', $warehouse_batch)->value('product_id');

            $products[$index] = [
                'product_id' => $product_id,
                'quantity' => $qty,
                'warehouse_batch' => $warehouse_batch
            ];
        }

       
        $updateData = $request->only([
            'operator',
            'license_number',
            'security_seal',
            'security_seal_number',
            'unit_plates',
            'trailer_plates',
            'comments',
            'supplier_id',
            'transport_line_id'
        ]);

        DB::table('inputs')->where('input_id', $request->id)->update($updateData);

        foreach ($products as $product) {

            ProductInputs::where('input_id', $request->id)
                ->where('product_id', $product['product_id'])
                ->update([
                    'quantity' => $product['quantity']
                ]);

            Inventory::where('batch', $product['warehouse_batch'])
                ->update(['stock' => $product['quantity']]);
        }

        return response()->json(['message' => 'Operation successfuly make it'], 201);

    } catch (QueryException $e) {
        return DatabaseErrors::handle($e);
    }
}


    public function store(Request $request){
        try{
            DB::transaction(function () use ($request){
                $products = [];
                $platforms_data = [];
                $product_ids = $request->input('product_id');
                $stocks = $request->input('stock');
                $batchs = $request->input('warehouse_batch');
                $concepts = $request->input('concept_id');
                $platforms = $request->input('platforms');
                $weights_per_units = $request->input('weight_per_unit');
                $quantities = $request->input('quantity');
                $locations = $request->input('location_name');
                $transaction = [
                    'supplier_id' => $request->supplier_id,
                    'transport_line_id' => $request->transport_line_id,
                    'operator' => Operator::where('operator_id',$request->operator_id)->value('name'),
                    'license_number' => Operator::where('operator_id',$request->operator_id)->value('license'),
                    'security_seal' => $request->security_seal,
                    'security_seal_number' => $request->security_seal_number ?? null,
                    'unit_plates' => $request->unit_plates,
                    'trailer_plates' => $request->trailer_plates,
                    'comments' => $request->comments
                ];
                $input_id = Input::create($transaction);
                foreach($product_ids as $index => $product){
                    $products[$index] = [
                        'product_id' => $product,
                        'input_id' => $input_id->input_id,
                        'quantity' => $stocks[$index],
                        'warehouse_batch' => $batchs[$index],
                        'concept_id' => $concepts[$index],
                        'platforms' => $platforms[$index],
                    ];
                    $inventory_id = Inventory::create([
                        'stock' => $products[$index]['quantity'],
                        'batch' => $products[$index]['warehouse_batch'],
                        'product_id' => $products[$index]['product_id'],
                    ]);
                    ProductInputs::insert([
                        'product_id' => $products[$index]['product_id'],
                        'input_id' => $input_id->input_id,
                        'quantity'=> $products[$index]['quantity'],
                        'warehouse_batch' => $products[$index]['warehouse_batch'],
                    ]);
                    $products[$index]['inventory_id'] = $inventory_id->inventory_id;
                }
                foreach($locations as $index => $location){
                    $platforms_data[$index] = [
                        'weight_per_unit' => $weights_per_units[$index][0],
                        'quantity' => $quantities[$index][0],
                        'location_id' => Location::where('name',$location)->value('location_id')
                    ];
                }        
                foreach($products as $index => $product){
                    for($i=0;$i<$product['platforms'];$i++){
                        Cli::create([
                            'inventory_id' => $product['inventory_id'],
                            'concept_id' => $product['concept_id'],
                            'location_id' => $platforms_data[$i]['location_id'],
                            'quantity' => $platforms_data[$i]['quantity'],
                            'weight_per_unit' => $platforms_data[$i]['weight_per_unit'],
                            'net_weight' => $platforms_data[$i]['quantity']*$platforms_data[$i]['weight_per_unit']
                        ]);                
                    }
                    for($i=0;$i<$product['platforms'];$i++){
                        array_shift($platforms_data);
                    }
                }
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }
}
