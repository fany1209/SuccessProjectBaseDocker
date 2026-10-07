<?php

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['name' => 'Admin']);

    $this->adminUser = User::factory()->create([
        'email' => 'admin_task_' . uniqid() . '@example.com',
    ]);
    $this->adminUser->assignRole($adminRole);

    $this->normalUser = User::factory()->create([
        'email' => 'user_task_' . uniqid() . '@example.com',
    ]);

    $this->otherUser = User::factory()->create([
        'email' => 'other_task_' . uniqid() . '@example.com',
    ]);
});

test('index renders tasks view with status counts for admin and normal user', function () {
    Task::create([
        'title' => 'Tarea Asignada a Normal',
        'admin_id' => $this->adminUser->id,
        'user_id' => $this->normalUser->id,
        'priority' => 'high',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)->get(route('tasks.index'));
    $response->assertStatus(200);
    $response->assertViewIs('tasks.index');
    $response->assertViewHas(['tasks', 'users', 'statusCounts', 'isAdmin']);

    $responseNormal = $this->actingAs($this->normalUser)->get(route('tasks.index'));
    $responseNormal->assertStatus(200);
    $responseNormal->assertViewIs('tasks.index');
});

test('index returns json tasks list when requested with json header', function () {
    Task::create([
        'title' => 'Tarea API',
        'admin_id' => $this->adminUser->id,
        'user_id' => $this->normalUser->id,
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->normalUser)->getJson(route('tasks.index'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'tasks' => [
            '*' => [
                'id',
                'title',
                'status',
                'priority',
            ],
        ],
        'statusCounts',
    ]);
});



test('store fails validation when required fields are missing', function () {
    $response = $this->actingAs($this->adminUser)->postJson(route('tasks.store'), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title', 'user_id', 'priority']);
});

test('store creates task and sends notification to assignee', function () {
    Notification::fake();

    $payload = [
        'title' => 'Revisión mensual de caldera',
        'description' => 'Verificar presiones y niveles de agua.',
        'user_id' => $this->normalUser->id,
        'priority' => 'urgent',
        'due_date' => '2026-10-20',
    ];

    $response = $this->actingAs($this->adminUser)->postJson(route('tasks.store'), $payload);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Revisión mensual de caldera',
        'user_id' => $this->normalUser->id,
        'priority' => 'urgent',
        'status' => 'pending',
    ]);

    Notification::assertSentTo($this->normalUser, TaskNotification::class);
});

test('updateStatus modifies task status and timestamps completed_at', function () {
    $task = Task::create([
        'title' => 'Tarea En Curso',
        'admin_id' => $this->adminUser->id,
        'user_id' => $this->normalUser->id,
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->normalUser)->patchJson(
        route('tasks.updateStatus', $task->id),
        ['status' => 'completed']
    );

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'completed',
    ]);

    $task->refresh();
    expect($task->completed_at)->not->toBeNull();
});

test('edit returns task resource json', function () {
    $task = Task::create([
        'title' => 'Tarea Para Editar',
        'admin_id' => $this->adminUser->id,
        'user_id' => $this->normalUser->id,
        'priority' => 'low',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)->getJson(route('tasks.edit', $task->id));

    $response->assertStatus(200);
    $response->assertJson([
        'id' => $task->id,
        'title' => 'Tarea Para Editar',
    ]);
});

test('update modifies task information', function () {
    $task = Task::create([
        'title' => 'Tarea Inicial',
        'admin_id' => $this->adminUser->id,
        'user_id' => $this->normalUser->id,
        'priority' => 'low',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)->putJson(route('tasks.update', $task->id), [
        'title' => 'Tarea Modificada',
        'description' => 'Nueva descripción',
        'user_id' => $this->otherUser->id,
        'priority' => 'high',
        'due_date' => '2026-10-30',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Tarea Modificada',
        'user_id' => $this->otherUser->id,
        'priority' => 'high',
    ]);
});

test('destroy fails with 403 when user is not admin', function () {
    $task = Task::create([
        'title' => 'Tarea No Autorizada Para Borrar',
        'admin_id' => $this->adminUser->id,
        'user_id' => $this->normalUser->id,
        'priority' => 'low',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->normalUser)->deleteJson(route('tasks.destroy', $task->id));

    $response->assertStatus(403);
    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});

test('destroy removes task when user is admin', function () {
    $task = Task::create([
        'title' => 'Tarea Para Borrar Admin',
        'admin_id' => $this->adminUser->id,
        'user_id' => $this->normalUser->id,
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->adminUser)->deleteJson(route('tasks.destroy', $task->id));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
