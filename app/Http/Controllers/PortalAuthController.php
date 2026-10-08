<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\PortalAuth\PortalAuthRepository;
use App\Http\Requests\PortalAuth\PortalLoginRequest;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PortalAuthController extends Controller
{
    protected UtilResponse $utilResponse;
    protected PortalAuthRepository $portalAuthRepo;

    public function __construct(UtilResponse $utilResponse, PortalAuthRepository $portalAuthRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->portalAuthRepo = $portalAuthRepo;
    }

    public function showLoginForm(): View
    {
        return view('portal.login');
    }

    public function login(PortalLoginRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $credentials = [
                'email'    => $request->input('email'),
                'password' => $request->input('password'),
            ];

            if (Auth::guard('client')->attempt($credentials)) {
                $clientUser = Auth::guard('client')->user();

                if ($clientUser && $clientUser->is_active) {
                    $request->session()->regenerate();

                    if ($request->ajax() || $request->expectsJson()) {
                        return response()->json([
                            'success'  => true,
                            'message'  => 'Inicio de sesión exitoso.',
                            'redirect' => url('/portal/dashboard'),
                        ]);
                    }

                    return redirect()->intended('/portal/dashboard');
                }

                Auth::guard('client')->logout();

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cuenta inactiva.',
                    ], 403);
                }

                return back()->withErrors(['email' => 'Cuenta inactiva.']);
            }

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas.',
                ], 422);
            }

            return back()->withErrors(['email' => 'Credenciales incorrectas.']);
        } catch (Throwable $e) {
            Log::error('Error en autenticación de cliente de portal', [
                'action' => 'PortalAuthController@login',
                'email'  => $request->input('email'),
                'error'  => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar el inicio de sesión.',
                ], 500);
            }

            return back()->withErrors(['email' => 'Error al procesar el inicio de sesión.']);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/portal-clientes');
    }

    public function index(): View
    {
        $user = Auth::guard('client')->user();
        $ventas = $this->portalAuthRepo->getCustomerSales((int) $user->customer_id);

        return view('portal.dashboard', compact('ventas'));
    }

    public function dashboard(): View
    {
        return $this->index();
    }

    public function verDetalle($id): View|RedirectResponse
    {
        $user = Auth::guard('client')->user();
        if (!$user) {
            return redirect()->route('portal.login');
        }

        $detail = $this->portalAuthRepo->getSaleDetail((int) $id, (int) $user->customer_id);
        if (!$detail) {
            abort(403, 'No tienes autorización para ver esta remisión.');
        }

        return view('portal.detalle', [
            'venta'     => $detail['venta'],
            'articulos' => $detail['articulos'],
        ]);
    }

    public function descargarPdf($id)
    {
        $user = Auth::guard('client')->user();
        if (!$user || !$this->portalAuthRepo->isSaleOwnedByCustomer((int) $id, (int) $user->customer_id)) {
            abort(403, 'No tienes autorización para descargar este documento.');
        }

        return app(\App\Http\Controllers\PdfController::class)->makeDeliveryNotePDF((int) $id);
    }
}