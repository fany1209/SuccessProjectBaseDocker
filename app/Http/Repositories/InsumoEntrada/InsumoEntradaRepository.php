<?php

namespace App\Http\Repositories\InsumoEntrada;

use App\Models\InsumoEntrada;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InsumoEntradaRepository
{
    protected InsumoEntrada $model;

    public function __construct(InsumoEntrada $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene el listado completo de entradas ordenadas por ID descendente.
     *
     * @return Collection<int, InsumoEntrada>
     */
    public function all(): Collection
    {
        return $this->model->orderByDesc('id')->get();
    }

    /**
     * Busca una entrada de insumo por su identificador.
     *
     * @param int|string $id
     * @return InsumoEntrada|null
     */
    public function find($id): ?InsumoEntrada
    {
        return $this->model->find($id);
    }

    /**
     * Registra una nueva entrada de insumos con control transaccional.
     *
     * @param array<string, mixed> $data
     * @return InsumoEntrada
     */
    public function create(array $data): InsumoEntrada
    {
        return DB::transaction(function () use ($data) {
            $data['proveedor'] = $this->resolveSupplierName($data);
            unset($data['supplier_id'], $data['supplier_name'], $data['sector_id']);

            return $this->model->create($data);
        });
    }

    /**
     * Actualiza una entrada de insumos con bloqueo pesimista contra condiciones de carrera.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return InsumoEntrada|null
     */
    public function update($id, array $data): ?InsumoEntrada
    {
        return DB::transaction(function () use ($id, $data) {
            $entry = $this->model->lockForUpdate()->find($id);

            if (!$entry) {
                return null;
            }

            if (isset($data['supplier_id']) || isset($data['proveedor'])) {
                $data['proveedor'] = $this->resolveSupplierName($data);
            }

            unset($data['supplier_id'], $data['supplier_name'], $data['sector_id']);

            $entry->update($data);
            return $entry->fresh();
        });
    }

    /**
     * Elimina una entrada de insumos con bloqueo pesimista contra TOCTOU.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $entry = $this->model->lockForUpdate()->find($id);

            if (!$entry) {
                return false;
            }

            return (bool) $entry->delete();
        });
    }

    /**
     * Resuelve el nombre del proveedor a persistir en la entrada.
     * Si se solicita registrar uno nuevo (__other__), se verifica o se da de alta en la tabla suppliers.
     *
     * @param array<string, mixed> $data
     * @return string
     * @throws InvalidArgumentException
     */
    public function resolveSupplierName(array $data): string
    {
        $supplierId = $data['supplier_id'] ?? null;

        if ($supplierId === '__other__') {
            $name = trim((string) ($data['supplier_name'] ?? ''));

            if ($name === '') {
                throw new InvalidArgumentException('El nombre del proveedor no puede estar vacío.');
            }

            $existing = Supplier::whereRaw('LOWER(TRIM(name)) = LOWER(TRIM(?))', [$name])->first();
            if ($existing) {
                return $existing->name;
            }

            $maxId = Supplier::max('supplier_id') ?? 0;
            $supplierCode = 'SP' . ($maxId + 1);

            while (Supplier::where('supplier_code', $supplierCode)->exists()) {
                $maxId++;
                $supplierCode = 'SP' . ($maxId + 1);
            }

            $supplier = Supplier::create([
                'name'          => $name,
                'sector_id'     => $data['sector_id'] ?? null,
                'supplier_code' => $supplierCode,
            ]);

            return $supplier->name;
        }

        if (!empty($supplierId)) {
            $supplier = Supplier::where('supplier_id', $supplierId)->first();
            if ($supplier) {
                return $supplier->name;
            }
            throw new InvalidArgumentException('Proveedor no encontrado.');
        }

        if (!empty($data['proveedor'])) {
            return trim((string) $data['proveedor']);
        }

        throw new InvalidArgumentException('No se especificó la información del proveedor.');
    }

    /**
     * Resuelve rutas absolutas hacia public_html de forma desacoplada.
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
