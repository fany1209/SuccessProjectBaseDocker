<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Xls\XlsRepository;
use App\Http\Requests\Xls\XlsReportsRequest;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class XlsController extends Controller
{
    protected UtilResponse $utilResponse;
    protected XlsRepository $xlsRepo;

    public function __construct(UtilResponse $utilResponse, XlsRepository $xlsRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->xlsRepo = $xlsRepo;
    }

    public function reports(XlsReportsRequest $request): StreamedResponse|JsonResponse
    {
        try {
            $year = $request->validated('year');
            $result = $this->xlsRepo->generateReportsSpreadsheet($year);

            $writer = new Xlsx($result['spreadsheet']);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $result['fileName'], [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Throwable $e) {
            Log::error('Error al exportar reporte de movimientos en Excel', [
                'action'  => 'XlsController@reports',
                'user_id' => auth()->id(),
                'payload' => $request->all(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al generar el reporte de movimientos en Excel.', 500);
        }
    }

    public function inventoryXls(): StreamedResponse|JsonResponse
    {
        try {
            $result = $this->xlsRepo->generateInventorySpreadsheet();

            $writer = new Xlsx($result['spreadsheet']);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $result['fileName'], [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Throwable $e) {
            Log::error('Error al exportar inventario en Excel', [
                'action'  => 'XlsController@inventoryXls',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al generar el reporte de inventario en Excel.', 500);
        }
    }

    public function proteinXls(): StreamedResponse|JsonResponse
    {
        try {
            $result = $this->xlsRepo->generateProteinSpreadsheet();

            $writer = new Xlsx($result['spreadsheet']);
            if (!empty($result['hasCharts'])) {
                $writer->setIncludeCharts(true);
            }

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $result['fileName'], [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        } catch (Throwable $e) {
            Log::error('Error al exportar reporte de proteína en Excel', [
                'action'  => 'XlsController@proteinXls',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al generar el reporte de proteína en Excel.', 500);
        }
    }
}
