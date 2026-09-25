<?php

use App\Models\SupplierDirectory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    // Generate unique supplier code for test isolation
    $this->code = rand(80000, 89999);

    $this->supplier = SupplierDirectory::create([
        'Code_supplier' => $this->code,
        'Name'          => 'Proveedor Químico del Bajío S.A. de C.V.',
        'Product'       => 'Reactivos Industriales',
        'Address'       => 'Carretera Panamericana Km 12',
        'Phone'         => '4611234567',
        'Email'         => 'ventas@quimicobajio.com',
        'RFC'           => 'PQB900101XYZ',
        'Contact'       => 'Ing. Carlos Mendoza',
    ]);
});

test('1. API GET /suppliers-get-directory (Listado para DataTables con compatibilidad)', function () {
    $response = $this->actingAs($this->user)->getJson('/suppliers-get-directory');

    echo "\n\n>>> LLAMADA: GET /suppliers-get-directory\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('suppliers'))->toBeArray();
    expect($response->json('data'))->toBeArray();

    $item = collect($response->json('suppliers'))->firstWhere('Code_supplier', $this->code);
    expect($item)->not->toBeNull();
    expect($item['Name'])->toBe('Proveedor Químico del Bajío S.A. de C.V.');
});

test('2. API GET /purchases/supplier-directory (Resource index)', function () {
    $response = $this->actingAs($this->user)->getJson('/purchases/supplier-directory');

    echo "\n\n>>> LLAMADA: GET /purchases/supplier-directory\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data'))->toBeArray();
});

test('3. API GET /purchases/supplier-directory/{code} (Consulta individual existente)', function () {
    $response = $this->actingAs($this->user)->getJson("/purchases/supplier-directory/{$this->code}");

    echo "\n\n>>> LLAMADA: GET /purchases/supplier-directory/{$this->code}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.Code_supplier'))->toBe($this->code);
    expect($response->json('data.Name'))->toBe('Proveedor Químico del Bajío S.A. de C.V.');
});

test('4. API GET /purchases/supplier-directory/{code} (Código inexistente retorna 404)', function () {
    $response = $this->actingAs($this->user)->getJson('/purchases/supplier-directory/99999999');

    echo "\n\n>>> LLAMADA: GET /purchases/supplier-directory/99999999 (Inexistente)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('flag'))->toBeFalse();
});

test('5. API POST /purchases/supplier-directory (Creación exitosa)', function () {
    $newCode = rand(90000, 99999);
    $payload = [
        'Code_supplier' => $newCode,
        'Name'          => 'Nuevo Proveedor de Empaques S.A.',
        'Product'       => 'Cajas de Cartón Corrugado',
        'Address'       => 'Parque Industrial Norte Bodega 4',
        'Phone'         => '4429876543',
        'Email'         => 'contacto@empaquesnorte.com',
        'RFC'           => 'NPE180202ABC',
        'Contact'       => 'Lic. Mariana Soto',
    ];

    $response = $this->actingAs($this->user)->postJson('/purchases/supplier-directory', $payload);

    echo "\n\n>>> LLAMADA: POST /purchases/supplier-directory\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('message'))->toBe('Proveedor guardado correctamente');
    expect($response->json('data.Code_supplier'))->toBe($newCode);

    $this->assertDatabaseHas('supplier_directory', [
        'Code_supplier' => $newCode,
        'Name'          => 'Nuevo Proveedor de Empaques S.A.',
    ]);
});

test('6. API POST /purchases/supplier-directory (Error de validación por campos obligatorios)', function () {
    $payload = [
        'Name' => '',
        'Code_supplier' => '',
    ];

    $response = $this->actingAs($this->user)->postJson('/purchases/supplier-directory', $payload);

    echo "\n\n>>> LLAMADA: POST /purchases/supplier-directory con campos vacíos\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['Name', 'Code_supplier']);
});

test('7. API POST /purchases/supplier-directory (Error de validación por código duplicado)', function () {
    $payload = [
        'Code_supplier' => $this->code, // Ya existe
        'Name'          => 'Proveedor Duplicado',
    ];

    $response = $this->actingAs($this->user)->postJson('/purchases/supplier-directory', $payload);

    echo "\n\n>>> LLAMADA: POST /purchases/supplier-directory con código duplicado\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['Code_supplier']);
});

test('8. API POST /purchases/supplier-directory (Sanitización anti Stored-XSS)', function () {
    $newCode = rand(90000, 99999);
    $payload = [
        'Code_supplier' => $newCode,
        'Name'          => '<script>alert("XSS")</script>Proveedor Seguro',
        'Product'       => '<b>Materia Prima</b>',
        'Address'       => '<script>malicious()</script>Calle Segura 456',
        'Contact'       => '<span>Ing. Seguro</span>',
    ];

    $response = $this->actingAs($this->user)->postJson('/purchases/supplier-directory', $payload);

    echo "\n\n>>> LLAMADA: POST /purchases/supplier-directory con payload XSS\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('data.Name'))->toBe('alert("XSS")Proveedor Seguro');
    expect($response->json('data.Product'))->toBe('Materia Prima');
    expect($response->json('data.Address'))->toBe('malicious()Calle Segura 456');
    expect($response->json('data.Contact'))->toBe('Ing. Seguro');

    $this->assertDatabaseHas('supplier_directory', [
        'Code_supplier' => $newCode,
        'Name'          => 'alert("XSS")Proveedor Seguro',
        'Product'       => 'Materia Prima',
    ]);
});

test('9. API PUT /purchases/supplier-directory/{code} (Actualización exitosa)', function () {
    $payload = [
        'original_code' => $this->code,
        'Code_supplier' => $this->code,
        'Name'          => 'Proveedor Químico del Bajío Actualizado S.A.',
        'Product'       => 'Solventes y Reactivos de Grado Analítico',
        'Phone'         => '4619871122',
        'Contact'       => 'Dra. Patricia Silva',
    ];

    $response = $this->actingAs($this->user)->putJson("/purchases/supplier-directory/{$this->code}", $payload);

    echo "\n\n>>> LLAMADA: PUT /purchases/supplier-directory/{$this->code}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('message'))->toBe('Proveedor actualizado correctamente');
    expect($response->json('data.Name'))->toBe('Proveedor Químico del Bajío Actualizado S.A.');
    expect($response->json('data.Product'))->toBe('Solventes y Reactivos de Grado Analítico');

    $this->assertDatabaseHas('supplier_directory', [
        'Code_supplier' => $this->code,
        'Name'          => 'Proveedor Químico del Bajío Actualizado S.A.',
        'Product'       => 'Solventes y Reactivos de Grado Analítico',
    ]);
});

test('10. API PUT /purchases/supplier-directory/{code} (Actualización en código inexistente retorna 404)', function () {
    $payload = [
        'original_code' => 99999999,
        'Code_supplier' => 99999999,
        'Name'          => 'Proveedor Fantasma',
    ];

    $response = $this->actingAs($this->user)->putJson('/purchases/supplier-directory/99999999', $payload);

    echo "\n\n>>> LLAMADA: PUT /purchases/supplier-directory/99999999 (Inexistente)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('flag'))->toBeFalse();
});

test('11. API DELETE /purchases/supplier-directory/{code} (Eliminación exitosa)', function () {
    $response = $this->actingAs($this->user)->deleteJson("/purchases/supplier-directory/{$this->code}");

    echo "\n\n>>> LLAMADA: DELETE /purchases/supplier-directory/{$this->code}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('status'))->toBe('success');
    expect($response->json('message'))->toBe('Proveedor eliminado correctamente.');

    $this->assertDatabaseMissing('supplier_directory', [
        'Code_supplier' => $this->code,
    ]);
});

test('12. API DELETE /purchases/supplier-directory/{code} (Eliminación de código inexistente retorna 404)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/purchases/supplier-directory/99999999');

    echo "\n\n>>> LLAMADA: DELETE /purchases/supplier-directory/99999999 (Inexistente)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('status'))->toBe('error');
});
