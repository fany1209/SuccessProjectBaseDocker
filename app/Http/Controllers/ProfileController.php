<?php
/*
profile
18/08/25
stefany
Actualizado por: Jacob
Fecha de actualización: 17-10-2025
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Detection\MobileDetect;
use App\Helpers\DeviceAgent;
use Carbon\Carbon;
use App\Models\User;

class ProfileController extends Controller
{
public function showProfile()
{
    $user = Auth::user();
    $sessions = DB::table('sessions')
        ->where('user_id', $user->id)
        ->orderBy('last_activity', 'desc')
        ->get()
        ->map(function ($session) {
            // Usar DeviceAgent, fallback al user agent actual si es null
            $agent = new DeviceAgent($session->user_agent ?? request()->header('User-Agent'));
            return [
                'id' => $session->id,
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'agent' => [
                    'platform' => $agent->platform(),      // Windows / Mac / Android / iOS / Linux
                    'browser' => $agent->browser(),        // Siempre 'Unknown'
                    'is_desktop' => $agent->isDesktop(),   // true/false
                ],
            ];
        });

    return view('profile.show', compact('user', 'sessions'));
}


    public function updateProfileInfo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'photo' => 'nullable|image|max:1024'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->save();

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated.');
    }

    public function logoutOtherSessions(Request $request)
{
    $user = Auth::user();

    // Verificar la contraseña
    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'The password you entered is incorrect.'
        ], 422);
    }

    // Eliminar todas las sesiones excepto la actual
    $currentSessionId = Session::getId();

    DB::table('sessions')
        ->where('user_id', $user->id)
        ->where('id', '!=', $currentSessionId)
        ->delete();

    return response()->json(['message' => 'Logged out from other sessions.']);
}

    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();
        }

        return response()->json(['message' => 'Profile photo removed.']);
    }

    public function deleteUser(Request $request)
    {
        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', 'Account deleted.');
    }
}
