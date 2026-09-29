<?php

use App\Models\LaboratorySample;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();
});

test('1. puede visualizar la vista del gráfico de laboratorio (GET /lab/grafica)', function () {
    $response = $this->actingAs($this->user)->get(route('lab.charts.index'));

    $response->assertStatus(200);
    $response->assertViewIs('laboratory.grafica');
});

test('2. puede obtener datos de gráfica en JSON (GET /lab/grafica)', function () {
    $response = $this->actingAs($this->user)->getJson(route('lab.charts.index'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Datos de gráfica de laboratorio obtenidos correctamente.',
        ])
        ->assertJsonStructure([
            'success',
            'flag',
            'code',
            'message',
            'data' => [
                'reception_of_samples',
                'weekly_results',
                'laboratory_samples',
                'pdf_clicks_d',
            ],
        ]);
});

test('3. puede obtener los conteos de laboratorio en JSON estandarizado y compatible (GET /lab/grafica/counts)', function () {
    $response = $this->actingAs($this->user)->getJson(route('lab.charts.counts'));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
            'code'    => 200,
            'message' => 'Conteos de registros obtenidos correctamente.',
        ])
        ->assertJsonStructure([
            'success',
            'flag',
            'code',
            'message',
            'data' => [
                'reception_of_samples',
                'weekly_results',
                'laboratory_samples',
                'pdf_clicks_d',
            ],
            // Compatibilidad legacy de frontend con claves en raíz
            'reception_of_samples',
            'weekly_results',
            'laboratory_samples',
            'pdf_clicks_d',
        ]);
});

test('4. calcula correctamente los conteos agregados desde la base de datos', function () {
    $initialRes = $this->actingAs($this->user)->getJson(route('lab.charts.counts'));
    $initialCounts = $initialRes->json('data');

    // Generar un folio único de 4 caracteres
    $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    while (LaboratorySample::where('folio', $folio)->exists()) {
        $folio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
    }

    // Insertar registros controlados
    $sample = LaboratorySample::create([
        'folio'         => $folio,
        'tipo_muestra'  => 'Materia Prima',
        'producto'      => 'Levadura Test',
        'stock_inicial' => 10,
    ]);

    $receptionId = DB::table('reception_of_samples')->insertGetId([
        'folio_muestra' => 'REC-' . uniqid(),
        'cantidad'      => 1,
    ]);

    $afterRes = $this->actingAs($this->user)->getJson(route('lab.charts.counts'));
    $afterCounts = $afterRes->json('data');

    expect($afterCounts['laboratory_samples'])->toBe($initialCounts['laboratory_samples'] + 1)
        ->and($afterCounts['reception_of_samples'])->toBe($initialCounts['reception_of_samples'] + 1);

    // Limpieza
    $sample->delete();
    DB::table('reception_of_samples')->where('id', $receptionId)->delete();
});

test('5. sanitiza parámetros de consulta contra XSS (GET /lab/grafica/counts)', function () {
    $response = $this->actingAs($this->user)->getJson(route('lab.charts.counts', [
        'type' => '<script>alert("xss")</script>general',
    ]));

    $response->assertStatus(200);
});

test('6. valida parámetros de fecha y retorna 422 si el formato es inválido', function () {
    $response = $this->actingAs($this->user)->getJson(route('lab.charts.counts', [
        'from' => 'fecha-invalida',
    ]));

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['from']);
});
