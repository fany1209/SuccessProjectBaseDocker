<?php

namespace App\Http\Repositories\Concept;

use App\Models\Concept;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ConceptRepository
{
    protected Concept $model;

    public function __construct(Concept $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->orderBy('name')
        ->paginate($perPage);
    }

    public function find(int $id): ?Concept
    {
        return $this->model->find($id);
    }

    public function create(array $data): Concept
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update(int $id, array $data): ?Concept
    {
        return DB::transaction(function () use ($id, $data) {
            $concept = $this->model->where('concept_id', $id)->lockForUpdate()->first();

            if (!$concept) {
                return null;
            }

            $concept->update($data);

            return $concept;
        });
    }

    public function hasClis(int $id): bool
    {
        return $this->model->where('concept_id', $id)
            ->whereHas('clis')
            ->exists();
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $concept = $this->model->where('concept_id', $id)->lockForUpdate()->first();

            if (!$concept) {
                return false;
            }

            return (bool) $concept->delete();
        });
    }
}
