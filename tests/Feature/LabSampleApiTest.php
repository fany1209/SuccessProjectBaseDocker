<?php

use App\Models\LaboratorySample;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('1. puede visualizar la vista principal de muestras de laboratorio (GET /lab-samples)', function () {
    $response = $this->actingAs($this->user)->get(route('lab.samples.index'));

    $response->assertStatus(200);
});

test('2. puede listar muestras de laboratorio en formato JSON (GET /lab-samples)', function () {
    $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    while (LaboratorySample::where('folio', $folio)->exists()) {
        $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    }

    $sample = LaboratorySample::create([
        'folio'         => $folio,
        'tipo_muestra'  => 'Materia Prima',
        'producto'      => 'Levadura Test A',
        'stock_inicial' => 50,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('lab.samples.index'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Muestras de laboratorio obtenidas correctamente.',
        ])
        ->assertJsonStructure([
            'success',
            'flag',
            'code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'folio',
                    'tipo_muestra',
                    'producto',
                    'sku',
                    'proveedor',
                    'ubicacion_stock',
                    'stock_inicial',
                    'cantidad_salida',
                    'stock_final',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    $sample->delete();
});

test('3. puede obtener los datos en formato DataTables (GET /lab-samples/datatable)', function () {
    $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    while (LaboratorySample::where('folio', $folio)->exists()) {
        $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    }

    $sample = LaboratorySample::create([
        'folio'         => $folio,
        'tipo_muestra'  => 'Producto Terminado',
        'producto'      => 'Bioyeast Plus',
        'stock_inicial' => 100,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('lab.samples.datatable'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'folio',
                    'tipo_muestra',
                    'producto',
                    'sku',
                    'proveedor',
                    'lote',
                    'ubicacion',
                    'stock_inicial',
                    'salida',
                    'stock_final',
                    'fecha_entrada',
                    'fecha_salida',
                    'status',
                    'acciones',
                ],
            ],
        ]);

    $sample->delete();
});

test('4. puede consultar el detalle de una muestra (GET /laboratory/lab-samples/{id})', function () {
    $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    while (LaboratorySample::where('folio', $folio)->exists()) {
        $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    }

    $sample = LaboratorySample::create([
        'folio'         => $folio,
        'tipo_muestra'  => 'Agro',
        'producto'      => 'Fertilizante Orgánico',
        'stock_inicial' => 25,
        'ubicacion_stock' => 'Estante B3',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('lab.samples.show', $sample->id));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Registro obtenido exitosamente.',
            'data'    => [
                'id'              => $sample->id,
                'folio'           => $folio,
                'tipo_muestra'    => 'Agro',
                'producto'        => 'Fertilizante Orgánico',
                'ubicacion_stock' => 'Estante B3',
            ],
        ]);

    $sample->delete();
});

test('5. retorna 404 al consultar una muestra inexistente (GET /laboratory/lab-samples/{id})', function () {
    $response = $this->actingAs($this->user)->getJson(route('lab.samples.show', 99999999));

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Registro no encontrado.',
        ]);
});

test('6. puede actualizar una muestra de laboratorio y sanitizar contra XSS (PUT /laboratory/lab-samples/{id})', function () {
    $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    while (LaboratorySample::where('folio', $folio)->exists()) {
        $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    }

    $sample = LaboratorySample::create([
        'folio'         => $folio,
        'tipo_muestra'  => 'Original',
        'producto'      => 'Producto Antiguo',
        'stock_inicial' => 10,
    ]);

    $payload = [
        'tipo_muestra'     => '<script>alert("xss")</script>Insumo Actualizado',
        'producto'         => '<b>Producto Modificado</b>',
        'ubicacion_stock'  => 'Gaveta 1',
        'cantidad_salida'  => 3,
        'status'           => 'En proceso',
    ];

    $response = $this->actingAs($this->user)->putJson(route('lab.samples.update', $sample->id), $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Registro actualizado.',
            'data'    => [
                'id'              => $sample->id,
                'tipo_muestra'    => 'alert("xss")Insumo Actualizado',
                'producto'        => 'Producto Modificado',
                'ubicacion_stock' => 'Gaveta 1',
                'status'          => 'En proceso',
            ],
        ]);

    $this->assertDatabaseHas('laboratory_samples', [
        'id'           => $sample->id,
        'tipo_muestra' => 'alert("xss")Insumo Actualizado',
        'producto'     => 'Producto Modificado',
    ]);

    $sample->delete();
});

test('7. retorna 404 al intentar actualizar una muestra inexistente (PUT /laboratory/lab-samples/{id})', function () {
    $response = $this->actingAs($this->user)->putJson(route('lab.samples.update', 99999999), [
        'producto' => 'Cualquier cosa',
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Registro no encontrado.',
        ]);
});

test('8. puede eliminar una muestra de laboratorio (DELETE /laboratory/lab-samples/{id})', function () {
    $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    while (LaboratorySample::where('folio', $folio)->exists()) {
        $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    }

    $sample = LaboratorySample::create([
        'folio'         => $folio,
        'tipo_muestra'  => 'Para Eliminar',
        'producto'      => 'A Eliminar',
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('lab.samples.destroy', $sample->id));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Deleted',
        ]);

    $this->assertDatabaseMissing('laboratory_samples', [
        'id' => $sample->id,
    ]);
});

test('9. retorna 404 al intentar eliminar una muestra inexistente (DELETE /laboratory/lab-samples/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('lab.samples.destroy', 99999999));

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Not found',
        ]);
});
