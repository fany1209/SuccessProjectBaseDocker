<?php

use App\Models\User;
use App\Models\VitayelaInventory;
use App\Models\VitayelaProduction;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'user_vita_' . uniqid() . '@example.com',
    ]);
});

test('index renders vitayela production view for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('production.vitayela.index'));

    $response->assertStatus(200);
    $response->assertViewIs('production.vitayela.index');
    $response->assertViewHas(['productions', 'inventories', 'db_products']);
});

test('storeProduction creates a new vitayela production record', function () {
    $payload = [
        'fecha_preparacion' => '2026-10-01',
        'kg_preparados' => 500.5,
        'fecha_ensacado' => '2026-10-02',
        'kg_ensacados' => 495.0,
        'num_sacos' => 20,
        'descripcion' => 'Lote de prueba Vitayela',
    ];

    $response = $this->actingAs($this->user)->postJson(route('production.vitayela.storeProduction'), $payload);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Registro agregado correctamente.',
    ]);

    $this->assertDatabaseHas('vitayela_productions', [
        'num_sacos' => 20,
        'descripcion' => 'Lote de prueba Vitayela',
    ]);
});

test('updateProduction updates an existing vitayela production record', function () {
    $production = VitayelaProduction::create([
        'fecha_preparacion' => '2026-10-01',
        'kg_preparados' => 300,
        'num_sacos' => 12,
    ]);

    $response = $this->actingAs($this->user)->putJson(
        route('production.vitayela.updateProduction', $production->vitayela_production_id),
        [
            'kg_preparados' => 350.5,
            'num_sacos' => 14,
            'descripcion' => 'Actualizado con éxito',
        ]
    );

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Registro actualizado correctamente.',
    ]);

    $this->assertDatabaseHas('vitayela_productions', [
        'vitayela_production_id' => $production->vitayela_production_id,
        'num_sacos' => 14,
        'descripcion' => 'Actualizado con éxito',
    ]);
});

test('destroyProduction deletes production record', function () {
    $production = VitayelaProduction::create([
        'kg_preparados' => 100,
        'num_sacos' => 4,
    ]);

    $response = $this->actingAs($this->user)->deleteJson(
        route('production.vitayela.destroyProduction', $production->vitayela_production_id)
    );

    $response->assertStatus(200);
    $this->assertDatabaseMissing('vitayela_productions', [
        'vitayela_production_id' => $production->vitayela_production_id,
    ]);
});

test('storeInventory creates inventory and generates initial movement', function () {
    $payload = [
        'producto_descripcion' => 'Materia Prima Vitayela A',
        'cantidad' => 150.0,
        'unidad' => 'KG',
        'stock_min' => 20.0,
    ];

    $response = $this->actingAs($this->user)->postJson(route('production.vitayela.storeInventory'), $payload);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Producto agregado correctamente.',
    ]);

    $this->assertDatabaseHas('vitayela_inventories', [
        'producto_descripcion' => 'Materia Prima Vitayela A',
        'cantidad' => 150.0,
    ]);

    $this->assertDatabaseHas('vitayela_inventory_movements', [
        'tipo' => 'Entrada',
        'cantidad' => 150.0,
    ]);
});

test('updateInventory updates stock and logs movement difference', function () {
    $inv = VitayelaInventory::create([
        'producto_descripcion' => 'Insumo Base Vitayela',
        'cantidad' => 100.0,
        'unidad' => 'KG',
        'stock_min' => 10.0,
    ]);

    $response = $this->actingAs($this->user)->putJson(
        route('production.vitayela.updateInventory', $inv->vitayela_inventory_id),
        [
            'producto_descripcion' => 'Insumo Base Vitayela',
            'cantidad' => 140.0,
            'unidad' => 'KG',
            'stock_min' => 10.0,
        ]
    );

    $response->assertStatus(200);
    $this->assertDatabaseHas('vitayela_inventories', [
        'vitayela_inventory_id' => $inv->vitayela_inventory_id,
        'cantidad' => 140.0,
    ]);

    $this->assertDatabaseHas('vitayela_inventory_movements', [
        'vitayela_inventory_id' => $inv->vitayela_inventory_id,
        'tipo' => 'Entrada',
        'cantidad' => 40.0,
    ]);
});

test('destroyInventory removes inventory record', function () {
    $inv = VitayelaInventory::create([
        'producto_descripcion' => 'Insumo Obsoleto',
        'cantidad' => 10.0,
    ]);

    $response = $this->actingAs($this->user)->deleteJson(
        route('production.vitayela.destroyInventory', $inv->vitayela_inventory_id)
    );

    $response->assertStatus(200);
    $this->assertDatabaseMissing('vitayela_inventories', [
        'vitayela_inventory_id' => $inv->vitayela_inventory_id,
    ]);
});

test('outputInventory returns 400 when quantity exceeds stock', function () {
    $inv = VitayelaInventory::create([
        'producto_descripcion' => 'Producto Stock Limitado',
        'cantidad' => 50.0,
        'stock_min' => 10.0,
    ]);

    $response = $this->actingAs($this->user)->postJson(
        route('production.vitayela.outputInventory', $inv->vitayela_inventory_id),
        ['cantidad_salida' => 60.0]
    );

    $response->assertStatus(400);
    $response->assertJson([
        'success' => false,
        'message' => 'La cantidad de salida no puede ser mayor a la cantidad en stock.',
    ]);
});

test('outputInventory registers output and sets alert if stock is below minimum', function () {
    $inv = VitayelaInventory::create([
        'producto_descripcion' => 'Producto Salida Test',
        'cantidad' => 50.0,
        'stock_min' => 20.0,
    ]);

    $response = $this->actingAs($this->user)->postJson(
        route('production.vitayela.outputInventory', $inv->vitayela_inventory_id),
        ['cantidad_salida' => 35.0]
    );

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'alert' => true,
    ]);

    $this->assertDatabaseHas('vitayela_inventories', [
        'vitayela_inventory_id' => $inv->vitayela_inventory_id,
        'cantidad' => 15.0,
    ]);

    $this->assertDatabaseHas('vitayela_inventory_movements', [
        'vitayela_inventory_id' => $inv->vitayela_inventory_id,
        'tipo' => 'Salida',
        'cantidad' => 35.0,
    ]);
});

test('getMovements returns movement history for inventory item', function () {
    $inv = VitayelaInventory::create([
        'producto_descripcion' => 'Producto Con Movimientos',
        'cantidad' => 20.0,
    ]);

    $this->actingAs($this->user)->postJson(
        route('production.vitayela.outputInventory', $inv->vitayela_inventory_id),
        ['cantidad_salida' => 5.0]
    );

    $response = $this->actingAs($this->user)->getJson(
        route('production.vitayela.getMovements', $inv->vitayela_inventory_id)
    );

    $response->assertStatus(200);
    $movements = $response->json();
    expect(count($movements))->toBeGreaterThanOrEqual(1);
});

test('storeMaterialRequest creates material request and items', function () {
    $payload = [
        'applicant_name' => 'Operador Vitayela',
        'comments' => 'Material urgente para turno nocturno',
        'products' => [
            ['name' => 'Azúcar estándar', 'quantity' => 100.0],
            ['name' => 'Levadura viva', 'quantity' => 25.0],
        ],
    ];

    $response = $this->actingAs($this->user)->postJson(
        route('production.vitayela.storeMaterialRequest'),
        $payload
    );

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Solicitud enviada a almacén correctamente.',
    ]);

    $this->assertDatabaseHas('production_material_requests', [
        'area' => 'vitayela',
        'applicant_name' => 'Operador Vitayela',
        'status' => 'Pendiente',
    ]);

    $this->assertDatabaseHas('production_material_request_items', [
        'product_name' => 'Azúcar estándar',
        'quantity' => 100.0,
    ]);
});
