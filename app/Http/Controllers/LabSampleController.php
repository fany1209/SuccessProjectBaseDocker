<?php

namespace App\Http\Controllers;

use App\Http\Repositories\LabSample\LabSampleRepository;
use App\Http\Requests\LabSample\LabSampleRequest;
use App\Http\Resources\LabSample\LabSampleResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class LabSampleController extends Controller
{
    /**
     * @var UtilResponse
     */
    protected UtilResponse $utilResponse;

    /**
     * @var LabSampleRepository
     */
    protected LabSampleRepository $labSampleRepo;

    /**
     * Inyección exclusiva de UtilResponse y Repositorio.
     *
     * @param UtilResponse $utilResponse
     * @param LabSampleRepository $labSampleRepo
     */
    public function __construct(UtilResponse $utilResponse, LabSampleRepository $labSampleRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->labSampleRepo = $labSampleRepo;
    }

    /**
     * Muestra la vista principal o listado JSON de muestras de laboratorio.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if ($request->expectsJson() || $request->is('api/*')) {
                $samples = $this->labSampleRepo->all($request->only(['search', 'status']));
                return $this->utilResponse->successResponse(
                    LabSampleResource::collection($samples),
                    'Muestras de laboratorio obtenidas correctamente.'
                );
            }

            if (view()->exists('laboratory.samples.index')) {
                return view('laboratory.samples.index');
            }

            return view('formats.laboratory.inv_samples');
        } catch (Throwable $e) {
            Log::error('Error al listar muestras en LabSampleController@index', [
                'action'    => 'LabSampleController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->errorResponse('Error al consultar muestras de laboratorio.', 500);
            }

            abort(500, 'Error al consultar muestras de laboratorio.');
        }
    }

    /**
     * Retorna los datos procesados para el componente DataTables.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function datatable(Request $request): JsonResponse
    {
        try {
            $rows = $this->labSampleRepo->getDatatableRows();
            return response()->json(['data' => $rows]);
        } catch (Throwable $e) {
            Log::error('Error al generar DataTables en LabSampleController@datatable', [
                'action'    => 'LabSampleController@datatable',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener datos de tabla de laboratorio.', 500);
        }
    }

    /**
     * Retorna el detalle de una muestra de laboratorio específica.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function showLabSample(Request $request, $id): JsonResponse
    {
        try {
            $sample = $this->labSampleRepo->find($id);

            if (!$sample) {
                return $this->utilResponse->errorResponse('Registro no encontrado.', 404);
            }

            return $this->utilResponse->successResponse(
                new LabSampleResource($sample),
                'Registro obtenido exitosamente.'
            );
        } catch (Throwable $e) {
            Log::error('Error al consultar muestra en LabSampleController@showLabSample', [
                'action'    => 'LabSampleController@showLabSample',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar el registro.', 500);
        }
    }

    /**
     * Actualiza una muestra de laboratorio existente con bloqueo pesimista.
     *
     * @param LabSampleRequest $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function updateLabSample(LabSampleRequest $request, $id): JsonResponse
    {
        try {
            $sample = $this->labSampleRepo->update($id, $request->validated());

            if (!$sample) {
                return $this->utilResponse->errorResponse('Registro no encontrado.', 404);
            }

            return $this->utilResponse->successResponse(
                new LabSampleResource($sample),
                'Registro actualizado.'
            );
        } catch (Throwable $e) {
            Log::error('Error al actualizar muestra en LabSampleController@updateLabSample', [
                'action'    => 'LabSampleController@updateLabSample',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar el registro.', 500);
        }
    }

    /**
     * Elimina una muestra de laboratorio existente con bloqueo pesimista.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $deleted = $this->labSampleRepo->delete($id);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('Not found', 404);
            }

            return $this->utilResponse->successResponse([], 'Deleted', 200);
        } catch (Throwable $e) {
            Log::error('Error al eliminar muestra en LabSampleController@destroy', [
                'action'    => 'LabSampleController@destroy',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar el registro.', 500);
        }
    }
}
