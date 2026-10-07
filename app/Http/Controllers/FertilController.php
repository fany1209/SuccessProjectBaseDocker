<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Fertil\FertilRepository;
use App\Http\Requests\Fertil\FertilInventoryRequest;
use App\Http\Requests\Fertil\FertilMaterialRequest;
use App\Http\Requests\Fertil\FertilOutputInventoryRequest;
use App\Http\Requests\Fertil\FertilProductionRequest;
use App\Http\Resources\Fertil\FertilInventoryMovementResource;
use App\Http\Resources\Fertil\FertilInventoryResource;
use App\Http\Resources\Fertil\FertilMaterialRequestResource;
use App\Http\Resources\Fertil\FertilProductionResource;
use App\Traits\UtilResponse;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class FertilController extends Controller
{
    protected UtilResponse $utilResponse;
    protected FertilRepository $fertilRepo;

    public function __construct(UtilResponse $utilResponse, FertilRepository $fertilRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->fertilRepo = $fertilRepo;
    }

    public function index(Request $request)
    {
        try {
            $productions = $this->fertilRepo->getAllProductions();
            $inventories = $this->fertilRepo->getAllInventories();
            $db_products = $this->fertilRepo->getDbProducts();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 200,
                    'message' => 'Datos de Fertil obtenidos correctamente',
                    'data'    => [
                        'productions' => FertilProductionResource::collection($productions),
                        'inventories' => FertilInventoryResource::collection($inventories),
                        'db_products' => $db_products,
                    ],
                ], 200);
            }

            return view('production.fertil.index', compact('productions', 'inventories', 'db_products'));
        } catch (Throwable $e) {
            Log::error('Error al cargar la vista de fertil', [
                'action'  => 'FertilController@index',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Error al obtener los datos de Fertil',
                    'error'   => 'Error interno al consultar datos de Fertil.',
                    'data'    => null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar la vista de fertil.');
        }
    }

    public function storeProduction(FertilProductionRequest $request)
    {
        try {
            $production = $this->fertilRepo->createProduction($request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 201,
                    'message' => 'Registro agregado correctamente.',
                    'data'    => new FertilProductionResource($production),
                ], 201);
            }

            return redirect()->route('production.fertil.index')->with('success', 'Registro de producción agregado correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar producción de fertil', [
                'action'  => 'FertilController@storeProduction',
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Error al guardar el registro de producción.',
                    'error'   => 'Error interno al guardar la producción.',
                    'data'    => null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al guardar el registro de producción.');
        }
    }

    public function updateProduction(FertilProductionRequest $request, $id)
    {
        try {
            $production = $this->fertilRepo->updateProduction((int) $id, $request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 200,
                    'message' => 'Registro actualizado correctamente.',
                    'data'    => new FertilProductionResource($production),
                ], 200);
            }

            return redirect()->route('production.fertil.index')->with('success', 'Registro de producción actualizado correctamente.');
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Registro de producción no encontrado',
                    'error'   => 'Registro de producción no encontrado',
                    'data'    => null,
                ], 404);
            }

            return back()->with('error', 'Registro de producción no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar producción de fertil', [
                'action'  => 'FertilController@updateProduction',
                'id'      => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Error al actualizar el registro de producción.',
                    'error'   => 'Error interno al actualizar la producción.',
                    'data'    => null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar el registro de producción.');
        }
    }

    public function destroyProduction($id)
    {
        try {
            $this->fertilRepo->deleteProduction((int) $id);

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 200,
                    'message' => 'Registro eliminado correctamente.',
                    'data'    => [],
                ], 200);
            }

            return redirect()->route('production.fertil.index')->with('success', 'Registro de producción eliminado.');
        } catch (ModelNotFoundException $e) {
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Registro de producción no encontrado',
                    'error'   => 'Registro de producción no encontrado',
                    'data'    => [],
                ], 404);
            }

            return back()->with('error', 'Registro de producción no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar producción de fertil', [
                'action'  => 'FertilController@destroyProduction',
                'id'      => $id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Error al eliminar el registro de producción.',
                    'error'   => 'Error interno al eliminar la producción.',
                    'data'    => [],
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al eliminar el registro de producción.');
        }
    }

    public function storeInventory(FertilInventoryRequest $request)
    {
        try {
            $inv = $this->fertilRepo->createInventory($request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 201,
                    'message' => 'Producto agregado correctamente.',
                    'data'    => new FertilInventoryResource($inv),
                ], 201);
            }

            return redirect()->route('production.fertil.index')->with('success', 'Registro de inventario agregado correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar producto en inventario de fertil', [
                'action'  => 'FertilController@storeInventory',
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Error al agregar el producto al inventario.',
                    'error'   => 'Error interno al agregar producto.',
                    'data'    => null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al registrar el producto de inventario.');
        }
    }

    public function updateInventory(FertilInventoryRequest $request, $id)
    {
        try {
            $inventory = $this->fertilRepo->updateInventory((int) $id, $request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 200,
                    'message' => 'Producto actualizado correctamente.',
                    'data'    => new FertilInventoryResource($inventory),
                ], 200);
            }

            return redirect()->route('production.fertil.index')->with('success', 'Registro de inventario actualizado correctamente.');
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Producto de inventario no encontrado',
                    'error'   => 'Producto de inventario no encontrado',
                    'data'    => null,
                ], 404);
            }

            return back()->with('error', 'Producto de inventario no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar inventario de fertil', [
                'action'  => 'FertilController@updateInventory',
                'id'      => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Error al actualizar el producto de inventario.',
                    'error'   => 'Error interno al actualizar el producto.',
                    'data'    => null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar el producto de inventario.');
        }
    }

    public function destroyInventory($id)
    {
        try {
            $this->fertilRepo->deleteInventory((int) $id);

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 200,
                    'message' => 'Producto eliminado correctamente.',
                    'data'    => [],
                ], 200);
            }

            return redirect()->route('production.fertil.index')->with('success', 'Registro de inventario eliminado.');
        } catch (ModelNotFoundException $e) {
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Producto de inventario no encontrado',
                    'error'   => 'Producto de inventario no encontrado',
                    'data'    => [],
                ], 404);
            }

            return back()->with('error', 'Producto de inventario no encontrado.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar producto de inventario de fertil', [
                'action'  => 'FertilController@destroyInventory',
                'id'      => $id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Error al eliminar el producto de inventario.',
                    'error'   => 'Error interno al eliminar el producto.',
                    'data'    => [],
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al eliminar el producto de inventario.');
        }
    }

    public function outputInventory(FertilOutputInventoryRequest $request, $id): JsonResponse
    {
        try {
            $result = $this->fertilRepo->outputInventory((int) $id, (float) $request->validated()['cantidad_salida']);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => $result['message'],
                'alert'   => $result['alert'],
                'data'    => new FertilInventoryResource($result['inventory']),
            ], 200);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 400,
                'message' => $e->getMessage(),
                'error'   => $e->getMessage(),
                'data'    => null,
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 404,
                'message' => 'Producto de inventario no encontrado',
                'error'   => 'Producto de inventario no encontrado',
                'data'    => null,
            ], 404);
        } catch (Throwable $e) {
            Log::error('Error al registrar salida de inventario en fertil', [
                'action'  => 'FertilController@outputInventory',
                'id'      => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Error al procesar la salida de inventario.',
                'error'   => 'Error interno al procesar salida.',
                'data'    => null,
            ], 500);
        }
    }

    public function transferToWarehouse(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'cantidad_salida' => 'required|numeric|min:0.01',
            'peso_por_unidad' => 'required|numeric|min:0.01',
            'unit_type'       => 'nullable|string|max:50',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($id, $validated) {
                // This implicitly uses outputInventory logic or does it manually inside the transaction
                $inventory = \App\Models\FertilInventory::where('fertil_inventory_id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($validated['cantidad_salida'] > $inventory->cantidad) {
                    throw new DomainException('La cantidad a enviar no puede ser mayor a la cantidad en stock.');
                }

                $inventory->cantidad -= $validated['cantidad_salida'];
                $inventory->save();

                \App\Models\FertilInventoryMovement::create([
                    'fertil_inventory_id' => $inventory->fertil_inventory_id,
                    'tipo'                => 'Salida',
                    'cantidad'            => $validated['cantidad_salida'],
                ]);

                \App\Models\ProductionWarehouseTransfer::create([
                    'area' => 'fertil',
                    'production_id' => $id,
                    'product_id' => $validated['product_id'],
                    'product_name' => $inventory->producto_descripcion,
                    'quantity' => $validated['cantidad_salida'],
                    'unit_type' => $validated['unit_type'] ?? null,
                    'weight_per_unit' => $validated['peso_por_unidad'],
                    'total_weight' => $validated['cantidad_salida'] * $validated['peso_por_unidad'],
                    'status' => 'Pendiente',
                    'created_by' => auth()->id(),
                ]);
            });

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Producto enviado a almacén exitosamente.',
                'alert'   => false,
                'data'    => null,
            ], 200);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 400,
                'message' => $e->getMessage(),
                'error'   => $e->getMessage(),
                'data'    => null,
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Error al transferir a almacen en fertil', [
                'action'  => 'FertilController@transferToWarehouse',
                'id'      => $id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Error al procesar la transferencia.',
                'error'   => 'Error interno al procesar transferencia.',
                'data'    => null,
            ], 500);
        }
    }

    public function getMovements($id): JsonResponse
    {
        try {
            $movements = $this->fertilRepo->getMovements((int) $id);
            $resolved = FertilInventoryMovementResource::collection($movements)->resolve();

            return response()->json($resolved, 200);
        } catch (Throwable $e) {
            Log::error('Error al consultar movimientos de inventario en fertil', [
                'action'  => 'FertilController@getMovements',
                'id'      => $id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([], 500);
        }
    }

    public function storeMaterialRequest(FertilMaterialRequest $request)
    {
        try {
            $materialRequest = $this->fertilRepo->createMaterialRequest($request->validated());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'code'    => 201,
                    'message' => 'Solicitud enviada a almacén correctamente.',
                    'data'    => new FertilMaterialRequestResource($materialRequest),
                ], 201);
            }

            return redirect()->back()->with('success', 'Solicitud enviada a almacén correctamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar solicitud de materiales en fertil', [
                'action'  => 'FertilController@storeMaterialRequest',
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 500,
                    'message' => 'Error al enviar la solicitud.',
                    'error'   => 'Error interno al registrar solicitud.',
                    'data'    => null,
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al enviar la solicitud.');
        }
    }
}
