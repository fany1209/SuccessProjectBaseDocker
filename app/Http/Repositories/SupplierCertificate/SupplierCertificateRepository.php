<?php

namespace App\Http\Repositories\SupplierCertificate;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupplierCertificateRepository
{
    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = is_dir(base_path('../public_html')) ? base_path('../public_html') : public_path();
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    public function getAll(): Collection
    {
        return DB::table('supplier_certificates as sc')
            ->join('suppliers as s', 'sc.supplier_id', '=', 's.supplier_id')
            ->join('files as f', 'sc.file_id', '=', 'f.file_id')
            ->join('products as p', 'f.product_id', '=', 'p.product_id')
            ->select(
                'sc.id as certificate_id',
                's.name as supplier_name',
                'p.name as product_name',
                'f.path as file_path',
                'sc.fecha_emision',
                'sc.created_at'
            )
            ->orderBy('sc.created_at', 'desc')
            ->get();
    }

    public function store(array $data, UploadedFile $file): int
    {
        return DB::transaction(function () use ($data, $file) {
            $path = $file->store('certificates', 'public');

            $publicHtmlDest = $this->getPublicHtmlPath('storage/certificates');
            if (is_dir(base_path('../public_html')) && !file_exists($publicHtmlDest)) {
                @mkdir($publicHtmlDest, 0755, true);
            }
            if (is_dir(base_path('../public_html'))) {
                $storageSource = storage_path('app/public/' . $path);
                $destFile = $this->getPublicHtmlPath('storage/' . $path);
                if (file_exists($storageSource) && !file_exists($destFile)) {
                    @copy($storageSource, $destFile);
                }
            }

            $fileId = DB::table('files')->insertGetId([
                'path'       => $path,
                'product_id' => $data['product_id'],
            ]);

            return DB::table('supplier_certificates')->insertGetId([
                'supplier_id'   => $data['supplier_id'],
                'file_id'       => $fileId,
                'fecha_emision' => $data['fecha_emision'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        });
    }

    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $certificate = DB::table('supplier_certificates')->where('id', $id)->lockForUpdate()->first();
            if (!$certificate) {
                return false;
            }

            $file = DB::table('files')->where('file_id', $certificate->file_id)->lockForUpdate()->first();
            if ($file) {
                Storage::disk('public')->delete($file->path);

                $fullPath = $this->getPublicHtmlPath('storage/' . $file->path);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }

                DB::table('files')->where('file_id', $file->file_id)->delete();
            }

            DB::table('supplier_certificates')->where('id', $id)->delete();

            return true;
        });
    }
}
