<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Vehicle\VehicleRepository;
use App\Http\Requests\Vehicle\VehicleRequest;
use App\Http\Resources\Vehicle\VehicleResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class VehicleController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var VehicleRepository
     */
    protected VehicleRepository $vehicleRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param VehicleRepository $vehicleRepo
     */
    public function __construct(UtilResponse $utilResponse, VehicleRepository $vehicleRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->vehicleRepo = $vehicleRepo;
    }

    /**
     * Listado general de vehículos para API.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->getVehicles($request);
    }

    /**
     * Endpoint compatible con DataTables en la vista logística.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getVehicles(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 't_line']);
            $vehicles = $this->vehicleRepo->all($filters);
            $collection = VehicleResource::collection($vehicles);

            return response()->json([
                'success'  => true,
                'flag'     => true,
                'code'     => 200,
                'message'  => 'Vehículos obtenidos correctamente.',
                'data'     => $collection,
                'vehicles' => $collection,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al listar vehículos en VehicleController@getVehicles', [
                'action'    => 'VehicleController@getVehicles',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar los vehículos.', 500);
        }
    }

    /**
     * Obtiene los datos de un vehículo por su ID para su edición modal.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $vehicle = $this->vehicleRepo->find($id);

            if (!$vehicle) {
                return $this->utilResponse->errorResponse('Vehículo no encontrado.', 404);
            }

            $resource = (new VehicleResource($vehicle))->resolve();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Vehículo obtenido correctamente.',
                'data'    => $resource,
                'vehicle' => $resource,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar vehículo en VehicleController@show', [
                'action'    => 'VehicleController@show',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos del vehículo.', 500);
        }
    }

    /**
     * Registra un nuevo vehículo en transacción ACID.
     *
     * @param VehicleRequest $request
     * @return JsonResponse
     */
    public function store(VehicleRequest $request): JsonResponse
    {
        try {
            $vehicle = $this->vehicleRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfuly make it',
                'data'    => new VehicleResource($vehicle),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar vehículo en VehicleController@store', [
                'action'    => 'VehicleController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar el vehículo.', 500);
        }
    }

    /**
     * Actualiza un vehículo con bloqueo pesimista.
     *
     * @param VehicleRequest $request
     * @param int|string|null $id
     * @return JsonResponse
     */
    public function update(VehicleRequest $request, $id = null): JsonResponse
    {
        try {
            $vehicleId = $id ?? $request->input('vehicle_id');

            if (!$vehicleId) {
                return $this->utilResponse->errorResponse('ID de vehículo no proporcionado.', 400);
            }

            $vehicle = $this->vehicleRepo->update($vehicleId, $request->validated());

            if (!$vehicle) {
                return $this->utilResponse->errorResponse('Vehículo no encontrado.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Operation successfuly make it',
                'data'    => new VehicleResource($vehicle),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al actualizar vehículo en VehicleController@update', [
                'action'    => 'VehicleController@update',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar el vehículo.', 500);
        }
    }

    /**
     * Elimina un vehículo con bloqueo pesimista.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $deleted = $this->vehicleRepo->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Vehicle not deleted',
                    'data'    => [],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Vehicle deleted',
                'data'    => [],
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al eliminar vehículo en VehicleController@destroy', [
                'action'    => 'VehicleController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar el vehículo.', 500);
        }
    }
}
