<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\PerformanceNote\PerformanceNoteRepository;
use App\Http\Requests\PerformanceNote\PerformanceNoteStoreRequest;
use App\Http\Resources\PerformanceNote\PerformanceNoteResource;
use App\Traits\UtilResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PerformanceNoteController extends Controller
{
    protected UtilResponse $utilResponse;
    protected PerformanceNoteRepository $noteRepository;

    public function __construct(UtilResponse $utilResponse, PerformanceNoteRepository $noteRepository)
    {
        $this->utilResponse = $utilResponse;
        $this->noteRepository = $noteRepository;
    }

    public function index(Request $request): View|AnonymousResourceCollection|JsonResponse|RedirectResponse
    {
        try {
            $user = auth()->user();
            $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
            $notes = $this->noteRepository->getNotesForUser($user);

            if ($request->ajax() || $request->wantsJson()) {
                return PerformanceNoteResource::collection($notes);
            }

            $users = $isAdmin ? $this->noteRepository->getAllUsers() : collect();

            return view('performance_notes.index', compact('notes', 'users', 'isAdmin'));
        } catch (\Throwable $e) {
            Log::error('Error al consultar notas de desempeño', [
                'action' => 'index',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al consultar las notas de desempeño.', 500);
            }

            return back()->withErrors('Error al consultar las notas de desempeño.');
        }
    }

    public function store(PerformanceNoteStoreRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $note = $this->noteRepository->createNote($request->validated(), (int) auth()->id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Nota agregada correctamente.',
                    'data' => new PerformanceNoteResource($note),
                ], 201);
            }

            return redirect()->back()->with('success', 'Nota agregada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar nota de desempeño', [
                'action' => 'store',
                'user_id' => auth()->id(),
                'payload' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al registrar la nota de desempeño.', 500);
            }

            return back()->withErrors('Error al registrar la nota de desempeño.');
        }
    }

    public function destroy(Request $request, $id): JsonResponse|RedirectResponse
    {
        $user = auth()->user();
        if (!$user || !method_exists($user, 'isAdmin') || !$user->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'No autorizado'], 403);
            }
            abort(403, 'No autorizado');
        }

        try {
            $this->noteRepository->deleteNote((int) $id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Nota eliminada correctamente.',
                ]);
            }

            return redirect()->back()->with('success', 'Nota eliminada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar nota de desempeño', [
                'action' => 'destroy',
                'user_id' => auth()->id(),
                'note_id' => $id,
                'error' => $e->getMessage(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return $this->utilResponse->errorResponse('Error al eliminar la nota de desempeño.', 500);
            }

            return back()->withErrors('Error al eliminar la nota de desempeño.');
        }
    }

    public function unreadNotifications(): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['notifications' => []]);
            }

            $notifications = $this->noteRepository->getUnreadNotifications($user);

            return response()->json(['notifications' => $notifications]);
        } catch (\Throwable $e) {
            Log::error('Error al consultar notificaciones de desempeño no leídas', [
                'action' => 'unreadNotifications',
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['notifications' => []], 500);
        }
    }

    public function markNotificationAsRead($id): JsonResponse
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['success' => false], 401);
            }

            $marked = $this->noteRepository->markNotificationAsRead($user, (string) $id);

            if ($marked) {
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false], 404);
        } catch (\Throwable $e) {
            Log::error('Error al marcar notificación como leída', [
                'action' => 'markNotificationAsRead',
                'user_id' => auth()->id(),
                'notification_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['success' => false], 500);
        }
    }
}
