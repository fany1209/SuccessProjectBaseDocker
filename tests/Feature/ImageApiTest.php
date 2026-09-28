<?php

use App\Models\User;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    // 1x1 PNG transparente para pruebas
    $pngBinary = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');

    // Directorio de productos
    $this->productImageDir = storage_path('app/public/products');
    if (!File::exists($this->productImageDir)) {
        File::makeDirectory($this->productImageDir, 0755, true);
    }
    $this->testImageName = 'test_sample_product.png';
    $this->testImagePath = $this->productImageDir . '/' . $this->testImageName;
    File::put($this->testImagePath, $pngBinary);

    // Directorio de fotos de perfil
    $this->profilePhotoDir = storage_path('app/public/profile-photos');
    if (!File::exists($this->profilePhotoDir)) {
        File::makeDirectory($this->profilePhotoDir, 0755, true);
    }
    $this->testPhotoName = 'test_sample_profile.png';
    $this->testPhotoPath = $this->profilePhotoDir . '/' . $this->testPhotoName;
    File::put($this->testPhotoPath, $pngBinary);
});

afterEach(function () {
    if (isset($this->testImagePath) && File::exists($this->testImagePath)) {
        File::delete($this->testImagePath);
    }
    if (isset($this->testPhotoPath) && File::exists($this->testPhotoPath)) {
        File::delete($this->testPhotoPath);
    }
});

test('1. Web GET /image/{name} (Servir imagen de producto válida con cabecera nosniff)', function () {
    $response = $this->actingAs($this->user)->get("/image/{$this->testImageName}");

    echo "\n\n>>> LLAMADA: GET /image/{name} (Web Image Serve)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
    echo "CONTENT-TYPE: " . $response->headers->get('content-type') . "\n";
    echo "X-CONTENT-TYPE-OPTIONS: " . $response->headers->get('x-content-type-options') . "\n";

    $response->assertStatus(200);
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
});

test('2. API GET /image/{name}?info=1 (Consulta JSON de metadatos de imagen de producto)', function () {
    $response = $this->actingAs($this->user)->getJson("/image/{$this->testImageName}?info=1");

    echo "\n\n>>> LLAMADA: GET /image/{name}?info=1 (JSON Info)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.file_name'))->toBe($this->testImageName);
    expect($response->json('data.extension'))->toBe('png');
    expect($response->json('data.size_bytes'))->toBeGreaterThan(0);
});

test('3. Web GET /image/{name} (404 al solicitar imagen inexistente)', function () {
    $response = $this->actingAs($this->user)->get('/image/non_existent_image_12345.png');

    echo "\n\n>>> LLAMADA: GET /image/non_existent_image_12345.png (Web 404)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(404);
});

test('4. API GET /image/{name} (404 JSON al solicitar imagen inexistente)', function () {
    $response = $this->actingAs($this->user)->getJson('/image/non_existent_image_12345.png');

    echo "\n\n>>> LLAMADA: GET /image/non_existent_image_12345.png (JSON 404)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('5. API GET /image/{name} (Defensa activa contra Path Traversal CWE-22)', function () {
    $traversalPayloads = [
        '..%2F..%2Fetc%2Fpasswd.png',
        '..%2F..%2F.env.jpg',
        '....//....//config//app.png',
        '..%5C..%5C.env.png',
    ];

    foreach ($traversalPayloads as $payload) {
        $response = $this->actingAs($this->user)->getJson("/image/{$payload}");

        echo "\n\n>>> LLAMADA TRAVERSAL: GET /image/{$payload}\n";
        echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

        $response->assertStatus(404);
    }
});

test('6. API GET /image/{name} (Rechazo de extensiones ejecutables o no permitidas)', function () {
    $blockedFiles = [
        'shell_exploit.php',
        'binary_payload.exe',
        'attack_script.sh',
        'client_script.js',
    ];

    foreach ($blockedFiles as $file) {
        $response = $this->actingAs($this->user)->getJson("/image/{$file}");

        echo "\n\n>>> LLAMADA EXTENSIÓN NO PERMITIDA: GET /image/{$file}\n";
        echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

        $response->assertStatus(404);
    }
});

test('7. API GET /image/{name} (Neutralización de inyección de byte nulo)', function () {
    $response = $this->actingAs($this->user)->getJson('/image/test_sample_product%00.png');

    echo "\n\n>>> LLAMADA NULL BYTE: GET /image/test_sample_product%00.png\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(404);
});

test('8. Web GET /profile-photo/{name} (Servir foto de perfil válida)', function () {
    $response = $this->actingAs($this->user)->get("/profile-photo/{$this->testPhotoName}");

    echo "\n\n>>> LLAMADA: GET /profile-photo/{name} (Web Profile Photo Serve)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
    echo "CONTENT-TYPE: " . $response->headers->get('content-type') . "\n";

    $response->assertStatus(200);
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
});

test('9. API GET /profile-photo/{name}?info=1 (Consulta JSON de metadatos de foto de perfil)', function () {
    $response = $this->actingAs($this->user)->getJson("/profile-photo/{$this->testPhotoName}?info=1");

    echo "\n\n>>> LLAMADA: GET /profile-photo/{name}?info=1 (Profile Photo JSON Info)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data.file_name'))->toBe($this->testPhotoName);
    expect($response->json('data.extension'))->toBe('png');
});

test('10. Web GET /profile-photo/{name} (404 al solicitar foto de perfil inexistente)', function () {
    $response = $this->actingAs($this->user)->get('/profile-photo/non_existent_profile_photo.png');

    echo "\n\n>>> LLAMADA: GET /profile-photo/non_existent (Web 404)\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(404);
});

test('11. API GET /profile-photo/{name} (Defensa activa contra Path Traversal en perfil)', function () {
    $response = $this->actingAs($this->user)->getJson('/profile-photo/..%2F..%2F..%2Fetc%2Fpasswd.png');

    echo "\n\n>>> LLAMADA TRAVERSAL PERFIL: GET /profile-photo/..\n";
    echo "HTTP STATUS: " . $response->getStatusCode() . "\n";

    $response->assertStatus(404);
});
