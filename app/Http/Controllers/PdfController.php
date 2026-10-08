<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Pdf\PdfRepository;
use App\Traits\UtilResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class PdfController extends Controller
{
    protected UtilResponse $utilResponse;
    protected PdfRepository $pdfRepo;

    public function __construct(UtilResponse $utilResponse, PdfRepository $pdfRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->pdfRepo = $pdfRepo;
    }

    public function downloadPDF(Request $request, string $movType, int|string $movId): Response|JsonResponse
    {
        try {
            $includeComment = $request->query('include_comment') !== '0';
            $config = $this->pdfRepo->getMovementPdfData($movType, (int) $movId, $includeComment);

            $pdf = Pdf::loadView($config['view'], $config['data']);

            return $pdf->stream($config['fileName']);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Movimiento no encontrado.', 404);
        } catch (InvalidArgumentException $e) {
            return $this->utilResponse->errorResponse($e->getMessage(), 400);
        } catch (Throwable $e) {
            Log::error('Error al generar PDF de movimiento', [
                'action'  => 'PdfController@downloadPDF',
                'user_id' => auth()->id(),
                'type'    => $movType,
                'id'      => $movId,
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al generar el documento PDF.', 500);
        }
    }

    public function makeTemperaturePDF(
        int|string $week_a,
        int|string $week_b,
        int|string $year,
        int|string $warehouse_id
    ): Response|JsonResponse {
        try {
            $weekA = filter_var($week_a, FILTER_VALIDATE_INT);
            $weekB = filter_var($week_b, FILTER_VALIDATE_INT);
            $yearInt = filter_var($year, FILTER_VALIDATE_INT);
            $whId = filter_var($warehouse_id, FILTER_VALIDATE_INT);

            if ($weekA === false || $weekB === false || $yearInt === false || $whId === false) {
                return $this->utilResponse->errorResponse('Los parámetros del reporte deben ser números enteros válidos.', 400);
            }

            $config = $this->pdfRepo->getTemperaturePdfData((int) $weekA, (int) $weekB, (int) $yearInt, (int) $whId);

            $pdf = Pdf::loadView($config['view'], $config['data'])
                ->setPaper($config['paper'][0], $config['paper'][1]);

            return $pdf->stream($config['fileName']);
        } catch (InvalidArgumentException $e) {
            return $this->utilResponse->errorResponse($e->getMessage(), 400);
        } catch (Throwable $e) {
            Log::error('Error al generar reporte de temperatura en PDF', [
                'action'  => 'PdfController@makeTemperaturePDF',
                'user_id' => auth()->id(),
                'params'  => compact('week_a', 'week_b', 'year', 'warehouse_id'),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al generar el reporte de temperatura.', 500);
        }
    }

    public function makeDeliveryNotePDF(int|string $sale_id): Response|JsonResponse
    {
        try {
            $config = $this->pdfRepo->getDeliveryNotePdfData((int) $sale_id);

            $pdf = Pdf::loadView($config['view'], $config['data'])
                ->setPaper($config['paper'][0], $config['paper'][1]);

            return $pdf->stream($config['fileName']);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Venta no encontrada.', 404);
        } catch (Throwable $e) {
            Log::error('Error al generar Delivery Note PDF', [
                'action'  => 'PdfController@makeDeliveryNotePDF',
                'user_id' => auth()->id(),
                'sale_id' => $sale_id,
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al generar la nota de entrega.', 500);
        }
    }

    public function makeQuotePDF(int|string $quote_id): Response|JsonResponse
    {
        try {
            $config = $this->pdfRepo->getQuotePdfData((int) $quote_id);

            $pdf = Pdf::loadView($config['view'], $config['data']);

            return $pdf->stream($config['fileName']);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Cotización no encontrada.', 404);
        } catch (Throwable $e) {
            Log::error('Error al generar PDF de cotización', [
                'action'   => 'PdfController@makeQuotePDF',
                'user_id'  => auth()->id(),
                'quote_id' => $quote_id,
                'error'    => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al generar la cotización.', 500);
        }
    }

    public function makeRequisitionPDF(int|string $requisition_id): Response|JsonResponse
    {
        try {
            $config = $this->pdfRepo->getRequisitionPdfData((int) $requisition_id);

            $pdf = Pdf::setOptions($config['options'])
                ->loadView($config['view'], $config['data']);

            return $pdf->stream($config['fileName']);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Requisición no encontrada.', 404);
        } catch (Throwable $e) {
            Log::error('Error al generar PDF de requisición', [
                'action'         => 'PdfController@makeRequisitionPDF',
                'user_id'        => auth()->id(),
                'requisition_id' => $requisition_id,
                'error'          => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al generar el formato de requisición.', 500);
        }
    }
}
