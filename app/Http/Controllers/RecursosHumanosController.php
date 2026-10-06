<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\RecursosHumanos\RecursosHumanosRepository;
use App\Http\Requests\RecursosHumanos\CursoStoreRequest;
use App\Http\Requests\RecursosHumanos\ExpedientePdfRequest;
use App\Http\Requests\RecursosHumanos\PracticantePdfRequest;
use App\Http\Resources\RecursosHumanos\CursoResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RecursosHumanosController extends Controller
{
    protected UtilResponse $utilResponse;
    protected RecursosHumanosRepository $rhRepository;

    public function __construct(UtilResponse $utilResponse, RecursosHumanosRepository $rhRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->rhRepository = $rhRepository;
    }

    public function index(): View
    {
        return view('rh');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson()) {
            return $this->utilResponse->successResponse([], 'Expediente creado correctamente.');
        }

        return redirect()->route('expediente.index')
            ->with('success', 'Expediente creado correctamente.');
    }

    public function storeCurso(CursoStoreRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $curso = $this->rhRepository->storeCurso($request->validated());

            if ($request->wantsJson()) {
                return $this->utilResponse->successResponse(
                    new CursoResource($curso),
                    'Curso guardado correctamente.',
                    201
                );
            }

            return redirect()->back()->with('success', 'Curso guardado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar curso de RH', [
                'action' => 'storeCurso',
                'user_id' => auth()->id(),
                'payload' => $request->except(['asistentes']),
                'error' => $e->getMessage(),
            ]);

            if ($request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al registrar el curso.', 500);
            }

            return back()->withErrors('Error al registrar el curso.');
        }
    }

    public function indexCursos(Request $request): View|JsonResponse
    {
        try {
            $cursos = $this->rhRepository->getAllCursos();

            if ($request->wantsJson()) {
                return $this->utilResponse->successResponse(
                    CursoResource::collection($cursos),
                    'Lista de cursos obtenida correctamente.'
                );
            }

            return view('rh.cursos_resultados', compact('cursos'));
        } catch (\Throwable $e) {
            Log::error('Error al consultar lista de cursos', [
                'action' => 'indexCursos',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al consultar los cursos.', 500);
            }

            return back()->withErrors('Error al consultar los cursos.');
        }
    }

    public function generarPdfExpediente(ExpedientePdfRequest $req): Response|RedirectResponse
    {
        try {
            return $this->rhRepository->generateExpedientePdf(
                $req->all(),
                $req->file('foto')
            );
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de expediente', [
                'action' => 'generarPdfExpediente',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['pdf' => 'No se pudo generar el PDF del Expediente. Verifica los datos y la vista.']);
        }
    }

    public function descripcionPuestoPdf(Request $req): Response|RedirectResponse
    {
        try {
            return $this->rhRepository->generateDescripcionPuestoPdf($req->all());
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de descripción de puesto', [
                'action' => 'descripcionPuestoPdf',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['pdf' => 'No se pudo generar el PDF de la Descripción de Puesto. Verifica los datos y la vista.']);
        }
    }

    public function entrevistaTerminacionPdf(Request $req): Response|RedirectResponse
    {
        try {
            return $this->rhRepository->generateEntrevistaTerminacionPdf($req->all());
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de entrevista de terminación', [
                'action' => 'entrevistaTerminacionPdf',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['pdf' => 'No se pudo generar la entrevista. Verifica los campos.']);
        }
    }

    public function evaluacionDesempenoPdf(Request $req): Response|RedirectResponse
    {
        try {
            return $this->rhRepository->generateEvaluacionDesempenoPdf($req->all());
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de evaluación de desempeño', [
                'action' => 'evaluacionDesempenoPdf',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['pdf' => 'No se pudo generar la evaluación del desempeño. Verifica los campos.']);
        }
    }

    public function solicitudPersonalPdf(Request $req): Response|RedirectResponse
    {
        try {
            return $this->rhRepository->generateSolicitudPersonalPdf($req->all());
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de solicitud de personal', [
                'action' => 'solicitudPersonalPdf',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['pdf' => 'No se pudo generar la solicitud de personal. Verifica los campos.']);
        }
    }

    public function convenioInstitucionesPdf(Request $req): Response|RedirectResponse
    {
        try {
            return $this->rhRepository->generateConvenioInstitucionesPdf($req->all());
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de convenio de instituciones', [
                'action' => 'convenioInstitucionesPdf',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['pdf' => 'No se pudo generar el convenio con instituciones. Verifica los campos.']);
        }
    }

    public function generarPdfVacaciones(Request $request): Response|RedirectResponse
    {
        try {
            return $this->rhRepository->generateVacacionesPdf($request->all());
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de vacaciones', [
                'action' => 'generarPdfVacaciones',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['pdf' => 'No se pudo generar la solicitud de vacaciones. Verifica los campos.']);
        }
    }

    public function generarPdfDnc(Request $request): Response|RedirectResponse
    {
        try {
            return $this->rhRepository->generateDncPdf($request->all());
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF DNC', [
                'action' => 'generarPdfDnc',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['pdf' => 'No se pudo generar el Cuestionario DNC. Verifica los campos.']);
        }
    }

    public function generarExpedientePracticantePdf(PracticantePdfRequest $request): Response|RedirectResponse
    {
        try {
            return $this->rhRepository->generatePracticantePdf(
                $request->all(),
                $request->file('foto_infantil')
            );
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de expediente de practicante', [
                'action' => 'generarExpedientePracticantePdf',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['pdf' => 'No se pudo generar el expediente del practicante.']);
        }
    }
}