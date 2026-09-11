<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsumoEntradaController extends Controller
{
    private function resolveSupplierName(array $validated): string
    {
        if (($validated['supplier_id'] ?? null) === '__other__') {

            $name = trim((string)($validated['supplier_name'] ?? ''));

            if ($name === '') {
                throw new \Exception('Nombre del proveedor inválido.');
            }

            $existing = DB::table('suppliers')
                ->whereRaw('LOWER(TRIM(name)) = LOWER(TRIM(?))', [$name])
                ->first();

            if ($existing) {
                return $existing->name;
            }

            $count = DB::table('suppliers')->count();
            $supplier_code = 'SP' . ($count + 1);

            DB::table('suppliers')->insert([
                'name'          => $name,
                'sector_id'     => $validated['sector_id'],
                'supplier_code' => $supplier_code,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            return $name;
        }

        $supplier = DB::table('suppliers')
            ->where('supplier_id', $validated['supplier_id'])
            ->first();

        if (!$supplier) {
            throw new \Exception('Proveedor no encontrado.');
        }

        return $supplier->name;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_llegada' => ['required','date'],
            'categoria'     => ['required','in:warehouse,purchases,laboratory'],
            'supplier_id'   => ['required'],
            'supplier_name' => ['required_if:supplier_id,__other__','nullable','string','max:255'],
            'sector_id'     => ['required_if:supplier_id,__other__','nullable','integer','exists:sectors,sector_id'],
            'descripcion'   => ['nullable','string','max:2000'],
            'cantidad'      => ['required','numeric','min:0'],
            'unidad'        => ['required','string','max:20'],
            'insumo'        => ['required','string','max:255'],
            'lote'          => ['nullable','string','max:255'],
        ]);

        DB::transaction(function () use ($validated) {

            $supplierName = $this->resolveSupplierName($validated);

            DB::table('insumos_entradas')->insert([
                'fecha_llegada' => $validated['fecha_llegada'],
                'categoria'     => $validated['categoria'],
                'proveedor'     => $supplierName,
                'descripcion'   => $validated['descripcion'] ?? null,
                'cantidad'      => $validated['cantidad'],
                'unidad'        => $validated['unidad'],
                'insumo'        => $validated['insumo'],
                'lote'          => $validated['lote'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        });

        return back()->with('success', 'Entrada guardada correctamente.');
    }

    public function show($id)
    {
        $entry = DB::table('insumos_entradas')->where('id', $id)->first();

        if (!$entry) {
            return response()->json(['error' => 'Entrada no encontrada.'], 404);
        }

        return response()->json(['entry' => $entry]);
    }

    public function update(Request $request, $id)
    {
        $entry = DB::table('insumos_entradas')->where('id', $id)->first();

        if (!$entry) {
            return response()->json(['error' => 'Entrada no encontrada.'], 404);
        }

        $validated = $request->validate([
            'fecha_llegada' => ['required','date'],
            'fecha_salida'  => ['nullable','date'],
            'categoria'     => ['required','in:warehouse,purchases,laboratory,quality,Human resources,finance'],
            'proveedor'     => ['required','string','max:255'],
            'descripcion'   => ['nullable','string','max:2000'],
            'cantidad'      => ['required','numeric','min:0'],
            'unidad'        => ['required','string','max:20'],
            'insumo'        => ['required','string','max:255'],
            'lote'          => ['nullable','string','max:255'],
            'lote_salida'   => ['nullable','string','max:255'],
        ]);

        DB::table('insumos_entradas')->where('id', $id)->update([
            'fecha_llegada' => $validated['fecha_llegada'],
            'fecha_salida'  => $validated['fecha_salida'] ?? null,
            'categoria'     => $validated['categoria'],
            'proveedor'     => $validated['proveedor'],
            'descripcion'   => $validated['descripcion'] ?? null,
            'cantidad'      => $validated['cantidad'],
            'unidad'        => $validated['unidad'],
            'insumo'        => $validated['insumo'],
            'lote'          => $validated['lote'] ?? null,
            'lote_salida'   => $validated['lote_salida'] ?? null,
            'updated_at'    => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Entrada actualizada correctamente.']);
    }

    public function destroy($id)
    {
        $entry = DB::table('insumos_entradas')->where('id', $id)->first();

        if (!$entry) {
            return response()->json(['error' => 'Entrada no encontrada.'], 404);
        }

        DB::table('insumos_entradas')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Entrada eliminada correctamente.']);
    }
}
