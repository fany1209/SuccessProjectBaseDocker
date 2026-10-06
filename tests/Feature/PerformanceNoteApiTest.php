<?php

use App\Models\PerformanceNote;
use App\Models\User;
use App\Notifications\PerformanceNoteNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['name' => 'Admin']);

    $this->adminUser = User::factory()->create([
        'email' => 'admin_' . uniqid() . '@example.com',
    ]);
    $this->adminUser->assignRole($adminRole);

    $this->normalUser = User::factory()->create([
        'email' => 'normal_' . uniqid() . '@example.com',
    ]);
});

test('index renders performance notes view for web request', function () {
    $response = $this->actingAs($this->adminUser)->get(route('performance_notes.index'));

    $response->assertStatus(200);
    $response->assertViewIs('performance_notes.index');
    $response->assertViewHas(['notes', 'users', 'isAdmin']);
});

test('index returns json when requested with json header', function () {
    PerformanceNote::create([
        'user_id' => $this->normalUser->id,
        'admin_id' => $this->adminUser->id,
        'type' => 'positive',
        'comments' => 'Excelente entrega de proyectos a tiempo.',
    ]);

    $response = $this->actingAs($this->adminUser)->getJson(route('performance_notes.index'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'user_id',
                'admin_id',
                'type',
                'comments',
                'created_at',
                'updated_at',
            ],
        ],
    ]);
});

test('store fails with 403 when user is not admin', function () {
    $response = $this->actingAs($this->normalUser)->postJson(route('performance_notes.store'), [
        'user_id' => $this->normalUser->id,
        'type' => 'positive',
        'comments' => 'Comentario no autorizado.',
    ]);

    $response->assertStatus(403);
});

test('store fails validation when required fields are missing', function () {
    $response = $this->actingAs($this->adminUser)->postJson(route('performance_notes.store'), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['user_id', 'type', 'comments']);
});

test('store fails validation when invalid type is provided', function () {
    $response = $this->actingAs($this->adminUser)->postJson(route('performance_notes.store'), [
        'user_id' => $this->normalUser->id,
        'type' => 'invalido',
        'comments' => 'Comentario de prueba',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['type']);
});

test('store creates performance note and notifies target user', function () {
    Notification::fake();

    $response = $this->actingAs($this->adminUser)->postJson(route('performance_notes.store'), [
        'user_id' => $this->normalUser->id,
        'type' => 'positive',
        'comments' => 'Buen liderazgo en la reunión de equipo.',
    ]);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Nota agregada correctamente.',
    ]);

    $this->assertDatabaseHas('performance_notes', [
        'user_id' => $this->normalUser->id,
        'admin_id' => $this->adminUser->id,
        'type' => 'positive',
        'comments' => 'Buen liderazgo en la reunión de equipo.',
    ]);

    Notification::assertSentTo($this->normalUser, PerformanceNoteNotification::class);
});

test('destroy fails with 403 when user is not admin', function () {
    $note = PerformanceNote::create([
        'user_id' => $this->normalUser->id,
        'admin_id' => $this->adminUser->id,
        'type' => 'neutral',
        'comments' => 'Comentario a eliminar',
    ]);

    $response = $this->actingAs($this->normalUser)->deleteJson(route('performance_notes.destroy', $note->id));

    $response->assertStatus(403);
});

test('destroy deletes note successfully when user is admin', function () {
    $note = PerformanceNote::create([
        'user_id' => $this->normalUser->id,
        'admin_id' => $this->adminUser->id,
        'type' => 'improvement',
        'comments' => 'Puntualidad en entregas semanales',
    ]);

    $response = $this->actingAs($this->adminUser)->deleteJson(route('performance_notes.destroy', $note->id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Nota eliminada correctamente.',
    ]);

    $this->assertDatabaseMissing('performance_notes', [
        'id' => $note->id,
    ]);
});

test('unreadNotifications returns list of unread notifications', function () {
    $note = PerformanceNote::create([
        'user_id' => $this->normalUser->id,
        'admin_id' => $this->adminUser->id,
        'type' => 'positive',
        'comments' => 'Felicitaciones por el esfuerzo adicional.',
    ]);

    $this->normalUser->notify(new PerformanceNoteNotification($note));

    $response = $this->actingAs($this->normalUser)->getJson(route('performance_notes.notifications.unread'));

    $response->assertStatus(200);
    $response->assertJsonStructure(['notifications']);
    expect(count($response->json('notifications')))->toBeGreaterThanOrEqual(1);
});

test('markNotificationAsRead marks notification as read', function () {
    $note = PerformanceNote::create([
        'user_id' => $this->normalUser->id,
        'admin_id' => $this->adminUser->id,
        'type' => 'positive',
        'comments' => 'Excelente seguimiento a incidencias.',
    ]);

    $this->normalUser->notify(new PerformanceNoteNotification($note));
    $notification = $this->normalUser->unreadNotifications->first();

    $response = $this->actingAs($this->normalUser)->postJson(route('performance_notes.notifications.mark_read', $notification->id));

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->normalUser->refresh();
    expect($this->normalUser->unreadNotifications->where('id', $notification->id)->count())->toBe(0);
});
