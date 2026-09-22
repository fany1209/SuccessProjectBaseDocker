<?php

use App\Models\Cli;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    Permission::findOrCreate('warehouse.show', 'web');
    $this->user->givePermissionTo('warehouse.show');

    $this->warehouseId = DB::table('warehouses')->value('warehouse_id') ?? DB::table('warehouses')->insertGetId([
        'name'       => 'Almacén Principal Test',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->locationId = DB::table('locations')->value('location_id') ?? DB::table('locations')->insertGetId([
        'warehouse_id' => $this->warehouseId,
        'name'         => 'Ubicación Test A1',
        'created_at'   => now(),
        'updated_at'   => now(),
    ]);

    $this->conceptId = DB::table('concepts')->value('concept_id') ?? DB::table('concepts')->insertGetId([
        'name'       => 'Entrada por Compra',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->productId = DB::table('products')->value('product_id') ?? DB::table('products')->insertGetId([
        'name'       => 'Producto Base Test',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->inventoryId = DB::table('inventory')->value('inventory_id') ?? DB::table('inventory')->insertGetId([
        'product_id'  => $this->productId,
        'location_id' => $this->locationId,
        'quantity'    => 100,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    $this->cli = Cli::create([
        'location_id'     => $this->locationId,
        'inventory_id'    => $this->inventoryId,
        'concept_id'      => $this->conceptId,
        'quantity'        => 10,
        'weight_per_unit' => 25.5,
        'net_weight'      => 255.0,
        'bag_number'      => 'BAG-' . rand(10000, 99999),
        'protein'         => 14.5,
    ]);
});

test('1. API GET /cli (Listado de operaciones de almacén en JSON)', function () {
    $response = $this->actingAs($this->user)->getJson('/cli');

    echo "\n\n>>> LLAMADA: GET /cli\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect(count($response->json('data')))->toBeGreaterThan(0);
});

test('2. API GET /cli/{id} (Consulta detallada con datos de almacén y producto)', function () {
    $response = $this->actingAs($this->user)->getJson('/cli/' . $this->cli->cli_id);

    echo "\n\n>>> LLAMADA: GET /cli/{$this->cli->cli_id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('cli.cli_id'))->toBe($this->cli->cli_id);
    expect($response->json('cli.warehouse_id'))->toBe($this->warehouseId);
});

test('3. API POST /cli (Creación de operación en lote con transacción)', function () {
    $payload = [
        'location_id'     => $this->locationId,
        'concept_id'      => [$this->conceptId],
        'inventory_id'    => [$this->inventoryId],
        'quantity'        => [5],
        'weight_per_unit' => [20.0],
    ];

    $response = $this->actingAs($this->user)->postJson('/cli', $payload);

    echo "\n\n>>> LLAMADA: POST /cli\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.0.net_weight'))->toEqual(100);
});

test('4. API PUT /cli/{id} (Actualización de operación con recálculo de peso neto)', function () {
    $payload = [
        'location_id'     => $this->locationId,
        'concept_id'      => $this->conceptId,
        'inventory_id'    => $this->inventoryId,
        'quantity'        => 15,
        'weight_per_unit' => 30.0,
        'bag_number'      => 'BAG-UPD-' . rand(1000, 9999),
        'protein'         => 16.0,
    ];

    $response = $this->actingAs($this->user)->putJson('/cli/' . $this->cli->cli_id, $payload);

    echo "\n\n>>> LLAMADA: PUT /cli/{$this->cli->cli_id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.quantity'))->toEqual(15);
    expect($response->json('data.net_weight'))->toEqual(450);
});

test('5. API DELETE /cli/{id} (Eliminación transaccional con bloqueo pesimista)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/cli/' . $this->cli->cli_id);

    echo "\n\n>>> LLAMADA: DELETE /cli/{$this->cli->cli_id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect(Cli::find($this->cli->cli_id))->toBeNull();
});

test('6. API DELETE /cli/{id} con ID inexistente retorna 404', function () {
    $response = $this->actingAs($this->user)->deleteJson('/cli/99999999');

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
});
