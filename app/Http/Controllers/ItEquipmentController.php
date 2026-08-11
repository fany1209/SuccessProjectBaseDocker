<?php

namespace App\Http\Controllers;

use App\Models\ItEquipment;
use Illuminate\Http\Request;

class ItEquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = ItEquipment::query();

        // Optional filtering (can expand later)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('responsible', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('department') && $request->department != '') {
            $query->where('department', $request->department);
        }

        if ($request->has('article') && $request->article != '') {
            $query->where('article', $request->article);
        }

        $equipments = $query->latest()->get();

        return view('sistemas-ti.inventario', compact('equipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'responsible' => 'nullable|string|max:255',
            'article' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'success_code' => 'nullable|string|max:255',
            'image_url' => 'nullable|url|max:2048',
        ]);

        ItEquipment::create($validated);

        return redirect()->route('sistemas-ti.inventario')->with('success', 'Equipo registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $equipment = ItEquipment::findOrFail($id);
        
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'responsible' => 'nullable|string|max:255',
            'article' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'success_code' => 'nullable|string|max:255',
            'image_url' => 'nullable|url|max:2048',
        ]);

        $equipment->update($validated);

        return redirect()->route('sistemas-ti.inventario')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $equipment = ItEquipment::findOrFail($id);
        $equipment->delete();

        return redirect()->route('sistemas-ti.inventario')->with('success', 'Equipo eliminado correctamente.');
    }
}
