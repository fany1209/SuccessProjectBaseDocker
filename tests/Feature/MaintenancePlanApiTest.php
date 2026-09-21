<?php

use App\Models\Area;
use App\Models\Equipment;
use App\Models\MaintenancePlan;
use App\Models\MaintenanceRecord;
use App\Models\User;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    $this->area = Area::first() ?? Area::create([
        'name' => 'Área Base ' . uniqid(),
        'description' => 'Área para planes',
    ]);
    $this->equipment = Equipment::first() ?? Equipment::create([
        'code'      => 'EQ-PLAN-BASE',
        'name'      => 'Torno Paralelo',
        'area_id'   => $this->area->id,
        'is_active' => true,
    ]);
});

test('1. API GET /maintenance/plans (Listado general)', function () {
    $response = $this->actingAs($this->user)->getJson('/maintenance/plans');

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
});

test('2. API POST /maintenance/plans (Creación exitosa con checklist)', function () {
    $payload = [
        'equipment_id'    => $this->equipment->id,
        'name'            => 'Plan Preventivo Semestral ' . uniqid(),
        'frequency_days'  => 180,
        'type'            => 'deep',
        'checklist_items' => [
            'Revisión de niveles de aceite hidráulico',
            'Calibración de sensores de posición',
            'Limpieza de filtros y conductos',
        ],
    ];

    $response = $this->actingAs($this->user)->postJson('/maintenance/plans', $payload);

    $response->assertStatus(201);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(201);
    expect($response->json('data.name'))->toBe($payload['name']);
    expect($response->json('data.checklist_items'))->toHaveCount(3);
});

test('3. API POST /maintenance/plans (Validación fallida)', function () {
    $payload = [
        'equipment_id'   => 999999,
        'name'           => '',
        'frequency_days' => 0,
        'type'           => 'invalid_type',
    ];

    $response = $this->actingAs($this->user)->postJson('/maintenance/plans', $payload);

    $response->assertStatus(422);
    expect($response->json('errors'))->toHaveKeys(['equipment_id', 'name', 'frequency_days', 'type']);
});

test('4. API POST /maintenance/plans (Sanitización anti Stored-XSS en nombre y checklist)', function () {
    $payload = [
        'equipment_id'    => $this->equipment->id,
        'name'            => '<script>alert("XSS")</script>Plan Sanitizado ' . uniqid(),
        'frequency_days'  => 30,
        'type'            => 'frequent',
        'checklist_items' => [
            '<b>Inspección visual</b>',
            '<img src=x onerror=alert(1)>Prueba de fuga',
        ],
    ];

    $response = $this->actingAs($this->user)->postJson('/maintenance/plans', $payload);

    $response->assertStatus(201);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.name'))->not->toContain('<script>');
    expect($response->json('data.checklist_items.0.description'))->not->toContain('<b>');
});

test('5. API GET /maintenance/plans/{id} (Consulta por ID existente)', function () {
    $plan = MaintenancePlan::first() ?? MaintenancePlan::create([
        'equipment_id'   => $this->equipment->id,
        'name'           => 'Plan Muestra',
        'frequency_days' => 15,
        'type'           => 'frequent',
    ]);

    $response = $this->actingAs($this->user)->getJson("/maintenance/plans/{$plan->id}");

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.id'))->toBe($plan->id);
});

test('6. API GET /maintenance/plans/{id} (Consulta ID inexistente - 404)', function () {
    $response = $this->actingAs($this->user)->getJson('/maintenance/plans/9999999');

    $response->assertStatus(404);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(404);
});

test('7. API PUT /maintenance/plans/{id} (Actualización exitosa)', function () {
    $plan = MaintenancePlan::create([
        'equipment_id'   => $this->equipment->id,
        'name'           => 'Plan Para Actualizar',
        'frequency_days' => 45,
        'type'           => 'frequent',
    ]);

    $updatePayload = [
        'equipment_id'    => $this->equipment->id,
        'name'            => 'Plan Actualizado ' . uniqid(),
        'frequency_days'  => 60,
        'type'            => 'deep',
        'checklist_items' => [
            'Nuevo punto 1: Desmontaje de cabezal',
            'Nuevo punto 2: Reengrase de rodamientos',
        ],
    ];

    $response = $this->actingAs($this->user)->putJson("/maintenance/plans/{$plan->id}", $updatePayload);

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.name'))->toBe($updatePayload['name']);
    expect($response->json('data.checklist_items'))->toHaveCount(2);
});

test('8. API DELETE /maintenance/plans/{id} (Eliminación exitosa sin registros)', function () {
    $plan = MaintenancePlan::create([
        'equipment_id'   => $this->equipment->id,
        'name'           => 'Plan Para Borrar ' . uniqid(),
        'frequency_days' => 10,
        'type'           => 'frequent',
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/maintenance/plans/{$plan->id}");

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect(MaintenancePlan::find($plan->id))->toBeNull();
});

test('9. API DELETE /maintenance/plans/{id} (Bloqueo por tener registros históricos asociados)', function () {
    $plan = MaintenancePlan::create([
        'equipment_id'   => $this->equipment->id,
        'name'           => 'Plan Con Registros Históricos ' . uniqid(),
        'frequency_days' => 30,
        'type'           => 'frequent',
    ]);

    MaintenanceRecord::create([
        'code'                => 'REC-TEST-' . rand(1000, 9999),
        'equipment_id'        => $this->equipment->id,
        'maintenance_plan_id' => $plan->id,
        'scheduled_date'      => now()->addDays(5),
        'status'              => 'pending',
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/maintenance/plans/{$plan->id}");

    $response->assertStatus(422);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('code'))->toBe(422);
    expect($response->json('message'))->toContain('registros históricos');
});
