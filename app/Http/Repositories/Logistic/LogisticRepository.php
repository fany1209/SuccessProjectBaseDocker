<?php

namespace App\Http\Repositories\Logistic;

use App\Models\Concept;
use App\Models\Operator;
use App\Models\Quarantine;
use App\Models\Trailer;
use App\Models\TransportLine;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class LogisticRepository
{
    /**
     * @var TransportLine
     */
    protected TransportLine $model;

    /**
     * LogisticRepository constructor.
     *
     * @param TransportLine $model
     */
    public function __construct(TransportLine $model)
    {
        $this->model = $model;
    }

    /**
     * Obtiene todos los datos requeridos por la vista principal de logística.
     *
     * @return array<string, mixed>
     */
    public function getIndexData(): array
    {
        $tLines          = $this->model->all();
        $count_operators = Operator::count();
        $count_tLines    = $this->model->count();
        $count_vehicles  = Vehicle::count();
        $count_trailers  = Trailer::count();
        $warehouses      = Warehouse::all();
        $concepts        = Concept::all();
        $quarantine      = Quarantine::all();
        $transport_lines = $this->model->all();

        $products_all = DB::table('products')->select('product_id', 'name')->get();

        $products = DB::table('inventory')
            ->leftJoin('products', 'products.product_id', '=', 'inventory.product_id')
            ->select('products.product_id', 'products.name')
            ->orderBy('products.product_id')
            ->distinct()
            ->get();

        $suppliers = DB::table('suppliers')->select('supplier_id', 'name')->get();
        $customers = DB::table('customers')->select('customer_id', 'name')->get();

        return compact(
            'tLines',
            'count_operators',
            'count_tLines',
            'count_vehicles',
            'count_trailers',
            'warehouses',
            'concepts',
            'products',
            'suppliers',
            'customers',
            'products_all',
            'transport_lines',
            'quarantine'
        );
    }

    /**
     * Obtiene los datos agregados para los gráficos de vehículos y remolques por línea de transporte.
     *
     * @return array<string, mixed>
     */
    public function getChartsData(): array
    {
        $vehicles_per_tl = DB::table('transport_lines')
            ->join('vehicles', 'vehicles.transport_line_id', '=', 'transport_lines.transport_line_id')
            ->select(
                'transport_lines.name',
                DB::raw('count(*) as vehicles')
            )
            ->groupBy('transport_lines.name')
            ->get();

        $trailers_per_tl = DB::table('transport_lines')
            ->join('trailers', 'trailers.transport_line_id', '=', 'transport_lines.transport_line_id')
            ->select(
                'transport_lines.name',
                DB::raw('count(*) as trailers')
            )
            ->groupBy('transport_lines.name')
            ->get();

        return [
            'vehicles_per_tl' => $vehicles_per_tl,
            'trailers_per_tl' => $trailers_per_tl,
        ];
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
