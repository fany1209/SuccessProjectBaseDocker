<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Comparative\ComparativeRepository;
use App\Http\Requests\Comparative\StoreComparativeRequest;
use App\Http\Requests\Comparative\UpdateComparativeRequest;
use App\Http\Resources\Comparative\ComparativeResource;
use App\Traits\UtilResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ComparativeController extends Controller
{
    private UtilResponse $utilResponse;
    private ComparativeRepository $comparativeRepository;

    public function __construct(UtilResponse $utilResponse, ComparativeRepository $comparativeRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->comparativeRepository = $comparativeRepository;
    }

    public function index(Request $request)
    {
        try {
            $grupos = $this->comparativeRepository->getAllGroupedByFolio();
            $counts = $this->comparativeRepository->getRequisitionCounts(auth()->user()?->name);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->successResponse(
                    ComparativeResource::collection($this->comparativeRepository->all()),
                    'Comparativas obtenidas exitosamente.'
                );
            }

            $your_requisitions = $counts['your_requisitions'];
            $requisitions_count = $counts['requisitions_count'];
            $requisitions_no_check = $counts['requisitions_no_check'];

            $view = view()->exists('purchases.index') ? 'purchases.index' : 'purchases';

            return view($view, compact(
                'grupos',
                'your_requisitions',
                'requisitions_count',
                'requisitions_no_check'
            ));
        } catch (Throwable $e) {
            Log::error('Error fetching comparatives', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->errorResponse('Error al obtener comparativas.', 500);
            }

            return redirect()->back()->with('error', 'Error al obtener comparativas.');
        }
    }

    public function store(StoreComparativeRequest $request)
    {
        try {
            $userId = auth()->id() ?? 1;
            $result = $this->comparativeRepository->storeComparative($request->validated(), $userId);
            $folio = $result['folio'];

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->successResponse(
                    [
                        'folio' => $folio,
                        'items' => ComparativeResource::collection($result['items']),
                    ],
                    "Comparativa guardada con Folio: {$folio}",
                    201
                );
            }

            return redirect()->back()->with('success', "Comparativa guardada con Folio: {$folio}");
        } catch (Throwable $e) {
            Log::error('Error storing comparative', [
                'user_id' => auth()->id(),
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->errorResponse('Error al guardar comparativa: ' . $e->getMessage(), 500);
            }

            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroyByFolio(string $folio)
    {
        try {
            $deleted = $this->comparativeRepository->deleteByFolio($folio);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('El folio no existe.', 404);
            }

            return $this->utilResponse->successResponse(null, 'Comparativa eliminada exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error deleting comparative by folio', [
                'folio' => $folio,
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar comparativa.', 500);
        }
    }

    public function generatePDF(Request $request, string $folio)
    {
        try {
            $productos = $this->comparativeRepository->getByFolio($folio);

            if ($productos->isEmpty()) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return $this->utilResponse->errorResponse('No se encontraron datos.', 404);
                }

                return redirect()->back()->with('error', 'No se encontraron datos.');
            }

            $pdf = Pdf::loadView('formats.purchases.03', compact('productos', 'folio'));
            $pdf->getDomPDF()->set_option("isRemoteEnabled", true);
            $pdf->getDomPDF()->set_option("isHtml5ParserEnabled", true);

            return $pdf->setPaper('letter', 'landscape')->stream("Comparativa_{$folio}.pdf");
        } catch (Throwable $e) {
            Log::error('Error generating PDF for comparative', [
                'folio' => $folio,
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->errorResponse('Error al generar PDF de la comparativa.', 500);
            }

            return redirect()->back()->with('error', 'Error al generar PDF.');
        }
    }

    public function updateAll(UpdateComparativeRequest $request)
    {
        try {
            $userId = auth()->id() ?? 1;
            $folio = $this->comparativeRepository->updateAll($request->validated(), $userId);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->successResponse(
                    ['folio' => $folio],
                    '¡La comparativa ha sido actualizada correctamente!'
                );
            }

            return redirect()->back()->with('success', '¡La comparativa ha sido actualizada correctamente!');
        } catch (Throwable $e) {
            Log::error('Error updating all comparative items', [
                'user_id' => auth()->id(),
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->errorResponse('Hubo un problema al guardar los cambios.', 500);
            }

            return redirect()->back()->with('error', 'Hubo un problema al guardar los cambios: ' . $e->getMessage());
        }
    }
}