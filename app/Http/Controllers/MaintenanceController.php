<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Maintenance\MaintenanceRepository;
use App\Http\Requests\Maintenance\CompleteMaintenanceRequest;
use App\Http\Resources\Maintenance\MaintenanceRecordResource;
use App\Models\MaintenanceRecord;
use App\Traits\UtilResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MaintenanceController extends Controller
{
    private $utilResponse;
    private $maintenanceRepository;

    public function __construct(UtilResponse $utilResponse, MaintenanceRepository $maintenanceRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->maintenanceRepository = $maintenanceRepository;
    }

    public function index()
    {
        try {
            $records = $this->maintenanceRepository->getPendingRecords();

            if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                return $this->utilResponse->successResponse(
                    MaintenanceRecordResource::collection($records),
                    'Registros de mantenimiento pendientes obtenidos correctamente'
                );
            }

            return view('maintenance.index', compact('records'));
        } catch (Throwable $e) {
            Log::error('Error al consultar alertas pendientes de mantenimiento', [
                'action'    => 'MaintenanceController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar las alertas.', 500);
        }
    }

    public function history()
    {
        try {
            $records = $this->maintenanceRepository->getHistoryRecords(15);

            if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                return $this->utilResponse->successResponse(
                    MaintenanceRecordResource::collection($records),
                    'Historial de mantenimiento obtenido correctamente'
                );
            }

            return view('maintenance.history', compact('records'));
        } catch (Throwable $e) {
            Log::error('Error al consultar historial de mantenimiento', [
                'action'    => 'MaintenanceController@history',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar el historial.', 500);
        }
    }

    public function printChecklist($record)
    {
        try {
            $recordId = $record instanceof MaintenanceRecord ? $record->id : $record;
            $maintenanceRecord = $this->maintenanceRepository->find($recordId);

            if (!$maintenanceRecord) {
                return $this->utilResponse->errorResponse('No existe el registro de mantenimiento solicitado', 404);
            }

            if ($maintenanceRecord->status === 'pending') {
                $maintenanceRecord = $this->maintenanceRepository->markAsPrinted($recordId);
            }

            $pdf = Pdf::loadView('maintenance.pdf.checklist', ['record' => $maintenanceRecord]);

            if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                return $this->utilResponse->successResponse(
                    new MaintenanceRecordResource($maintenanceRecord),
                    'Checklist generado y estado actualizado a impreso exitosamente'
                );
            }

            return $pdf->download("Checklist_{$maintenanceRecord->code}.pdf");
        } catch (Throwable $e) {
            Log::error('Error al generar formato checklist de mantenimiento', [
                'action'    => 'MaintenanceController@printChecklist',
                'record_id' => $record instanceof MaintenanceRecord ? $record->id : $record,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al generar el checklist.', 500);
        }
    }

    public function complete(CompleteMaintenanceRequest $request, $record)
    {
        try {
            $recordId = $record instanceof MaintenanceRecord ? $record->id : $record;
            $maintenanceRecord = $this->maintenanceRepository->find($recordId);

            if (!$maintenanceRecord) {
                return $this->utilResponse->errorResponse('No existe el registro de mantenimiento a completar', 404);
            }

            if ($maintenanceRecord->status !== 'printed') {
                return $this->utilResponse->errorResponse(
                    'Debes imprimir el formato antes de poder completarlo.',
                    422
                );
            }

            $extension = $request->file('evidence_file')->guessExtension() ?? 'pdf';
            $filename = time() . '_' . Str::uuid() . '.' . $extension;
            $path = $request->file('evidence_file')->storeAs('maintenance_evidence', $filename, 'public');

            $completedRecord = $this->maintenanceRepository->completeMaintenance($recordId, $path);

            if (request()->expectsJson() || request()->is('api/*') || request()->ajax()) {
                return $this->utilResponse->successResponse(
                    new MaintenanceRecordResource($completedRecord),
                    'Mantenimiento marcado como completado y evidencia guardada.'
                );
            }

            return redirect()->route('maintenance.index')->with('success', 'Mantenimiento marcado como completado y evidencia guardada.');
        } catch (Throwable $e) {
            Log::error('Error al completar orden de mantenimiento', [
                'action'    => 'MaintenanceController@complete',
                'record_id' => $record instanceof MaintenanceRecord ? $record->id : $record,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Ocurrió un error inesperado al completar el mantenimiento.', 500);
        }
    }
}
