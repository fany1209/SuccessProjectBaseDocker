<?php

namespace App\Http\Repositories\Maintenance;

use App\Models\MaintenanceRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MaintenanceRepository
{
    protected $record;

    public function __construct(MaintenanceRecord $record)
    {
        $this->record = $record;
    }

    public function getPendingRecords(): Collection
    {
        return $this->record->with(['equipment', 'maintenancePlan'])
            ->whereIn('status', ['pending', 'printed'])
            ->orderBy('scheduled_date', 'asc')
            ->get();
    }

    public function getHistoryRecords(int $perPage = 15): LengthAwarePaginator
    {
        return $this->record->with(['equipment', 'maintenancePlan'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->paginate($perPage);
    }

    public function find($id): ?MaintenanceRecord
    {
        return $this->record->with(['equipment', 'maintenancePlan.checklistItems'])->find($id);
    }

    public function markAsPrinted($id): ?MaintenanceRecord
    {
        return DB::transaction(function () use ($id) {
            $record = $this->record->lockForUpdate()->find($id);
            if (!$record) {
                return null;
            }

            if ($record->status === 'pending') {
                $record->update([
                    'status'     => 'printed',
                    'printed_at' => now(),
                ]);
            }

            return $record;
        });
    }

    public function completeMaintenance($id, string $evidencePath): ?MaintenanceRecord
    {
        return DB::transaction(function () use ($id, $evidencePath) {
            $record = $this->record->lockForUpdate()->find($id);
            if (!$record) {
                return null;
            }

            $record->update([
                'status'        => 'completed',
                'completed_at'  => now(),
                'evidence_file' => $evidencePath,
            ]);

            return $record;
        });
    }
}
