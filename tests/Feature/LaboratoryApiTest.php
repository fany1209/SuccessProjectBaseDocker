<?php

use App\Models\Customer;
use App\Models\CustomerSampleRequest;
use App\Models\CustomerSampleRequestItem;
use App\Models\Inventory;
use App\Models\LaboratorySample;
use App\Models\Product;
use App\Models\SalidaMuestra;
use App\Models\Supplier;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    $this->unauthorizedUser = User::factory()->create();

    Permission::findOrCreate('laboratory.show', 'web');
    $this->user->givePermissionTo('laboratory.show');

    $this->product = Product::first() ?? Product::create([
        'name'        => 'Producto Lab Test ' . uniqid(),
        'sku'         => 'PL-' . rand(1000, 9999),
        'sat_code'    => '01010101',
        'category_id' => 1,
    ]);

    $this->supplier = Supplier::first() ?? Supplier::create([
        'name'          => 'Proveedor Lab ' . uniqid(),
        'supplier_code' => 'SL-' . rand(1000, 9999),
    ]);

    $this->customer = Customer::first() ?? Customer::create([
        'name'        => 'Cliente Test ' . uniqid(),
        'rfc'         => 'XAXX010101000',
        'phone'       => '5551234567',
        'email'       => 'cliente@test.com',
        'address'     => 'Calle Test 123',
        'city'        => 'CDMX',
        'state'       => 'CDMX',
        'postal_code' => '01000',
        'country'     => 'Mexico',
    ]);
});

afterEach(function () {
    CustomerSampleRequestItem::query()->delete();
    CustomerSampleRequest::query()->delete();
    SalidaMuestra::query()->delete();
    LaboratorySample::query()->delete();
});

test('laboratory index renders successfully with required permissions', function () {
    $response = $this->actingAs($this->user)->get(route('laboratory.index'));
    $response->assertStatus(200);
    $response->assertViewIs('laboratory');
});

test('preview and pdf format 01 return successful responses', function () {
    $preview = $this->actingAs($this->user)->get(route('laboratory.format01.preview'));
    $preview->assertStatus(200);

    $pdf = $this->actingAs($this->user)->get(route('laboratory.format01.pdf'));
    $pdf->assertStatus(200);
    expect($pdf->headers->get('content-type'))->toContain('application/pdf');
});

test('store2 creates a customer sample request with items and auto-generated folio', function () {
    $payload = [
        'customer_id'             => $this->customer->customer_id,
        'cliente_nombre'          => $this->customer->name,
        'fecha_solicitud'         => now()->toDateString(),
        'fecha_recoleccion'       => now()->addDays(2)->toDateString(),
        'cliente_correo'          => 'contacto@cliente.com',
        'cliente_telefono'        => '5559876543',
        'cliente_direccion'       => 'Av Principal 456',
        'cliente_estatus'         => 'nuevo',
        'personal_seguimiento'    => 'Agente Ventas',
        'solicitante_nombre'      => 'Solicitante Test',
        'observaciones'           => 'Prueba de envío de muestra',
        'entrega_paqueteria'      => 1,
        'paq_nombre'              => 'DHL',
        'paq_guia'                => 'GUID123456',
        'items' => [
            [
                'product_id'      => $this->product->product_id,
                'sku'             => $this->product->sku,
                'um'              => 'kg',
                'cantidad'        => 2.5,
                'pres_ziploc'     => 1,
                'docs_ft'         => 1,
                'lote_almacen'    => 'LOTE-A1',
                'lote_venta'      => 'LOTE-V1',
            ]
        ]
    ];

    $response = $this->actingAs($this->user)->postJson(route('laboratory.store2'), $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    $this->assertDatabaseHas('customer_sample_requests', [
        'cliente_nombre' => $this->customer->name,
    ]);
    $this->assertDatabaseHas('customer_sample_request_items', [
        'product_id' => $this->product->product_id,
        'sku'        => $this->product->sku,
    ]);
});

test('getCustomerRequestsJson and showCustomerRequest return expected data', function () {
    $csr = CustomerSampleRequest::create([
        'folio'                => 'S0001',
        'cliente_nombre'       => 'Cliente Consulta',
        'fecha_solicitud'      => now()->toDateString(),
        'solicitante_nombre'   => 'Solicitante A',
        'status'               => 0,
    ]);

    $listResponse = $this->actingAs($this->user)->getJson(route('laboratory.customer_requests.json'));
    $listResponse->assertStatus(200);
    $listResponse->assertJsonPath('success', true);

    $showResponse = $this->actingAs($this->user)->getJson("/laboratory/customer-request/{$csr->id}");
    $showResponse->assertStatus(200);
    $showResponse->assertJsonPath('success', true);
    $showResponse->assertJsonPath('data.id', $csr->id);
});

test('updateCustomerRequest modifies request and synchronizes items', function () {
    $csr = CustomerSampleRequest::create([
        'folio'                => 'S0002',
        'cliente_nombre'       => 'Cliente Original',
        'fecha_solicitud'      => now()->toDateString(),
        'solicitante_nombre'   => 'Solicitante B',
        'status'               => 0,
    ]);

    $updatePayload = [
        'cliente_nombre'       => 'Cliente Editado',
        'fecha_solicitud'      => now()->toDateString(),
        'solicitante_nombre'   => 'Solicitante Modificado',
        'items' => [
            [
                'product_id'   => $this->product->product_id,
                'sku'          => $this->product->sku,
                'um'           => 'l',
                'cantidad'     => 10,
                'pres_frasco'  => 1,
            ]
        ]
    ];

    $response = $this->actingAs($this->user)->postJson(route('laboratory.update2', $csr->id), $updatePayload);
    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('customer_sample_requests', [
        'id'             => $csr->id,
        'cliente_nombre' => 'Cliente Editado',
    ]);
    $this->assertDatabaseHas('customer_sample_request_items', [
        'customer_sample_request_id' => $csr->id,
        'um'                         => 'l',
    ]);
});

test('updateStatus changes request status successfully', function () {
    $csr = CustomerSampleRequest::create([
        'folio'          => 'S0003',
        'cliente_nombre' => 'Cliente Status',
        'status'         => 0,
    ]);

    $response = $this->actingAs($this->user)->patchJson(route('laboratory.update_status', $csr->id), [
        'status' => 2,
    ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $this->assertDatabaseHas('customer_sample_requests', [
        'id'     => $csr->id,
        'status' => 2,
    ]);
});

test('destroyCustomerRequest deletes the sample request and cascades its items', function () {
    $csr = CustomerSampleRequest::create([
        'folio'          => 'S0004',
        'cliente_nombre' => 'Cliente Eliminar',
        'status'         => 0,
    ]);

    CustomerSampleRequestItem::create([
        'customer_sample_request_id' => $csr->id,
        'product_id'                 => $this->product->product_id,
        'cantidad'                   => 1,
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/laboratory/customer-request/{$csr->id}");
    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseMissing('customer_sample_requests', ['id' => $csr->id]);
    $this->assertDatabaseMissing('customer_sample_request_items', ['customer_sample_request_id' => $csr->id]);
});

test('inv creates laboratory sample record and exportCsv streams content', function () {
    $payload = [
        'folio'           => 'T001',
        'tipo_muestra'    => 'Retencion',
        'proveedor'       => $this->supplier->name,
        'sku'             => $this->product->sku,
        'producto'        => $this->product->name,
        'stock_inicial'   => 500,
        'presentacion'    => 'Frasco 500ml',
        'ubicacion_stock' => 'Estante A1',
        'fecha_entrada'   => now()->toDateString(),
        'solicitante'     => 'Lab User',
    ];

    $response = $this->actingAs($this->user)->postJson(route('laboratory_samples.inv'), $payload);
    $response->assertStatus(201);
    $response->assertJsonPath('success', true);

    $generatedFolio = $response->json('data.folio');
    $this->assertDatabaseHas('laboratory_samples', [
        'folio'    => $generatedFolio,
        'producto' => $this->product->name,
    ]);

    $exportResponse = $this->actingAs($this->user)->get(route('laboratory_samples.export'));
    $exportResponse->assertStatus(200);
    expect($exportResponse->headers->get('content-type'))->toContain('text/csv');
});

test('pdf3 records salida muestra and deducts stock when matching folio exists', function () {
    $sample = LaboratorySample::create([
        'folio'           => 'T002',
        'tipo_muestra'    => 'Retencion',
        'producto'        => $this->product->name,
        'stock_inicial'   => 1000,
        'cantidad_salida' => 0,
    ]);

    $payload = [
        'folio_muestra'    => 'T002',
        'product_id'       => $this->product->product_id,
        'cantidad'         => '200',
        'um'               => 'g',
        'motivo_salida'    => 'analisis',
        'dest_nombre'      => 'Cliente Destino',
    ];

    $response = $this->actingAs($this->user)->post(route('laboratory.pdf3'), $payload);
    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');

    $this->assertDatabaseHas('salida_muestras', [
        'folio_muestra' => 'T002',
        'product_id'    => $this->product->product_id,
    ]);

    $sample->refresh();
    expect((float)$sample->cantidad_salida)->toEqual(200.0);
});

test('getMuestras, updateMuestra and destroyMuestra manage salida_muestras correctly', function () {
    $salida = SalidaMuestra::create([
        'folio_muestra'    => 'SM01',
        'product_id'       => $this->product->product_id,
        'nombre_comercial' => 'Muestra 1',
        'cantidad'         => '100',
        'um'               => 'g',
        'motivo_salida'    => 'analisis',
        'dest_nombre'      => 'Destinatario Original',
    ]);

    $getResponse = $this->actingAs($this->user)->getJson(route('laboratory.getMuestras'));
    $getResponse->assertStatus(200);
    $getResponse->assertJsonPath('success', true);

    $updateResponse = $this->actingAs($this->user)->putJson(route('laboratory.updateMuestra', $salida->id), [
        'dest_nombre'   => 'Destinatario Actualizado',
        'motivo_salida' => 'retencion',
    ]);
    $updateResponse->assertStatus(200);
    $updateResponse->assertJsonPath('success', true);

    $this->assertDatabaseHas('salida_muestras', [
        'id'          => $salida->id,
        'dest_nombre' => 'Destinatario Actualizado',
    ]);

    $destroyResponse = $this->actingAs($this->user)->deleteJson(route('laboratory.destroyMuestra', $salida->id));
    $destroyResponse->assertStatus(200);
    $destroyResponse->assertJsonPath('success', true);

    $this->assertDatabaseMissing('salida_muestras', ['id' => $salida->id]);
});

test('pdf generation endpoints return valid pdf stream', function () {
    $pdf1 = $this->actingAs($this->user)->post(route('laboratory.pdf1'), [
        'product_id'       => $this->product->product_id,
        'nombre_comercial' => 'Producto PDF 1',
    ]);
    $pdf1->assertStatus(200);
    expect($pdf1->headers->get('content-type'))->toContain('application/pdf');

    $pdf2 = $this->actingAs($this->user)->post(route('laboratory.pdf2'), [
        'cliente_nombre' => 'Cliente PDF 2',
        'items' => [
            [
                'product_id' => $this->product->product_id,
                'cantidad'   => 1,
            ]
        ]
    ]);
    $pdf2->assertStatus(200);
    expect($pdf2->headers->get('content-type'))->toContain('application/pdf');

    $pdf5 = $this->actingAs($this->user)->post(route('laboratory.pdf5'), [
        'tipo'    => 'limpieza',
        'periodo' => 'Semanal',
    ]);
    $pdf5->assertStatus(200);
    expect($pdf5->headers->get('content-type'))->toContain('application/pdf');

    $pdf6 = $this->actingAs($this->user)->post(route('laboratory.pdf6'), [
        'material' => 'Vidrio',
    ]);
    $pdf6->assertStatus(200);
    expect($pdf6->headers->get('content-type'))->toContain('application/pdf');

    $pdf7 = $this->actingAs($this->user)->post(route('laboratory.pdf7'), [
        'equipo' => 'Balanza Analitica',
    ]);
    $pdf7->assertStatus(200);
    expect($pdf7->headers->get('content-type'))->toContain('application/pdf');

    $pdf9 = $this->actingAs($this->user)->post(route('laboratory.pdf9'), [
        'solucion' => 'Agua Destilada',
    ]);
    $pdf9->assertStatus(200);
    expect($pdf9->headers->get('content-type'))->toContain('application/pdf');

    $pdf10 = $this->actingAs($this->user)->post(route('laboratory.pdf10'), [
        'codigo' => 'DISP-01',
    ]);
    $pdf10->assertStatus(200);
    expect($pdf10->headers->get('content-type'))->toContain('application/pdf');

    $pdf11 = $this->actingAs($this->user)->post(route('laboratory.pdf11'), [
        'temperatura' => '22',
    ]);
    $pdf11->assertStatus(200);
    expect($pdf11->headers->get('content-type'))->toContain('application/pdf');

    $pdf12 = $this->actingAs($this->user)->post(route('laboratory.pdf12'), [
        'insumo' => 'Guantes de Nitrilo',
    ]);
    $pdf12->assertStatus(200);
    expect($pdf12->headers->get('content-type'))->toContain('application/pdf');

    $pdf14 = $this->actingAs($this->user)->post(route('laboratory.pdf14'), [
        'responsable' => 'QFB Supervisor',
    ]);
    $pdf14->assertStatus(200);
    expect($pdf14->headers->get('content-type'))->toContain('application/pdf');

    $pdf01pr = $this->actingAs($this->user)->post(route('laboratory.pdf01pr'), [
        'equipo' => 'pHmetro Metrohm',
    ]);
    $pdf01pr->assertStatus(200);
    expect($pdf01pr->headers->get('content-type'))->toContain('application/pdf');
});
