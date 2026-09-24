<?php

namespace App\Http\Repositories\Contrato;

use App\Models\Contrato;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ContratoRepository
{
    protected Contrato $model;

    public function __construct(Contrato $model)
    {
        $this->model = $model;
    }

    public function getTrabajadoresActivos(): Collection
    {
        return User::where('status', 'Activo')
            ->where('tipo_empleado', 'Trabajador')
            ->with('contrato')
            ->orderBy('name')
            ->get();
    }

    public function getPracticantesActivos(): Collection
    {
        return User::where('status', 'Activo')
            ->where('tipo_empleado', 'Practicante')
            ->with('contrato')
            ->orderBy('name')
            ->get();
    }

    public function findByUserId(int $userId): ?Contrato
    {
        return $this->model->where('user_id', $userId)->with('user')->first();
    }

    public function find(int $id): ?Contrato
    {
        return $this->model->with('user')->find($id);
    }

    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = is_dir(base_path('../public_html'))
            ? base_path('../public_html')
            : public_path();

        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    public function uploadContratos(int $userId, array $files): Contrato
    {
        return DB::transaction(function () use ($userId, $files) {
            $contrato = $this->model->where('user_id', $userId)->lockForUpdate()->first();

            if (!$contrato) {
                $contrato = $this->model->create(['user_id' => $userId]);
            }

            $allowedFields = ['mes_1', 'mes_2', 'mes_3', 'indefinido', 'confidencialidad'];
            $dest = $this->getPublicHtmlPath('contratos');

            if (!file_exists($dest)) {
                @mkdir($dest, 0755, true);
            }

            foreach ($allowedFields as $field) {
                if (isset($files[$field]) && $files[$field] instanceof UploadedFile) {
                    $file = $files[$field];

                    if ($contrato->$field) {
                        $oldFullPath = $this->getPublicHtmlPath($contrato->$field);
                        if (file_exists($oldFullPath)) {
                            @unlink($oldFullPath);
                        }
                    }

                    $cleanOriginalName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file->getClientOriginalName());
                    $filename = $userId . '_' . $field . '_' . time() . '_' . $cleanOriginalName;

                    $file->move($dest, $filename);
                    $contrato->$field = 'contratos/' . $filename;
                }
            }

            $contrato->save();

            return $contrato->load('user');
        });
    }

    public function deleteContratoFile(int $userId, string $field): ?Contrato
    {
        $allowedFields = ['mes_1', 'mes_2', 'mes_3', 'indefinido', 'confidencialidad'];
        if (!in_array($field, $allowedFields, true)) {
            return null;
        }

        return DB::transaction(function () use ($userId, $field) {
            $contrato = $this->model->where('user_id', $userId)->lockForUpdate()->first();

            if (!$contrato) {
                return null;
            }

            if ($contrato->$field) {
                $fullPath = $this->getPublicHtmlPath($contrato->$field);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
                $contrato->$field = null;
                $contrato->save();
            }

            return $contrato->load('user');
        });
    }
}
