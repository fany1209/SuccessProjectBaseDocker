<?php

namespace App\Http\Repositories\File;

class FileRepository
{
    public const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'];

    public function sanitizeFileName(string $fileName): ?string
    {
        $sanitized = str_replace(["\0", "\r", "\n"], '', urldecode($fileName));
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

    public function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');

        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    public function getCandidateDirectories(): array
    {
        return [
            // Producción GoDaddy (hermano de test_migration)
            $this->getPublicHtmlPath('files'),
            $this->getPublicHtmlPath('products/files'),
            $this->getPublicHtmlPath('orders_files'),
            $this->getPublicHtmlPath('portal_docs'),
            $this->getPublicHtmlPath('storage/files'),
            $this->getPublicHtmlPath('storage/products/files'),
            // Entorno local / Docker
            storage_path('app/public/files'),
            storage_path('app/public/products/files'),
            storage_path('app/public'),
            base_path('public/files'),
            base_path('public/products/files'),
            base_path('public/storage/files'),
            base_path('public/storage/products/files'),
        ];
    }

    public function resolveFilePath(string $fileName): ?string
    {
        $cleanFileName = $this->sanitizeFileName($fileName);
        if (!$cleanFileName) {
            return null;
        }

        if (!$this->isValidExtension($cleanFileName)) {
            return null;
        }

        foreach ($this->getCandidateDirectories() as $dir) {
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

    public function getFileInfo(string $fileName): ?array
    {
        $resolvedPath = $this->resolveFilePath($fileName);
        if (!$resolvedPath) {
            return null;
        }

        $size = filesize($resolvedPath) ?: 0;
        $mtime = filemtime($resolvedPath) ?: time();
        $mime = mime_content_type($resolvedPath) ?: 'application/octet-stream';
        $extension = strtolower(pathinfo($resolvedPath, PATHINFO_EXTENSION));

        return [
            'file_name'     => basename($resolvedPath),
            'path'          => $resolvedPath,
            'extension'     => $extension,
            'size_bytes'    => $size,
            'size_human'    => $this->formatBytes($size),
            'mime_type'     => $mime,
            'last_modified' => date('d-m-Y H:i:s', $mtime),
        ];
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
