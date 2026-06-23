<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Minuta;
use Illuminate\Support\Facades\Log;
    use Barryvdh\DomPDF\Facade\Pdf;


class MinutaController extends Controller
{
    public function index()
    {
        $total_minutas = Minuta::count();
        return view('minutas', compact('total_minutas'));
    }

    public function getMinutas(Request $request)
    {
        $query = Minuta::query();

        if ($request->status) {
            $query->where('estatus', 'LIKE', "%{$request->status}%");
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('tema_general', 'LIKE', "%{$request->search}%")
                  ->orWhere('ponente', 'LIKE', "%{$request->search}%")
                  ->orWhere('asistente_nombre', 'LIKE', "%{$request->search}%");
            });
        }

        $minutas = $query->get()->map(function($m) {
            return [
                'id_minuta'              => $m->id_minuta,
                'fecha_hora'             => $m->fecha_hora,
                'lugar'                  => $m->lugar,
                'tema_general'           => $m->tema_general,
                'ponente'                => $m->ponente,
                'asistente_nombre'       => $m->asistente_nombre,
                'asistente_departamento' => $m->asistente_departamento,
                'tema_tratado'           => $m->tema_tratado,
                'acuerdo'                => $m->acuerdo,
                'responsable'            => $m->responsable,
                'fecha_cierre'           => $m->fecha_cierre,
                'estatus'                => $m->estatus,
                'canUpdate'              => true, 
                'canDelete'              => true
            ];
        });

        return response()->json(['minutas' => $minutas]);
    }

    private function formatRequestData(Request $request)
    {
        $data = $request->all();
        $user = auth()->user();

        if ($request->has('asistente_nombre')) {
            $data['asistente_nombre'] = implode(", ", array_filter($request->asistente_nombre));
            $data['asistente_departamento'] = implode(", ", array_filter($request->asistente_departamento));
        }

        if ($request->has('acuerdo')) {
            $data['tema_tratado'] = implode(" | ", array_filter($request->tema_tratado));
            $data['acuerdo']      = implode(" | ", array_filter($request->acuerdo));
            $data['responsable']  = implode(", ", array_filter($request->responsable));

            if ($user->can('admin.dashboard')) {
                
                if ($request->has('fecha_compromiso')) {
                    $fechas_comp_limpias = array_map(function($fecha) {
                        return $fecha ? substr($fecha, 0, 10) : null;
                    }, $request->fecha_compromiso);
                    $data['fecha_compromiso'] = implode(", ", array_filter($fechas_comp_limpias));
                }

                if ($request->has('fecha_cierre')) {
                    $fechas_cierre_limpias = array_map(function($fecha) {
                        return $fecha ? substr($fecha, 0, 10) : null;
                    }, $request->fecha_cierre);
                    $data['fecha_cierre'] = implode(", ", array_filter($fechas_cierre_limpias));
                }

                $data['estatus'] = implode(", ", array_filter($request->estatus));

            } else {
                
                $minutaExistente = \App\Models\Minuta::find($request->id_minuta);
                
                if ($minutaExistente) {
                    $data['fecha_compromiso'] = $minutaExistente->fecha_compromiso;
                    $data['fecha_cierre']     = $minutaExistente->fecha_cierre;
                    $data['estatus']          = $minutaExistente->estatus;
                } else {
                    $data['fecha_compromiso'] = "";
                    $data['fecha_cierre']     = "";
                    $data['estatus']          = "Pendiente";
                }
            }
        }

        return $data;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'fecha_hora'   => 'required',
                'tema_general' => 'required',
            ]);
            $formattedData = $this->formatRequestData($request);
            $formattedData['user_id'] = auth()->id(); 

            Minuta::create($formattedData);

            return response()->json(['success' => true, 'message' => 'Minuta guardada con éxito']);
        } catch (\Exception $e) {
            Log::error("Error en Minuta Store: " . $e->getMessage());
            return response()->json(['error' => 'Error al crear: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $minuta = Minuta::findOrFail($id);
            return response()->json(['minuta' => $minuta]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se encontró la minuta.'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $minuta = Minuta::findOrFail($id);
            
            $formattedData = $this->formatRequestData($request);
            
            $minuta->update($formattedData);

            return response()->json(['success' => true, 'message' => 'Minuta actualizada']);
        } catch (\Exception $e) {
            Log::error("Error en Minuta Update: " . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $minuta = Minuta::findOrFail($id);
            $minuta->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar.'], 500);
        }
    }

    public function downloadPDF($id)
    {
        $minuta = Minuta::with('usuario')->findOrFail($id);

        $fecha_hora = \Carbon\Carbon::parse($minuta->fecha_hora);
        $fecha = $fecha_hora->format('Y-m-d'); 
        $hora  = $fecha_hora->format('g:i A'); 
        $creado_por = $minuta->usuario->name ?? 'No identificado';

        $pdf = Pdf::loadView('formats.minutas.10', [
            'fecha'                  => $fecha,
            'hora'                   => $hora, 
            'lugar'                  => $minuta->lugar,
            'tema_general'           => $minuta->tema_general,
            'ponente'                => $minuta->ponente,
            'creado_por'             => $creado_por, 
            'asistente_nombre'       => $minuta->asistente_nombre,
            'asistente_departamento' => $minuta->asistente_departamento,
            'tema_tratado'           => $minuta->tema_tratado,
            'acuerdo'                => $minuta->acuerdo,
            'responsable'            => $minuta->responsable,
            'fecha_compromiso'       => $minuta->fecha_compromiso,
            'fecha_cierre'           => $minuta->fecha_cierre,
            'estatus'                => $minuta->estatus,
        ]);

        return $pdf->stream("Minuta_SSS_FOR_REH_10_{$id}.pdf");
    }
}