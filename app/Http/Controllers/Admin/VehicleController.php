<?php
/*
Controlador: VehicleController
13/08/25
stefany
*/

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Trailer;   
use App\Models\TransportLine;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
   public function index(Request $request)
{
    $transport_lines = TransportLine::all();

    $vehicles = Vehicle::with('transportLine')
        ->when($request->search, function($query) use ($request) {
            $query->where('plate', 'like', "%{$request->search}%");
        })
        ->paginate(10);

    $open = false;
    $create = false;
    $vehicle = null;

    return view('admin.vehicles', compact('transport_lines', 'vehicles', 'open', 'create', 'vehicle'));
}


    public function create()
    {
        $transport_lines = TransportLine::all();
        $vehicles = Vehicle::with('transportLine')->paginate(10);
        $open = true;
        $create = true;
        $vehicle = null;

        return view('admin.vehicles', compact('transport_lines', 'vehicles', 'open', 'create', 'vehicle'));
    }


    public function edit($id)
    {
        $transport_lines = TransportLine::all();
        $vehicles = Vehicle::with('transportLine')->paginate(10); // También traemos la tabla
        $vehicle = Vehicle::findOrFail($id);
        $open = true;
        $create = false;

        return view('admin.vehicles', compact('transport_lines', 'vehicles', 'open', 'create', 'vehicle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate' => 'required|string|max:255',
            'transport_line_id' => 'required|exists:transport_lines,transport_line_id',
            'unit_number' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        Vehicle::create($request->all());

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'plate' => 'required|string|max:255',
            'transport_line_id' => 'required|exists:transport_lines,transport_line_id',
            'unit_number' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update($request->all());

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
