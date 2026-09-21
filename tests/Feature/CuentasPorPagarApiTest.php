<?php

use App\Models\CxpDetail;
use App\Models\CxpPayment;
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

    // Create a base factura for testing
    $this->facturaId = DB::table('facturas')->insertGetId([
        'empresa'        => 'Proveedor Test S.A. de C.V.',
        'folio_factura'  => 'FAC-TEST-' . rand(10000, 99999),
        'tipo_documento' => 'factura',
        'subtotal'       => 10000.00,
        'iva'            => 1600.00,
        'total'          => 11600.00,
        'moneda'         => 'MXN',
        'tipo_cambio'    => 1.0,
        'departamento'   => 'Operaciones',
        'fecha_factura'  => now()->subDays(10)->toDateString(),
        'descripcion'    => 'Insumos de producción y suministros industriales',
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);

    // Create a base CxpDetail
    $this->cxp = CxpDetail::create([
        'factura_id'     => $this->facturaId,
        'semana'         => 38,
        'anio'           => now()->year,
        'fecha_pago'     => now()->addDays(5)->toDateString(),
        'estatus'        => 'PENDIENTE',
        'is_canceled'    => false,
        'comentarios'    => 'Programado para pago estándar',
        'comentario_img' => null,
    ]);
});

test('1. API GET /cuentas-por-pagar (Resumen con pendingPaymentsCount en formato JSON)', function () {
    $response = $this->actingAs($this->user)->getJson('/cuentas-por-pagar');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-pagar\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toHaveKey('pendingPaymentsCount');
});

test('2. API GET /cuentas-por-pagar/dashboard (Métricas y KPIs analíticos en JSON)', function () {
    $response = $this->actingAs($this->user)->getJson('/cuentas-por-pagar/dashboard');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-pagar/dashboard\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toHaveKeys([
        'totalPorPagar',
        'pagosVencidos',
        'proximosVencer',
        'pagadoEsteMes',
        'agingData',
        'proveedoresLabels',
        'mesesLabels'
    ]);
});

test('3. API GET /cuentas-por-pagar/datatable (Listado de facturas y balances)', function () {
    $response = $this->actingAs($this->user)->getJson('/cuentas-por-pagar/datatable');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-pagar/datatable\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json())->toHaveKey('data');
    expect($response->json('data'))->toBeArray();
});

test('4. API POST /cuentas-por-pagar/{id}/update (Actualización de semana, departamento, comentarios)', function () {
    $payload = [
        'semana'       => 40,
        'banco'        => 'BBVA Bancomer',
        'departamento' => 'Mantenimiento y Planta',
        'comentarios'  => 'Pago reprogramado para inicio de mes con visto bueno',
    ];

    $response = $this->actingAs($this->user)->postJson('/cuentas-por-pagar/' . $this->cxp->id . '/update', $payload);

    echo "\n\n>>> LLAMADA: POST /cuentas-por-pagar/{$this->cxp->id}/update\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.semana'))->toBe(40);
    expect($response->json('data.comentarios'))->toBe('Pago reprogramado para inicio de mes con visto bueno');
});

test('5. API POST /cuentas-por-pagar/{id}/payments (Registro de abono con recálculo de estatus y saldo)', function () {
    $payload = [
        'amount'      => 5000.00,
        'date'        => now()->toDateString(),
        'banco'       => 'BBVA Bancomer',
        'metodo_pago' => 'Transferencia Electrónica SPEI',
        'notas'       => 'Primer abono correspondiente al 50%',
    ];

    $response = $this->actingAs($this->user)->postJson('/cuentas-por-pagar/' . $this->cxp->id . '/payments', $payload);

    echo "\n\n>>> LLAMADA: POST /cuentas-por-pagar/{$this->cxp->id}/payments\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.amount'))->toEqual(5000);

    // Verify status updated to PARCIAL
    $this->cxp->refresh();
    expect($this->cxp->estatus)->toBe('PARCIAL');
});

test('6. API GET /cuentas-por-pagar/{id}/payments (Listado de abonos del detalle)', function () {
    // Add payment first
    $this->cxp->payments()->create([
        'amount'      => 3000.00,
        'date'        => now()->toDateString(),
        'banco'       => 'Santander',
        'metodo_pago' => 'Transferencia',
        'user_id'     => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)->getJson('/cuentas-por-pagar/' . $this->cxp->id . '/payments');

    echo "\n\n>>> LLAMADA: GET /cuentas-por-pagar/{$this->cxp->id}/payments\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('payments'))->toBeArray();
    expect(count($response->json('payments')))->toBeGreaterThan(0);
});

test('7. API PUT /cuentas-por-pagar/payments/{payment_id} (Edición de abono registrado)', function () {
    $payment = $this->cxp->payments()->create([
        'amount'      => 2000.00,
        'date'        => now()->toDateString(),
        'banco'       => 'Banamex',
        'metodo_pago' => 'Cheque',
        'user_id'     => $this->user->id,
    ]);

    $payload = [
        'amount'      => 2500.00,
        'date'        => now()->toDateString(),
        'banco'       => 'Banamex Corregido',
        'metodo_pago' => 'Transferencia',
        'notas'       => 'Monto ajustado tras revisión contable',
    ];

    $response = $this->actingAs($this->user)->putJson('/cuentas-por-pagar/payments/' . $payment->id, $payload);

    echo "\n\n>>> LLAMADA: PUT /cuentas-por-pagar/payments/{$payment->id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.amount'))->toEqual(2500);
});

test('8. API DELETE /cuentas-por-pagar/payments/{payment_id} (Eliminación de abono)', function () {
    $payment = $this->cxp->payments()->create([
        'amount'      => 1000.00,
        'date'        => now()->toDateString(),
        'banco'       => 'Banorte',
        'metodo_pago' => 'Transferencia',
        'user_id'     => $this->user->id,
    ]);

    $response = $this->actingAs($this->user)->deleteJson('/cuentas-por-pagar/payments/' . $payment->id);

    echo "\n\n>>> LLAMADA: DELETE /cuentas-por-pagar/payments/{$payment->id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect(CxpPayment::find($payment->id))->toBeNull();
});

test('9. API POST /cuentas-por-pagar/{id}/documents (Subida de documentos fiscales PDF y XML)', function () {
    $pdf = UploadedFile::fake()->create('factura_fiscal.pdf', 100, 'application/pdf');
    $xml = UploadedFile::fake()->create('factura_fiscal.xml', 50, 'text/xml');

    $response = $this->actingAs($this->user)->postJson('/cuentas-por-pagar/' . $this->cxp->id . '/documents', [
        'pdf' => $pdf,
        'xml' => $xml,
    ]);

    echo "\n\n>>> LLAMADA: POST /cuentas-por-pagar/{$this->cxp->id}/documents\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.pdf_path'))->not->toBeNull();
    expect($response->json('data.xml_path'))->not->toBeNull();
});

test('10. API DELETE /cuentas-por-pagar/{id}/comentario-img (Eliminación de comprobante adjunto a comentario)', function () {
    $this->cxp->update(['comentario_img' => 'uploads/comentarios_cxp/dummy_test.png']);

    $response = $this->actingAs($this->user)->deleteJson('/cuentas-por-pagar/' . $this->cxp->id . '/comentario-img');

    echo "\n\n>>> LLAMADA: DELETE /cuentas-por-pagar/{$this->cxp->id}/comentario-img\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    $this->cxp->refresh();
    expect($this->cxp->comentario_img)->toBeNull();
});

test('11. API POST /cuentas-por-pagar/{id}/cancel (Cancelación lógica de cuenta por pagar)', function () {
    $response = $this->actingAs($this->user)->postJson('/cuentas-por-pagar/' . $this->cxp->id . '/cancel');

    echo "\n\n>>> LLAMADA: POST /cuentas-por-pagar/{$this->cxp->id}/cancel\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.is_canceled'))->toBeTrue();
    expect($response->json('data.estatus'))->toBe('CANCELADO');
});
