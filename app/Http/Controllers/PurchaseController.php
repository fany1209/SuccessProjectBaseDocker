<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Purchase\PurchaseRepository;
use App\Http\Requests\Purchase\PurchaseOrderStoreRequest;
use App\Http\Requests\Purchase\PurchaseOrderUpdateRequest;
use App\Http\Requests\Purchase\SupplierEvaluationRequest;
use App\Http\Requests\Purchase\SupplierSelectionCriteriaRequest;
use App\Traits\UtilResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    protected UtilResponse $utilResponse;
    protected PurchaseRepository $purchaseRepo;

    public function __construct(UtilResponse $utilResponse, PurchaseRepository $purchaseRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->purchaseRepo = $purchaseRepo;
    }

    public function index()
    {
        try {
            $data = $this->purchaseRepo->getIndexData();
            return view('purchases', $data);
        } catch (\Throwable $e) {
            Log::error('Error loading purchases index', [
                'action'    => 'PurchaseController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return back()->withErrors('Error al cargar el módulo de compras.');
        }
    }

    public function getPurchasesCharts(): JsonResponse
    {
        try {
            $data = $this->purchaseRepo->getPurchasesCharts();
            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('Error in getPurchasesCharts', ['exception' => $e->getMessage()]);
            return response()->json(['requisitions_per_department' => [], 'requisitions_per_employee' => []], 500);
        }
    }

    public function gPurchaseOrder(PurchaseOrderStoreRequest $request)
    {
        try {
            $result = $this->purchaseRepo->createPurchaseOrderWithDetails($request->validated());

            return Pdf::loadView('formats.purchases.01', $result['view_data'])
                ->setPaper('letter')
                ->stream('OrdenCompra_' . $result['order']->id . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error generating purchase order PDF', [
                'action'    => 'PurchaseController@gPurchaseOrder',
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function gSupSelectCrit(SupplierSelectionCriteriaRequest $request)
    {
        try {
            $data = $this->purchaseRepo->buildSelectionCriteriaPdfData($request->all());

            return Pdf::loadView('formats.purchases.05', $data)
                ->setPaper('letter')
                ->stream('CriteriosSeleccion_DEMO.pdf');
        } catch (\Throwable $e) {
            Log::error('Error generating supplier selection criteria PDF', [
                'action'    => 'PurchaseController@gSupSelectCrit',
                'exception' => $e->getMessage(),
            ]);

            return back()->withErrors('Error al generar PDF de criterios de selección: ' . $e->getMessage());
        }
    }

    public function gSupplierEvaluation(SupplierEvaluationRequest $request)
    {
        try {
            $data = $this->purchaseRepo->buildSupplierEvaluationPdfData($request->all());

            return Pdf::loadView('formats.purchases.06', $data)
                ->setPaper('letter')
                ->stream('EvaluacionProveedores_DEMO.pdf');
        } catch (\Throwable $e) {
            Log::error('Error generating supplier evaluation PDF', [
                'action'    => 'PurchaseController@gSupplierEvaluation',
                'exception' => $e->getMessage(),
            ]);

            return back()->withErrors('Error al generar PDF de evaluación de proveedores: ' . $e->getMessage());
        }
    }

    public function orders()
    {
        try {
            $data = $this->purchaseRepo->getOrdersIndexData();
            return view('purchases.order', $data);
        } catch (\Throwable $e) {
            Log::error('Error loading purchase orders view', ['exception' => $e->getMessage()]);
            return back()->withErrors('Error al cargar la vista de órdenes de compra.');
        }
    }

    public function show($id)
    {
        if ($id === 'orders') {
            return $this->orders();
        }

        if ($id === 'get-orders') {
            return $this->getPurchaseOrders(request());
        }

        return $this->streamPdf($id);
    }

    public function getPurchaseOrders(Request $request)
    {
        try {
            if ($request->ajax() || $request->wantsJson()) {
                $orders = $this->purchaseRepo->getPurchaseOrders($request->only(['search_orders', 'date_filter']));
                return response()->json(['orders' => $orders]);
            }

            return abort(404);
        } catch (\Throwable $e) {
            Log::error('Error in getPurchaseOrders', ['exception' => $e->getMessage()]);
            return response()->json(['orders' => []], 500);
        }
    }

    public function streamPdf($id)
    {
        try {
            $order = $this->purchaseRepo->findOrderWithDetails($id);

            if (!$order) {
                return abort(404, 'Orden de compra no encontrada');
            }

            $data = $this->purchaseRepo->buildOrderPdfData($order);

            return Pdf::loadView('formats.purchases.01', $data)
                ->setPaper('letter')
                ->stream('OrdenCompra_' . $order->id . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error streaming purchase order PDF', [
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return abort(500, 'Error al generar el PDF de la orden de compra');
        }
    }

    public function edit($id): JsonResponse
    {
        try {
            $order = $this->purchaseRepo->findOrderWithDetails($id);

            if (!$order) {
                return response()->json(['message' => 'Orden no encontrada'], 404);
            }

            return response()->json($order);
        } catch (\Throwable $e) {
            Log::error('Error fetching order for edit', [
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Error al obtener la orden'], 500);
        }
    }

    public function update(PurchaseOrderUpdateRequest $request, $id): JsonResponse
    {
        try {
            $order = $this->purchaseRepo->updatePurchaseOrderWithDetails($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'new_id'  => $order->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error updating purchase order', [
                'id'        => $id,
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->purchaseRepo->deleteOrder($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró la orden a eliminar',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error deleting purchase order', [
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function demoPdf()
    {
        $dummy = [
            'supplier_name'    => 'STD Soluciones Tecnológicas',
            'email'            => 'contacto@std.mx',
            'phone'            => '3312345678',
            'rfc'              => 'STD010101AAA',
            'address'          => 'Av. Vallarta 100, Guadalajara',
            'cc_code'          => 'DEMO-001',
            'contact'          => 'Juan Pérez',
            'method_payment'   => '03',
            'payment_method'   => 'PUE',
            'cfdi'             => 'G01',
            'application_date' => now()->toDateString(),
            'delivery_time'    => '3 días',
            'delivery_date'    => now()->addDays(3)->toDateString(),
            'guia'             => 'GUIA-DEMO-01',
            'items'            => [
                ['description' => 'Producto Demo 1', 'quantity' => 10, 'unit_price' => 100.0, 'iva' => 1]
            ],
            'subtotal'         => 1000.0,
            'iva'              => 160.0,
            'total'            => 1160.0,
        ];

        return Pdf::loadView('formats.purchases.01', $dummy)->setPaper('letter')->stream('OrdenCompra_DEMO.pdf');
    }

    public function demo()
    {
        $dummy = [
            'supplier' => 'STD Soluciones Tecnológicas',
            'address'  => 'Guadalajara, Jalisco',
            'date'     => now()->toDateString(),
            'products' => 'Insumos Químicos',
            'checks'   => [1, 1, 1, 1, 1],
            'result'   => 100,
        ];

        return Pdf::loadView('formats.purchases.05', $dummy)->setPaper('letter')->stream('CriteriosSeleccion_DEMO.pdf');
    }

    public function demo6()
    {
        $dummy = [
            'supplier'          => 'STD Soluciones Tecnológicas',
            'rfc'               => 'STD010101AAA',
            'address'           => 'Guadalajara, Jalisco',
            'evaluation_date'   => now()->toDateString(),
            'evaluator'         => 'Evaluador Demo',
            'products'          => 'Insumos Químicos',
            'observations'      => 'Evaluación demo correcta',
            'questions_answers' => array_fill(0, 9, ['answer' => '1', 'qualification' => '10']),
        ];

        return Pdf::loadView('formats.purchases.06', $dummy)->setPaper('letter')->stream('EvaluacionProveedores_DEMO.pdf');
    }
}