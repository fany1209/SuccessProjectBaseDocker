<?php

use App\Models\Customer;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    Permission::findOrCreate('customers.show', 'web');
    Permission::findOrCreate('customers.create', 'web');
    Permission::findOrCreate('customers.update', 'web');
    Permission::findOrCreate('customers.delete', 'web');
    $this->user->givePermissionTo(['customers.show', 'customers.create', 'customers.update', 'customers.delete']);

    // Use existing sectors with valid codes
    $this->sector1 = Sector::where('code', 'P')->first() ?? Sector::first();
    $this->sector2 = Sector::where('code', 'A')->first() ?? Sector::skip(1)->first();

    $randomSuffix = rand(10000, 99999);
    // Create a base Customer
    $this->customer = Customer::create([
        'sector_id' => $this->sector1->sector_id,
        'customer_code' => 'SCP' . $randomSuffix,
        'name' => 'Cliente de Prueba ' . $randomSuffix . ' S.A. de C.V.',
        'phone' => '4611234567',
        'email' => 'cliente.' . $randomSuffix . '@example.com',
        'rfc' => 'CPR' . $randomSuffix,
        'postal_code' => '38000',
        'state' => 'Guanajuato',
        'city' => 'Celaya',
        'district' => 'Centro',
        'address' => 'Av. Tecnológico 123',
        'country' => 'México',
        'vendedor' => $this->user->name,
        'contact' => 'Lic. Roberto Gómez',
        'delivery_address' => 'Planta Industrial Km 5',
    ]);
});

test('1. API GET /customers (JSON - Resumen e información del catálogo de clientes)', function () {
    $response = $this->actingAs($this->user)->getJson('/customers');

    echo "\n\n>>> LLAMADA: GET /customers (JSON)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
    expect($response->json('data'))->toHaveKeys([
        'sectors',
        'total_customers',
        'customers_per_sector',
        'sellers',
        'clientesSistemas',
    ]);
});

test('2. Web GET /customers (Retorna vista Blade)', function () {
    $response = $this->actingAs($this->user)->get('/customers');

    echo "\n\n>>> LLAMADA: GET /customers (Web View)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(200);
    $response->assertViewIs('customers');
    $response->assertViewHas(['sectors', 'total_customers', 'customers_per_sector', 'sellers']);
});

test('3. API GET /getCustomers (Listado para DataTables con permisos)', function () {
    $response = $this->actingAs($this->user)->getJson('/getCustomers');

    echo "\n\n>>> LLAMADA: GET /getCustomers\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('customers'))->toBeArray();
    expect($response->json('data'))->toBeArray();

    $firstCustomer = collect($response->json('customers'))->firstWhere('customer_id', $this->customer->customer_id);
    expect($firstCustomer)->not->toBeNull();
    expect($firstCustomer['canUpdate'])->toBeTrue();
    expect($firstCustomer['canDelete'])->toBeTrue();
});

test('4. API GET /getCustomers con filtros de búsqueda', function () {
    $response = $this->actingAs($this->user)->getJson('/getCustomers?search=Cliente&city=Celaya');

    echo "\n\n>>> LLAMADA: GET /getCustomers (Filtros de búsqueda)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect(count($response->json('customers')))->toBeGreaterThanOrEqual(1);
});

test('5. API GET /customers/{id} (Consulta individual de cliente con compatibilidad)', function () {
    $response = $this->actingAs($this->user)->getJson("/customers/{$this->customer->customer_id}");

    echo "\n\n>>> LLAMADA: GET /customers/{$this->customer->customer_id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('customer.customer_id'))->toBe($this->customer->customer_id);
    expect($response->json('data.customer_id'))->toBe($this->customer->customer_id);
    expect($response->json('customer.name'))->toBe($this->customer->name);
});

test('6. API GET /customers/{id} (Cliente inexistente retorna 404)', function () {
    $response = $this->actingAs($this->user)->getJson('/customers/999999');

    echo "\n\n>>> LLAMADA: GET /customers/999999 (Inexistente)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('flag'))->toBeFalse();
});

test('7. API POST /customers (Creación exitosa con código auto-generado y vendedor)', function () {
    $randomSuffix = rand(100000, 999999);
    $payload = [
        'sector_id' => $this->sector1->sector_id,
        'name' => 'Nuevo Cliente Agroindustrial ' . $randomSuffix . ' S.A.',
        'phone' => '4421234567',
        'email' => 'contacto.' . $randomSuffix . '@nuevoagro.com',
        'rfc' => 'NCA' . $randomSuffix,
        'postal_code' => '76000',
        'state' => 'Querétaro',
        'city' => 'Querétaro',
        'district' => 'Parque Industrial',
        'address' => 'Acceso IV No. 50',
        'country' => 'México',
        'contact' => 'Ing. Laura Martínez',
        'delivery_address' => 'Andén 3',
    ];

    $response = $this->actingAs($this->user)->postJson('/customers', $payload);

    echo "\n\n>>> LLAMADA: POST /customers\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('customer_code'))->toStartWith('SC' . $this->sector1->code);
    expect($response->json('data.name'))->toBe($payload['name']);

    $this->assertDatabaseHas('customers', [
        'name' => $payload['name'],
        'sector_id' => $this->sector1->sector_id,
    ]);
});

test('8. API POST /customers con errores de validación', function () {
    $payload = [
        'name' => '', // Obligatorio
        'sector_id' => 999999, // Inexistente
    ];

    $response = $this->actingAs($this->user)->postJson('/customers', $payload);

    echo "\n\n>>> LLAMADA: POST /customers con errores de validación\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'sector_id']);
});

test('9. API PUT /customers/{id} (Actualización exitosa de atributos generales)', function () {
    $payload = [
        'customer_id' => $this->customer->customer_id,
        'sector_id' => $this->sector1->sector_id,
        'name' => 'Cliente de Prueba Actualizado S.A.',
        'phone' => '4619876543',
        'email' => 'actualizado@example.com',
        'contact' => 'Lic. Roberto Gómez Modificado',
    ];

    $response = $this->actingAs($this->user)->putJson("/customers/{$this->customer->customer_id}", $payload);

    echo "\n\n>>> LLAMADA: PUT /customers/{$this->customer->customer_id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.name'))->toBe('Cliente de Prueba Actualizado S.A.');
    expect($response->json('data.phone'))->toBe('4619876543');

    $this->assertDatabaseHas('customers', [
        'customer_id' => $this->customer->customer_id,
        'name' => 'Cliente de Prueba Actualizado S.A.',
        'phone' => '4619876543',
    ]);
});

test('10. API PUT /customers/{id} (Cambio de sector reasigna customer_code con nuevo prefijo)', function () {
    $payload = [
        'customer_id' => $this->customer->customer_id,
        'sector_id' => $this->sector2->sector_id,
        'name' => $this->customer->name,
    ];

    $response = $this->actingAs($this->user)->putJson("/customers/{$this->customer->customer_id}", $payload);

    echo "\n\n>>> LLAMADA: PUT /customers/{$this->customer->customer_id} (Cambio de sector)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('new_code'))->toStartWith('SC' . $this->sector2->code);

    $this->customer->refresh();
    expect($this->customer->sector_id)->toBe($this->sector2->sector_id);
    expect($this->customer->customer_code)->toStartWith('SC' . $this->sector2->code);
});

test('11. API DELETE /customers/{id} (Eliminación exitosa de cliente sin ventas)', function () {
    $response = $this->actingAs($this->user)->deleteJson("/customers/{$this->customer->customer_id}");

    echo "\n\n>>> LLAMADA: DELETE /customers/{$this->customer->customer_id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('message'))->toBe('Customer deleted');

    $this->assertDatabaseMissing('customers', [
        'customer_id' => $this->customer->customer_id,
    ]);
});

test('12. API DELETE /customers/{id} (Rechaza eliminación si el cliente tiene ventas)', function () {
    // Asociar una venta de prueba
    $saleId = DB::table('sales')->insertGetId([
        'seller' => 'Vendedor Test',
        'first_time' => 0,
        'is_customer' => 1,
        'purchase_order' => 'OC-CUST-TEST-' . rand(100000, 999999),
        'invoice' => 'FAC-CUST-TEST-' . rand(100000, 999999),
        'sale_type' => 'cash',
        'term' => 'Contado',
        'date' => now()->toDateString(),
        'folio' => rand(10000, 99999),
        'customer_id' => $this->customer->customer_id,
        'prospect_id' => null,
        'user_id' => $this->user->id,
        'sales_status_id' => 1,
        'sector_id' => 1,
        'payment_status' => 'paid',
        'almacen_status' => 'confirmed',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/customers/{$this->customer->customer_id}");

    echo "\n\n>>> LLAMADA: DELETE /customers/{$this->customer->customer_id} con ventas asociadas\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('message'))->toContain('ventas');

    $this->assertDatabaseHas('customers', [
        'customer_id' => $this->customer->customer_id,
    ]);
});

test('13. API DELETE /customers/{id} (Cliente inexistente retorna 404)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/customers/999999');

    echo "\n\n>>> LLAMADA: DELETE /customers/999999 (Inexistente)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(404);
    expect($response->json('flag'))->toBeFalse();
});
