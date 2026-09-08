<?php
/*
pdf
19/02/2026
stefany 
*/
namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Input;
use App\Models\Output;
use App\Models\Control;
use App\Models\Sale;
use App\Models\Quote;
use App\Models\PurchaseRequisition;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PdfController extends Controller
{
    public function downloadPDF($movType, $movId)
    {
        if ($movType == 'inputs') {
            $movement = Input::find($movId);
            $name = "Recepcion de Producto - " . $movement->created_at->format('d-m-Y');
        } else {
            $movement = Output::find($movId);
            $name = "Salida de Producto - " . $movement->created_at->format('d-m-Y');
        }

        if (request()->query('include_comment') === '0') {
            $movement->comments = null;
        }

        $data = [
            'type' => $movType,
            'title' => $movType == 'inputs' ? "RECEPCIÓN DE PRODUCTO" : "SALIDA DE PRODUCTO",
            'movement' => $movement
        ];
        $pdf = pdf::loadView('formats.pdf', $data); //INVESTIGAR METODO PARA AGREGAR PAGINACION DINAMICA

        return $pdf->stream($name . '.pdf');
    }

    public function makeTemperaturePDF($week_a, $week_b, $year, $warehouse_id)
    {
        // 1. Sanitización y validación estricta de tipos enteros (prevención de Inyección SQL)
        $week_a       = filter_var($week_a, FILTER_VALIDATE_INT);
        $week_b       = filter_var($week_b, FILTER_VALIDATE_INT);
        $year         = filter_var($year, FILTER_VALIDATE_INT);
        $warehouse_id = filter_var($warehouse_id, FILTER_VALIDATE_INT);

        if ($week_a === false || $week_b === false || $year === false || $warehouse_id === false) {
            abort(400, 'Los parámetros del reporte deben ser números enteros válidos.');
        }

        if ($week_a < 1 || $week_a > 54 || $week_b < 1 || $week_b > 54 || $week_a > $week_b) {
            abort(400, 'El rango de semanas especificado es inválido.');
        }

        // 2. Consulta con bindings parametrizados PDO (elimina vector de inyección SQL)
        $rows = Control::select('temperature', 'humidity', 'created_at', DB::raw('WEEK(created_at,1) as week'))
            ->where('warehouse_id', '=', $warehouse_id)
            ->whereYear('created_at', $year)
            ->whereBetween(DB::raw('WEEK(created_at,1)'), [$week_a, $week_b])
            ->get()
            ->map(function ($item) {
                return [
                    'temperature' => $item->temperature,
                    'humidity' => $item->humidity,
                    'created_at' => $item->created_at,
                    'week' => $item->week,
                ];
            });

        $res = [];

        foreach ($rows->groupBy('week') as $week => $week_data) {
            $res[$week] = $week_data->groupBy(function ($item) {
                return Carbon::parse($item['created_at'])->format('Y-m-d');
            })->map(function ($day_data) {
                $week_day = Carbon::parse($day_data->first()['created_at'])->dayOfWeek;

                // Verificar si falta algún registro en el día
                $missingRecords = [];
                $expectedTimes = ['09', '12', '17'];
                foreach ($expectedTimes as $time) {
                    $foundRecord = $day_data->filter(function ($record) use ($time) {
                        return Carbon::parse($record['created_at'])->format('H') == $time;
                    })->isNotEmpty();
                    if (!$foundRecord) {
                        $missingRecords[] = $time;
                    }
                }

                // Calcular promedios
                $avg_temperature = number_format($day_data->avg('temperature'), 1);
                $avg_humidity = number_format($day_data->avg('humidity'), 1);

                return [
                    'week_day' => $week_day,
                    'average_temperature' => $avg_temperature,
                    'average_humidity' => $avg_humidity,
                    'missing_records' => $missingRecords,
                    'data' => $day_data->toArray()
                ];
            })->toArray();
        }

        $hours = ["09:00", "12:00", "17:30"];

        $total_pages = intval(ceil(count($res) / 4));

        $data = [
            'week_a' => $week_a,
            'week_b' => $week_b,
            'year' => $year,
            'hours' => $hours,
            'res' => $res,
            'page_number' => 1,
            'total_pages' => $total_pages,
            'count' => 0
        ];

        $pdf = Pdf::loadView('formats.temperature', $data)->setPaper('letter', 'landscape')
            ->setOption([]);

        return $pdf->stream('Temperature Report.pdf');
    }

    public function makeDeliveryNotePDF($sale_id)
    {
        $sale = Sale::findOrFail($sale_id);

        $subtotal = 0;
        $iva = 0;
        foreach ($sale->products as $item) {
            $import = $item->pivot->quantity * $item->pivot->cost;
            if ($item->iva)
                $iva += $import * 0.16;
            $subtotal += $import;
        }

        $total = $iva + $subtotal;

        $type = $sale->sector->sector_id;

        $data = [
            'sale' => $sale,
            'type' => $type,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total
        ];

        $pdf = Pdf::loadView('formats.delivery-note', $data)->setPaper('letter')
            ->setOption([]);

        return $pdf->stream('Delivery Note.pdf');
    }

    public function makeQuotePDF($quote_id)
    {
        $quote = Quote::findOrFail($quote_id);
        $subtotal = 0;
        $iva = 0;

        foreach ($quote->products as $item) {
            $import = $item->pivot->quantity * $item->pivot->cost;
            if ($item->iva)
                $iva += $import * 0.16;
            $subtotal += $import;
        }

        $total = $iva + $subtotal;

        $data = [
            'quote' => $quote,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total
        ];
        
        $pdf = pdf::loadView('formats.quote', $data);

        return $pdf->stream('Cotización' . '.pdf');
    }
    
    public function makeRequisitionPDF($requisition_id)
    {
        $requisition = PurchaseRequisition::with(['comparative', 'products'])->findOrFail($requisition_id);

        $data = [
            'requisition' => $requisition
        ];

        $pdf = PDF::setOptions([
            'isRemoteEnabled' => true,
            'chroot' => public_path(), 
        ])->loadView('formats.requisition', $data);

        return $pdf->stream('Requisición.pdf');
    }

}
