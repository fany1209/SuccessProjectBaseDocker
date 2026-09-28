<?php

namespace App\Http\Repositories\Image;

use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ImageRepository
{
    protected Image $model;

    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'];

    public function __construct(Image $model)
    {
        $this->model = $model;
    }

    public function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');

        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    public function sanitizeFileName(string $rawFileName): ?string
    {
        if (str_contains($rawFileName, "\0") || str_contains($rawFileName, '%00')) {
            return null;
        }

        $sanitized = str_replace(["\0", "\r", "\n"], '', urldecode($rawFileName));
        $cleanFileName = basename(str_replace('\\', '/', $sanitized));

        if (empty($cleanFileName) || $cleanFileName === '.' || $cleanFileName === '..') {
            return null;
        }

        return $cleanFileName;
    }

    public function isValidExtension(string $fileName): bool
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }

    public function getProductCandidateDirectories(): array
    {
        return [
            // Producción GoDaddy (hermano de test_migration)
            $this->getPublicHtmlPath('images'),
            $this->getPublicHtmlPath('images/products'),
            $this->getPublicHtmlPath('products'),
            $this->getPublicHtmlPath('storage/products'),
            $this->getPublicHtmlPath('storage/images'),
            // Entorno local / Docker (desacoplado sin public_path())
            storage_path('app/public/products'),
            storage_path('app/public/images'),
            base_path('public/products'),
            base_path('public/images'),
            base_path('public/storage/products'),
        ];
    }

    public function getProfilePhotoCandidateDirectories(): array
    {
        return [
            // Producción GoDaddy (hermano de test_migration)
            $this->getPublicHtmlPath('profile-photos'),
            $this->getPublicHtmlPath('images/profile-photos'),
            $this->getPublicHtmlPath('images'),
            $this->getPublicHtmlPath('storage/profile-photos'),
            // Entorno local / Docker (desacoplado sin public_path())
            storage_path('app/public/profile-photos'),
            storage_path('app/public/images/profile-photos'),
            base_path('public/profile-photos'),
            base_path('public/storage/profile-photos'),
        ];
    }

    public function resolveProductImagePath(string $rawFileName): ?string
    {
        return $this->resolveSafeFile($this->getProductCandidateDirectories(), $rawFileName);
    }

    public function resolveProfilePhotoPath(string $rawFileName): ?string
    {
        return $this->resolveSafeFile($this->getProfilePhotoCandidateDirectories(), $rawFileName);
    }

    protected function resolveSafeFile(array $candidateDirs, string $rawFileName): ?string
    {
        $cleanFileName = $this->sanitizeFileName($rawFileName);
        if (!$cleanFileName) {
            return null;
        }

        if (!$this->isValidExtension($cleanFileName)) {
            return null;
        }

        foreach ($candidateDirs as $dir) {
            $baseDir = realpath($dir);
            if (!$baseDir) {
                continue;
            }

            $candidatePath = realpath($baseDir . DIRECTORY_SEPARATOR . $cleanFileName);

            // Verificación estricta de confinamiento contra Path Traversal (CWE-22)
            if ($candidatePath && str_starts_with($candidatePath, $baseDir . DIRECTORY_SEPARATOR) && is_file($candidatePath)) {
                return $candidatePath;
            }
        }

        return null;
    }

    public function getImageInfo(string $resolvedPath): array
    {
        $size = filesize($resolvedPath) ?: 0;
        $mtime = filemtime($resolvedPath) ?: time();
        $mime = mime_content_type($resolvedPath) ?: 'image/jpeg';
        $extension = strtolower(pathinfo($resolvedPath, PATHINFO_EXTENSION));

        $dimensions = @getimagesize($resolvedPath);

        return [
            'file_name'     => basename($resolvedPath),
            'extension'     => $extension,
            'size_bytes'    => $size,
            'size_human'    => $this->formatBytes($size),
            'mime_type'     => $mime,
            'width'         => $dimensions ? $dimensions[0] : null,
            'height'        => $dimensions ? $dimensions[1] : null,
            'last_modified' => date('d-m-Y H:i:s', $mtime),
        ];
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function findByProduct(int $productId): Collection
    {
        return $this->model->where('product_id', $productId)->get();
    }

    public function create(array $data): Image
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }
}
