<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Sector;
use App\Models\User;
use App\Notifications\SaleAlmacenNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    Permission::findOrCreate('sales.show', 'web');
    $this->user->givePermissionTo(['sales.show']);

    $adminRole = Role::findOrCreate('Admin', 'web');
    $this->user->assignRole($adminRole);

    $this->sector = Sector::first() ?? Sector::create([
        'name' => 'Sector Ventas ' . uniqid(),
        'code' => 'V' . rand(10, 99),
    ]);

    $randomSuffix = rand(10000, 99999);
    $this->customer = Customer::create([
        'sector_id'     => $this->sector->sector_id,
        'customer_code' => 'SCV' . $randomSuffix,
        'name'          => 'Cliente Ventas ' . $randomSuffix,
        'email'         => 'ventas.' . $randomSuffix . '@test.com',
        'phone'         => '4421234567',
    ]);

    $this->prospect = Prospect::create([
        'sector_id' => $this->sector->sector_id,
        'name'      => 'Prospecto Test ' . $randomSuffix,
        'phone'     => '4429988776',
        'email'     => 'prospecto.' . $randomSuffix . '@test.com',
        'rfc'       => 'PRP' . $randomSuffix,
        'district'  => 'Centro',
        'city'      => 'Querétaro',
        'state'     => 'Querétaro',
        'address'   => 'Av. 5 de Febrero',
    ]);

    $this->category = Category::first() ?? Category::create([
        'name' => 'Categoría Ventas ' . uniqid(),
    ]);

    $this->product = Product::create([
        'name'         => 'Producto Venta ' . uniqid(),
        'category_id'  => $this->category->category_id,
        'sat_code'     => '10101501',
        'sku'          => substr('SKU-' . uniqid(), 0, 20),
        'presentation' => 'Bulto 25kg',
        'unit'         => 'kg',
    ]);

    $statusRow = DB::table('sales_status')->first();
    $this->statusId = $statusRow ? $statusRow->sales_status_id : 1;

    $this->createSale = function (array $overrides = []) {
        static $counter = 7000;
        $counter++;

        $sale = Sale::create(array_merge([
            'seller'          => 'Vendedor Prueba',
            'first_time'      => 1,
            'is_customer'     => 1,
            'purchase_order'  => 'PO-' . rand(100000, 999999),
            'invoice'         => 'INV-' . rand(100000, 999999),
            'sale_type'       => 'Credit',
            'term'            => '30 días',
            'date'            => '2026-10-05',
            'folio'           => $counter,
            'customer_id'     => $this->customer->customer_id,
            'prospect_id'     => null,
            'user_id'         => $this->user->id,
            'sales_status_id' => $this->statusId,
            'sector_id'       => $this->sector->sector_id,
            'payment_status'  => 'PENDING',
            'almacen_status'  => 'pending',
        ], $overrides));

        SaleDetail::create([
            'sale_id'             => $sale->sale_id,
            'product_id'          => $this->product->product_id,
            'public_product_name' => $this->product->name,
            'quantity'            => 10,
            'cost'                => 250.00,
            'has_tax'             => 1,
            'invoice_val'         => 0,
        ]);

        return $sale;
    };
});

test('index renders sales view for web request', function () {
    $response = $this->actingAs($this->user)->get(route('sales.index'));

    $response->assertStatus(200);
    $response->assertViewIs('sales');
});

test('index returns json when requested with json header', function () {
    $response = $this->actingAs($this->user)->getJson(route('sales.index'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'total_sales',
            'sectors',
            'products',
            'prospects',
            'customers',
            'statuses',
            'sellers',
        ],
    ]);
});

test('getSales returns filtered sales list', function () {
    ($this->createSale)();

    $response = $this->actingAs($this->user)->getJson(route('sales.getSales', [
        'clients' => 1,
    ]));

    $response->assertStatus(200);
    $response->assertJsonStructure(['sales']);
});

test('store creates sale with existing customer and generates sequential folio', function () {
    $payload = [
        'seller'              => 'Vendedor Titular',
        'first_time'          => 1,
        'is_customer'         => 1,
        'purchase_order'      => 'PO-TEST-' . rand(10000, 99999),
        'invoice'             => 'INV-TEST-' . rand(10000, 99999),
        'sale_type'           => 'Cash',
        'term'                => 'Contado',
        'date'                => '2026-10-05',
        'customer_id'         => $this->customer->customer_id,
        'user_id'             => $this->user->id,
        'sales_status_id'     => $this->statusId,
        'sector_id'           => $this->sector->sector_id,
        'product_id'          => [$this->product->product_id],
        'quantity'            => [5],
        'cost'                => [300],
        'has_tax'             => [1],
        'invoice_val'         => [0],
        'public_product_name' => [$this->product->name],
    ];

    $response = $this->actingAs($this->user)->postJson(route('sales.store'), $payload);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'message',
        'sale' => [
            'sale_id',
            'folio',
            'seller',
            'payment_status',
        ],
    ]);

    $saleId = $response->json('sale.sale_id');

    $this->assertDatabaseHas('sales', [
        'sale_id'        => $saleId,
        'seller'         => 'Vendedor Titular',
        'customer_id'    => $this->customer->customer_id,
        'payment_status' => 'PAID',
    ]);

    $this->assertDatabaseHas('sale_detail', [
        'sale_id'    => $saleId,
        'product_id' => $this->product->product_id,
        'quantity'   => 5,
    ]);
});

test('store auto-creates customer when first_time=0 and is_customer=0', function () {
    $randomCode = rand(10000, 99999);
    $payload = [
        'seller'              => 'Vendedor Nuevos',
        'first_time'          => 0,
        'is_customer'         => 0,
        'name'                => 'Cliente Nuevo Auto ' . $randomCode,
        'phone'               => '4421112233',
        'email'               => 'nuevo.' . $randomCode . '@empresa.com',
        'rfc'                 => 'NUE' . $randomCode,
        'sale_type'           => 'Credit',
        'term'                => '15 días',
        'date'                => '2026-10-05',
        'user_id'             => $this->user->id,
        'sales_status_id'     => $this->statusId,
        'sector_id'           => $this->sector->sector_id,
        'product_id'          => [$this->product->product_id],
        'quantity'            => [2],
        'cost'                => [500],
    ];

    $response = $this->actingAs($this->user)->postJson(route('sales.store'), $payload);

    $response->assertStatus(201);
    $saleId = $response->json('sale.sale_id');

    $this->assertDatabaseHas('customers', [
        'name'      => 'Cliente Nuevo Auto ' . $randomCode,
        'sector_id' => $this->sector->sector_id,
    ]);

    $this->assertDatabaseHas('sales', [
        'sale_id'     => $saleId,
        'is_customer' => 1,
    ]);
});

test('store converts prospect to customer and migrates existing sales', function () {
    $prospectSale = ($this->createSale)([
        'is_customer' => 0,
        'customer_id' => null,
        'prospect_id' => $this->prospect->prospect_id,
    ]);

    $payload = [
        'seller'              => 'Vendedor Conversion',
        'first_time'          => 1,
        'is_customer'         => 0,
        'prospect_id'         => $this->prospect->prospect_id,
        'sale_type'           => 'Credit',
        'date'                => '2026-10-05',
        'user_id'             => $this->user->id,
        'sales_status_id'     => $this->statusId,
        'sector_id'           => $this->sector->sector_id,
        'product_id'          => [$this->product->product_id],
        'quantity'            => [1],
        'cost'                => [150],
    ];

    $response = $this->actingAs($this->user)->postJson(route('sales.store'), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseMissing('prospects', [
        'prospect_id' => $this->prospect->prospect_id,
    ]);

    $this->assertDatabaseHas('sales', [
        'sale_id'     => $prospectSale->sale_id,
        'is_customer' => 1,
        'prospect_id' => null,
    ]);
});

test('store fails validation on missing required fields', function () {
    $response = $this->actingAs($this->user)->postJson(route('sales.store'), [
        'date' => '2026-10-05',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['seller', 'sale_type', 'sales_status_id', 'sector_id', 'product_id']);
});

test('store sanitizes input against XSS tags', function () {
    $payload = [
        'seller'              => '<b>Vendedor Limpio</b>',
        'first_time'          => 1,
        'is_customer'         => 1,
        'customer_id'         => $this->customer->customer_id,
        'purchase_order'      => 'PO-SAN-' . rand(10000, 99999),
        'sale_type'           => 'Credit',
        'term'                => '<span>30 días</span>',
        'date'                => '2026-10-05',
        'user_id'             => $this->user->id,
        'sales_status_id'     => $this->statusId,
        'sector_id'           => $this->sector->sector_id,
        'product_id'          => [$this->product->product_id],
        'quantity'            => [3],
        'cost'                => [120],
        'public_product_name' => ['<i>Producto Etiqueta</i>'],
    ];

    $response = $this->actingAs($this->user)->postJson(route('sales.store'), $payload);

    $response->assertStatus(201);
    $saleId = $response->json('sale.sale_id');

    $this->assertDatabaseHas('sales', [
        'sale_id' => $saleId,
        'seller'  => 'Vendedor Limpio',
        'term'    => '30 días',
    ]);

    $this->assertDatabaseHas('sale_detail', [
        'sale_id'             => $saleId,
        'public_product_name' => 'Producto Etiqueta',
    ]);
});

test('update modifies sale attributes and synchronizes details', function () {
    $sale = ($this->createSale)();
    $existingDetail = SaleDetail::where('sale_id', $sale->sale_id)->first();

    $product2 = Product::create([
        'name'         => 'Producto Extra ' . uniqid(),
        'category_id'  => $this->category->category_id,
        'sat_code'     => '10101501',
        'sku'          => 'SKU-EX-' . rand(10000, 99999),
        'presentation' => 'Caja',
        'unit'         => 'cj',
    ]);

    $payload = [
        'sale_id'             => $sale->sale_id,
        'seller'              => 'Vendedor Reasignado',
        'is_customer'         => 1,
        'customer_id'         => $this->customer->customer_id,
        'sale_type'           => 'Credit',
        'date'                => '2026-10-06',
        'user_id'             => $this->user->id,
        'sales_status_id'     => $this->statusId,
        'sector_id'           => $this->sector->sector_id,
        'sale_detail'         => [$existingDetail->sale_detail_id, 0],
        'product_id'          => [$this->product->product_id, $product2->product_id],
        'quantity'            => [20, 5],
        'cost'                => [260, 100],
        'has_tax'             => [1, 0],
        'invoice_val'         => [0, 0],
        'public_product_name' => ['Actualizado', 'Nuevo Item'],
    ];

    $response = $this->actingAs($this->user)->putJson(route('sales.update', $sale->sale_id), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('sales', [
        'sale_id' => $sale->sale_id,
        'seller'  => 'Vendedor Reasignado',
    ]);

    $this->assertDatabaseHas('sale_detail', [
        'sale_detail_id' => $existingDetail->sale_detail_id,
        'quantity'       => 20,
    ]);

    $this->assertDatabaseHas('sale_detail', [
        'sale_id'             => $sale->sale_id,
        'product_id'          => $product2->product_id,
        'public_product_name' => 'Nuevo Item',
    ]);
});

test('update returns 404 for non-existent sale', function () {
    $response = $this->actingAs($this->user)->putJson(route('sales.update', 99999999), [
        'sale_id'         => 99999999,
        'seller'          => 'Inexistente',
        'is_customer'     => 1,
        'customer_id'     => $this->customer->customer_id,
        'sale_type'       => 'Credit',
        'date'            => '2026-10-06',
        'user_id'         => $this->user->id,
        'sales_status_id' => $this->statusId,
        'sector_id'       => $this->sector->sector_id,
        'product_id'      => [$this->product->product_id],
        'quantity'        => [1],
        'cost'            => [10],
    ]);

    $response->assertStatus(404);
});

test('show returns sale details and client information', function () {
    $sale = ($this->createSale)();

    $response = $this->actingAs($this->user)->getJson(route('sales.show', $sale->sale_id));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'sale',
        'sale_detail',
        'sectors',
    ]);
});

test('show returns 404 for non-existent sale', function () {
    $response = $this->actingAs($this->user)->getJson(route('sales.show', 99999999));

    $response->assertStatus(404);
});

test('destroy removes sale and its detail lines', function () {
    $sale = ($this->createSale)();

    $response = $this->actingAs($this->user)->deleteJson(route('sales.destroy', $sale->sale_id));

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseMissing('sales', [
        'sale_id' => $sale->sale_id,
    ]);

    $this->assertDatabaseMissing('sale_detail', [
        'sale_id' => $sale->sale_id,
    ]);
});

test('destroy returns 404 for non-existent sale', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('sales.destroy', 99999999));

    $response->assertStatus(404);
});

test('deleteDetail removes specific sale detail row', function () {
    $sale = ($this->createSale)();
    $detail = SaleDetail::where('sale_id', $sale->sale_id)->first();

    $response = $this->actingAs($this->user)->deleteJson(route('sales.deleteDetail', [
        'id' => $detail->sale_detail_id,
    ]));

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseMissing('sale_detail', [
        'sale_detail_id' => $detail->sale_detail_id,
    ]);
});

test('updateStatus modifies sale status successfully', function () {
    $sale = ($this->createSale)();

    $response = $this->actingAs($this->user)->patchJson(route('sales.updateStatus', $sale->sale_id), [
        'sales_status_id' => $this->statusId,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

test('getRemisionesChartData returns grouped chart data', function () {
    ($this->createSale)();

    $response = $this->actingAs($this->user)->getJson(route('sales.chartData'));

    $response->assertStatus(200);
    $response->assertJsonStructure(['datos']);
});

test('getClienteData returns client sales history', function () {
    ($this->createSale)();

    $response = $this->actingAs($this->user)->getJson(route('api.clienteData', [
        'q' => $this->customer->name,
    ]));

    $response->assertStatus(200);
    $this->assertIsArray($response->json());
});

test('getSaleLots returns lots structure for sale products', function () {
    $sale = ($this->createSale)();

    $response = $this->actingAs($this->user)->getJson(route('sales.almacen_lots', $sale->sale_id));

    $response->assertStatus(200);
    $response->assertJsonStructure(['products']);
});

test('almacenAction postpones sale with comment and date', function () {
    $sale = ($this->createSale)();

    $response = $this->actingAs($this->user)->postJson(route('sales.almacen_action', $sale->sale_id), [
        'action' => 'postpone',
        'reason' => 'Falta transporte',
        'date'   => '2026-10-10',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('sales', [
        'sale_id'                => $sale->sale_id,
        'almacen_status'         => 'postponed',
        'almacen_comment'        => 'Falta transporte',
        'almacen_postponed_date' => '2026-10-10',
    ]);
});

test('almacenAction marks sale ready from postponed state', function () {
    $sale = ($this->createSale)([
        'almacen_status'         => 'postponed',
        'almacen_comment'        => 'Pospuesta previa',
        'almacen_postponed_date' => '2026-10-10',
    ]);

    $response = $this->actingAs($this->user)->postJson(route('sales.almacen_action', $sale->sale_id), [
        'action' => 'mark_ready',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('sales', [
        'sale_id'                => $sale->sale_id,
        'almacen_status'         => 'pending',
        'almacen_comment'        => null,
        'almacen_postponed_date' => null,
    ]);
});

test('almacenAction cancels sale and sets cxcDetail to cancelled', function () {
    $sale = ($this->createSale)();

    $response = $this->actingAs($this->user)->postJson(route('sales.almacen_action', $sale->sale_id), [
        'action' => 'cancel',
        'reason' => 'Cancelación por cliente',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('sales', [
        'sale_id'         => $sale->sale_id,
        'almacen_status'  => 'cancelled',
        'almacen_comment' => 'Cancelación por cliente',
    ]);

    $this->assertDatabaseHas('cxc_details', [
        'sale_id'     => $sale->sale_id,
        'is_canceled' => 1,
    ]);
});

test('postponedReminders returns postponed sales for warehouse/admin users', function () {
    ($this->createSale)([
        'almacen_status'         => 'postponed',
        'almacen_comment'        => 'Pospuesta vencida',
        'almacen_postponed_date' => now()->subDay()->toDateString(),
    ]);

    $response = $this->actingAs($this->user)->getJson(route('almacen.postponed_reminders'));

    $response->assertStatus(200);
    $response->assertJsonStructure(['reminders']);
});

test('update sends sale_edited notification with changes to warehouse and admin users', function () {
    Notification::fake();

    $warehouseRole = Role::findOrCreate('Warehouse', 'web');
    $warehouseUser = User::factory()->create();
    $warehouseUser->assignRole($warehouseRole);

    $sale = ($this->createSale)([
        'seller' => 'Vendedor Original',
        'date'   => '2026-10-01',
    ]);
    $existingDetail = SaleDetail::where('sale_id', $sale->sale_id)->first();

    $payload = [
        'sale_id'             => $sale->sale_id,
        'seller'              => 'Vendedor Editado',
        'is_customer'         => 1,
        'customer_id'         => $this->customer->customer_id,
        'sale_type'           => 'Cash',
        'date'                => '2026-10-05',
        'user_id'             => $this->user->id,
        'sales_status_id'     => $this->statusId,
        'sector_id'           => $this->sector->sector_id,
        'sale_detail'         => [$existingDetail->sale_detail_id],
        'product_id'          => [$this->product->product_id],
        'quantity'            => [45], // changed from 5 to 45
        'cost'                => [300],
        'has_tax'             => [1],
        'invoice_val'         => [0],
        'public_product_name' => [$this->product->name],
    ];

    $response = $this->actingAs($this->user)->putJson(route('sales.update', $sale->sale_id), $payload);

    $response->assertStatus(201);

    Notification::assertSentTo(
        [$this->user, $warehouseUser],
        SaleAlmacenNotification::class,
        function ($notification) use ($sale) {
            $data = $notification->toArray($this->user);
            return $notification->type === 'sale_edited'
                && $data['sale_id'] === $sale->sale_id
                && !empty($data['changes'])
                && collect($data['changes'])->contains(fn($c) => str_contains($c, 'Cantidad modificada'))
                && collect($data['changes'])->contains(fn($c) => str_contains($c, 'Vendedor modificado'))
                && collect($data['changes'])->contains(fn($c) => str_contains($c, 'Fecha modificada'));
        }
    );
});

test('almacenDetail view and getAlmacenDetail include recentEdits', function () {
    $sale = ($this->createSale)();

    // Insert an edit notification for this sale
    DB::table('notifications')->insert([
        'id'              => (string) \Illuminate\Support\Str::uuid(),
        'type'            => 'App\Notifications\SaleAlmacenNotification',
        'notifiable_type' => 'App\Models\User',
        'notifiable_id'   => $this->user->id,
        'data'            => json_encode([
            'sale_id' => $sale->sale_id,
            'folio'   => $sale->folio,
            'type'    => 'sale_edited',
            'title'   => 'Venta Folio ' . $sale->folio . ' Modificada',
            'message' => 'La venta Folio ' . $sale->folio . ' fue editada por Juan.',
            'changes' => ["Cantidad modificada para 'Insumo': de 10 a 20 (+10)"],
            'url'     => route('sales.almacen_detail', $sale->sale_id),
        ]),
        'read_at'         => null,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $response = $this->actingAs($this->user)->get(route('sales.almacen_detail', $sale->sale_id));

    $response->assertStatus(200);
    $response->assertSee('Alerta: Este pedido ha sido editado');
    $response->assertSee("Cantidad modificada para &#039;Insumo&#039;: de 10 a 20 (+10)", false);
});

