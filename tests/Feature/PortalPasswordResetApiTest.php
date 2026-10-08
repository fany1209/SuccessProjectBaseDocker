<?php

use App\Models\Customer;
use App\Models\PortalUser;
use App\Notifications\PortalPasswordResetNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->customer = Customer::create([
        'name'          => 'Cliente Reset Test ' . uniqid(),
        'customer_code' => 'CLI-R-' . rand(1000, 9999),
        'email'         => 'reset_' . uniqid() . '@example.com',
        'rfc'           => 'XAXX010101000',
    ]);

    $this->portalUser = PortalUser::create([
        'customer_id'     => $this->customer->customer_id,
        'nombre_contacto' => 'Cliente Reset',
        'empresa'         => 'Empresa Reset',
        'email'           => 'portal_reset_' . uniqid() . '@example.com',
        'password'        => Hash::make('oldpassword123'),
        'is_active'       => 1,
    ]);
});

test('showForgotForm renders forgot password view', function () {
    $response = $this->get(route('portal.password.request'));

    $response->assertStatus(200);
    $response->assertViewIs('portal.forgot-password');
});

test('sendResetLink sends notification and stores hashed token in database', function () {
    Notification::fake();

    $response = $this->post(route('portal.password.email'), [
        'email' => $this->portalUser->email,
    ]);

    $response->assertSessionHas('status');

    Notification::assertSentTo($this->portalUser, PortalPasswordResetNotification::class);

    $this->assertDatabaseHas('portal_password_resets', [
        'email' => $this->portalUser->email,
    ]);
});

test('sendResetLink returns generic message even if email does not exist', function () {
    Notification::fake();

    $response = $this->post(route('portal.password.email'), [
        'email' => 'nonexistent_' . uniqid() . '@example.com',
    ]);

    $response->assertSessionHas('status');
    Notification::assertNothingSent();
});

test('showResetForm displays reset password form with valid token', function () {
    $plainToken = Str::random(64);
    $hashedToken = hash('sha256', $plainToken);

    DB::table('portal_password_resets')->insert([
        'email'      => $this->portalUser->email,
        'token'      => $hashedToken,
        'created_at' => now(),
    ]);

    $response = $this->get(route('portal.password.reset', [
        'token' => $plainToken,
        'email' => $this->portalUser->email,
    ]));

    $response->assertStatus(200);
    $response->assertViewIs('portal.reset-password');
    $response->assertViewHas(['token', 'email']);
});

test('showResetForm redirects with error when token is invalid or expired', function () {
    $response = $this->get(route('portal.password.reset', [
        'token' => 'invalid-token',
        'email' => $this->portalUser->email,
    ]));

    $response->assertRedirect(route('portal.password.request'));
    $response->assertSessionHasErrors(['email']);
});

test('resetPassword updates user password and removes reset token', function () {
    $plainToken = Str::random(64);
    $hashedToken = hash('sha256', $plainToken);

    DB::table('portal_password_resets')->insert([
        'email'      => $this->portalUser->email,
        'token'      => $hashedToken,
        'created_at' => now(),
    ]);

    $response = $this->post(route('portal.password.update'), [
        'token'                 => $plainToken,
        'email'                 => $this->portalUser->email,
        'password'              => 'newsecurepassword123',
        'password_confirmation' => 'newsecurepassword123',
    ]);

    $response->assertRedirect(route('portal.login'));
    $response->assertSessionHas('status');

    $this->portalUser->refresh();
    expect(Hash::check('newsecurepassword123', $this->portalUser->password))->toBeTrue();

    $this->assertDatabaseMissing('portal_password_resets', [
        'email' => $this->portalUser->email,
    ]);
});

test('changePassword updates password for authenticated client', function () {
    $response = $this->actingAs($this->portalUser, 'client')->postJson(route('portal.password.change'), [
        'current_password'          => 'oldpassword123',
        'new_password'              => 'brandnewpassword123',
        'new_password_confirmation' => 'brandnewpassword123',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => '¡Contraseña actualizada correctamente!',
    ]);

    $this->portalUser->refresh();
    expect(Hash::check('brandnewpassword123', $this->portalUser->password))->toBeTrue();
});

test('changePassword rejects incorrect current password', function () {
    $response = $this->actingAs($this->portalUser, 'client')->postJson(route('portal.password.change'), [
        'current_password'          => 'wrongcurrentpassword',
        'new_password'              => 'brandnewpassword123',
        'new_password_confirmation' => 'brandnewpassword123',
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'La contraseña actual no es correcta.',
    ]);
});
