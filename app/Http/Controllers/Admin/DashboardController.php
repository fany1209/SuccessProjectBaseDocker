<?php
/*
controlador
dasboardcontroller
11/08/25
stefany
*/ 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Input;
use App\Models\Output;
use App\Models\Tweak;
use App\Models\Cli;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index()

    {
        $products_high = Product::leftJoin('inventory', 'products.product_id', '=', 'inventory.product_id')
            ->select('products.product_id', 'products.name', 'products.unit', 'products.presentation', 'products.stock_max', DB::raw('COALESCE(sum(inventory.stock),0) as stock'))
            ->havingRaw('COALESCE(sum(inventory.stock),0) >= products.stock_max')
            ->groupBy('products.product_id', 'products.name', 'products.unit', 'products.presentation', 'products.stock_max')
            ->orderBy('stock', 'desc')
            ->get();

        $products_low = Product::leftJoin('inventory', 'products.product_id', '=', 'inventory.product_id')
            ->select('products.product_id', 'products.name', 'products.unit', 'products.presentation', 'products.stock_min', DB::raw('COALESCE(sum(inventory.stock),0) as stock'))
            ->havingRaw('COALESCE(sum(inventory.stock),0) <= products.stock_min')
            ->groupBy('products.product_id', 'products.name', 'products.unit', 'products.presentation', 'products.stock_min')
            ->orderBy('stock')
            ->get();

        $inventory_inputs = Input::take(6)->orderBy('created_at', 'desc')->get();
        $inventory_outputs = Output::take(6)->orderBy('created_at', 'desc')->get();

        $warehouse_movs = Cli::join('inventory', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select('cli.updated_at', 'cli.quantity', 'cli.weight_per_unit', 'cli.net_weight', 'products.name', 'products.unit', 'products.presentation', 'locations.name as lName')
            ->selectRaw("DATE_FORMAT(cli.updated_at,'%W, %d %M %Y, %H:%i') as date")
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $tweaks = Tweak::join('inventory', 'tweaks.inventory_id', '=', 'inventory.inventory_id')
            ->join('products', 'inventory.product_id', '=', 'products.product_id')
            ->select('type', 'quantity', 'batch', 'name', 'tweaks.created_at', 'unit')
            ->take(10)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('products_low', 'products_high', 'inventory_inputs', 'inventory_outputs', 'warehouse_movs', 'tweaks'));
    }
}
