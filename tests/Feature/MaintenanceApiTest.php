<?php

use App\Models\Area;
use App\Models\Equipment;
use App\Models\MaintenancePlan;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    $this->user = User::first() ?? User::factory()->create();
    $this->area = Area::first() ?? Area::create([
        'name' => 'Área Base ' . uniqid(),
        'description' => 'Área para pruebas',
    ]);
    $this->equipment = Equipment::first() ?? Equipment::create([
        'code'      => 'EQ-REC-BASE',
        'name'      => 'Torno CNC Base',
        'area_id'   => $this->area->id,
        'is_active' => true,
    ]);
    $this->plan = MaintenancePlan::first() ?? MaintenancePlan::create([
        'equipment_id'   => $this->equipment->id,
        'name'           => 'Plan Mensual Base',
        'frequency_days' => 30,
        'type'           => 'frequent',
    ]);
});

test('1. API GET /maintenance (Listado de órdenes pendientes/impresas)', function () {
    $response = $this->actingAs($this->user)->getJson('/maintenance');

    echo "\n\n>>> LLAMADA: GET /maintenance\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
});

test('2. API GET /maintenance/history (Historial de mantenimientos completados)', function () {
    $response = $this->actingAs($this->user)->getJson('/maintenance/history');

    echo "\n\n>>> LLAMADA: GET /maintenance/history\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('code'))->toBe(200);
});

test('3. API GET /maintenance/{record}/print (Generación de checklist y cambio a estado impreso)', function () {
    $record = MaintenanceRecord::create([
        'code'                => 'REC-PRINT-' . rand(1000, 9999),
        'equipment_id'        => $this->equipment->id,
        'maintenance_plan_id' => $this->plan->id,
        'scheduled_date'      => now(),
        'status'              => 'pending',
    ]);

    $response = $this->actingAs($this->user)->getJson("/maintenance/{$record->id}/print");

    echo "\n\n>>> LLAMADA: GET /maintenance/{$record->id}/print\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.status'))->toBe('printed');
});

test('4. API POST /maintenance/{record}/complete (Fallo por intentar completar sin haber impreso)', function () {
    $record = MaintenanceRecord::create([
        'code'                => 'REC-NOPRINT-' . rand(1000, 9999),
        'equipment_id'        => $this->equipment->id,
        'maintenance_plan_id' => $this->plan->id,
        'scheduled_date'      => now(),
        'status'              => 'pending', // No está 'printed'
    ]);

    $file = UploadedFile::fake()->create('evidencia.pdf', 500, 'application/pdf');

    $response = $this->actingAs($this->user)->postJson("/maintenance/{$record->id}/complete", [
        'evidence_file' => $file,
    ]);

    echo "\n\n>>> LLAMADA: POST /maintenance/{$record->id}/complete (Sin imprimir)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('flag'))->toBeFalse();
    expect($response->json('message'))->toContain('imprimir');
});

test('5. API POST /maintenance/{record}/complete (Fallo de validación por falta de archivo de evidencia)', function () {
    $record = MaintenanceRecord::create([
        'code'                => 'REC-NOFILE-' . rand(1000, 9999),
        'equipment_id'        => $this->equipment->id,
        'maintenance_plan_id' => $this->plan->id,
        'scheduled_date'      => now(),
        'status'              => 'printed',
    ]);

    $response = $this->actingAs($this->user)->postJson("/maintenance/{$record->id}/complete", []);

    echo "\n\n>>> LLAMADA: POST /maintenance/{$record->id}/complete (Sin archivo)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(422);
    expect($response->json('errors'))->toHaveKey('evidence_file');
});

test('6. API POST /maintenance/{record}/complete (Completado exitoso con subida segura de evidencia)', function () {
    $record = MaintenanceRecord::create([
        'code'                => 'REC-SUCCESS-' . rand(1000, 9999),
        'equipment_id'        => $this->equipment->id,
        'maintenance_plan_id' => $this->plan->id,
        'scheduled_date'      => now(),
        'status'              => 'printed',
    ]);

    $file = UploadedFile::fake()->create('evidencia_mantenimiento.jpg', 300, 'image/jpeg');

    $response = $this->actingAs($this->user)->postJson("/maintenance/{$record->id}/complete", [
        'evidence_file' => $file,
    ]);

    echo "\n\n>>> LLAMADA: POST /maintenance/{$record->id}/complete (Completado exitoso)\n";
    echo "HTTP STATUS: " . $response->status() . "\n";
    echo "RESPUESTA JSON:\n" . json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    $response->assertStatus(200);
    expect($response->json('flag'))->toBeTrue();
    expect($response->json('data.status'))->toBe('completed');
    expect($response->json('data.evidence_file'))->not->toBeNull();

    $record->refresh();
    expect($record->status)->toBe('completed');
    expect($record->evidence_file)->not->toBeNull();
});
