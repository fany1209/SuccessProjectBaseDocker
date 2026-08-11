<?php

namespace App\Http\Controllers;

use App\Models\ItInspection;
use Illuminate\Http\Request;

class ItInspectionController extends Controller
{
    public function index(Request $request)
    {
        $query = ItInspection::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('folio', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
        }

        $inspections = $query->latest()->get();
        return view('sistemas-ti.inspecciones.index', compact('inspections'));
    }

    public function create()
    {
        $folio = ItInspection::generateFolio();
        $equipments = \App\Models\ItEquipment::orderBy('article')->get();
        return view('sistemas-ti.inspecciones.create', compact('folio', 'equipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'folio' => 'required|string|unique:it_inspections,folio',
            'date' => 'required|date',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'req1' => 'required|string|in:cumple,nocumple,na',
            'req2' => 'required|string|in:cumple,nocumple,na',
            'req3' => 'required|string|in:cumple,nocumple,na',
            'req4' => 'required|string|in:cumple,nocumple,na',
            'req5' => 'required|string|in:cumple,nocumple,na',
            'observations' => 'nullable|string',
        ]);

        ItInspection::create($validated);

        return redirect()->route('sistemas-ti.inspecciones.index')->with('success', 'Inspección guardada correctamente.');
    }

    public function print($id)
    {
        $inspection = ItInspection::findOrFail($id);
        return view('sistemas-ti.inspecciones.print', compact('inspection'));
    }
}
