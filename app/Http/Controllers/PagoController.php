<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Pago\PagoRepository;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PagoController extends Controller
{
    protected UtilResponse $utilResponse;
    protected PagoRepository $pagoRepo;

    public function __construct(UtilResponse $utilResponse, PagoRepository $pagoRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->pagoRepo = $pagoRepo;
    }

    public function index(): View|JsonResponse
    {
        try {
            $facturas = $this->pagoRepo->getFacturasForPagos();

            return view('finance.pagos.index', compact('facturas'));
        } catch (Throwable $e) {
            Log::error('Error al cargar la programación de pagos', [
                'action'  => 'PagoController@index',
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return $this->utilResponse->errorResponse('Error al cargar la programación de pagos.', 500);
        }
    }
}
