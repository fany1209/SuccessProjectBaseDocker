<?php

use App\Models\Contrato;
use App\Models\User;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->user = User::factory()->create([
        'status' => 'Activo',
        'tipo_empleado' => 'Trabajador',
    ]);
});

test('1. API GET /rh/contratos (Listado de contratos de trabajadores y practicantes en JSON)', function () {
    $response = $this->actingAs($this->user)->getJson('/rh/contratos');

    echo "\n\n>>> LLAMADA: GET /rh/contratos\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data.trabajadores'))->toBeArray();
    expect($response->json('data.practicantes'))->toBeArray();
});

test('2. Web GET /rh/contratos (Carga de vista Blade)', function () {
    $response = $this->actingAs($this->user)->get('/rh/contratos');

    echo "\n\n>>> LLAMADA: GET /rh/contratos (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('rh.contratos.index');
    $response->assertViewHas('trabajadores');
    $response->assertViewHas('practicantes');
});

test('3. API POST /rh/contratos/update (Carga exitosa de contratos PDF)', function () {
    $pdfMes1 = UploadedFile::fake()->create('contrato_mes_1.pdf', 300, 'application/pdf');
    $pdfConf = UploadedFile::fake()->create('acuerdo_confidencialidad.pdf', 200, 'application/pdf');

    $payload = [
        'user_id' => $this->user->id,
        'mes_1' => $pdfMes1,
        'confidencialidad' => $pdfConf,
    ];

    $response = $this->actingAs($this->user)->postJson('/rh/contratos/update', $payload);

    echo "\n\n>>> LLAMADA: POST /rh/contratos/update\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data.mes_1_path'))->toContain('contratos/');
    expect($response->json('data.confidencialidad_path'))->toContain('contratos/');
    expect($response->json('data.user_id'))->toBe($this->user->id);

    $this->assertDatabaseHas('contratos', [
        'user_id' => $this->user->id,
    ]);
});

test('4. API GET /rh/contratos/{userId} (Consulta de contrato individual)', function () {
    Contrato::create([
        'user_id' => $this->user->id,
        'mes_1' => 'contratos/test_mes_1.pdf',
    ]);

    $response = $this->actingAs($this->user)->getJson("/rh/contratos/{$this->user->id}");

    echo "\n\n>>> LLAMADA: GET /rh/contratos/{$this->user->id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.user_id'))->toBe($this->user->id);
    expect($response->json('data.mes_1_path'))->toBe('contratos/test_mes_1.pdf');
});

test('5. API GET /rh/contratos/{userId} (Retorna 404 si el usuario no tiene contratos)', function () {
    $nonExistentUserId = 999999;
    $response = $this->actingAs($this->user)->getJson("/rh/contratos/{$nonExistentUserId}");

    echo "\n\n>>> LLAMADA: GET /rh/contratos/999999\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('6. API POST /rh/contratos/update (Error de validación con archivo no PDF o usuario inexistente)', function () {
    $fakeTxt = UploadedFile::fake()->create('documento.txt', 100, 'text/plain');

    $payload = [
        'user_id' => 999999,
        'mes_1' => $fakeTxt,
    ];

    $response = $this->actingAs($this->user)->postJson('/rh/contratos/update', $payload);

    echo "\n\n>>> LLAMADA: POST /rh/contratos/update con errores de validación\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('errors.user_id'))->toBeArray();
    expect($response->json('errors.mes_1'))->toBeArray();
});
