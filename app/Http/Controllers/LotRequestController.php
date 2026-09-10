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
            'product'    => 'nullable|string|max:255',
            'quantity'   => 'nullable|string|max:255',
            'provider'   => 'nullable|string|max:255',
            'collector'  => 'nullable|string|max:255',
            'sector'     => 'nullable|string|max:255',
        ]);

        DB::table('lot_requests')->insert([
            'department'   => $request->department,
            'comments'     => $request->comments,
            'product'      => $request->product,
            'quantity'     => $request->quantity,
            'provider'     => $request->provider,
            'collector'    => $request->collector,
            'sector'       => $request->sector,
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
                'product',
                'quantity',
                'provider',
                'collector',
                'sector',
                'sku',
                'batch'
            ])
            ->orderByDesc('requested_at')
            ->orderByDesc('id')
            ->limit(500)
            ->get();

        $productSkus = DB::table('lot_requests')
            ->whereNotNull('sku')
            ->where('sku', '!=', '')
            ->whereNotNull('product')
            ->where('product', '!=', '')
            ->select('product', 'sku')
            ->distinct()
            ->get()
            ->groupBy('product')
            ->map(function ($items) {
                return $items->pluck('sku')->toArray();
            });

        $productBatches = DB::table('lot_requests')
            ->whereNotNull('batch')
            ->where('batch', '!=', '')
            ->whereNotNull('product')
            ->where('product', '!=', '')
            ->select('product', 'batch')
            ->distinct()
            ->get()
            ->groupBy('product')
            ->map(function ($items) {
                return $items->pluck('batch')->toArray();
            });

        return response()->json([
            'lots' => $rows, 
            'productSkus' => $productSkus,
            'productBatches' => $productBatches,
        ]);
    }

    public function updateStatus($id, Request $request)
    {
        $nuevoEstado = $request->status === 'terminado'
            ? 'terminado'
            : 'pendiente';

        $dataToUpdate = ['status' => $nuevoEstado];
        if ($request->has('sku')) {
            $dataToUpdate['sku'] = $request->sku;
        }
        if ($request->has('batch')) {
            $dataToUpdate['batch'] = $request->batch;
        }

        DB::table('lot_requests')
            ->where('id', $id)
            ->update($dataToUpdate);

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

        if (!$user || !$user->roles()->whereIn('name', ['Quality', 'quality'])->exists()) {
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

    public function countCompleted()
    {
        $count = DB::table('lot_requests')
            ->where('status', 'terminado')
            ->count();

        return response()->json(['count' => $count]);
    }
}
