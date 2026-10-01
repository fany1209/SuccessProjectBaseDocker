<?php

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ReceptionOfSample;
use App\Models\Supplier;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    $this->unauthorizedUser = User::factory()->create();

    Permission::findOrCreate('laboratory.show', 'web');
    $this->user->givePermissionTo('laboratory.show');

    $this->product = Product::first() ?? Product::create([
        'name'        => 'Materia Prima Test ' . uniqid(),
        'sku'         => 'MP-' . rand(1000, 9999),
        'sat_code'    => '01010101',
        'category_id' => 1,
    ]);

    $this->supplier = Supplier::first() ?? Supplier::create([
        'name'          => 'Proveedor Test ' . uniqid(),
        'supplier_code' => 'SP-' . rand(1000, 9999),
    ]);

    $this->inventory = Inventory::create([
        'product_id' => $this->product->product_id,
        'batch'      => 'BATCH-LAB-' . uniqid(),
        'stock'      => 100.0,
    ]);

    $this->reception = ReceptionOfSample::create([
        'folio_muestra'    => 'RM-TEST-' . uniqid(),
        'product_id'       => $this->product->product_id,
        'supplier_id'      => $this->supplier->supplier_id,
        'sku'              => $this->product->sku,
        'batch'            => $this->inventory->batch,
        'nombre_comercial' => 'Muestra Comercial Test',
        'fecha_entrada'    => now()->toDateString(),
        'fecha_caducidad'  => now()->addMonths(6)->toDateString(),
        'descripcion'      => 'Muestra de control de calidad',
        'origen_muestra'   => 'proveedor',
        'objetivo_muestra' => 'inspeccion, analisis',
        'cantidad'         => 5.0,
        'um'               => 'kg',
        'docs_ccf'         => true,
        'docs_ft'          => true,
        'estatus'          => 0,
    ]);
});

afterEach(function () {
    if (isset($this->reception)) {
        ReceptionOfSample::where('id', $this->reception->id)->delete();
    }
    if (isset($this->inventory)) {
        $this->inventory->delete();
    }
    if (isset($this->unauthorizedUser)) {
        $this->unauthorizedUser->delete();
    }
});

test('1. retorna 403 al acceder a recepción sin permiso laboratory.show', function () {
    $response = $this->actingAs($this->unauthorizedUser)->getJson('/reception');

    $response->assertStatus(403);
});

test('2. puede listar las recepciones en formato JSON para DataTables (GET /reception)', function () {
    $response = $this->actingAs($this->user)->getJson('/reception');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'product',
                    'batch',
                    'entry_at',
                    'estatus',
                    'status',
                ],
            ],
        ]);
});

test('3. puede obtener los lotes de inventario asociados a un producto (GET /reception/batches)', function () {
    $response = $this->actingAs($this->user)->getJson('/reception/batches?product_id=' . $this->product->product_id);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ])
        ->assertJsonFragment([$this->inventory->batch]);
});

test('4. puede consultar el detalle de una recepción existente (GET /reception/{id})', function () {
    $response = $this->actingAs($this->user)->getJson('/reception/' . $this->reception->id);

    $response->assertStatus(200)
        ->assertJson([
            'success'   => true,
            'flag'      => true,
            'recepcion' => [
                'id'            => $this->reception->id,
                'folio_muestra' => $this->reception->folio_muestra,
                'product_id'    => $this->product->product_id,
            ],
        ]);
});

test('5. puede registrar una nueva recepción con producto existente (POST /laboratory/reception/store)', function () {
    $payload = [
        'folio_muestra'    => 'RM-NEW-' . uniqid(),
        'product_id'       => $this->product->product_id,
        'supplier_id'      => $this->supplier->supplier_id,
        'batch'            => 'LOTE-NUEVO-1',
        'fecha_entrada'    => now()->toDateString(),
        'cantidad'         => 12.5,
        'um'               => 'kg',
        'origen_muestra'   => 'proveedor',
        'objetivo_muestra' => ['inspeccion'],
    ];

    $response = $this->actingAs($this->user)->postJson('/laboratory/reception/store', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'message' => 'Guardado correctamente',
        ]);

    $newId = $response->json('id');
    $this->assertDatabaseHas('reception_of_samples', [
        'id'            => $newId,
        'product_id'    => $this->product->product_id,
        'folio_muestra' => $payload['folio_muestra'],
    ]);

    ReceptionOfSample::where('id', $newId)->delete();
});

test('6. puede registrar recepción creando automáticamente producto y proveedor por nombre (POST /laboratory/reception/store)', function () {
    $uniqueName = 'Insumo Químico ' . uniqid();
    $supplierName = 'Química Industrial ' . uniqid();

    $payload = [
        'producto'         => $uniqueName,
        'proveedor'        => $supplierName,
        'batch'            => 'LOTE-AUTO-1',
        'fecha_entrada'    => now()->toDateString(),
        'cantidad'         => 2.0,
        'um'               => 'l',
        'origen_muestra'   => 'almacen',
        'objetivo_muestra' => ['analisis'],
    ];

    $response = $this->actingAs($this->user)->postJson('/laboratory/reception/store', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'message' => 'Guardado correctamente',
        ]);

    $this->assertDatabaseHas('products', ['name' => $uniqueName]);
    $this->assertDatabaseHas('suppliers', ['name' => $supplierName]);

    $newId = $response->json('id');
    ReceptionOfSample::where('id', $newId)->delete();
    Product::where('name', $uniqueName)->delete();
    Supplier::where('name', $supplierName)->delete();
});

test('7. puede actualizar una recepción de muestra (PATCH /pdf1/{id})', function () {
    $payload = [
        'descripcion' => 'Descripción actualizada de la muestra',
        'cantidad'    => 8.0,
        'um'          => 'kg',
    ];

    $response = $this->actingAs($this->user)->patchJson('/pdf1/' . $this->reception->id, $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'message' => 'Cambios guardados correctamente',
        ]);

    $this->assertDatabaseHas('reception_of_samples', [
        'id'          => $this->reception->id,
        'descripcion' => 'Descripción actualizada de la muestra',
        'cantidad'    => 8.0,
    ]);
});

test('8. puede actualizar los datos de calidad y marcar como terminado (PATCH /reception/{id}/quality)', function () {
    $payload = [
        'observaciones_laboratorio' => 'Muestra aprobada sin observaciones',
        'docs_ccf'                  => true,
    ];

    $response = $this->actingAs($this->user)->patchJson('/reception/' . $this->reception->id . '/quality', $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'message' => 'Calidad actualizada correctamente',
        ]);

    $this->assertDatabaseHas('reception_of_samples', [
        'id'      => $this->reception->id,
        'estatus' => 1,
    ]);
});

test('9. puede descargar el PDF de recepción de muestra (GET /laboratory/reception/{id}/pdf)', function () {
    $response = $this->actingAs($this->user)->get('/laboratory/reception/' . $this->reception->id . '/pdf');

    $response->assertStatus(200)
        ->assertHeader('content-type', 'application/pdf');
});

test('10. puede eliminar una recepción de muestra (DELETE /laboratory/reception/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson('/laboratory/reception/' . $this->reception->id);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'message' => 'Eliminado correctamente.',
        ]);

    $this->assertDatabaseMissing('reception_of_samples', [
        'id' => $this->reception->id,
    ]);
});

test('11. retorna 404 al consultar una recepción inexistente (GET /reception/999999)', function () {
    $response = $this->actingAs($this->user)->getJson('/reception/999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
        ]);
});

test('12. consulta muestras con calidad pendiente para usuarios con rol Quality', function () {
    Role::findOrCreate('Quality', 'web');
    $this->user->assignRole('Quality');

    $response = $this->actingAs($this->user)->getJson('/quality/check-pending-quality');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ])
        ->assertJsonStructure([
            'pending' => [
                '*' => ['id', 'folio_muestra'],
            ],
        ]);
});
