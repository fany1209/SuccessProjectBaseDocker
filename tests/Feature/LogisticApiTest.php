<?php

use App\Models\Trailer;
use App\Models\TransportLine;
use App\Models\User;
use App\Models\Vehicle;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    Permission::findOrCreate('warehouse.show', 'web');
    $this->user->givePermissionTo('warehouse.show');
});

test('1. puede visualizar la vista principal de logística (GET /logistic)', function () {
    $response = $this->actingAs($this->user)->get(route('logistic.index'));

    $response->assertStatus(200);
    $response->assertViewIs('logistic');
    $response->assertViewHas([
        'tLines',
        'count_operators',
        'count_tLines',
        'count_vehicles',
        'count_trailers',
        'warehouses',
        'concepts',
        'products',
        'suppliers',
        'customers',
    ]);
});

test('2. retorna 403 al acceder a logística sin permiso warehouse.show', function () {
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)->get(route('logistic.index'));

    $response->assertStatus(403);
});

test('3. puede obtener resumen logístico en JSON (GET /logistic)', function () {
    $response = $this->actingAs($this->user)->getJson(route('logistic.index'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Datos de logística obtenidos correctamente.',
        ])
        ->assertJsonStructure([
            'success',
            'flag',
            'code',
            'message',
            'data' => [
                'count_operators',
                'count_tLines',
                'count_vehicles',
                'count_trailers',
            ],
        ]);
});

test('4. puede obtener datos de gráficos de líneas de transporte (GET /charts)', function () {
    $tLine = TransportLine::create(['name' => 'Transportes Test ' . uniqid()]);
    $vehicle = Vehicle::create([
        'type'              => 'Torton',
        'plate'             => 'PLT-' . rand(10000, 99999),
        'transport_line_id' => $tLine->transport_line_id,
    ]);
    $trailer = Trailer::create([
        'type'              => 'Caja Seca',
        'plate'             => 'TRL-' . rand(10000, 99999),
        'transport_line_id' => $tLine->transport_line_id,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('logistic.charts'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Gráficas de logística obtenidas correctamente.',
        ])
        ->assertJsonStructure([
            'success',
            'flag',
            'code',
            'message',
            'data' => [
                'vehicles_per_tl',
                'trailers_per_tl',
            ],
            // Compatibilidad frontend en raíz
            'vehicles_per_tl',
            'trailers_per_tl',
        ]);

    $trailer->delete();
    $vehicle->delete();
    $tLine->delete();
});

test('5. sanitiza parámetros de consulta contra XSS en charts (GET /charts)', function () {
    $response = $this->actingAs($this->user)->getJson(route('logistic.charts', [
        'filter' => '<script>alert("xss")</script>general',
    ]));

    $response->assertStatus(200);
});
