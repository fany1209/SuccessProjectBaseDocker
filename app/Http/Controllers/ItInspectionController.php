<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ItInspection\ItInspectionRepository;
use App\Http\Requests\ItInspection\ItInspectionRequest;
use App\Http\Resources\ItEquipment\ItEquipmentResource;
use App\Http\Resources\ItInspection\ItInspectionResource;
use App\Models\ItEquipment;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ItInspectionController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var ItInspectionRepository
     */
    protected ItInspectionRepository $inspectionRepo;

    /**
     * Inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param ItInspectionRepository $inspectionRepo
     */
    public function __construct(UtilResponse $utilResponse, ItInspectionRepository $inspectionRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->inspectionRepo = $inspectionRepo;
    }

    /**
     * Listado de inspecciones de TI con filtro de búsqueda opcional.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search']);
            $inspections = $this->inspectionRepo->all($filters);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    ItInspectionResource::collection($inspections),
                    'Inspecciones de TI obtenidas correctamente.'
                );
            }

            return view('sistemas-ti.inspecciones.index', compact('inspections'));
        } catch (Throwable $e) {
            Log::error('Error al consultar inspecciones en ItInspectionController@index', [
                'action'    => 'ItInspectionController@index',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar las inspecciones de TI.', 500);
            }

            abort(500, 'Error al consultar las inspecciones de TI.');
        }
    }

    /**
     * Formulario de creación de inspección con generación de folio y listado de equipos.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $folio = $this->inspectionRepo->generateNextFolio();
            $equipments = ItEquipment::orderBy('article')->get();

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse([
                    'folio'      => $folio,
                    'equipments' => ItEquipmentResource::collection($equipments),
                ], 'Datos para nueva inspección obtenidos correctamente.');
            }

            return view('sistemas-ti.inspecciones.create', compact('folio', 'equipments'));
        } catch (Throwable $e) {
            Log::error('Error al preparar creación de inspección en ItInspectionController@create', [
                'action'    => 'ItInspectionController@create',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al preparar la creación de la inspección.', 500);
            }

            abort(500, 'Error al preparar la creación de la inspección.');
        }
    }

    /**
     * Muestra el detalle de una inspección específica.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse|View
     */
    public function show(Request $request, $id)
    {
        try {
            $inspection = $this->inspectionRepo->find($id);

            if (!$inspection) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->errorResponse('Inspección de TI no encontrada.', 404);
                }
                abort(404, 'Inspección de TI no encontrada.');
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ItInspectionResource($inspection),
                    'Inspección de TI obtenida correctamente.'
                );
            }

            return view('sistemas-ti.inspecciones.print', compact('inspection'));
        } catch (Throwable $e) {
            Log::error('Error al consultar inspección en ItInspectionController@show', [
                'action'    => 'ItInspectionController@show',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar la inspección.', 500);
            }

            abort(500, 'Error al consultar la inspección.');
        }
    }

    /**
     * Almacena una nueva inspección de TI bajo transacción ACID.
     *
     * @param ItInspectionRequest $request
     * @return JsonResponse|RedirectResponse
     */
    public function store(ItInspectionRequest $request)
    {
        try {
            $inspection = $this->inspectionRepo->create($request->validated());

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ItInspectionResource($inspection),
                    'Inspección guardada correctamente.',
                    201
                );
            }

            return redirect()->route('sistemas-ti.inspecciones.index')
                ->with('success', 'Inspección guardada correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar inspección en ItInspectionController@store', [
                'action'    => 'ItInspectionController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->except(['password']),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al guardar la inspección de TI.', 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al guardar la inspección.');
        }
    }

    /**
     * Actualiza una inspección existente aplicando bloqueo pesimista.
     *
     * @param ItInspectionRequest $request
     * @param int|string $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(ItInspectionRequest $request, $id)
    {
        try {
            $inspection = $this->inspectionRepo->update($id, $request->validated());

            if (!$inspection) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->errorResponse('Inspección de TI no encontrada para actualizar.', 404);
                }

                return redirect()->back()
                    ->with('error', 'Inspección no encontrada.');
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ItInspectionResource($inspection),
                    'Inspección actualizada correctamente.'
                );
            }

            return redirect()->route('sistemas-ti.inspecciones.index')
                ->with('success', 'Inspección actualizada correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar inspección en ItInspectionController@update', [
                'action'    => 'ItInspectionController@update',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->except(['password']),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al actualizar la inspección de TI.', 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la inspección.');
        }
    }

    /**
     * Elimina una inspección existente aplicando bloqueo pesimista.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            $deleted = $this->inspectionRepo->delete($id);

            if (!$deleted) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->errorResponse('Inspección de TI no encontrada para eliminar.', 404);
                }

                return redirect()->back()
                    ->with('error', 'Inspección no encontrada.');
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    [],
                    'Inspección eliminada correctamente.'
                );
            }

            return redirect()->route('sistemas-ti.inspecciones.index')
                ->with('success', 'Inspección eliminada correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar inspección en ItInspectionController@destroy', [
                'action'    => 'ItInspectionController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al eliminar la inspección de TI.', 500);
            }

            return redirect()->back()
                ->with('error', 'Error al eliminar la inspección.');
        }
    }

    /**
     * Vista de impresión / PDF de la cédula técnica de inspección.
     *
     * @param Request $request
     * @param int|string $id
     * @return View|JsonResponse
     */
    public function print(Request $request, $id)
    {
        try {
            $inspection = $this->inspectionRepo->find($id);

            if (!$inspection) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->errorResponse('Inspección no encontrada.', 404);
                }
                abort(404, 'Inspección no encontrada.');
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ItInspectionResource($inspection),
                    'Inspección obtenida para impresión.'
                );
            }

            return view('sistemas-ti.inspecciones.print', compact('inspection'));
        } catch (Throwable $e) {
            Log::error('Error al consultar inspección para imprimir en ItInspectionController@print', [
                'action'    => 'ItInspectionController@print',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al obtener la inspección para imprimir.', 500);
            }

            abort(500, 'Error al obtener la inspección para imprimir.');
        }
    }
}
