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
        $trabajadores = User::where('status', 'Activo')->where('tipo_empleado', 'Trabajador')->with('contrato')->get();
        $practicantes = User::where('status', 'Activo')->where('tipo_empleado', 'Practicante')->with('contrato')->get();
        return view('rh.contratos.index', compact('trabajadores', 'practicantes'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'mes_1' => 'nullable|mimes:pdf|max:10240',
            'mes_2' => 'nullable|mimes:pdf|max:10240',
            'mes_3' => 'nullable|mimes:pdf|max:10240',
            'indefinido' => 'nullable|mimes:pdf|max:10240',
            'confidencialidad' => 'nullable|mimes:pdf|max:10240',
        ]);

        $contrato = Contrato::firstOrCreate(['user_id' => $request->user_id]);

        $campos = ['mes_1', 'mes_2', 'mes_3', 'indefinido', 'confidencialidad'];

        foreach ($campos as $campo) {
            if ($request->hasFile($campo)) {
                if ($contrato->$campo) {
                    Storage::disk('public_html')->delete($contrato->$campo);
                }
                $originalName = $request->file($campo)->getClientOriginalName();
                $filename = $request->user_id . '_' . $campo . '_' . time() . '_' . $originalName;
                $path = $request->file($campo)->storeAs('contratos', $filename, 'public_html');
                $contrato->$campo = $path;
            }
        }

        $contrato->save();

        return redirect()->back()->with('success', 'Contratos guardados correctamente.');
    }
}
