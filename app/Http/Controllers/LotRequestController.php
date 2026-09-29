<?php

namespace App\Http\Controllers;

use App\Http\Repositories\LotRequest\LotRequestRepository;
use App\Http\Requests\LotRequest\LotRequestRequest;
use App\Http\Resources\LotRequest\LotRequestResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class LotRequestController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var LotRequestRepository
     */
    protected LotRequestRepository $lotRequestRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param LotRequestRepository $lotRequestRepo
     */
    public function __construct(UtilResponse $utilResponse, LotRequestRepository $lotRequestRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->lotRequestRepo = $lotRequestRepo;
    }

    /**
     * Muestra la vista principal de peticiones de lote o retorna listado JSON.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if ($request->expectsJson() || $request->is('api/*')) {
                $lots = $this->lotRequestRepo->all($request->only(['status', 'department']));
                return $this->utilResponse->successResponse(
                    LotRequestResource::collection($lots),
                    'Peticiones de lote obtenidas correctamente.'
                );
            }

            $pendingLotRequests = $this->lotRequestRepo->countPending();
            return view('formats.laboratory.lot_requests.index', compact('pendingLotRequests'));
        } catch (Throwable $e) {
            Log::error('Error al consultar peticiones de lote en LotRequestController@index', [
                'action'    => 'LotRequestController@index',
                'user_id'   => Auth::id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->errorResponse('Error al consultar peticiones de lote.', 500);
            }

            abort(500, 'Error al consultar peticiones de lote.');
        }
    }

    /**
     * Registra una nueva petición de lote.
     *
     * @param LotRequestRequest $request
     * @return JsonResponse
     */
    public function store(LotRequestRequest $request): JsonResponse
    {
        try {
            $lot = $this->lotRequestRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Registered request.',
                'data'    => new LotRequestResource($lot),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar petición de lote en LotRequestController@store', [
                'action'    => 'LotRequestController@store',
                'user_id'   => Auth::id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar la petición de lote.', 500);
        }
    }

    /**
     * Retorna datos formateados para DataTables junto con agrupaciones de skus y lotes.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatable(Request $request): JsonResponse
    {
        try {
            $data = $this->lotRequestRepo->getDatatableData();

            return response()->json(array_merge([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Datos de peticiones de lote obtenidos correctamente.',
                'data'    => $data,
            ], $data), 200);
        } catch (Throwable $e) {
            Log::error('Error al generar DataTables en LotRequestController@datatable', [
                'action'    => 'LotRequestController@datatable',
                'user_id'   => Auth::id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos de peticiones de lote.', 500);
        }
    }

    /**
     * Actualiza el estatus y asignación de SKU/Lote aplicando bloqueo pesimista.
     *
     * @param int|string $id
     * @param LotRequestRequest $request
     * @return JsonResponse
     */
    public function updateStatus($id, LotRequestRequest $request): JsonResponse
    {
        try {
            $result = $this->lotRequestRepo->updateStatus($id, $request->validated());

            if (!$result) {
                return $this->utilResponse->errorResponse('Petición de lote no encontrada.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Estatus de petición de lote actualizado correctamente.',
                'pending' => $result['pending'],
                'data'    => new LotRequestResource($result['lot']),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al actualizar estatus en LotRequestController@updateStatus', [
                'action'    => 'LotRequestController@updateStatus',
                'id'        => $id,
                'user_id'   => Auth::id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar estatus de la petición.', 500);
        }
    }

    /**
     * Consulta las peticiones pendientes para usuarios con rol Quality.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkPending(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->roles()->whereIn('name', ['Quality', 'quality'])->exists()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 200,
                    'message' => 'Sin autorizacion o rol de calidad.',
                    'pending' => [],
                    'count'   => 0,
                    'data'    => [
                        'pending' => [],
                        'count'   => 0,
                    ],
                ]);
            }

            $pending = $this->lotRequestRepo->getPending();
            $count = $pending->count();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Peticiones pendientes obtenidas correctamente.',
                'pending' => $pending,
                'count'   => $count,
                'data'    => [
                    'pending' => LotRequestResource::collection($pending),
                    'count'   => $count,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Error al consultar peticiones pendientes en LotRequestController@checkPending', [
                'action'    => 'LotRequestController@checkPending',
                'user_id'   => Auth::id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar peticiones pendientes.', 500);
        }
    }

    /**
     * Retorna el conteo de peticiones terminadas.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function countCompleted(Request $request): JsonResponse
    {
        try {
            $count = $this->lotRequestRepo->countCompleted();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Conteo de peticiones terminadas obtenido correctamente.',
                'count'   => $count,
                'data'    => [
                    'count' => $count,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Error al contar peticiones terminadas en LotRequestController@countCompleted', [
                'action'    => 'LotRequestController@countCompleted',
                'user_id'   => Auth::id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al contar peticiones terminadas.', 500);
        }
    }
}
