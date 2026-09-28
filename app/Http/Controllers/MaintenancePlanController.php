<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenancePlan\MaintenancePlanRequest;
use App\Http\Resources\MaintenancePlan\MaintenancePlanResource;
use App\Http\Repositories\MaintenancePlan\MaintenancePlanRepository;
use App\Traits\UtilResponse;
use Illuminate\Http\Request;
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

    public function index(Request $request)
    {
        try {
            $plans = $this->planRepository->all();

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    MaintenancePlanResource::collection($plans),
                    'Planes de mantenimiento obtenidos correctamente'
                );
            }

            $equipments = $this->planRepository->getActiveEquipments();
            return view('maintenance.plans.index', compact('plans', 'equipments'));
        } catch (Throwable $e) {
            Log::error('Error al consultar listado de planes de mantenimiento', [
                'action'    => 'MaintenancePlanController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar los planes de mantenimiento.', 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al consultar los planes de mantenimiento.');
        }
    }

    public function show($id)
    {
        try {
            $plan = $this->planRepository->find($id);
            if ($plan) {
                return $this->utilResponse->successResponse(
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
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->successResponse(
                        new MaintenancePlanResource($plan),
                        'Plan registrado exitosamente.',
                        201
                    );
                }

                return back()->with('success', 'Plan registrado exitosamente.');
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al registrar el plan de mantenimiento.');
            }

            return back()->with('error', 'Error al registrar el plan de mantenimiento.');
        } catch (Throwable $e) {
            Log::error('Error al registrar plan de mantenimiento', [
                'action'    => 'MaintenancePlanController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Ocurrió un error inesperado al registrar el plan de mantenimiento.', 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al registrar el plan de mantenimiento.');
        }
    }

    public function update(MaintenancePlanRequest $request, $id)
    {
        try {
            $plan = $this->planRepository->find($id);

            if (!$plan) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->errorResponse('No existe el plan de mantenimiento solicitado', 404);
                }

                return back()->with('error', 'No existe el plan de mantenimiento solicitado');
            }

            $data = $request->safe()->only(['equipment_id', 'name', 'frequency_days', 'type']);
            $checklistItems = $request->has('checklist_items') ? $request->input('checklist_items') : null;

            $updatedPlan = $this->planRepository->update($id, $data, $checklistItems);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new MaintenancePlanResource($updatedPlan),
                    'Plan actualizado exitosamente.'
                );
            }

            return back()->with('success', 'Plan actualizado exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar plan de mantenimiento', [
                'action'    => 'MaintenancePlanController@update',
                'plan_id'   => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Ocurrió un error inesperado al actualizar el plan de mantenimiento.', 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al actualizar el plan de mantenimiento.');
        }
    }

    public function destroy($id)
    {
        try {
            $plan = $this->planRepository->find($id);

            if (!$plan) {
                if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                    return $this->utilResponse->errorResponse('No existe el plan de mantenimiento a eliminar', 404);
                }

                return back()->with('error', 'No existe el plan de mantenimiento a eliminar');
            }

            if ($this->planRepository->hasMaintenanceRecords($id)) {
                if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                    return $this->utilResponse->errorResponse('No se puede eliminar el plan porque tiene registros históricos de mantenimiento asociados.', 422);
                }

                return back()->withErrors(['No se puede eliminar el plan porque tiene registros históricos de mantenimiento asociados.']);
            }

            if ($this->planRepository->delete($id)) {
                if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                    return $this->utilResponse->successResponse(null, 'Plan eliminado exitosamente.');
                }

                return back()->with('success', 'Plan eliminado exitosamente.');
            }

            if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                return $this->utilResponse->errorResponse('No se pudo eliminar el plan de mantenimiento.');
            }

            return back()->with('error', 'No se pudo eliminar el plan de mantenimiento.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar plan de mantenimiento', [
                'action'    => 'MaintenancePlanController@destroy',
                'plan_id'   => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                return $this->utilResponse->errorResponse('Ocurrió un error inesperado al eliminar el plan de mantenimiento.', 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al eliminar el plan de mantenimiento.');
        }
    }
}
