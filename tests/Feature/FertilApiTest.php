<?php

use App\Models\FertilInventory;
use App\Models\FertilInventoryMovement;
use App\Models\FertilProduction;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->production = FertilProduction::create([
        'fecha_preparacion' => '2026-09-28',
        'kg_preparados'     => 1500.50,
        'fecha_ensacado'    => '2026-09-29',
        'kg_ensacados'      => 1450.00,
        'num_sacos'         => 58,
        'descripcion'       => 'Lote de fertilizante granulado estándar',
    ]);

    $this->inventory = FertilInventory::create([
        'producto_descripcion' => 'Urea Agrícola 46%',
        'cantidad'             => 100.0,
        'unidad'               => 'Sacos 50kg',
        'stock_min'            => 20.0,
    ]);

    FertilInventoryMovement::create([
        'fertil_inventory_id' => $this->inventory->fertil_inventory_id,
        'tipo'                => 'Entrada',
        'cantidad'            => 100.0,
    ]);
});

test('1. Web GET /production/fertil (Retorna vista Blade production.fertil.index)', function () {
    $response = $this->actingAs($this->user)->get('/production/fertil');

    echo "\n\n>>> LLAMADA: GET /production/fertil (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('production.fertil.index');
    $response->assertViewHas(['productions', 'inventories', 'db_products']);
});

test('2. API GET /production/fertil (JSON API response con producciones, inventarios y productos)', function () {
    $response = $this->actingAs($this->user)->getJson('/production/fertil');

    echo "\n\n>>> LLAMADA: GET /production/fertil (JSON)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data'))->toHaveKeys(['productions', 'inventories', 'db_products']);
    expect(count($response->json('data.productions')))->toBeGreaterThanOrEqual(1);
    expect(count($response->json('data.inventories')))->toBeGreaterThanOrEqual(1);
});

test('3. API POST /production/fertil/productions (Creación exitosa con sanitización anti-XSS)', function () {
    $payload = [
        'fecha_preparacion' => '2026-09-28',
        'kg_preparados'     => 2000,
        'fecha_ensacado'    => '2026-09-29',
        'kg_ensacados'      => 1980,
        'num_sacos'         => 80,
        'descripcion'       => '<b>Producción especial para exportación</b>',
    ];

    $response = $this->actingAs($this->user)->postJson('/production/fertil/productions', $payload);

    echo "\n\n>>> LLAMADA: POST /production/fertil/productions (Creación exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('message'))->toBe('Registro agregado correctamente.');

    $created = $response->json('data');
    expect($created['descripcion'])->toBe('Producción especial para exportación');
    expect($created['kg_preparados'])->toEqual(2000);
    expect($created['num_sacos'])->toBe(80);

    $this->assertDatabaseHas('fertil_productions', [
        'fertil_production_id' => $created['fertil_production_id'],
        'descripcion'          => 'Producción especial para exportación',
        'num_sacos'            => 80,
    ]);
});

test('4. API POST /production/fertil/productions (Error de validación 422 por valores inválidos)', function () {
    $payload = [
        'fecha_preparacion' => 'fecha-invalida',
        'kg_preparados'     => -50,
        'num_sacos'         => -5,
    ];

    $response = $this->actingAs($this->user)->postJson('/production/fertil/productions', $payload);

    echo "\n\n>>> LLAMADA: POST /production/fertil/productions (422 Error de validación)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['fecha_preparacion', 'kg_preparados', 'num_sacos']);
});

test('5. API PUT /production/fertil/productions/{id} (Actualización exitosa de producción)', function () {
    $payload = [
        'fecha_preparacion' => '2026-09-28',
        'kg_preparados'     => 1600.0,
        'fecha_ensacado'    => '2026-09-30',
        'kg_ensacados'      => 1580.0,
        'num_sacos'         => 64,
        'descripcion'       => 'Lote corregido y verificado',
    ];

    $response = $this->actingAs($this->user)->putJson("/production/fertil/productions/{$this->production->fertil_production_id}", $payload);

    echo "\n\n>>> LLAMADA: PUT /production/fertil/productions/{id} (Actualización exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.descripcion'))->toBe('Lote corregido y verificado');
    expect($response->json('data.num_sacos'))->toBe(64);

    $this->assertDatabaseHas('fertil_productions', [
        'fertil_production_id' => $this->production->fertil_production_id,
        'descripcion'          => 'Lote corregido y verificado',
        'num_sacos'            => 64,
    ]);
});

test('6. API PUT /production/fertil/productions/999999 (404 al actualizar producción inexistente)', function () {
    $payload = [
        'descripcion' => 'Producción fantasma',
    ];

    $response = $this->actingAs($this->user)->putJson('/production/fertil/productions/999999', $payload);

    echo "\n\n>>> LLAMADA: PUT /production/fertil/productions/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('7. API DELETE /production/fertil/productions/{id} (Eliminación exitosa de producción)', function () {
    $response = $this->actingAs($this->user)->deleteJson("/production/fertil/productions/{$this->production->fertil_production_id}");

    echo "\n\n>>> LLAMADA: DELETE /production/fertil/productions/{id} (Eliminación exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('message'))->toBe('Registro eliminado correctamente.');

    $this->assertDatabaseMissing('fertil_productions', [
        'fertil_production_id' => $this->production->fertil_production_id,
    ]);
});

test('8. API DELETE /production/fertil/productions/999999 (404 al eliminar producción inexistente)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/production/fertil/productions/999999');

    echo "\n\n>>> LLAMADA: DELETE /production/fertil/productions/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('9. API POST /production/fertil/inventories (Creación de producto e inserción de movimiento Entrada)', function () {
    $payload = [
        'producto_descripcion' => '<b>Sulfato de Amonio</b>',
        'cantidad'             => 50.0,
        'unidad'               => 'Sacos 25kg',
        'stock_min'            => 10.0,
    ];

    $response = $this->actingAs($this->user)->postJson('/production/fertil/inventories', $payload);

    echo "\n\n>>> LLAMADA: POST /production/fertil/inventories (Creación exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('message'))->toBe('Producto agregado correctamente.');

    $created = $response->json('data');
    expect($created['producto_descripcion'])->toBe('Sulfato de Amonio');
    expect($created['cantidad'])->toEqual(50.0);

    $this->assertDatabaseHas('fertil_inventories', [
        'fertil_inventory_id'  => $created['fertil_inventory_id'],
        'producto_descripcion' => 'Sulfato de Amonio',
        'cantidad'             => 50.0,
    ]);

    $this->assertDatabaseHas('fertil_inventory_movements', [
        'fertil_inventory_id' => $created['fertil_inventory_id'],
        'tipo'                => 'Entrada',
        'cantidad'            => 50.0,
    ]);
});

test('10. API POST /production/fertil/inventories (422 Validación sin producto_descripcion)', function () {
    $payload = [
        'cantidad'  => 10,
        'stock_min' => 5,
    ];

    $response = $this->actingAs($this->user)->postJson('/production/fertil/inventories', $payload);

    echo "\n\n>>> LLAMADA: POST /production/fertil/inventories (422 Validation Error)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['producto_descripcion']);
});

test('11. API PUT /production/fertil/inventories/{id} (Actualización de stock y movimiento de Ajuste)', function () {
    $payload = [
        'producto_descripcion' => 'Urea Agrícola 46% Modificada',
        'cantidad'             => 80.0, // Bajó de 100 a 80 -> Ajuste de 20
        'unidad'               => 'Sacos 50kg',
        'stock_min'            => 20.0,
    ];

    $response = $this->actingAs($this->user)->putJson("/production/fertil/inventories/{$this->inventory->fertil_inventory_id}", $payload);

    echo "\n\n>>> LLAMADA: PUT /production/fertil/inventories/{id} (Ajuste de inventario)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.cantidad'))->toEqual(80.0);

    $this->assertDatabaseHas('fertil_inventory_movements', [
        'fertil_inventory_id' => $this->inventory->fertil_inventory_id,
        'tipo'                => 'Ajuste',
        'cantidad'            => 20.0,
    ]);
});

test('12. API POST /production/fertil/inventories/{id}/output (Salida de inventario normal)', function () {
    $payload = [
        'cantidad_salida' => 15.0,
    ];

    $response = $this->actingAs($this->user)->postJson("/production/fertil/inventories/{$this->inventory->fertil_inventory_id}/output", $payload);

    echo "\n\n>>> LLAMADA: POST /production/fertil/inventories/{id}/output (Salida normal)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('alert'))->toBeFalse();
    expect($response->json('data.cantidad'))->toEqual(85.0);

    $this->assertDatabaseHas('fertil_inventories', [
        'fertil_inventory_id' => $this->inventory->fertil_inventory_id,
        'cantidad'            => 85.0,
    ]);

    $this->assertDatabaseHas('fertil_inventory_movements', [
        'fertil_inventory_id' => $this->inventory->fertil_inventory_id,
        'tipo'                => 'Salida',
        'cantidad'            => 15.0,
    ]);
});

test('13. API POST /production/fertil/inventories/{id}/output (Salida que alcanza stock mínimo y genera alerta)', function () {
    $payload = [
        'cantidad_salida' => 85.0, // Quedan 15.0 <= stock_min de 20.0
    ];

    $response = $this->actingAs($this->user)->postJson("/production/fertil/inventories/{$this->inventory->fertil_inventory_id}/output", $payload);

    echo "\n\n>>> LLAMADA: POST /production/fertil/inventories/{id}/output (Alerta de stock mínimo)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('alert'))->toBeTrue();
    expect($response->json('message'))->toContain('ALERTA');
    expect($response->json('data.cantidad'))->toEqual(15.0);
});

test('14. API POST /production/fertil/inventories/{id}/output (Error 400 por cantidad de salida mayor al stock disponible)', function () {
    $payload = [
        'cantidad_salida' => 999.0,
    ];

    $response = $this->actingAs($this->user)->postJson("/production/fertil/inventories/{$this->inventory->fertil_inventory_id}/output", $payload);

    echo "\n\n>>> LLAMADA: POST /production/fertil/inventories/{id}/output (400 Excede stock)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(400);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('message'))->toContain('La cantidad de salida no puede ser mayor a la cantidad en stock.');
});

test('15. API GET /production/fertil/inventories/{id}/movements (Listado de movimientos)', function () {
    $response = $this->actingAs($this->user)->getJson("/production/fertil/inventories/{$this->inventory->fertil_inventory_id}/movements");

    echo "\n\n>>> LLAMADA: GET /production/fertil/inventories/{id}/movements\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json())->toBeArray();
    expect(count($response->json()))->toBeGreaterThanOrEqual(1);
    expect($response->json('0.tipo'))->toBe('Entrada');
    expect($response->json('0.cantidad'))->toEqual(100.0);
});

test('16. API DELETE /production/fertil/inventories/{id} (Eliminación exitosa de inventario)', function () {
    $response = $this->actingAs($this->user)->deleteJson("/production/fertil/inventories/{$this->inventory->fertil_inventory_id}");

    echo "\n\n>>> LLAMADA: DELETE /production/fertil/inventories/{id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('message'))->toBe('Producto eliminado correctamente.');

    $this->assertDatabaseMissing('fertil_inventories', [
        'fertil_inventory_id' => $this->inventory->fertil_inventory_id,
    ]);
});

test('17. API POST /production/fertil/request-materials (Creación de solicitud de materiales con partidas)', function () {
    $payload = [
        'applicant_name' => 'Ing. Juan Pérez',
        'comments'       => '<b>Requerimiento urgente de aditivos para lote 45</b>',
        'products'       => [
            [
                'name'     => 'Fosfato Diamónico',
                'quantity' => 12.5,
            ],
            [
                'name'     => 'Sulfato de Magnesio',
                'quantity' => 5.0,
            ],
        ],
    ];

    $response = $this->actingAs($this->user)->postJson('/production/fertil/request-materials', $payload);

    echo "\n\n>>> LLAMADA: POST /production/fertil/request-materials (Creación exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('message'))->toBe('Solicitud enviada a almacén correctamente.');

    $created = $response->json('data');
    expect($created['area'])->toBe('fertil');
    expect($created['applicant_name'])->toBe('Ing. Juan Pérez');
    expect($created['comments'])->toBe('Requerimiento urgente de aditivos para lote 45');
    expect(count($created['items']))->toBe(2);

    $this->assertDatabaseHas('production_material_requests', [
        'id'             => $created['id'],
        'area'           => 'fertil',
        'applicant_name' => 'Ing. Juan Pérez',
        'comments'       => 'Requerimiento urgente de aditivos para lote 45',
    ]);

    $this->assertDatabaseHas('production_material_request_items', [
        'request_id'   => $created['id'],
        'product_name' => 'Fosfato Diamónico',
        'quantity'     => 12.5,
    ]);
});

test('18. API POST /production/fertil/request-materials (422 Validación por campos faltantes)', function () {
    $payload = [
        'applicant_name' => '',
        'products'       => [],
    ];

    $response = $this->actingAs($this->user)->postJson('/production/fertil/request-materials', $payload);

    echo "\n\n>>> LLAMADA: POST /production/fertil/request-materials (422 Validation Error)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['applicant_name', 'products']);
});
