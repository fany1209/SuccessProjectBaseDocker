<?php

use App\Models\TransportLine;
use App\Models\User;
use App\Models\Vehicle;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    $this->tLine = TransportLine::first() ?? TransportLine::create(['name' => 'Transportes Test ' . uniqid()]);
});

test('1. puede listar vehículos vía getVehicles (GET /getVehicles)', function () {
    $vehicle = Vehicle::create([
        'type'              => 'Torton',
        'unit_number'       => 'U-' . uniqid(),
        'plate'             => 'PLK-' . uniqid(),
        'color'             => 'Blanco',
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('vehicle.getVehicles'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Vehículos obtenidos correctamente.',
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'vehicle_id',
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
            'vehicles' => [
                '*' => [
                    'vehicle_id',
                    'type',
                    'plate',
                    'name',
                ],
            ],
        ]);

    $vehicle->delete();
});

test('2. puede filtrar vehículos por transport_line y búsqueda (GET /getVehicles)', function () {
    $plate = 'XYZ-' . uniqid();
    $vehicle = Vehicle::create([
        'type'              => 'Rabón',
        'unit_number'       => 'U-' . uniqid(),
        'plate'             => $plate,
        'color'             => 'Azul',
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('vehicle.getVehicles', [
        't_line' => $this->tLine->transport_line_id,
        'search' => $plate,
    ]));

    $response->assertStatus(200);
    $data = $response->json('data');
    expect(count($data))->toBeGreaterThanOrEqual(1);
    expect($data[0]['plate'])->toBe($plate);

    $vehicle->delete();
});

test('3. puede listar vehículos vía index (GET /vehicle)', function () {
    $response = $this->actingAs($this->user)->getJson('/vehicle');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
        ]);
});

test('4. puede registrar un vehículo (POST /vehicle)', function () {
    $uniqueUnit = 'TRAC-' . uniqid();
    $uniquePlate = 'PL-' . uniqid();

    $payload = [
        'type'              => 'Tractor',
        'unit_number'       => $uniqueUnit,
        'plate'             => $uniquePlate,
        'color'             => 'Rojo',
        'transport_line_id' => $this->tLine->transport_line_id,
    ];

    $response = $this->actingAs($this->user)->postJson('/vehicle', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'type'              => 'Tractor',
                'unit_number'       => $uniqueUnit,
                'plate'             => $uniquePlate,
                'color'             => 'Rojo',
                'transport_line_id' => $this->tLine->transport_line_id,
            ],
        ]);

    $this->assertDatabaseHas('vehicles', [
        'plate'             => $uniquePlate,
        'unit_number'       => $uniqueUnit,
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);
});

test('5. sanitiza entradas contra Stored-XSS al registrar vehículo', function () {
    $uniquePlate = 'XSS-' . uniqid();
    $uniqueUnit = 'U-' . uniqid();

    $payload = [
        'type'              => '<script>alert("xss")</script>Camioneta',
        'unit_number'       => '<b>' . $uniqueUnit . '</b>',
        'plate'             => '<i>' . $uniquePlate . '</i>',
        'color'             => '<u>Verde</u>',
        'transport_line_id' => $this->tLine->transport_line_id,
    ];

    $response = $this->actingAs($this->user)->postJson('/vehicle', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('vehicles', [
        'type'        => 'alert("xss")Camioneta',
        'unit_number' => $uniqueUnit,
        'plate'       => $uniquePlate,
        'color'       => 'Verde',
    ]);
});

test('6. valida campos requeridos y existencia de transport_line_id y retorna 422', function () {
    $response = $this->actingAs($this->user)->postJson('/vehicle', [
        'type'              => '',
        'plate'             => '',
        'transport_line_id' => 99999999,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type', 'plate', 'transport_line_id']);
});

test('7. puede consultar un vehículo para edición modal (GET /vehicle/{id})', function () {
    $vehicle = Vehicle::create([
        'type'              => 'Pipa',
        'unit_number'       => 'PIP-' . uniqid(),
        'plate'             => 'PIP-' . uniqid(),
        'color'             => 'Plata',
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $response = $this->actingAs($this->user)->getJson("/vehicle/{$vehicle->vehicle_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'data'    => [
                'vehicle_id'        => $vehicle->vehicle_id,
                'type'              => 'Pipa',
                'plate'             => $vehicle->plate,
                'transport_line_id' => $this->tLine->transport_line_id,
            ],
            'vehicle' => [
                'vehicle_id'        => $vehicle->vehicle_id,
                'type'              => 'Pipa',
                'plate'             => $vehicle->plate,
                'transport_line_id' => $this->tLine->transport_line_id,
            ],
        ]);

    $vehicle->delete();
});

test('8. retorna 404 al consultar vehículo inexistente (GET /vehicle/{id})', function () {
    $response = $this->actingAs($this->user)->getJson('/vehicle/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Vehículo no encontrado.',
        ]);
});

test('9. puede actualizar un vehículo (PUT /vehicle/{id})', function () {
    $vehicle = Vehicle::create([
        'type'              => 'Pickup',
        'unit_number'       => 'PK-' . uniqid(),
        'plate'             => 'PK-' . uniqid(),
        'color'             => 'Gris',
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $updatedPlate = 'PK-U-' . uniqid();
    $updatedUnit = 'PK-UN-' . uniqid();
    $payload = [
        'type'              => 'Pickup Doble Cabina',
        'unit_number'       => $updatedUnit,
        'plate'             => $updatedPlate,
        'color'             => 'Negro',
        'transport_line_id' => $this->tLine->transport_line_id,
    ];

    $response = $this->actingAs($this->user)->putJson("/vehicle/{$vehicle->vehicle_id}", $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'vehicle_id'  => $vehicle->vehicle_id,
                'type'        => 'Pickup Doble Cabina',
                'unit_number' => $updatedUnit,
                'plate'       => $updatedPlate,
                'color'       => 'Negro',
            ],
        ]);

    $this->assertDatabaseHas('vehicles', [
        'vehicle_id'  => $vehicle->vehicle_id,
        'unit_number' => $updatedUnit,
        'plate'       => $updatedPlate,
        'color'       => 'Negro',
    ]);

    $vehicle->delete();
});

test('10. retorna 404 al intentar actualizar vehículo inexistente (PUT /vehicle/{id})', function () {
    $response = $this->actingAs($this->user)->putJson('/vehicle/99999999', [
        'type'              => 'Fantasma',
        'plate'             => 'GHOST-' . uniqid(),
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Vehículo no encontrado.',
        ]);
});

test('11. puede eliminar un vehículo (DELETE /vehicle/{id})', function () {
    $vehicle = Vehicle::create([
        'type'              => 'Borrable',
        'unit_number'       => 'DEL-' . uniqid(),
        'plate'             => 'DEL-' . uniqid(),
        'transport_line_id' => $this->tLine->transport_line_id,
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/vehicle/{$vehicle->vehicle_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Vehicle deleted',
        ]);

    $this->assertDatabaseMissing('vehicles', [
        'vehicle_id' => $vehicle->vehicle_id,
    ]);
});

test('12. retorna 404 al intentar eliminar vehículo inexistente (DELETE /vehicle/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson('/vehicle/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Vehicle not deleted',
        ]);
});
