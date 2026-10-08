<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Profile\ProfileRepository;
use App\Http\Requests\Profile\ProfileLogoutSessionsRequest;
use App\Http\Requests\Profile\ProfileUpdateInfoRequest;
use App\Http\Requests\Profile\ProfileUpdatePasswordRequest;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    protected UtilResponse $utilResponse;
    protected ProfileRepository $profileRepo;

    public function __construct(UtilResponse $utilResponse, ProfileRepository $profileRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->profileRepo = $profileRepo;
    }

    public function showProfile(): View
    {
        $user = Auth::user();
        $sessions = $this->profileRepo->getUserSessions((int) $user->id, (string) Session::getId());

        return view('profile.show', compact('user', 'sessions'));
    }

    public function updateProfileInfo(ProfileUpdateInfoRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $user = Auth::user();
            $updatedUser = $this->profileRepo->updateInfo($user, $request->validated(), $request->file('photo'));

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated.',
                    'data'    => $updatedUser,
                ]);
            }

            return back()->with('success', 'Profile updated.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar información de perfil', [
                'action'  => 'ProfileController@updateProfileInfo',
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el perfil.',
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar el perfil.');
        }
    }

    public function updatePassword(ProfileUpdatePasswordRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$this->profileRepo->verifyPassword($user, $request->input('current_password'))) {
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect.',
                    ], 422);
                }

                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $this->profileRepo->updatePassword($user, $request->input('password'));

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password updated.',
                ]);
            }

            return back()->with('success', 'Password updated.');
        } catch (Throwable $e) {
            Log::error('Error al actualizar contraseña de usuario', [
                'action'  => 'ProfileController@updatePassword',
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la contraseña.',
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al actualizar la contraseña.');
        }
    }

    public function logoutOtherSessions(ProfileLogoutSessionsRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$this->profileRepo->verifyPassword($user, $request->input('password'))) {
                return response()->json([
                    'message' => 'The password you entered is incorrect.',
                ], 422);
            }

            $this->profileRepo->logoutOtherSessions((int) $user->id, (string) Session::getId());

            return response()->json(['message' => 'Logged out from other sessions.']);
        } catch (Throwable $e) {
            Log::error('Error al cerrar otras sesiones de usuario', [
                'action'  => 'ProfileController@logoutOtherSessions',
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Error al cerrar las otras sesiones.'], 500);
        }
    }

    public function removePhoto(): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->profileRepo->removePhoto($user);

            return response()->json(['message' => 'Profile photo removed.']);
        } catch (Throwable $e) {
            Log::error('Error al eliminar foto de perfil', [
                'action'  => 'ProfileController@removePhoto',
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Error al eliminar la foto.'], 500);
        }
    }

    public function deleteUser(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $user = Auth::user();
            if (method_exists(Auth::guard(), 'logout')) {
                Auth::guard()->logout();
            }
            $this->profileRepo->deleteUser($user);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Account deleted.',
                ]);
            }

            return redirect('/')->with('success', 'Account deleted.');
        } catch (Throwable $e) {
            Log::error('Error al eliminar cuenta de usuario', [
                'action'  => 'ProfileController@deleteUser',
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            return redirect('/')->with('error', 'Error al eliminar la cuenta.');
        }
    }
}
