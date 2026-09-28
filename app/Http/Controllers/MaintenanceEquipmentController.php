<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenanceEquipment\MaintenanceEquipmentRequest;
use App\Http\Resources\MaintenanceEquipment\MaintenanceEquipmentResource;
use App\Http\Repositories\MaintenanceEquipment\MaintenanceEquipmentRepository;
use App\Traits\UtilResponse;
use Illuminate\Http\Request;
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

    public function index(Request $request)
    {
        try {
            $equipments = $this->equipmentRepository->all();

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    MaintenanceEquipmentResource::collection($equipments),
                    'Equipos de mantenimiento obtenidos correctamente'
                );
            }

            $areas = $this->equipmentRepository->getAreas();
            return view('maintenance.equipment.index', compact('equipments', 'areas'));
        } catch (Throwable $e) {
            Log::error('Error al consultar listado de equipos de mantenimiento', [
                'action'    => 'MaintenanceEquipmentController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar los equipos.', 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al consultar los equipos.');
        }
    }

    public function show($id)
    {
        try {
            $equipment = $this->equipmentRepository->find($id);
            if ($equipment) {
                return $this->utilResponse->successResponse(
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
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->successResponse(
                        new MaintenanceEquipmentResource($equipment),
                        'Equipo registrado exitosamente.',
                        201
                    );
                }

                return back()->with('success', 'Equipo registrado exitosamente.');
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al registrar el equipo.');
            }

            return back()->with('error', 'Error al registrar el equipo.');
        } catch (Throwable $e) {
            Log::error('Error al registrar equipo de mantenimiento', [
                'action'    => 'MaintenanceEquipmentController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Ocurrió un error inesperado al registrar el equipo.', 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al registrar el equipo.');
        }
    }

    public function update(MaintenanceEquipmentRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $equipment = $this->equipmentRepository->find($id);

            if (!$equipment) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->errorResponse('No existe el equipo solicitado', 404);
                }

                return back()->with('error', 'No existe el equipo solicitado');
            }

            $equipment = $this->equipmentRepository->update($id, $data);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new MaintenanceEquipmentResource($equipment),
                    'Equipo actualizado exitosamente.'
                );
            }

            return back()->with('success', 'Equipo actualizado exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar equipo de mantenimiento', [
                'action'       => 'MaintenanceEquipmentController@update',
                'equipment_id' => $id,
                'user_id'      => auth()->id(),
                'payload'      => $request->validated(),
                'exception'    => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Ocurrió un error inesperado al actualizar el equipo.', 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al actualizar el equipo.');
        }
    }

    public function destroy($id)
    {
        try {
            $equipment = $this->equipmentRepository->find($id);

            if (!$equipment) {
                if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                    return $this->utilResponse->errorResponse('No existe el equipo a eliminar', 404);
                }

                return back()->with('error', 'No existe el equipo a eliminar');
            }

            if ($this->equipmentRepository->hasMaintenanceRecords($id) || $this->equipmentRepository->hasMaintenancePlans($id)) {
                if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                    return $this->utilResponse->errorResponse('No se puede eliminar el equipo porque tiene registros o planes de mantenimiento asociados.', 422);
                }

                return back()->withErrors(['No se puede eliminar el equipo porque tiene registros o planes de mantenimiento asociados.']);
            }

            if ($this->equipmentRepository->delete($id)) {
                if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                    return $this->utilResponse->successResponse(null, 'Equipo eliminado exitosamente.');
                }

                return back()->with('success', 'Equipo eliminado exitosamente.');
            }

            if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                return $this->utilResponse->errorResponse('No se pudo eliminar el equipo.');
            }

            return back()->with('error', 'No se pudo eliminar el equipo.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar equipo de mantenimiento', [
                'action'       => 'MaintenanceEquipmentController@destroy',
                'equipment_id' => $id,
                'user_id'      => auth()->id(),
                'exception'    => $e->getMessage(),
            ]);

            if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                return $this->utilResponse->errorResponse('Ocurrió un error inesperado al eliminar el equipo.', 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al eliminar el equipo.');
        }
    }
}
