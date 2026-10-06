<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Rh\AttendanceRepository;
use App\Http\Requests\Rh\RhAttendanceFilterRequest;
use App\Http\Requests\Rh\RhCsvUploadRequest;
use App\Http\Resources\Rh\AttendanceResource;
use App\Traits\UtilResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RhController extends Controller
{
    protected UtilResponse $utilResponse;
    protected AttendanceRepository $attendanceRepository;

    public function __construct(UtilResponse $utilResponse, AttendanceRepository $attendanceRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->attendanceRepository = $attendanceRepository;
    }

    public function index(RhAttendanceFilterRequest $request): View|AnonymousResourceCollection|JsonResponse
    {
        try {
            $empleadoSeleccionado = $request->validated('empleado');
            $fechaInicio = $request->validated('fecha_inicio') ?: Carbon::now()->subDays(7)->toDateString();
            $fechaFin = $request->validated('fecha_fin') ?: Carbon::now()->toDateString();

            $registros = $this->attendanceRepository->getFilteredAttendances(
                $empleadoSeleccionado,
                $fechaInicio,
                $fechaFin
            );

            if ($request->ajax() || $request->wantsJson()) {
                return AttendanceResource::collection($registros);
            }

            $empleados = $this->attendanceRepository->getDistinctEmployees();
            $metrics = $this->attendanceRepository->calculateDashboardMetrics($registros, $empleadoSeleccionado);

            return view('rh.asistencia', array_merge([
                'empleados' => $empleados,
                'empleadoSeleccionado' => $empleadoSeleccionado,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
            ], $metrics));
        } catch (\Throwable $e) {
            Log::error('Error al consultar asistencias de RH', [
                'action' => 'index',
                'user_id' => auth()->id(),
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al consultar los registros de asistencia.', 500);
            }

            return back()->withErrors('Error al consultar los registros de asistencia.');
        }
    }

    public function uploadCsv(RhCsvUploadRequest $request): RedirectResponse
    {
        try {
            $totalImportados = $this->attendanceRepository->importCsv($request->file('csv_file'));

            return back()->with('success', "¡Datos importados correctamente! Se procesaron {$totalImportados} registros de asistencia de forma eficiente.");
        } catch (\Throwable $e) {
            Log::error('Error en importación masiva de asistencias', [
                'action' => 'uploadCsv',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors('Error al procesar el archivo CSV: ' . $e->getMessage());
        }
    }
}