<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Nomina\NominaRepository;
use App\Http\Requests\Nomina\NominaStoreRequest;
use App\Http\Requests\Nomina\NominaUpdateRequest;
use App\Http\Resources\Nomina\NominaResource;
use App\Traits\UtilResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NominaController extends Controller
{
    protected UtilResponse $utilResponse;
    protected NominaRepository $nominaRepo;

    public function __construct(UtilResponse $utilResponse, NominaRepository $nominaRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->nominaRepo = $nominaRepo;
    }

    public function index(Request $request)
    {
        try {
            $data = $this->nominaRepo->getIndexData();

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse($data, 'Filtros y datos de nóminas');
            }

            return view('rh.nominas.index', $data);
        } catch (\Throwable $e) {
            Log::error('Error al cargar vista de nóminas', [
                'action'    => 'index',
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error interno al obtener datos de nómina', 500);
            }

            abort(500, 'Error interno del servidor');
        }
    }

    public function datatable(Request $request)
    {
        try {
            $nominas = $this->nominaRepo->getDatatable($request->all());

            return response()->json([
                'data' => NominaResource::collection($nominas),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener datos para datatable de nómina', [
                'action'    => 'datatable',
                'filters'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['data' => []], 500);
        }
    }

    public function store(NominaStoreRequest $request)
    {
        try {
            $userId = Auth::id() ?? 1;
            $nomina = $this->nominaRepo->store($request->validated(), $userId);

            return response()->json([
                'success' => true,
                'message' => 'Empleado registrado en nómina correctamente.',
                'data'    => new NominaResource($nomina),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error al guardar empleado en nómina', [
                'action'    => 'store',
                'user_id'   => Auth::id(),
                'payload'   => $request->except(['_token']),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar empleado en nómina: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $nomina = $this->nominaRepo->find((int) $id);

            if (!$nomina) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empleado de nómina no encontrado.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'nomina'  => new NominaResource($nomina),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al consultar empleado de nómina', [
                'action'    => 'show',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al consultar empleado.',
            ], 500);
        }
    }

    public function update(NominaUpdateRequest $request, $id)
    {
        try {
            $updated = $this->nominaRepo->update((int) $id, $request->validated());

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empleado de nómina no encontrado.',
                ], 404);
            }

            $nomina = $this->nominaRepo->find((int) $id);

            return response()->json([
                'success' => true,
                'message' => 'Nómina actualizada correctamente.',
                'data'    => new NominaResource($nomina),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar empleado de nómina', [
                'action'    => 'update',
                'id'        => $id,
                'payload'   => $request->except(['_token']),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar nómina: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->nominaRepo->delete((int) $id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empleado de nómina no encontrado.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Registro de nómina eliminado correctamente.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar registro de nómina', [
                'action'    => 'destroy',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar nómina.',
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            return $this->nominaRepo->exportExcel($request->all());
        } catch (\Throwable $e) {
            Log::error('Error al exportar nómina a Excel', [
                'action'    => 'exportExcel',
                'filters'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al exportar archivo Excel: ' . $e->getMessage(),
            ], 500);
        }
    }
}
