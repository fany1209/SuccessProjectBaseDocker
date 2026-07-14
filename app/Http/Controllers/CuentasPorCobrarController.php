<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CxcDetail;
use App\Models\CxcPayment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CuentasPorCobrarController extends Controller
{
    public function index()
    {
        // Calculate overdue invoices for the alert
        $sales = DB::table('cxc_details as cxc')
            ->join('sales as s', 's.sale_id', '=', 'cxc.sale_id')
            ->leftJoin('sale_detail as sd', 'sd.sale_id', '=', 's.sale_id')
            ->select(
                'cxc.id as cxc_id',
                's.date as fecha_emision',
                's.term',
                'cxc.estatus',
                'cxc.is_canceled',
                DB::raw("SUM(sd.quantity * sd.cost * IF(sd.has_tax = 1, 1.16, 1)) as total_venta")
            )
            ->groupBy('cxc.id', 's.date', 's.term', 'cxc.estatus', 'cxc.is_canceled')
            ->get();

        $payments = DB::table('cxc_payments')
            ->select('cxc_detail_id', DB::raw("SUM(amount) as total_pagado"))
            ->groupBy('cxc_detail_id')
            ->get()
            ->keyBy('cxc_detail_id');

        $pendingPaymentsCount = 0;
        $today = now()->startOfDay();

        foreach ($sales as $sale) {
            $pagado = isset($payments[$sale->cxc_id]) ? $payments[$sale->cxc_id]->total_pagado : 0;
            $saldo = round($sale->total_venta - $pagado, 2);

            if (!$sale->is_canceled && $sale->estatus !== 'Pagado' && $saldo > 0) {
                $daysToAdd = 0;
                if (!empty($sale->term)) {
                    preg_match('/\d+/', $sale->term, $matches);
                    if (isset($matches[0])) {
                        $daysToAdd = (int) $matches[0];
                    }
                }
                
                $dueDate = \Carbon\Carbon::parse($sale->fecha_emision)->addDays($daysToAdd)->startOfDay();
                if ($today->gt($dueDate)) {
                    $pendingPaymentsCount++;
                }
            }
        }

        return view('finance.cuentas_por_cobrar.index', compact('pendingPaymentsCount'));
    }

    public function dashboard(Request $request)
    {
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);
        $targetDate = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();

        // Fetch all sales in finance
        $sales = DB::table('cxc_details as cxc')
            ->join('sales as s', 's.sale_id', '=', 'cxc.sale_id')
            ->leftJoin('sale_detail as sd', 'sd.sale_id', '=', 's.sale_id')
            ->select(
                'cxc.id as cxc_id',
                's.sale_id',
                's.customer_id',
                's.prospect_id',
                's.date as fecha_emision',
                's.term',
                'cxc.estatus',
                'cxc.is_canceled',
                DB::raw("SUM(sd.quantity * sd.cost * IF(sd.has_tax = 1, 1.16, 1)) as total_venta")
            )
            ->groupBy(
                'cxc.id', 's.sale_id', 's.customer_id', 's.prospect_id', 's.date', 's.term', 'cxc.estatus', 'cxc.is_canceled'
            )
            ->get();

        // Fetch all payments
        $payments = DB::table('cxc_payments')
            ->select('cxc_detail_id', DB::raw("SUM(amount) as total_pagado"))
            ->groupBy('cxc_detail_id')
            ->get()
            ->keyBy('cxc_detail_id');

        $facturasVencidas = 0;
        $clientesAdeudoSet = [];
        $today = now()->startOfDay();
        
        // Structure for chart: last 6 months ending in targetDate
        $monthsArray = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $targetDate->copy()->startOfMonth()->subMonths($i);
            $key = $date->format('Y-m'); // e.g. "2026-05"
            $monthsArray[$key] = [
                'label' => $date->translatedFormat('F Y'),
                'total_facturado' => 0,
                'total_cobrado' => 0
            ];
        }

        foreach ($sales as $sale) {
            $pagado = isset($payments[$sale->cxc_id]) ? $payments[$sale->cxc_id]->total_pagado : 0;
            $saldo = round($sale->total_venta - $pagado, 2);

            $saleMonthStr = \Carbon\Carbon::parse($sale->fecha_emision)->format('Y-m');

            // Metrics 1 & 2: Vencidas y Adeudo (Only for the selected month/year)
            $isSelectedMonth = (\Carbon\Carbon::parse($sale->fecha_emision)->month == $selectedMonth && \Carbon\Carbon::parse($sale->fecha_emision)->year == $selectedYear);

            if ($isSelectedMonth && !$sale->is_canceled && $sale->estatus !== 'Pagado') {
                if ($saldo > 0) {
                    $clientId = 'c_' . $sale->customer_id . '_p_' . $sale->prospect_id;
                    $clientesAdeudoSet[$clientId] = true;
                }

                // Check vencida
                $daysToAdd = 0;
                if (!empty($sale->term)) {
                    preg_match('/\d+/', $sale->term, $matches);
                    if (isset($matches[0])) {
                        $daysToAdd = (int) $matches[0];
                    }
                }
                
                $dueDate = \Carbon\Carbon::parse($sale->fecha_emision)->addDays($daysToAdd)->startOfDay();
                if ($today->gt($dueDate)) {
                    $facturasVencidas++;
                }
            }

            // Metric 3: Chart Data
            if (!$sale->is_canceled) {
                if (isset($monthsArray[$saleMonthStr])) {
                    $monthsArray[$saleMonthStr]['total_facturado'] += $sale->total_venta;
                    $monthsArray[$saleMonthStr]['total_cobrado'] += $pagado;
                }
            }
        }

        $clientesAdeudo = count($clientesAdeudoSet);

        // Compute percentages for chart
        $chartLabels = [];
        $chartData = [];
        foreach ($monthsArray as $key => $data) {
            $chartLabels[] = ucfirst($data['label']);
            if ($data['total_facturado'] > 0) {
                $pct = ($data['total_cobrado'] / $data['total_facturado']) * 100;
                $chartData[] = round($pct, 2);
            } else {
                $chartData[] = 0; // Or null if you don't want to show
            }
        }

        // Global percentage for the selected month
        $currentMonthKey = $targetDate->format('Y-m');
        $currentMonthPct = 0;
        if ($monthsArray[$currentMonthKey]['total_facturado'] > 0) {
            $currentMonthPct = round(($monthsArray[$currentMonthKey]['total_cobrado'] / $monthsArray[$currentMonthKey]['total_facturado']) * 100, 2);
        }

        return view('finance.cuentas_por_cobrar.dashboard', compact(
            'facturasVencidas', 
            'clientesAdeudo', 
            'currentMonthPct', 
            'chartLabels', 
            'chartData',
            'selectedMonth',
            'selectedYear'
        ));
    }

    public function clientes()
    {
        return view('finance.cuentas_por_cobrar.clientes');
    }

    public function datatable(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        // Query only sales that have a corresponding CxcDetail record
        $query = DB::table('cxc_details as cxc')
            ->join('sales as s', 's.sale_id', '=', 'cxc.sale_id');

        if ($month) {
            $query->whereMonth('s.date', $month);
        }
        if ($year) {
            $query->whereYear('s.date', $year);
        }

        $sales = $query->leftJoin('customers as c', 's.customer_id', '=', 'c.customer_id')
            ->leftJoin('prospects as p', 's.prospect_id', '=', 'p.prospect_id')
            ->leftJoin('users as u', 's.user_id', '=', 'u.id')
            ->leftJoin('sale_detail as sd', 'sd.sale_id', '=', 's.sale_id')
            ->select(
                'cxc.id as cxc_id',
                's.sale_id',
                's.folio',
                DB::raw("COALESCE(c.name, p.name, 'Sin Cliente') as cliente_name"),
                'cxc.documento',
                'cxc.metodo_pago',
                'cxc.descripcion',
                'cxc.estatus',
                DB::raw("COALESCE(s.seller, u.name) as asesor"),
                's.date as fecha_emision',
                'cxc.fecha_conclusion',
                'cxc.is_canceled',
                DB::raw("SUM(sd.quantity * sd.cost * IF(sd.has_tax = 1, 1.16, 1)) as total_venta")
            )
            ->groupBy(
                'cxc.id', 's.sale_id', 's.folio', 'cliente_name', 'cxc.documento', 'cxc.metodo_pago', 
                'cxc.descripcion', 'cxc.estatus', 'asesor', 's.date', 'cxc.fecha_conclusion', 'cxc.is_canceled'
            )
            ->orderByDesc('s.sale_id')
            ->get();

        // Subquery for payments
        $payments = DB::table('cxc_payments')
            ->select('cxc_detail_id', DB::raw("SUM(amount) as total_pagado"))
            ->groupBy('cxc_detail_id')
            ->get()
            ->keyBy('cxc_detail_id');

        foreach ($sales as $sale) {
            $pagado = isset($payments[$sale->cxc_id]) ? $payments[$sale->cxc_id]->total_pagado : 0;
            $sale->total_venta = round($sale->total_venta, 2);
            $sale->saldo = round($sale->total_venta - $pagado, 2);
            $sale->pagado = round($pagado, 2);
            $sale->is_canceled = (bool) $sale->is_canceled;
        }

        return response()->json(['data' => $sales]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'documento' => 'nullable|string|max:50',
            'metodo_pago' => 'required|in:N/A,PUE,PPD',
            'fecha_conclusion' => 'nullable|date',
            'descripcion' => 'nullable|string'
        ]);

        $cxc = CxcDetail::findOrFail($id);
        
        if ($cxc->is_canceled) {
            return response()->json(['success' => false, 'message' => 'No se puede editar una cuenta cancelada.'], 403);
        }

        $cxc->update([
            'documento' => $request->documento ?: 'Factura',
            'metodo_pago' => $request->metodo_pago,
            'fecha_conclusion' => $request->fecha_conclusion,
            'descripcion' => $request->descripcion
        ]);

        return response()->json(['success' => true, 'message' => 'Actualizado correctamente']);
    }

    public function cancel($id)
    {
        $cxc = CxcDetail::findOrFail($id);
        $cxc->update(['is_canceled' => true]);

        return response()->json(['success' => true, 'message' => 'Cuenta cancelada correctamente']);
    }

    public function getPayments($id)
    {
        $cxc = CxcDetail::findOrFail($id);
        $payments = $cxc->payments()->orderByDesc('date')->get();
        return response()->json(['success' => true, 'payments' => $payments]);
    }

    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:25600',
        ]);

        $cxc = CxcDetail::findOrFail($id);

        if ($cxc->is_canceled) {
            return response()->json(['success' => false, 'message' => 'No se pueden añadir pagos a una cuenta cancelada.'], 403);
        }

        if ($cxc->estatus === 'Pagado') {
            return response()->json(['success' => false, 'message' => 'Esta cuenta ya se encuentra pagada.'], 403);
        }

        $comprobanteName = null;
        if ($request->hasFile('comprobante')) {
            $file = $request->file('comprobante');
            $comprobanteName = time() . '_' . $file->getClientOriginalName();
            $file->move(base_path('storage/app/public/comprobantes'), $comprobanteName);
        }

        $cxc->payments()->create([
            'amount' => $request->amount,
            'date' => $request->date,
            'comprobante' => $comprobanteName
        ]);

        // Calculate new balance and estatus
        $totalVenta = DB::table('sale_detail')
            ->where('sale_id', $cxc->sale_id)
            ->sum(DB::raw("quantity * cost * IF(has_tax = 1, 1.16, 1)"));
        
        $pagado = $cxc->payments()->sum('amount');
        $saldo = round($totalVenta - $pagado, 2);

        $newEstatus = 'Pendiente';
        if ($saldo <= 0) {
            $newEstatus = 'Pagado';
        } elseif ($pagado > 0) {
            $newEstatus = 'Parcial';
        }

        $cxc->update(['estatus' => $newEstatus]);

        return response()->json(['success' => true, 'message' => 'Pago añadido correctamente']);
    }

    public function exportExcel(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        // Query exact same data as datatable
        $query = DB::table('cxc_details as cxc')
            ->join('sales as s', 's.sale_id', '=', 'cxc.sale_id');

        if ($month) {
            $query->whereMonth('s.date', $month);
        }
        if ($year) {
            $query->whereYear('s.date', $year);
        }

        $sales = $query->leftJoin('customers as c', 's.customer_id', '=', 'c.customer_id')
            ->leftJoin('prospects as p', 's.prospect_id', '=', 'p.prospect_id')
            ->leftJoin('users as u', 's.user_id', '=', 'u.id')
            ->leftJoin('sale_detail as sd', 'sd.sale_id', '=', 's.sale_id')
            ->select(
                'cxc.id as cxc_id',
                's.sale_id',
                's.folio',
                DB::raw("COALESCE(c.name, p.name, 'Sin Cliente') as cliente_name"),
                'cxc.documento',
                'cxc.metodo_pago',
                'cxc.descripcion',
                'cxc.estatus',
                DB::raw("COALESCE(s.seller, u.name) as asesor"),
                's.date as fecha_emision',
                'cxc.fecha_conclusion',
                'cxc.is_canceled',
                DB::raw("SUM(sd.quantity * sd.cost * IF(sd.has_tax = 1, 1.16, 1)) as total_venta")
            )
            ->groupBy(
                'cxc.id', 's.sale_id', 's.folio', 'cliente_name', 'cxc.documento', 'cxc.metodo_pago', 
                'cxc.descripcion', 'cxc.estatus', 'asesor', 's.date', 'cxc.fecha_conclusion', 'cxc.is_canceled'
            )
            ->orderByDesc('s.sale_id')
            ->get();

        // Subquery for payments
        $payments = DB::table('cxc_payments')
            ->select('cxc_detail_id', DB::raw("SUM(amount) as total_pagado"))
            ->groupBy('cxc_detail_id')
            ->get()
            ->keyBy('cxc_detail_id');

        // Initialize Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cuentas por Cobrar');

        // Add Main Title
        $sheet->setCellValue('A1', 'Cuentas por cobrar');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF217346'], // Darker Green for Title
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Headers
        $headers = ['Remisión', 'Fecha Emisión', 'Cliente', 'Asesor', 'Documento', 'Método Pago', 'Estatus', 'Fecha Conclusión', 'Total', 'Monto Abonado', 'Saldo Restante'];
        $columnLetter = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnLetter . '2', $header);
            $columnLetter++;
        }

        // Header Styling
        $lastCol = chr(ord('A') + count($headers) - 1);
        $headerRange = 'A2:' . $lastCol . '2';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF198754'], // Green
            ]
        ]);

        // Add Data
        $row = 3;
        foreach ($sales as $sale) {
            $pagado = isset($payments[$sale->cxc_id]) ? $payments[$sale->cxc_id]->total_pagado : 0;
            $totalVenta = round($sale->total_venta, 2);
            $saldo = round($totalVenta - $pagado, 2);
            $pagado = round($pagado, 2);

            if ($sale->is_canceled) {
                $saldo = 0; // Or whatever is needed for canceled, UI shows 0
            }

            $sheet->setCellValue('A' . $row, $sale->folio);
            $sheet->setCellValue('B' . $row, $sale->fecha_emision);
            $sheet->setCellValue('C' . $row, $sale->cliente_name);
            $sheet->setCellValue('D' . $row, $sale->asesor);
            $sheet->setCellValue('E' . $row, $sale->documento);
            $sheet->setCellValue('F' . $row, $sale->metodo_pago);
            
            // Estatus handling
            $estatusCell = 'G' . $row;
            if ($sale->is_canceled) {
                $sheet->setCellValue($estatusCell, 'CANCELADA');
                $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB(Color::COLOR_RED);
                $sheet->getStyle($estatusCell)->getFont()->setBold(true);
            } else {
                $sheet->setCellValue($estatusCell, strtoupper($sale->estatus));
                if ($sale->estatus === 'Pagado') {
                    $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB('FF157347');
                } elseif ($sale->estatus === 'Pendiente') {
                    $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB('FF0000FF');
                } elseif ($sale->estatus === 'Parcial') {
                    $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB('FFF59E0B');
                }
            }

            $sheet->setCellValue('H' . $row, $sale->fecha_conclusion);
            $sheet->setCellValue('I' . $row, $totalVenta);
            $sheet->setCellValue('J' . $row, $pagado);
            $sheet->setCellValue('K' . $row, $saldo);

            // Formatting for currency
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            
            // Text color for saldo
            if (!$sale->is_canceled) {
                if ($saldo <= 0) {
                    $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB('FF157347'); // Green
                } else {
                    $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB(Color::COLOR_RED);
                }
            } else {
                 $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB(Color::COLOR_RED);
            }

            $row++;
        }

        // Auto size columns
        foreach (range('A', $lastCol) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Write to stream
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Cuentas_Por_Cobrar_' . date('Y-m-d') . '.xlsx';

        // Clean output buffer to prevent corrupted file if anything was echoed
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
