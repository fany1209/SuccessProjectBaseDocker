<?php

use App\Models\Operator;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('1. puede listar operadores vía getOperators (GET /getOperators)', function () {
    $operator = Operator::create([
        'name'    => 'Juan Pérez ' . uniqid(),
        'license' => 'LIC-' . rand(1000, 9999),
    ]);

    $response = $this->actingAs($this->user)->getJson(route('operator.getOperators'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Operadores obtenidos correctamente.',
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'operator_id',
                    'id',
                    'name',
                    'license',
                    'created_at',
                    'updated_at',
                ],
            ],
            'operators' => [
                '*' => [
                    'operator_id',
                    'name',
                    'license',
                ],
            ],
        ]);

    $operator->delete();
});

test('2. puede listar operadores vía index (GET /operator)', function () {
    $response = $this->actingAs($this->user)->getJson('/operator');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
        ]);
});

test('3. puede registrar un operador (POST /operator)', function () {
    $uniqueName = 'Operador ' . uniqid();
    $uniqueLicense = 'LIC-' . rand(10000, 99999);
    $payload = [
        'name'    => $uniqueName,
        'license' => $uniqueLicense,
    ];

    $response = $this->actingAs($this->user)->postJson('/operator', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'name'    => $uniqueName,
                'license' => $uniqueLicense,
            ],
        ]);

    $this->assertDatabaseHas('operators', [
        'name'    => $uniqueName,
        'license' => $uniqueLicense,
    ]);
});

test('4. sanitiza entrada contra Stored-XSS al registrar operador', function () {
    $unique = uniqid();
    $payload = [
        'name'    => '<script>alert("xss")</script>Carlos ' . $unique,
        'license' => '<b>LIC-' . rand(100, 999) . '</b>',
    ];

    $response = $this->actingAs($this->user)->postJson('/operator', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('operators', [
        'name' => 'alert("xss")Carlos ' . $unique,
    ]);
});

test('5. valida campos requeridos name y license y retorna 422 si faltan', function () {
    $response = $this->actingAs($this->user)->postJson('/operator', [
        'name'    => '',
        'license' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'license']);
});

test('6. puede consultar un operador para edición modal (GET /operator/{id})', function () {
    $operator = Operator::create([
        'name'    => 'Operador Consulta ' . uniqid(),
        'license' => 'LIC-7777',
    ]);

    $response = $this->actingAs($this->user)->getJson("/operator/{$operator->operator_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'data'    => [
                'operator_id' => $operator->operator_id,
                'name'        => $operator->name,
                'license'     => 'LIC-7777',
            ],
            'operator' => [
                'operator_id' => $operator->operator_id,
                'name'        => $operator->name,
                'license'     => 'LIC-7777',
            ],
        ]);

    $operator->delete();
});

test('7. retorna 404 al consultar operador inexistente (GET /operator/{id})', function () {
    $response = $this->actingAs($this->user)->getJson('/operator/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Operador no encontrado.',
        ]);
});

test('8. puede actualizar un operador (PUT /operator/{id})', function () {
    $operator = Operator::create([
        'name'    => 'Operador Inicial ' . uniqid(),
        'license' => 'LIC-1111',
    ]);

    $updatedName = 'Operador Actualizado ' . uniqid();
    $payload = [
        'name'    => $updatedName,
        'license' => 'LIC-9999',
    ];

    $response = $this->actingAs($this->user)->putJson("/operator/{$operator->operator_id}", $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'operator_id' => $operator->operator_id,
                'name'        => $updatedName,
                'license'     => 'LIC-9999',
            ],
        ]);

    $this->assertDatabaseHas('operators', [
        'operator_id' => $operator->operator_id,
        'name'        => $updatedName,
        'license'     => 'LIC-9999',
    ]);

    $operator->delete();
});

test('9. retorna 404 al intentar actualizar operador inexistente (PUT /operator/{id})', function () {
    $response = $this->actingAs($this->user)->putJson('/operator/99999999', [
        'name'    => 'Fantasma',
        'license' => 'LIC-0000',
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Operador no encontrado.',
        ]);
});

test('10. puede eliminar un operador (DELETE /operator/{id})', function () {
    $operator = Operator::create([
        'name'    => 'Operador Borrable ' . uniqid(),
        'license' => 'LIC-DEL',
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/operator/{$operator->operator_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Operator deleted',
        ]);

    $this->assertDatabaseMissing('operators', [
        'operator_id' => $operator->operator_id,
    ]);
});

test('11. retorna 404 al intentar eliminar operador inexistente (DELETE /operator/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson('/operator/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Operator not deleted',
        ]);
});
