<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FertilProduction;
use App\Models\FertilInventory;
use App\Models\FertilInventoryMovement;

class FertilController extends Controller
{
    public function index()
    {
        $productions = FertilProduction::orderBy('fertil_production_id', 'desc')->get();
        $inventories = FertilInventory::orderBy('fertil_inventory_id', 'desc')->get();
        $db_products = \App\Models\Product::orderBy('name', 'asc')->get();

        return view('production.fertil.index', compact('productions', 'inventories', 'db_products'));
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

        FertilProduction::create($request->all());

        if ($request->ajax()) {
            return response()->json(['message' => 'Registro agregado correctamente.']);
        }
        return redirect()->route('production.fertil.index')->with('success', 'Registro de producción agregado correctamente.');
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

        $production = FertilProduction::findOrFail($id);
        $production->update($request->all());

        if ($request->ajax()) {
            return response()->json(['message' => 'Registro actualizado correctamente.']);
        }
        return redirect()->route('production.fertil.index')->with('success', 'Registro de producción actualizado correctamente.');
    }

    public function destroyProduction($id)
    {
        $production = FertilProduction::findOrFail($id);
        $production->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Registro eliminado correctamente.']);
        }
        return redirect()->route('production.fertil.index')->with('success', 'Registro de producción eliminado.');
    }

    public function storeInventory(Request $request)
    {
        $request->validate([
            'producto_descripcion' => 'required|string',
            'cantidad' => 'nullable|numeric|min:0',
            'unidad' => 'nullable|string',
            'stock_min' => 'nullable|numeric|min:0'
        ]);

        $inv = FertilInventory::create($request->all());

        if ($inv->cantidad > 0) {
            FertilInventoryMovement::create([
                'fertil_inventory_id' => $inv->fertil_inventory_id,
                'tipo' => 'Entrada',
                'cantidad' => $inv->cantidad
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Producto agregado correctamente.']);
        }
        return redirect()->route('production.fertil.index')->with('success', 'Registro de inventario agregado correctamente.');
    }

    public function updateInventory(Request $request, $id)
    {
        $request->validate([
            'producto_descripcion' => 'required|string',
            'cantidad' => 'nullable|numeric|min:0',
            'unidad' => 'nullable|string',
            'stock_min' => 'nullable|numeric|min:0'
        ]);

        $inventory = FertilInventory::findOrFail($id);
        $oldCantidad = $inventory->cantidad;
        $inventory->update($request->all());

        if ($inventory->cantidad != $oldCantidad) {
            $diff = $inventory->cantidad - $oldCantidad;
            FertilInventoryMovement::create([
                'fertil_inventory_id' => $inventory->fertil_inventory_id,
                'tipo' => $diff > 0 ? 'Entrada' : 'Ajuste',
                'cantidad' => abs($diff)
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Producto actualizado correctamente.']);
        }
        return redirect()->route('production.fertil.index')->with('success', 'Registro de inventario actualizado correctamente.');
    }

    public function destroyInventory($id)
    {
        $inventory = FertilInventory::findOrFail($id);
        $inventory->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Producto eliminado correctamente.']);
        }
        return redirect()->route('production.fertil.index')->with('success', 'Registro de inventario eliminado.');
    }

    public function outputInventory(Request $request, $id)
    {
        $request->validate([
            'cantidad_salida' => 'required|numeric|min:0.01'
        ]);

        $inventory = FertilInventory::findOrFail($id);
        
        if ($request->cantidad_salida > $inventory->cantidad) {
            return response()->json(['message' => 'La cantidad de salida no puede ser mayor a la cantidad en stock.'], 400);
        }

        $inventory->cantidad -= $request->cantidad_salida;
        $inventory->save();

        FertilInventoryMovement::create([
            'fertil_inventory_id' => $inventory->fertil_inventory_id,
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
        $movements = FertilInventoryMovement::where('fertil_inventory_id', $id)
            ->orderBy('movement_id', 'desc')
            ->get();
            
        return response()->json($movements);
    }

    public function storeMaterialRequest(Request $request)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:100',
            'comments' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.name' => 'required|string|max:255',
            'products.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            \DB::transaction(function () use ($request) {
                $materialRequest = \App\Models\ProductionMaterialRequest::create([
                    'area' => 'fertil',
                    'applicant_name' => $request->applicant_name,
                    'status' => 'Pendiente',
                    'comments' => $request->comments
                ]);

                foreach ($request->products as $prod) {
                    \App\Models\ProductionMaterialRequestItem::create([
                        'request_id' => $materialRequest->id,
                        'product_name' => $prod['name'],
                        'quantity' => $prod['quantity'],
                        'dispatched_quantity' => 0
                    ]);
                }
            });

            if ($request->ajax()) {
                return response()->json(['message' => 'Solicitud enviada a almacén correctamente.']);
            }
            return redirect()->back()->with('success', 'Solicitud enviada a almacén correctamente.');
        } catch (\Throwable $e) {
            \Log::error('Error storeMaterialRequest', ['error' => $e->getMessage()]);
            if ($request->ajax()) {
                return response()->json(['message' => 'Error al enviar la solicitud.'], 500);
            }
            return redirect()->back()->with('error', 'Error al enviar la solicitud.');
        }
    }
}
