<?php

namespace App\Http\Repositories\Profile;

use App\Helpers\DeviceAgent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileRepository
{
    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = is_dir(base_path('../public_html')) ? base_path('../public_html') : public_path();
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    protected function saveFile(UploadedFile $file, string $folder = 'profile-photos'): string
    {
        $path = $file->store($folder, 'public');

        if (is_dir(base_path('../public_html'))) {
            $destDir = $this->getPublicHtmlPath('storage/' . $folder);
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $storageSource = storage_path('app/public/' . $path);
            $destFile = $this->getPublicHtmlPath('storage/' . $path);
            if (file_exists($storageSource) && !file_exists($destFile)) {
                @copy($storageSource, $destFile);
            }
        }

        return $path;
    }

    protected function removeFile(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        Storage::disk('public')->delete($relativePath);

        $destFile = $this->getPublicHtmlPath('storage/' . $relativePath);
        if (file_exists($destFile)) {
            @unlink($destFile);
        }
    }

    public function getUserSessions(int $userId, string $currentSessionId): Collection
    {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                $agent = new DeviceAgent($session->user_agent ?? request()->header('User-Agent'));

                return [
                    'id'                 => $session->id,
                    'ip_address'         => $session->ip_address,
                    'is_current_device'  => $session->id === $currentSessionId,
                    'last_active'        => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'agent'              => [
                        'platform'   => $agent->platform(),
                        'browser'    => $agent->browser(),
                        'is_desktop' => $agent->isDesktop(),
                    ],
                ];
            });
    }

    public function updateInfo(User $user, array $data, ?UploadedFile $photo = null): User
    {
        return DB::transaction(function () use ($user, $data, $photo) {
            $lockedUser = User::lockForUpdate()->findOrFail($user->id);

            $lockedUser->name = $data['name'];
            $lockedUser->email = $data['email'];

            if ($photo) {
                if ($lockedUser->profile_photo_path) {
                    $this->removeFile($lockedUser->profile_photo_path);
                }
                $lockedUser->profile_photo_path = $this->saveFile($photo, 'profile-photos');
            }

            $lockedUser->save();

            return $lockedUser;
        });
    }

    public function verifyPassword(User $user, string $plainPassword): bool
    {
        return Hash::check($plainPassword, $user->password);
    }

    public function updatePassword(User $user, string $newPassword): bool
    {
        return DB::transaction(function () use ($user, $newPassword) {
            $lockedUser = User::lockForUpdate()->findOrFail($user->id);
            $lockedUser->password = Hash::make($newPassword);

            return $lockedUser->save();
        });
    }

    public function logoutOtherSessions(int $userId, string $currentSessionId): void
    {
        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }

    public function removePhoto(User $user): void
    {
        DB::transaction(function () use ($user) {
            $lockedUser = User::lockForUpdate()->findOrFail($user->id);

            if ($lockedUser->profile_photo_path) {
                $this->removeFile($lockedUser->profile_photo_path);
                $lockedUser->profile_photo_path = null;
                $lockedUser->save();
            }
        });
    }

    public function deleteUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $lockedUser = User::lockForUpdate()->findOrFail($user->id);

            if ($lockedUser->profile_photo_path) {
                $this->removeFile($lockedUser->profile_photo_path);
            }

            $lockedUser->delete();
        });
    }
}
