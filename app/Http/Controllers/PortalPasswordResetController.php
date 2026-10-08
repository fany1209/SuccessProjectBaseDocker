<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\PortalPasswordReset\PortalPasswordResetRepository;
use App\Http\Requests\PortalPasswordReset\PortalChangePasswordRequest;
use App\Http\Requests\PortalPasswordReset\PortalForgotPasswordRequest;
use App\Http\Requests\PortalPasswordReset\PortalResetPasswordRequest;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PortalPasswordResetController extends Controller
{
    protected UtilResponse $utilResponse;
    protected PortalPasswordResetRepository $passwordResetRepo;

    public function __construct(UtilResponse $utilResponse, PortalPasswordResetRepository $passwordResetRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->passwordResetRepo = $passwordResetRepo;
    }

    public function showForgotForm(): View
    {
        return view('portal.forgot-password');
    }

    public function sendResetLink(PortalForgotPasswordRequest $request): RedirectResponse|JsonResponse
    {
        $genericMessage = 'Si tu correo está registrado, recibirás un enlace para restablecer tu contraseña en los próximos minutos.';

        try {
            $this->passwordResetRepo->sendResetLink($request->input('email'));

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $genericMessage,
                ]);
            }

            return back()->with('status', $genericMessage);
        } catch (Throwable $e) {
            Log::error('Error al generar enlace de restablecimiento de contraseña en portal', [
                'action' => 'PortalPasswordResetController@sendResetLink',
                'email'  => $request->input('email'),
                'error'  => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $genericMessage,
                ]);
            }

            return back()->with('status', $genericMessage);
        }
    }

    public function showResetForm(Request $request, string $token): View|RedirectResponse
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('portal.password.request')
                ->withErrors(['email' => 'El enlace de restablecimiento no es válido.']);
        }

        if (!$this->passwordResetRepo->isTokenValid($email, $token)) {
            return redirect()->route('portal.password.request')
                ->withErrors(['email' => 'El enlace ha expirado o no es válido. Por favor, solicita uno nuevo.']);
        }

        return view('portal.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(PortalResetPasswordRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $success = $this->passwordResetRepo->resetPassword(
                $request->input('email'),
                $request->input('token'),
                $request->input('password')
            );

            if (!$success) {
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El enlace ha expirado o no es válido. Por favor, solicita uno nuevo.',
                    ], 422);
                }

                return back()->withErrors([
                    'email' => 'El enlace ha expirado o no es válido. Por favor, solicita uno nuevo.',
                ]);
            }

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Contraseña actualizada con éxito! Ya puedes iniciar sesión con tu nueva contraseña.',
                ]);
            }

            return redirect()->route('portal.login')
                ->with('status', '¡Contraseña actualizada con éxito! Ya puedes iniciar sesión con tu nueva contraseña.');
        } catch (Throwable $e) {
            Log::error('Error al procesar restablecimiento de contraseña en portal', [
                'action' => 'PortalPasswordResetController@resetPassword',
                'email'  => $request->input('email'),
                'error'  => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno al actualizar la contraseña.',
                ], 500);
            }

            return back()->withErrors(['email' => 'Error interno al actualizar la contraseña.']);
        }
    }

    public function changePassword(PortalChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = Auth::guard('client')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado.',
                ], 401);
            }

            $changed = $this->passwordResetRepo->changePassword(
                $user,
                $request->input('current_password'),
                $request->input('new_password')
            );

            if (!$changed) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual no es correcta.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => '¡Contraseña actualizada correctamente!',
            ]);
        } catch (Throwable $e) {
            Log::error('Error al cambiar contraseña de cliente en portal', [
                'action'  => 'PortalPasswordResetController@changePassword',
                'user_id' => Auth::guard('client')->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno al actualizar la contraseña.',
            ], 500);
        }
    }
}
