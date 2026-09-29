<?php

namespace App\Http\Controllers;

use App\Models\PerformanceNote;
use App\Models\User;
use Illuminate\Http\Request;

class PerformanceNoteController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = auth()->user()->isAdmin();

        if ($isAdmin) {
            $notes = PerformanceNote::with('user', 'admin')
                        ->orderBy('created_at', 'desc')
                        ->get();
            $users = User::all();
        } else {
            $notes = PerformanceNote::with('admin')
                        ->where('user_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->get();
            $users = collect(); // normal users don't need the list of users
        }

        return view('performance_notes.index', compact('notes', 'users', 'isAdmin'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|in:positive,improvement,neutral',
            'comments' => 'required|string'
        ]);

        PerformanceNote::create([
            'user_id' => $request->user_id,
            'admin_id' => auth()->id(),
            'type' => $request->type,
            'comments' => $request->comments
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Nota agregada correctamente.']);
        }
        return redirect()->back()->with('success', 'Nota agregada correctamente.');
    }

    public function destroy($id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $note = PerformanceNote::findOrFail($id);
        $note->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Nota eliminada correctamente.']);
        }
        return redirect()->back()->with('success', 'Nota eliminada correctamente.');
    }
}
