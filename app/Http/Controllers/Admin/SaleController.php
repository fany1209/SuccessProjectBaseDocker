<?php

namespace App\Http\Controllers\Admin;
/*
Controlador
SaleController
14/08/25
Stefany
*/
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Prospect;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    
public function index(Request $request)
{
    $customers_filter = $request->input('customers_filter', 'prospects'); // valor por defecto
    $search = $request->input('search');

    $query = Sale::query();

    if ($customers_filter == 'customers') {
        $query->leftJoin('customers', 'sales.customer_id', '=', 'customers.customer_id')
              ->select('sales.*', 'customers.name as name')
              ->where('customers.name', 'like', '%' . $search . '%');
    } else {
        $query->leftJoin('prospects', 'sales.prospect_id', '=', 'prospects.prospect_id')
              ->select('sales.*', 'prospects.name as name')
              ->where('prospects.name', 'like', '%' . $search . '%');
    }

    $sales = $query->when($request->seller, function($q) use ($request) {
                        $q->where('sales.seller', 'like', "%{$request->seller}%");
                   })
                   ->when($request->sale_type, function($q) use ($request) {
                        $q->where('sales.sale_type', 'like', "%{$request->sale_type}%");
                   })
                   ->when($request->date_from, function($q) use ($request) {
                        $q->whereDate('sales.date', '>=', $request->date_from);
                   })
                   ->when($request->date_to, function($q) use ($request) {
                        $q->whereDate('sales.date', '<=', $request->date_to);
                   })
                   ->orderBy('sales.created_at', 'desc')
                   ->paginate(10);

    $sales->appends($request->except('page'));

    return view('admin.sale', compact('sales', 'customers_filter', 'search'));
}




    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Venta eliminada correctamente.');
    }
}
