<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FacturaController extends Controller
{
    public function index()
    {
        return view('finance.facturas.index');
    }

    public function datatable()
    {
        $facturas = DB::table('facturas as f')
            ->leftJoin('factura_detalles as d', 'd.factura_id', '=', 'f.factura_id')
            ->select(
                'f.factura_id',
                'f.tipo_documento',
                'f.insumo',
                'f.empresa',
                'f.folio_factura',
                'f.departamento',
                DB::raw('COALESCE(NULLIF(f.subtotal, 0), SUM(d.precio)) as subtotal'),
                'f.iva',
                'f.total',
                DB::raw('GROUP_CONCAT(d.producto SEPARATOR ", ") as productos')
            )
            ->groupBy(
                'f.factura_id',
                'f.tipo_documento', 
                'f.insumo',
                'f.empresa',
                'f.departamento',
                'f.folio_factura',
                'f.subtotal', 
                'f.iva',
                'f.total'
            )
            ->orderByDesc('f.factura_id')
            ->get();

        return response()->json([
            'data' => $facturas
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_documento' => 'required|in:factura,nota_venta',
            'insumo'         => 'required|in:directo,indirecto', 
            'empresa'        => 'required|string|max:150',
            'folio_factura'  => 'required|string|max:100|unique:facturas,folio_factura',
            'fecha_factura'  => 'required|date',
            'moneda'         => 'required|in:MXN,USD',
            'tipo_cambio'    => 'required_if:moneda,USD|numeric|min:0.01',
            'departamento'   => 'nullable|string|max:255',
            'descripcion'    => 'nullable|string|max:1000',
            'productos'                   => 'required|array|min:1',
            'productos.*.producto'        => 'required|string|max:255',
            'productos.*.cantidad'        => 'required|numeric|min:0.01',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.aplica_iva'      => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $subtotalGlobal  = 0;
            $descuentoGlobal = 0;
            $ivaGlobal       = 0;
            $trasladoGlobal  = 0; 
            $retencionGlobal = 0; 

            $detallesAInsertar = [];

            foreach ($request->productos as $p) {
                $importeBase = $p['cantidad'] * $p['precio_unitario'];
                $descuento   = $p['descuento'] ?? 0;
                $baseImpuestos = $importeBase - $descuento;

                $ivaCalc = $p['aplica_iva'] ? ($baseImpuestos * (($p['iva_porcentaje'] ?? 0) / 100)) : 0;
                $otroCalc = !empty($p['otro_impuesto']) ? ($baseImpuestos * ($p['otro_impuesto'] / 100)) : 0;

                $subtotalGlobal  += $importeBase;
                $descuentoGlobal += $descuento;
                $ivaGlobal       += ($ivaCalc + $otroCalc);
                $trasladoGlobal  += (($p['traslado'] ?? 0) + ($p['ilc'] ?? 0));
                $retencionGlobal += (($p['retencion'] ?? 0) + ($p['isr'] ?? 0));

                $detallesAInsertar[] = [
                    'producto'        => $p['producto'],
                    'clave_sat'       => $p['clave_sat'] ?? null,
                    'unidad'          => $p['unidad'] ?? null,
                    'cantidad'        => $p['cantidad'],
                    'precio_unitario' => $p['precio_unitario'],
                    'precio'          => $importeBase, 
                    'subtotal'        => $baseImpuestos,
                    'descuento'       => $descuento,
                    'aplica_iva'      => $p['aplica_iva'],
                    'iva_porcentaje'  => $p['aplica_iva'] ? ($p['iva_porcentaje'] ?? 0) : 0,
                    'otro_impuesto_porcentaje' => $p['otro_impuesto'] ?? 0,
                    'impuesto_total'  => round($ivaCalc + $otroCalc, 5),
                    'traslado'        => $p['traslado'] ?? 0,
                    'ilc'             => $p['ilc'] ?? 0,
                    'retencion'       => $p['retencion'] ?? 0,
                    'isr'             => $p['isr'] ?? 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            $granTotal = $subtotalGlobal - $descuentoGlobal + $ivaGlobal + $trasladoGlobal - $retencionGlobal;

            $facturaId = DB::table('facturas')->insertGetId([
                'tipo_documento'  => $request->tipo_documento, 
                'insumo'          => $request->insumo, 
                'empresa'         => $request->empresa,
                'folio_factura'   => $request->folio_factura,
                'fecha_factura'   => $request->fecha_factura,
                'moneda'          => $request->moneda,
                'tipo_cambio'     => $request->moneda === 'USD' ? $request->tipo_cambio : 1.0,
                'departamento'    => $request->departamento,
                'descripcion'     => $request->descripcion,
                'subtotal'        => round($subtotalGlobal, 5),
                'descuento_total' => round($descuentoGlobal, 5),
                'iva'             => round($ivaGlobal, 5),
                'traslado_total'  => round($trasladoGlobal, 5),
                'retencion_total' => round($retencionGlobal, 5),
                'total'           => round($granTotal, 5),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            foreach ($detallesAInsertar as &$detalle) {
                $detalle['factura_id'] = $facturaId;
            }
            DB::table('factura_detalles')->insert($detallesAInsertar);

            // Crear registro automático en Cuentas por Pagar (cxp_details)
            DB::table('cxp_details')->insert([
                'factura_id'  => $facturaId,
                'fecha_pago'  => null,
                'semana'      => \Carbon\Carbon::parse($request->fecha_factura)->weekOfYear ?? \Carbon\Carbon::now()->weekOfYear,
                'anio'        => \Carbon\Carbon::parse($request->fecha_factura)->year ?? \Carbon\Carbon::now()->year,
                'estatus'     => 'PENDIENTE',
                'is_canceled' => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Documento guardado correctamente'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el documento',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $factura = DB::table('facturas')->where('factura_id', $id)->first();

        if (!$factura) {
            return response()->json(['success' => false, 'message' => 'Factura no encontrada'], 404);
        }

        $detalles = DB::table('factura_detalles')
            ->where('factura_id', $id)
            ->get()
            ->map(function ($d) {
                return [
                    'producto'        => $d->producto,
                    'clave_sat'       => $d->clave_sat,
                    'unidad'          => $d->unidad,
                    'cantidad'        => $d->cantidad,
                    'precio_unitario' => $d->precio_unitario,
                    'precio'          => $d->precio, 
                    'descuento'       => $d->descuento, 
                    'iva_porcentaje'  => $d->iva_porcentaje,
                    'otro_impuesto'   => $d->otro_impuesto_porcentaje, 
                    'traslado'        => $d->traslado, 
                    'ilc'             => $d->ilc,       
                    'retencion'       => $d->retencion, 
                    'isr'             => $d->isr,       
                ];
            });

        return response()->json([
            'success'  => true,
            'factura'  => $factura,
            'detalles' => $detalles
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipo_documento' => 'required|in:factura,nota_venta',
            'insumo'         => 'required|in:directo,indirecto', 
            'empresa'        => 'required|string|max:150',
            'folio_factura'  => [
                'required',
                'string',
                'max:100',
                Rule::unique('facturas', 'folio_factura')->ignore($id, 'factura_id'),
            ],
            'fecha_factura'  => 'required|date',
            'moneda'         => 'required|in:MXN,USD',
            'tipo_cambio'    => 'required_if:moneda,USD|numeric|min:0.01',
            'departamento'   => 'nullable|string|max:255',
            'productos'      => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $subtotalGlobal = 0; $descuentoGlobal = 0; $ivaGlobal = 0; $trasladoGlobal = 0; $retencionGlobal = 0;
            $detallesAInsertar = [];

            foreach ($request->productos as $p) {
                $importeBase = $p['cantidad'] * $p['precio_unitario'];
                $descuento = $p['descuento'] ?? 0;
                $baseImp = $importeBase - $descuento;
                
                $ivaC = $p['aplica_iva'] ? ($baseImp * (($p['iva_porcentaje'] ?? 0) / 100)) : 0;
                $otroC = !empty($p['otro_impuesto']) ? ($baseImp * ($p['otro_impuesto'] / 100)) : 0;

                $subtotalGlobal += $importeBase;
                $descuentoGlobal += $descuento;
                $ivaGlobal += ($ivaC + $otroC);
                $trasladoGlobal += (($p['traslado'] ?? 0) + ($p['ilc'] ?? 0));
                $retencionGlobal += (($p['retencion'] ?? 0) + ($p['isr'] ?? 0));

                $detallesAInsertar[] = [
                    'factura_id'      => $id,
                    'producto'        => $p['producto'],
                    'clave_sat'       => $p['clave_sat'] ?? null,
                    'unidad'          => $p['unidad'] ?? null,
                    'cantidad'        => $p['cantidad'],
                    'precio_unitario' => $p['precio_unitario'],
                    'precio'          => $importeBase,
                    'subtotal'        => $baseImp,
                    'descuento'       => $descuento,
                    'aplica_iva'      => $p['aplica_iva'],
                    'iva_porcentaje'  => $p['aplica_iva'] ? ($p['iva_porcentaje'] ?? 0) : 0,
                    'otro_impuesto_porcentaje' => $p['otro_impuesto'] ?? 0,
                    'impuesto_total'  => round($ivaC + $otroC, 5),
                    'traslado'        => $p['traslado'] ?? 0,
                    'ilc'             => $p['ilc'] ?? 0,
                    'retencion'       => $p['retencion'] ?? 0,
                    'isr'             => $p['isr'] ?? 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            $granTotal = $subtotalGlobal - $descuentoGlobal + $ivaGlobal + $trasladoGlobal - $retencionGlobal;

            DB::table('facturas')->where('factura_id', $id)->update([
                'tipo_documento'  => $request->tipo_documento,
                'insumo'          => $request->insumo, 
                'empresa'         => $request->empresa,
                'folio_factura'   => $request->folio_factura,
                'fecha_factura'   => $request->fecha_factura,
                'moneda'          => $request->moneda,
                'tipo_cambio'     => $request->moneda === 'USD' ? $request->tipo_cambio : 1.0,
                'departamento'    => $request->departamento,
                'descripcion'     => $request->descripcion,
                'subtotal'        => round($subtotalGlobal, 5),
                'descuento_total' => round($descuentoGlobal, 5),
                'iva'             => round($ivaGlobal, 5),
                'traslado_total'  => round($trasladoGlobal, 5),
                'retencion_total' => round($retencionGlobal, 5),
                'total'           => round($granTotal, 5),
                'updated_at'      => now(),
            ]);

            DB::table('factura_detalles')->where('factura_id', $id)->delete();
            DB::table('factura_detalles')->insert($detallesAInsertar);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Documento actualizado correctamente']);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            DB::table('factura_detalles')->where('factura_id', $id)->delete();
            DB::table('facturas')->where('factura_id', $id)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Documento eliminado correctamente']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}