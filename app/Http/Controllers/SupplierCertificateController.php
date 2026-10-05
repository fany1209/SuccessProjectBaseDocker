<?php

namespace App\Http\Controllers;

use App\Http\Repositories\SupplierCertificate\SupplierCertificateRepository;
use App\Http\Requests\SupplierCertificate\SupplierCertificateStoreRequest;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SupplierCertificateController extends Controller
{
    protected UtilResponse $utilResponse;
    protected SupplierCertificateRepository $certificateRepo;

    public function __construct(UtilResponse $utilResponse, SupplierCertificateRepository $certificateRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->certificateRepo = $certificateRepo;
    }

    public function index(): JsonResponse
    {
        try {
            $certificates = $this->certificateRepo->getAll();
            return response()->json(['certificates' => $certificates]);
        } catch (\Throwable $e) {
            Log::error('Error fetching supplier certificates', [
                'action'    => 'SupplierCertificateController@index',
                'exception' => $e->getMessage(),
            ]);
            return response()->json(['certificates' => []], 500);
        }
    }

    public function store(SupplierCertificateStoreRequest $request)
    {
        try {
            $this->certificateRepo->store($request->validated(), $request->file('file'));

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Certificado guardado correctamente.',
                ], 201);
            }

            return back()->with('success', 'Certificado guardado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error storing supplier certificate', [
                'action'    => 'SupplierCertificateController@store',
                'payload'   => $request->except(['file']),
                'exception' => $e->getMessage(),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar el certificado: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors('Error al guardar el certificado: ' . $e->getMessage());
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->certificateRepo->delete($id);

            if (!$deleted) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            return response()->json(['message' => 'Eliminado correctamente']);
        } catch (\Throwable $e) {
            Log::error('Error deleting supplier certificate', [
                'action'    => 'SupplierCertificateController@destroy',
                'id'        => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Error al eliminar el certificado'], 500);
        }
    }
}