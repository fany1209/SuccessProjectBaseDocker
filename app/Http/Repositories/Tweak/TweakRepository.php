<?php

namespace App\Http\Repositories\Tweak;

use App\Models\Inventory;
use App\Models\Tweak;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TweakRepository
{
    /**
     * @var Tweak
     */
    protected Tweak $model;

    /**
     * @var Inventory
     */
    protected Inventory $inventoryModel;

    /**
     * TweakRepository constructor.
     *
     * @param Tweak $model
     * @param Inventory $inventoryModel
     */
    public function __construct(Tweak $model, Inventory $inventoryModel)
    {
        $this->model = $model;
        $this->inventoryModel = $inventoryModel;
    }

    /**
     * Obtiene el listado de ajustes con soporte para filtros.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery()->with(['inventory.product', 'user']);

        if (!empty($filters['inventory_id'])) {
            $query->where('inventory_id', $filters['inventory_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->latest('tweak_id')->get();
    }

    /**
     * Busca un ajuste por su ID.
     *
     * @param int|string $id
     * @return Tweak|null
     */
    public function find($id): ?Tweak
    {
        return $this->model->newQuery()
            ->with(['inventory.product', 'user'])
            ->where('tweak_id', $id)
            ->first();
    }

    /**
     * Registra un nuevo ajuste y actualiza el stock de inventario con bloqueo pesimista.
     *
     * @param array<string, mixed> $data
     * @return Tweak
     */
    public function create(array $data): Tweak
    {
        return DB::transaction(function () use ($data) {
            $inventory = $this->inventoryModel->newQuery()
                ->where('inventory_id', $data['inventory_id'])
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                throw new InvalidArgumentException('El registro de inventario no existe.');
            }

            $quantity = (float) $data['quantity'];
            $currentStock = (float) $inventory->stock;

            if (strcasecmp($data['type'], 'Input') === 0) {
                $newStock = $currentStock + $quantity;
            } else {
                $newStock = max(0.0, $currentStock - $quantity);
            }

            $inventory->update(['stock' => $newStock]);

            if (empty($data['user_id'])) {
                $data['user_id'] = auth()->id() ?? 1;
            }

            $tweak = $this->model->create($data);
            $tweak->load(['inventory.product', 'user']);

            return $tweak;
        });
    }

    /**
     * Elimina un ajuste y revierte el stock en inventario con bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $tweak = $this->model->newQuery()
                ->where('tweak_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$tweak) {
                return false;
            }

            $inventory = $this->inventoryModel->newQuery()
                ->where('inventory_id', $tweak->inventory_id)
                ->lockForUpdate()
                ->first();

            if ($inventory) {
                $quantity = (float) $tweak->quantity;
                $currentStock = (float) $inventory->stock;

                if (strcasecmp($tweak->type, 'Input') === 0) {
                    $revertedStock = max(0.0, $currentStock - $quantity);
                } else {
                    $revertedStock = $currentStock + $quantity;
                }

                $inventory->update(['stock' => $revertedStock]);
            }

            return (bool) $tweak->delete();
        });
    }

    /**
     * Resuelve rutas físicas a public_html fuera del directorio base del framework.
     *
     * @param string $subpath
     * @return string
     */
    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }
}
