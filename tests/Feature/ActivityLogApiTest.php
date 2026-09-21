<?php

use App\Models\User;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    // Ensure there is at least one auth activity log for testing
    activity('auth')
        ->causedBy($this->user)
        ->log('El usuario ha iniciado sesión');
});

test('1. API GET /activity-log (Listado de logs con estadísticas de login en JSON)', function () {
    $response = $this->actingAs($this->user)->getJson('/activity-log');

    echo "\n\n>>> LLAMADA: GET /activity-log\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('success'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toHaveKeys([
        'activities',
        'pagination',
        'chart_labels',
        'chart_data',
    ]);
});

test('2. API GET /activity-log?per_page=10 (Paginación personalizada)', function () {
    $response = $this->actingAs($this->user)->getJson('/activity-log?per_page=10');

    echo "\n\n>>> LLAMADA: GET /activity-log?per_page=10\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.pagination.per_page'))->toBe(10);
});

test('3. API GET /activity-log sin autenticación debe retornar 401 no autorizado', function () {
    $response = $this->getJson('/activity-log');

    $response->assertStatus(401);
});
