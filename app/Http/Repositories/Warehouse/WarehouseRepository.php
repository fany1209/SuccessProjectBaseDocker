<?php

namespace App\Http\Repositories\Warehouse;

use App\Models\Concept;
use App\Models\Control;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\ProductionMaterialRequest;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WarehouseRepository
{
    protected Warehouse $warehouse;

    public function __construct(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     * Obtiene el listado completo de almacenes.
     */
    public function all(): Collection
    {
        return $this->warehouse->all();
    }

    /**
     * Busca un almacén por su ID.
     */
    public function find(int $id): ?Warehouse
    {
        return $this->warehouse->find($id);
    }

    /**
     * Obtiene todos los conceptos.
     */
    public function getAllConcepts(): Collection
    {
        return Concept::all();
    }

    /**
     * Obtiene todas las ubicaciones.
     */
    public function getAllLocations(): Collection
    {
        return Location::all();
    }

    /**
     * Obtiene el catálogo de productos con inventario activo de manera única.
     */
    public function getProductsInventory(): Collection
    {
        return Inventory::select('products.product_id', 'products.name', 'products.unit')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->distinct()
            ->get();
    }

    /**
     * Resumen de stock agrupado por producto y almacén en ubicaciones CLI.
     */
    public function getInventorySummary(): Collection
    {
        return DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select('products.name', 'products.unit', DB::raw('SUM(cli.net_weight) as net_weight'), 'locations.warehouse_id')
            ->groupBy('products.name', 'locations.warehouse_id', 'products.unit')
            ->get();
    }

    /**
     * Ubicaciones que actualmente cuentan con existencias.
     */
    public function getAvailableLocations(): Collection
    {
        return DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select('locations.name')
            ->get();
    }

    /**
     * Detalle de lotes de inventario calculando el remanente en almacén (stock_wh).
     */
    public function getInventoryStockWH(): Collection
    {
        return DB::table('inventory')
            ->leftJoin('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->leftJoin('products', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'inventory.inventory_id',
                'products.product_id',
                'inventory.batch',
                'products.name',
                'inventory.stock',
                DB::raw('CAST(inventory.stock - COALESCE(SUM(cli.net_weight), 0) AS DECIMAL(10,3)) as stock_wh')
            )
            ->groupBy(
                'inventory.inventory_id',
                'products.product_id',
                'inventory.batch',
                'products.name',
                'inventory.stock'
            )
            ->get();
    }

    /**
     * Evalúa el estatus de registro para las horas de control de temperatura del día actual.
     *
     * @param int|null $warehouseId
     * @param array<int, string> $hours
     * @param string $timezone
     * @return array<int, bool>
     */
    public function getFeedback(?int $warehouseId, array $hours, string $timezone): array
    {
        $feedback = array_fill(0, count($hours), false);

        if ($warehouseId) {
            $controls = Control::where('warehouse_id', $warehouseId)
                ->whereDate('created_at', Carbon::today($timezone))
                ->get();

            foreach ($hours as $index => $hour) {
                $hourInt = (int) explode(':', $hour)[0];

                $feedback[$index] = $controls->contains(function ($c) use ($hourInt, $timezone) {
                    $controlDate = Carbon::parse($c->created_at)->timezone($timezone);
                    return (int) $controlDate->format('H') === $hourInt;
                });
            }
        }

        return $feedback;
    }

    /**
     * Almacena mediciones de temperatura y humedad dentro de una transacción ACID.
     *
     * @param int $warehouseId
     * @param array<int, array<string, mixed>> $measurements
     * @param array<string, mixed> $meta
     * @return Collection
     */
    public function storeTemperatureMeasurements(int $warehouseId, array $measurements, array $meta): Collection
    {
        return DB::transaction(function () use ($warehouseId, $measurements, $meta) {
            $created = collect();

            foreach ($measurements as $measurement) {
                $control = Control::create([
                    'temperature'  => $measurement['temperature'],
                    'humidity'     => $measurement['humidity'],
                    'warehouse_id' => $warehouseId,
                    'user_id'      => $meta['user_id'] ?? null,
                    'host_ip'      => $meta['host_ip'] ?? null,
                    'host_user'    => $meta['host_user'] ?? null,
                    'host_name'    => $meta['host_name'] ?? null,
                ]);

                $created->push($control);
            }

            return $created;
        });
    }

    /**
     * Consulta el detalle de productos y peso ubicados en una locación específica.
     */
    public function getInfoLocation(string $location): Collection
    {
        return DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'products.name as pName',
                'concepts.name as cName',
                'bag_number',
                'quantity',
                'weight_per_unit as wpu',
                'products.unit',
                'net_weight as total',
                'batch'
            )
            ->where('locations.name', $location)
            ->get();
    }

    /**
     * Consulta dinámica de existencias con filtros de búsqueda y concepto.
     */
    public function getWarehouseStock(?string $concept = null, ?string $search = null): Collection
    {
        $query = DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'cli_id',
                'locations.name as lName',
                'products.name as pName',
                'concepts.name as cName',
                'bag_number',
                'quantity',
                DB::raw('concat(format(weight_per_unit,2)," ",products.unit) as weight_per_unit'),
                DB::raw('concat(format(net_weight,2)," ",products.unit) as net_weight'),
                'batch',
                'products.unit'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', '%' . $search . '%')
                    ->orWhere('locations.name', 'like', '%' . $search . '%')
                    ->orWhere('concepts.name', 'like', '%' . $search . '%')
                    ->orWhere('inventory.batch', 'like', '%' . $search . '%');
            });
        }

        if (!empty($concept)) {
            $query->where('concepts.concept_id', $concept);
        }

        return $query->get();
    }

    /**
     * Obtiene el listado de solicitudes de material para producción con sus partidas.
     */
    public function getProductionRequests(): Collection
    {
        return ProductionMaterialRequest::with('items')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Marca una solicitud de material como 'Surtido' aplicando bloqueo pesimista contra TOCTOU.
     */
    public function attendProductionRequest(int $id): ProductionMaterialRequest
    {
        return DB::transaction(function () use ($id) {
            $matReq = ProductionMaterialRequest::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $matReq->status = 'Surtido';
            $matReq->save();

            return $matReq;
        });
    }

    /**
     * Obtiene las partidas de una solicitud de material vinculando el product_id si existe coincidencia.
     */
    public function getRequestItems(int $id): Collection
    {
        $request = ProductionMaterialRequest::with('items')->findOrFail($id);

        return $request->items->map(function ($item) {
            $product = DB::table('products')->where('name', $item->product_name)->first();
            return [
                'product_id'   => $product ? $product->product_id : null,
                'product_name' => $item->product_name,
                'quantity'     => $item->quantity,
            ];
        });
    }
}
