<?php

namespace App\Http\Repositories\ActivityLog;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Activitylog\Models\Activity;

class ActivityLogRepository
{
    protected Activity $activity;

    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function getPaginated(int $perPage = 50): LengthAwarePaginator
    {
        return $this->activity
            ->with('causer')
            ->latest()
            ->paginate($perPage);
    }

    public function getLoginStatistics(int $limit = 10): array
    {
        $loginStats = $this->activity
            ->where('log_name', 'auth')
            ->where('description', 'El usuario ha iniciado sesión')
            ->selectRaw('causer_type, causer_id, count(*) as total')
            ->groupBy('causer_type', 'causer_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('causer')
            ->get();

        $chartLabels = [];
        $chartData = [];

        foreach ($loginStats as $stat) {
            if ($stat->causer) {
                $chartLabels[] = $stat->causer->name;
                $chartData[] = (int) $stat->total;
            }
        }

        return [
            'labels' => $chartLabels,
            'data'   => $chartData,
        ];
    }
}
