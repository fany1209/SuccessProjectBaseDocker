<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenanceEquipment\MaintenanceEquipmentRequest;
use App\Http\Resources\MaintenanceEquipment\MaintenanceEquipmentResource;
use App\Http\Repositories\MaintenanceEquipment\MaintenanceEquipmentRepository;
use App\Traits\UtilResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class MaintenanceEquipmentController extends Controller
{
    private $utilResponse;
    private $equipmentRepository;

    public function __construct(UtilResponse $utilResponse, MaintenanceEquipmentRepository $equipmentRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->equipmentRepository = $equipmentRepository;
    }

    public function index()
    {
        try {
            return $this->utilResponse->succesResponse(
                MaintenanceEquipmentResource::collection($this->equipmentRepository->all()),
                'Equipos de mantenimiento obtenidos correctamente'
            );
        } catch (Throwable $e) {
            Log::error('Error al consultar listado de equipos de mantenimiento', [
                'action'    => 'MaintenanceEquipmentController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar los equipos.', 500);
        }
    }

    public function show($id)
    {
        try {
            $equipment = $this->equipmentRepository->find($id);
            if ($equipment) {
                return $this->utilResponse->succesResponse(
                    new MaintenanceEquipmentResource($equipment),
                    'Equipo encontrado'
                );
            }
            return $this->utilResponse->errorResponse('No existe el equipo solicitado', 404);
        } catch (Throwable $e) {
            Log::error('Error al consultar equipo de mantenimiento', [
                'action'       => 'MaintenanceEquipmentController@show',
                'equipment_id' => $id,
                'user_id'      => auth()->id(),
                'exception'    => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar el equipo.', 500);
        }
    }

    public function store(MaintenanceEquipmentRequest $request)
    {
        try {
            $data = $request->validated();
            $equipment = $this->equipmentRepository->create($data);

            if ($equipment) {
                return $this->utilResponse->succesResponse(
                    new MaintenanceEquipmentResource($equipment),
                    'Equipo registrado exitosamente.',
                    201
                );
            }
            return $this->utilResponse->errorResponse('Error al registrar el equipo.');
        } catch (Throwable $e) {
            Log::error('Error al registrar equipo de mantenimiento', [
                'action'    => 'MaintenanceEquipmentController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al registrar el equipo.', 500);
        }
    }

    public function update(MaintenanceEquipmentRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $equipment = $this->equipmentRepository->find($id);

            if (!$equipment) {
                return $this->utilResponse->errorResponse('No existe el equipo solicitado', 404);
            }

            $equipment = $this->equipmentRepository->update($id, $data);
            return $this->utilResponse->succesResponse(
                new MaintenanceEquipmentResource($equipment),
                'Equipo actualizado exitosamente.'
            );
        } catch (Throwable $e) {
            Log::error('Error al actualizar equipo de mantenimiento', [
                'action'       => 'MaintenanceEquipmentController@update',
                'equipment_id' => $id,
                'user_id'      => auth()->id(),
                'payload'      => $request->validated(),
                'exception'    => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al actualizar el equipo.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $equipment = $this->equipmentRepository->find($id);

            if (!$equipment) {
                return $this->utilResponse->errorResponse('No existe el equipo a eliminar', 404);
            }

            if ($this->equipmentRepository->hasMaintenanceRecords($id) || $this->equipmentRepository->hasMaintenancePlans($id)) {
                return $this->utilResponse->errorResponse('No se puede eliminar el equipo porque tiene registros o planes de mantenimiento asociados.', 422);
            }

            if ($this->equipmentRepository->delete($id)) {
                return $this->utilResponse->succesResponse(null, 'Equipo eliminado exitosamente.');
            }

            return $this->utilResponse->errorResponse('No se pudo eliminar el equipo.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar equipo de mantenimiento', [
                'action'       => 'MaintenanceEquipmentController@destroy',
                'equipment_id' => $id,
                'user_id'      => auth()->id(),
                'exception'    => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al eliminar el equipo.', 500);
        }
    }
}
