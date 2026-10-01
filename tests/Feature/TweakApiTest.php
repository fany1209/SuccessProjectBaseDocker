<?php

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Tweak;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->product = Product::first() ?? Product::create([
        'name'        => 'Producto Test ' . uniqid(),
        'unit'        => 'kg',
        'category_id' => 1,
    ]);

    $this->inventory = Inventory::create([
        'product_id' => $this->product->product_id,
        'batch'      => 'BATCH-' . uniqid(),
        'stock'      => 100.0,
    ]);
});

afterEach(function () {
    if (isset($this->inventory)) {
        Tweak::where('inventory_id', $this->inventory->inventory_id)->delete();
        $this->inventory->delete();
    }
});

test('1. puede listar ajustes de inventario (GET /tweaks)', function () {
    $tweak = Tweak::create([
        'type'         => 'Input',
        'quantity'     => 15.0,
        'comments'     => 'Ajuste inicial',
        'inventory_id' => $this->inventory->inventory_id,
        'user_id'      => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)->getJson('/tweaks');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Ajustes de inventario obtenidos correctamente.',
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'tweak_id',
                    'id',
                    'type',
                    'quantity',
                    'comments',
                    'inventory_id',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});

test('2. puede registrar un ajuste de tipo Input incrementando el stock (POST /tweaks)', function () {
    $initialStock = (float) $this->inventory->stock;
    $quantityToAdd = 25.5;

    $payload = [
        'type'         => 'Input',
        'quantity'     => $quantityToAdd,
        'comments'     => 'Entrada por ajuste físico',
        'inventory_id' => $this->inventory->inventory_id,
    ];

    $response = $this->actingAs($this->user)->postJson('/tweaks', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'type'         => 'Input',
                'quantity'     => $quantityToAdd,
                'inventory_id' => $this->inventory->inventory_id,
            ],
        ]);

    $this->assertDatabaseHas('tweaks', [
        'type'         => 'Input',
        'quantity'     => $quantityToAdd,
        'inventory_id' => $this->inventory->inventory_id,
    ]);

    $this->inventory->refresh();
    expect((float) $this->inventory->stock)->toBe($initialStock + $quantityToAdd);
});

test('3. puede registrar un ajuste de tipo Output decrementando el stock (POST /tweaks)', function () {
    $initialStock = (float) $this->inventory->stock;
    $quantityToSubtract = 30.0;

    $payload = [
        'type'         => 'Output',
        'quantity'     => $quantityToSubtract,
        'comments'     => 'Salida por merma',
        'inventory_id' => $this->inventory->inventory_id,
    ];

    $response = $this->actingAs($this->user)->postJson('/tweaks', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'type'         => 'Output',
                'quantity'     => $quantityToSubtract,
                'inventory_id' => $this->inventory->inventory_id,
            ],
        ]);

    $this->inventory->refresh();
    expect((float) $this->inventory->stock)->toBe($initialStock - $quantityToSubtract);
});

test('4. sanitiza comentarios contra Stored-XSS al registrar ajuste', function () {
    $payload = [
        'type'         => 'Input',
        'quantity'     => 10.0,
        'comments'     => '<script>alert("xss")</script>Ajuste de lote <b>limpio</b>',
        'inventory_id' => $this->inventory->inventory_id,
    ];

    $response = $this->actingAs($this->user)->postJson('/tweaks', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('tweaks', [
        'inventory_id' => $this->inventory->inventory_id,
        'comments'     => 'alert("xss")Ajuste de lote limpio',
    ]);
});

test('5. valida campos requeridos y tipos y retorna 422 si son invalidos', function () {
    $response = $this->actingAs($this->user)->postJson('/tweaks', [
        'type'         => 'InvalidType',
        'quantity'     => -5,
        'inventory_id' => 99999999,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type', 'quantity', 'inventory_id']);
});

test('6. puede consultar el detalle de un ajuste (GET /tweaks/{id})', function () {
    $tweak = Tweak::create([
        'type'         => 'Input',
        'quantity'     => 50.0,
        'comments'     => 'Detalle consulta',
        'inventory_id' => $this->inventory->inventory_id,
        'user_id'      => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)->getJson("/tweaks/{$tweak->tweak_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'data'    => [
                'tweak_id'     => $tweak->tweak_id,
                'type'         => 'Input',
                'quantity'     => 50.0,
                'inventory_id' => $this->inventory->inventory_id,
            ],
        ]);
});

test('7. retorna 404 al consultar un ajuste inexistente (GET /tweaks/{id})', function () {
    $response = $this->actingAs($this->user)->getJson('/tweaks/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Ajuste de inventario no encontrado.',
        ]);
});

test('8. puede eliminar un ajuste revirtiendo el stock en inventario (DELETE /tweaks/{id})', function () {
    // Primero hacemos un ajuste Output de 20 kg: stock baja de 100 a 80
    $tweak = app(\App\Http\Repositories\Tweak\TweakRepository::class)->create([
        'type'         => 'Output',
        'quantity'     => 20.0,
        'comments'     => 'Salida a revertir',
        'inventory_id' => $this->inventory->inventory_id,
        'user_id'      => $this->user->id,
    ]);

    $this->inventory->refresh();
    expect((float) $this->inventory->stock)->toBe(80.0);

    // Al eliminar el ajuste, se revierte sumando los 20 kg: stock vuelve a 100
    $response = $this->actingAs($this->user)->deleteJson("/tweaks/{$tweak->tweak_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
        ]);

    $this->assertDatabaseMissing('tweaks', [
        'tweak_id' => $tweak->tweak_id,
    ]);

    $this->inventory->refresh();
    expect((float) $this->inventory->stock)->toBe(100.0);
});

test('9. retorna 404 al intentar eliminar un ajuste inexistente (DELETE /tweaks/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson('/tweaks/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Tweak not deleted',
        ]);
});
