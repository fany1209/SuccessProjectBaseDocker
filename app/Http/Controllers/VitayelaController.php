<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Vitayela\VitayelaRepository;
use App\Http\Requests\Vitayela\VitayelaInventoryOutputRequest;
use App\Http\Requests\Vitayela\VitayelaInventoryStoreRequest;
use App\Http\Requests\Vitayela\VitayelaMaterialRequestStoreRequest;
use App\Http\Requests\Vitayela\VitayelaProductionStoreRequest;
use App\Http\Resources\Vitayela\VitayelaInventoryResource;
use App\Http\Resources\Vitayela\VitayelaMovementResource;
use App\Http\Resources\Vitayela\VitayelaProductionResource;
use App\Traits\UtilResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VitayelaController extends Controller
{
    protected UtilResponse $utilResponse;
    protected VitayelaRepository $vitayelaRepo;

    public function __construct(UtilResponse $utilResponse, VitayelaRepository $vitayelaRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->vitayelaRepo = $vitayelaRepo;
    }

    public function index(): View
    {
        $productions = $this->vitayelaRepo->getAllProductions();
        $inventories = $this->vitayelaRepo->getAllInventories();
        $db_products = $this->vitayelaRepo->getAllProducts();

        return view('production.vitayela.index', compact('productions', 'inventories', 'db_products'));
    }

    public function storeProduction(VitayelaProductionStoreRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $production = $this->vitayelaRepo->storeProduction($request->validated());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registro agregado correctamente.',
                    'data' => new VitayelaProductionResource($production),
                ], 201);
            }

            return redirect()->route('production.vitayela.index')
                ->with('success', 'Registro de producción agregado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al guardar producción de vitayela', [
                'action' => 'storeProduction',
                'user_id' => auth()->id(),
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al registrar producción.', 500);
            }

            return back()->withErrors('Error al registrar producción.');
        }
    }

    public function updateProduction(VitayelaProductionStoreRequest $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $production = $this->vitayelaRepo->updateProduction((int) $id, $request->validated());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registro actualizado correctamente.',
                    'data' => new VitayelaProductionResource($production),
                ]);
            }

            return redirect()->route('production.vitayela.index')
                ->with('success', 'Registro de producción actualizado correctamente.');
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Registro no encontrado.'], 404);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar producción de vitayela', [
                'action' => 'updateProduction',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al actualizar producción.', 500);
            }

            return back()->withErrors('Error al actualizar producción.');
        }
    }

    public function destroyProduction($id): JsonResponse|RedirectResponse
    {
        try {
            $this->vitayelaRepo->destroyProduction((int) $id);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registro eliminado correctamente.',
                ]);
            }

            return redirect()->route('production.vitayela.index')
                ->with('success', 'Registro de producción eliminado.');
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Registro no encontrado.'], 404);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar producción de vitayela', [
                'action' => 'destroyProduction',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al eliminar producción.', 500);
            }

            return back()->withErrors('Error al eliminar producción.');
        }
    }

    public function storeInventory(VitayelaInventoryStoreRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $inv = $this->vitayelaRepo->storeInventory($request->validated());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto agregado correctamente.',
                    'data' => new VitayelaInventoryResource($inv),
                ], 201);
            }

            return redirect()->route('production.vitayela.index')
                ->with('success', 'Registro de inventario agregado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al guardar inventario de vitayela', [
                'action' => 'storeInventory',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al agregar producto a inventario.', 500);
            }

            return back()->withErrors('Error al agregar producto a inventario.');
        }
    }

    public function updateInventory(VitayelaInventoryStoreRequest $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $inventory = $this->vitayelaRepo->updateInventory((int) $id, $request->validated());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto actualizado correctamente.',
                    'data' => new VitayelaInventoryResource($inventory),
                ]);
            }

            return redirect()->route('production.vitayela.index')
                ->with('success', 'Registro de inventario actualizado correctamente.');
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar inventario de vitayela', [
                'action' => 'updateInventory',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al actualizar inventario.', 500);
            }

            return back()->withErrors('Error al actualizar inventario.');
        }
    }

    public function destroyInventory($id): JsonResponse|RedirectResponse
    {
        try {
            $this->vitayelaRepo->destroyInventory((int) $id);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado correctamente.',
                ]);
            }

            return redirect()->route('production.vitayela.index')
                ->with('success', 'Registro de inventario eliminado.');
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar inventario de vitayela', [
                'action' => 'destroyInventory',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al eliminar producto.', 500);
            }

            return back()->withErrors('Error al eliminar producto.');
        }
    }

    public function outputInventory(VitayelaInventoryOutputRequest $request, $id): JsonResponse
    {
        try {
            $result = $this->vitayelaRepo->outputInventory((int) $id, (float) $request->validated('cantidad_salida'));

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'alert' => $result['alert'],
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al registrar salida de inventario vitayela', [
                'action' => 'outputInventory',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar salida de inventario.', 500);
        }
    }

    public function getMovements($id): JsonResponse
    {
        try {
            $movements = $this->vitayelaRepo->getMovements((int) $id);

            return response()->json($movements);
        } catch (\Throwable $e) {
            Log::error('Error al obtener movimientos de inventario vitayela', [
                'action' => 'getMovements',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([], 500);
        }
    }

    public function storeMaterialRequest(VitayelaMaterialRequestStoreRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $materialRequest = $this->vitayelaRepo->storeMaterialRequest($request->validated());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Solicitud enviada a almacén correctamente.',
                    'data' => $materialRequest,
                ], 201);
            }

            return redirect()->back()->with('success', 'Solicitud enviada a almacén correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error en storeMaterialRequest de vitayela', [
                'action' => 'storeMaterialRequest',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Error al enviar la solicitud.'], 500);
            }

            return redirect()->back()->with('error', 'Error al enviar la solicitud.');
        }
    }
}
