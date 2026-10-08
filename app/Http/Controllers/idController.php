<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Id\IdRepository;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class idController extends Controller
{
    protected UtilResponse $utilResponse;
    protected IdRepository $idRepo;

    public function __construct(UtilResponse $utilResponse, IdRepository $idRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->idRepo = $idRepo;
    }

    public function index(Request $request): Response|JsonResponse
    {
        try {
            $status = $this->idRepo->getModuleStatus();

            if ($request->expectsJson() || $request->ajax()) {
                return $this->utilResponse->successResponse(
                    $status,
                    'Módulo de Investigación y Desarrollo (I+D).'
                );
            }

            return response()->noContent();
        } catch (Throwable $e) {
            Log::error('Error en módulo I+D', [
                'action'  => 'idController@index',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al consultar el módulo I+D.', 500);
        }
    }
}
