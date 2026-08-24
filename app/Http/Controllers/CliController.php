<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\DatabaseErrors;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Requests\StoreCliRequest;
use App\Models\Cli;
use Illuminate\Http\Request;

class CliController extends Controller
{
    public function index(){
    }

    public function destroy($id){
        $cli = Cli::find($id);
        if($cli) {
            $cli->delete();
            return response()->json(['success' => true, 'message' => 'cli deleted']);
        } else {
            return response()->json(['success' => false, 'message' => 'cli not deleted'], 404);
        }
    }

    public function show($id){
        $cli = Cli::select(
            'cli.cli_id',
            'cli.location_id',
            'cli.concept_id',
            'cli.inventory_id',
            'cli.quantity',
            'cli.weight_per_unit',
            'cli.net_weight',
            'cli.bag_number',
            'cli.protein',
            'inventory.product_id',
            'locations.warehouse_id'
        )
        ->join('locations','locations.location_id','=','cli.location_id')
        ->join('inventory','inventory.inventory_id','=','cli.inventory_id')
        ->where('cli_id',$id)->first();
        return response()->json(['cli'=> $cli]);
    }

    public function update(Request $request){
        $data = [
            'location_id' => $request->location_id,
            'concept_id' => $request->concept_id,
            'inventory_id' => $request->inventory_id,
            'quantity' => $request->quantity,
            'weight_per_unit' => $request->weight_per_unit,
            'net_weight' => $request->quantity*$request->weight_per_unit,
            'bag_number' => $request->bag_number,
            'protein' => $request->protein,
        ];
        Cli::where('cli_id',$request->cli_id)->update($data);
        return response()->json(['message' => 'Operation successfuly make it'], 201);
    }

    public function store(StoreCliRequest $request){
        try{
            DB::transaction(function () use ($request){
                $concepts = $request->input('concept_id');
                $inventories = $request->input('inventory_id');
                $quantities = $request->input('quantity');
                $weights = $request->input('weight_per_unit');
                $bag_numbers = $request->input('bag_number', []);
                $proteins = $request->input('protein', []);

                foreach($concepts as $index => $concept){
                    if (isset($bag_numbers[$index]) && is_array($bag_numbers[$index]) && count($bag_numbers[$index]) > 0) {
                        foreach ($bag_numbers[$index] as $b_idx => $bag_num) {
                            Cli::create([
                                'location_id' => $request->location_id,
                                'inventory_id' => $inventories[$index],
                                'concept_id' => $concept,
                                'quantity' => 1,
                                'weight_per_unit' => $weights[$index],
                                'net_weight' => 1 * $weights[$index],
                                'bag_number' => $bag_num,
                                'protein' => $proteins[$index][$b_idx] ?? null,
                            ]);
                        }
                    } else {
                        Cli::create([
                            'location_id' => $request->location_id,
                            'inventory_id' => $inventories[$index],
                            'concept_id' => $concept,
                            'quantity' => $quantities[$index],
                            'weight_per_unit' => $weights[$index],
                            'net_weight' => $quantities[$index]*$weights[$index],
                        ]);
                    }
                }
                return response()->json(['message' => 'Operation successfuly make it'], 201);
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }
}