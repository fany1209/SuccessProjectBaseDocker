<?php

namespace App\Http\Repositories\Purchase;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseRequisition;
use App\Models\Sector;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseRepository
{
    protected PurchaseOrder $purchaseOrder;
    protected PurchaseOrderDetail $purchaseOrderDetail;
    protected PurchaseRequisition $purchaseRequisition;
    protected Supplier $supplier;
    protected Sector $sector;

    public function __construct(
        PurchaseOrder $purchaseOrder,
        PurchaseOrderDetail $purchaseOrderDetail,
        PurchaseRequisition $purchaseRequisition,
        Supplier $supplier,
        Sector $sector
    ) {
        $this->purchaseOrder = $purchaseOrder;
        $this->purchaseOrderDetail = $purchaseOrderDetail;
        $this->purchaseRequisition = $purchaseRequisition;
        $this->supplier = $supplier;
        $this->sector = $sector;
    }

    public function getIndexData(): array
    {
        $requisitions_count = $this->purchaseRequisition->count();
        $requisitions_no_check = $this->purchaseRequisition->whereNull('consecutive')
            ->whereNull('purchase_order')
            ->count();

        $userName = auth()->user()?->name ?? '';
        $your_requisitions = $this->purchaseRequisition->where('applicant', $userName)->count();

        $suppliers = $this->supplier->select('supplier_id', 'name')->get();
        $sectors = $this->sector->select('sector_id', 'name')->get();

        $warehouseQuery = DB::table('insumos_entradas')
            ->select('id', 'fecha_llegada', 'proveedor', 'descripcion', 'cantidad', 'unidad', 'insumo', 'costo', 'moneda', 'categoria')
            ->orderByDesc('id');

        $user = auth()->user();
        if (!$user || !$user->can('purchases.admin')) {
            $warehouseQuery->where('categoria', 'warehouse');
        }

        $warehouse_entries = $warehouseQuery->get();

        $payment_method = [
            (object)["type" => "PUE", "description" => "Pago en una sola exhibición"],
            (object)["type" => "PPD", "description" => "Pago en parcialidades o diferido"]
        ];

        $method_payment = [
            (object)["code" => "01", "description" => "Efectivo"],
            (object)["code" => "02", "description" => "Cheque nominativo"],
            (object)["code" => "03", "description" => "Transferencia electrónica de fondos"],
            (object)["code" => "04", "description" => "Tarjeta de crédito"],
            (object)["code" => "05", "description" => "Monedero electrónico"],
            (object)["code" => "06", "description" => "Dinero electrónico"],
            (object)["code" => "08", "description" => "Vales de despensa"],
            (object)["code" => "12", "description" => "Dación en pago"],
            (object)["code" => "13", "description" => "Pago por subrogación"],
            (object)["code" => "14", "description" => "Pago por consignación"],
            (object)["code" => "15", "description" => "Condonación"],
            (object)["code" => "17", "description" => "Compensación"],
            (object)["code" => "23", "description" => "Novación"],
            (object)["code" => "24", "description" => "Confusión"],
            (object)["code" => "25", "description" => "Remisión de deuda"],
            (object)["code" => "26", "description" => "Prescripción o caducidad"],
            (object)["code" => "27", "description" => "A satisfacción del acreedor"],
            (object)["code" => "28", "description" => "Tarjeta de débito"],
            (object)["code" => "29", "description" => "Tarjeta de servicios"],
            (object)["code" => "30", "description" => "Aplicación de anticipos"],
            (object)["code" => "99", "description" => "Por definir"]
        ];

        $cfdi = [
            (object)["cfdi" => "G01", "description" => "Adquisición de mercancías"],
            (object)["cfdi" => "G02", "description" => "Devoluciones, descuentos o bonificaciones"],
            (object)["cfdi" => "G03", "description" => "Gastos en general"],
            (object)["cfdi" => "I01", "description" => "Construcciones"],
            (object)["cfdi" => "I02", "description" => "Mobiliario y equipo de oficina por inversiones"],
            (object)["cfdi" => "I03", "description" => "Equipo de transporte"],
            (object)["cfdi" => "I04", "description" => "Equipo de computo y accesorios"],
            (object)["cfdi" => "I05", "description" => "Dados, troqueles, moldes, matrices y herramental"],
            (object)["cfdi" => "I06", "description" => "Comunicaciones telefónicas"],
            (object)["cfdi" => "I07", "description" => "Comunicaciones satelitales"],
            (object)["cfdi" => "I08", "description" => "Otra maquinaria y equipo"],
            (object)["cfdi" => "D01", "description" => "Honorarios médicos, dentales y gastos hospitalarios"],
            (object)["cfdi" => "D02", "description" => "Gastos médicos por incapacidad o discapacidad"],
            (object)["cfdi" => "D03", "description" => "Gastos funerales"],
            (object)["cfdi" => "D04", "description" => "Donativos"],
            (object)["cfdi" => "D05", "description" => "Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)"],
            (object)["cfdi" => "D06", "description" => "Aportaciones voluntarias al SAR"],
            (object)["cfdi" => "D07", "description" => "Primas por seguros de gastos médicos"],
            (object)["cfdi" => "D08", "description" => "Gastos de transportación escolar obligatoria"],
            (object)["cfdi" => "D09", "description" => "Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones"],
            (object)["cfdi" => "D10", "description" => "Pagos por servicios educativos (colegiaturas)"],
            (object)["cfdi" => "S01", "description" => "Sin efectos fiscales"],
            (object)["cfdi" => "CP01", "description" => "Pagos"],
            (object)["cfdi" => "CN01", "description" => "Nómina"]
        ];

        return compact(
            'requisitions_count',
            'requisitions_no_check',
            'your_requisitions',
            'suppliers',
            'sectors',
            'warehouse_entries',
            'payment_method',
            'method_payment',
            'cfdi'
        );
    }

    public function getOrdersIndexData(): array
    {
        $suppliers = $this->supplier->all();

        $payment_method = [
            (object)["type" => "PUE", "description" => "Pago en una sola exhibición"],
            (object)["type" => "PPD", "description" => "Pago en parcialidades o diferido"]
        ];

        $method_payment = [
            (object)["code" => "01", "description" => "Efectivo"],
            (object)["code" => "02", "description" => "Cheque nominativo"],
            (object)["code" => "03", "description" => "Transferencia electrónica de fondos"],
            (object)["code" => "04", "description" => "Tarjeta de crédito"],
            (object)["code" => "05", "description" => "Monedero electrónico"],
            (object)["code" => "06", "description" => "Dinero electrónico"],
            (object)["code" => "08", "description" => "Vales de despensa"],
            (object)["code" => "12", "description" => "Dación en pago"],
            (object)["code" => "13", "description" => "Pago por subrogación"],
            (object)["code" => "14", "description" => "Pago por consignación"],
            (object)["code" => "15", "description" => "Condonación"],
            (object)["code" => "17", "description" => "Compensación"],
            (object)["code" => "23", "description" => "Novación"],
            (object)["code" => "24", "description" => "Confusión"],
            (object)["code" => "25", "description" => "Remisión de deuda"],
            (object)["code" => "26", "description" => "Prescripción o caducidad"],
            (object)["code" => "27", "description" => "A satisfacción del acreedor"],
            (object)["code" => "28", "description" => "Tarjeta de débito"],
            (object)["code" => "29", "description" => "Tarjeta de servicios"],
            (object)["code" => "30", "description" => "Aplicación de anticipos"],
            (object)["code" => "99", "description" => "Por definir"]
        ];

        $cfdi = [
            (object)["cfdi" => "G01", "description" => "Adquisición de mercancías"],
            (object)["cfdi" => "G02", "description" => "Devoluciones, descuentos o bonificaciones"],
            (object)["cfdi" => "G03", "description" => "Gastos en general"],
            (object)["cfdi" => "I01", "description" => "Construcciones"],
            (object)["cfdi" => "I02", "description" => "Mobiliario y equipo de oficina por inversiones"],
            (object)["cfdi" => "I03", "description" => "Equipo de transporte"],
            (object)["cfdi" => "I04", "description" => "Equipo de computo y accesorios"],
            (object)["cfdi" => "I05", "description" => "Dados, troqueles, moldes, matrices y herramental"],
            (object)["cfdi" => "I06", "description" => "Comunicaciones telefónicas"],
            (object)["cfdi" => "I07", "description" => "Comunicaciones satelitales"],
            (object)["cfdi" => "I08", "description" => "Otra maquinaria y equipo"],
            (object)["cfdi" => "D01", "description" => "Honorarios médicos, dentales y gastos hospitalarios"],
            (object)["cfdi" => "D02", "description" => "Gastos médicos por incapacidad o discapacidad"],
            (object)["cfdi" => "D03", "description" => "Gastos funerales"],
            (object)["cfdi" => "D04", "description" => "Donativos"],
            (object)["cfdi" => "D05", "description" => "Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)"],
            (object)["cfdi" => "D06", "description" => "Aportaciones voluntarias al SAR"],
            (object)["cfdi" => "D07", "description" => "Primas por seguros de gastos médicos"],
            (object)["cfdi" => "D08", "description" => "Gastos de transportación escolar obligatoria"],
            (object)["cfdi" => "D09", "description" => "Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones"],
            (object)["cfdi" => "D10", "description" => "Pagos por servicios educativos (colegiaturas)"],
            (object)["cfdi" => "S01", "description" => "Sin efectos fiscales"],
            (object)["cfdi" => "CP01", "description" => "Pagos"],
            (object)["cfdi" => "CN01", "description" => "Nómina"]
        ];

        return compact('suppliers', 'payment_method', 'method_payment', 'cfdi');
    }

    public function getPurchasesCharts(): array
    {
        $requisitions_per_department = $this->purchaseRequisition
            ->select(DB::raw('DISTINCT(department)'), DB::raw('count(*) as quantity'))
            ->groupBy('department')->get();

        $requisitions_per_employee = $this->purchaseRequisition
            ->select(DB::raw('DISTINCT(applicant)'), DB::raw('count(*) as quantity'))
            ->groupBy('applicant')->orderBy('quantity', 'desc')->limit(6)->get();

        return [
            'requisitions_per_department' => $requisitions_per_department,
            'requisitions_per_employee'   => $requisitions_per_employee,
        ];
    }

    public function getPurchaseOrders(array $filters = []): Collection
    {
        $query = $this->purchaseOrder->with('supplier');

        if (!empty($filters['search_orders'])) {
            $search = $filters['search_orders'];
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('applicant', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['date_filter'])) {
            $query->whereDate('application_date', $filters['date_filter']);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function findOrderWithDetails($id): ?PurchaseOrder
    {
        return $this->purchaseOrder->with(['details', 'supplier'])->where('id', $id)->first();
    }

    public function createPurchaseOrderWithDetails(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $supplierId = $data['supplier_id'] ?? null;
            $supplierObj = null;

            if (empty($supplierId)) {
                $name = $data['name'] ?? 'Proveedor';
                $supplierObj = $this->supplier->where('name', $name)->first();

                if (!$supplierObj) {
                    $next = $this->supplier->count() + 1;
                    while ($this->supplier->where('supplier_code', 'SP' . $next)->exists()) {
                        $next++;
                    }

                    $supplierObj = $this->supplier->create([
                        'supplier_code' => 'SP' . $next,
                        'name'          => $name,
                        'phone'         => $data['phone'] ?? null,
                        'rfc'           => $data['rfc'] ?? null,
                        'address'       => $data['address'] ?? null,
                        'city'          => $data['city'] ?? null,
                        'state'         => $data['state'] ?? null,
                        'district'      => $data['district'] ?? null,
                    ]);
                }
                $supplierId = $supplierObj->supplier_id;
            } else {
                $supplierObj = $this->supplier->findOrFail($supplierId);
            }

            if ($this->purchaseOrder->where('id', $data['id'])->exists()) {
                throw new \Exception("El folio {$data['id']} ya está registrado.");
            }

            $purchaseOrder = $this->purchaseOrder->create([
                'id'               => $data['id'],
                'supplier_id'      => $supplierId,
                'contact'          => $data['contact'] ?? null,
                'delivery_time'    => $data['delivery_time'] ?? null,
                'delivery_date'    => $data['delivery_date'] ?? null,
                'guia'             => $data['guia'] ?? null,
                'cfdi'             => $data['cfdi'] ?? null,
                'payment_method'   => $data['payment_method'] ?? null,
                'method_payment'   => $data['method_payment'] ?? null,
                'application_date' => $data['application_date'] ?? null,
                'applicant'        => $data['applicant'] ?? null,
                'price'            => $data['price'] ?? 0,
            ]);

            $products = $data['product_name'] ?? [];
            $items = [];
            foreach ($products as $index => $product) {
                if (!empty($product)) {
                    $hasIva = !empty($data['iva'][$index]) ? 1 : 0;
                    $unitPrice = $data['unit_price'][$index] ?? 0;
                    $qty = $data['quantity'][$index] ?? 0;

                    $this->purchaseOrderDetail->create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_name'      => $product,
                        'has_iva'           => $hasIva,
                        'unit_price'        => $unitPrice,
                        'quantity'          => $qty,
                    ]);

                    $items[] = [
                        'description' => $product,
                        'quantity'    => $qty,
                        'iva'         => $hasIva,
                        'unit_price'  => $unitPrice,
                    ];
                }
            }

            $address = trim(($supplierObj->address ?? '') . ', ' . ($supplierObj->city ?? ''), ', ');

            $viewData = [
                'supplier_name'    => $supplierObj->name ?? '',
                'email'            => $supplierObj->email ?? '',
                'phone'            => $supplierObj->phone ?? '',
                'rfc'              => $supplierObj->rfc ?? '',
                'address'          => $address,
                'cc_code'          => $purchaseOrder->id,
                'contact'          => $data['contact'] ?? '',
                'method_payment'   => $data['method_payment'] ?? '',
                'payment_method'   => $data['payment_method'] ?? '',
                'cfdi'             => $data['cfdi'] ?? '',
                'application_date' => $data['application_date'] ?? '',
                'delivery_time'    => $data['delivery_time'] ?? '',
                'delivery_date'    => $data['delivery_date'] ?? '',
                'numero_guia'      => $data['guia'] ?? '',
                'items'            => $items,
                'total'            => $data['price'] ?? 0,
            ];

            return [
                'order'     => $purchaseOrder,
                'view_data' => $viewData,
            ];
        });
    }

    public function updatePurchaseOrderWithDetails($id, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($id, $data) {
            $order = $this->purchaseOrder->where('id', $id)->lockForUpdate()->firstOrFail();

            if (!empty($data['id']) && $data['id'] != $id && $this->purchaseOrder->where('id', $data['id'])->exists()) {
                throw new \Exception("El folio número {$data['id']} ya está en uso por otra orden.");
            }

            $order->update([
                'id'               => $data['id'] ?? $order->id,
                'supplier_id'      => $data['supplier_id'] ?? $order->supplier_id,
                'contact'          => $data['contact'] ?? $order->contact,
                'delivery_time'    => $data['delivery_time'] ?? $order->delivery_time,
                'delivery_date'    => $data['delivery_date'] ?? $order->delivery_date,
                'guia'             => $data['guia'] ?? $order->guia,
                'cfdi'             => $data['cfdi'] ?? $order->cfdi,
                'payment_method'   => $data['payment_method'] ?? $order->payment_method,
                'method_payment'   => $data['method_payment'] ?? $order->method_payment,
                'application_date' => $data['application_date'] ?? $order->application_date,
                'applicant'        => $data['applicant'] ?? $order->applicant,
                'price'            => $data['price'] ?? $order->price,
            ]);

            $order->details()->delete();

            $products = $data['product_name'] ?? [];
            if ($products) {
                foreach ($products as $index => $product) {
                    if (!empty($product)) {
                        $order->details()->create([
                            'product_name' => $product,
                            'quantity'     => $data['quantity'][$index] ?? 0,
                            'unit_price'   => $data['unit_price'][$index] ?? 0,
                            'has_iva'      => !empty($data['iva'][$index]) ? 1 : 0,
                        ]);
                    }
                }
            }

            return $order->fresh(['details', 'supplier']);
        });
    }

    public function deleteOrder($id): bool
    {
        return DB::transaction(function () use ($id) {
            $order = $this->purchaseOrder->where('id', $id)->lockForUpdate()->first();
            if (!$order) {
                return false;
            }

            $order->details()->delete();
            $order->delete();

            return true;
        });
    }

    public function buildOrderPdfData(PurchaseOrder $order): array
    {
        $supplier = $order->supplier;

        $items = $order->details->map(function ($item) {
            return [
                'description' => $item->product_name,
                'quantity'    => (float) $item->quantity,
                'unit_price'  => (float) $item->unit_price,
                'iva'         => $item->has_iva ? 1 : 0,
            ];
        })->toArray();

        $subtotal = 0;
        $iva = 0;
        foreach ($items as $item) {
            $importe = $item['quantity'] * $item['unit_price'];
            $subtotal += round($importe, 2);
            if ($item['iva'] == 1) {
                $iva += round($importe * 0.16, 2);
            }
        }

        return [
            'supplier_name'    => $supplier?->name ?? '',
            'email'            => $supplier?->email ?? '',
            'phone'            => $supplier?->phone ?? '',
            'rfc'              => $supplier?->rfc ?? '',
            'address'          => $supplier?->address ?? '',
            'cc_code'          => $order->id,
            'contact'          => $order->contact ?? '',
            'method_payment'   => $order->method_payment ?? '',
            'payment_method'   => $order->payment_method ?? '',
            'cfdi'             => $order->cfdi ?? '',
            'application_date' => $order->application_date ?? '',
            'delivery_time'    => $order->delivery_time ?? '',
            'delivery_date'    => $order->delivery_date ?? '',
            'guia'             => $order->guia ?? '',
            'items'            => $items,
            'subtotal'         => $subtotal,
            'iva'              => $iva,
            'total'            => round($subtotal + $iva, 2),
        ];
    }

    public function buildSelectionCriteriaPdfData(array $input): array
    {
        $data = [
            'supplier' => $input['supplier'] ?? '',
            'address'  => $input['address'] ?? '',
            'date'     => $input['date'] ?? now()->toDateString(),
            'products' => $input['products'] ?? '',
            'checks'   => [],
        ];

        $count = 0;
        foreach ($input as $key => $value) {
            if (preg_match('/^(e|q|o)-\d+$/', $key)) {
                $data['checks'][] = $value;
                if ($value == 1) {
                    $count += 1;
                }
            }
        }

        $data['result'] = round(($count / 26) * 100, 2);
        return $data;
    }

    public function buildSupplierEvaluationPdfData(array $input): array
    {
        $answers = (array) ($input['answers'] ?? []);
        $qualifications = (array) ($input['qualification'] ?? []);

        $questions_answers = [];
        for ($i = 0; $i < 9; $i++) {
            $questions_answers[] = [
                'answer'        => $answers[$i] ?? '0',
                'qualification' => $qualifications[$i] ?? '',
            ];
        }

        return [
            'supplier'          => $input['supplier_name'] ?? '',
            'rfc'               => $input['rfc'] ?? '',
            'address'           => $input['address'] ?? '',
            'evaluation_date'   => $input['evaluation_date'] ?? now()->toDateString(),
            'evaluator'         => $input['evaluator'] ?? '',
            'products'          => $input['products'] ?? '',
            'observations'      => $input['observations'] ?? '',
            'questions_answers' => $questions_answers,
        ];
    }
}
