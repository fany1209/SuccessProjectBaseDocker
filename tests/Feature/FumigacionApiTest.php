<?php

use App\Models\Fumigacion;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->fumigacion = Fumigacion::create([
        'proveedor'         => 'FUMIGACIONES AMBIENTALES DEL BAJIO S.A. DE C.V.',
        'fecha_programada'  => now()->addDays(5)->format('Y-m-d H:i:s'),
        'metodo_aplicacion' => 'Aspersión',
        'estado'            => 'Pendiente',
        'observaciones'     => 'Servicio preventivo para bodegas de granos y materias primas',
    ]);
});

afterEach(function () {
    if (isset($this->fumigacion) && $this->fumigacion->exists) {
        Fumigacion::where('id', $this->fumigacion->id)->delete();
    }
});

test('1. Web GET /fumigaciones (Renderizar vista de control con calendario de servicios)', function () {
    $response = $this->actingAs($this->user)->get('/fumigaciones');

    echo "\n\n>>> LLAMADA: GET /fumigaciones (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('quality.fumigaciones.index');
    $response->assertViewHas('todasLasFumigaciones');
    $response->assertViewHas('eventos');
});

test('2. API GET /fumigaciones (Consulta JSON de fumigaciones)', function () {
    $response = $this->actingAs($this->user)->getJson('/fumigaciones');

    echo "\n\n>>> LLAMADA: GET /fumigaciones (JSON List)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data'))->toBeArray();
});

test('3. API POST /fumigaciones/store (Programación exitosa de nueva fumigación)', function () {
    $payload = [
        'proveedor'         => 'CONTROL DE PLAGAS INTEGRAL',
        'fecha_programada'  => now()->addDays(10)->format('Y-m-d H:i'),
        'metodo_aplicacion' => 'Termonebulización',
        'estado'            => 'Pendiente',
        'observaciones'     => 'Aplicación en naves de producción y laboratorio',
    ];

    $response = $this->actingAs($this->user)->postJson('/fumigaciones/store', $payload);

    echo "\n\n>>> LLAMADA: POST /fumigaciones/store (Create Fumigacion)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.proveedor'))->toBe('CONTROL DE PLAGAS INTEGRAL');
    expect($response->json('data.metodo_aplicacion'))->toBe('Termonebulización');

    // Limpieza
    if ($id = $response->json('data.id')) {
        Fumigacion::where('id', $id)->delete();
    }
});

test('4. API POST /fumigaciones/store (Validación defensiva 422 ante campos obligatorios)', function () {
    $response = $this->actingAs($this->user)->postJson('/fumigaciones/store', []);

    echo "\n\n>>> LLAMADA: POST /fumigaciones/store (422 Validation)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['proveedor', 'fecha_programada']);
});

test('5. API POST /fumigaciones/store (Sanitización anti Stored-XSS)', function () {
    $payload = [
        'proveedor'         => '<b>Fumigaciones Seguras S.A.</b>',
        'fecha_programada'  => now()->addDays(7)->format('Y-m-d H:i'),
        'metodo_aplicacion' => 'Nebulización',
        'estado'            => 'Pendiente',
        'observaciones'     => '<i>Observaciones de fumigación limpia</i>',
    ];

    $response = $this->actingAs($this->user)->postJson('/fumigaciones/store', $payload);

    echo "\n\n>>> LLAMADA: POST /fumigaciones/store (Anti-XSS Check)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(201);
    expect($response->json('data.proveedor'))->toBe('Fumigaciones Seguras S.A.');
    expect($response->json('data.observaciones'))->toBe('Observaciones de fumigación limpia');

    // Limpieza
    if ($id = $response->json('data.id')) {
        Fumigacion::where('id', $id)->delete();
    }
});

test('6. API POST /fumigaciones/update/{id} (Actualización exitosa de registro con bloqueo pesimista)', function () {
    $payload = [
        'proveedor'         => 'FUMIGACIONES AMBIENTALES ACTUALIZADO',
        'fecha_programada'  => now()->addDays(2)->format('Y-m-d H:i'),
        'metodo_aplicacion' => 'Gel',
        'estado'            => 'Realizado',
        'observaciones'     => 'Servicio concluido exitosamente sin incidencias',
    ];

    $response = $this->actingAs($this->user)->postJson("/fumigaciones/update/{$this->fumigacion->id}", $payload);

    echo "\n\n>>> LLAMADA: POST /fumigaciones/update/{id} (Update Fumigacion)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.proveedor'))->toBe('FUMIGACIONES AMBIENTALES ACTUALIZADO');
    expect($response->json('data.estado'))->toBe('Realizado');
});

test('7. API POST /fumigaciones/update/{id} (404 al actualizar registro inexistente)', function () {
    $response = $this->actingAs($this->user)->postJson('/fumigaciones/update/999999', [
        'proveedor'        => 'Proveedor Inexistente',
        'fecha_programada' => now()->format('Y-m-d H:i'),
    ]);

    echo "\n\n>>> LLAMADA: POST /fumigaciones/update/999999 (404 Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
});

test('8. API POST /fumigaciones/delete/{id} (Eliminación exitosa de fumigación)', function () {
    $response = $this->actingAs($this->user)->postJson("/fumigaciones/delete/{$this->fumigacion->id}");

    echo "\n\n>>> LLAMADA: POST /fumigaciones/delete/{id} (Delete Fumigacion)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect(Fumigacion::where('id', $this->fumigacion->id)->exists())->toBeFalse();
});

test('9. API POST /fumigaciones/delete/{id} (404 al eliminar registro inexistente)', function () {
    $response = $this->actingAs($this->user)->postJson('/fumigaciones/delete/999999');

    echo "\n\n>>> LLAMADA: POST /fumigaciones/delete/999999 (404 Delete Not Found)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
});

test('10. Web GET /fumigaciones (Redirección a login cuando no está autenticado)', function () {
    $response = $this->get('/fumigaciones');

    echo "\n\n>>> LLAMADA SIN AUTENTICAR: GET /fumigaciones\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(302);
    $response->assertRedirect('/login');
});
