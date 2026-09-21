<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenancePlan\MaintenancePlanRequest;
use App\Http\Resources\MaintenancePlan\MaintenancePlanResource;
use App\Http\Repositories\MaintenancePlan\MaintenancePlanRepository;
use App\Traits\UtilResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class MaintenancePlanController extends Controller
{
    private $utilResponse;
    private $planRepository;

    public function __construct(UtilResponse $utilResponse, MaintenancePlanRepository $planRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->planRepository = $planRepository;
    }

    public function index()
    {
        try {
            return $this->utilResponse->succesResponse(
                MaintenancePlanResource::collection($this->planRepository->all()),
                'Planes de mantenimiento obtenidos correctamente'
            );
        } catch (Throwable $e) {
            Log::error('Error al consultar listado de planes de mantenimiento', [
                'action'    => 'MaintenancePlanController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar los planes de mantenimiento.', 500);
        }
    }

    public function show($id)
    {
        try {
            $plan = $this->planRepository->find($id);
            if ($plan) {
                return $this->utilResponse->succesResponse(
                    new MaintenancePlanResource($plan),
                    'Plan de mantenimiento encontrado'
                );
            }
            return $this->utilResponse->errorResponse('No existe el plan de mantenimiento solicitado', 404);
        } catch (Throwable $e) {
            Log::error('Error al consultar plan de mantenimiento', [
                'action'    => 'MaintenancePlanController@show',
                'plan_id'   => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar el plan de mantenimiento.', 500);
        }
    }

    public function store(MaintenancePlanRequest $request)
    {
        try {
            $data = $request->safe()->only(['equipment_id', 'name', 'frequency_days', 'type']);
            $checklistItems = $request->input('checklist_items', []);

            $plan = $this->planRepository->create($data, $checklistItems);

            if ($plan) {
                return $this->utilResponse->succesResponse(
                    new MaintenancePlanResource($plan),
                    'Plan registrado exitosamente.',
                    201
                );
            }
            return $this->utilResponse->errorResponse('Error al registrar el plan de mantenimiento.');
        } catch (Throwable $e) {
            Log::error('Error al registrar plan de mantenimiento', [
                'action'    => 'MaintenancePlanController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al registrar el plan de mantenimiento.', 500);
        }
    }

    public function update(MaintenancePlanRequest $request, $id)
    {
        try {
            $plan = $this->planRepository->find($id);

            if (!$plan) {
                return $this->utilResponse->errorResponse('No existe el plan de mantenimiento solicitado', 404);
            }

            $data = $request->safe()->only(['equipment_id', 'name', 'frequency_days', 'type']);
            $checklistItems = $request->has('checklist_items') ? $request->input('checklist_items') : null;

            $updatedPlan = $this->planRepository->update($id, $data, $checklistItems);

            return $this->utilResponse->succesResponse(
                new MaintenancePlanResource($updatedPlan),
                'Plan actualizado exitosamente.'
            );
        } catch (Throwable $e) {
            Log::error('Error al actualizar plan de mantenimiento', [
                'action'    => 'MaintenancePlanController@update',
                'plan_id'   => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al actualizar el plan de mantenimiento.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $plan = $this->planRepository->find($id);

            if (!$plan) {
                return $this->utilResponse->errorResponse('No existe el plan de mantenimiento a eliminar', 404);
            }

            if ($this->planRepository->hasMaintenanceRecords($id)) {
                return $this->utilResponse->errorResponse('No se puede eliminar el plan porque tiene registros históricos de mantenimiento asociados.', 422);
            }

            if ($this->planRepository->delete($id)) {
                return $this->utilResponse->succesResponse(null, 'Plan eliminado exitosamente.');
            }

            return $this->utilResponse->errorResponse('No se pudo eliminar el plan de mantenimiento.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar plan de mantenimiento', [
                'action'    => 'MaintenancePlanController@destroy',
                'plan_id'   => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al eliminar el plan de mantenimiento.', 500);
        }
    }
}
