<?php

namespace App\Http\Repositories\Production;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductionRepository
{
    protected Product $productModel;

    public function __construct(Product $productModel)
    {
        $this->productModel = $productModel;
    }

    public function getAllProducts(): Collection
    {
        return $this->productModel->newQuery()
            ->with(['category', 'images'])
            ->orderBy('name', 'asc')
            ->get();
    }
}
