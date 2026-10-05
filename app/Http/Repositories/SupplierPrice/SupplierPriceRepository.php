<?php

namespace App\Http\Repositories\SupplierPrice;

use App\Models\SupplierPrice;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SupplierPriceRepository
{
    protected SupplierPrice $supplierPrice;

    public function __construct(SupplierPrice $supplierPrice)
    {
        $this->supplierPrice = $supplierPrice;
    }

    public function getIndexData(): array
    {
        return [
            'todosLosPrecios' => $this->supplierPrice->orderBy('id', 'desc')->get(),
            'insumos'         => $this->getDistinctInsumos(),
        ];
    }

    public function getDistinctInsumos(): Collection
    {
        return $this->supplierPrice->distinct()->orderBy('insumo', 'asc')->pluck('insumo');
    }

    public function getGraficaData(?string $insumo, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        if (empty($insumo)) {
            return ['data' => [], 'analisis' => null];
        }

        $query = $this->supplierPrice->where('insumo', $insumo);

        if (!empty($fechaInicio)) {
            $query->whereDate('fecha_cotizacion', '>=', $fechaInicio);
        }

        if (!empty($fechaFin)) {
            $query->whereDate('fecha_cotizacion', '<=', $fechaFin);
        }

        $datos = $query->orderBy('fecha_cotizacion', 'asc')->get();

        if ($datos->isEmpty()) {
            return ['data' => [], 'analisis' => null];
        }

        $masBarato = $datos->sortBy('precio')->first();
        $promedio = (float) $datos->avg('precio');
        $ultimoPrecio = (float) $datos->last()->precio;
        $precioAnterior = $datos->count() > 1 ? (float) $datos[$datos->count() - 2]->precio : $ultimoPrecio;

        $tendencia = 'Estable';
        if ($ultimoPrecio > $precioAnterior) {
            $tendencia = 'Alza';
        } elseif ($ultimoPrecio < $precioAnterior) {
            $tendencia = 'Baja';
        }

        $formateados = $datos->map(fn($item) => [
            'fecha'     => Carbon::parse($item->fecha_cotizacion)->format('d/m/Y'),
            'precio'    => (float) $item->precio,
            'moneda'    => $item->moneda,
            'proveedor' => $item->proveedor,
        ]);

        return [
            'data'     => $formateados,
            'analisis' => [
                'mejor_proveedor'  => $masBarato->proveedor,
                'mejor_precio'     => number_format((float) $masBarato->precio, 2),
                'moneda'           => $masBarato->moneda,
                'ahorro_potencial' => number_format($promedio - (float) $masBarato->precio, 2),
                'tendencia'        => $tendencia,
                'total_registros'  => $datos->count(),
            ],
        ];
    }

    public function find($id): ?SupplierPrice
    {
        return $this->supplierPrice->find($id);
    }

    public function create(array $data): SupplierPrice
    {
        return DB::transaction(function () use ($data) {
            return $this->supplierPrice->create([
                'insumo'           => $data['insumo'],
                'clave_sat'        => $data['clave_sat'] ?? null,
                'proveedor'        => $data['proveedor'],
                'precio'           => $data['precio'],
                'tiene_iva'        => !empty($data['tiene_iva']) ? 1 : 0,
                'moneda'           => $data['moneda'] ?? 'MXN',
                'fecha_cotizacion' => $data['fecha_cotizacion'],
            ]);
        });
    }

    public function update($id, array $data): ?SupplierPrice
    {
        return DB::transaction(function () use ($id, $data) {
            $record = $this->supplierPrice->where('id', $id)->lockForUpdate()->first();
            if (!$record) {
                return null;
            }

            $record->update([
                'insumo'           => $data['insumo'],
                'clave_sat'        => $data['clave_sat'] ?? null,
                'proveedor'        => $data['proveedor'],
                'precio'           => $data['precio'],
                'tiene_iva'        => !empty($data['tiene_iva']) ? 1 : 0,
                'moneda'           => $data['moneda'] ?? 'MXN',
                'fecha_cotizacion' => $data['fecha_cotizacion'],
            ]);

            return $record->fresh();
        });
    }

    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->supplierPrice->where('id', $id)->lockForUpdate()->first();
            if (!$record) {
                return false;
            }

            return (bool) $record->delete();
        });
    }
}
