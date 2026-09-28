<?php

namespace App\Http\Repositories\Input;

use App\Models\Cli;
use App\Models\Input;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Operator;
use App\Models\ProductInputs;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InputRepository
{
    protected Input $model;

    public function __construct(Input $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['supplier', 'transportLine'])->orderBy('created_at', 'desc')->get();
    }

    public function findById(int $id): ?Input
    {
        return $this->model->find($id);
    }

    public function getWithDetails(int $id): ?array
    {
        $input = Input::select(
            'inputs.input_id',
            'inputs.supplier_id',
            'inputs.security_seal',
            'inputs.security_seal_number',
            'inputs.transport_line_id',
            'inputs.operator',
            'inputs.license_number',
            'inputs.unit_plates',
            'inputs.trailer_plates',
            'inputs.comments',
            'inputs.created_at',
            'suppliers.name as sName',
            'transport_lines.name as tName'
        )
            ->where('inputs.input_id', $id)
            ->leftJoin('suppliers', 'suppliers.supplier_id', '=', 'inputs.supplier_id')
            ->leftJoin('transport_lines', 'transport_lines.transport_line_id', '=', 'inputs.transport_line_id')
            ->first();

        if (!$input) {
            return null;
        }

        $products = ProductInputs::where('input_id', $id)
            ->join('products', 'products.product_id', '=', 'product_inputs.product_id')
            ->select(
                'product_inputs.*',
                'products.name',
                'products.unit'
            )
            ->get();

        return [
            'input'    => $input,
            'products' => $products,
        ];
    }

    public function createWithTransactions(array $data): Input
    {
        return DB::transaction(function () use ($data) {
            $operatorName = $data['operator'] ?? null;
            $licenseNumber = $data['license_number'] ?? null;

            if (isset($data['operator_id']) && (!$operatorName || !$licenseNumber)) {
                $op = Operator::find($data['operator_id']);
                if ($op) {
                    $operatorName = $operatorName ?: $op->name;
                    $licenseNumber = $licenseNumber ?: $op->license;
                }
            }

            $input = $this->model->create([
                'supplier_id'          => $data['supplier_id'],
                'transport_line_id'    => $data['transport_line_id'] ?? null,
                'operator'             => $operatorName,
                'license_number'       => $licenseNumber,
                'security_seal'        => $data['security_seal'] ?? 0,
                'security_seal_number' => $data['security_seal_number'] ?? null,
                'unit_plates'          => $data['unit_plates'] ?? null,
                'trailer_plates'       => $data['trailer_plates'] ?? null,
                'comments'             => $data['comments'] ?? null,
            ]);

            $products = [];
            $productIds = $data['product_id'] ?? [];
            $stocks = $data['stock'] ?? [];
            $batches = $data['warehouse_batch'] ?? [];
            $concepts = $data['concept_id'] ?? [];
            $platforms = $data['platforms'] ?? [];

            foreach ($productIds as $index => $productId) {
                $qty = isset($stocks[$index]) ? (float) $stocks[$index] : 0.0;
                $batch = $batches[$index] ?? 'BATCH-' . uniqid();
                $conceptId = $concepts[$index] ?? null;
                $numPlatforms = isset($platforms[$index]) ? (int) $platforms[$index] : 0;

                $inventory = Inventory::create([
                    'stock'      => $qty,
                    'batch'      => $batch,
                    'product_id' => $productId,
                ]);

                ProductInputs::create([
                    'product_id'      => $productId,
                    'input_id'        => $input->input_id,
                    'quantity'        => $qty,
                    'warehouse_batch' => $batch,
                ]);

                $products[$index] = [
                    'product_id'      => $productId,
                    'input_id'        => $input->input_id,
                    'quantity'        => $qty,
                    'warehouse_batch' => $batch,
                    'concept_id'      => $conceptId,
                    'platforms'       => $numPlatforms,
                    'inventory_id'    => $inventory->inventory_id,
                ];
            }

            $locations = $data['location_name'] ?? [];
            $weightsPerUnit = $data['weight_per_unit'] ?? [];
            $quantities = $data['quantity'] ?? [];
            $platformsData = [];

            if (!empty($locations)) {
                foreach ($locations as $index => $locationName) {
                    $w = isset($weightsPerUnit[$index][0]) ? (float) $weightsPerUnit[$index][0] : 1.0;
                    $q = isset($quantities[$index][0]) ? (float) $quantities[$index][0] : 1.0;
                    $locId = Location::where('name', $locationName)->value('location_id');

                    $platformsData[$index] = [
                        'weight_per_unit' => $w,
                        'quantity'        => $q,
                        'location_id'     => $locId,
                    ];
                }

                foreach ($products as $product) {
                    for ($i = 0; $i < $product['platforms']; $i++) {
                        if (!empty($platformsData[0])) {
                            $item = $platformsData[0];
                            Cli::create([
                                'inventory_id'    => $product['inventory_id'],
                                'concept_id'      => $product['concept_id'],
                                'location_id'     => $item['location_id'],
                                'quantity'        => $item['quantity'],
                                'weight_per_unit' => $item['weight_per_unit'],
                                'net_weight'      => $item['quantity'] * $item['weight_per_unit'],
                            ]);
                            array_shift($platformsData);
                        }
                    }
                }
            }

            return $input;
        });
    }

    public function updateWithInventory(int $id, array $data): ?Input
    {
        return DB::transaction(function () use ($id, $data) {
            $input = $this->model->where('input_id', $id)->lockForUpdate()->first();
            if (!$input) {
                return null;
            }

            $input->update([
                'operator'             => $data['operator'] ?? $input->operator,
                'license_number'       => $data['license_number'] ?? $input->license_number,
                'security_seal'        => $data['security_seal'] ?? $input->security_seal,
                'security_seal_number' => $data['security_seal_number'] ?? $input->security_seal_number,
                'unit_plates'          => $data['unit_plates'] ?? $input->unit_plates,
                'trailer_plates'       => $data['trailer_plates'] ?? $input->trailer_plates,
                'comments'             => $data['comments'] ?? $input->comments,
                'supplier_id'          => $data['supplier_id'] ?? $input->supplier_id,
                'transport_line_id'    => $data['transport_line_id'] ?? $input->transport_line_id,
            ]);

            $batches = $data['warehouse_batch'] ?? [];
            $quantities = $data['quantity'] ?? [];

            foreach ($batches as $index => $warehouseBatch) {
                $batchStr = is_string($warehouseBatch) ? trim($warehouseBatch) : $warehouseBatch;
                $qty = isset($quantities[$index]) ? (float) $quantities[$index] : 0.0;

                $productId = Inventory::where('batch', $batchStr)->value('product_id');

                if ($productId) {
                    ProductInputs::where('input_id', $id)
                        ->where('product_id', $productId)
                        ->update(['quantity' => $qty]);
                } else {
                    ProductInputs::where('input_id', $id)
                        ->where('warehouse_batch', $batchStr)
                        ->update(['quantity' => $qty]);
                }

                Inventory::where('batch', $batchStr)->update(['stock' => $qty]);
            }

            return $input;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $input = $this->model->where('input_id', $id)->lockForUpdate()->first();
            if (!$input) {
                return false;
            }

            ProductInputs::where('input_id', $id)->delete();

            return (bool) $input->delete();
        });
    }
}
