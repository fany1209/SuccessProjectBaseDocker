<?php

use App\Models\Material;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('1. puede visualizar la vista principal de materiales (GET /laboratory/materials)', function () {
    $response = $this->actingAs($this->user)->get(route('materials.index'));

    $response->assertStatus(200);
    $response->assertViewIs('laboratory.table_materials');
});

test('2. puede listar materiales en formato JSON para DataTables (GET /laboratory/materials)', function () {
    $material = Material::create([
        'name'     => 'Pipetas Pasteur ' . uniqid(),
        'entries'  => 100,
        'exits'    => 10,
        'stock'    => 90,
        'um'       => 'piezas',
        'brand'    => 'Pyrex',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('materials.index'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Materiales obtenidos correctamente.',
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'entries',
                    'exits',
                    'stock',
                    'um',
                    'brand',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $material->delete();
});

test('3. puede registrar un material de laboratorio (POST /laboratory/materials)', function () {
    $payload = [
        'name'    => 'Tubos de Ensayo ' . uniqid(),
        'um'      => 'cajas',
        'brand'   => 'Kimax',
        'entries' => 50,
    ];

    $response = $this->actingAs($this->user)->postJson(route('materials.store'), $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Material created successfully.',
            'data'    => [
                'name'    => $payload['name'],
                'entries' => 50,
                'exits'   => 0,
                'stock'   => 50,
                'um'      => 'cajas',
                'brand'   => 'Kimax',
            ],
        ]);

    $this->assertDatabaseHas('material_lab', [
        'name'  => $payload['name'],
        'stock' => 50,
    ]);
});

test('4. sanitiza entradas contra Stored-XSS al registrar material', function () {
    $uniqueName = uniqid();
    $payload = [
        'name'    => '<script>alert("xss")</script>Vaso Precipitado ' . $uniqueName,
        'um'       => '<b>mL</b>',
        'brand'    => '<i>BrandX</i>',
        'entries'  => 20,
    ];

    $response = $this->actingAs($this->user)->postJson(route('materials.store'), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('material_lab', [
        'name'  => 'alert("xss")Vaso Precipitado ' . $uniqueName,
        'um'    => 'mL',
        'brand' => 'BrandX',
    ]);
});

test('5. valida campos requeridos y retorna 422 si falta el nombre', function () {
    $response = $this->actingAs($this->user)->postJson(route('materials.store'), [
        'name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('6. puede consultar datos para edición modal (GET /laboratory/materials/{id}/edit)', function () {
    $material = Material::create([
        'name'    => 'Matraz Erlenmeyer ' . uniqid(),
        'entries' => 15,
        'exits'   => 5,
        'stock'   => 10,
        'um'      => 'piezas',
        'brand'   => 'Corning',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('materials.edit', $material->id));

    $response->assertStatus(200)
        ->assertJson([
            'id'      => $material->id,
            'name'    => $material->name,
            'entries' => 15,
            'exits'   => 5,
            'stock'   => 10,
            'um'      => 'piezas',
            'brand'   => 'Corning',
            'data'    => [
                'id'      => $material->id,
                'name'    => $material->name,
                'entries' => 15,
                'exits'   => 5,
                'stock'   => 10,
            ],
        ]);

    $material->delete();
});

test('7. retorna 404 al consultar material inexistente para edición (GET /laboratory/materials/{id}/edit)', function () {
    $response = $this->actingAs($this->user)->getJson(route('materials.edit', 99999999));

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Material no encontrado.',
        ]);
});

test('8. puede actualizar un material recalculando stock (PUT /laboratory/materials/{id})', function () {
    $material = Material::create([
        'name'    => 'Guantes de Nitrilo ' . uniqid(),
        'entries' => 30,
        'exits'   => 0,
        'stock'   => 30,
    ]);

    $updatePayload = [
        'name'    => 'Guantes Nitrilo Azul',
        'entries' => 30,
        'exits'   => 12,
        'um'      => 'pares',
        'brand'   => 'Safex',
    ];

    $response = $this->actingAs($this->user)->putJson(route('materials.update', $material->id), $updatePayload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Record updated successfully.',
            'data'    => [
                'id'      => $material->id,
                'name'    => 'Guantes Nitrilo Azul',
                'entries' => 30,
                'exits'   => 12,
                'stock'   => 18,
                'um'      => 'pares',
            ],
        ]);

    $this->assertDatabaseHas('material_lab', [
        'id'    => $material->id,
        'name'  => 'Guantes Nitrilo Azul',
        'exits' => 12,
        'stock' => 18,
    ]);

    $material->delete();
});

test('9. retorna 404 al intentar actualizar material inexistente (PUT /laboratory/materials/{id})', function () {
    $response = $this->actingAs($this->user)->putJson(route('materials.update', 99999999), [
        'name' => 'Fantasma',
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Material no encontrado.',
        ]);
});

test('10. puede eliminar un material (DELETE /laboratory/materials/{id})', function () {
    $material = Material::create([
        'name'    => 'Material Descartable ' . uniqid(),
        'entries' => 1,
        'stock'   => 1,
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('materials.destroy', $material->id));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Record removed.',
            'data'    => [],
        ]);

    $this->assertDatabaseMissing('material_lab', [
        'id' => $material->id,
    ]);
});

test('11. retorna 404 al intentar eliminar material inexistente (DELETE /laboratory/materials/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('materials.destroy', 99999999));

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Material no encontrado.',
        ]);
});
