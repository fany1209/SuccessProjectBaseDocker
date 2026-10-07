<?php

namespace App\Http\Repositories\Vitayela;

use App\Models\Product;
use App\Models\ProductionMaterialRequest;
use App\Models\ProductionMaterialRequestItem;
use App\Models\VitayelaInventory;
use App\Models\VitayelaInventoryMovement;
use App\Models\VitayelaProduction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class VitayelaRepository
{
    protected VitayelaProduction $productionModel;
    protected VitayelaInventory $inventoryModel;
    protected VitayelaInventoryMovement $movementModel;
    protected ProductionMaterialRequest $materialRequestModel;

    public function __construct(
        VitayelaProduction $productionModel,
        VitayelaInventory $inventoryModel,
        VitayelaInventoryMovement $movementModel,
        ProductionMaterialRequest $materialRequestModel
    ) {
        $this->productionModel = $productionModel;
        $this->inventoryModel = $inventoryModel;
        $this->movementModel = $movementModel;
        $this->materialRequestModel = $materialRequestModel;
    }

    public function getAllProductions(): Collection
    {
        return $this->productionModel->newQuery()
            ->orderBy('vitayela_production_id', 'desc')
            ->get();
    }

    public function getAllInventories(): Collection
    {
        return $this->inventoryModel->newQuery()
            ->orderBy('vitayela_inventory_id', 'desc')
            ->get();
    }

    public function getAllProducts(): Collection
    {
        return Product::orderBy('name', 'asc')->get();
    }

    public function storeProduction(array $data): VitayelaProduction
    {
        return DB::transaction(function () use ($data) {
            return $this->productionModel->create($data);
        });
    }

    public function updateProduction(int $id, array $data): VitayelaProduction
    {
        return DB::transaction(function () use ($id, $data) {
            $production = $this->productionModel->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            $production->update($data);

            return $production;
        });
    }

    public function destroyProduction(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $production = $this->productionModel->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            return (bool) $production->delete();
        });
    }

    public function storeInventory(array $data): VitayelaInventory
    {
        return DB::transaction(function () use ($data) {
            $inv = $this->inventoryModel->create($data);

            if (isset($inv->cantidad) && $inv->cantidad > 0) {
                $this->movementModel->create([
                    'vitayela_inventory_id' => $inv->vitayela_inventory_id,
                    'tipo' => 'Entrada',
                    'cantidad' => $inv->cantidad,
                ]);
            }

            return $inv;
        });
    }

    public function updateInventory(int $id, array $data): VitayelaInventory
    {
        return DB::transaction(function () use ($id, $data) {
            $inventory = $this->inventoryModel->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            $oldCantidad = (float) $inventory->cantidad;
            $inventory->update($data);
            $newCantidad = (float) $inventory->cantidad;

            if ($newCantidad != $oldCantidad) {
                $diff = $newCantidad - $oldCantidad;
                $this->movementModel->create([
                    'vitayela_inventory_id' => $inventory->vitayela_inventory_id,
                    'tipo' => $diff > 0 ? 'Entrada' : 'Ajuste',
                    'cantidad' => abs($diff),
                ]);
            }

            return $inventory;
        });
    }

    public function destroyInventory(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $inventory = $this->inventoryModel->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            return (bool) $inventory->delete();
        });
    }

    public function outputInventory(int $id, float $cantidadSalida): array
    {
        return DB::transaction(function () use ($id, $cantidadSalida) {
            $inventory = $this->inventoryModel->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($cantidadSalida > (float) $inventory->cantidad) {
                throw new \DomainException('La cantidad de salida no puede ser mayor a la cantidad en stock.');
            }

            $inventory->cantidad -= $cantidadSalida;
            $inventory->save();

            $this->movementModel->create([
                'vitayela_inventory_id' => $inventory->vitayela_inventory_id,
                'tipo' => 'Salida',
                'cantidad' => $cantidadSalida,
            ]);

            $alert = false;
            $message = 'Salida registrada correctamente.';

            if ($inventory->cantidad <= $inventory->stock_min) {
                $alert = true;
                $message = 'Salida registrada. ALERTA: El producto ha alcanzado su stock mínimo, es necesario solicitar más.';
            }

            return [
                'inventory' => $inventory,
                'message' => $message,
                'alert' => $alert,
            ];
        });
    }

    public function getMovements(int $inventoryId): Collection
    {
        return $this->movementModel->newQuery()
            ->where('vitayela_inventory_id', $inventoryId)
            ->orderBy('movement_id', 'desc')
            ->get();
    }

    public function storeMaterialRequest(array $data): ProductionMaterialRequest
    {
        return DB::transaction(function () use ($data) {
            $materialRequest = $this->materialRequestModel->create([
                'area' => 'vitayela',
                'applicant_name' => $data['applicant_name'],
                'status' => 'Pendiente',
                'comments' => $data['comments'] ?? null,
            ]);

            foreach ($data['products'] as $prod) {
                ProductionMaterialRequestItem::create([
                    'request_id' => $materialRequest->id,
                    'product_name' => $prod['name'],
                    'quantity' => $prod['quantity'],
                    'dispatched_quantity' => 0,
                ]);
            }

            return $materialRequest->load('items');
        });
    }
}
