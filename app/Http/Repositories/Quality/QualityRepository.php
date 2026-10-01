<?php

namespace App\Http\Repositories\Quality;

use App\Models\Customer;
use App\Models\InspectionW;
use App\Models\Observacion;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QualityRepository
{
    protected InspectionW $inspectionModel;
    protected Observacion $observacionModel;

    public function __construct(InspectionW $inspectionModel, Observacion $observacionModel)
    {
        $this->inspectionModel = $inspectionModel;
        $this->observacionModel = $observacionModel;
    }

    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = is_dir(base_path('../public_html')) ? base_path('../public_html') : public_path();
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    public function getIndexData(): array
    {
        $products = Product::orderBy('name')->get(['product_id', 'name']);

        $suppliers = DB::table('suppliers')
            ->select('supplier_id', 'supplier_code', 'name')
            ->orderBy('name')
            ->get();

        $customers = DB::table('customers')
            ->select('customer_id', 'name')
            ->get();

        return compact('products', 'suppliers', 'customers');
    }

    public function getIncidencias(int $limit = 100): Collection
    {
        return DB::table('incidencias')
            ->select('folio', 'descripcion')
            ->orderByDesc('fecha_incidencia')
            ->limit($limit)
            ->get();
    }

    public function getSuppliersData(): Collection
    {
        return DB::table('suppliers')
            ->select('supplier_code', 'name')
            ->get();
    }

    public function getGroupedInventoryProducts(): Collection
    {
        $rows = DB::table('inventory as i')
            ->leftJoin('products as p', 'p.product_id', '=', 'i.product_id')
            ->select('p.product_id', 'p.name', 'i.batch')
            ->orderBy('p.name')
            ->get();

        return $rows->groupBy('product_id')->map(function ($items) {
            return [
                'product_id' => $items->first()->product_id,
                'name'       => $items->first()->name,
                'batches'    => $items->pluck('batch')->filter()->unique()->values()->all(),
            ];
        })->values();
    }

    public function getInspections(?User $user): Collection
    {
        $inspections = $this->inspectionModel->newQuery()
            ->with(['observaciones' => function ($q) {
                $q->orderBy('id');
            }])
            ->select(
                'id',
                'fecha_inspeccion',
                'inspector',
                'hora_turno',
                'turno',
                'area',
                'area_otro',
                'responsable',
                'comentarios',
                'comentarios_q',
                'status'
            )
            ->get();

        $fmtYmd = fn($d) => $d ? Carbon::parse($d)->format('Y-m-d') : null;

        $normalizeTurno = function ($t) {
            $t = strtolower(trim((string) $t));
            $map = [
                '1' => '1', '2' => '2', '3' => '3', 'mixto' => 'mixto',
                'matutina' => '1', 'mañana' => '1', 'am' => '1',
                'vespertina' => '2', 'tarde' => '2', 'pm' => '2',
                'nocturna' => '3', 'noche' => '3',
            ];
            return $map[$t] ?? null;
        };

        $turnoLabel = fn($norm) => [
            '1'     => '1',
            '2'     => '2',
            '3'     => '3',
            'mixto' => 'Mixto',
        ][$norm] ?? null;

        $normalizeArea = function ($raw) {
            if (is_array($raw)) {
                $arr = $raw;
            } elseif (is_string($raw)) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $arr = $decoded;
                } else {
                    $arr = array_map('trim', array_filter(explode(',', $raw)));
                }
            } else {
                $arr = [];
            }
            $allowed = ['nave1', 'nave2', 'otro'];
            return array_values(array_unique(array_filter($arr, fn($v) => in_array($v, $allowed, true))));
        };

        return $inspections->map(function ($r) use ($fmtYmd, $normalizeTurno, $turnoLabel, $normalizeArea, $user) {
            $obs = $r->observaciones->map(function ($o) use ($fmtYmd) {
                $evi = $o->evidencia_path;
                $cor = $o->ev_corr_path;
                return [
                    'id'             => $o->id,
                    'name'           => $o->name,
                    'rev'            => $o->rev,
                    'fecha'          => $fmtYmd($o->fecha),
                    'evidencia_url'  => $evi ? asset($evi) : null,
                    'ev_corr_url'    => $cor ? asset($cor) : null,
                    'evidencia_path' => $evi,
                    'ev_corr_path'   => $cor,
                    'ubicacion'      => $o->ubicacion,
                ];
            })->values()->all();

            $turnoRaw = $r->turno;
            $turnoNorm = $normalizeTurno($turnoRaw);
            $turnoLbl = $turnoLabel($turnoNorm);
            $areaArr = $normalizeArea($r->area);

            return [
                'id'               => $r->id,
                'status'           => $r->status,
                'fecha_inspeccion' => $fmtYmd($r->fecha_inspeccion),
                'inspector'        => $r->inspector,
                'hora_turno'       => $r->hora_turno,
                'turno_raw'        => $turnoRaw,
                'turno'            => $turnoNorm,
                'turno_label'      => $turnoLbl,
                'area'             => $areaArr,
                'area_otro'        => $r->area_otro,
                'responsable'      => $r->responsable,
                'comentarios'      => $r->comentarios,
                'comentarios_q'    => $r->comentarios_q,
                'obs'              => $obs,
                'canUpdate'        => $user?->can('quality.update') ?? false,
                'canDelete'        => $user?->can('quality.delete') ?? false,
                'canUpdateW'       => $user?->can('quality.updateW') ?? false,
            ];
        });
    }

    public function findInspection(int $id): ?InspectionW
    {
        return $this->inspectionModel->find($id);
    }

    public function findInspectionWithObservations(int $id): ?InspectionW
    {
        return $this->inspectionModel->with('observaciones')->find($id);
    }

    public function storeAlmacenInspection(array $data, array $obsList, array $uploadedFiles, ?int $userId): InspectionW
    {
        return DB::transaction(function () use ($data, $obsList, $uploadedFiles, $userId) {
            $rec = new InspectionW();
            $rec->fecha_inspeccion = $data['fecha_inspeccion'] ?? null;
            $rec->inspector        = $data['inspector'] ?? null;
            $rec->hora_turno       = $data['hora_turno'] ?? null;
            $rec->turno            = $data['turno'] ?? null;
            $rec->area             = !empty($data['area']) ? array_values($data['area']) : null;
            $rec->area_otro        = $data['area_otro'] ?? null;
            $rec->responsable      = $data['responsable'] ?? null;
            $rec->comentarios      = $data['comentarios'] ?? null;
            $rec->comentarios_q    = $data['comentarios_q'] ?? null;
            $rec->user_id          = $userId;
            $rec->save();

            $targetDir = $this->getPublicHtmlPath('inspections_w/evidencias');
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            foreach ($obsList as $i => $row) {
                $name = trim((string)($row['name'] ?? ''));
                $rev = in_array(($row['rev'] ?? ''), ['cumple', 'no_cumple'], true) ? $row['rev'] : null;
                $fecha = $row['fecha'] ?? null;
                $evidencia_path = null;

                if (isset($uploadedFiles[$i]) && $uploadedFiles[$i]->isValid()) {
                    $f = $uploadedFiles[$i];
                    $ext = $f->guessExtension() ?: 'jpg';
                    $filename = time() . '_' . Str::uuid() . '.' . $ext;
                    $f->move($targetDir, $filename);
                    $evidencia_path = 'inspections_w/evidencias/' . $filename;
                }

                if ($name === '' && !$evidencia_path && !$fecha && !$rev) {
                    continue;
                }

                Observacion::create([
                    'inspection_id'  => $rec->id,
                    'name'           => ($name !== '') ? $name : null,
                    'rev'            => $rev,
                    'fecha'          => $fecha ?: null,
                    'evidencia_path' => $evidencia_path,
                    'ubicacion'      => isset($row['ubicacion']) && trim($row['ubicacion']) !== '' ? trim($row['ubicacion']) : null,
                ]);
            }

            return $rec;
        });
    }

    public function updateInspection(int $id, array $data, array $obsList, array $uploadedFiles): InspectionW
    {
        return DB::transaction(function () use ($id, $data, $obsList, $uploadedFiles) {
            $inspection = $this->inspectionModel->where('id', $id)->lockForUpdate()->firstOrFail();

            $inspection->fecha_inspeccion = $data['fecha_inspeccion'] ?? $inspection->fecha_inspeccion;
            $inspection->inspector        = $data['inspector'] ?? $inspection->inspector;
            $inspection->hora_turno       = $data['hora_turno'] ?? $inspection->hora_turno;
            $inspection->turno            = $data['turno'] ?? $inspection->turno;
            $inspection->area             = $data['area'] ?? $inspection->area;
            $inspection->area_otro        = $data['area_otro'] ?? $inspection->area_otro;

            if (!empty($data['responsable'])) {
                $inspection->responsable = $data['responsable'];
            }
            if (isset($data['comentarios_q'])) {
                $inspection->comentarios_q = $data['comentarios_q'];
            }
            $inspection->save();

            $targetDir = $this->getPublicHtmlPath('inspections_w/evidencias');
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            foreach ($obsList as $i => $obsData) {
                $obs = isset($obsData['id'])
                    ? Observacion::where('id', $obsData['id'])->lockForUpdate()->first()
                    : new Observacion();

                if (!$obs) {
                    $obs = new Observacion();
                }

                $obs->inspection_id = $inspection->id;
                $obs->name          = $obsData['name'] ?? null;
                $obs->rev           = $obsData['rev'] ?? null;

                if (!empty($obsData['fecha'])) {
                    $obs->fecha = $obsData['fecha'];
                }

                $obs->ubicacion = isset($obsData['ubicacion']) && trim($obsData['ubicacion']) !== ''
                    ? trim($obsData['ubicacion'])
                    : null;

                if (isset($uploadedFiles[$i]) && $uploadedFiles[$i]->isValid()) {
                    if ($obs->evidencia_path) {
                        $oldPath = $this->getPublicHtmlPath($obs->evidencia_path);
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    $f = $uploadedFiles[$i];
                    $ext = $f->guessExtension() ?: 'jpg';
                    $filename = time() . '_' . Str::uuid() . '.' . $ext;
                    $f->move($targetDir, $filename);
                    $obs->evidencia_path = 'inspections_w/evidencias/' . $filename;
                } elseif (isset($obsData['existing_evidencia_path'])) {
                    $obs->evidencia_path = $obsData['existing_evidencia_path'];
                }

                $obs->save();
            }

            return $inspection;
        });
    }

    public function updateWarehouseInspection(int $id, ?string $comentarios, array $obsList, array $uploadedFiles): InspectionW
    {
        return DB::transaction(function () use ($id, $comentarios, $obsList, $uploadedFiles) {
            $inspection = $this->inspectionModel->where('id', $id)->lockForUpdate()->firstOrFail();

            if ($comentarios !== null) {
                $inspection->comentarios = $comentarios;
                $inspection->save();
            }

            $targetDir = $this->getPublicHtmlPath('observaciones');
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            foreach ($obsList as $i => $obs) {
                $observacion = isset($obs['id'])
                    ? Observacion::where('id', $obs['id'])->lockForUpdate()->first()
                    : new Observacion();

                if (!$observacion) {
                    $observacion = new Observacion();
                }

                $observacion->inspection_id = $inspection->id;
                if (isset($obs['name'])) {
                    $observacion->name = $obs['name'];
                }

                if (!empty($obs['fecha'])) {
                    $observacion->fecha = $obs['fecha'];
                }

                if (isset($uploadedFiles[$i]) && $uploadedFiles[$i]->isValid()) {
                    if ($observacion->ev_corr_path) {
                        $oldPath = $this->getPublicHtmlPath($observacion->ev_corr_path);
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    $file = $uploadedFiles[$i];
                    $ext = $file->guessExtension() ?: 'jpg';
                    $filename = time() . '_' . Str::uuid() . '.' . $ext;
                    $file->move($targetDir, $filename);
                    $observacion->ev_corr_path = 'observaciones/' . $filename;
                }

                $observacion->save();
            }

            return $inspection;
        });
    }

    public function deleteObservation(int $id, ?string $path = null): bool
    {
        return DB::transaction(function () use ($id, $path) {
            $observation = Observacion::where('id', $id)->lockForUpdate()->first();
            if (!$observation) {
                return false;
            }

            $filesToDelete = array_filter([$observation->evidencia_path, $observation->ev_corr_path, $path]);
            foreach ($filesToDelete as $filePath) {
                $fullPath = $this->getPublicHtmlPath($filePath);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }

            return (bool) $observation->delete();
        });
    }

    public function deleteInspection(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $inspection = $this->inspectionModel->with('observaciones')->where('id', $id)->lockForUpdate()->firstOrFail();

            foreach ($inspection->observaciones as $obs) {
                $filesToDelete = array_filter([$obs->evidencia_path, $obs->ev_corr_path]);
                foreach ($filesToDelete as $filePath) {
                    $fullPath = $this->getPublicHtmlPath($filePath);
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }
            }

            $inspection->observaciones()->delete();
            return (bool) $inspection->delete();
        });
    }

    public function getPendingInspections(): Collection
    {
        return $this->inspectionModel->where('status', 0)->get();
    }

    public function getPdfGenerationsChartData(?Carbon $from, ?Carbon $to): array
    {
        $q = DB::table('pdf_clicks')->whereIn('pdf_type', ['A', 'B', 'C']);

        if ($from) {
            $q->where('generated_at', '>=', $from->startOfDay());
        }

        if ($to) {
            $q->where('generated_at', '<=', $to->endOfDay());
        }

        $rows = $q->select('pdf_type', DB::raw('COUNT(*) as total'))
            ->groupBy('pdf_type')
            ->orderBy('pdf_type')
            ->get();

        $data = [['Tipo', 'Generados']];
        foreach ($rows as $r) {
            $label = match ($r->pdf_type) {
                'A' => 'SSS-FOR-CAL-04',
                'B' => 'SSS-FOR-CAL-08',
                'C' => 'SSS-FOR-CAL-10',
            };

            $data[] = [$label, (int) $r->total];
        }

        return $data;
    }
}
