<?php

use App\Models\User;
use App\Models\WeeklyFinding;
use App\Models\WeeklyNextAction;
use App\Models\WeeklyObjective;
use App\Models\WeeklyResult;
use App\Models\WeeklyWorkPlan;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    $this->unauthorizedUser = User::factory()->create();

    Permission::findOrCreate('laboratory.show', 'web');
    Permission::findOrCreate('laboratory.delete', 'web');

    $this->user->givePermissionTo('laboratory.show');
    $this->user->givePermissionTo('laboratory.delete');

    $this->plan = WeeklyWorkPlan::create([
        'week_range'       => 'Semana 40 (28 Sep - 04 Oct)',
        'review_date'      => now()->toDateString(),
        'project_name'     => 'Mejora de Procesos Calidad ' . uniqid(),
        'responsible_name' => 'Dr. Fernando Vega',
        'total_hours'      => 40,
    ]);

    $this->objective = WeeklyObjective::create([
        'plan_id'        => $this->plan->id,
        'item_number'    => 1,
        'title'          => 'Calibración de espectrofotómetro',
        'description'    => 'Realizar calibración y verificación de curvas',
        'hours'          => 8,
        'position_order' => 1,
    ]);

    $this->result = WeeklyResult::create([
        'plan_id'          => $this->plan->id,
        'objective_number' => 1,
        'result_text'      => 'Equipo calibrado según norma',
        'is_met'           => true,
        'position_order'   => 1,
    ]);

    $this->finding = WeeklyFinding::create([
        'plan_id'        => $this->plan->id,
        'finding_text'   => 'Lámpara UV con desgaste leve',
        'cause_text'     => 'Uso continuo',
        'proposal_text'  => 'Programar cambio en próximo mantenimiento',
        'position_order' => 1,
    ]);

    $this->nextAction = WeeklyNextAction::create([
        'plan_id'         => $this->plan->id,
        'next_week_range' => 'Semana 41',
        'plan_text'       => 'Muestreo de aguas de proceso',
        'actions_text'    => 'Tomar 10 puntos de control',
        'position_order'  => 1,
    ]);
});

afterEach(function () {
    if (isset($this->plan)) {
        WeeklyObjective::where('plan_id', $this->plan->id)->delete();
        WeeklyResult::where('plan_id', $this->plan->id)->delete();
        WeeklyFinding::where('plan_id', $this->plan->id)->delete();
        WeeklyNextAction::where('plan_id', $this->plan->id)->delete();
        WeeklyWorkPlan::where('id', $this->plan->id)->delete();
    }
    if (isset($this->unauthorizedUser)) {
        $this->unauthorizedUser->delete();
    }
});

test('1. retorna 403 al acceder a planes semanales sin permiso laboratory.show', function () {
    $response = $this->actingAs($this->unauthorizedUser)->get('/weekly-plans');

    $response->assertStatus(403);
});

test('2. puede cargar la vista principal de planes semanales (GET /weekly-plans)', function () {
    $response = $this->actingAs($this->user)->get('/weekly-plans');

    $response->assertStatus(200)
        ->assertViewIs('weekly_plans.index');
});

test('3. puede listar los planes semanales en formato JSON para DataTables (GET /weekly-plans/json)', function () {
    $response = $this->actingAs($this->user)->getJson('/weekly-plans/json');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'report_code',
                    'week_range',
                    'entry_date',
                    'issue_date',
                    'client_name',
                    'project_name',
                    'responsible_name',
                    'total_hours',
                    'pdf_url',
                    'delete_url',
                ],
            ],
        ]);
});

test('4. puede generar y visualizar el PDF de un plan semanal (GET /weekly-plans/{id}/pdf)', function () {
    $response = $this->actingAs($this->user)->get('/weekly-plans/' . $this->plan->id . '/pdf');

    $response->assertStatus(200)
        ->assertHeader('content-type', 'application/pdf');
});

test('5. retorna 404 al generar PDF de un plan inexistente (GET /weekly-plans/999999/pdf)', function () {
    $response = $this->actingAs($this->user)->get('/weekly-plans/999999/pdf');

    $response->assertStatus(404);
});

test('6. retorna 403 al intentar eliminar sin permiso laboratory.delete (DELETE /weekly-plans/{id})', function () {
    $userWithoutDelete = User::factory()->create();
    $userWithoutDelete->givePermissionTo('laboratory.show');

    $response = $this->actingAs($userWithoutDelete)->deleteJson('/weekly-plans/' . $this->plan->id);

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'message' => 'No autorizado.',
        ]);

    $userWithoutDelete->delete();
});

test('7. puede eliminar un plan semanal con permiso laboratory.delete (DELETE /weekly-plans/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson('/weekly-plans/' . $this->plan->id);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'message' => 'Eliminado correctamente.',
        ]);

    $this->assertDatabaseMissing('weekly_work_plans', [
        'id' => $this->plan->id,
    ]);
});

test('8. retorna 404 al intentar eliminar un plan semanal inexistente (DELETE /weekly-plans/999999)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/weekly-plans/999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
        ]);
});
