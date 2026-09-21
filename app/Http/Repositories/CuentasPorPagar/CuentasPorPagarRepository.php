<?php

namespace App\Http\Repositories\CuentasPorPagar;

use App\Models\CxpDetail;
use App\Models\CxpPayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CuentasPorPagarRepository
{
    protected $cxpDetail;
    protected $cxpPayment;

    public function __construct(CxpDetail $cxpDetail, CxpPayment $cxpPayment)
    {
        $this->cxpDetail = $cxpDetail;
        $this->cxpPayment = $cxpPayment;
    }

    public function find($id): ?CxpDetail
    {
        return $this->cxpDetail->with('payments')->find($id);
    }

    public function getPendingOverdueCount(): int
    {
        $facturas = DB::table('cxp_details as cxp')
            ->join('facturas as f', 'f.factura_id', '=', 'cxp.factura_id')
            ->select('cxp.id as cxp_id', 'cxp.fecha_pago', 'cxp.estatus', 'cxp.is_canceled', 'f.total')
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
                    $dueDate = Carbon::parse($factura->fecha_pago)->startOfDay();
                    if ($today->gt($dueDate)) {
                        $pendingPaymentsCount++;
                    }
                }
            }
        }

        return $pendingPaymentsCount;
    }

    public function getDashboardMetrics(array $filters = []): array
    {
        $selectedMonth = $filters['month'] ?? now()->month;
        $selectedYear = $filters['year'] ?? now()->year;
        $selectedSemana = $filters['semana'] ?? null;
        $targetDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();

        $query = DB::table('cxp_details as cxp')
            ->join('facturas as f', 'f.factura_id', '=', 'cxp.factura_id');

        if ($selectedSemana) {
            $query->where('cxp.semana', $selectedSemana);
        }

        $facturas = $query->select(
            'cxp.id as cxp_id',
            'cxp.fecha_pago',
            'cxp.estatus',
            'cxp.is_canceled',
            'f.factura_id',
            'f.empresa',
            'f.departamento',
            'f.total',
            'f.moneda',
            'f.tipo_cambio',
            'f.fecha_factura'
        )->get();

        $paymentsList = DB::table('cxp_payments')->select('cxp_detail_id', 'amount', 'date')->get();
        $paymentsGrouped = $paymentsList->groupBy('cxp_detail_id');

        $today = now()->startOfDay();
        $threeDaysFromNow = now()->addDays(3)->endOfDay();

        $totalPorPagar = 0;
        $pagosVencidos = 0;
        $proximosVencer = 0;
        $pagadoEsteMes = 0;

        $proveedoresMap = [];
        $departamentosMap = [];
        $agingBuckets = ['0_30' => 0, '31_60' => 0, '61_90' => 0, '90_plus' => 0];

        foreach ($facturas as $factura) {
            $pagos = $paymentsGrouped[$factura->cxp_id] ?? collect();
            $pagado = $pagos->sum('amount');
            $saldo = round($factura->total - $pagado, 2);

            $tc = ($factura->moneda === 'USD' && !empty($factura->tipo_cambio)) ? (float)$factura->tipo_cambio : 1.0;
            $totalMXN = $factura->total * $tc;
            $saldoMXN = $saldo * $tc;

            if (!$factura->is_canceled) {
                $rawDep = $factura->departamento ?: 'Sin Departamento';
                $dep = mb_convert_case(trim($rawDep), MB_CASE_TITLE, "UTF-8");
                if (!empty($dep)) {
                    $departamentosMap[$dep] = ($departamentosMap[$dep] ?? 0) + $totalMXN;
                }
            }

            if (!$factura->is_canceled && $factura->estatus !== 'PAGADO' && $saldo > 0) {
                $totalPorPagar += $saldoMXN;

                if (!empty($factura->fecha_pago)) {
                    $dueDate = Carbon::parse($factura->fecha_pago)->startOfDay();
                    if ($today->gt($dueDate)) {
                        $pagosVencidos += $saldoMXN;
                    } elseif ($dueDate->between($today, $threeDaysFromNow)) {
                        $proximosVencer += $saldoMXN;
                    }
                }

                $rawEmp = $factura->empresa ?: 'Desconocido';
                $emp = mb_convert_case(trim($rawEmp), MB_CASE_TITLE, "UTF-8");
                $proveedoresMap[$emp] = ($proveedoresMap[$emp] ?? 0) + $saldoMXN;

                if (!empty($factura->fecha_factura)) {
                    $ffDate = Carbon::parse($factura->fecha_factura)->startOfDay();
                    $daysOld = $ffDate->diffInDays($today, false);
                    if ($daysOld <= 30) {
                        $agingBuckets['0_30'] += $saldoMXN;
                    } elseif ($daysOld <= 60) {
                        $agingBuckets['31_60'] += $saldoMXN;
                    } elseif ($daysOld <= 90) {
                        $agingBuckets['61_90'] += $saldoMXN;
                    } else {
                        $agingBuckets['90_plus'] += $saldoMXN;
                    }
                } else {
                    $agingBuckets['0_30'] += $saldoMXN;
                }
            }
        }

        $monthsArray = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $targetDate->copy()->startOfMonth()->subMonths($i);
            $key = $date->format('Y-m');
            $monthsArray[$key] = [
                'label'        => $date->translatedFormat('F Y'),
                'total_pagado' => 0
            ];
        }

        $currentMonthKey = $targetDate->format('Y-m');

        foreach ($paymentsList as $payment) {
            $paymentMonthStr = Carbon::parse($payment->date)->format('Y-m');
            if ($paymentMonthStr === $currentMonthKey) {
                $pagadoEsteMes += $payment->amount;
            }
            if (isset($monthsArray[$paymentMonthStr])) {
                $monthsArray[$paymentMonthStr]['total_pagado'] += $payment->amount;
            }
        }

        arsort($proveedoresMap);
        $topProveedores = array_slice($proveedoresMap, 0, 10, true);

        arsort($departamentosMap);
        $topDepartamentos = array_slice($departamentosMap, 0, 10, true);

        $mesesLabels = [];
        $mesesData = [];
        foreach ($monthsArray as $data) {
            $mesesLabels[] = ucfirst($data['label']);
            $mesesData[] = round($data['total_pagado'], 2);
        }

        $agingData = [
            round($agingBuckets['0_30'], 2),
            round($agingBuckets['31_60'], 2),
            round($agingBuckets['61_90'], 2),
            round($agingBuckets['90_plus'], 2)
        ];

        return [
            'totalPorPagar'       => round($totalPorPagar, 2),
            'pagosVencidos'       => round($pagosVencidos, 2),
            'proximosVencer'      => round($proximosVencer, 2),
            'pagadoEsteMes'       => round($pagadoEsteMes, 2),
            'proveedoresLabels'   => array_keys($topProveedores),
            'proveedoresData'     => array_values($topProveedores),
            'mesesLabels'         => $mesesLabels,
            'mesesData'           => $mesesData,
            'agingData'           => $agingData,
            'departamentosLabels' => array_keys($topDepartamentos),
            'departamentosData'   => array_values($topDepartamentos),
            'selectedMonth'       => $selectedMonth,
            'selectedYear'        => $selectedYear,
            'selectedSemana'      => $selectedSemana,
        ];
    }

    public function getDatatable(array $filters = []): Collection
    {
        $month = $filters['month'] ?? null;
        $year = $filters['year'] ?? null;
        $semana = $filters['semana'] ?? null;

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
            'f.moneda',
            'f.tipo_cambio',
            'f.total'
        )
        ->orderByDesc('f.factura_id')
        ->get();

        $payments = DB::table('cxp_payments')
            ->select(
                'cxp_detail_id',
                DB::raw("SUM(amount) as total_pagado"),
                DB::raw("COUNT(comprobante) as comprobantes_count"),
                DB::raw("GROUP_CONCAT(DISTINCT banco SEPARATOR ', ') as bancos_usados")
            )
            ->groupBy('cxp_detail_id')
            ->get()
            ->keyBy('cxp_detail_id');

        foreach ($facturas as $factura) {
            $paymentInfo = $payments[$factura->cxp_id] ?? null;
            $pagado = $paymentInfo ? $paymentInfo->total_pagado : 0;
            $comprobantes_count = $paymentInfo ? $paymentInfo->comprobantes_count : 0;

            $factura->total = round($factura->total, 2);
            $factura->saldo = round($factura->total - $pagado, 2);
            $factura->pagado = round($pagado, 2);
            $factura->is_canceled = (bool) $factura->is_canceled;
            $factura->comprobantes_count = $comprobantes_count;
            $factura->banco = $paymentInfo && $paymentInfo->bancos_usados ? $paymentInfo->bancos_usados : ($factura->banco ?: '—');
        }

        return $facturas;
    }

    public function updateDetail($id, array $data, $imageFile = null, ?string $departamento = null): ?CxpDetail
    {
        return DB::transaction(function () use ($id, $data, $imageFile, $departamento) {
            $cxp = $this->cxpDetail->lockForUpdate()->find($id);
            if (!$cxp || $cxp->is_canceled) {
                return null;
            }

            $updatePayload = [
                'semana'      => $data['semana'] ?? $cxp->semana,
                'comentarios' => $data['comentarios'] ?? $cxp->comentarios,
            ];

            if ($imageFile) {
                $ext = $imageFile->guessExtension() ?: 'jpg';
                $filename = time() . '_' . Str::uuid() . '.' . $ext;
                $dest = public_path('uploads/comentarios_cxp');
                if (!file_exists($dest)) {
                    @mkdir($dest, 0755, true);
                }
                $imageFile->move($dest, $filename);
                $updatePayload['comentario_img'] = 'uploads/comentarios_cxp/' . $filename;
            }

            $cxp->update($updatePayload);

            $facturaUpdates = [];
            if ($departamento !== null) {
                $facturaUpdates['departamento'] = $departamento;
            }
            if (!empty($data['banco'])) {
                $facturaUpdates['banco'] = $data['banco'];
            }
            if (!empty($facturaUpdates)) {
                DB::table('facturas')->where('factura_id', $cxp->factura_id)->update($facturaUpdates);
            }

            return $cxp;
        });
    }

    public function cancelAccount($id): ?CxpDetail
    {
        return DB::transaction(function () use ($id) {
            $cxp = $this->cxpDetail->lockForUpdate()->find($id);
            if (!$cxp) {
                return null;
            }

            $cxp->update([
                'is_canceled' => true,
                'estatus'     => 'CANCELADO',
            ]);

            return $cxp;
        });
    }

    public function getPaymentsByCxpId($id): Collection
    {
        $cxp = $this->cxpDetail->findOrFail($id);
        $payments = $cxp->payments()->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();

        foreach ($payments as $payment) {
            if ($payment->user_id) {
                $user = DB::table('users')->where('id', $payment->user_id)->first();
                $payment->user_name = $user ? $user->name : null;
            }
        }

        return $payments;
    }

    public function addPayment($id, array $data, $comprobanteFile = null): ?CxpPayment
    {
        return DB::transaction(function () use ($id, $data, $comprobanteFile) {
            $cxp = $this->cxpDetail->lockForUpdate()->find($id);
            if (!$cxp || $cxp->is_canceled) {
                return null;
            }

            $comprobantePath = null;
            if ($comprobanteFile) {
                $ext = $comprobanteFile->guessExtension() ?: 'pdf';
                $filename = time() . '_' . Str::uuid() . '.' . $ext;
                $dest = public_path('uploads/cxp/payments');
                if (!file_exists($dest)) {
                    @mkdir($dest, 0755, true);
                }
                $comprobanteFile->move($dest, $filename);
                $comprobantePath = 'uploads/cxp/payments/' . $filename;
            }

            $payment = $cxp->payments()->create([
                'amount'      => $data['amount'],
                'date'        => $data['date'],
                'comprobante' => $comprobantePath,
                'notas'       => $data['notas'] ?? null,
                'banco'       => $data['banco'] ?? null,
                'metodo_pago' => $data['metodo_pago'] ?? null,
                'user_id'     => auth()->id(),
            ]);

            $this->recalculateBalanceAndStatus($cxp);

            return $payment;
        });
    }

    public function updatePayment($paymentId, array $data, $comprobanteFile = null): ?CxpPayment
    {
        return DB::transaction(function () use ($paymentId, $data, $comprobanteFile) {
            $payment = $this->cxpPayment->lockForUpdate()->find($paymentId);
            if (!$payment) {
                return null;
            }

            $cxp = $this->cxpDetail->lockForUpdate()->find($payment->cxp_detail_id);
            if (!$cxp || $cxp->is_canceled) {
                return null;
            }

            $paymentData = [
                'amount'      => $data['amount'],
                'date'        => $data['date'],
                'notas'       => $data['notas'] ?? null,
                'banco'       => $data['banco'] ?? null,
                'metodo_pago' => $data['metodo_pago'] ?? null,
            ];

            if ($comprobanteFile) {
                $ext = $comprobanteFile->guessExtension() ?: 'pdf';
                $filename = time() . '_' . Str::uuid() . '.' . $ext;
                $dest = public_path('uploads/cxp/payments');
                if (!file_exists($dest)) {
                    @mkdir($dest, 0755, true);
                }
                $comprobanteFile->move($dest, $filename);
                $paymentData['comprobante'] = 'uploads/cxp/payments/' . $filename;
            }

            $payment->update($paymentData);
            $this->recalculateBalanceAndStatus($cxp);

            return $payment;
        });
    }

    public function deletePayment($paymentId): bool
    {
        return DB::transaction(function () use ($paymentId) {
            $payment = $this->cxpPayment->lockForUpdate()->find($paymentId);
            if (!$payment) {
                return false;
            }

            $cxp = $this->cxpDetail->lockForUpdate()->find($payment->cxp_detail_id);
            if (!$cxp || $cxp->is_canceled) {
                return false;
            }

            $payment->delete();
            $this->recalculateBalanceAndStatus($cxp);

            return true;
        });
    }

    public function uploadDocuments($id, $pdfFile = null, $xmlFile = null): ?CxpDetail
    {
        return DB::transaction(function () use ($id, $pdfFile, $xmlFile) {
            $cxp = $this->cxpDetail->lockForUpdate()->find($id);
            if (!$cxp) {
                return null;
            }

            $updateData = [];
            $dest = public_path('uploads/cxp/documents');
            if (!file_exists($dest)) {
                @mkdir($dest, 0755, true);
            }

            if ($pdfFile) {
                $filename = time() . '_' . Str::uuid() . '.pdf';
                $pdfFile->move($dest, $filename);
                $updateData['pdf_path'] = 'uploads/cxp/documents/' . $filename;
            }

            if ($xmlFile) {
                $filename = time() . '_' . Str::uuid() . '.xml';
                $xmlFile->move($dest, $filename);
                $updateData['xml_path'] = 'uploads/cxp/documents/' . $filename;
            }

            if (!empty($updateData)) {
                $cxp->update($updateData);
                return $cxp;
            }

            return null;
        });
    }

    public function deleteComentarioImage($id): bool
    {
        return DB::transaction(function () use ($id) {
            $cxp = $this->cxpDetail->lockForUpdate()->find($id);
            if (!$cxp) {
                return false;
            }

            if ($cxp->comentario_img && file_exists(public_path($cxp->comentario_img))) {
                @unlink(public_path($cxp->comentario_img));
            }

            $cxp->update(['comentario_img' => null]);
            return true;
        });
    }

    public function getFacturasForExcel(array $filters = []): array
    {
        $month = $filters['month'] ?? null;
        $year = $filters['year'] ?? null;
        $semana = $filters['semana'] ?? null;

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
        ->orderByRaw("CASE WHEN cxp.estatus = 'PENDIENTE' THEN 1 WHEN cxp.estatus = 'PARCIAL' THEN 2 WHEN cxp.estatus = 'PAGADO' THEN 3 ELSE 4 END")
        ->orderByDesc('f.factura_id')
        ->get();

        $paymentsList = DB::table('cxp_payments')
            ->select('cxp_detail_id', 'amount', 'banco')
            ->get();
        $paymentsGrouped = $paymentsList->groupBy('cxp_detail_id');

        return [
            'facturas'        => $facturas,
            'paymentsGrouped' => $paymentsGrouped,
        ];
    }

    protected function recalculateBalanceAndStatus(CxpDetail $cxp): void
    {
        $factura = DB::table('facturas')->where('factura_id', $cxp->factura_id)->first();
        if (!$factura) {
            return;
        }

        $pagado = $cxp->payments()->sum('amount');
        $saldo = round($factura->total - $pagado, 2);

        $newEstatus = 'PENDIENTE';
        if ($saldo <= 0) {
            $newEstatus = 'PAGADO';
        } elseif ($pagado > 0) {
            $newEstatus = 'PARCIAL';
        }

        $fecha_pago = ($newEstatus === 'PAGADO') ? $cxp->payments()->max('date') : null;

        $cxp->update([
            'estatus'    => $newEstatus,
            'fecha_pago' => $fecha_pago,
        ]);
    }
}
