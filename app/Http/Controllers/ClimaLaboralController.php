<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ClimaLaboral\ClimaLaboralRepository;
use App\Http\Requests\ClimaLaboral\StoreClimaLaboralRequest;
use App\Http\Resources\ClimaLaboral\ClimaLaboralResource;
use App\Traits\UtilResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClimaLaboralController extends Controller
{
    private UtilResponse $utilResponse;
    private ClimaLaboralRepository $climaLaboralRepository;

    public function __construct(UtilResponse $utilResponse, ClimaLaboralRepository $climaLaboralRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->climaLaboralRepository = $climaLaboralRepository;
    }

    public function store(StoreClimaLaboralRequest $request)
    {
        try {
            $userId = Auth::id();

            if ($userId && $this->climaLaboralRepository->hasUserAnswered($userId)) {
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                    return $this->utilResponse->errorResponse('Ya has contestado la encuesta de clima laboral anteriormente.', 422);
                }
                return redirect()->back()->with('error', 'Ya has contestado la encuesta de clima laboral anteriormente.');
            }

            $data = $request->validated();
            $data['user_id'] = $userId;

            $record = $this->climaLaboralRepository->create($data);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    new ClimaLaboralResource($record),
                    'Encuesta guardada exitosamente.',
                    201
                );
            }

            return redirect()->back()->with('success', 'Encuesta guardada exitosamente.');
        } catch (Throwable $e) {
            Log::error('Error al registrar encuesta de clima laboral: ' . $e->getMessage(), [
                'action'    => 'ClimaLaboralController@store',
                'user_id'   => Auth::id(),
                'exception' => $e,
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al registrar la encuesta de clima laboral.', 500);
            }

            return redirect()->back()->with('error', 'Ocurrió un error inesperado al guardar la encuesta.');
        }
    }

    public function index(Request $request)
    {
        try {
            $resultados = $this->climaLaboralRepository->all();

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    ClimaLaboralResource::collection($resultados),
                    'Resultados de clima laboral obtenidos correctamente'
                );
            }

            return view('rh.clima_laboral_resultados', compact('resultados'));
        } catch (Throwable $e) {
            Log::error('Error al consultar resultados de clima laboral: ' . $e->getMessage(), [
                'action'    => 'ClimaLaboralController@index',
                'user_id'   => Auth::id(),
                'exception' => $e,
            ]);

            if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
                return $this->utilResponse->errorResponse('Error al consultar resultados de clima laboral.', 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar los resultados.');
        }
    }
}
