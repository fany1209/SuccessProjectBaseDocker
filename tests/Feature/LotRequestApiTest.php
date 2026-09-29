<?php

use App\Models\LotRequest;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('1. puede visualizar la vista principal de peticiones de lote (GET /lot-requests)', function () {
    $response = $this->actingAs($this->user)->get(route('lot.request.index'));

    $response->assertStatus(200);
    $response->assertViewIs('formats.laboratory.lot_requests.index');
    $response->assertViewHas('pendingLotRequests');
});

test('2. puede listar peticiones de lote en JSON (GET /lot-requests)', function () {
    $lot = LotRequest::create([
        'department'   => 'Calidad',
        'comments'     => 'Comentario inicial',
        'status'       => 'pendiente',
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->getJson(route('lot.request.index'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Peticiones de lote obtenidas correctamente.',
        ])
        ->assertJsonStructure([
            'success',
            'flag',
            'code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'department',
                    'requested_at',
                    'status',
                    'comments',
                    'product',
                    'quantity',
                    'provider',
                    'collector',
                    'sector',
                    'sku',
                    'batch',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $lot->delete();
});

test('3. puede registrar una petición de lote retornando 201 (POST /lot-requests)', function () {
    $payload = [
        'department' => 'Producción',
        'comments'   => 'Solicitud urgente de lote',
        'product'    => 'Levadura Bioyeast',
        'quantity'   => '500 kg',
        'provider'   => 'Proveedor Alfa',
        'collector'  => 'Juan Pérez',
        'sector'     => 'Alimentos',
    ];

    $response = $this->actingAs($this->user)->postJson(route('lot.request.store'), $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Registered request.',
            'data'    => [
                'department' => 'Producción',
                'comments'   => 'Solicitud urgente de lote',
                'product'    => 'Levadura Bioyeast',
                'quantity'   => '500 kg',
                'provider'   => 'Proveedor Alfa',
                'collector'  => 'Juan Pérez',
                'sector'     => 'Alimentos',
                'status'     => 'pendiente',
            ],
        ]);

    $this->assertDatabaseHas('lot_requests', [
        'department' => 'Producción',
        'product'    => 'Levadura Bioyeast',
        'status'     => 'pendiente',
    ]);
});

test('4. sanitiza entradas contra Stored-XSS al registrar petición de lote', function () {
    $payload = [
        'department' => '<script>alert("xss")</script>Sistemas',
        'comments'   => '<b>Texto con formato</b>',
        'product'    => '<i>Producto Limpio</i>',
    ];

    $response = $this->actingAs($this->user)->postJson(route('lot.request.store'), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('lot_requests', [
        'department' => 'alert("xss")Sistemas',
        'comments'   => 'Texto con formato',
        'product'    => 'Producto Limpio',
    ]);
});

test('5. valida campos requeridos y retorna 422 si falta departamento', function () {
    $response = $this->actingAs($this->user)->postJson(route('lot.request.store'), [
        'department' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['department']);
});

test('6. puede obtener datos para DataTables (GET /lot-requests/datatable)', function () {
    $lot = LotRequest::create([
        'department'   => 'I+D',
        'product'      => 'Producto Especial',
        'sku'          => 'SKU-TEST-99',
        'batch'        => 'BATCH-TEST-99',
        'status'       => 'pendiente',
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->getJson(route('lot.request.datatable'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'flag',
            'code',
            'message',
            'data' => [
                'lots',
                'productSkus',
                'productBatches',
            ],
            // Compatibilidad frontend DataTables
            'lots',
            'productSkus',
            'productBatches',
        ]);

    $lot->delete();
});

test('7. puede actualizar el estatus de una petición de lote (PATCH /lot-requests/{id})', function () {
    $lot = LotRequest::create([
        'department'   => 'Finanzas',
        'status'       => 'pendiente',
        'requested_at' => now(),
    ]);

    $updatePayload = [
        'status' => 'terminado',
        'sku'    => 'SKU-FINAL-01',
        'batch'  => 'BATCH-FINAL-01',
    ];

    $response = $this->actingAs($this->user)->patchJson(route('lot.request.update', $lot->id), $updatePayload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Estatus de petición de lote actualizado correctamente.',
            'data'    => [
                'id'     => $lot->id,
                'status' => 'terminado',
                'sku'    => 'SKU-FINAL-01',
                'batch'  => 'BATCH-FINAL-01',
            ],
        ]);

    $this->assertDatabaseHas('lot_requests', [
        'id'     => $lot->id,
        'status' => 'terminado',
        'sku'    => 'SKU-FINAL-01',
        'batch'  => 'BATCH-FINAL-01',
    ]);

    $lot->delete();
});

test('8. retorna 404 al intentar actualizar una petición inexistente (PATCH /lot-requests/{id})', function () {
    $response = $this->actingAs($this->user)->patchJson(route('lot.request.update', 99999999), [
        'status' => 'terminado',
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Petición de lote no encontrada.',
        ]);
});

test('9. puede consultar peticiones pendientes con rol Quality (GET /lot-requests/check)', function () {
    Role::findOrCreate('Quality', 'web');
    $this->user->assignRole('Quality');

    $lot = LotRequest::create([
        'department'   => 'Control Calidad',
        'status'       => 'pendiente',
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->getJson(route('lot.request.check'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Peticiones pendientes obtenidas correctamente.',
        ])
        ->assertJsonStructure([
            'pending',
            'count',
            'data' => [
                'pending',
                'count',
            ],
        ]);

    $lot->delete();
});

test('10. puede contar peticiones completadas (GET /lot-requests/count-completed)', function () {
    $lot = LotRequest::create([
        'department'   => 'Almacén',
        'status'       => 'terminado',
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->getJson(route('lot.request.count_completed'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Conteo de peticiones terminadas obtenido correctamente.',
        ])
        ->assertJsonStructure([
            'count',
            'data' => [
                'count',
            ],
        ]);

    $lot->delete();
});
