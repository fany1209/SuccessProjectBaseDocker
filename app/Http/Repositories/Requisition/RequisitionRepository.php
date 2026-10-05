<?php

namespace App\Http\Repositories\Requisition;

use App\Models\PurchaseRequisition;
use App\Models\RequisitionProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RequisitionRepository
{
    protected PurchaseRequisition $purchaseRequisition;
    protected RequisitionProduct $requisitionProduct;

    public function __construct(
        PurchaseRequisition $purchaseRequisition,
        RequisitionProduct $requisitionProduct
    ) {
        $this->purchaseRequisition = $purchaseRequisition;
        $this->requisitionProduct = $requisitionProduct;
    }

    public function getRequisitions(array $filters = []): Collection
    {
        $search = $filters['search_requisitions'] ?? null;
        $check = $filters['requisitions_check'] ?? null;

        $query = DB::table('purchases_requisitions');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('applicant', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('consecutive', 'like', '%' . $search . '%')
                    ->orWhere('purchase_order', 'like', '%' . $search . '%');
            });
        }

        if ((int) $check === 1) {
            $query->whereNotNull('consecutive')->whereNotNull('purchase_order');
        } else {
            $query->whereNull('consecutive')->whereNull('purchase_order');
        }

        $requisitions = $query->orderBy('id', 'desc')->get();
        $ids = $requisitions->pluck('id')->filter()->values()->all();

        $insumoMap = [];
        if (!empty($ids)) {
            $insumoRows = DB::table('requisition_products')
                ->select('pr_id', 'insumo', DB::raw('COUNT(*) as c'))
                ->whereIn('pr_id', $ids)
                ->groupBy('pr_id', 'insumo')
                ->get();

            foreach ($insumoRows as $r) {
                $pid = $r->pr_id;
                if (!isset($insumoMap[$pid])) {
                    $insumoMap[$pid] = [];
                }
                $insumoMap[$pid][strtolower((string) $r->insumo)] = (int) $r->c;
            }
        }

        $user = auth()->user();
        $canUpdate = $user ? $user->can('purchases.requisitions.update') : false;
        $canDelete = $user ? $user->can('purchases.requisitions.delete') : false;

        return $requisitions->map(function ($row) use ($insumoMap, $canUpdate, $canDelete) {
            $pid = $row->id;
            $summary = '—';
            if (isset($insumoMap[$pid])) {
                $hasDirecto = !empty($insumoMap[$pid]['directo']);
                $hasIndirecto = !empty($insumoMap[$pid]['indirecto']);

                if ($hasDirecto && $hasIndirecto) {
                    $summary = 'mixto';
                } elseif ($hasDirecto) {
                    $summary = 'directo';
                } elseif ($hasIndirecto) {
                    $summary = 'indirecto';
                }
            }

            return [
                'id'             => $row->id,
                'consecutive'    => $row->consecutive,
                'purchase_order' => $row->purchase_order,
                'applicant'      => $row->applicant,
                'department'     => $row->department,
                'insumo_resumen' => $summary,
                'data_sheet'     => $row->data_sheet,
                'safety_sheet'   => $row->safety_sheet,
                'canUpdate'      => $canUpdate,
                'canDelete'      => $canDelete,
            ];
        });
    }

    public function getYourRequisitions(string $applicantName): Collection
    {
        $query = DB::table('purchases_requisitions')
            ->leftJoin('requisition_products', 'requisition_products.pr_id', '=', 'purchases_requisitions.id')
            ->select(
                'purchases_requisitions.id',
                DB::raw("CONCAT(DAYNAME(purchases_requisitions.updated_at), ', ', DAY(purchases_requisitions.updated_at), ' ', MONTHNAME(purchases_requisitions.updated_at), ' ', YEAR(purchases_requisitions.updated_at)) as date_formatted"),
                DB::raw('COUNT(requisition_products.id) as products'),
                'purchases_requisitions.data_sheet',
                'purchases_requisitions.safety_sheet',
                'purchases_requisitions.consecutive',
                'purchases_requisitions.purchase_order'
            )
            ->groupBy(
                'purchases_requisitions.id',
                'purchases_requisitions.updated_at',
                'purchases_requisitions.data_sheet',
                'purchases_requisitions.safety_sheet',
                'purchases_requisitions.consecutive',
                'purchases_requisitions.purchase_order'
            )
            ->where('purchases_requisitions.applicant', $applicantName)
            ->orderBy('purchases_requisitions.id', 'desc');

        $requisitions = $query->get();
        $ids = $requisitions->pluck('id')->filter()->values()->all();
        $insumoMap = [];

        if (!empty($ids)) {
            $insumoRows = DB::table('requisition_products')
                ->select('pr_id', 'insumo', DB::raw('COUNT(*) as c'))
                ->whereIn('pr_id', $ids)
                ->groupBy('pr_id', 'insumo')
                ->get();

            foreach ($insumoRows as $r) {
                $pid = $r->pr_id;
                if (!isset($insumoMap[$pid])) {
                    $insumoMap[$pid] = [];
                }
                $insumoMap[$pid][strtolower((string) $r->insumo)] = (int) $r->c;
            }
        }

        $user = auth()->user();
        $canUpdateGlobal = $user ? $user->can('purchases.requisitions.update') : false;
        $canDeleteGlobal = $user ? $user->can('purchases.requisitions.delete') : false;

        return $requisitions->map(function ($row) use ($insumoMap, $canUpdateGlobal, $canDeleteGlobal) {
            $pid = $row->id;
            $summary = '—';

            if (isset($insumoMap[$pid])) {
                $hasDirecto = !empty($insumoMap[$pid]['directo']);
                $hasIndirecto = !empty($insumoMap[$pid]['indirecto']);

                if ($hasDirecto && $hasIndirecto) {
                    $summary = 'mixto';
                } elseif ($hasDirecto) {
                    $summary = 'directo';
                } elseif ($hasIndirecto) {
                    $summary = 'indirecto';
                }
            }

            $isLocked = !is_null($row->consecutive) || !is_null($row->purchase_order);

            return [
                'id'             => $row->id,
                'date_formatted' => $row->date_formatted,
                'products'       => $row->products,
                'insumo_resumen' => $summary,
                'data_sheet'     => $row->data_sheet,
                'safety_sheet'   => $row->safety_sheet,
                'canUpdate'      => !$isLocked && $canUpdateGlobal,
                'canDelete'      => !$isLocked && $canDeleteGlobal,
                'status'         => $isLocked ? 'Revisada / Procesada' : 'Pendiente',
            ];
        });
    }

    public function findWithProducts($id): ?array
    {
        $requisition = $this->purchaseRequisition->where('id', $id)->first();
        if (!$requisition) {
            return null;
        }

        $products = $this->requisitionProduct->where('pr_id', $id)->get();
        $products->transform(function ($product) {
            if (preg_match('/^(\d+(\.\d+)?)\s*([a-zA-Z]+)?$/', (string) $product->quantity, $matches)) {
                $product->qty_number = $matches[1];
                $product->qty_unit = $matches[3] ?? '';
            } else {
                $product->qty_number = $product->quantity;
                $product->qty_unit = '';
            }
            return $product;
        });

        return [
            'requisition' => $requisition,
            'products'    => $products,
        ];
    }

    public function store(array $data): PurchaseRequisition
    {
        return DB::transaction(function () use ($data) {
            $descriptions = $data['description'] ?? [];
            $suppliers = $data['supplier'] ?? [];
            $urls = $data['url'] ?? [];
            $uses = $data['use'] ?? [];
            $quantities = $data['quantity'] ?? [];
            $units = $data['unit'] ?? [];
            $imageUrls = $data['image_url'] ?? [];
            $insumos = $data['insumo'] ?? [];

            $requisition = $this->purchaseRequisition->create([
                'applicant'      => $data['applicant'],
                'department'     => $data['department'],
                'data_sheet'     => $data['data_sheet'],
                'safety_sheet'   => $data['safety_sheet'],
                'comparative_id' => $data['comparative_id'] ?? null,
                'consecutive'    => $data['consecutive'] ?? null,
            ]);

            foreach ($descriptions as $index => $description) {
                if (!empty($description)) {
                    $auxQty = '' . ($quantities[$index] ?? '') . ($units[$index] ?? '');

                    $this->requisitionProduct->create([
                        'pr_id'       => $requisition->id,
                        'description' => $description,
                        'supplier'    => $suppliers[$index] ?? '',
                        'url'         => $urls[$index] ?? '',
                        'use'         => $uses[$index] ?? '',
                        'quantity'    => $auxQty !== '' ? $auxQty : '1',
                        'image_url'   => $imageUrls[$index] ?? '',
                        'insumo'      => $insumos[$index] ?? 'directo',
                    ]);
                }
            }

            return $requisition;
        });
    }

    public function update(int $reqId, array $data): bool
    {
        return DB::transaction(function () use ($reqId, $data) {
            $requisition = $this->purchaseRequisition->where('id', $reqId)->lockForUpdate()->first();
            if (!$requisition) {
                return false;
            }

            $ids = $data['id'] ?? [];
            $descriptions = $data['description'] ?? [];
            $suppliers = $data['supplier'] ?? [];
            $urls = $data['url'] ?? [];
            $uses = $data['use'] ?? [];
            $quantities = $data['quantity'] ?? [];
            $units = $data['unit'] ?? [];
            $imageUrls = $data['image_url'] ?? [];
            $insumos = $data['insumo'] ?? [];

            $keptIds = array_filter((array) $ids, function ($val) {
                return $val !== 'null' && !empty($val);
            });

            $this->requisitionProduct->where('pr_id', $reqId)
                ->whereNotIn('id', $keptIds)
                ->delete();

            $requisition->update([
                'applicant'      => $data['applicant'],
                'department'     => $data['department'],
                'data_sheet'     => $data['data_sheet'],
                'safety_sheet'   => $data['safety_sheet'],
                'comparative_id' => $data['comparative_id'] ?? null,
                'consecutive'    => $data['consecutive'] ?? $requisition->consecutive,
            ]);

            foreach ($ids as $index => $id) {
                $description = $descriptions[$index] ?? null;
                if (!empty($description)) {
                    $auxQty = '' . ($quantities[$index] ?? '') . ($units[$index] ?? '');

                    $productData = [
                        'description' => $description,
                        'supplier'    => $suppliers[$index] ?? '',
                        'url'         => $urls[$index] ?? '',
                        'use'         => $uses[$index] ?? '',
                        'quantity'    => $auxQty !== '' ? $auxQty : '1',
                        'image_url'   => $imageUrls[$index] ?? '',
                        'insumo'      => $insumos[$index] ?? 'directo',
                    ];

                    if ($id == 'null' || empty($id)) {
                        $productData['pr_id'] = $reqId;
                        $this->requisitionProduct->create($productData);
                    } else {
                        $this->requisitionProduct->where('id', $id)->update($productData);
                    }
                }
            }

            return true;
        });
    }

    public function checkRequisition(int $id, string $consecutive, string $purchaseOrder): bool
    {
        return DB::transaction(function () use ($id, $consecutive, $purchaseOrder) {
            $requisition = $this->purchaseRequisition->where('id', $id)->lockForUpdate()->first();
            if (!$requisition) {
                return false;
            }

            $requisition->update([
                'consecutive'    => $consecutive,
                'purchase_order' => $purchaseOrder,
            ]);

            return true;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $requisition = $this->purchaseRequisition->where('id', $id)->lockForUpdate()->first();
            if (!$requisition) {
                return false;
            }

            $this->requisitionProduct->where('pr_id', $id)->delete();
            $requisition->delete();

            return true;
        });
    }

    public function deleteProduct(int $productId): bool
    {
        return DB::transaction(function () use ($productId) {
            $product = $this->requisitionProduct->where('id', $productId)->lockForUpdate()->first();
            if (!$product) {
                return false;
            }

            $product->delete();
            return true;
        });
    }

    public function getPurchaseOrderFolios(): Collection
    {
        return DB::table('purchase_orders')->select('id')->orderBy('id', 'desc')->get();
    }

    public function getComparativeFolios(int $userId): Collection
    {
        return DB::table('comparative')
            ->where('user_id', $userId)
            ->select(DB::raw('MAX(id) as id'), 'folio')
            ->groupBy('folio')
            ->get();
    }

    public function getComparativeProducts(int $comparativeId): Collection
    {
        $comparative = DB::table('comparative')->where('id', $comparativeId)->first();
        if (!$comparative) {
            return collect();
        }

        return DB::table('comparative')
            ->where('folio', $comparative->folio)
            ->whereNotNull('comentarios')
            ->where('comentarios', '!=', '')
            ->get();
    }
}
