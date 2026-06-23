<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierPrice; 
use Illuminate\Support\Facades\Log;

class SupplierPriceController extends Controller
{
    
    public function index()
    {
        $todosLosPrecios = SupplierPrice::orderBy('id', 'desc')->get();
        $insumos = SupplierPrice::distinct()->orderBy('insumo', 'asc')->pluck('insumo');
        
        return view('finance.precios.index', compact('todosLosPrecios', 'insumos'));
    }

    public function grafica(Request $request)
    {
        if ($request->ajax()) {
            $insumo = $request->insumo;

            if (!$insumo) {
                return response()->json(['data' => [], 'analisis' => null]);
            }

            $query = SupplierPrice::where('insumo', $insumo);

            if ($request->filled('fecha_inicio')) {
                $query->whereDate('fecha_cotizacion', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $query->whereDate('fecha_cotizacion', '<=', $request->fecha_fin);
            }

            $datos = $query->orderBy('fecha_cotizacion', 'asc')->get();

            if ($datos->isEmpty()) {
                return response()->json(['data' => [], 'analisis' => null]);
            }

            $masBarato = $datos->sortBy('precio')->first(); 
            $promedio = $datos->avg('precio');
            $ultimoPrecio = $datos->last()->precio;
            $precioAnterior = $datos->count() > 1 ? $datos[$datos->count() - 2]->precio : $ultimoPrecio;
            
            $tendencia = 'Estable';
            if ($ultimoPrecio > $precioAnterior) $tendencia = 'Alza';
            if ($ultimoPrecio < $precioAnterior) $tendencia = 'Baja';

            $formateados = $datos->map(fn($item) => [
                'fecha'     => \Carbon\Carbon::parse($item->fecha_cotizacion)->format('d/m/Y'),
                'precio'    => (float)$item->precio,
                'moneda'    => $item->moneda,
                'proveedor' => $item->proveedor
            ]);

            return response()->json([
                'data' => $formateados,
                'analisis' => [
                    'mejor_proveedor'  => $masBarato->proveedor,
                    'mejor_precio'     => number_format($masBarato->precio, 2),
                    'moneda'           => $masBarato->moneda,
                    'ahorro_potencial' => number_format($promedio - $masBarato->precio, 2),
                    'tendencia'        => $tendencia,
                    'total_registros'  => $datos->count()
                ]
            ]);
        }

        $insumos = SupplierPrice::distinct()->orderBy('insumo', 'asc')->pluck('insumo');
        return view('finance.precios.grafica', compact('insumos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'insumo'           => 'required|string|max:255',
            'clave_sat'        => 'nullable|string|max:20',
            'proveedor'        => 'required|string|max:255',
            'precio'           => 'required|numeric|min:0',
            'tiene_iva'        => 'nullable',
            'moneda'           => 'nullable|string|max:3',
            'fecha_cotizacion' => 'required|date',
        ]);

        try {
            SupplierPrice::create([
                'insumo'           => $request->insumo,
                'clave_sat'        => $request->clave_sat,
                'proveedor'        => $request->proveedor,
                'precio'           => $request->precio,
                'tiene_iva'        => $request->has('tiene_iva') ? 1 : 0,
                'moneda'           => $request->moneda ?? 'MXN',
                'fecha_cotizacion' => $request->fecha_cotizacion,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'El precio ha sido guardado exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error("Error al guardar precio de proveedor: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al guardar el precio: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'insumo'           => 'required|string|max:255',
            'clave_sat'        => 'nullable|string|max:20',
            'proveedor'        => 'required|string|max:255',
            'precio'           => 'required|numeric|min:0',
            'tiene_iva'        => 'nullable',
            'moneda'           => 'nullable|string|max:3',
            'fecha_cotizacion' => 'required|date',
        ]);

        try {
            $precioDB = SupplierPrice::findOrFail($id);
            
            $precioDB->update([
                'insumo'           => $request->insumo,
                'clave_sat'        => $request->clave_sat,
                'proveedor'        => $request->proveedor,
                'precio'           => $request->precio,
                'tiene_iva'        => $request->has('tiene_iva') ? 1 : 0,
                'moneda'           => $request->moneda ?? 'MXN',
                'fecha_cotizacion' => $request->fecha_cotizacion,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'El precio ha sido actualizado correctamente.'
            ]);

        } catch (\Exception $e) {
            Log::error("Error al actualizar precio de proveedor: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $precio = SupplierPrice::findOrFail($id);
            $precio->delete();

            return response()->json([
                'success' => true,
                'message' => 'El registro ha sido eliminado correctamente.'
            ]);

        } catch (\Exception $e) {
            Log::error("Error al eliminar precio: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el registro.'
            ], 500);
        }
    }
}