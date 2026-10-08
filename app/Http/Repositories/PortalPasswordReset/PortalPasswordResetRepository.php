<?php

namespace App\Http\Repositories\PortalPasswordReset;

use App\Models\PortalUser;
use App\Notifications\PortalPasswordResetNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PortalPasswordResetRepository
{
    protected int $tokenExpiration = 30;

    public function sendResetLink(string $email): bool
    {
        $user = PortalUser::where('email', $email)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return false;
        }

        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        DB::table('portal_password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token'      => $hashedToken,
                'created_at' => now(),
            ]
        );

        $user->notify(new PortalPasswordResetNotification($plainToken, $user->email));

        return true;
    }

    public function isTokenValid(string $email, string $plainToken): bool
    {
        $record = DB::table('portal_password_resets')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return false;
        }

        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes($this->tokenExpiration)->isPast()) {
            return false;
        }

        return hash_equals($record->token, hash('sha256', $plainToken));
    }

    public function resetPassword(string $email, string $plainToken, string $newPassword): bool
    {
        return DB::transaction(function () use ($email, $plainToken, $newPassword) {
            if (!$this->isTokenValid($email, $plainToken)) {
                return false;
            }

            $user = PortalUser::where('email', $email)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$user) {
                return false;
            }

            $user->password = Hash::make($newPassword);
            $user->save();

            DB::table('portal_password_resets')
                ->where('email', $email)
                ->delete();

            return true;
        });
    }

    public function changePassword(PortalUser $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        return DB::transaction(function () use ($user, $newPassword) {
            $lockedUser = PortalUser::lockForUpdate()->findOrFail($user->id);
            $lockedUser->password = Hash::make($newPassword);
            $lockedUser->save();

            return true;
        });
    }
}
