<?php

use App\Models\Prospect;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    Permission::findOrCreate('prospects.show', 'web');
    Permission::findOrCreate('prospects.create', 'web');
    Permission::findOrCreate('prospects.update', 'web');
    Permission::findOrCreate('prospects.delete', 'web');

    $this->user->givePermissionTo([
        'prospects.show',
        'prospects.create',
        'prospects.update',
        'prospects.delete',
    ]);

    $this->sector = Sector::first() ?? Sector::create([
        'name' => 'Industrial',
        'code' => 'IND',
    ]);

    $this->prospect = Prospect::create([
        'sector_id' => $this->sector->sector_id,
        'name'      => 'Prospecto Test ' . uniqid(),
        'phone'     => '3312345678',
        'email'     => 'prospecto@test.com',
        'rfc'       => 'PR' . rand(10000000, 99999999) . 'X',
        'state'     => 'Jalisco',
        'city'      => 'Guadalajara',
        'district'  => 'Americana',
        'address'   => 'Av. Vallarta 1234',
    ]);
});

afterEach(function () {
    if (isset($this->prospect)) {
        Prospect::where('prospect_id', $this->prospect->prospect_id)->delete();
    }
});

it('renders the prospect index view with sectors', function () {
    $this->actingAs($this->user);

    $response = $this->get('/prospects');

    $response->assertStatus(200)
        ->assertViewIs('prospect')
        ->assertViewHas('sectors');
});

it('returns json on index when requested via ajax', function () {
    $this->actingAs($this->user);

    $response = $this->getJson('/prospects');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ])
        ->assertJsonStructure([
            'data' => [
                'sectors',
            ],
        ]);
});

it('can list prospects with permissions and concatenated address', function () {
    $this->actingAs($this->user);

    $response = $this->getJson(route('prospects.getProspects'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'prospects' => [
                '*' => [
                    'prospect_id',
                    'sector',
                    'name',
                    'phone',
                    'email',
                    'rfc',
                    'address',
                    'canUpdate',
                    'canDelete',
                ],
            ],
        ]);

    $prospects = $response->json('prospects');
    $found = collect($prospects)->firstWhere('prospect_id', $this->prospect->prospect_id);
    expect($found)->not->toBeNull()
        ->and($found['name'])->toBe($this->prospect->name)
        ->and($found['canUpdate'])->toBeTrue()
        ->and($found['canDelete'])->toBeTrue();
});

it('can filter prospects by search, sector, city, state, name and rfc', function () {
    $this->actingAs($this->user);

    $responseSearch = $this->getJson(route('prospects.getProspects', [
        'search' => $this->prospect->name,
    ]));
    $responseSearch->assertStatus(200);
    expect(count($responseSearch->json('prospects')))->toBeGreaterThanOrEqual(1);

    $responseFilters = $this->getJson(route('prospects.getProspects', [
        'sector' => $this->sector->sector_id,
        'city'   => 'Guadalajara',
        'state'  => 'Jalisco',
        'name'   => $this->prospect->name,
        'rfc'    => $this->prospect->rfc,
    ]));
    $responseFilters->assertStatus(200);
    expect(count($responseFilters->json('prospects')))->toBeGreaterThanOrEqual(1);
});

it('can show prospect details', function () {
    $this->actingAs($this->user);

    $response = $this->getJson("/prospects/{$this->prospect->prospect_id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'prospect' => [
                'prospect_id',
                'sector_id',
                'name',
                'phone',
                'email',
                'rfc',
                'state',
                'city',
                'district',
                'address',
            ],
            'data' => [
                'prospect_id',
                'sector_id',
                'sector',
                'name',
                'full_address',
            ],
        ]);

    expect($response->json('prospect.prospect_id'))->toBe($this->prospect->prospect_id);
});

it('returns 404 when showing non existent prospect', function () {
    $this->actingAs($this->user);

    $response = $this->getJson('/prospects/999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
        ]);
});

it('can store a new prospect with sanitized inputs', function () {
    $this->actingAs($this->user);

    $payload = [
        'sector_id' => $this->sector->sector_id,
        'name'      => '<b>Prospecto Nuevo</b> <script>alert("xss")</script>',
        'phone'     => ' 3399887766 ',
        'email'     => 'nuevo@prospecto.com',
        'rfc'       => 'PNEW123456789',
        'state'     => 'Colima',
        'city'      => 'Manzanillo',
        'district'  => 'Centro',
        'address'   => 'Calle Mar 456',
    ];

    $response = $this->postJson('/prospects', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Operation successfully made',
        ]);

    $newId = $response->json('data.prospect_id');
    expect($newId)->not->toBeNull();

    $this->assertDatabaseHas('prospects', [
        'prospect_id' => $newId,
        'name'        => 'Prospecto Nuevo alert("xss")',
        'phone'       => '3399887766',
        'city'        => 'Manzanillo',
    ]);

    Prospect::where('prospect_id', $newId)->delete();
});

it('fails to store prospect when missing required fields', function () {
    $this->actingAs($this->user);

    $response = $this->postJson('/prospects', [
        'name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['sector_id', 'name']);
});

it('can update an existing prospect', function () {
    $this->actingAs($this->user);

    $payload = [
        'prospect_id' => $this->prospect->prospect_id,
        'sector_id'   => $this->sector->sector_id,
        'name'        => 'Prospecto Actualizado',
        'phone'       => '3311223344',
        'email'       => 'updated@prospecto.com',
        'rfc'         => 'PUPD123456789',
        'state'       => 'Jalisco',
        'city'        => 'Zapopan',
        'district'    => 'Puerta de Hierro',
        'address'     => 'Av. Empresarios 100',
    ];

    $response = $this->putJson("/prospects/{$this->prospect->prospect_id}", $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Operation successfully made',
        ]);

    $this->assertDatabaseHas('prospects', [
        'prospect_id' => $this->prospect->prospect_id,
        'name'        => 'Prospecto Actualizado',
        'city'        => 'Zapopan',
    ]);
});

it('returns 404 when updating non existent prospect', function () {
    $this->actingAs($this->user);

    $payload = [
        'sector_id' => $this->sector->sector_id,
        'name'      => 'Inexistente',
    ];

    $response = $this->putJson('/prospects/999999', $payload);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
        ]);
});

it('can delete a prospect', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson("/prospects/{$this->prospect->prospect_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Prospect deleted',
        ]);

    $this->assertDatabaseMissing('prospects', [
        'prospect_id' => $this->prospect->prospect_id,
    ]);
});

it('returns 404 when deleting non existent prospect', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson('/prospects/999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Prospect not deleted',
        ]);
});

it('prevents deleting prospect with associated sales', function () {
    $this->actingAs($this->user);

    $statusId = DB::table('sales_status')->value('sales_status_id') ?? 1;

    DB::table('sales')->insert([
        'prospect_id'     => $this->prospect->prospect_id,
        'folio'           => rand(10000, 99999),
        'first_time'      => 0,
        'is_customer'     => 0,
        'sale_type'       => 'cash',
        'date'            => now()->toDateString(),
        'user_id'         => $this->user->id,
        'sales_status_id' => $statusId,
        'sector_id'       => $this->sector->sector_id,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $response = $this->deleteJson("/prospects/{$this->prospect->prospect_id}");

    $response->assertStatus(409)
        ->assertJson([
            'success' => false,
            'flag'    => false,
        ]);

    DB::table('sales')->where('prospect_id', $this->prospect->prospect_id)->delete();
});

it('denies access to unauthorized user', function () {
    $guest = User::factory()->create();
    $this->actingAs($guest);

    $response = $this->get('/prospects');
    $response->assertStatus(403);

    $responseJson = $this->getJson(route('prospects.getProspects'));
    $responseJson->assertStatus(403);

    $guest->delete();
});
