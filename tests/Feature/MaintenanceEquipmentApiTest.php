<?php

use App\Models\Area;
use App\Models\Equipment;
use App\Models\MaintenancePlan;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    $this->area = Area::first() ?? Area::create([
        'name' => 'Área General ' . uniqid(),
        'description' => 'Área base para pruebas',
    ]);
});

test('1. API GET /maintenance/equipment (Listado general)', function () {
    $response = $this->actingAs($this->user)->getJson('/maintenance/equipment');

    echo "\n\n>>> LLAMADA: GET /maintenance/equipment\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
});

test('2. API POST /maintenance/equipment (Creación exitosa)', function () {
    $payload = [
        'code'      => 'EQ-TEST-' . rand(10000, 99999),
        'name'      => 'Compresor de Alta Presión ' . uniqid(),
        'area_id'   => $this->area->id,
        'is_active' => true,
    ];

    $response = $this->actingAs($this->user)->postJson('/maintenance/equipment', $payload);

    echo "\n\n>>> LLAMADA: POST /maintenance/equipment\n";
    echo "PAYLOAD: " . json_encode($payload, JSON_UNESCAPED_UNICODE) . "\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.code'))->toBe($payload['code']);
});

test('3. API POST /maintenance/equipment (Fallo de validación por código duplicado o campo vacío)', function () {
    $existing = Equipment::first() ?? Equipment::create([
        'code' => 'EQ-DUP-1',
        'name' => 'Equipo Duplicado',
        'area_id' => $this->area->id,
        'is_active' => true,
    ]);

    $payload = [
        'code'    => $existing->code, // Código repetido
        'name'    => '',              // Nombre vacío
        'area_id' => 9999999,         // Área inexistente
    ];

    $response = $this->actingAs($this->user)->postJson('/maintenance/equipment', $payload);

    echo "\n\n>>> LLAMADA: POST /maintenance/equipment (Validación fallida)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('errors'))->toHaveKeys(['code', 'name', 'area_id']);
});

test('4. API POST /maintenance/equipment (Sanitización anti Stored-XSS)', function () {
    $payload = [
        'code'      => '<script>alert("xss")</script>EQ-XSS-' . rand(1000, 9999),
        'name'      => '<b>Torno CNC Sanitizado</b> ' . uniqid(),
        'area_id'   => $this->area->id,
        'is_active' => true,
    ];

    $response = $this->actingAs($this->user)->postJson('/maintenance/equipment', $payload);

    $response->assertStatus(201);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.code'))->not->toContain('<script>');
    expect($response->json('data.name'))->not->toContain('<b>');
});

test('5. API GET /maintenance/equipment/{id} (Consulta por ID existente)', function () {
    $equipment = Equipment::first() ?? Equipment::create([
        'code' => 'EQ-SHOW-1',
        'name' => 'Equipo de Muestra',
        'area_id' => $this->area->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->getJson("/maintenance/equipment/{$equipment->id}");

    echo "\n\n>>> LLAMADA: GET /maintenance/equipment/{$equipment->id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.id'))->toBe($equipment->id);
});

test('6. API GET /maintenance/equipment/{id} (Consulta ID inexistente - 404)', function () {
    $response = $this->actingAs($this->user)->getJson('/maintenance/equipment/9999999');

    echo "\n\n>>> LLAMADA: GET /maintenance/equipment/9999999\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('7. API PUT /maintenance/equipment/{id} (Actualización exitosa)', function () {
    $equipment = Equipment::create([
        'code' => 'EQ-UPDATE-' . rand(1000, 9999),
        'name' => 'Equipo Original',
        'area_id' => $this->area->id,
        'is_active' => true,
    ]);

    $updatePayload = [
        'code'      => $equipment->code,
        'name'      => 'Equipo Renovado ' . uniqid(),
        'area_id'   => $this->area->id,
        'is_active' => false,
    ];

    $response = $this->actingAs($this->user)->putJson("/maintenance/equipment/{$equipment->id}", $updatePayload);

    echo "\n\n>>> LLAMADA: PUT /maintenance/equipment/{$equipment->id}\n";
    echo "PAYLOAD: " . json_encode($updatePayload, JSON_UNESCAPED_UNICODE) . "\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.name'))->toBe($updatePayload['name']);
    expect($response->json('data.is_active'))->toBeFalse();
});

test('8. API DELETE /maintenance/equipment/{id} (Eliminación exitosa sin planes ni registros)', function () {
    $equipment = Equipment::create([
        'code' => 'EQ-DEL-' . rand(1000, 9999),
        'name' => 'Equipo Para Eliminar',
        'area_id' => $this->area->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/maintenance/equipment/{$equipment->id}");

    echo "\n\n>>> LLAMADA: DELETE /maintenance/equipment/{$equipment->id} (Sin dependencias)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect(Equipment::find($equipment->id))->toBeNull();
});

test('9. API DELETE /maintenance/equipment/{id} (Bloqueo por tener planes de mantenimiento asociados)', function () {
    $equipment = Equipment::create([
        'code' => 'EQ-WITHPLAN-' . rand(1000, 9999),
        'name' => 'Equipo Con Plan Asignado',
        'area_id' => $this->area->id,
        'is_active' => true,
    ]);

    MaintenancePlan::create([
        'equipment_id'   => $equipment->id,
        'name'           => 'Plan Preventivo Trimestral',
        'frequency_days' => 90,
        'type'           => 'frequent',
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/maintenance/equipment/{$equipment->id}");

    echo "\n\n>>> LLAMADA: DELETE /maintenance/equipment/{$equipment->id} (Con planes)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(422);
    expect($response->json('message'))->toContain('planes de mantenimiento asociados');
});
