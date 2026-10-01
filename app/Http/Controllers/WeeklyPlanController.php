<?php

namespace App\Http\Controllers;

use App\Http\Repositories\WeeklyPlan\WeeklyPlanRepository;
use App\Http\Resources\WeeklyPlan\WeeklyPlanResource;
use App\Traits\UtilResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WeeklyPlanController extends Controller
{
    protected UtilResponse $utilResponse;
    protected WeeklyPlanRepository $weeklyPlanRepo;

    public function __construct(UtilResponse $utilResponse, WeeklyPlanRepository $weeklyPlanRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->weeklyPlanRepo = $weeklyPlanRepo;
    }

    /**
     * Muestra la vista principal de planes de trabajo semanales.
     *
     * @return View
     */
    public function index(): View
    {
        return view('weekly_plans.index');
    }

    /**
     * Retorna el listado de planes semanales en formato JSON para DataTables.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatable(Request $request): JsonResponse
    {
        try {
            $plans = $this->weeklyPlanRepo->all();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'data'    => WeeklyPlanResource::collection($plans),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al consultar datatable de planes semanales', [
                'action'    => 'WeeklyPlanController@datatable',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al cargar planes semanales', 500);
        }
    }

    /**
     * Genera y transmite el reporte en PDF del plan de trabajo semanal.
     *
     * @param int|string $id
     * @return mixed
     */
    public function pdf($id)
    {
        try {
            $data = $this->weeklyPlanRepo->getPdfData((int) $id);

            $pdf = Pdf::loadView('formats.laboratory.11', $data)->setPaper('letter');

            $slug = Str::slug(($data['semana_rango'] ?: 'semana'), '-');
            $fileName = 'PlanSemanal_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

            return $pdf->stream($fileName);
        } catch (ModelNotFoundException $e) {
            return abort(404, 'Registro no encontrado');
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de plan semanal', [
                'action'    => 'WeeklyPlanController@pdf',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al generar PDF', 500);
        }
    }

    /**
     * Elimina el plan semanal especificado si el usuario cuenta con el permiso requerido.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        if (Gate::denies('laboratory.delete')) {
            return $this->utilResponse->errorResponse('No autorizado.', 403);
        }

        if (!$id || !is_numeric($id)) {
            return $this->utilResponse->errorResponse('ID no proporcionado o inválido.', 400);
        }

        try {
            $deleted = $this->weeklyPlanRepo->delete((int) $id);

            if ($deleted) {
                return $this->utilResponse->successResponse([], 'Eliminado correctamente.', 200);
            }

            return $this->utilResponse->errorResponse('Registro no encontrado.', 404);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar plan semanal', [
                'action'    => 'WeeklyPlanController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error en el servidor al eliminar.', 500);
        }
    }
}
