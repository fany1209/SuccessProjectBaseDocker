<?php

use App\Models\Comparative;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\RequisitionProduct;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::first() ?? User::factory()->create();

    Permission::findOrCreate('purchases.requisitions.create', 'web');
    Permission::findOrCreate('purchases.requisitions.show', 'web');
    Permission::findOrCreate('purchases.requisitions.update', 'web');
    Permission::findOrCreate('purchases.requisitions.delete', 'web');

    $this->user->givePermissionTo([
        'purchases.requisitions.create',
        'purchases.requisitions.show',
        'purchases.requisitions.update',
        'purchases.requisitions.delete',
    ]);

    $this->requisition = PurchaseRequisition::create([
        'applicant'      => $this->user->name,
        'department'     => 'Almacen',
        'data_sheet'     => 1,
        'safety_sheet'   => 0,
        'consecutive'    => null,
        'purchase_order' => null,
    ]);

    $this->product = RequisitionProduct::create([
        'pr_id'       => $this->requisition->id,
        'description' => 'Test Item 1',
        'supplier'    => 'Test Supplier',
        'url'         => 'https://example.com/item1',
        'use'         => 'Maintenance',
        'quantity'    => '5pz',
        'image_url'   => '',
        'insumo'      => 'directo',
    ]);
});

afterEach(function () {
    if (isset($this->requisition)) {
        RequisitionProduct::where('pr_id', $this->requisition->id)->delete();
        PurchaseRequisition::where('id', $this->requisition->id)->delete();
    }
});

it('can list requisitions with permissions and insumo summary', function () {
    $this->actingAs($this->user);

    $response = $this->getJson(route('requisitions.getRequisitions'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'requisitions' => [
                '*' => [
                    'id',
                    'consecutive',
                    'purchase_order',
                    'applicant',
                    'department',
                    'insumo_resumen',
                    'data_sheet',
                    'safety_sheet',
                    'canUpdate',
                    'canDelete',
                ],
            ],
        ]);
});

it('can filter requisitions by search term and checked status', function () {
    $this->actingAs($this->user);

    $response = $this->getJson(route('requisitions.getRequisitions', [
        'search_requisitions' => $this->requisition->applicant,
        'requisitions_check'  => 0,
    ]));

    $response->assertStatus(200);
    $data = $response->json('requisitions');
    expect(count($data))->toBeGreaterThanOrEqual(1);
});

it('can list your requisitions for the authenticated applicant', function () {
    $this->actingAs($this->user);

    $response = $this->getJson(route('requisitions.getYourRequisitions'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'requisitions' => [
                '*' => [
                    'id',
                    'date_formatted',
                    'products',
                    'insumo_resumen',
                    'data_sheet',
                    'safety_sheet',
                    'canUpdate',
                    'canDelete',
                    'status',
                ],
            ],
        ]);

    $data = $response->json('requisitions');
    $found = collect($data)->firstWhere('id', $this->requisition->id);
    expect($found)->not->toBeNull();
});

it('can show requisition details with parsed quantities and units', function () {
    $this->actingAs($this->user);

    $response = $this->getJson("/requisitions/{$this->requisition->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'requisition',
            'products' => [
                '*' => [
                    'id',
                    'pr_id',
                    'description',
                    'quantity',
                    'qty_number',
                    'qty_unit',
                ],
            ],
        ]);

    $products = $response->json('products');
    expect($products[0]['qty_number'])->toBe('5')
        ->and($products[0]['qty_unit'])->toBe('pz');
});

it('returns 404 when showing non existent requisition', function () {
    $this->actingAs($this->user);

    $response = $this->getJson('/requisitions/999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
        ]);
});

it('can store a new requisition with multiple products and sanitized inputs', function () {
    $this->actingAs($this->user);

    $payload = [
        'applicant'    => '<b>Juan Perez</b>',
        'department'   => 'Calidad',
        'data_sheet'   => 1,
        'safety_sheet' => 0,
        'consecutive'  => 'REQ-NEW-' . rand(1000, 9999),
        'description'  => ['<script>alert(1)</script>Papel bond', 'Tinta negra'],
        'supplier'     => ['Office Depot', 'HP'],
        'url'          => ['https://office.com', 'https://hp.com'],
        'use'          => ['Oficina', 'Impresión'],
        'quantity'     => [10, 2],
        'unit'         => ['caja', 'pz'],
        'insumo'       => ['indirecto', 'directo'],
    ];

    $response = $this->postJson('/requisitions', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Operation successfully completed',
        ]);

    $reqId = $response->json('id');
    expect($reqId)->not->toBeNull();

    $this->assertDatabaseHas('purchases_requisitions', [
        'id'        => $reqId,
        'applicant' => 'Juan Perez',
    ]);

    $this->assertDatabaseHas('requisition_products', [
        'pr_id'       => $reqId,
        'description' => 'alert(1)Papel bond',
        'quantity'    => '10caja',
    ]);

    RequisitionProduct::where('pr_id', $reqId)->delete();
    PurchaseRequisition::where('id', $reqId)->delete();
});

it('fails to store requisition when missing required fields', function () {
    $this->actingAs($this->user);

    $response = $this->postJson('/requisitions', [
        'applicant' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['applicant', 'department', 'data_sheet', 'safety_sheet']);
});

it('can update an existing requisition and sync its products', function () {
    $this->actingAs($this->user);

    $payload = [
        'req_id'       => $this->requisition->id,
        'applicant'    => 'Applicant Updated',
        'department'   => 'Sistemas',
        'data_sheet'   => 0,
        'safety_sheet' => 1,
        'id'           => [$this->product->id, 'null'],
        'description'  => ['Updated Test Item 1', 'Newly Added Item 2'],
        'supplier'     => ['Supplier A', 'Supplier B'],
        'quantity'     => [15, 3],
        'unit'         => ['kg', 'pz'],
        'insumo'       => ['directo', 'indirecto'],
    ];

    $response = $this->putJson("/requisitions/{$this->requisition->id}", $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Operation successfully completed',
        ]);

    $this->assertDatabaseHas('purchases_requisitions', [
        'id'         => $this->requisition->id,
        'applicant'  => 'Applicant Updated',
        'department' => 'Sistemas',
    ]);

    $this->assertDatabaseHas('requisition_products', [
        'id'          => $this->product->id,
        'description' => 'Updated Test Item 1',
        'quantity'    => '15kg',
    ]);

    $this->assertDatabaseHas('requisition_products', [
        'pr_id'       => $this->requisition->id,
        'description' => 'Newly Added Item 2',
        'quantity'    => '3pz',
    ]);
});

it('can check/process a requisition setting consecutive and purchase order', function () {
    $this->actingAs($this->user);

    $payload = [
        'id'             => $this->requisition->id,
        'consecutive'    => 'CONF-CONSEC-100',
        'purchase_order' => 'PO-TEST-999',
    ];

    $response = $this->postJson(route('requisitions.checkRequisition'), $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Operation successfully completed',
        ]);

    $this->assertDatabaseHas('purchases_requisitions', [
        'id'             => $this->requisition->id,
        'consecutive'    => 'CONF-CONSEC-100',
        'purchase_order' => 'PO-TEST-999',
    ]);
});

it('can delete a single product from requisition', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson(route('requisitions.deleteProductRequisition'), [
        'id' => $this->product->id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Product deleted',
        ]);

    $this->assertDatabaseMissing('requisition_products', [
        'id' => $this->product->id,
    ]);
});

it('returns 404 when deleting non existent product from requisition', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson(route('requisitions.deleteProductRequisition'), [
        'id' => 999999,
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Product not deleted',
        ]);
});

it('can delete a requisition and its related products', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson("/requisitions/{$this->requisition->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Requisition deleted',
        ]);

    $this->assertDatabaseMissing('purchases_requisitions', [
        'id' => $this->requisition->id,
    ]);

    $this->assertDatabaseMissing('requisition_products', [
        'id' => $this->product->id,
    ]);
});

it('returns 404 when deleting non existent requisition', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson('/requisitions/999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Requisition not deleted',
        ]);
});

it('can retrieve purchase order folios', function () {
    $this->actingAs($this->user);

    $response = $this->getJson(route('requisitions.getPurchaseOrderFolios'));

    $response->assertStatus(200);
});

it('can retrieve comparative folios and comparative products', function () {
    $this->actingAs($this->user);

    $comparative = Comparative::create([
        'folio'        => 'COMP-' . rand(1000, 9999),
        'user_id'      => $this->user->id,
        'insumo'       => 'Insumo Test',
        'cantidad'     => 5,
        'proveedor'    => 'Proveedor Test',
        'precio_unt'   => 10.00,
        'precio_total' => 50.00,
        'descripcion'  => 'Item de prueba',
        'comentarios'  => 'Comparativa de prueba',
    ]);

    $foliosResponse = $this->getJson('/get-comparative-folios');
    $foliosResponse->assertStatus(200);

    $productsResponse = $this->getJson("/get-comparative-products/{$comparative->id}");
    $productsResponse->assertStatus(200);

    $comparative->delete();
});

it('denies access when user lacks appropriate permissions', function () {
    $guestUser = User::factory()->create();
    $this->actingAs($guestUser);

    $responseGet = $this->getJson(route('requisitions.getRequisitions'));
    $responseGet->assertStatus(403);

    $responseYour = $this->getJson(route('requisitions.getYourRequisitions'));
    $responseYour->assertStatus(403);

    $responseStore = $this->postJson('/requisitions', [
        'applicant' => 'Unauthorized User',
    ]);
    $responseStore->assertStatus(403);

    $guestUser->delete();
});
