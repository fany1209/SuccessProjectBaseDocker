<?php

use App\Models\Cli;
use App\Models\Concept;
use App\Models\Control;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductionMaterialRequest;
use App\Models\ProductionMaterialRequestItem;
use App\Models\User;
use App\Models\Warehouse;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->unauthorizedUser = User::factory()->create();

    Permission::findOrCreate('warehouse.show', 'web');
    $this->user->givePermissionTo('warehouse.show');

    $this->warehouse = Warehouse::firstOrCreate(
        ['name' => 'Almacén General Test ' . uniqid()]
    );

    $this->location = Location::firstOrCreate(
        ['name' => 'L' . rand(1000, 9999)],
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
        'batch'      => 'BATCH-' . uniqid(),
        'stock'      => 500.0,
    ]);

    $this->cli = Cli::create([
        'bag_number'      => 'B-' . rand(100000, 999999),
        'protein'         => 12.5,
        'quantity'        => 10,
        'weight_per_unit' => 25.0,
        'net_weight'      => 250.0,
        'inventory_id'    => $this->inventory->inventory_id,
        'location_id'     => $this->location->location_id,
        'concept_id'      => $this->concept->concept_id,
    ]);
});

afterEach(function () {
    if (isset($this->cli)) {
        $this->cli->delete();
    }
    if (isset($this->inventory)) {
        $this->inventory->delete();
    }
    if (isset($this->location)) {
        $this->location->delete();
    }
    if (isset($this->warehouse)) {
        Control::where('warehouse_id', $this->warehouse->warehouse_id)->delete();
        $this->warehouse->delete();
    }
    if (isset($this->concept)) {
        $this->concept->delete();
    }
    if (isset($this->unauthorizedUser)) {
        $this->unauthorizedUser->delete();
    }
});

test('1. retorna 403 al acceder a almacén sin permiso warehouse.show', function () {
    $response = $this->actingAs($this->unauthorizedUser)->get('/warehouse');

    $response->assertStatus(403);
});

test('2. puede cargar la vista principal de almacén con datos y componentes (GET /warehouse)', function () {
    $response = $this->actingAs($this->user)->get('/warehouse');

    $response->assertStatus(200)
        ->assertViewIs('warehouse')
        ->assertViewHasAll([
            'concepts',
            'warehouses',
            'hours',
            'feedback',
            'locations',
            'products_inventory',
            'inventory',
            'available_locations',
            'summary',
        ]);
});

test('3. retorna feedback de temperatura en formato JSON vía AJAX (GET /warehouse?warehouse_id=...)', function () {
    $response = $this->actingAs($this->user)->getJson('/warehouse?warehouse_id=' . $this->warehouse->warehouse_id);

    $response->assertStatus(200)
        ->assertJson([
            'success'  => true,
            'flag'     => true,
            'feedback' => [false, false, false],
        ]);
});

test('4. puede registrar mediciones de temperatura y humedad en almacén (POST /temperature)', function () {
    $payload = [
        'warehouse_id' => $this->warehouse->warehouse_id,
        'measurements' => [
            [
                'hour'        => '09:00',
                'temperature' => 22.5,
                'humidity'    => 55.0,
            ],
            [
                'hour'        => '10:00',
                'temperature' => 24.0,
                'humidity'    => 58.2,
            ],
        ],
    ];

    $response = $this->actingAs($this->user)->postJson('/temperature', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'message' => 'Mediciones registradas correctamente',
        ]);

    $this->assertDatabaseHas('control', [
        'warehouse_id' => $this->warehouse->warehouse_id,
        'temperature'  => 22.5,
        'humidity'     => 55.0,
    ]);

    $this->assertDatabaseHas('control', [
        'warehouse_id' => $this->warehouse->warehouse_id,
        'temperature'  => 24.0,
        'humidity'     => 58.2,
    ]);
});

test('5. valida campos obligatorios al registrar mediciones de temperatura (POST /temperature)', function () {
    $payload = [
        'warehouse_id' => 999999, // Inexistente
        'measurements' => [],
    ];

    $response = $this->actingAs($this->user)->postJson('/temperature', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['warehouse_id', 'measurements']);
});

test('6. puede consultar información de productos en una ubicación (GET /getInfoLocation)', function () {
    $response = $this->actingAs($this->user)->getJson('/getInfoLocation?location=' . urlencode($this->location->name));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ])
        ->assertJsonStructure([
            'products' => [
                '*' => [
                    'pName',
                    'cName',
                    'bag_number',
                    'quantity',
                    'wpu',
                    'unit',
                    'total',
                    'batch',
                ],
            ],
        ]);
});

test('7. puede filtrar existencias en almacén por concepto o texto de búsqueda (GET /getWarehouse)', function () {
    $response = $this->actingAs($this->user)->getJson('/getWarehouse?concept=' . $this->concept->concept_id . '&search=' . urlencode($this->product->name));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ])
        ->assertJsonStructure([
            'products' => [
                '*' => [
                    'cli_id',
                    'lName',
                    'pName',
                    'cName',
                    'bag_number',
                    'quantity',
                    'weight_per_unit',
                    'net_weight',
                    'batch',
                    'unit',
                ],
            ],
        ]);
});

test('8. puede listar las solicitudes de material para producción (GET /warehouse/production-requests)', function () {
    $matReq = ProductionMaterialRequest::create([
        'area'           => 'Envasado',
        'applicant_name' => 'Juan Pérez',
        'status'         => 'Pendiente',
        'comments'       => 'Requerimiento urgente de MP',
    ]);

    $response = $this->actingAs($this->user)->get('/warehouse/production-requests');

    $response->assertStatus(200)
        ->assertViewIs('warehouse.production_requests')
        ->assertViewHas('requests');

    $matReq->delete();
});

test('9. puede marcar una solicitud de material como surtida vía AJAX (POST /warehouse/production-requests/{id}/attend)', function () {
    $matReq = ProductionMaterialRequest::create([
        'area'           => 'Mezclado',
        'applicant_name' => 'María López',
        'status'         => 'Pendiente',
        'comments'       => 'Entrega para turno matutino',
    ]);

    $response = $this->actingAs($this->user)->postJson('/warehouse/production-requests/' . $matReq->id . '/attend');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'message' => 'Solicitud marcada como Surtida.',
        ]);

    $this->assertDatabaseHas('production_material_requests', [
        'id'     => $matReq->id,
        'status' => 'Surtido',
    ]);

    $matReq->delete();
});

test('10. retorna 404 al intentar atender una solicitud inexistente (POST /warehouse/production-requests/999999/attend)', function () {
    $response = $this->actingAs($this->user)->postJson('/warehouse/production-requests/999999/attend');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
        ]);
});

test('11. puede obtener las partidas asociadas a una solicitud de material (GET /warehouse/production-requests/{id}/items)', function () {
    $matReq = ProductionMaterialRequest::create([
        'area'           => 'Control de Calidad',
        'applicant_name' => 'Carlos Ruiz',
        'status'         => 'Pendiente',
        'comments'       => 'Muestras requeridas',
    ]);

    $item = ProductionMaterialRequestItem::create([
        'request_id'          => $matReq->id,
        'product_name'        => $this->product->name,
        'quantity'            => 50.0,
        'dispatched_quantity' => 0.0,
    ]);

    $response = $this->actingAs($this->user)->getJson('/warehouse/production-requests/' . $matReq->id . '/items');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ])
        ->assertJsonStructure([
            'items' => [
                '*' => [
                    'product_id',
                    'product_name',
                    'quantity',
                ],
            ],
        ]);

    $item->delete();
    $matReq->delete();
});

test('12. retorna 404 al consultar partidas de una solicitud inexistente (GET /warehouse/production-requests/999999/items)', function () {
    $response = $this->actingAs($this->user)->getJson('/warehouse/production-requests/999999/items');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
        ]);
});
