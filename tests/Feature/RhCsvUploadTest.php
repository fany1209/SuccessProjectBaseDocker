<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\User;

beforeEach(function () {
    DB::table('asistencias')->truncate();
});

test('csv upload efficiently imports attendance records with batch upsert', function () {
    $user = new User(['name' => 'Admin']);
    $user->id = 1;

    // Create synthetic CSV content with multiple rows
    $csvContent = "Nombre;Fecha;Entrada;Salida Comida;Regreso Comida;Salida Final;Tipo;Comentario\n";
    $csvContent .= "Juan Perez;2026-09-01;08:30:00;13:00:00;14:00:00;17:30:00;Normal;A tiempo\n";
    $csvContent .= "Maria Lopez;2026-09-01;08:35:00;13:00:00;14:00:00;17:35:00;Normal;Retardo menor\n";
    $csvContent .= "fanny;2026-09-01;08:25:00;13:00:00;14:00:00;17:30:00;Normal;Alias check\n";

    $file = UploadedFile::fake()->createWithContent('asistencias.csv', $csvContent);

    $response = $this->actingAs($user)->post('/rh/asistencia/upload', [
        'csv_file' => $file,
    ]);

    $response->assertSessionHas('success');

    // Assert that 3 records were created
    expect(DB::table('asistencias')->count())->toBe(3);

    // Verify alias normalization
    $fany = DB::table('asistencias')->where('fecha', '2026-09-01')->where('nombre', 'Fany')->first();
    expect($fany)->not->toBeNull();
    expect($fany->entrada)->toBe('08:25:00');
});

test('csv upload updates existing records without creating duplicates', function () {
    $user = new User(['name' => 'Admin']);
    $user->id = 1;

    // Pre-seed an attendance record
    DB::table('asistencias')->insert([
        'nombre' => 'Carlos Gomez',
        'fecha' => '2026-09-02',
        'entrada' => '09:00:00',
        'tipo' => 'Normal',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // CSV with updated hours for the same person on the same date
    $csvContent = "Nombre;Fecha;Entrada;Salida Comida;Regreso Comida;Salida Final;Tipo;Comentario\n";
    $csvContent .= "Carlos Gomez;2026-09-02;08:45:00;13:00:00;14:00:00;17:45:00;Normal;Horario corregido\n";

    $file = UploadedFile::fake()->createWithContent('update_asistencias.csv', $csvContent);

    $response = $this->actingAs($user)->post('/rh/asistencia/upload', [
        'csv_file' => $file,
    ]);

    $response->assertSessionHas('success');

    // Count should still be 1 (upsert updated, not inserted duplicate)
    expect(DB::table('asistencias')->where('nombre', 'Carlos Gomez')->count())->toBe(1);

    $updated = DB::table('asistencias')->where('nombre', 'Carlos Gomez')->first();
    expect($updated->entrada)->toBe('08:45:00');
    expect($updated->comentario)->toBe('Horario corregido');
});
