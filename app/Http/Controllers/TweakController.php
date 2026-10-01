<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Tweak\TweakRepository;
use App\Http\Requests\Tweak\TweakRequest;
use App\Http\Resources\Tweak\TweakResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TweakController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var TweakRepository
     */
    protected TweakRepository $tweakRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param TweakRepository $tweakRepo
     */
    public function __construct(UtilResponse $utilResponse, TweakRepository $tweakRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->tweakRepo = $tweakRepo;
    }

    /**
     * Listado general de ajustes de inventario para API o AJAX.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $tweaks = $this->tweakRepo->all($request->only(['inventory_id', 'type']));
            $collection = TweakResource::collection($tweaks);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Ajustes de inventario obtenidos correctamente.',
                'data'    => $collection,
                'tweaks'  => $collection,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al listar ajustes en TweakController@index', [
                'action'    => 'TweakController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar los ajustes de inventario.', 500);
        }
    }

    /**
     * Obtiene el detalle de un ajuste de inventario.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $tweak = $this->tweakRepo->find($id);

            if (!$tweak) {
                return $this->utilResponse->errorResponse('Ajuste de inventario no encontrado.', 404);
            }

            $resource = (new TweakResource($tweak))->resolve();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Ajuste de inventario obtenido correctamente.',
                'data'    => $resource,
                'tweak'   => $resource,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar ajuste en TweakController@show', [
                'action'    => 'TweakController@show',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos del ajuste.', 500);
        }
    }

    /**
     * Registra un nuevo ajuste de inventario y actualiza el stock en transacción ACID.
     *
     * @param TweakRequest $request
     * @return JsonResponse
     */
    public function store(TweakRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id() ?? 1;

            $tweak = $this->tweakRepo->create($data);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfuly make it',
                'data'    => new TweakResource($tweak),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar ajuste de inventario en TweakController@store', [
                'action'    => 'TweakController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar el ajuste de inventario: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Elimina un ajuste de inventario y revierte el stock con bloqueo pesimista.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $deleted = $this->tweakRepo->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Tweak not deleted',
                    'data'    => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Tweak deleted and stock reverted successfully',
                'data'    => [],
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al eliminar ajuste en TweakController@destroy', [
                'action'    => 'TweakController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar el ajuste de inventario.', 500);
        }
    }
}
