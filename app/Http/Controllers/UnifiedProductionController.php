<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Production\UnifiedProductionRepository;
use App\Http\Requests\Production\ProductionInventoryRequest;
use App\Http\Requests\Production\ProductionMaterialRequestStoreRequest;
use App\Http\Requests\Production\ProductionOutputInventoryRequest;
use App\Http\Requests\Production\ProductionRecordRequest;
use App\Http\Requests\Production\ProductionTransferToWarehouseRequest;
use App\Http\Resources\Production\ProductionInventoryMovementResource;
use App\Http\Resources\Production\ProductionInventoryResource;
use App\Http\Resources\Production\ProductionMaterialRequestResource;
use App\Http\Resources\Production\ProductionRecordResource;
use App\Traits\UtilResponse;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class UnifiedProductionController extends Controller
{
    protected UtilResponse $utilResponse;
    protected UnifiedProductionRepository $productionRepo;

    public function __construct(UtilResponse $utilResponse, UnifiedProductionRepository $productionRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->productionRepo = $productionRepo;
    }

    public function index(Request $request)
    {
        try {
            $selectedArea = $request->query('area');
            if ($selectedArea && !in_array($selectedArea, ['fertil', 'vitayela'], true)) {
                $selectedArea = null;
            }

            $productions = $this->productionRepo->getAllProductions();
            $inventories = $this->productionRepo->getAllInventories();
            $db_products = $this->productionRepo->getDbProducts();

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->successResponse([
                    'productions' => ProductionRecordResource::collection($productions),
                    'inventories' => ProductionInventoryResource::collection($inventories),
                    'db_products' => $db_products,
                ], 'Datos de producción obtenidos correctamente.', 200);
            }

            return view('production.records.index', compact('productions', 'inventories', 'db_products', 'selectedArea'));
        } catch (Throwable $e) {
            Log::error('Error al cargar la vista unificada de producción', [
                'action'    => 'UnifiedProductionController@index',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al obtener los datos de producción.', 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar el módulo de producción.');
        }
    }

    public function storeProduction(ProductionRecordRequest $request)
    {
        try {
            $production = $this->productionRepo->createProduction($request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->successResponse(
                    new ProductionRecordResource($production),
                    'Registro de producción agregado correctamente.',
                    201
                );
            }

            return redirect()->route('production.records.index')->with('success', 'Registro de producción agregado correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar producción unificada', [
                'action'    => 'UnifiedProductionController@storeProduction',
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al guardar el registro de producción.', 500);
            }

            return back()->with('error', 'Ocurrió un error al guardar el registro de producción.');
        }
    }

    public function updateProduction(ProductionRecordRequest $request, $id)
    {
        try {
            $production = $this->productionRepo->updateProduction((int) $id, $request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->successResponse(
                    new ProductionRecordResource($production),
                    'Registro de producción actualizado correctamente.',
                    200
                );
            }

            return redirect()->route('production.records.index')->with('success', 'Registro de producción actualizado correctamente.');
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->errorResponse('Registro de producción no encontrado.', 404);
            }

            return back()->with('error', 'Registro de producción no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar producción unificada', [
                'action'    => 'UnifiedProductionController@updateProduction',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al actualizar el registro de producción.', 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar el registro de producción.');
        }
    }

    public function destroyProduction($id)
    {
        try {
            $this->productionRepo->deleteProduction((int) $id);

            if (request()->ajax() || request()->expectsJson()) {
                return $this->utilResponse->successResponse([], 'Registro eliminado correctamente.', 200);
            }

            return redirect()->route('production.records.index')->with('success', 'Registro de producción eliminado.');
        } catch (ModelNotFoundException $e) {
            if (request()->ajax() || request()->expectsJson()) {
                return $this->utilResponse->errorResponse('Registro de producción no encontrado.', 404);
            }

            return back()->with('error', 'Registro de producción no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar producción unificada', [
                'action'    => 'UnifiedProductionController@destroyProduction',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if (request()->ajax() || request()->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al eliminar el registro de producción.', 500);
            }

            return back()->with('error', 'Ocurrió un error al eliminar el registro de producción.');
        }
    }

    public function storeInventory(ProductionInventoryRequest $request)
    {
        try {
            $inv = $this->productionRepo->createInventory($request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->successResponse(
                    new ProductionInventoryResource($inv),
                    'Producto agregado al inventario correctamente.',
                    201
                );
            }

            return redirect()->route('production.records.index')->with('success', 'Producto agregado al inventario correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar producto en inventario de producción', [
                'action'    => 'UnifiedProductionController@storeInventory',
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al agregar el producto al inventario.', 500);
            }

            return back()->with('error', 'Ocurrió un error al registrar el producto de inventario.');
        }
    }

    public function updateInventory(ProductionInventoryRequest $request, $id)
    {
        try {
            $inventory = $this->productionRepo->updateInventory((int) $id, $request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->successResponse(
                    new ProductionInventoryResource($inventory),
                    'Producto actualizado correctamente.',
                    200
                );
            }

            return redirect()->route('production.records.index')->with('success', 'Producto actualizado correctamente.');
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->errorResponse('Producto de inventario no encontrado.', 404);
            }

            return back()->with('error', 'Producto de inventario no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar inventario de producción', [
                'action'    => 'UnifiedProductionController@updateInventory',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al actualizar el producto de inventario.', 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar el producto de inventario.');
        }
    }

    public function destroyInventory($id)
    {
        try {
            $this->productionRepo->deleteInventory((int) $id);

            if (request()->ajax() || request()->expectsJson()) {
                return $this->utilResponse->successResponse([], 'Producto eliminado correctamente.', 200);
            }

            return redirect()->route('production.records.index')->with('success', 'Producto de inventario eliminado.');
        } catch (ModelNotFoundException $e) {
            if (request()->ajax() || request()->expectsJson()) {
                return $this->utilResponse->errorResponse('Producto de inventario no encontrado.', 404);
            }

            return back()->with('error', 'Producto de inventario no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar producto de inventario de producción', [
                'action'    => 'UnifiedProductionController@destroyInventory',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if (request()->ajax() || request()->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al eliminar el producto de inventario.', 500);
            }

            return back()->with('error', 'Ocurrió un error al eliminar el producto de inventario.');
        }
    }

    public function outputInventory(ProductionOutputInventoryRequest $request, $id): JsonResponse
    {
        try {
            $result = $this->productionRepo->outputInventory((int) $id, (float) $request->validated()['cantidad_salida']);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => $result['message'],
                'alert'   => $result['alert'],
                'data'    => new ProductionInventoryResource($result['inventory']),
            ], 200);
        } catch (DomainException $e) {
            return $this->utilResponse->errorResponse($e->getMessage(), 400);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Producto de inventario no encontrado.', 404);
        } catch (Throwable $e) {
            Log::error('Error al registrar salida de inventario en producción', [
                'action'    => 'UnifiedProductionController@outputInventory',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al procesar la salida de inventario.', 500);
        }
    }

    public function transferToWarehouse(ProductionTransferToWarehouseRequest $request, $id): JsonResponse
    {
        try {
            $transfer = $this->productionRepo->transferToWarehouse((int) $id, $request->validated(), (int) auth()->id());

            return $this->utilResponse->successResponse(
                $transfer,
                'Producto enviado a almacén exitosamente.',
                200
            );
        } catch (DomainException $e) {
            return $this->utilResponse->errorResponse($e->getMessage(), 400);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Producto de inventario no encontrado.', 404);
        } catch (Throwable $e) {
            Log::error('Error al transferir producto a almacén desde producción', [
                'action'    => 'UnifiedProductionController@transferToWarehouse',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al procesar la transferencia a almacén.', 500);
        }
    }

    public function getMovements($id): JsonResponse
    {
        try {
            $movements = $this->productionRepo->getMovements((int) $id);
            $resolved = ProductionInventoryMovementResource::collection($movements)->resolve();

            return response()->json($resolved, 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar movimientos de inventario en producción', [
                'action'    => 'UnifiedProductionController@getMovements',
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([], 500);
        }
    }

    public function storeMaterialRequest(ProductionMaterialRequestStoreRequest $request)
    {
        try {
            $materialRequest = $this->productionRepo->createMaterialRequest($request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->successResponse(
                    new ProductionMaterialRequestResource($materialRequest),
                    'Solicitud enviada a almacén correctamente.',
                    201
                );
            }

            return redirect()->back()->with('success', 'Solicitud enviada a almacén correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar solicitud de materiales en producción', [
                'action'    => 'UnifiedProductionController@storeMaterialRequest',
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al enviar la solicitud a almacén.', 500);
            }

            return redirect()->back()->with('error', 'Error al enviar la solicitud.');
        }
    }
}

