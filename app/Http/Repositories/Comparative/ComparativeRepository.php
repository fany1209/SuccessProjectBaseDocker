<?php

namespace App\Http\Repositories\Comparative;

use App\Models\Comparative;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ComparativeRepository
{
    protected Comparative $model;

    public function __construct(Comparative $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->orderBy('id', 'desc')->get();
    }

    public function getAllGroupedByFolio()
    {
        return $this->all()->groupBy('folio');
    }

    public function getByFolio(string $folio): Collection
    {
        return $this->model->where('folio', $folio)->get();
    }

    public function getRequisitionCounts(?string $applicantName = null): array
    {
        $yourRequisitions = $applicantName
            ? DB::table('purchases_requisitions')->where('applicant', $applicantName)->count()
            : 0;

        $requisitionsCount = DB::table('purchases_requisitions')->count();

        $requisitionsNoCheck = DB::table('purchases_requisitions')
            ->whereNull('consecutive')
            ->count();

        return [
            'your_requisitions' => $yourRequisitions,
            'requisitions_count' => $requisitionsCount,
            'requisitions_no_check' => $requisitionsNoCheck,
        ];
    }

    public function generateFolio(): string
    {
        $fecha = now()->format('d-m-Y');
        $prefijo = "COMP-" . $fecha;

        $ultimoRegistro = $this->model
            ->where('folio', 'like', $prefijo . '%')
            ->distinct()
            ->count('folio');

        $consecutivo = str_pad($ultimoRegistro + 1, 3, '0', STR_PAD_LEFT);

        return $prefijo . "-" . $consecutivo;
    }

    public function storeComparative(array $data, int $userId): array
    {
        return DB::transaction(function () use ($data, $userId) {
            $folio = $this->generateFolio();
            $createdItems = [];

            foreach ($data['insumo'] as $key => $insumo) {
                $cantidad = isset($data['cantidad'][$key]) && is_numeric($data['cantidad'][$key]) && (int) $data['cantidad'][$key] > 0
                    ? (int) $data['cantidad'][$key]
                    : 1;

                $entregaEstimada = (!empty($data['entrega_estimada'][$key]) && strtotime($data['entrega_estimada'][$key]))
                    ? date('Y-m-d', strtotime($data['entrega_estimada'][$key]))
                    : null;

                $recordData = [
                    'folio' => $folio,
                    'user_id' => $userId,
                    'insumo' => $insumo,
                    'cantidad' => $cantidad,
                    'proveedor' => $data['proveedor'][$key] ?? null,
                    'precio_unt' => $data['precio_unt'][$key] ?? 0,
                    'precio_total' => $data['precio_total'][$key] ?? 0,
                    'imagen' => $data['imagen'][$key] ?? null,
                    'descripcion' => $data['descripcion'][$key] ?? null,
                    'comentarios' => $data['comentarios'][$key] ?? null,
                    'entrega_estimada' => $entregaEstimada,
                    'link' => $data['link'][$key] ?? null,
                ];

                $createdItems[] = $this->model->create($recordData);
            }

            return [
                'folio' => $folio,
                'items' => $createdItems,
            ];
        });
    }

    public function deleteByFolio(string $folio): bool
    {
        return DB::transaction(function () use ($folio) {
            $items = $this->model->where('folio', $folio)->lockForUpdate()->get();

            if ($items->isEmpty()) {
                return false;
            }

            $this->model->where('folio', $folio)->delete();

            return true;
        });
    }

    public function updateAll(array $data, int $userId): ?string
    {
        return DB::transaction(function () use ($data, $userId) {
            $idsRecibidos = array_filter($data['id'] ?? []);
            $folio = null;

            if (count($idsRecibidos) > 0) {
                $primerItem = $this->model->where('id', reset($idsRecibidos))->lockForUpdate()->first();
                $folio = $primerItem ? $primerItem->folio : null;
            }

            if ($folio) {
                $this->model
                    ->where('folio', $folio)
                    ->whereNotIn('id', $idsRecibidos)
                    ->lockForUpdate()
                    ->delete();
            }

            foreach ($data['insumo'] as $key => $insumoValor) {
                $id = $data['id'][$key] ?? null;
                $cantidad = (isset($data['cantidad'][$key]) && is_numeric($data['cantidad'][$key]) && (int) $data['cantidad'][$key] > 0)
                    ? (int) $data['cantidad'][$key]
                    : 1;

                $precioTotal = isset($data['precio_total'][$key]) ? (float) $data['precio_total'][$key] : 0.0;
                $precioUnitario = $cantidad > 0 ? ($precioTotal / $cantidad) : 0.0;

                $entregaEstimada = (!empty($data['entrega_estimada'][$key]) && strtotime($data['entrega_estimada'][$key]))
                    ? date('Y-m-d', strtotime($data['entrega_estimada'][$key]))
                    : null;

                $recordData = [
                    'insumo' => $insumoValor,
                    'cantidad' => $cantidad,
                    'proveedor' => $data['proveedor'][$key] ?? null,
                    'precio_total' => $precioTotal,
                    'precio_unt' => $precioUnitario,
                    'imagen' => $data['imagen'][$key] ?? null,
                    'descripcion' => $data['descripcion'][$key] ?? null,
                    'comentarios' => $data['comentarios'][$key] ?? null,
                    'entrega_estimada' => $entregaEstimada,
                    'link' => $data['link'][$key] ?? null,
                ];

                if (!empty($id)) {
                    $item = $this->model->where('id', $id)->lockForUpdate()->first();
                    if ($item) {
                        $item->update($recordData);
                        if (!$folio) {
                            $folio = $item->folio;
                        }
                    }
                } else {
                    if ($folio) {
                        $recordData['folio'] = $folio;
                        $recordData['user_id'] = $userId;
                        $this->model->create($recordData);
                    }
                }
            }

            return $folio;
        });
    }
}
