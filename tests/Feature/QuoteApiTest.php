<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteDetail;
use App\Models\QuoteStatus;
use App\Models\Sector;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->status = QuoteStatus::firstOrCreate(
        ['quotes_status_id' => 1],
        ['name' => 'Pendiente']
    );

    $this->statusApproved = QuoteStatus::firstOrCreate(
        ['quotes_status_id' => 2],
        ['name' => 'Aprobada']
    );

    $this->sector = Sector::first() ?? Sector::create([
        'name' => 'Sector Test ' . uniqid(),
        'code' => 'SEC' . rand(10, 99),
    ]);

    $randomSuffix = rand(10000, 99999);
    $this->customer = Customer::create([
        'sector_id'     => $this->sector->sector_id,
        'customer_code' => 'SCT' . $randomSuffix,
        'name'          => 'Cliente Test ' . $randomSuffix,
        'email'         => 'cotizaciones.' . $randomSuffix . '@test.com',
        'phone'         => '4421234567',
    ]);

    $this->category = Category::first() ?? Category::create([
        'name' => 'Categoría Test ' . uniqid(),
    ]);

    $this->product = Product::create([
        'name'         => 'Producto Cotizado ' . uniqid(),
        'category_id'  => $this->category->category_id,
        'sat_code'     => '10101501',
        'sku'          => 'SKU-COT-' . rand(10000, 99999),
        'presentation' => 'Bulto 25kg',
        'unit'         => 'kg',
    ]);

    $this->createQuote = function (array $overrides = []) {
        $num = rand(1000000, 9999999);

        return Quote::create(array_merge([
            'folio'            => 'SCT-' . $num,
            'company'          => 'Empresa Test ' . $num,
            'date'             => '2026-10-05',
            'currency'         => 'MXN',
            'attention'        => 'Lic. Roberto Gómez',
            'department'       => 'Compras',
            'phone'            => '4421234567',
            'quotes_status_id' => $this->status->quotes_status_id,
            'user_id'          => $this->user->id,
        ], $overrides));
    };
});

test('index renders quotes view for web request', function () {
    $response = $this->actingAs($this->user)->get(route('quotes'));

    $response->assertStatus(200);
    $response->assertViewIs('quotes');
});

test('index returns json list when requested with json header', function () {
    $response = $this->actingAs($this->user)
        ->getJson(route('quotes'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'flag',
        'code',
        'message',
        'data' => [
            'quotes',
            'sectors',
            'db_products',
            'db_customers',
        ],
    ]);
});

test('create renders create quote form', function () {
    $response = $this->actingAs($this->user)->get(route('quotes.create'));

    $response->assertStatus(200);
    $response->assertViewIs('sales.quotes.form-create');
});

test('create returns json payload when requested with json header', function () {
    $response = $this->actingAs($this->user)
        ->getJson(route('quotes.create'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'db_products',
            'db_customers',
            'newFolio',
        ],
    ]);
});

test('store successfully creates quote and details with generated folio', function () {
    $product2 = Product::create([
        'name'         => 'Servicio Extra ' . uniqid(),
        'category_id'  => $this->category->category_id,
        'sat_code'     => '10101501',
        'sku'          => 'SKU-EXTRA-' . rand(10000, 99999),
        'presentation' => 'Servicio',
        'unit'         => 'srv',
    ]);

    $payload = [
        'company'                 => 'Empresa Compradora S.A. de C.V.',
        'date'                    => '2026-10-05',
        'currency'                => 'MXN',
        'attention'               => 'Ing. Roberto Gómez',
        'department'              => 'Compras',
        'phone'                   => '4429988776',
        'place_of_delivery'       => 'Planta Querétaro',
        'transport_specification' => 'Entrega en anden 3',
        'deadline'                => '2026-10-20',
        'terms'                   => 'Crédito a 30 días',
        'notes'                   => 'Precios incluyen flete local.',
        'quotes_status_id'        => $this->status->quotes_status_id,
        'products'                => [
            [
                'product_id'         => $this->product->product_id,
                'quote_product_name' => $this->product->name,
                'quantity'           => 10,
                'cost'               => 150.50,
                'presentation'       => 'Bulto 25kg',
                'unit'               => 'kg',
                'iva'                => 0.16,
            ],
            [
                'product_id'         => $product2->product_id,
                'quote_product_name' => 'Servicio Extra de Flete',
                'quantity'           => 1,
                'cost'               => 500.00,
                'presentation'       => 'Servicio',
                'unit'               => 'srv',
                'iva'                => 0.16,
            ],
        ],
    ];

    $response = $this->actingAs($this->user)
        ->postJson(route('quotes.store'), $payload);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'success',
        'flag',
        'message',
        'data' => [
            'quote' => [
                'quote_id',
                'folio',
                'company',
                'details',
            ],
            'folio',
        ],
    ]);

    $quoteId = $response->json('data.quote.quote_id');

    $this->assertDatabaseHas('quotes', [
        'quote_id'         => $quoteId,
        'company'          => 'Empresa Compradora S.A. de C.V.',
        'quotes_status_id' => $this->status->quotes_status_id,
    ]);

    $this->assertDatabaseHas('quote_detail', [
        'quote_id'           => $quoteId,
        'quote_product_name' => 'Servicio Extra de Flete',
        'quantity'           => 1,
    ]);
});

test('store fails validation when company or products are missing', function () {
    $response = $this->actingAs($this->user)
        ->postJson(route('quotes.store'), [
            'date'             => '2026-10-05',
            'quotes_status_id' => $this->status->quotes_status_id,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['company', 'products']);
});

test('store sanitizes input and removes potential XSS tags', function () {
    $payload = [
        'company'                 => '<b>Empresa Blindada</b>',
        'date'                    => '2026-10-05',
        'currency'                => 'MXN',
        'attention'               => '<b>Lic. Juan</b>',
        'notes'                   => '<span>Notas limpias</span>',
        'quotes_status_id'        => $this->status->quotes_status_id,
        'products'                => [
            [
                'product_id'         => $this->product->product_id,
                'quote_product_name' => '<i>Producto Sanitizado</i>',
                'quantity'           => 2,
                'cost'               => 100,
                'presentation'       => 'Pza',
                'unit'               => 'pz',
                'iva'                => 0.16,
            ],
        ],
    ];

    $response = $this->actingAs($this->user)
        ->postJson(route('quotes.store'), $payload);

    $response->assertStatus(201);
    $quoteId = $response->json('data.quote.quote_id');

    $this->assertDatabaseHas('quotes', [
        'quote_id'  => $quoteId,
        'company'   => 'Empresa Blindada',
        'attention' => 'Lic. Juan',
        'notes'     => 'Notas limpias',
    ]);

    $this->assertDatabaseHas('quote_detail', [
        'quote_id'           => $quoteId,
        'quote_product_name' => 'Producto Sanitizado',
    ]);
});

test('show returns ok view for existing quote', function () {
    $quote = ($this->createQuote)();

    $response = $this->actingAs($this->user)
        ->get(route('quotes.show', $quote->quote_id));

    $response->assertStatus(200);
    $response->assertViewIs('sales.quotes.show');
});

test('show returns json for existing quote when requested', function () {
    $quote = ($this->createQuote)(['company' => 'Empresa Json Show']);

    QuoteDetail::create([
        'quote_id'           => $quote->quote_id,
        'product_id'         => $this->product->product_id,
        'quote_product_name' => 'Item Show',
        'quantity'           => 5,
        'cost'               => 200,
        'presentation'       => 'Caja',
        'unit'               => 'cj',
        'iva'                => 0.16,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson(route('quotes.show', $quote->quote_id));

    $response->assertStatus(200);
    $response->assertJsonPath('data.quote_id', $quote->quote_id);
    $response->assertJsonPath('data.company', 'Empresa Json Show');
    $this->assertCount(1, $response->json('data.details'));
});

test('show returns 404 for non-existent quote', function () {
    $response = $this->actingAs($this->user)
        ->getJson(route('quotes.show', 99999999));

    $response->assertStatus(404);
});

test('edit returns ok view for existing quote', function () {
    $quote = ($this->createQuote)();

    $response = $this->actingAs($this->user)
        ->get(route('quotes.edit', $quote->quote_id));

    $response->assertStatus(200);
    $response->assertViewIs('sales.quotes.edit');
});

test('edit returns json data for existing quote', function () {
    $quote = ($this->createQuote)(['company' => 'Empresa Edit Json']);

    $response = $this->actingAs($this->user)
        ->getJson(route('quotes.edit', $quote->quote_id));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'quote',
            'db_products',
            'statuses',
            'db_customers',
        ],
    ]);
});

test('edit returns 404 for non-existent quote', function () {
    $response = $this->actingAs($this->user)
        ->getJson(route('quotes.edit', 99999999));

    $response->assertStatus(404);
});

test('update modifies quote attributes and recreates detail lines', function () {
    $quote = ($this->createQuote)(['company' => 'Empresa Inicial']);

    QuoteDetail::create([
        'quote_id'           => $quote->quote_id,
        'product_id'         => $this->product->product_id,
        'quote_product_name' => 'Detalle Viejo',
        'quantity'           => 1,
        'cost'               => 50,
        'presentation'       => 'Pza',
        'unit'               => 'pz',
        'iva'                => 0.16,
    ]);

    $updatePayload = [
        'company'                 => 'Empresa Actualizada S.A.',
        'date'                    => '2026-10-06',
        'currency'                => 'USD',
        'attention'               => 'Lic. Mariana Soto',
        'department'              => 'Logística',
        'quotes_status_id'        => $this->statusApproved->quotes_status_id,
        'products'                => [
            [
                'product_id'         => $this->product->product_id,
                'quote_product_name' => 'Detalle Nuevo Reemplazado',
                'quantity'           => 15,
                'cost'               => 75.00,
                'presentation'       => 'Tarima',
                'unit'               => 'trm',
                'iva'                => 0.16,
            ],
        ],
    ];

    $response = $this->actingAs($this->user)
        ->putJson(route('quotes.update', $quote->quote_id), $updatePayload);

    $response->assertStatus(200);

    $this->assertDatabaseHas('quotes', [
        'quote_id'         => $quote->quote_id,
        'company'          => 'Empresa Actualizada S.A.',
        'currency'         => 'USD',
        'attention'        => 'Lic. Mariana Soto',
        'quotes_status_id' => $this->statusApproved->quotes_status_id,
    ]);

    $this->assertDatabaseMissing('quote_detail', [
        'quote_id'           => $quote->quote_id,
        'quote_product_name' => 'Detalle Viejo',
    ]);

    $this->assertDatabaseHas('quote_detail', [
        'quote_id'           => $quote->quote_id,
        'quote_product_name' => 'Detalle Nuevo Reemplazado',
        'quantity'           => 15,
    ]);
});

test('update returns 404 for non-existent quote', function () {
    $response = $this->actingAs($this->user)
        ->putJson(route('quotes.update', 99999999), [
            'company'  => 'Non existent',
            'products' => [
                [
                    'product_id'         => $this->product->product_id,
                    'quote_product_name' => 'Test',
                    'quantity'           => 1,
                    'cost'               => 10,
                ],
            ],
        ]);

    $response->assertStatus(404);
});

test('updateStatus updates quote status successfully', function () {
    $quote = ($this->createQuote)();

    $response = $this->actingAs($this->user)
        ->patchJson(route('quotes.updateStatus', $quote->quote_id), [
            'quotes_status_id' => $this->statusApproved->quotes_status_id,
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('quotes', [
        'quote_id'         => $quote->quote_id,
        'quotes_status_id' => $this->statusApproved->quotes_status_id,
    ]);
});

test('updateStatus fails validation on invalid quotes_status_id', function () {
    $quote = ($this->createQuote)();

    $response = $this->actingAs($this->user)
        ->patchJson(route('quotes.updateStatus', $quote->quote_id), [
            'quotes_status_id' => 999999,
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['quotes_status_id']);
});

test('destroy removes quote and its details successfully', function () {
    $quote = ($this->createQuote)();

    QuoteDetail::create([
        'quote_id'           => $quote->quote_id,
        'product_id'         => $this->product->product_id,
        'quote_product_name' => 'Detalle A Eliminar',
        'quantity'           => 3,
        'cost'               => 120,
        'presentation'       => 'Pza',
        'unit'               => 'pz',
        'iva'                => 0.16,
    ]);

    $response = $this->actingAs($this->user)
        ->deleteJson(route('quotes.destroy', $quote->quote_id));

    $response->assertStatus(200);

    $this->assertDatabaseMissing('quotes', [
        'quote_id' => $quote->quote_id,
    ]);

    $this->assertDatabaseMissing('quote_detail', [
        'quote_id' => $quote->quote_id,
    ]);
});

test('destroy returns 404 for non-existent quote', function () {
    $response = $this->actingAs($this->user)
        ->deleteJson(route('quotes.destroy', 99999999));

    $response->assertStatus(404);
});

test('generatePdf downloads pdf report for existing quote', function () {
    $quote = ($this->createQuote)();

    QuoteDetail::create([
        'quote_id'           => $quote->quote_id,
        'product_id'         => $this->product->product_id,
        'quote_product_name' => 'Producto Para PDF',
        'quantity'           => 5,
        'cost'               => 100,
        'presentation'       => 'Saco',
        'unit'               => 'sc',
        'iva'                => 0.16,
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('quotes.pdf', $quote->quote_id));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});

test('generatePdf returns 404 for non-existent quote', function () {
    $response = $this->actingAs($this->user)
        ->get(route('quotes.pdf', 99999999));

    $response->assertStatus(404);
});
