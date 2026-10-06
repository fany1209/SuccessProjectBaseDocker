<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Minuta\MinutaRepository;
use App\Http\Requests\Minuta\MinutaStoreRequest;
use App\Http\Requests\Minuta\MinutaUpdateRequest;
use App\Http\Resources\Minuta\MinutaResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MinutaController extends Controller
{
    protected UtilResponse $utilResponse;
    protected MinutaRepository $minutaRepository;

    public function __construct(UtilResponse $utilResponse, MinutaRepository $minutaRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->minutaRepository = $minutaRepository;
    }

    public function index(): View
    {
        $total_minutas = $this->minutaRepository->countTotal();
        return view('minutas', compact('total_minutas'));
    }

    public function getMinutas(Request $request): JsonResponse
    {
        try {
            $minutas = $this->minutaRepository->getMinutas($request->input('status'), $request->input('search'));

            return response()->json([
                'minutas' => MinutaResource::collection($minutas),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al consultar lista de minutas', [
                'action' => 'getMinutas',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['minutas' => []], 500);
        }
    }

    public function store(MinutaStoreRequest $request): JsonResponse
    {
        try {
            $minuta = $this->minutaRepository->create($request->all(), auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'Minuta guardada con éxito',
                'data' => new MinutaResource($minuta),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error en creación de minuta', [
                'action' => 'store',
                'user_id' => auth()->id(),
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al crear: ' . $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $minuta = $this->minutaRepository->find((int) $id);

            if (!$minuta) {
                return response()->json(['error' => 'No se encontró la minuta.'], 404);
            }

            return response()->json([
                'minuta' => new MinutaResource($minuta),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al consultar detalle de minuta', [
                'action' => 'show',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'No se encontró la minuta.'], 404);
        }
    }

    public function update(MinutaUpdateRequest $request, $id): JsonResponse
    {
        try {
            $minuta = $this->minutaRepository->update((int) $id, $request->all(), auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'Minuta actualizada',
                'data' => new MinutaResource($minuta),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar minuta', [
                'action' => 'update',
                'id' => $id,
                'user_id' => auth()->id(),
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->minutaRepository->delete((int) $id);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar minuta', [
                'action' => 'destroy',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al eliminar.'], 500);
        }
    }

    public function downloadPDF($id): Response|JsonResponse
    {
        try {
            return $this->minutaRepository->generatePdf((int) $id);
        } catch (\Throwable $e) {
            Log::error('Error al generar PDF de minuta', [
                'action' => 'downloadPDF',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al generar el PDF de la minuta.'], 500);
        }
    }
}