<?php

use App\Models\Cli;
use App\Models\Concept;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductQr;
use App\Models\User;
use App\Models\Warehouse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->unauthorizedUser = User::factory()->create();

    $perm = Permission::firstOrCreate(
        ['name' => 'product_inf.show', 'guard_name' => 'web'],
        ['description' => 'Acceso y visualización al módulo Product Inf']
    );
    $this->user->givePermissionTo($perm);

    $this->warehouse = Warehouse::firstOrCreate(
        ['name' => 'Almacén Test ' . uniqid()]
    );

    $this->location = Location::firstOrCreate(
        ['name' => 'LOC-' . rand(1000, 9999)],
        ['warehouse_id' => $this->warehouse->warehouse_id]
    );

    $this->concept = Concept::firstOrCreate(
        ['name' => 'Concepto Test ' . uniqid()]
    );

    $this->product = Product::first() ?? Product::create([
        'name'        => 'Producto Test ' . uniqid(),
        'unit'        => 'kg',
        'category_id' => 1,
    ]);

    $this->inventory = Inventory::create([
        'product_id' => $this->product->product_id,
        'batch'      => 'LOT-' . uniqid(),
        'stock'      => 1000.0,
    ]);

    $this->cli = Cli::create([
        'bag_number'      => 'BARC-' . rand(10000, 99999),
        'protein'         => 15.0,
        'quantity'        => 5,
        'weight_per_unit' => 50.0,
        'net_weight'      => 250.0,
        'inventory_id'    => $this->inventory->inventory_id,
        'location_id'     => $this->location->location_id,
        'concept_id'      => $this->concept->concept_id,
    ]);
});

afterEach(function () {
    if (isset($this->cli) && $this->cli->exists) {
        ProductQr::where('cli_id', $this->cli->cli_id)->delete();
        $this->cli->delete();
    }
});

test('1. retorna 403 al acceder a product-inf sin permiso ni rol autorizado', function () {
    $response = $this->actingAs($this->unauthorizedUser)
        ->get(route('product-inf.index'));

    $response->assertStatus(403);
});

test('2. carga correctamente la vista de product-inf para usuario autorizado', function () {
    $response = $this->actingAs($this->user)
        ->get(route('product-inf.index'));

    $response->assertStatus(200);
    $response->assertSee('Product Inf');
    $response->assertSee('Crear Nuevo Código QR');
});

test('3. consulta existencias de una ubicación en el mapa correctamente', function () {
    $response = $this->actingAs($this->user)
        ->getJson(route('product-inf.location-products', ['location' => $this->location->name]));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ]);

    $this->assertNotEmpty($response->json('products'));
});

test('4. valida que la descripción a detalle sea obligatoria al generar QR', function () {
    $response = $this->actingAs($this->user)
        ->postJson(route('product-inf.store'), [
            'cli_id' => $this->cli->cli_id,
            'description' => '', // Inválido
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['description']);
});

test('5. genera código QR permanente con UUID estático y asocia a cli', function () {
    $response = $this->actingAs($this->user)
        ->postJson(route('product-inf.store'), [
            'cli_id'      => $this->cli->cli_id,
            'description' => 'Inspección de saco completada, tarima en excelentes condiciones.',
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ]);

    $data = $response->json('data');
    $this->assertNotEmpty($data['uuid']);
    $this->assertNotEmpty($data['scan_url']);
    $this->assertStringContainsString('/rack/' . $data['uuid'], $data['scan_url']);
    $this->assertNotEmpty($data['qr_svg']);

    // Verificar en BD
    $this->cli->refresh();
    $this->assertEquals($data['uuid'], $this->cli->uuid);
    $this->assertEquals('Inspección de saco completada, tarima en excelentes condiciones.', $this->cli->detailed_description);
});

test('6. ruta pública /rack/{uuid} muestra datos en tiempo real y código QR', function () {
    // Generar primero el QR
    $storeResponse = $this->actingAs($this->user)
        ->postJson(route('product-inf.store'), [
            'cli_id'      => $this->cli->cli_id,
            'description' => 'Prueba de visualización pública en tiempo real.',
        ]);

    $uuid = $storeResponse->json('data.uuid');

    // Consultar ruta pública sin estar autenticado
    $publicResponse = $this->get(route('product-inf.rack', ['uuid' => $uuid]));

    $publicResponse->assertStatus(200);
    $publicResponse->assertSee($this->product->name);
    $publicResponse->assertSee($this->location->name);
    $publicResponse->assertSee('Prueba de visualización pública en tiempo real.');
    $publicResponse->assertSee('Producto Activo en Almacén');
});

test('7. descarga de archivo SVG retorna tipo de contenido image/svg+xml', function () {
    $storeResponse = $this->actingAs($this->user)
        ->postJson(route('product-inf.store'), [
            'cli_id'      => $this->cli->cli_id,
            'description' => 'Prueba de descarga SVG.',
        ]);

    $uuid = $storeResponse->json('data.uuid');

    $svgResponse = $this->actingAs($this->user)
        ->get(route('product-inf.download-svg', ['uuid' => $uuid]));

    $svgResponse->assertStatus(200);
    $svgResponse->assertHeader('Content-Type', 'image/svg+xml');
    $this->assertStringContainsString('<svg', $svgResponse->getContent());
});

test('8. retorna vista 404 amigable si el UUID no existe en /rack/{uuid}', function () {
    $nonExistentUuid = (string) Illuminate\Support\Str::uuid();
    $response = $this->get(route('product-inf.rack', ['uuid' => $nonExistentUuid]));

    $response->assertStatus(200); // Renderiza vista not_found
    $response->assertSee('Código QR No Válido o Expirado');
});

test('9. si se registra una venta o salida y cambia el peso en cli, el QR refleja el peso actualizado automáticamente', function () {
    // 1. Generar código QR con peso original (250 kg)
    $storeResponse = $this->actingAs($this->user)
        ->postJson(route('product-inf.store'), [
            'cli_id'      => $this->cli->cli_id,
            'description' => 'Lote para prueba de venta.',
        ]);

    $uuid = $storeResponse->json('data.uuid');

    // 2. Simular venta o salida de 100 kg en el registro cli
    $this->cli->net_weight = 150.0;
    $this->cli->quantity = 3.0;
    $this->cli->save();

    // 3. Consultar la URL pública del QR (sin caché estática)
    $publicResponse = $this->get(route('product-inf.rack', ['uuid' => $uuid]));
    $publicResponse->assertStatus(200);
    $publicResponse->assertSee('150.00'); // El nuevo peso neto en vivo
    $publicResponse->assertDontSee('250.00 kg'); // No debe mostrar el peso viejo fijo

    // 4. Verificar que el modelo y resource reporten el peso en tiempo real
    $qr = ProductQr::where('uuid', $uuid)->first();
    $this->assertEquals(150.0, $qr->live_net_weight);
});

test('10. elimina un código QR, revoca su URL y limpia cli correctamente', function () {
    // 1. Generar código QR
    $storeResponse = $this->actingAs($this->user)
        ->postJson(route('product-inf.store'), [
            'cli_id'      => $this->cli->cli_id,
            'description' => 'QR para prueba de eliminación.',
        ]);

    $uuid = $storeResponse->json('data.uuid');

    // 2. Ejecutar eliminación
    $deleteResponse = $this->actingAs($this->user)
        ->deleteJson(route('product-inf.destroy', ['uuid' => $uuid]));

    $deleteResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ]);

    // 3. Verificar que se eliminó de la tabla product_qrs
    $this->assertFalse(ProductQr::where('uuid', $uuid)->exists());

    // 4. Verificar que se limpió el uuid y descripción en cli
    $this->cli->refresh();
    $this->assertNull($this->cli->uuid);
    $this->assertNull($this->cli->detailed_description);

    // 5. Verificar que la URL pública ahora muestra la vista 404 / expirada
    $publicResponse = $this->get(route('product-inf.rack', ['uuid' => $uuid]));
    $publicResponse->assertStatus(200);
    $publicResponse->assertSee('Código QR No Válido o Expirado');
});

test('11. retorna 403 al intentar eliminar un código QR sin permisos autorizados', function () {
    $uuid = (string) Illuminate\Support\Str::uuid();
    ProductQr::create([
        'uuid'        => $uuid,
        'product_id'  => $this->product->product_id,
        'description' => 'QR prueba permisos eliminación.',
        'created_by'  => $this->user->id,
    ]);

    $deleteResponse = $this->actingAs($this->unauthorizedUser)
        ->deleteJson(route('product-inf.destroy', ['uuid' => $uuid]));

    $deleteResponse->assertStatus(403);
});

