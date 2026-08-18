<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = Activity::with('causer')->latest()->paginate(50);

        // Datos para la gráfica de logins por usuario
        $loginStats = Activity::where('log_name', 'auth')
            ->where('description', 'El usuario ha iniciado sesión')
            ->selectRaw('causer_type, causer_id, count(*) as total')
            ->groupBy('causer_type', 'causer_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('causer')
            ->get();

        $chartLabels = [];
        $chartData = [];

        foreach ($loginStats as $stat) {
            if ($stat->causer) {
                $chartLabels[] = $stat->causer->name;
                $chartData[] = $stat->total;
            }
        }

        return view('activity_log.index', compact('activities', 'chartLabels', 'chartData'));
    }
}
