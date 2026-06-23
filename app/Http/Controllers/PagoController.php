<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PagoController extends Controller
{
  
    public function index()
{
    $facturas = DB::table('facturas')
        ->select('factura_id', 'folio_factura', 'empresa', 'total')
        ->orderByDesc('factura_id')
        ->get();

    return view('finance.pagos.index', compact('facturas'));
}
}
