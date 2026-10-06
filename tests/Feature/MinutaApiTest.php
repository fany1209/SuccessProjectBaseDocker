<?php

use App\Models\Minuta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'user_minuta_' . uniqid() . '@example.com',
    ]);

    $this->createMinuta = function (array $overrides = []) {
        return Minuta::create(array_merge([
            'user_id' => $this->user->id,
            'fecha_hora' => Carbon::now()->format('Y-m-d H:i:s'),
            'lugar' => 'Sala de Juntas Principal',
            'tema_general' => 'Revisión Operativa ' . uniqid(),
            'ponente' => 'Gerente de Calidad',
            'asistente_nombre' => 'Juan Perez, Maria Gomez',
            'asistente_departamento' => 'Calidad, Producción',
            'tema_tratado' => 'Auditoría interna | Inventario',
            'acuerdo' => 'Actualizar bitácoras | Conteo mensual',
            'responsable' => 'Maria Gomez, Juan Perez',
            'fecha_compromiso' => '2026-10-15',
            'fecha_cierre' => '2026-10-20',
            'estatus' => 'Pendiente',
        ], $overrides));
    };
});

test('index renders minutas view for web request', function () {
    $response = $this->actingAs($this->user)->get(route('minutas.index'));

    $response->assertStatus(200);
    $response->assertViewIs('minutas');
    $response->assertViewHas('total_minutas');
});

test('getMinutas returns json collection under minutas key', function () {
    ($this->createMinuta)();

    $response = $this->actingAs($this->user)->getJson(route('minutas.getSuppliers'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'minutas' => [
            '*' => [
                'id_minuta',
                'fecha_hora',
                'lugar',
                'tema_general',
                'ponente',
                'asistente_nombre',
                'asistente_departamento',
                'tema_tratado',
                'acuerdo',
                'responsable',
                'fecha_compromiso',
                'fecha_cierre',
                'estatus',
                'canUpdate',
                'canDelete',
            ],
        ],
    ]);
});

test('getMinutas filters by search keyword', function () {
    $uniqueTema = 'Tema Super Unico ' . uniqid();
    ($this->createMinuta)(['tema_general' => $uniqueTema]);
    ($this->createMinuta)(['tema_general' => 'Otro Tema Aleatorio']);

    $response = $this->actingAs($this->user)->getJson(route('minutas.getSuppliers', [
        'search' => $uniqueTema,
    ]));

    $response->assertStatus(200);
    $minutas = $response->json('minutas');
    expect(count($minutas))->toBeGreaterThanOrEqual(1);
    foreach ($minutas as $m) {
        expect($m['tema_general'])->toContain($uniqueTema);
    }
});

test('store fails validation when required fields are missing', function () {
    $response = $this->actingAs($this->user)->postJson(route('minutas.store'), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['fecha_hora', 'tema_general']);
});

test('store creates minuta successfully with array parameters', function () {
    $payload = [
        'fecha_hora' => '2026-10-06 10:00:00',
        'tema_general' => 'Plan de Capacitación Q4',
        'lugar' => 'Oficinas CEDIS',
        'ponente' => 'Coordinador RH',
        'asistente_nombre' => ['Roberto Gomez', 'Laura Diaz'],
        'asistente_departamento' => ['Recursos Humanos', 'Operaciones'],
        'tema_tratado' => ['Cursos NOM-035', 'Prevención de riesgos'],
        'acuerdo' => ['Agendar fechas', 'Confirmar instructores'],
        'responsable' => ['Laura Diaz', 'Roberto Gomez'],
    ];

    $response = $this->actingAs($this->user)->postJson(route('minutas.store'), $payload);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Minuta guardada con éxito',
    ]);

    $this->assertDatabaseHas('minutas', [
        'tema_general' => 'Plan de Capacitación Q4',
        'lugar' => 'Oficinas CEDIS',
    ]);
});

test('show returns 404 when minuta is not found', function () {
    $response = $this->actingAs($this->user)->getJson('/minutas/9999999');

    $response->assertStatus(404);
});

test('show returns minuta details in json', function () {
    $minuta = ($this->createMinuta)();

    $response = $this->actingAs($this->user)->getJson('/minutas/' . $minuta->id_minuta);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'minuta' => [
            'id_minuta',
            'fecha_hora',
            'tema_general',
        ],
    ]);
});

test('update modifies existing minuta successfully', function () {
    $minuta = ($this->createMinuta)();

    $updatePayload = [
        'tema_general' => 'Tema Modificado Exitosamente',
        'lugar' => 'Sala B',
        'ponente' => 'Nuevo Ponente',
    ];

    $response = $this->actingAs($this->user)->postJson('/minutas/' . $minuta->id_minuta, $updatePayload);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Minuta actualizada',
    ]);

    $this->assertDatabaseHas('minutas', [
        'id_minuta' => $minuta->id_minuta,
        'tema_general' => 'Tema Modificado Exitosamente',
        'lugar' => 'Sala B',
    ]);
});

test('destroy removes minuta successfully', function () {
    $minuta = ($this->createMinuta)();

    $response = $this->actingAs($this->user)->deleteJson('/minutas/' . $minuta->id_minuta);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseMissing('minutas', [
        'id_minuta' => $minuta->id_minuta,
    ]);
});

test('downloadPDF streams pdf response for minuta', function () {
    $minuta = ($this->createMinuta)();

    $response = $this->actingAs($this->user)->get(route('minutas.pdf', $minuta->id_minuta));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});
