<?php

use App\Models\Trailer;
use App\Models\TransportLine;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    $this->tLine = TransportLine::first() ?? TransportLine::create(['name' => 'Transportes Trailer Test ' . uniqid()]);
});

test('1. puede listar remolques vía getTrailers (GET /getTrailers)', function () {
    $trailer = Trailer::create([
        'type'              => 'Caja Seca 53',
        'unit_number'       => 'CS-' . uniqid(),
        'plate'             => 'TRL-' . uniqid(),
        'color'             => 'Blanco',
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('trailer.getTrailers'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Remolques obtenidos correctamente.',
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'trailer_id',
                    'id',
                    'type',
                    'unit_number',
                    'plate',
                    'color',
                    'transport_line_id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
            'trailers' => [
                '*' => [
                    'trailer_id',
                    'type',
                    'plate',
                    'name',
                ],
            ],
        ]);

    $trailer->delete();
});

test('2. puede filtrar remolques por transport_line y búsqueda (GET /getTrailers)', function () {
    $plate = 'BOX-' . uniqid();
    $trailer = Trailer::create([
        'type'              => 'Plataforma',
        'unit_number'       => 'PLT-' . uniqid(),
        'plate'             => $plate,
        'color'             => 'Rojo',
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('trailer.getTrailers', [
        't_line' => $this->tLine->transport_line_id,
        'search' => $plate,
    ]));

    $response->assertStatus(200);
    $data = $response->json('data');
    expect(count($data))->toBeGreaterThanOrEqual(1);
    expect($data[0]['plate'])->toBe($plate);

    $trailer->delete();
});

test('3. puede listar remolques vía index (GET /trailer)', function () {
    $response = $this->actingAs($this->user)->getJson('/trailer');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
        ]);
});

test('4. puede registrar un remolque (POST /trailer)', function () {
    $uniqueUnit = 'CR-' . uniqid();
    $uniquePlate = 'TRL-' . uniqid();

    $payload = [
        'type'              => 'Caja Refrigerada',
        'unit_number'       => $uniqueUnit,
        'plate'             => $uniquePlate,
        'color'             => 'Azul',
        'transport_line_id' => $this->tLine->transport_line_id,
    ];

    $response = $this->actingAs($this->user)->postJson('/trailer', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'type'              => 'Caja Refrigerada',
                'unit_number'       => $uniqueUnit,
                'plate'             => $uniquePlate,
                'color'             => 'Azul',
                'transport_line_id' => $this->tLine->transport_line_id,
            ],
        ]);

    $this->assertDatabaseHas('trailers', [
        'plate'             => $uniquePlate,
        'unit_number'       => $uniqueUnit,
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);
});

test('5. sanitiza entradas contra Stored-XSS al registrar remolque', function () {
    $uniquePlate = 'X-' . substr(uniqid(), -10);
    $uniqueUnit = 'TLV-' . uniqid();

    $payload = [
        'type'              => '<script>alert("xss")</script>Tolva',
        'unit_number'       => '<b>' . $uniqueUnit . '</b>',
        'plate'             => '<i>' . $uniquePlate . '</i>',
        'color'             => '<u>Amarillo</u>',
        'transport_line_id' => $this->tLine->transport_line_id,
    ];

    $response = $this->actingAs($this->user)->postJson('/trailer', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('trailers', [
        'type'        => 'alert("xss")Tolva',
        'unit_number' => $uniqueUnit,
        'plate'       => $uniquePlate,
        'color'       => 'Amarillo',
    ]);
});

test('6. valida campos requeridos y existencia de transport_line_id y retorna 422', function () {
    $response = $this->actingAs($this->user)->postJson('/trailer', [
        'type'              => '',
        'plate'             => '',
        'transport_line_id' => 99999999,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type', 'plate', 'transport_line_id']);
});

test('7. puede consultar un remolque para edición modal (GET /trailer/{id})', function () {
    $trailer = Trailer::create([
        'type'              => 'Góndola',
        'unit_number'       => 'GON-' . uniqid(),
        'plate'             => 'GON-' . uniqid(),
        'color'             => 'Gris',
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $response = $this->actingAs($this->user)->getJson("/trailer/{$trailer->trailer_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'data'    => [
                'trailer_id'        => $trailer->trailer_id,
                'type'              => 'Góndola',
                'plate'             => $trailer->plate,
                'transport_line_id' => $this->tLine->transport_line_id,
            ],
            'trailer' => [
                'trailer_id'        => $trailer->trailer_id,
                'type'              => 'Góndola',
                'plate'             => $trailer->plate,
                'transport_line_id' => $this->tLine->transport_line_id,
            ],
        ]);

    $trailer->delete();
});

test('8. retorna 404 al consultar remolque inexistente (GET /trailer/{id})', function () {
    $response = $this->actingAs($this->user)->getJson('/trailer/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Remolque no encontrado.',
        ]);
});

test('9. puede actualizar un remolque (PUT /trailer/{id})', function () {
    $trailer = Trailer::create([
        'type'              => 'Lowboy',
        'unit_number'       => 'LB-' . uniqid(),
        'plate'             => 'LB-' . uniqid(),
        'color'             => 'Naranja',
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $updatedPlate = 'LB-U-' . uniqid();
    $updatedUnit = 'LB-UN-' . uniqid();
    $payload = [
        'type'              => 'Lowboy Extensible',
        'unit_number'       => $updatedUnit,
        'plate'             => $updatedPlate,
        'color'             => 'Negro',
        'transport_line_id' => $this->tLine->transport_line_id,
    ];

    $response = $this->actingAs($this->user)->putJson("/trailer/{$trailer->trailer_id}", $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'trailer_id'  => $trailer->trailer_id,
                'type'        => 'Lowboy Extensible',
                'unit_number' => $updatedUnit,
                'plate'       => $updatedPlate,
                'color'       => 'Negro',
            ],
        ]);

    $this->assertDatabaseHas('trailers', [
        'trailer_id'  => $trailer->trailer_id,
        'unit_number' => $updatedUnit,
        'plate'       => $updatedPlate,
        'color'       => 'Negro',
    ]);

    $trailer->delete();
});

test('10. retorna 404 al intentar actualizar remolque inexistente (PUT /trailer/{id})', function () {
    $response = $this->actingAs($this->user)->putJson('/trailer/99999999', [
        'type'              => 'Fantasma',
        'plate'             => 'GHOST-' . uniqid(),
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Remolque no encontrado.',
        ]);
});

test('11. puede eliminar un remolque (DELETE /trailer/{id})', function () {
    $trailer = Trailer::create([
        'type'              => 'Borrable',
        'unit_number'       => 'DEL-T-' . uniqid(),
        'plate'             => 'DEL-T-' . uniqid(),
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/trailer/{$trailer->trailer_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Trailer deleted',
        ]);

    $this->assertDatabaseMissing('trailers', [
        'trailer_id' => $trailer->trailer_id,
    ]);
});

test('12. retorna 404 al intentar eliminar remolque inexistente (DELETE /trailer/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson('/trailer/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Trailer not deleted',
        ]);
});
