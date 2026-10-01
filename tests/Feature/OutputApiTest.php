<?php

use App\Models\Customer;
use App\Models\Output;
use App\Models\Product;
use App\Models\ProductOutputs;
use App\Models\TransportLine;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->customer = Customer::first() ?? Customer::create([
        'name' => 'Cliente Test ' . uniqid(),
    ]);

    $this->tLine = TransportLine::first() ?? TransportLine::create([
        'name' => 'Transportes Output ' . uniqid(),
    ]);

    $this->product = Product::first() ?? Product::create([
        'name'        => 'Producto Output ' . uniqid(),
        'unit'        => 'kg',
        'category_id' => 1,
    ]);
});

test('1. puede listar salidas de almacén (GET /outputs)', function () {
    $output = Output::create([
        'operator'          => 'Operador Test',
        'license_number'    => 'LIC-12345',
        'security_seal'     => 1,
        'unit_plates'       => 'PLK-123',
        'customer_id'       => $this->customer->customer_id,
        'transport_line_id' => $this->tLine->transport_line_id,
        'vendedor'          => 'Vendedor Test',
    ]);

    $response = $this->actingAs($this->user)->getJson('/outputs');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Salidas de almacén obtenidas correctamente.',
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'output_id',
                    'operator',
                    'vendedor',
                    'customer_id',
                    'transport_line_id',
                ],
            ],
        ]);

    $output->delete();
});

test('2. puede registrar una salida de almacén con sus productos (POST /outputs)', function () {
    $payload = [
        'operator'             => 'Carlos Chofer',
        'license_number'       => 'LIC-998877',
        'security_seal'        => 1,
        'security_seal_number' => 'SEAL-001',
        'unit_plates'          => 'PL-7788',
        'trailer_plates'       => 'TR-9900',
        'comments'             => 'Entrega urgente',
        'customer_id'          => $this->customer->customer_id,
        'transport_line_id'    => $this->tLine->transport_line_id,
        'vendedor'             => 'Ana Ventas',
        'product_id'           => [$this->product->product_id],
        'quantity'             => [150.5],
        'label_batch'          => ['LOTE-TEST-01'],
    ];

    $response = $this->actingAs($this->user)->postJson('/outputs', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'operator'             => 'Carlos Chofer',
                'license_number'       => 'LIC-998877',
                'security_seal_number' => 'SEAL-001',
                'customer_id'          => $this->customer->customer_id,
            ],
        ]);

    $this->assertDatabaseHas('outputs', [
        'operator'    => 'Carlos Chofer',
        'customer_id' => $this->customer->customer_id,
    ]);

    $createdOutputId = $response->json('data.output_id');
    $this->assertDatabaseHas('product_outputs', [
        'output_id'   => $createdOutputId,
        'product_id'  => $this->product->product_id,
        'quantity'    => 150.5,
        'label_batch' => 'LOTE-TEST-01',
    ]);
});

test('3. sanitiza campos contra Stored-XSS y normaliza comas en cantidades', function () {
    $payload = [
        'operator'             => '<script>alert("xss")</script>Pedro Perez',
        'license_number'       => '<b>LIC-XSS</b>',
        'security_seal'        => 1,
        'security_seal_number' => 'SEAL-XSS',
        'unit_plates'          => 'PL-XSS',
        'customer_id'          => $this->customer->customer_id,
        'transport_line_id'    => $this->tLine->transport_line_id,
        'vendedor'             => '<i>Vendedor XSS</i>',
        'product_id'           => [$this->product->product_id],
        'quantity'             => ['45,75'],
        'label_batch'          => ['<u>LOTE-XSS</u>'],
    ];

    $response = $this->actingAs($this->user)->postJson('/outputs', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('outputs', [
        'operator' => 'alert("xss")Pedro Perez',
        'vendedor' => 'Vendedor XSS',
    ]);

    $createdOutputId = $response->json('data.output_id');
    $this->assertDatabaseHas('product_outputs', [
        'output_id'   => $createdOutputId,
        'quantity'    => 45.75,
        'label_batch' => 'LOTE-XSS',
    ]);
});

test('4. valida campos obligatorios y retorna 422', function () {
    $response = $this->actingAs($this->user)->postJson('/outputs', [
        'operator' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['operator', 'customer_id', 'transport_line_id', 'product_id', 'quantity']);
});

test('5. puede consultar el detalle de una salida para modal (GET /outputs/{id})', function () {
    $output = Output::create([
        'operator'          => 'Operador Detalle',
        'license_number'    => 'LIC-777',
        'security_seal'     => 1,
        'unit_plates'       => 'PL-777',
        'customer_id'       => $this->customer->customer_id,
        'transport_line_id' => $this->tLine->transport_line_id,
        'vendedor'          => 'Vendedor 1',
    ]);

    ProductOutputs::create([
        'output_id'       => $output->output_id,
        'product_id'      => $this->product->product_id,
        'quantity'        => 80.0,
        'warehouse_batch' => 'WH-BATCH-DETALLE',
        'label_batch'     => 'BATCH-DETALLE',
    ]);

    $response = $this->actingAs($this->user)->getJson("/outputs/{$output->output_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'output'  => [
                'output_id' => $output->output_id,
                'operator'  => 'Operador Detalle',
            ],
        ])
        ->assertJsonStructure([
            'output',
            'products' => [
                '*' => [
                    'product_id',
                    'quantity',
                    'label_batch',
                    'name',
                ],
            ],
        ]);

    ProductOutputs::where('output_id', $output->output_id)->delete();
    $output->delete();
});

test('6. retorna 404 al consultar salida inexistente (GET /outputs/{id})', function () {
    $response = $this->actingAs($this->user)->getJson('/outputs/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Salida de almacén no encontrada.',
        ]);
});

test('7. puede actualizar una salida de almacén y sus partidas de productos (PUT /outputs/{id})', function () {
    $output = Output::create([
        'operator'          => 'Operador Inicial',
        'license_number'    => 'LIC-INIT',
        'security_seal'     => 0,
        'unit_plates'       => 'PL-INIT',
        'customer_id'       => $this->customer->customer_id,
        'transport_line_id' => $this->tLine->transport_line_id,
        'vendedor'          => 'Vendedor Inicial',
    ]);

    ProductOutputs::create([
        'output_id'       => $output->output_id,
        'product_id'      => $this->product->product_id,
        'quantity'        => 20.0,
        'warehouse_batch' => 'WH-BATCH-INIT',
        'label_batch'     => 'BATCH-INIT',
    ]);

    $updatePayload = [
        'operator'             => 'Operador Modificado',
        'license_number'       => 'LIC-MOD',
        'security_seal'        => 1,
        'security_seal_number' => 'SEAL-MOD',
        'unit_plates'          => 'PL-MOD',
        'customer_id'          => $this->customer->customer_id,
        'transport_line_id'    => $this->tLine->transport_line_id,
        'vendedor'             => 'Vendedor Modificado',
        'product_id'           => [$this->product->product_id],
        'quantity'             => [35.0],
        'label_batch'          => ['BATCH-MOD'],
    ];

    $response = $this->actingAs($this->user)->putJson("/outputs/{$output->output_id}", $updatePayload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Operation successfuly make it',
            'data'    => [
                'output_id' => $output->output_id,
                'operator'  => 'Operador Modificado',
            ],
        ]);

    $this->assertDatabaseHas('outputs', [
        'output_id' => $output->output_id,
        'operator'  => 'Operador Modificado',
    ]);

    $this->assertDatabaseHas('product_outputs', [
        'output_id'   => $output->output_id,
        'product_id'  => $this->product->product_id,
        'quantity'    => 35.0,
        'label_batch' => 'BATCH-MOD',
    ]);

    ProductOutputs::where('output_id', $output->output_id)->delete();
    $output->delete();
});

test('8. retorna 404 al intentar actualizar salida inexistente (PUT /outputs/{id})', function () {
    $payload = [
        'operator'          => 'Fantasma',
        'license_number'    => 'LIC-0',
        'security_seal'     => 0,
        'unit_plates'       => 'PL-0',
        'customer_id'       => $this->customer->customer_id,
        'transport_line_id' => $this->tLine->transport_line_id,
        'vendedor'          => 'Vendedor 0',
        'product_id'        => [$this->product->product_id],
        'quantity'          => [1.0],
        'label_batch'       => ['BATCH-0'],
    ];

    $response = $this->actingAs($this->user)->putJson('/outputs/99999999', $payload);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Salida de almacén no encontrada.',
        ]);
});

test('9. puede eliminar una salida de almacén (DELETE /outputs/{id})', function () {
    $output = Output::create([
        'operator'          => 'Operador Borrable',
        'license_number'    => 'LIC-DEL',
        'security_seal'     => 0,
        'unit_plates'       => 'PL-DEL',
        'customer_id'       => $this->customer->customer_id,
        'transport_line_id' => $this->tLine->transport_line_id,
        'vendedor'          => 'Vendedor Del',
    ]);

    ProductOutputs::create([
        'output_id'       => $output->output_id,
        'product_id'      => $this->product->product_id,
        'quantity'        => 10.0,
        'warehouse_batch' => 'WH-BATCH-DEL',
        'label_batch'     => 'BATCH-DEL',
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/outputs/{$output->output_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Output deleted successfully',
        ]);

    $this->assertDatabaseMissing('outputs', [
        'output_id' => $output->output_id,
    ]);

    $this->assertDatabaseMissing('product_outputs', [
        'output_id' => $output->output_id,
    ]);
});

test('10. retorna 404 al intentar eliminar salida inexistente (DELETE /outputs/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson('/outputs/99999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Output not found',
        ]);
});
