<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Certificate\CertificateRepository;
use App\Http\Resources\Certificate\CertificateResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class CertificateApiController extends Controller
{
    private UtilResponse $utilResponse;
    private CertificateRepository $certificateRepository;

    public function __construct(UtilResponse $utilResponse, CertificateRepository $certificateRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->certificateRepository = $certificateRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $certificates = $this->certificateRepository->all();

            return $this->utilResponse->successResponse(
                CertificateResource::collection($certificates),
                'Certificados de calidad obtenidos correctamente'
            );
        } catch (Throwable $e) {
            Log::error('Error al consultar certificados de calidad: ' . $e->getMessage(), [
                'action'    => 'CertificateApiController@index',
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error al consultar los certificados de calidad.', 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            if (!ctype_digit((string) $id)) {
                return $this->utilResponse->errorResponse('Identificador de certificado inválido.', 422);
            }

            $deleted = $this->certificateRepository->delete((int) $id);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('Certificado de calidad no encontrado o ya eliminado.', 404);
            }

            return $this->utilResponse->successResponse([
                'ok'      => true,
                'deleted' => 1,
            ], 'Certificado de calidad eliminado exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar certificado de calidad: ' . $e->getMessage(), [
                'action'    => 'CertificateApiController@destroy',
                'id'        => $id,
                'exception' => $e,
            ]);

            return $this->utilResponse->errorResponse('Error interno al eliminar el certificado de calidad.', 500);
        }
    }

    public function export(): StreamedResponse
    {
        $rows = $this->certificateRepository->getForExport();
        $filename = 'certificados_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control'       => 'no-store, no-cache',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');

            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, [
                'ID', 'Folio', 'Fecha', 'Cliente', 'Producto', 'Lote', 'Cantidad',
                'No. Tarimas', 'Fecha salida CEDIS', 'Certificado',
                'Tipo (Muestra/PF)', 'Creado en'
            ]);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->id,
                    $r->folio,
                    $r->fecha?->format('Y-m-d') ?? $r->fecha,
                    $r->cliente,
                    $r->producto,
                    $r->lote,
                    $r->cantidad,
                    $r->no_tarimas,
                    $r->fecha_salida_cedis?->format('Y-m-d') ?? $r->fecha_salida_cedis,
                    $r->certificado_tarima,
                    $r->muestra_o_pf,
                    $r->created_at?->format('d-m-Y H:i:s'),
                ]);
            }

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
