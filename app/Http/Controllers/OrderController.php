<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Order\OrderRepository;
use App\Http\Requests\Order\OrderStatusUpdateRequest;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Http\Resources\Order\OrderResource;
use App\Traits\UtilResponse;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected UtilResponse $utilResponse;
    protected OrderRepository $orderRepo;

    public function __construct(UtilResponse $utilResponse, OrderRepository $orderRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->orderRepo = $orderRepo;
    }

    public function index(Request $request)
    {
        try {
            $data = $this->orderRepo->getIndexData();

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse($data, 'Catálogo de pedidos obtenido');
            }

            return view('orders', $data);
        } catch (\Throwable $e) {
            Log::error('Error al listar pedidos', [
                'action'    => 'index',
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error interno al obtener pedidos', 500);
            }

            abort(500, 'Error interno del servidor');
        }
    }

    public function getData()
    {
        try {
            $orders = $this->orderRepo->getData(Auth::user());

            return response()->json([
                'data' => OrderResource::collection($orders),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener datos JSON de pedidos', [
                'action'    => 'getData',
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['data' => []], 500);
        }
    }

    public function store(OrderStoreRequest $request)
    {
        try {
            $pdfFile = $request->hasFile('pdf_file') ? $request->file('pdf_file') : null;
            $order = $this->orderRepo->store($request->validated(), $pdfFile, Auth::user());

            return response()->json([
                'status'  => 'success',
                'message' => 'Pedido registrado y notificado correctamente.',
                'order'   => new OrderResource($order),
            ], 200);
        } catch (DomainException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Error al guardar pedido', [
                'action'    => 'store',
                'user_id'   => Auth::id(),
                'payload'   => $request->except(['_token', 'pdf_file']),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            $order = $this->orderRepo->find((int) $id);

            if (!$order) {
                return response()->json(['error' => 'Pedido no encontrado'], 404);
            }

            $user = Auth::user();
            if (!$this->orderRepo->isAuthorized($order, $user)) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            return response()->json(new OrderResource($order));
        } catch (\Throwable $e) {
            Log::error('Error al consultar pedido para edición', [
                'action'    => 'edit',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error interno al consultar pedido'], 500);
        }
    }

    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            $pdfFile = $request->hasFile('pdf_file') ? $request->file('pdf_file') : null;
            $updated = $this->orderRepo->update((int) $id, $request->validated(), $pdfFile, Auth::user());

            if (!$updated) {
                return response()->json(['error' => 'Pedido no encontrado'], 404);
            }

            return response()->json(['success' => true]);
        } catch (DomainException $e) {
            $status = $e->getCode() >= 400 && $e->getCode() <= 499 ? $e->getCode() : 403;
            return response()->json(['error' => $e->getMessage()], $status);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar pedido', [
                'action'    => 'update',
                'id'        => $id,
                'payload'   => $request->except(['_token', 'pdf_file']),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatus(OrderStatusUpdateRequest $request, $id)
    {
        try {
            $updated = $this->orderRepo->updateStatus((int) $id, $request->validated(), Auth::user());

            if (!$updated) {
                return response()->json(['error' => 'Pedido no encontrado'], 404);
            }

            return response()->json(['success' => true]);
        } catch (DomainException $e) {
            $status = $e->getCode() >= 400 && $e->getCode() <= 499 ? $e->getCode() : 403;
            return response()->json(['error' => $e->getMessage()], $status);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar estatus de pedido', [
                'action'    => 'updateStatus',
                'id'        => $id,
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->orderRepo->delete((int) $id, Auth::user());

            if (!$deleted) {
                return response()->json(['error' => 'Pedido no encontrado'], 404);
            }

            return response()->json(['success' => true]);
        } catch (DomainException $e) {
            $status = $e->getCode() >= 400 && $e->getCode() <= 499 ? $e->getCode() : 403;
            return response()->json(['error' => $e->getMessage()], $status);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar pedido', [
                'action'    => 'destroy',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }
}