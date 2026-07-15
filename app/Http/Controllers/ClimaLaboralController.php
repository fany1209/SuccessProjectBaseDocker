<?php

namespace App\Http\Controllers;

use App\Models\ClimaLaboral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClimaLaboralController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'q1_ambiente' => 'required|integer|min:1|max:5',
            'q2_respeto' => 'required|integer|min:1|max:5',
            'q3_comunicacion_oportuna' => 'required|integer|min:1|max:5',
            'q4_comunicacion_escucha' => 'required|integer|min:1|max:5',
            'q5_liderazgo' => 'required|integer|min:1|max:5',
            'q6_reconocimiento' => 'required|integer|min:1|max:5',
            'q7_desarrollo' => 'required|integer|min:1|max:5',
            'q8_motivacion' => 'required|integer|min:1|max:5',
            'q9_satisfaccion' => 'required|integer|min:1|max:5',
            'q10_bienestar_carga' => 'required|integer|min:1|max:5',
            'q11_bienestar_preocupacion' => 'required|integer|min:1|max:5',
            'q12_sugerencias' => 'nullable|string',
        ]);

        ClimaLaboral::create([
            'user_id' => Auth::id(), // Guardar id anonimamente sin mostrar
            'q1_ambiente' => $request->q1_ambiente,
            'q2_respeto' => $request->q2_respeto,
            'q3_comunicacion_oportuna' => $request->q3_comunicacion_oportuna,
            'q4_comunicacion_escucha' => $request->q4_comunicacion_escucha,
            'q5_liderazgo' => $request->q5_liderazgo,
            'q6_reconocimiento' => $request->q6_reconocimiento,
            'q7_desarrollo' => $request->q7_desarrollo,
            'q8_motivacion' => $request->q8_motivacion,
            'q9_satisfaccion' => $request->q9_satisfaccion,
            'q10_bienestar_carga' => $request->q10_bienestar_carga,
            'q11_bienestar_preocupacion' => $request->q11_bienestar_preocupacion,
            'q12_sugerencias' => $request->q12_sugerencias,
        ]);

        return redirect()->back()->with('success', 'Encuesta guardada exitosamente.');
    }

    /**
     * Display a listing of the resource for CRUD.
     */
    public function index()
    {
        $resultados = ClimaLaboral::orderBy('created_at', 'desc')->get();
        return view('rh.clima_laboral_resultados', compact('resultados'));
    }
}
