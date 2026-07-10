<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteDetail;
use App\Models\Sector; 
use App\Models\Product; 
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::with(['customer', 'prospect', 'status'])->get();
        $sectors = Sector::all();
        $db_products = Product::all(['product_id', 'name', 'sku']); 
        $db_customers = Customer::orderBy('name', 'asc')->get(['name']); 

        return view('quotes', compact('quotes', 'sectors', 'db_products', 'db_customers'));
    }

    public function create()
    {
        $lastQuote = Quote::orderBy('quote_id', 'desc')->first();
        
        $nextNumber = 1;
        if ($lastQuote && $lastQuote->folio) {
            $lastNumber = (int) filter_var($lastQuote->folio, FILTER_SANITIZE_NUMBER_INT);
            $nextNumber = $lastNumber + 1;
        }

        $newFolio = "SCT-" . $nextNumber;

        $db_products = Product::all();
        $db_customers = Customer::all();

        return view('sales.quotes.form-create', compact('db_products', 'db_customers', 'newFolio'));
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'company'          => 'required',
            'date'             => 'required|date',
            'quotes_status_id' => 'required',
            'products'         => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            return \DB::transaction(function () use ($request) {
                
                $lastQuote = Quote::where('folio', 'like', 'SCT-%')
                    ->selectRaw("folio, CAST(SUBSTRING(folio, 5) AS UNSIGNED) as num")
                    ->orderBy('num', 'desc')
                    ->lockForUpdate()
                    ->first();

                $nextNumber = $lastQuote ? ($lastQuote->num + 1) : 428;
                $finalFolio = "SCT-" . $nextNumber;

                $quote = Quote::create([
                    'folio'                   => $finalFolio,
                    'company'                 => $request->company,
                    'date'                    => $request->date,
                    'attention'               => $request->attention,
                    'department'              => $request->department,
                    'phone'                   => $request->phone ?? '', 
                    'place_of_delivery'       => $request->place_of_delivery ?? '',
                    'transport_specification' => $request->transport_specification ?? '',
                    'deadline'                => $request->deadline,
                    'terms'                   => $request->terms ?? '',
                    'notes'                   => $request->notes ?? '',
                    'quotes_status_id'        => $request->quotes_status_id,
                    'user_id'                 => auth()->id() ?? 1,
                ]);

                foreach ($request->products as $productData) {
                    $nombreProducto = $productData['quote_product_name'] ?? null;

                    if (empty($nombreProducto) && !empty($productData['product_id'])) {
                        $dbProduct = Product::find($productData['product_id']);
                        $nombreProducto = $dbProduct ? $dbProduct->name : 'Producto sin nombre';
                    }

                    if (!empty($productData['product_id']) || !empty($nombreProducto)) {
                        QuoteDetail::create([
                            'quote_id'           => $quote->quote_id,
                            'product_id'         => $productData['product_id'] ?: null, 
                            'quote_product_name' => $nombreProducto ?? 'Producto sin nombre',
                            'quantity'           => $productData['quantity'] ?? 0,
                            'cost'               => $productData['cost'] ?? 0,
                            'presentation'       => $productData['presentation'] ?? '',
                            'unit'               => $productData['unit'] ?? '',
                            'iva'                => $productData['iva'] ?? 0
                        ]);
                    }
                }

                return response()->json([
                    'message' => 'Cotización guardada',
                    'folio'   => $finalFolio
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $quote = Quote::with(['details', 'status'])->findOrFail($id);
        return view('sales.quotes.show', compact('quote'));
    }

    public function edit($id)
    {
        $quote = Quote::with('details')->findOrFail($id);
        $db_products = \App\Models\Product::all();
        $db_customers = \DB::table('customers')->get(); 
        $statuses = \DB::table('quotes_status')->get();

        return view('sales.quotes.edit', compact('quote', 'db_products', 'statuses', 'db_customers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'company'  => 'required',
            'products' => 'required|array',
        ]);

        try {
            \DB::beginTransaction();

            $quote = Quote::findOrFail($id);
            
            $quote->update($request->only([
                'folio', 'company', 'date', 'attention', 'department', 
                'phone', 'place_of_delivery', 'transport_specification', 
                'deadline', 'terms', 'notes', 'quotes_status_id'
            ]));

            $quote->details()->delete(); 

            foreach ($request->products as $item) {
                $nombreProducto = $item['quote_product_name'] ?? null;

                if (empty($nombreProducto) && !empty($item['product_id'])) {
                    $dbProduct = Product::find($item['product_id']);
                    $nombreProducto = $dbProduct ? $dbProduct->name : 'Producto sin nombre';
                }

                if (!empty($item['product_id']) || !empty($nombreProducto)) {
                    $quote->details()->create([
                        'product_id'         => $item['product_id'] ?: null,
                        'quote_product_name' => $nombreProducto ?? 'Producto sin nombre',
                        'quantity'           => $item['quantity'] ?? 0,
                        'cost'               => $item['cost'] ?? 0,
                        'presentation'       => $item['presentation'] ?? '',
                        'unit'               => $item['unit'] ?? '', 
                        'iva'                => $item['iva'] ?? 0
                    ]);
                }
            }

            \DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Cotización actualizada correctamente.'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $quote = Quote::findOrFail($id);
            $quote->details()->delete();
            $quote->delete();
            \DB::commit();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => 'Cotización eliminada correctamente']);
            }
            return redirect()->route('quotes')->with('success', 'Cotización eliminada');
        } catch (\Exception $e) {
            \DB::rollBack();
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => 'Error al eliminar: ' . $e->getMessage()], 500);
            }
            return back()->withErrors('Error al eliminar: ' . $e->getMessage());
        }
    }

    public function generatePdf($id)
    {
    
        $quote = Quote::with(['details.product'])->findOrFail($id);
        $viewData = [
            'codigo_formato'      => 'SSS-FOR-COM-03',
            'fecha_elaboracion'   => '30-Enero-2023',
            'fecha_actualizacion' => '--',
            'version'             => '00',
            'pagina'              => '1 de 1',
            'empresa'             => $quote->company,
            'folio'               => $quote->folio,
            'atencion'            => $quote->attention,
            'departamento'        => $quote->department ?? 'Compras',
            'fecha_texto'         => 'Apaseo el Grande, Guanajuato, México. a ' . \Carbon\Carbon::parse($quote->date)->translatedFormat('d \d\e F \d\e\l Y') . '.',
            'productos'           => $quote->details,
            'incoterm'            => $quote->place_of_delivery ?? 'LAB Apaseo El Grande.',
            'phone'               => $quote->phone ?? 'N/A',
            'presentacion'        => $quote->presentation ?? 'N/A',
            'transporte'          => $quote->transport_specification ?? 'Paquetería consolidada',
            'tiempo_entrega'      => $quote->deadline ?? '6 días hábiles una vez recibida la orden de compra y pago.',
            'terminos'            => $quote->terms ?? 'Contado 100%',
            'notas'               => $quote->notes ?? 'Los precios antes mencionados son netos. La cotización es válida por 15 días.',
            'firma_nombre'        => auth()->user()->name ?? 'Manola Ramírez'
        ];

        try {
            $pdf = \PDF::loadView('formats.sales.quotes', $viewData)->setPaper('a4', 'portrait');

            if (app()->bound('debugbar')) {
                try { app('debugbar')->disable(); } catch (\Throwable $e) {}
            }
            @ini_set('zlib.output_compression', '0');
            while (ob_get_level() > 0) { @ob_end_clean(); }

            $filename = $quote->folio . '.pdf';
            return $pdf->download($filename);

        } catch (\Throwable $e) {
            
            return response("Error al generar el formato PDF: " . $e->getMessage(), 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $quote = Quote::findOrFail($id);
            $quote->update([
                'quotes_status_id' => $request->quotes_status_id
            ]);

            return response()->json(['message' => 'Estatus actualizado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar'], 500);
        }
    }
}