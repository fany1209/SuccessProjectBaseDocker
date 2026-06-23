<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintAdminController extends Controller
{
    public function index(Request $req)
    {
        $q     = $req->input('q');
        $tipo  = $req->input('tipo');
        $desde = $req->input('desde');
        $hasta = $req->input('hasta');

        $rows = Complaint::query()
            ->when($q, function($qq) use ($q){
                $qq->where(function($w) use ($q){
                    $w->where('descripcion', 'like', "%{$q}%")
                      ->orWhere('motivo_otro', 'like', "%{$q}%");
                });
            })
            ->when($tipo, fn($qq)=> $qq->where('tipo', $tipo))
            ->when($desde, fn($qq)=> $qq->whereDate('fecha','>=',$desde))
            ->when($hasta, fn($qq)=> $qq->whereDate('fecha','<=',$hasta))
            ->orderByDesc('fecha')->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.complaints', ['complaints' => $rows]);
    }

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return back()->with('success', 'Registro eliminado.');
    }
}
