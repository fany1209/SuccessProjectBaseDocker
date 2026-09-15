<?php
/*
Controlador
ProductsController
15/08/25
Stefany
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
use App\Models\File;
use App\Models\Concept;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->paginate(10)->appends($request->except('page'));
        $categories = Category::all();

        $open = false;
        $create = false;

        return view('admin.products', compact('products', 'categories', 'open', 'create'));
    }


    public function create(Request $request)
    {
        $categories = Category::all();
        $open = true;
        $create = true;

        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(10)->appends($request->except('page'));

        return view('admin.products', compact('categories', 'products', 'open', 'create'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'sku' => 'nullable|string|max:100',
            'sat_code' => 'nullable|string|max:100',
            'stock_min' => 'nullable|numeric',
            'stock_max' => 'nullable|numeric',
            'presentation' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:100',
            'batch_code' => 'nullable|string|max:100',
            'images.*' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'files.*' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::create($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $ext = $image->guessExtension() ?: 'jpg';
                    $imageName = time() . '_' . Str::uuid() . '.' . $ext;
                    $path = $image->storeAs('products', $imageName, 'public');

                    Image::create([
                        'product_id' => $product->product_id,
                        'path' => $path,
                    ]);
                }
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . Str::uuid() . '.pdf';
                    $path = $file->storeAs('products/files', $fileName, 'public');

                    File::create([
                        'product_id' => $product->product_id,
                        'path' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error saving product: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'sku' => 'nullable|string|max:100',
            'sat_code' => 'nullable|string|max:100',
            'stock_min' => 'nullable|numeric',
            'stock_max' => 'nullable|numeric',
            'presentation' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:100',
            'batch_code' => 'nullable|string|max:100',
            'images.*' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'files.*' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $product->update($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $ext = $image->guessExtension() ?: 'jpg';
                    $imageName = time() . '_' . Str::uuid() . '.' . $ext;
                    $path = $image->storeAs('products', $imageName, 'public');

                    Image::create([
                        'product_id' => $product->product_id,
                        'path' => $path,
                    ]);
                }
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . Str::uuid() . '.pdf';
                    $path = $file->storeAs('products/files', $fileName, 'public');

                    File::create([
                        'product_id' => $product->product_id,
                        'path' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error updating product: ' . $e->getMessage())->withInput();
        }
    }

   public function edit($id)
    {
        $product = Product::with(['images', 'files'])->findOrFail($id);
        $categories = Category::all();
        $linked_images = $product->images;
        $linked_files = $product->files;
        $products = Product::with('category')->paginate(10); 
        $open = true;
        $create = false;

        return view('admin.products', compact('product', 'categories', 'linked_images', 'linked_files', 'products', 'open', 'create'));
    }

    public function destroy($id)
    {
        $product = Product::with(['images', 'files'])->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($product->images as $image) {
                Storage::delete($image->path);
                $image->delete();
            }
            foreach ($product->files as $file) {
                Storage::delete($file->path);
                $file->delete();
            }

            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error deleting product: ' . $e->getMessage());
        }
    }

    public function deleteImage($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return back()->withErrors('Identificador de imagen no válido.');
        }

        $image = Image::findOrFail($id);

        if ($image->path) {
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            } elseif (Storage::exists($image->path)) {
                Storage::delete($image->path);
            }

            // Soporte dual producción cPanel public_html
            $productionPath = base_path('../public_html/products/' . basename($image->path));
            if (file_exists($productionPath) && is_file($productionPath)) {
                @unlink($productionPath);
            }
        }

        $image->delete();

        return back()->with('success', 'Image deleted successfully.');
    }

    public function deleteFile($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            return back()->withErrors('Identificador de archivo no válido.');
        }

        $file = File::findOrFail($id);

        if ($file->path) {
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            } elseif (Storage::exists($file->path)) {
                Storage::delete($file->path);
            }

            // Soporte dual producción cPanel public_html
            $productionPath = base_path('../public_html/products/files/' . basename($file->path));
            if (file_exists($productionPath) && is_file($productionPath)) {
                @unlink($productionPath);
            }
        }

        $file->delete();

        return back()->with('success', 'File deleted successfully.');
    }
}
