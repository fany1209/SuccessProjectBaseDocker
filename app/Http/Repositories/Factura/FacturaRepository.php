<?php

namespace App\Http\Repositories\Factura;

use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FacturaRepository
{
    protected Factura $model;

    public function __construct(Factura $model)
    {
        $this->model = $model;
    }

    public function getDatatableData(): Collection
    {
        return DB::table('facturas as f')
            ->leftJoin('factura_detalles as d', 'd.factura_id', '=', 'f.factura_id')
            ->select(
                'f.factura_id',
                'f.tipo_documento',
                'f.insumo',
                'f.empresa',
                'f.folio_factura',
                'f.departamento',
                'f.moneda',
                'f.tipo_cambio',
                DB::raw('COALESCE(NULLIF(f.subtotal, 0), SUM(d.precio)) as subtotal'),
                'f.iva',
                'f.total',
                DB::raw('GROUP_CONCAT(d.producto SEPARATOR ", ") as productos')
            )
            ->groupBy(
                'f.factura_id',
                'f.tipo_documento',
                'f.insumo',
                'f.empresa',
                'f.departamento',
                'f.folio_factura',
                'f.moneda',
                'f.tipo_cambio',
                'f.subtotal',
                'f.iva',
                'f.total'
            )
            ->orderByDesc('f.factura_id')
            ->get();
    }

    public function find(int $id): ?Factura
    {
        return $this->model->with('detalles')->find($id);
    }

    public function calculateTotals(array $productos): array
    {
        $subtotalGlobal  = 0;
        $descuentoGlobal = 0;
        $ivaGlobal       = 0;
        $trasladoGlobal  = 0;
        $retencionGlobal = 0;

        $detalles = [];

        foreach ($productos as $p) {
            $cantidad       = (float) ($p['cantidad'] ?? 0);
            $precioUnitario = (float) ($p['precio_unitario'] ?? 0);
            $importeBase    = $cantidad * $precioUnitario;
            $descuento      = (float) ($p['descuento'] ?? 0);
            $baseImpuestos  = $importeBase - $descuento;

            $aplicaIva      = filter_var($p['aplica_iva'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $ivaPorcentaje  = (float) ($p['iva_porcentaje'] ?? 0);
            $otroPorcentaje = (float) ($p['otro_impuesto'] ?? $p['otro_impuesto_porcentaje'] ?? 0);

            $ivaCalc  = $aplicaIva ? ($baseImpuestos * ($ivaPorcentaje / 100)) : 0;
            $otroCalc = $otroPorcentaje > 0 ? ($baseImpuestos * ($otroPorcentaje / 100)) : 0;

            $traslado  = (float) ($p['traslado'] ?? 0);
            $ilc       = (float) ($p['ilc'] ?? 0);
            $retencion = (float) ($p['retencion'] ?? 0);
            $isr       = (float) ($p['isr'] ?? 0);

            $subtotalGlobal  += $importeBase;
            $descuentoGlobal += $descuento;
            $ivaGlobal       += ($ivaCalc + $otroCalc);
            $trasladoGlobal  += ($traslado + $ilc);
            $retencionGlobal += ($retencion + $isr);

            $detalles[] = [
                'producto'                 => $p['producto'],
                'clave_sat'                => $p['clave_sat'] ?? null,
                'unidad'                   => $p['unidad'] ?? null,
                'cantidad'                 => $cantidad,
                'precio_unitario'          => $precioUnitario,
                'precio'                   => $importeBase,
                'subtotal'                 => $baseImpuestos,
                'descuento'                => $descuento,
                'aplica_iva'               => $aplicaIva,
                'iva_porcentaje'           => $aplicaIva ? $ivaPorcentaje : 0,
                'otro_impuesto_porcentaje' => $otroPorcentaje,
                'impuesto_total'           => round($ivaCalc + $otroCalc, 5),
                'traslado'                 => $traslado,
                'ilc'                      => $ilc,
                'retencion'                => $retencion,
                'isr'                      => $isr,
                'created_at'               => now(),
                'updated_at'               => now(),
            ];
        }

        $granTotal = $subtotalGlobal - $descuentoGlobal + $ivaGlobal + $trasladoGlobal - $retencionGlobal;

        return [
            'subtotal'        => round($subtotalGlobal, 5),
            'descuento_total' => round($descuentoGlobal, 5),
            'iva'             => round($ivaGlobal, 5),
            'traslado_total'  => round($trasladoGlobal, 5),
            'retencion_total' => round($retencionGlobal, 5),
            'total'           => round($granTotal, 5),
            'detalles'        => $detalles,
        ];
    }

    public function create(array $data): Factura
    {
        return DB::transaction(function () use ($data) {
            $calc = $this->calculateTotals($data['productos'] ?? []);

            $tipoCambio = ($data['moneda'] ?? 'MXN') === 'USD'
                ? (float) ($data['tipo_cambio'] ?? 1.0)
                : 1.0;

            $factura = $this->model->create([
                'tipo_documento'  => $data['tipo_documento'],
                'insumo'          => $data['insumo'],
                'empresa'         => $data['empresa'],
                'folio_factura'   => $data['folio_factura'],
                'fecha_factura'   => $data['fecha_factura'],
                'moneda'          => $data['moneda'] ?? 'MXN',
                'tipo_cambio'     => $tipoCambio,
                'departamento'    => $data['departamento'] ?? null,
                'descripcion'     => $data['descripcion'] ?? null,
                'subtotal'        => $calc['subtotal'],
                'descuento_total' => $calc['descuento_total'],
                'iva'             => $calc['iva'],
                'traslado_total'  => $calc['traslado_total'],
                'retencion_total' => $calc['retencion_total'],
                'total'           => $calc['total'],
            ]);

            $detallesAInsertar = array_map(function ($det) use ($factura) {
                $det['factura_id'] = $factura->factura_id;
                return $det;
            }, $calc['detalles']);

            DB::table('factura_detalles')->insert($detallesAInsertar);

            $preciosAInsertar = [];
            foreach ($data['productos'] as $p) {
                $preciosAInsertar[] = [
                    'factura_id'       => $factura->factura_id,
                    'insumo'           => $p['producto'],
                    'clave_sat'        => $p['clave_sat'] ?? null,
                    'proveedor'        => $data['empresa'],
                    'precio'           => $p['precio_unitario'],
                    'tiene_iva'        => filter_var($p['aplica_iva'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                    'fecha_cotizacion' => $data['fecha_factura'],
                    'moneda'           => $data['moneda'] ?? 'MXN',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
            DB::table('supplier_prices')->insert($preciosAInsertar);

            $fechaFacturaCarbon = Carbon::parse($data['fecha_factura']);
            DB::table('cxp_details')->insert([
                'factura_id'  => $factura->factura_id,
                'fecha_pago'  => null,
                'semana'      => $fechaFacturaCarbon->weekOfYear ?? Carbon::now()->weekOfYear,
                'anio'        => $fechaFacturaCarbon->year ?? Carbon::now()->year,
                'estatus'     => 'PENDIENTE',
                'is_canceled' => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            return $factura->load('detalles');
        });
    }

    public function update(int $id, array $data): Factura
    {
        return DB::transaction(function () use ($id, $data) {
            $factura = $this->model->where('factura_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $calc = $this->calculateTotals($data['productos'] ?? []);

            $tipoCambio = ($data['moneda'] ?? 'MXN') === 'USD'
                ? (float) ($data['tipo_cambio'] ?? 1.0)
                : 1.0;

            $factura->update([
                'tipo_documento'  => $data['tipo_documento'],
                'insumo'          => $data['insumo'],
                'empresa'         => $data['empresa'],
                'folio_factura'   => $data['folio_factura'],
                'fecha_factura'   => $data['fecha_factura'],
                'moneda'          => $data['moneda'] ?? 'MXN',
                'tipo_cambio'     => $tipoCambio,
                'departamento'    => $data['departamento'] ?? null,
                'descripcion'     => $data['descripcion'] ?? null,
                'subtotal'        => $calc['subtotal'],
                'descuento_total' => $calc['descuento_total'],
                'iva'             => $calc['iva'],
                'traslado_total'  => $calc['traslado_total'],
                'retencion_total' => $calc['retencion_total'],
                'total'           => $calc['total'],
            ]);

            DB::table('factura_detalles')->where('factura_id', $id)->delete();

            $detallesAInsertar = array_map(function ($det) use ($id) {
                $det['factura_id'] = $id;
                return $det;
            }, $calc['detalles']);

            DB::table('factura_detalles')->insert($detallesAInsertar);

            $preciosAInsertar = [];
            foreach ($data['productos'] as $p) {
                $preciosAInsertar[] = [
                    'factura_id'       => $id,
                    'insumo'           => $p['producto'],
                    'clave_sat'        => $p['clave_sat'] ?? null,
                    'proveedor'        => $data['empresa'],
                    'precio'           => $p['precio_unitario'],
                    'tiene_iva'        => filter_var($p['aplica_iva'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                    'fecha_cotizacion' => $data['fecha_factura'],
                    'moneda'           => $data['moneda'] ?? 'MXN',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
            DB::table('supplier_prices')->where('factura_id', $id)->delete();
            DB::table('supplier_prices')->insert($preciosAInsertar);

            return $factura->load('detalles');
        });
    }

    public function checkPayments(int $id): array
    {
        $cxpDetail = DB::table('cxp_details')->where('factura_id', $id)->first();

        $hasCxpPayments = false;
        if ($cxpDetail) {
            $hasCxpPayments = DB::table('cxp_payments')
                ->where('cxp_detail_id', $cxpDetail->id)
                ->exists();
        }

        $hasFinancePayments = DB::table('finance_payments')
            ->where('factura', $id)
            ->where('estatus', '!=', 'CANCELADO')
            ->exists();

        return [
            'has_cxp'      => $hasCxpPayments,
            'has_finance'  => $hasFinancePayments,
            'has_payments' => $hasCxpPayments || $hasFinancePayments,
            'cxp_detail'   => $cxpDetail,
        ];
    }

    public function delete(int $id, bool $force = false): bool
    {
        return DB::transaction(function () use ($id, $force) {
            $factura = $this->model->where('factura_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $paymentsStatus = $this->checkPayments($id);

            if ($paymentsStatus['has_payments'] && !$force) {
                throw new \DomainException('No es posible eliminar la factura porque cuenta con pagos aplicados. Elimine o cancele los pagos asociados antes de eliminarla.');
            }

            $cxpDetail = $paymentsStatus['cxp_detail'];

            if ($cxpDetail) {
                if ($force && $paymentsStatus['has_cxp']) {
                    $comprobantes = DB::table('cxp_payments')
                        ->where('cxp_detail_id', $cxpDetail->id)
                        ->whereNotNull('comprobante')
                        ->pluck('comprobante');

                    foreach ($comprobantes as $comp) {
                        $compPath = $this->getPublicHtmlPath($comp);
                        if (file_exists($compPath) && is_file($compPath)) {
                            @unlink($compPath);
                        }
                    }

                    DB::table('cxp_payments')->where('cxp_detail_id', $cxpDetail->id)->delete();
                }

                $filesToDelete = array_filter([
                    $cxpDetail->pdf_path,
                    $cxpDetail->xml_path,
                    $cxpDetail->comentario_img ?? null,
                ]);

                foreach ($filesToDelete as $filePath) {
                    $fullPath = $this->getPublicHtmlPath($filePath);
                    if (file_exists($fullPath) && is_file($fullPath)) {
                        @unlink($fullPath);
                    }
                }

                DB::table('cxp_details')->where('id', $cxpDetail->id)->delete();
            }

            if ($force) {
                DB::table('finance_payments')->where('factura', $id)->delete();
            } else {
                DB::table('finance_payments')->where('factura', $id)->where('estatus', 'CANCELADO')->delete();
            }

            DB::table('factura_detalles')->where('factura_id', $id)->delete();
            DB::table('supplier_prices')->where('factura_id', $id)->delete();

            return (bool) $factura->delete();
        });
    }

    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }
}
