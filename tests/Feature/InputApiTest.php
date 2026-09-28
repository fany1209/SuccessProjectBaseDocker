<?php

use App\Models\Input;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductInputs;
use App\Models\Supplier;
use App\Models\TransportLine;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    Permission::findOrCreate('inventory.show', 'web');
    $this->user->givePermissionTo('inventory.show');

    $this->supplier = Supplier::first() ?? Supplier::create([
        'name'          => 'PROVEEDOR INDUSTRIAL DEMO S.A.',
        'supplier_code' => 'SUP-TEST-' . rand(100, 999),
        'contact'       => 'Contacto Proveedor',
        'email'         => 'proveedor@demo.com',
    ]);

    $this->product = Product::first() ?? Product::create([
        'name'        => 'PRODUCTO MATERIA PRIMA TEST',
        'unit'        => 'KG',
        'cost'        => 50.0,
        'description' => 'Producto de prueba para almacén',
    ]);

    $this->transportLine = TransportLine::first();

    $this->input = Input::create([
        'supplier_id'          => $this->supplier->supplier_id,
        'transport_line_id'    => $this->transportLine ? $this->transportLine->transport_line_id : null,
        'operator'             => 'Juan Perez Conductor',
        'license_number'       => 'LIC-12345-MEX',
        'security_seal'        => 1,
        'security_seal_number' => 'SEAL-987654',
        'unit_plates'          => 'ABC-123',
        'trailer_plates'       => 'TRL-999',
        'comments'             => 'Entrada de prueba automatizada',
    ]);

    $this->batch = 'BATCH-TEST-' . rand(1000, 9999);

    $this->inventory = Inventory::create([
        'stock'      => 100.0,
        'batch'      => $this->batch,
        'product_id' => $this->product->product_id,
    ]);

    $this->productInput = ProductInputs::create([
        'product_id'      => $this->product->product_id,
        'input_id'        => $this->input->input_id,
        'quantity'        => 100.0,
        'warehouse_batch' => $this->batch,
    ]);
});

afterEach(function () {
    if (isset($this->productInput) && $this->productInput->exists) {
        ProductInputs::where('id', $this->productInput->id)->delete();
    }
    if (isset($this->inventory) && $this->inventory->exists) {
        Inventory::where('inventory_id', $this->inventory->inventory_id)->delete();
    }
    if (isset($this->input) && $this->input->exists) {
        Input::where('input_id', $this->input->input_id)->delete();
    }
});

test('1. API GET /inputs (Listado general de entradas de almacén)', function () {
    $response = $this->actingAs($this->user)->getJson('/inputs');

    echo "\n\n>>> LLAMADA: GET /inputs (List Inputs)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
    echo "TOTAL ENTRADAS: " . count($response->json('data') ?? []) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data'))->toBeArray();
});

test('2. API GET /inputs/{id} (Consulta de entrada con productos y formato compatible con modal)', function () {
    $response = $this->actingAs($this->user)->getJson("/inputs/{$this->input->input_id}");

    echo "\n\n>>> LLAMADA: GET /inputs/{id} (Show Input)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    // Verificación de compatibilidad con editTransaction.blade.php
    expect($response->json('input'))->not->toBeNull();
    expect($response->json('products'))->toBeArray();
    expect($response->json('input.input_id'))->toBe($this->input->input_id);
    expect($response->json('input.supplier_id'))->toBe($this->supplier->supplier_id);
});

test('3. API GET /inputs/{id} (404 al consultar entrada inexistente)', function () {
    $response = $this->actingAs($this->user)->getJson('/inputs/999999');

    echo "\n\n>>> LLAMADA: GET /inputs/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
});

test('4. API POST /inputs (Creación de entrada con transacciones de inventario)', function () {
    $newBatch = 'BATCH-NEW-' . rand(1000, 9999);
    $payload = [
        'supplier_id'          => $this->supplier->supplier_id,
        'operator'             => 'Carlos Operador Demo',
        'license_number'       => 'LIC-OPER-777',
        'security_seal'        => 1,
        'security_seal_number' => 'SEAL-NEW-12345',
        'unit_plates'          => 'UN-888',
        'trailer_plates'       => 'TR-777',
        'comments'             => 'Carga recibida conforme',
        'product_id'           => [$this->product->product_id],
        'stock'                => [500.0],
        'warehouse_batch'      => [$newBatch],
    ];

    $response = $this->actingAs($this->user)->postJson('/inputs', $payload);

    echo "\n\n>>> LLAMADA: POST /inputs (Create Input)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.input_id'))->toBeGreaterThan(0);

    // Limpieza
    $createdId = $response->json('data.input_id');
    ProductInputs::where('input_id', $createdId)->delete();
    Inventory::where('batch', $newBatch)->delete();
    Input::where('input_id', $createdId)->delete();
});

test('5. API POST /inputs (Validación 422 ante campos requeridos faltantes)', function () {
    $response = $this->actingAs($this->user)->postJson('/inputs', []);

    echo "\n\n>>> LLAMADA: POST /inputs (422 Validation Error)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['supplier_id', 'product_id', 'stock', 'warehouse_batch']);
});

test('6. API PUT /inputs/{id} (Actualización de entrada y stock de lote con bloqueo pesimista)', function () {
    $payload = [
        'supplier_id'          => $this->supplier->supplier_id,
        'operator'             => 'Juan Perez Editado',
        'license_number'       => 'LIC-REV-999',
        'security_seal'        => 1,
        'security_seal_number' => 'SEAL-REV-001',
        'unit_plates'          => 'ABC-123-MOD',
        'trailer_plates'       => 'TRL-999-MOD',
        'comments'             => 'Observaciones actualizadas',
        'warehouse_batch'      => [$this->batch],
        'quantity'             => [150.5],
    ];

    $response = $this->actingAs($this->user)->putJson("/inputs/{$this->input->input_id}", $payload);

    echo "\n\n>>> LLAMADA: PUT /inputs/{id} (Update Input)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('message'))->toBe('Operation successfuly make it');

    // Verificar actualización en inventario y productos
    expect((float) Inventory::where('batch', $this->batch)->value('stock'))->toBe(150.5);
    expect((float) ProductInputs::where('input_id', $this->input->input_id)->value('quantity'))->toBe(150.5);
});

test('7. API PUT /inputs/{id} (Normalización de decimales con coma a punto)', function () {
    $payload = [
        'supplier_id'     => $this->supplier->supplier_id,
        'warehouse_batch' => [$this->batch],
        'quantity'        => ['75,25'], // Coma en lugar de punto
    ];

    $response = $this->actingAs($this->user)->putJson("/inputs/{$this->input->input_id}", $payload);

    echo "\n\n>>> LLAMADA: PUT /inputs/{id} (Comma to Dot Decimal Normalization)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect((float) Inventory::where('batch', $this->batch)->value('stock'))->toBe(75.25);
});

test('8. API PUT /inputs/{id} (404 al actualizar entrada inexistente)', function () {
    $payload = [
        'supplier_id'     => $this->supplier->supplier_id,
        'warehouse_batch' => ['BATCH-NONEXISTENT'],
        'quantity'        => [10],
    ];

    $response = $this->actingAs($this->user)->putJson('/inputs/999999', $payload);

    echo "\n\n>>> LLAMADA: PUT /inputs/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
});

test('9. API DELETE /inputs/{id} (Eliminación segura de entrada y sus relaciones)', function () {
    $tempInput = Input::create([
        'supplier_id'   => $this->supplier->supplier_id,
        'operator'      => 'Operador Temporal',
        'security_seal' => 0,
    ]);
    ProductInputs::create([
        'product_id'      => $this->product->product_id,
        'input_id'        => $tempInput->input_id,
        'quantity'        => 50.0,
        'warehouse_batch' => 'BATCH-TEMP-' . rand(1000, 9999),
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/inputs/{$tempInput->input_id}");

    echo "\n\n>>> LLAMADA: DELETE /inputs/{id} (Delete Input)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect(Input::where('input_id', $tempInput->input_id)->exists())->toBeFalse();
    expect(ProductInputs::where('input_id', $tempInput->input_id)->exists())->toBeFalse();
});

test('10. API DELETE /inputs/{id} (404 al eliminar entrada inexistente)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/inputs/999999');

    echo "\n\n>>> LLAMADA: DELETE /inputs/999999 (404 Delete Not Found)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
});

test('11. API GET /inputs (Redirección o bloqueo si el usuario no tiene permisos)', function () {
    $unauthorizedUser = User::factory()->create();

    $response = $this->actingAs($unauthorizedUser)->getJson('/inputs');

    echo "\n\n>>> LLAMADA SIN PERMISO: GET /inputs\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(403);

    $unauthorizedUser->delete();
});
