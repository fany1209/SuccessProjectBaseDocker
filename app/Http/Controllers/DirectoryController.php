<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Directory\DirectoryRepository;
use App\Http\Requests\Directory\DirectoryRequest;
use App\Http\Resources\Directory\SupplierDirectoryResource;
use App\Traits\UtilResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DirectoryController extends Controller
{
    protected UtilResponse $utilResponse;
    protected DirectoryRepository $directoryRepo;

    public function __construct(UtilResponse $utilResponse, DirectoryRepository $directoryRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->directoryRepo = $directoryRepo;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->getDirectory();
    }

    public function getDirectory(): JsonResponse
    {
        try {
            $suppliers = $this->directoryRepo->all();
            $resource = SupplierDirectoryResource::collection($suppliers);

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Proveedores obtenidos correctamente',
                'data' => $resource,
                'suppliers' => $resource,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al obtener directorio de proveedores', [
                'action' => 'DirectoryController@getDirectory',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al obtener los proveedores',
                'error' => 'Error interno al consultar el directorio de proveedores.',
                'data' => [],
                'suppliers' => [],
            ], 500);
        }
    }

    public function show($code): JsonResponse
    {
        try {
            $supplier = $this->directoryRepo->findByCode((int) $code);

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 404,
                    'message' => 'No se encontró el proveedor especificado.',
                    'error' => 'No se encontró el proveedor especificado.',
                    'data' => null,
                ], 404);
            }

            $resource = new SupplierDirectoryResource($supplier);

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Proveedor obtenido correctamente',
                'data' => $resource,
                'supplier' => $resource,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar proveedor en directorio', [
                'action' => 'DirectoryController@show',
                'code' => $code,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al obtener el proveedor',
                'error' => 'Error interno al consultar el proveedor.',
                'data' => null,
            ], 500);
        }
    }

    public function store(DirectoryRequest $request): JsonResponse
    {
        try {
            $supplier = $this->directoryRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 201,
                'message' => 'Proveedor guardado correctamente',
                'data' => new SupplierDirectoryResource($supplier),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar proveedor en directorio', [
                'action' => 'DirectoryController@store',
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al guardar proveedor',
                'error' => 'Error interno al registrar el proveedor.',
                'data' => null,
            ], 500);
        }
    }

    public function update(DirectoryRequest $request, $code): JsonResponse
    {
        try {
            $targetCode = (int) ($request->original_code ?? $code);
            $supplier = $this->directoryRepo->update($targetCode, $request->validated());

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Proveedor actualizado correctamente',
                'data' => new SupplierDirectoryResource($supplier),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 404,
                'message' => 'No se encontró el proveedor especificado.',
                'error' => 'No se encontró el proveedor especificado.',
                'data' => null,
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al actualizar proveedor en directorio', [
                'action' => 'DirectoryController@update',
                'code' => $code,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al actualizar proveedor',
                'error' => 'Error interno al actualizar el proveedor.',
                'data' => null,
            ], 500);
        }
    }

    public function destroy($code): JsonResponse
    {
        try {
            $this->directoryRepo->delete((int) $code);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Proveedor eliminado correctamente.',
                'data' => [],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'success' => false,
                'flag' => false,
                'code' => 404,
                'message' => 'No se encontró el proveedor especificado.',
                'error' => 'No se encontró el proveedor especificado.',
                'data' => [],
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al eliminar proveedor del directorio', [
                'action' => 'DirectoryController@destroy',
                'code' => $code,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al eliminar el proveedor.',
                'error' => 'Error interno al eliminar el proveedor.',
                'data' => [],
            ], 500);
        }
    }
}