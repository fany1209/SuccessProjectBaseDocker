<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProteaminProduction;
use App\Models\ProteaminInventory;
use App\Models\ProteaminInventoryMovement;

class ProteaminController extends Controller
{
    public function index()
    {
        $productions = ProteaminProduction::orderBy('proteamin_production_id', 'desc')->get();
        $inventories = ProteaminInventory::orderBy('proteamin_inventory_id', 'desc')->get();

        return view('production.proteamin.index', compact('productions', 'inventories'));
    }

    public function storeProduction(Request $request)
    {
        $request->validate([
            'fecha_preparacion' => 'nullable|date',
            'kg_preparados' => 'nullable|numeric|min:0',
            'fecha_ensacado' => 'nullable|date',
            'kg_ensacados' => 'nullable|numeric|min:0',
            'num_sacos' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string'
        ]);

        ProteaminProduction::create($request->all());

        if ($request->ajax()) {
            return response()->json(['message' => 'Registro agregado correctamente.']);
        }
        return redirect()->route('production.proteamin.index')->with('success', 'Registro de producción agregado correctamente.');
    }

    public function updateProduction(Request $request, $id)
    {
        $request->validate([
            'fecha_preparacion' => 'nullable|date',
            'kg_preparados' => 'nullable|numeric|min:0',
            'fecha_ensacado' => 'nullable|date',
            'kg_ensacados' => 'nullable|numeric|min:0',
            'num_sacos' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string'
        ]);

        $production = ProteaminProduction::findOrFail($id);
        $production->update($request->all());

        if ($request->ajax()) {
            return response()->json(['message' => 'Registro actualizado correctamente.']);
        }
        return redirect()->route('production.proteamin.index')->with('success', 'Registro de producción actualizado correctamente.');
    }

    public function destroyProduction($id)
    {
        $production = ProteaminProduction::findOrFail($id);
        $production->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Registro eliminado correctamente.']);
        }
        return redirect()->route('production.proteamin.index')->with('success', 'Registro de producción eliminado.');
    }

    public function storeInventory(Request $request)
    {
        $request->validate([
            'producto_descripcion' => 'required|string',
            'cantidad' => 'nullable|numeric|min:0',
            'unidad' => 'nullable|string',
            'stock_min' => 'nullable|numeric|min:0'
        ]);

        $inv = ProteaminInventory::create($request->all());

        if ($inv->cantidad > 0) {
            ProteaminInventoryMovement::create([
                'proteamin_inventory_id' => $inv->proteamin_inventory_id,
                'tipo' => 'Entrada',
                'cantidad' => $inv->cantidad
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Producto agregado correctamente.']);
        }
        return redirect()->route('production.proteamin.index')->with('success', 'Registro de inventario agregado correctamente.');
    }

    public function updateInventory(Request $request, $id)
    {
        $request->validate([
            'producto_descripcion' => 'required|string',
            'cantidad' => 'nullable|numeric|min:0',
            'unidad' => 'nullable|string',
            'stock_min' => 'nullable|numeric|min:0'
        ]);

        $inventory = ProteaminInventory::findOrFail($id);
        $oldCantidad = $inventory->cantidad;
        $inventory->update($request->all());

        if ($inventory->cantidad != $oldCantidad) {
            $diff = $inventory->cantidad - $oldCantidad;
            ProteaminInventoryMovement::create([
                'proteamin_inventory_id' => $inventory->proteamin_inventory_id,
                'tipo' => $diff > 0 ? 'Entrada' : 'Ajuste',
                'cantidad' => abs($diff)
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Producto actualizado correctamente.']);
        }
        return redirect()->route('production.proteamin.index')->with('success', 'Registro de inventario actualizado correctamente.');
    }

    public function destroyInventory($id)
    {
        $inventory = ProteaminInventory::findOrFail($id);
        $inventory->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Producto eliminado correctamente.']);
        }
        return redirect()->route('production.proteamin.index')->with('success', 'Registro de inventario eliminado.');
    }

    public function outputInventory(Request $request, $id)
    {
        $request->validate([
            'cantidad_salida' => 'required|numeric|min:0.01'
        ]);

        $inventory = ProteaminInventory::findOrFail($id);
        
        if ($request->cantidad_salida > $inventory->cantidad) {
            return response()->json(['message' => 'La cantidad de salida no puede ser mayor a la cantidad en stock.'], 400);
        }

        $inventory->cantidad -= $request->cantidad_salida;
        $inventory->save();

        ProteaminInventoryMovement::create([
            'proteamin_inventory_id' => $inventory->proteamin_inventory_id,
            'tipo' => 'Salida',
            'cantidad' => $request->cantidad_salida
        ]);

        $alert = false;
        $message = 'Salida registrada correctamente.';

        if ($inventory->cantidad <= $inventory->stock_min) {
            $alert = true;
            $message = 'Salida registrada. ALERTA: El producto ha alcanzado su stock mínimo, es necesario solicitar más.';
        }

        return response()->json([
            'message' => $message,
            'alert' => $alert
        ]);
    }

    public function getMovements($id)
    {
        $movements = ProteaminInventoryMovement::where('proteamin_inventory_id', $id)
            ->orderBy('movement_id', 'desc')
            ->get();
            
        return response()->json($movements);
    }
}
