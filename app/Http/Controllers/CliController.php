<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Cli\CliRepository;
use App\Http\Requests\Cli\StoreCliRequest;
use App\Http\Requests\Cli\UpdateCliRequest;
use App\Http\Resources\Cli\CliResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class CliController extends Controller
{
    private UtilResponse $utilResponse;
    private CliRepository $cliRepository;

    public function __construct(UtilResponse $utilResponse, CliRepository $cliRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->cliRepository = $cliRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $clis = $this->cliRepository->all();

            return $this->utilResponse->successResponse(
                CliResource::collection($clis),
                'Operaciones de almacén obtenidas correctamente'
            );
        } catch (Throwable $e) {
            Log::error('Error al consultar operaciones de almacén: ' . $e->getMessage(), [
                'action'    => 'CliController@index',
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error al consultar operaciones de almacén.', 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $cli = $this->cliRepository->findWithWarehouseAndProduct((int) $id);

            if (!$cli) {
                return $this->utilResponse->errorResponse('Operación de almacén no encontrada.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Operación de almacén obtenida correctamente',
                'data'    => $cli,
                'cli'     => $cli,
            ]);
        } catch (Throwable $e) {
            Log::error('Error al consultar operación de almacén: ' . $e->getMessage(), [
                'action'    => 'CliController@show',
                'id'        => $id,
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error al consultar la operación de almacén.', 500);
        }
    }

    public function store(StoreCliRequest $request): JsonResponse
    {
        try {
            $createdRecords = $this->cliRepository->createMany($request->validated());

            return $this->utilResponse->successResponse(
                CliResource::collection(collect($createdRecords)),
                'Operación de almacén registrada exitosamente.',
                201
            );
        } catch (Throwable $e) {
            Log::error('Error al registrar operación de almacén: ' . $e->getMessage(), [
                'action'    => 'CliController@store',
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error al registrar la operación de almacén.', 500);
        }
    }

    public function update(UpdateCliRequest $request, $id = null): JsonResponse
    {
        try {
            $cliId = $id ?? $request->input('cli_id');

            if (!$cliId) {
                return $this->utilResponse->errorResponse('Identificador de operación no proporcionado.', 422);
            }

            $updated = $this->cliRepository->update((int) $cliId, $request->validated());

            if (!$updated) {
                return $this->utilResponse->errorResponse('Operación de almacén no encontrada.', 404);
            }

            return $this->utilResponse->successResponse(
                new CliResource($updated),
                'Operación de almacén actualizada exitosamente.'
            );
        } catch (Throwable $e) {
            Log::error('Error al actualizar operación de almacén: ' . $e->getMessage(), [
                'action'    => 'CliController@update',
                'id'        => $id,
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar la operación de almacén.', 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->cliRepository->delete((int) $id);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('Operación de almacén no encontrada o ya eliminada.', 404);
            }

            return $this->utilResponse->successResponse(null, 'Operación de almacén eliminada exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar operación de almacén: ' . $e->getMessage(), [
                'action'    => 'CliController@destroy',
                'id'        => $id,
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar la operación de almacén.', 500);
        }
    }
}