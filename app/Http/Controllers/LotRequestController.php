<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class LotRequestController extends Controller
{
    public function index()
    {
        $pendingLotRequests = LotRequest::where('status', 'pendiente')->count();

        return view('formats.laboratory.lot_requests.index', compact('pendingLotRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department' => 'required|string|max:100',
            'comments'   => 'nullable|string|max:1000',
        ]);

        DB::table('lot_requests')->insert([
            'department'   => $request->department,
            'comments'     => $request->comments,
            'requested_at' => now(),
            'status'       => 'pendiente',
        ]);

        return response()->json(['success' => true, 'message' => 'Registered request.']);
    }

    public function datatable(Request $request)
    {
        $rows = DB::table('lot_requests')
            ->select([
                'id',
                'department',
                DB::raw("DATE_FORMAT(requested_at, '%Y-%m-%d %H:%i') as requested_at"),
                'status',
                'comments',
            ])
            ->orderByDesc('requested_at')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'lots' => $rows, 
        ]);
    }

    public function updateStatus($id, Request $request)
    {
        $nuevoEstado = $request->status === 'terminado'
            ? 'terminado'
            : 'pendiente';

        DB::table('lot_requests')
            ->where('id', $id)
            ->update(['status' => $nuevoEstado]);

        $pending = DB::table('lot_requests')
            ->where('status', 'pendiente')
            ->count();

        return response()->json([
            'success' => true,
            'pending' => $pending
        ]);
    }

    public function checkPending()
    {
        $user = Auth::user();

        if (!$user || !$user->roles()->whereIn('name', ['Warehouse', 'warehouse'])->exists()) {
            return response()->json(['pending' => [], 'count' => 0]);
        }

        $pending = DB::table('lot_requests')
            ->where('status', 'pendiente')
            ->orderByDesc('requested_at')
            ->get();

        return response()->json([
            'pending' => $pending,
            'count'   => $pending->count(),
        ]);
    }

}
