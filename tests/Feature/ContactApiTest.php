<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    RateLimiter::clear('contact-form');
    $this->user = User::factory()->create();
});

test('1. API POST /contact (Envío exitoso de mensaje en formato JSON)', function () {
    $payload = [
        'name' => 'Juan Pérez',
        'phone' => '4611234567',
        'email' => 'juan.perez@example.com',
        'message' => 'Solicito información sobre distribución de materias primas.',
    ];

    $response = $this->postJson('/contact', $payload);

    echo "\n\n>>> LLAMADA: POST /contact (JSON)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(201);
    expect($response->json('success'))->toBeTrue();
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.name'))->toBe('Juan Pérez');
    expect($response->json('data.email'))->toBe('juan.perez@example.com');
    expect($response->json('data.phone'))->toBe('4611234567');
    expect($response->json('data.contact_id'))->toBeInt();

    $this->assertDatabaseHas('contacts', [
        'email' => 'juan.perez@example.com',
        'name' => 'Juan Pérez',
    ]);
});

test('2. Web POST /contact (Envío desde formulario web con redirección)', function () {
    $payload = [
        'name' => 'María Gómez',
        'phone' => '4619876543',
        'email' => 'maria.gomez@example.com',
        'message' => 'Interesada en cotización de producto terminado.',
    ];

    $response = $this->post('/contact', $payload);

    echo "\n\n>>> LLAMADA: POST /contact (Web Form)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Mensaje enviado exitosamente.');

    $this->assertDatabaseHas('contacts', [
        'email' => 'maria.gomez@example.com',
    ]);
});

test('3. POST /contact (Detección de honeypot bot_check descarta sin persistir)', function () {
    $payload = [
        'name' => 'Spam Bot Automatic',
        'phone' => '0000000000',
        'email' => 'bot@spammer.org',
        'message' => 'Buy cheap links now',
        'bot_check' => 'I am a bot',
    ];

    $response = $this->postJson('/contact', $payload);

    echo "\n\n>>> LLAMADA: POST /contact (Honeypot Triggered)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();

    $this->assertDatabaseMissing('contacts', [
        'email' => 'bot@spammer.org',
    ]);
});

test('4. POST /contact (Validación de campos obligatorios y formato de correo)', function () {
    $payload = [
        'name' => '',
        'phone' => '',
        'email' => 'correo-invalido',
        'message' => '',
    ];

    $response = $this->postJson('/contact', $payload);

    echo "\n\n>>> LLAMADA: POST /contact con errores de validación\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('errors.name'))->toBeArray();
    expect($response->json('errors.phone'))->toBeArray();
    expect($response->json('errors.email'))->toBeArray();
    expect($response->json('errors.message'))->toBeArray();
});

test('5. Admin POST /admin/contacts/{id}/mark-read (Marcar mensaje como leído)', function () {
    Permission::findOrCreate('admin.dashboard', 'web');
    $this->user->givePermissionTo('admin.dashboard');

    $contact = Contact::create([
        'name' => 'Carlos López',
        'phone' => '4615551234',
        'email' => 'carlos@example.com',
        'message' => 'Pregunta sobre entregas.',
    ]);

    $response = $this->actingAs($this->user)->postJson("/admin/contacts/{$contact->contact_id}/mark-read");

    echo "\n\n>>> LLAMADA: POST /admin/contacts/{$contact->contact_id}/mark-read\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('success'))->toBeTrue();

    $this->assertDatabaseMissing('contacts', [
        'contact_id' => $contact->contact_id,
        'read_at' => null,
    ]);
});

test('6. Admin DELETE /admin/contacts/{id} (Eliminación de mensaje)', function () {
    Permission::findOrCreate('admin.dashboard', 'web');
    $this->user->givePermissionTo('admin.dashboard');

    $contact = Contact::create([
        'name' => 'Contacto Para Borrar',
        'phone' => '4619998888',
        'email' => 'borrar@example.com',
        'message' => 'Mensaje desechable.',
    ]);

    $response = $this->actingAs($this->user)->delete("/admin/contacts/{$contact->contact_id}");

    echo "\n\n>>> LLAMADA: DELETE /admin/contacts/{$contact->contact_id}\n";
    echo "HTTP STATUS: " . $response->status() . "\n";

    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Contact deleted successfully.');

    $this->assertDatabaseMissing('contacts', [
        'contact_id' => $contact->contact_id,
    ]);
});
