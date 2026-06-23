<?php
/*
controlador
locationscontroller
12/08/25
stefany
*/ 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
   
    public function index(Request $request)
    {
        $search = $request->get('search');
        $currentWarehouse = $request->get('warehouse_id');

        $warehouses = Warehouse::all();


        $locations = Location::with('warehouse')
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($currentWarehouse, function ($q, $currentWarehouse) {
                $q->where('warehouse_id', $currentWarehouse);
            })
            ->orderBy('name')
            ->paginate(10);

        
        $open = false;          
        $create = true;        
        $location = null;       

        return view('admin.locations', compact(
            'locations',
            'warehouses',
            'search',
            'currentWarehouse',
            'open',
            'create',
            'location'
        ));
    }

   
    public function create(Request $request)
    {
        $search = $request->get('search');
        $currentWarehouse = $request->get('warehouse_id');

        $warehouses = Warehouse::all();

        $locations = Location::with('warehouse')
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($currentWarehouse, function ($q, $currentWarehouse) {
                $q->where('warehouse_id', $currentWarehouse);
            })
            ->orderBy('name')
            ->paginate(10);

        $open = true;    
        $create = true;  
        $location = null;

        return view('admin.locations', compact(
            'locations',
            'warehouses',
            'search',
            'currentWarehouse',
            'open',
            'create',
            'location'
        ));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'allergen' => 'nullable|string|max:255',
        ]);

        Location::create($request->only(['name', 'warehouse_id', 'allergen']));

        return redirect()->route('admin.locations.index')->with('success', 'Location created successfully.');
    }

    
    public function edit($id, Request $request)
    {
        $location = Location::with('warehouse')->findOrFail($id);

        $search = $request->get('search');
        $currentWarehouse = $request->get('warehouse_id');
        $warehouses = Warehouse::all();

        $locations = Location::with('warehouse')
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($currentWarehouse, function ($q, $currentWarehouse) {
                $q->where('warehouse_id', $currentWarehouse);
            })
            ->orderBy('name')
            ->paginate(10);

        $open = true;     
        $create = false; 

        return view('admin.locations', compact(
            'locations',
            'warehouses',
            'search',
            'currentWarehouse',
            'open',
            'create',
            'location'
        ));
    }

   
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'allergen' => 'nullable|string|max:255',
        ]);

        $location = Location::findOrFail($id);
        $location->update($request->only(['name', 'warehouse_id', 'allergen']));

        return redirect()->route('admin.locations.index')->with('success', 'Location updated successfully.');
    }

    
    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Location deleted successfully.');
    }
}
