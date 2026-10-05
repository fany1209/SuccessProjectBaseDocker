<?php

namespace App\Http\Controllers;

use App\Http\Repositories\SupplierPrice\SupplierPriceRepository;
use App\Http\Requests\SupplierPrice\SupplierPriceStoreRequest;
use App\Http\Requests\SupplierPrice\SupplierPriceUpdateRequest;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierPriceController extends Controller
{
    protected UtilResponse $utilResponse;
    protected SupplierPriceRepository $priceRepo;

    public function __construct(UtilResponse $utilResponse, SupplierPriceRepository $priceRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->priceRepo = $priceRepo;
    }

    public function index()
    {
        try {
            $data = $this->priceRepo->getIndexData();
            return view('finance.precios.index', $data);
        } catch (\Throwable $e) {
            Log::error('Error loading supplier prices index', [
                'action'    => 'SupplierPriceController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return back()->withErrors('Error al cargar los precios de proveedores.');
        }
    }

    public function grafica(Request $request)
    {
        try {
            if ($request->ajax() || $request->wantsJson()) {
                $payload = $this->priceRepo->getGraficaData(
                    $request->input('insumo'),
                    $request->input('fecha_inicio'),
                    $request->input('fecha_fin')
                );
                return response()->json($payload);
            }

            $insumos = $this->priceRepo->getDistinctInsumos();
            return view('finance.precios.grafica', compact('insumos'));
        } catch (\Throwable $e) {
            Log::error('Error loading grafica data', [
                'action'    => 'SupplierPriceController@grafica',
                'exception' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['data' => [], 'analisis' => null], 500);
            }

            return back()->withErrors('Error al cargar la gráfica de precios.');
        }
    }

    public function store(SupplierPriceStoreRequest $request): JsonResponse
    {
        try {
            $this->priceRepo->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'El precio ha sido guardado exitosamente.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error storing supplier price', [
                'action'    => 'SupplierPriceController@store',
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al guardar el precio: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(SupplierPriceUpdateRequest $request, $id): JsonResponse
    {
        try {
            $updated = $this->priceRepo->update($id, $request->validated());

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Precio no encontrado para actualizar.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'El precio ha sido actualizado correctamente.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error updating supplier price', [
                'action'    => 'SupplierPriceController@update',
                'id'        => $id,
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al actualizar: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->priceRepo->delete($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo encontrar el registro para eliminar.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'El registro ha sido eliminado correctamente.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error deleting supplier price', [
                'action'    => 'SupplierPriceController@destroy',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el registro.',
            ], 500);
        }
    }
}