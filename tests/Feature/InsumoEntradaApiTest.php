<?php

use App\Models\InsumoEntrada;
use App\Models\Sector;
use App\Models\Supplier;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->sector = Sector::first() ?? Sector::create([
        'name' => 'Sector Test ' . uniqid(),
        'code' => 'ST' . rand(100, 999),
    ]);

    $count = Supplier::count();
    $this->supplier = Supplier::first() ?? Supplier::create([
        'name'          => 'Proveedor Test ' . uniqid(),
        'sector_id'     => $this->sector->sector_id,
        'supplier_code' => 'SP' . ($count + 1),
    ]);

    $this->entry = InsumoEntrada::create([
        'fecha_llegada' => now()->toDateString(),
        'categoria'     => 'warehouse',
        'proveedor'     => $this->supplier->name,
        'descripcion'   => 'Insumo de prueba inicial',
        'cantidad'      => 50.5,
        'unidad'        => 'kg',
        'insumo'        => 'Directo',
        'lote'          => 'LOT-INIT-001',
        'costo'         => 1250.00,
        'moneda'        => 'MXN',
    ]);
});

test('1. API GET /insumos_entradas (Listado general de entradas)', function () {
    $response = $this->actingAs($this->user)->getJson('/insumos_entradas');

    echo "\n\n>>> LLAMADA: GET /insumos_entradas\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect(count($response->json('data')))->toBeGreaterThanOrEqual(1);

    $item = collect($response->json('data'))->firstWhere('id', $this->entry->id);
    expect($item)->not->toBeNull();
    expect($item['proveedor'])->toBe($this->supplier->name);
    expect($item['insumo'])->toBe('Directo');
});

test('2. API GET /insumos_entradas/{id} (Consulta de entrada por ID existente)', function () {
    $response = $this->actingAs($this->user)->getJson("/insumos_entradas/{$this->entry->id}");

    echo "\n\n>>> LLAMADA: GET /insumos_entradas/{$this->entry->id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('id'))->toBe($this->entry->id);
    expect($response->json('proveedor'))->toBe($this->supplier->name);
    expect($response->json('entry.id'))->toBe($this->entry->id);
    expect($response->json('data.id'))->toBe($this->entry->id);
});

test('3. API GET /insumos_entradas/{id} (404 al consultar registro inexistente)', function () {
    $response = $this->actingAs($this->user)->getJson('/insumos_entradas/9999999');

    echo "\n\n>>> LLAMADA: GET /insumos_entradas/9999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
    expect($response->json('message'))->toBe('Entrada no encontrada.');
});

test('4. API POST /insumos_entradas (Creación exitosa con proveedor existente)', function () {
    $payload = [
        'fecha_llegada' => now()->toDateString(),
        'categoria'     => 'purchases',
        'supplier_id'   => $this->supplier->supplier_id,
        'descripcion'   => 'Entrada con proveedor del catálogo',
        'cantidad'      => 100,
        'unidad'        => 'litros',
        'insumo'        => 'Indirecto',
        'lote'          => 'LOT-SUP-EXISTING',
    ];

    $response = $this->actingAs($this->user)->postJson('/insumos_entradas', $payload);

    echo "\n\n>>> LLAMADA: POST /insumos_entradas (Proveedor Existente)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.proveedor'))->toBe($this->supplier->name);
    expect($response->json('data.cantidad'))->toEqual(100);

    $this->assertDatabaseHas('insumos_entradas', [
        'id'        => $response->json('data.id'),
        'proveedor' => $this->supplier->name,
        'lote'      => 'LOT-SUP-EXISTING',
    ]);
});

test('5. API POST /insumos_entradas (Creación con nuevo proveedor __other__ y Anti-XSS)', function () {
    $rawSupplierName = '<script>alert("xss")</script>Química Bajío ' . uniqid();
    $expectedCleanName = strip_tags(trim($rawSupplierName));

    $payload = [
        'fecha_llegada' => now()->toDateString(),
        'categoria'     => 'warehouse',
        'supplier_id'   => '__other__',
        'supplier_name' => $rawSupplierName,
        'sector_id'     => $this->sector->sector_id,
        'descripcion'   => '<b>Descripción con HTML</b> notas seguras.',
        'cantidad'      => 25.75,
        'unidad'        => 'pza',
        'insumo'        => '<i>Directo</i>',
        'lote'          => 'LOT-XSS-' . rand(100, 999),
    ];

    $response = $this->actingAs($this->user)->postJson('/insumos_entradas', $payload);

    echo "\n\n>>> LLAMADA: POST /insumos_entradas (Nuevo Proveedor & Anti-XSS)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.proveedor'))->toBe($expectedCleanName);
    expect($response->json('data.proveedor'))->not->toContain('<script>');
    expect($response->json('data.descripcion'))->toBe('Descripción con HTML notas seguras.');
    expect($response->json('data.insumo'))->toBe('Directo');

    $this->assertDatabaseHas('suppliers', [
        'name'      => $expectedCleanName,
        'sector_id' => $this->sector->sector_id,
    ]);

    $this->assertDatabaseHas('insumos_entradas', [
        'id'        => $response->json('data.id'),
        'proveedor' => $expectedCleanName,
    ]);
});

test('6. API POST /insumos_entradas (Validación 422 por campos requeridos faltantes)', function () {
    $payload = [
        'categoria' => 'categoria_invalida',
        'cantidad'  => -10, // Cantidad negativa
    ];

    $response = $this->actingAs($this->user)->postJson('/insumos_entradas', $payload);

    echo "\n\n>>> LLAMADA: POST /insumos_entradas (422 Validation Error)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['fecha_llegada', 'categoria', 'cantidad', 'unidad', 'insumo']);
});

test('7. API PUT /insumos_entradas/{id} (Actualización exitosa y Anti-XSS)', function () {
    $payload = [
        'fecha_llegada' => now()->toDateString(),
        'fecha_salida'  => now()->addDays(5)->toDateString(),
        'categoria'     => 'laboratory',
        'proveedor'     => '<b>Proveedor Actualizado</b> ' . uniqid(),
        'descripcion'   => 'Descripción editada correctamente.',
        'cantidad'      => 75.25,
        'unidad'        => 'kg',
        'insumo'        => 'Directo',
        'lote'          => 'LOT-MODIFIED-001',
        'lote_salida'   => 'LOT-OUT-001',
    ];

    $response = $this->actingAs($this->user)->putJson("/insumos_entradas/{$this->entry->id}", $payload);

    echo "\n\n>>> LLAMADA: PUT /insumos_entradas/{$this->entry->id} (Actualización exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.proveedor'))->not->toContain('<b>');
    expect($response->json('data.categoria'))->toBe('laboratory');
    expect($response->json('data.cantidad'))->toEqual(75.25);
    expect($response->json('data.lote_salida'))->toBe('LOT-OUT-001');

    $this->assertDatabaseHas('insumos_entradas', [
        'id'          => $this->entry->id,
        'categoria'   => 'laboratory',
        'lote_salida' => 'LOT-OUT-001',
    ]);
});

test('8. API PUT /insumos_entradas/{id} (404 al intentar actualizar entrada inexistente)', function () {
    $payload = [
        'fecha_llegada' => now()->toDateString(),
        'categoria'     => 'warehouse',
        'proveedor'     => 'Proveedor Inexistente',
        'cantidad'      => 10,
        'unidad'        => 'kg',
        'insumo'        => 'Directo',
    ];

    $response = $this->actingAs($this->user)->putJson('/insumos_entradas/9999999', $payload);

    echo "\n\n>>> LLAMADA: PUT /insumos_entradas/9999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('9. API DELETE /insumos_entradas/{id} (Eliminación exitosa de entrada)', function () {
    $toDelete = InsumoEntrada::create([
        'fecha_llegada' => now()->toDateString(),
        'categoria'     => 'warehouse',
        'proveedor'     => 'Proveedor Temporal',
        'cantidad'      => 10,
        'unidad'        => 'kg',
        'insumo'        => 'Directo',
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/insumos_entradas/{$toDelete->id}");

    echo "\n\n>>> LLAMADA: DELETE /insumos_entradas/{$toDelete->id} (Eliminación exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('message'))->toBe('Entrada eliminada correctamente.');

    $this->assertDatabaseMissing('insumos_entradas', [
        'id' => $toDelete->id,
    ]);
});

test('10. API DELETE /insumos_entradas/{id} (404 al intentar eliminar registro inexistente)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/insumos_entradas/9999999');

    echo "\n\n>>> LLAMADA: DELETE /insumos_entradas/9999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});
