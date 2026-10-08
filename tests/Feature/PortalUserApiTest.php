<?php

use App\Models\Customer;
use App\Models\PortalDocument;
use App\Models\PortalUser;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'admin_portal_' . uniqid() . '@example.com',
    ]);

    $this->sector = \App\Models\Sector::first() ?? \App\Models\Sector::create([
        'name' => 'Sector ' . uniqid(),
        'code' => 'S' . rand(10, 99),
    ]);

    $this->customer = Customer::create([
        'sector_id'     => $this->sector->sector_id,
        'name'          => 'Cliente Portal Test ' . uniqid(),
        'customer_code' => 'CLI-' . rand(1000, 9999),
        'email'         => 'cliente_' . uniqid() . '@example.com',
        'rfc'           => 'XAXX010101000',
    ]);

    $this->createSale = function (array $overrides = []) {
        static $counter = 8000;
        $counter++;
        return Sale::create(array_merge([
            'seller'          => 'Vendedor Prueba',
            'first_time'      => 1,
            'is_customer'     => 1,
            'purchase_order'  => 'PO-' . uniqid(),
            'invoice'         => 'INV-' . uniqid(),
            'sale_type'       => 'Credit',
            'term'            => '30 días',
            'date'            => now()->format('Y-m-d'),
            'folio'           => $counter,
            'customer_id'     => $this->customer->customer_id,
            'sector_id'       => $this->sector->sector_id,
            'user_id'         => $this->user->id,
            'sales_status_id' => 1,
        ], $overrides));
    };
});

test('index renders portal users view for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('admin.portal-users.index'));

    $response->assertStatus(200);
    $response->assertViewIs('portal-users');
    $response->assertViewHas('clientesSistemas');
});

test('getPortalUsers returns json list of portal users', function () {
    $portalUser = PortalUser::create([
        'customer_id'     => $this->customer->customer_id,
        'nombre_contacto' => 'Contacto Test ' . uniqid(),
        'empresa'         => 'Empresa Test ' . uniqid(),
        'email'           => 'portal_test_' . uniqid() . '@example.com',
        'password'        => Hash::make('secret123'),
        'is_active'       => 1,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('admin.portal-users.getPortalUsers'));

    $response->assertStatus(200);
    $response->assertJsonStructure(['users']);

    $users = collect($response->json('users'));
    expect($users->pluck('email'))->toContain($portalUser->email);
});

test('store creates a new portal user and hashes password', function () {
    $email = 'nuevo_portal_' . uniqid() . '@example.com';
    $payload = [
        'customer_id'           => $this->customer->customer_id,
        'nombre_contacto'       => 'Juan Perez',
        'empresa'               => 'Empresa Alfa',
        'email'                 => $email,
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'is_active'             => '1',
    ];

    $response = $this->actingAs($this->user)->postJson(route('admin.portal-users.store'), $payload);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => '¡El usuario para el portal de clientes se ha creado con éxito!',
    ]);

    $this->assertDatabaseHas('portal_users', [
        'email'           => $email,
        'nombre_contacto' => 'Juan Perez',
        'empresa'         => 'Empresa Alfa',
        'is_active'       => 1,
    ]);

    $created = PortalUser::where('email', $email)->first();
    expect(Hash::check('password123', $created->password))->toBeTrue();
});

test('store fails validation when required fields are missing', function () {
    $response = $this->actingAs($this->user)->postJson(route('admin.portal-users.store'), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['customer_id', 'nombre_contacto', 'empresa', 'email', 'password']);
});

test('edit returns user data or 404 when not found', function () {
    $portalUser = PortalUser::create([
        'customer_id'     => $this->customer->customer_id,
        'nombre_contacto' => 'Contacto Edit ' . uniqid(),
        'empresa'         => 'Empresa Edit ' . uniqid(),
        'email'           => 'edit_' . uniqid() . '@example.com',
        'password'        => Hash::make('secret123'),
        'is_active'       => 1,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('admin.portal-users.edit', $portalUser->id));

    $response->assertStatus(200);
    $response->assertJsonPath('user.email', $portalUser->email);

    $notFound = $this->actingAs($this->user)->getJson(route('admin.portal-users.edit', 99999999));
    $notFound->assertStatus(404);
});

test('update modifies existing portal user', function () {
    $portalUser = PortalUser::create([
        'customer_id'     => $this->customer->customer_id,
        'nombre_contacto' => 'Antes de Update',
        'empresa'         => 'Empresa Original',
        'email'           => 'original_' . uniqid() . '@example.com',
        'password'        => Hash::make('secret123'),
        'is_active'       => 1,
    ]);

    $newEmail = 'updated_' . uniqid() . '@example.com';
    $payload = [
        'customer_id'           => $this->customer->customer_id,
        'nombre_contacto'       => 'Contacto Modificado',
        'empresa'               => 'Empresa Nueva',
        'email'                 => $newEmail,
        'password'              => 'nuevaPass123',
        'password_confirmation' => 'nuevaPass123',
        'is_active'             => '1',
    ];

    $response = $this->actingAs($this->user)->putJson(route('admin.portal-users.update', $portalUser->id), $payload);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => '¡Usuario actualizado con éxito!',
    ]);

    $portalUser->refresh();
    expect($portalUser->nombre_contacto)->toBe('Contacto Modificado');
    expect($portalUser->email)->toBe($newEmail);
    expect(Hash::check('nuevaPass123', $portalUser->password))->toBeTrue();
});

test('destroy deletes portal user', function () {
    $portalUser = PortalUser::create([
        'customer_id'     => $this->customer->customer_id,
        'nombre_contacto' => 'Para Eliminar',
        'empresa'         => 'Empresa Delete',
        'email'           => 'delete_' . uniqid() . '@example.com',
        'password'        => Hash::make('secret123'),
        'is_active'       => 1,
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('admin.portal-users.destroy', $portalUser->id));

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseMissing('portal_users', ['id' => $portalUser->id]);
});

test('getClientSales returns customer sales with document status', function () {
    $portalUser = PortalUser::create([
        'customer_id'     => $this->customer->customer_id,
        'nombre_contacto' => 'Cliente Ventas',
        'empresa'         => 'Empresa Con Ventas',
        'email'           => 'ventas_' . uniqid() . '@example.com',
        'password'        => Hash::make('secret123'),
        'is_active'       => 1,
    ]);

    $sale = ($this->createSale)();

    $response = $this->actingAs($this->user)->getJson("admin/portal-users/{$portalUser->id}/sales");

    $response->assertStatus(200);
    $response->assertJsonStructure(['sales']);

    $sales = collect($response->json('sales'));
    expect($sales->pluck('sale_id'))->toContain($sale->sale_id);
});

test('uploadDocs uploads files and deleteDoc removes them', function () {
    Storage::fake('public');

    $sale = ($this->createSale)();

    $pdf = UploadedFile::fake()->create('factura.pdf', 100, 'application/pdf');
    $xml = UploadedFile::fake()->create('factura.xml', 50, 'text/xml');
    $coa = UploadedFile::fake()->create('certificado.pdf', 100, 'application/pdf');

    $uploadResponse = $this->actingAs($this->user)->postJson("admin/sales/{$sale->sale_id}/upload-docs", [
        'pdf_file' => $pdf,
        'xml_file' => $xml,
        'coa_file' => $coa,
    ]);

    $uploadResponse->assertStatus(200);
    $uploadResponse->assertJson(['success' => true]);

    $this->assertDatabaseHas('portal_documents', [
        'sale_id'   => $sale->sale_id,
        'file_type' => 'pdf',
    ]);
    $this->assertDatabaseHas('portal_documents', [
        'sale_id'   => $sale->sale_id,
        'file_type' => 'xml',
    ]);
    $this->assertDatabaseHas('portal_documents', [
        'sale_id'   => $sale->sale_id,
        'file_type' => 'coa',
    ]);

    // Test deleteDoc
    $deleteResponse = $this->actingAs($this->user)->deleteJson("admin/sales/{$sale->sale_id}/delete-doc/pdf");
    $deleteResponse->assertStatus(200);
    $deleteResponse->assertJson(['success' => true]);

    $this->assertDatabaseMissing('portal_documents', [
        'sale_id'   => $sale->sale_id,
        'file_type' => 'pdf',
    ]);

    // Delete non-existent doc returns 404
    $notFoundResponse = $this->actingAs($this->user)->deleteJson("admin/sales/{$sale->sale_id}/delete-doc/pdf");
    $notFoundResponse->assertStatus(404);

    // Delete invalid type returns 400
    $invalidTypeResponse = $this->actingAs($this->user)->deleteJson("admin/sales/{$sale->sale_id}/delete-doc/invalid");
    $invalidTypeResponse->assertStatus(400);
});
