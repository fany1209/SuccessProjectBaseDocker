<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; 

class SoilAnalysisController extends Controller
{
    public function index()
    {
        return view('laboratory.soil.index');
    }

    public function datatable(Request $request)
    {
        try {
            $rows = DB::table('soil_internal_analyses')
                ->select(['id', 'report_code', 'entry_date', 'issue_date', 'client_name'])
                ->orderByDesc('entry_date')
                ->get()
                ->map(function ($r) {
                    return [
                        'id'          => $r->id,
                        'report_code' => $r->report_code ?? '—',
                        'entry_date'  => $r->entry_date ?? '—',
                        'issue_date'  => $r->issue_date ?? '—',
                        'client_name' => $r->client_name ?? '—',
                        'pdf_url'     => route('soil.analyses.pdf', ['id' => $r->id]),
                        'delete_url'  => route('soil.analyses.delete', ['id' => $r->id]),
                    ];
                });

            return response()->json(['data' => $rows]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function pdfShow($id)
    {
        $a = DB::table('soil_internal_analyses')->where('id', $id)->first();
        
        if (!$a) {
            return abort(404, 'Registro no encontrado en la base de datos.');
        }

        try {
            $conv = DB::table('soil_conventional_variables')
                ->where('soil_analysis_id', $id)
                ->orderBy('position_order')
                ->get();

            $data = [
                'reporte'        => $a->report_code,
                'fecha_ingreso'  => $a->entry_date,
                'fecha_emision'  => $a->issue_date,
                'cliente_nombre' => $a->client_name,
                'convencionales' => $conv->map(fn($r) => [
                    'v' => $r->variable_name,
                    'r' => $r->result_text,
                    'u' => $r->unit_text,
                ])->toArray(),
            ];

            $pdf = Pdf::loadView('formats.laboratory.06', $data)->setPaper('letter');
            
            return $pdf->download('Analisis_Suelo_'.$id.'.pdf');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno al generar PDF'], 500);
        }
    }

    public function destroy($id)
    {
        if (!$id) {
            return response()->json(['message' => 'ID no proporcionado.'], 400);
        }

        try {
            $deleted = DB::table('soil_internal_analyses')->where('id', $id)->delete();

            if ($deleted) {
                return response()->json(['message' => 'Registro eliminado correctamente.'], 200);
            } else {
                return response()->json(['message' => 'No se encontró el registro para eliminar.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error en el servidor al eliminar.'], 500);
        }
    }
}