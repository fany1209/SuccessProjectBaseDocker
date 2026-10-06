<?php

namespace App\Http\Repositories\PerformanceNote;

use App\Models\PerformanceNote;
use App\Models\User;
use App\Notifications\PerformanceNoteNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\DB;

class PerformanceNoteRepository
{
    protected PerformanceNote $model;

    public function __construct(PerformanceNote $model)
    {
        $this->model = $model;
    }

    public function getNotesForUser(User $user): Collection
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $this->model->newQuery()
                ->with(['user', 'admin'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return $this->model->newQuery()
            ->with('admin')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllUsers(): Collection|BaseCollection
    {
        return User::all();
    }

    public function createNote(array $data, int $adminId): PerformanceNote
    {
        return DB::transaction(function () use ($data, $adminId) {
            $note = $this->model->create([
                'user_id' => $data['user_id'],
                'admin_id' => $adminId,
                'type' => $data['type'],
                'comments' => $data['comments'],
            ]);

            $destUser = User::find($data['user_id']);
            if ($destUser) {
                $destUser->notify(new PerformanceNoteNotification($note));
            }

            return $note;
        });
    }

    public function deleteNote(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $note = $this->model->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            return (bool) $note->delete();
        });
    }

    public function getUnreadNotifications(User $user)
    {
        return $user->unreadNotifications->where('type', 'App\Notifications\PerformanceNoteNotification');
    }

    public function markNotificationAsRead(User $user, string $notificationId): bool
    {
        $notification = $user->unreadNotifications->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }
}
