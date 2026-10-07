<?php

namespace App\Http\Repositories\YeastProduction;

use App\Models\Pallet;
use App\Models\YeastProduction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class YeastProductionRepository
{
    protected YeastProduction $yeastModel;
    protected Pallet $palletModel;

    public function __construct(YeastProduction $yeastModel, Pallet $palletModel)
    {
        $this->yeastModel = $yeastModel;
        $this->palletModel = $palletModel;
    }

    public function getAllProductions(): Collection
    {
        return $this->yeastModel->newQuery()
            ->with(['output', 'pallets'])
            ->orderBy('yeast_production_id', 'desc')
            ->get();
    }

    public function getAllPallets(): Collection
    {
        return $this->palletModel->newQuery()
            ->with('yeastProductions')
            ->orderBy('pallet_id', 'desc')
            ->get();
    }

    public function find(int $id): ?YeastProduction
    {
        return $this->yeastModel->newQuery()
            ->with(['output', 'pallets'])
            ->find($id);
    }

    public function sendPalletToInventory(int $id): Pallet
    {
        return DB::transaction(function () use ($id) {
            $pallet = $this->palletModel->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($pallet->status !== 'Cerrada' || $pallet->inventory_status !== 'Pendiente') {
                throw new \DomainException('La tarima no está disponible para enviar.');
            }

            $pallet->inventory_status = 'Enviada';
            $pallet->save();

            return $pallet;
        });
    }

    public function updateProduction(int $id, array $data): YeastProduction
    {
        return DB::transaction(function () use ($id, $data) {
            $yeastProduction = $this->yeastModel->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            $bagsNatural = isset($data['bags_natural']) ? (int)$data['bags_natural'] : 0;
            $bagsMix = isset($data['bags_mix']) ? (int)$data['bags_mix'] : 0;
            $bagsWhite = isset($data['bags_white']) ? (int)$data['bags_white'] : 0;
            $bagsQuantity = $bagsNatural + $bagsMix + $bagsWhite;

            $yeastProduction->update([
                'internal_weight' => $data['internal_weight'] ?? null,
                'external_weight' => $data['external_weight'] ?? null,
                'bags_natural' => $data['bags_natural'] ?? null,
                'bags_mix' => $data['bags_mix'] ?? null,
                'bags_white' => $data['bags_white'] ?? null,
                'finished_product_kg' => $data['finished_product_kg'] ?? null,
                'bags_quantity' => $bagsQuantity,
            ]);

            $pivots = DB::table('pallet_yeast_production')
                ->where('yeast_production_id', $yeastProduction->yeast_production_id)
                ->get();

            foreach ($pivots as $pivot) {
                $pallet = $this->palletModel->newQuery()
                    ->lockForUpdate()
                    ->find($pivot->pallet_id);

                if ($pallet) {
                    $pallet->current_sacks -= $pivot->sacks_contributed;
                    $pallet->status = 'Abierta';
                    $pallet->save();
                }
            }

            DB::table('pallet_yeast_production')
                ->where('yeast_production_id', $yeastProduction->yeast_production_id)
                ->delete();

            $this->allocateSacks($yeastProduction->yeast_production_id, 'Natural', $bagsNatural);
            $this->allocateSacks($yeastProduction->yeast_production_id, 'Mix', $bagsMix);
            $this->allocateSacks($yeastProduction->yeast_production_id, 'Blanca', $bagsWhite);

            return $yeastProduction->fresh(['output', 'pallets']);
        });
    }

    protected function allocateSacks(int $yeastProductionId, string $colorType, int $sacksToAllocate): void
    {
        while ($sacksToAllocate > 0) {
            $pallet = $this->palletModel->newQuery()
                ->where('color_type', $colorType)
                ->where('status', 'Abierta')
                ->lockForUpdate()
                ->first();

            if (!$pallet) {
                $pallet = new Pallet();
                $pallet->pallet_number = $this->getNextPalletNumber($colorType);
                $pallet->color_type = $colorType;
                $pallet->current_sacks = 0;
                $pallet->status = 'Abierta';
                $pallet->inventory_status = 'Pendiente';
                $pallet->save();
            }

            $availableSpace = 40 - $pallet->current_sacks;
            $toAdd = min($availableSpace, $sacksToAllocate);

            $pallet->current_sacks += $toAdd;
            if ($pallet->current_sacks >= 40) {
                $pallet->status = 'Cerrada';
            }
            $pallet->save();

            DB::table('pallet_yeast_production')->insert([
                'pallet_id' => $pallet->pallet_id,
                'yeast_production_id' => $yeastProductionId,
                'sacks_contributed' => $toAdd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sacksToAllocate -= $toAdd;
        }
    }

    protected function getNextPalletNumber(string $colorType): string
    {
        $prefix = match ($colorType) {
            'Natural' => 'TAR-NAT-',
            'Mix' => 'TAR-MIX-',
            'Blanca' => 'TAR-BLC-',
            default => 'TAR-',
        };

        $last = $this->palletModel->newQuery()
            ->where('color_type', $colorType)
            ->orderBy('pallet_id', 'desc')
            ->first();

        if ($last) {
            $num = (int) str_replace($prefix, '', $last->pallet_number);
            return $prefix . ($num + 1);
        }

        return $prefix . '1';
    }
}
