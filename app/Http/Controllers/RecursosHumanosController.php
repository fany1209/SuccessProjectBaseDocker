<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;                   
use Barryvdh\DomPDF\Facade\Pdf;     
use Illuminate\Support\Str;
class RecursosHumanosController extends Controller
{
    public function index()
    {
        return view('rh'); 
    }

    public function store(Request $request)
    {

        return redirect()->route('expediente.index')
                         ->with('success', 'Expediente creado correctamente.');
    }

    public function generarPdfExpediente(Request $req)
    {
        $validated = $req->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $fmt = fn($d) => $d ? Carbon::parse($d)->format('d/m/Y') : null;

        $viewData = [
            'nombre'              => $req->input('nombre'),
            'sexo'                => $req->input('sexo'),
            'estado_civil'        => $req->input('estado_civil'),
            'anios_empresa'       => $req->input('anios_empresa'),
            'dias_vacaciones'     => $req->input('dias_vacaciones'),
            'requisitos'          => $req->input('requisitos', []),
            'fecha_ingreso_1'     => $fmt($req->input('fecha_ingreso_1')),
            'duracion_1'          => $req->input('duracion_1'),
            'fecha_baja_1'        => $fmt($req->input('fecha_baja_1')),
            'fecha_ingreso_2'     => $fmt($req->input('fecha_ingreso_2')),
            'duracion_2'          => $req->input('duracion_2'),
            'fecha_baja_2'        => $fmt($req->input('fecha_baja_2')),
            'motivo_baja'         => $req->input('motivo_baja'),
            'motivo_baja_otro'    => $req->input('motivo_baja_otro'),
            'tiene_hijos'         => $req->input('tiene_hijos'),
            'cuantos_hijos'       => $req->input('cuantos_hijos'),
            'hijos'               => $req->input('hijos', []),
            'pareja_trabaja'      => $req->input('pareja_trabaja'),
            'empresa_pareja'      => $req->input('empresa_pareja'),
            'accidente_nombre'    => $req->input('accidente_nombre'),
            'accidente_parentesco'=> $req->input('accidente_parentesco'),
            'accidente_telefono'  => $req->input('accidente_telefono'),
            'alergico'            => $req->input('alergico'),
            'alergias_desc'       => $req->input('alergias_desc'),
            'tipo_sangre'         => $req->input('tipo_sangre'),
        ];

        try {
            $pdf = Pdf::loadView('formats.rh.expediente', $viewData)
                      ->setPaper('letter');

            $dompdf = $pdf->getDomPDF();
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();
            
            $canvas = $dompdf->get_canvas();
            $w      = $canvas->get_width();
            $fm     = $dompdf->getFontMetrics();
            $font   = $fm->getFont('DejaVu Sans', 'normal');
            $size   = 7;
            $text   = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';
            
            $ml = 24 * 0.75; $mr = 24 * 0.75; $innerW = $w - $ml - $mr;
            $colStart = $ml + ($innerW * 0.80); $colWidth = $innerW * 0.20;
            $pad = 10 * 0.75; $colStart += $pad; $colWidth -= $pad * 2;
            $textWidth = $fm->getTextWidth($text, $font, $size);
            $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 29;
            $y = 63;
            
            $canvas->page_text($x, $y, $text, $font, $size, [0,0,0]);

            $slug = Str::slug('expediente_personal_' . $viewData['nombre'], '_');
            return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['pdf' => 'No se pudo generar el PDF del Expediente. Verifica los datos y la vista.']);
        }
    }

    public function descripcionPuestoPdf(Request $req)
    {
        $viewData = $req->all();

        try {
            $pdf = Pdf::loadView('formats.rh.descripcion_puesto', $viewData)
                      ->setPaper('letter');

            $dompdf = $pdf->getDomPDF();
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();
            $canvas = $dompdf->get_canvas();
            $w      = $canvas->get_width();
            $fm     = $dompdf->getFontMetrics();
            $font   = $fm->getFont('DejaVu Sans', 'normal');
            $size   = 7;
            $text   = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';
            
            $ml = 24 * 0.75; $mr = 24 * 0.75; $innerW = $w - $ml - $mr;
            $colStart = $ml + ($innerW * 0.80); $colWidth = $innerW * 0.20;
            $pad = 10 * 0.75; $colStart += $pad; $colWidth -= $pad * 2;
            $textWidth = $fm->getTextWidth($text, $font, $size);
            
            $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 29;
            $y = 60;
            
            $canvas->page_text($x, $y, $text, $font, $size, [0,0,0]);

            $nombrePuesto = $viewData['nombre_puesto'] ?? 'General';
            $slug = Str::slug('descripcion_puesto_' . $nombrePuesto, '_');
            
            return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['pdf' => 'No se pudo generar el PDF de la Descripción de Puesto. Verifica los datos y la vista.']);
        }
    }

    public function entrevistaTerminacionPdf(Request $req)
    {
        $viewData = $req->all();
        
        $viewData['fecha_hoy'] = now()->format('d/m/Y');
        if (!empty($viewData['fecha_ultimo_dia'])) {
            $viewData['fecha_ultimo_dia'] = \Carbon\Carbon::parse($viewData['fecha_ultimo_dia'])->format('d/m/Y');
        }

        try {
            $pdf = Pdf::loadView('formats.rh.entrevista_terminacion', $viewData)
                      ->setPaper('letter');

            $dompdf = $pdf->getDomPDF();
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();
            $canvas = $dompdf->get_canvas();
            $w      = $canvas->get_width();
            $fm     = $dompdf->getFontMetrics();
            $font   = $fm->getFont('DejaVu Sans', 'normal');
            $size   = 9;
            $text   = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';
            
            $ml = 24 * 0.75; $mr = 24 * 0.75; $innerW = $w - $ml - $mr;
            $colStart = $ml + ($innerW * 0.80); $colWidth = $innerW * 0.20;
            $pad = 10 * 0.75; $colStart += $pad; $colWidth -= $pad * 2;
            $textWidth = $fm->getTextWidth($text, $font, $size);
            
            $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 25;
            $y = 58;
            
            $canvas->page_text($x, $y, $text, $font, $size, [0,0,0]);

            $slug = Str::slug('entrevista_terminacion_' . ($viewData['nombre'] ?? 'empleado'), '_');
            return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['pdf' => 'No se pudo generar la entrevista. Verifica los campos.']);
        }
    }

    public function evaluacionDesempenoPdf(Request $req)
    {
        $viewData = $req->all();

        try {
            $pdf = Pdf::loadView('formats.rh.evaluacion_desempeno', $viewData)
                      ->setPaper('letter');

            $dompdf = $pdf->getDomPDF();
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();
            
            $slug = Str::slug('evaluacion_desempeno_' . ($viewData['nombre_evaluado'] ?? 'empleado'), '_');
            return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['pdf' => 'No se pudo generar la evaluación del desempeño. Verifica los campos.']);
        }
    }

    public function solicitudPersonalPdf(Request $req)
    {
        $viewData = $req->all();

        try {
            $pdf = Pdf::loadView('formats.rh.solicitud_personal', $viewData)
                      ->setPaper('letter');

            $dompdf = $pdf->getDomPDF();
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();
            
            $slug = Str::slug('solicitud_personal_' . ($viewData['nombre_puesto'] ?? 'puesto'), '_');
            return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['pdf' => 'No se pudo generar la solicitud de personal. Verifica los campos.']);
        }
    }

    public function convenioInstitucionesPdf(Request $req)
    {
        $viewData = $req->all();

        try {
            $pdf = Pdf::loadView('formats.rh.convenio_instituciones', $viewData)
                      ->setPaper('letter');

            $dompdf = $pdf->getDomPDF();
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();
            
            $slug = Str::slug('convenio_instituciones_' . ($viewData['escuela'] ?? 'escuela'), '_');
            return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['pdf' => 'No se pudo generar el convenio con instituciones. Verifica los campos.']);
        }
    }
}