<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Input\InputRepository;
use App\Http\Requests\Input\InputStoreRequest;
use App\Http\Requests\Input\InputUpdateRequest;
use App\Http\Resources\Input\InputResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class InputController extends Controller
{
    protected UtilResponse $utilResponse;
    protected InputRepository $inputRepo;

    public function __construct(UtilResponse $utilResponse, InputRepository $inputRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->inputRepo = $inputRepo;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $inputs = $this->inputRepo->all();

            return $this->utilResponse->successResponse(
                InputResource::collection($inputs),
                'Listado de entradas obtenido correctamente',
                200
            );
        } catch (Throwable $e) {
            Log::error('Error al listar entradas en InputController@index', [
                'action'  => 'InputController@index',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar las entradas de almacén.', 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $details = $this->inputRepo->getWithDetails($id);

            if (!$details) {
                return $this->utilResponse->errorResponse('Entrada no encontrada.', 404);
            }

            return response()->json([
                'success'  => true,
                'flag'     => true,
                'code'     => 200,
                'message'  => 'Entrada obtenida exitosamente.',
                'input'    => $details['input'],
                'products' => $details['products'],
                'data'     => [
                    'input'    => new InputResource($details['input']),
                    'products' => $details['products'],
                ],
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar entrada en InputController@show', [
                'action'   => 'InputController@show',
                'input_id' => $id,
                'user_id'  => auth()->id(),
                'error'    => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar detalle de la entrada.', 500);
        }
    }

    public function store(InputStoreRequest $request): JsonResponse
    {
        try {
            $input = $this->inputRepo->createWithTransactions($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'The input was added successfully.',
                'data'    => [
                    'input_id' => $input->input_id,
                    'input'    => new InputResource($input),
                ],
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar entrada en InputController@store', [
                'action'  => 'InputController@store',
                'payload' => $request->all(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al registrar la entrada de almacén.', 500);
        }
    }

    public function update(InputUpdateRequest $request, $id = null): JsonResponse
    {
        $targetId = (int) ($id ?? $request->route('input') ?? $request->input('id'));

        if ($targetId <= 0) {
            return $this->utilResponse->errorResponse('Identificador de entrada inválido.', 400);
        }

        try {
            $updated = $this->inputRepo->updateWithInventory($targetId, $request->validated());

            if (!$updated) {
                return $this->utilResponse->errorResponse('Entrada no encontrada para actualizar.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Operation successfuly make it',
                'data'    => new InputResource($updated),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al actualizar entrada en InputController@update', [
                'action'   => 'InputController@update',
                'input_id' => $targetId,
                'payload'  => $request->all(),
                'user_id'  => auth()->id(),
                'error'    => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al actualizar la entrada.', 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $deleted = $this->inputRepo->delete($id);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('No se pudo eliminar la entrada.', 404);
            }

            return $this->utilResponse->successResponse(
                [],
                'Entrada eliminada correctamente.',
                200
            );
        } catch (Throwable $e) {
            Log::error('Error al eliminar entrada en InputController@destroy', [
                'action'   => 'InputController@destroy',
                'input_id' => $id,
                'user_id'  => auth()->id(),
                'error'    => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al eliminar la entrada.', 500);
        }
    }
}
