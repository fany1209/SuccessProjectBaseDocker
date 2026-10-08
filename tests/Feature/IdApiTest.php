<?php

use App\Models\User;
use App\Http\Repositories\Id\IdRepository;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('returns 204 no content for standard web request on i+d endpoint', function () {
    $response = $this->actingAs($this->user)->get(route('i+d.index'));

    $response->assertNoContent();
});

it('returns json status response when json requested on i+d endpoint', function () {
    $response = $this->actingAs($this->user)->getJson(route('i+d.index'));

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'flag'    => true,
        'message' => 'Módulo de Investigación y Desarrollo (I+D).',
        'data'    => [
            'name'   => 'Investigación y Desarrollo (I+D)',
            'status' => 'under_construction',
        ],
    ]);
});

it('handles exception and returns error response', function () {
    $mockRepo = Mockery::mock(IdRepository::class);
    $mockRepo->shouldReceive('getModuleStatus')->andThrow(new Exception('Error interno simulado'));
    $this->app->instance(IdRepository::class, $mockRepo);

    $response = $this->actingAs($this->user)->getJson(route('i+d.index'));

    $response->assertStatus(500);
    $response->assertJson([
        'success' => false,
        'message' => 'Error al consultar el módulo I+D.',
    ]);
});
