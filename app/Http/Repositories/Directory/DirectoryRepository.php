<?php

namespace App\Http\Repositories\Directory;

use App\Models\SupplierDirectory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DirectoryRepository
{
    protected SupplierDirectory $model;

    public function __construct(SupplierDirectory $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->orderBy('Name', 'asc')->get();
    }

    public function findByCode(int $code): ?SupplierDirectory
    {
        return $this->model->where('Code_supplier', $code)->first();
    }

    public function create(array $data): SupplierDirectory
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update(int $code, array $data): SupplierDirectory
    {
        return DB::transaction(function () use ($code, $data) {
            $supplier = $this->model->where('Code_supplier', $code)
                ->lockForUpdate()
                ->firstOrFail();

            $supplier->fill($data);
            $supplier->save();

            return $supplier;
        });
    }

    public function delete(int $code): bool
    {
        return DB::transaction(function () use ($code) {
            $supplier = $this->model->where('Code_supplier', $code)
                ->lockForUpdate()
                ->firstOrFail();

            return (bool) $supplier->delete();
        });
    }
}
