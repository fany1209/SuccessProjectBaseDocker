<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\TaskAssignedEvent;
use App\Notifications\TaskNotification;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');

        $query = Task::with(['responsable', 'creador']);
        
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }
        
        $tasks = $query->get();

        $statusCounts = [
            'Pendientes'  => $tasks->where('status', 'pending')->count(),
            'En Proceso'  => $tasks->where('status', 'in_progress')->count(),
            'Completadas' => $tasks->where('status', 'completed')->count(),
        ];

        $users = $isAdmin ? User::all() : collect();

        return view('tasks.index', compact('tasks', 'users', 'statusCounts', 'isAdmin'));
    }

    public function create()
    {
        $users = User::all(); 
        return view('tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'admin_id' => auth()->id(),
            'user_id' => $request->user_id,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'status' => 'pending',
        ]);
        
        // broadcast(new TaskAssignedEvent($task))->toOthers();

        $usuarioDestino = User::find($request->user_id);
        if ($usuarioDestino) {
            $usuarioDestino->notify(new TaskNotification($task));
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'task' => $task]);
        }

        return redirect()->route('tasks.index')->with('success', 'Tarea asignada con éxito.');
    }

    public function updateStatus(Request $request, Task $task) 
    {
        $updateData = ['status' => $request->status];

        if ($request->status === 'completed') {
            $updateData['completed_at'] = now(); 
        } else {
            $updateData['completed_at'] = null;
        }

        $task->update($updateData);

        return response()->json([
            'success' => true,
            'completed_at' => $task->completed_at ? $task->completed_at->format('d/m/Y H:i') : null
        ]);
    }

    public function edit(Task $task)
    {
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $request->user_id,
            'priority' => $request->priority,
            'due_date'    => $request->due_date,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        if (!auth()->user()->hasRole('Admin')) {
            return response()->json(['error' => 'No tienes permisos para borrar tareas.'], 403);
        }

        $task->delete();

        return response()->json(['success' => 'Tarea eliminada correctamente.']);
    }
}