<?php
/* producto- catalog
04/08/25
stefany
Actualizado por: Stefany
Fecha de actualización: 11-03-2026
*/
namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Concept;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Models\File;
use App\Models\Sector;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Timer\Timer;

class ProductController extends Controller
{
    public function index(){
        $categories = Category::all();
        $sectors = Sector::all();
        $total_products = Product::count();
        return view('catalog', compact('sectors','categories','total_products'));
    }

    public function getProducts(Request $request){
        $category = $request->input('category');
        $search = $request->input('search');
        $query = DB::table('products')
            ->leftJoin('categories','categories.category_id','=','products.category_id')
            ->leftJoin('images','images.product_id','=','products.product_id')
            ->leftJoin('files','files.product_id','=','products.product_id')
            ->select(
                'products.product_id',
                'categories.name as category',
                'products.sat_code',
                'products.name',
                'products.sku',
                DB::raw("if(images.path like '%products/%','Si','No') as img"),
                DB::raw("if(files.path like '%.pdf%','Si','No') as file")
            )
        ->distinct();
        if(!empty($query)){
            $query->where(function ($q) use ($search) {
                $q->where('categories.name', 'like', '%' . $search . '%')
                ->orWhere('products.sat_code', 'like', '%' . $search . '%')
                ->orWhere('products.name', 'like', '%' . $search . '%')
                ->orWhere('products.sku', 'like', '%' . $search . '%');
            });
        }
        if (!empty($category)) {
            $query->where('products.category_id', $category);
        }
        $query->orderBy('products.product_id', 'desc');
        $products = $query->get();
        $productsWithPermissions = $products->map(function ($query){
            return[
                'product_id' => $query->product_id,
                'category' => $query->category,
                'name' => $query->name,
                'sat_code' => $query->sat_code,
                'sku' => $query->sku,
                'img' => $query->img,
                'file' => $query->file,
                'canUpdate' => auth()->user()->can('products.update'),
                'canDelete' => auth()->user()->can('products.delete'),
            ];
        });
        return response()->json(['products'=>$productsWithPermissions]);
    }

    public function deleteFile(Request $request){
        $id = $request->id;
        $file = File::find($id);
        $path = $file->path;
        if (Storage::disk('public')->exists($path)){
            Storage::disk('public')->delete($path);
        }
        if($file) {
            $file->delete();
            return response()->json(['success' => true, 'message' => 'File deleted']);
        } else {
            return response()->json(['success' => false, 'message' => 'File not deleted'], 404);
        }
    }

    public function deleteImage(Request $request){
        $id = $request->id;
        $image = Image::find($id);
        $path = $image->path;
        if (Storage::disk('public')->exists($path)){
            Storage::disk('public')->delete($path);
        }
        if($image) {
            $image->delete();
            return response()->json(['success' => true, 'message' => 'Image deleted']);
        } else {
            return response()->json(['success' => false, 'message' => 'Image not deleted'], 404);
        }
    }

    public function destroy(Request $request){
        try{
            DB::transaction(function () use ($request){
                $id = $request->id;
                $product = Product::find($id);
                $images = Image::where('product_id',$id)->get();
                foreach($images as $image){
                    if (Storage::disk('public')->exists($image->path)){
                        Storage::disk('public')->delete($image->path);
                    }
                }
                $files = File::where('product_id',$id)->get();
                foreach($files as $file){
                    if (Storage::disk('public')->exists($file->path)){
                        Storage::disk('public')->delete($file->path);
                    }
                }
                if($product) {
                    $product->delete();
                    return response()->json(['success' => true, 'message' => 'Product deleted']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Product not deleted'], 404);
                }
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }

    public function show($id){
        $product = Product::where('product_id',$id)->first();
        $images = Image::where('product_id', $id)->get();
        $files = File::where('product_id', $id)->get();
        return response()->json(['files'=>$files,'images'=>$images,'product'=> $product->only(['product_id','sat_code','category_id','name','sku','presentation','unit','batch_code','stock_min','stock_max'])]);
    }

    public function update(StoreProductRequest $request) 
    {
        try {
            return DB::transaction(function () use ($request) {
                $images = $request->file('imgs');
                $files = $request->file('files');
                $sectors = $request->input('file_sectors');

                Product::where('product_id', $request->product_id)
                    ->update($request->only(['sat_code', 'category_id', 'name', 'sku', 'presentation', 'unit', 'batch_code', 'stock_min', 'stock_max']));

                if (isset($images)) {
                    foreach ($images as $image) {
                        $imageName = time() . $image->getClientOriginalName();
                        $imagePath = $image->storeAs('products', $imageName, 'public');
                        Image::create([
                            'path' => $imagePath,
                            'product_id' => $request->product_id
                        ]);
                    }
                }

                if (isset($files)) {
                    foreach ($files as $index => $file) {
                        $fileName = time() . $file->getClientOriginalName();
                        $filePath = $file->storeAs('files', $fileName, 'public');
                        
                        File::create([
                            'path' => $filePath,
                            'product_id' => $request->product_id,
                            'sector' => $sectors[$index] ?? null 
                        ]);
                    }
                }

                return response()->json(['message' => 'Operation successfuly updated'], 201);
            });
        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        }
    }

    public function store(StoreProductRequest $request) 
    {
        try {
            return DB::transaction(function () use ($request) {
                $images = $request->file('imgs');
                $files = $request->file('files');
                $sectors = $request->input('file_sectors'); 

                $product = Product::create($request->only(['sat_code', 'category_id', 'name', 'sku', 'presentation', 'unit', 'batch_code', 'stock_min', 'stock_max']));

                if (isset($images)) {
                    foreach ($images as $image) {
                        $imageName = time() . $image->getClientOriginalName();
                        $imagePath = $image->storeAs('products', $imageName, 'public');
                        Image::create([
                            'path' => $imagePath,
                            'product_id' => $product->product_id
                        ]);
                    }
                }

                if (isset($files)) {
                    foreach ($files as $index => $file) {
                        $fileName = time() . $file->getClientOriginalName();
                        $filePath = $file->storeAs('files', $fileName, 'public');
                        
                        File::create([
                            'path' => $filePath,
                            'product_id' => $product->product_id,
                            'sector' => $sectors[$index] ?? null 
                        ]);
                    }
                }

                return response()->json(['message' => 'Operation successfuly created'], 201);
            });
        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        }
    }
}
