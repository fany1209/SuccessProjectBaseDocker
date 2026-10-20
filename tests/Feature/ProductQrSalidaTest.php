<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductQr;
use App\Models\Sale;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('Warehouse', 'web');
    Role::findOrCreate('Admin', 'web');
    Role::findOrCreate('Sales', 'web');

    Permission::findOrCreate('inventory.show', 'web');

    $this->warehouseUser = User::factory()->create([
        'email' => 'warehouse_' . uniqid() . '@example.com',
    ]);
    $this->warehouseUser->assignRole('Warehouse');
    $this->warehouseUser->givePermissionTo('inventory.show');

    $this->regularUser = User::factory()->create([
        'email' => 'regular_' . uniqid() . '@example.com',
    ]);
    $this->regularUser->assignRole('Sales');

    $this->category = Category::first() ?? Category::create([
        'name' => 'Categoría Test ' . uniqid(),
    ]);

    $this->product = Product::create([
        'name'         => 'Producto QR Salida ' . uniqid(),
        'unit'         => 'kg',
        'category_id'  => $this->category->category_id,
        'sat_code'     => '10101501',
        'sku'          => 'SKU-' . rand(10000, 99999),
        'presentation' => 'Bolsa 25kg',
        'batch_code'   => 'LOTE-001',
        'stock_min'    => 10,
        'stock_max'    => 100,
    ]);

    $this->otherProduct = Product::create([
        'name'         => 'Otro Producto ' . uniqid(),
        'unit'         => 'kg',
        'category_id'  => $this->category->category_id,
        'sat_code'     => '10101501',
        'sku'          => 'SKU-' . rand(10000, 99999),
        'presentation' => 'Bolsa 25kg',
        'batch_code'   => 'LOTE-002',
        'stock_min'    => 10,
        'stock_max'    => 100,
    ]);

    $this->warehouse = Warehouse::first() ?? Warehouse::create([
        'name' => 'Almacén QR ' . uniqid(),
    ]);

    $this->location = Location::first() ?? Location::create([
        'name'         => 'RACK-TEST-' . rand(100, 999),
        'warehouse_id' => $this->warehouse->warehouse_id,
    ]);

    $this->qr = ProductQr::create([
        'uuid'            => (string) Str::uuid(),
        'product_id'      => $this->product->product_id,
        'location_id'     => $this->location->location_id,
        'batch'           => 'LOTE-TEST-SALIDA',
        'quantity'        => 10,
        'weight_per_unit' => 50,
        'net_weight'      => 500,
        'unit'            => 'kg',
        'description'     => 'Descripción de prueba para salida',
        'created_by'      => $this->warehouseUser->id,
    ]);
});

afterEach(function () {
    if (isset($this->qr) && $this->qr->exists) {
        $this->qr->delete();
    }
});

test('1. la vista pública /rack/{uuid} muestra el botón Realizar Salida y los modales requeridos', function () {
    $response = $this->get(route('product-inf.rack', ['uuid' => $this->qr->uuid]));

    $response->assertStatus(200);
    $response->assertSee('Realizar Salida');
    $response->assertSee('¿El tipo de salida es para Venta o Producción?');
    $response->assertSee('Salida para Venta');
    $response->assertSee('Salida para Producción');
    $response->assertSee(route('product-inf.salida', $this->qr->uuid));
});

test('2. un usuario no autenticado al pulsar Realizar Salida es enviado a login y se guarda url.intended', function () {
    $salidaUrl = route('product-inf.salida', ['uuid' => $this->qr->uuid]);
    $expectedIntended = route('product-inf.rack', ['uuid' => $this->qr->uuid, 'action' => 'salida']);

    $response = $this->get($salidaUrl);

    $response->assertRedirect(route('login'));
    expect(session('url.intended'))->toBe($expectedIntended);
});

test('3. un usuario autenticado sin rol de almacén es redirigido a la vista QR con error de permisos', function () {
    $salidaUrl = route('product-inf.salida', ['uuid' => $this->qr->uuid]);

    $response = $this->actingAs($this->regularUser)
        ->get($salidaUrl);

    $response->assertRedirect(route('product-inf.rack', [
        'uuid'  => $this->qr->uuid,
        'error' => 'unauthorized_role',
    ]));
});

test('4. un usuario autenticado con rol de almacén regresa a la vista QR con action=salida para elegir destino', function () {
    $salidaUrl = route('product-inf.salida', ['uuid' => $this->qr->uuid]);

    $response = $this->actingAs($this->warehouseUser)
        ->get($salidaUrl);

    $response->assertRedirect(route('product-inf.rack', [
        'uuid'   => $this->qr->uuid,
        'action' => 'salida',
    ]));
});

test('5. al seleccionar Salida para Venta, la vista de inventario filtra únicamente ventas pendientes con el producto escaneado', function () {
    $customer = Customer::first() ?? Customer::create([
        'name'      => 'Cliente Test ' . uniqid(),
        'rfc'       => 'TEST' . rand(1000, 9999),
        'email'     => 'cust' . uniqid() . '@test.com',
        'seller_id' => $this->warehouseUser->id,
    ]);

    $sector = \App\Models\Sector::first() ?? \App\Models\Sector::create([
        'name' => 'Sector Test ' . uniqid(),
        'code' => 'ST' . rand(10, 99),
    ]);

    $statusRow = \Illuminate\Support\Facades\DB::table('sales_status')->first();
    $statusId = $statusRow ? $statusRow->sales_status_id : 1;

    $folioMatch = rand(910000, 919999);
    $folioConfirmed = rand(920000, 929999);
    $folioOther = rand(930000, 939999);

    // Venta 1: Pendiente con el producto escaneado (DEBE APARECER)
    $saleMatch = Sale::create([
        'folio'           => $folioMatch,
        'seller'          => 'Vendedor Almacén',
        'first_time'      => 1,
        'purchase_order'  => 'PO-1001',
        'invoice'         => 'INV-1001',
        'term'            => 'Contado',
        'sales_status_id' => $statusId,
        'sector_id'       => $sector->sector_id,
        'payment_status'  => 'PENDING',
        'date'            => now()->format('Y-m-d'),
        'customer_id'     => $customer->customer_id,
        'user_id'         => $this->warehouseUser->id,
        'almacen_status'  => 'pending',
        'is_customer'     => 1,
        'sale_type'       => 'cash',
    ]);
    $saleMatch->products()->attach($this->product->product_id, [
        'quantity' => 5,
        'cost'     => 100,
    ]);

    // Venta 2: Confirmada con el producto escaneado (NO DEBE APARECER por no ser pendiente)
    $saleConfirmed = Sale::create([
        'folio'           => $folioConfirmed,
        'seller'          => 'Vendedor Almacén',
        'first_time'      => 1,
        'purchase_order'  => 'PO-1002',
        'invoice'         => 'INV-1002',
        'term'            => 'Contado',
        'sales_status_id' => $statusId,
        'sector_id'       => $sector->sector_id,
        'payment_status'  => 'PAID',
        'date'            => now()->format('Y-m-d'),
        'customer_id'     => $customer->customer_id,
        'user_id'         => $this->warehouseUser->id,
        'almacen_status'  => 'confirmed',
        'is_customer'     => 1,
        'sale_type'       => 'cash',
    ]);
    $saleConfirmed->products()->attach($this->product->product_id, [
        'quantity' => 2,
        'cost'     => 100,
    ]);

    // Venta 3: Pendiente pero con OTRO producto diferente (NO DEBE APARECER)
    $saleOtherProd = Sale::create([
        'folio'           => $folioOther,
        'seller'          => 'Vendedor Almacén',
        'first_time'      => 1,
        'purchase_order'  => 'PO-1003',
        'invoice'         => 'INV-1003',
        'term'            => 'Contado',
        'sales_status_id' => $statusId,
        'sector_id'       => $sector->sector_id,
        'payment_status'  => 'PENDING',
        'date'            => now()->format('Y-m-d'),
        'customer_id'     => $customer->customer_id,
        'user_id'         => $this->warehouseUser->id,
        'almacen_status'  => 'pending',
        'is_customer'     => 1,
        'sale_type'       => 'cash',
    ]);
    $saleOtherProd->products()->attach($this->otherProduct->product_id, [
        'quantity' => 10,
        'cost'     => 50,
    ]);

    // Consultamos la vista de inventario con los parámetros que genera el botón del modal
    $response = $this->actingAs($this->warehouseUser)
        ->get(route('inventory.index', [
            'tab'         => 'sales',
            'product_id'  => $this->product->product_id,
            'sale_status' => 'pending',
        ]));

    $response->assertStatus(200);

    // Debe mostrar el banner informativo del filtro
    $response->assertSee('Filtro por Producto Escaneado (Salida de Almacén)');
    $response->assertSee($this->product->name);
    $response->assertSee('Quitar filtro y ver todas');

    // Debe mostrar la venta pendiente del producto escaneado
    $response->assertSee((string) $folioMatch);

    // NO debe mostrar la venta confirmada ni la venta con otro producto
    $response->assertDontSee((string) $folioConfirmed);
    $response->assertDontSee((string) $folioOther);

    // Limpieza de ventas creadas
    $saleMatch->products()->detach();
    $saleMatch->delete();
    $saleConfirmed->products()->detach();
    $saleConfirmed->delete();
    $saleOtherProd->products()->detach();
    $saleOtherProd->delete();
});
