<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Product\ProductRepository;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\Product\ProductResource;
use App\Traits\UtilResponse;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected UtilResponse $utilResponse;
    protected ProductRepository $productRepo;

    public function __construct(UtilResponse $utilResponse, ProductRepository $productRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->productRepo = $productRepo;
    }

    public function index(Request $request)
    {
        try {
            $data = $this->productRepo->getIndexData();

            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    $data,
                    'Catálogo de productos obtenido correctamente'
                );
            }

            return view('catalog', $data);
        } catch (\Throwable $e) {
            Log::error('Error loading product catalog index', [
                'action'    => 'ProductController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return back()->withErrors('Error al cargar el catálogo de productos.');
        }
    }

    public function getProducts(Request $request): JsonResponse
    {
        try {
            $products = $this->productRepo->getProducts($request->all());
            return response()->json(['products' => $products]);
        } catch (\Throwable $e) {
            Log::error('Error in ProductController@getProducts', [
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al consultar los productos.', 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $data = $this->productRepo->findWithMedia((int) $id);

            if (!$data) {
                return $this->utilResponse->errorResponse('Producto no encontrado', 404);
            }

            return response()->json([
                'product' => $data['product']->only([
                    'product_id', 'sat_code', 'category_id', 'name', 'sku',
                    'presentation', 'unit', 'batch_code', 'stock_min', 'stock_max',
                ]),
                'images'  => $data['images'],
                'files'   => $data['files'],
                'data'    => new ProductResource($data['product']),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in ProductController@show', [
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al obtener el producto.', 500);
        }
    }

    public function store(ProductStoreRequest $request): JsonResponse
    {
        try {
            $product = $this->productRepo->store(
                $request->validated(),
                $request->file('imgs'),
                $request->file('files'),
                $request->input('file_sectors')
            );

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfully created',
                'data'    => new ProductResource($product),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error in ProductController@store', [
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al registrar el producto.', 500);
        }
    }

    public function update(ProductUpdateRequest $request, $id = null): JsonResponse
    {
        try {
            $productId = (int) ($id ?? $request->input('product_id'));
            $updated = $this->productRepo->update(
                $productId,
                $request->validated(),
                $request->file('imgs'),
                $request->file('files'),
                $request->input('file_sectors')
            );

            if (!$updated) {
                return $this->utilResponse->errorResponse('Producto no encontrado', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfully updated',
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error in ProductController@update', [
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al actualizar el producto.', 500);
        }
    }

    public function destroy(Request $request, $id = null): JsonResponse
    {
        try {
            $productId = (int) ($id ?? $request->input('id'));
            $deleted = $this->productRepo->delete($productId);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Product not deleted',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Product deleted',
            ], 200);
        } catch (DomainException $e) {
            return $this->utilResponse->errorResponse($e->getMessage(), 409);
        } catch (\Throwable $e) {
            Log::error('Error in ProductController@destroy', [
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al eliminar el producto.', 500);
        }
    }

    public function deleteImage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id' => 'required|integer',
            ]);

            $deleted = $this->productRepo->deleteImage((int) $request->input('id'));

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Image not deleted',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Image deleted',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error in ProductController@deleteImage', [
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al eliminar la imagen.', 500);
        }
    }

    public function deleteFile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id' => 'required|integer',
            ]);

            $deleted = $this->productRepo->deleteFile((int) $request->input('id'));

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'File not deleted',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'File deleted',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error in ProductController@deleteFile', [
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al eliminar el archivo.', 500);
        }
    }
}
