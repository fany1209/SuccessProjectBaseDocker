<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Area;
use Illuminate\Http\Request;

class MaintenanceEquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::with('area')->get();
        $areas = Area::all();
        
        // If there are no areas, create a default one so the user isn't stuck.
        if($areas->count() == 0) {
            $defaultArea = Area::create(['name' => 'General', 'description' => 'Área General']);
            $areas->push($defaultArea);
        }
        
        return view('maintenance.equipment.index', compact('equipments', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:equipment',
            'name' => 'required',
            'area_id' => 'required|exists:areas,id'
        ]);

        Equipment::create([
            'code' => $request->code,
            'name' => $request->name,
            'area_id' => $request->area_id,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Equipo registrado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        
        $request->validate([
            'code' => 'required|unique:equipment,code,' . $equipment->id,
            'name' => 'required',
            'area_id' => 'required|exists:areas,id'
        ]);

        $equipment->update([
            'code' => $request->code,
            'name' => $request->name,
            'area_id' => $request->area_id,
            'is_active' => $request->has('is_active')
        ]);

        return back()->with('success', 'Equipo actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();
        return back()->with('success', 'Equipo eliminado exitosamente.');
    }
}
