<?php

use App\Models\User;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->testDirectory = storage_path('app/public/files');
    if (!File::exists($this->testDirectory)) {
        File::makeDirectory($this->testDirectory, 0755, true);
    }

    $this->testFileName = 'sample_catalog_test.pdf';
    $this->testFilePath = $this->testDirectory . '/' . $this->testFileName;
    File::put($this->testFilePath, '%PDF-1.4 Mock PDF Content For Testing Antigravity');
});

afterEach(function () {
    if (isset($this->testFilePath) && File::exists($this->testFilePath)) {
        File::delete($this->testFilePath);
    }
});

test('1. Web GET /open-file/{fileName} (Servir archivo PDF válido con cabeceras de seguridad)', function () {
    $response = $this->actingAs($this->user)->get("/open-file/{$this->testFileName}");

    echo "\n\n>>> LLAMADA: GET /open-file/{fileName} (Web File Serve)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
    echo "CONTENT-TYPE: " . $response->headers->get('content-type') . "\n";
    echo "X-CONTENT-TYPE-OPTIONS: " . $response->headers->get('x-content-type-options') . "\n";

    $response->assertStatus(200);
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
});

test('2. API GET /open-file/{fileName}?info=1 (Consulta JSON de metadatos del archivo)', function () {
    $response = $this->actingAs($this->user)->getJson("/open-file/{$this->testFileName}?info=1");

    echo "\n\n>>> LLAMADA: GET /open-file/{fileName}?info=1 (JSON Info)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.file_name'))->toBe($this->testFileName);
    expect($response->json('data.extension'))->toBe('pdf');
    expect($response->json('data.size_bytes'))->toBeGreaterThan(0);
});

test('3. Web GET /open-file/{fileName} (404 al solicitar archivo inexistente)', function () {
    $response = $this->actingAs($this->user)->get('/open-file/non_existent_document_12345.pdf');

    echo "\n\n>>> LLAMADA: GET /open-file/non_existent_document_12345.pdf (Web 404)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(404);
});

test('4. API GET /open-file/{fileName} (404 JSON al solicitar archivo inexistente)', function () {
    $response = $this->actingAs($this->user)->getJson('/open-file/non_existent_document_12345.pdf');

    echo "\n\n>>> LLAMADA: GET /open-file/non_existent_document_12345.pdf (JSON 404)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('5. API GET /open-file (Defensa activa contra Path Traversal CWE-22)', function () {
    // Intentos de escape de directorio
    $traversalPayloads = [
        '..%2F..%2Fetc%2Fpasswd',
        '..%2F..%2F.env',
        '....//....//config//app.php',
        '..%5C..%5C.env',
    ];

    foreach ($traversalPayloads as $payload) {
        $response = $this->actingAs($this->user)->getJson("/open-file/{$payload}");

        echo "\n\n>>> LLAMADA TRAVERSAL: GET /open-file/{$payload}\n";
        echo "HTTP STATUS: " . $response->status() . "\n";

        $response->assertStatus(404);
    }
});

test('6. API GET /open-file (Rechazo de extensiones no permitidas: php, exe, sh, js)', function () {
    $blockedFiles = [
        'malicious_script.php',
        'executable_payload.exe',
        'shell_exploit.sh',
        'script.js',
    ];

    foreach ($blockedFiles as $file) {
        $response = $this->actingAs($this->user)->getJson("/open-file/{$file}");

        echo "\n\n>>> LLAMADA EXTENSIÓN NO PERMITIDA: GET /open-file/{$file}\n";
        echo "HTTP STATUS: " . $response->status() . "\n";

        $response->assertStatus(404);
    }
});

test('7. API GET /open-file (Rechazo de inyección de byte nulo)', function () {
    $response = $this->actingAs($this->user)->getJson('/open-file/sample%00_catalog.pdf');

    echo "\n\n>>> LLAMADA NULL BYTE: GET /open-file/sample%00_catalog.pdf\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(404);
});

test('8. Web GET /open-file (Redirección a login cuando no está autenticado)', function () {
    $response = $this->get("/open-file/{$this->testFileName}");

    echo "\n\n>>> LLAMADA SIN AUTENTICAR: GET /open-file/{fileName}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(302);
    $response->assertRedirect('/login');
});
