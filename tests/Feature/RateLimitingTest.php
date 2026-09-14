<?php

use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('portal-login');
    RateLimiter::clear('contact-form');
    RateLimiter::clear('pdf-reports');
});

test('portal login is throttled after 5 failed attempts', function () {
    $email = 'attacker@example.com';

    for ($i = 0; $i < 5; $i++) {
        $response = $this->post('/portal-clientes', [
            'email' => $email,
            'password' => 'wrong-password',
        ]);
        // The first 5 requests should not be 429 (they will be validation or invalid credentials redirect)
        expect($response->status())->not->toBe(429);
    }

    // 6th attempt must be throttled with HTTP 429
    $response = $this->post('/portal-clientes', [
        'email' => $email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(429);
    $response->assertSessionHasErrors('email');
});

test('contact form is throttled after 5 requests', function () {
    for ($i = 0; $i < 5; $i++) {
        $response = $this->post('/contact', [
            'name' => 'Spam Bot',
            'email' => 'spambot@example.com',
            'phone' => '1234567890',
            'message' => 'Spam content message',
        ]);
        expect($response->status())->not->toBe(429);
    }

    // 6th attempt must be throttled with HTTP 429
    $response = $this->post('/contact', [
        'name' => 'Spam Bot',
        'email' => 'spambot@example.com',
        'phone' => '1234567890',
        'message' => 'Spam content message',
    ]);

    $response->assertStatus(429);
});

test('pdf reports limiter throttles excessive requests and renders 429 view', function () {
    \Illuminate\Support\Facades\Route::get('/test-pdf-limit', function () {
        return 'ok';
    })->middleware(['auth', 'throttle:pdf-reports']);

    $user = new \App\Models\User();
    $user->id = 999;

    for ($i = 0; $i < 30; $i++) {
        $response = $this->actingAs($user)->get('/test-pdf-limit');
        expect($response->status())->toBe(200);
    }

    // 31st request must be throttled with HTTP 429
    $response = $this->actingAs($user)->get('/test-pdf-limit');
    $response->assertStatus(429);
    $response->assertSee('Demasiadas Solicitudes');
});

