<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContratoController extends Controller
{
    public function index()
    {
        $users = User::where('status', 'Activo')->with('contrato')->get();
        return view('rh.contratos.index', compact('users'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'periodo' => 'required|in:mes_1,mes_2,mes_3,indefinido',
            'archivo' => 'required|mimes:pdf|max:10240', // max 10MB
        ]);

        $contrato = Contrato::firstOrCreate(['user_id' => $request->user_id]);
        $periodo = $request->periodo;

        if ($request->hasFile('archivo')) {
            if ($contrato->$periodo) {
                Storage::disk('public')->delete($contrato->$periodo);
            }

            //$path = $request->file('archivo')->store('contratos', 'public');
              $originalName = $request->file('archivo')->getClientOriginalName();
            $filename = $request->user_id . '_' . $periodo . '_' . time() . '_' . $originalName;
            $path = $request->file('archivo')->storeAs('contratos', $filename, 'public_html');
            $contrato->$periodo = $path;
            $contrato->save();
        }

        return redirect()->back()->with('success', 'Contrato guardado correctamente.');
    }
}
