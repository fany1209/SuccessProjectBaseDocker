<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CuentasPorCobrarController extends Controller
{
    public function index()
    {
        return view('finance.cuentas_por_cobrar.index');
    }
}
