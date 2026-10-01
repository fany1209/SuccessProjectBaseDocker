<?php

use App\Models\Customer;
use App\Models\InspectionW;
use App\Models\Inventory;
use App\Models\Observacion;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    $this->unauthorizedUser = User::factory()->create();

    Permission::findOrCreate('quality.show', 'web');
    Permission::findOrCreate('quality.update', 'web');
    Permission::findOrCreate('quality.delete', 'web');
    Permission::findOrCreate('quality.updateW', 'web');

    $this->user->givePermissionTo(['quality.show', 'quality.update', 'quality.delete', 'quality.updateW']);

    $role = Role::findOrCreate('Warehouse', 'web');
    $this->user->assignRole($role);

    $this->product = Product::first() ?? Product::create([
        'name'        => 'Producto Calidad Test ' . uniqid(),
        'sku'         => 'PC-' . rand(1000, 9999),
        'sat_code'    => '01010101',
        'category_id' => 1,
    ]);

    $this->supplier = Supplier::first() ?? Supplier::create([
        'name'          => 'Proveedor Calidad ' . uniqid(),
        'supplier_code' => 'QC-' . rand(1000, 9999),
    ]);

    $this->customer = Customer::first() ?? Customer::create([
        'name'          => 'Cliente Calidad ' . uniqid(),
        'customer_code' => 'CL-' . rand(100, 999),
        'rfc'           => 'XAXX010101000',
        'phone'         => '5551234567',
        'email'         => 'calidad@test.com',
        'address'       => 'Av Calidad 100',
        'city'          => 'Culiacán',
        'state'         => 'Sinaloa',
        'postal_code'   => '80000',
        'country'       => 'Mexico',
    ]);

    $this->inventory = Inventory::create([
        'product_id' => $this->product->product_id,
        'batch'      => 'LOTE-QUAL-' . uniqid(),
        'stock'      => 50.0,
    ]);
});

afterEach(function () {
    Observacion::query()->delete();
    InspectionW::query()->delete();
    DB::table('incidencias')->delete();
    DB::table('pdf_clicks')->delete();
});

test('unauthorized user cannot access quality module', function () {
    $response = $this->actingAs($this->unauthorizedUser)->get(route('quality.index'));
    $response->assertStatus(403);
});

test('authorized user can view quality index, ver and pdf5form', function () {
    $resIndex = $this->actingAs($this->user)->get(route('quality.index'));
    $resIndex->assertStatus(200);
    $resIndex->assertViewIs('quality');

    $resVer = $this->actingAs($this->user)->get(route('quality.pdf'));
    $resVer->assertStatus(200);

    $resPdf5Form = $this->actingAs($this->user)->get(route('quality.pdf5.form'));
    $resPdf5Form->assertStatus(200);
});

test('incidencias returns json list of recent records', function () {
    DB::table('incidencias')->insert([
        'folio'            => 'INC-TEST-001',
        'supplier_id'      => $this->supplier->supplier_id,
        'product_id'       => $this->product->product_id,
        'supplier_name'    => $this->supplier->name,
        'product_name'     => $this->product->name,
        'categoria'        => 'MP',
        'remitidos'        => '100 kg',
        'fecha_incidencia' => now()->toDateString(),
        'descripcion'      => 'Prueba de incidencia',
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);

    $response = $this->actingAs($this->user)->getJson(route('quality.incidencias'));
    $response->assertStatus(200);
    $response->assertJsonFragment(['folio' => 'INC-TEST-001']);
});

test('getData and getProducts return json collections', function () {
    $resData = $this->actingAs($this->user)->getJson(route('quality.getDataq'));
    $resData->assertStatus(200);
    $resData->assertJsonStructure(['suppliers']);

    $resProducts = $this->actingAs($this->user)->getJson(route('quality.getProducts'));
    $resProducts->assertStatus(200);
    $resProducts->assertJsonStructure(['products']);
});

test('store generates reception inspection pdf download', function () {
    $payload = [
        'supplier'         => $this->supplier->supplier_code,
        'supplier_name'    => $this->supplier->name,
        'arrival_date'     => now()->toDateString(),
        'inspection_date'  => now()->toDateString(),
        'inspector_nombre' => 'Inspector Calidad',
        'products'         => [
            [
                'name' => $this->product->name,
                'lot'  => 'LOTE-REC-01',
                'qty'  => 10,
                'pack' => 'Saco',
            ]
        ],
        'release_eval'     => [
            'cantidad'       => 5,
            'identificacion' => 5,
            'empaque'        => 10,
            'sellado'        => 15,
            'limpieza'       => 15,
            'caducidad'      => 25,
            'certificado'    => 25,
        ],
    ];

    $response = $this->actingAs($this->user)->post(route('quality.store'), $payload);
    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('almacenStore saves inspection with observations and evidence files', function () {
    $fakeFile = UploadedFile::fake()->image('evidencia.jpg', 600, 600);

    $payload = [
        'fecha_inspeccion' => now()->toDateString(),
        'inspector'        => 'Inspector Almacen',
        'hora_turno'       => '08:30',
        'turno'            => '1',
        'area'             => ['nave1'],
        'responsable'      => 'Jefe Turno',
        'comentarios'      => 'Sin novedades',
        'obs'              => [
            [
                'name'           => 'Pisos limpios',
                'rev'            => 'cumple',
                'fecha'          => now()->toDateString(),
                'ubicacion'      => 'Pasillo A',
                'evidencia_file' => $fakeFile,
            ]
        ]
    ];

    $response = $this->actingAs($this->user)->post(route('quality.almacen.store'), $payload);
    $response->assertStatus(302);
    $response->assertSessionHas('ok');

    $this->assertDatabaseHas('inspections_w', [
        'inspector'   => 'Inspector Almacen',
        'responsable' => 'Jefe Turno',
    ]);

    $this->assertDatabaseHas('observaciones', [
        'name'      => 'Pisos limpios',
        'rev'       => 'cumple',
        'ubicacion' => 'Pasillo A',
    ]);
});

test('getInspections, getInspection, getWarehouseInspection and getInspectionWView return inspection data', function () {
    $inspection = InspectionW::create([
        'fecha_inspeccion' => now()->toDateString(),
        'inspector'        => 'Inspector Query',
        'responsable'      => 'Responsable Query',
        'turno'            => '1',
        'area'             => ['nave1', 'nave2'],
    ]);

    $obs = Observacion::create([
        'inspection_id' => $inspection->id,
        'name'          => 'Observacion Query',
        'rev'           => 'cumple',
        'ubicacion'     => 'Estante 1',
    ]);

    $resAll = $this->actingAs($this->user)->getJson(route('quality.getInspections'));
    $resAll->assertStatus(200);
    $resAll->assertJsonFragment(['inspector' => 'Inspector Query']);

    $resSingle = $this->actingAs($this->user)->getJson(route('quality.getInspection', ['id' => $inspection->id]));
    $resSingle->assertStatus(200);
    $resSingle->assertJsonPath('inspection.id', $inspection->id);

    $resWh = $this->actingAs($this->user)->getJson(route('quality.getWarehouseInspection', ['id' => $inspection->id]));
    $resWh->assertStatus(200);
    $resWh->assertJsonPath('id', $inspection->id);

    $resView = $this->actingAs($this->user)->getJson(route('quality.getInspectionWView', ['id' => $inspection->id]));
    $resView->assertStatus(200);
    $resView->assertJsonPath('id', $inspection->id);
});

test('update modifies inspection and observations', function () {
    $inspection = InspectionW::create([
        'fecha_inspeccion' => now()->toDateString(),
        'inspector'        => 'Inspector Pre-Update',
        'responsable'      => 'Responsable Pre-Update',
    ]);

    $obs = Observacion::create([
        'inspection_id' => $inspection->id,
        'name'          => 'Obs Original',
        'rev'           => 'cumple',
    ]);

    $payload = [
        'inspection_id' => $inspection->id,
        'inspector'     => 'Inspector Modificado',
        'responsable'   => 'Responsable Modificado',
        'obs'           => [
            [
                'id'        => $obs->id,
                'name'      => 'Obs Modificada',
                'rev'       => 'no_cumple',
                'ubicacion' => 'Zona B',
            ]
        ]
    ];

    $response = $this->actingAs($this->user)->postJson(route('quality.updateInspection'), $payload);
    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('inspections_w', [
        'id'        => $inspection->id,
        'inspector' => 'Inspector Modificado',
    ]);

    $this->assertDatabaseHas('observaciones', [
        'id'        => $obs->id,
        'name'      => 'Obs Modificada',
        'rev'       => 'no_cumple',
        'ubicacion' => 'Zona B',
    ]);
});

test('updatew updates warehouse inspection feedback and evidence', function () {
    $inspection = InspectionW::create([
        'fecha_inspeccion' => now()->toDateString(),
        'inspector'        => 'Inspector WH',
    ]);

    $obs = Observacion::create([
        'inspection_id' => $inspection->id,
        'name'          => 'Obs WH',
        'rev'           => 'no_cumple',
    ]);

    $fakeCorrFile = UploadedFile::fake()->image('ev_corr.png');

    $payload = [
        'comentarios' => 'Corrección atendida en almacén',
        'obs'         => [
            [
                'id'           => $obs->id,
                'name'         => 'Obs WH Corregida',
                'fecha'        => now()->toDateString(),
                'ev_corr_file' => $fakeCorrFile,
            ]
        ]
    ];

    $response = $this->actingAs($this->user)->patch(route('quality.warehouse.updatew', $inspection->id), $payload);
    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('inspections_w', [
        'id'          => $inspection->id,
        'comentarios' => 'Corrección atendida en almacén',
    ]);

    $this->assertDatabaseHas('observaciones', [
        'id'   => $obs->id,
        'name' => 'Obs WH Corregida',
    ]);
});

test('eliminarObs deletes single observation and destroy removes entire inspection', function () {
    $inspection = InspectionW::create([
        'fecha_inspeccion' => now()->toDateString(),
        'inspector'        => 'Inspector Eliminar',
    ]);

    $obs1 = Observacion::create([
        'inspection_id' => $inspection->id,
        'name'          => 'Obs Para Borrar 1',
    ]);

    $obs2 = Observacion::create([
        'inspection_id' => $inspection->id,
        'name'          => 'Obs Para Borrar 2',
    ]);

    $resObs = $this->actingAs($this->user)->deleteJson(route('quality.eliminarObs'), ['id' => $obs1->id]);
    $resObs->assertStatus(200);
    $resObs->assertJsonPath('success', true);
    $this->assertDatabaseMissing('observaciones', ['id' => $obs1->id]);

    $resIns = $this->actingAs($this->user)->deleteJson(route('quality.inspections.destroy', $inspection->id));
    $resIns->assertStatus(200);
    $resIns->assertJsonPath('success', true);

    $this->assertDatabaseMissing('inspections_w', ['id' => $inspection->id]);
    $this->assertDatabaseMissing('observaciones', ['id' => $obs2->id]);
});

test('checkPending and pdfGenerationsChartData return expected results', function () {
    InspectionW::create([
        'fecha_inspeccion' => now()->toDateString(),
        'status'           => 0,
    ]);

    $resPending = $this->actingAs($this->user)->getJson(route('quality.checkPendingWarehouseInspections'));
    $resPending->assertStatus(200);
    $resPending->assertJsonStructure(['pending']);

    DB::table('pdf_clicks')->insert([
        'reference_id' => 1,
        'pdf_type'     => 'A',
        'user_id'      => $this->user->id,
        'generated_at' => now(),
    ]);

    $resChart = $this->actingAs($this->user)->getJson(route('charts.pdf-generations'));
    $resChart->assertStatus(200);
    $resChart->assertJsonStructure(['data']);
});

test('quality pdf generation endpoints return successful pdf responses', function () {
    $inspection = InspectionW::create([
        'fecha_inspeccion' => now()->toDateString(),
        'inspector'        => 'Inspector PDF',
        'responsable'      => 'Responsable PDF',
        'turno'            => '1',
    ]);

    $pdf2 = $this->actingAs($this->user)->post(route('quality.pdf2'), [
        'product_id'        => $this->product->product_id,
        'descripcion_breve' => 'Descripción breve producto',
    ]);
    $pdf2->assertStatus(200);
    expect($pdf2->headers->get('content-type'))->toContain('application/pdf');

    $pdf3 = $this->actingAs($this->user)->post(route('quality.pdf3'), [
        'producto_nombre' => 'Producto SDS',
    ]);
    $pdf3->assertStatus(200);
    expect($pdf3->headers->get('content-type'))->toContain('application/pdf');

    $pdf4 = $this->actingAs($this->user)->post(route('quality.pdf4'), [
        'inspection_id' => $inspection->id,
    ]);
    $pdf4->assertStatus(200);
    expect($pdf4->headers->get('content-type'))->toContain('application/pdf');

    $pdf6 = $this->actingAs($this->user)->post(route('quality.pdf6'), [
        'folio'         => 'INC-PDF-001',
        'supplier_name' => $this->supplier->name,
        'product_name'  => $this->product->name,
    ]);
    $pdf6->assertStatus(200);
    expect($pdf6->headers->get('content-type'))->toContain('application/pdf');

    $pdf7 = $this->actingAs($this->user)->post(route('quality.pdf7'), [
        'titulo'      => 'Instructivo PDF 7',
        'descripcion' => 'Instrucciones de calidad',
    ]);
    $pdf7->assertStatus(200);
    expect($pdf7->headers->get('content-type'))->toContain('application/pdf');

    $pdf8 = $this->actingAs($this->user)->post(route('quality.pdf8'), [
        'batch'       => [['LOTE-A']],
        'observation' => [['Observación Lote A']],
    ]);
    $pdf8->assertStatus(200);
    expect($pdf8->headers->get('content-type'))->toContain('application/pdf');

    $pdf9 = $this->actingAs($this->user)->post(route('quality.pdf9'), [
        'fecha'              => now()->toDateString(),
        'customer_id'        => $this->customer->customer_id,
        'fecha_incidencia'   => now()->toDateString(),
        'producto_servicio'  => 'Servicio Calidad',
        'descripcion'        => 'Descripción carta garantía',
        'impacto'            => 'Impacto mínimo',
        'importancia'        => 'Media',
        'resolucion'         => 'Resolución acordada',
        'medidas'            => 'Medidas preventivas',
        'responsable_accion' => 'QFB Supervisor',
        'accion_final'       => 'Cierre de reporte',
    ]);
    $pdf9->assertStatus(200);
    expect($pdf9->headers->get('content-type'))->toContain('application/pdf');

    $pdf10 = $this->actingAs($this->user)->post(route('quality.pdf10'), [
        'customer_id'      => $this->customer->customer_id,
        'fecha_inspeccion' => now()->toDateString(),
        'items'            => [
            [
                'product_id' => $this->product->product_id,
                'lote'       => 'LOTE-SAL-01',
                'cantidad'   => 10,
            ]
        ],
    ]);
    $pdf10->assertStatus(200);
    expect($pdf10->headers->get('content-type'))->toContain('application/pdf');

    $pdf11 = $this->actingAs($this->user)->post(route('quality.pdf11'), [
        'fecha'       => now()->toDateString(),
        'supplier_id' => $this->supplier->supplier_id,
        'motivo'      => 'Desviación en entrega',
    ]);
    $pdf11->assertStatus(200);
    expect($pdf11->headers->get('content-type'))->toContain('application/pdf');

    $pdf12 = $this->actingAs($this->user)->post(route('quality.pdf12'), [
        'fecha'           => now()->toDateString(),
        'tipo'            => 'queja',
        'nombre'          => 'Usuario Queja',
        'persona'         => 'cliente',
        'descripcion'     => 'Descripción de queja',
        'respuesta_email' => 'no',
    ]);
    $pdf12->assertStatus(200);
    expect($pdf12->headers->get('content-type'))->toContain('application/pdf');
});
