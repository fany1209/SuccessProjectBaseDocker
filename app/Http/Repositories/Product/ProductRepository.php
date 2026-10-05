<?php

namespace App\Http\Repositories\Product;

use App\Models\Category;
use App\Models\File;
use App\Models\Image;
use App\Models\Product;
use App\Models\Sector;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductRepository
{
    protected Product $product;
    protected Category $category;
    protected Sector $sector;
    protected Image $image;
    protected File $file;

    public function __construct(
        Product $product,
        Category $category,
        Sector $sector,
        Image $image,
        File $file
    ) {
        $this->product = $product;
        $this->category = $category;
        $this->sector = $sector;
        $this->image = $image;
        $this->file = $file;
    }

    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');

        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    protected function deletePhysicalFile(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $candidates = [
            $this->getPublicHtmlPath('storage/' . $path),
            $this->getPublicHtmlPath($path),
            $this->getPublicHtmlPath('products/' . basename($path)),
            $this->getPublicHtmlPath('files/' . basename($path)),
        ];

        foreach ($candidates as $file) {
            if (file_exists($file) && is_file($file)) {
                @unlink($file);
            }
        }
    }

    public function getIndexData(): array
    {
        return [
            'categories'     => $this->category->orderBy('name', 'asc')->get(),
            'sectors'        => $this->sector->orderBy('name', 'asc')->get(),
            'total_products' => $this->product->count(),
        ];
    }

    public function getProducts(array $filters = []): Collection
    {
        $category = $filters['category'] ?? null;
        $search = $filters['search'] ?? null;

        $query = DB::table('products')
            ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
            ->leftJoin('images', 'images.product_id', '=', 'products.product_id')
            ->leftJoin('files', 'files.product_id', '=', 'products.product_id')
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

        if (!empty($search)) {
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

        $user = auth()->user();
        $canUpdate = $user ? $user->can('products.update') : false;
        $canDelete = $user ? $user->can('products.delete') : false;

        $products = $query->get();

        return $products->map(function ($row) use ($canUpdate, $canDelete) {
            return [
                'product_id' => $row->product_id,
                'category'   => $row->category,
                'name'       => $row->name,
                'sat_code'   => $row->sat_code,
                'sku'        => $row->sku,
                'img'        => $row->img,
                'file'       => $row->file,
                'canUpdate'  => $canUpdate,
                'canDelete'  => $canDelete,
            ];
        });
    }

    public function findWithMedia(int $id): ?array
    {
        $product = $this->product->where('product_id', $id)->first();
        if (!$product) {
            return null;
        }

        $images = $this->image->where('product_id', $id)->get();
        $files = $this->file->where('product_id', $id)->get();

        return [
            'product' => $product,
            'images'  => $images,
            'files'   => $files,
        ];
    }

    public function store(array $data, ?array $images = null, ?array $files = null, ?array $fileSectors = null): Product
    {
        return DB::transaction(function () use ($data, $images, $files, $fileSectors) {
            $product = $this->product->create([
                'sat_code'     => $data['sat_code'],
                'category_id'  => $data['category_id'],
                'name'         => $data['name'],
                'sku'          => $data['sku'],
                'presentation' => $data['presentation'] ?? null,
                'unit'         => $data['unit'] ?? null,
                'batch_code'   => $data['batch_code'] ?? null,
                'stock_min'    => $data['stock_min'] ?? null,
                'stock_max'    => $data['stock_max'] ?? null,
            ]);

            if (!empty($images)) {
                foreach ($images as $img) {
                    $slug = Str::slug(pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME));
                    $ext = $img->guessExtension() ?? 'jpg';
                    $imageName = time() . '_' . uniqid() . '_' . $slug . '.' . $ext;
                    $imagePath = $img->storeAs('products', $imageName, 'public');

                    $this->image->create([
                        'path'       => $imagePath,
                        'product_id' => $product->product_id,
                    ]);
                }
            }

            if (!empty($files)) {
                foreach ($files as $index => $f) {
                    $slug = Str::slug(pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME));
                    $ext = $f->guessExtension() ?? 'pdf';
                    $fileName = time() . '_' . uniqid() . '_' . $slug . '.' . $ext;
                    $filePath = $f->storeAs('files', $fileName, 'public');

                    $this->file->create([
                        'path'       => $filePath,
                        'product_id' => $product->product_id,
                        'sector'     => $fileSectors[$index] ?? null,
                    ]);
                }
            }

            return $product;
        });
    }

    public function update(int $id, array $data, ?array $images = null, ?array $files = null, ?array $fileSectors = null): bool
    {
        return DB::transaction(function () use ($id, $data, $images, $files, $fileSectors) {
            $product = $this->product->where('product_id', $id)->lockForUpdate()->first();
            if (!$product) {
                return false;
            }

            $product->update([
                'sat_code'     => $data['sat_code'],
                'category_id'  => $data['category_id'],
                'name'         => $data['name'],
                'sku'          => $data['sku'],
                'presentation' => $data['presentation'] ?? null,
                'unit'         => $data['unit'] ?? null,
                'batch_code'   => $data['batch_code'] ?? null,
                'stock_min'    => $data['stock_min'] ?? null,
                'stock_max'    => $data['stock_max'] ?? null,
            ]);

            if (!empty($images)) {
                foreach ($images as $img) {
                    $slug = Str::slug(pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME));
                    $ext = $img->guessExtension() ?? 'jpg';
                    $imageName = time() . '_' . uniqid() . '_' . $slug . '.' . $ext;
                    $imagePath = $img->storeAs('products', $imageName, 'public');

                    $this->image->create([
                        'path'       => $imagePath,
                        'product_id' => $product->product_id,
                    ]);
                }
            }

            if (!empty($files)) {
                foreach ($files as $index => $f) {
                    $slug = Str::slug(pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME));
                    $ext = $f->guessExtension() ?? 'pdf';
                    $fileName = time() . '_' . uniqid() . '_' . $slug . '.' . $ext;
                    $filePath = $f->storeAs('files', $fileName, 'public');

                    $this->file->create([
                        'path'       => $filePath,
                        'product_id' => $product->product_id,
                        'sector'     => $fileSectors[$index] ?? null,
                    ]);
                }
            }

            return true;
        });
    }

    public function hasRelatedRecords(int $productId): bool
    {
        $hasSales = DB::table('sale_detail')->where('product_id', $productId)->exists();
        $hasInventory = DB::table('inventory')->where('product_id', $productId)->exists();
        $hasOutputs = DB::table('product_outputs')->where('product_id', $productId)->exists();
        $hasInputs = DB::table('product_inputs')->where('product_id', $productId)->exists();

        return $hasSales || $hasInventory || $hasOutputs || $hasInputs;
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $product = $this->product->where('product_id', $id)->lockForUpdate()->first();
            if (!$product) {
                return false;
            }

            if ($this->hasRelatedRecords($id)) {
                throw new DomainException('No se puede eliminar el producto porque tiene movimientos de inventario o ventas asociadas.');
            }

            $images = $this->image->where('product_id', $id)->get();
            foreach ($images as $img) {
                $this->deletePhysicalFile($img->path);
                $img->delete();
            }

            $files = $this->file->where('product_id', $id)->get();
            foreach ($files as $f) {
                $this->deletePhysicalFile($f->path);
                $f->delete();
            }

            return (bool) $product->delete();
        });
    }

    public function deleteImage(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $image = $this->image->where('image_id', $id)->lockForUpdate()->first();
            if (!$image) {
                return false;
            }

            $this->deletePhysicalFile($image->path);

            return (bool) $image->delete();
        });
    }

    public function deleteFile(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $file = $this->file->where('file_id', $id)->lockForUpdate()->first();
            if (!$file) {
                return false;
            }

            $this->deletePhysicalFile($file->path);

            return (bool) $file->delete();
        });
    }
}
