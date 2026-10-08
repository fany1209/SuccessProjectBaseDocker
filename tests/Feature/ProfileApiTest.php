<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name'     => 'Usuario Perfil Test',
        'email'    => 'profile_' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
    ]);
});

test('showProfile renders profile show view for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('profile.show'));

    $response->assertStatus(200);
    $response->assertViewIs('profile.show');
    $response->assertViewHas(['user', 'sessions']);
});

test('updateProfileInfo updates user name and email', function () {
    $newEmail = 'updated_profile_' . uniqid() . '@example.com';

    $response = $this->actingAs($this->user)->postJson(route('profile.update-info'), [
        'name'  => 'Nombre Modificado',
        'email' => $newEmail,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Profile updated.',
    ]);

    $this->user->refresh();
    expect($this->user->name)->toBe('Nombre Modificado');
    expect($this->user->email)->toBe($newEmail);
});

test('updateProfileInfo fails validation when email is taken by another user', function () {
    $otherUser = User::factory()->create([
        'email' => 'other_' . uniqid() . '@example.com',
    ]);

    $response = $this->actingAs($this->user)->postJson(route('profile.update-info'), [
        'name'  => 'Test User',
        'email' => $otherUser->email,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

test('updatePassword updates password when current password is correct', function () {
    $response = $this->actingAs($this->user)->postJson(route('profile.update-password'), [
        'current_password'      => 'password123',
        'password'              => 'newpassword1234',
        'password_confirmation' => 'newpassword1234',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Password updated.',
    ]);

    $this->user->refresh();
    expect(Hash::check('newpassword1234', $this->user->password))->toBeTrue();
});

test('updatePassword fails when current password is incorrect', function () {
    $response = $this->actingAs($this->user)->postJson(route('profile.update-password'), [
        'current_password'      => 'wrongcurrentpass',
        'password'              => 'newpassword1234',
        'password_confirmation' => 'newpassword1234',
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'Current password is incorrect.',
    ]);
});

test('logoutOtherSessions clears other sessions from database', function () {
    DB::table('sessions')->insert([
        [
            'id'            => 'session_other_' . uniqid(),
            'user_id'       => $this->user->id,
            'ip_address'    => '127.0.0.1',
            'user_agent'    => 'Mozilla/5.0 Chrome',
            'payload'       => 'test',
            'last_activity' => time(),
        ],
        [
            'id'            => 'session_other_2_' . uniqid(),
            'user_id'       => $this->user->id,
            'ip_address'    => '192.168.1.1',
            'user_agent'    => 'Mozilla/5.0 Safari',
            'payload'       => 'test',
            'last_activity' => time(),
        ],
    ]);

    $response = $this->actingAs($this->user)->postJson(route('other-browser-sessions.destroy'), [
        'password' => 'password123',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Logged out from other sessions.']);

    $remainingSessions = DB::table('sessions')->where('user_id', $this->user->id)->count();
    expect($remainingSessions)->toBeLessThanOrEqual(1);
});

test('removePhoto removes user profile photo', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('profile.jpg');
    $path = $file->store('profile-photos', 'public');

    $this->user->profile_photo_path = $path;
    $this->user->save();

    $response = $this->actingAs($this->user)->postJson(route('profile-photo.destroy'));

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Profile photo removed.']);

    $this->user->refresh();
    expect($this->user->profile_photo_path)->toBeNull();
});

test('deleteUser logs out and removes user account', function () {
    $userId = $this->user->id;

    $response = $this->actingAs($this->user)->post(route('profile.delete'));

    $response->assertRedirect('/');
    $this->assertDatabaseMissing('users', ['id' => $userId]);
});
