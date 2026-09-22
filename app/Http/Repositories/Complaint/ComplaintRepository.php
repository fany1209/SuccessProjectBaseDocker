<?php

namespace App\Http\Repositories\Complaint;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ComplaintRepository
{
    protected Complaint $model;

    public function __construct(Complaint $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->orderByDesc('fecha')->orderByDesc('id')->get();
    }

    public function find(int $id): ?Complaint
    {
        return $this->model->find($id);
    }

    public function create(array $data): Complaint
    {
        return DB::transaction(function () use ($data) {
            $motivos = $data['motivos'] ?? [];
            if (!in_array('otro', $motivos, true)) {
                $data['motivo_otro'] = null;
            }

            return $this->model->create($data);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $complaint = $this->model->where('id', $id)->lockForUpdate()->first();

            if (!$complaint) {
                return false;
            }

            return (bool) $complaint->delete();
        });
    }
}
