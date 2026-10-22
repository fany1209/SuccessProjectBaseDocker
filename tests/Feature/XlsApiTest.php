<?php

use App\Http\Repositories\Xls\XlsRepository;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('reports exports spreadsheet without year filter and streams response', function () {
    $response = $this->actingAs($this->user)->get(route('reports'));

    $response->assertOk();
    expect($response->headers->get('content-type'))
        ->toContain('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    expect($response->headers->get('content-disposition'))
        ->toContain('Reporte_movimientos.xlsx');
});

it('reports exports spreadsheet with specific year filter', function () {
    $response = $this->actingAs($this->user)->get(route('reports', ['year' => 2025]));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))
        ->toContain('Reporte_movimientos_2025.xlsx');
});

it('reports fails validation when year is out of range', function () {
    $response = $this->actingAs($this->user)->get(route('reports', ['year' => 1800]));

    $response->assertSessionHasErrors(['year']);
});

it('inventoryXls exports inventory lots and totals spreadsheet', function () {
    $product = Product::first() ?? Product::create([
        'name' => 'Producto Test Xls',
        'unit' => 'KG',
    ]);

    Inventory::create([
        'product_id' => $product->product_id,
        'batch'      => 'BATCH-XLS-' . rand(1000, 99999),
        'stock'      => 150.75,
    ]);

    $response = $this->actingAs($this->user)->get(route('export-inventory'));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))
        ->toContain('Inventario_Success.xlsx');
});

it('proteinXls exports protein data with chart', function () {
    $response = $this->actingAs($this->user)->get(route('export-protein'));

    $response->assertOk();
    expect($response->headers->get('content-disposition'))
        ->toContain('Reporte_Proteina.xlsx');
});

it('generateProteinSpreadsheet includes packing list, peso real and total sum', function () {
    $product = Product::first() ?? Product::create([
        'name'     => 'Proteina Especial Test',
        'unit'     => 'KG',
        'sat_code' => '12181602',
    ]);

    $inventory = Inventory::create([
        'product_id' => $product->product_id,
        'batch'      => 'BATCH-PROT-' . rand(1000, 99999),
        'stock'      => 500,
    ]);

    $bagNumber = 'BT-' . rand(100000, 999999);
    $cliId = DB::table('cli')->insertGetId([
        'inventory_id'    => $inventory->inventory_id,
        'location_id'     => 1,
        'concept_id'      => 1,
        'bag_number'      => $bagNumber,
        'packing_list'    => 'PL-7788',
        'quantity'        => 1,
        'weight_per_unit' => 1025.50,
        'net_weight'      => 1025.50,
        'protein'         => '38.50',
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    try {
        $repo = app(XlsRepository::class);
        $result = $repo->generateProteinSpreadsheet();

        expect($result)->toHaveKeys(['spreadsheet', 'fileName', 'hasCharts']);
        expect($result['hasCharts'])->toBeTrue();

        $sheet = $result['spreadsheet']->getActiveSheet();

        // Verify headers
        expect($sheet->getCell('A3')->getValue())->toBe('Producto');
        expect($sheet->getCell('B3')->getValue())->toBe('Lote');
        expect($sheet->getCell('C3')->getValue())->toBe('Barcina');
        expect($sheet->getCell('D3')->getValue())->toBe('Packing List');
        expect($sheet->getCell('E3')->getValue())->toBe('Peso Real (kg)');
        expect($sheet->getCell('F3')->getValue())->toBe('Proteína (%)');
        expect($sheet->getCell('G3')->getValue())->toBe('Rango');

        // Verify data row contains packing list and peso real
        expect($sheet->getCell('D4')->getValue())->toBe('PL-7788');
        expect((float) $sheet->getCell('E4')->getValue())->toBe(1025.50);
        expect((float) $sheet->getCell('F4')->getValue())->toBe(38.5);
        expect($sheet->getCell('G4')->getValue())->toBe('35 a 39.9');

        // Verify distribution summary moved to columns I and J
        expect($sheet->getCell('I3')->getValue())->toBe('Rango');
        expect($sheet->getCell('J3')->getValue())->toBe('Cantidad');
        expect($sheet->getCell('I8')->getValue())->toBe('Total Barcinas');
        expect($sheet->getCell('I10')->getValue())->toBe('Total Peso Real (kg)');
        expect((float) $sheet->getCell('J10')->getValue())->toBeGreaterThanOrEqual(1025.50);

        // Verify chart
        $charts = $sheet->getChartCollection();
        expect(count($charts))->toBe(1);
        expect($charts[0]->getTopLeftPosition()['cell'])->toBe('L3');
    } finally {
        DB::table('cli')->where('cli_id', $cliId)->delete();
        $inventory->delete();
    }
});

it('handles exception in reports and returns error response', function () {
    $mockRepo = Mockery::mock(XlsRepository::class);
    $mockRepo->shouldReceive('generateReportsSpreadsheet')->andThrow(new Exception('Error simulado XLS'));
    $this->app->instance(XlsRepository::class, $mockRepo);

    $response = $this->actingAs($this->user)->getJson(route('reports'));

    $response->assertStatus(500);
    $response->assertJson([
        'success' => false,
        'message' => 'Error al generar el reporte de movimientos en Excel.',
    ]);
});
