<?php

namespace App\Http\Repositories\Pdf;

use App\Models\Control;
use App\Models\Input;
use App\Models\Output;
use App\Models\PurchaseRequisition;
use App\Models\Quote;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PdfRepository
{
    protected Input $inputModel;
    protected Output $outputModel;
    protected Control $controlModel;
    protected Sale $saleModel;
    protected Quote $quoteModel;
    protected PurchaseRequisition $requisitionModel;

    public function __construct(
        Input $inputModel,
        Output $outputModel,
        Control $controlModel,
        Sale $saleModel,
        Quote $quoteModel,
        PurchaseRequisition $requisitionModel
    ) {
        $this->inputModel = $inputModel;
        $this->outputModel = $outputModel;
        $this->controlModel = $controlModel;
        $this->saleModel = $saleModel;
        $this->quoteModel = $quoteModel;
        $this->requisitionModel = $requisitionModel;
    }

    public function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = base_path('../public_html');
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    public function getMovementPdfData(string $movType, int $movId, bool $includeComment = true): array
    {
        if ($movType === 'inputs') {
            $movement = $this->inputModel->newQuery()
                ->with(['products', 'supplier', 'transportLine'])
                ->findOrFail($movId);

            $title = 'RECEPCIÓN DE PRODUCTO';
            $dateStr = $movement->created_at ? $movement->created_at->format('d-m-Y') : now()->format('d-m-Y');
            $fileName = 'Recepcion de Producto - ' . $dateStr . '.pdf';
        } elseif ($movType === 'outputs') {
            $movement = $this->outputModel->newQuery()
                ->with(['products', 'customer', 'transportLine'])
                ->findOrFail($movId);

            $title = 'SALIDA DE PRODUCTO';
            $dateStr = $movement->created_at ? $movement->created_at->format('d-m-Y') : now()->format('d-m-Y');
            $fileName = 'Salida de Producto - ' . $dateStr . '.pdf';
        } else {
            throw new InvalidArgumentException('Tipo de movimiento no válido. Debe ser inputs o outputs.');
        }

        if (!$includeComment) {
            $movement->comments = null;
        }

        return [
            'view'     => 'formats.pdf',
            'fileName' => $fileName,
            'data'     => [
                'type'     => $movType,
                'title'    => $title,
                'movement' => $movement,
            ],
            'paper'    => ['letter', 'portrait'],
            'options'  => [],
        ];
    }

    public function getTemperaturePdfData(int $weekA, int $weekB, int $year, int $warehouseId): array
    {
        if ($weekA < 1 || $weekA > 54 || $weekB < 1 || $weekB > 54 || $weekA > $weekB) {
            throw new InvalidArgumentException('El rango de semanas especificado es inválido.');
        }

        $rows = $this->controlModel->newQuery()
            ->select('temperature', 'humidity', 'created_at', DB::raw('WEEK(created_at, 1) as week'))
            ->where('warehouse_id', $warehouseId)
            ->whereYear('created_at', $year)
            ->whereBetween(DB::raw('WEEK(created_at, 1)'), [$weekA, $weekB])
            ->get()
            ->map(function ($item) {
                return [
                    'temperature' => $item->temperature,
                    'humidity'    => $item->humidity,
                    'created_at'  => $item->created_at,
                    'week'        => $item->week,
                ];
            });

        $res = [];
        foreach ($rows->groupBy('week') as $week => $weekData) {
            $res[$week] = $weekData->groupBy(function ($item) {
                return Carbon::parse($item['created_at'])->format('Y-m-d');
            })->map(function ($dayData) {
                $weekDay = Carbon::parse($dayData->first()['created_at'])->dayOfWeek;

                $missingRecords = [];
                $expectedTimes = ['09', '12', '17'];
                foreach ($expectedTimes as $time) {
                    $found = $dayData->filter(function ($record) use ($time) {
                        return Carbon::parse($record['created_at'])->format('H') == $time;
                    })->isNotEmpty();

                    if (!$found) {
                        $missingRecords[] = $time;
                    }
                }

                $avgTemp = number_format((float) $dayData->avg('temperature'), 1);
                $avgHumidity = number_format((float) $dayData->avg('humidity'), 1);

                return [
                    'week_day'            => $weekDay,
                    'average_temperature' => $avgTemp,
                    'average_humidity'    => $avgHumidity,
                    'missing_records'     => $missingRecords,
                    'data'                => $dayData->toArray(),
                ];
            })->toArray();
        }

        $totalPages = (int) ceil(count($res) / 4);
        $data = [
            'week_a'      => $weekA,
            'week_b'      => $weekB,
            'year'        => $year,
            'hours'       => ['09:00', '12:00', '17:30'],
            'res'         => $res,
            'page_number' => 1,
            'total_pages' => $totalPages,
            'count'       => 0,
        ];

        return [
            'view'     => 'formats.temperature',
            'fileName' => 'Temperature Report.pdf',
            'data'     => $data,
            'paper'    => ['letter', 'landscape'],
            'options'  => [],
        ];
    }

    public function getDeliveryNotePdfData(int $saleId): array
    {
        $sale = $this->saleModel->newQuery()
            ->with(['products', 'sector'])
            ->findOrFail($saleId);

        $subtotal = 0.0;
        $iva = 0.0;
        foreach ($sale->products as $item) {
            $import = ((float) ($item->pivot->quantity ?? 0)) * ((float) ($item->pivot->cost ?? 0));
            if (!empty($item->pivot->has_tax) || !empty($item->iva)) {
                $iva += $import * 0.16;
            }
            $subtotal += $import;
        }
        $total = $subtotal + $iva;
        $type = $sale->sector->sector_id ?? 1;

        return [
            'view'     => 'formats.delivery-note',
            'fileName' => 'Delivery Note.pdf',
            'data'     => [
                'sale'     => $sale,
                'type'     => $type,
                'subtotal' => $subtotal,
                'iva'      => $iva,
                'total'    => $total,
            ],
            'paper'    => ['letter', 'portrait'],
            'options'  => [],
        ];
    }

    public function getQuotePdfData(int $quoteId): array
    {
        $quote = $this->quoteModel->newQuery()
            ->with('products')
            ->findOrFail($quoteId);

        $subtotal = 0.0;
        $iva = 0.0;
        foreach ($quote->products as $item) {
            $import = ((float) ($item->pivot->quantity ?? 0)) * ((float) ($item->pivot->cost ?? 0));
            if (!empty($item->iva)) {
                $iva += $import * 0.16;
            }
            $subtotal += $import;
        }
        $total = $subtotal + $iva;

        return [
            'view'     => 'formats.quote',
            'fileName' => 'Cotización.pdf',
            'data'     => [
                'quote'    => $quote,
                'subtotal' => $subtotal,
                'iva'      => $iva,
                'total'    => $total,
            ],
            'paper'    => ['letter', 'portrait'],
            'options'  => [],
        ];
    }

    public function getRequisitionPdfData(int $requisitionId): array
    {
        $requisition = $this->requisitionModel->newQuery()
            ->with(['comparative', 'products'])
            ->findOrFail($requisitionId);

        return [
            'view'     => 'formats.requisition',
            'fileName' => 'Requisición.pdf',
            'data'     => [
                'requisition' => $requisition,
            ],
            'paper'    => ['letter', 'portrait'],
            'options'  => [
                'isRemoteEnabled' => true,
                'chroot'          => [$this->getPublicHtmlPath(), public_path(), base_path()],
            ],
        ];
    }
}
