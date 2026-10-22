<?php

namespace App\Http\Repositories\Xls;

use App\Models\Inventory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class XlsRepository
{
    protected Inventory $inventoryModel;

    public function __construct(Inventory $inventoryModel)
    {
        $this->inventoryModel = $inventoryModel;
    }

    public function generateReportsSpreadsheet(?int $year = null): array
    {
        $months = 6;
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();

        if ($year) {
            $activeSheet->setTitle('Salidas ' . $year);
        } else {
            $activeSheet->setTitle('Salidas ' . $months . ' meses');
        }

        $outputs = $this->getMovementOutputs($year, $months);

        $estilosEncabezados = [
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 12,
                'name'  => 'Segoe UI',
            ],
            'fill' => [
                'fillType'   => Fill::FILL_GRADIENT_LINEAR,
                'rotation'   => 90,
                'startColor' => ['argb' => '2ECC71'],
                'endColor'   => ['argb' => '27AE60'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFFFFFFF'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];

        $fechaConsulta = now()->toDateTimeString();
        $activeSheet->setCellValue('A1', 'Fecha de consulta: ' . $fechaConsulta);
        $activeSheet->setCellValue('A3', 'Fecha de Salida');
        $activeSheet->setCellValue('B3', 'Cliente');
        $activeSheet->setCellValue('C3', 'Producto');
        $activeSheet->setCellValue('D3', 'Vendedor');
        $activeSheet->setCellValue('E3', 'Lote');
        $activeSheet->setCellValue('F3', 'Cantidad');

        foreach (['A3', 'B3', 'C3', 'D3', 'E3', 'F3'] as $cell) {
            $activeSheet->getStyle($cell)->applyFromArray($estilosEncabezados);
        }

        $activeSheet->getRowDimension('3')->setRowHeight(20);

        $filaInicial = 4;
        foreach ($outputs as $item) {
            $activeSheet->setCellValue('A' . $filaInicial, $item->outputDate);
            $activeSheet->setCellValue('B' . $filaInicial, $item->cName);
            $activeSheet->setCellValue('C' . $filaInicial, $item->pName);
            $activeSheet->setCellValue('D' . $filaInicial, $item->vendedor);
            $activeSheet->setCellValue('E' . $filaInicial, $item->warehouse_batch);
            $activeSheet->setCellValue('F' . $filaInicial, $item->quantity);
            $filaInicial++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
            $activeSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $activeSheet->getStyle('C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $activeSheet = $spreadsheet->createSheet();
        if ($year) {
            $activeSheet->setTitle('Entradas ' . $year);
        } else {
            $activeSheet->setTitle('Entradas ' . $months . ' meses');
        }

        $inputs = $this->getMovementInputs($year, $months);

        $activeSheet->setCellValue('A1', 'Fecha de consulta: ' . $fechaConsulta);
        $activeSheet->setCellValue('A3', 'Fecha de Entrada');
        $activeSheet->setCellValue('B3', 'Proveedor');
        $activeSheet->setCellValue('C3', 'Producto');
        $activeSheet->setCellValue('D3', 'Lote');
        $activeSheet->setCellValue('E3', 'Cantidad');

        foreach (['A3', 'B3', 'C3', 'D3', 'E3'] as $cell) {
            $activeSheet->getStyle($cell)->applyFromArray($estilosEncabezados);
        }

        $filaInicial = 4;
        foreach ($inputs as $item) {
            $activeSheet->setCellValue('A' . $filaInicial, $item->inputDate);
            $activeSheet->setCellValue('B' . $filaInicial, $item->sName);
            $activeSheet->setCellValue('C' . $filaInicial, $item->pName);
            $activeSheet->setCellValue('D' . $filaInicial, $item->warehouse_batch);
            $activeSheet->setCellValue('E' . $filaInicial, $item->quantity);
            $filaInicial++;
        }

        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $activeSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = $year ? 'Reporte_movimientos_' . $year . '.xlsx' : 'Reporte_movimientos.xlsx';

        return [
            'spreadsheet' => $spreadsheet,
            'fileName'    => $fileName,
            'hasCharts'   => false,
        ];
    }

    public function generateInventorySpreadsheet(): array
    {
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();
        $activeSheet->setTitle('Por Lote');

        $inventory = $this->inventoryModel->newQuery()->with('product')->get();

        $estilosEncabezados = [
            'font' => [
                'bold'  => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size'  => 12,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => '000000'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => Color::COLOR_WHITE],
                ],
            ],
        ];

        $fechaConsulta = now()->toDateTimeString();
        $activeSheet->setCellValue('A1', 'Fecha de consulta: ' . $fechaConsulta);
        $activeSheet->setCellValue('A3', 'Producto');
        $activeSheet->getStyle('A3')->applyFromArray($estilosEncabezados);
        $activeSheet->setCellValue('B3', 'Lote');
        $activeSheet->getStyle('B3')->applyFromArray($estilosEncabezados);
        $activeSheet->setCellValue('C3', 'En Stock');
        $activeSheet->getStyle('C3')->applyFromArray($estilosEncabezados);
        $activeSheet->setCellValue('D3', 'Unidad');
        $activeSheet->getStyle('D3')->applyFromArray($estilosEncabezados);

        $activeSheet->getRowDimension('3')->setRowHeight(20);

        $filaInicial = 4;
        foreach ($inventory as $item) {
            $activeSheet->setCellValue('A' . $filaInicial, $item->product->name ?? 'N/A');
            $activeSheet->setCellValue('B' . $filaInicial, $item->batch);
            $activeSheet->setCellValue('C' . $filaInicial, number_format((float) $item->stock, 3));
            $activeSheet->setCellValue('D' . $filaInicial, $item->product->unit ?? 'N/A');
            $filaInicial++;
        }

        $activeSheet->getColumnDimension('A')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
        $activeSheet->getStyle('C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $activeSheet = $spreadsheet->createSheet();
        $activeSheet->setTitle('Totales');

        $inventorySummary = $this->inventoryModel->newQuery()
            ->with('product')
            ->select('product_id', DB::raw('SUM(stock) as total'))
            ->groupBy('product_id')
            ->orderBy('total', 'desc')
            ->get();

        $filaInicial = 4;
        $activeSheet->setCellValue('A1', 'Fecha de consulta: ' . $fechaConsulta);
        $activeSheet->setCellValue('A3', 'Producto');
        $activeSheet->getStyle('A3')->applyFromArray($estilosEncabezados);
        $activeSheet->setCellValue('B3', 'Total');
        $activeSheet->getStyle('B3')->applyFromArray($estilosEncabezados);
        $activeSheet->setCellValue('C3', 'Unidad');
        $activeSheet->getStyle('C3')->applyFromArray($estilosEncabezados);

        foreach ($inventorySummary as $item) {
            $activeSheet->setCellValue('A' . $filaInicial, $item->product->name ?? 'N/A');
            $activeSheet->setCellValue('B' . $filaInicial, number_format((float) $item->total, 3));
            $activeSheet->setCellValue('C' . $filaInicial, $item->product->unit ?? 'N/A');
            $filaInicial++;
        }

        $activeSheet->getColumnDimension('A')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getStyle('B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [
            'spreadsheet' => $spreadsheet,
            'fileName'    => 'Inventario_Success.xlsx',
            'hasCharts'   => false,
        ];
    }

    public function generateProteinSpreadsheet(): array
    {
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();
        $activeSheet->setTitle('Reporte de Proteína');

        $data = DB::table('cli')
            ->select(
                'cli.bag_number',
                'cli.packing_list',
                'cli.net_weight',
                'cli.weight_per_unit',
                'cli.protein',
                'products.name as pName',
                'inventory.batch as batch'
            )
            ->join('inventory', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->whereNotNull('cli.protein')
            ->orderBy('cli.cli_id', 'desc')
            ->get();

        $estilosEncabezados = [
            'font' => [
                'bold'  => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size'  => 12,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => '008000'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => Color::COLOR_WHITE],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];

        $estilosTotal = [
            'font' => [
                'bold'  => true,
                'color' => ['argb' => '004D20'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'E8F5E9'],
            ],
            'borders' => [
                'top'    => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => '008000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color'       => ['argb' => '008000'],
                ],
            ],
        ];

        $fechaConsulta = now()->toDateTimeString();
        $activeSheet->setCellValue('A1', 'Fecha de consulta: ' . $fechaConsulta);
        $activeSheet->setCellValue('A3', 'Producto');
        $activeSheet->setCellValue('B3', 'Lote');
        $activeSheet->setCellValue('C3', 'Barcina');
        $activeSheet->setCellValue('D3', 'Packing List');
        $activeSheet->setCellValue('E3', 'Peso Real (kg)');
        $activeSheet->setCellValue('F3', 'Proteína (%)');
        $activeSheet->setCellValue('G3', 'Rango');

        foreach (['A3', 'B3', 'C3', 'D3', 'E3', 'F3', 'G3'] as $col) {
            $activeSheet->getStyle($col)->applyFromArray($estilosEncabezados);
        }

        $activeSheet->getRowDimension('3')->setRowHeight(20);

        $counts = [
            '< 30'      => 0,
            '30 a 34.9' => 0,
            '35 a 39.9' => 0,
            '>= 40'     => 0,
        ];

        $totalPesoReal = 0;
        $filaInicial = 4;
        foreach ($data as $item) {
            $rango = 'N/A';
            if (is_numeric($item->protein)) {
                $val = (float) $item->protein;
                if ($val < 30) {
                    $rango = '< 30';
                } elseif ($val >= 30 && $val < 35) {
                    $rango = '30 a 34.9';
                } elseif ($val >= 35 && $val < 40) {
                    $rango = '35 a 39.9';
                } elseif ($val >= 40) {
                    $rango = '>= 40';
                }

                if (isset($counts[$rango])) {
                    $counts[$rango]++;
                }
            }

            $pesoReal = (float) ($item->net_weight ?: $item->weight_per_unit);
            $totalPesoReal += $pesoReal;
            $packingList = ($item->packing_list !== null && $item->packing_list !== '') ? $item->packing_list : '-';

            $activeSheet->setCellValue('A' . $filaInicial, $item->pName);
            $activeSheet->setCellValue('B' . $filaInicial, $item->batch);
            $activeSheet->setCellValue('C' . $filaInicial, $item->bag_number);
            $activeSheet->setCellValue('D' . $filaInicial, $packingList);
            $activeSheet->setCellValue('E' . $filaInicial, round($pesoReal, 2));
            $activeSheet->setCellValue('F' . $filaInicial, $item->protein);
            $activeSheet->setCellValue('G' . $filaInicial, $rango);
            $filaInicial++;
        }

        $filaTotal = $filaInicial;
        $activeSheet->setCellValue('D' . $filaTotal, 'Total Peso Real:');
        if ($data->isNotEmpty()) {
            $activeSheet->setCellValue('E' . $filaTotal, '=SUM(E4:E' . ($filaTotal - 1) . ')');
        } else {
            $activeSheet->setCellValue('E' . $filaTotal, 0);
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $activeSheet->getStyle($col . $filaTotal)->applyFromArray($estilosTotal);
        }
        $activeSheet->getStyle('D' . $filaTotal)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $activeSheet->getStyle('E' . $filaTotal)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $activeSheet->getStyle('E' . $filaTotal)->getNumberFormat()->setFormatCode('#,##0.00');

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $activeSheet->getColumnDimension($col)->setAutoSize(true);
        }

        if ($filaTotal > 4) {
            $activeSheet->getStyle('D4:D' . ($filaTotal - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $activeSheet->getStyle('E4:E' . ($filaTotal - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $activeSheet->getStyle('E4:E' . ($filaTotal - 1))->getNumberFormat()->setFormatCode('#,##0.00');
            $activeSheet->getStyle('F4:F' . ($filaTotal - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $activeSheet->getStyle('G4:G' . ($filaTotal - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $activeSheet->setCellValue('I3', 'Rango');
        $activeSheet->setCellValue('J3', 'Cantidad');
        $activeSheet->getStyle('I3:J3')->applyFromArray($estilosEncabezados);

        $activeSheet->setCellValue('I4', '< 30');
        $activeSheet->setCellValue('J4', $counts['< 30']);
        $activeSheet->setCellValue('I5', '30 a 34.9');
        $activeSheet->setCellValue('J5', $counts['30 a 34.9']);
        $activeSheet->setCellValue('I6', '35 a 39.9');
        $activeSheet->setCellValue('J6', $counts['35 a 39.9']);
        $activeSheet->setCellValue('I7', '>= 40');
        $activeSheet->setCellValue('J7', $counts['>= 40']);

        $activeSheet->setCellValue('I8', 'Total Barcinas');
        $activeSheet->setCellValue('J8', '=SUM(J4:J7)');
        $activeSheet->getStyle('I8:J8')->applyFromArray($estilosTotal);

        $activeSheet->setCellValue('I10', 'Total Peso Real (kg)');
        $activeSheet->setCellValue('J10', round($totalPesoReal, 2));
        $activeSheet->getStyle('I10:J10')->applyFromArray($estilosTotal);
        $activeSheet->getStyle('J10')->getNumberFormat()->setFormatCode('#,##0.00');

        foreach (['I', 'J'] as $col) {
            $activeSheet->getColumnDimension($col)->setAutoSize(true);
        }
        $activeSheet->getStyle('J4:J8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $activeSheet->getStyle('J10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheetTitle = "'Reporte de Proteína'";
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $sheetTitle . '!$J$3', null, 1),
        ];
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $sheetTitle . '!$I$4:$I$7', null, 4),
        ];
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $sheetTitle . '!$J$4:$J$7', null, 4),
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Distribución de Proteína');
        $chart = new Chart(
            'grafica_proteina',
            $title,
            $legend,
            $plotArea,
            true,
            0,
            null,
            null
        );

        $chart->setTopLeftPosition('L3');
        $chart->setBottomRightPosition('T18');
        $activeSheet->addChart($chart);

        return [
            'spreadsheet' => $spreadsheet,
            'fileName'    => 'Reporte_Proteina.xlsx',
            'hasCharts'   => true,
        ];
    }

    protected function getMovementOutputs(?int $year, int $months): Collection
    {
        $outputsQuery = DB::table('product_outputs')
            ->select(
                DB::raw("concat(DAY(outputs.updated_at), '-', MONTH(outputs.updated_at), '-', YEAR(outputs.updated_at)) as outputDate"),
                'customers.name as cName',
                'products.name as pName',
                'outputs.vendedor as vendedor',
                'product_outputs.warehouse_batch',
                'product_outputs.quantity'
            )
            ->join('outputs', 'outputs.output_id', '=', 'product_outputs.output_id')
            ->join('customers', 'customers.customer_id', '=', 'outputs.customer_id')
            ->join('products', 'products.product_id', '=', 'product_outputs.product_id')
            ->orderByDesc('outputs.updated_at');

        if ($year) {
            $outputsQuery->whereYear('outputs.updated_at', $year);
        } else {
            $outputsQuery->where('outputs.updated_at', '>=', now()->subMonths($months));
        }

        return $outputsQuery->get();
    }

    protected function getMovementInputs(?int $year, int $months): Collection
    {
        $inputsQuery = DB::table('product_inputs')
            ->select(
                DB::raw("concat(DAY(inputs.updated_at), '-', MONTH(inputs.updated_at), '-', YEAR(inputs.updated_at)) as inputDate"),
                'suppliers.name as sName',
                'products.name as pName',
                'product_inputs.warehouse_batch',
                'product_inputs.quantity'
            )
            ->join('inputs', 'inputs.input_id', '=', 'product_inputs.input_id')
            ->join('suppliers', 'suppliers.supplier_id', '=', 'inputs.supplier_id')
            ->join('products', 'products.product_id', '=', 'product_inputs.product_id')
            ->orderByDesc('inputs.updated_at');

        if ($year) {
            $inputsQuery->whereYear('inputs.updated_at', $year);
        } else {
            $inputsQuery->where('inputs.updated_at', '>=', now()->subMonths($months));
        }

        return $inputsQuery->get();
    }
}
