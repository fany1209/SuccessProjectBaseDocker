<?php

namespace App\Http\Repositories\SoilAnalysis;

use App\Models\SoilInternalAnalysis;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class SoilAnalysisRepository
{
    protected SoilInternalAnalysis $model;

    public function __construct(SoilInternalAnalysis $model)
    {
        $this->model = $model;
    }

    /**
     * Resuelve una ruta física dentro de public_html desacoplada.
     */
    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    /**
     * Obtiene todos los análisis de suelo ordenados por fecha de ingreso.
     *
     * @return Collection<int, SoilInternalAnalysis>
     */
    public function all(): Collection
    {
        return $this->model
            ->select(['id', 'report_code', 'entry_date', 'issue_date', 'client_name', 'created_at', 'updated_at'])
            ->orderByDesc('entry_date')
            ->get();
    }

    /**
     * Busca un análisis de suelo por su ID incluyendo variables convencionales.
     */
    public function find(int $id): ?SoilInternalAnalysis
    {
        return $this->model->with('conventionalVariables')->find($id);
    }

    /**
     * Busca un análisis de suelo por su ID o lanza ModelNotFoundException.
     */
    public function findOrFail(int $id): SoilInternalAnalysis
    {
        return $this->model->with('conventionalVariables')->findOrFail($id);
    }

    /**
     * Construye la estructura de datos para la generación del PDF de análisis de suelo.
     *
     * @param int $id
     * @return array<string, mixed>
     */
    public function getPdfData(int $id): array
    {
        $analysis = $this->findOrFail($id);

        return [
            'reporte'        => $analysis->report_code,
            'fecha_ingreso'  => $analysis->entry_date ? (is_string($analysis->entry_date) ? $analysis->entry_date : $analysis->entry_date->format('Y-m-d')) : null,
            'fecha_emision'  => $analysis->issue_date ? (is_string($analysis->issue_date) ? $analysis->issue_date : $analysis->issue_date->format('Y-m-d')) : null,
            'cliente_nombre' => $analysis->client_name,
            'convencionales' => $analysis->conventionalVariables->map(fn($r) => [
                'v' => $r->variable_name,
                'r' => $r->result_text,
                'u' => $r->unit_text,
            ])->toArray(),
        ];
    }

    /**
     * Elimina un análisis de suelo y sus dependencias aplicando bloqueo pesimista en transacción ACID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $analysis = $this->model
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$analysis) {
                return false;
            }

            if (!empty($analysis->image_path)) {
                $fullPath = $this->getPublicHtmlPath($analysis->image_path);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }

            $analysis->conventionalVariables()->delete();

            return (bool) $analysis->delete();
        });
    }
}
