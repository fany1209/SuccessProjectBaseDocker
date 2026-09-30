<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Operator\OperatorRepository;
use App\Http\Requests\Operator\OperatorRequest;
use App\Http\Resources\Operator\OperatorResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class OperatorController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var OperatorRepository
     */
    protected OperatorRepository $operatorRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param OperatorRepository $operatorRepo
     */
    public function __construct(UtilResponse $utilResponse, OperatorRepository $operatorRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->operatorRepo = $operatorRepo;
    }

    /**
     * Listado general de operadores para API.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->getOperators($request);
    }

    /**
     * Endpoint compatible con DataTables en la vista logística.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getOperators(Request $request): JsonResponse
    {
        try {
            $operators = $this->operatorRepo->all($request->only(['search']));
            $collection = OperatorResource::collection($operators);

            return response()->json([
                'success'   => true,
                'flag'      => true,
                'code'      => 200,
                'message'   => 'Operadores obtenidos correctamente.',
                'data'      => $collection,
                'operators' => $collection,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al listar operadores en OperatorController@getOperators', [
                'action'    => 'OperatorController@getOperators',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar los operadores.', 500);
        }
    }

    /**
     * Obtiene los datos de un operador por su ID para su edición modal.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $operator = $this->operatorRepo->find($id);

            if (!$operator) {
                return $this->utilResponse->errorResponse('Operador no encontrado.', 404);
            }

            $resource = (new OperatorResource($operator))->resolve();

            return response()->json([
                'success'  => true,
                'flag'     => true,
                'code'     => 200,
                'message'  => 'Operador obtenido correctamente.',
                'data'     => $resource,
                'operator' => $resource,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar operador en OperatorController@show', [
                'action'    => 'OperatorController@show',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos del operador.', 500);
        }
    }

    /**
     * Registra un nuevo operador en transacción ACID.
     *
     * @param OperatorRequest $request
     * @return JsonResponse
     */
    public function store(OperatorRequest $request): JsonResponse
    {
        try {
            $operator = $this->operatorRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfuly make it',
                'data'    => new OperatorResource($operator),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar operador en OperatorController@store', [
                'action'    => 'OperatorController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar el operador.', 500);
        }
    }

    /**
     * Actualiza un operador con bloqueo pesimista.
     *
     * @param OperatorRequest $request
     * @param int|string|null $id
     * @return JsonResponse
     */
    public function update(OperatorRequest $request, $id = null): JsonResponse
    {
        try {
            $operatorId = $id ?? $request->input('operator_id');

            if (!$operatorId) {
                return $this->utilResponse->errorResponse('ID de operador no proporcionado.', 400);
            }

            $operator = $this->operatorRepo->update($operatorId, $request->validated());

            if (!$operator) {
                return $this->utilResponse->errorResponse('Operador no encontrado.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Operation successfuly make it',
                'data'    => new OperatorResource($operator),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al actualizar operador en OperatorController@update', [
                'action'    => 'OperatorController@update',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar el operador.', 500);
        }
    }

    /**
     * Elimina un operador con bloqueo pesimista.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $deleted = $this->operatorRepo->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Operator not deleted',
                    'data'    => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Operator deleted',
                'data'    => [],
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al eliminar operador en OperatorController@destroy', [
                'action'    => 'OperatorController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar el operador.', 500);
        }
    }
}
