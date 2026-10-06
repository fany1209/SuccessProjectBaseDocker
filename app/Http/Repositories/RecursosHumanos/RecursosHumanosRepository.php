<?php

namespace App\Http\Repositories\RecursosHumanos;

use App\Models\Curso;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecursosHumanosRepository
{
    protected Curso $cursoModel;

    public function __construct(Curso $cursoModel)
    {
        $this->cursoModel = $cursoModel;
    }

    public function getAllCursos(): Collection
    {
        return $this->cursoModel->newQuery()
            ->orderBy('fecha', 'desc')
            ->get();
    }

    public function storeCurso(array $data): Curso
    {
        return DB::transaction(function () use ($data) {
            return $this->cursoModel->create($data);
        });
    }

    public function generateExpedientePdf(array $data, ?UploadedFile $foto = null)
    {
        $fmt = fn($d) => $d ? Carbon::parse($d)->format('d/m/Y') : null;

        $fotoBase64 = null;
        if ($foto && $foto->isValid()) {
            $path = $foto->getRealPath();
            $imagen = base64_encode(file_get_contents($path));
            $tipo = $foto->getClientMimeType();
            $fotoBase64 = 'data:' . $tipo . ';base64,' . $imagen;
        }

        $viewData = [
            'nombre'               => $data['nombre'] ?? '',
            'foto'                 => $fotoBase64,
            'sexo'                 => $data['sexo'] ?? null,
            'estado_civil'         => $data['estado_civil'] ?? null,
            'anios_empresa'        => $data['anios_empresa'] ?? null,
            'dias_vacaciones'      => $data['dias_vacaciones'] ?? null,
            'requisitos'           => $data['requisitos'] ?? [],
            'fecha_ingreso_1'      => $fmt($data['fecha_ingreso_1'] ?? null),
            'duracion_1'           => $data['duracion_1'] ?? null,
            'fecha_baja_1'         => $fmt($data['fecha_baja_1'] ?? null),
            'fecha_ingreso_2'      => $fmt($data['fecha_ingreso_2'] ?? null),
            'duracion_2'           => $data['duracion_2'] ?? null,
            'fecha_baja_2'         => $fmt($data['fecha_baja_2'] ?? null),
            'motivo_baja'          => $data['motivo_baja'] ?? null,
            'motivo_baja_otro'     => $data['motivo_baja_otro'] ?? null,
            'tiene_hijos'          => $data['tiene_hijos'] ?? null,
            'cuantos_hijos'        => $data['cuantos_hijos'] ?? null,
            'hijos'                => $data['hijos'] ?? [],
            'pareja_trabaja'       => $data['pareja_trabaja'] ?? null,
            'empresa_pareja'       => $data['empresa_pareja'] ?? null,
            'accidente_nombre'     => $data['accidente_nombre'] ?? null,
            'accidente_parentesco' => $data['accidente_parentesco'] ?? null,
            'accidente_telefono'   => $data['accidente_telefono'] ?? null,
            'alergico'             => $data['alergico'] ?? null,
            'alergias_desc'        => $data['alergias_desc'] ?? null,
            'tipo_sangre'          => $data['tipo_sangre'] ?? null,
        ];

        $pdf = Pdf::loadView('formats.rh.expediente', $viewData)->setPaper('letter');

        $dompdf = $pdf->getDomPDF();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        $canvas = $dompdf->get_canvas();
        $w = $canvas->get_width();
        $fm = $dompdf->getFontMetrics();
        $font = $fm->getFont('DejaVu Sans', 'normal');
        $size = 9;
        $text = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';

        $ml = 24 * 0.75;
        $mr = 24 * 0.75;
        $innerW = $w - $ml - $mr;
        $colStart = $ml + ($innerW * 0.80);
        $colWidth = $innerW * 0.20;
        $pad = 10 * 0.75;
        $colStart += $pad;
        $colWidth -= $pad * 2;
        $textWidth = $fm->getTextWidth($text, $font, $size);
        $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 25;
        $y = 60;

        $canvas->page_text($x, $y, $text, $font, $size, [0, 0, 0]);

        $slug = Str::slug('expediente_personal_' . $viewData['nombre'], '_');
        return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function generateDescripcionPuestoPdf(array $data)
    {
        $pdf = Pdf::loadView('formats.rh.descripcion_puesto', $data)->setPaper('letter');

        $dompdf = $pdf->getDomPDF();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        $canvas = $dompdf->get_canvas();
        $w = $canvas->get_width();
        $fm = $dompdf->getFontMetrics();
        $font = $fm->getFont('DejaVu Sans', 'normal');
        $size = 7;
        $text = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';

        $ml = 24 * 0.75;
        $mr = 24 * 0.75;
        $innerW = $w - $ml - $mr;
        $colStart = $ml + ($innerW * 0.80);
        $colWidth = $innerW * 0.20;
        $pad = 10 * 0.75;
        $colStart += $pad;
        $colWidth -= $pad * 2;
        $textWidth = $fm->getTextWidth($text, $font, $size);
        $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 29;
        $y = 60;

        $canvas->page_text($x, $y, $text, $font, $size, [0, 0, 0]);

        $nombrePuesto = $data['nombre_puesto'] ?? 'General';
        $slug = Str::slug('descripcion_puesto_' . $nombrePuesto, '_');

        return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function generateEntrevistaTerminacionPdf(array $data)
    {
        $viewData = $data;
        $viewData['fecha_hoy'] = now()->format('d/m/Y');
        if (!empty($viewData['fecha_ultimo_dia'])) {
            $viewData['fecha_ultimo_dia'] = Carbon::parse($viewData['fecha_ultimo_dia'])->format('d/m/Y');
        }

        $pdf = Pdf::loadView('formats.rh.entrevista_terminacion', $viewData)->setPaper('letter');

        $dompdf = $pdf->getDomPDF();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        $canvas = $dompdf->get_canvas();
        $w = $canvas->get_width();
        $fm = $dompdf->getFontMetrics();
        $font = $fm->getFont('DejaVu Sans', 'normal');
        $size = 9;
        $text = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';

        $ml = 24 * 0.75;
        $mr = 24 * 0.75;
        $innerW = $w - $ml - $mr;
        $colStart = $ml + ($innerW * 0.80);
        $colWidth = $innerW * 0.20;
        $pad = 10 * 0.75;
        $colStart += $pad;
        $colWidth -= $pad * 2;
        $textWidth = $fm->getTextWidth($text, $font, $size);
        $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 25;
        $y = 58;

        $canvas->page_text($x, $y, $text, $font, $size, [0, 0, 0]);

        $slug = Str::slug('entrevista_terminacion_' . ($viewData['nombre'] ?? 'empleado'), '_');
        return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function generateEvaluacionDesempenoPdf(array $data)
    {
        $pdf = Pdf::loadView('formats.rh.evaluacion_desempeno', $data)->setPaper('letter');

        $dompdf = $pdf->getDomPDF();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        $slug = Str::slug('evaluacion_desempeno_' . ($data['nombre_evaluado'] ?? 'empleado'), '_');
        return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function generateSolicitudPersonalPdf(array $data)
    {
        $data['fecha_incorporacion'] = $data['fecha_incorporacion'] ?? null;

        $pdf = Pdf::loadView('formats.rh.solicitud_personal', $data)->setPaper('letter');

        $dompdf = $pdf->getDomPDF();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        $slug = Str::slug('solicitud_personal_' . ($data['nombre_puesto'] ?? 'puesto'), '_');
        return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function generateConvenioInstitucionesPdf(array $data)
    {
        $data['fecha_convenio'] = $data['fecha_convenio'] ?? null;

        $pdf = Pdf::loadView('formats.rh.convenio_instituciones', $data)->setPaper('letter');

        $dompdf = $pdf->getDomPDF();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        $slug = Str::slug('convenio_instituciones_' . ($data['escuela'] ?? 'escuela'), '_');
        return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function generateVacacionesPdf(array $data)
    {
        $vacation = (object) $data;

        if (isset($data['pending_tasks'])) {
            $vacation->pending_tasks = json_decode(json_encode($data['pending_tasks']));
        }

        $pdf = Pdf::loadView('formats.rh.vacation', compact('vacation'))->setPaper('letter');

        $dompdf = $pdf->getDomPDF();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        $canvas = $dompdf->get_canvas();
        $w = $canvas->get_width();
        $fm = $dompdf->getFontMetrics();
        $font = $fm->getFont('DejaVu Sans', 'normal');
        $size = 9;
        $text = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';

        $ml = 24 * 0.75;
        $mr = 24 * 0.75;
        $innerW = $w - $ml - $mr;
        $colStart = $ml + ($innerW * 0.80);
        $colWidth = $innerW * 0.20;
        $pad = 10 * 0.75;
        $colStart += $pad;
        $colWidth -= $pad * 2;
        $textWidth = $fm->getTextWidth($text, $font, $size);
        $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 25;
        $y = 58;

        $canvas->page_text($x, $y, $text, $font, $size, [0, 0, 0]);

        $slug = Str::slug('solicitud_vacaciones_' . ($vacation->employee_name ?? 'empleado'), '_');
        return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function generateDncPdf(array $data)
    {
        $dnc = (object) $data;

        $pdf = Pdf::loadView('formats.rh.dnc', compact('dnc'))->setPaper('letter');

        $dompdf = $pdf->getDomPDF();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        $canvas = $dompdf->get_canvas();
        $w = $canvas->get_width();
        $fm = $dompdf->getFontMetrics();
        $font = $fm->getFont('DejaVu Sans', 'normal');
        $size = 9;
        $text = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';

        $ml = 24 * 0.75;
        $mr = 24 * 0.75;
        $innerW = $w - $ml - $mr;
        $colStart = $ml + ($innerW * 0.80);
        $colWidth = $innerW * 0.20;
        $pad = 10 * 0.75;
        $colStart += $pad;
        $colWidth -= $pad * 2;
        $textWidth = $fm->getTextWidth($text, $font, $size);
        $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 25;
        $y = 74;

        $canvas->page_text($x, $y, $text, $font, $size, [0, 0, 0]);

        $slug = Str::slug('dnc_' . ($dnc->employee_name ?? 'empleado'), '_');
        return $pdf->stream($slug . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function generatePracticantePdf(array $data, ?UploadedFile $foto = null)
    {
        $practicante = (object) $data;
        $practicante->foto_base64 = null;

        if ($foto && $foto->isValid()) {
            $imageData = file_get_contents($foto->getRealPath());
            $mimeType = $foto->getClientMimeType();
            $practicante->foto_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
        }

        $reqData = [];
        $nombresRequisitos = [
            'Fotografía infantil', 'Solicitud de empleo o CV', 'Copia de acta de nacimiento',
            'Copia de comprobante de domicilio', 'Copia de INE', 'Copia de CURP', 'Copia de RFC',
            'Número de Seguro Social', 'Copia del último grado de estudios', 'Constancias de cursos tomados',
            'Copia de licencia de manejo', 'Solicitud de Residencias', 'Liberación de servicio social',
            'Carta de Presentación', 'Carta de Aceptación', 'Convenio de Colaboración', 'Carta de Terminación'
        ];

        $requisitosInput = $data['req'] ?? [];
        foreach ($requisitosInput as $index => $reqRow) {
            $reqData[] = [
                'nombre' => $nombresRequisitos[$index] ?? 'N/A',
                'ok'     => $reqRow['ok'] ?? null,
                'com'    => $reqRow['com'] ?? '',
            ];
        }
        $practicante->req = $reqData;

        $pdf = Pdf::loadView('formats.rh.practicante', compact('practicante'))->setPaper('letter');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        $canvas = $dompdf->get_canvas();
        $w = $canvas->get_width();
        $fm = $dompdf->getFontMetrics();
        $font = $fm->getFont('Arial', 'bold');
        $size = 9;
        $text = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';

        $x = $w - 100;
        $y = 60;

        $canvas->page_text($x, $y, $text, $font, $size, [0, 0, 0]);

        return $pdf->stream('Expediente_' . Str::slug($practicante->nombre ?? 'practicante') . '.pdf');
    }
}
