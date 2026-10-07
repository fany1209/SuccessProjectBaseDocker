<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\YeastProduction\YeastProductionRepository;
use App\Http\Requests\YeastProduction\YeastProductionUpdateRequest;
use App\Http\Resources\YeastProduction\YeastProductionResource;
use App\Traits\UtilResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class YeastProductionController extends Controller
{
    protected UtilResponse $utilResponse;
    protected YeastProductionRepository $yeastRepo;

    public function __construct(UtilResponse $utilResponse, YeastProductionRepository $yeastRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->yeastRepo = $yeastRepo;
    }

    public function index(Request $request): View|AnonymousResourceCollection|JsonResponse
    {
        try {
            $yeastProductions = $this->yeastRepo->getAllProductions();
            $pallets = $this->yeastRepo->getAllPallets();

            if ($request->ajax() || $request->wantsJson()) {
                return YeastProductionResource::collection($yeastProductions);
            }

            return view('production.yeast.index', compact('yeastProductions', 'pallets'));
        } catch (\Throwable $e) {
            Log::error('Error al listar producción de levadura', [
                'action' => 'index',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al consultar producción de levadura.', 500);
            }

            return back()->withErrors('Error al consultar producción de levadura.');
        }
    }

    public function sendToInventory(Request $request, $id): JsonResponse
    {
        try {
            $this->yeastRepo->sendPalletToInventory((int) $id);

            return response()->json([
                'success' => true,
                'message' => 'Tarima enviada al almacén correctamente.',
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tarima no encontrada.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al enviar tarima a inventario', [
                'action' => 'sendToInventory',
                'id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al enviar tarima a almacén.', 500);
        }
    }

    public function update(YeastProductionUpdateRequest $request, $id): JsonResponse
    {
        try {
            $updated = $this->yeastRepo->updateProduction((int) $id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Registro de producción de levadura actualizado correctamente.',
                'data' => new YeastProductionResource($updated),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registro de producción no encontrado.',
            ], 404);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar registro de producción de levadura', [
                'action' => 'update',
                'id' => $id,
                'user_id' => auth()->id(),
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al actualizar registro de producción.', 500);
        }
    }
}
