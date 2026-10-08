<?php

use App\Http\Repositories\Pdf\PdfRepository;
use App\Models\Control;
use App\Models\Customer;
use App\Models\Input;
use App\Models\Output;
use App\Models\Product;
use App\Models\PurchaseRequisition;
use App\Models\Quote;
use App\Models\QuoteStatus;
use App\Models\Sale;
use App\Models\SaleStatus;
use App\Models\Sector;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->product = Product::first() ?? Product::create([
        'name' => 'Producto Prueba PDF',
        'unit' => 'KG',
    ]);
});

it('downloadPDF generates and streams pdf for inputs movement', function () {
    $supplier = Supplier::first() ?? Supplier::create([
        'name'          => 'Proveedor Test PDF',
        'supplier_code' => 'SUP-TEST-01',
    ]);

    $input = Input::create([
        'operator'             => 'Juan Perez',
        'supplier_id'          => $supplier->supplier_id,
        'comments'             => 'Comentarios de prueba input',
        'unit_plates'          => 'ABC-123',
        'trailer_plates'       => 'XYZ-789',
        'security_seal'        => 1,
        'security_seal_number' => 'SEAL-01',
    ]);

    DB::table('product_inputs')->insert([
        'input_id'        => $input->input_id,
        'product_id'      => $this->product->product_id,
        'quantity'        => 50.0,
        'warehouse_batch' => 'LOTE-INP-01',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('makePDF', ['movType' => 'inputs', 'movId' => $input->input_id]));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

it('downloadPDF generates and streams pdf for outputs movement', function () {
    $customer = Customer::first() ?? Customer::create([
        'name'          => 'Cliente Test PDF',
        'customer_code' => 'CLI-TEST-01',
    ]);

    $output = Output::create([
        'operator'             => 'Carlos Gomez',
        'customer_id'          => $customer->customer_id,
        'comments'             => 'Comentarios de prueba output',
        'vendedor'             => 'Vendedor PDF',
        'unit_plates'          => 'DEF-456',
        'trailer_plates'       => 'UVW-123',
        'security_seal'        => 1,
        'security_seal_number' => 'SEAL-02',
    ]);

    DB::table('product_outputs')->insert([
        'output_id'       => $output->output_id,
        'product_id'      => $this->product->product_id,
        'quantity'        => 30.0,
        'warehouse_batch' => 'LOTE-OUT-01',
        'label_batch'     => 'LABEL-01',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('makePDF', ['movType' => 'outputs', 'movId' => $output->output_id]));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

it('downloadPDF returns 400 for invalid movement type', function () {
    $response = $this->actingAs($this->user)
        ->get(route('makePDF', ['movType' => 'invalido', 'movId' => 1]));

    $response->assertStatus(400);
    $response->assertJson([
        'success' => false,
        'flag'    => false,
    ]);
});

it('downloadPDF returns 404 when movement record not found', function () {
    $response = $this->actingAs($this->user)
        ->get(route('makePDF', ['movType' => 'inputs', 'movId' => 999999]));

    $response->assertStatus(404);
});

it('makeTemperaturePDF generates and streams temperature report pdf', function () {
    Control::create([
        'warehouse_id' => 1,
        'host_ip'      => '192.168.1.1',
        'host_user'    => 'admin_user',
        'host_name'    => 'host_main',
        'temperature'  => 22.5,
        'humidity'     => 55.0,
        'created_at'   => '2025-05-10 09:00:00',
    ]);

    $response = $this->actingAs($this->user)->get(route('temperature-pdf', [
        'week_a'       => 10,
        'week_b'       => 25,
        'year'         => 2025,
        'warehouse_id' => 1,
    ]));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

it('makeTemperaturePDF returns 400 for invalid week ranges', function () {
    $response = $this->actingAs($this->user)->get(route('temperature-pdf', [
        'week_a'       => 30,
        'week_b'       => 20,
        'year'         => 2025,
        'warehouse_id' => 1,
    ]));

    $response->assertStatus(400);
    $response->assertJson([
        'success' => false,
        'message' => 'El rango de semanas especificado es inválido.',
    ]);
});

it('makeDeliveryNotePDF generates and streams delivery note pdf for sale', function () {
    $sector = Sector::first() ?? Sector::create(['name' => 'General']);
    $status = SaleStatus::first() ?? SaleStatus::create(['name' => 'Completada', 'color' => '#10B981']);
    $customer = Customer::first() ?? Customer::create(['name' => 'Cliente Delivery']);

    $sale = Sale::create([
        'first_time'      => 0,
        'seller'          => 'Vendedor Delivery',
        'is_customer'     => 1,
        'purchase_order'  => 'OC-DELIV-' . rand(1000, 9999),
        'invoice'         => 'FAC-DELIV-' . rand(1000, 9999),
        'sale_type'       => 'credit',
        'term'            => 15,
        'date'            => now()->format('Y-m-d'),
        'folio'           => rand(10000, 99999),
        'customer_id'     => $customer->customer_id,
        'sector_id'       => $sector->sector_id,
        'user_id'         => $this->user->id,
        'sales_status_id' => $status->sales_status_id,
    ]);

    $sale->products()->attach($this->product->product_id, [
        'quantity' => 10,
        'cost'     => 100.0,
        'has_tax'  => 1,
    ]);

    $response = $this->actingAs($this->user)->get(route('delivery-note', ['sale_id' => $sale->sale_id]));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

it('makeDeliveryNotePDF returns 404 when sale does not exist', function () {
    $response = $this->actingAs($this->user)->get(route('delivery-note', ['sale_id' => 999999]));

    $response->assertStatus(404);
});

it('makeQuotePDF generates and streams quote pdf', function () {
    $quoteStatus = QuoteStatus::first() ?? QuoteStatus::create(['name' => 'Activa']);

    $quote = Quote::create([
        'folio'            => 'COT-' . rand(1000, 9999),
        'company'          => 'Empresa Cotización',
        'date'             => now()->format('Y-m-d'),
        'currency'         => 'MXN',
        'attention'        => 'Ing. Rodriguez',
        'phone'            => '5551234567',
        'email'            => 'contacto@cotizacion.test',
        'department'       => 'Ventas',
        'quotes_status_id' => $quoteStatus->quotes_status_id,
        'user_id'          => $this->user->id,
    ]);

    $quote->products()->attach($this->product->product_id, [
        'quote_product_name' => 'Producto Cotizado Test',
        'quantity'           => 5,
        'cost'               => 200.0,
        'presentation'       => 'Bolsa 25kg',
    ]);

    $response = $this->actingAs($this->user)->get(route('quote', ['quote_id' => $quote->quote_id]));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

it('makeRequisitionPDF generates and streams requisition pdf', function () {
    $requisition = PurchaseRequisition::create([
        'consecutive'    => 'REQ-TEST-' . rand(1000, 9999),
        'applicant'      => 'Ing. Requisición',
        'department'     => 'Mantenimiento',
        'purchase_order' => 'PO-' . rand(1000, 9999),
        'data_sheet'     => 0,
        'safety_sheet'   => 0,
    ]);

    $response = $this->actingAs($this->user)->get(route('requisition.download', ['requisition_id' => $requisition->id]));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

it('handles exception in downloadPDF and returns 500 error response', function () {
    $mockRepo = Mockery::mock(PdfRepository::class);
    $mockRepo->shouldReceive('getMovementPdfData')->andThrow(new Exception('Error PDF simulado'));
    $this->app->instance(PdfRepository::class, $mockRepo);

    $response = $this->actingAs($this->user)
        ->getJson(route('makePDF', ['movType' => 'inputs', 'movId' => 1]));

    $response->assertStatus(500);
    $response->assertJson([
        'success' => false,
        'message' => 'Error al generar el documento PDF.',
    ]);
});
