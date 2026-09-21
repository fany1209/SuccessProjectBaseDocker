<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ActivityLog\ActivityLogRepository;
use App\Http\Resources\ActivityLog\ActivityResource;
use App\Traits\UtilResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityLogController extends Controller
{
    private UtilResponse $utilResponse;
    private ActivityLogRepository $activityLogRepository;

    public function __construct(UtilResponse $utilResponse, ActivityLogRepository $activityLogRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->activityLogRepository = $activityLogRepository;
    }

    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 50);
            $activities = $this->activityLogRepository->getPaginated($perPage);
            $loginStats = $this->activityLogRepository->getLoginStatistics(10);

            $chartLabels = $loginStats['labels'];
            $chartData = $loginStats['data'];

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse([
                    'activities'   => ActivityResource::collection($activities),
                    'pagination'   => [
                        'current_page' => $activities->currentPage(),
                        'last_page'    => $activities->lastPage(),
                        'per_page'     => $activities->perPage(),
                        'total'        => $activities->total(),
                    ],
                    'chart_labels' => $chartLabels,
                    'chart_data'   => $chartData,
                ], 'Registros de actividad obtenidos correctamente');
            }

            return view('activity_log.index', compact('activities', 'chartLabels', 'chartData'));
        } catch (Throwable $e) {
            Log::error('Error al consultar el registro de actividades: ' . $e->getMessage(), [
                'action'    => 'ActivityLogController@index',
                'user_id'   => auth()->id(),
                'exception' => $e,
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Ocurrió un error inesperado al consultar los registros de actividad.', 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar el registro de actividad.');
        }
    }
}
