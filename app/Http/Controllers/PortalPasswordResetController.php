<?php

namespace App\Http\Controllers;

use App\Models\PortalUser;
use App\Notifications\PortalPasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PortalPasswordResetController extends Controller
{
    /**
     * Tiempo de expiración del token en minutos.
     */
    protected int $tokenExpiration = 30;

    // -----------------------------------------------------------------------
    // STEP 1: Mostrar formulario "Olvidé mi contraseña"
    // -----------------------------------------------------------------------

    public function showForgotForm()
    {
        return view('portal.forgot-password');
    }

    // -----------------------------------------------------------------------
    // STEP 2: Recibir correo, generar token y enviar email
    // -----------------------------------------------------------------------

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Respuesta genérica siempre, para prevenir enumeración de usuarios.
        $successMessage = 'Si tu correo está registrado, recibirás un enlace para restablecer tu contraseña en los próximos minutos.';

        /** @var PortalUser|null $user */
        $user = PortalUser::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if ($user) {
            // Generar token seguro (SHA-256 sobre un string aleatorio de 64 chars)
            $plainToken = Str::random(64);
            $hashedToken = hash('sha256', $plainToken);

            // Guardar (o actualizar) el token en la tabla portal_password_resets
            DB::table('portal_password_resets')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token'      => $hashedToken,
                    'created_at' => now(),
                ]
            );

            // Enviar la notificación por correo
            $user->notify(new PortalPasswordResetNotification($plainToken, $user->email));
        }

        return back()->with('status', $successMessage);
    }

    // -----------------------------------------------------------------------
    // STEP 3: Mostrar formulario de nueva contraseña (valida token)
    // -----------------------------------------------------------------------

    public function showResetForm(Request $request, string $token)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('portal.password.request')
                ->withErrors(['email' => 'El enlace de restablecimiento no es válido.']);
        }

        // Verificar que el token exista y no haya expirado
        $record = DB::table('portal_password_resets')
            ->where('email', $email)
            ->first();

        if (!$record || !$this->isTokenValid($record, $token)) {
            return redirect()->route('portal.password.request')
                ->withErrors(['email' => 'El enlace ha expirado o no es válido. Por favor, solicita uno nuevo.']);
        }

        return view('portal.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    // -----------------------------------------------------------------------
    // STEP 4: Procesar la nueva contraseña
    // -----------------------------------------------------------------------

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        // Buscar el registro de reset
        $record = DB::table('portal_password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$record || !$this->isTokenValid($record, $request->token)) {
            return back()->withErrors([
                'email' => 'El enlace ha expirado o no es válido. Por favor, solicita uno nuevo.',
            ]);
        }

        // Buscar el portal_user y actualizar contraseña
        $user = PortalUser::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No se encontró una cuenta activa con ese correo.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Eliminar el token usado (one-time use)
        DB::table('portal_password_resets')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('portal.login')
            ->with('status', '¡Contraseña actualizada con éxito! Ya puedes iniciar sesión con tu nueva contraseña.');
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|string',
            'new_password'          => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        /** @var \App\Models\PortalUser $user */
        $user = \Illuminate\Support\Facades\Auth::guard('client')->user();

        // Verificar que la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual no es correcta.',
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '¡Contraseña actualizada correctamente!',
        ]);
    }

    private function isTokenValid(object $record, string $plainToken): bool
    {
        // Verificar que no ha expirado
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes($this->tokenExpiration)->isPast()) {
            return false;
        }

        // Verificar que el hash coincide
        return hash_equals($record->token, hash('sha256', $plainToken));
    }
}
