<?php

use App\Models\Complaint;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('1. API GET /complaints/create (Obtención de opciones de formulario en JSON)', function () {
    $response = $this->actingAs($this->user)->getJson('/complaints/create');

    echo "\n\n>>> LLAMADA: GET /complaints/create\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data.tipos'))->toContain('queja', 'sugerencia', 'denuncia');
    expect($response->json('data.motivos'))->toContain('ambiente_laboral', 'otro');
});

test('2. API POST /complaints (Envío exitoso de queja/experiencia con motivos)', function () {
    $payload = [
        'fecha' => now()->format('Y-m-d'),
        'tipo' => 'sugerencia',
        'motivos' => ['ambiente_laboral', 'equipo_de_trabajo'],
        'descripcion' => 'Propongo implementar una sesión semanal de retroalimentación de equipo.',
    ];

    $response = $this->actingAs($this->user)->postJson('/complaints', $payload);

    echo "\n\n>>> LLAMADA: POST /complaints\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.tipo'))->toBe('sugerencia');
    expect($response->json('data.motivos'))->toBe(['ambiente_laboral', 'equipo_de_trabajo']);
    expect($response->json('data.motivo_otro'))->toBeNull();
    expect($response->json('data.descripcion'))->toBe('Propongo implementar una sesión semanal de retroalimentación de equipo.');

    $this->assertDatabaseHas('complaints', [
        'id' => $response->json('data.id'),
        'tipo' => 'sugerencia',
    ]);
});

test('3. API POST /complaints (Envío exitoso con motivo otro especificado)', function () {
    $payload = [
        'fecha' => now()->format('Y-m-d'),
        'tipo' => 'peticion',
        'motivos' => ['otro'],
        'motivo_otro' => 'Solicitud de capacitación en nuevas herramientas de desarrollo',
        'descripcion' => 'Requerimos cursos técnicos para el área de TI.',
    ];

    $response = $this->actingAs($this->user)->postJson('/complaints', $payload);

    echo "\n\n>>> LLAMADA: POST /complaints con motivo_otro\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.motivo_otro'))->toBe('Solicitud de capacitación en nuevas herramientas de desarrollo');

    $this->assertDatabaseHas('complaints', [
        'id' => $response->json('data.id'),
        'motivo_otro' => 'Solicitud de capacitación en nuevas herramientas de desarrollo',
    ]);
});

test('4. API POST /complaints (Limpieza de motivo_otro cuando otro no está seleccionado)', function () {
    $payload = [
        'fecha' => now()->format('Y-m-d'),
        'tipo' => 'felicitacion',
        'motivos' => ['trato_personal'],
        'motivo_otro' => 'Texto que no debe guardarse porque otro no fue seleccionado',
        'descripcion' => 'Felicitaciones al equipo de soporte por su rápida atención.',
    ];

    $response = $this->actingAs($this->user)->postJson('/complaints', $payload);

    echo "\n\n>>> LLAMADA: POST /complaints sin motivo otro\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('data.motivo_otro'))->toBeNull();
});

test('5. API POST /complaints (Validación de campos requeridos y tipos no válidos)', function () {
    $payload = [
        'tipo' => 'tipo_invalido',
        'motivos' => ['motivo_inexistente'],
    ];

    $response = $this->actingAs($this->user)->postJson('/complaints', $payload);

    echo "\n\n>>> LLAMADA: POST /complaints con errores de validación\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('errors.fecha'))->toBeArray();
    expect($response->json('errors.tipo'))->toBeArray();
    expect($response->json('errors.descripcion'))->toBeArray();
});

test('6. API DELETE /complaints/{complaint} (Eliminación de queja)', function () {
    $this->user->givePermissionTo('admin.dashboard');

    $complaint = Complaint::create([
        'fecha' => now()->format('Y-m-d'),
        'tipo' => 'queja',
        'motivos' => ['tiempos_respuesta'],
        'descripcion' => 'Queja de prueba para eliminación',
    ]);

    $response = $this->actingAs($this->user)->delete("/complaints/{$complaint->id}");

    echo "\n\n>>> LLAMADA: DELETE /complaints/{$complaint->id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Registro eliminado.');

    $this->assertDatabaseMissing('complaints', [
        'id' => $complaint->id,
    ]);
});
