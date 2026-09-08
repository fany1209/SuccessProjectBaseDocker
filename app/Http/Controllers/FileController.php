<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    /**
     * Extensiones de documentos permitidas para apertura.
     */
    private const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'];

    /**
     * Sirve un archivo de catálogo o producto de manera segura.
     * Previene Path Traversal (CWE-22) mediante saneamiento de basename,
     * lista blanca de extensiones y confinamiento estricto con realpath().
     *
     * @param string $fileName
     * @return BinaryFileResponse
     */
    public function openFile(string $fileName): BinaryFileResponse
    {
        // 1. Sanitizar parámetro: remover bytes nulos y extraer únicamente el basename
        $sanitized = str_replace(["\0", "\r", "\n"], '', urldecode($fileName));
        $cleanFileName = basename(str_replace('\\', '/', $sanitized));

        if (empty($cleanFileName) || $cleanFileName === '.' || $cleanFileName === '..') {
            abort(404, 'File not found');
        }

        // 2. Validación contra lista blanca de extensiones de documentos
        $extension = strtolower(pathinfo($cleanFileName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            abort(404, 'File not found');
        }

        // 3. Buscar exclusivamente dentro de los directorios autorizados
        // Soporta tanto producción (../public_html hermano de test_migration) como entornos locales/Docker
        $candidateDirs = [
            // Producción GoDaddy (hermano de test_migration)
            base_path('../public_html/files'),
            base_path('../public_html/products/files'),
            base_path('../public_html/orders_files'),
            base_path('../public_html/portal_docs'),
            base_path('../public_html/storage/files'),
            base_path('../public_html/storage/products/files'),
            // Entorno local / Docker
            storage_path('app/public/files'),
            storage_path('app/public/products/files'),
            public_path('files'),
            public_path('products/files'),
            public_path('storage/files'),
            public_path('storage/products/files'),
        ];

        $resolvedPath = null;
        foreach ($candidateDirs as $dir) {
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
            abort(404, 'File not found');
        }

        return response()->file($resolvedPath, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}