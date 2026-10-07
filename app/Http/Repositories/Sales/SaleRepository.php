<?php

namespace App\Http\Repositories\Sales;

use App\Models\Cli;
use App\Models\Customer;
use App\Models\CxcDetail;
use App\Models\Inventory;
use App\Models\Output;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Sector;
use App\Models\User;
use App\Notifications\SaleAlmacenNotification;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SaleRepository
{
    protected Sale $sale;
    protected SaleDetail $saleDetail;
    protected Sector $sector;
    protected Customer $customer;
    protected Prospect $prospect;
    protected Product $product;
    protected User $user;
    protected Inventory $inventory;
    protected Cli $cli;
    protected Output $output;

    public function __construct(
        Sale $sale,
        SaleDetail $saleDetail,
        Sector $sector,
        Customer $customer,
        Prospect $prospect,
        Product $product,
        User $user,
        Inventory $inventory,
        Cli $cli,
        Output $output
    ) {
        $this->sale = $sale;
        $this->saleDetail = $saleDetail;
        $this->sector = $sector;
        $this->customer = $customer;
        $this->prospect = $prospect;
        $this->product = $product;
        $this->user = $user;
        $this->inventory = $inventory;
        $this->cli = $cli;
        $this->output = $output;
    }

    public function getIndexData(): array
    {
        $totalSales = $this->sale->max('folio') ?? 0;
        $sectors = $this->sector->all();
        $products = $this->product->select('product_id', 'name')->orderBy('name', 'asc')->get();
        $prospects = $this->prospect->select('prospect_id', 'name', 'sector_id')->orderBy('name', 'asc')->get();
        $customers = $this->customer->select('customer_id', 'name', 'sector_id')->orderBy('name', 'asc')->get();
        $statuses = DB::table('sales_status')->get();

        $sellers = $this->user->select('users.id as seller_number', 'users.name')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->whereIn('roles.name', ['Sales', 'Admin'])
            ->distinct()
            ->get();

        return [
            'total_sales' => $totalSales,
            'sectors'     => $sectors,
            'products'    => $products,
            'prospects'   => $prospects,
            'customers'   => $customers,
            'statuses'    => $statuses,
            'sellers'     => $sellers,
        ];
    }

    public function getSales(array $filters, $user)
    {
        $sector = $filters['sector'] ?? null;
        $search = $filters['search'] ?? null;
        $clients = $filters['clients'] ?? null;
        $seller = $filters['seller'] ?? null;
        $saleType = $filters['sale_type'] ?? null;
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        $query = DB::table('sales')
            ->leftJoin('sectors', 'sectors.sector_id', '=', 'sales.sector_id')
            ->leftJoin('sale_detail', 'sale_detail.sale_id', '=', 'sales.sale_id')
            ->select(
                'sales.sale_id',
                'sales.folio',
                'sales.date',
                'sales.seller',
                'sales.sales_status_id',
                'sectors.name as sName',
                'sales.sale_type',
                'sales.purchase_order',
                'sales.invoice',
                DB::raw('count(sale_detail.sale_detail_id) as products')
            )
            ->groupBy(
                'sales.sale_id',
                'sales.folio',
                'sales.date',
                'sales.seller',
                'sales.sales_status_id',
                'sectors.name',
                'sales.sale_type',
                'sales.purchase_order',
                'sales.invoice'
            );

        if ($clients == 1) {
            $query->leftJoin('customers', 'customers.customer_id', '=', 'sales.customer_id')
                ->addSelect('customers.name as cName')
                ->where('sales.is_customer', 1)
                ->groupBy('customers.name');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('sectors.name', 'like', '%' . $search . '%')
                        ->orWhere('customers.name', 'like', '%' . $search . '%')
                        ->orWhere('sales.seller', 'like', '%' . $search . '%')
                        ->orWhere('sales.folio', 'like', '%' . $search . '%');
                });
            }
        } else {
            $query->leftJoin('prospects', 'prospects.prospect_id', '=', 'sales.prospect_id')
                ->addSelect('prospects.name as cName')
                ->where('sales.is_customer', 0)
                ->groupBy('prospects.name');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('sectors.name', 'like', '%' . $search . '%')
                        ->orWhere('prospects.name', 'like', '%' . $search . '%')
                        ->orWhere('sales.seller', 'like', '%' . $search . '%')
                        ->orWhere('sales.folio', 'like', '%' . $search . '%');
                });
            }
        }

        if (!empty($sector)) {
            $query->where('sales.sector_id', $sector);
        }

        if (!empty($seller)) {
            $query->where('sales.seller', 'like', '%' . $seller . '%');
        }

        if (!empty($saleType)) {
            $query->where('sales.sale_type', 'like', '%' . $saleType . '%');
        }

        if (!empty($dateFrom)) {
            $query->whereDate('sales.date', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $query->whereDate('sales.date', '<=', $dateTo);
        }

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('Admin')) {
            $query->where('sales.user_id', $user->id);
        }

        return $query->get();
    }

    public function deleteDetail(int $detailId): bool
    {
        return DB::transaction(function () use ($detailId) {
            $detail = $this->saleDetail->where('sale_detail_id', $detailId)->lockForUpdate()->first();
            if (!$detail) {
                return false;
            }

            return (bool) $detail->delete();
        });
    }

    public function generateCustomerCode(int $sectorId): string
    {
        $sector = $this->sector->where('sector_id', $sectorId)->lockForUpdate()->firstOrFail();

        $prefix = 'SC' . $sector->code;
        $prefixLen = strlen($prefix);

        $last = $this->customer->where('sector_id', $sector->sector_id)
            ->where('customer_code', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(SUBSTRING(customer_code, " . ($prefixLen + 1) . ") AS UNSIGNED)) AS max_num")
            ->lockForUpdate()
            ->value('max_num');

        $next = ((int) $last) + 1;

        return $prefix . $next;
    }

    public function store(array $data, ?User $currentUser = null): Sale
    {
        return DB::transaction(function () use ($data, $currentUser) {
            $paymentStatus = (strtolower($data['sale_type']) === 'cash') ? 'PAID' : 'PENDING';

            $saleData = [
                'seller'          => $data['seller'],
                'first_time'      => (int) $data['first_time'],
                'is_customer'     => (int) $data['is_customer'],
                'purchase_order'  => $data['purchase_order'] ?? null,
                'invoice'         => $data['invoice'] ?? null,
                'sale_type'       => $data['sale_type'],
                'term'            => $data['term'] ?? null,
                'date'            => $data['date'],
                'customer_id'     => $data['customer_id'] ?? null,
                'prospect_id'     => $data['prospect_id'] ?? null,
                'user_id'         => $data['user_id'] ?? ($currentUser ? $currentUser->id : 1),
                'sales_status_id' => $data['sales_status_id'],
                'sector_id'       => $data['sector_id'],
                'payment_status'  => $paymentStatus,
            ];

            if ($saleData['first_time'] === 0 && $saleData['is_customer'] === 0) {
                if (empty($data['name'])) {
                    throw new DomainException('El nombre del cliente es obligatorio para registros manuales.');
                }

                $customerCode = $this->generateCustomerCode((int) $saleData['sector_id']);

                $customer = $this->customer->create([
                    'sector_id'     => $saleData['sector_id'],
                    'name'          => $data['name'],
                    'phone'         => $data['phone'] ?? null,
                    'email'         => $data['email'] ?? null,
                    'rfc'           => $data['rfc'] ?? null,
                    'state'         => $data['state'] ?? null,
                    'city'          => $data['city'] ?? null,
                    'district'      => $data['district'] ?? null,
                    'address'       => $data['address'] ?? null,
                    'customer_code' => $customerCode,
                ]);

                $saleData['customer_id'] = $customer->customer_id;
                $saleData['prospect_id'] = null;
                $saleData['is_customer'] = 1;
            } elseif ($saleData['is_customer'] === 0 && !empty($saleData['prospect_id'])) {
                $prospect = $this->prospect->where('prospect_id', $saleData['prospect_id'])->lockForUpdate()->first();
                if ($prospect) {
                    $customerCode = $this->generateCustomerCode((int) $saleData['sector_id']);

                    $customer = $this->customer->create([
                        'name'          => $prospect->name,
                        'phone'         => $prospect->phone,
                        'email'         => $prospect->email,
                        'rfc'           => $prospect->rfc,
                        'district'      => $prospect->district,
                        'city'          => $prospect->city,
                        'state'         => $prospect->state,
                        'address'       => $prospect->address,
                        'sector_id'     => $saleData['sector_id'],
                        'customer_code' => $customerCode,
                    ]);

                    $saleData['customer_id'] = $customer->customer_id;
                    $saleData['prospect_id'] = null;
                    $saleData['is_customer'] = 1;

                    $this->sale->where('prospect_id', $prospect->prospect_id)->update([
                        'customer_id' => $customer->customer_id,
                        'prospect_id' => null,
                        'is_customer' => 1,
                    ]);

                    $prospect->delete();
                }
            } else {
                if ($saleData['is_customer'] === 1) {
                    $saleData['prospect_id'] = null;
                } else {
                    $saleData['customer_id'] = null;
                }
            }

            $ultimoFolio = $this->sale->lockForUpdate()->max('folio') ?? 0;
            $saleData['folio'] = $ultimoFolio + 1;

            $sale = $this->sale->create($saleData);

            $productsId = $data['product_id'] ?? [];
            $publicProductNames = $data['public_product_name'] ?? [];
            $publicBatchs = $data['public_batch'] ?? [];
            $quantities = $data['quantity'] ?? [];
            $invoiceValues = $data['invoice_val'] ?? [];
            $costs = $data['cost'] ?? [];
            $hasTaxs = $data['has_tax'] ?? [];

            foreach ($productsId as $i => $productId) {
                $invoiceVal = (int) ($invoiceValues[$i] ?? 0);
                $cost = (float) ($costs[$i] ?? 0);

                if ($invoiceVal === 1) {
                    $cost = 0;
                }

                $this->saleDetail->create([
                    'sale_id'             => $sale->sale_id,
                    'product_id'          => $productId,
                    'public_product_name' => $publicProductNames[$i] ?? '',
                    'quantity'            => $quantities[$i] ?? 0,
                    'cost'                => $cost,
                    'invoice_val'         => $invoiceVal,
                    'public_batch'        => $publicBatchs[$i] ?? null,
                    'has_tax'             => (int) ($hasTaxs[$i] ?? 0),
                ]);
            }

            try {
                $almacenUsers = $this->user->role(['Warehouse', 'Admin'])->get();
                if ($almacenUsers->isNotEmpty()) {
                    Notification::send($almacenUsers, new SaleAlmacenNotification(
                        $sale,
                        'new_sale',
                        'Nueva venta registrada: Folio ' . $sale->folio . ' esperando confirmación.'
                    ));
                }

                if ($sale->user) {
                    $sale->user->notify(new SaleAlmacenNotification(
                        $sale,
                        'pending',
                        'Tu venta Folio ' . $sale->folio . ' ha sido enviada a almacén para confirmación.'
                    ));
                }
            } catch (\Throwable $ignored) {
            }

            return $sale->load(['customer', 'prospect', 'details.product', 'sector', 'status', 'user']);
        });
    }

    public function update(int $saleId, array $data, ?User $currentUser = null): bool
    {
        return DB::transaction(function () use ($saleId, $data, $currentUser) {
            $sale = $this->sale->where('sale_id', $saleId)->lockForUpdate()->first();
            if (!$sale) {
                return false;
            }

            // Snapshot old relations and values for diff detection
            $sale->loadMissing(['customer', 'prospect']);
            $oldDetails = $this->saleDetail->with('product')->where('sale_id', $saleId)->get()->keyBy('sale_detail_id');

            $oldClientName = (int) $sale->is_customer === 1
                ? ($sale->customer?->name ?? ($sale->customer_id ? 'Cliente #' . $sale->customer_id : 'N/A'))
                : ($sale->prospect?->name ?? ($sale->prospect_id ? 'Prospecto #' . $sale->prospect_id : 'N/A'));
            $oldSeller = $sale->seller ? trim($sale->seller) : '';
            $oldDate = $sale->date;
            $oldPO = $sale->purchase_order ? trim($sale->purchase_order) : '';
            $oldInvoice = $sale->invoice ? trim($sale->invoice) : '';
            $oldSaleType = $sale->sale_type ? trim($sale->sale_type) : '';
            $oldTerm = $sale->term ? trim($sale->term) : '';

            $fields = [
                'seller', 'purchase_order', 'invoice', 'sale_type', 'term', 'date',
                'folio', 'user_id', 'sales_status_id', 'sector_id', 'is_customer', 'payment_status',
            ];

            $updateData = [];
            foreach ($fields as $field) {
                if (array_key_exists($field, $data)) {
                    $updateData[$field] = $data[$field];
                }
            }

            if ((int) ($data['is_customer'] ?? $sale->is_customer) === 1) {
                $updateData['customer_id'] = $data['customer_id'] ?? $sale->customer_id;
                $updateData['prospect_id'] = null;
                $updateData['is_customer'] = 1;
            } else {
                $prospectId = $data['prospect_id'] ?? $sale->prospect_id;
                if (!empty($prospectId)) {
                    $prospect = $this->prospect->where('prospect_id', $prospectId)->lockForUpdate()->first();
                    if ($prospect) {
                        $sectorId = (int) ($data['sector_id'] ?? $sale->sector_id);
                        $customerCode = $this->generateCustomerCode($sectorId);

                        $customer = $this->customer->create([
                            'name'          => $prospect->name,
                            'phone'         => $prospect->phone,
                            'email'         => $prospect->email,
                            'rfc'           => $prospect->rfc,
                            'district'      => $prospect->district,
                            'city'          => $prospect->city,
                            'state'         => $prospect->state,
                            'address'       => $prospect->address,
                            'sector_id'     => $sectorId,
                            'customer_code' => $customerCode,
                        ]);

                        $updateData['customer_id'] = $customer->customer_id;
                        $updateData['prospect_id'] = null;
                        $updateData['is_customer'] = 1;

                        $this->sale->where('prospect_id', $prospect->prospect_id)->update([
                            'customer_id' => $customer->customer_id,
                            'prospect_id' => null,
                            'is_customer' => 1,
                        ]);

                        $prospect->delete();
                    } else {
                        $updateData['prospect_id'] = $prospectId;
                        $updateData['customer_id'] = null;
                    }
                } else {
                    $updateData['prospect_id'] = null;
                    $updateData['customer_id'] = null;
                }
            }

            $sale->update($updateData);

            $saleDetailsIds     = $data['sale_detail'] ?? [];
            $productsId         = $data['product_id'] ?? [];
            $publicProductNames = $data['public_product_name'] ?? [];
            $publicBatchs       = $data['public_batch'] ?? [];
            $quantities         = $data['quantity'] ?? [];
            $invoiceValues      = $data['invoice_val'] ?? [];
            $costs              = $data['cost'] ?? [];
            $hasTaxs            = $data['has_tax'] ?? [];

            $existingIds = array_filter(array_map('intval', $saleDetailsIds), function ($id) {
                return $id > 0;
            });

            // Map product IDs to names in bulk for fast lookups
            $incomingProdIds = array_unique(array_filter(array_map('intval', $productsId)));
            $productNamesMap = !empty($incomingProdIds)
                ? $this->product->whereIn('product_id', $incomingProdIds)->pluck('name', 'product_id')->toArray()
                : [];

            $changes = [];

            // Detect product deletions
            foreach ($oldDetails as $oldId => $oldDetail) {
                if (!in_array($oldId, $existingIds)) {
                    $pName = $oldDetail->public_product_name ?: ($oldDetail->product?->name ?? 'Producto #' . $oldDetail->product_id);
                    $qty = (float) $oldDetail->quantity;
                    $changes[] = "Producto removido: '{$pName}' (tenía {$qty} unidades)";
                }
            }

            $this->saleDetail->where('sale_id', $saleId)
                ->whereNotIn('sale_detail_id', $existingIds)
                ->delete();

            foreach ($productsId as $i => $productId) {
                $detailId   = (int) ($saleDetailsIds[$i] ?? 0);
                $invoiceVal = (int) ($invoiceValues[$i] ?? 0);
                $cost       = (float) ($costs[$i] ?? 0);
                $newQty     = (float) ($quantities[$i] ?? 0);
                $newBatch   = !empty($publicBatchs[$i]) ? trim($publicBatchs[$i]) : null;
                $fallback   = $productNamesMap[$productId] ?? "Producto #{$productId}";
                $pName      = !empty($publicProductNames[$i]) ? trim($publicProductNames[$i]) : $fallback;

                if ($invoiceVal === 1) {
                    $cost = 0;
                }

                $detailPayload = [
                    'product_id'          => $productId,
                    'public_product_name' => $publicProductNames[$i] ?? '',
                    'quantity'            => $newQty,
                    'cost'                => $cost,
                    'invoice_val'         => $invoiceVal,
                    'public_batch'        => $newBatch,
                    'has_tax'             => (int) ($hasTaxs[$i] ?? 0),
                ];

                if ($detailId > 0 && isset($oldDetails[$detailId])) {
                    $oldDetail = $oldDetails[$detailId];
                    $oldQty = (float) $oldDetail->quantity;
                    $oldBatch = $oldDetail->public_batch ? trim($oldDetail->public_batch) : null;
                    $oldName = $oldDetail->public_product_name ? trim($oldDetail->public_product_name) : ($oldDetail->product?->name ?? "Producto #{$oldDetail->product_id}");

                    if (abs($oldQty - $newQty) > 0.0001) {
                        $diff = $newQty - $oldQty;
                        $diffStr = $diff > 0 ? "+{$diff}" : "{$diff}";
                        $changes[] = "Cantidad modificada para '{$pName}': de {$oldQty} a {$newQty} ({$diffStr})";
                    }

                    if ($oldBatch !== $newBatch) {
                        $b1 = $oldBatch ?: 'Sin lote';
                        $b2 = $newBatch ?: 'Sin lote';
                        $changes[] = "Lote modificado para '{$pName}': de '{$b1}' a '{$b2}'";
                    }

                    if ((int)$oldDetail->product_id !== (int)$productId) {
                        $changes[] = "Producto reemplazado: de '{$oldName}' a '{$pName}'";
                    }

                    $this->saleDetail->where('sale_detail_id', $detailId)->update($detailPayload);
                } else {
                    $batchStr = $newBatch ? " (Lote: {$newBatch})" : "";
                    $changes[] = "Producto agregado: '{$pName}' con cantidad {$newQty}{$batchStr}";

                    $detailPayload['sale_id'] = $saleId;
                    $this->saleDetail->create($detailPayload);
                }
            }

            // Detect general attributes changes
            if (isset($data['date']) && $oldDate != $data['date']) {
                $oldDStr = \Carbon\Carbon::parse($oldDate)->format('d/m/Y');
                $newDStr = \Carbon\Carbon::parse($data['date'])->format('d/m/Y');
                $changes[] = "Fecha modificada: de {$oldDStr} a {$newDStr}";
            }

            if (array_key_exists('purchase_order', $data) && $oldPO !== trim((string)$data['purchase_order'])) {
                $oldPoVal = $oldPO ?: 'Sin PO';
                $newPoVal = trim((string)$data['purchase_order']) ?: 'Sin PO';
                $changes[] = "Orden de compra (PO): de '{$oldPoVal}' a '{$newPoVal}'";
            }

            if (array_key_exists('invoice', $data) && $oldInvoice !== trim((string)$data['invoice'])) {
                $oldInvVal = $oldInvoice ?: 'Sin factura';
                $newInvVal = trim((string)$data['invoice']) ?: 'Sin factura';
                $changes[] = "Factura: de '{$oldInvVal}' a '{$newInvVal}'";
            }

            if (isset($data['sale_type']) && $oldSaleType !== trim((string)$data['sale_type'])) {
                $changes[] = "Tipo de venta: de '{$oldSaleType}' a '{$data['sale_type']}'";
            }

            if (array_key_exists('term', $data) && $oldTerm !== trim((string)$data['term'])) {
                $oldTVal = $oldTerm ?: 'Inmediato';
                $newTVal = trim((string)$data['term']) ?: 'Inmediato';
                $changes[] = "Plazo de crédito: de '{$oldTVal}' a '{$newTVal}'";
            }

            if (isset($data['seller']) && $oldSeller !== trim((string)$data['seller'])) {
                $changes[] = "Vendedor modificado: de '{$oldSeller}' a '{$data['seller']}'";
            }

            // Customer / Prospect changes
            $isCustNew = (int) ($data['is_customer'] ?? $sale->is_customer);
            if ($isCustNew === 1) {
                $newCustId = (int) ($data['customer_id'] ?? $sale->customer_id);
                if ((int)$sale->is_customer !== 1 || (int)$sale->customer_id !== $newCustId) {
                    $newCust = $this->customer->find($newCustId);
                    $newCustName = $newCust?->name ?? "Cliente #{$newCustId}";
                    $changes[] = "Cliente modificado: de '{$oldClientName}' a '{$newCustName}'";
                }
            } else {
                $newProspId = (int) ($data['prospect_id'] ?? $sale->prospect_id);
                if ((int)$sale->is_customer === 1 || (int)$sale->prospect_id !== $newProspId) {
                    $newProsp = $this->prospect->find($newProspId);
                    $newProspName = $newProsp?->name ?? "Prospecto #{$newProspId}";
                    $changes[] = "Prospecto modificado: de '{$oldClientName}' a '{$newProspName}'";
                }
            }

            // Send notification to Warehouse and Admin if changes occurred
            if (!empty($changes)) {
                try {
                    $editorName = $currentUser?->name ?? (auth()->check() ? auth()->user()->name : 'un vendedor');
                    $almacenUsers = $this->user->role(['Warehouse', 'Admin'])->get();
                    if ($almacenUsers->isNotEmpty()) {
                        $message = 'La venta Folio ' . $sale->folio . ' ha sido modificada por ' . $editorName . '.';
                        Notification::send($almacenUsers, new SaleAlmacenNotification(
                            $sale,
                            'sale_edited',
                            $message,
                            null,
                            null,
                            $changes,
                            'Venta Folio ' . $sale->folio . ' Modificada'
                        ));
                    }
                } catch (\Throwable $e) {
                    Log::warning('Error al enviar notificación de venta editada', [
                        'sale_id'   => $saleId,
                        'exception' => $e->getMessage(),
                    ]);
                }
            }

            return true;
        });
    }

    public function delete(int $saleId): bool
    {
        return DB::transaction(function () use ($saleId) {
            $sale = $this->sale->where('sale_id', $saleId)->lockForUpdate()->first();
            if (!$sale) {
                return false;
            }

            $this->saleDetail->where('sale_id', $saleId)->delete();

            return (bool) $sale->delete();
        });
    }

    public function find(int $saleId): ?Sale
    {
        return $this->sale->with(['customer', 'prospect', 'details.product', 'sector', 'status', 'user'])
            ->where('sale_id', $saleId)
            ->first();
    }

    public function findWithDetails(int $saleId): ?array
    {
        $saleBase = $this->sale->select('is_customer')->where('sale_id', $saleId)->first();
        if (!$saleBase) {
            return null;
        }

        $sectors = $this->sector->all();

        $query = $this->sale->select('sales.*', 'sectors.name as sector_name')
            ->leftJoin('sectors', 'sectors.sector_id', '=', 'sales.sector_id')
            ->where('sales.sale_id', $saleId);

        if ((int) $saleBase->is_customer === 1) {
            $query->join('customers', 'customers.customer_id', '=', 'sales.customer_id')
                ->addSelect(
                    'customers.name as client_name',
                    'customers.email',
                    'customers.phone',
                    'customers.rfc',
                    'customers.address',
                    'customers.city',
                    'customers.state',
                    'customers.district'
                );
        } else {
            $query->join('prospects', 'prospects.prospect_id', '=', 'sales.prospect_id')
                ->addSelect(
                    'prospects.name as client_name',
                    'prospects.email',
                    'prospects.phone',
                    'prospects.rfc',
                    'prospects.address',
                    'prospects.city',
                    'prospects.state',
                    'prospects.district'
                );
        }

        $sale = $query->first();

        $saleDetail = $this->saleDetail->select('sale_detail.*', 'products.name as original_product_name', 'products.sku')
            ->join('products', 'products.product_id', '=', 'sale_detail.product_id')
            ->where('sale_id', $saleId)
            ->get();

        return [
            'sale'        => $sale,
            'sale_detail' => $saleDetail,
            'sectors'     => $sectors,
        ];
    }

    public function getRemisionesChartData(): array
    {
        $data = DB::table('sales')
            ->join('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id')
            ->join('sales_status', 'sales.sales_status_id', '=', 'sales_status.sales_status_id')
            ->select(
                'sales.date',
                'sales.seller',
                'sales.folio',
                'sales_status.name as status_name',
                'sale_detail.quantity',
                'sale_detail.cost',
                'sale_detail.has_tax',
                'sale_detail.public_product_name'
            )
            ->whereNotNull('sales.seller')
            ->where('sales.seller', '!=', '')
            ->get();

        $aliasVendedores = [
            'Flor de Maria Gutierrez Sanchez' => 'Flor de María Gutiérrez Sánchez',
            'Manola Ramirez Perez'           => 'Manola Ramirez',
        ];

        $data->transform(function ($item) use ($aliasVendedores) {
            $nombreVendedor = trim($item->seller);

            if (array_key_exists($nombreVendedor, $aliasVendedores)) {
                $item->seller = $aliasVendedores[$nombreVendedor];
            }

            $esCancelado = (strtolower($item->status_name) === 'cancelado' || strtolower($item->status_name) === 'cancelada');

            if ($esCancelado) {
                $item->total_money = 0;
            } else {
                $subtotal = (float) ($item->quantity ?? 0) * (float) ($item->cost ?? 0);
                $item->total_money = ($item->has_tax == 1) ? ($subtotal * 1.16) : $subtotal;
            }

            return $item;
        });

        return [
            'datos' => $data->groupBy('date'),
        ];
    }

    public function updateStatus(int $saleId, int $statusId): bool
    {
        return DB::transaction(function () use ($saleId, $statusId) {
            $sale = $this->sale->where('sale_id', $saleId)->lockForUpdate()->first();
            if (!$sale) {
                return false;
            }

            return (bool) $sale->update([
                'sales_status_id' => $statusId,
                'updated_at'      => now(),
            ]);
        });
    }

    public function getClienteData(string $busqueda)
    {
        if (empty(trim($busqueda))) {
            return [];
        }

        return DB::table('sales')
            ->join('sale_detail', 'sales.sale_id', '=', 'sale_detail.sale_id')
            ->join('customers', 'sales.customer_id', '=', 'customers.customer_id')
            ->where('customers.name', 'LIKE', "%{$busqueda}%")
            ->select(
                'sales.date',
                'sale_detail.public_product_name as producto',
                'sale_detail.quantity',
                'sale_detail.cost',
                DB::raw('(sale_detail.quantity * sale_detail.cost) as subtotal'),
                DB::raw('MONTH(sales.date) as mes'),
                DB::raw('YEAR(sales.date) as anio')
            )
            ->orderBy('sales.date', 'asc')
            ->get();
    }

    public function getAlmacenDetail(int $saleId): ?array
    {
        $sale = $this->sale->with(['customer', 'prospect', 'user', 'products'])->find($saleId);
        if (!$sale) {
            return null;
        }

        $output = null;
        if (in_array($sale->almacen_status, ['confirmed'])) {
            $output = $this->output->with('products')
                ->where('comments', 'Salida automática de Venta Folio ' . $sale->folio)
                ->first();
        }

        $recentEdits = DB::table('notifications')
            ->where('type', 'App\\Notifications\\SaleAlmacenNotification')
            ->where(function ($q) use ($saleId) {
                $q->where('data', 'like', '%"sale_id":' . $saleId . '%')
                  ->orWhere('data', 'like', '%"sale_id":"' . $saleId . '"%');
            })
            ->where('data', 'like', '%"type":"sale_edited"%')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notif) {
                $data = json_decode($notif->data, true);
                return [
                    'created_at' => $notif->created_at,
                    'message'    => $data['message'] ?? '',
                    'changes'    => $data['changes'] ?? [],
                ];
            })
            ->unique(function ($item) {
                return $item['created_at'] . serialize($item['changes']);
            })
            ->values();

        return [
            'sale'        => $sale,
            'output'      => $output,
            'recentEdits' => $recentEdits,
        ];
    }

    public function almacenAction(int $saleId, array $data, ?User $currentUser = null): array
    {
        return DB::transaction(function () use ($saleId, $data) {
            $sale = $this->sale->with(['user', 'products'])->where('sale_id', $saleId)->lockForUpdate()->first();
            if (!$sale) {
                throw new DomainException('Venta no encontrada.');
            }

            $action = $data['action'] ?? null;
            $userVentas = $sale->user;
            $almacenUsers = $this->user->role(['Warehouse', 'Admin'])->get();

            if ($action === 'confirm') {
                $lotAssignments = $data['lot_assignments'] ?? [];
                if (empty($lotAssignments)) {
                    throw new DomainException('Debes seleccionar un lote para cada producto.');
                }

                $sale->almacen_status = 'confirmed';
                $sale->save();

                $output = $this->output->create([
                    'customer_id'          => $sale->customer_id ?? 1,
                    'vendedor'             => $sale->seller ?? ($sale->user ? $sale->user->name : 'N/A'),
                    'operator'             => null,
                    'license_number'       => null,
                    'security_seal'        => 0,
                    'security_seal_number' => null,
                    'unit_plates'          => null,
                    'trailer_plates'       => null,
                    'comments'             => 'Salida automática de Venta Folio ' . $sale->folio,
                    'transport_line_id'    => null,
                ]);

                foreach ($lotAssignments as $assignment) {
                    $cliId = (int) $assignment['cli_id'];
                    $quantity = (float) $assignment['quantity'];

                    $cliRecord = $this->cli->where('cli_id', $cliId)->lockForUpdate()->first();
                    if (!$cliRecord) {
                        throw new DomainException("Registro de ubicación no encontrado (CLI ID: {$cliId}).");
                    }
                    if ($cliRecord->net_weight < $quantity) {
                        throw new DomainException("Stock insuficiente en la ubicación seleccionada. Disponible: {$cliRecord->net_weight}, Solicitado: {$quantity}");
                    }

                    $cliRecord->net_weight -= $quantity;
                    if ($cliRecord->weight_per_unit > 0) {
                        $cliRecord->quantity = $cliRecord->net_weight / $cliRecord->weight_per_unit;
                    }
                    $cliRecord->save();

                    $inventory = $this->inventory->where('inventory_id', $cliRecord->inventory_id)->lockForUpdate()->first();
                    if ($inventory) {
                        $inventory->stock -= $quantity;
                        $inventory->save();

                        DB::table('product_outputs')->insert([
                            'output_id'       => $output->output_id,
                            'product_id'      => $inventory->product_id,
                            'quantity'        => $quantity,
                            'warehouse_batch' => $inventory->batch,
                            'label_batch'     => '',
                        ]);
                    }
                }

                try {
                    if ($userVentas) {
                        $userVentas->notify(new SaleAlmacenNotification($sale, 'confirmed', 'Tu venta Folio ' . $sale->folio . ' ha sido confirmada por almacén.'));
                    }
                    if ($almacenUsers->isNotEmpty()) {
                        Notification::send($almacenUsers, new SaleAlmacenNotification($sale, 'inventory_updated', 'El inventario ha sido reducido para la venta Folio ' . $sale->folio));
                    }
                } catch (\Throwable $ignored) {
                }

                return [
                    'success' => true,
                    'message' => 'Venta confirmada e inventario actualizado. Se ha generado la salida automática (ID: ' . $output->output_id . ').',
                ];
            }

            if ($action === 'postpone') {
                $cleanedReason = strip_tags(trim($data['reason'] ?? ''));
                $postponedDate = $data['date'] ?? null;

                $sale->almacen_status = 'postponed';
                $sale->almacen_comment = $cleanedReason;
                $sale->almacen_postponed_date = $postponedDate;
                $sale->save();

                try {
                    if ($userVentas) {
                        $userVentas->notify(new SaleAlmacenNotification(
                            $sale,
                            'postponed',
                            'Tu venta Folio ' . $sale->folio . ' fue pospuesta por almacén. Recordatorio: ' . $postponedDate,
                            $cleanedReason,
                            $postponedDate
                        ));
                    }
                } catch (\Throwable $ignored) {
                }

                return [
                    'success' => true,
                    'message' => 'Venta pospuesta. Se te recordará el ' . $postponedDate,
                ];
            }

            if ($action === 'mark_ready') {
                $sale->almacen_status = 'pending';
                $sale->almacen_comment = null;
                $sale->almacen_postponed_date = null;
                $sale->save();

                return [
                    'success' => true,
                    'message' => 'Venta marcada como lista. Ahora puedes confirmar la salida.',
                ];
            }

            if ($action === 'cancel') {
                $cleanedReason = strip_tags(trim($data['reason'] ?? ''));
                $sale->almacen_status = 'cancelled';
                $sale->almacen_comment = $cleanedReason;
                $cancelStatus = DB::table('sales_status')
                    ->where('name', 'like', '%Cancelad%')
                    ->value('sales_status_id') ?? 4;
                $sale->sales_status_id = $cancelStatus;
                $sale->save();

                CxcDetail::where('sale_id', $sale->sale_id)->update(['is_canceled' => 1]);

                try {
                    if ($userVentas) {
                        $userVentas->notify(new SaleAlmacenNotification(
                            $sale,
                            'cancelled',
                            'Tu venta Folio ' . $sale->folio . ' fue cancelada por almacén.',
                            $cleanedReason
                        ));
                    }
                } catch (\Throwable $ignored) {
                }

                return [
                    'success' => true,
                    'message' => 'Venta cancelada.',
                ];
            }

            throw new DomainException('Acción inválida.');
        });
    }

    public function getSaleLots(int $saleId): array
    {
        $saleDetails = $this->saleDetail->where('sale_id', $saleId)->get();
        $result = [];

        foreach ($saleDetails as $detail) {
            $productName = $detail->public_product_name;
            if (empty($productName)) {
                $product = $this->product->find($detail->product_id);
                $productName = $product ? $product->name : 'Producto ID: ' . $detail->product_id;
            }

            $cliRecords = DB::table('cli')
                ->join('inventory', 'inventory.inventory_id', '=', 'cli.inventory_id')
                ->join('locations', 'locations.location_id', '=', 'cli.location_id')
                ->where('inventory.product_id', $detail->product_id)
                ->where('cli.net_weight', '>', 0)
                ->select(
                    'cli.cli_id',
                    'inventory.inventory_id',
                    'inventory.batch',
                    'locations.name as location_name',
                    'cli.location_id',
                    'cli.net_weight',
                    'cli.quantity',
                    'cli.weight_per_unit'
                )
                ->get();

            $result[] = [
                'detail_id'         => $detail->sale_detail_id,
                'product_id'        => $detail->product_id,
                'product_name'      => $productName,
                'quantity_required' => $detail->quantity,
                'lots'              => $cliRecords,
            ];
        }

        return $result;
    }

    public function getPostponedReminders($user)
    {
        if (!$user || !method_exists($user, 'hasAnyRole') || !$user->hasAnyRole(['Warehouse', 'Admin'])) {
            return collect([]);
        }

        $today = now()->toDateString();

        return $this->sale->where('almacen_status', 'postponed')
            ->whereNotNull('almacen_postponed_date')
            ->whereDate('almacen_postponed_date', '<=', $today)
            ->select('sale_id', 'folio', 'almacen_comment', 'almacen_postponed_date')
            ->get();
    }
}
