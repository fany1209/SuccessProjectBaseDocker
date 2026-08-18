<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CxpDetail;
use App\Models\CxpPayment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CuentasPorPagarController extends Controller
{
    public function index()
    {
        // Calculate overdue invoices for the alert
        $facturas = DB::table('cxp_details as cxp')
            ->join('facturas as f', 'f.factura_id', '=', 'cxp.factura_id')
            ->select(
                'cxp.id as cxp_id',
                'cxp.fecha_pago',
                'cxp.estatus',
                'cxp.is_canceled',
                'f.total'
            )
            ->get();

        $payments = DB::table('cxp_payments')
            ->select('cxp_detail_id', DB::raw("SUM(amount) as total_pagado"))
            ->groupBy('cxp_detail_id')
            ->get()
            ->keyBy('cxp_detail_id');

        $pendingPaymentsCount = 0;
        $today = now()->startOfDay();

        foreach ($facturas as $factura) { 
            $pagado = isset($payments[$factura->cxp_id]) ? $payments[$factura->cxp_id]->total_pagado : 0;
            $saldo = round($factura->total - $pagado, 2);

            if (!$factura->is_canceled && $factura->estatus !== 'PAGADO' && $saldo > 0) {
                if (!empty($factura->fecha_pago)) {
                    $dueDate = \Carbon\Carbon::parse($factura->fecha_pago)->startOfDay();
                    if ($today->gt($dueDate)) {
                        $pendingPaymentsCount++;
                    }
                }
            }
        }

        return view('finance.cuentas_por_pagar.index', compact('pendingPaymentsCount'));
    }

    public function dashboard(Request $request)
    {
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);
        $targetDate = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();

        $facturas = DB::table('cxp_details as cxp')
            ->join('facturas as f', 'f.factura_id', '=', 'cxp.factura_id')
            ->select(
                'cxp.id as cxp_id',
                'cxp.fecha_pago',
                'cxp.estatus',
                'cxp.is_canceled',
                'f.empresa',
                'f.total',
                'f.fecha_factura'
            )
            ->get();

        $paymentsList = DB::table('cxp_payments')
            ->select('cxp_detail_id', 'amount', 'date')
            ->get();

        $paymentsGrouped = $paymentsList->groupBy('cxp_detail_id');

        $today = now()->startOfDay();
        $threeDaysFromNow = now()->addDays(3)->endOfDay();

        // KPIs
        $totalPorPagar = 0;
        $pagosVencidos = 0;
        $proximosVencer = 0;
        $pagadoEsteMes = 0;

        // Gráfica: Proveedores
        $proveedoresMap = [];

        // Gráfica: Antigüedad
        $agingBuckets = [
            '0_30' => 0,
            '31_60' => 0,
            '61_90' => 0,
            '90_plus' => 0
        ];

        foreach ($facturas as $factura) {
            $pagos = isset($paymentsGrouped[$factura->cxp_id]) ? $paymentsGrouped[$factura->cxp_id] : collect();
            $pagado = $pagos->sum('amount');
            $saldo = round($factura->total - $pagado, 2);

            if (!$factura->is_canceled && $factura->estatus !== 'PAGADO' && $saldo > 0) {
                // KPI 1: Total por pagar
                $totalPorPagar += $saldo;

                if (!empty($factura->fecha_pago)) {
                    $dueDate = \Carbon\Carbon::parse($factura->fecha_pago)->startOfDay();
                    
                    // KPI 2: Pagos vencidos
                    if ($today->gt($dueDate)) {
                        $pagosVencidos += $saldo;
                    } 
                    // KPI 3: Próximos a vencer (entre hoy y +3 días)
                    elseif ($dueDate->between($today, $threeDaysFromNow)) {
                        $proximosVencer += $saldo;
                    }
                }

                // Chart 1: Proveedores
                $emp = $factura->empresa ?: 'Desconocido';
                if (!isset($proveedoresMap[$emp])) {
                    $proveedoresMap[$emp] = 0;
                }
                $proveedoresMap[$emp] += $saldo;

                // Chart 3: Antigüedad (Basado en fecha_factura)
                if (!empty($factura->fecha_factura)) {
                    $ffDate = \Carbon\Carbon::parse($factura->fecha_factura)->startOfDay();
                    $daysOld = $ffDate->diffInDays($today, false); // if positive, it means in the past
                    
                    if ($daysOld <= 30) {
                        $agingBuckets['0_30'] += $saldo;
                    } elseif ($daysOld <= 60) {
                        $agingBuckets['31_60'] += $saldo;
                    } elseif ($daysOld <= 90) {
                        $agingBuckets['61_90'] += $saldo;
                    } else {
                        $agingBuckets['90_plus'] += $saldo;
                    }
                } else {
                    $agingBuckets['0_30'] += $saldo; // default if no date
                }
            }
        }

        // KPI 4 & Chart 2: Pagos por mes
        $monthsArray = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $targetDate->copy()->startOfMonth()->subMonths($i);
            $key = $date->format('Y-m');
            $monthsArray[$key] = [
                'label' => $date->translatedFormat('F Y'),
                'total_pagado' => 0
            ];
        }

        $currentMonthKey = $targetDate->format('Y-m');

        foreach ($paymentsList as $payment) {
            $paymentMonthStr = \Carbon\Carbon::parse($payment->date)->format('Y-m');
            
            // KPI 4
            if ($paymentMonthStr === $currentMonthKey) {
                $pagadoEsteMes += $payment->amount;
            }

            // Chart 2
            if (isset($monthsArray[$paymentMonthStr])) {
                $monthsArray[$paymentMonthStr]['total_pagado'] += $payment->amount;
            }
        }

        // Format Provider Chart Data (Top 10 max to avoid clutter)
        arsort($proveedoresMap);
        $topProveedores = array_slice($proveedoresMap, 0, 10, true);
        
        $proveedoresLabels = array_keys($topProveedores);
        $proveedoresData = array_values($topProveedores);

        // Format Months Chart Data
        $mesesLabels = [];
        $mesesData = [];
        foreach ($monthsArray as $data) {
            $mesesLabels[] = ucfirst($data['label']);
            $mesesData[] = round($data['total_pagado'], 2);
        }

        // Format Aging Chart Data
        $agingData = [
            round($agingBuckets['0_30'], 2),
            round($agingBuckets['31_60'], 2),
            round($agingBuckets['61_90'], 2),
            round($agingBuckets['90_plus'], 2)
        ];

        return view('finance.cuentas_por_pagar.dashboard', compact(
            'totalPorPagar',
            'pagosVencidos',
            'proximosVencer',
            'pagadoEsteMes',
            'proveedoresLabels',
            'proveedoresData',
            'mesesLabels',
            'mesesData',
            'agingData',
            'selectedMonth',
            'selectedYear'
        ));
    }

    public function facturas()
    {
        return view('finance.cuentas_por_pagar.facturas');
    }

    public function datatable(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $semana = $request->input('semana');

        $query = DB::table('cxp_details as cxp')
            ->join('facturas as f', 'f.factura_id', '=', 'cxp.factura_id');

        if ($month) {
            $query->whereMonth('f.fecha_factura', $month);
        }
        if ($year) {
            $query->whereYear('f.fecha_factura', $year);
        }
        if ($semana) {
            $query->where('cxp.semana', $semana);
        }

        $facturas = $query->select(
                'cxp.id as cxp_id',
                'cxp.fecha_pago',
                'cxp.semana',
                'cxp.anio',
                'cxp.estatus',
                'cxp.is_canceled',
                'cxp.pdf_path',
                'cxp.xml_path',
                'cxp.comentarios',
                'cxp.comentario_img',
                'f.factura_id',
                'f.empresa',
                'f.departamento',
                'f.folio_factura',
                'f.fecha_factura',
                'f.descripcion as motivo',
                'f.banco',
                'f.metodo_pago',
                'f.total'
            )
            ->orderByDesc('f.factura_id')
            ->get();

        $payments = DB::table('cxp_payments')
            ->select('cxp_detail_id', DB::raw("SUM(amount) as total_pagado"))
            ->groupBy('cxp_detail_id')
            ->get()
            ->keyBy('cxp_detail_id');

        foreach ($facturas as $factura) {
            $pagado = isset($payments[$factura->cxp_id]) ? $payments[$factura->cxp_id]->total_pagado : 0;
            $factura->total = round($factura->total, 2);
            $factura->saldo = round($factura->total - $pagado, 2);
            $factura->pagado = round($pagado, 2);
            $factura->is_canceled = (bool) $factura->is_canceled;
        }

        return response()->json(['data' => $facturas]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'semana' => 'nullable|integer',
            'banco' => 'nullable|string',
            'departamento' => 'nullable|string|max:255',
            'comentarios' => 'nullable|string',
            'comentario_img' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480'
        ]);

        $cxp = CxpDetail::findOrFail($id);
        
        if ($cxp->is_canceled) {
            return response()->json(['success' => false, 'message' => 'No se puede editar una cuenta cancelada.'], 403);
        }

        $cxp->update([
            'semana' => $request->semana,
            'comentarios' => $request->comentarios
        ]);

        if ($request->hasFile('comentario_img')) {
            $file = $request->file('comentario_img');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->move(public_path('uploads/comentarios_cxp'), $filename);
            $cxp->update(['comentario_img' => 'uploads/comentarios_cxp/' . $filename]);
        }

        if ($request->has('banco') || $request->has('departamento')) {
            $updateFactura = [];
            if ($request->has('banco')) $updateFactura['banco'] = $request->banco;
            if ($request->has('departamento')) $updateFactura['departamento'] = $request->departamento;

            DB::table('facturas')->where('factura_id', $cxp->factura_id)->update($updateFactura);
        }

        return response()->json(['success' => true, 'message' => 'Actualizado correctamente']);
    }

    public function cancel($id)
    {
        $cxp = CxpDetail::findOrFail($id);
        $cxp->update([
            'is_canceled' => true,
            'estatus' => 'CANCELADO'
        ]);

        return response()->json(['success' => true, 'message' => 'Cuenta cancelada correctamente']);
    }

    public function getPayments($id)
    {
        $cxp = CxpDetail::findOrFail($id);
        $payments = $cxp->payments()->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();
        // Load user explicitly to avoid missing relationship definition if we don't have it in the model
        foreach ($payments as $payment) {
            if ($payment->user_id) {
                $user = DB::table('users')->where('id', $payment->user_id)->first();
                $payment->user_name = $user ? $user->name : null;
            }
        }
        return response()->json(['success' => true, 'payments' => $payments]);
    }

    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:25600',
            'notas' => 'nullable|string',
            'banco' => 'nullable|string',
            'metodo_pago' => 'nullable|string'
        ]);

        $cxp = CxpDetail::findOrFail($id);

        if ($cxp->is_canceled) {
            return response()->json(['success' => false, 'message' => 'No se pueden añadir pagos a una cuenta cancelada.'], 403);
        }

        $comprobantePath = null;
        if ($request->hasFile('comprobante')) {
            $file = $request->file('comprobante');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(base_path('public/uploads/cxp/payments'), $filename);
            $comprobantePath = 'uploads/cxp/payments/' . $filename;
        }

        $cxp->payments()->create([
            'amount' => $request->amount,
            'date' => $request->date,
            'comprobante' => $comprobantePath,
            'notas' => $request->notas,
            'banco' => $request->banco,
            'metodo_pago' => $request->metodo_pago,
            'user_id' => auth()->id()
        ]);

        // Calculate new balance
        $factura = DB::table('facturas')->where('factura_id', $cxp->factura_id)->first();
        
        $pagado = $cxp->payments()->sum('amount');
        $saldo = round($factura->total - $pagado, 2);

        $newEstatus = 'PENDIENTE';
        if ($saldo <= 0) {
            $newEstatus = 'PAGADO';
        } elseif ($pagado > 0) {
            $newEstatus = 'PARCIAL';
        }

        $cxp->update(['estatus' => $newEstatus]);

        return response()->json(['success' => true, 'message' => 'Abono añadido correctamente']);
    }

    public function uploadDocuments(Request $request, $id)
    {
        $request->validate([
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'xml_file' => 'nullable|file|mimes:xml|max:10240',
        ]);

        $cxp = CxpDetail::findOrFail($id);

        $updateData = [];

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(base_path('public/uploads/cxp/documents'), $filename);
            $updateData['pdf_path'] = 'uploads/cxp/documents/' . $filename;
        }

        if ($request->hasFile('xml_file')) {
            $file = $request->file('xml_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(base_path('public/uploads/cxp/documents'), $filename);
            $updateData['xml_path'] = 'uploads/cxp/documents/' . $filename;
        }

        if (!empty($updateData)) {
            $cxp->update($updateData);
            return response()->json(['success' => true, 'message' => 'Documentos subidos correctamente']);
        }

        return response()->json(['success' => false, 'message' => 'No se adjuntó ningún archivo'], 400);
    }

    public function exportExcel(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $query = DB::table('cxp_details as cxp')
            ->join('facturas as f', 'f.factura_id', '=', 'cxp.factura_id');

        if ($month) {
            $query->whereMonth('f.fecha_factura', $month);
        }
        if ($year) {
            $query->whereYear('f.fecha_factura', $year);
        }

        $facturas = $query->select(
                'cxp.id as cxp_id',
                'cxp.fecha_pago',
                'cxp.semana',
                'cxp.anio',
                'cxp.estatus',
                'cxp.is_canceled',
                'f.factura_id',
                'f.empresa',
                'f.departamento',
                'f.folio_factura',
                'f.fecha_factura',
                'f.descripcion as motivo',
                'f.banco',
                'f.metodo_pago',
                'f.total'
            )
            ->orderByDesc('f.factura_id')
            ->get();

        $payments = DB::table('cxp_payments')
            ->select('cxp_detail_id', DB::raw("SUM(amount) as total_pagado"))
            ->groupBy('cxp_detail_id')
            ->get()
            ->keyBy('cxp_detail_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cuentas por Pagar');

        $sheet->setCellValue('A1', 'Cuentas por pagar');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF217346'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $headers = ['Empresa', 'Departamento', 'Factura', 'Motivo', 'Banco/Método', 'Fecha Factura', 'Fecha Pago', 'Estatus', 'Total', 'Monto Pagado', 'Saldo Restante'];
        $columnLetter = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnLetter . '2', $header);
            $columnLetter++;
        }

        $lastCol = chr(ord('A') + count($headers) - 1);
        $headerRange = 'A2:' . $lastCol . '2';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF198754'],
            ]
        ]);

        $row = 3;
        foreach ($facturas as $factura) {
            $pagado = isset($payments[$factura->cxp_id]) ? $payments[$factura->cxp_id]->total_pagado : 0;
            $total = round($factura->total, 2);
            $saldo = round($total - $pagado, 2);
            $pagado = round($pagado, 2);
            $factura->is_canceled = (bool) $factura->is_canceled;

            if ($factura->is_canceled) {
                $saldo = 0;
            }

            $sheet->setCellValue('A' . $row, $factura->empresa);
            $sheet->setCellValue('B' . $row, $factura->departamento);
            $sheet->setCellValue('C' . $row, $factura->folio_factura);
            $sheet->setCellValue('D' . $row, $factura->motivo);
            $sheet->setCellValue('E' . $row, $factura->banco . ' / ' . $factura->metodo_pago);
            $sheet->setCellValue('F' . $row, $factura->fecha_factura);
            $sheet->setCellValue('G' . $row, $factura->fecha_pago);
            
            $estatusCell = 'H' . $row;
            if ($factura->is_canceled) {
                $sheet->setCellValue($estatusCell, 'CANCELADO');
                $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB(Color::COLOR_RED);
                $sheet->getStyle($estatusCell)->getFont()->setBold(true);
            } else {
                $sheet->setCellValue($estatusCell, strtoupper($factura->estatus));
                if ($factura->estatus === 'PAGADO') {
                    $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB('FF157347');
                } elseif ($factura->estatus === 'PENDIENTE') {
                    $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB('FF0000FF');
                } elseif ($factura->estatus === 'PARCIAL') {
                    $sheet->getStyle($estatusCell)->getFont()->getColor()->setARGB('FFF59E0B');
                }
            }

            $sheet->setCellValue('I' . $row, $total);
            $sheet->setCellValue('J' . $row, $pagado);
            $sheet->setCellValue('K' . $row, $saldo);

            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            
            if (!$factura->is_canceled) {
                if ($saldo <= 0) {
                    $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB('FF157347');
                } else {
                    $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB(Color::COLOR_RED);
                }
            } else {
                 $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB(Color::COLOR_RED);
            }

            $row++;
        }

        foreach (range('A', $lastCol) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Cuentas_Por_Pagar_' . date('Y-m-d') . '.xlsx';

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
