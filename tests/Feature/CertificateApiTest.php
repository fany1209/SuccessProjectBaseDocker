<?php

use App\Models\QualityCertificate;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->certificate = QualityCertificate::create([
        'folio'              => 'CERT-TEST-' . rand(10000, 99999),
        'fecha'              => now()->toDateString(),
        'cliente'            => 'Cliente de Prueba Calidad S.A.',
        'producto'           => 'Harina Deshidratada Premium',
        'lote'               => 'LOTE-' . rand(100, 999),
        'cantidad'           => '500 kg',
        'no_tarimas'         => 2,
        'fecha_salida_cedis' => now()->addDays(2)->toDateString(),
        'certificado_tarima' => 'TAR-001',
        'muestra_o_pf'       => 'PT',
    ]);
});

test('1. API GET /certificados/json (Listado de certificados de calidad en formato JSON)', function () {
    $response = $this->actingAs($this->user)->getJson('/certificados/json');

    echo "\n\n>>> LLAMADA: GET /certificados/json\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toBeArray();
    expect(count($response->json('data')))->toBeGreaterThan(0);
});

test('2. API DELETE /certificados/{id} (Eliminación exitosa de certificado con bloqueo pesimista)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/certificados/' . $this->certificate->id);

    echo "\n\n>>> LLAMADA: DELETE /certificados/{$this->certificate->id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect(QualityCertificate::find($this->certificate->id))->toBeNull();
});

test('3. API DELETE /certificados/{id} con ID no numérico retorna 422', function () {
    $response = $this->actingAs($this->user)->deleteJson('/certificados/invalido');

    $response->assertStatus(422);
    expect($response->json('success'))->toBeFalse();
});

test('4. API DELETE /certificados/{id} con ID inexistente retorna 404', function () {
    $response = $this->actingAs($this->user)->deleteJson('/certificados/99999999');

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
});

test('5. API GET /certificados/export (Descarga en streaming de archivo CSV)', function () {
    $response = $this->actingAs($this->user)->get('/certificados/export');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});
