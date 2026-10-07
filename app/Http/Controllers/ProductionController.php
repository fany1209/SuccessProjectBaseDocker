<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Production\ProductionRepository;
use App\Http\Resources\Product\ProductResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProductionController extends Controller
{
    protected UtilResponse $utilResponse;
    protected ProductionRepository $productionRepo;

    public function __construct(UtilResponse $utilResponse, ProductionRepository $productionRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->productionRepo = $productionRepo;
    }

    public function index(Request $request): View|AnonymousResourceCollection|JsonResponse
    {
        try {
            $products = $this->productionRepo->getAllProducts();

            if ($request->ajax() || $request->wantsJson()) {
                return ProductResource::collection($products);
            }

            return view('production', compact('products'));
        } catch (\Throwable $e) {
            Log::error('Error al consultar vista de producción', [
                'action' => 'index',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al consultar productos de producción.', 500);
            }

            return back()->withErrors('Error al consultar productos de producción.');
        }
    }
}
