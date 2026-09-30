<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Material\MaterialRepository;
use App\Http\Requests\Material\MaterialRequest;
use App\Http\Resources\Material\MaterialResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class MaterialController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var MaterialRepository
     */
    protected MaterialRepository $materialRepo;

    /**
     * Constructor con inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param MaterialRepository $materialRepo
     */
    public function __construct(UtilResponse $utilResponse, MaterialRepository $materialRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->materialRepo = $materialRepo;
    }

    /**
     * Listado general de materiales o respuesta para DataTables / API.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                $materials = $this->materialRepo->all($request->only(['search']));
                $collection = MaterialResource::collection($materials);

                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 200,
                    'message' => 'Materiales obtenidos correctamente.',
                    'data'    => $collection,
                ], 200);
            }

            return view('laboratory.table_materials');
        } catch (Throwable $e) {
            Log::error('Error al listar materiales en MaterialController@index', [
                'action'    => 'MaterialController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar materiales de laboratorio.', 500);
            }

            abort(500, 'Error al consultar materiales de laboratorio.');
        }
    }

    /**
     * Obtiene los datos de un material para su edición modal.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function edit(Request $request, $id): JsonResponse
    {
        try {
            $material = $this->materialRepo->find($id);

            if (!$material) {
                return $this->utilResponse->errorResponse('Material no encontrado.', 404);
            }

            $resource = (new MaterialResource($material))->resolve();

            // Garantizamos compatibilidad total para consumidores frontend que leen directamente `data.name`, etc.
            // manteniendo a su vez la firma estándar en `data`.
            return response()->json(array_merge([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Material obtenido correctamente.',
                'data'    => $resource,
            ], $resource), 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar material para edición en MaterialController@edit', [
                'action'    => 'MaterialController@edit',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos del material.', 500);
        }
    }

    /**
     * Registra un nuevo material de laboratorio en transacción ACID.
     *
     * @param MaterialRequest $request
     * @return JsonResponse
     */
    public function store(MaterialRequest $request): JsonResponse
    {
        try {
            $material = $this->materialRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Material created successfully.',
                'data'    => new MaterialResource($material),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar material en MaterialController@store', [
                'action'    => 'MaterialController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar el material.', 500);
        }
    }

    /**
     * Actualiza un material calculando su stock restante con bloqueo pesimista.
     *
     * @param MaterialRequest $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function update(MaterialRequest $request, $id): JsonResponse
    {
        try {
            $material = $this->materialRepo->update($id, $request->validated());

            if (!$material) {
                return $this->utilResponse->errorResponse('Material no encontrado.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Record updated successfully.',
                'data'    => new MaterialResource($material),
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al actualizar material en MaterialController@update', [
                'action'    => 'MaterialController@update',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar el material.', 500);
        }
    }

    /**
     * Elimina un material de laboratorio con bloqueo pesimista.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $deleted = $this->materialRepo->delete($id);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('Material no encontrado.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Record removed.',
                'data'    => [],
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al eliminar material en MaterialController@destroy', [
                'action'    => 'MaterialController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar el material.', 500);
        }
    }
}