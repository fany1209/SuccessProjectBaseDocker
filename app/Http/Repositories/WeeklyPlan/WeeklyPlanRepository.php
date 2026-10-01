<?php

namespace App\Http\Repositories\WeeklyPlan;

use App\Models\WeeklyWorkPlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WeeklyPlanRepository
{
    protected WeeklyWorkPlan $model;

    public function __construct(WeeklyWorkPlan $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene todos los planes semanales ordenados descendentemente por fecha de revisión.
     *
     * @return Collection<int, WeeklyWorkPlan>
     */
    public function all(): Collection
    {
        return $this->model
            ->orderByDesc('review_date')
            ->get();
    }

    /**
     * Busca un plan semanal por su ID con todas sus relaciones cargadas.
     */
    public function find(int $id): ?WeeklyWorkPlan
    {
        return $this->model
            ->with(['objectives', 'results', 'findings', 'nextActions'])
            ->find($id);
    }

    /**
     * Busca un plan semanal o lanza ModelNotFoundException.
     */
    public function findOrFail(int $id): WeeklyWorkPlan
    {
        return $this->model
            ->with(['objectives', 'results', 'findings', 'nextActions'])
            ->findOrFail($id);
    }

    /**
     * Estructura los datos para la plantilla PDF del formato 11 de laboratorio.
     *
     * @param int $id
     * @return array<string, mixed>
     */
    public function getPdfData(int $id): array
    {
        $plan = $this->findOrFail($id);

        $nextActions = $plan->nextActions;

        return [
            'pagina_actual'        => 1,
            'paginas_total'        => 3,
            'semana_rango'         => $plan->week_range,
            'fecha_revision'       => $plan->review_date ? (is_string($plan->review_date) ? $plan->review_date : $plan->review_date->format('Y-m-d')) : null,
            'proyecto'             => $plan->project_name,
            'responsable'          => $plan->responsible_name,
            'total_hours'          => $plan->total_hours,
            'objetivos'            => $plan->objectives->map(fn($o) => [
                'n'           => $o->item_number,
                'titulo'      => $o->title,
                'descripcion' => $o->description,
                'horas'       => $o->hours,
            ])->toArray(),
            'resultados'           => $plan->results->map(fn($r) => [
                'n'      => $r->objective_number,
                'texto'  => $r->result_text,
                'cumple' => (bool) $r->is_met,
            ])->toArray(),
            'hallazgos'            => $plan->findings->map(fn($h) => [
                'hallazgo'  => $h->finding_text,
                'causa'     => $h->cause_text,
                'propuesta' => $h->proposal_text,
            ])->toArray(),
            'proxima_semana_rango' => optional($nextActions->first())->next_week_range ?? '',
            'plan_proxima'         => $nextActions->map(fn($n) => [
                'plan'     => $n->plan_text,
                'acciones' => $n->actions_text,
            ])->toArray(),
        ];
    }

    /**
     * Elimina un plan de trabajo semanal y sus secciones con bloqueo pesimista en transacción ACID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $plan = $this->model
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$plan) {
                return false;
            }

            $plan->objectives()->delete();
            $plan->results()->delete();
            $plan->findings()->delete();
            $plan->nextActions()->delete();

            return (bool) $plan->delete();
        });
    }
}
