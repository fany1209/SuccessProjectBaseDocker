<?php

namespace App\Http\Controllers;

use App\Http\Repositories\File\FileRepository;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class FileController extends Controller
{
    protected UtilResponse $utilResponse;
    protected FileRepository $fileRepo;

    public function __construct(UtilResponse $utilResponse, FileRepository $fileRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->fileRepo = $fileRepo;
    }

    public function openFile(Request $request, string $fileName): BinaryFileResponse|JsonResponse
    {
        try {
            $resolvedPath = $this->fileRepo->resolveFilePath($fileName);

            if (!$resolvedPath) {
                if ($request->expectsJson()) {
                    return $this->utilResponse->errorResponse('Archivo no encontrado o no permitido.', 404);
                }

                abort(404, 'File not found');
            }

            if ($request->expectsJson() && $request->has('info')) {
                $fileInfo = $this->fileRepo->getFileInfo($fileName);

                return $this->utilResponse->successResponse(
                    $fileInfo,
                    'Metadatos de archivo obtenidos correctamente',
                    200
                );
            }

            return response()->file($resolvedPath, [
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control'          => 'private, max-age=3600',
            ]);
        } catch (Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }

            Log::error('Error al abrir archivo seguro en FileController@openFile', [
                'action'    => 'FileController@openFile',
                'file_name' => $fileName,
                'user_id'   => auth()->id(),
                'error'     => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al procesar la apertura del archivo.', 500);
            }

            abort(500, 'Error processing file');
        }
    }
}