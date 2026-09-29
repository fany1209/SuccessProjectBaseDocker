<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsumoEntrada\InsumoEntradaRequest;
use App\Http\Repositories\InsumoEntrada\InsumoEntradaRepository;
use App\Http\Resources\InsumoEntrada\InsumoEntradaResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class InsumoEntradaController extends Controller
{
    private UtilResponse $utilResponse;
    private InsumoEntradaRepository $insumoEntradaRepo;

    public function __construct(UtilResponse $utilResponse, InsumoEntradaRepository $insumoEntradaRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->insumoEntradaRepo = $insumoEntradaRepo;
    }

    /**
     * Muestra el listado de entradas de insumos.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $entries = $this->insumoEntradaRepo->all();

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    InsumoEntradaResource::collection($entries),
                    'Entradas de insumos obtenidas correctamente.'
                );
            }

            return redirect()->route('purchases.index');
        } catch (Throwable $e) {
            Log::error('Error al consultar listado de entradas de insumos', [
                'action'    => 'InsumoEntradaController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Ocurrió un error inesperado al consultar las entradas.',
                    'error'   => 'Ocurrió un error inesperado al consultar las entradas.',
                    'data'    => [],
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al consultar las entradas.');
        }
    }

    /**
     * Muestra los detalles de una entrada de insumo específica.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $entry = $this->insumoEntradaRepo->find($id);

            if (!$entry) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Entrada no encontrada.',
                    'error'   => 'Entrada no encontrada.',
                    'data'    => [],
                ], 404);
            }

            $resource = new InsumoEntradaResource($entry);

            return response()->json(array_merge($resource->resolve(), [
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Entrada obtenida correctamente.',
                'entry'   => $resource,
                'data'    => $resource,
            ]), 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar entrada de insumo', [
                'action'    => 'InsumoEntradaController@show',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Ocurrió un error inesperado al consultar la entrada.',
                'error'   => 'Ocurrió un error inesperado al consultar la entrada.',
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Almacena una nueva entrada de insumos en la base de datos.
     *
     * @param InsumoEntradaRequest $request
     * @return JsonResponse|RedirectResponse
     */
    public function store(InsumoEntradaRequest $request)
    {
        try {
            $entry = $this->insumoEntradaRepo->create($request->validated());

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 201,
                    'message' => 'Entrada guardada correctamente.',
                    'entry'   => new InsumoEntradaResource($entry),
                    'data'    => new InsumoEntradaResource($entry),
                ], 201);
            }

            return back()->with('success', 'Entrada guardada correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar entrada de insumo', [
                'action'    => 'InsumoEntradaController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Ocurrió un error inesperado al registrar la entrada.',
                    'error'   => 'Ocurrió un error inesperado al registrar la entrada.',
                    'data'    => [],
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al registrar la entrada.');
        }
    }

    /**
     * Actualiza los datos de una entrada de insumos existente.
     *
     * @param InsumoEntradaRequest $request
     * @param int|string $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(InsumoEntradaRequest $request, $id)
    {
        try {
            $entry = $this->insumoEntradaRepo->find($id);

            if (!$entry) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'flag'    => false,
                        'code'    => 404,
                        'message' => 'Entrada no encontrada.',
                        'error'   => 'Entrada no encontrada.',
                        'data'    => [],
                    ], 404);
                }

                return back()->with('error', 'Entrada no encontrada.');
            }

            $updated = $this->insumoEntradaRepo->update($id, $request->validated());

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 200,
                    'message' => 'Entrada actualizada correctamente.',
                    'entry'   => new InsumoEntradaResource($updated),
                    'data'    => new InsumoEntradaResource($updated),
                ], 200);
            }

            return back()->with('success', 'Entrada actualizada correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar entrada de insumo', [
                'action'    => 'InsumoEntradaController@update',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Ocurrió un error inesperado al actualizar la entrada.',
                    'error'   => 'Ocurrió un error inesperado al actualizar la entrada.',
                    'data'    => [],
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al actualizar la entrada.');
        }
    }

    /**
     * Elimina una entrada de insumos de la base de datos.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            $entry = $this->insumoEntradaRepo->find($id);

            if (!$entry) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'flag'    => false,
                        'code'    => 404,
                        'message' => 'Entrada no encontrada.',
                        'error'   => 'Entrada no encontrada.',
                        'data'    => [],
                    ], 404);
                }

                return back()->with('error', 'Entrada no encontrada.');
            }

            $this->insumoEntradaRepo->delete($id);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 200,
                    'message' => 'Entrada eliminada correctamente.',
                    'data'    => [],
                ], 200);
            }

            return back()->with('success', 'Entrada eliminada correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar entrada de insumo', [
                'action'    => 'InsumoEntradaController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Ocurrió un error inesperado al eliminar la entrada.',
                    'error'   => 'Ocurrió un error inesperado al eliminar la entrada.',
                    'data'    => [],
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al eliminar la entrada.');
        }
    }
}
