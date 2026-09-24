<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Concept\ConceptRepository;
use App\Http\Requests\Concept\StoreConceptRequest;
use App\Http\Requests\Concept\UpdateConceptRequest;
use App\Http\Resources\Concept\ConceptResource;
use App\Traits\UtilResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConceptController extends Controller
{
    private UtilResponse $utilResponse;
    private ConceptRepository $conceptRepository;

    public function __construct(UtilResponse $utilResponse, ConceptRepository $conceptRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->conceptRepository = $conceptRepository;
    }

    public function index(Request $request)
    {
        try {
            $search = $request->get('search');
            $concepts = $search
                ? $this->conceptRepository->paginate($search)
                : $this->conceptRepository->all();

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    ConceptResource::collection($concepts),
                    'Conceptos obtenidos exitosamente.'
                );
            }

            return response()->json($concepts);
        } catch (Throwable $e) {
            Log::error('Error fetching concepts', [
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener conceptos.', 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $concept = $this->conceptRepository->find((int) $id);

            if (!$concept) {
                return $this->utilResponse->errorResponse('Concepto no encontrado.', 404);
            }

            return $this->utilResponse->successResponse(
                new ConceptResource($concept),
                'Concepto obtenido exitosamente.'
            );
        } catch (Throwable $e) {
            Log::error('Error fetching concept', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener el concepto.', 500);
        }
    }

    public function store(StoreConceptRequest $request)
    {
        try {
            $concept = $this->conceptRepository->create($request->validated());

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ConceptResource($concept),
                    'Concepto creado exitosamente.',
                    201
                );
            }

            return redirect()->back()->with('success', 'Concepto creado exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error storing concept', [
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar concepto.', 500);
        }
    }

    public function update(UpdateConceptRequest $request, $id)
    {
        try {
            $concept = $this->conceptRepository->update((int) $id, $request->validated());

            if (!$concept) {
                return $this->utilResponse->errorResponse('Concepto no encontrado.', 404);
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ConceptResource($concept),
                    'Concepto actualizado exitosamente.'
                );
            }

            return redirect()->back()->with('success', 'Concepto actualizado exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error updating concept', [
                'id' => $id,
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar concepto.', 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $conceptId = (int) $id;

            if ($this->conceptRepository->hasClis($conceptId)) {
                return $this->utilResponse->errorResponse(
                    'No se puede eliminar el concepto porque tiene registros asociados.',
                    422
                );
            }

            $deleted = $this->conceptRepository->delete($conceptId);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('Concepto no encontrado.', 404);
            }

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(null, 'Concepto eliminado exitosamente.');
            }

            return redirect()->back()->with('success', 'Concepto eliminado exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error deleting concept', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar concepto.', 500);
        }
    }
}
