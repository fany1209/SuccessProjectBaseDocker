<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Equipment\EquipmentRepository;
use App\Http\Requests\Equipment\EquipmentRequest;
use App\Http\Resources\Equipment\EquipmentResource;
use App\Traits\UtilResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EquipmentController extends Controller
{
    protected UtilResponse $utilResponse;
    protected EquipmentRepository $equipmentRepo;

    public function __construct(UtilResponse $utilResponse, EquipmentRepository $equipmentRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->equipmentRepo = $equipmentRepo;
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax() || $request->expectsJson()) {
                $equipments = $this->equipmentRepo->all();
                $resource = EquipmentResource::collection($equipments);

                return response()->json([
                    'success' => true,
                    'flag' => true,
                    'code' => 200,
                    'message' => 'Equipos obtenidos correctamente',
                    'data' => $resource,
                ], 200);
            }

            return view('laboratory.table_equipments');
        } catch (Throwable $e) {
            Log::error('Error al listar equipos de laboratorio', [
                'action' => 'EquipmentController@index',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 500,
                    'message' => 'Error al obtener los equipos',
                    'error' => 'Error interno al consultar los equipos.',
                    'data' => [],
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar la vista de equipos.');
        }
    }

    public function edit($id): JsonResponse
    {
        try {
            $equipment = $this->equipmentRepo->find((int) $id);

            if (!$equipment) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 404,
                    'message' => 'Equipo no encontrado',
                    'error' => 'Equipo no encontrado',
                    'data' => null,
                ], 404);
            }

            $resource = new EquipmentResource($equipment);

            return response()->json(array_merge($resource->resolve(), [
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Equipo obtenido correctamente',
                'data' => $resource,
            ]), 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar equipo de laboratorio para edición', [
                'action' => 'EquipmentController@edit',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al obtener el equipo',
                'error' => 'Error interno al consultar el equipo.',
                'data' => null,
            ], 500);
        }
    }

    public function store(EquipmentRequest $request): JsonResponse
    {
        try {
            $equipment = $this->equipmentRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 201,
                'message' => 'Equipment created successfully.',
                'data' => new EquipmentResource($equipment),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al crear equipo de laboratorio', [
                'action' => 'EquipmentController@store',
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al guardar el equipo',
                'error' => 'Error interno al guardar el equipo.',
                'data' => null,
            ], 500);
        }
    }

    public function update(EquipmentRequest $request, $id): JsonResponse
    {
        try {
            $equipment = $this->equipmentRepo->update((int) $id, $request->validated());

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Record updated successfully.',
                'data' => new EquipmentResource($equipment),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 404,
                'message' => 'Equipo no encontrado',
                'error' => 'Equipo no encontrado',
                'data' => null,
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al actualizar equipo de laboratorio', [
                'action' => 'EquipmentController@update',
                'id' => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al actualizar el equipo',
                'error' => 'Error interno al actualizar el equipo.',
                'data' => null,
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->equipmentRepo->delete((int) $id);

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Record removed.',
                'data' => [],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 404,
                'message' => 'Equipo no encontrado',
                'error' => 'Equipo no encontrado',
                'data' => [],
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al eliminar equipo de laboratorio', [
                'action' => 'EquipmentController@destroy',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al eliminar el equipo',
                'error' => 'Error interno al eliminar el equipo.',
                'data' => [],
            ], 500);
        }
    }
}
