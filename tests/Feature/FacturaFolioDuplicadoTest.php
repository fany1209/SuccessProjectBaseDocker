<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('permite crear y actualizar facturas con folios repetidos como NA o 00', function () {
    $payload1 = [
        'tipo_documento' => 'factura',
        'insumo'         => 'directo',
        'empresa'        => 'Proveedor Test A',
        'folio_factura'  => 'NA',
        'fecha_factura'  => now()->toDateString(),
        'moneda'         => 'MXN',
        'tipo_cambio'    => 1.0,
        'departamento'   => 'Operaciones',
        'descripcion'    => 'Factura de prueba con folio NA 1',
        'productos'      => [
            [
                'producto'        => 'Insumo Test 1',
                'cantidad'        => 2,
                'precio_unitario' => 100,
                'aplica_iva'      => 1,
                'iva_porcentaje'  => 16,
            ],
        ],
    ];

    $response1 = $this->actingAs($this->user)->postJson(route('facturas.store'), $payload1);
    $response1->assertStatus(200);
    $response1->assertJson(['success' => true]);

    $payload2 = [
        'tipo_documento' => 'nota_venta',
        'insumo'         => 'indirecto',
        'empresa'        => 'Proveedor Test B',
        'folio_factura'  => 'NA', // Folio repetido
        'fecha_factura'  => now()->toDateString(),
        'moneda'         => 'MXN',
        'tipo_cambio'    => 1.0,
        'departamento'   => 'Administración',
        'descripcion'    => 'Segunda factura con folio repetido NA',
        'productos'      => [
            [
                'producto'        => 'Insumo Test 2',
                'cantidad'        => 1,
                'precio_unitario' => 250,
                'aplica_iva'      => 0,
            ],
        ],
    ];

    $response2 = $this->actingAs($this->user)->postJson(route('facturas.store'), $payload2);
    $response2->assertStatus(200);
    $response2->assertJson(['success' => true]);

    $payload3 = [
        'tipo_documento' => 'factura',
        'insumo'         => 'directo',
        'empresa'        => 'Proveedor Test C',
        'folio_factura'  => '00', // Folio 00
        'fecha_factura'  => now()->toDateString(),
        'moneda'         => 'MXN',
        'tipo_cambio'    => 1.0,
        'departamento'   => 'Taller',
        'descripcion'    => 'Factura con folio 00',
        'productos'      => [
            [
                'producto'        => 'Insumo Test 3',
                'cantidad'        => 5,
                'precio_unitario' => 50,
                'aplica_iva'      => 1,
                'iva_porcentaje'  => 16,
            ],
        ],
    ];

    $response3 = $this->actingAs($this->user)->postJson(route('facturas.store'), $payload3);
    $response3->assertStatus(200);
    $response3->assertJson(['success' => true]);

    $payload4 = [
        'tipo_documento' => 'factura',
        'insumo'         => 'directo',
        'empresa'        => 'Proveedor Test D',
        'folio_factura'  => '00', // Folio 00 repetido
        'fecha_factura'  => now()->toDateString(),
        'moneda'         => 'MXN',
        'tipo_cambio'    => 1.0,
        'departamento'   => 'Taller',
        'descripcion'    => 'Segunda factura con folio 00',
        'productos'      => [
            [
                'producto'        => 'Insumo Test 4',
                'cantidad'        => 1,
                'precio_unitario' => 80,
                'aplica_iva'      => 1,
                'iva_porcentaje'  => 16,
            ],
        ],
    ];

    $response4 = $this->actingAs($this->user)->postJson(route('facturas.store'), $payload4);
    $response4->assertStatus(200);
    $response4->assertJson(['success' => true]);

    // Verificar actualización con folio duplicado
    $facturaNA = DB::table('facturas')->where('empresa', 'Proveedor Test B')->first();
    expect($facturaNA)->not->toBeNull();

    $payloadUpdate = [
        'tipo_documento' => 'factura',
        'insumo'         => 'directo',
        'empresa'        => 'Proveedor Test B Actualizado',
        'folio_factura'  => '00', // Actualizar a un folio '00' que ya existe
        'fecha_factura'  => now()->toDateString(),
        'moneda'         => 'MXN',
        'tipo_cambio'    => 1.0,
        'departamento'   => 'Administración',
        'productos'      => [
            [
                'producto'        => 'Insumo Test 2 Editado',
                'cantidad'        => 3,
                'precio_unitario' => 300,
                'aplica_iva'      => 0,
            ],
        ],
    ];

    $responseUpdate = $this->actingAs($this->user)->patchJson(route('facturas.update', $facturaNA->factura_id), $payloadUpdate);
    $responseUpdate->assertStatus(200);
    $responseUpdate->assertJson(['success' => true]);

    // Limpieza de datos de prueba
    $facturaIds = DB::table('facturas')
        ->whereIn('empresa', ['Proveedor Test A', 'Proveedor Test B', 'Proveedor Test B Actualizado', 'Proveedor Test C', 'Proveedor Test D'])
        ->pluck('factura_id');

    foreach ($facturaIds as $fId) {
        $this->actingAs($this->user)->deleteJson(route('facturas.destroy', $fId));
    }
});
