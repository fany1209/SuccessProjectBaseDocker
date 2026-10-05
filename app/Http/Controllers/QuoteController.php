<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Quote\QuoteRepository;
use App\Http\Requests\Quote\QuoteStatusUpdateRequest;
use App\Http\Requests\Quote\QuoteStoreRequest;
use App\Http\Requests\Quote\QuoteUpdateRequest;
use App\Http\Resources\Quote\QuoteResource;
use App\Traits\UtilResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuoteController extends Controller
{
    protected UtilResponse $utilResponse;
    protected QuoteRepository $quoteRepository;

    public function __construct(UtilResponse $utilResponse, QuoteRepository $quoteRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->quoteRepository = $quoteRepository;
    }

    public function index(Request $request)
    {
        try {
            $data = $this->quoteRepository->getIndexData();

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse([
                    'quotes'       => QuoteResource::collection($data['quotes']),
                    'sectors'      => $data['sectors'],
                    'db_products'  => $data['db_products'],
                    'db_customers' => $data['db_customers'],
                ], 'Cotizaciones recuperadas exitosamente');
            }

            return view('quotes', $data);
        } catch (\Throwable $e) {
            Log::error('Error al listar cotizaciones', [
                'action'    => 'index',
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error interno al obtener las cotizaciones', 500);
            }

            abort(500, 'Error interno del servidor');
        }
    }

    public function create(Request $request)
    {
        try {
            $data = $this->quoteRepository->getCreateData();

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse($data, 'Datos para creación de cotización');
            }

            return view('sales.quotes.form-create', $data);
        } catch (\Throwable $e) {
            Log::error('Error al cargar formulario de creación de cotización', [
                'action'    => 'create',
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error interno al preparar creación de cotización', 500);
            }

            abort(500, 'Error interno del servidor');
        }
    }

    public function store(QuoteStoreRequest $request)
    {
        try {
            $userId = Auth::id() ?? 1;
            $quote = $this->quoteRepository->store($request->validated(), $userId);

            return $this->utilResponse->successResponse([
                'quote' => new QuoteResource($quote),
                'folio' => $quote->folio,
            ], 'Cotización guardada', 201);
        } catch (\Throwable $e) {
            Log::error('Error al almacenar cotización', [
                'action'    => 'store',
                'user_id'   => Auth::id(),
                'payload'   => $request->except(['_token']),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al guardar la cotización: ' . $e->getMessage(), 500);
        }
    }

    public function show($id, Request $request)
    {
        try {
            $quote = $this->quoteRepository->find((int) $id);

            if (!$quote) {
                if ($request->ajax() || $request->wantsJson()) {
                    return $this->utilResponse->errorResponse('Cotización no encontrada', 404);
                }
                abort(404, 'Cotización no encontrada');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse(new QuoteResource($quote), 'Cotización consultada exitosamente');
            }

            return view('sales.quotes.show', compact('quote'));
        } catch (\Throwable $e) {
            Log::error('Error al consultar cotización', [
                'action'    => 'show',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error interno al consultar la cotización', 500);
            }

            abort(500, 'Error interno del servidor');
        }
    }

    public function edit($id, Request $request)
    {
        try {
            $editData = $this->quoteRepository->getEditData((int) $id);

            if (!$editData) {
                if ($request->ajax() || $request->wantsJson()) {
                    return $this->utilResponse->errorResponse('Cotización no encontrada', 404);
                }
                abort(404, 'Cotización no encontrada');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse([
                    'quote'        => new QuoteResource($editData['quote']),
                    'db_products'  => $editData['db_products'],
                    'statuses'     => $editData['statuses'],
                    'db_customers' => $editData['db_customers'],
                ], 'Datos para edición de cotización');
            }

            return view('sales.quotes.edit', $editData);
        } catch (\Throwable $e) {
            Log::error('Error al cargar formulario de edición de cotización', [
                'action'    => 'edit',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error interno al preparar la edición', 500);
            }

            abort(500, 'Error interno del servidor');
        }
    }

    public function update(QuoteUpdateRequest $request, $id)
    {
        try {
            $updated = $this->quoteRepository->update((int) $id, $request->validated());

            if (!$updated) {
                return $this->utilResponse->errorResponse('Cotización no encontrada', 404);
            }

            $quote = $this->quoteRepository->find((int) $id);

            return $this->utilResponse->successResponse(
                new QuoteResource($quote),
                'Cotización actualizada correctamente.'
            );
        } catch (\Throwable $e) {
            Log::error('Error al actualizar cotización', [
                'action'    => 'update',
                'id'        => $id,
                'payload'   => $request->except(['_token', '_method']),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $deleted = $this->quoteRepository->delete((int) $id);

            if (!$deleted) {
                if ($request->ajax() || $request->wantsJson()) {
                    return $this->utilResponse->errorResponse('Cotización no encontrada', 404);
                }
                return back()->withErrors('Cotización no encontrada');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse([], 'Cotización eliminada correctamente');
            }

            return redirect()->route('quotes')->with('success', 'Cotización eliminada correctamente');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar cotización', [
                'action'    => 'destroy',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al eliminar: ' . $e->getMessage(), 500);
            }

            return back()->withErrors('Error al eliminar: ' . $e->getMessage());
        }
    }

    public function updateStatus(QuoteStatusUpdateRequest $request, $id)
    {
        try {
            $updated = $this->quoteRepository->updateStatus((int) $id, (int) $request->validated()['quotes_status_id']);

            if (!$updated) {
                return $this->utilResponse->errorResponse('Cotización no encontrada', 404);
            }

            return $this->utilResponse->successResponse([], 'Estatus actualizado correctamente');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar estatus de cotización', [
                'action'    => 'updateStatus',
                'id'        => $id,
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar', 500);
        }
    }

    public function generatePdf($id)
    {
        try {
            $pdfData = $this->quoteRepository->getPdfData((int) $id);

            if (!$pdfData) {
                return response('Cotización no encontrada', 404);
            }

            $pdf = Pdf::loadView('formats.sales.quotes', $pdfData['viewData'])->setPaper('a4', 'portrait');

            if (app()->bound('debugbar')) {
                try {
                    app('debugbar')->disable();
                } catch (\Throwable $e) {
                }
            }

            if (!app()->environment('testing')) {
                @ini_set('zlib.output_compression', '0');
                while (ob_get_level() > 0) {
                    @ob_end_clean();
                }
            }

            $filename = $pdfData['quote']->folio . '.pdf';

            return $pdf->download($filename);
        } catch (\Throwable $e) {
            Log::error('Error generando PDF de cotización', [
                'action'    => 'generatePdf',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response('Error al generar el formato PDF: ' . $e->getMessage(), 500);
        }
    }
}