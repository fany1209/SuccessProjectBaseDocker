<?php

use App\Models\ItEquipment;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('1. puede visualizar la vista del inventario de TI (GET /sistemas-ti/inventario)', function () {
    $equipment = ItEquipment::create([
        'department'    => 'Sistemas',
        'responsible'   => 'Juan Pérez',
        'article'       => 'Laptop',
        'brand'         => 'Dell',
        'model'         => 'Latitude 5420',
        'serial_number' => 'SN-12345',
        'success_code'  => 'TI-001',
    ]);

    $response = $this->actingAs($this->user)->get(route('sistemas-ti.inventario'));

    $response->assertStatus(200);
    $response->assertViewIs('sistemas-ti.inventario');
    $response->assertViewHas('equipments');
});

test('2. puede listar equipos de TI en JSON (GET /sistemas-ti/inventario)', function () {
    $equipment = ItEquipment::create([
        'department'    => 'Gerencia',
        'responsible'   => 'Ana Gomez',
        'article'       => 'Monitor',
        'brand'         => 'LG',
        'model'         => 'UltraWide 34',
        'serial_number' => 'LG-98765',
        'success_code'  => 'TI-002',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('sistemas-ti.inventario'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Equipos de TI obtenidos correctamente.',
        ])
        ->assertJsonStructure([
            'success',
            'flag',
            'code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'department',
                    'responsible',
                    'article',
                    'brand',
                    'model',
                    'serial_number',
                    'success_code',
                    'image_url',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});

test('3. puede filtrar equipos de TI por busqueda, departamento y articulo', function () {
    $eq1 = ItEquipment::create([
        'department'    => 'Sistemas',
        'responsible'   => 'Carlos Diaz',
        'article'       => 'PC',
        'brand'         => 'HP',
        'model'         => 'ProDesk 600',
        'serial_number' => 'HP-FILTER-1',
    ]);

    $eq2 = ItEquipment::create([
        'department'    => 'RH',
        'responsible'   => 'Laura Ruiz',
        'article'       => 'Laptop',
        'brand'         => 'Lenovo',
        'model'         => 'ThinkPad',
        'serial_number' => 'LN-FILTER-2',
    ]);

    // Filtrar por serial_number
    $resSearch = $this->actingAs($this->user)->getJson(route('sistemas-ti.inventario', ['search' => 'HP-FILTER-1']));
    $resSearch->assertStatus(200);
    $dataSearch = $resSearch->json('data');
    expect(collect($dataSearch)->pluck('serial_number'))->toContain('HP-FILTER-1')
        ->and(collect($dataSearch)->pluck('serial_number'))->not->toContain('LN-FILTER-2');

    // Filtrar por departamento
    $resDept = $this->actingAs($this->user)->getJson(route('sistemas-ti.inventario', ['department' => 'RH']));
    $resDept->assertStatus(200);
    $dataDept = $resDept->json('data');
    expect(collect($dataDept)->pluck('department'))->toContain('RH')
        ->and(collect($dataDept)->pluck('department'))->not->toContain('Sistemas');

    // Filtrar por artículo
    $resArt = $this->actingAs($this->user)->getJson(route('sistemas-ti.inventario', ['article' => 'PC']));
    $resArt->assertStatus(200);
    $dataArt = $resArt->json('data');
    expect(collect($dataArt)->pluck('article'))->toContain('PC')
        ->and(collect($dataArt)->pluck('article'))->not->toContain('Laptop');
});

test('4. puede consultar un equipo de TI específico en JSON (GET /sistemas-ti/inventario/{id})', function () {
    $equipment = ItEquipment::create([
        'department'    => 'Calidad',
        'responsible'   => 'Roberto Solis',
        'article'       => 'Laptop',
        'brand'         => 'Dell',
        'model'         => 'Inspiron 15',
        'serial_number' => 'SN-SHOW-123',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('sistemas-ti.inventario.show', $equipment->id));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Equipo de TI obtenido correctamente.',
            'data'    => [
                'id'            => $equipment->id,
                'department'    => 'Calidad',
                'responsible'   => 'Roberto Solis',
                'article'       => 'Laptop',
                'serial_number' => 'SN-SHOW-123',
            ],
        ]);
});

test('5. retorna 404 al consultar un equipo de TI inexistente (GET /sistemas-ti/inventario/{id})', function () {
    $response = $this->actingAs($this->user)->getJson(route('sistemas-ti.inventario.show', 99999999));

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Equipo de TI no encontrado.',
        ]);
});

test('6. puede registrar equipo en formato JSON retornando 201 (POST /sistemas-ti/inventario)', function () {
    $payload = [
        'department'    => 'Finanzas',
        'responsible'   => 'Marta Sánchez',
        'article'       => 'Laptop',
        'brand'         => 'Apple',
        'model'         => 'MacBook Pro 14',
        'serial_number' => 'SN-APPLE-001',
        'success_code'  => 'TI-FN-01',
        'image_url'     => 'https://example.com/laptop.jpg',
    ];

    $response = $this->actingAs($this->user)->postJson(route('sistemas-ti.inventario.store'), $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Equipo registrado correctamente.',
            'data'    => [
                'department'    => 'Finanzas',
                'responsible'   => 'Marta Sánchez',
                'article'       => 'Laptop',
                'brand'         => 'Apple',
                'model'         => 'MacBook Pro 14',
                'serial_number' => 'SN-APPLE-001',
                'success_code'  => 'TI-FN-01',
                'image_url'     => 'https://example.com/laptop.jpg',
            ],
        ]);

    $this->assertDatabaseHas('it_equipments', [
        'serial_number' => 'SN-APPLE-001',
        'success_code'  => 'TI-FN-01',
    ]);
});

test('7. sanitiza entradas contra Stored-XSS al registrar equipo (POST /sistemas-ti/inventario)', function () {
    $payload = [
        'department'    => '<script>alert("xss")</script>Sistemas',
        'responsible'   => '<b>Pedro Infante</b>',
        'article'       => '<h1>PC Gamer</h1>',
        'brand'         => '<div onclick="steal()">Asus</div>',
        'model'         => 'ROG Strix',
        'serial_number' => 'SN-XSS-999',
    ];

    $response = $this->actingAs($this->user)->postJson(route('sistemas-ti.inventario.store'), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('it_equipments', [
        'department'    => 'alert("xss")Sistemas',
        'responsible'   => 'Pedro Infante',
        'article'       => 'PC Gamer',
        'brand'         => 'Asus',
        'serial_number' => 'SN-XSS-999',
    ]);
});

test('8. valida campos obligatorios y retorna 422 al registrar equipo inválido', function () {
    $payload = [
        'department' => '',
        'article'    => '',
    ];

    $response = $this->actingAs($this->user)->postJson(route('sistemas-ti.inventario.store'), $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['department', 'article']);
});

test('9. puede registrar equipo via formulario web Blade redireccionando con éxito', function () {
    $payload = [
        'department'    => 'Laboratorio',
        'responsible'   => 'Dra. Méndez',
        'article'       => 'Laptop',
        'brand'         => 'Dell',
        'model'         => 'Precision 3560',
        'serial_number' => 'SN-WEB-STORE-01',
    ];

    $response = $this->actingAs($this->user)->post(route('sistemas-ti.inventario.store'), $payload);

    $response->assertRedirect(route('sistemas-ti.inventario'));
    $response->assertSessionHas('success', 'Equipo registrado correctamente.');

    $this->assertDatabaseHas('it_equipments', [
        'serial_number' => 'SN-WEB-STORE-01',
    ]);
});

test('10. puede actualizar equipo en formato JSON retornando 200 (PUT /sistemas-ti/inventario/{id})', function () {
    $equipment = ItEquipment::create([
        'department'    => 'Oficina',
        'responsible'   => 'Original Name',
        'article'       => 'Monitor',
        'brand'         => 'Samsung',
        'model'         => 'F24T350',
        'serial_number' => 'SN-UPD-01',
    ]);

    $updatePayload = [
        'department'    => 'Oficina',
        'responsible'   => 'Updated Responsible',
        'article'       => 'Monitor Curvo',
        'brand'         => 'Samsung',
        'model'         => 'Odyssey G5',
        'serial_number' => 'SN-UPD-01-MOD',
    ];

    $response = $this->actingAs($this->user)->putJson(route('sistemas-ti.inventario.update', $equipment->id), $updatePayload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Registro actualizado correctamente.',
            'data'    => [
                'id'            => $equipment->id,
                'responsible'   => 'Updated Responsible',
                'article'       => 'Monitor Curvo',
                'model'         => 'Odyssey G5',
                'serial_number' => 'SN-UPD-01-MOD',
            ],
        ]);

    $this->assertDatabaseHas('it_equipments', [
        'id'            => $equipment->id,
        'responsible'   => 'Updated Responsible',
        'model'         => 'Odyssey G5',
        'serial_number' => 'SN-UPD-01-MOD',
    ]);
});

test('11. sanitiza entradas contra Stored-XSS al actualizar equipo (PUT /sistemas-ti/inventario/{id})', function () {
    $equipment = ItEquipment::create([
        'department'    => 'I+D',
        'responsible'   => 'Investigador A',
        'article'       => 'Laptop',
        'brand'         => 'Lenovo',
        'model'         => 'ThinkPad X1',
        'serial_number' => 'SN-XSS-UPD',
    ]);

    $updatePayload = [
        'department'    => 'I+D',
        'responsible'   => '<script>alert("hacked")</script>Dr. House',
        'article'       => 'Laptop',
        'brand'         => '<b>Lenovo</b>',
    ];

    $response = $this->actingAs($this->user)->putJson(route('sistemas-ti.inventario.update', $equipment->id), $updatePayload);

    $response->assertStatus(200);

    $this->assertDatabaseHas('it_equipments', [
        'id'          => $equipment->id,
        'responsible' => 'alert("hacked")Dr. House',
        'brand'       => 'Lenovo',
    ]);
});

test('12. retorna 404 al intentar actualizar un equipo inexistente (PUT /sistemas-ti/inventario/{id})', function () {
    $payload = [
        'department' => 'Sistemas',
        'article'    => 'Laptop',
    ];

    $response = $this->actingAs($this->user)->putJson(route('sistemas-ti.inventario.update', 99999999), $payload);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Equipo de TI no encontrado para actualizar.',
        ]);
});

test('13. puede actualizar equipo via formulario web Blade redireccionando con éxito', function () {
    $equipment = ItEquipment::create([
        'department'    => 'Almacen',
        'responsible'   => 'Bodeguero',
        'article'       => 'PC',
        'brand'         => 'Dell',
        'model'         => 'Optiplex',
        'serial_number' => 'SN-ALM-001',
    ]);

    $payload = [
        'department'    => 'Almacen',
        'responsible'   => 'Jefe de Almacén',
        'article'       => 'PC',
        'brand'         => 'Dell',
        'model'         => 'Optiplex 7080',
        'serial_number' => 'SN-ALM-001-V2',
    ];

    $response = $this->actingAs($this->user)->put(route('sistemas-ti.inventario.update', $equipment->id), $payload);

    $response->assertRedirect(route('sistemas-ti.inventario'));
    $response->assertSessionHas('success', 'Registro actualizado correctamente.');

    $this->assertDatabaseHas('it_equipments', [
        'id'          => $equipment->id,
        'responsible' => 'Jefe de Almacén',
        'model'       => 'Optiplex 7080',
    ]);
});

test('14. puede eliminar equipo en formato JSON retornando 200 (DELETE /sistemas-ti/inventario/{id})', function () {
    $equipment = ItEquipment::create([
        'department'    => 'Sistemas',
        'responsible'   => 'Para Eliminar',
        'article'       => 'Laptop',
        'brand'         => 'Acer',
        'model'         => 'Aspire',
        'serial_number' => 'SN-DEL-01',
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('sistemas-ti.inventario.destroy', $equipment->id));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Equipo eliminado correctamente.',
            'data'    => [],
        ]);

    $this->assertDatabaseMissing('it_equipments', [
        'id' => $equipment->id,
    ]);
});

test('15. retorna 404 al intentar eliminar un equipo inexistente (DELETE /sistemas-ti/inventario/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('sistemas-ti.inventario.destroy', 99999999));

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Equipo de TI no encontrado para eliminar.',
        ]);
});

test('16. puede eliminar equipo via formulario web Blade redireccionando con éxito', function () {
    $equipment = ItEquipment::create([
        'department'    => 'RH',
        'responsible'   => 'Para Eliminar Web',
        'article'       => 'Monitor',
        'brand'         => 'HP',
        'model'         => 'V24',
        'serial_number' => 'SN-DEL-WEB-01',
    ]);

    $response = $this->actingAs($this->user)->delete(route('sistemas-ti.inventario.destroy', $equipment->id));

    $response->assertRedirect(route('sistemas-ti.inventario'));
    $response->assertSessionHas('success', 'Equipo eliminado correctamente.');

    $this->assertDatabaseMissing('it_equipments', [
        'id' => $equipment->id,
    ]);
});
