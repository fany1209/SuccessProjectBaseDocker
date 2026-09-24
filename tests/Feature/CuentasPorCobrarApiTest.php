<?php

use App\Models\CxcDetail;
use App\Models\CxcPayment;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Storage::fake('public');

    $this->user = User::first() ?? User::factory()->create();
    Permission::findOrCreate('finance.show', 'web');
    $this->user->givePermissionTo('finance.show');

    // Create a confirmed sale for testing
    $randomSuffix = rand(100000, 999999);
    $saleId = DB::table('sales')->insertGetId([
        'seller' => 'Asesor de Ventas Test',
        'first_time' => 0,
        'is_customer' => 1,
        'purchase_order' => 'OC-TEST-' . $randomSuffix,
        'invoice' => 'FAC-CXC-' . $randomSuffix,
        'sale_type' => 'credit',
        'term' => '30 días',
        'date' => now()->subDays(45)->toDateString(),
        'folio' => rand(10000, 99999),
        'customer_id' => null,
        'prospect_id' => null,
        'user_id' => $this->user->id,
        'sales_status_id' => 1,
        'sector_id' => 1,
        'payment_status' => 'pending',
        'almacen_status' => 'confirmed',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Insert sale detail (table does not have timestamps)
    DB::table('sale_detail')->insert([
        'sale_id' => $saleId,
        'product_id' => 1,
        'quantity' => 10,
        'cost' => 500.00,
        'has_tax' => 1,
    ]);

    // Create CXC Detail for the sale
    $this->cxc = CxcDetail::create([
        'sale_id' => $saleId,
        'documento' => 'Factura',
        'metodo_pago' => 'PPD',
        'descripcion' => 'Venta a crédito de prueba',
        'estatus' => 'Pendiente',
        'is_canceled' => false,
        'fecha_conclusion' => null,
    ]);
});

test('1. API GET /cuentas-por-cobrar (JSON - Resumen con facturas pendientes)', function () {
    $response = $this->actingAs($this->user)->getJson('/cuentas-por-cobrar');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-cobrar (JSON)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toHaveKey('pendingPaymentsCount');
});

test('2. Web GET /cuentas-por-cobrar (Retorna vista Blade)', function () {
    $response = $this->actingAs($this->user)->get('/cuentas-por-cobrar');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-cobrar (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('finance.cuentas_por_cobrar.index');
    $response->assertViewHas('pendingPaymentsCount');
});

test('3. API GET /cuentas-por-cobrar/dashboard (JSON - Métricas y gráficos)', function () {
    $response = $this->actingAs($this->user)->getJson('/cuentas-por-cobrar/dashboard');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-cobrar/dashboard (JSON)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data'))->toHaveKeys([
        'facturasVencidas',
        'clientesAdeudo',
        'currentMonthPct',
        'chartLabels',
        'chartData',
    ]);
});

test('4. Web GET /cuentas-por-cobrar/dashboard (Retorna vista Blade)', function () {
    $response = $this->actingAs($this->user)->get('/cuentas-por-cobrar/dashboard');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-cobrar/dashboard (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('finance.cuentas_por_cobrar.dashboard');
    $response->assertViewHas(['facturasVencidas', 'chartLabels', 'chartData']);
});

test('5. Web GET /cuentas-por-cobrar/clientes (Retorna vista Blade)', function () {
    $response = $this->actingAs($this->user)->get('/cuentas-por-cobrar/clientes');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-cobrar/clientes (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('finance.cuentas_por_cobrar.clientes');
});

test('6. API GET /cuentas-por-cobrar/datatable (Estructura para DataTables)', function () {
    $response = $this->actingAs($this->user)->getJson('/cuentas-por-cobrar/datatable');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-cobrar/datatable\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
});

test('7. API POST /cuentas-por-cobrar/{id}/update (Actualización exitosa)', function () {
    $payload = [
        'documento' => 'Remisión',
        'metodo_pago' => 'PUE',
        'fecha_conclusion' => now()->addDays(15)->toDateString(),
        'descripcion' => 'Actualización de condiciones comerciales',
    ];

    $response = $this->actingAs($this->user)->postJson("/cuentas-por-cobrar/{$this->cxc->id}/update", $payload);

    echo "\n\n>>> LLAMADA: POST /cuentas-por-cobrar/{$this->cxc->id}/update\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.metodo_pago'))->toBe('PUE');
    expect($response->json('data.documento'))->toBe('Remisión');

    $this->assertDatabaseHas('cxc_details', [
        'id' => $this->cxc->id,
        'documento' => 'Remisión',
        'metodo_pago' => 'PUE',
    ]);
});

test('8. API POST /cuentas-por-cobrar/{id}/update (Validación con método de pago inválido)', function () {
    $payload = [
        'metodo_pago' => 'INVALIDO',
    ];

    $response = $this->actingAs($this->user)->postJson("/cuentas-por-cobrar/{$this->cxc->id}/update", $payload);

    echo "\n\n>>> LLAMADA: POST /cuentas-por-cobrar/{$this->cxc->id}/update con error de validación\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['metodo_pago']);
});

test('9. API POST /cuentas-por-cobrar/{id}/cancel (Cancelación exitosa)', function () {
    $response = $this->actingAs($this->user)->postJson("/cuentas-por-cobrar/{$this->cxc->id}/cancel");

    echo "\n\n>>> LLAMADA: POST /cuentas-por-cobrar/{$this->cxc->id}/cancel\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.is_canceled'))->toBeTrue();

    $this->assertDatabaseHas('cxc_details', [
        'id' => $this->cxc->id,
        'is_canceled' => 1,
    ]);
});

test('10. API POST /cuentas-por-cobrar/{id}/update (Rechaza edición en cuenta cancelada)', function () {
    $this->cxc->update(['is_canceled' => true]);

    $payload = [
        'metodo_pago' => 'PUE',
    ];

    $response = $this->actingAs($this->user)->postJson("/cuentas-por-cobrar/{$this->cxc->id}/update", $payload);

    echo "\n\n>>> LLAMADA: POST /cuentas-por-cobrar/{$this->cxc->id}/update en cuenta cancelada\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(403);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('message'))->toContain('No se puede editar una cuenta cancelada');
});

test('11. API GET /cuentas-por-cobrar/{id}/payments (Lista de pagos con compatibilidad dual)', function () {
    CxcPayment::create([
        'cxc_detail_id' => $this->cxc->id,
        'amount' => 1500.00,
        'date' => now()->toDateString(),
        'comprobante' => null,
    ]);

    $response = $this->actingAs($this->user)->getJson("/cuentas-por-cobrar/{$this->cxc->id}/payments");

    echo "\n\n>>> LLAMADA: GET /cuentas-por-cobrar/{$this->cxc->id}/payments\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('payments'))->toBeArray();
    expect($response->json('data'))->toBeArray();
    expect(count($response->json('payments')))->toBeGreaterThanOrEqual(1);
});

test('12. API POST /cuentas-por-cobrar/{id}/payments (Registro de abono exitoso)', function () {
    $file = UploadedFile::fake()->create('comprobante_pago.pdf', 100, 'application/pdf');

    $payload = [
        'amount' => 2000.00,
        'date' => now()->toDateString(),
        'comprobante' => $file,
    ];

    $response = $this->actingAs($this->user)->postJson("/cuentas-por-cobrar/{$this->cxc->id}/payments", $payload);

    echo "\n\n>>> LLAMADA: POST /cuentas-por-cobrar/{$this->cxc->id}/payments\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.amount'))->toEqual(2000);

    $this->assertDatabaseHas('cxc_payments', [
        'cxc_detail_id' => $this->cxc->id,
        'amount' => 2000.00,
    ]);

    $this->assertDatabaseHas('cxc_details', [
        'id' => $this->cxc->id,
        'estatus' => 'Parcial',
    ]);
});

test('13. API POST /cuentas-por-cobrar/{id}/payments (Rechaza pago en cuenta cancelada)', function () {
    $this->cxc->update(['is_canceled' => true]);

    $payload = [
        'amount' => 500.00,
        'date' => now()->toDateString(),
    ];

    $response = $this->actingAs($this->user)->postJson("/cuentas-por-cobrar/{$this->cxc->id}/payments", $payload);

    echo "\n\n>>> LLAMADA: POST /cuentas-por-cobrar/{$this->cxc->id}/payments en cuenta cancelada\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(403);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('message'))->toContain('No se pueden añadir pagos a una cuenta cancelada');
});

test('14. Web GET /cuentas-por-cobrar/export-excel (Descarga de archivo Excel)', function () {
    $response = $this->actingAs($this->user)->get('/cuentas-por-cobrar/export-excel');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-cobrar/export-excel\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    expect($response->getStatusCode())->toBe(200);
    expect($response->headers->get('content-type'))->toBe('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
