<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RhController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener la lista de empleados para el select
        $empleados = DB::table('asistencias')->select('nombre')->distinct()->orderBy('nombre')->pluck('nombre');
        
        // VALIDACIÓN ANTIBLANK: Corrige los textos vacíos "" que envía DataTables por AJAX
        $empleadoSeleccionado = $request->input('empleado');
        if (empty($empleadoSeleccionado)) {
            $empleadoSeleccionado = null;
        }

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin    = $request->input('fecha_fin');

        if (empty($fechaInicio)) {
            $fechaInicio = Carbon::now()->subDays(7)->toDateString();
        }
        if (empty($fechaFin)) {
            $fechaFin = Carbon::now()->toDateString();
        }

        // 2. Consulta a la Base de Datos
        $registros = DB::table('asistencias')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->when($empleadoSeleccionado, function($q) use ($empleadoSeleccionado) {
                return $q->where('nombre', $empleadoSeleccionado);
            })
            ->orderBy('fecha', 'asc')
            ->get();

        // RESPUESTA PARA DATATABLES: Si es petición AJAX, responde el JSON limpio aquí mismo
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($registros);
        }

        // 3. Función auxiliar para formatear los minutos en texto legible
        $formatearTiempo = function($minutosTotales) {
            if ($minutosTotales <= 0) return "0 min";
            $horas = floor($minutosTotales / 60);
            $minutos = $minutosTotales % 60;
            if ($horas > 0 && $minutos > 0) return "{$horas} hr y {$minutos} min";
            elseif ($horas > 0) return "{$horas} hr";
            else return "{$minutos} min";
        };

        // 4. Inicializar arreglos para las 3 Gráficas
        $chartData = [['Fecha', 'Horas Trabajadas']];
        $chartDataIncidencias = [['Fecha', 'Retardo Entrada', 'Retardo Comida', 'Tiempo Extra']];
        $chartDataOmisiones = [['Fecha', 'Omisiones de Checada']];

        // 5. Inicializar contadores para las Tarjetas Superiores
        $totalMinutosExtra = 0;
        $totalMinutosPerdidos = 0; 
        $omisionesChecada = 0; 

        // 🌟 MAPA DE HORARIOS ESPECIALES CON CONFIGURACIÓN DE COMIDA 🌟
        $horariosEspeciales = [
            // Configuración para Guadalupe Rangel (7:00 a 12:00 sin comida)
            'Ma Guadalupe Rangel Vargas' => [
                'entrada'    => '07:00:00',
                'salida_lv'  => '12:00:00',
                'salida_sab' => '12:00:00', 
                'horas_lv'   => 5.0,
                'horas_sab'  => 5.0,
                'comida'     => false // 🌟 FALSE: Evita que el sistema le pida checar comida
            ],
            'Claudio Rugarcia' => [
                'entrada'    => '09:00:00',
                'salida_lv'  => '18:00:00',
                'salida_sab' => '14:00:00',
                'horas_lv'   => 8.0,
                'horas_sab'  => 5.0,
                'comida'     => true
            ],
        ];

        // 6. Ciclo de procesamiento de datos
        foreach ($registros as $reg) {
            $h_entrada = $reg->entrada ? Carbon::parse($reg->fecha . ' ' . $reg->entrada) : null;
            $h_salida_c = $reg->salida_comida ? Carbon::parse($reg->fecha . ' ' . $reg->salida_comida) : null;
            $h_regreso_c = $reg->regreso_comida ? Carbon::parse($reg->fecha . ' ' . $reg->regreso_comida) : null;
            $h_salida_f = $reg->salida_final ? Carbon::parse($reg->fecha . ' ' . $reg->salida_final) : null;

            $fechaCorta = Carbon::parse($reg->fecha)->format('d/m/Y'); 
            $primerNombre = explode(' ', trim($reg->nombre))[0]; 

            // ASIGNACIÓN DINÁMICA DE HORARIO (Si no existe en el mapa, toma el horario por defecto con comida)
            $horario = $horariosEspeciales[$reg->nombre] ?? [
                'entrada'    => '08:30:00',
                'salida_lv'  => '17:30:00',
                'salida_sab' => '13:00:00',
                'horas_lv'   => 8.0,
                'horas_sab'  => 4.5,
                'comida'     => true
            ];

            $esSabado = Carbon::parse($reg->fecha)->isSaturday();
            $horaSalidaOficial = $esSabado ? $horario['salida_sab'] : $horario['salida_lv'];
            
            $limiteSalida = Carbon::parse($reg->fecha . ' ' . $horaSalidaOficial);
            $limiteEntrada = Carbon::parse($reg->fecha . ' ' . $horario['entrada']);

            $tipoDiaStr = strtolower(trim($reg->tipo ?? 'normal'));
            $esJustificado = in_array($tipoDiaStr, ['viaje', 'curso', 'permiso', 'otro']);

            // --- LÓGICA GRÁFICA 1: Horas Efectivas ---
            $horasEfectivas = 0;
            if ($tipoDiaStr === 'viaje' || $tipoDiaStr === 'curso') {
                $horasEfectivas = $esSabado ? $horario['horas_sab'] : $horario['horas_lv']; 
            } elseif ($tipoDiaStr === 'permiso' || $tipoDiaStr === 'otro') {
                $horasEfectivas = 0; 
            } else {
                if ($h_entrada && $h_salida_f) {
                    $totalMinutos = $h_entrada->diffInMinutes($h_salida_f);
                    // Solo resta minutos de comida si el horario del empleado lo requiere
                    $minutosComida = ($horario['comida'] && $h_salida_c && $h_regreso_c) ? $h_salida_c->diffInMinutes($h_regreso_c) : 0;
                    $horasEfectivas = round(($totalMinutos - $minutosComida) / 60, 2);
                }
            }
            
            $etiqueta = $empleadoSeleccionado ? $fechaCorta : $primerNombre;
            $chartData[] = [$etiqueta, $horasEfectivas];

            // --- LÓGICA GRÁFICA 2 Y 3: Incidencias y Omisiones ---
            $retardoEntrada = 0;
            $retardoComida = 0;
            $tiempoExtra = 0;
            $salidaAnticipada = 0; 
            $omisionesDelDia = 0; 

            if (!$esJustificado) {
                // Conteo de checadas en blanco principales
                if (!$reg->entrada) $omisionesDelDia++;
                if (!$reg->salida_final) $omisionesDelDia++;
                
                // 🌟 Exigir comida SOLO si no es sábado Y el horario del empleado dice comida = true
                if (!$esSabado && $horario['comida']) {
                    if (!$reg->salida_comida) $omisionesDelDia++;
                    if (!$reg->regreso_comida) $omisionesDelDia++;
                }

                $omisionesChecada += $omisionesDelDia;

                // Cálculo de Retardo Entrada
                if ($h_entrada && $h_entrada > $limiteEntrada) {
                    $retardoEntrada = $limiteEntrada->diffInMinutes($h_entrada);
                }
                // 🌟 Cálculo de Retardo Comida (Solo si el empleado tiene comida)
                if ($horario['comida'] && !$esSabado && $h_salida_c && $h_regreso_c) {
                    $duracionComida = $h_salida_c->diffInMinutes($h_regreso_c);
                    if ($duracionComida > 60) $retardoComida = $duracionComida - 60;
                }
                // Cálculo de Salida Anticipada
                if ($h_salida_f && $h_salida_f < $limiteSalida) {
                    $salidaAnticipada = $h_salida_f->diffInMinutes($limiteSalida);
                }
            }

            // Cálculo de Tiempo Extra
            if ($h_salida_f && $h_salida_f > $limiteSalida) {
                $tiempoExtra = $limiteSalida->diffInMinutes($h_salida_f);
                $totalMinutosExtra += $tiempoExtra;
            }

            $totalMinutosPerdidos += ($retardoEntrada + $retardoComida + $salidaAnticipada);

            $chartDataIncidencias[] = [
                $etiqueta, 
                ['v' => (int)$retardoEntrada, 'f' => $formatearTiempo($retardoEntrada)],
                ['v' => (int)$retardoComida,  'f' => $formatearTiempo($retardoComida)],
                ['v' => (int)$tiempoExtra,    'f' => $formatearTiempo($tiempoExtra)]
            ];

            $chartDataOmisiones[] = [$etiqueta, $omisionesDelDia];
        }

        $textoTiempoExtraTotal = $formatearTiempo($totalMinutosExtra);
        $textoTiempoPerdidoTotal = $formatearTiempo($totalMinutosPerdidos);

        return view('rh.asistencia', compact(
            'empleados', 'empleadoSeleccionado', 'fechaInicio', 'fechaFin', 
            'chartData', 'chartDataIncidencias', 'chartDataOmisiones', 
            'textoTiempoExtraTotal', 'textoTiempoPerdidoTotal', 'omisionesChecada'
        ));
    }

    public function uploadCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:10240']);
        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');
        
        $primeraLinea = fgets($file);
        $delimitador = strpos($primeraLinea, ';') !== false ? ';' : ',';
        rewind($file);
        fgetcsv($file, 1000, $delimitador); 

        while (($row = fgetcsv($file, 1000, $delimitador)) !== false) {
            if (!isset($row[0]) || empty(trim($row[0]))) continue;
            
            $nombre = trim($row[0]);
            $nombreLower = mb_strtolower($nombre, 'UTF-8');
            if ($nombreLower === 'fanny') $nombre = 'Fany';
            if ($nombreLower === 'flor de maria gutierrez sanchez' || $nombreLower === 'flor de maría gutiérrez sánchez') $nombre = 'Flor de María Gutiérrez Sánchez';
            if ($nombreLower === 'manola ramirez perez') $nombre = 'Manola Ramirez';

            $fechaLimpia = substr(trim($row[1]), 0, 10);
            $estatus = isset($row[6]) && !empty(trim($row[6])) ? trim($row[6]) : 'Normal';
            $comentarios = isset($row[7]) && !empty(trim($row[7])) ? trim($row[7]) : null;

            DB::table('asistencias')->updateOrInsert(
                ['nombre' => $nombre, 'fecha' => $fechaLimpia],
                [
                    'entrada'        => !empty(trim($row[2])) ? trim($row[2]) : null,
                    'salida_comida'  => !empty(trim($row[3])) ? trim($row[3]) : null,
                    'regreso_comida' => !empty(trim($row[4])) ? trim($row[4]) : null,
                    'salida_final'   => !empty(trim($row[5])) ? trim($row[5]) : null,
                    'tipo'           => $estatus,
                    'comentario'     => $comentarios
                ]
            );
        }
        fclose($file);
        return back()->with('success', '¡Datos importados correctamente!');
    }
}