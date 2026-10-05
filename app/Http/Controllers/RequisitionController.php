<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Requisition\RequisitionRepository;
use App\Http\Requests\Requisition\RequisitionCheckRequest;
use App\Http\Requests\Requisition\RequisitionStoreRequest;
use App\Http\Requests\Requisition\RequisitionUpdateRequest;
use App\Http\Resources\Requisition\RequisitionResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequisitionController extends Controller
{
    protected UtilResponse $utilResponse;
    protected RequisitionRepository $requisitionRepo;

    public function __construct(UtilResponse $utilResponse, RequisitionRepository $requisitionRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->requisitionRepo = $requisitionRepo;
    }

    public function getRequisitions(Request $request): JsonResponse
    {
        try {
            $requisitions = $this->requisitionRepo->getRequisitions($request->all());
            return response()->json(['requisitions' => $requisitions]);
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@getRequisitions', [
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al consultar las requisiciones.', 500);
        }
    }

    public function getYourRequisitions(): JsonResponse
    {
        try {
            $applicantName = auth()->user()?->name ?? '';
            $requisitions = $this->requisitionRepo->getYourRequisitions($applicantName);
            return response()->json(['requisitions' => $requisitions]);
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@getYourRequisitions', [
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al consultar tus requisiciones.', 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $data = $this->requisitionRepo->findWithProducts($id);
            if (!$data) {
                return $this->utilResponse->errorResponse('Requisición no encontrada', 404);
            }
            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@show', [
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al obtener la requisición.', 500);
        }
    }

    public function store(RequisitionStoreRequest $request): JsonResponse
    {
        try {
            $requisition = $this->requisitionRepo->store($request->validated());

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfully completed',
                'id'      => $requisition->id,
                'data'    => new RequisitionResource($requisition),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@store', [
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al registrar la requisición.', 500);
        }
    }

    public function update(RequisitionUpdateRequest $request, $id = null): JsonResponse
    {
        try {
            $reqId = (int) ($id ?? $request->input('req_id'));
            $updated = $this->requisitionRepo->update($reqId, $request->validated());

            if (!$updated) {
                return $this->utilResponse->errorResponse('Requisición no encontrada', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 201,
                'message' => 'Operation successfully completed',
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@update', [
                'id'        => $id,
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al actualizar la requisición.', 500);
        }
    }

    public function checkRequisition(RequisitionCheckRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $updated = $this->requisitionRepo->checkRequisition(
                (int) $validated['id'],
                $validated['consecutive'],
                $validated['purchase_order']
            );

            if (!$updated) {
                return $this->utilResponse->errorResponse('Requisición no encontrada para procesar.', 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Operation successfully completed',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@checkRequisition', [
                'user_id'   => auth()->id(),
                'payload'   => $request->validated(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al procesar la requisición.', 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->requisitionRepo->delete((int) $id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'flag'    => false,
                    'code'    => 404,
                    'message' => 'Requisition not deleted',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'flag'    => true,
                'code'    => 200,
                'message' => 'Requisition deleted',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@destroy', [
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al eliminar la requisición.', 500);
        }
    }

    public function deleteProductRequisition(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id' => 'required|integer',
            ]);

            $deleted = $this->requisitionRepo->deleteProduct((int) $request->input('id'));

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
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@deleteProductRequisition', [
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);
            return $this->utilResponse->errorResponse('Error al eliminar el producto de la requisición.', 500);
        }
    }

    public function getPurchaseOrderFolios(): JsonResponse
    {
        try {
            $folios = $this->requisitionRepo->getPurchaseOrderFolios();
            return response()->json($folios);
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@getPurchaseOrderFolios', [
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return response()->json([], 500);
        }
    }

    public function getComparativeFolios(): JsonResponse
    {
        try {
            $folios = $this->requisitionRepo->getComparativeFolios((int) (auth()->id() ?? 0));
            return response()->json($folios);
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@getComparativeFolios', [
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return response()->json([], 500);
        }
    }

    public function getComparativeProducts($id): JsonResponse
    {
        try {
            $products = $this->requisitionRepo->getComparativeProducts((int) $id);
            return response()->json($products);
        } catch (\Throwable $e) {
            Log::error('Error in RequisitionController@getComparativeProducts', [
                'id'        => $id,
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return response()->json([], 500);
        }
    }
}