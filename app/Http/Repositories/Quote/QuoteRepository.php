<?php

namespace App\Http\Repositories\Quote;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteDetail;
use App\Models\QuoteStatus;
use App\Models\Sector;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuoteRepository
{
    protected Quote $quote;
    protected QuoteDetail $detail;
    protected QuoteStatus $status;
    protected Product $product;
    protected Customer $customer;
    protected Sector $sector;

    public function __construct(
        Quote $quote,
        QuoteDetail $detail,
        QuoteStatus $status,
        Product $product,
        Customer $customer,
        Sector $sector
    ) {
        $this->quote = $quote;
        $this->detail = $detail;
        $this->status = $status;
        $this->product = $product;
        $this->customer = $customer;
        $this->sector = $sector;
    }

    public function getIndexData(): array
    {
        return [
            'quotes'       => $this->quote->with(['customer', 'prospect', 'status'])->orderBy('quote_id', 'desc')->get(),
            'sectors'      => $this->sector->orderBy('name', 'asc')->get(),
            'db_products'  => $this->product->orderBy('name', 'asc')->get(['product_id', 'name', 'sku']),
            'db_customers' => $this->customer->orderBy('name', 'asc')->get(['customer_id', 'name']),
        ];
    }

    public function getCreateData(): array
    {
        $lastQuote = $this->quote->orderBy('quote_id', 'desc')->first();
        $nextNumber = 1;

        if ($lastQuote && $lastQuote->folio) {
            $lastNumber = (int) filter_var($lastQuote->folio, FILTER_SANITIZE_NUMBER_INT);
            $nextNumber = $lastNumber + 1;
        }

        return [
            'db_products'  => $this->product->orderBy('name', 'asc')->get(),
            'db_customers' => $this->customer->orderBy('name', 'asc')->get(),
            'newFolio'     => 'SCT-' . $nextNumber,
        ];
    }

    public function getEditData(int $id): ?array
    {
        $quote = $this->quote->with(['details.product', 'status'])->where('quote_id', $id)->first();
        if (!$quote) {
            return null;
        }

        return [
            'quote'        => $quote,
            'db_products'  => $this->product->orderBy('name', 'asc')->get(),
            'statuses'     => $this->status->all(),
            'db_customers' => $this->customer->orderBy('name', 'asc')->get(),
        ];
    }

    public function find(int $id): ?Quote
    {
        return $this->quote->with(['details.product', 'status', 'user'])->where('quote_id', $id)->first();
    }

    public function generateNextFolio(): string
    {
        $lastQuote = $this->quote->where('folio', 'like', 'SCT-%')
            ->selectRaw("folio, CAST(SUBSTRING(folio, 5) AS UNSIGNED) as num")
            ->orderBy('num', 'desc')
            ->lockForUpdate()
            ->first();

        $nextNumber = $lastQuote ? ($lastQuote->num + 1) : 428;

        return 'SCT-' . $nextNumber;
    }

    public function store(array $data, int $userId): Quote
    {
        return DB::transaction(function () use ($data, $userId) {
            $finalFolio = $this->generateNextFolio();

            $quote = $this->quote->create([
                'folio'                   => $finalFolio,
                'company'                 => $data['company'],
                'date'                    => $data['date'],
                'currency'                => $data['currency'] ?? 'MXN',
                'attention'               => $data['attention'] ?? '',
                'department'              => $data['department'] ?? '',
                'phone'                   => $data['phone'] ?? '',
                'place_of_delivery'       => $data['place_of_delivery'] ?? '',
                'transport_specification' => $data['transport_specification'] ?? '',
                'deadline'                => $data['deadline'] ?? null,
                'terms'                   => $data['terms'] ?? '',
                'notes'                   => $data['notes'] ?? '',
                'quotes_status_id'        => $data['quotes_status_id'],
                'user_id'                 => $userId,
            ]);

            $products = $data['products'] ?? [];
            foreach ($products as $item) {
                $nombreProducto = $item['quote_product_name'] ?? null;

                if (empty($nombreProducto) && !empty($item['product_id'])) {
                    $dbProduct = $this->product->find($item['product_id']);
                    $nombreProducto = $dbProduct ? $dbProduct->name : 'Producto sin nombre';
                }

                if (!empty($item['product_id']) || !empty($nombreProducto)) {
                    $this->detail->create([
                        'quote_id'           => $quote->quote_id,
                        'product_id'         => !empty($item['product_id']) ? $item['product_id'] : null,
                        'quote_product_name' => $nombreProducto ?? 'Producto sin nombre',
                        'quantity'           => $item['quantity'] ?? 0,
                        'cost'               => $item['cost'] ?? 0,
                        'presentation'       => $item['presentation'] ?? '',
                        'unit'               => $item['unit'] ?? '',
                        'iva'                => $item['iva'] ?? 0,
                    ]);
                }
            }

            return $quote->load('details');
        });
    }

    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $quote = $this->quote->where('quote_id', $id)->lockForUpdate()->first();
            if (!$quote) {
                return false;
            }

            $updateData = [
                'company'                 => $data['company'],
                'currency'                => $data['currency'] ?? $quote->currency,
            ];

            if (isset($data['folio'])) {
                $updateData['folio'] = $data['folio'];
            }
            if (isset($data['date'])) {
                $updateData['date'] = $data['date'];
            }
            if (isset($data['attention'])) {
                $updateData['attention'] = $data['attention'];
            }
            if (isset($data['department'])) {
                $updateData['department'] = $data['department'];
            }
            if (isset($data['phone'])) {
                $updateData['phone'] = $data['phone'];
            }
            if (isset($data['place_of_delivery'])) {
                $updateData['place_of_delivery'] = $data['place_of_delivery'];
            }
            if (isset($data['transport_specification'])) {
                $updateData['transport_specification'] = $data['transport_specification'];
            }
            if (isset($data['deadline'])) {
                $updateData['deadline'] = $data['deadline'];
            }
            if (isset($data['terms'])) {
                $updateData['terms'] = $data['terms'];
            }
            if (isset($data['notes'])) {
                $updateData['notes'] = $data['notes'];
            }
            if (isset($data['quotes_status_id'])) {
                $updateData['quotes_status_id'] = $data['quotes_status_id'];
            }

            $quote->update($updateData);

            $this->detail->where('quote_id', $id)->delete();

            $products = $data['products'] ?? [];
            foreach ($products as $item) {
                $nombreProducto = $item['quote_product_name'] ?? null;

                if (empty($nombreProducto) && !empty($item['product_id'])) {
                    $dbProduct = $this->product->find($item['product_id']);
                    $nombreProducto = $dbProduct ? $dbProduct->name : 'Producto sin nombre';
                }

                if (!empty($item['product_id']) || !empty($nombreProducto)) {
                    $this->detail->create([
                        'quote_id'           => $quote->quote_id,
                        'product_id'         => !empty($item['product_id']) ? $item['product_id'] : null,
                        'quote_product_name' => $nombreProducto ?? 'Producto sin nombre',
                        'quantity'           => $item['quantity'] ?? 0,
                        'cost'               => $item['cost'] ?? 0,
                        'presentation'       => $item['presentation'] ?? '',
                        'unit'               => $item['unit'] ?? '',
                        'iva'                => $item['iva'] ?? 0,
                    ]);
                }
            }

            return true;
        });
    }

    public function updateStatus(int $id, int $statusId): bool
    {
        return DB::transaction(function () use ($id, $statusId) {
            $quote = $this->quote->where('quote_id', $id)->lockForUpdate()->first();
            if (!$quote) {
                return false;
            }

            return $quote->update([
                'quotes_status_id' => $statusId,
            ]);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $quote = $this->quote->where('quote_id', $id)->lockForUpdate()->first();
            if (!$quote) {
                return false;
            }

            $this->detail->where('quote_id', $id)->delete();

            return (bool) $quote->delete();
        });
    }

    public function getPdfData(int $id): ?array
    {
        $quote = $this->quote->with(['details.product'])->where('quote_id', $id)->first();
        if (!$quote) {
            return null;
        }

        $user = auth()->user();
        $authorName = $user ? $user->name : 'Manola Ramírez';

        $viewData = [
            'codigo_formato'          => 'SSS-FOR-COM-03',
            'fecha_elaboracion'       => '30-Enero-2023',
            'fecha_actualizacion'     => '--',
            'version'                 => '00',
            'pagina'                  => '1 de 1',
            'empresa'                 => $quote->company,
            'folio'                   => $quote->folio,
            'moneda'                  => $quote->currency ?? 'MXN',
            'atencion'                => $quote->attention,
            'departamento'            => $quote->department ?? 'Compras',
            'fecha_texto'             => 'Apaseo el Grande, Guanajuato, México a ' . Carbon::parse($quote->date)->translatedFormat('d \d\e F \d\e\l Y') . '.',
            'productos'               => $quote->details,
            'incoterm'                => $quote->place_of_delivery ?? 'LAB Apaseo El Grande.',
            'phone'                   => $quote->phone ?? 'N/A',
            'presentacion'            => $quote->presentation ?? 'N/A',
            'transporte'              => $quote->transport_specification ?? 'Paquetería consolidada',
            'tiempo_entrega'          => $quote->deadline ?? '6 días hábiles una vez recibida la orden de compra y pago.',
            'terminos'                => $quote->terms ?? 'Contado 100%',
            'notas'                   => $quote->notes ?: ('Los precios antes mencionados son netos en ' . (($quote->currency ?? 'MXN') === 'USD' ? 'Dólares Americanos (USD)' : 'Moneda Nacional (MXN)') . '. La cotización es válida por 15 días.'),
            'firma_nombre'            => $authorName,
        ];

        return [
            'quote'    => $quote,
            'viewData' => $viewData,
        ];
    }
}
