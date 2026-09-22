<?php

use App\Models\ClimaLaboral;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('1. API POST /rh/clima-laboral (Registro de encuesta de clima laboral en JSON)', function () {
    $payload = [
        'q1_ambiente'                => 5,
        'q2_respeto'                 => 4,
        'q3_comunicacion_oportuna'   => 5,
        'q4_comunicacion_escucha'    => 4,
        'q5_liderazgo'               => 5,
        'q6_reconocimiento'          => 4,
        'q7_desarrollo'              => 5,
        'q8_motivacion'              => 4,
        'q9_satisfaccion'            => 5,
        'q10_bienestar_carga'        => 4,
        'q11_bienestar_preocupacion' => 5,
        'q12_sugerencias'            => 'Excelente ambiente y trabajo en equipo.',
    ];

    $response = $this->actingAs($this->user)->postJson('/rh/clima-laboral', $payload);

    echo "\n\n>>> LLAMADA: POST /rh/clima-laboral\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.q1_ambiente'))->toBe(5);
    expect($response->json('data.q12_sugerencias'))->toBe('Excelente ambiente y trabajo en equipo.');
});

test('2. API POST /rh/clima-laboral (Prevención de envío duplicado para un mismo usuario)', function () {
    ClimaLaboral::create([
        'user_id'                    => $this->user->id,
        'q1_ambiente'                => 4,
        'q2_respeto'                 => 4,
        'q3_comunicacion_oportuna'   => 4,
        'q4_comunicacion_escucha'    => 4,
        'q5_liderazgo'               => 4,
        'q6_reconocimiento'          => 4,
        'q7_desarrollo'              => 4,
        'q8_motivacion'              => 4,
        'q9_satisfaccion'            => 4,
        'q10_bienestar_carga'        => 4,
        'q11_bienestar_preocupacion' => 4,
    ]);

    $payload = [
        'q1_ambiente'                => 5,
        'q2_respeto'                 => 5,
        'q3_comunicacion_oportuna'   => 5,
        'q4_comunicacion_escucha'    => 5,
        'q5_liderazgo'               => 5,
        'q6_reconocimiento'          => 5,
        'q7_desarrollo'              => 5,
        'q8_motivacion'              => 5,
        'q9_satisfaccion'            => 5,
        'q10_bienestar_carga'        => 5,
        'q11_bienestar_preocupacion' => 5,
    ];

    $response = $this->actingAs($this->user)->postJson('/rh/clima-laboral', $payload);

    echo "\n\n>>> LLAMADA: POST /rh/clima-laboral (Duplicado)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('message'))->toContain('Ya has contestado la encuesta');
});

test('3. API POST /rh/clima-laboral (Validación estricta de rango de calificación 1 a 5)', function () {
    $payload = [
        'q1_ambiente'                => 99, // Inválido
        'q2_respeto'                 => 4,
        'q3_comunicacion_oportuna'   => 4,
        'q4_comunicacion_escucha'    => 4,
        'q5_liderazgo'               => 4,
        'q6_reconocimiento'          => 4,
        'q7_desarrollo'              => 4,
        'q8_motivacion'              => 4,
        'q9_satisfaccion'            => 4,
        'q10_bienestar_carga'        => 4,
        'q11_bienestar_preocupacion' => 4,
    ];

    $response = $this->actingAs($this->user)->postJson('/rh/clima-laboral', $payload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['q1_ambiente']);
});

test('4. API GET /rh/clima-laboral/resultados (Listado de resultados de encuestas en JSON)', function () {
    $response = $this->actingAs($this->user)->getJson('/rh/clima-laboral/resultados');

    echo "\n\n>>> LLAMADA: GET /rh/clima-laboral/resultados\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toBeArray();
});
