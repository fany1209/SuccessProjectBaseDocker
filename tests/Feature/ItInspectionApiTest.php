<?php

use App\Models\ItEquipment;
use App\Models\ItInspection;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    ItInspection::query()->delete();
});

test('1. puede visualizar la vista principal de inspecciones de TI (GET /sistemas-ti/inspecciones)', function () {
    $inspection = ItInspection::create([
        'folio'         => 'INS-' . uniqid(),
        'date'          => '2026-09-29',
        'brand'         => 'Dell',
        'model'         => 'Latitude 5420',
        'serial_number' => 'SN-INSP-01',
        'req1'          => 'cumple',
        'req2'          => 'cumple',
        'req3'          => 'cumple',
        'req4'          => 'cumple',
        'req5'          => 'cumple',
    ]);

    $response = $this->actingAs($this->user)->get(route('sistemas-ti.inspecciones.index'));

    $response->assertStatus(200);
    $response->assertViewIs('sistemas-ti.inspecciones.index');
    $response->assertViewHas('inspections');
});

test('2. puede listar inspecciones de TI en JSON con estructura UtilResponse (GET /sistemas-ti/inspecciones)', function () {
    $inspection = ItInspection::create([
        'folio'         => 'INS-' . uniqid(),
        'date'          => '2026-09-29',
        'brand'         => 'HP',
        'model'         => 'ProDesk',
        'serial_number' => 'SN-INSP-02',
        'req1'          => 'cumple',
        'req2'          => 'cumple',
        'req3'          => 'cumple',
        'req4'          => 'cumple',
        'req5'          => 'cumple',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('sistemas-ti.inspecciones.index'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Inspecciones de TI obtenidas correctamente.',
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
                    'date',
                    'brand',
                    'model',
                    'serial_number',
                    'location',
                    'area',
                    'req1',
                    'req2',
                    'req3',
                    'req4',
                    'req5',
                    'observations',
                    'inspector_name',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});

test('3. puede filtrar inspecciones de TI por término de búsqueda (search)', function () {
    $insp1 = ItInspection::create([
        'folio'         => 'INS-SRCH-' . uniqid(),
        'date'          => '2026-09-29',
        'brand'         => 'Lenovo-Search',
        'serial_number' => 'SN-SEARCH-AAA',
        'req1'          => 'cumple',
        'req2'          => 'cumple',
        'req3'          => 'cumple',
        'req4'          => 'cumple',
        'req5'          => 'cumple',
    ]);

    $insp2 = ItInspection::create([
        'folio'         => 'INS-OTHR-' . uniqid(),
        'date'          => '2026-09-29',
        'brand'         => 'Asus-Other',
        'serial_number' => 'SN-OTHER-BBB',
        'req1'          => 'cumple',
        'req2'          => 'cumple',
        'req3'          => 'cumple',
        'req4'          => 'cumple',
        'req5'          => 'cumple',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('sistemas-ti.inspecciones.index', ['search' => 'Lenovo-Search']));

    $response->assertStatus(200);
    $data = $response->json('data');
    expect(collect($data)->pluck('brand'))->toContain('Lenovo-Search')
        ->and(collect($data)->pluck('brand'))->not->toContain('Asus-Other');
});

test('4. puede acceder a la vista create de inspecciones (Blade / JSON)', function () {
    ItEquipment::create([
        'department'    => 'Sistemas',
        'article'       => 'Laptop',
        'brand'         => 'Dell',
        'model'         => 'Latitude',
        'serial_number' => 'SN-EQ-' . uniqid(),
    ]);

    // Prueba en Blade
    $resBlade = $this->actingAs($this->user)->get(route('sistemas-ti.inspecciones.create'));
    $resBlade->assertStatus(200)
        ->assertViewIs('sistemas-ti.inspecciones.create')
        ->assertViewHas(['folio', 'equipments']);

    // Prueba en JSON
    $resJson = $this->actingAs($this->user)->getJson(route('sistemas-ti.inspecciones.create'));
    $resJson->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
        ])
        ->assertJsonStructure([
            'data' => [
                'folio',
                'equipments',
            ],
        ]);
});

test('5. puede consultar una inspección específica en JSON (GET /sistemas-ti/inspecciones/{id})', function () {
    $folio = 'INS-' . uniqid();
    $inspection = ItInspection::create([
        'folio'         => $folio,
        'date'          => '2026-09-29',
        'brand'         => 'Dell',
        'model'         => 'Optiplex',
        'serial_number' => 'SN-SHOW-99',
        'location'      => 'Piso 2',
        'area'          => 'Contabilidad',
        'req1'          => 'cumple',
        'req2'          => 'cumple',
        'req3'          => 'nocumple',
        'req4'          => 'na',
        'req5'          => 'cumple',
        'observations'  => 'Cable desgastado',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('sistemas-ti.inspecciones.show', $inspection->id));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Inspección de TI obtenida correctamente.',
            'data'    => [
                'id'            => $inspection->id,
                'folio'         => $folio,
                'brand'         => 'Dell',
                'serial_number' => 'SN-SHOW-99',
                'location'      => 'Piso 2',
                'area'          => 'Contabilidad',
                'req3'          => 'nocumple',
                'observations'  => 'Cable desgastado',
            ],
        ]);
});

test('6. retorna 404 al consultar una inspección inexistente (GET /sistemas-ti/inspecciones/{id})', function () {
    $response = $this->actingAs($this->user)->getJson(route('sistemas-ti.inspecciones.show', 99999999));

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Inspección de TI no encontrada.',
        ]);
});

test('7. puede registrar una inspección en JSON retornando 201 (POST /sistemas-ti/inspecciones)', function () {
    $folio = 'INS-STR-' . uniqid();
    $payload = [
        'folio'          => $folio,
        'date'           => '2026-09-29',
        'brand'          => 'Lenovo',
        'model'          => 'IdeaPad 3',
        'serial_number'  => 'SN-STORE-JSON-01',
        'location'       => 'Planta Alta',
        'area'           => 'Sistemas',
        'req1'           => 'cumple',
        'req2'           => 'cumple',
        'req3'           => 'cumple',
        'req4'           => 'cumple',
        'req5'           => 'nocumple',
        'observations'   => 'Antivirus desactualizado.',
        'inspector_name' => 'Ing. Juan Pérez',
    ];

    $response = $this->actingAs($this->user)->postJson(route('sistemas-ti.inspecciones.store'), $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 201,
            'message' => 'Inspección guardada correctamente.',
            'data'    => [
                'folio'          => $folio,
                'brand'          => 'Lenovo',
                'serial_number'  => 'SN-STORE-JSON-01',
                'req5'           => 'nocumple',
                'inspector_name' => 'Ing. Juan Pérez',
            ],
        ]);

    $this->assertDatabaseHas('it_inspections', [
        'folio'          => $folio,
        'serial_number'  => 'SN-STORE-JSON-01',
        'inspector_name' => 'Ing. Juan Pérez',
    ]);
});

test('8. sanitiza entradas contra Stored-XSS al registrar inspección', function () {
    $uniqueCode = uniqid();
    $payload = [
        'folio'          => '<script>alert("xss")</script>INS-' . $uniqueCode,
        'date'           => '2026-09-29',
        'brand'          => '<b>Acer</b>',
        'model'          => '<i>Aspire 5</i>',
        'serial_number'  => 'SN-XSS-111',
        'location'       => '<u>Oficina 1</u>',
        'area'           => '<a href="#">Direccion</a>',
        'req1'           => 'cumple',
        'req2'           => 'cumple',
        'req3'           => 'cumple',
        'req4'           => 'cumple',
        'req5'           => 'cumple',
        'observations'   => '<script>steal()</script>Texto limpio de prueba',
        'inspector_name' => '<span>Inspector XSS</span>',
    ];

    $response = $this->actingAs($this->user)->postJson(route('sistemas-ti.inspecciones.store'), $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('it_inspections', [
        'folio'          => 'alert("xss")INS-' . $uniqueCode,
        'brand'          => 'Acer',
        'model'          => 'Aspire 5',
        'location'       => 'Oficina 1',
        'area'           => 'Direccion',
        'observations'   => 'steal()Texto limpio de prueba',
        'inspector_name' => 'Inspector XSS',
    ]);
});

test('9. valida campos requeridos y formatos retornando 422 al registrar inspección inválida', function () {
    $payload = [
        'folio' => '',
        'date'  => 'no-es-fecha',
        'req1'  => 'invalido',
    ];

    $response = $this->actingAs($this->user)->postJson(route('sistemas-ti.inspecciones.store'), $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['folio', 'date', 'req1', 'req2', 'req3', 'req4', 'req5']);
});

test('10. puede registrar inspección via formulario web Blade redireccionando con éxito', function () {
    $folio = 'INS-WEB-' . uniqid();
    $payload = [
        'folio'         => $folio,
        'date'          => '2026-09-29',
        'brand'         => 'HP',
        'model'         => 'EliteBook',
        'serial_number' => 'SN-WEB-INSP-01',
        'req1'          => 'cumple',
        'req2'          => 'cumple',
        'req3'          => 'cumple',
        'req4'          => 'cumple',
        'req5'          => 'cumple',
    ];

    $response = $this->actingAs($this->user)->post(route('sistemas-ti.inspecciones.store'), $payload);

    $response->assertRedirect(route('sistemas-ti.inspecciones.index'));
    $response->assertSessionHas('success', 'Inspección guardada correctamente.');

    $this->assertDatabaseHas('it_inspections', [
        'folio' => $folio,
    ]);
});

test('11. puede actualizar inspección en formato JSON retornando 200 (PUT /sistemas-ti/inspecciones/{id})', function () {
    $folio = 'INS-UPD-' . uniqid();
    $inspection = ItInspection::create([
        'folio'         => $folio,
        'date'          => '2026-09-29',
        'brand'         => 'Dell',
        'model'         => 'Vostro',
        'serial_number' => 'SN-UPD-001',
        'req1'          => 'nocumple',
        'req2'          => 'cumple',
        'req3'          => 'cumple',
        'req4'          => 'cumple',
        'req5'          => 'cumple',
        'observations'  => 'Pendiente mantenimiento',
    ]);

    $updatePayload = [
        'folio'         => $folio,
        'date'          => '2026-09-30',
        'brand'         => 'Dell',
        'model'         => 'Vostro 3500',
        'serial_number' => 'SN-UPD-001-MOD',
        'req1'          => 'cumple',
        'req2'          => 'cumple',
        'req3'          => 'cumple',
        'req4'          => 'cumple',
        'req5'          => 'cumple',
        'observations'  => 'Mantenimiento realizado y corregido.',
    ];

    $response = $this->actingAs($this->user)->putJson(route('sistemas-ti.inspecciones.update', $inspection->id), $updatePayload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Inspección actualizada correctamente.',
            'data'    => [
                'id'           => $inspection->id,
                'req1'         => 'cumple',
                'model'        => 'Vostro 3500',
                'observations' => 'Mantenimiento realizado y corregido.',
            ],
        ]);

    $this->assertDatabaseHas('it_inspections', [
        'id'           => $inspection->id,
        'req1'         => 'cumple',
        'model'        => 'Vostro 3500',
        'observations' => 'Mantenimiento realizado y corregido.',
    ]);
});

test('12. sanitiza entradas contra Stored-XSS al actualizar inspección', function () {
    $inspection = ItInspection::create([
        'folio'         => 'INS-XSS-' . uniqid(),
        'date'          => '2026-09-29',
        'brand'         => 'Apple',
        'req1'          => 'cumple',
        'req2'          => 'cumple',
        'req3'          => 'cumple',
        'req4'          => 'cumple',
        'req5'          => 'cumple',
    ]);

    $updatePayload = [
        'observations'   => '<script>alert("hack")</script>Sin novedades detectadas',
        'inspector_name' => '<b>Ing. Seguridad</b>',
    ];

    $response = $this->actingAs($this->user)->putJson(route('sistemas-ti.inspecciones.update', $inspection->id), $updatePayload);

    $response->assertStatus(200);

    $this->assertDatabaseHas('it_inspections', [
        'id'             => $inspection->id,
        'observations'   => 'alert("hack")Sin novedades detectadas',
        'inspector_name' => 'Ing. Seguridad',
    ]);
});

test('13. retorna 404 al intentar actualizar una inspección inexistente (PUT /sistemas-ti/inspecciones/{id})', function () {
    $payload = [
        'observations' => 'Actualizando fantasma',
    ];

    $response = $this->actingAs($this->user)->putJson(route('sistemas-ti.inspecciones.update', 99999999), $payload);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Inspección de TI no encontrada para actualizar.',
        ]);
});

test('14. puede eliminar inspección en formato JSON retornando 200 (DELETE /sistemas-ti/inspecciones/{id})', function () {
    $inspection = ItInspection::create([
        'folio'         => 'INS-DEL-' . uniqid(),
        'date'          => '2026-09-29',
        'brand'         => 'Toshiba',
        'req1'          => 'cumple',
        'req2'          => 'cumple',
        'req3'          => 'cumple',
        'req4'          => 'cumple',
        'req5'          => 'cumple',
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('sistemas-ti.inspecciones.destroy', $inspection->id));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Inspección eliminada correctamente.',
            'data'    => [],
        ]);

    $this->assertDatabaseMissing('it_inspections', [
        'id' => $inspection->id,
    ]);
});

test('15. retorna 404 al intentar eliminar una inspección inexistente (DELETE /sistemas-ti/inspecciones/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('sistemas-ti.inspecciones.destroy', 99999999));

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Inspección de TI no encontrada para eliminar.',
        ]);
});

test('16. puede visualizar la vista print de inspección y retorna 404 si no existe', function () {
    $folio = 'INS-PRN-' . uniqid();
    $inspection = ItInspection::create([
        'folio'         => $folio,
        'date'          => '2026-09-29',
        'brand'         => 'Dell',
        'model'         => 'Latitude 7420',
        'req1'          => 'cumple',
        'req2'          => 'cumple',
        'req3'          => 'cumple',
        'req4'          => 'cumple',
        'req5'          => 'cumple',
    ]);

    // Vista Blade de impresión
    $response = $this->actingAs($this->user)->get(route('sistemas-ti.inspecciones.print', $inspection->id));
    $response->assertStatus(200);
    $response->assertViewIs('sistemas-ti.inspecciones.print');
    $response->assertViewHas('inspection');

    // JSON de impresión
    $responseJson = $this->actingAs($this->user)->getJson(route('sistemas-ti.inspecciones.print', $inspection->id));
    $responseJson->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Inspección obtenida para impresión.',
            'data'    => [
                'id'    => $inspection->id,
                'folio' => $folio,
            ],
        ]);

    // 404
    $response404 = $this->actingAs($this->user)->getJson(route('sistemas-ti.inspecciones.print', 99999999));
    $response404->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
            'message' => 'Inspección no encontrada.',
        ]);
});
