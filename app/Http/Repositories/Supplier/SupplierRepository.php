<?php

namespace App\Http\Repositories\Supplier;

use App\Models\Sector;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SupplierRepository
{
    protected Supplier $supplier;
    protected Sector $sector;

    public function __construct(Supplier $supplier, Sector $sector)
    {
        $this->supplier = $supplier;
        $this->sector = $sector;
    }

    public function getIndexData(): array
    {
        return [
            'sectors'         => $this->sector->all(),
            'total_suppliers' => $this->supplier->count(),
        ];
    }

    public function getSuppliers(array $filters = []): Collection
    {
        $search = $filters['search'] ?? null;
        $sectorId = $filters['sector'] ?? null;

        $query = DB::table('suppliers')
            ->join('sectors', 'sectors.sector_id', '=', 'suppliers.sector_id')
            ->select(
                'suppliers.supplier_id',
                'sectors.name as sector',
                'suppliers.supplier_code as code',
                'suppliers.name',
                'suppliers.contact',
                'suppliers.phone',
                'suppliers.email',
                'suppliers.rfc',
                DB::raw("CONCAT(IFNULL(CONCAT(suppliers.address, ' '), ''), IFNULL(CONCAT(suppliers.district, ', '), ''), IFNULL(CONCAT(suppliers.city, ', '), ''), IFNULL(CONCAT(suppliers.state, ''), '')) as address")
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('sectors.name', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.supplier_code', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.name', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.contact', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.phone', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.email', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.rfc', 'like', '%' . $search . '%');
            });
        }

        if (!empty($sectorId)) {
            $query->where('suppliers.sector_id', $sectorId);
        }

        return $query->orderBy('suppliers.supplier_id', 'desc')->get();
    }

    public function find($id): ?Supplier
    {
        return $this->supplier->where('supplier_id', $id)->first();
    }

    public function create(array $data): Supplier
    {
        return DB::transaction(function () use ($data) {
            $count = $this->supplier->count();
            $next = $count + 1;

            $prefix = 'SP';
            if (!empty($data['sector_id'])) {
                $sector = $this->sector->find($data['sector_id']);
                if ($sector) {
                    $sectorName = strtolower(trim($sector->name));
                    $prefix = match ($sectorName) {
                        'pecuario'   => 'SPP',
                        'agro'       => 'SPA',
                        'food'       => 'SPCH',
                        'petfood'    => 'SPPF',
                        'envases'    => 'SPE',
                        'industrial' => 'SPI',
                        'otro'       => 'SPO',
                        default      => 'SP',
                    };
                }
            }

            while ($this->supplier->where('supplier_code', $prefix . $next)->exists()) {
                $next++;
            }

            $data['supplier_code'] = $prefix . $next;

            return $this->supplier->create($data);
        });
    }

    public function update($id, array $data): ?Supplier
    {
        return DB::transaction(function () use ($id, $data) {
            $record = $this->supplier->where('supplier_id', $id)->lockForUpdate()->first();
            if (!$record) {
                return null;
            }

            $record->update($data);
            return $record->fresh();
        });
    }

    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->supplier->where('supplier_id', $id)->lockForUpdate()->first();
            if (!$record) {
                return false;
            }

            return (bool) $record->delete();
        });
    }
}
