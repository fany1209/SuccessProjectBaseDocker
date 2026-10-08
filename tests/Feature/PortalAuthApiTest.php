<?php

use App\Models\Customer;
use App\Models\PortalUser;
use App\Models\Sale;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->sector = \App\Models\Sector::first() ?? \App\Models\Sector::create([
        'name' => 'Sector Auth ' . uniqid(),
        'code' => 'SA' . rand(10, 99),
    ]);

    $this->customer = Customer::create([
        'sector_id'     => $this->sector->sector_id,
        'name'          => 'Cliente Portal Auth ' . uniqid(),
        'customer_code' => 'CLI-A-' . rand(1000, 9999),
        'email'         => 'auth_' . uniqid() . '@example.com',
        'rfc'           => 'XAXX010101000',
    ]);

    $this->portalUser = PortalUser::create([
        'customer_id'     => $this->customer->customer_id,
        'nombre_contacto' => 'Cliente Auth',
        'empresa'         => 'Empresa Auth',
        'email'           => 'portal_auth_' . uniqid() . '@example.com',
        'password'        => Hash::make('password123'),
        'is_active'       => 1,
    ]);

    $this->user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

    $this->createSale = function (array $overrides = []) {
        static $counter = 9000;
        $counter++;
        return Sale::create(array_merge([
            'seller'          => 'Vendedor Test',
            'first_time'      => 1,
            'is_customer'     => 1,
            'purchase_order'  => 'PO-A-' . uniqid(),
            'invoice'         => 'INV-A-' . uniqid(),
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

test('showLoginForm renders login view for portal clients', function () {
    $response = $this->get(route('portal.login'));

    $response->assertStatus(200);
    $response->assertViewIs('portal.login');
});

test('login authenticates active client and redirects to dashboard', function () {
    $response = $this->post(route('portal.login'), [
        'email'    => $this->portalUser->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('/portal/dashboard');
    expect(Auth::guard('client')->check())->toBeTrue();
    expect(Auth::guard('client')->id())->toBe($this->portalUser->id);
});

test('login fails when credentials are invalid', function () {
    $response = $this->post(route('portal.login'), [
        'email'    => $this->portalUser->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors(['email']);
    expect(Auth::guard('client')->check())->toBeFalse();
});

test('login rejects inactive client user', function () {
    $inactiveUser = PortalUser::create([
        'customer_id'     => $this->customer->customer_id,
        'nombre_contacto' => 'Cliente Inactivo',
        'empresa'         => 'Empresa Inactiva',
        'email'           => 'inactive_' . uniqid() . '@example.com',
        'password'        => Hash::make('password123'),
        'is_active'       => 0,
    ]);

    $response = $this->post(route('portal.login'), [
        'email'    => $inactiveUser->email,
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
    expect(Auth::guard('client')->check())->toBeFalse();
});

test('logout unauthenticates client and redirects to login', function () {
    Auth::guard('client')->login($this->portalUser);
    expect(Auth::guard('client')->check())->toBeTrue();

    $response = $this->post(route('portal.logout'));

    $response->assertRedirect('/portal-clientes');
    expect(Auth::guard('client')->check())->toBeFalse();
});

test('dashboard displays customer sales for authenticated client', function () {
    $sale = ($this->createSale)();

    $response = $this->actingAs($this->portalUser, 'client')->get(route('portal.dashboard'));

    $response->assertStatus(200);
    $response->assertViewIs('portal.dashboard');
    $response->assertViewHas('ventas');
});

test('verDetalle displays sale detail when owned and returns 403 when not owned', function () {
    $sale = ($this->createSale)();

    DB::table('sale_detail')->insert([
        'sale_id'    => $sale->sale_id,
        'product_id' => 1,
        'quantity'   => 10,
        'cost'       => 100,
        'has_tax'    => 1,
    ]);

    $response = $this->actingAs($this->portalUser, 'client')->get(route('portal.ver-detalle', $sale->sale_id));

    $response->assertStatus(200);
    $response->assertViewIs('portal.detalle');
    $response->assertViewHas(['venta', 'articulos']);

    // Another customer sale should 403
    $otherCustomer = Customer::create([
        'sector_id'     => $this->sector->sector_id,
        'name'          => 'Otro Cliente ' . uniqid(),
        'customer_code' => 'CLI-O-' . rand(1000, 9999),
    ]);
    $otherSale = ($this->createSale)(['customer_id' => $otherCustomer->customer_id]);

    $forbiddenResponse = $this->actingAs($this->portalUser, 'client')->get(route('portal.ver-detalle', $otherSale->sale_id));
    $forbiddenResponse->assertStatus(403);
});
