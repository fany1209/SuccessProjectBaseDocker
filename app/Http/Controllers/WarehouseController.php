<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Concept;
use App\Models\Control;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WarehouseController extends Controller{
 public function index(Request $request) 
    {
        $summary = DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select('products.name','products.unit',DB::raw('SUM(cli.net_weight) as net_weight'),'locations.warehouse_id')
            ->groupBy('products.name','locations.warehouse_id','products.unit')
            ->get();

        $available_locations = DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select('locations.name')->get();

        $concepts = Concept::all();
        $warehouses = Warehouse::all();
        $locations = Location::all();
        $products_inventory = Inventory::select('products.product_id','products.name','products.unit')->join('products','products.product_id','=','inventory.product_id')->distinct()->get();

        $inventory = DB::table('inventory')
            ->leftJoin('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->leftJoin('products', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'inventory.inventory_id',
                'products.product_id',
                'inventory.batch',
                'products.name',
                'inventory.stock',
                DB::raw('CAST(inventory.stock - COALESCE(SUM(cli.net_weight), 0) AS DECIMAL(10,3)) as stock_wh')
            )
            ->groupBy(
                'inventory.inventory_id',
                'products.product_id',
                'inventory.batch',
                'products.name',
                'inventory.stock'
            )
            ->get();

        $hours = ['09:00', '10:00', '16:00'];
        $selectedWarehouse = $request->input('warehouse_id'); 
        $feedback = [false, false, false]; 

        $timezone = config('app.timezone');

        if ($selectedWarehouse) {
            $controls = Control::where('warehouse_id', $selectedWarehouse)
                ->whereDate('created_at', Carbon::today($timezone))
                ->get();

            foreach ($hours as $index => $hour) {
                $hourInt = (int) explode(':', $hour)[0];
                
                $feedback[$index] = $controls->contains(function($c) use ($hourInt, $timezone) {
                    $controlDate = Carbon::parse($c->created_at)->timezone($timezone);
                    return (int)$controlDate->format('H') === $hourInt;
                });
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'feedback' => array_values($feedback) 
            ]);
        }

        $isFormActive = !empty($selectedWarehouse);

        return view('warehouse', compact(
            'concepts',
            'warehouses',
            'hours',
            'feedback',
            'selectedWarehouse',
            'isFormActive',
            'locations',
            'products_inventory',
            'inventory',
            'available_locations',
            'summary'
        ));
    }

    public function temperature(Request $request) 
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'measurements' => 'required|array',
            'measurements.*.hour' => 'required|string',
            'measurements.*.temperature' => 'required|numeric',
            'measurements.*.humidity' => 'required|numeric',
        ]);

        $timezone = config('app.timezone');

        foreach ($request->measurements as $measure) {
            
            $targetDateTime = Carbon::parse(Carbon::today($timezone)->toDateString() . ' ' . $measure['hour'], $timezone);

            $control = new Control();
            $control->temperature  = $measure['temperature'];
            $control->humidity     = $measure['humidity'];
            $control->warehouse_id = $request->warehouse_id;
            $control->user_id      = auth()->id();
            $control->host_ip      = $request->ip();
            $control->host_user    = gethostname();
            $control->host_name    = gethostbyaddr($request->ip());
            $control->created_at   = $targetDateTime;
            $control->updated_at   = now();
            
            $control->save(); 
        }

        return response()->json(['success' => true]);
    }
    
    public function getInfoLocation(Request $request){
        $location = $request->input('location');
        $products = DB::table('inventory')
        ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
        ->join('locations', 'locations.location_id', '=', 'cli.location_id')
        ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
        ->join('products', 'products.product_id', '=', 'inventory.product_id')
        ->select(
            'products.name as pName',
            'concepts.name as cName',
            'bag_number',
            'quantity',
            'weight_per_unit as wpu',
            'products.unit',
            'net_weight as total',
            'batch'
        )
        ->where('locations.name',$location)
        ->get();
        return response()->json(['products'=>$products]);
    }

    public function getWarehouse(Request $request){
        $concept = $request->input('concept');
        $search = $request->input('search');
        $query = DB::table('inventory')
        ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
        ->join('locations', 'locations.location_id', '=', 'cli.location_id')
        ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
        ->join('products', 'products.product_id', '=', 'inventory.product_id')
        ->select(
            'cli_id',
            'locations.name as lName',
            'products.name as pName',
            'concepts.name as cName',
            'bag_number',
            'quantity',
            DB::raw('concat(format(weight_per_unit,2)," ",products.unit) as weight_per_unit'),
            DB::raw('concat(format(net_weight,2)," ",products.unit) as net_weight'),
            'batch',
            'products.unit'
        );
        if(!empty($query)){
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', '%' . $search . '%')
                ->orWhere('locations.name', 'like', '%' . $search . '%')
                ->orWhere('concepts.name', 'like', '%' . $search . '%')
                ->orWhere('inventory.batch', 'like', '%' . $search . '%');
            });
        }
        if (!empty($concept)) {
            $query->where('concepts.concept_id', $concept);
        }
        $result = $query->get();
        return response()->json(['products'=>$result]);
    }
}
