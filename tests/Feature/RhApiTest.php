<?php

use App\Models\Asistencia;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->createAsistencia = function (array $overrides = []) {
        $unique = uniqid();

        return Asistencia::create(array_merge([
            'nombre'         => 'Trabajador Test ' . $unique,
            'fecha'          => Carbon::now()->subDays(rand(1, 4))->format('Y-m-d'),
            'entrada'        => '08:30:00',
            'salida_comida'  => '14:00:00',
            'regreso_comida' => '15:00:00',
            'salida_final'   => '17:30:00',
            'tipo'           => 'Normal',
            'comentario'     => 'Sin incidencias',
        ], $overrides));
    };
});

test('index renders rh asistencia view for web request', function () {
    ($this->createAsistencia)();

    $response = $this->actingAs($this->user)->get(route('attendance.index'));

    $response->assertStatus(200);
    $response->assertViewIs('rh.asistencia');
    $response->assertViewHas([
        'empleados',
        'empleadoSeleccionado',
        'fechaInicio',
        'fechaFin',
        'chartData',
        'chartDataIncidencias',
        'chartDataOmisiones',
        'textoTiempoExtraTotal',
        'textoTiempoPerdidoTotal',
        'omisionesChecada',
    ]);
});

test('index returns json when requested with json header', function () {
    $asistencia = ($this->createAsistencia)();

    $response = $this->actingAs($this->user)->getJson(route('attendance.index', [
        'fecha_inicio' => Carbon::now()->subDays(5)->format('Y-m-d'),
        'fecha_fin'    => Carbon::now()->format('Y-m-d'),
    ]));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'nombre',
                'fecha',
                'entrada',
                'salida_comida',
                'regreso_comida',
                'salida_final',
                'tipo',
                'comentario',
                'created_at',
                'updated_at',
            ],
        ],
    ]);
});

test('index filters by empleado correctly', function () {
    $uniqueName = 'Empleado Unico ' . uniqid();
    $emp1 = ($this->createAsistencia)(['nombre' => $uniqueName]);
    $emp2 = ($this->createAsistencia)(['nombre' => 'Empleado Distinto ' . uniqid()]);

    $response = $this->actingAs($this->user)->getJson(route('attendance.index', [
        'empleado'     => $uniqueName,
        'fecha_inicio' => Carbon::now()->subDays(5)->format('Y-m-d'),
        'fecha_fin'    => Carbon::now()->format('Y-m-d'),
    ]));

    $response->assertStatus(200);
    $data = $response->json('data');
    expect($data)->not->toBeEmpty();
    foreach ($data as $item) {
        expect($item['nombre'])->toBe($uniqueName);
    }
});

test('index fails validation if fecha_fin is before fecha_inicio', function () {
    $response = $this->actingAs($this->user)->getJson(route('attendance.index', [
        'fecha_inicio' => '2026-05-10',
        'fecha_fin'    => '2026-05-01',
    ]));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['fecha_fin']);
});

test('uploadCsv fails validation if no file is provided', function () {
    $response = $this->actingAs($this->user)->post(route('attendance.upload'), []);

    $response->assertSessionHasErrors(['csv_file']);
});

test('uploadCsv imports valid csv file and normalizes employee alias', function () {
    $uniqueDate = Carbon::now()->subDays(rand(10, 20))->format('Y-m-d');
    $csvContent = implode("\n", [
        "Nombre;Fecha;Entrada;Salida Comida;Regreso Comida;Salida Final;Tipo;Comentarios",
        "fanny;{$uniqueDate};08:25:00;14:05:00;15:00:00;17:35:00;Normal;Buen desempeño",
        "Carlos Ruiz {$uniqueDate};{$uniqueDate};08:30:00;14:00:00;15:00:00;17:30:00;Normal;",
    ]);

    $file = UploadedFile::fake()->createWithContent('asistencias.csv', $csvContent);

    $response = $this->actingAs($this->user)->post(route('attendance.upload'), [
        'csv_file' => $file,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('asistencias', [
        'nombre' => 'Fany',
        'fecha' => $uniqueDate,
        'entrada' => '08:25:00',
    ]);

    $this->assertDatabaseHas('asistencias', [
        'nombre' => "Carlos Ruiz {$uniqueDate}",
        'fecha' => $uniqueDate,
        'entrada' => '08:30:00',
    ]);
});
