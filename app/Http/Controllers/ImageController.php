<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Image\ImageRepository;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ImageController extends Controller
{
    protected UtilResponse $utilResponse;
    protected ImageRepository $imageRepo;

    public function __construct(UtilResponse $utilResponse, ImageRepository $imageRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->imageRepo = $imageRepo;
    }

    public function mostrar(Request $request, string $name): BinaryFileResponse|JsonResponse
    {
        try {
            $resolvedPath = $this->imageRepo->resolveProductImagePath($name);

            if (!$resolvedPath) {
                if ($request->expectsJson()) {
                    return $this->utilResponse->errorResponse('Imagen no encontrada o no permitida.', 404);
                }

                abort(404, 'Image not found');
            }

            if ($request->expectsJson() && $request->has('info')) {
                $info = $this->imageRepo->getImageInfo($resolvedPath);

                return $this->utilResponse->successResponse(
                    $info,
                    'Metadatos de imagen obtenidos correctamente',
                    200
                );
            }

            return response()->file($resolvedPath, [
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control'          => 'public, max-age=86400',
            ]);
        } catch (Throwable $e) {
            if ($e instanceof HttpException) {
                throw $e;
            }

            Log::error('Error al servir imagen en ImageController@mostrar', [
                'action'     => 'ImageController@mostrar',
                'image_name' => $name,
                'user_id'    => auth()->id(),
                'error'      => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error interno al consultar la imagen.', 500);
            }

            abort(500, 'Error processing image');
        }
    }

    public function profilePhoto(Request $request, string $name): BinaryFileResponse|JsonResponse
    {
        try {
            $resolvedPath = $this->imageRepo->resolveProfilePhotoPath($name);

            if (!$resolvedPath) {
                if ($request->expectsJson()) {
                    return $this->utilResponse->errorResponse('Foto de perfil no encontrada o no permitida.', 404);
                }

                abort(404, 'Profile photo not found');
            }

            if ($request->expectsJson() && $request->has('info')) {
                $info = $this->imageRepo->getImageInfo($resolvedPath);

                return $this->utilResponse->successResponse(
                    $info,
                    'Metadatos de foto de perfil obtenidos correctamente',
                    200
                );
            }

            return response()->file($resolvedPath, [
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control'          => 'private, max-age=3600',
            ]);
        } catch (Throwable $e) {
            if ($e instanceof HttpException) {
                throw $e;
            }

            Log::error('Error al servir foto de perfil en ImageController@profilePhoto', [
                'action'     => 'ImageController@profilePhoto',
                'photo_name' => $name,
                'user_id'    => auth()->id(),
                'error'      => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error interno al consultar la foto de perfil.', 500);
            }

            abort(500, 'Error processing profile photo');
        }
    }
}
