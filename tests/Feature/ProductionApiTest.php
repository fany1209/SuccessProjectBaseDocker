<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'user_prod_' . uniqid() . '@example.com',
    ]);
});

test('index renders production formats view for authenticated user', function () {
    $response = $this->actingAs($this->user)->get(route('production.index'));

    $response->assertStatus(200);
    $response->assertViewIs('production');
    $response->assertViewHas('products');
});

test('index returns json product collection when requested with json header', function () {
    $category = Category::first() ?? Category::create(['name' => 'Categoría Test ' . uniqid()]);
    Product::create([
        'category_id' => $category->category_id,
        'name' => 'Producto Producción Test ' . uniqid(),
        'sat_code' => '12345678',
        'sku' => 'PROD-' . rand(1000, 9999),
        'presentation' => 'Bulto 25kg',
        'unit' => 'KG',
        'stock_min' => 10,
        'stock_max' => 100,
    ]);

    $response = $this->actingAs($this->user)->getJson(route('production.index'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'product_id',
                'name',
                'sku',
            ],
        ],
    ]);
});

test('index redirects unauthenticated users to login', function () {
    $response = $this->get(route('production.index'));

    $response->assertRedirect(route('login'));
});
