<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceptionOfSample;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReceptionController extends Controller
{
    public function datatable(Request $request)
    {
        $rows = DB::table('reception_of_samples as r')
            ->leftJoin('products as p', 'p.product_id', '=', 'r.product_id')
            ->orderByDesc('r.fecha_entrada')
            ->limit(500)
            ->select(
                'r.id',
                'r.batch',
                'r.fecha_entrada',
                DB::raw('COALESCE(p.name, r.nombre_comercial) as product'),
                DB::raw('COALESCE(r.estatus, 0) as estatus')
            )
            ->get();

        $data = $rows->map(function ($r) {
            return [
                'id'       => $r->id,
                'product'  => $r->product ?? '—',
                'batch'    => $r->batch ?? '—',
                'entry_at' => $r->fecha_entrada ? Carbon::parse($r->fecha_entrada)->format('Y-m-d') : '',
                'estatus'  => (int) $r->estatus,
                'status'   => (int)$r->estatus === 1 ? 'TERMINADO' : 'PENDIENTE',
            ];
        });

        return response()->json(['data' => $data], 200);
    }

    public function show($id)
    {
        $rec = ReceptionOfSample::findOrFail($id);

        $prod = !empty($rec->product_id) 
            ? DB::table('products')->where('product_id', $rec->product_id)->first() 
            : null;

        $supplierId = $rec->supplier_id ?? ($rec->product_id ? $this->resolveSupplierIdForProduct((int)$rec->product_id) : null);
        $supplierName = $supplierId ? DB::table('suppliers')->where('supplier_id', $supplierId)->value('name') : null;

        $batches = DB::table('inventory')
            ->where('product_id', $rec->product_id)
            ->whereNotNull('batch')
            ->whereRaw("TRIM(batch) <> ''")
            ->distinct()
            ->pluck('batch');

        return response()->json([
            'recepcion' => [
                'id'                        => $rec->id,
                'folio_muestra'             => $rec->folio_muestra,
                'product_id'                => $rec->product_id,
                'product_name'              => $prod->name ?? '',
                'sku'                       => $rec->sku ?? ($prod->sku ?? ''),
                'batch'                     => $rec->batch,
                'batches'                   => $batches,
                'nombre_comercial'          => $rec->nombre_comercial,
                'fecha_entrada'             => $rec->fecha_entrada ? Carbon::parse($rec->fecha_entrada)->format('Y-m-d') : '',
                'fecha_caducidad'           => $rec->fecha_caducidad ? Carbon::parse($rec->fecha_caducidad)->format('Y-m-d') : '',
                'descripcion'               => $rec->descripcion,
                'supplier_id'               => $supplierId,
                'supplier_name'             => $supplierName,
                'origen_muestra'            => $rec->origen_muestra,
                'origen_otro'               => $rec->origen_otro,
                
                // Enviamos el string para que el JS lo procese
                'objetivo_muestra'          => $rec->objetivo_muestra ?? '', 
                
                'objetivo_otro'             => $rec->objetivo_otro,
                'cantidad'                  => $rec->cantidad,
                'um'                        => $rec->um,
                'um_otro'                   => $rec->um_otro,
                'docs_ccf'                  => (bool)$rec->docs_ccf,
                'docs_ft'                   => (bool)$rec->docs_ft,
                'docs_hs'                   => (bool)$rec->docs_hs,
                'docs_otro'                 => (bool)$rec->docs_otro,
                'docs_otro_txt'             => $rec->docs_otro_txt,
                'observaciones_laboratorio' => $rec->observaciones_laboratorio,
                'firma_entrega_nombre'      => $rec->firma_entrega_nombre,
                'firma_recepcion_nombre'    => $rec->firma_recepcion_nombre,
            ]
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'folio_muestra'             => ['nullable','string','max:50'],
                'product_id'                => ['nullable','integer','exists:products,product_id'],
                'producto'                  => ['nullable','string','max:255'],
                'nombre_comercial'          => ['nullable','string','max:255'],
                'sku'                       => ['nullable','string','max:100'],
                'batch'                     => ['nullable','string','max:100'],
                'fecha_entrada'             => ['nullable','date'],
                'fecha_caducidad'           => ['nullable','date'],
                'descripcion'               => ['nullable','string','max:2000'],
                'origen_muestra'            => ['nullable','in:proveedor,produccion,almacen,otro'],
                'origen_otro'               => ['nullable','string','max:255'],
                'objetivo_muestra'          => ['nullable','array'],
                'objetivo_muestra.*'        => ['in:inspeccion,retencion,analisis,desarrollo,exposicion,otro'],
                'objetivo_otro'             => ['nullable','string','max:255'],
                'cantidad'                  => ['nullable','numeric'],
                'um'                        => ['nullable','in:g,kg,l,ml,otro'],
                'um_otro'                   => ['nullable','string','max:50'],
                'supplier_id'               => ['nullable','integer','exists:suppliers,supplier_id'],
                'proveedor'                 => ['nullable','string','max:255'],
                'observaciones_laboratorio' => ['nullable','string','max:5000'],
                'firma_entrega_nombre'      => ['nullable','string','max:255'],
                'firma_recepcion_nombre'    => ['nullable','string','max:255'],
            ]);

            $objArr = $request->input('objetivo_muestra', []);
            $objStr = is_array($objArr) ? implode(', ', $objArr) : '';

            if (($validated['origen_muestra'] ?? null) !== 'otro') $validated['origen_otro'] = null;
            if (!in_array('otro', $objArr)) $validated['objetivo_otro'] = null;
            if (($validated['um'] ?? null) !== 'otro') $validated['um_otro'] = null;

            $validated['folio_muestra'] = $validated['folio_muestra'] ?? ('RM-' . now()->format('Ymd-His'));
            
            $productId = $validated['product_id'] ?? null;
            if (!$productId && !empty($request->producto)) {
                $productName = trim($request->producto);
                $product = \App\Models\Product::where('name', $productName)->first();
                if (!$product) {
                    $lastProduct = \App\Models\Product::orderBy('product_id', 'desc')->first();
                    $nextId = $lastProduct ? $lastProduct->product_id + 1 : 1;
                    $sku = 'LAB' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                    
                    $product = \App\Models\Product::create([
                        'name' => $productName,
                        'sku' => $validated['sku'] ?: $sku,
                        'sat_code' => '01010101',
                        'category_id' => 1,
                        'is_public' => 1
                    ]);
                }
                $productId = $product->product_id;
                $validated['sku'] = $validated['sku'] ?: $product->sku;
            }
            if (!$productId) {
                return response()->json(['message' => 'El producto es requerido.', 'errors' => ['producto' => ['El producto es requerido.']]], 422);
            }
            $validated['product_id'] = $productId;
            unset($validated['producto']);

            $supplierId = $validated['supplier_id'] ?? null;
            if (!$supplierId && !empty($request->proveedor)) {
                $supplierId = \App\Models\Supplier::where('name', $request->proveedor)->value('supplier_id');
                if (!$supplierId) {
                    $s = \App\Models\Supplier::create([
                        'name' => $request->proveedor,
                        'supplier_code' => 'SP'.str_pad((string)(\App\Models\Supplier::max('supplier_id')+1), 5, '0', STR_PAD_LEFT),
                    ]);
                    $supplierId = $s->supplier_id;
                }
            }
            $validated['supplier_id'] = $supplierId;
            unset($validated['proveedor']);

            $record = ReceptionOfSample::create([
                ...$validated,
                'objetivo_muestra' => $objStr,
                'supplier_id'      => $supplierId,
                'docs_ccf'         => $request->boolean('docs_ccf'),
                'docs_ft'          => $request->boolean('docs_ft'),
                'docs_hs'          => $request->boolean('docs_hs'),
                'docs_otro'        => $request->boolean('docs_otro'),
                'docs_otro_txt'    => $request->docs_otro_txt ?? '',
            ]);

            return response()->json(['message' => 'Guardado correctamente', 'id' => $record->id]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $rec = ReceptionOfSample::findOrFail($id);

        $validated = $request->validate([
            'folio_muestra'             => ['nullable', 'string', 'max:100'],
            'product_id'                => ['nullable', 'integer', 'exists:products,product_id'],
            'sku'                       => ['nullable', 'string', 'max:100'],
            'batch'                     => ['nullable', 'string', 'max:100'],
            'nombre_comercial'          => ['nullable', 'string', 'max:255'],
            'fecha_entrada'             => ['nullable', 'date'],
            'fecha_caducidad'           => ['nullable', 'date'],
            'descripcion'               => ['nullable', 'string'],
            'supplier_id'               => ['nullable', 'integer', 'exists:suppliers,supplier_id'],
            'origen_muestra'            => ['nullable', 'string', 'in:proveedor,produccion,almacen,otro'],
            'origen_otro'               => ['nullable', 'string', 'max:255'],
            'objetivo_muestra'          => ['nullable', 'array'],
            'objetivo_muestra.*'        => ['in:inspeccion,retencion,analisis,desarrollo,exposicion,otro'],
            'objetivo_otro'             => ['nullable', 'string', 'max:255'],
            'cantidad'                  => ['nullable', 'numeric'],
            'um'                        => ['nullable', 'string', 'in:g,kg,l,ml,otro'],
            'um_otro'                   => ['nullable', 'string', 'max:50'],
            'docs_ccf'                  => ['nullable', 'boolean'],
            'docs_ft'                   => ['nullable', 'boolean'],
            'docs_hs'                   => ['nullable', 'boolean'],
            'docs_otro'                 => ['nullable', 'boolean'],
            'docs_otro_txt'             => ['nullable', 'string', 'max:255'],
            'observaciones_laboratorio' => ['nullable', 'string'],
            'firma_entrega_nombre'      => ['nullable', 'string', 'max:150'],
            'firma_recepcion_nombre'    => ['nullable', 'string', 'max:150'],
        ]);

        $objetivosArray = $request->input('objetivo_muestra', []);
        $objetivoString = is_array($objetivosArray) ? implode(', ', $objetivosArray) : '';

        if (($validated['origen_muestra'] ?? null) !== 'otro') $validated['origen_otro'] = null;
        if (!in_array('otro', $objetivosArray)) $validated['objetivo_otro'] = null;
        if (($validated['um'] ?? null) !== 'otro') $validated['um_otro'] = null;

        foreach (['docs_ccf', 'docs_ft', 'docs_hs', 'docs_otro'] as $cb) {
            $validated[$cb] = (int) $request->boolean($cb);
        }
        
        if (!$validated['docs_otro']) $validated['docs_otro_txt'] = null;

        $rec->fill($validated);
        $rec->objetivo_muestra = $objetivoString; 
        $rec->save();

        return response()->json(['message' => 'Cambios guardados correctamente'], 200);
    }

    public function updateq(Request $request, $id)
    {
        $rec = ReceptionOfSample::findOrFail($id);
        
        $objArr = $request->input('objetivo_muestra', []);
        $objStr = is_array($objArr) ? implode(', ', $objArr) : '';

        $rec->fill($request->all());
        $rec->objetivo_muestra = $objStr;
        $rec->docs_ccf  = (int) $request->boolean('docs_ccf');
        $rec->docs_ft   = (int) $request->boolean('docs_ft');
        $rec->docs_hs   = (int) $request->boolean('docs_hs');
        $rec->docs_otro = (int) $request->boolean('docs_otro');
        $rec->estatus = 1;
        $rec->save();

        return response()->json(['message' => 'Calidad actualizada correctamente']);
    }

    public function pdfShow($id)
    {
        $rec = ReceptionOfSample::findOrFail($id);
        $productName = DB::table('products')->where('product_id', $rec->product_id)->value('name');
        $supplierName = $rec->supplier_id ? Supplier::where('supplier_id', $rec->supplier_id)->value('name') : '';

        $data = $rec->toArray();
        $data['producto']  = $productName ?? '';
        $data['proveedor'] = $supplierName ?? '';

        $pdf = Pdf::loadView('formats.laboratory.01', $data)->setPaper('letter');
        return $pdf->download('RecepcionMuestras_'.$rec->folio_muestra.'.pdf');
    }

    public function destroy($id)
    {
        $record = ReceptionOfSample::findOrFail($id);
        $record->delete();
        return response()->json(['message' => 'Eliminado correctamente.']);
    }

    private function resolveSupplierIdForProduct(int $productId): ?int
    {
        if (Schema::hasTable('inventory')) {
            $sid = DB::table('inventory')->where('product_id', $productId)->whereNotNull('supplier_id')->value('supplier_id');
            if ($sid) return (int)$sid;
        }
        return null;
    }

    public function checkPendingQuality()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('Quality')) {
            return response()->json(['pending' => []]);
        }
        $pending = ReceptionOfSample::where('estatus', 0)->get();
        return response()->json(['pending' => $pending]);
    }
}