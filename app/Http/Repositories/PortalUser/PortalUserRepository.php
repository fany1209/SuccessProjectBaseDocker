<?php

namespace App\Http\Repositories\PortalUser;

use App\Models\Customer;
use App\Models\PortalDocument;
use App\Models\PortalUser;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PortalUserRepository
{
    protected PortalUser $portalUserModel;

    public function __construct(PortalUser $portalUserModel)
    {
        $this->portalUserModel = $portalUserModel;
    }

    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = is_dir(base_path('../public_html')) ? base_path('../public_html') : public_path();
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    protected function saveFile(UploadedFile $file, string $folder = 'portal_docs'): string
    {
        $path = $file->store($folder, 'public');

        if (is_dir(base_path('../public_html'))) {
            $destDir = $this->getPublicHtmlPath('storage/' . $folder);
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $storageSource = storage_path('app/public/' . $path);
            $destFile = $this->getPublicHtmlPath('storage/' . $path);
            if (file_exists($storageSource) && !file_exists($destFile)) {
                @copy($storageSource, $destFile);
            }
        }

        return $path;
    }

    protected function removeFile(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        Storage::disk('public')->delete($relativePath);

        $destFile = $this->getPublicHtmlPath('storage/' . $relativePath);
        if (file_exists($destFile)) {
            @unlink($destFile);
        }
    }

    public function getSystemCustomers(): Collection
    {
        return DB::table('customers')
            ->select('customer_id', 'name', 'customer_code')
            ->orderByRaw('TRIM(LOWER(name)) ASC')
            ->get();
    }

    public function getPortalUsers(?string $filterName = null): Collection
    {
        $query = DB::table('portal_users')
            ->join('customers', 'customers.customer_id', '=', 'portal_users.customer_id')
            ->select(
                'portal_users.id as portal_id',
                'portal_users.id',
                'portal_users.customer_id',
                'portal_users.nombre_contacto',
                'portal_users.empresa',
                'portal_users.email',
                'portal_users.is_active',
                'customers.customer_code',
                'customers.name as customer_name',
                'portal_users.created_at',
                'portal_users.updated_at'
            );

        if ($filterName !== null && trim($filterName) !== '') {
            $query->where('portal_users.empresa', 'like', '%' . trim($filterName) . '%');
        }

        return $query->orderBy('portal_users.created_at', 'desc')->get();
    }

    public function findById(int $id): PortalUser
    {
        return $this->portalUserModel->newQuery()->findOrFail($id);
    }

    public function store(array $data): PortalUser
    {
        return DB::transaction(function () use ($data) {
            $isActive = isset($data['is_active']) && ($data['is_active'] == 1 || $data['is_active'] === true || $data['is_active'] === 'on' || $data['is_active'] === '1');

            return $this->portalUserModel->create([
                'customer_id'     => $data['customer_id'],
                'nombre_contacto' => $data['nombre_contacto'],
                'empresa'         => $data['empresa'],
                'email'           => $data['email'],
                'password'        => Hash::make($data['password']),
                'is_active'       => $isActive ? 1 : 0,
            ]);
        });
    }

    public function update(int $id, array $data): PortalUser
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->portalUserModel->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            $isActive = isset($data['is_active']) && ($data['is_active'] == 1 || $data['is_active'] === true || $data['is_active'] === 'on' || $data['is_active'] === '1');

            $updateData = [
                'customer_id'     => $data['customer_id'],
                'nombre_contacto' => $data['nombre_contacto'],
                'empresa'         => $data['empresa'],
                'email'           => $data['email'],
                'is_active'       => $isActive ? 1 : 0,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            return $user;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = $this->portalUserModel->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            return (bool) $user->delete();
        });
    }

    public function getClientSales(int $portalUserId): Collection
    {
        $user = $this->portalUserModel->find($portalUserId);

        if (!$user) {
            return collect();
        }

        $sales = DB::table('sales')
            ->where('customer_id', $user->customer_id)
            ->select('sale_id', 'folio', 'date')
            ->orderBy('date', 'desc')
            ->get();

        if ($sales->isEmpty()) {
            return collect();
        }

        $saleIds = $sales->pluck('sale_id')->toArray();
        $docs = DB::table('portal_documents')
            ->whereIn('sale_id', $saleIds)
            ->get()
            ->groupBy('sale_id');

        return $sales->map(function ($sale) use ($docs) {
            $saleDocs = $docs->get($sale->sale_id, collect());

            $pdfDoc = $saleDocs->firstWhere('file_type', 'pdf');
            $xmlDoc = $saleDocs->firstWhere('file_type', 'xml');
            $coaDoc = $saleDocs->firstWhere('file_type', 'coa');

            $sale->has_pdf = (bool) $pdfDoc;
            $sale->pdf_path = $pdfDoc ? asset('storage/' . $pdfDoc->file_path) : null;
            $sale->pdf_name = $pdfDoc ? $pdfDoc->file_name : null;

            $sale->has_xml = (bool) $xmlDoc;
            $sale->xml_path = $xmlDoc ? asset('storage/' . $xmlDoc->file_path) : null;
            $sale->xml_name = $xmlDoc ? $xmlDoc->file_name : null;

            $sale->has_coa = (bool) $coaDoc;
            $sale->coa_path = $coaDoc ? asset('storage/' . $coaDoc->file_path) : null;
            $sale->coa_name = $coaDoc ? $coaDoc->file_name : null;

            return $sale;
        });
    }

    public function uploadDocs(int $saleId, ?UploadedFile $pdfFile = null, ?UploadedFile $xmlFile = null, ?UploadedFile $coaFile = null): void
    {
        DB::transaction(function () use ($saleId, $pdfFile, $xmlFile, $coaFile) {
            if ($pdfFile) {
                $oldDoc = DB::table('portal_documents')
                    ->where('sale_id', $saleId)
                    ->where('file_type', 'pdf')
                    ->first();
                if ($oldDoc) {
                    $this->removeFile($oldDoc->file_path);
                }

                $pdfPath = $this->saveFile($pdfFile, 'portal_docs');

                DB::table('portal_documents')->updateOrInsert(
                    ['sale_id' => $saleId, 'file_type' => 'pdf'],
                    [
                        'file_name'  => $pdfFile->getClientOriginalName(),
                        'file_path'  => $pdfPath,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            if ($xmlFile) {
                $oldDoc = DB::table('portal_documents')
                    ->where('sale_id', $saleId)
                    ->where('file_type', 'xml')
                    ->first();
                if ($oldDoc) {
                    $this->removeFile($oldDoc->file_path);
                }

                $xmlPath = $this->saveFile($xmlFile, 'portal_docs');

                DB::table('portal_documents')->updateOrInsert(
                    ['sale_id' => $saleId, 'file_type' => 'xml'],
                    [
                        'file_name'  => $xmlFile->getClientOriginalName(),
                        'file_path'  => $xmlPath,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            if ($coaFile) {
                $oldDoc = DB::table('portal_documents')
                    ->where('sale_id', $saleId)
                    ->where('file_type', 'coa')
                    ->first();
                if ($oldDoc) {
                    $this->removeFile($oldDoc->file_path);
                }

                $coaPath = $this->saveFile($coaFile, 'portal_docs');

                DB::table('portal_documents')->updateOrInsert(
                    ['sale_id' => $saleId, 'file_type' => 'coa'],
                    [
                        'file_name'  => $coaFile->getClientOriginalName(),
                        'file_path'  => $coaPath,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });
    }

    public function deleteDoc(int $saleId, string $type): bool
    {
        return DB::transaction(function () use ($saleId, $type) {
            $doc = DB::table('portal_documents')
                ->where('sale_id', $saleId)
                ->where('file_type', $type)
                ->first();

            if ($doc) {
                $this->removeFile($doc->file_path);

                DB::table('portal_documents')
                    ->where('sale_id', $saleId)
                    ->where('file_type', $type)
                    ->delete();

                return true;
            }

            return false;
        });
    }
}
