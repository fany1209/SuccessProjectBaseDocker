<?php
/*
Controlador
WarehouseController
15/08/25
Stefany
*/

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Control;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehousesController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->get('search');

        $warehouses = Warehouse::where('name', 'like', "%{$search}%")
            ->orderBy('name')
            ->paginate(15);

        $open = false;
        $create = true;
        $warehouse = null;

        $years = Control::select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year')->toArray();

        $weeks = range(1, 53);

        return view('admin.warehouses.warehouses', compact(
            'warehouses', 'search', 'open', 'create', 'warehouse','years','weeks'
        ));
    }

    public function create(Request $request)
    {
        $warehouses = Warehouse::orderBy('name')->paginate(15);
        $search = $request->get('search');

        $open    = true;     
        $create  = true;    
        $warehouse = null;    
        $open2   = false;     
        $modal_data = [];

        $years = Control::select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $weeks = range(1, 53);

        return view('admin.warehouses.warehouses', compact(
            'warehouses','search','open','create','warehouse','open2','modal_data','years','weeks' 
        ));
    }
        
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Warehouse::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse created successfully.');
    }

   
   public function edit($id, Request $request)
    {
        $warehouse   = Warehouse::findOrFail($id);
        $warehouses  = Warehouse::orderBy('name')->paginate(15);
        $search      = $request->get('search');
        $open        = true;     
        $create      = false;    
        $open2       = false;
        $modal_data  = [];

        $years = Control::select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $weeks = range(1, 53);

        return view('admin.warehouses.warehouses', compact(
            'warehouses',
            'search',
            'open',
            'create',
            'warehouse',
            'open2',
            'modal_data',
            'years',
            'weeks'   
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    
    public function destroy($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete();

        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse deleted successfully.');
    }

    // Modal Form
    public function modalForm(Warehouse $warehouse)
    {
        $now = now();

        
        $years = Control::where('warehouse_id', $warehouse->warehouse_id)
            ->select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        $selected_year = $years->first() ?? $now->format('Y');

        $weeks = range(1, 53);

        $current_week = $now->format('W');

        $modal_data = [
            'warehouse_id' => $warehouse->warehouse_id,
            'warehouse_name' => $warehouse->name,
            'current_week' => $current_week,
            'current_year' => $now->format('Y'),
            'week_a' => 1,              
            'week_b' => 33,             
            'year' => $selected_year,
        ];

        $warehouses = Warehouse::orderBy('name')->paginate(15);

        $open = false;   
        $open2 = true;   

        return view('admin.warehouses.warehouses', compact(
            'warehouses', 'open', 'open2', 'modal_data', 'years', 'weeks'
        ));
    }

    // Generate File
    public function generateFile(Request $request, Warehouse $warehouse)
    {
    
        $request->validate([
            'week_a' => 'required|integer|lte:week_b|min:1|max:53',
            'week_b' => 'required|integer|min:1|max:53',
            'year'   => 'required|integer|min:2000|max:2100'
        ], [
            'week_a.lte' => 'Week A must be less than or equal to Week B.',
            'week_a.required' => 'Week A is required.',
            'week_b.required' => 'Week B is required.',
            'year.required' => 'Year is required.'
        ]);

    
        $temp_rows = Control::selectRaw('WEEK(created_at,1) as week')
            ->where('warehouse_id', $warehouse->warehouse_id)
            ->whereYear('created_at', $request->year)
            ->whereBetween(DB::raw('WEEK(created_at,1)'), [$request->week_a, $request->week_b])
            ->distinct()
            ->pluck('week')
            ->toArray();

    
        if (empty($temp_rows)) {
            return back()->withErrors([
                'temp_rows' => 'No records were found in the database with the weeks and year reported'
            ])->withInput();
        }

    
        return redirect()->route('temperature-pdf', [
            'week_a' => $request->week_a,
            'week_b' => $request->week_b,
            'year' => $request->year,
            'warehouse_id' => $warehouse->warehouse_id
        ]);
    }

}