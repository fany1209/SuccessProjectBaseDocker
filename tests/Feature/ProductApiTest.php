<?php

use App\Models\Category;
use App\Models\File;
use App\Models\Image;
use App\Models\Product;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Storage::fake('public');

    $this->user = User::first() ?? User::factory()->create();

    Permission::findOrCreate('products.show', 'web');
    Permission::findOrCreate('products.update', 'web');
    Permission::findOrCreate('products.delete', 'web');

    $this->user->givePermissionTo([
        'products.show',
        'products.update',
        'products.delete',
    ]);

    $this->category = Category::first() ?? Category::create([
        'name' => 'Categoría Test ' . uniqid(),
    ]);

    $this->sector = Sector::first() ?? Sector::create([
        'name' => 'Industrial',
        'code' => 'IND',
    ]);

    $this->product = Product::create([
        'name'         => 'Producto Test ' . uniqid(),
        'category_id'  => $this->category->category_id,
        'sat_code'     => '10101501',
        'sku'          => 'SKU-' . rand(10000, 99999),
        'presentation' => 'Bolsa 25kg',
        'unit'         => 'kg',
        'batch_code'   => 'LOTE-001',
        'stock_min'    => 10,
        'stock_max'    => 100,
    ]);

    $this->image = Image::create([
        'path'       => 'products/sample.jpg',
        'product_id' => $this->product->product_id,
    ]);

    $this->file = File::create([
        'path'       => 'files/sample.pdf',
        'product_id' => $this->product->product_id,
        'sector'     => 'Industrial',
    ]);
});

afterEach(function () {
    if (isset($this->product)) {
        Image::where('product_id', $this->product->product_id)->delete();
        File::where('product_id', $this->product->product_id)->delete();
        Product::where('product_id', $this->product->product_id)->delete();
    }
});

it('renders the catalog index view with categories and sectors', function () {
    $this->actingAs($this->user);

    $response = $this->get('/catalogs');

    $response->assertStatus(200)
        ->assertViewIs('catalog')
        ->assertViewHas(['sectors', 'categories', 'total_products']);
});

it('returns json on index when requested via ajax', function () {
    $this->actingAs($this->user);

    $response = $this->getJson('/catalogs');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'flag'    => true,
        ])
        ->assertJsonStructure([
            'data' => [
                'categories',
                'sectors',
                'total_products',
            ],
        ]);
});

it('can list products with permissions and img/file indicators', function () {
    $this->actingAs($this->user);

    $response = $this->getJson(route('catalog.getProducts'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'products' => [
                '*' => [
                    'product_id',
                    'category',
                    'name',
                    'sat_code',
                    'sku',
                    'img',
                    'file',
                    'canUpdate',
                    'canDelete',
                ],
            ],
        ]);

    $products = $response->json('products');
    $found = collect($products)->firstWhere('product_id', $this->product->product_id);
    expect($found)->not->toBeNull()
        ->and($found['name'])->toBe($this->product->name)
        ->and($found['img'])->toBe('Si')
        ->and($found['file'])->toBe('Si')
        ->and($found['canUpdate'])->toBeTrue()
        ->and($found['canDelete'])->toBeTrue();
});

it('can filter products by category and search keyword', function () {
    $this->actingAs($this->user);

    $responseSearch = $this->getJson(route('catalog.getProducts', [
        'search' => $this->product->sku,
    ]));
    $responseSearch->assertStatus(200);
    expect(count($responseSearch->json('products')))->toBeGreaterThanOrEqual(1);

    $responseCategory = $this->getJson(route('catalog.getProducts', [
        'category' => $this->category->category_id,
    ]));
    $responseCategory->assertStatus(200);
    expect(count($responseCategory->json('products')))->toBeGreaterThanOrEqual(1);
});

it('can show product details with images and files', function () {
    $this->actingAs($this->user);

    $response = $this->getJson("/catalogs/{$this->product->product_id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'product' => [
                'product_id',
                'sat_code',
                'category_id',
                'name',
                'sku',
                'presentation',
                'unit',
                'batch_code',
                'stock_min',
                'stock_max',
            ],
            'images',
            'files',
            'data',
        ]);

    expect($response->json('product.product_id'))->toBe($this->product->product_id)
        ->and(count($response->json('images')))->toBe(1)
        ->and(count($response->json('files')))->toBe(1);
});

it('returns 404 when showing non existent product', function () {
    $this->actingAs($this->user);

    $response = $this->getJson('/catalogs/999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
        ]);
});

it('can store a new product with uploaded images and files', function () {
    $this->actingAs($this->user);

    $fakeImage = UploadedFile::fake()->image('producto_foto.jpg', 600, 600);
    $fakePdf = UploadedFile::fake()->create('ficha_tecnica.pdf', 500, 'application/pdf');

    $sku = 'SKU-NEW-' . rand(1000, 9999);
    $payload = [
        'name'         => '<b>Fertilizante Especial</b> <script>alert(1)</script>',
        'category_id'  => $this->category->category_id,
        'sat_code'     => '12345678',
        'sku'          => $sku,
        'presentation' => 'Saco 50kg',
        'unit'         => 'kg',
        'batch_code'   => 'L-99',
        'stock_min'    => 5,
        'stock_max'    => 50,
        'imgs'         => [$fakeImage],
        'files'        => [$fakePdf],
        'file_sectors' => ['Industrial'],
    ];

    $response = $this->postJson('/catalogs', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Operation successfully created',
        ]);

    $newId = $response->json('data.product_id');
    expect($newId)->not->toBeNull();

    $this->assertDatabaseHas('products', [
        'product_id' => $newId,
        'name'       => 'Fertilizante Especial alert(1)',
        'sku'        => $sku,
    ]);

    $this->assertDatabaseHas('images', [
        'product_id' => $newId,
    ]);

    $this->assertDatabaseHas('files', [
        'product_id' => $newId,
        'sector'     => 'Industrial',
    ]);

    Image::where('product_id', $newId)->delete();
    File::where('product_id', $newId)->delete();
    Product::where('product_id', $newId)->delete();
});

it('fails to store product when missing required fields or duplicate sku', function () {
    $this->actingAs($this->user);

    $responseMissing = $this->postJson('/catalogs', [
        'name' => '',
    ]);
    $responseMissing->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'category_id', 'sat_code', 'sku']);

    $responseDuplicate = $this->postJson('/catalogs', [
        'name'        => 'Duplicate SKU',
        'category_id' => $this->category->category_id,
        'sat_code'    => '10101501',
        'sku'         => $this->product->sku,
    ]);
    $responseDuplicate->assertStatus(422)
        ->assertJsonValidationErrors(['sku']);
});

it('can update an existing product and add more media files', function () {
    $this->actingAs($this->user);

    $fakeImage = UploadedFile::fake()->image('extra.png', 400, 400);

    $payload = [
        'product_id'   => $this->product->product_id,
        'name'         => 'Producto Actualizado',
        'category_id'  => $this->category->category_id,
        'sat_code'     => '99887766',
        'sku'          => $this->product->sku,
        'presentation' => 'Garrafa 20L',
        'unit'         => 'lt',
        'imgs'         => [$fakeImage],
    ];

    $response = $this->putJson("/catalogs/{$this->product->product_id}", $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Operation successfully updated',
        ]);

    $this->assertDatabaseHas('products', [
        'product_id'   => $this->product->product_id,
        'name'         => 'Producto Actualizado',
        'presentation' => 'Garrafa 20L',
        'sat_code'     => '99887766',
    ]);

    expect(Image::where('product_id', $this->product->product_id)->count())->toBe(2);
});

it('returns 404 when updating non existent product', function () {
    $this->actingAs($this->user);

    $payload = [
        'name'        => 'Inexistente',
        'category_id' => $this->category->category_id,
        'sat_code'    => '10101501',
        'sku'         => 'SKU-NON-EXISTENT',
    ];

    $response = $this->putJson('/catalogs/999999', $payload);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'flag'    => false,
        ]);
});

it('can delete a single image of a product', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson(route('catalog.deleteImage'), [
        'id' => $this->image->image_id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Image deleted',
        ]);

    $this->assertDatabaseMissing('images', [
        'image_id' => $this->image->image_id,
    ]);
});

it('returns 404 when deleting non existent image', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson(route('catalog.deleteImage'), [
        'id' => 999999,
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Image not deleted',
        ]);
});

it('can delete a single file of a product', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson(route('catalog.deleteFile'), [
        'id' => $this->file->file_id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'File deleted',
        ]);

    $this->assertDatabaseMissing('files', [
        'file_id' => $this->file->file_id,
    ]);
});

it('returns 404 when deleting non existent file', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson(route('catalog.deleteFile'), [
        'id' => 999999,
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'File not deleted',
        ]);
});

it('can delete a product and its associated media', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson("/catalogs/{$this->product->product_id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Product deleted',
        ]);

    $this->assertDatabaseMissing('products', [
        'product_id' => $this->product->product_id,
    ]);

    $this->assertDatabaseMissing('images', [
        'image_id' => $this->image->image_id,
    ]);

    $this->assertDatabaseMissing('files', [
        'file_id' => $this->file->file_id,
    ]);
});

it('returns 404 when deleting non existent product', function () {
    $this->actingAs($this->user);

    $response = $this->deleteJson('/catalogs/999999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Product not deleted',
        ]);
});

it('prevents deleting product with existing inventory records', function () {
    $this->actingAs($this->user);

    $invId = DB::table('inventory')->insertGetId([
        'product_id' => $this->product->product_id,
        'stock'      => 100,
        'batch'      => 'B-001',
        'bar_code'   => '1234567890',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->deleteJson("/catalogs/{$this->product->product_id}");

    $response->assertStatus(409)
        ->assertJson([
            'success' => false,
            'flag'    => false,
        ]);

    DB::table('inventory')->where('inventory_id', $invId)->delete();
});

it('denies access to unauthorized user', function () {
    $guest = User::factory()->create();
    $this->actingAs($guest);

    $response = $this->get('/catalogs');
    $response->assertStatus(403);

    $responseJson = $this->getJson(route('catalog.getProducts'));
    $responseJson->assertStatus(403);

    $guest->delete();
});
