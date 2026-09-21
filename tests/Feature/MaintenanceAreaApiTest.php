<?php

use App\Models\Area;
use App\Models\Equipment;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('1. API GET /maintenance/areas (Index)', function () {
    $response = $this->actingAs($this->user)->getJson('/maintenance/areas');

    echo "\n\n>>> LLAMADA: GET /maintenance/areas\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
});

test('2. API POST /maintenance/areas (Creación exitosa)', function () {
    $payload = [
        'name' => 'Calidad y Metrología ' . uniqid(),
        'description' => 'Área de calibración e inspección técnica',
    ];

    $response = $this->actingAs($this->user)->postJson('/maintenance/areas', $payload);

    echo "\n\n>>> LLAMADA: POST /maintenance/areas\n";
    echo "PAYLOAD: " . json_encode($payload, JSON_UNESCAPED_UNICODE) . "\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
});

test('3. API POST /maintenance/areas (Error de validación por duplicado o vacío)', function () {
    $payload = [
        'name' => '', // nombre vacío inválido
    ];

    $response = $this->actingAs($this->user)->postJson('/maintenance/areas', $payload);

    echo "\n\n>>> LLAMADA: POST /maintenance/areas (Validación fallida)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
});

test('4. API GET /maintenance/areas/{id} (Consulta por ID existente)', function () {
    $area = Area::first();

    $response = $this->actingAs($this->user)->getJson("/maintenance/areas/{$area->id}");

    echo "\n\n>>> LLAMADA: GET /maintenance/areas/{$area->id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
});

test('5. API GET /maintenance/areas/{id} (Consulta ID inexistente - 404)', function () {
    $response = $this->actingAs($this->user)->getJson('/maintenance/areas/9999999');

    echo "\n\n>>> LLAMADA: GET /maintenance/areas/9999999\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('6. API PUT /maintenance/areas/{id} (Actualización exitosa)', function () {
    $area = Area::create([
        'name' => 'Área Temporal ' . uniqid(),
        'description' => 'Original',
    ]);

    $updatePayload = [
        'name' => 'Área Actualizada ' . uniqid(),
        'description' => 'Descripción actualizada mediante PUT',
    ];

    $response = $this->actingAs($this->user)->putJson("/maintenance/areas/{$area->id}", $updatePayload);

    echo "\n\n>>> LLAMADA: PUT /maintenance/areas/{$area->id}\n";
    echo "PAYLOAD: " . json_encode($updatePayload, JSON_UNESCAPED_UNICODE) . "\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
});

test('7. API DELETE /maintenance/areas/{id} (Eliminación protegida por tener equipos)', function () {
    $area = Area::create([
        'name' => 'Área Con Equipos ' . uniqid(),
        'description' => 'Área con equipos vinculados',
    ]);

    Equipment::create([
        'name' => 'Fresadora CNC ' . uniqid(),
        'code' => 'EQ-' . rand(1000, 9999),
        'area_id' => $area->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/maintenance/areas/{$area->id}");

    echo "\n\n>>> LLAMADA: DELETE /maintenance/areas/{$area->id} (Con equipos)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(422);
});

test('8. API DELETE /maintenance/areas/{id} (Eliminación exitosa sin equipos)', function () {
    $area = Area::create([
        'name' => 'Área Para Borrar ' . uniqid(),
        'description' => 'Sin equipos',
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/maintenance/areas/{$area->id}");

    echo "\n\n>>> LLAMADA: DELETE /maintenance/areas/{$area->id} (Sin equipos)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
});

test('9. API POST /maintenance/areas sanitizes HTML tags to prevent Stored XSS', function () {
    $payload = [
        'name' => '<b>Área Sanitizada</b> ' . uniqid(),
        'description' => '<script>alert("XSS")</script>Descripción limpia sin tags HTML',
    ];

    $response = $this->actingAs($this->user)->postJson('/maintenance/areas', $payload);

    $response->assertStatus(201);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.name'))->not->toContain('<b>');
    expect($response->json('data.name'))->not->toContain('</b>');
    expect($response->json('data.description'))->not->toContain('<script>');
    expect($response->json('data.description'))->not->toContain('</script>');
});
