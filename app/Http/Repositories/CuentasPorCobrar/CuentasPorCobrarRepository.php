<?php

namespace App\Http\Repositories\CuentasPorCobrar;

use App\Models\CxcDetail;
use App\Models\CxcPayment;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CuentasPorCobrarRepository
{
    protected CxcDetail $cxcDetail;
    protected CxcPayment $cxcPayment;

    public function __construct(CxcDetail $cxcDetail, CxcPayment $cxcPayment)
    {
        $this->cxcDetail = $cxcDetail;
        $this->cxcPayment = $cxcPayment;
    }

    public function find(int $id): ?CxcDetail
    {
        return $this->cxcDetail->with('payments')->find($id);
    }

    public function getPendingPaymentsCount(): int
    {
        $sales = DB::table('cxc_details as cxc')
            ->join('sales as s', 's.sale_id', '=', 'cxc.sale_id')
            ->leftJoin('sale_detail as sd', 'sd.sale_id', '=', 's.sale_id')
            ->where('s.almacen_status', 'confirmed')
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

                $dueDate = Carbon::parse($sale->fecha_emision)->addDays($daysToAdd)->startOfDay();
                if ($today->gt($dueDate)) {
                    $pendingPaymentsCount++;
                }
            }
        }

        return $pendingPaymentsCount;
    }

    public function getDashboardData(?int $selectedMonth = null, ?int $selectedYear = null): array
    {
        $selectedMonth = $selectedMonth ?? now()->month;
        $selectedYear = $selectedYear ?? now()->year;
        $targetDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();

        $sales = DB::table('cxc_details as cxc')
            ->join('sales as s', 's.sale_id', '=', 'cxc.sale_id')
            ->leftJoin('sale_detail as sd', 'sd.sale_id', '=', 's.sale_id')
            ->where('s.almacen_status', 'confirmed')
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

        $payments = DB::table('cxc_payments')
            ->select('cxc_detail_id', DB::raw("SUM(amount) as total_pagado"))
            ->groupBy('cxc_detail_id')
            ->get()
            ->keyBy('cxc_detail_id');

        $facturasVencidas = 0;
        $clientesAdeudoSet = [];
        $today = now()->startOfDay();

        $monthsArray = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $targetDate->copy()->startOfMonth()->subMonths($i);
            $key = $date->format('Y-m');
            $monthsArray[$key] = [
                'label' => $date->translatedFormat('F Y'),
                'total_facturado' => 0,
                'total_cobrado' => 0,
            ];
        }

        foreach ($sales as $sale) {
            $pagado = isset($payments[$sale->cxc_id]) ? $payments[$sale->cxc_id]->total_pagado : 0;
            $saldo = round($sale->total_venta - $pagado, 2);
            $saleMonthStr = Carbon::parse($sale->fecha_emision)->format('Y-m');
            $isSelectedMonth = (Carbon::parse($sale->fecha_emision)->month == $selectedMonth && Carbon::parse($sale->fecha_emision)->year == $selectedYear);

            if ($isSelectedMonth && !$sale->is_canceled && $sale->estatus !== 'Pagado') {
                if ($saldo > 0) {
                    $clientId = 'c_' . $sale->customer_id . '_p_' . $sale->prospect_id;
                    $clientesAdeudoSet[$clientId] = true;
                }

                $daysToAdd = 0;
                if (!empty($sale->term)) {
                    preg_match('/\d+/', $sale->term, $matches);
                    if (isset($matches[0])) {
                        $daysToAdd = (int) $matches[0];
                    }
                }

                $dueDate = Carbon::parse($sale->fecha_emision)->addDays($daysToAdd)->startOfDay();
                if ($today->gt($dueDate)) {
                    $facturasVencidas++;
                }
            }

            if (!$sale->is_canceled && isset($monthsArray[$saleMonthStr])) {
                $monthsArray[$saleMonthStr]['total_facturado'] += $sale->total_venta;
                $monthsArray[$saleMonthStr]['total_cobrado'] += $pagado;
            }
        }

        $clientesAdeudo = count($clientesAdeudoSet);
        $chartLabels = [];
        $chartData = [];

        foreach ($monthsArray as $key => $data) {
            $chartLabels[] = ucfirst($data['label']);
            if ($data['total_facturado'] > 0) {
                $pct = ($data['total_cobrado'] / $data['total_facturado']) * 100;
                $chartData[] = round($pct, 2);
            } else {
                $chartData[] = 0;
            }
        }

        $currentMonthKey = $targetDate->format('Y-m');
        $currentMonthPct = 0;
        if (isset($monthsArray[$currentMonthKey]) && $monthsArray[$currentMonthKey]['total_facturado'] > 0) {
            $currentMonthPct = round(($monthsArray[$currentMonthKey]['total_cobrado'] / $monthsArray[$currentMonthKey]['total_facturado']) * 100, 2);
        }

        return [
            'facturasVencidas' => $facturasVencidas,
            'clientesAdeudo' => $clientesAdeudo,
            'currentMonthPct' => $currentMonthPct,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
        ];
    }

    public function getDatatable(?int $month = null, ?int $year = null): Collection
    {
        $query = DB::table('cxc_details as cxc')
            ->join('sales as s', 's.sale_id', '=', 'cxc.sale_id')
            ->where('s.almacen_status', 'confirmed');

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

        return $sales;
    }

    public function updateDetail(int $id, array $data): CxcDetail
    {
        return DB::transaction(function () use ($id, $data) {
            $cxc = $this->cxcDetail->where('id', $id)->lockForUpdate()->firstOrFail();

            if ($cxc->is_canceled) {
                throw new DomainException('No se puede editar una cuenta cancelada.');
            }

            $cxc->update([
                'documento' => !empty($data['documento']) ? $data['documento'] : 'Factura',
                'metodo_pago' => $data['metodo_pago'],
                'fecha_conclusion' => $data['fecha_conclusion'] ?? null,
                'descripcion' => $data['descripcion'] ?? null,
            ]);

            return $cxc;
        });
    }

    public function cancel(int $id): CxcDetail
    {
        return DB::transaction(function () use ($id) {
            $cxc = $this->cxcDetail->where('id', $id)->lockForUpdate()->firstOrFail();
            $cxc->update(['is_canceled' => true]);

            return $cxc;
        });
    }

    public function getPaymentsByDetailId(int $id): Collection
    {
        $cxc = $this->cxcDetail->findOrFail($id);

        return $cxc->payments()->orderByDesc('date')->get();
    }

    public function addPayment(int $id, array $data, $file = null): CxcPayment
    {
        return DB::transaction(function () use ($id, $data, $file) {
            $cxc = $this->cxcDetail->where('id', $id)->lockForUpdate()->firstOrFail();

            if ($cxc->is_canceled) {
                throw new DomainException('No se pueden añadir pagos a una cuenta cancelada.');
            }

            if ($cxc->estatus === 'Pagado') {
                throw new DomainException('Esta cuenta ya se encuentra pagada.');
            }

            $comprobanteName = null;
            if ($file) {
                $ext = $file->guessExtension() ?: 'pdf';
                $comprobanteName = time() . '_' . Str::uuid() . '.' . $ext;
                $dest = storage_path('app/public/comprobantes');

                if (!file_exists($dest)) {
                    @mkdir($dest, 0755, true);
                }

                $file->move($dest, $comprobanteName);
            }

            $payment = $cxc->payments()->create([
                'amount' => $data['amount'],
                'date' => $data['date'],
                'comprobante' => $comprobanteName,
            ]);

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

            return $payment;
        });
    }

    public function getSpreadsheet(?int $month = null, ?int $year = null): Spreadsheet
    {
        $sales = $this->getDatatable($month, $year);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cuentas por Cobrar');

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
                'startColor' => ['argb' => 'FF217346'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $headers = ['Remisión', 'Fecha Emisión', 'Cliente', 'Asesor', 'Documento', 'Método Pago', 'Estatus', 'Fecha Conclusión', 'Total', 'Monto Abonado', 'Saldo Restante'];
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
            ],
        ]);

        $row = 3;
        foreach ($sales as $sale) {
            $totalVenta = round($sale->total_venta, 2);
            $pagado = round($sale->pagado, 2);
            $saldo = round($sale->saldo, 2);

            if ($sale->is_canceled) {
                $saldo = 0;
            }

            $sheet->setCellValue('A' . $row, $sale->folio);
            $sheet->setCellValue('B' . $row, $sale->fecha_emision);
            $sheet->setCellValue('C' . $row, $sale->cliente_name);
            $sheet->setCellValue('D' . $row, $sale->asesor);
            $sheet->setCellValue('E' . $row, $sale->documento);
            $sheet->setCellValue('F' . $row, $sale->metodo_pago);

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

            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);

            if (!$sale->is_canceled && $saldo <= 0) {
                $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB('FF157347');
            } else {
                $sheet->getStyle('K' . $row)->getFont()->getColor()->setARGB(Color::COLOR_RED);
            }

            $row++;
        }

        foreach (range('A', $lastCol) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
