<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Logistic\LogisticRepository;
use App\Http\Requests\Logistic\LogisticRequest;
use App\Http\Resources\Logistic\LogisticResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class LogisticController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var LogisticRepository
     */
    protected LogisticRepository $logisticRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param LogisticRepository $logisticRepo
     */
    public function __construct(UtilResponse $utilResponse, LogisticRepository $logisticRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->logisticRepo = $logisticRepo;
    }

    /**
     * Muestra la vista principal del módulo de logística o retorna resumen en JSON.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $data = $this->logisticRepo->getIndexData();

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->successResponse(
                    new LogisticResource([
                        'count_operators' => $data['count_operators'],
                        'count_tLines'    => $data['count_tLines'],
                        'count_vehicles'  => $data['count_vehicles'],
                        'count_trailers'  => $data['count_trailers'],
                    ]),
                    'Datos de logística obtenidos correctamente.'
                );
            }

            return view('logistic', $data);
        } catch (Throwable $e) {
            Log::error('Error al consultar datos de logística en LogisticController@index', [
                'action'    => 'LogisticController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->errorResponse('Error al consultar el módulo de logística.', 500);
            }

            abort(500, 'Error al consultar el módulo de logística.');
        }
    }

    /**
     * Retorna los datos agrupados para los gráficos de líneas de transporte.
     *
     * @param LogisticRequest $request
     * @return JsonResponse
     */
    public function charts(LogisticRequest $request): JsonResponse
    {
        try {
            $charts = $this->logisticRepo->getChartsData();

            // Retornamos estructura compatible tanto con UtilResponse {"success":true,"data":{...}}
            // como con las llamadas frontend que consumen directamente response.vehicles_per_tl
            return response()->json(array_merge([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Gráficas de logística obtenidas correctamente.',
                'data'    => $charts,
            ], $charts), 200);
        } catch (Throwable $e) {
            Log::error('Error al generar gráficas de transporte en LogisticController@charts', [
                'action'    => 'LogisticController@charts',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener gráficas de transporte.', 500);
        }
    }
}
