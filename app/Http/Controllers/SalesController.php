<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Sales\SaleRepository;
use App\Http\Requests\Sales\SaleAlmacenActionRequest;
use App\Http\Requests\Sales\SaleStatusUpdateRequest;
use App\Http\Requests\Sales\SaleStoreRequest;
use App\Http\Requests\Sales\SaleUpdateRequest;
use App\Http\Resources\Sales\SaleResource;
use App\Traits\UtilResponse;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    protected UtilResponse $utilResponse;
    protected SaleRepository $saleRepo;

    public function __construct(UtilResponse $utilResponse, SaleRepository $saleRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->saleRepo = $saleRepo;
    }

    public function index(Request $request)
    {
        try {
            $data = $this->saleRepo->getIndexData();

            $user = Auth::user();
            $userAdmin = $user && method_exists($user, 'hasRole') ? $user->hasRole('Admin') : false;
            $userId = $user?->id;
            $userName = $user?->name;

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse(array_merge($data, [
                    'user_admin' => $userAdmin,
                    'user_id'    => $userId,
                    'user_name'  => $userName,
                ]), 'Datos de ventas obtenidos correctamente');
            }

            return view('sales', array_merge($data, [
                'user_admin' => $userAdmin,
                'user_id'    => $userId,
                'user_name'  => $userName,
            ]));
        } catch (\Throwable $e) {
            Log::error('Error al listar ventas', [
                'action'    => 'index',
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error interno al obtener datos de ventas', 500);
            }

            abort(500, 'Error interno del servidor');
        }
    }

    public function deleteDetail(Request $request)
    {
        try {
            $id = (int) $request->input('id');
            $deleted = $this->saleRepo->deleteDetail($id);

            if (!$deleted) {
                return response()->json(['success' => false, 'message' => 'Detail not found'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Detail deleted']);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar detalle de venta', [
                'action'    => 'deleteDetail',
                'id'        => $request->input('id'),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Error al eliminar detalle'], 500);
        }
    }

    public function getSales(Request $request)
    {
        try {
            $sales = $this->saleRepo->getSales($request->all(), Auth::user());

            return response()->json(['sales' => $sales]);
        } catch (\Throwable $e) {
            Log::error('Error al filtrar ventas', [
                'action'    => 'getSales',
                'filters'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['sales' => [], 'error' => 'Error interno al consultar ventas'], 500);
        }
    }

    public function store(SaleStoreRequest $request)
    {
        try {
            $sale = $this->saleRepo->store($request->validated(), Auth::user());

            return response()->json([
                'message' => 'Venta registrada y enviada a almacén para confirmación',
                'sale'    => new SaleResource($sale),
            ], 201);
        } catch (DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Error al registrar venta', [
                'action'    => 'store',
                'user_id'   => Auth::id(),
                'payload'   => $request->except(['_token']),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al registrar venta: ' . $e->getMessage()], 500);
        }
    }

    public function update(SaleUpdateRequest $request, $id = null)
    {
        try {
            $saleId = (int) ($id ?? $request->input('sale_id'));
            $updated = $this->saleRepo->update($saleId, $request->validated(), Auth::user());

            if (!$updated) {
                return response()->json(['message' => 'Sale not found'], 404);
            }

            return response()->json(['message' => 'Operation successfully completed'], 201);
        } catch (DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar venta', [
                'action'    => 'update',
                'id'        => $id ?? $request->input('sale_id'),
                'payload'   => $request->except(['_token', '_method']),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al actualizar venta: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->saleRepo->delete((int) $id);

            if (!$deleted) {
                return response()->json(['success' => false, 'message' => 'Sale not found'], 404);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar venta', [
                'action'    => 'destroy',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id, Request $request)
    {
        try {
            $data = $this->saleRepo->findWithDetails((int) $id);

            if (!$data) {
                return response()->json(['message' => 'Sale not found'], 404);
            }

            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('Error al consultar detalle de venta', [
                'action'    => 'show',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Error interno al consultar venta'], 500);
        }
    }

    public function getRemisionesChartData()
    {
        try {
            $chartData = $this->saleRepo->getRemisionesChartData();

            return response()->json($chartData, 200);
        } catch (\Throwable $e) {
            Log::error('Error al obtener datos de gráfica de remisiones', [
                'action'    => 'getRemisionesChartData',
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(SaleStatusUpdateRequest $request, $id)
    {
        try {
            $updated = $this->saleRepo->updateStatus((int) $id, (int) $request->validated()['sales_status_id']);

            if (!$updated) {
                return response()->json(['success' => false, 'message' => 'Sale not found'], 404);
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar estatus de venta', [
                'action'    => 'updateStatus',
                'id'        => $id,
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Error al actualizar estatus'], 500);
        }
    }

    public function getClienteData(Request $request)
    {
        try {
            $busqueda = $request->get('q', '');
            $data = $this->saleRepo->getClienteData($busqueda);

            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('Error al obtener datos de gráfica por cliente', [
                'action'    => 'getClienteData',
                'busqueda'  => $request->get('q'),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function almacenDetail($id)
    {
        $user = Auth::user();
        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole(['Warehouse', 'Production', 'Admin', 'Sales'])) {
            abort(403, 'No tienes permiso para ver esta vista.');
        }

        $detail = $this->saleRepo->getAlmacenDetail((int) $id);
        if (!$detail) {
            abort(404, 'Venta no encontrada.');
        }

        return view('sales.almacen_detail', $detail);
    }

    public function almacenAction(SaleAlmacenActionRequest $request, $id)
    {
        try {
            $result = $this->saleRepo->almacenAction((int) $id, $request->validated(), Auth::user());

            return response()->json($result);
        } catch (DomainException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Error en acción de almacén para venta', [
                'action'    => 'almacenAction',
                'id'        => $id,
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function unreadNotifications()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => []]);
        }

        $notifications = $user->unreadNotifications->whereIn('type', [
            'App\Notifications\SaleAlmacenNotification',
            'App\Notifications\OrderUpdatedNotification',
        ]);

        return response()->json(['notifications' => $notifications]);
    }

    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $notification = $user->unreadNotifications->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function postponedReminders()
    {
        $user = Auth::user();
        if (!$user || !method_exists($user, 'hasAnyRole') || !$user->hasAnyRole(['Warehouse', 'Admin'])) {
            return response()->json(['reminders' => []]);
        }

        $reminders = $this->saleRepo->getPostponedReminders($user);

        return response()->json(['reminders' => $reminders]);
    }

    public function getSaleLots($id)
    {
        try {
            $lots = $this->saleRepo->getSaleLots((int) $id);

            return response()->json(['products' => $lots]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener lotes de venta', [
                'action'    => 'getSaleLots',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['products' => []], 500);
        }
    }
}