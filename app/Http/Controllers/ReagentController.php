<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Reagent\ReagentRepository;
use App\Http\Requests\Reagent\ReagentRequest;
use App\Http\Resources\Reagent\ReagentResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ReagentController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var ReagentRepository
     */
    protected ReagentRepository $reagentRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param ReagentRepository $reagentRepo
     */
    public function __construct(UtilResponse $utilResponse, ReagentRepository $reagentRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->reagentRepo = $reagentRepo;
    }

    /**
     * Listado general de reactivos o respuesta para DataTables / API.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                $reagents = $this->reagentRepo->all($request->only(['search']));
                $collection = ReagentResource::collection($reagents);

                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 200,
                    'message' => 'Reactivos obtenidos correctamente.',
                    'data'    => $collection,
                ], 200);
            }

            return view('laboratory.table_react');
        } catch (Throwable $e) {
            Log::error('Error al listar reactivos en ReagentController@index', [
                'action'    => 'ReagentController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar reactivos de laboratorio.', 500);
            }

            abort(500, 'Error al consultar reactivos de laboratorio.');
        }
    }

    /**
     * Obtiene los datos de un reactivo para su edición modal.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function edit(Request $request, $id): JsonResponse
    {
        try {
            $reagent = $this->reagentRepo->find($id);

            if (!$reagent) {
                return $this->utilResponse->errorResponse('Reactivo no encontrado.', 404);
            }

            $resource = (new ReagentResource($reagent))->resolve();

            // Garantizamos compatibilidad total para consumidores frontend que leen directamente `data.code`, etc.
            // manteniendo a su vez la firma estándar en `data`.
            return response()->json(array_merge([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Reactivo obtenido correctamente.',
                'data'    => $resource,
            ], $resource), 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar reactivo para edición en ReagentController@edit', [
                'action'    => 'ReagentController@edit',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos del reactivo.', 500);
        }
    }

    /**
     * Registra un nuevo reactivo de laboratorio en transacción ACID.
     *
     * @param ReagentRequest $request
     * @return JsonResponse
     */
    public function store(ReagentRequest $request): JsonResponse
    {
        try {
            $reagent = $this->reagentRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Reagent created successfully.',
                'data'    => new ReagentResource($reagent),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar reactivo en ReagentController@store', [
                'action'    => 'ReagentController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar el reactivo.', 500);
        }
    }

    /**
     * Actualiza un reactivo recalculando su stock restante con bloqueo pesimista.
     *
     * @param ReagentRequest $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function update(ReagentRequest $request, $id): JsonResponse
    {
        try {
            $reagent = $this->reagentRepo->update($id, $request->validated());

            if (!$reagent) {
                return $this->utilResponse->errorResponse('Reactivo no encontrado.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Record updated successfully.',
                'data'    => new ReagentResource($reagent),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al actualizar reactivo en ReagentController@update', [
                'action'    => 'ReagentController@update',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar el reactivo.', 500);
        }
    }

    /**
     * Elimina un reactivo de laboratorio con bloqueo pesimista.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $deleted = $this->reagentRepo->delete($id);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('Reactivo no encontrado.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Record removed.',
                'data'    => [],
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al eliminar reactivo en ReagentController@destroy', [
                'action'    => 'ReagentController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar el reactivo.', 500);
        }
    }
}