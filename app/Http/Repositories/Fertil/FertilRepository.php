<?php

namespace App\Http\Repositories\Fertil;

use App\Models\FertilInventory;
use App\Models\FertilInventoryMovement;
use App\Models\FertilProduction;
use App\Models\Product;
use App\Models\ProductionMaterialRequest;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FertilRepository
{
    protected FertilProduction $productionModel;
    protected FertilInventory $inventoryModel;
    protected FertilInventoryMovement $movementModel;
    protected ProductionMaterialRequest $materialRequestModel;
    protected Product $productModel;

    public function __construct(
        FertilProduction $productionModel,
        FertilInventory $inventoryModel,
        FertilInventoryMovement $movementModel,
        ProductionMaterialRequest $materialRequestModel,
        Product $productModel
    ) {
        $this->productionModel = $productionModel;
        $this->inventoryModel = $inventoryModel;
        $this->movementModel = $movementModel;
        $this->materialRequestModel = $materialRequestModel;
        $this->productModel = $productModel;
    }

    public function getAllProductions(): Collection
    {
        return $this->productionModel->orderBy('fertil_production_id', 'desc')->get();
    }

    public function findProduction(int $id): ?FertilProduction
    {
        return $this->productionModel->find($id);
    }

    public function createProduction(array $data): FertilProduction
    {
        return DB::transaction(function () use ($data) {
            return $this->productionModel->create($data);
        });
    }

    public function updateProduction(int $id, array $data): FertilProduction
    {
        return DB::transaction(function () use ($id, $data) {
            $production = $this->productionModel->where('fertil_production_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $production->update($data);

            return $production;
        });
    }

    public function deleteProduction(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $production = $this->productionModel->where('fertil_production_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            return (bool) $production->delete();
        });
    }

    public function getAllInventories(): Collection
    {
        return $this->inventoryModel->orderBy('fertil_inventory_id', 'desc')->get();
    }

    public function findInventory(int $id): ?FertilInventory
    {
        return $this->inventoryModel->find($id);
    }

    public function createInventory(array $data): FertilInventory
    {
        return DB::transaction(function () use ($data) {
            $inv = $this->inventoryModel->create($data);

            if (($inv->cantidad ?? 0) > 0) {
                $this->movementModel->create([
                    'fertil_inventory_id' => $inv->fertil_inventory_id,
                    'tipo'                => 'Entrada',
                    'cantidad'            => $inv->cantidad,
                ]);
            }

            return $inv;
        });
    }

    public function updateInventory(int $id, array $data): FertilInventory
    {
        return DB::transaction(function () use ($id, $data) {
            $inventory = $this->inventoryModel->where('fertil_inventory_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldCantidad = (float) $inventory->cantidad;
            $inventory->update($data);
            $newCantidad = (float) $inventory->cantidad;

            if ($newCantidad != $oldCantidad) {
                $diff = $newCantidad - $oldCantidad;
                $this->movementModel->create([
                    'fertil_inventory_id' => $inventory->fertil_inventory_id,
                    'tipo'                => $diff > 0 ? 'Entrada' : 'Ajuste',
                    'cantidad'            => abs($diff),
                ]);
            }

            return $inventory;
        });
    }

    public function deleteInventory(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $inventory = $this->inventoryModel->where('fertil_inventory_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            return (bool) $inventory->delete();
        });
    }

    public function outputInventory(int $id, float $cantidadSalida): array
    {
        return DB::transaction(function () use ($id, $cantidadSalida) {
            $inventory = $this->inventoryModel->where('fertil_inventory_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($cantidadSalida > $inventory->cantidad) {
                throw new DomainException('La cantidad de salida no puede ser mayor a la cantidad en stock.');
            }

            $inventory->cantidad -= $cantidadSalida;
            $inventory->save();

            $this->movementModel->create([
                'fertil_inventory_id' => $inventory->fertil_inventory_id,
                'tipo'                => 'Salida',
                'cantidad'            => $cantidadSalida,
            ]);

            $alert = false;
            $message = 'Salida registrada correctamente.';

            if ($inventory->cantidad <= $inventory->stock_min) {
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

    public function getMovements(int $inventoryId): Collection
    {
        return $this->movementModel->where('fertil_inventory_id', $inventoryId)
            ->orderBy('movement_id', 'desc')
            ->get();
    }

    public function createMaterialRequest(array $data): ProductionMaterialRequest
    {
        return DB::transaction(function () use ($data) {
            $materialRequest = $this->materialRequestModel->create([
                'area'           => 'fertil',
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
        return $this->productModel->orderBy('name', 'asc')->get();
    }
}
