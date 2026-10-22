<?php

use App\Models\User;  
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Sector;
use App\Notifications\SaleAlmacenNotification;
use App\Notifications\TaskNotification;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'user_' . uniqid() . '@example.com',
    ]);

    $this->otherUser = User::factory()->create([
        'email' => 'other_' . uniqid() . '@example.com',
    ]);
});

test('markOneRead marks a specific unread notification as read for authenticated user', function () {
    $task = Task::create([
        'title' => 'Test Task ' . uniqid(),
        'description' => 'Test Description',
        'user_id' => $this->user->id,
        'admin_id' => $this->user->id,
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->user->notify(new TaskNotification($task));

    $notification = $this->user->unreadNotifications()->first();
    expect($notification)->not->toBeNull();

    $response = $this->actingAs($this->user)
        ->postJson(route('notifications.markOneRead', $notification->id));

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->user->refresh();
    expect($this->user->unreadNotifications()->where('id', $notification->id)->count())->toBe(0);
    expect($this->user->notifications()->where('id', $notification->id)->first()->read_at)->not->toBeNull();
});

test('markOneRead returns 404 when notification does not exist or belongs to another user', function () {
    $task = Task::create([
        'title' => 'Other Task ' . uniqid(),
        'description' => 'Test Description',
        'user_id' => $this->otherUser->id,
        'admin_id' => $this->otherUser->id,
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->otherUser->notify(new TaskNotification($task));
    $otherNotification = $this->otherUser->unreadNotifications()->first();

    // User tries to mark otherUser's notification
    $response = $this->actingAs($this->user)
        ->postJson(route('notifications.markOneRead', $otherNotification->id));

    $response->assertStatus(404)
        ->assertJson(['success' => false]);

    // Random non-existent UUID
    $fakeUuid = (string) \Illuminate\Support\Str::uuid();
    $responseFake = $this->actingAs($this->user)
        ->postJson(route('notifications.markOneRead', $fakeUuid));

    $responseFake->assertStatus(404)
        ->assertJson(['success' => false]);
});

test('markOneRead returns 401 when not authenticated', function () {
    $response = $this->postJson(route('notifications.markOneRead', 'some-id'));
    $response->assertStatus(401);
});

test('markAllRead marks all notifications as read for authenticated user', function () {
    $task1 = Task::create([
        'title' => 'Task 1 ' . uniqid(),
        'user_id' => $this->user->id,
        'admin_id' => $this->user->id,
        'priority' => 'low',
        'status' => 'pending',
    ]);
    $task2 = Task::create([
        'title' => 'Task 2 ' . uniqid(),
        'user_id' => $this->user->id,
        'admin_id' => $this->user->id,
        'priority' => 'high',
        'status' => 'pending',
    ]);

    $this->user->notify(new TaskNotification($task1));
    $this->user->notify(new TaskNotification($task2));

    expect($this->user->unreadNotifications()->count())->toBeGreaterThanOrEqual(2);

    $response = $this->actingAs($this->user)
        ->postJson(route('notifications.markRead'));

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->user->refresh();
    expect($this->user->unreadNotifications()->count())->toBe(0);
});

test('almacen_notifications mark_read marks notification as read via SalesController', function () {
    $sector = Sector::first() ?? Sector::create(['name' => 'Sec ' . uniqid(), 'code' => 'S' . rand(10, 99)]);
    $customer = Customer::create([
        'sector_id' => $sector->sector_id,
        'customer_code' => 'CUST' . rand(1000, 9999),
        'name' => 'Test Customer',
        'email' => 'cust_' . uniqid() . '@example.com',
    ]);

    $statusRow = DB::table('sales_status')->first();
    $statusId = $statusRow ? $statusRow->sales_status_id : 1;

    $sale = Sale::create([
        'customer_id' => $customer->customer_id,
        'sector_id' => $sector->sector_id,
        'seller' => 'Vendedor',
        'first_time' => 1,
        'is_customer' => 1,
        'sale_type' => 'Credit',
        'sales_status_id' => $statusId,
        'folio' => rand(100000, 999999),
        'date' => now()->toDateString(),
        'almacen_status' => 'pending',
        'status' => 'active',
        'user_id' => $this->user->id,
    ]);

    $this->user->notify(new SaleAlmacenNotification($sale, 'new_sale', 'Nueva venta para preparar'));
    $notification = $this->user->unreadNotifications()->first();

    $response = $this->actingAs($this->user)
        ->postJson(route('almacen_notifications.mark_read', $notification->id));

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->user->refresh();
    expect($this->user->unreadNotifications()->where('id', $notification->id)->count())->toBe(0);
});
