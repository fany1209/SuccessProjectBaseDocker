<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Warehouse\WarehouseRepository;
use App\Http\Requests\Warehouse\WarehouseTemperatureRequest;
use App\Http\Resources\Warehouse\ControlResource;
use App\Http\Resources\Warehouse\ProductionMaterialRequestResource;
use App\Traits\UtilResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    protected UtilResponse $utilResponse;
    protected WarehouseRepository $warehouseRepository;

    public function __construct(UtilResponse $utilResponse, WarehouseRepository $warehouseRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * Muestra la vista principal de almacén o retorna feedback de temperatura vía AJAX.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $hours = ['09:00', '10:00', '16:00'];
            $selectedWarehouse = $request->input('warehouse_id') ? (int) $request->input('warehouse_id') : null;
            $timezone = config('app.timezone', 'UTC');

            $feedback = $this->warehouseRepository->getFeedback($selectedWarehouse, $hours, $timezone);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'flag'     => true,
                    'feedback' => $feedback,
                    'data'     => ['feedback' => $feedback],
                ]);
            }

            $summary = $this->warehouseRepository->getInventorySummary();
            $available_locations = $this->warehouseRepository->getAvailableLocations();
            $concepts = $this->warehouseRepository->getAllConcepts();
            $warehouses = $this->warehouseRepository->all();
            $locations = $this->warehouseRepository->getAllLocations();
            $products_inventory = $this->warehouseRepository->getProductsInventory();
            $inventory = $this->warehouseRepository->getInventoryStockWH();
            $isFormActive = !empty($selectedWarehouse);

            return view('warehouse', compact(
                'concepts',
                'warehouses',
                'hours',
                'feedback',
                'selectedWarehouse',
                'isFormActive',
                'locations',
                'products_inventory',
                'inventory',
                'available_locations',
                'summary'
            ));
        } catch (\Throwable $e) {
            Log::error('Error al cargar vista/datos de almacén', [
                'action'    => 'WarehouseController@index',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al consultar datos de almacén', 500);
            }

            return back()->withErrors('Error al cargar la información de almacén.');
        }
    }

    /**
     * Registra mediciones horarias de temperatura y humedad en el almacén especificado.
     *
     * @param WarehouseTemperatureRequest $request
     * @return JsonResponse
     */
    public function temperature(WarehouseTemperatureRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $clientIp = $request->ip();

            $meta = [
                'user_id'   => auth()->id(),
                'host_ip'   => $clientIp,
                'host_user' => gethostname(),
                'host_name' => @gethostbyaddr($clientIp) ?: gethostname(),
            ];

            $created = $this->warehouseRepository->storeTemperatureMeasurements(
                (int) $validated['warehouse_id'],
                $validated['measurements'],
                $meta
            );

            return $this->utilResponse->successResponse(
                ControlResource::collection($created),
                'Mediciones registradas correctamente',
                201
            );
        } catch (\Throwable $e) {
            Log::error('Error al guardar mediciones de temperatura', [
                'action'    => 'WarehouseController@temperature',
                'user_id'   => auth()->id(),
                'payload'   => $request->except(['_token']),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al guardar las mediciones de temperatura', 500);
        }
    }

    /**
     * Retorna productos y peso contenidos en la ubicación especificada.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getInfoLocation(Request $request): JsonResponse
    {
        try {
            $location = strip_tags(trim((string) $request->input('location')));
            $products = $this->warehouseRepository->getInfoLocation($location);

            return response()->json([
                'success'  => true,
                'flag'     => true,
                'products' => $products,
                'data'     => $products,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al consultar información de ubicación', [
                'action'    => 'WarehouseController@getInfoLocation',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar información de la ubicación', 500);
        }
    }

    /**
     * Filtra las existencias en almacén por concepto o texto de búsqueda.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getWarehouse(Request $request): JsonResponse
    {
        try {
            $concept = $request->filled('concept') ? strip_tags(trim((string) $request->input('concept'))) : null;
            $search = $request->filled('search') ? strip_tags(trim((string) $request->input('search'))) : null;

            $result = $this->warehouseRepository->getWarehouseStock($concept, $search);

            return response()->json([
                'success'  => true,
                'flag'     => true,
                'products' => $result,
                'data'     => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al consultar inventario en almacén', [
                'action'    => 'WarehouseController@getWarehouse',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar existencias de almacén', 500);
        }
    }

    /**
     * Muestra el listado de solicitudes de material para producción.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function productionRequests(Request $request)
    {
        try {
            $requests = $this->warehouseRepository->getProductionRequests();

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse(
                    ProductionMaterialRequestResource::collection($requests),
                    'Solicitudes de producción recuperadas exitosamente'
                );
            }

            return view('warehouse.production_requests', compact('requests'));
        } catch (\Throwable $e) {
            Log::error('Error al listar solicitudes de producción', [
                'action'    => 'WarehouseController@productionRequests',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al recuperar solicitudes de producción', 500);
            }

            return back()->withErrors('Error al recuperar solicitudes de producción.');
        }
    }

    /**
     * Marca una solicitud de material para producción como 'Surtido'.
     *
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse|RedirectResponse
     */
    public function attendProductionRequest(Request $request, $id)
    {
        try {
            $matReq = $this->warehouseRepository->attendProductionRequest((int) $id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'message' => 'Solicitud marcada como Surtida.',
                    'data'    => new ProductionMaterialRequestResource($matReq),
                ]);
            }

            return redirect()->back()->with('success', 'Solicitud marcada como Surtida.');
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Solicitud de material no encontrada', 404);
            }
            return redirect()->back()->withErrors('Solicitud de material no encontrada.');
        } catch (\Throwable $e) {
            Log::error('Error al atender solicitud de producción', [
                'action'    => 'WarehouseController@attendProductionRequest',
                'user_id'   => auth()->id(),
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al marcar la solicitud como surtida', 500);
            }

            return redirect()->back()->withErrors('Error al marcar la solicitud como surtida.');
        }
    }

    /**
     * Obtiene los productos y cantidades solicitadas para una orden de producción.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function getRequestItems($id): JsonResponse
    {
        try {
            $items = $this->warehouseRepository->getRequestItems((int) $id);

            return response()->json([
                'success' => true,
                'flag'    => true,
                'items'   => $items,
                'data'    => $items,
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Solicitud de producción no encontrada', 404);
        } catch (\Throwable $e) {
            Log::error('Error al obtener partidas de solicitud de producción', [
                'action'    => 'WarehouseController@getRequestItems',
                'user_id'   => auth()->id(),
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar partidas de la solicitud', 500);
        }
    }

    public function pendingTransfers(Request $request)
    {
        try {
            $transfers = \App\Models\ProductionWarehouseTransfer::with('product')
                ->where('status', 'Pendiente')
                ->orderBy('created_at', 'desc')
                ->get();

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse(
                    $transfers,
                    'Transferencias de producción pendientes recuperadas exitosamente'
                );
            }

            return view('warehouse.production_transfers', compact('transfers'));
        } catch (\Throwable $e) {
            Log::error('Error al listar transferencias de producción', [
                'action'    => 'WarehouseController@pendingTransfers',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al recuperar transferencias', 500);
            }

            return back()->withErrors('Error al recuperar transferencias.');
        }
    }

    public function receiveTransfer(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'location_id'       => 'required|exists:locations,location_id',
                'supplier_id'       => 'nullable|exists:suppliers,supplier_id',
                'concept_id'        => 'nullable|exists:concepts,concept_id',
                'product_id'        => 'nullable|exists:products,product_id',
                'quantity'          => 'nullable|numeric|min:0.1',
                'weight_per_unit'   => 'nullable|numeric|min:0.1',
                'final_weight'      => 'nullable|numeric|min:0.1',
                'warehouse_batch'   => 'nullable|string',
                'comments'          => 'nullable|string',
            ]);

            $result = \Illuminate\Support\Facades\DB::transaction(function () use ($id, $validated) {
                $transfer = \App\Models\ProductionWarehouseTransfer::where('transfer_id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($transfer->status !== 'Pendiente') {
                    throw new \InvalidArgumentException('La transferencia no está pendiente de ser recibida.');
                }

                // Resolución de proveedor
                $supplierId = $validated['supplier_id'] ?? null;
                if (!$supplierId) {
                    $supplier = \App\Models\Supplier::where('name', 'Producción Interna')
                        ->orWhere('supplier_code', 'PROD-INT')
                        ->first();
                    if (!$supplier) {
                        $supplier = \App\Models\Supplier::create([
                            'name'          => 'Producción Interna',
                            'supplier_code' => 'PROD-INT',
                            'contact'       => 'Planta Producción',
                            'sector_id'     => 1,
                        ]);
                    }
                    $supplierId = $supplier->supplier_id;
                }

                // Resolución de concepto
                $conceptId = $validated['concept_id'] ?? null;
                if (!$conceptId) {
                    $concept = \App\Models\Concept::where('name', 'Producto Terminado')->first()
                        ?? \App\Models\Concept::where('name', 'like', '%Terminado%')->first();
                    $conceptId = $concept ? $concept->concept_id : 2;
                }

                $productId = $validated['product_id'] ?? $transfer->product_id;
                if (!$productId) {
                    $defaultProd = \App\Models\Product::where('product_id', 623)->first();
                    $productId = $defaultProd ? $defaultProd->product_id : 1;
                }

                $sacks = $validated['quantity'] ?? $transfer->quantity;
                $weightPerUnit = $validated['weight_per_unit'] ?? $transfer->weight_per_unit;
                $finalWeight = $validated['final_weight'] ?? $transfer->total_weight;
                $batch = $validated['warehouse_batch'] ?? $transfer->batch ?? 'TRANSFER-' . $transfer->transfer_id;

                // 1. Registro Input
                $input = \App\Models\Input::create([
                    'supplier_id'          => $supplierId,
                    'security_seal'        => 0, // Campo requerido por la base de datos
                    'comments'             => $validated['comments'] ?? null,
                ]);

                // 2. Registro Inventory
                $inventory = \App\Models\Inventory::create([
                    'stock'      => $finalWeight,
                    'batch'      => $batch,
                    'product_id' => $productId,
                ]);

                // 3. Registro ProductInputs
                \App\Models\ProductInputs::insert([
                    'product_id'      => $productId,
                    'input_id'        => $input->input_id,
                    'quantity'        => $finalWeight,
                    'warehouse_batch' => $batch,
                ]);

                // 4. Registro CLI
                \App\Models\Cli::create([
                    'inventory_id'    => $inventory->inventory_id,
                    'concept_id'      => $conceptId,
                    'location_id'     => $validated['location_id'],
                    'quantity'        => $sacks,
                    'weight_per_unit' => $weightPerUnit,
                    'net_weight'      => $finalWeight,
                ]);

                // 5. Actualizar status de la transferencia
                $transfer->status = 'Ingresada';
                $transfer->received_by = auth()->id();
                $transfer->save();

                return [
                    'final_weight' => $finalWeight,
                    'inventory_id' => $inventory->inventory_id,
                ];
            });

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'flag'    => true,
                    'message' => '¡Entrada registrada e ingresada al almacén correctamente por un total de ' . number_format($result['final_weight'], 2) . '!',
                    'data'    => $result,
                ], 200);
            }

            return redirect()->back()->with('success', 'Transferencia ingresada correctamente.');
        } catch (\InvalidArgumentException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse($e->getMessage(), 400);
            }
            return redirect()->back()->withErrors($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error al recibir transferencia de producción', [
                'action'    => 'WarehouseController@receiveTransfer',
                'user_id'   => auth()->id(),
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al registrar la entrada al almacén', 500);
            }

            return redirect()->back()->withErrors('Error al registrar la entrada al almacén.');
        }
    }
}
