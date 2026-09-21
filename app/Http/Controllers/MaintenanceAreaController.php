<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenanceArea\MaintenanceAreaRequest;
use App\Http\Resources\MaintenanceArea\MaintenanceAreaResource;
use App\Http\Repositories\MaintenanceArea\MaintenanceAreaRepository;
use App\Traits\UtilResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class MaintenanceAreaController extends Controller
{
    private $utilResponse;
    private $maintenanceAreaRepository;

    public function __construct(UtilResponse $utilResponse, MaintenanceAreaRepository $maintenanceAreaRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->maintenanceAreaRepository = $maintenanceAreaRepository;
    }

    public function index()
    {
        try {
            return $this->utilResponse->successResponse(
                MaintenanceAreaResource::collection($this->maintenanceAreaRepository->all()),
                'Categorías / Áreas obtenidas correctamente'
            );
        } catch (Throwable $e) {
            Log::error('Error al consultar listado de áreas de mantenimiento', [
                'action'    => 'MaintenanceAreaController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar las áreas.', 500);
        }
    }

    public function show($id)
    {
        try {
            $area = $this->maintenanceAreaRepository->find($id);
            if ($area) {
                return $this->utilResponse->successResponse(new MaintenanceAreaResource($area), 'Categoría / Área encontrada');
            }
            return $this->utilResponse->errorResponse('No existe la categoría o área', 404);
        } catch (Throwable $e) {
            Log::error('Error al consultar área de mantenimiento', [
                'action'    => 'MaintenanceAreaController@show',
                'area_id'   => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar la categoría o área.', 500);
        }
    }

    public function store(MaintenanceAreaRequest $request)
    {
        try {
            $data = $request->validated();
            $area = $this->maintenanceAreaRepository->create($data);

            if ($area) {
                return $this->utilResponse->successResponse(
                    new MaintenanceAreaResource($area),
                    'Categoría/Área registrada exitosamente.',
                    201
                );
            }
            return $this->utilResponse->errorResponse('Error al crear la categoría o área');
        } catch (Throwable $e) {
            Log::error('Error al registrar área de mantenimiento', [
                'action'    => 'MaintenanceAreaController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al registrar el área.', 500);
        }
    }

    public function update(MaintenanceAreaRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $area = $this->maintenanceAreaRepository->find($id);

            if (!$area) {
                return $this->utilResponse->errorResponse('No existe la categoría o área', 404);
            }

            $area = $this->maintenanceAreaRepository->update($id, $data);
            return $this->utilResponse->successResponse(
                new MaintenanceAreaResource($area),
                'Categoría/Área actualizada exitosamente.'
            );
        } catch (Throwable $e) {
            Log::error('Error al actualizar área de mantenimiento', [
                'action'    => 'MaintenanceAreaController@update',
                'area_id'   => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al actualizar el área.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $area = $this->maintenanceAreaRepository->find($id);

            if (!$area) {
                return $this->utilResponse->errorResponse('No se pudo eliminar la categoría o no existe', 404);
            }

            if ($this->maintenanceAreaRepository->hasEquipment($id)) {
                return $this->utilResponse->errorResponse('No se puede eliminar esta categoría porque tiene equipos asignados.', 422);
            }

            if ($this->maintenanceAreaRepository->delete($id)) {
                return $this->utilResponse->successResponse(null, 'Categoría/Área eliminada exitosamente.');
            }

            return $this->utilResponse->errorResponse('No se pudo eliminar la categoría o área');
        } catch (Throwable $e) {
            Log::error('Error al eliminar área de mantenimiento', [
                'action'    => 'MaintenanceAreaController@destroy',
                'area_id'   => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al eliminar el área.', 500);
        }
    }
}
