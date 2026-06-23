<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class CertificateApiController extends Controller
{
    public function index(): JsonResponse
    {
        $rows = DB::table('quality_certificates')
            ->select(
                'id',
                'folio',
                'fecha',
                'cliente',
                'producto',
                'lote',
                'cantidad',
                'no_tarimas',
                'fecha_salida_cedis',
                'certificado_tarima',
                'muestra_o_pf',
                'created_at'
            )
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        return response()->json(['data' => $rows]);
    }

    public function destroy($id): JsonResponse
    {
        if (!ctype_digit((string)$id)) {
            return response()->json(['ok' => false, 'message' => 'ID inválido'], 422);
        }

        $deleted = DB::table('quality_certificates')->where('id', (int)$id)->delete();

        return response()->json([
            'ok' => (bool) $deleted,
            'deleted' => (int) $deleted
        ]);
    }

   public function export()
    {
        $rows = \DB::table('quality_certificates')
            ->select(
                'id',
                'folio',
                'fecha',
                'cliente',
                'producto',
                'lote',
                'cantidad',
                'no_tarimas',
                'fecha_salida_cedis',
                'certificado_tarima',
                'muestra_o_pf',   
                'created_at'
            )

            ->orderBy('fecha', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $filename = 'certificados_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control'       => 'no-store, no-cache',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');

            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, [
                'ID','Folio','Fecha','Cliente','Producto','Lote','Cantidad',
                'No. Tarimas','Fecha salida CEDIS','Certificado',
                'Tipo (Muestra/PF)','Creado en'
            ]);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->id,
                    $r->folio,
                    $r->fecha,
                    $r->cliente,
                    $r->producto,
                    $r->lote,
                    $r->cantidad,
                    $r->no_tarimas,
                    $r->fecha_salida_cedis,
                    $r->certificado_tarima,
                    $r->muestra_o_pf, 
                    $r->created_at,
                ]);
            }

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

}
