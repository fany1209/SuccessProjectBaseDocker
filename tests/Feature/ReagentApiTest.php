<?php

use App\Models\Reagent;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('1. puede visualizar la vista principal de reactivos (GET /laboratory/reagents)', function () {
    $response = $this->actingAs($this->user)->get(route('reagents.index'));

    $response->assertStatus(200);
    $response->assertViewIs('laboratory.table_react');
});

test('2. puede listar reactivos en formato JSON para DataTables (GET /laboratory/reagents)', function () {
    $reagent = Reagent::create([
        'code'     => 'REA-' . uniqid(),
        'name'     => 'Ácido Clorhídrico ' . uniqid(),
        'entries'  => 50,
        'exits'    => 10,
        'stock'    => 40,
        'um'       => 'mL',
        'brand'    => 'Merck',
        'color'    => 'Incoloro',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('reagents.index'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Reactivos obtenidos correctamente.',
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'code',
                    'name',
                    'entries',
                    'exits',
                    'stock',
                    'um',
                    'brand',
                    'color',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $reagent->delete();
});

test('3. puede registrar un reactivo de laboratorio (POST /laboratory/reagents)', function () {
    $payload = [
        'code'    => 'REA-' . uniqid(),
        'name'    => 'Hidróxido de Sodio ' . uniqid(),
        'um'      => 'g',
        'brand'   => 'Sigma-Aldrich',
        'color'   => 'Blanco',
        'entries' => 25,
    ];

    $response = $this->actingAs($this->user)->postJson(route('reagents.store'), $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Reagent created successfully.',
            'data'    => [
                'code'    => $payload['code'],
                'name'    => $payload['name'],
                'entries' => 25,
                'exits'   => 0,
                'stock'   => 25,
                'um'      => 'g',
                'brand'   => 'Sigma-Aldrich',
                'color'   => 'Blanco',
            ],
        ]);

    $this->assertDatabaseHas('reagent_inventory', [
        'code'  => $payload['code'],
        'name'  => $payload['name'],
        'stock' => 25,
    ]);
});

test('4. sanitiza entradas contra Stored-XSS al registrar reactivo', function () {
    $uniqueCode = uniqid();
    $uniqueName = uniqid();
    $payload = [
        'code'     => '<b>REA-' . $uniqueCode . '</b>',
        'name'     => '<script>alert("xss")</script>Alcohol Etílico ' . $uniqueName,
        'um'       => '<i>L</i>',
        'brand'    => '<style>body{}</style>J.T. Baker',
        'color'    => '<u>Transparente</u>',
        'entries'  => 10,
    ];

    $response = $this->actingAs($this->user)->postJson(route('reagents.store'), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('reagent_inventory', [
        'code'  => 'REA-' . $uniqueCode,
        'name'  => 'alert("xss")Alcohol Etílico ' . $uniqueName,
        'um'    => 'L',
        'brand' => 'body{}J.T. Baker',
        'color' => 'Transparente',
    ]);
});

test('5. valida campos requeridos y retorna 422 si falta el nombre o el código', function () {
    $response = $this->actingAs($this->user)->postJson(route('reagents.store'), [
        'code' => '',
        'name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['code', 'name']);
});

test('6. puede consultar datos para edición modal (GET /laboratory/reagents/{id}/edit)', function () {
    $reagent = Reagent::create([
        'code'    => 'REA-' . uniqid(),
        'name'    => 'Fenolftaleína ' . uniqid(),
        'entries' => 10,
        'exits'   => 2,
        'stock'   => 8,
        'um'      => 'mL',
        'brand'   => 'Fermont',
        'color'   => 'Rosa',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('reagents.edit', $reagent->id));

    $response->assertStatus(200)
        ->assertJson([
            'id'      => $reagent->id,
            'code'    => $reagent->code,
            'name'    => $reagent->name,
            'entries' => 10,
            'exits'   => 2,
            'stock'   => 8,
            'um'      => 'mL',
            'brand'   => 'Fermont',
            'color'   => 'Rosa',
            'data'    => [
                'id'      => $reagent->id,
                'code'    => $reagent->code,
                'name'    => $reagent->name,
                'entries' => 10,
                'exits'   => 2,
                'stock'   => 8,
            ],
        ]);

    $reagent->delete();
});

test('7. retorna 404 al consultar reactivo inexistente para edición (GET /laboratory/reagents/{id}/edit)', function () {
    $response = $this->actingAs($this->user)->getJson(route('reagents.edit', 99999999));

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Reactivo no encontrado.',
        ]);
});

test('8. puede actualizar un reactivo recalculando stock (PUT /laboratory/reagents/{id})', function () {
    $reagent = Reagent::create([
        'code'    => 'REA-' . uniqid(),
        'name'    => 'Agua Destilada ' . uniqid(),
        'entries' => 100,
        'exits'   => 0,
        'stock'   => 100,
        'um'      => 'L',
    ]);

    $updatePayload = [
        'code'    => $reagent->code,
        'name'    => 'Agua Destilada Grado Reactivo',
        'entries' => 100,
        'exits'   => 35,
        'um'      => 'L',
        'brand'   => 'Milli-Q',
        'color'   => 'Transparente',
    ];

    $response = $this->actingAs($this->user)->putJson(route('reagents.update', $reagent->id), $updatePayload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Record updated successfully.',
            'data'    => [
                'id'      => $reagent->id,
                'name'    => 'Agua Destilada Grado Reactivo',
                'entries' => 100,
                'exits'   => 35,
                'stock'   => 65,
                'um'      => 'L',
                'brand'   => 'Milli-Q',
            ],
        ]);

    $this->assertDatabaseHas('reagent_inventory', [
        'id'    => $reagent->id,
        'name'  => 'Agua Destilada Grado Reactivo',
        'exits' => 35,
        'stock' => 65,
    ]);

    $reagent->delete();
});

test('9. retorna 404 al intentar actualizar reactivo inexistente (PUT /laboratory/reagents/{id})', function () {
    $response = $this->actingAs($this->user)->putJson(route('reagents.update', 99999999), [
        'name' => 'Fantasma',
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Reactivo no encontrado.',
        ]);
});

test('10. puede eliminar un reactivo (DELETE /laboratory/reagents/{id})', function () {
    $reagent = Reagent::create([
        'code'    => 'DEL-' . uniqid(),
        'name'    => 'Reactivo Descartable ' . uniqid(),
        'entries' => 5,
        'stock'   => 5,
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('reagents.destroy', $reagent->id));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Record removed.',
            'data'    => [],
        ]);

    $this->assertDatabaseMissing('reagent_inventory', [
        'id' => $reagent->id,
    ]);
});

test('11. retorna 404 al intentar eliminar reactivo inexistente (DELETE /laboratory/reagents/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('reagents.destroy', 99999999));

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Reactivo no encontrado.',
        ]);
});
