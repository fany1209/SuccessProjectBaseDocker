<?php

namespace App\Http\Repositories\Equipment;

use App\Models\LaboratoryEquipment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EquipmentRepository
{
    protected LaboratoryEquipment $model;

    public function __construct(LaboratoryEquipment $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->orderBy('id', 'desc')->get();
    }

    public function find(int $id): ?LaboratoryEquipment
    {
        return $this->model->find($id);
    }

    public function create(array $data): LaboratoryEquipment
    {
        return DB::transaction(function () use ($data) {
            $data['quantity'] = $data['quantity'] ?? 0;
            $data['status'] = $data['status'] ?? 'funcional';

            return $this->model->create($data);
        });
    }

    public function update(int $id, array $data): LaboratoryEquipment
    {
        return DB::transaction(function () use ($id, $data) {
            $equipment = $this->model->where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $equipment->update($data);

            return $equipment;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $equipment = $this->model->where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            return (bool) $equipment->delete();
        });
    }
}
