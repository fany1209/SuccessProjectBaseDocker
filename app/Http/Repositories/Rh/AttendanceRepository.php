<?php

namespace App\Http\Repositories\Rh;

use App\Models\Asistencia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class AttendanceRepository
{
    protected Asistencia $model;

    protected array $specialSchedules = [
        'Ma Guadalupe Rangel Vargas' => [
            'entrada' => '07:00:00',
            'salida_lv' => '12:00:00',
            'salida_sab' => '12:00:00',
            'horas_lv' => 5.0,
            'horas_sab' => 5.0,
            'comida' => false,
        ],
        'Claudio Rugarcia' => [
            'entrada' => '09:00:00',
            'salida_lv' => '18:00:00',
            'salida_sab' => '14:00:00',
            'horas_lv' => 8.0,
            'horas_sab' => 5.0,
            'comida' => true,
        ],
    ];

    protected array $defaultSchedule = [
        'entrada' => '08:30:00',
        'salida_lv' => '17:30:00',
        'salida_sab' => '13:00:00',
        'horas_lv' => 8.0,
        'horas_sab' => 4.5,
        'comida' => true,
    ];

    public function __construct(Asistencia $model)
    {
        $this->model = $model;
    }

    public function getDistinctEmployees(): Collection
    {
        return $this->model->newQuery()
            ->select('nombre')
            ->distinct()
            ->orderBy('nombre')
            ->pluck('nombre');
    }

    public function getFilteredAttendances(?string $empleado, ?string $fechaInicio, ?string $fechaFin): Collection
    {
        $query = $this->model->newQuery();

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        } elseif ($fechaInicio) {
            $query->where('fecha', '>=', $fechaInicio);
        } elseif ($fechaFin) {
            $query->where('fecha', '<=', $fechaFin);
        }

        if (!empty($empleado)) {
            $query->where('nombre', $empleado);
        }

        return $query->orderBy('fecha', 'asc')->get();
    }

    public function calculateDashboardMetrics(Collection $registros, ?string $empleadoSeleccionado): array
    {
        $chartData = [['Fecha', 'Horas Trabajadas']];
        $chartDataIncidencias = [['Fecha', 'Retardo Entrada', 'Retardo Comida', 'Tiempo Extra']];
        $chartDataOmisiones = [['Fecha', 'Omisiones de Checada']];

        $totalMinutosExtra = 0;
        $totalMinutosPerdidos = 0;
        $omisionesChecada = 0;

        foreach ($registros as $reg) {
            $fechaStr = $reg->fecha instanceof \DateTimeInterface ? $reg->fecha->format('Y-m-d') : (string)$reg->fecha;
            $h_entrada = !empty($reg->entrada) ? Carbon::parse($fechaStr . ' ' . $reg->entrada) : null;
            $h_salida_c = !empty($reg->salida_comida) ? Carbon::parse($fechaStr . ' ' . $reg->salida_comida) : null;
            $h_regreso_c = !empty($reg->regreso_comida) ? Carbon::parse($fechaStr . ' ' . $reg->regreso_comida) : null;
            $h_salida_f = !empty($reg->salida_final) ? Carbon::parse($fechaStr . ' ' . $reg->salida_final) : null;

            $fechaCorta = Carbon::parse($fechaStr)->format('d/m/Y');
            $primerNombre = explode(' ', trim($reg->nombre))[0];

            $horario = $this->specialSchedules[$reg->nombre] ?? $this->defaultSchedule;

            $esSabado = Carbon::parse($fechaStr)->isSaturday();
            $horaSalidaOficial = $esSabado ? $horario['salida_sab'] : $horario['salida_lv'];

            $limiteSalida = Carbon::parse($fechaStr . ' ' . $horaSalidaOficial);
            $limiteEntrada = Carbon::parse($fechaStr . ' ' . $horario['entrada']);

            $tipoDiaStr = strtolower(trim($reg->tipo ?? 'normal'));
            $esJustificado = in_array($tipoDiaStr, ['viaje', 'curso', 'permiso', 'otro']);

            $horasEfectivas = 0;
            if ($tipoDiaStr === 'viaje' || $tipoDiaStr === 'curso') {
                $horasEfectivas = $esSabado ? $horario['horas_sab'] : $horario['horas_lv'];
            } elseif ($tipoDiaStr === 'permiso' || $tipoDiaStr === 'otro') {
                $horasEfectivas = 0;
            } else {
                if ($h_entrada && $h_salida_f) {
                    $totalMinutos = $h_entrada->diffInMinutes($h_salida_f);
                    $minutosComida = ($horario['comida'] && $h_salida_c && $h_regreso_c) ? $h_salida_c->diffInMinutes($h_regreso_c) : 0;
                    $horasEfectivas = round(($totalMinutos - $minutosComida) / 60, 2);
                }
            }

            $etiqueta = !empty($empleadoSeleccionado) ? $fechaCorta : $primerNombre;
            $chartData[] = [$etiqueta, $horasEfectivas];

            $retardoEntrada = 0;
            $retardoComida = 0;
            $tiempoExtra = 0;
            $salidaAnticipada = 0;
            $omisionesDelDia = 0;

            if (!$esJustificado) {
                if (!$reg->entrada) {
                    $omisionesDelDia++;
                }
                if (!$reg->salida_final) {
                    $omisionesDelDia++;
                }

                if (!$esSabado && $horario['comida']) {
                    if (!$reg->salida_comida) {
                        $omisionesDelDia++;
                    }
                    if (!$reg->regreso_comida) {
                        $omisionesDelDia++;
                    }
                }

                $omisionesChecada += $omisionesDelDia;

                if ($h_entrada && $h_entrada > $limiteEntrada) {
                    $retardoEntrada = $limiteEntrada->diffInMinutes($h_entrada);
                }

                if ($horario['comida'] && !$esSabado && $h_salida_c && $h_regreso_c) {
                    $duracionComida = $h_salida_c->diffInMinutes($h_regreso_c);
                    if ($duracionComida > 60) {
                        $retardoComida = $duracionComida - 60;
                    }
                }

                if ($h_salida_f && $h_salida_f < $limiteSalida) {
                    $salidaAnticipada = $h_salida_f->diffInMinutes($limiteSalida);
                }
            }

            if ($h_salida_f && $h_salida_f > $limiteSalida) {
                $tiempoExtra = $limiteSalida->diffInMinutes($h_salida_f);
                $totalMinutosExtra += $tiempoExtra;
            }

            $totalMinutosPerdidos += ($retardoEntrada + $retardoComida + $salidaAnticipada);

            $chartDataIncidencias[] = [
                $etiqueta,
                ['v' => (int)$retardoEntrada, 'f' => $this->formatMinutesToText((int)$retardoEntrada)],
                ['v' => (int)$retardoComida,  'f' => $this->formatMinutesToText((int)$retardoComida)],
                ['v' => (int)$tiempoExtra,    'f' => $this->formatMinutesToText((int)$tiempoExtra)],
            ];

            $chartDataOmisiones[] = [$etiqueta, $omisionesDelDia];
        }

        return [
            'chartData' => $chartData,
            'chartDataIncidencias' => $chartDataIncidencias,
            'chartDataOmisiones' => $chartDataOmisiones,
            'textoTiempoExtraTotal' => $this->formatMinutesToText($totalMinutosExtra),
            'textoTiempoPerdidoTotal' => $this->formatMinutesToText($totalMinutosPerdidos),
            'omisionesChecada' => $omisionesChecada,
        ];
    }

    public function formatMinutesToText(int|float $minutosTotales): string
    {
        $minutosTotales = (int) $minutosTotales;
        if ($minutosTotales <= 0) {
            return "0 min";
        }
        $horas = (int) floor($minutosTotales / 60);
        $minutos = $minutosTotales % 60;

        if ($horas > 0 && $minutos > 0) {
            return "{$horas} hr y {$minutos} min";
        } elseif ($horas > 0) {
            return "{$horas} hr";
        }

        return "{$minutos} min";
    }

    public function importCsv(UploadedFile $file): int
    {
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException('No se pudo abrir el archivo CSV.');
        }

        $primeraLinea = fgets($handle);
        $delimitador = str_contains($primeraLinea, ';') ? ';' : ',';
        rewind($handle);
        fgetcsv($handle, 1000, $delimitador);

        $batchSize = 500;
        $batch = [];
        $totalImportados = 0;
        $now = now();

        return DB::transaction(function () use ($handle, $delimitador, $batchSize, &$batch, &$totalImportados, $now) {
            try {
                while (($row = fgetcsv($handle, 1000, $delimitador)) !== false) {
                    if (!isset($row[0]) || empty(trim($row[0]))) {
                        continue;
                    }

                    $nombre = $this->normalizeEmployeeName($row[0]);
                    $fechaRaw = trim($row[1] ?? '');
                    if (empty($fechaRaw)) {
                        continue;
                    }

                    $fechaLimpia = substr($fechaRaw, 0, 10);
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaLimpia)) {
                        try {
                            $fechaLimpia = Carbon::parse($fechaRaw)->format('Y-m-d');
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    $estatus = isset($row[6]) && !empty(trim($row[6])) ? trim($row[6]) : 'Normal';
                    $comentarios = isset($row[7]) && !empty(trim($row[7])) ? trim($row[7]) : null;

                    $key = $nombre . '|' . $fechaLimpia;
                    $batch[$key] = [
                        'nombre' => $nombre,
                        'fecha' => $fechaLimpia,
                        'entrada' => !empty(trim($row[2] ?? '')) ? trim($row[2]) : null,
                        'salida_comida' => !empty(trim($row[3] ?? '')) ? trim($row[3]) : null,
                        'regreso_comida' => !empty(trim($row[4] ?? '')) ? trim($row[4]) : null,
                        'salida_final' => !empty(trim($row[5] ?? '')) ? trim($row[5]) : null,
                        'tipo' => $estatus,
                        'comentario' => $comentarios,
                        'updated_at' => $now,
                    ];

                    if (count($batch) >= $batchSize) {
                        $this->flushBatch($batch);
                        $totalImportados += count($batch);
                        $batch = [];
                    }
                }

                if (!empty($batch)) {
                    $this->flushBatch($batch);
                    $totalImportados += count($batch);
                    $batch = [];
                }

                return $totalImportados;
            } finally {
                fclose($handle);
            }
        });
    }

    protected function flushBatch(array $batch): void
    {
        if (empty($batch)) {
            return;
        }

        DB::table('asistencias')->upsert(
            array_values($batch),
            ['nombre', 'fecha'],
            ['entrada', 'salida_comida', 'regreso_comida', 'salida_final', 'tipo', 'comentario', 'updated_at']
        );
    }

    public function normalizeEmployeeName(string $rawName): string
    {
        $clean = trim($rawName);
        $lower = mb_strtolower($clean, 'UTF-8');

        $aliases = [
            'fanny' => 'Fany',
            'flor de maria gutierrez sanchez' => 'Flor de María Gutiérrez Sánchez',
            'flor de maría gutiérrez sánchez' => 'Flor de María Gutiérrez Sánchez',
            'manola ramirez perez' => 'Manola Ramirez',
        ];

        return $aliases[$lower] ?? $clean;
    }
}
