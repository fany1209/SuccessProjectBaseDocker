<?php

use App\Models\Cli;
use App\Models\Concept;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('1. API GET /concepts (Listado de conceptos en JSON)', function () {
    Concept::create(['name' => 'Concepto Test A ' . uniqid()]);
    Concept::create(['name' => 'Concepto Test B ' . uniqid()]);

    $response = $this->actingAs($this->user)->getJson('/concepts');

    echo "\n\n>>> LLAMADA: GET /concepts\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect(count($response->json('data')))->toBeGreaterThanOrEqual(2);
});

test('2. API POST /concepts (Creación exitosa de un concepto)', function () {
    $name = 'Nuevo Concepto ' . uniqid();
    $payload = [
        'name' => $name,
    ];

    $response = $this->actingAs($this->user)->postJson('/concepts', $payload);

    echo "\n\n>>> LLAMADA: POST /concepts\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.name'))->toBe($name);
    expect($response->json('data.concept_id'))->toBeInt();

    $this->assertDatabaseHas('concepts', [
        'name' => $name,
    ]);
});

test('3. API POST /concepts (Validación de nombre obligatorio y único)', function () {
    $existing = Concept::create(['name' => 'Concepto Duplicado ' . uniqid()]);

    $response = $this->actingAs($this->user)->postJson('/concepts', [
        'name' => $existing->name,
    ]);

    echo "\n\n>>> LLAMADA: POST /concepts con nombre duplicado\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('errors.name'))->toBeArray();
});

test('4. API GET /concepts/{id} (Obtención de un concepto específico)', function () {
    $concept = Concept::create(['name' => 'Concepto Detalle ' . uniqid()]);

    $response = $this->actingAs($this->user)->getJson("/concepts/{$concept->concept_id}");

    echo "\n\n>>> LLAMADA: GET /concepts/{$concept->concept_id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.concept_id'))->toBe($concept->concept_id);
    expect($response->json('data.name'))->toBe($concept->name);
});

test('5. API GET /concepts/{id} (Retorna 404 para concepto inexistente)', function () {
    $response = $this->actingAs($this->user)->getJson('/concepts/999999');

    echo "\n\n>>> LLAMADA: GET /concepts/999999\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
    expect($response->json('message'))->toBe('Concepto no encontrado.');
});

test('6. API PUT /concepts/{id} (Actualización exitosa de un concepto)', function () {
    $concept = Concept::create(['name' => 'Nombre Antiguo ' . uniqid()]);
    $newName = 'Nombre Modificado ' . uniqid();

    $response = $this->actingAs($this->user)->putJson("/concepts/{$concept->concept_id}", [
        'name' => $newName,
    ]);

    echo "\n\n>>> LLAMADA: PUT /concepts/{$concept->concept_id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.name'))->toBe($newName);

    $this->assertDatabaseHas('concepts', [
        'concept_id' => $concept->concept_id,
        'name' => $newName,
    ]);
});

test('7. API DELETE /concepts/{id} (Eliminación exitosa sin relaciones asociadas)', function () {
    $concept = Concept::create(['name' => 'Concepto Para Borrar ' . uniqid()]);

    $response = $this->actingAs($this->user)->deleteJson("/concepts/{$concept->concept_id}");

    echo "\n\n>>> LLAMADA: DELETE /concepts/{$concept->concept_id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('message'))->toBe('Concepto eliminado exitosamente.');

    $this->assertDatabaseMissing('concepts', [
        'concept_id' => $concept->concept_id,
    ]);
});

test('8. API DELETE /concepts/{id} (Previene eliminación si tiene registros asociados en CLI)', function () {
    $concept = Concept::create(['name' => 'Concepto Con CLI ' . uniqid()]);

    $warehouseId = DB::table('warehouses')->value('warehouse_id') ?? DB::table('warehouses')->insertGetId([
        'name' => 'Almacén Test',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $locationId = DB::table('locations')->value('location_id') ?? DB::table('locations')->insertGetId([
        'warehouse_id' => $warehouseId,
        'name' => 'Ubicación Test',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $productId = DB::table('products')->value('product_id') ?? DB::table('products')->insertGetId([
        'name' => 'Producto Test',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $inventoryId = DB::table('inventory')->value('inventory_id') ?? DB::table('inventory')->insertGetId([
        'product_id' => $productId,
        'location_id' => $locationId,
        'quantity' => 10,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Cli::create([
        'location_id' => $locationId,
        'inventory_id' => $inventoryId,
        'concept_id' => $concept->concept_id,
        'quantity' => 5,
        'weight_per_unit' => 10.0,
        'net_weight' => 50.0,
        'bag_number' => 'BAG-' . rand(10000, 99999),
        'protein' => 12.0,
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/concepts/{$concept->concept_id}");

    echo "\n\n>>> LLAMADA: DELETE /concepts/{$concept->concept_id} con registros asociados\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('message'))->toContain('No se puede eliminar el concepto porque tiene registros asociados');

    $this->assertDatabaseHas('concepts', [
        'concept_id' => $concept->concept_id,
    ]);
});
