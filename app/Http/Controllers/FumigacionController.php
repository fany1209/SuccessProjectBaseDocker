<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fumigacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; 

class FumigacionController extends Controller
{
    public function index()
    {
        $todasLasFumigaciones = Fumigacion::orderBy('fecha_programada', 'asc')->get();

        $eventos = $todasLasFumigaciones->map(function($f) {
            return [
                'id'    => $f->id,
                'title' => $f->proveedor . ' (' . $f->metodo_aplicacion . ')',
                'start' => \Carbon\Carbon::parse($f->fecha_programada)->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $f->estado == 'Realizado' ? '#198754' : ($f->estado == 'Cancelado' ? '#dc3545' : '#ffc107'),
                'borderColor'     => $f->estado == 'Realizado' ? '#157347' : ($f->estado == 'Cancelado' ? '#a71d2a' : '#e0a800'),
                'textColor'       => $f->estado == 'Pendiente' ? '#000000' : '#ffffff',
            ];
        });

        return view('quality.fumigaciones.index', [
            'todasLasFumigaciones' => $todasLasFumigaciones,
            'eventos' => $eventos
        ]);
    }

    public function store(Request $request)
    {
        try {
            Log::info('Intento de guardado:', $request->all());

            $request->validate([
                'proveedor' => 'required',
                'fecha_programada' => 'required',
            ]);

            Fumigacion::create([
                'proveedor' => $request->proveedor,
                'fecha_programada' => $request->fecha_programada,
                'metodo_aplicacion' => $request->metodo_aplicacion,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error en store: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $fumigacion = Fumigacion::findOrFail($id);
            
            $fumigacion->update([
                'proveedor' => $request->proveedor,
                'fecha_programada' => $request->fecha_programada,
                'metodo_aplicacion' => $request->metodo_aplicacion,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error en update: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $fumigacion = Fumigacion::findOrFail($id);
            $fumigacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}