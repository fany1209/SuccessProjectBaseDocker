<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    $this->supplier = Supplier::create([
        'supplier_code' => 'SP-CERT-' . rand(1000, 9999),
        'name'          => 'Proveedor Certificados S.A.',
        'rfc'           => 'CERT' . rand(10000000, 99999999) . 'X',
    ]);

    $this->product = Product::first() ?? Product::create([
        'name'        => 'Producto Cert Test',
        'sku'         => 'SKU-CERT-' . rand(1000, 9999),
        'sat_code'    => '01010101',
        'category_id' => 1,
    ]);
});

afterEach(function () {
    if (isset($this->supplier)) {
        Supplier::where('supplier_id', $this->supplier->supplier_id)->delete();
    }
});

test('index returns json list of supplier certificates', function () {
    $response = $this->actingAs($this->user)->getJson(route('supplier_certificates.index'));

    $response->assertStatus(200);
    $response->assertJsonStructure(['certificates']);
});

test('store uploads certificate pdf and saves records in database', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('certificado_test.pdf', 500, 'application/pdf');

    $payload = [
        'supplier_id'   => $this->supplier->supplier_id,
        'product_id'    => $this->product->product_id,
        'fecha_emision' => now()->toDateString(),
        'file'          => $file,
    ];

    $response = $this->actingAs($this->user)->post(route('supplier_certificates.store'), $payload);

    $response->assertSessionHas('success');

    $certificate = DB::table('supplier_certificates')
        ->where('supplier_id', $this->supplier->supplier_id)
        ->first();

    expect($certificate)->not->toBeNull();

    $fileRecord = DB::table('files')->where('file_id', $certificate->file_id)->first();
    expect($fileRecord)->not->toBeNull();
    Storage::disk('public')->assertExists($fileRecord->path);

    // Clean up
    DB::table('supplier_certificates')->where('id', $certificate->id)->delete();
    DB::table('files')->where('file_id', $fileRecord->file_id)->delete();
});

test('store returns json response when wantsJson is set', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('certificado_api.pdf', 300, 'application/pdf');

    $payload = [
        'supplier_id'   => $this->supplier->supplier_id,
        'product_id'    => $this->product->product_id,
        'fecha_emision' => now()->toDateString(),
        'file'          => $file,
    ];

    $response = $this->actingAs($this->user)->postJson(route('supplier_certificates.store'), $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);

    $certificate = DB::table('supplier_certificates')
        ->where('supplier_id', $this->supplier->supplier_id)
        ->first();

    if ($certificate) {
        $fileRecord = DB::table('files')->where('file_id', $certificate->file_id)->first();
        DB::table('supplier_certificates')->where('id', $certificate->id)->delete();
        if ($fileRecord) {
            DB::table('files')->where('file_id', $fileRecord->file_id)->delete();
        }
    }
});

test('store fails validation when required fields or file are missing', function () {
    $response = $this->actingAs($this->user)->postJson(route('supplier_certificates.store'), [
        'supplier_id' => $this->supplier->supplier_id,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['product_id', 'fecha_emision', 'file']);
});

test('destroy removes certificate and deletes physical file', function () {
    Storage::fake('public');

    $fakePath = 'certificates/test_delete_' . uniqid() . '.pdf';
    Storage::disk('public')->put($fakePath, 'dummy pdf content');

    $fileId = DB::table('files')->insertGetId([
        'path'       => $fakePath,
        'product_id' => $this->product->product_id,
    ]);

    $certId = DB::table('supplier_certificates')->insertGetId([
        'supplier_id'   => $this->supplier->supplier_id,
        'file_id'       => $fileId,
        'fecha_emision' => now()->toDateString(),
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('supplier_certificates.destroy', $certId));

    $response->assertStatus(200);
    $response->assertJsonPath('message', 'Eliminado correctamente');

    $this->assertDatabaseMissing('supplier_certificates', ['id' => $certId]);
    $this->assertDatabaseMissing('files', ['file_id' => $fileId]);
    Storage::disk('public')->assertMissing($fakePath);
});

test('destroy returns 404 for non-existent certificate', function () {
    $response = $this->actingAs($this->user)->deleteJson(route('supplier_certificates.destroy', 999999));

    $response->assertStatus(404);
    $response->assertJsonPath('message', 'No encontrado');
});
