<?php

use App\Models\Customer;
use App\Models\FinancePayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    Permission::findOrCreate('finance.show', 'web');
    $this->user->givePermissionTo('finance.show');

    $this->payment = FinancePayment::create([
        'empresa'       => 'SERVICIOS INDUSTRIALES S.A. DE C.V.',
        'cantidad'      => 25000.75,
        'motivo'        => 'Pago por suministros y mantenimiento general',
        'banco'         => 'BBVA Bancomer',
        'factura'       => 'FAC-TEST-999',
        'fecha_factura' => now()->format('Y-m-d'),
        'fecha_pago'    => now()->format('Y-m-d'),
        'semana'        => 39,
        'anio'          => 2026,
        'estatus'       => 'PENDIENTE',
        'comentarios'   => 'Transferencia',
        'terminacion'   => null,
        'efectivo'      => null,
    ]);

    $this->customer = Customer::first() ?? Customer::create([
        'name'     => 'CLIENTE DE PRUEBAS FINANCIERAS',
        'contact'  => 'Ing. Carlos Mendoza',
        'phone'    => '5551234567',
        'email'    => 'carlos@testcliente.com',
        'vendedor' => 'Vendedor Demo',
    ]);
});

afterEach(function () {
    if (isset($this->payment) && $this->payment->exists) {
        FinancePayment::where('id', $this->payment->id)->delete();
    }
});

test('1. Web GET /finance (Renderizar vista principal con pagos)', function () {
    $response = $this->actingAs($this->user)->get('/finance');

    echo "\n\n>>> LLAMADA: GET /finance (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('finance');
    $response->assertViewHas('payments');
});

test('2. API GET /finance?semana=39&anio=2026 (Consulta JSON filtrada de pagos)', function () {
    $response = $this->actingAs($this->user)->getJson('/finance?semana=39&anio=2026');

    echo "\n\n>>> LLAMADA: GET /finance?semana=39&anio=2026 (JSON Filtered)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data'))->toBeArray();
});

test('3. API GET /finance/datatable (Listado de pagos con formato DataTables)', function () {
    $response = $this->actingAs($this->user)->getJson('/finance/datatable');

    echo "\n\n>>> LLAMADA: GET /finance/datatable (DataTables Data)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "TOTAL REGISTROS: " . count($response->json('data') ?? []) . "\n";

    $response->assertStatus(200);
    expect($response->json('data'))->toBeArray();
    expect($response->json('success'))->toBeTrue();
});

test('4. API POST /finance (Creación exitosa de nuevo pago)', function () {
    $payload = [
        'empresa'       => 'DISTRIBUIDORA AGRICOLA DEL NORTE',
        'cantidad'      => 14500.50,
        'motivo'        => 'Adquisición de fertilizantes fosfatados',
        'banco'         => 'Banamex',
        'factura'       => 'FAC-AGRO-2026',
        'fecha_factura' => '2026-09-20',
        'fecha_pago'    => '2026-09-28',
        'semana'        => 39,
        'anio'          => 2026,
        'estatus'       => 'PENDIENTE',
        'comentarios'   => 'Tarjeta',
        'terminacion'   => '4589',
        'efectivo'      => null,
    ];

    $response = $this->actingAs($this->user)->postJson('/finance', $payload);

    echo "\n\n>>> LLAMADA: POST /finance (Create Payment)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('id'))->toBeGreaterThan(0);
    expect($response->json('data.empresa'))->toBe('DISTRIBUIDORA AGRICOLA DEL NORTE');
    expect($response->json('data.terminacion'))->toBe('4589');

    // Limpieza
    FinancePayment::where('id', $response->json('id'))->delete();
});

test('5. API POST /finance (Validación defensiva 422 ante campos obligatorios faltantes)', function () {
    $response = $this->actingAs($this->user)->postJson('/finance', []);

    echo "\n\n>>> LLAMADA: POST /finance (422 Validation Error)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['empresa', 'cantidad', 'motivo', 'factura', 'semana', 'anio', 'estatus']);
});

test('6. API GET /finance/{id} (Consulta individual de pago)', function () {
    $response = $this->actingAs($this->user)->getJson("/finance/{$this->payment->id}");

    echo "\n\n>>> LLAMADA: GET /finance/{id} (Show Payment)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.id'))->toBe($this->payment->id);
    expect($response->json('data.empresa'))->toBe($this->payment->empresa);
});

test('7. API GET /finance/{id} (404 al consultar pago inexistente)', function () {
    $response = $this->actingAs($this->user)->getJson('/finance/999999');

    echo "\n\n>>> LLAMADA: GET /finance/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
});

test('8. API PATCH /finance/{id} (Actualización completa de pago con bloqueo pesimista)', function () {
    $payload = [
        'empresa'       => 'SERVICIOS INDUSTRIALES EDITADO',
        'cantidad'      => 32000.00,
        'banco'         => 'Santander',
        'factura'       => 'FAC-TEST-999-REV',
        'motivo'        => 'Mantenimiento preventivo mayor y refacciones',
        'fecha_factura' => '2026-09-25',
        'fecha_pago'    => '2026-09-28',
        'semana'        => 39,
        'anio'          => 2026,
        'estatus'       => 'PENDIENTE',
        'comentarios'   => 'Efectivo',
        'terminacion'   => null,
        'efectivo'      => 'Si',
    ];

    $response = $this->actingAs($this->user)->patchJson("/finance/{$this->payment->id}", $payload);

    echo "\n\n>>> LLAMADA: PATCH /finance/{id} (Update Payment)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.empresa'))->toBe('SERVICIOS INDUSTRIALES EDITADO');
    expect($response->json('data.efectivo'))->toBe('Si');
});

test('9. API PATCH /finance/{id}/status (Cambio de estatus operativo a PAGADO)', function () {
    $response = $this->actingAs($this->user)->patchJson("/finance/{$this->payment->id}/status", [
        'estatus' => 'PAGADO',
    ]);

    echo "\n\n>>> LLAMADA: PATCH /finance/{id}/status (Update Status)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($this->payment->fresh()->estatus)->toBe('PAGADO');
});

test('10. API PATCH /finance/{id}/status (422 al enviar estatus inválido)', function () {
    $response = $this->actingAs($this->user)->patchJson("/finance/{$this->payment->id}/status", [
        'estatus' => 'ESTATUS_NO_PERMITIDO',
    ]);

    echo "\n\n>>> LLAMADA: PATCH /finance/{id}/status (422 Invalid Status)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['estatus']);
});

test('11. API DELETE /finance/{id} (Eliminación segura de pago)', function () {
    $response = $this->actingAs($this->user)->deleteJson("/finance/{$this->payment->id}");

    echo "\n\n>>> LLAMADA: DELETE /finance/{id} (Delete Payment)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect(FinancePayment::where('id', $this->payment->id)->exists())->toBeFalse();
});

test('12. Web GET /finance/historial-compras (Vista principal del historial de clientes)', function () {
    $response = $this->actingAs($this->user)->get('/finance/historial-compras');

    echo "\n\n>>> LLAMADA: GET /finance/historial-compras (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('finance.historial-compras.index');
    $response->assertViewHas('customers');
});

test('13. API GET /finance/historial-compras/dashboard (Métricas globales de compras y clientes)', function () {
    $response = $this->actingAs($this->user)->getJson('/finance/historial-compras/dashboard');

    echo "\n\n>>> LLAMADA: GET /finance/historial-compras/dashboard (Dashboard Analytics)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data'))->toHaveKeys([
        'frequent_customers',
        'inactive_customers',
        'best_customers',
        'max_amount',
        'top_products',
        'max_qty',
    ]);
});

test('14. API GET /finance/historial-compras/{customerId} (Historial detallado y analítica por cliente)', function () {
    $customerId = $this->customer->customer_id ?? $this->customer->id;
    $response = $this->actingAs($this->user)->getJson("/finance/historial-compras/{$customerId}");

    echo "\n\n>>> LLAMADA: GET /finance/historial-compras/{customerId} (Customer Purchase History)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data'))->toHaveKeys([
        'customer_name',
        'contact_name',
        'total_purchased',
        'charts',
    ]);
});

test('15. API GET /finance/historial-compras/{customerId} (404 al consultar cliente inexistente)', function () {
    $response = $this->actingAs($this->user)->getJson('/finance/historial-compras/999999');

    echo "\n\n>>> LLAMADA: GET /finance/historial-compras/999999 (404 Customer Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
});
