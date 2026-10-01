<?php

use App\Models\SoilConventionalVariable;
use App\Models\SoilInternalAnalysis;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
    $this->unauthorizedUser = User::factory()->create();

    Permission::findOrCreate('laboratory.show', 'web');
    $this->user->givePermissionTo('laboratory.show');

    $this->analysis = SoilInternalAnalysis::create([
        'report_code'          => 'SUELO-' . uniqid(),
        'entry_date'           => now()->toDateString(),
        'issue_date'           => now()->addDays(2)->toDateString(),
        'client_name'          => 'Rancho Santa Fe ' . uniqid(),
        'client_city'          => 'Culiacán',
        'client_address'       => 'Km 15 Carr. Culiacán-Navolato',
        'client_phone'         => '6671234567',
        'crop_type'            => 'Tomate',
        'sample_weight'        => '500g',
        'is_control'           => false,
        'location'             => 'Lote 4B',
        'sampling_type'        => 'Compuesta',
        'sampling_responsible' => 'Ing. López',
        'purpose'              => 'Fertilidad',
    ]);

    $this->variable = SoilConventionalVariable::create([
        'soil_analysis_id' => $this->analysis->id,
        'variable_name'    => 'pH en Agua',
        'result_text'      => '6.8',
        'unit_text'        => 'Unidades',
        'position_order'   => 1,
    ]);
});

afterEach(function () {
    if (isset($this->variable)) {
        SoilConventionalVariable::where('id', $this->variable->id)->delete();
    }
    if (isset($this->analysis)) {
        SoilInternalAnalysis::where('id', $this->analysis->id)->delete();
    }
    if (isset($this->unauthorizedUser)) {
        $this->unauthorizedUser->delete();
    }
});

test('1. retorna 403 al acceder a análisis de suelo sin permiso laboratory.show', function () {
    $response = $this->actingAs($this->unauthorizedUser)->get('/soil-analyses');

    $response->assertStatus(403);
});

test('2. puede cargar la vista de análisis de suelos (GET /soil-analyses)', function () {
    $response = $this->actingAs($this->user)->get('/soil-analyses');

    $response->assertStatus(200)
        ->assertViewIs('laboratory.soil.index');
});

test('3. puede listar los análisis de suelo en formato JSON para DataTables (GET /soil-analyses/json)', function () {
    $response = $this->actingAs($this->user)->getJson('/soil-analyses/json');

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
                    'entry_date',
                    'issue_date',
                    'client_name',
                    'pdf_url',
                    'delete_url',
                ],
            ],
        ]);
});

test('4. puede generar y descargar el reporte PDF de un análisis de suelo (GET /soil-analyses/{id}/pdf)', function () {
    $response = $this->actingAs($this->user)->get('/soil-analyses/' . $this->analysis->id . '/pdf');

    $response->assertStatus(200)
        ->assertHeader('content-type', 'application/pdf');
});

test('5. retorna 404 al generar PDF de un análisis de suelo inexistente (GET /soil-analyses/999999/pdf)', function () {
    $response = $this->actingAs($this->user)->get('/soil-analyses/999999/pdf');

    $response->assertStatus(404);
});

test('6. puede eliminar un análisis de suelo y sus variables asociadas (DELETE /soil-analyses/{id})', function () {
    $response = $this->actingAs($this->user)->deleteJson('/soil-analyses/' . $this->analysis->id);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'message' => 'Registro eliminado correctamente.',
        ]);

    $this->assertDatabaseMissing('soil_internal_analyses', [
        'id' => $this->analysis->id,
    ]);

    $this->assertDatabaseMissing('soil_conventional_variables', [
        'id' => $this->variable->id,
    ]);
});

test('7. retorna 404 al intentar eliminar un análisis de suelo inexistente (DELETE /soil-analyses/999999)', function () {
    $response = $this->actingAs($this->user)->deleteJson('/soil-analyses/999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
            'code'    => 404,
        ]);
});
