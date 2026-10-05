<?php

use App\Models\Sector;
use App\Models\Supplier;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    $this->unauthorizedUser = User::factory()->create();

    Permission::findOrCreate('suppliers.show', 'web');
    Permission::findOrCreate('suppliers.update', 'web');
    Permission::findOrCreate('suppliers.delete', 'web');

    $this->user->givePermissionTo(['suppliers.show', 'suppliers.update', 'suppliers.delete']);

    $this->sector = Sector::first() ?? Sector::create([
        'name' => 'Pecuario',
        'code' => 'PEC',
    ]);

    $this->supplier = Supplier::create([
        'supplier_code' => 'SPP-TEST-' . rand(1000, 9999),
        'sector_id'     => $this->sector->sector_id,
        'name'          => 'Proveedor Test S.A.',
        'contact'       => 'Contacto Test',
        'phone'         => '1234567890',
        'email'         => 'proveedor@test.com',
        'rfc'           => 'TEST010101AAA',
        'state'         => 'Jalisco',
        'city'          => 'Guadalajara',
        'district'      => 'Centro',
        'address'       => 'Av. Principal 123',
    ]);
});

afterEach(function () {
    if (isset($this->supplier)) {
        Supplier::where('supplier_id', $this->supplier->supplier_id)->delete();
    }
    if (isset($this->unauthorizedUser)) {
        $this->unauthorizedUser->delete();
    }
});

test('unauthenticated user cannot access suppliers index', function () {
    $response = $this->get('/suppliers');

    $response->assertStatus(302);
});

test('user without permission cannot view suppliers', function () {
    $response = $this->actingAs($this->unauthorizedUser)->get('/suppliers');

    $response->assertStatus(403);
});

test('user with permission can view suppliers index blade', function () {
    $response = $this->actingAs($this->user)->get('/suppliers');

    $response->assertStatus(200);
    $response->assertViewIs('suppliers');
    $response->assertViewHasAll(['sectors', 'total_suppliers']);
});

test('getSuppliers returns json list with mapped permissions and filters', function () {
    $response = $this->actingAs($this->user)->getJson(route('suppliers.getSuppliers', [
        'search' => 'Proveedor Test S.A.',
        'sector' => $this->sector->sector_id,
    ]));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'suppliers' => [
            '*' => [
                'supplier_id',
                'sector',
                'code',
                'name',
                'contact',
                'phone',
                'email',
                'rfc',
                'address',
                'canUpdate',
                'canDelete',
            ],
        ],
    ]);

    $data = $response->json('suppliers');
    expect(count($data))->toBeGreaterThanOrEqual(1);
    expect($data[0]['canUpdate'])->toBeTrue();
    expect($data[0]['canDelete'])->toBeTrue();
});

test('show returns single supplier data', function () {
    $response = $this->actingAs($this->user)->getJson("/suppliers/{$this->supplier->supplier_id}");

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'supplier' => [
            'supplier_id',
            'supplier_code',
            'sector_id',
            'name',
            'contact',
            'phone',
            'email',
            'rfc',
        ],
    ]);
    expect($response->json('supplier.name'))->toBe('Proveedor Test S.A.');
});

test('show returns 404 for non-existent supplier', function () {
    $response = $this->actingAs($this->user)->getJson('/suppliers/999999');

    $response->assertStatus(404);
});

test('store creates a new supplier with auto-generated code according to sector', function () {
    $payload = [
        'name'      => '<b>Nuevo Proveedor Seguro</b>',
        'sector_id' => $this->sector->sector_id,
        'contact'   => 'Juan Pérez',
        'phone'     => '3312345678',
        'email'     => 'nuevo@proveedor.com',
        'rfc'       => 'NP' . rand(10000000, 99999999) . 'X',
        'state'     => 'CDMX',
        'city'      => 'Ciudad de México',
        'district'  => 'Polanco',
        'address'   => 'Calle Reforma 100',
    ];

    $response = $this->actingAs($this->user)->postJson('/suppliers', $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('message', 'Operation successfully make it');

    $created = Supplier::where('name', 'Nuevo Proveedor Seguro')->first();
    expect($created)->not->toBeNull();
    expect($created->supplier_code)->toStartWith('SPP');

    if ($created) {
        $created->delete();
    }
});

test('store fails validation when required fields are missing', function () {
    $response = $this->actingAs($this->user)->postJson('/suppliers', [
        'phone' => '123456',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});

test('update modifies supplier details successfully', function () {
    $payload = [
        'supplier_id' => $this->supplier->supplier_id,
        'name'        => 'Proveedor Actualizado S.A.',
        'phone'       => '9998887766',
        'email'       => 'actualizado@proveedor.com',
    ];

    $response = $this->actingAs($this->user)->putJson("/suppliers/{$this->supplier->supplier_id}", $payload);

    $response->assertStatus(201);
    $this->supplier->refresh();
    expect($this->supplier->name)->toBe('Proveedor Actualizado S.A.');
    expect($this->supplier->phone)->toBe('9998887766');
});

test('destroy removes supplier and returns success json', function () {
    $tempSupplier = Supplier::create([
        'supplier_code' => 'SP-TEMP-' . rand(1000, 9999),
        'name'          => 'Proveedor Para Borrar',
        'sector_id'     => $this->sector->sector_id,
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/suppliers/{$tempSupplier->supplier_id}");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $this->assertDatabaseMissing('suppliers', ['supplier_id' => $tempSupplier->supplier_id]);
});

test('destroy returns 404 for non-existent supplier', function () {
    $response = $this->actingAs($this->user)->deleteJson('/suppliers/999999');

    $response->assertStatus(404);
    $response->assertJsonPath('success', false);
});
