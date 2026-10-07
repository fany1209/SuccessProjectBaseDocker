<?php

use App\Models\Pallet;
use App\Models\User;
use App\Models\YeastProduction;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'user_yeast_' . uniqid() . '@example.com',
    ]);

    \Spatie\Permission\Models\Permission::findOrCreate('quality.show', 'web');
    $this->user->givePermissionTo('quality.show');
});

test('index renders yeast production view for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('production.yeast.index'));

    $response->assertStatus(200);
    $response->assertViewIs('production.yeast.index');
    $response->assertViewHas(['yeastProductions', 'pallets']);
});

test('index returns json resource collection when requested with json header', function () {
    YeastProduction::create([
        'date' => now()->toDateString(),
        'bag_number' => 10,
        'bags_natural' => 5,
        'bags_mix' => 3,
        'bags_white' => 2,
        'finished_product_kg' => 250,
        'bags_quantity' => 10,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('production.yeast.index'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'yeast_production_id',
                'bags_natural',
                'bags_mix',
                'bags_white',
                'finished_product_kg',
            ],
        ],
    ]);
});

test('update fails validation when weights or bags are negative', function () {
    $production = YeastProduction::create([
        'date' => now()->toDateString(),
        'finished_product_kg' => 100,
    ]);

    $response = $this->actingAs($this->user)->putJson(route('production.yeast.update', $production->yeast_production_id), [
        'internal_weight' => -5,
        'bags_natural' => -2,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['internal_weight', 'bags_natural']);
});

test('update returns 404 for non-existent yeast production record', function () {
    $response = $this->actingAs($this->user)->putJson(route('production.yeast.update', 999999), [
        'finished_product_kg' => 100,
    ]);

    $response->assertStatus(404);
});

test('update modifies production and allocates sacks into pallets correctly', function () {
    $production = YeastProduction::create([
        'date' => now()->toDateString(),
        'finished_product_kg' => 500,
        'bags_natural' => 0,
        'bags_mix' => 0,
        'bags_white' => 0,
        'bags_quantity' => 0,
    ]);

    $payload = [
        'internal_weight' => 25.4,
        'external_weight' => 25.1,
        'bags_natural' => 45,
        'bags_mix' => 10,
        'bags_white' => 5,
        'finished_product_kg' => 1500,
    ];

    $response = $this->actingAs($this->user)->putJson(route('production.yeast.update', $production->yeast_production_id), $payload);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Registro de producción de levadura actualizado correctamente.',
    ]);

    $this->assertDatabaseHas('yeast_productions', [
        'yeast_production_id' => $production->yeast_production_id,
        'bags_quantity' => 60,
        'finished_product_kg' => 1500,
    ]);

    $this->assertDatabaseHas('pallets', [
        'color_type' => 'Natural',
        'status' => 'Cerrada',
        'current_sacks' => 40,
    ]);
});

test('sendToInventory returns 404 for non-existent pallet', function () {
    $response = $this->actingAs($this->user)->postJson(route('production.yeast.sendToInventory', 999999));

    $response->assertStatus(404);
});

test('sendToInventory returns 400 when pallet is not closed or already sent', function () {
    $pallet = Pallet::create([
        'pallet_number' => 'TAR-NAT-' . uniqid(),
        'color_type' => 'Natural',
        'current_sacks' => 25,
        'status' => 'Abierta',
        'inventory_status' => 'Pendiente',
    ]);

    $response = $this->actingAs($this->user)->postJson(route('production.yeast.sendToInventory', $pallet->pallet_id));

    $response->assertStatus(400);
    $response->assertJson([
        'success' => false,
        'message' => 'La tarima no está disponible para enviar.',
    ]);
});

test('sendToInventory marks closed pallet as Enviada successfully', function () {
    $pallet = Pallet::create([
        'pallet_number' => 'TAR-NAT-' . uniqid(),
        'color_type' => 'Natural',
        'current_sacks' => 40,
        'status' => 'Cerrada',
        'inventory_status' => 'Pendiente',
    ]);

    $response = $this->actingAs($this->user)->postJson(route('production.yeast.sendToInventory', $pallet->pallet_id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Tarima enviada al almacén correctamente.',
    ]);

    $this->assertDatabaseHas('pallets', [
        'pallet_id' => $pallet->pallet_id,
        'inventory_status' => 'Enviada',
    ]);
});
