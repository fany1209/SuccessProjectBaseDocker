<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\InventoryAcceptPalletRequest;
use App\Http\Requests\Inventory\InventoryPalletRequest;
use App\Http\Requests\Inventory\InventoryQuarantineRequest;
use App\Http\Requests\Inventory\InventoryRequest;
use App\Http\Requests\Inventory\InventoryTransactionRequest;
use App\Http\Requests\Inventory\InventoryUpdateCommentRequest;
use App\Http\Requests\Inventory\InventoryUpdateDateRequest;
use App\Http\Repositories\Inventory\InventoryRepository;
use App\Http\Resources\Inventory\InventoryResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class InventoryController extends Controller
{
    private UtilResponse $utilResponse;
    private InventoryRepository $inventoryRepo;

    public function __construct(UtilResponse $utilResponse, InventoryRepository $inventoryRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->inventoryRepo = $inventoryRepo;
    }

    /**
     * Muestra la vista principal del inventario o retorna listado JSON.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                $items = $this->inventoryRepo->all();
                return $this->utilResponse->successResponse(
                    InventoryResource::collection($items),
                    'Inventario obtenido correctamente.'
                );
            }

            $data = $this->inventoryRepo->getIndexData();
            return view('inventory', $data);
        } catch (Throwable $e) {
            Log::error('Error al consultar inventario en InventoryController@index', [
                'action'    => 'InventoryController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar el inventario.', 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al cargar el inventario.');
        }
    }

    /**
     * Consulta un registro de inventario específico por ID.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $inventory = $this->inventoryRepo->find($id);

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Registro de inventario no encontrado.',
                    'error'   => 'Registro de inventario no encontrado.',
                    'data'    => [],
                ], 404);
            }

            return $this->utilResponse->successResponse(
                new InventoryResource($inventory),
                'Inventario obtenido exitosamente.'
            );
        } catch (Throwable $e) {
            Log::error('Error al consultar registro de inventario', [
                'action'       => 'InventoryController@show',
                'inventory_id' => $id,
                'user_id'      => auth()->id(),
                'exception'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Ocurrió un error inesperado al consultar el inventario.',
                'error'   => 'Ocurrió un error inesperado al consultar el inventario.',
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Almacena un nuevo registro de inventario.
     *
     * @param InventoryRequest $request
     * @return JsonResponse
     */
    public function store(InventoryRequest $request): JsonResponse
    {
        try {
            $inventory = $this->inventoryRepo->create($request->validated());

            return $this->utilResponse->successResponse(
                new InventoryResource($inventory),
                'Registro de inventario creado exitosamente.',
                201
            );
        } catch (Throwable $e) {
            Log::error('Error al crear registro de inventario', [
                'action'    => 'InventoryController@store',
                'payload'   => $request->validated(),
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Ocurrió un error inesperado al registrar el inventario.',
                'error'   => 'Ocurrió un error inesperado al registrar el inventario.',
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Actualiza un registro de inventario existente.
     *
     * @param InventoryRequest $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function update(InventoryRequest $request, $id): JsonResponse
    {
        try {
            $inventory = $this->inventoryRepo->find($id);

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Registro de inventario no encontrado.',
                    'error'   => 'Registro de inventario no encontrado.',
                    'data'    => [],
                ], 404);
            }

            $updated = $this->inventoryRepo->update($id, $request->validated());

            return $this->utilResponse->successResponse(
                new InventoryResource($updated),
                'Inventario actualizado exitosamente.'
            );
        } catch (Throwable $e) {
            Log::error('Error al actualizar registro de inventario', [
                'action'       => 'InventoryController@update',
                'inventory_id' => $id,
                'payload'      => $request->validated(),
                'user_id'      => auth()->id(),
                'exception'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Ocurrió un error inesperado al actualizar el inventario.',
                'error'   => 'Ocurrió un error inesperado al actualizar el inventario.',
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Elimina un registro de inventario existente.
     *
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $inventory = $this->inventoryRepo->find($id);

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Registro de inventario no encontrado.',
                    'error'   => 'Registro de inventario no encontrado.',
                    'data'    => [],
                ], 404);
            }

            $this->inventoryRepo->delete($id);

            return $this->utilResponse->successResponse([], 'Inventario eliminado exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar registro de inventario', [
                'action'       => 'InventoryController@destroy',
                'inventory_id' => $id,
                'user_id'      => auth()->id(),
                'exception'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'code'    => 500,
                'message' => 'Ocurrió un error inesperado al eliminar el inventario.',
                'error'   => 'Ocurrió un error inesperado al eliminar el inventario.',
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Consulta el stock disponible, en cuarentena, productos altos y bajos.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getInventoryAvailable(Request $request): JsonResponse
    {
        try {
            $data = $this->inventoryRepo->getAvailableStock($request->input('search'));
            return response()->json($data);
        } catch (Throwable $e) {
            Log::error('Error al consultar stock disponible', [
                'action'    => 'InventoryController@getInventoryAvailable',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'products'      => [],
                'productsLow'   => [],
                'productsHight' => [],
                'quarantine'    => [],
                'error'         => 'Error al consultar stock disponible.',
            ], 500);
        }
    }

    /**
     * Envía producto a cuarentena y descuenta inventario.
     *
     * @param InventoryQuarantineRequest $request
     * @return JsonResponse
     */
    public function addQuarantine(InventoryQuarantineRequest $request): JsonResponse
    {
        try {
            $this->inventoryRepo->addQuarantine($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'message' => 'Operation successfuly make it',
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al agregar a cuarentena', [
                'action'    => 'InventoryController@addQuarantine',
                'payload'   => $request->validated(),
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'error'   => 'Error al enviar a cuarentena.',
                'message' => 'Error al enviar a cuarentena.',
            ], 500);
        }
    }

    /**
     * Libera producto de cuarentena y regresa stock a inventario.
     *
     * @param InventoryQuarantineRequest $request
     * @return JsonResponse
     */
    public function updateQuarantine(InventoryQuarantineRequest $request): JsonResponse
    {
        try {
            $this->inventoryRepo->updateQuarantine($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'message' => 'Operation successfuly make it',
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al actualizar cuarentena', [
                'action'    => 'InventoryController@updateQuarantine',
                'payload'   => $request->validated(),
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'error'   => 'Error al actualizar cuarentena.',
                'message' => 'Error al actualizar cuarentena.',
            ], 500);
        }
    }

    /**
     * Realiza transacción de inventario (Entrada, Salida, Salida Interna).
     *
     * @param InventoryTransactionRequest $request
     * @return JsonResponse
     */
    public function makeTransaction(InventoryTransactionRequest $request): JsonResponse
    {
        try {
            $result = $this->inventoryRepo->makeTransaction($request->validated(), $request->user());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'message' => 'Success',
                'data'    => $result,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'flag'    => false,
                'error'   => $e->errors(),
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('Error al realizar transacción en InventoryController@makeTransaction', [
                'action'    => 'InventoryController@makeTransaction',
                'payload'   => $request->validated(),
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'error'   => $e->getMessage(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualiza la fecha de un movimiento de entrada o salida.
     *
     * @param InventoryUpdateDateRequest $request
     * @return JsonResponse
     */
    public function updateDate(InventoryUpdateDateRequest $request): JsonResponse
    {
        try {
            $this->inventoryRepo->updateDate(
                $request->input('type'),
                (int) $request->input('id'),
                $request->input('updated_at')
            );

            return response()->json([
                'success' => true,
                'flag'    => true,
                'message' => 'Operation successfuly make it',
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al actualizar fecha de movimiento', [
                'action'    => 'InventoryController@updateDate',
                'payload'   => $request->validated(),
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'error'   => 'Error al actualizar la fecha del movimiento.',
                'message' => 'Error al actualizar la fecha del movimiento.',
            ], 500);
        }
    }

    /**
     * Actualiza el comentario de un movimiento de entrada o salida.
     *
     * @param InventoryUpdateCommentRequest $request
     * @param string $type
     * @param int|string $id
     * @return JsonResponse
     */
    public function updateComment(InventoryUpdateCommentRequest $request, string $type, $id): JsonResponse
    {
        try {
            $this->inventoryRepo->updateComment($type, (int) $id, $request->input('comments'));

            return response()->json([
                'success' => true,
                'flag'    => true,
                'message' => 'Updated',
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al actualizar comentario de movimiento', [
                'action'    => 'InventoryController@updateComment',
                'type'      => $type,
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'error'   => 'Error al actualizar el comentario.',
                'message' => 'Error al actualizar el comentario.',
            ], 500);
        }
    }

    /**
     * Muestra el listado de tarimas pendientes o retorna JSON.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|JsonResponse
     */
    public function pendingPallets(Request $request)
    {
        try {
            $data = $this->inventoryRepo->getPendingPalletsData();

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    $data['pallets'],
                    'Tarimas pendientes obtenidas exitosamente.'
                );
            }

            return view('inventory.pallets.pending', $data);
        } catch (Throwable $e) {
            Log::error('Error al consultar tarimas pendientes', [
                'action'    => 'InventoryController@pendingPallets',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar tarimas pendientes.', 500);
            }

            return back()->with('error', 'Ocurrió un error inesperado al consultar tarimas pendientes.');
        }
    }

    /**
     * Acepta e ingresa una tarima al inventario.
     *
     * @param InventoryPalletRequest $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function acceptPallet(InventoryPalletRequest $request, $id): JsonResponse
    {
        try {
            $result = $this->inventoryRepo->acceptPallet($id, $request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'message' => '¡Entrada registrada y tarima ingresada al inventario correctamente con ' . number_format($result['final_weight'], 2) . ' kg!',
                'data'    => $result,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Error al aceptar tarima en inventario', [
                'action'    => 'InventoryController@acceptPallet',
                'pallet_id' => $id,
                'payload'   => $request->validated(),
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'flag'    => false,
                'error'   => $e->getMessage(),
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}