<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Fumigacion\FumigacionRepository;
use App\Http\Requests\Fumigacion\FumigacionRequest;
use App\Http\Resources\Fumigacion\FumigacionResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class FumigacionController extends Controller
{
    protected UtilResponse $utilResponse;
    protected FumigacionRepository $fumigacionRepo;

    public function __construct(UtilResponse $utilResponse, FumigacionRepository $fumigacionRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->fumigacionRepo = $fumigacionRepo;
    }

    public function index(Request $request): View|JsonResponse
    {
        try {
            $todasLasFumigaciones = $this->fumigacionRepo->all();
            $eventos = $this->fumigacionRepo->getCalendarEvents($todasLasFumigaciones);

            if ($request->expectsJson()) {
                return $this->utilResponse->successResponse(
                    FumigacionResource::collection($todasLasFumigaciones),
                    'Listado de fumigaciones obtenido correctamente',
                    200
                );
            }

            return view('quality.fumigaciones.index', [
                'todasLasFumigaciones' => $todasLasFumigaciones,
                'eventos'              => $eventos,
            ]);
        } catch (Throwable $e) {
            Log::error('Error al listar fumigaciones en FumigacionController@index', [
                'action'  => 'FumigacionController@index',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al consultar las fumigaciones.', 500);
            }

            abort(500, 'Error al cargar el panel de control de fumigaciones');
        }
    }

    public function store(FumigacionRequest $request): JsonResponse
    {
        try {
            $fumigacion = $this->fumigacionRepo->create($request->validated());

            return $this->utilResponse->successResponse(
                new FumigacionResource($fumigacion),
                'Fumigación programada correctamente.',
                201
            );
        } catch (Throwable $e) {
            Log::error('Error al programar fumigación en FumigacionController@store', [
                'action'  => 'FumigacionController@store',
                'payload' => $request->all(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al programar la fumigación.', 500);
        }
    }

    public function update(FumigacionRequest $request, int $id): JsonResponse
    {
        try {
            $fumigacion = $this->fumigacionRepo->update($id, $request->validated());

            if (!$fumigacion) {
                return $this->utilResponse->errorResponse('Fumigación no encontrada.', 404);
            }

            return $this->utilResponse->successResponse(
                new FumigacionResource($fumigacion),
                'Fumigación actualizada correctamente.',
                200
            );
        } catch (Throwable $e) {
            Log::error('Error al actualizar fumigación en FumigacionController@update', [
                'action'        => 'FumigacionController@update',
                'fumigacion_id' => $id,
                'payload'       => $request->all(),
                'user_id'       => auth()->id(),
                'error'         => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al actualizar la fumigación.', 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $deleted = $this->fumigacionRepo->delete($id);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('Fumigación no encontrada.', 404);
            }

            return $this->utilResponse->successResponse(
                [],
                'Eliminado correctamente.',
                200
            );
        } catch (Throwable $e) {
            Log::error('Error al eliminar fumigación en FumigacionController@destroy', [
                'action'        => 'FumigacionController@destroy',
                'fumigacion_id' => $id,
                'user_id'       => auth()->id(),
                'error'         => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al eliminar la fumigación.', 500);
        }
    }
}