<?php

use App\Models\Factura;
use App\Models\User;
use App\Http\Repositories\Pago\PagoRepository;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();
    Permission::findOrCreate('finance.show', 'web');
    $this->user->givePermissionTo('finance.show');
});

it('renders pagos index view with facturas for authenticated user', function () {
    Factura::create([
        'tipo_documento'  => 'factura',
        'insumo'          => 'directo',
        'empresa'         => 'Empresa Test',
        'folio_factura'   => 'FOL-123',
        'fecha_factura'   => now()->format('Y-m-d'),
        'moneda'          => 'MXN',
        'tipo_cambio'     => 1.0,
        'departamento'    => 'Finanzas',
        'subtotal'        => 1500.50,
        'descuento_total' => 0.0,
        'iva'             => 240.08,
        'total'           => 1740.58,
    ]);

    $response = $this->actingAs($this->user)->get(route('pagos.index'));

    $response->assertOk();
    $response->assertViewIs('finance.pagos.index');
    $response->assertViewHas('facturas');
});

it('handles exception in pagos index and returns error response', function () {
    $mockRepo = Mockery::mock(PagoRepository::class);
    $mockRepo->shouldReceive('getFacturasForPagos')->andThrow(new Exception('DB failure'));
    $this->app->instance(PagoRepository::class, $mockRepo);

    $response = $this->actingAs($this->user)->get(route('pagos.index'));

    $response->assertStatus(500);
    $response->assertJson([
        'success' => false,
        'message' => 'Error al cargar la programación de pagos.',
    ]);
});
