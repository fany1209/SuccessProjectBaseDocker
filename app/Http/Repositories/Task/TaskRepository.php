<?php

namespace App\Http\Repositories\Task;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TaskRepository
{
    protected Task $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    public function getTasksForUser(?User $user): Collection
    {
        $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('Admin');

        $query = $this->model->newQuery()->with(['responsable', 'creador']);

        if (!$isAdmin && $user) {
            $query->where('user_id', $user->id);
        }

        return $query->get();
    }

    public function getStatusCounts(Collection $tasks): array
    {
        return [
            'Pendientes'  => $tasks->where('status', 'pending')->count(),
            'En Proceso'  => $tasks->where('status', 'in_progress')->count(),
            'Completadas' => $tasks->where('status', 'completed')->count(),
        ];
    }

    public function getAllUsers(): Collection
    {
        return User::all();
    }

    public function find(int $id): ?Task
    {
        return $this->model->newQuery()->with(['responsable', 'creador'])->find($id);
    }

    public function create(array $data, int $adminId): Task
    {
        return DB::transaction(function () use ($data, $adminId) {
            $task = $this->model->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'admin_id' => $adminId,
                'user_id' => $data['user_id'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'] ?? null,
                'status' => 'pending',
            ]);

            $usuarioDestino = User::find($data['user_id']);
            if ($usuarioDestino) {
                $usuarioDestino->notify(new TaskNotification($task));
            }

            return $task->load(['responsable', 'creador']);
        });
    }

    public function update(int $id, array $data): Task
    {
        return DB::transaction(function () use ($id, $data) {
            $task = $this->model->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            $task->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'user_id' => $data['user_id'],
                'priority' => $data['priority'],
                'due_date' => $data['due_date'] ?? null,
            ]);

            return $task->fresh(['responsable', 'creador']);
        });
    }

    public function updateStatus(int $id, string $status): Task
    {
        return DB::transaction(function () use ($id, $status) {
            $task = $this->model->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            $updateData = ['status' => $status];
            if ($status === 'completed') {
                $updateData['completed_at'] = now();
            } else {
                $updateData['completed_at'] = null;
            }

            $task->update($updateData);

            return $task->fresh(['responsable', 'creador']);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $task = $this->model->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            return (bool) $task->delete();
        });
    }
}
