<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory;
use App\Models\ProductOutputs;

class XlsController extends Controller
{
    public function reports(Request $request)
    {
        $year = $request->input('year');
        $months = 6;
        
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();
        
        if ($year) {
            $activeSheet->setTitle('Salidas ' . $year);
        } else {
            $activeSheet->setTitle('Salidas '.$months.' meses');
        }

        $outputsQuery = DB::table('product_outputs')
            ->select(
                DB::raw("concat(DAY(outputs.updated_at), '-', MONTH(outputs.updated_at), '-', YEAR(outputs.updated_at)) as outputDate"),
                'customers.name as cName',
                'products.name as pName',
                'outputs.vendedor as vendedor', 
                'product_outputs.warehouse_batch',
                'product_outputs.quantity'
            )
            ->join('outputs','outputs.output_id','=','product_outputs.output_id')
            ->join('customers','customers.customer_id','=','outputs.customer_id')
            ->join('products','products.product_id','=','product_outputs.product_id')
            ->orderByDesc('outputs.updated_at');

        if ($year) {
            $outputsQuery->whereYear('outputs.updated_at', $year);
        } else {
            $outputsQuery->where('outputs.updated_at', '>=', now()->subMonths($months));
        }
        $outputs = $outputsQuery->get();

        $estilosEncabezados = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 12,
                'name' => 'Segoe UI',
            ],
            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['argb' => '2ECC71'],
                'endColor' => ['argb' => '27AE60'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];

        $fechaConsulta = now();
        $activeSheet->setCellValue('A1', 'Fecha de consulta: '.$fechaConsulta);
        $activeSheet->setCellValue('A3', 'Fecha de Salida');
        $activeSheet->setCellValue('B3', 'Cliente');
        $activeSheet->setCellValue('C3', 'Producto');
        $activeSheet->setCellValue('D3', 'Vendedor');
        $activeSheet->setCellValue('E3', 'Lote');
        $activeSheet->setCellValue('F3', 'Cantidad');

        foreach (['A3','B3','C3','D3','E3','F3'] as $cell) {
            $activeSheet->getStyle($cell)->applyFromArray($estilosEncabezados);
        }

        $activeSheet->getRowDimension('3')->setRowHeight(20);

        $filaInicial = 4;

        foreach($outputs as $item){
            $activeSheet->setCellValue('A'.$filaInicial, $item->outputDate);
            $activeSheet->setCellValue('B'.$filaInicial, $item->cName);
            $activeSheet->setCellValue('C'.$filaInicial, $item->pName);
            $activeSheet->setCellValue('D'.$filaInicial, $item->vendedor); 
            $activeSheet->setCellValue('E'.$filaInicial, $item->warehouse_batch);
            $activeSheet->setCellValue('F'.$filaInicial, $item->quantity);
            $filaInicial++;
        }

        foreach (['A','B','C','D','E','F'] as $col) {
            $activeSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $activeSheet->getStyle('C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $activeSheet = $spreadsheet->createSheet();
        
        if ($year) {
            $activeSheet->setTitle('Entradas ' . $year);
        } else {
            $activeSheet->setTitle('Entradas '.$months.' meses');
        }

        $inputsQuery = DB::table('product_inputs')
            ->select(
                DB::raw("concat(DAY(inputs.updated_at), '-', MONTH(inputs.updated_at), '-', YEAR(inputs.updated_at)) as inputDate"),
                'suppliers.name as sName',
                'products.name as pName',
                'product_inputs.warehouse_batch',
                'product_inputs.quantity'
            )
            ->join('inputs','inputs.input_id','=','product_inputs.input_id')
            ->join('suppliers','suppliers.supplier_id','=','inputs.supplier_id')
            ->join('products','products.product_id','=','product_inputs.product_id')
            ->orderByDesc('inputs.updated_at');

        if ($year) {
            $inputsQuery->whereYear('inputs.updated_at', $year);
        } else {
            $inputsQuery->where('inputs.updated_at', '>=', now()->subMonths($months));
        }
        $inputs = $inputsQuery->get();

        $activeSheet->setCellValue('A1', 'Fecha de consulta: '.$fechaConsulta);
        $activeSheet->setCellValue('A3', 'Fecha de Entrada');
        $activeSheet->setCellValue('B3', 'Proveedor');
        $activeSheet->setCellValue('C3', 'Producto');
        $activeSheet->setCellValue('D3', 'Lote');
        $activeSheet->setCellValue('E3', 'Cantidad');

        foreach (['A3','B3','C3','D3','E3'] as $cell) {
            $activeSheet->getStyle($cell)->applyFromArray($estilosEncabezados);
        }

        $filaInicial = 4;

        foreach($inputs as $item){
            $activeSheet->setCellValue('A'.$filaInicial, $item->inputDate);
            $activeSheet->setCellValue('B'.$filaInicial, $item->sName);
            $activeSheet->setCellValue('C'.$filaInicial, $item->pName);
            $activeSheet->setCellValue('D'.$filaInicial, $item->warehouse_batch);
            $activeSheet->setCellValue('E'.$filaInicial, $item->quantity);
            $filaInicial++;
        }

        foreach (['A','B','C','D','E'] as $col) {
            $activeSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = $year ? 'Reporte_movimientos_'.$year.'.xlsx' : 'Reporte_movimientos.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$fileName);
        $writer->save("php://output");
    }

    public function inventoryXls(){
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();
        $activeSheet->setTitle('Por Lote');

        $inventory = Inventory::all();

        $estilosEncabezados = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE], // Color de la fuente
                'size' => 12, // Tamaño de la fuente
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, // Tipo de relleno
                'startColor' => ['argb' => '000000'], // Color de fondo (azul)
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // Borde delgado
                    'color' => ['argb' => Color::COLOR_WHITE], // Color del borde
                ],
            ],
        ];

        $fechaConsulta = now();

        $activeSheet->setCellValue('A1', 'Fecha de consulta: '.$fechaConsulta);

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

        foreach($inventory as $item){
            $activeSheet->setCellValue('A'.$filaInicial, $item->product->name);
            $activeSheet->setCellValue('B'.$filaInicial, $item->batch);
            $activeSheet->setCellValue('C'.$filaInicial, number_format($item->stock,3));
            $activeSheet->setCellValue('D'.$filaInicial, $item->product->unit);
            $filaInicial++;
        }

        $activeSheet->getColumnDimension('A')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);

        $activeSheet->getStyle('C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        /* -------------------------------------------- HOJA DE TOTALES -------------------------------------------- */
        $activeSheet = $spreadsheet->createSheet();
        $activeSheet->setTitle('Totales');

        $inventorySummary = Inventory::select('product_id', DB::raw('SUM(stock) as total'))
                            ->groupBy('product_id')
                            ->orderBy('total', 'desc')
                            ->get();

        $filaInicial = 4;

        $activeSheet->setCellValue('A1', 'Fecha de consulta: '.$fechaConsulta);

        $activeSheet->setCellValue('A3', 'Producto');
        $activeSheet->getStyle('A3')->applyFromArray($estilosEncabezados);
        $activeSheet->setCellValue('B3', 'Total');
        $activeSheet->getStyle('B3')->applyFromArray($estilosEncabezados);
        $activeSheet->setCellValue('C3', 'Unidad');
        $activeSheet->getStyle('C3')->applyFromArray($estilosEncabezados);

        foreach($inventorySummary as $item){
            $activeSheet->setCellValue('A'.$filaInicial, $item->product->name);
            $activeSheet->setCellValue('B'.$filaInicial, number_format($item->total,3));
            $activeSheet->setCellValue('C'.$filaInicial, $item->product->unit);
            $filaInicial++;
        }

        $activeSheet->getColumnDimension('A')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);

        $activeSheet->getStyle('B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Guardar el archivo en formato .xlsx
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Inventario_Success.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$fileName.'');
        $writer->save("php://output");
    }
}
