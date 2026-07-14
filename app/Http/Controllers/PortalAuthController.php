<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

class PortalAuthController extends Controller
{
    public function index()
    {
        $user = Auth::guard('client')->user();
        
        $ventas = \DB::table('sales') 
                    ->where('customer_id', $user->customer_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('portal.dashboard', compact('ventas'));
    }

    public function dashboard()
    {
        $ventas = DB::table('sales')
            ->where('customer_id', Auth::guard('client')->user()->customer_id)
            ->get();

        return view('portal.dashboard', compact('ventas'));
    }

    public function verDetalle($id)
    {
        $user = Auth::guard('client')->user();

        if (!$user) {
            return redirect()->route('portal.login');
        }

        $venta = DB::table('sales')
            ->where('sale_id', $id)
            ->where('customer_id', $user->customer_id)
            ->first();
        
        if (!$venta) {
            abort(403, 'No tienes autorización para ver esta remisión.');
        }

        $articulos = DB::table('sale_detail')
            ->where('sale_id', $id)
            ->get();

        return view('portal.detalle', compact('venta', 'articulos'));
    }

    public function showLoginForm()
    {
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('client')->attempt(['email' => $request->email, 'password' => $request->password])) {
            
            if (Auth::guard('client')->user()->is_active) {
                $request->session()->regenerate();
                return redirect()->intended('/portal/dashboard');
            } else {
                Auth::guard('client')->logout();
                return back()->withErrors(['email' => 'Cuenta inactiva.']);
            }
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/portal-clientes');
    }

    public function descargarPdf($id)
    {
        $user = Auth::guard('client')->user();

        $venta = DB::table('sales')
            ->where('sale_id', $id)
            ->where('customer_id', $user->customer_id)
            ->first();

        if (!$venta) {
            abort(403, 'No tienes autorización para descargar este documento.');
        }

        return app(\App\Http\Controllers\PdfController::class)->makeDeliveryNotePDF($id);
    }
}