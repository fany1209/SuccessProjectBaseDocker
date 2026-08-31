<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\YeastProduction;
use Illuminate\Support\Facades\DB;
class YeastProductionController extends Controller
{
    public function index()
    {
        $yeastProductions = YeastProduction::with('output')->orderBy('yeast_production_id', 'desc')->get();
        $pallets = \App\Models\Pallet::with('yeastProductions')->orderBy('pallet_id', 'desc')->get();
        return view('production.yeast.index', compact('yeastProductions', 'pallets'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'internal_weight' => 'nullable|numeric|min:0',
            'external_weight' => 'nullable|numeric|min:0',
            'bags_natural' => 'nullable|integer|min:0',
            'bags_mix' => 'nullable|integer|min:0',
            'bags_white' => 'nullable|integer|min:0',
            'finished_product_kg' => 'nullable|numeric|min:0',
        ]);

        $yeastProduction = YeastProduction::findOrFail($id);

        $bagsNatural = $request->bags_natural ?? 0;
        $bagsMix = $request->bags_mix ?? 0;
        $bagsWhite = $request->bags_white ?? 0;
        $bagsQuantity = $bagsNatural + $bagsMix + $bagsWhite;

        DB::transaction(function () use ($request, $yeastProduction, $bagsNatural, $bagsMix, $bagsWhite, $bagsQuantity) {
            // Update the production record
            $yeastProduction->update([
                'internal_weight' => $request->internal_weight,
                'external_weight' => $request->external_weight,
                'bags_natural' => $request->bags_natural,
                'bags_mix' => $request->bags_mix,
                'bags_white' => $request->bags_white,
                'finished_product_kg' => $request->finished_product_kg,
                'bags_quantity' => $bagsQuantity,
            ]);

            // Reverse previous allocations
            $pivots = DB::table('pallet_yeast_production')->where('yeast_production_id', $yeastProduction->yeast_production_id)->get();
            foreach ($pivots as $pivot) {
                $pallet = \App\Models\Pallet::find($pivot->pallet_id);
                if ($pallet) {
                    $pallet->current_sacks -= $pivot->sacks_contributed;
                    $pallet->status = 'Abierta';
                    $pallet->save();
                }
            }
            DB::table('pallet_yeast_production')->where('yeast_production_id', $yeastProduction->yeast_production_id)->delete();

            // Allocate new sacks
            $this->allocateSacks($yeastProduction->yeast_production_id, 'Natural', $bagsNatural);
            $this->allocateSacks($yeastProduction->yeast_production_id, 'Mix', $bagsMix);
            $this->allocateSacks($yeastProduction->yeast_production_id, 'Blanca', $bagsWhite);
        });

        return response()->json([
            'message' => 'Registro de producción de levadura actualizado correctamente.'
        ]);
    }

    private function getNextPalletNumber($color_type)
    {
        $prefix = '';
        if ($color_type == 'Natural') $prefix = 'TAR-NAT-';
        elseif ($color_type == 'Mix') $prefix = 'TAR-MIX-';
        elseif ($color_type == 'Blanca') $prefix = 'TAR-BLC-';

        $last = \App\Models\Pallet::where('color_type', $color_type)->orderBy('pallet_id', 'desc')->first();
        if ($last) {
            $num = (int) str_replace($prefix, '', $last->pallet_number);
            return $prefix . ($num + 1);
        }
        return $prefix . '1';
    }

    private function allocateSacks($yeast_production_id, $color_type, $sacks_to_allocate)
    {
        while ($sacks_to_allocate > 0) {
            $pallet = \App\Models\Pallet::where('color_type', $color_type)->where('status', 'Abierta')->first();

            if (!$pallet) {
                $pallet = new \App\Models\Pallet();
                $pallet->pallet_number = $this->getNextPalletNumber($color_type);
                $pallet->color_type = $color_type;
                $pallet->current_sacks = 0;
                $pallet->status = 'Abierta';
                $pallet->save();
            }

            $available_space = 40 - $pallet->current_sacks;
            $to_add = min($available_space, $sacks_to_allocate);

            $pallet->current_sacks += $to_add;
            if ($pallet->current_sacks >= 40) {
                $pallet->status = 'Cerrada';
            }
            $pallet->save();

            DB::table('pallet_yeast_production')->insert([
                'pallet_id' => $pallet->pallet_id,
                'yeast_production_id' => $yeast_production_id,
                'sacks_contributed' => $to_add,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sacks_to_allocate -= $to_add;
        }
    }
}
