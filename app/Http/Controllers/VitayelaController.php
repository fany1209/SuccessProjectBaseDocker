<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Vitayela\VitayelaRepository;
use App\Http\Requests\Vitayela\VitayelaInventoryOutputRequest;
use App\Http\Requests\Vitayela\VitayelaInventoryStoreRequest;
use App\Http\Requests\Vitayela\VitayelaMaterialRequestStoreRequest;
use App\Http\Requests\Vitayela\VitayelaProductionStoreRequest;
use App\Http\Requests\Vitayela\VitayelaTransferToWarehouseRequest;
use App\Http\Resources\Vitayela\VitayelaInventoryResource;
use App\Http\Resources\Vitayela\VitayelaMovementResource;
use App\Http\Resources\Vitayela\VitayelaProductionResource;
use App\Traits\UtilResponse;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class VitayelaController extends Controller
{
    protected UtilResponse $utilResponse;
    protected VitayelaRepository $vitayelaRepo;

    public function __construct(UtilResponse $utilResponse, VitayelaRepository $vitayelaRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->vitayelaRepo = $vitayelaRepo;
    }

    public function index()
    {
        $productions = $this->vitayelaRepo->getAllProductions();
        $inventories = $this->vitayelaRepo->getAllInventories();
        $db_products = $this->vitayelaRepo->getAllProducts();

        return view('production.vitayela.index', compact('productions', 'inventories', 'db_products'));
    }

    public function storeProduction(VitayelaProductionStoreRequest $request)
    {
        try {
            $production = $this->vitayelaRepo->storeProduction($request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag' => true,
                    'code' => 201,
                    'message' => 'Registro agregado correctamente.',
                    'data' => new VitayelaProductionResource($production),
                ], 201);
            }

            return redirect()->route('production.vitayela.index')->with('success', 'Registro de producción agregado correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al guardar registro de producción Vitayela', [
                'action' => 'VitayelaController@storeProduction',
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 500,
                    'message' => 'Error al guardar el registro de producción.',
                    'data' => null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al guardar el registro de producción.');
        }
    }

    public function updateProduction(VitayelaProductionStoreRequest $request, $id)
    {
        try {
            $production = $this->vitayelaRepo->updateProduction((int) $id, $request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag' => true,
                    'code' => 200,
                    'message' => 'Registro actualizado correctamente.',
                    'data' => new VitayelaProductionResource($production),
                ], 200);
            }

            return redirect()->route('production.vitayela.index')->with('success', 'Registro de producción actualizado correctamente.');
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 404,
                    'message' => 'Registro de producción no encontrado',
                    'data' => null,
                ], 404);
            }

            return back()->with('error', 'Registro de producción no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar registro de producción Vitayela', [
                'action' => 'VitayelaController@updateProduction',
                'id' => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 500,
                    'message' => 'Error al actualizar el registro de producción.',
                    'data' => null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar el registro de producción.');
        }
    }

    public function destroyProduction($id)
    {
        try {
            $this->vitayelaRepo->destroyProduction((int) $id);

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag' => true,
                    'code' => 200,
                    'message' => 'Registro eliminado correctamente.',
                    'data' => [],
                ], 200);
            }

            return redirect()->route('production.vitayela.index')->with('success', 'Registro de producción eliminado.');
        } catch (ModelNotFoundException $e) {
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 404,
                    'message' => 'Registro de producción no encontrado',
                    'data' => [],
                ], 404);
            }

            return back()->with('error', 'Registro de producción no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar registro de producción Vitayela', [
                'action' => 'VitayelaController@destroyProduction',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 500,
                    'message' => 'Error al eliminar el registro de producción.',
                    'data' => [],
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al eliminar el registro de producción.');
        }
    }

    public function storeInventory(VitayelaInventoryStoreRequest $request)
    {
        try {
            $inv = $this->vitayelaRepo->storeInventory($request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag' => true,
                    'code' => 201,
                    'message' => 'Producto agregado correctamente.',
                    'data' => new VitayelaInventoryResource($inv),
                ], 201);
            }

            return redirect()->route('production.vitayela.index')->with('success', 'Registro de inventario agregado correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al guardar producto en inventario Vitayela', [
                'action' => 'VitayelaController@storeInventory',
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 500,
                    'message' => 'Error al guardar el producto de inventario.',
                    'data' => null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al guardar el producto en inventario.');
        }
    }

    public function updateInventory(VitayelaInventoryStoreRequest $request, $id)
    {
        try {
            $inventory = $this->vitayelaRepo->updateInventory((int) $id, $request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag' => true,
                    'code' => 200,
                    'message' => 'Producto actualizado correctamente.',
                    'data' => new VitayelaInventoryResource($inventory),
                ], 200);
            }

            return redirect()->route('production.vitayela.index')->with('success', 'Registro de inventario actualizado correctamente.');
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 404,
                    'message' => 'Producto de inventario no encontrado',
                    'data' => null,
                ], 404);
            }

            return back()->with('error', 'Producto de inventario no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar inventario Vitayela', [
                'action' => 'VitayelaController@updateInventory',
                'id' => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 500,
                    'message' => 'Error al actualizar el producto de inventario.',
                    'data' => null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar el producto de inventario.');
        }
    }

    public function destroyInventory($id)
    {
        try {
            $this->vitayelaRepo->destroyInventory((int) $id);

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag' => true,
                    'code' => 200,
                    'message' => 'Producto eliminado correctamente.',
                    'data' => [],
                ], 200);
            }

            return redirect()->route('production.vitayela.index')->with('success', 'Registro de inventario eliminado.');
        } catch (ModelNotFoundException $e) {
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 404,
                    'message' => 'Producto de inventario no encontrado',
                    'data' => [],
                ], 404);
            }

            return back()->with('error', 'Producto de inventario no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar inventario Vitayela', [
                'action' => 'VitayelaController@destroyInventory',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 500,
                    'message' => 'Error al eliminar el producto de inventario.',
                    'data' => [],
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al eliminar el producto de inventario.');
        }
    }

    public function outputInventory(VitayelaInventoryOutputRequest $request, $id): JsonResponse
    {
        try {
            $result = $this->vitayelaRepo->outputInventory((int) $id, (float) $request->validated()['cantidad_salida']);

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => $result['message'],
                'alert' => $result['alert'],
                'data' => new VitayelaInventoryResource($result['inventory']),
            ], 200);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 400,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
                'data' => null,
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 404,
                'message' => 'Producto de inventario no encontrado',
                'error' => 'Producto de inventario no encontrado',
                'data' => null,
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al registrar salida de inventario en Vitayela', [
                'action' => 'VitayelaController@outputInventory',
                'id' => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al procesar la salida de inventario.',
                'error' => 'Error interno al procesar salida.',
                'data' => null,
            ], 500);
        }
    }

    public function transferToWarehouse(VitayelaTransferToWarehouseRequest $request, $id): JsonResponse
    {
        try {
            $transfer = $this->vitayelaRepo->transferToWarehouse((int) $id, $request->validated(), (int) auth()->id());

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Producto enviado a almacén exitosamente.',
                'alert' => false,
                'data' => $transfer,
            ], 200);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 400,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
                'data' => null,
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 404,
                'message' => 'Producto de inventario no encontrado',
                'error' => 'Producto de inventario no encontrado',
                'data' => null,
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al transferir a almacén en Vitayela', [
                'action' => 'VitayelaController@transferToWarehouse',
                'id' => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag' => false,
                'code' => 500,
                'message' => 'Error al procesar la transferencia.',
                'error' => 'Error interno al procesar transferencia.',
                'data' => null,
            ], 500);
        }
    }
    

    public function getMovements($id): JsonResponse
    {
        try {
            $movements = $this->vitayelaRepo->getMovements((int) $id);
            $resolved = VitayelaMovementResource::collection($movements)->resolve();

            return response()->json($resolved, 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar movimientos de inventario en Vitayela', [
                'action' => 'VitayelaController@getMovements',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([], 500);
        }
    }

    public function storeMaterialRequest(VitayelaMaterialRequestStoreRequest $request)
    {
        try {
            $materialRequest = $this->vitayelaRepo->storeMaterialRequest($request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag' => true,
                    'code' => 201,
                    'message' => 'Solicitud enviada a almacén correctamente.',
                    'data' => $materialRequest,
                ], 201);
            }

            return redirect()->back()->with('success', 'Solicitud enviada a almacén correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar solicitud de materiales en Vitayela', [
                'action' => 'VitayelaController@storeMaterialRequest',
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag' => false,
                    'code' => 500,
                    'message' => 'Error al enviar la solicitud.',
                    'data' => null,
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al enviar la solicitud.');
        }
    }
}
