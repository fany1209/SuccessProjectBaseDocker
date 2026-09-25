<?php

namespace App\Http\Repositories\Customer;

use App\Models\Customer;
use App\Models\Sector;
use App\Models\User;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerRepository
{
    protected Customer $customer;
    protected Sector $sector;

    public function __construct(Customer $customer, Sector $sector)
    {
        $this->customer = $customer;
        $this->sector = $sector;
    }

    public function find(int $id): ?Customer
    {
        return $this->customer->with('sector')->where('customer_id', $id)->first();
    }

    public function getIndexData(): array
    {
        $sectors = $this->sector->all();

        $totalCustomers = DB::table('customers')
            ->join('sectors', 'sectors.sector_id', '=', 'customers.sector_id')
            ->count();

        $customersPerSector = DB::table('sectors')
            ->leftJoin('customers', 'customers.sector_id', '=', 'sectors.sector_id')
            ->select('sectors.sector_id', 'sectors.code')
            ->selectRaw("
                COALESCE(
                    MAX(
                        CAST(
                            SUBSTRING(customers.customer_code, 3 + CHAR_LENGTH(sectors.code)) AS UNSIGNED
                        )
                    ),
                0) AS last_number
            ")
            ->groupBy('sectors.sector_id', 'sectors.code')
            ->get();

        $sellers = User::select('users.id as seller_number', 'users.name')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->whereIn('roles.name', ['Sales', 'Admin'])
            ->get();

        $clientesSistemas = DB::table('customers')
            ->select('customer_id', 'name', 'customer_code')
            ->orderBy('name', 'asc')
            ->get();

        return [
            'sectors' => $sectors,
            'total_customers' => $totalCustomers,
            'customers_per_sector' => $customersPerSector,
            'sellers' => $sellers,
            'clientesSistemas' => $clientesSistemas,
        ];
    }

    public function getFilteredCustomers(array $filters): Collection
    {
        $query = DB::table('customers')
            ->join('sectors', 'sectors.sector_id', '=', 'customers.sector_id')
            ->select(
                'customers.customer_id',
                'sectors.name as sector',
                'customers.customer_code as code',
                'customers.name',
                'customers.phone',
                'customers.email',
                'customers.rfc',
                'customers.vendedor',
                'customers.contact',
                'customers.delivery_address',
                DB::raw("concat(coalesce(customers.address, ''), ', ', coalesce(customers.district, ''), ', ', coalesce(customers.city, ''), ', ', coalesce(customers.state, ''), ', ', coalesce(customers.country, ''), ' ', coalesce(customers.postal_code, '')) as address")
            );

        $search = $filters['search'] ?? null;
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('sectors.name', 'like', '%' . $search . '%')
                    ->orWhere('customers.customer_code', 'like', '%' . $search . '%')
                    ->orWhere('customers.name', 'like', '%' . $search . '%')
                    ->orWhere('customers.phone', 'like', '%' . $search . '%')
                    ->orWhere('customers.email', 'like', '%' . $search . '%')
                    ->orWhere('customers.rfc', 'like', '%' . $search . '%')
                    ->orWhere('customers.vendedor', 'like', '%' . $search . '%')
                    ->orWhere('customers.contact', 'like', '%' . $search . '%')
                    ->orWhere('customers.delivery_address', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['sector'])) {
            $query->where('customers.sector_id', $filters['sector']);
        }
        if (!empty($filters['city'])) {
            $query->where('customers.city', 'like', '%' . $filters['city'] . '%');
        }
        if (!empty($filters['state'])) {
            $query->where('customers.state', 'like', '%' . $filters['state'] . '%');
        }
        if (!empty($filters['vendedor'])) {
            $query->where('customers.vendedor', 'like', '%' . $filters['vendedor'] . '%');
        }
        if (!empty($filters['name'])) {
            $query->where('customers.name', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['rfc'])) {
            $query->where('customers.rfc', 'like', '%' . $filters['rfc'] . '%');
        }
        if (!empty($filters['contact'])) {
            $query->where('customers.contact', 'like', '%' . $filters['contact'] . '%');
        }

        $query->orderBy('customers.customer_id', 'desc');

        $customers = $query->get();

        $canUpdate = auth()->check() ? auth()->user()->can('customers.update') : false;
        $canDelete = auth()->check() ? auth()->user()->can('customers.delete') : false;

        return $customers->map(function ($row) use ($canUpdate, $canDelete) {
            return [
                'customer_id' => $row->customer_id,
                'sector' => $row->sector,
                'code' => $row->code,
                'name' => $row->name,
                'phone' => $row->phone,
                'email' => $row->email,
                'rfc' => $row->rfc,
                'vendedor' => $row->vendedor,
                'contact' => $row->contact,
                'delivery_address' => $row->delivery_address,
                'address' => $row->address,
                'canUpdate' => $canUpdate,
                'canDelete' => $canDelete,
            ];
        });
    }

    public function create(array $data, ?string $userName = null): Customer
    {
        return DB::transaction(function () use ($data, $userName) {
            $sector = $this->sector->where('sector_id', $data['sector_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (empty($sector->code)) {
                throw new DomainException('El sector seleccionado no tiene código (code) asignado.');
            }

            $prefix = 'SC' . $sector->code;
            $prefixLen = strlen($prefix);

            $last = $this->customer->where('sector_id', $sector->sector_id)
                ->where('customer_code', 'like', $prefix . '%')
                ->selectRaw("MAX(CAST(SUBSTRING(customer_code, " . ($prefixLen + 1) . ") AS UNSIGNED)) AS max_num")
                ->value('max_num');

            $next = ((int) $last) + 1;
            $data['customer_code'] = $prefix . $next;
            $data['vendedor'] = $data['vendedor'] ?? $userName ?? 'Sistema';

            return $this->customer->create($data);
        });
    }

    public function update(int $id, array $data): Customer
    {
        return DB::transaction(function () use ($id, $data) {
            $customer = $this->customer->where('customer_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if (isset($data['sector_id']) && $customer->sector_id != $data['sector_id']) {
                $sector = $this->sector->where('sector_id', $data['sector_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (empty($sector->code)) {
                    throw new DomainException('El nuevo sector no tiene un código (code) asignado.');
                }

                $prefix = 'SC' . $sector->code;
                $prefixLen = strlen($prefix);

                $last = $this->customer->where('sector_id', $sector->sector_id)
                    ->where('customer_code', 'like', $prefix . '%')
                    ->selectRaw("MAX(CAST(SUBSTRING(customer_code, " . ($prefixLen + 1) . ") AS UNSIGNED)) AS max_num")
                    ->value('max_num');

                $next = ((int) $last) + 1;
                $data['customer_code'] = $prefix . $next;
            }

            $customer->update($data);

            return $customer;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $customer = $this->customer->where('customer_id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->hasSales($id)) {
                throw new DomainException('No se puede eliminar el cliente porque tiene ventas registradas.');
            }

            if ($this->hasOutputs($id)) {
                throw new DomainException('No se puede eliminar el cliente porque tiene salidas de almacén registradas.');
            }

            return (bool) $customer->delete();
        });
    }

    public function hasSales(int $id): bool
    {
        return DB::table('sales')->where('customer_id', $id)->exists();
    }

    public function hasOutputs(int $id): bool
    {
        return DB::table('outputs')->where('customer_id', $id)->exists();
    }
}
