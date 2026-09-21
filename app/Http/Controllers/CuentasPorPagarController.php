<?php

namespace App\Http\Controllers;

use App\Http\Repositories\CuentasPorPagar\CuentasPorPagarRepository;
use App\Http\Requests\CuentasPorPagar\CxpPaymentRequest;
use App\Http\Requests\CuentasPorPagar\UpdateCxpDetailRequest;
use App\Http\Requests\CuentasPorPagar\UploadCxpDocumentsRequest;
use App\Http\Resources\CuentasPorPagar\CxpDetailResource;
use App\Http\Resources\CuentasPorPagar\CxpPaymentResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuentasPorPagarController extends Controller
{
    private UtilResponse $utilResponse;
    private CuentasPorPagarRepository $cxpRepository;

    public function __construct(UtilResponse $utilResponse, CuentasPorPagarRepository $cxpRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->cxpRepository = $cxpRepository;
    }

    public function index(Request $request)
    {
        try {
            $pendingPaymentsCount = $this->cxpRepository->getPendingOverdueCount();

            if ($request->expectsJson()) {
                return $this->utilResponse->successResponse(
                    ['pendingPaymentsCount' => $pendingPaymentsCount],
                    'Resumen de cuentas por pagar obtenido correctamente'
                );
            }

            return view('finance.cuentas_por_pagar.index', compact('pendingPaymentsCount'));
        } catch (\Throwable $e) {
            Log::error('Error al obtener índice de Cuentas Por Pagar: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            if ($request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al obtener datos de cuentas por pagar', 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar las cuentas por pagar.');
        }
    }

    public function dashboard(Request $request)
    {
        try {
            $filters = [
                'month'  => $request->input('month', now()->month),
                'year'   => $request->input('year', now()->year),
                'semana' => $request->input('semana'),
            ];

            $metrics = $this->cxpRepository->getDashboardMetrics($filters);

            if ($request->expectsJson()) {
                return $this->utilResponse->successResponse(
                    $metrics,
                    'Métricas de dashboard obtenidas correctamente'
                );
            }

            return view('finance.cuentas_por_pagar.dashboard', $metrics);
        } catch (\Throwable $e) {
            Log::error('Error al generar dashboard de Cuentas Por Pagar: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            if ($request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al cargar métricas del dashboard', 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar el dashboard.');
        }
    }

    public function facturas()
    {
        return view('finance.cuentas_por_pagar.facturas');
    }

  
    public function datatable(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['month', 'year', 'semana']);
            $facturas = $this->cxpRepository->getDatatable($filters);

            return response()->json(['data' => $facturas]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener datos de datatable en Cuentas Por Pagar: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json(['data' => [], 'error' => 'Error al cargar registros'], 500);
        }
    }

    public function update(UpdateCxpDetailRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $image = $request->file('comentario_img');
            $departamento = $request->input('departamento');

            $updated = $this->cxpRepository->updateDetail($id, $validated, $image, $departamento);

            if (!$updated) {
                return $this->utilResponse->errorResponse('No se puede editar una cuenta cancelada o inexistente.', 403);
            }

            return $this->utilResponse->successResponse(
                new CxpDetailResource($updated),
                'Actualizado correctamente'
            );
        } catch (\Throwable $e) {
            Log::error('Error al actualizar cuenta por pagar: ' . $e->getMessage(), [
                'cxp_id'    => $id,
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error interno al actualizar la cuenta por pagar', 500);
        }
    }

    /**
     * Delete comment image attached to account.
     */
    public function deleteComentarioImg($id): JsonResponse
    {
        try {
            $deleted = $this->cxpRepository->deleteComentarioImage($id);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('Cuenta no encontrada.', 404);
            }

            return $this->utilResponse->successResponse(null, 'Imagen eliminada correctamente');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar imagen de comentario en Cuentas Por Pagar: ' . $e->getMessage(), [
                'cxp_id'    => $id,
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar la imagen', 500);
        }
    }

    /**
     * Cancel an account payable.
     */
    public function cancel($id): JsonResponse
    {
        try {
            $canceled = $this->cxpRepository->cancelAccount($id);

            if (!$canceled) {
                return $this->utilResponse->errorResponse('Cuenta no encontrada.', 404);
            }

            return $this->utilResponse->successResponse(
                new CxpDetailResource($canceled),
                'Cuenta cancelada correctamente'
            );
        } catch (\Throwable $e) {
            Log::error('Error al cancelar cuenta por pagar: ' . $e->getMessage(), [
                'cxp_id'    => $id,
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error interno al cancelar la cuenta', 500);
        }
    }

    /**
     * List all payments for a given account.
     */
    public function getPayments($id): JsonResponse
    {
        try {
            $payments = $this->cxpRepository->getPaymentsByCxpId($id);

            return response()->json([
                'success'  => true,
                'payments' => CxpPaymentResource::collection($payments),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener abonos de cuenta por pagar: ' . $e->getMessage(), [
                'cxp_id'    => $id,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los abonos',
            ], 500);
        }
    }

    /**
     * Add a payment to an account.
     */
    public function addPayment(CxpPaymentRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $comprobante = $request->file('comprobante');

            $payment = $this->cxpRepository->addPayment($id, $validated, $comprobante);

            if (!$payment) {
                return $this->utilResponse->errorResponse('No se pueden añadir pagos a una cuenta cancelada o inexistente.', 403);
            }

            return $this->utilResponse->successResponse(
                new CxpPaymentResource($payment),
                'Abono añadido correctamente',
                201
            );
        } catch (\Throwable $e) {
            Log::error('Error al añadir abono a cuenta por pagar: ' . $e->getMessage(), [
                'cxp_id'    => $id,
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error interno al registrar el abono', 500);
        }
    }

    /**
     * Update an existing payment.
     */
    public function updatePayment(CxpPaymentRequest $request, $payment_id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $comprobante = $request->file('comprobante');

            $payment = $this->cxpRepository->updatePayment($payment_id, $validated, $comprobante);

            if (!$payment) {
                return $this->utilResponse->errorResponse('No se pueden editar pagos de una cuenta cancelada o inexistente.', 403);
            }

            return $this->utilResponse->successResponse(
                new CxpPaymentResource($payment),
                'Abono actualizado correctamente'
            );
        } catch (\Throwable $e) {
            Log::error('Error al actualizar abono en Cuentas Por Pagar: ' . $e->getMessage(), [
                'payment_id' => $payment_id,
                'exception'  => $e,
            ]);

            return $this->utilResponse->errorResponse('Error interno al actualizar el abono', 500);
        }
    }

    /**
     * Delete an existing payment.
     */
    public function deletePayment($payment_id): JsonResponse
    {
        try {
            $deleted = $this->cxpRepository->deletePayment($payment_id);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('No se pueden eliminar pagos de una cuenta cancelada o inexistente.', 403);
            }

            return $this->utilResponse->successResponse(null, 'Abono eliminado correctamente');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar abono en Cuentas Por Pagar: ' . $e->getMessage(), [
                'payment_id' => $payment_id,
                'exception'  => $e,
            ]);

            return $this->utilResponse->errorResponse('Error interno al eliminar el abono', 500);
        }
    }

    /**
     * Upload fiscal PDF / XML documents for account.
     */
    public function uploadDocuments(UploadCxpDocumentsRequest $request, $id): JsonResponse
    {
        try {
            $pdf = $request->file('pdf') ?? $request->file('pdf_file');
            $xml = $request->file('xml') ?? $request->file('xml_file');

            if (!$pdf && !$xml) {
                return $this->utilResponse->errorResponse('No se adjuntó ningún archivo válido', 400);
            }

            $cxp = $this->cxpRepository->uploadDocuments($id, $pdf, $xml);

            if (!$cxp) {
                return $this->utilResponse->errorResponse('Cuenta no encontrada.', 404);
            }

            return $this->utilResponse->successResponse(
                new CxpDetailResource($cxp),
                'Documentos subidos correctamente'
            );
        } catch (\Throwable $e) {
            Log::error('Error al subir documentos en Cuentas Por Pagar: ' . $e->getMessage(), [
                'cxp_id'    => $id,
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error interno al subir documentos', 500);
        }
    }

    /**
     * Export styled Excel spreadsheet of accounts payable.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $filters = $request->only(['month', 'year', 'semana']);
        $excelData = $this->cxpRepository->getFacturasForExcel($filters);

        $facturas = $excelData['facturas'];
        $paymentsGrouped = $excelData['paymentsGrouped'];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cuentas por Pagar');

        $semana = $request->input('semana');
        $year = $request->input('year');

        // Dynamic Title
        $titleStr = 'PROGRAMACION DE PAGOS';
        if ($semana && $year) {
            $titleStr .= ' SEMANA ' . $semana . '-' . $year;
        }

        $sheet->setCellValue('A1', $titleStr);
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font'      => [
                'bold'  => true,
                'size'  => 14,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF92D050'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Header Row (Row 2)
        $headers = ['Empresa', 'Semana Fiscal', 'Cantidad', 'Restante', 'Motivo', 'Banco', 'Factura', 'Fecha de factura', 'Fecha de pago', 'Estatus', 'Comentarios', 'Departamento'];
        $columnLetter = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnLetter . '2', $header);
            $columnLetter++;
        }

        $lastCol = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'font'      => [
                'bold'  => true,
                'italic'=> true,
                'color' => ['argb' => Color::COLOR_BLACK],
            ],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFA9D08E'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $row = 3;
        foreach ($facturas as $factura) {
            $total = round($factura->total, 2);
            $factura->is_canceled = (bool) $factura->is_canceled;

            $pagos = $paymentsGrouped[$factura->cxp_id] ?? collect();
            $pagado = $pagos->sum('amount');
            $restante = round($total - $pagado, 2);
            $bancosUsados = $pagos->pluck('banco')->filter()->unique()->implode(', ') ?: ($factura->banco ?: '—');

            $sheet->setCellValue('A' . $row, $factura->empresa);
            $sheet->setCellValue('B' . $row, $factura->semana ? 'Semana ' . $factura->semana : '—');
            $sheet->setCellValue('C' . $row, $total);
            $sheet->setCellValue('D' . $row, $factura->estatus === 'PAGADO' ? 0 : $restante);
            $sheet->setCellValue('E' . $row, $factura->motivo);
            $sheet->setCellValue('F' . $row, $bancosUsados . ($factura->metodo_pago ? ' / ' . $factura->metodo_pago : ''));
            $sheet->setCellValue('G' . $row, $factura->folio_factura);
            $sheet->setCellValue('H' . $row, $factura->fecha_factura);
            $sheet->setCellValue('I' . $row, $factura->fecha_pago);

            $estatusCell = 'J' . $row;
            if ($factura->is_canceled) {
                $sheet->setCellValue($estatusCell, 'CANCELADO');
                $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB(Color::COLOR_RED);
                $sheet->getStyle($estatusCell)->getFont()->setBold(true);
            } else {
                $sheet->setCellValue($estatusCell, strtoupper($factura->estatus));
                if ($factura->estatus === 'PAGADO') {
                    $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB('FF157347');
                } elseif ($factura->estatus === 'PENDIENTE') {
                    $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB('FF0000FF');
                } elseif ($factura->estatus === 'PARCIAL') {
                    $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB('FFF59E0B');
                }
            }

            $comentariosText = $factura->comentarios;
            $sheet->setCellValue('K' . $row, $comentariosText);

            if ($factura->comentario_img) {
                $ext = strtolower(pathinfo($factura->comentario_img, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $imagePath = public_path($factura->comentario_img);
                    if (file_exists($imagePath)) {
                        $drawing = new Drawing();
                        $drawing->setName('Comentario Adjunto');
                        $drawing->setDescription('Imagen del comentario');
                        $drawing->setPath($imagePath);
                        $drawing->setCoordinates('K' . $row);
                        $drawing->setHeight(60);

                        if ($comentariosText) {
                            $drawing->setOffsetY(20);
                            $sheet->getRowDimension($row)->setRowHeight(80);
                        } else {
                            $drawing->setOffsetY(5);
                            $sheet->getRowDimension($row)->setRowHeight(70);
                        }

                        $drawing->setOffsetX(60);
                        $drawing->setWorksheet($sheet);
                    }
                }
            }

            $sheet->setCellValue('L' . $row, $factura->departamento);

            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);

            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFCCCCCC'],
                    ],
                ],
            ]);

            $sheet->getStyle('A' . $row . ':L' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K' . $row)->getAlignment()->setWrapText(true);

            $row++;
        }

        foreach (range('A', 'L') as $col) {
            if ($col === 'K') {
                $sheet->getColumnDimension($col)->setWidth(35);
            } else {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Cuentas_Por_Pagar_' . date('Y-m-d') . '.xlsx';

        if (ob_get_length()) {
            ob_end_clean();
        }

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
