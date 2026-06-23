<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;


class ProductionController extends Controller
{
   public function index()
{
    $products = Product::all();

    return view('production', compact('products'));
}

}
