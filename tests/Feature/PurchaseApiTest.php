<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseRequisition;
use App\Models\Sector;
use App\Models\Supplier;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    Permission::findOrCreate('purchases.show', 'web');
    Permission::findOrCreate('purchases.admin', 'web');
    $this->user->givePermissionTo(['purchases.show', 'purchases.admin']);

    $this->sector = Sector::first() ?? Sector::create([
        'name' => 'Industrial',
        'code' => 'IND',
    ]);

    $this->supplier = Supplier::create([
        'supplier_code' => 'SP-PO-' . rand(1000, 9999),
        'name'          => 'Proveedor PO ' . uniqid(),
        'rfc'           => 'PO' . rand(10000000, 99999999) . 'X',
        'address'       => 'Av. Compras 456',
        'city'          => 'Guadalajara',
        'email'         => 'compras@proveedor.com',
        'phone'         => '3333333333',
    ]);

    $this->orderId = 'OC-TEST-' . rand(10000, 99999);

    $this->order = PurchaseOrder::create([
        'id'               => $this->orderId,
        'supplier_id'      => $this->supplier->supplier_id,
        'contact'          => 'Contacto PO',
        'delivery_time'    => '2 días',
        'delivery_date'    => now()->addDays(2)->toDateString(),
        'guia'             => 'GUIA-12345',
        'cfdi'             => 'G01',
        'payment_method'   => 'PUE',
        'method_payment'   => '03',
        'application_date' => now()->toDateString(),
        'applicant'        => $this->user->name,
        'price'            => 1160.00,
    ]);

    $this->detail = PurchaseOrderDetail::create([
        'purchase_order_id' => $this->order->id,
        'product_name'      => 'Producto Detalle 1',
        'quantity'          => 10,
        'unit_price'        => 100.00,
        'has_iva'           => 1,
    ]);

    $this->requisition = PurchaseRequisition::create([
        'applicant'    => $this->user->name,
        'department'   => 'Compras',
        'consecutive'  => 1,
        'data_sheet'   => 0,
        'safety_sheet' => 0,
    ]);
});

afterEach(function () {
    if (isset($this->order)) {
        PurchaseOrderDetail::where('purchase_order_id', $this->order->id)->delete();
        PurchaseOrder::where('id', $this->order->id)->delete();
    }
    if (isset($this->supplier)) {
        Supplier::where('supplier_id', $this->supplier->supplier_id)->delete();
    }
    if (isset($this->requisition)) {
        PurchaseRequisition::where('id', $this->requisition->id)->delete();
    }
});

test('index renders purchases view with counters and catalogs', function () {
    $response = $this->actingAs($this->user)->get('/purchases');

    $response->assertStatus(200);
    $response->assertViewIs('purchases');
    $response->assertViewHasAll([
        'requisitions_count',
        'requisitions_no_check',
        'your_requisitions',
        'suppliers',
        'sectors',
        'warehouse_entries',
        'payment_method',
        'method_payment',
        'cfdi',
    ]);
});

test('getPurchasesCharts returns json of requisitions by department and employee', function () {
    $response = $this->actingAs($this->user)->getJson(route('purchases.charts'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'requisitions_per_department',
        'requisitions_per_employee',
    ]);
});

test('orders renders purchases.order view', function () {
    $response = $this->actingAs($this->user)->get(route('purchases.orders'));

    $response->assertStatus(200);
    $response->assertViewIs('purchases.order');
    $response->assertViewHasAll(['suppliers', 'payment_method', 'method_payment', 'cfdi']);
});

test('getPurchaseOrders returns json list with filters', function () {
    $response = $this->actingAs($this->user)->getJson(route('purchases.getPurchaseOrders', [
        'search_orders' => $this->order->id,
        'date_filter'   => now()->toDateString(),
    ]));

    $response->assertStatus(200);
    $response->assertJsonStructure(['orders']);
    $orders = $response->json('orders');
    expect(count($orders))->toBeGreaterThanOrEqual(1);
    expect($orders[0]['id'])->toBe($this->order->id);
});

test('show with get-orders delegates to getPurchaseOrders', function () {
    $response = $this->actingAs($this->user)->getJson('/purchases/get-orders');

    $response->assertStatus(200);
    $response->assertJsonStructure(['orders']);
});

test('gPurchaseOrder creates order and details and returns pdf stream', function () {
    $newId = 'OC-NEW-' . rand(10000, 99999);

    $payload = [
        'id'               => $newId,
        'supplier_id'      => $this->supplier->supplier_id,
        'contact'          => 'Nuevo Contacto',
        'delivery_time'    => '3 días',
        'delivery_date'    => now()->addDays(3)->toDateString(),
        'guia'             => 'GUIA-NEW-01',
        'cfdi'             => 'G01',
        'payment_method'   => 'PUE',
        'method_payment'   => '03',
        'application_date' => now()->toDateString(),
        'applicant'        => $this->user->name,
        'price'            => 232.00,
        'product_name'     => ['Articulo A', 'Articulo B'],
        'quantity'         => [2, 1],
        'unit_price'       => [50, 100],
        'iva'              => [1, 1],
    ];

    $response = $this->actingAs($this->user)->post(route('purchases.gPurchaseOrder'), $payload);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');

    $this->assertDatabaseHas('purchase_orders', ['id' => $newId]);
    $this->assertDatabaseHas('purchase_order_details', [
        'purchase_order_id' => $newId,
        'product_name'      => 'Articulo A',
    ]);

    // Clean up
    PurchaseOrderDetail::where('purchase_order_id', $newId)->delete();
    PurchaseOrder::where('id', $newId)->delete();
});

test('streamPdf returns purchase order pdf response', function () {
    $response = $this->actingAs($this->user)->get(route('purchases.streamPdf', $this->order->id));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('edit returns order details json', function () {
    $response = $this->actingAs($this->user)->getJson(route('purchases.editOrder', $this->order->id));

    $response->assertStatus(200);
    $response->assertJsonPath('id', $this->order->id);
    $response->assertJsonStructure(['details', 'supplier']);
});

test('update modifies purchase order and details', function () {
    $payload = [
        'id'               => $this->order->id,
        'supplier_id'      => $this->supplier->supplier_id,
        'contact'          => 'Contacto Modificado',
        'delivery_time'    => '5 días',
        'delivery_date'    => now()->addDays(5)->toDateString(),
        'guia'             => 'GUIA-MOD-99',
        'cfdi'             => 'G03',
        'payment_method'   => 'PPD',
        'method_payment'   => '99',
        'application_date' => now()->toDateString(),
        'applicant'        => $this->user->name,
        'price'            => 500.00,
        'product_name'     => ['Producto Modificado'],
        'quantity'         => [5],
        'unit_price'       => [100],
        'iva'              => [0],
    ];

    $response = $this->actingAs($this->user)->putJson(route('purchases.updateOrder', $this->order->id), $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->order->refresh();
    expect($this->order->contact)->toBe('Contacto Modificado');
    expect($this->order->guia)->toBe('GUIA-MOD-99');

    $this->assertDatabaseHas('purchase_order_details', [
        'purchase_order_id' => $this->order->id,
        'product_name'      => 'Producto Modificado',
    ]);
});

test('destroy removes purchase order and its details', function () {
    $tempId = 'OC-TEMP-' . rand(10000, 99999);
    $tempOrder = PurchaseOrder::create([
        'id'          => $tempId,
        'supplier_id' => $this->supplier->supplier_id,
    ]);
    PurchaseOrderDetail::create([
        'purchase_order_id' => $tempId,
        'product_name'      => 'Temp Detail',
        'quantity'          => 1,
        'unit_price'        => 10.0,
        'has_iva'           => 0,
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('purchases.destroyOrder', $tempId));

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseMissing('purchase_orders', ['id' => $tempId]);
    $this->assertDatabaseMissing('purchase_order_details', ['purchase_order_id' => $tempId]);
});

test('destroy returns 404 for non-existent order', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('purchases.destroyOrder', 'OC-INEXISTENTE'));

    $response->assertStatus(404);
    $response->assertJsonPath('success', false);
});

test('gSupSelectCrit generates selection criteria pdf stream', function () {
    $payload = [
        'supplier' => $this->supplier->name,
        'address'  => 'Dirección Criterios',
        'date'     => now()->toDateString(),
        'products' => 'Insumos Diversos',
        'e-1'      => 1,
        'q-2'      => 1,
        'o-3'      => 0,
    ];

    $response = $this->actingAs($this->user)->post(route('suppliers.gSupSelectCrit'), $payload);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('gSupplierEvaluation generates supplier evaluation pdf stream', function () {
    $payload = [
        'supplier_name'   => $this->supplier->name,
        'rfc'             => $this->supplier->rfc,
        'address'         => $this->supplier->address,
        'evaluation_date' => now()->toDateString(),
        'evaluator'       => 'Ing. Auditor',
        'products'        => 'Materiales Químicos',
        'observations'    => 'Cumple con estándares',
        'answers'         => ['Respuesta 1', 'Respuesta 2'],
        'qualification'   => ['10', '9'],
    ];

    $response = $this->actingAs($this->user)->post(route('suppliers.gSupplierEvaluation'), $payload);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('demo endpoints return pdf streams', function () {
    $resDemo1 = $this->actingAs($this->user)->get(route('purchases.po.demo'));
    $resDemo1->assertStatus(200);
    expect($resDemo1->headers->get('content-type'))->toContain('application/pdf');

    $resDemo2 = $this->actingAs($this->user)->get(route('suppliers.criteria.demo'));
    $resDemo2->assertStatus(200);
    expect($resDemo2->headers->get('content-type'))->toContain('application/pdf');

    $resDemo3 = $this->actingAs($this->user)->get(route('suppliers.evaluation.demo6'));
    $resDemo3->assertStatus(200);
    expect($resDemo3->headers->get('content-type'))->toContain('application/pdf');
});
