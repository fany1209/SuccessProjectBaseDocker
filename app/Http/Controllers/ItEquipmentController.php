<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ItEquipment\ItEquipmentRepository;
use App\Http\Requests\ItEquipment\ItEquipmentRequest;
use App\Http\Resources\ItEquipment\ItEquipmentResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ItEquipmentController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var ItEquipmentRepository
     */
    protected ItEquipmentRepository $equipmentRepo;

    /**
     * Inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param ItEquipmentRepository $equipmentRepo
     */
    public function __construct(UtilResponse $utilResponse, ItEquipmentRepository $equipmentRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->equipmentRepo = $equipmentRepo;
    }

    /**
     * Listado de equipos de TI con filtros opcionales (Blade / JSON).
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'department', 'article']);
            $equipments = $this->equipmentRepo->all($filters);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    ItEquipmentResource::collection($equipments),
                    'Equipos de TI obtenidos correctamente.'
                );
            }

            return view('sistemas-ti.inventario', compact('equipments'));
        } catch (Throwable $e) {
            Log::error('Error al listar equipos de TI en ItEquipmentController@index', [
                'action'    => 'ItEquipmentController@index',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar equipos de TI.', 500);
            }

            abort(500, 'Error al consultar equipos de TI.');
        }
    }

    /**
     * Muestra el detalle de un equipo de TI específico.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse|View
     */
    public function show(Request $request, $id)
    {
        try {
            $equipment = $this->equipmentRepo->find($id);

            if (!$equipment) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->errorResponse('Equipo de TI no encontrado.', 404);
                }
                abort(404, 'Equipo de TI no encontrado.');
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ItEquipmentResource($equipment),
                    'Equipo de TI obtenido correctamente.'
                );
            }

            return view('sistemas-ti.inventario', ['equipments' => collect([$equipment])]);
        } catch (Throwable $e) {
            Log::error('Error al consultar equipo de TI en ItEquipmentController@show', [
                'action'    => 'ItEquipmentController@show',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar equipo de TI.', 500);
            }

            abort(500, 'Error al consultar equipo de TI.');
        }
    }

    /**
     * Almacena un nuevo equipo de TI.
     *
     * @param ItEquipmentRequest $request
     * @return JsonResponse|RedirectResponse
     */
    public function store(ItEquipmentRequest $request)
    {
        try {
            $equipment = $this->equipmentRepo->create($request->validated());

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ItEquipmentResource($equipment),
                    'Equipo registrado correctamente.',
                    201
                );
            }

            return redirect()->route('sistemas-ti.inventario')
                ->with('success', 'Equipo registrado correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar equipo de TI en ItEquipmentController@store', [
                'action'    => 'ItEquipmentController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->except(['password']),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al registrar el equipo de TI.', 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar el equipo.');
        }
    }

    /**
     * Actualiza un equipo de TI existente.
     *
     * @param ItEquipmentRequest $request
     * @param int|string $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(ItEquipmentRequest $request, $id)
    {
        try {
            $equipment = $this->equipmentRepo->update($id, $request->validated());

            if (!$equipment) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->errorResponse('Equipo de TI no encontrado para actualizar.', 404);
                }

                return redirect()->back()
                    ->with('error', 'Equipo no encontrado.');
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ItEquipmentResource($equipment),
                    'Registro actualizado correctamente.'
                );
            }

            return redirect()->route('sistemas-ti.inventario')
                ->with('success', 'Registro actualizado correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar equipo de TI en ItEquipmentController@update', [
                'action'    => 'ItEquipmentController@update',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->except(['password']),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al actualizar el equipo de TI.', 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el equipo.');
        }
    }

    /**
     * Elimina un equipo de TI existente.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            $deleted = $this->equipmentRepo->delete($id);

            if (!$deleted) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->errorResponse('Equipo de TI no encontrado para eliminar.', 404);
                }

                return redirect()->back()
                    ->with('error', 'Equipo no encontrado.');
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    [],
                    'Equipo eliminado correctamente.'
                );
            }

            return redirect()->route('sistemas-ti.inventario')
                ->with('success', 'Equipo eliminado correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar equipo de TI en ItEquipmentController@destroy', [
                'action'    => 'ItEquipmentController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al eliminar el equipo de TI.', 500);
            }

            return redirect()->back()
                ->with('error', 'Error al eliminar el equipo.');
        }
    }
}
