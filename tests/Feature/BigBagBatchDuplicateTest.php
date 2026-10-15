<?php

use App\Models\Category;
use App\Models\Concept;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\Supplier;
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

    // Producto BigBag
    $this->bigBagProduct = Product::create([
        'name'        => 'Harina de Pescado BGBG ' . uniqid(),
        'unit'        => 'BigBags',
        'category_id' => $this->category->category_id,
        'stock_min'   => 1,
        'stock_max'   => 100,
    ]);

    // Producto regular (no BigBag)
    $this->regularProduct = Product::create([
        'name'        => 'Levadura Estandar Saco ' . uniqid(),
        'unit'        => 'kg',
        'category_id' => $this->category->category_id,
        'stock_min'   => 10,
        'stock_max'   => 500,
    ]);

    $this->warehouse1 = Warehouse::create([
        'name' => 'Almacén 1 ' . uniqid(),
    ]);
    $this->warehouse2 = Warehouse::create([
        'name' => 'Almacén 2 ' . uniqid(),
    ]);

    $this->location1 = Location::create([
        'name'         => 'Loc A-' . rand(1, 99),
        'warehouse_id' => $this->warehouse1->warehouse_id,
    ]);
    $this->location2 = Location::create([
        'name'         => 'Loc B-' . rand(1, 99),
        'warehouse_id' => $this->warehouse2->warehouse_id,
    ]);

    $this->concept = Concept::first() ?? Concept::create([
        'name' => 'Materia Prima',
    ]);

    $this->supplier = Supplier::first() ?? Supplier::create([
        'name'          => 'Proveedor Test ' . uniqid(),
        'supplier_code' => 'SP' . rand(1000, 9999),
    ]);
});

test('1. Permite distribuir BigBags con el mismo lote entre múltiples almacenes y acumula stock', function () {
    $batch = 'LOT-BGBG-' . uniqid();

    $payload = [
        'type'            => 'Input',
        'security_seal'   => 0,
        'supplier'        => $this->supplier->supplier_id,
        'operator'        => 'Chofer BigBags',
        'comments'        => 'Llegaron 26 BigBags distribuidos en Almacén 1 y 2',
        'product_id'      => [
            $this->bigBagProduct->product_id,
            $this->bigBagProduct->product_id,
        ],
        'quantity'        => [13, 13],
        'warehouse_batch' => [$batch, $batch],
        'location_id'     => [
            $this->location1->location_id,
            $this->location2->location_id,
        ],
        'concept_id'      => [
            $this->concept->concept_id,
            $this->concept->concept_id,
        ],
        'weight_per_unit' => [1000, 1000],
        'packing_list'    => ['PL-999', 'PL-999'],
        'bag_number'      => [
            ['BB-01', 'BB-02'],
            ['BB-03', 'BB-04'],
        ],
        'bag_weight'      => [
            [1010, 1020],
            [990, 995],
        ],
        'bag_location_id' => [
            [$this->location1->location_id, $this->location1->location_id],
            [$this->location2->location_id, $this->location2->location_id],
        ],
    ];

    $response = $this->actingAs($this->user)->postJson('/makeTransaction', $payload);

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();

    // Debe existir un único registro en inventory para este lote con la suma de stock (13 + 13 = 26)
    $inv = Inventory::where('batch', $batch)->get();
    expect($inv)->toHaveCount(1);
    expect((float) $inv->first()->stock)->toEqual(26.0);

    // Deben haberse creado los registros de cli distribuidos por ubicación
    $this->assertDatabaseHas('cli', [
        'inventory_id' => $inv->first()->inventory_id,
        'bag_number'   => 'BB-01',
        'location_id'  => $this->location1->location_id,
        'packing_list' => 'PL-999',
    ]);
    $this->assertDatabaseHas('cli', [
        'inventory_id' => $inv->first()->inventory_id,
        'bag_number'   => 'BB-03',
        'location_id'  => $this->location2->location_id,
        'packing_list' => 'PL-999',
    ]);
});

test('2. Rechaza lote duplicado en el mismo formulario si el producto NO es BigBag', function () {
    $batch = 'LOT-REGULAR-' . uniqid();

    $payload = [
        'type'            => 'Input',
        'security_seal'   => 0,
        'supplier'        => $this->supplier->supplier_id,
        'operator'        => 'Chofer Regular',
        'comments'        => 'Intento de duplicar lote no BigBag',
        'product_id'      => [
            $this->regularProduct->product_id,
            $this->regularProduct->product_id,
        ],
        'quantity'        => [50, 50],
        'warehouse_batch' => [$batch, $batch],
        'location_id'     => [
            $this->location1->location_id,
            $this->location2->location_id,
        ],
        'concept_id'      => [
            $this->concept->concept_id,
            $this->concept->concept_id,
        ],
        'weight_per_unit' => [25, 25],
    ];

    $response = $this->actingAs($this->user)->postJson('/makeTransaction', $payload);

    $response->assertStatus(422);
    expect($response->json('errors.warehouse_batch.1'))->not->toBeNull();
});

test('3. Rechaza reutilizar lote que ya existe en inventario para productos que NO son BigBag', function () {
    $existingBatch = 'EXISTING-REG-' . uniqid();
    Inventory::create([
        'product_id' => $this->regularProduct->product_id,
        'stock'      => 100,
        'batch'      => $existingBatch,
    ]);

    $payload = [
        'type'            => 'Input',
        'security_seal'   => 0,
        'supplier'        => $this->supplier->supplier_id,
        'operator'        => 'Chofer Regular',
        'comments'        => 'Intento de reutilizar lote existente no BigBag',
        'product_id'      => [$this->regularProduct->product_id],
        'quantity'        => [50],
        'warehouse_batch' => [$existingBatch],
        'location_id'     => [$this->location1->location_id],
        'concept_id'      => [$this->concept->concept_id],
        'weight_per_unit' => [25],
    ];

    $response = $this->actingAs($this->user)->postJson('/makeTransaction', $payload);

    $response->assertStatus(422);
    expect($response->json('errors.warehouse_batch.0'))->not->toBeNull();
});

test('4. Rechaza usar un lote de BigBag si ya pertenece a otro producto diferente', function () {
    $conflictBatch = 'CONFLICT-BATCH-' . uniqid();
    Inventory::create([
        'product_id' => $this->regularProduct->product_id,
        'stock'      => 50,
        'batch'      => $conflictBatch,
    ]);

    $payload = [
        'type'            => 'Input',
        'security_seal'   => 0,
        'supplier'        => $this->supplier->supplier_id,
        'operator'        => 'Chofer Test',
        'comments'        => 'BigBag con lote asignado a otro producto',
        'product_id'      => [$this->bigBagProduct->product_id],
        'quantity'        => [10],
        'warehouse_batch' => [$conflictBatch],
        'location_id'     => [$this->location1->location_id],
        'concept_id'      => [$this->concept->concept_id],
        'weight_per_unit' => [1000],
    ];

    $response = $this->actingAs($this->user)->postJson('/makeTransaction', $payload);

    $response->assertStatus(422);
    expect($response->json('errors.warehouse_batch.0'))->not->toBeNull();
});
