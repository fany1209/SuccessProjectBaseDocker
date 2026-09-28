<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Finance\FinanceRepository;
use App\Http\Requests\Finance\FinancePaymentRequest;
use App\Http\Requests\Finance\FinanceStatusRequest;
use App\Http\Resources\Finance\FinancePaymentResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class FinanceController extends Controller
{
    protected UtilResponse $utilResponse;
    protected FinanceRepository $financeRepo;

    public function __construct(UtilResponse $utilResponse, FinanceRepository $financeRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->financeRepo = $financeRepo;
    }

    public function index(Request $request): View|JsonResponse
    {
        try {
            $semana = $request->filled('semana') ? (int) $request->get('semana') : null;
            $anio = $request->filled('anio') ? (int) $request->get('anio') : null;

            $payments = $this->financeRepo->getFilteredPayments($semana, $anio);

            if ($request->expectsJson()) {
                return $this->utilResponse->successResponse(
                    FinancePaymentResource::collection($payments),
                    'Pagos obtenidos exitosamente',
                    200
                );
            }

            return view('finance', compact('payments', 'semana', 'anio'));
        } catch (Throwable $e) {
            Log::error('Error al listar pagos en FinanceController@index', [
                'action'  => 'FinanceController@index',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al consultar los pagos financieros.', 500);
            }

            abort(500, 'Error al cargar vista de finanzas');
        }
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $payments = $this->financeRepo->getDatatableData();

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Listado de pagos obtenido correctamente',
                'data'    => $payments,
            ]);
        } catch (Throwable $e) {
            Log::error('Error al obtener datos datatable en FinanceController@datatable', [
                'action'  => 'FinanceController@datatable',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al cargar datos de pagos para datatable.', 500);
        }
    }

    public function updateStatus(FinanceStatusRequest $request, int $id): JsonResponse
    {
        try {
            $status = $request->validated()['estatus'];
            $updated = $this->financeRepo->updateStatus($id, $status);

            if (!$updated) {
                return $this->utilResponse->errorResponse('Registro de pago no encontrado.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Estatus actualizado correctamente.',
                'data'    => ['id' => $id, 'estatus' => $status],
            ]);
        } catch (Throwable $e) {
            Log::error('Error al actualizar estatus en FinanceController@updateStatus', [
                'action'     => 'FinanceController@updateStatus',
                'payment_id' => $id,
                'user_id'    => auth()->id(),
                'error'      => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al actualizar estatus del pago.', 500);
        }
    }

    public function store(FinancePaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->financeRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'id'      => $payment->id,
                'message' => 'Pago creado correctamente',
                'data'    => new FinancePaymentResource($payment),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error al registrar pago en FinanceController@store', [
                'action'  => 'FinanceController@store',
                'payload' => $request->all(),
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al registrar el pago.', 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $payment = $this->financeRepo->findById($id);

            if (!$payment) {
                return $this->utilResponse->errorResponse('Registro no encontrado', 404);
            }

            return $this->utilResponse->successResponse(
                new FinancePaymentResource($payment),
                'Pago obtenido exitosamente',
                200
            );
        } catch (Throwable $e) {
            Log::error('Error al consultar pago en FinanceController@show', [
                'action'     => 'FinanceController@show',
                'payment_id' => $id,
                'user_id'    => auth()->id(),
                'error'      => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar registro de pago.', 500);
        }
    }

    public function update(FinancePaymentRequest $request, int $id): JsonResponse
    {
        try {
            $payment = $this->financeRepo->update($id, $request->validated());

            if (!$payment) {
                return $this->utilResponse->errorResponse('Registro no encontrado', 404);
            }

            return $this->utilResponse->successResponse(
                new FinancePaymentResource($payment),
                'Pago actualizado correctamente',
                200
            );
        } catch (Throwable $e) {
            Log::error('Error al actualizar pago en FinanceController@update', [
                'action'     => 'FinanceController@update',
                'payment_id' => $id,
                'payload'    => $request->all(),
                'user_id'    => auth()->id(),
                'error'      => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al actualizar el pago.', 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $deleted = $this->financeRepo->delete($id);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('No se pudo eliminar el registro', 404);
            }

            return $this->utilResponse->successResponse([], 'Registro eliminado correctamente', 200);
        } catch (Throwable $e) {
            Log::error('Error al eliminar pago en FinanceController@destroy', [
                'action'     => 'FinanceController@destroy',
                'payment_id' => $id,
                'user_id'    => auth()->id(),
                'error'      => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al eliminar el registro de pago.', 500);
        }
    }

    public function purchaseHistory(Request $request): View|JsonResponse
    {
        try {
            $customers = $this->financeRepo->getPurchaseHistoryCustomers();

            if ($request->expectsJson()) {
                return $this->utilResponse->successResponse(
                    $customers,
                    'Listado de clientes para historial obtenido correctamente',
                    200
                );
            }

            return view('finance.historial-compras.index', compact('customers'));
        } catch (Throwable $e) {
            Log::error('Error al cargar historial de compras en FinanceController@purchaseHistory', [
                'action'  => 'FinanceController@purchaseHistory',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return $this->utilResponse->errorResponse('Error al obtener lista de clientes para historial.', 500);
            }

            abort(500, 'Error al cargar vista de historial de compras');
        }
    }

    public function getPurchaseHistoryData(Request $request, int $customerId): JsonResponse
    {
        try {
            $year = $request->filled('year') ? (int) $request->query('year') : null;
            $data = $this->financeRepo->getCustomerPurchaseHistory($customerId, $year);

            if (!$data) {
                return $this->utilResponse->errorResponse('Cliente no encontrado', 404);
            }

            return $this->utilResponse->successResponse(
                $data,
                'Historial de compras obtenido exitosamente',
                200
            );
        } catch (Throwable $e) {
            Log::error('Error al consultar historial de cliente en FinanceController@getPurchaseHistoryData', [
                'action'      => 'FinanceController@getPurchaseHistoryData',
                'customer_id' => $customerId,
                'user_id'     => auth()->id(),
                'error'       => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al consultar historial del cliente.', 500);
        }
    }

    public function getDashboardData(Request $request): JsonResponse
    {
        try {
            $dashboardData = $this->financeRepo->getPurchaseHistoryDashboard();

            return $this->utilResponse->successResponse(
                $dashboardData,
                'Datos del dashboard financiero obtenidos correctamente',
                200
            );
        } catch (Throwable $e) {
            Log::error('Error al generar dashboard en FinanceController@getDashboardData', [
                'action'  => 'FinanceController@getDashboardData',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al generar datos del dashboard.', 500);
        }
    }
}
