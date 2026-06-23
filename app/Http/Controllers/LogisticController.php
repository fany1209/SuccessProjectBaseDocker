<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Concept;
use App\Models\Operator;
use App\Models\Quarantine;
use App\Models\Trailer;
use App\Models\TransportLine;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticController extends Controller
{
    public function index(){
        $tLines = TransportLine::all();
        $count_operators = Operator::count();
        $count_tLines = TransportLine::count();
        $count_vehicles = Vehicle::count();
        $count_trailers = Trailer::count();
        $warehouses = Warehouse::all();
        $concepts = Concept::all();
        $quarantine = Quarantine::all();
        $transport_lines = TransportLine::all();
        $products_all = DB::table('products')->select('product_id','name')->get();
        $products = DB::table('inventory')->leftJoin('products','products.product_id','=','inventory.product_id')->select('products.product_id','products.name')->orderBy('products.product_id')->distinct()->get();
        $suppliers = DB::table('suppliers')->select('supplier_id','name')->get();
        $customers = DB::table('customers')->select('customer_id','name')->get();
        return view('logistic',compact('tLines','count_operators','count_tLines','count_vehicles','count_trailers','warehouses','concepts','products','suppliers','customers','products_all','transport_lines','quarantine'));
    }

    public function charts(){
        $vehicles_per_tl = DB::table('transport_lines')->join('vehicles','vehicles.transport_line_id','=','transport_lines.transport_line_id')
            ->select(
                'transport_lines.name',
                DB::raw('count(*) as vehicles')
                )
            ->groupBy('transport_lines.name')->get();
        $trailers_per_tl = DB::table('transport_lines')->join('trailers','trailers.transport_line_id','=','transport_lines.transport_line_id')
            ->select(
                'transport_lines.name',
                DB::raw('count(*) as trailers')
                )
            ->groupBy('transport_lines.name')->get();
        return response()->json(['vehicles_per_tl'=>$vehicles_per_tl,'trailers_per_tl'=>$trailers_per_tl]);
    }
}
