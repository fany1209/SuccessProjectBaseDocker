<?php

namespace App\Http\Repositories\Fumigacion;

use App\Models\Fumigacion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class FumigacionRepository
{
    protected Fumigacion $model;

    public function __construct(Fumigacion $model)
    {
        $this->model = $model;
    }

    public function all(string $orderBy = 'fecha_programada', string $direction = 'asc'): Collection
    {
        return $this->model->orderBy($orderBy, $direction)->get();
    }

    public function findById(int $id): ?Fumigacion
    {
        return $this->model->find($id);
    }

    public function create(array $data): Fumigacion
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update(int $id, array $data): ?Fumigacion
    {
        return DB::transaction(function () use ($id, $data) {
            $record = $this->model->where('id', $id)->lockForUpdate()->first();
            if (!$record) {
                return null;
            }

            $record->update($data);

            return $record;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->model->where('id', $id)->lockForUpdate()->first();
            if (!$record) {
                return false;
            }

            return (bool) $record->delete();
        });
    }

    public function getCalendarEvents(?Collection $fumigaciones = null): SupportCollection
    {
        $records = $fumigaciones ?? $this->all();

        return $records->map(function ($f) {
            $estado = $f->estado ?? 'Pendiente';

            return [
                'id'              => $f->id,
                'title'           => $f->proveedor . ' (' . ($f->metodo_aplicacion ?: 'Sin método') . ')',
                'start'           => Carbon::parse($f->fecha_programada)->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $estado === 'Realizado' ? '#198754' : ($estado === 'Cancelado' ? '#dc3545' : '#ffc107'),
                'borderColor'     => $estado === 'Realizado' ? '#157347' : ($estado === 'Cancelado' ? '#a71d2a' : '#e0a800'),
                'textColor'       => $estado === 'Pendiente' ? '#000000' : '#ffffff',
            ];
        });
    }
}
