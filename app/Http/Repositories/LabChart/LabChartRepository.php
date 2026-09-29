<?php

namespace App\Http\Repositories\LabChart;

use App\Models\LaboratorySample;
use Illuminate\Support\Facades\DB;

class LabChartRepository
{
    /**
     * @var LaboratorySample
     */
    protected LaboratorySample $model;

    /**
     * LabChartRepository constructor.
     *
     * @param LaboratorySample $model
     */
    public function __construct(LaboratorySample $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene los conteos agregados de registros para los gráficos de laboratorio.
     *
     * @return array<string, int>
     */
    public function getCounts(): array
    {
        $reception = DB::table('reception_of_samples')->count();
        $weekly    = DB::table('weekly_results')->count();
        $lab       = $this->model->newQuery()->count();
        $pdfClicksD = DB::table('pdf_clicks')
            ->whereRaw('UPPER(TRIM(pdf_type)) = ?', ['D'])
            ->count();

        return [
            'reception_of_samples' => (int) $reception,
            'weekly_results'       => (int) $weekly,
            'laboratory_samples'   => (int) $lab,
            'pdf_clicks_d'         => (int) $pdfClicksD,
        ];
    }

    /**
     * Resuelve rutas físicas a public_html fuera del directorio base del framework.
     *
     * @param string $subpath
     * @return string
     */
    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }
}
