<?php

namespace App\Http\Repositories\Cli;

use App\Models\Cli;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CliRepository
{
    protected Cli $cli;

    public function __construct(Cli $cli)
    {
        $this->cli = $cli;
    }

    public function all(): Collection
    {
        return $this->cli->with(['inventory', 'location', 'concept'])->latest('cli_id')->get();
    }

    public function find(int $id): ?Cli
    {
        return $this->cli->find($id);
    }

    public function findWithWarehouseAndProduct(int $id)
    {
        return DB::table('cli')
            ->select(
                'cli.cli_id',
                'cli.location_id',
                'cli.concept_id',
                'cli.inventory_id',
                'cli.quantity',
                'cli.weight_per_unit',
                'cli.net_weight',
                'cli.bag_number',
                'cli.protein',
                'inventory.product_id',
                'locations.warehouse_id'
            )
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('inventory', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->where('cli.cli_id', $id)
            ->first();
    }

    public function createMany(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $locationId = $data['location_id'];
            $concepts = $data['concept_id'];
            $inventories = $data['inventory_id'];
            $quantities = $data['quantity'];
            $weights = $data['weight_per_unit'];
            $bagNumbers = $data['bag_number'] ?? [];
            $proteins = $data['protein'] ?? [];
            $bagWeights = $data['bag_weight'] ?? [];
            $packingLists = $data['packing_list'] ?? [];

            $createdRecords = [];

            foreach ($concepts as $index => $concept) {
                $packingList = !empty($packingLists[$index]) ? trim((string) $packingLists[$index]) : null;

                if (isset($bagNumbers[$index]) && is_array($bagNumbers[$index]) && count($bagNumbers[$index]) > 0) {
                    foreach ($bagNumbers[$index] as $bIdx => $bagNum) {
                        $rawBagWeight = $bagWeights[$index][$bIdx] ?? null;
                        $realWeight = (!is_null($rawBagWeight) && $rawBagWeight !== '' && is_numeric($rawBagWeight))
                            ? (float) $rawBagWeight
                            : (float) $weights[$index];

                        $rawProtein = $proteins[$index][$bIdx] ?? null;
                        $proteinVal = (!is_null($rawProtein) && $rawProtein !== '' && is_numeric($rawProtein))
                            ? (float) $rawProtein
                            : null;

                        $createdRecords[] = $this->cli->create([
                            'location_id'     => $locationId,
                            'inventory_id'    => $inventories[$index],
                            'concept_id'      => $concept,
                            'quantity'        => 1,
                            'weight_per_unit' => $realWeight,
                            'net_weight'      => 1 * $realWeight,
                            'bag_number'      => $bagNum,
                            'protein'         => $proteinVal,
                            'packing_list'    => $packingList,
                        ]);
                    }
                } else {
                    $qty = $quantities[$index];
                    $weight = $weights[$index];
                    $createdRecords[] = $this->cli->create([
                        'location_id'     => $locationId,
                        'inventory_id'    => $inventories[$index],
                        'concept_id'      => $concept,
                        'quantity'        => $qty,
                        'weight_per_unit' => $weight,
                        'net_weight'      => $qty * $weight,
                        'packing_list'    => $packingList,
                    ]);
                }
            }

            return $createdRecords;
        });
    }

    public function update(int $id, array $data): ?Cli
    {
        return DB::transaction(function () use ($id, $data) {
            $cli = $this->cli->lockForUpdate()->find($id);
            if (!$cli) {
                return null;
            }

            $qty = $data['quantity'] ?? $cli->quantity;
            $weight = $data['weight_per_unit'] ?? $cli->weight_per_unit;

            $cli->update([
                'location_id'     => $data['location_id'] ?? $cli->location_id,
                'concept_id'      => $data['concept_id'] ?? $cli->concept_id,
                'inventory_id'    => $data['inventory_id'] ?? $cli->inventory_id,
                'quantity'        => $qty,
                'weight_per_unit' => $weight,
                'net_weight'      => $qty * $weight,
                'bag_number'      => array_key_exists('bag_number', $data) ? $data['bag_number'] : $cli->bag_number,
                'protein'         => array_key_exists('protein', $data) ? $data['protein'] : $cli->protein,
            ]);

            return $cli;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $cli = $this->cli->lockForUpdate()->find($id);
            if (!$cli) {
                return false;
            }

            return (bool) $cli->delete();
        });
    }
}
