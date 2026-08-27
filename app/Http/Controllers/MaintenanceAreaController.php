<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class MaintenanceAreaController extends Controller
{
    public function index()
    {
        $areas = Area::withCount('equipment')->get();
        return view('maintenance.areas.index', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:areas,name',
            'description' => 'nullable|string'
        ]);

        Area::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return back()->with('success', 'Categoría/Área registrada exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);
        
        $request->validate([
            'name' => 'required|unique:areas,name,' . $area->id,
            'description' => 'nullable|string'
        ]);

        $area->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return back()->with('success', 'Categoría/Área actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        
        if($area->equipment()->count() > 0) {
            return back()->withErrors(['No se puede eliminar esta categoría porque tiene equipos asignados.']);
        }
        
        $area->delete();
        return back()->with('success', 'Categoría/Área eliminada exitosamente.');
    }
}
