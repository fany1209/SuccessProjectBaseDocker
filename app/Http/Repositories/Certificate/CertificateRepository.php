<?php

namespace App\Http\Repositories\Certificate;

use App\Models\QualityCertificate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CertificateRepository
{
    protected QualityCertificate $certificate;

    public function __construct(QualityCertificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function all(): Collection
    {
        return $this->certificate
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();
    }

    public function find(int $id): ?QualityCertificate
    {
        return $this->certificate->find($id);
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->certificate->lockForUpdate()->find($id);
            if (!$record) {
                return false;
            }

            return (bool) $record->delete();
        });
    }

    public function getForExport(): Collection
    {
        return $this->certificate
            ->orderBy('fecha', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }
}
