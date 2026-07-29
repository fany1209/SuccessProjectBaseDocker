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

    public function storeCurso(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'sede' => 'required|string|max:255',
            'horario' => 'required|string|max:255',
            'curso' => 'required|string|max:255',
            'objetivo' => 'required|string',
            'asistentes' => 'nullable|array'
        ]);

        // Filter empty assistants
        if (isset($validated['asistentes'])) {
            $validated['asistentes'] = array_filter($validated['asistentes'], function($value) {
                return !is_null($value) && trim($value) !== '';
            });
        }

        \App\Models\Curso::create($validated);

        return redirect()->back()->with('success', 'Curso guardado correctamente.');
    }

    public function indexCursos()
    {
        $cursos = \App\Models\Curso::orderBy('fecha', 'desc')->get();
        return view('rh.cursos_resultados', compact('cursos'));
    }

    public function generarPdfExpediente(Request $req)
    {
        $validated = $req->validate([
            'nombre' => 'required|string|max:255',
            'foto'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        $fmt = fn($d) => $d ? Carbon::parse($d)->format('d/m/Y') : null;

        $fotoBase64 = null;
        if ($req->hasFile('foto')) {
            $path = $req->file('foto')->getRealPath();
            $imagen = base64_encode(file_get_contents($path));
            $tipo = $req->file('foto')->getClientMimeType();
            $fotoBase64 = 'data:' . $tipo . ';base64,' . $imagen;
        }

        $viewData = [
            'nombre'              => $req->input('nombre'),
            'foto'                => $fotoBase64, 
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
            $size   = 9;
            $text   = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';
            
            $ml = 24 * 0.75; $mr = 24 * 0.75; $innerW = $w - $ml - $mr;
            $colStart = $ml + ($innerW * 0.80); $colWidth = $innerW * 0.20;
            $pad = 10 * 0.75; $colStart += $pad; $colWidth -= $pad * 2;
            $textWidth = $fm->getTextWidth($text, $font, $size);
            $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 25;
            $y = 60;
            
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

    public function generarPdfVacaciones(Request $request)
    {
        $vacation = (object) $request->all();

        if ($request->has('pending_tasks')) {
            $vacation->pending_tasks = json_decode(json_encode($request->pending_tasks));
        }

        try {
            $pdf = Pdf::loadView('formats.rh.vacation', compact('vacation'))
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

            $slug = \Illuminate\Support\Str::slug('solicitud_vacaciones_' . ($vacation->employee_name ?? 'empleado'), '_');
            
            return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['pdf' => 'No se pudo generar la solicitud de vacaciones. Verifica los campos.']);
        }
    }

    public function generarPdfDnc(Request $request)
    {
        $dnc = (object) $request->all();

        try {
            $pdf = Pdf::loadView('formats.rh.dnc', compact('dnc'))
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
            $y = 74; 
            
            $canvas->page_text($x, $y, $text, $font, $size, [0,0,0]);

            $slug = \Illuminate\Support\Str::slug('dnc_' . ($dnc->employee_name ?? 'empleado'), '_');
            
            return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');

        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['pdf' => 'No se pudo generar el Cuestionario DNC. Verifica los campos.']);
        }
    }

    public function generarExpedientePracticantePdf(Request $request)
    {
        $practicante = (object) $request->all();
        
        $practicante->foto_base64 = null; 

        if ($request->hasFile('foto_infantil') && $request->file('foto_infantil')->isValid()) {
            $image = $request->file('foto_infantil');
            $imageData = file_get_contents($image->getRealPath());
            $mimeType = $image->getMimeType(); 
            $practicante->foto_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
        }

        $reqData = [];
        $nombresRequisitos = ['Fotografía infantil', 'Solicitud de empleo o CV', 'Copia de acta de nacimiento', 'Copia de comprobante de domicilio', 'Copia de INE', 'Copia de CURP', 'Copia de RFC', 'Número de Seguro Social', 'Copia del último grado de estudios', 'Constancias de cursos tomados', 'Copia de licencia de manejo', 'Solicitud de Residencias', 'Liberación de servicio social', 'Carta de Presentación', 'Carta de Aceptación', 'Convenio de Colaboración', 'Carta de Terminación'];
        
        $requisitosInput = $request->req ?? [];
        foreach ($requisitosInput as $index => $data) {
            $reqData[] = [
                'nombre' => $nombresRequisitos[$index] ?? 'N/A',
                'ok'     => $data['ok'] ?? null,
                'com'    => $data['com'] ?? ''
            ];
        }
        $practicante->req = $reqData;

        $pdf = \Pdf::loadView('formats.rh.practicante', compact('practicante'))->setPaper('letter');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->get_canvas();
        $w      = $canvas->get_width();
        $fm     = $dompdf->getFontMetrics();
        $font   = $fm->getFont('Arial', 'bold');
        $size   = 9;
        $text   = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';
        
        $x = $w - 100; 
        $y = 60; 
        
        $canvas->page_text($x, $y, $text, $font, $size, [0, 0, 0]);

        return $pdf->stream('Expediente_' . \Illuminate\Support\Str::slug($practicante->nombre ?? 'practicante') . '.pdf');
    }
}