<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Supplier\SupplierRepository;
use App\Http\Requests\Supplier\SupplierStoreRequest;
use App\Http\Requests\Supplier\SupplierUpdateRequest;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    protected UtilResponse $utilResponse;
    protected SupplierRepository $supplierRepo;

    public function __construct(UtilResponse $utilResponse, SupplierRepository $supplierRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->supplierRepo = $supplierRepo;
    }

    public function index()
    {
        try {
            $data = $this->supplierRepo->getIndexData();
            return view('suppliers', $data);
        } catch (\Throwable $e) {
            Log::error('Error loading suppliers index', [
                'action'    => 'SupplierController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return back()->withErrors('Error al cargar la lista de proveedores.');
        }
    }

    public function getSuppliers(Request $request): JsonResponse
    {
        try {
            $suppliers = $this->supplierRepo->getSuppliers($request->only(['sector', 'search']));

            $user = auth()->user();
            $canUpdate = $user ? $user->can('suppliers.update') : false;
            $canDelete = $user ? $user->can('suppliers.delete') : false;

            $suppliersWithPermissions = $suppliers->map(function ($row) use ($canUpdate, $canDelete) {
                return [
                    'supplier_id' => $row->supplier_id,
                    'sector'      => $row->sector,
                    'code'        => $row->code,
                    'name'        => $row->name,
                    'contact'     => $row->contact,
                    'phone'       => $row->phone,
                    'email'       => $row->email,
                    'rfc'         => $row->rfc,
                    'address'     => $row->address,
                    'canUpdate'   => $canUpdate,
                    'canDelete'   => $canDelete,
                ];
            });

            return response()->json(['suppliers' => $suppliersWithPermissions]);
        } catch (\Throwable $e) {
            Log::error('Error in getSuppliers', [
                'action'    => 'SupplierController@getSuppliers',
                'exception' => $e->getMessage(),
            ]);
            return response()->json(['suppliers' => []], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $supplier = $this->supplierRepo->find($id);

            if (!$supplier) {
                return response()->json(['message' => 'Proveedor no encontrado'], 404);
            }

            return response()->json([
                'supplier' => $supplier->only([
                    'supplier_id',
                    'supplier_code',
                    'sector_id',
                    'name',
                    'contact',
                    'phone',
                    'email',
                    'rfc',
                    'postal_code',
                    'state',
                    'city',
                    'district',
                    'address',
                    'country',
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error fetching supplier', [
                'action'    => 'SupplierController@show',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Error al obtener el proveedor'], 500);
        }
    }

    public function store(SupplierStoreRequest $request): JsonResponse
    {
        try {
            $this->supplierRepo->create($request->validated());

            return response()->json(['message' => 'Operation successfully make it'], 201);
        } catch (\Throwable $e) {
            Log::error('Error storing supplier', [
                'action'    => 'SupplierController@store',
                'payload'   => $request->except(['password']),
                'exception' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Error al registrar el proveedor'], 500);
        }
    }

    public function update(SupplierUpdateRequest $request, $id = null): JsonResponse
    {
        try {
            $targetId = $id ?? $request->input('supplier_id');
            $data = $request->only([
                'sector_id',
                'name',
                'contact',
                'phone',
                'email',
                'rfc',
                'postal_code',
                'state',
                'city',
                'district',
                'address',
                'country',
            ]);

            $supplier = $this->supplierRepo->update($targetId, $data);

            if (!$supplier) {
                return response()->json(['message' => 'Proveedor no encontrado para actualizar'], 404);
            }

            return response()->json(['message' => 'Operation successfully make it'], 201);
        } catch (\Throwable $e) {
            Log::error('Error updating supplier', [
                'action'    => 'SupplierController@update',
                'id'        => $id,
                'payload'   => $request->except(['password']),
                'exception' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Error al actualizar el proveedor'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->supplierRepo->delete($id);

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Supplier deleted']);
            }

            return response()->json(['success' => false, 'message' => 'Supplier not deleted'], 404);
        } catch (\Throwable $e) {
            Log::error('Error deleting supplier', [
                'action'    => 'SupplierController@destroy',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Error al eliminar el proveedor'], 500);
        }
    }
}
