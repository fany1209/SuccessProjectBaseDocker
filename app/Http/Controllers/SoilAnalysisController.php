<?php

namespace App\Http\Controllers;

use App\Http\Repositories\SoilAnalysis\SoilAnalysisRepository;
use App\Http\Resources\SoilAnalysis\SoilAnalysisResource;
use App\Traits\UtilResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SoilAnalysisController extends Controller
{
    protected UtilResponse $utilResponse;
    protected SoilAnalysisRepository $soilAnalysisRepo;

    public function __construct(UtilResponse $utilResponse, SoilAnalysisRepository $soilAnalysisRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->soilAnalysisRepo = $soilAnalysisRepo;
    }

    /**
     * Muestra la vista principal de análisis de suelos.
     *
     * @return View
     */
    public function index(): View
    {
        return view('laboratory.soil.index');
    }

    /**
     * Retorna el listado de análisis de suelos en formato estructurado para DataTables.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatable(Request $request): JsonResponse
    {
        try {
            $analyses = $this->soilAnalysisRepo->all();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'data'    => SoilAnalysisResource::collection($analyses),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al consultar datatable de análisis de suelo', [
                'action'    => 'SoilAnalysisController@datatable',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al cargar análisis de suelo', 500);
        }
    }

    /**
     * Genera y descarga el reporte en PDF del análisis de suelo especificado.
     *
     * @param int|string $id
     * @return mixed
     */
    public function pdfShow($id)
    {
        try {
            $data = $this->soilAnalysisRepo->getPdfData((int) $id);

            $pdf = Pdf::loadView('formats.laboratory.06', $data)->setPaper('letter');

            return $pdf->download('Analisis_Suelo_' . $id . '.pdf');
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Registro no encontrado en la base de datos.');
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de análisis de suelo', [
                'action'    => 'SoilAnalysisController@pdfShow',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error interno al generar PDF'], 500);
        }
    }

    /**
     * Elimina el análisis de suelo y sus variables convencionales asociadas.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        if (!$id || !is_numeric($id)) {
            return $this->utilResponse->errorResponse('ID no proporcionado o inválido.', 400);
        }

        try {
            $deleted = $this->soilAnalysisRepo->delete((int) $id);

            if ($deleted) {
                return $this->utilResponse->successResponse([], 'Registro eliminado correctamente.', 200);
            }

            return $this->utilResponse->errorResponse('No se encontró el registro para eliminar.', 404);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar análisis de suelo', [
                'action'    => 'SoilAnalysisController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error en el servidor al eliminar.', 500);
        }
    }
}