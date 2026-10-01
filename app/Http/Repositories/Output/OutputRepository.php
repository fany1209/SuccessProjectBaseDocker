<?php

namespace App\Http\Repositories\Output;

use App\Models\Output;
use App\Models\ProductOutputs;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OutputRepository
{
    /**
     * @var Output
     */
    protected Output $model;

    /**
     * @var ProductOutputs
     */
    protected ProductOutputs $productOutputModel;

    /**
     * OutputRepository constructor.
     *
     * @param Output $model
     * @param ProductOutputs $productOutputModel
     */
    public function __construct(Output $model, ProductOutputs $productOutputModel)
    {
        $this->model = $model;
        $this->productOutputModel = $productOutputModel;
    }

    /**
     * Obtiene el listado de salidas de almacén con relaciones eager-loaded.
     *
     * @param array<string, mixed> $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery()
            ->with(['customer', 'transportLine', 'products']);

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('operator', 'like', "%{$search}%")
                  ->orWhere('unit_plates', 'like', "%{$search}%")
                  ->orWhere('vendedor', 'like', "%{$search}%");
            });
        }

        return $query->latest('output_id')->get();
    }

    /**
     * Busca una salida por su ID con datos relacionados del cliente, transporte y productos.
     *
     * @param int|string $id
     * @return Output|null
     */
    public function find($id): ?Output
    {
        $output = $this->model->newQuery()
            ->select(
                'outputs.*',
                'customers.name as cName',
                'transport_lines.name as tName'
            )
            ->where('outputs.output_id', $id)
            ->leftJoin('customers', 'customers.customer_id', '=', 'outputs.customer_id')
            ->leftJoin('transport_lines', 'transport_lines.transport_line_id', '=', 'outputs.transport_line_id')
            ->first();

        if ($output) {
            $output->load('products');
        }

        return $output;
    }

    /**
     * Obtiene los productos vinculados a una salida con nombres y unidades.
     *
     * @param int|string $outputId
     * @return \Illuminate\Support\Collection
     */
    public function getProductsForOutput($outputId)
    {
        return $this->productOutputModel->newQuery()
            ->where('output_id', $outputId)
            ->join('products', 'products.product_id', '=', 'product_outputs.product_id')
            ->select('product_outputs.*', 'products.name', 'products.unit')
            ->get();
    }

    /**
     * Registra una salida y sus productos en transacción ACID.
     *
     * @param array<string, mixed> $headerData
     * @param array<int, array<string, mixed>> $products
     * @return Output
     */
    public function create(array $headerData, array $products = []): Output
    {
        return DB::transaction(function () use ($headerData, $products) {
            $output = $this->model->create($headerData);

            foreach ($products as $item) {
                $this->productOutputModel->create([
                    'output_id'       => $output->output_id,
                    'product_id'      => $item['product_id'],
                    'quantity'        => $item['quantity'],
                    'warehouse_batch' => $item['warehouse_batch'] ?? '',
                    'label_batch'     => $item['label_batch'] ?? '',
                ]);
            }

            return $this->find($output->output_id);
        });
    }

    /**
     * Actualiza una salida y sus productos asociados con bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @param array<string, mixed> $headerData
     * @param array<int, array<string, mixed>> $products
     * @return Output|null
     */
    public function update($id, array $headerData, array $products = []): ?Output
    {
        return DB::transaction(function () use ($id, $headerData, $products) {
            $output = $this->model->newQuery()
                ->where('output_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$output) {
                return null;
            }

            $output->update($headerData);

            foreach ($products as $item) {
                $productId = $item['product_id'];
                $qty = (float) ($item['quantity'] ?? 0.0);
                $labelBatch = $item['label_batch'] ?? '';
                $whBatch = $item['warehouse_batch'] ?? '';

                $productOutput = $this->productOutputModel->newQuery()
                    ->where('output_id', $id)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($productOutput) {
                    $productOutput->update([
                        'quantity'        => $qty,
                        'label_batch'     => $labelBatch,
                        'warehouse_batch' => $whBatch,
                    ]);
                } else {
                    $this->productOutputModel->create([
                        'output_id'       => $id,
                        'product_id'      => $productId,
                        'quantity'        => $qty,
                        'warehouse_batch' => $whBatch,
                        'label_batch'     => $labelBatch,
                    ]);
                }
            }

            return $this->find($id);
        });
    }

    /**
     * Elimina una salida y sus productos en transacción ACID con bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $output = $this->model->newQuery()
                ->where('output_id', $id)
                ->lockForUpdate()
                ->first();

            if (!$output) {
                return false;
            }

            $this->productOutputModel->newQuery()
                ->where('output_id', $id)
                ->delete();

            return (bool) $output->delete();
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
