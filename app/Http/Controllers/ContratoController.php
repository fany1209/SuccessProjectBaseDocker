<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Contrato\ContratoRepository;
use App\Http\Requests\Contrato\UpdateContratoRequest;
use App\Http\Resources\Contrato\ContratoResource;
use App\Traits\UtilResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContratoController extends Controller
{
    private UtilResponse $utilResponse;
    private ContratoRepository $contratoRepository;

    public function __construct(UtilResponse $utilResponse, ContratoRepository $contratoRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->contratoRepository = $contratoRepository;
    }

    public function index(Request $request)
    {
        try {
            $trabajadores = $this->contratoRepository->getTrabajadoresActivos();
            $practicantes = $this->contratoRepository->getPracticantesActivos();

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse([
                    'trabajadores' => $trabajadores->map(fn ($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'tipo_empleado' => $u->tipo_empleado,
                        'contrato' => $u->contrato ? new ContratoResource($u->contrato) : null,
                    ]),
                    'practicantes' => $practicantes->map(fn ($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'tipo_empleado' => $u->tipo_empleado,
                        'contrato' => $u->contrato ? new ContratoResource($u->contrato) : null,
                    ]),
                ], 'Contratos de personal obtenidos exitosamente.');
            }

            return view('rh.contratos.index', compact('trabajadores', 'practicantes'));
        } catch (Throwable $e) {
            Log::error('Error fetching contratos', [
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al obtener contratos.', 500);
            }

            return redirect()->back()->with('error', 'Error al obtener contratos.');
        }
    }

    public function show(Request $request, $userId)
    {
        try {
            $contrato = $this->contratoRepository->findByUserId((int) $userId);

            if (!$contrato) {
                return $this->utilResponse->errorResponse('No se encontraron contratos para el trabajador especificado.', 404);
            }

            return $this->utilResponse->successResponse(
                new ContratoResource($contrato),
                'Contratos obtenidos exitosamente.'
            );
        } catch (Throwable $e) {
            Log::error('Error fetching single contrato', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar contratos.', 500);
        }
    }

    public function update(UpdateContratoRequest $request)
    {
        try {
            $contrato = $this->contratoRepository->uploadContratos(
                (int) $request->user_id,
                $request->allFiles()
            );

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ContratoResource($contrato),
                    'Contratos guardados correctamente.'
                );
            }

            return redirect()->back()->with('success', 'Contratos guardados correctamente.');
        } catch (Throwable $e) {
            Log::error('Error updating contratos', [
                'user_id' => $request->user_id,
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al guardar contratos.', 500);
            }

            return redirect()->back()->with('error', 'Ocurrió un error al guardar los contratos.');
        }
    }
}
