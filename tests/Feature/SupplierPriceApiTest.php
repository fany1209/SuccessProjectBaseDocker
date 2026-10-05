<?php

use App\Models\SupplierPrice;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->price1 = SupplierPrice::create([
        'insumo'           => 'Insumo Test Químico',
        'clave_sat'        => '12345678',
        'proveedor'        => 'Proveedor Alpha',
        'precio'           => 150.50,
        'tiene_iva'        => 1,
        'moneda'           => 'MXN',
        'fecha_cotizacion' => now()->subDays(10)->toDateString(),
    ]);

    $this->price2 = SupplierPrice::create([
        'insumo'           => 'Insumo Test Químico',
        'clave_sat'        => '12345678',
        'proveedor'        => 'Proveedor Beta',
        'precio'           => 120.00,
        'tiene_iva'        => 0,
        'moneda'           => 'MXN',
        'fecha_cotizacion' => now()->toDateString(),
    ]);
});

afterEach(function () {
    SupplierPrice::whereIn('id', [$this->price1->id, $this->price2->id])->delete();
});

test('unauthenticated user cannot access precios index', function () {
    $response = $this->get('/precios');

    $response->assertStatus(302);
});

test('user can view precios index view with prices and distinct insumos', function () {
    $response = $this->actingAs($this->user)->get('/precios');

    $response->assertStatus(200);
    $response->assertViewIs('finance.precios.index');
    $response->assertViewHasAll(['todosLosPrecios', 'insumos']);
});

test('store creates a new supplier price successfully and sanitizes input', function () {
    $payload = [
        'insumo'           => '<b>Insumo Especial Anti-XSS</b>',
        'clave_sat'        => '87654321',
        'proveedor'        => 'Proveedor Gamma',
        'precio'           => 99.99,
        'tiene_iva'        => 1,
        'moneda'           => 'USD',
        'fecha_cotizacion' => now()->toDateString(),
    ];

    $response = $this->actingAs($this->user)->postJson(route('precios.store'), $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $created = SupplierPrice::where('insumo', 'Insumo Especial Anti-XSS')->first();
    expect($created)->not->toBeNull();
    expect((float) $created->precio)->toBe(99.99);
    expect($created->moneda)->toBe('USD');

    if ($created) {
        $created->delete();
    }
});

test('store fails validation when required fields are missing', function () {
    $response = $this->actingAs($this->user)->postJson(route('precios.store'), [
        'precio' => -10,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['insumo', 'proveedor', 'precio', 'fecha_cotizacion']);
});

test('update modifies supplier price successfully', function () {
    $payload = [
        'insumo'           => 'Insumo Test Químico',
        'clave_sat'        => '99999999',
        'proveedor'        => 'Proveedor Alpha Actualizado',
        'precio'           => 200.00,
        'tiene_iva'        => 0,
        'moneda'           => 'MXN',
        'fecha_cotizacion' => now()->toDateString(),
    ];

    $response = $this->actingAs($this->user)->postJson(route('precios.update', $this->price1->id), $payload);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->price1->refresh();
    expect($this->price1->proveedor)->toBe('Proveedor Alpha Actualizado');
    expect((float) $this->price1->precio)->toBe(200.00);
});

test('update returns 404 for non-existent supplier price', function () {
    $payload = [
        'insumo'           => 'Insumo Inexistente',
        'proveedor'        => 'Proveedor Fantasma',
        'precio'           => 50.00,
        'fecha_cotizacion' => now()->toDateString(),
    ];

    $response = $this->actingAs($this->user)->postJson(route('precios.update', 999999), $payload);

    $response->assertStatus(404);
    $response->assertJsonPath('success', false);
});

test('destroy removes supplier price successfully', function () {
    $tempPrice = SupplierPrice::create([
        'insumo'           => 'Insumo Temporal Borrar',
        'proveedor'        => 'Proveedor Temp',
        'precio'           => 10.00,
        'fecha_cotizacion' => now()->toDateString(),
    ]);

    $response = $this->actingAs($this->user)->postJson(route('precios.destroy', $tempPrice->id));

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $this->assertDatabaseMissing('supplier_prices', ['id' => $tempPrice->id]);
});

test('destroy returns 404 for non-existent supplier price', function () {
    $response = $this->actingAs($this->user)->postJson(route('precios.destroy', 999999));

    $response->assertStatus(404);
    $response->assertJsonPath('success', false);
});

test('grafica non-ajax returns blade view with insumos list', function () {
    $response = $this->actingAs($this->user)->get(route('precios.grafica'));

    $response->assertStatus(200);
    $response->assertViewIs('finance.precios.grafica');
    $response->assertViewHas('insumos');
});

test('grafica ajax returns empty payload when no insumo provided', function () {
    $response = $this->actingAs($this->user)->getJson(route('precios.grafica'));

    $response->assertStatus(200);
    $response->assertJson([
        'data'     => [],
        'analisis' => null,
    ]);
});

test('grafica ajax returns trend analysis and comparative prices for insumo', function () {
    $response = $this->actingAs($this->user)->getJson(route('precios.grafica', [
        'insumo'       => 'Insumo Test Químico',
        'fecha_inicio' => now()->subDays(15)->toDateString(),
        'fecha_fin'    => now()->addDay()->toDateString(),
    ]));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => ['fecha', 'precio', 'moneda', 'proveedor'],
        ],
        'analisis' => [
            'mejor_proveedor',
            'mejor_precio',
            'moneda',
            'ahorro_potencial',
            'tendencia',
            'total_registros',
        ],
    ]);

    $analisis = $response->json('analisis');
    expect($analisis['mejor_proveedor'])->toBe('Proveedor Beta');
    expect($analisis['mejor_precio'])->toBe('120.00');
    expect($analisis['total_registros'])->toBe(2);
    expect($analisis['tendencia'])->toBe('Baja');
});
