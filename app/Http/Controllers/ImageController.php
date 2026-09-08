<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    /**
     * Extensiones de imagen permitidas.
     */
    private const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'];

    /**
     * Sirve imágenes de productos de forma segura.
     * Previene Path Traversal (CWE-22) confinando el archivo al directorio autorizado.
     *
     * @param string $name
     * @return BinaryFileResponse
     */
    public function mostrar(string $name): BinaryFileResponse
    {
        return $this->serveSafeImage([
            // Producción GoDaddy (hermano de test_migration)
            base_path('../public_html/images'),
            base_path('../public_html/images/products'),
            base_path('../public_html/products'),
            base_path('../public_html/storage/products'),
            base_path('../public_html/storage/images'),
            // Entorno local / Docker
            storage_path('app/public/products'),
            storage_path('app/public/images'),
            public_path('products'),
            public_path('images'),
            public_path('storage/products'),
        ], $name);
    }

    /**
     * Sirve fotos de perfil de usuarios de forma segura.
     * Previene Path Traversal (CWE-22) confinando el archivo al directorio autorizado.
     *
     * @param string $name
     * @return BinaryFileResponse
     */
    public function profilePhoto(string $name): BinaryFileResponse
    {
        return $this->serveSafeImage([
            // Producción GoDaddy (hermano de test_migration)
            base_path('../public_html/profile-photos'),
            base_path('../public_html/images/profile-photos'),
            base_path('../public_html/images'),
            base_path('../public_html/storage/profile-photos'),
            // Entorno local / Docker
            storage_path('app/public/profile-photos'),
            storage_path('app/public/images/profile-photos'),
            public_path('profile-photos'),
            public_path('storage/profile-photos'),
        ], $name);
    }

    /**
     * Valida y confina la entrega de imágenes dentro de los directorios base autorizados.
     *
     * @param array|string $baseDirPaths
     * @param string $rawFileName
     * @return BinaryFileResponse
     */
    private function serveSafeImage(array|string $baseDirPaths, string $rawFileName): BinaryFileResponse
    {
        // 1. Sanitizar parámetro: remover bytes nulos y extraer exclusivamente el basename
        $sanitized = str_replace(["\0", "\r", "\n"], '', urldecode($rawFileName));
        $cleanFileName = basename(str_replace('\\', '/', $sanitized));

        if (empty($cleanFileName) || $cleanFileName === '.' || $cleanFileName === '..') {
            abort(404, 'Image not found');
        }

        // 2. Validación estricta de extensión contra lista blanca de formatos de imagen
        $extension = strtolower(pathinfo($cleanFileName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS, true)) {
            abort(404, 'Image not found');
        }

        // 3. Buscar y verificar confinamiento dentro de los directorios autorizados
        $dirs = is_array($baseDirPaths) ? $baseDirPaths : [$baseDirPaths];
        $resolvedPath = null;

        foreach ($dirs as $dir) {
            $baseDir = realpath($dir);
            if (!$baseDir) {
                continue;
            }

            $candidatePath = realpath($baseDir . DIRECTORY_SEPARATOR . $cleanFileName);

            // Verificar que el archivo exista, sea un archivo regular y resida dentro del directorio base
            if ($candidatePath && str_starts_with($candidatePath, $baseDir . DIRECTORY_SEPARATOR) && is_file($candidatePath)) {
                $resolvedPath = $candidatePath;
                break;
            }
        }

        if (!$resolvedPath) {
            abort(404, 'Image not found');
        }

        return response()->file($resolvedPath, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

