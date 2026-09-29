<?php

use App\Models\Category;
use App\Models\Concept;
use App\Models\Customer;
use App\Models\Input;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\Quarantine;
use App\Models\Sector;
use App\Models\Supplier;
use App\Models\TransportLine;
use App\Models\User;
use App\Models\Warehouse;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    Permission::findOrCreate('inventory.show', 'web');
    $this->user->givePermissionTo('inventory.show');

    $this->category = Category::first() ?? Category::create([
        'name' => 'Insumos Generales ' . uniqid(),
    ]);

    $this->product = Product::first() ?? Product::create([
        'name'        => 'Levadura Seca Calidad ' . uniqid(),
        'unit'        => 'kg',
        'category_id' => $this->category->category_id,
        'stock_min'   => 10,
        'stock_max'   => 500,
    ]);

    $this->warehouse = Warehouse::first() ?? Warehouse::create([
        'name' => 'Almacén Principal ' . uniqid(),
    ]);

    $this->location = Location::first() ?? Location::create([
        'name'         => 'Ubicación A-' . rand(1, 99),
        'warehouse_id' => $this->warehouse->warehouse_id,
    ]);

    $this->concept = Concept::first() ?? Concept::create([
        'name' => 'Producto Terminado',
    ]);

    $this->sector = Sector::first() ?? Sector::create([
        'name' => 'Sector Agro ' . uniqid(),
        'code' => 'AG-' . rand(10, 99),
    ]);

    $this->supplier = Supplier::first() ?? Supplier::create([
        'name'          => 'Proveedor Test ' . uniqid(),
        'supplier_code' => 'SP' . rand(1000, 9999),
        'sector_id'     => $this->sector->sector_id,
    ]);

    $this->customer = Customer::first() ?? Customer::create([
        'name'          => 'Cliente Test ' . uniqid(),
        'customer_code' => 'CL-' . rand(1000, 9999),
    ]);

    $this->transportLine = TransportLine::first() ?? TransportLine::create([
        'name' => 'Transportes del Bajío ' . uniqid(),
    ]);

    $this->inventoryItem = Inventory::create([
        'product_id' => $this->product->product_id,
        'stock'      => 150.75,
        'batch'      => 'B-INI-' . substr(uniqid(), -8) . '-' . rand(10, 99),
        'bar_code'   => rand(10000, 99999),
    ]);
});

test('1. API GET /inventory (Listado general de inventario - JSON)', function () {
    $response = $this->actingAs($this->user)->getJson('/inventory');

    echo "\n\n>>> LLAMADA: GET /inventory (JSON)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toBeArray();

    $item = collect($response->json('data'))->firstWhere('inventory_id', $this->inventoryItem->inventory_id);
    expect($item)->not->toBeNull();
    expect($item['batch'])->toBe($this->inventoryItem->batch);
});

test('2. API GET /inventory/{id} (Consulta por ID de inventario existente)', function () {
    $response = $this->actingAs($this->user)->getJson("/inventory/{$this->inventoryItem->inventory_id}");

    echo "\n\n>>> LLAMADA: GET /inventory/{id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.inventory_id'))->toBe($this->inventoryItem->inventory_id);
    expect($response->json('data.batch'))->toBe($this->inventoryItem->batch);
});

test('3. API GET /inventory/{id} (404 al consultar inventario inexistente)', function () {
    $response = $this->actingAs($this->user)->getJson('/inventory/9999999');

    echo "\n\n>>> LLAMADA: GET /inventory/9999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('4. API POST /inventory (Creación exitosa de inventario y anti-XSS)', function () {
    $cleanBatch = 'B-NEW-' . rand(1000, 9999);
    $payload = [
        'product_id' => $this->product->product_id,
        'stock'      => 80.5,
        'batch'      => '<b>' . $cleanBatch . '</b>',
        'bar_code'   => 54321,
    ];

    $response = $this->actingAs($this->user)->postJson('/inventory', $payload);

    echo "\n\n>>> LLAMADA: POST /inventory\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.batch'))->toBe($cleanBatch);
    expect($response->json('data.batch'))->not->toContain('<b>');
    expect($response->json('data.bar_code'))->toBe('54321');

    $this->assertDatabaseHas('inventory', [
        'inventory_id' => $response->json('data.inventory_id'),
        'batch'        => $cleanBatch,
    ]);
});

test('5. API POST /inventory (Validación 422 por campo requerido faltante)', function () {
    $payload = [
        'stock' => -5,
    ];

    $response = $this->actingAs($this->user)->postJson('/inventory', $payload);

    echo "\n\n>>> LLAMADA: POST /inventory (422 Error)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['product_id', 'stock', 'batch']);
});

test('6. API PUT /inventory/{id} (Actualización exitosa de inventario)', function () {
    $updatedBatch = 'BATCH-UPDATED-' . uniqid();
    $payload = [
        'stock'    => 200.0,
        'batch'    => $updatedBatch,
        'bar_code' => 98765,
    ];

    $response = $this->actingAs($this->user)->putJson("/inventory/{$this->inventoryItem->inventory_id}", $payload);

    echo "\n\n>>> LLAMADA: PUT /inventory/{id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.batch'))->toBe($updatedBatch);
    expect($response->json('data.stock'))->toEqual(200.0);
});

test('7. API PUT /inventory/{id} (404 al intentar actualizar inventario inexistente)', function () {
    $payload = [
        'stock' => 10,
        'batch' => 'NON-EXISTENT',
    ];

    $response = $this->actingAs($this->user)->putJson('/inventory/9999999', $payload);

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('8. API DELETE /inventory/{id} (Eliminación exitosa de inventario)', function () {
    $toDelete = Inventory::create([
        'product_id' => $this->product->product_id,
        'stock'      => 10,
        'batch'      => 'BATCH-DEL-' . rand(100, 999),
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/inventory/{$toDelete->inventory_id}");

    echo "\n\n>>> LLAMADA: DELETE /inventory/{id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();

    $this->assertDatabaseMissing('inventory', [
        'inventory_id' => $toDelete->inventory_id,
    ]);
});

test('9. API GET /getInventoryAvailable (Consulta de stock disponible y cuarentena)', function () {
    $response = $this->actingAs($this->user)->getJson('/getInventoryAvailable');

    echo "\n\n>>> LLAMADA: GET /getInventoryAvailable\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    expect($response->json('products'))->toBeArray();
    expect($response->json('quarantine'))->toBeArray();
});

test('10. API Cuarentena (addQuarantine y updateQuarantine)', function () {
    $initialStock = $this->inventoryItem->stock;
    $quarantineQty = 25.5;

    // 10.1 Enviar a cuarentena
    $addPayload = [
        'inventory_id' => $this->inventoryItem->inventory_id,
        'quantity'     => $quarantineQty,
        'notes'        => '<b>Prueba de cuarentena anti-XSS</b>',
    ];

    $addResponse = $this->actingAs($this->user)->postJson('/addQuarantine', $addPayload);

    echo "\n\n>>> LLAMADA: POST /addQuarantine\n";
    echo "HTTP STATUS: " . $addResponse->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($addResponse->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $addResponse->assertStatus(201);
    expect($addResponse->json('message'))->toBe('Operation successfuly make it');

    $this->inventoryItem->refresh();
    expect((float) $this->inventoryItem->stock)->toEqual((float) ($initialStock - $quarantineQty));

    $quarantineRecord = Quarantine::where('inventory_id', $this->inventoryItem->inventory_id)->latest('quarantine_id')->first();
    expect($quarantineRecord)->not->toBeNull();
    expect($quarantineRecord->notes)->toBe('Prueba de cuarentena anti-XSS');

    // 10.2 Liberar de cuarentena
    $updatePayload = [
        'quarantine_id' => $quarantineRecord->quarantine_id,
        'quantity'      => $quarantineQty,
    ];

    $updateResponse = $this->actingAs($this->user)->putJson('/updateQuarantine', $updatePayload);

    echo "\n\n>>> LLAMADA: PUT /updateQuarantine\n";
    echo "HTTP STATUS: " . $updateResponse->status() . "\n";

    $updateResponse->assertStatus(201);
    $this->inventoryItem->refresh();
    expect((float) $this->inventoryItem->stock)->toEqual((float) $initialStock);
});

test('11. API POST /makeTransaction (Transacción de Entrada - Input)', function () {
    $transBatch = 'BATCH-IN-TRANS-' . uniqid();
    $payload = [
        'type'            => 'Input',
        'security_seal'   => 0,
        'supplier'        => $this->supplier->supplier_id,
        'operator'        => 'Juan Perez Chofer',
        'comments'        => 'Entrada de materia prima por prueba',
        'product_id'      => [$this->product->product_id],
        'quantity'        => [100],
        'warehouse_batch' => [$transBatch],
        'location_id'     => [$this->location->location_id],
        'concept_id'      => [$this->concept->concept_id],
        'weight_per_unit' => [25],
    ];

    $response = $this->actingAs($this->user)->postJson('/makeTransaction', $payload);

    echo "\n\n>>> LLAMADA: POST /makeTransaction (Input)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('message'))->toBe('Success');
    expect($response->json('success'))->toBeTrue();

    $this->assertDatabaseHas('inputs', [
        'supplier_id' => $this->supplier->supplier_id,
        'operator'    => 'Juan Perez Chofer',
    ]);

    $this->assertDatabaseHas('inventory', [
        'batch' => $transBatch,
        'stock' => 100,
    ]);
});

test('12. API POST /makeTransaction (Transacción de Salida - Output con decremento)', function () {
    $stockBefore = $this->inventoryItem->stock;
    $outQty = 30.0;

    $payload = [
        'type'            => 'Output',
        'security_seal'   => 0,
        'customer'        => $this->customer->customer_id,
        'vendedor'        => 'Vendedor Test',
        'comments'        => 'Salida a cliente',
        'product_id'      => [$this->product->product_id],
        'quantity'        => [$outQty],
        'warehouse_batch' => [$this->inventoryItem->inventory_id],
    ];

    $response = $this->actingAs($this->user)->postJson('/makeTransaction', $payload);

    echo "\n\n>>> LLAMADA: POST /makeTransaction (Output)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('message'))->toBe('Success');

    $this->inventoryItem->refresh();
    expect((float) $this->inventoryItem->stock)->toEqual((float) ($stockBefore - $outQty));

    $this->assertDatabaseHas('outputs', [
        'customer_id' => $this->customer->customer_id,
        'vendedor'    => 'Vendedor Test',
    ]);
});

test('13. API Actualizar fecha y comentarios (updateDate & updateComment)', function () {
    $input = Input::create([
        'supplier_id'   => $this->supplier->supplier_id,
        'security_seal' => 0,
        'comments'      => 'Comentario inicial',
    ]);

    // 13.1 Update Date
    $dateResponse = $this->actingAs($this->user)->putJson('/updateDate', [
        'type'       => 'input',
        'id'         => $input->input_id,
        'updated_at' => '2026-05-15',
    ]);

    echo "\n\n>>> LLAMADA: PUT /updateDate\n";
    echo "HTTP STATUS: " . $dateResponse->status() . "\n";

    $dateResponse->assertStatus(201);
    expect($dateResponse->json('message'))->toBe('Operation successfuly make it');

    // 13.2 Update Comment con Anti-XSS
    $commentResponse = $this->actingAs($this->user)->putJson("/updateComment/input/{$input->input_id}", [
        'comments' => '<b>Comentario actualizado limpio</b>',
    ]);

    echo "\n\n>>> LLAMADA: PUT /updateComment\n";
    echo "HTTP STATUS: " . $commentResponse->status() . "\n";

    $commentResponse->assertStatus(200);
    expect($commentResponse->json('message'))->toBe('Updated');

    $input->refresh();
    expect($input->comments)->toBe('Comentario actualizado limpio');
});

test('14. API POST /inventory/pallets/{id}/accept (Aceptación de tarima en almacén)', function () {
    $pallet = Pallet::create([
        'pallet_number'    => 'PAL-ACCPT-' . rand(100, 999),
        'color_type'       => 'Natural',
        'current_sacks'    => 40,
        'status'           => 'Cerrada',
    ]);
    $pallet->inventory_status = 'Enviada';
    $pallet->save();

    $payload = [
        'location_id'     => $this->location->location_id,
        'quantity'        => 40,
        'weight_per_unit' => 25,
        'final_weight'    => 1000,
        'comments'        => 'Ingreso de tarima en prueba',
    ];

    $response = $this->actingAs($this->user)->postJson("/inventory/pallets/{$pallet->pallet_id}/accept", $payload);

    echo "\n\n>>> LLAMADA: POST /inventory/pallets/{id}/accept\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();

    $pallet->refresh();
    expect($pallet->inventory_status)->toBe('Ingresada');

    $this->assertDatabaseHas('inventory', [
        'batch' => $pallet->pallet_number,
        'stock' => 1000,
    ]);
});
