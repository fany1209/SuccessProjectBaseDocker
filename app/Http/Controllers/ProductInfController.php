<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ProductInf\ProductInfRepository;
use App\Http\Requests\ProductInf\ProductInfRequest;
use App\Http\Resources\ProductInf\ProductQrResource;
use App\Traits\UtilResponse;
use F9WebLtd\QrCode\Facades\QrCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProductInfController extends Controller
{
    protected UtilResponse $utilResponse;
    protected ProductInfRepository $productInfRepository;

    public function __construct(UtilResponse $utilResponse, ProductInfRepository $productInfRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->productInfRepository = $productInfRepository;
    }

    public function index(Request $request): View|JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        if (!$user || (!method_exists($user, 'hasRole') || (!$user->hasRole(['Admin', 'Warehouse']) && !$user->can('product_inf.show')))) {
            abort(403, 'No tienes permisos para acceder al módulo Product Inf.');
        }

        try {
            $search = $request->filled('search') ? strip_tags(trim((string) $request->input('search'))) : null;
            $qrs = $this->productInfRepository->getAll($search);
            $mapData = $this->productInfRepository->getWarehouseMapData();

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->successResponse(
                    ProductQrResource::collection($qrs),
                    'Registros de Product Inf recuperados con éxito'
                );
            }

            return view('product_inf.index', [
                'qrs'                 => $qrs,
                'warehouses'          => $mapData['warehouses'],
                'available_locations' => $mapData['available_locations'],
                'search'              => $search,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cargar la vista de Product Inf', [
                'action'    => 'ProductInfController@index',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al cargar los registros de Product Inf.', 500);
            }

            return back()->withErrors('Error al cargar la información de Product Inf.');
        }
    }

    public function locationProducts(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user || (!method_exists($user, 'hasRole') || (!$user->hasRole(['Admin', 'Warehouse']) && !$user->can('product_inf.show')))) {
                return $this->utilResponse->errorResponse('No autorizado para consultar productos de almacén.', 403);
            }

            $location = strip_tags(trim((string) $request->input('location')));
            if (empty($location)) {
                return $this->utilResponse->errorResponse('La ubicación especificada es inválida.', 422);
            }

            $products = $this->productInfRepository->getLocationProducts($location);

            return response()->json([
                'success'  => true,
                'flag'     => true,
                'products' => $products,
                'data'     => $products,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al consultar productos de ubicación en Product Inf', [
                'action'    => 'ProductInfController@locationProducts',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar los productos de la ubicación.', 500);
        }
    }

    public function store(ProductInfRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $userId = (int) auth()->id();

            $qrRecord = $this->productInfRepository->createOrUpdateQr($validated, $userId);

            return $this->utilResponse->successResponse(
                new ProductQrResource($qrRecord),
                'Código QR generado y persistido correctamente con URL permanente.',
                201
            );
        } catch (\Throwable $e) {
            Log::error('Error al generar código QR en Product Inf', [
                'action'    => 'ProductInfController@store',
                'user_id'   => auth()->id(),
                'payload'   => $request->except(['_token']),
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al generar el código QR. Inténtelo nuevamente.', 500);
        }
    }

    public function show(string $uuid): JsonResponse
    {
        try {
            $cleanUuid = strip_tags(trim($uuid));
            $qrRecord = $this->productInfRepository->findByUuid($cleanUuid);

            if (!$qrRecord) {
                return $this->utilResponse->errorResponse('El código QR solicitado no existe o fue retirado.', 404);
            }

            return $this->utilResponse->successResponse(
                new ProductQrResource($qrRecord),
                'Información del código QR recuperada correctamente.'
            );
        } catch (\Throwable $e) {
            Log::error('Error al consultar detalle de código QR', [
                'action'    => 'ProductInfController@show',
                'uuid'      => $uuid,
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar la información del código QR.', 500);
        }
    }

    public function showPublic(string $uuid): View
    {
        try {
            $cleanUuid = strip_tags(trim($uuid));
            $qrRecord = $this->productInfRepository->findByUuid($cleanUuid);

            if (!$qrRecord) {
                return view('product_inf.not_found', [
                    'uuid' => $cleanUuid,
                ]);
            }

            // Datos en tiempo real
            $liveCli = $qrRecord->cli;
            $currentQuantity = $liveCli ? (float) $liveCli->quantity : (float) $qrRecord->quantity;
            $currentNetWeight = $liveCli ? (float) $liveCli->net_weight : (float) $qrRecord->net_weight;
            $currentWpu = $liveCli ? (float) $liveCli->weight_per_unit : (float) $qrRecord->weight_per_unit;
            $currentLocation = $liveCli && $liveCli->location ? $liveCli->location->name : ($qrRecord->location ? $qrRecord->location->name : 'N/A');
            $warehouseName = $liveCli && $liveCli->location && $liveCli->location->warehouse
                ? $liveCli->location->warehouse->name
                : ($qrRecord->location && $qrRecord->location->warehouse ? $qrRecord->location->warehouse->name : 'N/A');

            $scanUrl = url('/rack/' . $cleanUuid);
            $qrSvg = (string) QrCode::size(220)->generate($scanUrl);

            return view('product_inf.public_view', [
                'qr'               => $qrRecord,
                'qrSvg'            => $qrSvg,
                'scanUrl'          => $scanUrl,
                'currentQuantity'  => $currentQuantity,
                'currentNetWeight' => $currentNetWeight,
                'currentWpu'       => $currentWpu,
                'currentLocation'  => $currentLocation,
                'warehouseName'    => $warehouseName,
                'isActive'         => !is_null($liveCli),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al mostrar vista pública de QR', [
                'action'    => 'ProductInfController@showPublic',
                'uuid'      => $uuid,
                'exception' => $e->getMessage(),
            ]);

            return view('product_inf.not_found', [
                'uuid' => $uuid,
            ]);
        }
    }

    public function downloadSvg(string $uuid): Response
    {
        $cleanUuid = strip_tags(trim($uuid));
        $qrRecord = $this->productInfRepository->findByUuid($cleanUuid);

        $scanUrl = url('/rack/' . $cleanUuid);
        $svg = (string) QrCode::size(500)->generate($scanUrl);

        $filename = 'qr-' . ($qrRecord && $qrRecord->batch ? $qrRecord->batch . '-' : '') . substr($cleanUuid, 0, 8) . '.svg';

        return response($svg, 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, private',
        ]);
    }

    public function destroy(Request $request, string $uuid): JsonResponse
    {
        $user = $request->user();
        if (!$user || (!method_exists($user, 'hasRole') || (!$user->hasRole(['Admin', 'Warehouse']) && !$user->can('product_inf.show')))) {
            return $this->utilResponse->errorResponse('No tienes permisos para eliminar códigos QR.', 403);
        }

        try {
            $cleanUuid = strip_tags(trim($uuid));
            $deleted = $this->productInfRepository->deleteByUuid($cleanUuid);

            if (!$deleted) {
                return $this->utilResponse->errorResponse('El código QR que intentas eliminar no fue encontrado.', 404);
            }

            return $this->utilResponse->successResponse(
                ['uuid' => $cleanUuid],
                'El código QR y su URL asociada han sido eliminados correctamente.'
            );
        } catch (\Throwable $e) {
            Log::error('Error al eliminar código QR en Product Inf', [
                'action'    => 'ProductInfController@destroy',
                'user_id'   => auth()->id(),
                'uuid'      => $uuid,
                'exception' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error interno al eliminar el código QR.', 500);
        }
    }
}
