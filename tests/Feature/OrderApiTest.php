<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->adminRole = Role::findOrCreate('Admin', 'web');
    $this->salesRole = Role::findOrCreate('Sales', 'web');
    $this->warehouseRole = Role::findOrCreate('Warehouse', 'web');
    $this->qualityRole = Role::findOrCreate('Quality', 'web');

    $this->user->syncRoles([$this->adminRole]);

    $this->salesUser = User::factory()->create();
    $this->salesUser->syncRoles([$this->salesRole]);

    $this->otherSalesUser = User::factory()->create();
    $this->otherSalesUser->syncRoles([$this->salesRole]);

    $this->warehouseUser = User::factory()->create();
    $this->warehouseUser->syncRoles([$this->warehouseRole]);

    $this->qualityUser = User::factory()->create();
    $this->qualityUser->syncRoles([$this->qualityRole]);

    $this->product = Product::first() ?? Product::create([
        'name'         => 'Producto Orden ' . uniqid(),
        'sat_code'     => '10101501',
        'sku'          => 'SKU-ORD-' . rand(10000, 99999),
        'category_id'  => 1,
        'presentation' => 'Bulto 25kg',
        'unit'         => 'kg',
    ]);
});

test('index renders orders view for web request', function () {
    $response = $this->actingAs($this->user)->get(route('orders.index'));

    $response->assertStatus(200);
    $response->assertViewIs('orders');
});

test('index returns json when requested with json header', function () {
    $response = $this->actingAs($this->user)->getJson(route('orders.index'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'productos',
        ],
    ]);
});

test('getData returns list of orders with items', function () {
    $order = Order::create([
        'user_id' => $this->user->id,
        'año'     => 2026,
        'semana'  => 40,
        'empresa' => 'Empresa Pedido List',
        'po'      => 'PO-LIST-1',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'producto' => 'Producto A',
        'cantidad' => '50 bultos',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('orders.json'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'empresa',
                'items',
            ],
        ],
    ]);
});

test('getData filters orders by user if role is Sales without Admin', function () {
    Order::create([
        'user_id' => $this->salesUser->id,
        'año'     => 2026,
        'semana'  => 40,
        'empresa' => 'Empresa Sales Propia',
    ]);

    Order::create([
        'user_id' => $this->otherSalesUser->id,
        'año'     => 2026,
        'semana'  => 40,
        'empresa' => 'Empresa Otro Sales',
    ]);

    $response = $this->actingAs($this->salesUser)->getJson(route('orders.json'));

    $response->assertStatus(200);
    $orders = $response->json('data');

    $this->assertTrue(collect($orders)->every(fn ($o) => (int) $o['user_id'] === $this->salesUser->id));
});

test('store creates order and items with optional pdf attachment', function () {
    $fakePdf = UploadedFile::fake()->create('orden_compra.pdf', 100, 'application/pdf');

    $payload = [
        'año'                     => 2026,
        'semana'                  => 41,
        'empresa'                 => 'Comercializadora Industrial S.A.',
        'po'                      => 'PO-98765',
        'pdf_file'                => $fakePdf,
        'items'                   => [
            ['producto' => 'Producto A Granel', 'cantidad' => '20 ton'],
            ['producto' => 'Producto Envasado', 'cantidad' => '50 sacos'],
        ],
        'documentacion_requerida' => ['Certificado de Calidad', 'Factura', 'Hoja de Seguridad'],
        'transporte'              => 'Trailer refrigerado',
        'comentarios'             => 'Descarga en puerta 2',
    ];

    $response = $this->actingAs($this->user)->post(route('orders.store'), $payload);

    $response->assertStatus(200);
    $response->assertJson([
        'status' => 'success',
    ]);

    $this->assertDatabaseHas('orders', [
        'empresa'    => 'Comercializadora Industrial S.A.',
        'po'         => 'PO-98765',
        'transporte' => 'Trailer refrigerado',
    ]);

    $order = Order::where('po', 'PO-98765')->first();

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'producto' => 'Producto A Granel',
        'cantidad' => '20 ton',
    ]);

    $this->assertNotNull($order->pdf_path);
});

test('store fails validation on missing required fields', function () {
    $response = $this->actingAs($this->user)->postJson(route('orders.store'), [
        'po' => 'PO-FAIL',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['año', 'semana', 'empresa']);
});

test('store sanitizes input against XSS tags', function () {
    $payload = [
        'año'         => 2026,
        'semana'      => 41,
        'empresa'     => '<b>Empresa Sanitizada</b>',
        'po'          => 'PO-XSS',
        'comentarios' => '<script>alert("hack")</script>Sin scripts',
        'items'       => [
            ['producto' => '<i>Producto Limpio</i>', 'cantidad' => '10'],
        ],
    ];

    $response = $this->actingAs($this->user)->post(route('orders.store'), $payload);

    $response->assertStatus(200);

    $this->assertDatabaseHas('orders', [
        'empresa'     => 'Empresa Sanitizada',
        'comentarios' => 'alert("hack")Sin scripts',
    ]);

    $this->assertDatabaseHas('order_items', [
        'producto' => 'Producto Limpio',
    ]);
});

test('edit returns order json representation', function () {
    $order = Order::create([
        'user_id' => $this->user->id,
        'año'     => 2026,
        'semana'  => 40,
        'empresa' => 'Empresa Para Edicion',
    ]);

    $response = $this->actingAs($this->user)->getJson('/orders/' . $order->id . '/edit');

    $response->assertStatus(200);
    $response->assertJsonPath('id', $order->id);
    $response->assertJsonPath('empresa', 'Empresa Para Edicion');
});

test('edit returns 404 for non-existent order', function () {
    $response = $this->actingAs($this->user)->getJson('/orders/99999999/edit');

    $response->assertStatus(404);
});

test('edit returns 403 when sales user tries to access another users order', function () {
    $order = Order::create([
        'user_id' => $this->otherSalesUser->id,
        'año'     => 2026,
        'semana'  => 40,
        'empresa' => 'Empresa Ajena',
    ]);

    $response = $this->actingAs($this->salesUser)->getJson('/orders/' . $order->id . '/edit');

    $response->assertStatus(403);
});

test('update modifies order attributes and replaces items', function () {
    $order = Order::create([
        'user_id' => $this->user->id,
        'año'     => 2026,
        'semana'  => 40,
        'empresa' => 'Empresa Inicial',
        'po'      => 'PO-VIEJA',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'producto' => 'Item Viejo',
        'cantidad' => '5',
    ]);

    $payload = [
        'año'                     => 2026,
        'semana'                  => 42,
        'empresa'                 => 'Empresa Modificada',
        'po'                      => 'PO-NUEVA',
        'items'                   => [
            ['producto' => 'Item Nuevo', 'cantidad' => '15'],
        ],
        'documentacion_requerida' => ['Factura'],
    ];

    $response = $this->actingAs($this->user)->post('/orders/' . $order->id . '/update', $payload);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('orders', [
        'id'      => $order->id,
        'empresa' => 'Empresa Modificada',
        'po'      => 'PO-NUEVA',
    ]);

    $this->assertDatabaseMissing('order_items', [
        'order_id' => $order->id,
        'producto' => 'Item Viejo',
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'producto' => 'Item Nuevo',
        'cantidad' => '15',
    ]);
});

test('update returns 404 for non-existent order', function () {
    $response = $this->actingAs($this->user)->post('/orders/99999999/update', [
        'empresa' => 'Inexistente',
    ]);

    $response->assertStatus(404);
});

test('updateStatus updates warehouse status when authorized', function () {
    $order = Order::create([
        'user_id' => $this->user->id,
        'año'     => 2026,
        'semana'  => 40,
        'empresa' => 'Empresa Status WH',
    ]);

    $response = $this->actingAs($this->warehouseUser)->patchJson(route('orders.status', $order->id), [
        'estatus_almacen' => 'En preparación',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('orders', [
        'id'              => $order->id,
        'estatus_almacen' => 'En preparación',
    ]);
});

test('updateStatus fails 403 when unauthorized role modifies status', function () {
    $order = Order::create([
        'user_id' => $this->user->id,
        'año'     => 2026,
        'semana'  => 40,
        'empresa' => 'Empresa Status Deny',
    ]);

    $response = $this->actingAs($this->salesUser)->patchJson(route('orders.status', $order->id), [
        'estatus_administrativo' => 'Aprobado Admin',
    ]);

    $response->assertStatus(403);
});

test('destroy deletes order and cascades its items', function () {
    $order = Order::create([
        'user_id' => $this->user->id,
        'año'     => 2026,
        'semana'  => 40,
        'empresa' => 'Empresa Eliminar',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'producto' => 'Item a Borrar',
        'cantidad' => '1',
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('orders.destroy', $order->id));

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseMissing('orders', [
        'id' => $order->id,
    ]);

    $this->assertDatabaseMissing('order_items', [
        'order_id' => $order->id,
    ]);
});

test('destroy returns 404 for non-existent order', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('orders.destroy', 99999999));

    $response->assertStatus(404);
});
