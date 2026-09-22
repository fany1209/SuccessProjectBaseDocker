<?php

use App\Models\Comparative;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('1. API GET /comparative (Listado de comparativas en JSON)', function () {
    $folio = 'COMP-TEST-001';
    Comparative::create([
        'folio' => $folio,
        'user_id' => $this->user->id,
        'insumo' => 'Insumo Prueba A',
        'cantidad' => 10,
        'proveedor' => 'Proveedor Alpha',
        'precio_unt' => 50.00,
        'precio_total' => 500.00,
        'descripcion' => 'Descripción prueba',
    ]);

    $response = $this->actingAs($this->user)->getJson('/comparative');

    echo "\n\n>>> LLAMADA: GET /comparative\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect(count($response->json('data')))->toBeGreaterThanOrEqual(1);
});

test('2. API POST /comparative/store (Creación de comparativa con múltiples insumos)', function () {
    $payload = [
        'insumo' => ['Material X', 'Material Y'],
        'cantidad' => [5, 2],
        'proveedor' => ['Distribuidor 1', 'Distribuidor 2'],
        'precio_unt' => [100.50, 250.00],
        'precio_total' => [502.50, 500.00],
        'descripcion' => ['Pieza metálica', 'Herramienta especial'],
        'comentarios' => ['Urgente', 'Normal'],
        'entrega_estimada' => ['2026-10-01', '2026-10-05'],
        'link' => ['https://example.com/x', 'https://example.com/y'],
    ];

    $response = $this->actingAs($this->user)->postJson('/comparative/store', $payload);

    echo "\n\n>>> LLAMADA: POST /comparative/store\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.folio'))->toContain('COMP-');
    expect(count($response->json('data.items')))->toBe(2);
    expect($response->json('data.items.0.insumo'))->toBe('Material X');
    expect($response->json('data.items.1.insumo'))->toBe('Material Y');
});

test('3. API PUT /purchases/comparative/update-all (Actualización masiva y sincronización)', function () {
    $folio = 'COMP-UPDATE-001';
    $item1 = Comparative::create([
        'folio' => $folio,
        'user_id' => $this->user->id,
        'insumo' => 'Original Item 1',
        'cantidad' => 5,
        'proveedor' => 'Prov 1',
        'precio_total' => 1000.00,
        'precio_unt' => 200.00,
    ]);

    $item2 = Comparative::create([
        'folio' => $folio,
        'user_id' => $this->user->id,
        'insumo' => 'Original Item 2 (Eliminar)',
        'cantidad' => 2,
        'proveedor' => 'Prov 2',
        'precio_total' => 400.00,
        'precio_unt' => 200.00,
    ]);

    $payload = [
        'id' => [$item1->id, null],
        'insumo' => ['Item 1 Modificado', 'Item 3 Nuevo'],
        'cantidad' => [10, 4],
        'proveedor' => ['Prov 1 Editado', 'Prov 3'],
        'precio_total' => [1500.00, 800.00],
        'descripcion' => ['Editado', 'Nuevo'],
    ];

    $response = $this->actingAs($this->user)->putJson('/purchases/comparative/update-all', $payload);

    echo "\n\n>>> LLAMADA: PUT /purchases/comparative/update-all\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data.folio'))->toBe($folio);

    $this->assertDatabaseHas('comparative', [
        'id' => $item1->id,
        'insumo' => 'Item 1 Modificado',
        'proveedor' => 'Prov 1 Editado',
    ]);

    $this->assertDatabaseMissing('comparative', [
        'id' => $item2->id,
    ]);

    $this->assertDatabaseHas('comparative', [
        'folio' => $folio,
        'insumo' => 'Item 3 Nuevo',
    ]);
});

test('4. API DELETE /purchases/comparative/{folio} (Eliminación exitosa por folio)', function () {
    $folio = 'COMP-DEL-001';
    Comparative::create([
        'folio' => $folio,
        'user_id' => $this->user->id,
        'insumo' => 'Insumo to delete 1',
        'cantidad' => 1,
    ]);
    Comparative::create([
        'folio' => $folio,
        'user_id' => $this->user->id,
        'insumo' => 'Insumo to delete 2',
        'cantidad' => 2,
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/purchases/comparative/{$folio}");

    echo "\n\n>>> LLAMADA: DELETE /purchases/comparative/{$folio}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);

    $this->assertDatabaseMissing('comparative', [
        'folio' => $folio,
    ]);
});

test('5. API DELETE /purchases/comparative/{folio} (Retorna 404 para folio inexistente)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/purchases/comparative/COMP-NON-EXISTENT');

    echo "\n\n>>> LLAMADA: DELETE /purchases/comparative/COMP-NON-EXISTENT\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
    expect($response->json('message'))->toBe('El folio no existe.');
});

test('6. API POST /comparative/store (Error de validación cuando falta insumo)', function () {
    $payload = [
        'insumo' => [],
    ];

    $response = $this->actingAs($this->user)->postJson('/comparative/store', $payload);

    echo "\n\n>>> LLAMADA: POST /comparative/store con payload inválido\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('message'))->not->toBeEmpty();
    expect($response->json('errors.insumo'))->toBeArray();
});
