<?php

namespace App\Http\Repositories\Nomina;

use App\Models\Nomina;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NominaRepository
{
    protected Nomina $nomina;

    public function __construct(Nomina $nomina)
    {
        $this->nomina = $nomina;
    }

    public function getIndexData(): array
    {
        $puestos = $this->nomina->whereNotNull('puesto')
            ->where('puesto', '!=', '')
            ->distinct()
            ->orderBy('puesto')
            ->pluck('puesto');

        $years = $this->nomina->selectRaw('YEAR(fecha_ingreso) as year')
            ->whereNotNull('fecha_ingreso')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return [
            'puestos' => $puestos,
            'years'   => $years,
        ];
    }

    public function getDatatable(array $filters)
    {
        $query = $this->nomina->query();

        if (!empty($filters['estatus'])) {
            $query->where('estatus', $filters['estatus']);
        }

        if (!empty($filters['puesto'])) {
            $query->where('puesto', $filters['puesto']);
        }

        if (!empty($filters['year'])) {
            $query->whereYear('fecha_ingreso', $filters['year']);
        }

        return $query->orderBy('nombre', 'asc')->get();
    }

    public function find(int $id): ?Nomina
    {
        return $this->nomina->with('user')->find($id);
    }

    public function store(array $data, int $userId): Nomina
    {
        return DB::transaction(function () use ($data, $userId) {
            if (empty($data['estatus'])) {
                $data['estatus'] = !empty($data['fecha_baja']) ? 'Baja' : 'Activo';
            }

            if (!empty($data['fecha_nacimiento'])) {
                $data['edad'] = Carbon::parse($data['fecha_nacimiento'])->age;
            }

            if (!empty($data['fecha_ingreso'])) {
                $inicio = Carbon::parse($data['fecha_ingreso'])->startOfDay();
                $fin = !empty($data['fecha_baja']) ? Carbon::parse($data['fecha_baja'])->startOfDay() : Carbon::now()->startOfDay();
                $diff = $inicio->diff($fin);
                $partes = [];
                if ($diff->y > 0) {
                    $partes[] = $diff->y . ' ' . ($diff->y === 1 ? 'año' : 'años');
                }
                if ($diff->m > 0) {
                    $partes[] = $diff->m . ' ' . ($diff->m === 1 ? 'mes' : 'meses');
                }
                if (empty($partes)) {
                    $partes[] = $diff->d . ' ' . ($diff->d === 1 ? 'día' : 'días');
                }
                $data['antiguedad'] = implode(', ', $partes);
            }

            $data['user_id'] = $userId;

            return $this->nomina->create($data);
        });
    }

    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $nomina = $this->nomina->where('id', $id)->lockForUpdate()->first();
            if (!$nomina) {
                return false;
            }

            if (!empty($data['fecha_nacimiento'])) {
                $data['edad'] = Carbon::parse($data['fecha_nacimiento'])->age;
            } else {
                $data['edad'] = null;
            }

            if (!empty($data['fecha_ingreso'])) {
                $inicio = Carbon::parse($data['fecha_ingreso'])->startOfDay();
                $fin = !empty($data['fecha_baja']) ? Carbon::parse($data['fecha_baja'])->startOfDay() : Carbon::now()->startOfDay();
                $diff = $inicio->diff($fin);
                $partes = [];
                if ($diff->y > 0) {
                    $partes[] = $diff->y . ' ' . ($diff->y === 1 ? 'año' : 'años');
                }
                if ($diff->m > 0) {
                    $partes[] = $diff->m . ' ' . ($diff->m === 1 ? 'mes' : 'meses');
                }
                if (empty($partes)) {
                    $partes[] = $diff->d . ' ' . ($diff->d === 1 ? 'día' : 'días');
                }
                $data['antiguedad'] = implode(', ', $partes);
            } else {
                $data['antiguedad'] = null;
            }

            if (empty($data['estatus'])) {
                $data['estatus'] = !empty($data['fecha_baja']) ? 'Baja' : 'Activo';
            }

            return (bool) $nomina->update($data);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $nomina = $this->nomina->where('id', $id)->lockForUpdate()->first();
            if (!$nomina) {
                return false;
            }

            return (bool) $nomina->delete();
        });
    }

    public function exportExcel(array $filters): StreamedResponse
    {
        $nominas = $this->getDatatable($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nóminas de Personal');

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

        $sheet->getStyle('A1:S1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF198754'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
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

            $sheet->getStyle('A' . $row . ':S' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFE5E7EB'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row . ':L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('P' . $row . ':S' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Nominas_Personal_' . date('Y-m-d') . '.xlsx';

        if (!app()->environment('testing') && ob_get_length()) {
            ob_end_clean();
        }

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
