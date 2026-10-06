<?php

namespace App\Http\Repositories\Minuta;

use App\Models\Minuta;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MinutaRepository
{
    protected Minuta $model;

    public function __construct(Minuta $model)
    {
        $this->model = $model;
    }

    public function countTotal(): int
    {
        return $this->model->newQuery()->count();
    }

    public function getMinutas(?string $status, ?string $search): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($status)) {
            $query->where('estatus', 'LIKE', "%{$status}%");
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('tema_general', 'LIKE', "%{$search}%")
                    ->orWhere('ponente', 'LIKE', "%{$search}%")
                    ->orWhere('asistente_nombre', 'LIKE', "%{$search}%");
            });
        }

        return $query->get();
    }

    public function find(int $id): ?Minuta
    {
        return $this->model->newQuery()->with('usuario')->find($id);
    }

    public function create(array $data, ?User $user): Minuta
    {
        return DB::transaction(function () use ($data, $user) {
            $formatted = $this->formatAttributes($data, $user);
            $formatted['user_id'] = $user?->id;

            return $this->model->create($formatted);
        });
    }

    public function update(int $id, array $data, ?User $user): Minuta
    {
        return DB::transaction(function () use ($id, $data, $user) {
            $minuta = $this->model->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            $formatted = $this->formatAttributes($data, $user, $minuta);

            $minuta->update($formatted);

            return $minuta;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $minuta = $this->model->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            return (bool) $minuta->delete();
        });
    }

    public function generatePdf(int $id)
    {
        $minuta = $this->model->newQuery()
            ->with('usuario')
            ->findOrFail($id);

        $fecha_hora = $minuta->fecha_hora ? Carbon::parse($minuta->fecha_hora) : now();
        $fecha = $fecha_hora->format('Y-m-d');
        $hora = $fecha_hora->format('g:i A');
        $creado_por = $minuta->usuario->name ?? 'No identificado';

        $pdf = Pdf::loadView('formats.minutas.10', [
            'fecha' => $fecha,
            'hora' => $hora,
            'lugar' => $minuta->lugar,
            'tema_general' => $minuta->tema_general,
            'ponente' => $minuta->ponente,
            'creado_por' => $creado_por,
            'asistente_nombre' => $minuta->asistente_nombre,
            'asistente_departamento' => $minuta->asistente_departamento,
            'tema_tratado' => $minuta->tema_tratado,
            'acuerdo' => $minuta->acuerdo,
            'responsable' => $minuta->responsable,
            'fecha_compromiso' => $minuta->fecha_compromiso,
            'fecha_cierre' => $minuta->fecha_cierre,
            'estatus' => $minuta->estatus,
        ]);

        return $pdf->stream("Minuta_SSS_FOR_REH_10_{$id}.pdf");
    }

    public function formatAttributes(array $raw, ?User $user, ?Minuta $existing = null): array
    {
        $data = $raw;

        if (isset($raw['lugar'])) {
            $data['lugar'] = strip_tags(trim((string)$raw['lugar']));
        }
        if (isset($raw['tema_general'])) {
            $data['tema_general'] = strip_tags(trim((string)$raw['tema_general']));
        }
        if (isset($raw['ponente'])) {
            $data['ponente'] = strip_tags(trim((string)$raw['ponente']));
        }

        if (isset($raw['asistente_nombre']) && is_array($raw['asistente_nombre'])) {
            $nombres = array_map('strip_tags', array_filter($raw['asistente_nombre']));
            $deptos = isset($raw['asistente_departamento']) && is_array($raw['asistente_departamento'])
                ? array_map('strip_tags', array_filter($raw['asistente_departamento']))
                : [];
            $data['asistente_nombre'] = implode(", ", $nombres);
            $data['asistente_departamento'] = implode(", ", $deptos);
        }

        if (isset($raw['acuerdo']) && is_array($raw['acuerdo'])) {
            $temas = isset($raw['tema_tratado']) && is_array($raw['tema_tratado'])
                ? array_map('strip_tags', array_filter($raw['tema_tratado']))
                : [];
            $acuerdos = array_map('strip_tags', array_filter($raw['acuerdo']));
            $responsables = isset($raw['responsable']) && is_array($raw['responsable'])
                ? array_map('strip_tags', array_filter($raw['responsable']))
                : [];

            $data['tema_tratado'] = implode(" | ", $temas);
            $data['acuerdo'] = implode(" | ", $acuerdos);
            $data['responsable'] = implode(", ", $responsables);

            $isAdmin = $user && method_exists($user, 'can') && $user->can('admin.dashboard');

            if ($isAdmin) {
                if (isset($raw['fecha_compromiso']) && is_array($raw['fecha_compromiso'])) {
                    $fechas_comp = array_map(function ($fecha) {
                        return $fecha ? substr($fecha, 0, 10) : null;
                    }, $raw['fecha_compromiso']);
                    $data['fecha_compromiso'] = implode(", ", array_filter($fechas_comp));
                }

                if (isset($raw['fecha_cierre']) && is_array($raw['fecha_cierre'])) {
                    $fechas_cierre = array_map(function ($fecha) {
                        return $fecha ? substr($fecha, 0, 10) : null;
                    }, $raw['fecha_cierre']);
                    $data['fecha_cierre'] = implode(", ", array_filter($fechas_cierre));
                }

                if (isset($raw['estatus']) && is_array($raw['estatus'])) {
                    $data['estatus'] = implode(", ", array_filter($raw['estatus']));
                }
            } else {
                if ($existing) {
                    $data['fecha_compromiso'] = $existing->fecha_compromiso;
                    $data['fecha_cierre'] = $existing->fecha_cierre;
                    $data['estatus'] = $existing->estatus;
                } else {
                    $data['fecha_compromiso'] = "";
                    $data['fecha_cierre'] = "";
                    $data['estatus'] = "Pendiente";
                }
            }
        }

        return $data;
    }
}
