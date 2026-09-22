<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Complaint\ComplaintRepository;
use App\Http\Requests\Complaint\StoreComplaintRequest;
use App\Http\Resources\Complaint\ComplaintResource;
use App\Models\Complaint;
use App\Traits\UtilResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ComplaintController extends Controller
{
    private UtilResponse $utilResponse;
    private ComplaintRepository $complaintRepository;

    public function __construct(UtilResponse $utilResponse, ComplaintRepository $complaintRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->complaintRepository = $complaintRepository;
    }

    public function create(Request $request)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->utilResponse->successResponse([
                'tipos' => ['peticion', 'queja', 'reclamo', 'sugerencia', 'felicitacion', 'denuncia'],
                'motivos' => StoreComplaintRequest::ALLOWED_MOTIVOS,
            ], 'Opciones del formulario de buzón de experiencias.');
        }

        return view('complaints.create');
    }

    public function store(StoreComplaintRequest $request)
    {
        try {
            $complaint = $this->complaintRepository->create($request->validated());

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->successResponse(
                    new ComplaintResource($complaint),
                    '¡Tu experiencia se envió correctamente! Gracias por ayudarnos a mejorar.',
                    201
                );
            }

            return redirect()
                ->route('complaints.create')
                ->with('ok', '¡Tu experiencia se envió correctamente! Gracias por ayudarnos a mejorar.');
        } catch (Throwable $e) {
            Log::error('Error storing complaint', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->utilResponse->errorResponse('Ocurrió un error al guardar tu experiencia. Intenta nuevamente.', 500);
            }

            return back()
                ->withErrors('Ocurrió un error al guardar tu experiencia. Intenta nuevamente.')
                ->withInput();
        }
    }

    public function destroy(Request $request, Complaint $complaint)
    {
        try {
            $this->complaintRepository->delete($complaint->id);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(null, 'Registro eliminado.');
            }

            return back()->with('success', 'Registro eliminado.');
        } catch (Throwable $e) {
            Log::error('Error deleting complaint', [
                'id' => $complaint->id,
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al eliminar registro.', 500);
            }

            return back()->with('error', 'Error al eliminar el registro.');
        }
    }
}
