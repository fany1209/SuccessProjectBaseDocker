<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nomina;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class NominaController extends Controller
{
    /**
     * Muestra la vista principal del submódulo de nóminas.
     */
    public function index()
    {
        $puestos = Nomina::whereNotNull('puesto')
            ->where('puesto', '!=', '')
            ->distinct()
            ->orderBy('puesto')
            ->pluck('puesto');

        $years = Nomina::selectRaw('YEAR(fecha_ingreso) as year')
            ->whereNotNull('fecha_ingreso')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('rh.nominas.index', compact('puestos', 'years'));
    }

    /**
     * Retorna datos en formato JSON para DataTables.
     */
    public function datatable(Request $request)
    {
        $query = Nomina::query();

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        if ($request->filled('puesto')) {
            $query->where('puesto', $request->puesto);
        }

        if ($request->filled('year')) {
            $query->whereYear('fecha_ingreso', $request->year);
        }

        $nominas = $query->orderBy('nombre', 'asc')->get();

        // Enriquecer con los valores calculados
        $data = $nominas->map(function ($item) {
            return [
                'id' => $item->id,
                'nombre' => $item->nombre,
                'curp' => $item->curp ?: '—',
                'rfc' => $item->rfc ?: '—',
                'nss' => $item->nss ?: '—',
                'puesto' => $item->puesto,
                'fecha_ingreso' => $item->fecha_ingreso ? $item->fecha_ingreso->format('Y-m-d') : null,
                'fecha_baja' => $item->fecha_baja ? $item->fecha_baja->format('Y-m-d') : null,
                'edad' => $item->calculated_edad !== null ? $item->calculated_edad : ($item->edad ?? '—'),
                'antiguedad' => $item->calculated_antiguedad ?: '—',
                'sexo' => $item->sexo ?: '—',
                'estado_civil' => $item->estado_civil ?: '—',
                'fecha_nacimiento' => $item->fecha_nacimiento ? $item->fecha_nacimiento->format('Y-m-d') : null,
                'nombre_beneficiario' => $item->nombre_beneficiario ?: '—',
                'parentesco' => $item->parentesco ?: '—',
                'domicilio' => $item->domicilio ?: '—',
                'cp' => $item->cp ?: '—',
                'telefono' => $item->telefono ?: '—',
                'correo' => $item->correo ?: '—',
                'estatus' => $item->estatus,
                'created_at' => $item->created_at ? $item->created_at->format('d/m/Y') : '—',
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Registra un nuevo empleado en nómina.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'curp' => 'nullable|string|max:18',
            'rfc' => 'nullable|string|max:13',
            'nss' => 'nullable|string|max:20',
            'puesto' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            'fecha_baja' => 'nullable|date',
            'sexo' => 'nullable|string|max:20',
            'estado_civil' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'nullable|date',
            'nombre_beneficiario' => 'nullable|string|max:255',
            'parentesco' => 'nullable|string|max:100',
            'domicilio' => 'nullable|string',
            'cp' => 'nullable|string|max:10',
            'telefono' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:150',
            'estatus' => 'nullable|string|in:Activo,Baja',
        ]);

        // Formatear CURP y RFC a mayúsculas
        if (!empty($validated['curp'])) $validated['curp'] = strtoupper(trim($validated['curp']));
        if (!empty($validated['rfc'])) $validated['rfc'] = strtoupper(trim($validated['rfc']));

        // Determinar estatus automáticamente si no fue seleccionado explícitamente
        if (empty($validated['estatus'])) {
            $validated['estatus'] = !empty($validated['fecha_baja']) ? 'Baja' : 'Activo';
        }

        // Calcular edad
        if (!empty($validated['fecha_nacimiento'])) {
            $validated['edad'] = Carbon::parse($validated['fecha_nacimiento'])->age;
        }

        // Calcular antigüedad
        if (!empty($validated['fecha_ingreso'])) {
            $inicio = Carbon::parse($validated['fecha_ingreso'])->startOfDay();
            $fin = !empty($validated['fecha_baja']) ? Carbon::parse($validated['fecha_baja'])->startOfDay() : Carbon::now()->startOfDay();
            $diff = $inicio->diff($fin);
            $partes = [];
            if ($diff->y > 0) $partes[] = $diff->y . ' ' . ($diff->y === 1 ? 'año' : 'años');
            if ($diff->m > 0) $partes[] = $diff->m . ' ' . ($diff->m === 1 ? 'mes' : 'meses');
            if (empty($partes)) $partes[] = $diff->d . ' ' . ($diff->d === 1 ? 'día' : 'días');
            $validated['antiguedad'] = implode(', ', $partes);
        }

        $validated['user_id'] = auth()->id();

        $nomina = Nomina::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Empleado registrado en nómina correctamente.',
            'data' => $nomina
        ]);
    }

    /**
     * Muestra la información de un empleado específico.
     */
    public function show($id)
    {
        $nomina = Nomina::findOrFail($id);

        $data = [
            'id' => $nomina->id,
            'nombre' => $nomina->nombre,
            'curp' => $nomina->curp,
            'rfc' => $nomina->rfc,
            'nss' => $nomina->nss,
            'puesto' => $nomina->puesto,
            'fecha_ingreso' => $nomina->fecha_ingreso ? $nomina->fecha_ingreso->format('Y-m-d') : '',
            'fecha_baja' => $nomina->fecha_baja ? $nomina->fecha_baja->format('Y-m-d') : '',
            'edad' => $nomina->calculated_edad,
            'antiguedad' => $nomina->calculated_antiguedad,
            'sexo' => $nomina->sexo,
            'estado_civil' => $nomina->estado_civil,
            'fecha_nacimiento' => $nomina->fecha_nacimiento ? $nomina->fecha_nacimiento->format('Y-m-d') : '',
            'nombre_beneficiario' => $nomina->nombre_beneficiario,
            'parentesco' => $nomina->parentesco,
            'domicilio' => $nomina->domicilio,
            'cp' => $nomina->cp,
            'telefono' => $nomina->telefono,
            'correo' => $nomina->correo,
            'estatus' => $nomina->estatus,
        ];

        return response()->json([
            'success' => true,
            'nomina' => $data
        ]);
    }

    /**
     * Actualiza la información de un empleado.
     * Nota: Edad y Antigüedad se calculan automáticamente y no son editables directamente por el usuario.
     */
    public function update(Request $request, $id)
    {
        $nomina = Nomina::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'curp' => 'nullable|string|max:18',
            'rfc' => 'nullable|string|max:13',
            'nss' => 'nullable|string|max:20',
            'puesto' => 'required|string|max:255',
            'fecha_ingreso' => 'required|date',
            'fecha_baja' => 'nullable|date',
            'sexo' => 'nullable|string|max:20',
            'estado_civil' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'nullable|date',
            'nombre_beneficiario' => 'nullable|string|max:255',
            'parentesco' => 'nullable|string|max:100',
            'domicilio' => 'nullable|string',
            'cp' => 'nullable|string|max:10',
            'telefono' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:150',
            'estatus' => 'nullable|string|in:Activo,Baja',
        ]);

        if (!empty($validated['curp'])) $validated['curp'] = strtoupper(trim($validated['curp']));
        if (!empty($validated['rfc'])) $validated['rfc'] = strtoupper(trim($validated['rfc']));

        // Recalcular edad automáticamente a partir de fecha_nacimiento
        if (!empty($validated['fecha_nacimiento'])) {
            $validated['edad'] = Carbon::parse($validated['fecha_nacimiento'])->age;
        } else {
            $validated['edad'] = null;
        }

        // Recalcular antigüedad automáticamente a partir de fecha_ingreso y fecha_baja o hoy
        if (!empty($validated['fecha_ingreso'])) {
            $inicio = Carbon::parse($validated['fecha_ingreso'])->startOfDay();
            $fin = !empty($validated['fecha_baja']) ? Carbon::parse($validated['fecha_baja'])->startOfDay() : Carbon::now()->startOfDay();
            $diff = $inicio->diff($fin);
            $partes = [];
            if ($diff->y > 0) $partes[] = $diff->y . ' ' . ($diff->y === 1 ? 'año' : 'años');
            if ($diff->m > 0) $partes[] = $diff->m . ' ' . ($diff->m === 1 ? 'mes' : 'meses');
            if (empty($partes)) $partes[] = $diff->d . ' ' . ($diff->d === 1 ? 'día' : 'días');
            $validated['antiguedad'] = implode(', ', $partes);
        } else {
            $validated['antiguedad'] = null;
        }

        // Ajustar estatus si se proporciona fecha_baja y no se especificó estatus
        if (empty($validated['estatus'])) {
            $validated['estatus'] = !empty($validated['fecha_baja']) ? 'Baja' : 'Activo';
        }

        $nomina->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Nómina actualizada correctamente.',
            'data' => $nomina
        ]);
    }

    /**
     * Elimina el registro de la nómina.
     */
    public function destroy($id)
    {
        $nomina = Nomina::findOrFail($id);
        $nomina->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de nómina eliminado correctamente.'
        ]);
    }

    /**
     * Exporta la nómina a Excel con PhpSpreadsheet.
     */
    public function exportExcel(Request $request)
    {
        $query = Nomina::query();

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        if ($request->filled('puesto')) {
            $query->where('puesto', $request->puesto);
        }

        if ($request->filled('year')) {
            $query->whereYear('fecha_ingreso', $request->year);
        }

        $nominas = $query->orderBy('nombre', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nóminas de Personal');

        // Estilo encabezado corporativo
        $headers = [
            'A1' => 'NOMBRE',
            'B1' => 'CURP',
            'C1' => 'RFC',
            'D1' => 'NSS',
            'E1' => 'PUESTO',
            'F1' => 'FECHA DE INGRESO',
            'G1' => 'FECHA DE BAJA',
            'H1' => 'EDAD',
            'I1' => 'ANTIGÜEDAD',
            'J1' => 'SEXO',
            'K1' => 'ESTADO CIVIL',
            'L1' => 'FECHA DE NACIMIENTO',
            'M1' => 'NOMBRE DE BENEFICIARIO',
            'N1' => 'PARENTESCO',
            'O1' => 'DOMICILIO',
            'P1' => 'CP',
            'Q1' => 'TELÉFONO',
            'R1' => 'CORREO',
            'S1' => 'ESTATUS',
        ];

        foreach ($headers as $col => $val) {
            $sheet->setCellValue($col, $val);
        }

        // Estilos para encabezados (Verde corporativo #198754)
        $sheet->getStyle('A1:S1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF198754'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $row = 2;
        foreach ($nominas as $item) {
            $sheet->setCellValue('A' . $row, $item->nombre);
            $sheet->setCellValue('B' . $row, $item->curp ?: '—');
            $sheet->setCellValue('C' . $row, $item->rfc ?: '—');
            $sheet->setCellValue('D' . $row, $item->nss ?: '—');
            $sheet->setCellValue('E' . $row, $item->puesto);
            $sheet->setCellValue('F' . $row, $item->fecha_ingreso ? $item->fecha_ingreso->format('d/m/Y') : '—');
            $sheet->setCellValue('G' . $row, $item->fecha_baja ? $item->fecha_baja->format('d/m/Y') : '—');
            $sheet->setCellValue('H' . $row, $item->calculated_edad !== null ? $item->calculated_edad . ' años' : '—');
            $sheet->setCellValue('I' . $row, $item->calculated_antiguedad ?: '—');
            $sheet->setCellValue('J' . $row, $item->sexo ?: '—');
            $sheet->setCellValue('K' . $row, $item->estado_civil ?: '—');
            $sheet->setCellValue('L' . $row, $item->fecha_nacimiento ? $item->fecha_nacimiento->format('d/m/Y') : '—');
            $sheet->setCellValue('M' . $row, $item->nombre_beneficiario ?: '—');
            $sheet->setCellValue('N' . $row, $item->parentesco ?: '—');
            $sheet->setCellValue('O' . $row, $item->domicilio ?: '—');
            $sheet->setCellValue('P' . $row, $item->cp ?: '—');
            $sheet->setCellValue('Q' . $row, $item->telefono ?: '—');
            $sheet->setCellValue('R' . $row, $item->correo ?: '—');
            $sheet->setCellValue('S' . $row, $item->estatus);

            // Bordes y alineaciones
            $sheet->getStyle('A' . $row . ':S' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE5E7EB'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Alineación centrada para datos clave
            $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row . ':L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('P' . $row . ':S' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // Auto-ajustar ancho de columnas
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Nominas_Personal_' . date('Y-m-d') . '.xlsx';

        if (ob_get_length()) {
            ob_end_clean();
        }

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
