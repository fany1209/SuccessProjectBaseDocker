<?php

namespace App\Http\Repositories\Production;

use App\Models\Product;
use App\Models\ProductionInventory;
use App\Models\ProductionInventoryMovement;
use App\Models\ProductionMaterialRequest;
use App\Models\ProductionRecord;
use App\Models\ProductionWarehouseTransfer;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UnifiedProductionRepository
{
    protected ProductionRecord $productionModel;
    protected ProductionInventory $inventoryModel;
    protected ProductionInventoryMovement $movementModel;
    protected ProductionMaterialRequest $materialRequestModel;
    protected ProductionWarehouseTransfer $transferModel;
    protected Product $productModel;

    public function __construct(
        ProductionRecord $productionModel,
        ProductionInventory $inventoryModel,
        ProductionInventoryMovement $movementModel,
        ProductionMaterialRequest $materialRequestModel,
        ProductionWarehouseTransfer $transferModel,
        Product $productModel
    ) {
        $this->productionModel = $productionModel;
        $this->inventoryModel = $inventoryModel;
        $this->movementModel = $movementModel;
        $this->materialRequestModel = $materialRequestModel;
        $this->transferModel = $transferModel;
        $this->productModel = $productModel;
    }

    public function getAllProductions(?string $area = null): Collection
    {
        $query = $this->productionModel->newQuery();

        if ($area && in_array($area, ['fertil', 'vitayela'], true)) {
            $query->where('area', $area);
        }

        return $query->orderBy('production_record_id', 'desc')->get();
    }

    public function findProduction(int $id): ?ProductionRecord
    {
        return $this->productionModel->find($id);
    }

    public function createProduction(array $data): ProductionRecord
    {
        return DB::transaction(function () use ($data) {
            return $this->productionModel->create($data);
        });
    }

    public function updateProduction(int $id, array $data): ProductionRecord
    {
        return DB::transaction(function () use ($id, $data) {
            $production = $this->productionModel->newQuery()
                ->where('production_record_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $production->update($data);

            return $production;
        });
    }

    public function deleteProduction(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $production = $this->productionModel->newQuery()
                ->where('production_record_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            return (bool) $production->delete();
        });
    }

    public function getAllInventories(?string $area = null): Collection
    {
        $query = $this->inventoryModel->newQuery();

        if ($area && in_array($area, ['fertil', 'vitayela'], true)) {
            $query->where('area', $area);
        }

        return $query->orderBy('production_inventory_id', 'desc')->get();
    }

    public function findInventory(int $id): ?ProductionInventory
    {
        return $this->inventoryModel->find($id);
    }

    public function createInventory(array $data): ProductionInventory
    {
        return DB::transaction(function () use ($data) {
            $inv = $this->inventoryModel->create($data);

            if (($inv->cantidad ?? 0) > 0) {
                $this->movementModel->create([
                    'production_inventory_id' => $inv->production_inventory_id,
                    'tipo'                    => 'Entrada',
                    'cantidad'                => $inv->cantidad,
                ]);
            }

            return $inv;
        });
    }

    public function updateInventory(int $id, array $data): ProductionInventory
    {
        return DB::transaction(function () use ($id, $data) {
            $inventory = $this->inventoryModel->newQuery()
                ->where('production_inventory_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldCantidad = (float) $inventory->cantidad;
            $inventory->update($data);
            $newCantidad = (float) $inventory->cantidad;

            if ($newCantidad != $oldCantidad) {
                $diff = $newCantidad - $oldCantidad;
                $this->movementModel->create([
                    'production_inventory_id' => $inventory->production_inventory_id,
                    'tipo'                    => $diff > 0 ? 'Entrada' : 'Ajuste',
                    'cantidad'                => abs($diff),
                ]);
            }

            return $inventory;
        });
    }

    public function deleteInventory(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $inventory = $this->inventoryModel->newQuery()
                ->where('production_inventory_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            return (bool) $inventory->delete();
        });
    }

    public function outputInventory(int $id, float $cantidadSalida): array
    {
        return DB::transaction(function () use ($id, $cantidadSalida) {
            $inventory = $this->inventoryModel->newQuery()
                ->where('production_inventory_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($cantidadSalida > (float) $inventory->cantidad) {
                throw new DomainException('La cantidad de salida no puede ser mayor a la cantidad en stock.');
            }

            $inventory->cantidad -= $cantidadSalida;
            $inventory->save();

            $this->movementModel->create([
                'production_inventory_id' => $inventory->production_inventory_id,
                'tipo'                    => 'Salida',
                'cantidad'                => $cantidadSalida,
            ]);

            $alert = false;
            $message = 'Salida registrada correctamente.';

            if ($inventory->cantidad <= $inventory->stock_min && $inventory->stock_min > 0) {
                $alert = true;
                $message = 'Salida registrada. ALERTA: El producto ha alcanzado su stock mínimo, es necesario solicitar más.';
            }

            return [
                'inventory' => $inventory,
                'alert'     => $alert,
                'message'   => $message,
            ];
        });
    }

    public function transferToWarehouse(int $id, array $data, int $userId): ProductionWarehouseTransfer
    {
        return DB::transaction(function () use ($id, $data, $userId) {
            $inventory = $this->inventoryModel->newQuery()
                ->where('production_inventory_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $cantidadSalida = (float) $data['cantidad_salida'];

            if ($cantidadSalida > (float) $inventory->cantidad) {
                throw new DomainException('La cantidad a enviar no puede ser mayor a la cantidad en stock.');
            }

            $inventory->cantidad -= $cantidadSalida;
            $inventory->save();

            $this->movementModel->create([
                'production_inventory_id' => $inventory->production_inventory_id,
                'tipo'                    => 'Salida',
                'cantidad'                => $cantidadSalida,
            ]);

            $pesoPorUnidad = (float) $data['peso_por_unidad'];

            return $this->transferModel->create([
                'area'            => $inventory->area,
                'production_id'   => $inventory->production_inventory_id,
                'product_id'      => $data['product_id'],
                'product_name'    => $inventory->producto_descripcion,
                'unit_type'       => $data['unit_type'] ?? null,
                'quantity'        => $cantidadSalida,
                'weight_per_unit' => $pesoPorUnidad,
                'total_weight'    => $cantidadSalida * $pesoPorUnidad,
                'status'          => 'Pendiente',
                'created_by'      => $userId,
            ]);
        });
    }

    public function getMovements(int $inventoryId): Collection
    {
        return $this->movementModel->newQuery()
            ->where('production_inventory_id', $inventoryId)
            ->orderBy('movement_id', 'desc')
            ->get();
    }

    public function createMaterialRequest(array $data): ProductionMaterialRequest
    {
        return DB::transaction(function () use ($data) {
            $materialRequest = $this->materialRequestModel->create([
                'area'           => $data['area'],
                'applicant_name' => $data['applicant_name'],
                'status'         => 'Pendiente',
                'comments'       => $data['comments'] ?? null,
            ]);

            foreach ($data['products'] as $prod) {
                $materialRequest->items()->create([
                    'product_name'        => $prod['name'],
                    'quantity'            => $prod['quantity'],
                    'dispatched_quantity' => 0,
                ]);
            }

            return $materialRequest->load('items');
        });
    }

    public function getDbProducts(): Collection
    {
        return $this->productModel->newQuery()
            ->orderBy('name', 'asc')
            ->get();
    }
}

