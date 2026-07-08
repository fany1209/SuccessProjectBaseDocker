<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- Importante para usar las consultas

class PortalAuthController extends Controller
{
    // En app/Http/Controllers/PortalDashboardController.php

public function index()
{
    $user = Auth::guard('client')->user();
    
    // Asumiendo que tienes una tabla 'sales' o 'remisiones' 
    // relacionada con 'customers' mediante 'customer_id'
    $ventas = \DB::table('sales') 
                ->where('customer_id', $user->customer_id)
                ->orderBy('created_at', 'desc')
                ->get();

    return view('portal.dashboard', compact('ventas'));
}

public function dashboard()
    {
        $user = Auth::guard('client')->user();
        
        $ventas = DB::table('sales')
            ->leftJoin('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id') 
            ->select(
                'sales.sale_id', 
                'sales.folio', 
                'sales.date',    
                'sales.created_at',
                // Si has_tax es 1, multiplica cantidad * costo * 1.16. Si es 0, solo cantidad * costo.
                DB::raw('SUM(IF(sale_detail.has_tax = 1, (sale_detail.quantity * sale_detail.cost) * 1.16, sale_detail.quantity * sale_detail.cost)) as total_calculado') 
            )
            ->where('sales.customer_id', $user->customer_id)
            ->groupBy('sales.sale_id', 'sales.folio', 'sales.date', 'sales.created_at')
            ->orderBy('sales.date', 'desc')
            ->get();

        return view('portal.dashboard', compact('ventas'));
    }

    public function verDetalle($id)
    {
        $user = Auth::guard('client')->user();

        // Validamos usando 'sales' en plural
        $venta = DB::table('sales')->where('sale_id', $id)->where('customer_id', $user->customer_id)->first();
        
        if (!$venta) {
            abort(403, 'No tienes autorización para ver esta remisión.');
        }

        // Buscamos los artículos en 'sale_detail' en singular
        $articulos = DB::table('sale_detail')
            ->where('sale_id', $id)
            ->get();

        return view('portal.detalle', compact('venta', 'articulos'));
    }
    // Muestra el formulario de Login
    public function showLoginForm()
    {
        return view('portal.login');
    }

    // Procesa el inicio de sesión
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Intentamos autenticar con guard 'client'
        // NOTA: 'attempt' ya se encarga de verificar la contraseña cifrada
        if (Auth::guard('client')->attempt(['email' => $request->email, 'password' => $request->password])) {
            
            // Verificamos si el usuario que entró está activo
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

    // Cierra la sesión
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

    // SEGURIDAD: Buscamos la remisión SOLO si pertenece al cliente logueado
    $venta = DB::table('sales')
        ->where('sale_id', $id)
        ->where('customer_id', $user->customer_id)
        ->first();

    // Si no le pertenece, bloqueamos el acceso inmediatamente
    if (!$venta) {
        abort(403, 'No tienes autorización para descargar este documento.');
    }

    // Invocamos de manera segura tu método existente para generar e imprimir el PDF
    return app(\App\Http\Controllers\PdfController::class)->makeDeliveryNotePDF($id);
}
}