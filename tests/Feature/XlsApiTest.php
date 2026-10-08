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
