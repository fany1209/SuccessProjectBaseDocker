<?php
/*
Controlador
CategoryController
15/08/25
Stefany
*/

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $categories = Category::when($search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10);

        $open = false;
        $create = true;
        $category = null;

        return view('admin.categories.products', compact(
            'categories',
            'search',
            'open',
            'create',
            'category'
        ));
    }

    public function create(Request $request)
    {
        $search = $request->get('search');

        $categories = Category::when($search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10);

        $open = true;
        $create = true;
        $category = null;

        return view('admin.categories.products', compact(
            'categories',
            'search',
            'open',
            'create',
            'category'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create($request->only(['name']));

        return redirect()->route('admin.categories.products.index')->with('success', 'Category created successfully.');
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::orderBy('name')->paginate(10);
        $open = true;
        $create = false;

        return view('admin.categories.products', compact(
            'categories',
            'category',
            'open',
            'create'
        ));
    }

    
    public function edit($id, Request $request)
    {
        $category = Category::findOrFail($id);

        $search = $request->get('search');
        $categories = Category::when($search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10);

        $open = true;
        $create = false;

        return view('admin.categories.Products', compact(
            'categories',
            'search',
            'open',
            'create',
            'category'
        ));
    }

  
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->only(['name']));

        return redirect()->route('admin.categories.products.index')->with('success', 'Category updated successfully.');
    }


    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.products.index')->with('success', 'Category deleted successfully.');
    }
}
