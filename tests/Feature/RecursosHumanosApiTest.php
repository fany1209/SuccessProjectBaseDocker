<?php

use App\Models\Curso;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('index renders main rh view for web request', function () {
    $response = $this->actingAs($this->user)->get(route('expediente.index'));

    $response->assertStatus(200);
    $response->assertViewIs('rh');
});

test('store redirects with success message', function () {
    $response = $this->actingAs($this->user)->post(route('rh.expediente.store'), [
        'nombre' => 'Juan Perez',
    ]);

    $response->assertRedirect(route('expediente.index'));
    $response->assertSessionHas('success', 'Expediente creado correctamente.');
});

test('store returns json when requested with json header', function () {
    $response = $this->actingAs($this->user)->postJson(route('rh.expediente.store'), [
        'nombre' => 'Juan Perez',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Expediente creado correctamente.',
    ]);
});

test('storeCurso fails validation when required fields are missing', function () {
    $response = $this->actingAs($this->user)->postJson(route('rh.cursos.store'), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['fecha', 'sede', 'horario', 'curso', 'objetivo']);
});

test('storeCurso creates curso record successfully', function () {
    $payload = [
        'fecha' => Carbon::now()->format('Y-m-d'),
        'sede' => 'Sala de Capacitación CEDIS',
        'horario' => '09:00 - 13:00',
        'curso' => 'BPM y Seguridad Industrial',
        'objetivo' => 'Capacitar al personal operativo en buenas prácticas.',
        'asistentes' => ['Empleado Uno', 'Empleado Dos', ''],
    ];

    $response = $this->actingAs($this->user)->postJson(route('rh.cursos.store'), $payload);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Curso guardado correctamente.',
    ]);

    $this->assertDatabaseHas('cursos', [
        'sede' => 'Sala de Capacitación CEDIS',
        'curso' => 'BPM y Seguridad Industrial',
    ]);
});

test('indexCursos renders view for web request', function () {
    $response = $this->actingAs($this->user)->get(route('rh.cursos.resultados'));

    $response->assertStatus(200);
    $response->assertViewIs('rh.cursos_resultados');
    $response->assertViewHas('cursos');
});

test('indexCursos returns json when requested with json header', function () {
    Curso::create([
        'fecha' => Carbon::now()->format('Y-m-d'),
        'sede' => 'CEDIS',
        'horario' => '10:00 - 12:00',
        'curso' => 'Curso Calidad',
        'objetivo' => 'Objetivo de prueba',
        'asistentes' => ['Persona A'],
    ]);

    $response = $this->actingAs($this->user)->getJson(route('rh.cursos.resultados'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            '*' => [
                'id',
                'fecha',
                'sede',
                'horario',
                'curso',
                'objetivo',
                'asistentes',
                'created_at',
                'updated_at',
            ],
        ],
    ]);
});

test('generarPdfExpediente validates required nombre', function () {
    $response = $this->actingAs($this->user)->post(route('rh.expediente.pdf'), []);

    $response->assertSessionHasErrors(['nombre']);
});

test('generarPdfExpediente generates pdf stream successfully', function () {
    $response = $this->actingAs($this->user)->post(route('rh.expediente.pdf'), [
        'nombre' => 'Pedro Ramirez',
        'sexo' => 'Masculino',
        'estado_civil' => 'Casado',
    ]);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('descripcionPuestoPdf generates pdf stream successfully', function () {
    $response = $this->actingAs($this->user)->post(route('rh.descripcion_puesto.pdf'), [
        'nombre_puesto' => 'Coordinador de Almacen',
        'area' => 'Logística',
    ]);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('entrevistaTerminacionPdf generates pdf stream successfully', function () {
    $response = $this->actingAs($this->user)->post(route('rh.entrevista_terminacion.pdf'), [
        'nombre' => 'Colaborador Saliente',
        'puesto' => 'Operador',
    ]);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('evaluacionDesempenoPdf generates pdf stream successfully', function () {
    $response = $this->actingAs($this->user)->post(route('rh.evaluacion_desempeno.pdf'), [
        'nombre_evaluado' => 'Evaluado Prueba',
    ]);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('solicitudPersonalPdf generates pdf stream successfully', function () {
    $response = $this->actingAs($this->user)->post(route('rh.solicitud_personal.pdf'), [
        'nombre_puesto' => 'Chofer Repartidor',
    ]);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('convenioInstitucionesPdf generates pdf stream successfully', function () {
    $response = $this->actingAs($this->user)->post(route('rh.convenio_instituciones.pdf'), [
        'escuela' => 'Universidad Tecnológica',
    ]);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('generarPdfVacaciones generates pdf stream successfully', function () {
    $response = $this->actingAs($this->user)->post(route('rh.vacaciones.pdf'), [
        'employee_name' => 'Empleado Vacaciones',
        'start_date' => '2026-10-10',
        'end_date' => '2026-10-15',
    ]);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('generarPdfDnc generates pdf stream successfully', function () {
    $response = $this->actingAs($this->user)->post(route('rh.dnc.pdf'), [
        'employee_name' => 'Empleado DNC',
    ]);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('generarExpedientePracticantePdf generates pdf stream successfully', function () {
    $response = $this->actingAs($this->user)->post(route('rh.practicantes.pdf'), [
        'nombre' => 'Practicante Ingenieria',
    ]);

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});
