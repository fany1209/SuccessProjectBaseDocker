<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabSampleController extends Controller
{
    protected string $table = 'laboratory_samples'; 

    public function index()
    {
        return view('laboratory.samples.index');
    }

    public function datatable(Request $request)
    {
        $rows = DB::table($this->table . ' as ls')
            ->leftJoin(
                'reception_of_samples as ros',
                'ros.folio_muestra',
                '=',
                'ls.folio'
            )
            ->leftJoin(
                'suppliers as s',
                's.supplier_id',  
                '=',
                'ros.supplier_id'
            )
            ->select([
                'ls.id',
                'ls.folio',
                'ls.tipo_muestra',
                'ls.producto',
                'ls.sku',
                'ls.ubicacion_stock',
                'ls.stock_inicial',
                'ls.cantidad_salida',
                'ls.stock_final',
                'ls.fecha_entrada',
                'ls.fecha_salida',
                'ros.batch as lote',
                'ls.proveedor',
                'ls.status',
            ])
            ->orderByDesc('ls.fecha_entrada')
            ->orderByDesc('ls.id')
            ->get()
            ->map(function ($r) {

                $dash = fn($v) => (is_null($v) || $v === '') ? '—' : $v;
                $d    = fn($v) => $v
                    ? (is_string($v) ? $v : $v->format('Y-m-d'))
                    : '—';
                $num  = fn($v, $dec = 2) => is_null($v)
                    ? '—'
                    : number_format((float)$v, $dec, '.', '');

                return [
                    'id'             => $r->id,
                    'folio'          => $dash($r->folio),
                    'tipo_muestra'   => $dash($r->tipo_muestra),
                    'producto'       => $dash($r->producto),
                    'sku'            => $dash($r->sku),
                    'proveedor'      => $dash($r->proveedor),
                    'lote'           => $dash($r->lote),
                    'ubicacion'      => $dash($r->ubicacion_stock),
                    'stock_inicial'  => $num($r->stock_inicial),
                    'salida'         => $num($r->cantidad_salida),
                    'stock_final'    => $num($r->stock_final),
                    'fecha_entrada'  => $d($r->fecha_entrada),
                    'fecha_salida'   => $d($r->fecha_salida),
                    'status'         => $r->status ?? 'Fuera de laboratorio',
                    'acciones'       => '
                        <div class="flex gap-2">
                            <button class="px-2 py-1 rounded bg-emerald-600 text-white text-xs"
                                data-id="'.$r->id.'" data-action="ver">Ver</button>

                            <button class="px-2 py-1 rounded bg-amber-600 text-white text-xs"
                                data-id="'.$r->id.'" data-action="editar">Editar</button>

                            <button class="px-2 py-1 rounded bg-rose-600 text-white text-xs"
                                data-id="'.$r->id.'" data-action="eliminar">Eliminar</button>
                        </div>',
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function destroy($id)
    {
        $deleted = \DB::table('laboratory_samples')->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['message' => 'Deleted'], 200);
    }

    public function showLabSample($id)
    {
        $row = DB::table('laboratory_samples')->where('id', (int)$id)->first();

        if (!$row) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado.'
            ], 404);
        }

        $toYmd = function ($v) {
            if (!$v) return null;
            try { return \Carbon\Carbon::parse($v)->toDateString(); }
            catch (\Throwable $e) { return null; }
        };

        return response()->json([
            'success' => true,
            'data' => [
                'id'               => $row->id,
                'folio'            => $row->folio,
                'tipo_muestra'     => $row->tipo_muestra,
                'producto'         => $row->producto,
                'sku'              => $row->sku,
                'ubicacion_stock'  => $row->ubicacion_stock,
                'stock_inicial'    => $row->stock_inicial,
                'cantidad_salida'  => $row->cantidad_salida,
                'stock_final'      => $row->stock_final,
                'fecha_entrada'    => $row->fecha_entrada,
                'fecha_salida'     => $row->fecha_salida,
                'status'           => $row->status ?? 'Fuera de laboratorio',
                'fecha_entrada_raw'=> $toYmd($row->fecha_entrada),
                'fecha_salida_raw' => $toYmd($row->fecha_salida),
            ]
        ]);
    }

    public function updateLabSample(Request $request, $id)
    {
        $validated = $request->validate([
            'tipo_muestra'     => ['nullable','string','max:150'],
            'producto'         => ['nullable','string','max:255'],
            'sku'              => ['nullable','string','max:100'],
            'ubicacion_stock'  => ['nullable','string','max:255'],
            'fecha_entrada'    => ['nullable','date'],
            'fecha_salida'     => ['nullable','date'],
            'stock_inicial'    => ['nullable','numeric','min:0'],
            'cantidad_salida'  => ['nullable','numeric','min:0'],
            'status'           => ['nullable','string','max:50'],
        ]);

        unset($validated['stock_final']);
        unset($validated['folio']);

        $row = \App\Models\LaboratorySample::findOrFail($id);
        $row->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado.',
            'data' => $row->fresh(), 
        ]);
    }
}
