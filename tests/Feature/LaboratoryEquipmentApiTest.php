<?php

use App\Models\LaboratoryEquipment;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->equipment = LaboratoryEquipment::create([
        'internal_code' => 'LAB-EQ-001',
        'name'          => 'Microscopio Óptico Binocular',
        'quantity'      => 3,
        'brand'         => 'Olympus',
        'status'        => 'funcional',
    ]);
});

test('1. API GET /laboratory/equipments (JSON - Lista de equipos para DataTables)', function () {
    $response = $this->actingAs($this->user)->getJson('/laboratory/equipments');

    echo "\n\n>>> LLAMADA: GET /laboratory/equipments (JSON)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect(count($response->json('data')))->toBeGreaterThanOrEqual(1);

    $item = collect($response->json('data'))->firstWhere('id', $this->equipment->id);
    expect($item)->not->toBeNull();
    expect($item['internal_code'])->toBe('LAB-EQ-001');
    expect($item['name'])->toBe('Microscopio Óptico Binocular');
});

test('2. Web GET /laboratory/equipments (Retorna vista Blade table_equipments)', function () {
    $response = $this->actingAs($this->user)->get('/laboratory/equipments');

    echo "\n\n>>> LLAMADA: GET /laboratory/equipments (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('laboratory.table_equipments');
});

test('3. API GET /laboratory/equipments/{id}/edit (Consulta de equipo para modal de edición)', function () {
    $response = $this->actingAs($this->user)->getJson("/laboratory/equipments/{$this->equipment->id}/edit");

    echo "\n\n>>> LLAMADA: GET /laboratory/equipments/{id}/edit (JSON)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('id'))->toBe($this->equipment->id);
    expect($response->json('internal_code'))->toBe('LAB-EQ-001');
    expect($response->json('name'))->toBe('Microscopio Óptico Binocular');
    expect($response->json('brand'))->toBe('Olympus');
    expect($response->json('quantity'))->toEqual(3);
    expect($response->json('status'))->toBe('funcional');
    expect($response->json('success'))->toBeTrue();
});

test('4. API GET /laboratory/equipments/{id}/edit (404 al consultar equipo inexistente)', function () {
    $response = $this->actingAs($this->user)->getJson('/laboratory/equipments/999999/edit');

    echo "\n\n>>> LLAMADA: GET /laboratory/equipments/999999/edit (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('5. API POST /laboratory/equipments (Creación exitosa de equipo con sanitización anti-XSS)', function () {
    $payload = [
        'internal_code' => '<b>LAB-EQ-002</b>',
        'name'          => '<i>Centrífuga Digital</i>',
        'brand'         => 'Eppendorf',
        'quantity'      => 2,
        'status'        => 'funcional',
    ];

    $response = $this->actingAs($this->user)->postJson('/laboratory/equipments', $payload);

    echo "\n\n>>> LLAMADA: POST /laboratory/equipments (Creación exitosa y Anti-XSS)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('message'))->toBe('Equipment created successfully.');

    $created = $response->json('data');
    expect($created['internal_code'])->toBe('LAB-EQ-002');
    expect($created['name'])->toBe('Centrífuga Digital');
    expect($created['brand'])->toBe('Eppendorf');
    expect($created['quantity'])->toEqual(2);
    expect($created['status'])->toBe('funcional');

    $this->assertDatabaseHas('laboratory_equipments', [
        'id'            => $created['id'],
        'internal_code' => 'LAB-EQ-002',
        'name'          => 'Centrífuga Digital',
    ]);
});

test('6. API POST /laboratory/equipments (Validación 422 por campo requerido faltante o inválido)', function () {
    $payload = [
        'internal_code' => 'LAB-INVALID',
        'status'        => 'estado_invalido',
    ];

    $response = $this->actingAs($this->user)->postJson('/laboratory/equipments', $payload);

    echo "\n\n>>> LLAMADA: POST /laboratory/equipments (422 Validation Error)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'status']);
});

test('7. API PUT /laboratory/equipments/{id} (Actualización exitosa de equipo)', function () {
    $payload = [
        'internal_code' => 'LAB-EQ-001-MOD',
        'name'          => 'Microscopio Óptico Binocular Avanzado',
        'brand'         => 'Olympus Pro',
        'quantity'      => 4,
        'status'        => 'en reparacion',
    ];

    $response = $this->actingAs($this->user)->putJson("/laboratory/equipments/{$this->equipment->id}", $payload);

    echo "\n\n>>> LLAMADA: PUT /laboratory/equipments/{id} (Actualización exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('message'))->toBe('Record updated successfully.');
    expect($response->json('data.internal_code'))->toBe('LAB-EQ-001-MOD');
    expect($response->json('data.name'))->toBe('Microscopio Óptico Binocular Avanzado');
    expect($response->json('data.status'))->toBe('en reparacion');

    $this->assertDatabaseHas('laboratory_equipments', [
        'id'     => $this->equipment->id,
        'name'   => 'Microscopio Óptico Binocular Avanzado',
        'status' => 'en reparacion',
    ]);
});

test('8. API PUT /laboratory/equipments/{id} (404 al intentar actualizar equipo inexistente)', function () {
    $payload = [
        'name'   => 'Equipo Fantasma',
        'status' => 'funcional',
    ];

    $response = $this->actingAs($this->user)->putJson('/laboratory/equipments/999999', $payload);

    echo "\n\n>>> LLAMADA: PUT /laboratory/equipments/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('9. API DELETE /laboratory/equipments/{id} (Eliminación exitosa de equipo)', function () {
    $response = $this->actingAs($this->user)->deleteJson("/laboratory/equipments/{$this->equipment->id}");

    echo "\n\n>>> LLAMADA: DELETE /laboratory/equipments/{id} (Eliminación exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('message'))->toBe('Record removed.');

    $this->assertDatabaseMissing('laboratory_equipments', [
        'id' => $this->equipment->id,
    ]);
});

test('10. API DELETE /laboratory/equipments/{id} (404 al intentar eliminar equipo inexistente)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/laboratory/equipments/999999');

    echo "\n\n>>> LLAMADA: DELETE /laboratory/equipments/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});
