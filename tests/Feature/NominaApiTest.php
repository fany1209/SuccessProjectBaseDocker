<?php

use App\Models\Nomina;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->createNomina = function (array $overrides = []) {
        static $counter = 100;
        $counter++;

        return Nomina::create(array_merge([
            'nombre'              => 'Empleado Prueba ' . $counter,
            'curp'                => 'TEST' . rand(100000, 999999) . 'HDFRRN01',
            'rfc'                 => 'TEST' . rand(100000, 999999) . 'A1',
            'nss'                 => '12345678' . rand(10, 99),
            'puesto'              => 'Operador de Producción',
            'fecha_ingreso'       => '2024-01-15',
            'fecha_baja'          => null,
            'edad'                => 28,
            'antiguedad'          => '2 años',
            'sexo'                => 'Masculino',
            'estado_civil'        => 'Soltero',
            'fecha_nacimiento'    => '1996-05-10',
            'nombre_beneficiario' => 'Familiar Prueba',
            'parentesco'          => 'Madre',
            'domicilio'           => 'Calle Hidalgo 123',
            'cp'                  => '38000',
            'telefono'            => '4421234567',
            'correo'              => 'empleado.' . $counter . '@empresa.com',
            'estatus'             => 'Activo',
            'user_id'             => $this->user->id,
        ], $overrides));
    };
});

test('index renders nominas view for web request', function () {
    $response = $this->actingAs($this->user)->get(route('rh.nominas.index'));

    $response->assertStatus(200);
    $response->assertViewIs('rh.nominas.index');
});

test('index returns json when requested with json header', function () {
    $response = $this->actingAs($this->user)->getJson(route('rh.nominas.index'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'puestos',
            'years',
        ],
    ]);
});

test('datatable returns list of nominas with calculated fields and supports filters', function () {
    ($this->createNomina)([
        'puesto'  => 'Mecánico Industrial',
        'estatus' => 'Activo',
    ]);

    $response = $this->actingAs($this->user)->getJson(route('rh.nominas.datatable', [
        'puesto'  => 'Mecánico Industrial',
        'estatus' => 'Activo',
    ]));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'nombre',
                'curp',
                'puesto',
                'antiguedad',
                'edad',
                'estatus',
            ],
        ],
    ]);
});

test('store creates employee in nomina with uppercase CURP/RFC and calculated values', function () {
    $payload = [
        'nombre'              => 'Juan Carlos Pérez García',
        'curp'                => 'pegj950415hdfxxx01',
        'rfc'                 => 'pegj950415xx1',
        'nss'                 => '98765432101',
        'puesto'              => 'Supervisor de Turno',
        'fecha_ingreso'       => '2023-03-01',
        'fecha_nacimiento'    => '1995-04-15',
        'sexo'                => 'Masculino',
        'estado_civil'        => 'Casado',
        'nombre_beneficiario' => 'María García',
        'parentesco'          => 'Esposa',
        'domicilio'           => 'Av. Central 45',
        'cp'                  => '38100',
        'telefono'            => '4619876543',
        'correo'              => 'juan.perez@empresa.com',
    ];

    $response = $this->actingAs($this->user)->postJson(route('rh.nominas.store'), $payload);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseHas('nominas', [
        'nombre'  => 'Juan Carlos Pérez García',
        'curp'    => 'PEGJ950415HDFXXX01',
        'rfc'     => 'PEGJ950415XX1',
        'puesto'  => 'Supervisor de Turno',
        'estatus' => 'Activo',
    ]);

    $record = Nomina::where('curp', 'PEGJ950415HDFXXX01')->first();
    $this->assertNotNull($record->antiguedad);
    $this->assertNotNull($record->edad);
});

test('store fails validation on missing required fields', function () {
    $response = $this->actingAs($this->user)->postJson(route('rh.nominas.store'), [
        'curp' => 'TESTCURP',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['nombre', 'puesto', 'fecha_ingreso']);
});

test('store sanitizes input and strips XSS tags', function () {
    $payload = [
        'nombre'        => '<b>Pedro Armendáriz</b>',
        'puesto'        => '<i>Chofer Repartidor</i>',
        'fecha_ingreso' => '2025-01-10',
        'domicilio'     => '<script>alert("xss")</script>Calle Real 500',
    ];

    $response = $this->actingAs($this->user)->postJson(route('rh.nominas.store'), $payload);

    $response->assertStatus(200);

    $this->assertDatabaseHas('nominas', [
        'nombre'    => 'Pedro Armendáriz',
        'puesto'    => 'Chofer Repartidor',
        'domicilio' => 'alert("xss")Calle Real 500',
    ]);
});

test('show returns employee details', function () {
    $nomina = ($this->createNomina)();

    $response = $this->actingAs($this->user)->getJson(route('rh.nominas.show', $nomina->id));

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('nomina.id', $nomina->id);
    $response->assertJsonPath('nomina.nombre', $nomina->nombre);
});

test('show returns 404 for non-existent employee', function () {
    $response = $this->actingAs($this->user)->getJson(route('rh.nominas.show', 99999999));

    $response->assertStatus(404);
});

test('update modifies employee information and recalculates data', function () {
    $nomina = ($this->createNomina)();

    $payload = [
        'nombre'              => 'Empleado Nombre Actualizado',
        'puesto'              => 'Coordinador de Calidad',
        'fecha_ingreso'       => '2022-01-01',
        'fecha_baja'          => '2025-12-31',
        'fecha_nacimiento'    => '1990-08-20',
        'estatus'             => 'Baja',
        'curp'                => 'updt900820hdfxxx02',
    ];

    $response = $this->actingAs($this->user)->postJson(route('rh.nominas.update', $nomina->id), $payload);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseHas('nominas', [
        'id'      => $nomina->id,
        'nombre'  => 'Empleado Nombre Actualizado',
        'puesto'  => 'Coordinador de Calidad',
        'curp'    => 'UPDT900820HDFXXX02',
        'estatus' => 'Baja',
    ]);
});

test('update returns 404 for non-existent employee', function () {
    $response = $this->actingAs($this->user)->postJson(route('rh.nominas.update', 99999999), [
        'nombre'        => 'Test',
        'puesto'        => 'Puesto',
        'fecha_ingreso' => '2025-01-01',
    ]);

    $response->assertStatus(404);
});

test('destroy removes employee from nomina', function () {
    $nomina = ($this->createNomina)();

    $response = $this->actingAs($this->user)->deleteJson(route('rh.nominas.destroy', $nomina->id));

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseMissing('nominas', [
        'id' => $nomina->id,
    ]);
});

test('destroy returns 404 for non-existent employee', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('rh.nominas.destroy', 99999999));

    $response->assertStatus(404);
});

test('exportExcel downloads spreadsheet stream', function () {
    ($this->createNomina)();

    $response = $this->actingAs($this->user)->get(route('rh.nominas.export-excel'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
