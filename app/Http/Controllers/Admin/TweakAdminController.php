<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tweak;
use Illuminate\Http\Request;

class TweakAdminController extends Controller
{
    /**
     * Display a listing of the tweaks.
     */
    public function index(Request $request)
    {
        $tweaks = Tweak::with(['user', 'inventory.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.tweaks.index', compact('tweaks'));
    }
}
