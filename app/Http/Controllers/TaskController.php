<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Task\TaskRepository;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Http\Requests\Task\TaskUpdateStatusRequest;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TaskController extends Controller
{
    protected UtilResponse $utilResponse;
    protected TaskRepository $taskRepo;

    public function __construct(UtilResponse $utilResponse, TaskRepository $taskRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->taskRepo = $taskRepo;
    }

    public function index(Request $request): View|JsonResponse
    {
        try {
            $user = auth()->user();
            $isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('Admin');

            $tasks = $this->taskRepo->getTasksForUser($user);
            $statusCounts = $this->taskRepo->getStatusCounts($tasks);
            $users = $isAdmin ? $this->taskRepo->getAllUsers() : collect();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'tasks' => TaskResource::collection($tasks),
                    'statusCounts' => $statusCounts,
                ]);
            }

            return view('tasks.index', compact('tasks', 'users', 'statusCounts', 'isAdmin'));
        } catch (\Throwable $e) {
            Log::error('Error al consultar lista de tareas', [
                'action' => 'index',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al consultar tareas.', 500);
            }

            return back()->withErrors('Error al consultar tareas.');
        }
    }

    public function create(): View
    {
        $users = $this->taskRepo->getAllUsers();
        return view('tasks.create', compact('users'));
    }

    public function store(TaskStoreRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $task = $this->taskRepo->create($request->validated(), (int) auth()->id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'task' => new TaskResource($task),
                ], 201);
            }

            return redirect()->route('tasks.index')->with('success', 'Tarea asignada con éxito.');
        } catch (\Throwable $e) {
            Log::error('Error al crear tarea', [
                'action' => 'store',
                'user_id' => auth()->id(),
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al asignar tarea.', 500);
            }

            return back()->withErrors('Error al asignar tarea.');
        }
    }

    public function updateStatus(TaskUpdateStatusRequest $request, Task $task): JsonResponse
    {
        try {
            $updated = $this->taskRepo->updateStatus((int) $task->id, (string) $request->validated('status'));
            $completedAt = $updated->completed_at ? \Carbon\Carbon::parse($updated->completed_at)->format('d/m/Y H:i') : null;

            return response()->json([
                'success' => true,
                'completed_at' => $completedAt,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar estatus de tarea', [
                'action' => 'updateStatus',
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al actualizar estatus.'], 500);
        }
    }

    public function edit(Task $task): JsonResponse
    {
        return response()->json(new TaskResource($task->load(['responsable', 'creador'])));
    }

    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        try {
            $this->taskRepo->update((int) $task->id, $request->validated());

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar tarea', [
                'action' => 'update',
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al actualizar tarea.'], 500);
        }
    }

    public function destroy(Task $task): JsonResponse
    {
        $user = auth()->user();
        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('Admin')) {
            return response()->json(['error' => 'No tienes permisos para borrar tareas.'], 403);
        }

        try {
            $this->taskRepo->delete((int) $task->id);

            return response()->json(['success' => 'Tarea eliminada correctamente.']);
        } catch (\Throwable $e) {
            Log::error('Error al eliminar tarea', [
                'action' => 'destroy',
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al eliminar tarea.'], 500);
        }
    }
}