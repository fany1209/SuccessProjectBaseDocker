<?php

use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    Permission::findOrCreate('finance.show', 'web');
    $this->user->givePermissionTo('finance.show');

    $this->factura = Factura::create([
        'tipo_documento'  => 'factura',
        'insumo'          => 'directo',
        'empresa'         => 'PROVEEDOR INDUSTRIAL S.A. DE C.V.',
        'folio_factura'   => 'FAC-TEST-' . rand(1000, 9999),
        'fecha_factura'   => now()->format('Y-m-d'),
        'moneda'          => 'MXN',
        'tipo_cambio'     => 1.0,
        'departamento'    => 'Producción',
        'descripcion'     => 'Factura de prueba inicial',
        'subtotal'        => 1000.0,
        'descuento_total' => 0.0,
        'iva'             => 160.0,
        'traslado_total'  => 0.0,
        'retencion_total' => 0.0,
        'total'           => 1160.0,
    ]);

    $this->detalle = FacturaDetalle::create([
        'factura_id'               => $this->factura->factura_id,
        'producto'                 => 'Levadura Seca Especial 25kg',
        'clave_sat'                => '50202306',
        'unidad'                   => 'KGM',
        'cantidad'                 => 10,
        'precio_unitario'          => 100.0,
        'precio'                   => 1000.0,
        'subtotal'                 => 1000.0,
        'aplica_iva'               => true,
        'iva_porcentaje'           => 16.0,
        'otro_impuesto_porcentaje' => 0.0,
        'impuesto_total'           => 160.0,
        'traslado'                 => 0.0,
        'retencion'                => 0.0,
        'descuento'                => 0.0,
        'isr'                      => 0.0,
        'ilc'                      => 0.0,
    ]);

    DB::table('cxp_details')->insert([
        'factura_id'  => $this->factura->factura_id,
        'fecha_pago'  => null,
        'semana'      => now()->weekOfYear,
        'anio'        => now()->year,
        'estatus'     => 'PENDIENTE',
        'is_canceled' => false,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);
});

test('1. Web GET /facturas (Retorna vista Blade finance.facturas.index)', function () {
    $response = $this->actingAs($this->user)->get('/facturas');

    echo "\n\n>>> LLAMADA: GET /facturas (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('finance.facturas.index');
});

test('2. API GET /facturas/datatable (Listado de facturas para DataTables)', function () {
    $response = $this->actingAs($this->user)->getJson('/facturas/datatable');

    echo "\n\n>>> LLAMADA: GET /facturas/datatable (JSON)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON (primeros registros):\n" . json_encode(array_slice($response->json('data'), 0, 2), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('data'))->toBeArray();
    expect(count($response->json('data')))->toBeGreaterThanOrEqual(1);

    $row = collect($response->json('data'))->firstWhere('factura_id', $this->factura->factura_id);
    expect($row)->not->toBeNull();
    expect($row['empresa'])->toBe('PROVEEDOR INDUSTRIAL S.A. DE C.V.');
    expect($row['folio_factura'])->toBe($this->factura->folio_factura);
});

test('3. API GET /facturas/{id} (Consulta de factura y sus partidas)', function () {
    $response = $this->actingAs($this->user)->getJson("/facturas/{$this->factura->factura_id}");

    echo "\n\n>>> LLAMADA: GET /facturas/{id} (JSON)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('factura.factura_id'))->toBe($this->factura->factura_id);
    expect($response->json('detalles'))->toBeArray();
    expect(count($response->json('detalles')))->toBe(1);
    expect($response->json('detalles.0.producto'))->toBe('Levadura Seca Especial 25kg');
});

test('4. API GET /facturas/{id} (404 al consultar factura inexistente)', function () {
    $response = $this->actingAs($this->user)->getJson('/facturas/999999');

    echo "\n\n>>> LLAMADA: GET /facturas/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('5. API POST /facturas (Creación de factura con sanitización anti-XSS y relaciones)', function () {
    $folio = 'FAC-NUEVA-' . rand(1000, 9999);
    $payload = [
        'tipo_documento' => 'factura',
        'insumo'         => 'directo',
        'empresa'        => '<b>DISTRIBUIDORA QUÍMICA S.A.</b>',
        'folio_factura'  => $folio,
        'fecha_factura'  => '2026-09-25',
        'moneda'         => 'MXN',
        'tipo_cambio'    => 1.0,
        'departamento'   => '<span>Almacén Central</span>',
        'descripcion'    => '<p>Compra mensual de aditivos</p>',
        'productos'      => [
            [
                'producto'        => '<b>Ácido Cítrico Anhidro</b>',
                'clave_sat'       => '50202301',
                'unidad'          => 'KGM',
                'cantidad'        => 5,
                'precio_unitario' => 200,
                'aplica_iva'      => true,
                'iva_porcentaje'  => 16,
                'descuento'       => 50,
            ],
            [
                'producto'        => '<i>Enzima Pectolítica</i>',
                'clave_sat'       => '50202302',
                'unidad'          => 'LTR',
                'cantidad'        => 2,
                'precio_unitario' => 500,
                'aplica_iva'      => false,
            ],
        ],
    ];

    $response = $this->actingAs($this->user)->postJson('/facturas', $payload);

    echo "\n\n>>> LLAMADA: POST /facturas (Creación exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);

    $createdFactura = $response->json('data');
    expect($createdFactura['empresa'])->toBe('DISTRIBUIDORA QUÍMICA S.A.');
    expect($createdFactura['departamento'])->toBe('Almacén Central');
    expect($createdFactura['descripcion'])->toBe('Compra mensual de aditivos');

    // Cálculos:
    // P1: cant=5, pu=200, importe=1000, desc=50, base=950, iva=950*0.16=152
    // P2: cant=2, pu=500, importe=1000, desc=0, base=1000, iva=0
    // Subtotal: 2000, Descuento: 50, IVA: 152, Total: 2000 - 50 + 152 = 2102
    expect($createdFactura['subtotal'])->toEqual(2000);
    expect($createdFactura['descuento_total'])->toEqual(50);
    expect($createdFactura['iva'])->toEqual(152);
    expect($createdFactura['total'])->toEqual(2102);

    $this->assertDatabaseHas('facturas', [
        'factura_id'    => $createdFactura['factura_id'],
        'folio_factura' => $folio,
        'empresa'       => 'DISTRIBUIDORA QUÍMICA S.A.',
    ]);

    $this->assertDatabaseHas('factura_detalles', [
        'factura_id' => $createdFactura['factura_id'],
        'producto'   => 'Ácido Cítrico Anhidro',
    ]);

    $this->assertDatabaseHas('supplier_prices', [
        'factura_id' => $createdFactura['factura_id'],
        'insumo'     => 'Ácido Cítrico Anhidro',
    ]);

    $this->assertDatabaseHas('cxp_details', [
        'factura_id' => $createdFactura['factura_id'],
        'estatus'    => 'PENDIENTE',
    ]);
});

test('6. API POST /facturas (422 Validación por campos obligatorios faltantes)', function () {
    $payload = [
        'empresa' => '',
        'moneda'  => 'INVALID',
    ];

    $response = $this->actingAs($this->user)->postJson('/facturas', $payload);

    echo "\n\n>>> LLAMADA: POST /facturas (422 Validation Error)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['empresa', 'tipo_documento', 'insumo', 'folio_factura', 'fecha_factura', 'moneda', 'productos']);
});

test('7. API PATCH /facturas/{id} (Actualización exitosa de factura y recálculo)', function () {
    $payload = [
        'tipo_documento' => 'factura',
        'insumo'         => 'directo',
        'empresa'        => 'PROVEEDOR INDUSTRIAL RENOVADO S.A.',
        'folio_factura'  => $this->factura->folio_factura . '-REV',
        'fecha_factura'  => '2026-09-26',
        'moneda'         => 'MXN',
        'tipo_cambio'    => 1.0,
        'departamento'   => 'Mantenimiento',
        'descripcion'    => 'Factura actualizada',
        'productos'      => [
            [
                'producto'        => 'Levadura Seca Premium 50kg',
                'clave_sat'       => '50202306',
                'unidad'          => 'KGM',
                'cantidad'        => 20,
                'precio_unitario' => 150.0,
                'aplica_iva'      => true,
                'iva_porcentaje'  => 16.0,
                'descuento'       => 0.0,
            ],
        ],
    ];

    $response = $this->actingAs($this->user)->patchJson("/facturas/{$this->factura->factura_id}", $payload);

    echo "\n\n>>> LLAMADA: PATCH /facturas/{id} (Actualización exitosa)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data.empresa'))->toBe('PROVEEDOR INDUSTRIAL RENOVADO S.A.');
    expect($response->json('data.subtotal'))->toEqual(3000);
    expect($response->json('data.iva'))->toEqual(480);
    expect($response->json('data.total'))->toEqual(3480);

    $this->assertDatabaseHas('facturas', [
        'factura_id' => $this->factura->factura_id,
        'empresa'    => 'PROVEEDOR INDUSTRIAL RENOVADO S.A.',
        'total'      => 3480,
    ]);

    $this->assertDatabaseHas('factura_detalles', [
        'factura_id' => $this->factura->factura_id,
        'producto'   => 'Levadura Seca Premium 50kg',
    ]);
});

test('8. API PATCH /facturas/{id} (404 al intentar actualizar factura inexistente)', function () {
    $payload = [
        'tipo_documento' => 'factura',
        'insumo'         => 'directo',
        'empresa'        => 'EMPRESA X',
        'folio_factura'  => 'FX-999',
        'fecha_factura'  => '2026-09-26',
        'moneda'         => 'MXN',
        'tipo_cambio'    => 1.0,
        'productos'      => [
            [
                'producto'        => 'Producto X',
                'cantidad'        => 1,
                'precio_unitario' => 100,
                'aplica_iva'      => true,
            ],
        ],
    ];

    $response = $this->actingAs($this->user)->patchJson('/facturas/999999', $payload);

    echo "\n\n>>> LLAMADA: PATCH /facturas/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('9. API DELETE /facturas/{id} (Eliminación exitosa de factura sin pagos aplicados)', function () {
    $response = $this->actingAs($this->user)->deleteJson("/facturas/{$this->factura->factura_id}");

    echo "\n\n>>> LLAMADA: DELETE /facturas/{id} (Eliminación exitosa sin pagos)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('message'))->toBe('Documento eliminado correctamente.');

    $this->assertDatabaseMissing('facturas', [
        'factura_id' => $this->factura->factura_id,
    ]);
});

test('10. API DELETE /facturas/{id} (422 Bloqueo de eliminación si tiene pagos aplicados sin force)', function () {
    $cxpDetail = DB::table('cxp_details')->where('factura_id', $this->factura->factura_id)->first();
    DB::table('cxp_payments')->insert([
        'cxp_detail_id'   => $cxpDetail->id,
        'amount'          => 500,
        'date'            => now()->format('Y-m-d'),
        'metodo_pago'     => 'TRANSFERENCIA',
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/facturas/{$this->factura->factura_id}");

    echo "\n\n>>> LLAMADA: DELETE /facturas/{id} (422 Bloqueo por pagos)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('has_payments'))->toBeTrue();
    expect($response->json('code'))->toBe(422);

    $this->assertDatabaseHas('facturas', [
        'factura_id' => $this->factura->factura_id,
    ]);
});

test('11. API DELETE /facturas/{id}?force=1 (Eliminación forzada exitosa incluso con pagos)', function () {
    $cxpDetail = DB::table('cxp_details')->where('factura_id', $this->factura->factura_id)->first();
    DB::table('cxp_payments')->insert([
        'cxp_detail_id'   => $cxpDetail->id,
        'amount'          => 500,
        'date'            => now()->format('Y-m-d'),
        'metodo_pago'     => 'TRANSFERENCIA',
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/facturas/{$this->factura->factura_id}?force=1");

    echo "\n\n>>> LLAMADA: DELETE /facturas/{id}?force=1 (Eliminación forzada)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('code'))->toBe(200);

    $this->assertDatabaseMissing('facturas', [
        'factura_id' => $this->factura->factura_id,
    ]);
});

test('12. API DELETE /facturas/{id} (404 al intentar eliminar factura inexistente)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/facturas/999999');

    echo "\n\n>>> LLAMADA: DELETE /facturas/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});
