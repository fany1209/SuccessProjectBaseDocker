<?php

use App\Models\TransportLine;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('1. puede listar líneas de transporte vía getTransportLines (GET /getTransportLines)', function () {
    $line = TransportLine::create([
        'name' => 'Transportes ' . uniqid(),
    ]);

    $response = $this->actingAs($this->user)->getJson(route('transportLine.getTransportLines'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Líneas de transporte obtenidas correctamente.',
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'transport_line_id',
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
            'transport_lines' => [
                '*' => [
                    'transport_line_id',
                    'name',
                ],
            ],
        ]);

    $line->delete();
});

test('2. puede listar líneas de transporte vía index (GET /transportLine)', function () {
    $response = $this->actingAs($this->user)->getJson('/transportLine');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
        ]);
});

test('3. puede registrar una línea de transporte (POST /transportLine)', function () {
    $uniqueName = 'Línea Logística ' . uniqid();
    $payload = [
        'name' => $uniqueName,
    ];

    $response = $this->actingAs($this->user)->postJson('/transportLine', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'name' => $uniqueName,
            ],
        ]);

    $this->assertDatabaseHas('transport_lines', [
        'name' => $uniqueName,
    ]);
});

test('4. sanitiza entrada contra Stored-XSS al registrar línea de transporte', function () {
    $unique = uniqid();
    $payload = [
        'name' => '<script>alert("xss")</script>Trans ' . $unique,
    ];

    $response = $this->actingAs($this->user)->postJson('/transportLine', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('transport_lines', [
        'name' => 'alert("xss")Trans ' . $unique,
    ]);
});

test('5. valida campo requerido name y retorna 422 si está vacío', function () {
    $response = $this->actingAs($this->user)->postJson('/transportLine', [
        'name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('6. puede consultar una línea de transporte para edición modal (GET /transportLine/{id})', function () {
    $line = TransportLine::create([
        'name' => 'Transportes Express ' . uniqid(),
    ]);

    $response = $this->actingAs($this->user)->getJson("/transportLine/{$line->transport_line_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'data'    => [
                'transport_line_id' => $line->transport_line_id,
                'name'              => $line->name,
            ],
            'transport_line' => [
                'transport_line_id' => $line->transport_line_id,
                'name'              => $line->name,
            ],
        ]);

    $line->delete();
});

test('7. retorna 404 al consultar línea de transporte inexistente (GET /transportLine/{id})', function () {
    $response = $this->actingAs($this->user)->getJson('/transportLine/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Línea de transporte no encontrada.',
        ]);
});

test('8. puede actualizar una línea de transporte (PUT /transportLine/{id})', function () {
    $line = TransportLine::create([
        'name' => 'Transportes Inicial ' . uniqid(),
    ]);

    $updatedName = 'Transportes Actualizado ' . uniqid();
    $payload = [
        'name' => $updatedName,
    ];

    $response = $this->actingAs($this->user)->putJson("/transportLine/{$line->transport_line_id}", $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'transport_line_id' => $line->transport_line_id,
                'name'              => $updatedName,
            ],
        ]);

    $this->assertDatabaseHas('transport_lines', [
        'transport_line_id' => $line->transport_line_id,
        'name'              => $updatedName,
    ]);

    $line->delete();
});

test('9. retorna 404 al intentar actualizar línea de transporte inexistente (PUT /transportLine/{id})', function () {
    $response = $this->actingAs($this->user)->putJson('/transportLine/99999999', [
        'name' => 'Fantasma',
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Línea de transporte no encontrada.',
        ]);
});

test('10. puede eliminar una línea de transporte (DELETE /transportLine/{id})', function () {
    $line = TransportLine::create([
        'name' => 'Línea Borrable ' . uniqid(),
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/transportLine/{$line->transport_line_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Transport line deleted',
        ]);

    $this->assertDatabaseMissing('transport_lines', [
        'transport_line_id' => $line->transport_line_id,
    ]);
});

test('11. retorna 404 al intentar eliminar línea de transporte inexistente (DELETE /transportLine/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson('/transportLine/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Transport line not deleted',
        ]);
});
