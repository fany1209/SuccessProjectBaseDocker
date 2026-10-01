<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Trailer\TrailerRepository;
use App\Http\Requests\Trailer\TrailerRequest;
use App\Http\Resources\Trailer\TrailerResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TrailerController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var TrailerRepository
     */
    protected TrailerRepository $trailerRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param TrailerRepository $trailerRepo
     */
    public function __construct(UtilResponse $utilResponse, TrailerRepository $trailerRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->trailerRepo = $trailerRepo;
    }

    /**
     * Listado general de remolques para API.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->getTrailers($request);
    }

    /**
     * Endpoint compatible con DataTables en la vista logística.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTrailers(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 't_line']);
            $trailers = $this->trailerRepo->all($filters);
            $collection = TrailerResource::collection($trailers);

            return response()->json([
                'success'  => true,
                'flag'     => true,
                'code'     => 200,
                'message'  => 'Remolques obtenidos correctamente.',
                'data'     => $collection,
                'trailers' => $collection,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al listar remolques en TrailerController@getTrailers', [
                'action'    => 'TrailerController@getTrailers',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar los remolques.', 500);
        }
    }

    /**
     * Obtiene los datos de un remolque por su ID para su edición modal.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $trailer = $this->trailerRepo->find($id);

            if (!$trailer) {
                return $this->utilResponse->errorResponse('Remolque no encontrado.', 404);
            }

            $resource = (new TrailerResource($trailer))->resolve();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Remolque obtenido correctamente.',
                'data'    => $resource,
                'trailer' => $resource,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar remolque en TrailerController@show', [
                'action'    => 'TrailerController@show',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos del remolque.', 500);
        }
    }

    /**
     * Registra un nuevo remolque en transacción ACID.
     *
     * @param TrailerRequest $request
     * @return JsonResponse
     */
    public function store(TrailerRequest $request): JsonResponse
    {
        try {
            $trailer = $this->trailerRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfuly make it',
                'data'    => new TrailerResource($trailer),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar remolque en TrailerController@store', [
                'action'    => 'TrailerController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar el remolque.', 500);
        }
    }

    /**
     * Actualiza un remolque con bloqueo pesimista.
     *
     * @param TrailerRequest $request
     * @param int|string|null $id
     * @return JsonResponse
     */
    public function update(TrailerRequest $request, $id = null): JsonResponse
    {
        try {
            $trailerId = $id ?? $request->input('trailer_id');

            if (!$trailerId) {
                return $this->utilResponse->errorResponse('ID de remolque no proporcionado.', 400);
            }

            $trailer = $this->trailerRepo->update($trailerId, $request->validated());

            if (!$trailer) {
                return $this->utilResponse->errorResponse('Remolque no encontrado.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Operation successfuly make it',
                'data'    => new TrailerResource($trailer),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al actualizar remolque en TrailerController@update', [
                'action'    => 'TrailerController@update',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar el remolque.', 500);
        }
    }

    /**
     * Elimina un remolque con bloqueo pesimista.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $deleted = $this->trailerRepo->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Trailer not deleted',
                    'data'    => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Trailer deleted',
                'data'    => [],
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al eliminar remolque en TrailerController@destroy', [
                'action'    => 'TrailerController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar el remolque.', 500);
        }
    }
}
