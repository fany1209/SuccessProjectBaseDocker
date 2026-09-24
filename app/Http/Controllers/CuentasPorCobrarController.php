<?php

namespace App\Http\Controllers;

use App\Http\Repositories\CuentasPorCobrar\CuentasPorCobrarRepository;
use App\Http\Requests\CuentasPorCobrar\AddCxcPaymentRequest;
use App\Http\Requests\CuentasPorCobrar\UpdateCxcDetailRequest;
use App\Http\Resources\CuentasPorCobrar\CxcDetailResource;
use App\Http\Resources\CuentasPorCobrar\CxcPaymentResource;
use App\Traits\UtilResponse;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class CuentasPorCobrarController extends Controller
{
    protected UtilResponse $utilResponse;
    protected CuentasPorCobrarRepository $cxcRepo;

    public function __construct(UtilResponse $utilResponse, CuentasPorCobrarRepository $cxcRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->cxcRepo = $cxcRepo;
    }

    public function index(Request $request)
    {
        try {
            $pendingPaymentsCount = $this->cxcRepo->getPendingPaymentsCount();

            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    ['pendingPaymentsCount' => $pendingPaymentsCount],
                    'Resumen de cuentas por cobrar obtenido correctamente'
                );
            }

            return view('finance.cuentas_por_cobrar.index', compact('pendingPaymentsCount'));
        } catch (Throwable $e) {
            Log::error('Error al obtener índice de cuentas por cobrar', [
                'action' => 'CuentasPorCobrarController@index',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar el resumen de cuentas por cobrar', 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar las cuentas por cobrar.');
        }
    }

    public function dashboard(Request $request)
    {
        try {
            $selectedMonth = (int) $request->input('month', now()->month);
            $selectedYear = (int) $request->input('year', now()->year);

            $data = $this->cxcRepo->getDashboardData($selectedMonth, $selectedYear);

            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->successResponse($data, 'Datos del dashboard obtenidos correctamente');
            }

            return view('finance.cuentas_por_cobrar.dashboard', $data);
        } catch (Throwable $e) {
            Log::error('Error al cargar dashboard de cuentas por cobrar', [
                'action' => 'CuentasPorCobrarController@dashboard',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al cargar datos del dashboard', 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar el dashboard.');
        }
    }

    public function clientes(Request $request)
    {
        try {
            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->successResponse([], 'Vista de clientes de cuentas por cobrar');
            }

            return view('finance.cuentas_por_cobrar.clientes');
        } catch (Throwable $e) {
            Log::error('Error al acceder a vista clientes de cuentas por cobrar', [
                'action' => 'CuentasPorCobrarController@clientes',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al acceder a la vista de clientes', 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar la vista de clientes.');
        }
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $month = $request->input('month') ? (int) $request->input('month') : null;
            $year = $request->input('year') ? (int) $request->input('year') : null;

            $sales = $this->cxcRepo->getDatatable($month, $year);

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Registros obtenidos correctamente',
                'data' => $sales,
            ]);
        } catch (Throwable $e) {
            Log::error('Error al consultar datatable de cuentas por cobrar', [
                'action' => 'CuentasPorCobrarController@datatable',
                'user_id' => auth()->id(),
                'params' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al cargar la información del listado', 500);
        }
    }

    public function update(UpdateCxcDetailRequest $request, $id): JsonResponse
    {
        try {
            $cxc = $this->cxcRepo->updateDetail((int) $id, $request->validated());

            return $this->utilResponse->successResponse(
                new CxcDetailResource($cxc),
                'Actualizado correctamente'
            );
        } catch (DomainException $e) {
            return $this->utilResponse->errorResponse($e->getMessage(), 403);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Cuenta por cobrar no encontrada', 404);
        } catch (Throwable $e) {
            Log::error('Error al actualizar cuenta por cobrar', [
                'action' => 'CuentasPorCobrarController@update',
                'id' => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al guardar cambios de la cuenta', 500);
        }
    }

    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            $cxc = $this->cxcRepo->cancel((int) $id);

            return $this->utilResponse->successResponse(
                new CxcDetailResource($cxc),
                'Cuenta cancelada correctamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Cuenta por cobrar no encontrada', 404);
        } catch (Throwable $e) {
            Log::error('Error al cancelar cuenta por cobrar', [
                'action' => 'CuentasPorCobrarController@cancel',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al cancelar la cuenta', 500);
        }
    }

    public function getPayments(Request $request, $id): JsonResponse
    {
        try {
            $payments = $this->cxcRepo->getPaymentsByDetailId((int) $id);
            $resource = CxcPaymentResource::collection($payments);

            return response()->json([
                'success' => true,
                'flag' => true,
                'code' => 200,
                'message' => 'Pagos obtenidos correctamente',
                'data' => $resource,
                'payments' => $resource,
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Cuenta por cobrar no encontrada', 404);
        } catch (Throwable $e) {
            Log::error('Error al obtener pagos de cuenta por cobrar', [
                'action' => 'CuentasPorCobrarController@getPayments',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al obtener los pagos', 500);
        }
    }

    public function addPayment(AddCxcPaymentRequest $request, $id): JsonResponse
    {
        try {
            $payment = $this->cxcRepo->addPayment(
                (int) $id,
                $request->validated(),
                $request->file('comprobante')
            );

            return $this->utilResponse->successResponse(
                new CxcPaymentResource($payment),
                'Pago añadido correctamente',
                201
            );
        } catch (DomainException $e) {
            return $this->utilResponse->errorResponse($e->getMessage(), 403);
        } catch (ModelNotFoundException $e) {
            return $this->utilResponse->errorResponse('Cuenta por cobrar no encontrada', 404);
        } catch (Throwable $e) {
            Log::error('Error al añadir pago a cuenta por cobrar', [
                'action' => 'CuentasPorCobrarController@addPayment',
                'id' => $id,
                'payload' => $request->validated(),
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al registrar el pago', 500);
        }
    }

    public function exportExcel(Request $request): StreamedResponse|JsonResponse
    {
        try {
            $month = $request->input('month') ? (int) $request->input('month') : null;
            $year = $request->input('year') ? (int) $request->input('year') : null;

            $spreadsheet = $this->cxcRepo->getSpreadsheet($month, $year);
            $writer = new Xlsx($spreadsheet);
            $fileName = 'Cuentas_Por_Cobrar_' . date('Y-m-d') . '.xlsx';

            if (ob_get_length()) {
                ob_end_clean();
            }

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);
        } catch (Throwable $e) {
            Log::error('Error al exportar cuentas por cobrar a Excel', [
                'action' => 'CuentasPorCobrarController@exportExcel',
                'user_id' => auth()->id(),
                'params' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al generar el archivo Excel', 500);
        }
    }
}
