<?php

namespace App\Http\Repositories\Inventory;

use App\Models\Cli;
use App\Models\Concept;
use App\Models\Customer;
use App\Models\Input;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Operator;
use App\Models\Output;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\ProductInputs;
use App\Models\ProductOutputs;
use App\Models\ProductionMaterialRequest;
use App\Models\Quarantine;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Trailer;
use App\Models\TransportLine;
use App\Models\Vehicle;
use App\Models\Warehouse;
use App\Models\YeastProduction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class InventoryRepository
{
    protected Inventory $inventory;

    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * Obtiene todos los registros de inventario con su producto asociado.
     *
     * @return Collection<int, Inventory>
     */
    public function all(): Collection
    {
        return $this->inventory->with('product')->orderByDesc('inventory_id')->get();
    }

    /**
     * Busca un inventario por ID cargando relaciones.
     *
     * @param int|string $id
     * @return Inventory|null
     */
    public function find($id): ?Inventory
    {
        return $this->inventory->with(['product', 'clis'])->find($id);
    }

    /**
     * Registra un inventario de forma transaccional.
     *
     * @param array<string, mixed> $data
     * @return Inventory
     */
    public function create(array $data): Inventory
    {
        return DB::transaction(function () use ($data) {
            return $this->inventory->create($data);
        });
    }

    /**
     * Actualiza un inventario con bloqueo pesimista.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return Inventory|null
     */
    public function update($id, array $data): ?Inventory
    {
        return DB::transaction(function () use ($id, $data) {
            $inventory = $this->inventory->lockForUpdate()->find($id);
            if (!$inventory) {
                return null;
            }
            $inventory->update($data);
            return $inventory->fresh();
        });
    }

    /**
     * Elimina un inventario con bloqueo pesimista.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $inventory = $this->inventory->lockForUpdate()->find($id);
            if (!$inventory) {
                return false;
            }
            return (bool) $inventory->delete();
        });
    }

    /**
     * Obtiene todos los catálogos y conjuntos de datos requeridos para la vista index de inventario.
     *
     * @return array<string, mixed>
     */
    public function getIndexData(): array
    {
        $available_locations = DB::table('inventory')
            ->join('cli', 'inventory.inventory_id', '=', 'cli.inventory_id')
            ->join('locations', 'locations.location_id', '=', 'cli.location_id')
            ->join('concepts', 'concepts.concept_id', '=', 'cli.concept_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select('locations.name')
            ->get();

        $quarantine = Quarantine::all();
        $transport_lines = TransportLine::all();

        $products_all = DB::table('products')
            ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
            ->select('products.product_id', 'products.name', 'products.category_id', 'categories.name as category_name')
            ->get();

        $products = DB::table('inventory')
            ->leftJoin('products', 'products.product_id', '=', 'inventory.product_id')
            ->select('products.product_id', 'products.name')
            ->orderBy('products.product_id')
            ->distinct()
            ->get();

        $suppliers = DB::table('suppliers')->select('supplier_id', 'name')->get();
        $customers = DB::table('customers')->select('customer_id', 'name')->get();

        $products_warehouse = Inventory::select('inventory.product_id', 'products.name')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->distinct()
            ->get();

        $batchs = Inventory::select('inventory_id', 'batch', 'stock', 'inventory.product_id', 'products.name', 'products.unit', 'products.batch_code')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->get();

        $inputs_years = DB::table('inputs')->selectRaw('year(updated_at) as year')->orderByDesc('year')->distinct()->get();
        $inputs_months = DB::table('inputs')->selectRaw('year(updated_at) as year, month(updated_at) as month')->groupByRaw('year(updated_at), month(updated_at)')->orderByDesc('year')->orderByDesc('month')->get();
        $inputs_dates = DB::table('inputs')->selectRaw("input_id, comments, year(updated_at) as year, month(updated_at) as month,day(updated_at) as day, DATE_FORMAT(updated_at, '%W, %e %M %H:%i') as date")->groupByRaw('input_id, comments, year(updated_at), month(updated_at), day(updated_at), date')->orderByDesc('input_id')->orderByDesc('year')->orderByDesc('month')->orderByDesc('day')->orderByDesc('date')->get();
        $inputs_products = DB::table('product_inputs')->select('inputs.input_id', 'products.name', 'product_inputs.quantity', 'products.unit')->join('products', 'products.product_id', '=', 'product_inputs.product_id')->join('inputs', 'inputs.input_id', '=', 'product_inputs.input_id')->get();

        $outputs_years = DB::table('outputs')->selectRaw('year(updated_at) as year')->orderByDesc('year')->distinct()->get();
        $outputs_months = DB::table('outputs')->selectRaw('year(updated_at) as year, month(updated_at) as month')->groupByRaw('year(updated_at), month(updated_at)')->orderByDesc('year')->orderByDesc('month')->get();
        $outputs_dates = DB::table('outputs')->selectRaw("output_id, comments, year(updated_at) as year, month(updated_at) as month,day(updated_at) as day, DATE_FORMAT(updated_at, '%W, %e %M %H:%i') as date")->groupByRaw('output_id, comments, year(updated_at), month(updated_at), day(updated_at), date')->orderByDesc('output_id')->orderByDesc('year')->orderByDesc('month')->orderByDesc('day')->orderByDesc('date')->get();
        $outputs_products = DB::table('product_outputs')->select('outputs.output_id', 'products.name', 'product_outputs.quantity', 'products.unit')->join('products', 'products.product_id', '=', 'product_outputs.product_id')->join('outputs', 'outputs.output_id', '=', 'product_outputs.output_id')->get();

        $operators = Operator::select('operator_id', 'name', 'license')->get();
        $vehicles = Vehicle::select('plate', 'type')->get();
        $trailers = Trailer::select('plate', 'type')->get();
        $concepts = Concept::select('concept_id', 'name')->get();
        $warehouses = Warehouse::select('warehouse_id', 'name')->get();
        $locations = Location::select('location_id', 'name', 'warehouse_id')->get();

        $almacen_sales = Sale::with(['customer', 'prospect', 'user'])
            ->whereNotNull('almacen_status')
            ->orderBy('sale_id', 'desc')
            ->get();

        $product_locations = DB::table('cli')->select('inventory_id', 'location_id', 'bag_number', 'protein', 'weight_per_unit', 'quantity')->get();

        return compact(
            'warehouses', 'locations', 'available_locations', 'concepts', 'trailers', 'vehicles', 
            'operators', 'batchs', 'outputs_products', 'inputs_products', 'outputs_dates', 
            'inputs_dates', 'outputs_months', 'outputs_years', 'inputs_months', 'inputs_years', 
            'transport_lines', 'suppliers', 'customers', 'products', 'products_all', 
            'quarantine', 'products_warehouse', 'almacen_sales', 'product_locations'
        );
    }

    /**
     * Consulta el stock disponible, en cuarentena, productos altos y bajos.
     *
     * @param string|null $search
     * @return array<string, mixed>
     */
    public function getAvailableStock(?string $search = null): array
    {
        $query = DB::table('products')
            ->leftJoin('inventory', 'inventory.product_id', '=', 'products.product_id')
            ->select(
                'products.product_id',
                'products.name',
                DB::raw('COALESCE(SUM(inventory.stock), 0) as stock'),
                'products.unit'
            )
            ->groupBy('products.product_id', 'products.name', 'products.unit');

        if (!empty($search)) {
            $query->where('products.name', 'like', '%' . $search . '%');
        }

        $products = $query->get();

        $quarantine = DB::table('quarantine')
            ->join('inventory', 'inventory.inventory_id', '=', 'quarantine.inventory_id')
            ->join('products', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'quarantine.quarantine_id',
                'products.name',
                'inventory.batch',
                'quarantine.quantity',
                'products.unit',
                'quarantine.notes',
                'inventory.inventory_id'
            )
            ->get();

        $productsHigh = Product::leftJoin('inventory', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'products.product_id',
                'products.name',
                'products.unit',
                'products.stock_max',
                DB::raw('COALESCE(SUM(inventory.stock), 0) as stock')
            )
            ->where('products.category_id', '=', 17)
            ->groupBy('products.product_id', 'products.name', 'products.unit', 'products.stock_max')
            ->orderBy('stock', 'desc')
            ->get();

        $productsLow = Product::leftJoin('inventory', 'products.product_id', '=', 'inventory.product_id')
            ->select(
                'products.product_id',
                'products.name',
                'products.unit',
                'products.stock_min',
                DB::raw('COALESCE(SUM(inventory.stock), 0) as stock')
            )
            ->havingRaw('COALESCE(SUM(inventory.stock), 0) <= products.stock_min')
            ->groupBy('products.product_id', 'products.name', 'products.unit', 'products.stock_min')
            ->orderBy('stock')
            ->get();

        return [
            'products'      => $products,
            'productsLow'   => $productsLow,
            'productsHight' => $productsHigh,
            'quarantine'    => $quarantine,
        ];
    }

    /**
     * Mueve un producto a cuarentena y descuenta el stock con control transaccional y bloqueo pesimista.
     *
     * @param array<string, mixed> $data
     * @return Quarantine
     */
    public function addQuarantine(array $data): Quarantine
    {
        return DB::transaction(function () use ($data) {
            $inventory = $this->inventory->lockForUpdate()->findOrFail($data['inventory_id']);

            $quarantine = Quarantine::create([
                'inventory_id' => $data['inventory_id'],
                'quantity'     => $data['quantity'],
                'notes'        => $data['notes'],
            ]);

            $inventory->decrement('stock', $data['quantity']);

            return $quarantine;
        });
    }

    /**
     * Libera cantidad de cuarentena y repone el stock en inventario de forma segura.
     *
     * @param array<string, mixed> $data
     * @return bool
     */
    public function updateQuarantine(array $data): bool
    {
        return DB::transaction(function () use ($data) {
            $quarantine = Quarantine::lockForUpdate()->findOrFail($data['quarantine_id']);
            $inventory = $this->inventory->lockForUpdate()->findOrFail($quarantine->inventory_id);

            $qtyToRelease = (float) $data['quantity'];

            if (($quarantine->quantity - $qtyToRelease) <= 0) {
                $quarantine->delete();
            } else {
                $quarantine->decrement('quantity', $qtyToRelease);
            }

            $inventory->increment('stock', $qtyToRelease);

            return true;
        });
    }

    /**
     * Ejecuta una transacción compleja de inventario (Entrada, Salida, Salida Interna) con ACID y locks pesimistas.
     *
     * @param array<string, mixed> $data
     * @param object|null $user
     * @return array<string, mixed>
     * @throws ValidationException
     */
    public function makeTransaction(array $data, ?object $user = null): array
    {
        return DB::transaction(function () use ($data, $user) {
            // Regla de negocio para almacenista
            if ($data['type'] === 'Output' && $user && method_exists($user, 'hasRole') && $user->hasRole('Warehouse')) {
                $invalidProducts = DB::table('products')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
                    ->whereIn('products.product_id', $data['product_id'])
                    ->where(function ($query) {
                        $query->whereNull('categories.name')
                              ->orWhere('categories.name', 'not like', '%insumo%');
                    })
                    ->exists();

                if ($invalidProducts) {
                    throw ValidationException::withMessages([
                        'product_id' => 'El almacenista solo puede dar salida a insumos.'
                    ]);
                }
            }

            $movementData = [
                'operator'             => $data['operator'] ?? null,
                'license_number'       => $data['license_number'] ?? null,
                'security_seal'        => $data['security_seal'] ?? 0,
                'security_seal_number' => $data['security_seal_number'] ?? null,
                'unit_plates'          => $data['unit_plates'] ?? null,
                'trailer_plates'       => $data['trailer_plates'] ?? null,
                'comments'             => $data['comments'] ?? null,
                'transport_line_id'    => $data['transport_line'] ?? null,
            ];

            if ($data['type'] === 'Input') {
                $movementData['supplier_id'] = $data['supplier'] ?? null;
                $movement = Input::create($movementData);
                $fkField  = 'input_id';
                $fkValue  = $movement->input_id;
            } else {
                if ($data['type'] === 'InternalOutput') {
                    $internalCustomer = Customer::firstOrCreate(
                        ['name' => 'Consumo Interno Producción'],
                        ['customer_code' => 'INT-PROD']
                    );
                    $movementData['customer_id'] = $internalCustomer->customer_id;
                    $movementData['vendedor'] = 'Producción';
                } else {
                    $movementData['customer_id'] = $data['customer'] ?? null;
                    $movementData['vendedor'] = $data['vendedor'] ?? null;
                }
                $movement = Output::create($movementData);
                $fkField  = 'output_id';
                $fkValue  = $movement->output_id;
            }

            $details = [];
            foreach ($data['product_id'] as $index => $productId) {
                $qty = (float) $data['quantity'][$index];

                $row = [
                    'product_id' => $productId,
                    'quantity'   => $qty,
                    $fkField     => $fkValue,
                ];

                if ($data['type'] === 'Output' || $data['type'] === 'InternalOutput') {
                    $inventory = $this->inventory->lockForUpdate()
                        ->where('inventory_id', $data['warehouse_batch'][$index])
                        ->firstOrFail();

                    $row['warehouse_batch'] = $inventory->batch;
                    $row['label_batch']     = !empty($data['label_batch'][$index]) ? $data['label_batch'][$index] : ($inventory->batch ?? '');
                    $inventory->decrement('stock', $qty);

                    if (isset($data['bag_number'][$index]) && is_array($data['bag_number'][$index]) && count($data['bag_number'][$index]) > 0) {
                        foreach ($data['bag_number'][$index] as $bag_num) {
                            $query = Cli::where('inventory_id', $inventory->inventory_id)
                                ->where('bag_number', $bag_num);

                            if (isset($data['output_location_id'][$index])) {
                                $query->where('location_id', $data['output_location_id'][$index]);
                            } elseif (isset($data['location_id'][$index])) {
                                $query->where('location_id', $data['location_id'][$index]);
                            }

                            $cliRecord = $query->first();
                            $internalWeight = $cliRecord ? $cliRecord->weight_per_unit : null;

                            $query->delete();

                            if ($data['type'] === 'InternalOutput') {
                                YeastProduction::create([
                                    'output_id'       => $fkValue,
                                    'date'            => now(),
                                    'bag_number'      => $bag_num,
                                    'internal_weight' => $internalWeight,
                                ]);
                            }
                        }
                    } else {
                        $loc = $data['output_location_id'][$index] ?? ($data['location_id'][$index] ?? null);
                        if ($loc) {
                            $cli = Cli::where('inventory_id', $inventory->inventory_id)
                                ->where('location_id', $loc)
                                ->first();
                            if ($cli) {
                                if ($cli->quantity <= $qty) {
                                    $cli->delete();
                                } else {
                                    $cli->quantity -= $qty;
                                    $cli->net_weight = $cli->quantity * $cli->weight_per_unit;
                                    $cli->save();
                                }
                            }
                        }

                        if ($data['type'] === 'InternalOutput') {
                            YeastProduction::create([
                                'output_id'  => $fkValue,
                                'date'       => now(),
                                'bag_number' => null,
                            ]);
                        }
                    }
                } else {
                    $row['warehouse_batch'] = $data['warehouse_batch'][$index];
                    $inventory = $this->inventory->create([
                        'product_id' => $productId,
                        'stock'      => $qty,
                        'batch'      => $row['warehouse_batch'],
                    ]);

                    $locId = $data['location_id'][$index] ?? null;

                    $hasBagLocation = false;
                    if (isset($data['bag_location_id'][$index]) && is_array($data['bag_location_id'][$index])) {
                        foreach ($data['bag_location_id'][$index] as $bLoc) {
                            if (!empty($bLoc)) {
                                $hasBagLocation = true;
                                break;
                            }
                        }
                    }

                    if (($locId || $hasBagLocation) && !empty($data['concept_id'][$index])) {
                        $conceptId = $data['concept_id'][$index];
                        $weight = $data['weight_per_unit'][$index] ?? 0;

                        if (isset($data['bag_number'][$index]) && is_array($data['bag_number'][$index]) && count($data['bag_number'][$index]) > 0) {
                            foreach ($data['bag_number'][$index] as $b_idx => $bag_num) {
                                $bagLocId = $data['bag_location_id'][$index][$b_idx] ?? $locId;
                                $realWeight = $data['bag_weight'][$index][$b_idx] ?? $weight;

                                if ($bagLocId) {
                                    Cli::create([
                                        'location_id'     => $bagLocId,
                                        'inventory_id'    => $inventory->inventory_id,
                                        'concept_id'      => $conceptId,
                                        'quantity'        => 1,
                                        'weight_per_unit' => $realWeight,
                                        'net_weight'      => 1 * $realWeight,
                                        'bag_number'      => $bag_num,
                                        'protein'         => $data['protein'][$index][$b_idx] ?? null,
                                    ]);
                                }
                            }
                        } else {
                            if ($locId) {
                                Cli::create([
                                    'location_id'     => $locId,
                                    'inventory_id'    => $inventory->inventory_id,
                                    'concept_id'      => $conceptId,
                                    'quantity'        => $qty,
                                    'weight_per_unit' => $weight,
                                    'net_weight'      => $qty * $weight,
                                ]);
                            }
                        }
                    }
                }
                $details[] = $row;
            }

            if ($data['type'] === 'Input') {
                ProductInputs::insert($details);
            } else {
                ProductOutputs::insert($details);
            }

            if (!empty($data['req_id']) && $data['type'] === 'InternalOutput') {
                $matReq = ProductionMaterialRequest::find($data['req_id']);
                if ($matReq) {
                    $matReq->status = 'Surtido';
                    $matReq->save();
                }
            }

            return [
                'movement_id' => $fkValue,
                'type'        => $data['type'],
                'items_count' => count($details),
            ];
        });
    }

    /**
     * Actualiza la fecha de un registro de entrada o salida.
     *
     * @param string $type
     * @param int $id
     * @param string $newDate
     * @return bool
     */
    public function updateDate(string $type, int $id, string $newDate): bool
    {
        return DB::transaction(function () use ($type, $id, $newDate) {
            $parsedDate = Carbon::parse($newDate)->setTimeFrom(Carbon::now());
            $typeLower = strtolower($type);

            if ($typeLower === 'input') {
                $record = Input::lockForUpdate()->findOrFail($id);
                $record->updated_at = $parsedDate;
                return $record->save();
            }

            $record = Output::lockForUpdate()->findOrFail($id);
            $record->updated_at = $parsedDate;
            return $record->save();
        });
    }

    /**
     * Actualiza los comentarios de una entrada o salida.
     *
     * @param string $type
     * @param int $id
     * @param string|null $comments
     * @return bool
     */
    public function updateComment(string $type, int $id, ?string $comments): bool
    {
        return DB::transaction(function () use ($type, $id, $comments) {
            $typeLower = strtolower($type);

            if ($typeLower === 'input') {
                $record = Input::lockForUpdate()->findOrFail($id);
                $record->comments = $comments;
                return $record->save();
            }

            $record = Output::lockForUpdate()->findOrFail($id);
            $record->comments = $comments;
            return $record->save();
        });
    }

    /**
     * Carga el conjunto de datos de tarimas pendientes y catálogos requeridos.
     *
     * @return array<string, mixed>
     */
    public function getPendingPalletsData(): array
    {
        $pallets = Pallet::with('yeastProductions')
            ->where('inventory_status', 'Enviada')
            ->orderBy('pallet_id', 'desc')
            ->get();
            
        $transfers = \App\Models\ProductionWarehouseTransfer::with('product')
            ->where('status', 'Pendiente')
            ->orderBy('transfer_id', 'desc')
            ->get();

        foreach ($pallets as $pallet) {
            $calculatedWeight = 0;
            foreach ($pallet->yeastProductions as $yp) {
                if ($yp->bags_quantity > 0 && $yp->finished_product_kg > 0) {
                    $calculatedWeight += ($yp->finished_product_kg / $yp->bags_quantity) * ($yp->pivot->sacks_contributed ?? 0);
                }
            }
            $pallet->total_weight = $calculatedWeight > 0 ? round($calculatedWeight, 2) : round(($pallet->current_sacks ?? 0) * 25, 2);
        }

        $warehouses = Warehouse::all();
        $locations = Location::with('warehouse')->get();
        $suppliers = Supplier::select('supplier_id', 'name', 'supplier_code')->get();
        $concepts = Concept::select('concept_id', 'name')->get();
        $transport_lines = TransportLine::select('transport_line_id', 'name')->get();
        $products = Product::select('product_id', 'name', 'unit', 'batch_code', 'sku')->get();

        $internalSupplier = Supplier::where('name', 'Producción Interna')
            ->orWhere('supplier_code', 'PROD-INT')
            ->first();

        if (!$internalSupplier) {
            $internalSupplier = Supplier::create([
                'name'          => 'Producción Interna',
                'supplier_code' => 'PROD-INT',
                'contact'       => 'Planta Producción',
                'phone'         => 'N/A',
                'email'         => 'produccion@yeacali.com',
                'rfc'           => 'XAXX010101000',
                'address'       => 'Planta',
                'city'          => 'Local',
                'state'         => 'Local',
                'sector_id'     => 1,
            ]);
            $suppliers = Supplier::select('supplier_id', 'name', 'supplier_code')->get();
        }

        $internalConcept = Concept::where('name', 'Producto Terminado')->first()
            ?? Concept::where('name', 'like', '%Terminado%')->first()
            ?? Concept::firstOrCreate(['name' => 'Producto Terminado']);

        if (!$concepts->contains('concept_id', $internalConcept->concept_id)) {
            $concepts = Concept::select('concept_id', 'name')->get();
        }

        $defaultProduct = Product::where('product_id', 623)
            ->orWhere('name', 'like', '%Yeacali%')
            ->first();

        $transfers = \App\Models\ProductionWarehouseTransfer::with('product')
            ->where('status', 'Pendiente')
            ->orderBy('transfer_id', 'desc')
            ->get();

        return compact(
            'pallets', 'transfers', 'warehouses', 'locations', 'suppliers', 'concepts',
            'transport_lines', 'products', 'internalSupplier', 'internalConcept', 'defaultProduct'
        );
    }

    /**
     * Acepta e ingresa una tarima al almacén con registro completo de entrada, inventario y ubicación CLI.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function acceptPallet($id, array $data): array
    {
        return DB::transaction(function () use ($id, $data) {
            $pallet = Pallet::with('yeastProductions')->lockForUpdate()->findOrFail($id);

            if ($pallet->inventory_status !== 'Enviada') {
                throw new InvalidArgumentException('La tarima no está pendiente de ser recibida.');
            }

            // Resolución de proveedor
            $supplierId = $data['supplier_id'] ?? null;
            if (!$supplierId) {
                $supplier = Supplier::where('name', 'Producción Interna')
                    ->orWhere('supplier_code', 'PROD-INT')
                    ->first();
                if (!$supplier) {
                    $supplier = Supplier::create([
                        'name'          => 'Producción Interna',
                        'supplier_code' => 'PROD-INT',
                        'contact'       => 'Planta Producción',
                        'sector_id'     => 1,
                    ]);
                }
                $supplierId = $supplier->supplier_id;
            }

            // Resolución de concepto
            $conceptId = $data['concept_id'] ?? null;
            if (!$conceptId) {
                $concept = Concept::where('name', 'Producto Terminado')->first()
                    ?? Concept::where('name', 'like', '%Terminado%')->first();
                $conceptId = $concept ? $concept->concept_id : 2;
            }

            // Resolución de producto
            $defaultProd = Product::where('product_id', 623)->orWhere('name', 'like', '%Yeacali%')->first();
            $productId = !empty($data['product_id']) ? $data['product_id'] : ($defaultProd->product_id ?? 623);

            // Cantidades y pesos
            $sacks = (float) (!empty($data['quantity']) ? $data['quantity'] : $pallet->current_sacks);
            $weightPerUnit = (float) (!empty($data['weight_per_unit']) ? $data['weight_per_unit'] : 25);
            $finalWeight = (float) (!empty($data['final_weight']) ? $data['final_weight'] : ($sacks * $weightPerUnit));
            $batch = !empty($data['warehouse_batch']) ? $data['warehouse_batch'] : $pallet->pallet_number;

            // 1. Registro Input
            $input = Input::create([
                'supplier_id'          => $supplierId,
                'transport_line_id'    => $data['transport_line'] ?? null,
                'operator'             => $data['operator'] ?? null,
                'license_number'       => $data['license_number'] ?? null,
                'security_seal'        => $data['security_seal'] ?? 0,
                'security_seal_number' => $data['security_seal_number'] ?? null,
                'unit_plates'          => $data['unit_plates'] ?? null,
                'trailer_plates'       => $data['trailer_plates'] ?? null,
                'comments'             => $data['comments'] ?? null,
            ]);

            // 2. Registro Inventory
            $inventory = $this->inventory->create([
                'stock'      => $finalWeight,
                'batch'      => $batch,
                'product_id' => $productId,
            ]);

            // 3. Registro ProductInputs
            ProductInputs::insert([
                'product_id'      => $productId,
                'input_id'        => $input->input_id,
                'quantity'        => $finalWeight,
                'warehouse_batch' => $batch,
            ]);

            // 4. Registro CLI
            Cli::create([
                'inventory_id'    => $inventory->inventory_id,
                'concept_id'      => $conceptId,
                'location_id'     => $data['location_id'],
                'quantity'        => $sacks,
                'weight_per_unit' => $weightPerUnit,
                'net_weight'      => $finalWeight,
            ]);

            // 5. Actualizar status de tarima
            $pallet->inventory_status = 'Ingresada';
            $pallet->save();

            return [
                'final_weight' => $finalWeight,
                'pallet_id'    => $pallet->pallet_id,
                'inventory_id' => $inventory->inventory_id,
            ];
        });
    }

    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }
}
