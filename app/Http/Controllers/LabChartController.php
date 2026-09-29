<?php

namespace App\Http\Controllers;

use App\Http\Repositories\LabChart\LabChartRepository;
use App\Http\Requests\LabChart\LabChartRequest;
use App\Http\Resources\LabChart\LabChartResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class LabChartController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var LabChartRepository
     */
    protected LabChartRepository $labChartRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param LabChartRepository $labChartRepo
     */
    public function __construct(UtilResponse $utilResponse, LabChartRepository $labChartRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->labChartRepo = $labChartRepo;
    }

    /**
     * Muestra la vista del gráfico de registros de laboratorio.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if ($request->expectsJson() || $request->is('api/*')) {
                $counts = $this->labChartRepo->getCounts();
                return $this->utilResponse->successResponse(
                    new LabChartResource($counts),
                    'Datos de gráfica de laboratorio obtenidos correctamente.'
                );
            }

            return view('laboratory.grafica');
        } catch (Throwable $e) {
            Log::error('Error al cargar vista de gráfica en LabChartController@index', [
                'action'    => 'LabChartController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->errorResponse('Error al cargar gráfica de laboratorio.', 500);
            }

            abort(500, 'Error al cargar gráfica de laboratorio.');
        }
    }

    /**
     * Retorna los conteos de registros de laboratorio en formato JSON estandarizado.
     *
     * @param LabChartRequest $request
     * @return JsonResponse
     */
    public function counts(LabChartRequest $request): JsonResponse
    {
        try {
            $counts = $this->labChartRepo->getCounts();
            $resource = (new LabChartResource($counts))->resolve();

            // Garantizamos compatibilidad total tanto con UtilResponse {"success":true,"data":{...}}
            // como con consumidores legacy de frontend que leen las claves en la raíz del objeto JSON
            return response()->json(array_merge([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Conteos de registros obtenidos correctamente.',
                'data'    => $resource,
            ], $resource), 200);
        } catch (Throwable $e) {
            Log::error('Error al obtener conteos de laboratorio en LabChartController@counts', [
                'action'    => 'LabChartController@counts',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener los conteos de registros.', 500);
        }
    }
}
