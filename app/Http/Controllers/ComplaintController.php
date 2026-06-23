<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Throwable;


class ComplaintController extends Controller
{
    public function create()
    {
        return view('complaints.create');
    }

    public function store(Request $request)
    {
        $allowedMotivos = [
            'trato_personal',
            'tiempos_respuesta',
            'condiciones_trabajo',
            'procesos_internos',
            'comunicacion_interna',
            'instalaciones_equipo',
            'seguridad_higiene',
            'cumplimiento_politicas',
            'liderazgo_supervision',
            'falta_apoyo_recursos',
            'discriminacion_mal_ambiente',
            'sugerencia_mejora',
            'ambiente_laboral',
            'equipo_de_trabajo',
            'cumplimiento_reglas',
            'falta_de_recursos',
            'otro',
        ];

        $data = $request->validate([
            'fecha'       => ['required','date'],
            'tipo'        => ['required', Rule::in(['peticion','queja','reclamo','sugerencia','felicitacion','denuncia'])],
            'motivos'     => ['nullable','array'],
            'motivos.*'   => [Rule::in($allowedMotivos)],
            'motivo_otro' => [
                Rule::requiredIf(fn () => in_array('otro', $request->input('motivos', []), true)),
                'nullable','string','max:255'
            ],
            'descripcion' => ['required','string'],
        ]);

        $seleccion = $data['motivos'] ?? [];
        $seleccion = is_array($seleccion) ? $seleccion : [];
        $seleccion = array_values(array_unique($seleccion));
        $seleccion = array_values(array_intersect($seleccion, $allowedMotivos));
        $data['motivos'] = $seleccion;

        if (! in_array('otro', $data['motivos'], true)) {
            $data['motivo_otro'] = null;
        } else {
            $data['motivo_otro'] = isset($data['motivo_otro'])
                ? trim(Str::limit($data['motivo_otro'], 255, ''))
                : null;
        }

        try {
            Complaint::create($data);
        } catch (Throwable $e) {
            report($e);
            return back()
                ->withErrors('Ocurrió un error al guardar tu experiencia. Intenta nuevamente.')
                ->withInput();
        }

        return redirect()
            ->route('complaints.create')
            ->with('ok', '¡Tu experiencia se envió correctamente! Gracias por ayudarnos a mejorar.');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return back()->with('success', 'Registro eliminado.');
    }
}
