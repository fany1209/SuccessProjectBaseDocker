<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;   
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use App\Models\ReceptionOfSample;
use Illuminate\Validation\Rule;            
use App\Models\Customer;
use App\Models\LaboratoryMonitoring;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\LaboratorySample;
use App\Models\CustomerSampleRequest;
use App\Models\User; 
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewSampleRequestNotification;  

class LaboratoryController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::orderBy('name')->get(['product_id','name','sku']);

        $batchesByProduct = \DB::table('inventory')
            ->select('product_id','batch')
            ->whereNotNull('batch')
            ->whereRaw("TRIM(batch) <> ''")
            ->distinct()
            ->orderBy('product_id')
            ->orderBy('batch')
            ->get()
            ->groupBy('product_id')
            ->map(fn($g) => $g->pluck('batch')->values());

        $suppliers = \DB::table('suppliers')
            ->select('supplier_id', 'supplier_code', 'name')
            ->orderBy('name')
            ->get();

        $customers = \DB::table('customers')
            ->select(
                'customer_id',
                'name',
                'phone',
                'email',
                'address',
                'district',
                'city',
                'state',
                'postal_code',
                'country'
            )
            ->orderBy('name')
            ->get()
            ->map(function ($c) {
                $parts = array_filter([
                    $c->address,
                    $c->district,
                    $c->city,
                    $c->state,
                    $c->postal_code ? ('C.P. ' . $c->postal_code) : null,
                    $c->country,
                ]);
                $c->full_address = implode(', ', $parts);
                return $c;
            });

        $samples = \DB::table('laboratory_samples as ls')
            ->leftJoin('products as p', function ($join) {
                $join->on(
                    \DB::raw("CONVERT(p.sku USING utf8mb4)"),
                    '=',
                    \DB::raw("CONVERT(ls.sku USING utf8mb4)")
                );
            })
            ->leftJoin('suppliers as sup', function ($join) {
                $join->on(
                    \DB::raw("CONVERT(sup.name USING utf8mb4)"),
                    '=',
                    \DB::raw("CONVERT(ls.proveedor USING utf8mb4)")
                );
            })
            ->select([
                'ls.*',
                'p.product_id',
                'sup.supplier_id',
            ])
            ->orderBy('ls.id', 'desc')
            ->limit(200)
            ->get();

        $folios = \DB::table('soil_internal_analyses')
            ->whereNotNull('report_code')
            ->whereRaw("TRIM(report_code) <> ''")
            ->orderByDesc('id')
            ->limit(200)
            ->pluck('report_code')
            ->unique()
            ->values();

        $customerRequests = \App\Models\CustomerSampleRequest::orderByDesc('id')->limit(200)->get();

        return view('laboratory', compact(
            'products',
            'batchesByProduct',
            'suppliers',
            'customers',
            'samples',
            'folios',
            'customerRequests'
        ));
    }

    public function pdf1(Request $request)
    {
        try {
            $validated = $request->validate([
                'folio_muestra'             => ['nullable','string','max:50'],
                'product_id'                => ['required','integer','exists:products,product_id'],
                'nombre_comercial'          => ['nullable','string','max:255'],
                'sku'                       => ['nullable','string','max:100'],
                'batch'                     => ['nullable','string','max:100'],
                'fecha_entrada'             => ['nullable','date'],
                'fecha_caducidad'           => ['nullable','date'],
                'presentacion'              => ['nullable','string','max:100'],
                'cantidad_total'            => ['nullable','string','max:100'],
                'descripcion'               => ['nullable','string','max:2000'],
                'origen_muestra'            => ['nullable','in:proveedor,produccion,almacen,otro'],
                'origen_otro'               => ['nullable','string','max:255'],
                'objetivo_muestra'          => ['nullable','array'],
                'objetivo_muestra.*'        => ['in:inspeccion,retencion,analisis,desarrollo,exposicion,otro'],
                'objetivo_otro'             => ['nullable','string','max:255'],
                'cantidad'                  => ['nullable','numeric'],
                'um'                        => ['nullable','in:g,kg,l,ml,otro'],
                'um_otro'                   => ['nullable','string','max:50'],
                'supplier_id'               => ['nullable','integer','exists:suppliers,supplier_id'], 
                'proveedor'                 => ['nullable','string','max:255'],
                'docs_ccf'                  => ['nullable'],
                'docs_ft'                   => ['nullable'],
                'docs_hs'                   => ['nullable'],
                'docs_otro'                 => ['nullable'],
                'docs_otro_txt'             => ['nullable','string','max:255'],
                'observaciones'             => ['nullable','string','max:5000'],
                'observaciones_laboratorio' => ['nullable','string','max:5000'],
                'firma_entrega_nombre'      => ['nullable','string','max:255'],
                'firma_recepcion_nombre'    => ['nullable','string','max:255'],
            ]);


        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }

        $objetivosArray = $request->input('objetivo_muestra', []);
        $objetivoString = is_array($objetivosArray) ? implode(', ', $objetivosArray) : '';

        if (($validated['origen_muestra'] ?? null) !== 'otro') $validated['origen_otro'] = null;
        if (!in_array('otro', $objetivosArray)) $validated['objetivo_otro'] = null;
        if (($validated['um'] ?? null) !== 'otro') $validated['um_otro'] = null;

        $validated['docs_ccf']  = $request->boolean('docs_ccf');
        $validated['docs_ft']   = $request->boolean('docs_ft');
        $validated['docs_hs']   = $request->boolean('docs_hs');
        $validated['docs_otro'] = $request->boolean('docs_otro');
        $validated['folio_muestra'] = $validated['folio_muestra'] ?? ('RM-' . now()->format('Ymd-His'));
        
        $supplierId = $validated['supplier_id'] ?? null;
        try {
            if (!$supplierId && !empty($validated['proveedor'])) {
                $supplierId = Supplier::query()
                    ->where('name', $validated['proveedor'])
                    ->orWhere('supplier_code', $validated['proveedor'])
                    ->value('supplier_id');

                if (!$supplierId) {
                    $supplier = Supplier::create([
                        'name'          => $validated['proveedor'],
                        'supplier_code' => 'SP'.str_pad((string)(Supplier::max('supplier_id')+1), 5, '0', STR_PAD_LEFT),
                    ]);
                    $supplierId = $supplier->supplier_id;
                }
            }
        } catch (\Exception $e) {
        }

        try {
            $record = ReceptionOfSample::create([
                'folio_muestra'             => $validated['folio_muestra'],
                'product_id'                => $validated['product_id'],
                'nombre_comercial'          => $validated['nombre_comercial'] ?? '',
                'sku'                       => $validated['sku'] ?? '',
                'batch'                     => $validated['batch'] ?? '',
                'fecha_entrada'             => $validated['fecha_entrada'] ?? null,
                'fecha_caducidad'           => $validated['fecha_caducidad'] ?? null,
                'presentacion'              => $validated['presentacion'] ?? '',    
                'cantidad_total'            => $validated['cantidad_total'] ?? '', 
                'descripcion'               => $validated['descripcion'] ?? '',
                'origen_muestra'            => $validated['origen_muestra'] ?? '',
                'origen_otro'               => $validated['origen_otro'] ?? '',
                'objetivo_muestra'          => $objetivoString,
                'objetivo_otro'             => $validated['objetivo_otro'] ?? '',
                'cantidad'                  => $validated['cantidad'] ?? 0,
                'um'                        => $validated['um'] ?? '',
                'um_otro'                   => $validated['um_otro'] ?? '',
                'supplier_id'               => $supplierId, 
                'docs_ccf'                  => $validated['docs_ccf'],
                'docs_ft'                   => $validated['docs_ft'],
                'docs_hs'                   => $validated['docs_hs'],
                'docs_otro'                 => $validated['docs_otro'],
                'docs_otro_txt'             => $validated['docs_otro_txt'] ?? '',
                'observaciones'             => $validated['observaciones'] ?? '',
                'observaciones_laboratorio' => $validated['observaciones_laboratorio'] ?? '',
                'firma_entrega_nombre'      => $validated['firma_entrega_nombre'] ?? '',
                'firma_recepcion_nombre'    => $validated['firma_recepcion_nombre'] ?? '',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error de Base de Datos: ' . $e->getMessage()
            ], 500);
        }

        try {
            $productName = DB::table('products')->where('product_id', $validated['product_id'])->value('name');
            $supplierName = $supplierId ? Supplier::where('supplier_id', $supplierId)->value('name') : null;

            $data = [
                ...$validated,
                'objetivo_muestra' => $objetivoString,
                'producto'  => $productName ?? '',
                'proveedor' => $supplierName ?? ($validated['proveedor'] ?? ''), 
            ];

            $pdf = Pdf::loadView('formats.laboratory.01', $data)->setPaper('letter');
            $fileName = 'RecepcionMuestras_' . $data['folio_muestra'] . '_' . now()->format('d_m_Y_His') . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al generar PDF: ' . $e->getMessage()], 500);
        }
    }

    public function pdf2(Request $request)
    {
        $validated = $request->validate([
            'folio'                      => ['nullable', 'string', 'max:50'],
            'fecha_solicitud'            => ['nullable', 'date'],
            'fecha_recoleccion'          => ['nullable', 'date'],
            'cliente_nombre'             => ['required', 'string', 'max:255'],
            'cliente_direccion'          => ['nullable', 'string', 'max:500'],
            'cliente_correo'             => ['nullable', 'email', 'max:255'],
            'cliente_telefono'           => ['nullable', 'string', 'max:50'],
            'cliente_estatus'            => ['nullable', 'in:nuevo,frecuente'],
            'personal_seguimiento'       => ['nullable', 'string', 'max:255'],
            'entrega_paqueteria'         => ['nullable'],
            'entrega_personal_empresa'   => ['nullable'],
            'entrega_recoleccion_planta' => ['nullable'],
            'entrega_otro'               => ['nullable'],
            'entrega_otro_txt'           => ['nullable', 'string', 'max:255'],
            'paq_nombre'                 => ['nullable', 'string', 'max:255'],
            'paq_guia'                   => ['nullable', 'string', 'max:100'],
            'observaciones'              => ['nullable', 'string', 'max:5000'],
            'solicitante_nombre'         => ['nullable', 'string', 'max:255'],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.product_id'         => ['required', 'exists:products,product_id'],
            'items.*.sku'                => ['nullable', 'string', 'max:100'],
            'items.*.um'                 => ['nullable', 'string', 'max:50'],
            'items.*.cantidad'           => ['nullable', 'string', 'max:100'],
            'items.*.pres_ziploc'        => ['nullable'],
            'items.*.pres_whirlpak'      => ['nullable'],
            'items.*.pres_metalizada'    => ['nullable'],
            'items.*.pres_frasco'        => ['nullable'],
            'items.*.pres_bidon'         => ['nullable'],
            'items.*.pres_otro'          => ['nullable'],
            'items.*.pres_otro_txt'      => ['nullable', 'string', 'max:255'],
            'items.*.lote_almacen'       => ['nullable', 'string', 'max:100'],
            'items.*.lote_venta'         => ['nullable', 'string', 'max:100'],
            'items.*.docs_cc'            => ['nullable'],
            'items.*.docs_ft'            => ['nullable'],
            'items.*.docs_hs'            => ['nullable'],
            'items.*.docs_otro'          => ['nullable'],
            'items.*.docs_otro_txt'      => ['nullable', 'string', 'max:255'],
        ]);

        $b = fn(string $k) => $request->boolean($k);

        $data = [
            'folio'                      => $validated['folio'] ?? $request->input('folio', ''),
            'fecha_solicitud'            => $validated['fecha_solicitud'] ?? '',
            'fecha_recoleccion'          => $validated['fecha_recoleccion'] ?? '',
            'cliente_nombre'             => $validated['cliente_nombre'] ?? '',
            'cliente_direccion'          => $validated['cliente_direccion'] ?? '',
            'cliente_correo'             => $validated['cliente_correo'] ?? '',
            'cliente_telefono'           => $validated['cliente_telefono'] ?? '',
            'cliente_estatus'            => $validated['cliente_estatus'] ?? '',
            'personal_seguimiento'       => $validated['personal_seguimiento'] ?? '',
            'entrega_paqueteria'         => $b('entrega_paqueteria'),
            'entrega_personal_empresa'   => $b('entrega_personal_empresa'),
            'entrega_recoleccion_planta' => $b('entrega_recoleccion_planta'),
            'entrega_otro'               => $b('entrega_otro'),
            'entrega_otro_txt'           => $b('entrega_otro') ? ($validated['entrega_otro_txt'] ?? '') : null,
            'paq_nombre'                 => $validated['paq_nombre'] ?? '',
            'paq_guia'                   => $validated['paq_guia'] ?? '',
            'observaciones'              => $validated['observaciones'] ?? '',
            'solicitante_nombre'         => $validated['solicitante_nombre'] ?? '',
            'items'                      => [],
        ];

        foreach ($request->input('items', []) as $itemReq) {
            $prod = \App\Models\Product::find($itemReq['product_id']);
            $itemData = [
                'product_id'                 => $itemReq['product_id'],
                'producto'                   => $prod ? $prod->name : 'N/A',
                'sku'                        => !empty($itemReq['sku']) ? $itemReq['sku'] : ($prod ? $prod->sku : ''),
                'um'                         => $itemReq['um'] ?? '',
                'cantidad'                   => $itemReq['cantidad'] ?? '',
                'pres_ziploc'                => isset($itemReq['pres_ziploc']) && filter_var($itemReq['pres_ziploc'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'pres_whirlpak'              => isset($itemReq['pres_whirlpak']) && filter_var($itemReq['pres_whirlpak'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'pres_metalizada'            => isset($itemReq['pres_metalizada']) && filter_var($itemReq['pres_metalizada'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'pres_frasco'                => isset($itemReq['pres_frasco']) && filter_var($itemReq['pres_frasco'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'pres_bidon'                 => isset($itemReq['pres_bidon']) && filter_var($itemReq['pres_bidon'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'pres_otro'                  => isset($itemReq['pres_otro']) && filter_var($itemReq['pres_otro'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'pres_otro_txt'              => isset($itemReq['pres_otro']) && filter_var($itemReq['pres_otro'], FILTER_VALIDATE_BOOLEAN) ? ($itemReq['pres_otro_txt'] ?? '') : null,
                'lote_almacen'               => $itemReq['lote_almacen'] ?? '',
                'lote_venta'                 => $itemReq['lote_venta'] ?? '',
                'fecha_recoleccion'          => $validated['fecha_recoleccion'] ?? '',
                'docs_cc'                    => isset($itemReq['docs_cc']) && filter_var($itemReq['docs_cc'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'docs_ft'                    => isset($itemReq['docs_ft']) && filter_var($itemReq['docs_ft'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'docs_hs'                    => isset($itemReq['docs_hs']) && filter_var($itemReq['docs_hs'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'docs_otro'                  => isset($itemReq['docs_otro']) && filter_var($itemReq['docs_otro'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'docs_otro_txt'              => isset($itemReq['docs_otro']) && filter_var($itemReq['docs_otro'], FILTER_VALIDATE_BOOLEAN) ? ($itemReq['docs_otro_txt'] ?? '') : null,
            ];
            $data['items'][] = $itemData;
        }
        
        if (!empty($data['items'][0])) {
            $first = $data['items'][0];
            $data['producto']        = $first['producto'] ?? '';
            $data['sku']             = $first['sku'] ?? '';
            $data['cantidad']        = $first['cantidad'] ?? '';
            $data['um']              = $first['um'] ?? '';
            $data['pres_ziploc']     = $first['pres_ziploc'] ?? 0;
            $data['pres_whirlpak']   = $first['pres_whirlpak'] ?? 0;
            $data['pres_metalizada'] = $first['pres_metalizada'] ?? 0;
            $data['pres_frasco']     = $first['pres_frasco'] ?? 0;
            $data['pres_bidon']      = $first['pres_bidon'] ?? 0;
            $data['pres_otro']       = $first['pres_otro'] ?? 0;
            $data['pres_otro_txt']   = $first['pres_otro_txt'] ?? '';
            $data['lote_almacen']    = $first['lote_almacen'] ?? '';
            $data['lote_venta']      = $first['lote_venta'] ?? '';
            $data['docs_cc']         = $first['docs_cc'] ?? 0;
            $data['docs_ft']         = $first['docs_ft'] ?? 0;
            $data['docs_hs']         = $first['docs_hs'] ?? 0;
            $data['docs_otro']       = $first['docs_otro'] ?? 0;
            $data['docs_otro_txt']   = $first['docs_otro_txt'] ?? '';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('formats.laboratory.02', $data)->setPaper('letter');
        
        $slugCliente = \Illuminate\Support\Str::slug($data['cliente_nombre'] ?: 'Cliente', '-');
        $fileName = 'SolicitudMuestras_' . $slugCliente . '_' . \Carbon\Carbon::now()->format('d_m_Y_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function store2(Request $request)
    {
        $validated = $request->validate([
            'folio'                      => ['nullable', 'string', 'max:50'],
            'fecha_solicitud'            => ['nullable', 'date'],
            'fecha_recoleccion'          => ['nullable', 'date'],
            'customer_id'                => ['nullable', 'integer'],
            'cliente_nombre'             => ['required', 'string', 'max:255'],
            'cliente_direccion'          => ['nullable', 'string', 'max:500'],
            'cliente_correo'             => ['nullable', 'email', 'max:255'],
            'cliente_telefono'           => ['nullable', 'string', 'max:50'],
            'cliente_estatus'            => ['nullable', 'in:nuevo,frecuente'],
            'personal_seguimiento'       => ['nullable', 'string', 'max:255'],
            'entrega_paqueteria'         => ['nullable'],
            'entrega_personal_empresa'   => ['nullable'],
            'entrega_recoleccion_planta' => ['nullable'],
            'entrega_otro'               => ['nullable'],
            'entrega_otro_txt'           => ['nullable', 'string', 'max:255'],
            'paq_nombre'                 => ['nullable', 'string', 'max:255'],
            'paq_guia'                   => ['nullable', 'string', 'max:100'],
            'observaciones'              => ['nullable', 'string', 'max:5000'],
            'solicitante_nombre'         => ['nullable', 'string', 'max:255'],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.product_id'         => ['required', 'exists:products,product_id'],
            'items.*.sku'                => ['nullable', 'string', 'max:100'],
            'items.*.um'                 => ['nullable', 'string', 'max:50'],
            'items.*.cantidad'           => ['nullable', 'string', 'max:100'],
            'items.*.pres_ziploc'        => ['nullable'],
            'items.*.pres_whirlpak'      => ['nullable'],
            'items.*.pres_metalizada'    => ['nullable'],
            'items.*.pres_frasco'        => ['nullable'],
            'items.*.pres_bidon'         => ['nullable'],
            'items.*.pres_otro'          => ['nullable'],
            'items.*.pres_otro_txt'      => ['nullable', 'string', 'max:255'],
            'items.*.lote_almacen'       => ['nullable', 'string', 'max:100'],
            'items.*.lote_venta'         => ['nullable', 'string', 'max:100'],
            'items.*.docs_cc'            => ['nullable'],
            'items.*.docs_ft'            => ['nullable'],
            'items.*.docs_hs'            => ['nullable'],
            'items.*.docs_otro'          => ['nullable'],
            'items.*.docs_otro_txt'      => ['nullable', 'string', 'max:255'],
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                $folioFinal = $request->input('folio');
                
                if (empty($folioFinal)) {
                    $lastRecord = CustomerSampleRequest::orderBy('folio', 'desc')->first();

                    if ($lastRecord && preg_match('/SCR-(\d+)/', $lastRecord->folio, $matches)) {
                        $nextNumber = (int)$matches[1] + 1;
                    } else {
                        $nextNumber = 1;
                    }
                    
                    $folioFinal = 'SCR-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                }

                $data = $validated;
                $data['folio'] = $folioFinal;
                $data['status'] = 0;

                $checkboxes = [
                    'entrega_paqueteria', 'entrega_personal_empresa', 'entrega_recoleccion_planta', 'entrega_otro'
                ];

                foreach ($checkboxes as $field) {
                    $data[$field] = $request->boolean($field) ? 1 : 0;
                }

                if (!$data['entrega_otro']) $data['entrega_otro_txt'] = null;

                $record = CustomerSampleRequest::create($data);

                // Save items
                $items = $request->input('items', []);
                foreach ($items as $item) {
                    $itemData = $item;
                    $itemCheckboxes = [
                        'pres_ziploc', 'pres_whirlpak', 'pres_metalizada', 'pres_frasco', 'pres_bidon', 'pres_otro',
                        'docs_cc', 'docs_ft', 'docs_hs', 'docs_otro'
                    ];
                    foreach ($itemCheckboxes as $field) {
                        $itemData[$field] = isset($item[$field]) && filter_var($item[$field], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                    }
                    if (!$itemData['pres_otro']) $itemData['pres_otro_txt'] = null;
                    if (!$itemData['docs_otro']) $itemData['docs_otro_txt'] = null;

                    $record->items()->create($itemData);
                }

                $usersToNotify = User::role(['Admin', 'Laboratory'])->get();
                Notification::send($usersToNotify, new NewSampleRequestNotification($record));

                return response()->json([
                    'ok'      => true,
                    'message' => 'Request saved successfully with folio ' . $record->folio,
                    'id'      => $record->id,
                    'folio'   => $record->folio
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error("Error in store2: " . $e->getMessage());
            return response()->json([
                'ok' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getCustomerRequestsJson() 
    {
        $data = CustomerSampleRequest::with('items.product')->orderBy('id', 'desc')->limit(500)->get();
        
        $data = $data->map(function ($req) {
            $productNames = $req->items->pluck('product.name')->filter()->implode(', ');
            
            // To maintain compatibility with existing records during migration
            if (empty($productNames) && $req->product_id) {
                $product = \App\Models\Product::find($req->product_id);
                $productNames = $product ? $product->name : '';
            }

            $req->producto_nombre = $productNames ?: 'N/A';
            return $req;
        });
            
        return response()->json(['data' => $data]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:0,1,2']);

        try {
            $record = CustomerSampleRequest::findOrFail($id);
            $record->status = $request->status;
            $record->save();

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false], 500);
        }
    }

    public function showCustomerRequest($id)
    {
        try {
            $record = CustomerSampleRequest::with('items')->findOrFail($id);
            return response()->json(['data' => $record]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Record not found'], 404);
        }
    }

    public function updateCustomerRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'fecha_solicitud'    => 'nullable|date',
            'fecha_recoleccion'  => 'nullable|date',
            'cliente_nombre'     => 'required|string|max:255',
            'cliente_direccion'  => 'nullable|string',
            'cliente_correo'     => 'nullable|email',
            'cliente_telefono'   => 'nullable|string',
            'cliente_estatus'    => 'nullable|string',
            'paq_nombre'         => 'nullable|string',
            'paq_guia'           => 'nullable|string',
            'observaciones'      => 'nullable|string',
            'solicitante_nombre' => 'nullable|string',
            'entrega_otro_txt'   => 'nullable|string',
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.product_id'         => ['required', 'exists:products,product_id'],
            'items.*.sku'                => ['nullable', 'string', 'max:100'],
            'items.*.um'                 => ['nullable', 'string', 'max:50'],
            'items.*.cantidad'           => ['nullable', 'string', 'max:100'],
            'items.*.pres_ziploc'        => ['nullable'],
            'items.*.pres_whirlpak'      => ['nullable'],
            'items.*.pres_metalizada'    => ['nullable'],
            'items.*.pres_frasco'        => ['nullable'],
            'items.*.pres_bidon'         => ['nullable'],
            'items.*.pres_otro'          => ['nullable'],
            'items.*.pres_otro_txt'      => ['nullable', 'string', 'max:255'],
            'items.*.lote_almacen'       => ['nullable', 'string', 'max:100'],
            'items.*.lote_venta'         => ['nullable', 'string', 'max:100'],
            'items.*.docs_cc'            => ['nullable'],
            'items.*.docs_ft'            => ['nullable'],
            'items.*.docs_hs'            => ['nullable'],
            'items.*.docs_otro'          => ['nullable'],
            'items.*.docs_otro_txt'      => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $row = DB::transaction(function () use ($validated, $request, $id) {
                $record = CustomerSampleRequest::findOrFail($id);
                $data = $validated;
                unset($data['items']);

                $checkboxes = [
                    'entrega_paqueteria', 'entrega_personal_empresa', 'entrega_recoleccion_planta', 'entrega_otro'
                ];

                foreach ($checkboxes as $field) {
                    $data[$field] = $request->has($field) ? 1 : 0;
                }

                $record->update($data);

                $record->items()->delete();
                $items = $request->input('items', []);
                foreach ($items as $item) {
                    $itemData = $item;
                    $itemCheckboxes = [
                        'pres_ziploc', 'pres_whirlpak', 'pres_metalizada', 'pres_frasco', 'pres_bidon', 'pres_otro',
                        'docs_cc', 'docs_ft', 'docs_hs', 'docs_otro'
                    ];
                    foreach ($itemCheckboxes as $field) {
                        $itemData[$field] = isset($item[$field]) && filter_var($item[$field], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                    }
                    if (!$itemData['pres_otro']) $itemData['pres_otro_txt'] = null;
                    if (!$itemData['docs_otro']) $itemData['docs_otro_txt'] = null;

                    $record->items()->create($itemData);
                }
                
                // Load items to return in response
                $record->load('items');
                return $record;
            });

            return response()->json(['ok' => true, 'message' => 'Record updated', 'data' => $row]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyCustomerRequest($id) 
    {
        CustomerSampleRequest::findOrFail($id)->delete();
        return response()->json(['ok' => true]);
    }

      public function reprintPdf2($id) 
    {
        $record = CustomerSampleRequest::with('items.product')->findOrFail($id);
        $data = $record->toArray();

        if ($record->items && $record->items->isNotEmpty()) {
            $data['items'] = $record->items->map(function ($it) {
                $itemArr = $it->toArray();
                $itemArr['producto'] = $it->producto ?: ($it->product ? $it->product->name : 'N/A');
                $itemArr['sku'] = $it->sku ?: ($it->product ? $it->product->sku : '');
                return $itemArr;
            })->toArray();

            $first = $data['items'][0];
            $data['producto']        = $first['producto'] ?? '';
            $data['sku']             = $first['sku'] ?? '';
            $data['cantidad']        = $first['cantidad'] ?? '';
            $data['um']              = $first['um'] ?? '';
            $data['pres_ziploc']     = $first['pres_ziploc'] ?? 0;
            $data['pres_whirlpak']   = $first['pres_whirlpak'] ?? 0;
            $data['pres_metalizada'] = $first['pres_metalizada'] ?? 0;
            $data['pres_frasco']     = $first['pres_frasco'] ?? 0;
            $data['pres_bidon']      = $first['pres_bidon'] ?? 0;
            $data['pres_otro']       = $first['pres_otro'] ?? 0;
            $data['pres_otro_txt']   = $first['pres_otro_txt'] ?? '';
            $data['lote_almacen']    = $first['lote_almacen'] ?? '';
            $data['lote_venta']      = $first['lote_venta'] ?? '';
            $data['docs_cc']         = $first['docs_cc'] ?? 0;
            $data['docs_ft']         = $first['docs_ft'] ?? 0;
            $data['docs_hs']         = $first['docs_hs'] ?? 0;
            $data['docs_otro']       = $first['docs_otro'] ?? 0;
            $data['docs_otro_txt']   = $first['docs_otro_txt'] ?? '';
        } elseif (!empty($record->product_id)) {
            $prod = $record->product ?? \App\Models\Product::find($record->product_id);
            $legacyItem = [
                'producto'        => $prod ? $prod->name : ($record->producto ?? 'N/A'),
                'sku'             => $record->sku ?: ($prod ? $prod->sku : ''),
                'cantidad'        => $record->cantidad ?? '',
                'um'              => $record->um ?? '',
                'pres_ziploc'     => $record->pres_ziploc ?? 0,
                'pres_whirlpak'   => $record->pres_whirlpak ?? 0,
                'pres_metalizada' => $record->pres_metalizada ?? 0,
                'pres_frasco'     => $record->pres_frasco ?? 0,
                'pres_bidon'      => $record->pres_bidon ?? 0,
                'pres_otro'       => $record->pres_otro ?? 0,
                'pres_otro_txt'   => $record->pres_otro_txt ?? '',
                'lote_almacen'    => $record->lote_almacen ?? '',
                'lote_venta'      => $record->lote_venta ?? '',
                'docs_cc'         => $record->docs_cc ?? 0,
                'docs_ft'         => $record->docs_ft ?? 0,
                'docs_hs'         => $record->docs_hs ?? 0,
                'docs_otro'       => $record->docs_otro ?? 0,
                'docs_otro_txt'   => $record->docs_otro_txt ?? '',
            ];
            $data['items'] = [$legacyItem];
            $data['producto'] = $legacyItem['producto'];
            $data['sku']      = $legacyItem['sku'];
        } else {
            $data['items'] = [];
        }

        $pdf = Pdf::loadView('formats.laboratory.02', $data)->setPaper('letter');
        return $pdf->stream("Solicitud_{$record->folio}.pdf");
    }
    public function muestrasIndex()
    {
        $products = \App\Models\Product::all(); 
        $customers = \App\Models\Customer::all();

        $batchesByProduct = \DB::table('inventory')
            ->select('product_id', 'batch')
            ->whereNotNull('batch')
            ->whereRaw("TRIM(batch) <> ''")
            ->distinct()
            ->get()
            ->groupBy('product_id')
            ->map(fn($g) => $g->pluck('batch')->values());

        return view('samples_index', compact('products', 'customers', 'batchesByProduct'));
    }

    public function pdf3(Request $request)
    {
        $validated = $request->validate([
            'folio_muestra'               => ['nullable','string','max:50'],
            'product_id'                  => ['required','integer','exists:products,product_id'],
            'fecha_salida'                => ['nullable','date'],
            'nombre_comercial'            => ['nullable','string','max:255'],
            'sku'                         => ['nullable','string','max:100'],
            'lote'                        => ['nullable','string','max:100'],
            'um'                          => ['nullable','string','max:50'], 
            'cantidad'                    => ['nullable','string','max:50'],
            'descripcion'                 => ['nullable','string','max:2000'],
            'motivo_salida'               => ['nullable','in:cliente,analisis,desarrollo,caducado,exposicion,otro'],
            'motivo_otro'                 => ['nullable','string','max:255'],
            'entrega_paqueteria'          => ['nullable'],
            'entrega_recoleccion_planta'  => ['nullable'],
            'entrega_personal_empresa'    => ['nullable'],
            'entrega_otro'                => ['nullable'],
            'entrega_otro_txt'            => ['nullable','string','max:255'],
            'paq_empresa'                 => ['nullable','string','max:255'],
            'paq_guia'                    => ['nullable','string','max:100'],
            'dest_nombre'                 => ['nullable','string','max:255'],
            'dest_direccion'              => ['nullable','string','max:500'],
            'dest_recibe'                 => ['nullable','string','max:255'],
            'dest_correo'                 => ['nullable','email','max:255'],
            'dest_telefono'               => ['nullable','string','max:50'],
            'docs_cc'                     => ['nullable'],
            'docs_ft'                     => ['nullable'],
            'docs_hs'                     => ['nullable'],
            'docs_otro'                   => ['nullable'],
            'docs_otro_txt'               => ['nullable','string','max:255'],
            'observaciones'               => ['nullable','string','max:5000'],
            'solicitante_nombre'          => ['nullable','string','max:255'],
            'recolector_nombre'           => ['nullable','string','max:255'],
            'autoriza_nombre'             => ['nullable','string','max:255'],
        ]);

        $b = fn(string $k) => $request->boolean($k);
        $validated['entrega_paqueteria']         = $b('entrega_paqueteria');
        $validated['entrega_recoleccion_planta'] = $b('entrega_recoleccion_planta');
        $validated['entrega_personal_empresa']   = $b('entrega_personal_empresa');
        $validated['entrega_otro']               = $b('entrega_otro');
        $validated['docs_cc']                    = $b('docs_cc');
        $validated['docs_ft']                    = $b('docs_ft');
        $validated['docs_hs']                    = $b('docs_hs');
        $validated['docs_otro']                  = $b('docs_otro');

        if (($validated['motivo_salida'] ?? null) !== 'otro') $validated['motivo_otro'] = null;
        if (!$validated['entrega_otro']) $validated['entrega_otro_txt'] = null;
        if (!$validated['docs_otro'])    $validated['docs_otro_txt'] = null;

        $product     = \App\Models\Product::find($validated['product_id']);
        $productName = $product->name ?? '';
        $sku         = $validated['sku'] ?? ($product->sku ?? '');

        $fmtDate = function ($v) {
            if (!$v) return '';
            try { return \Carbon\Carbon::parse($v)->format('d/m/Y'); } catch (\Throwable $e) { return ''; }
        };

        $fechaSalidaGuardar = $validated['fecha_salida']
            ? \Carbon\Carbon::parse($validated['fecha_salida'])->toDateString()
            : now()->toDateString();

        $salidaG = (float)str_replace(',', '', $validated['cantidad'] ?? 0);

        try {
            \DB::transaction(function () use ($validated, $fechaSalidaGuardar, $salidaG, $productName, $sku, $request) {
                
                $tableLab = 'laboratory_samples';
                
                $q = \DB::table($tableLab)->lockForUpdate();
                
                if (!empty($validated['folio_muestra'])) {
                    $q->where('folio', $validated['folio_muestra']);
                } elseif (!empty($sku)) {
                    $q->where('sku', $sku);
                } else {
                    $q->where('producto', $productName);
                }

                $row = $q->where('stock_final', '>', 0)
                        ->orderByDesc('id')
                        ->first();

                if (!$row) {
                    throw new \Exception("No hay stock disponible para el producto: $productName");
                }

                $stockActual = (float)$row->stock_final;
                $loQueYaHabiaSalido = (float)($row->cantidad_salida ?? 0);

                if ($salidaG > $stockActual) {
                    throw new \Exception("La salida solicitada ($salidaG) es mayor al stock disponible ($stockActual).");
                }

                $nuevaSalidaTotal = round($loQueYaHabiaSalido + $salidaG, 2);
                $newStatus = ($nuevaSalidaTotal >= (float)$row->stock_inicial || ($stockActual - $salidaG) <= 0)
                    ? 'Fuera de laboratorio'
                    : ($row->status ?? 'Fuera de laboratorio');

                \DB::table($tableLab)->where('id', $row->id)->update([
                    'cantidad_salida' => $nuevaSalidaTotal,
                    'fecha_salida'    => $fechaSalidaGuardar,
                    'motivo_salida'   => $validated['motivo_salida'] ?? $row->motivo_salida,
                    'cliente'         => $validated['dest_nombre'] ?? $row->cliente,
                    'status'          => $newStatus,
                    'updated_at'      => now(),
                ]);

                \DB::table('salida_muestras')->insert([
                    'folio_muestra'               => $validated['folio_muestra'],
                    'product_id'                  => $validated['product_id'],
                    'fecha_salida'                => $fechaSalidaGuardar,
                    'nombre_comercial'            => $validated['nombre_comercial'],
                    'sku'                         => $sku,
                    'lote'                        => $validated['lote'],
                    'um'                          => $validated['um'],
                    'cantidad'                    => $validated['cantidad'],
                    'descripcion'                 => $validated['descripcion'],
                    'motivo_salida'               => $validated['motivo_salida'],
                    'motivo_otro'                 => $validated['motivo_otro'],
                    'entrega_paqueteria'          => $validated['entrega_paqueteria'] ? 1 : 0,
                    'entrega_recoleccion_planta'  => $validated['entrega_recoleccion_planta'] ? 1 : 0,
                    'entrega_personal_empresa'    => $validated['entrega_personal_empresa'] ? 1 : 0,
                    'entrega_otro'                => $validated['entrega_otro'] ? 1 : 0,
                    'entrega_otro_txt'            => $validated['entrega_otro_txt'],
                    'paq_empresa'                 => $validated['paq_empresa'],
                    'paq_guia'                    => $validated['paq_guia'],
                    'dest_nombre'                 => $validated['dest_nombre'],
                    'dest_direccion'              => $validated['dest_direccion'],
                    'dest_recibe'                 => $validated['dest_recibe'],
                    'dest_correo'                 => $validated['dest_correo'],
                    'dest_telefono'               => $validated['dest_telefono'],
                    'docs_cc'                     => $validated['docs_cc'] ? 1 : 0,
                    'docs_ft'                     => $validated['docs_ft'] ? 1 : 0,
                    'docs_hs'                     => $validated['docs_hs'] ? 1 : 0,
                    'docs_otro'                   => $validated['docs_otro'] ? 1 : 0,
                    'docs_otro_txt'               => $validated['docs_otro_txt'],
                    'created_at'                  => now(),
                    'updated_at'                  => now(),
                ]);
            });

            $data = array_merge($validated, [
                'pagina_actual' => 1,
                'paginas_total' => 1,
                'producto'      => $productName,
                'fecha_salida'  => $fmtDate($validated['fecha_salida'] ?? null),
            ]);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('formats.laboratory.03', $data)->setPaper('letter');
            $fileName = 'Salida_' . \Str::slug($productName) . '_' . now()->format('Ymd_His') . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function indexMuestras()
    {
        $products = \App\Models\Product::orderBy('name')->get(['product_id','name','sku']);

        $batchesByProduct = \DB::table('inventory')
            ->select('product_id','batch')
            ->whereNotNull('batch')
            ->whereRaw("TRIM(batch) <> ''")
            ->distinct()
            ->orderBy('product_id')
            ->orderBy('batch')
            ->get()
            ->groupBy('product_id')
            ->map(fn($g) => $g->pluck('batch')->values());

        return view('muestras.index', compact('products', 'batchesByProduct'));
    }

    public function getMuestras(Request $request)
    {
        try {
            $query = \DB::table('salida_muestras')
                ->leftJoin('products', 'salida_muestras.product_id', '=', 'products.product_id')
                ->select(
                    'salida_muestras.*', 
                    'products.name as nombre_producto_original' 
                );

            if ($request->filled('motivo')) {
                $query->where('salida_muestras.motivo_salida', $request->motivo);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('salida_muestras.folio_muestra', 'LIKE', "%$search%")
                    ->orWhere('salida_muestras.sku', 'LIKE', "%$search%")
                    ->orWhere('products.name', 'LIKE', "%$search%") 
                    ->orWhere('salida_muestras.nombre_comercial', 'LIKE', "%$search%");
                });
            }

            $muestras = $query->orderBy('salida_muestras.created_at', 'desc')->get();

            return response()->json(['muestras' => $muestras]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyMuestra($id)
    {
        try {
            $salida = \DB::table('salida_muestras')->where('id', $id)->first();

            if (!$salida) {
                return response()->json(['error' => 'No se encontró el registro de la muestra de salida.'], 404);
            }

            \DB::table('salida_muestras')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Muestra de salida eliminada correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Hubo un error al eliminar la muestra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateMuestra(Request $request, $id)
    {
        try {
            $salida = \DB::table('salida_muestras')->where('id', $id)->first();

            if (!$salida) {
                return response()->json(['error' => 'No se encontró el registro.'], 404);
            }

            $validated = $request->validate([
                'folio_muestra' => 'nullable|string|max:50',
                'product_id' => 'nullable|integer',
                'fecha_salida' => 'nullable|date',
                'nombre_comercial' => 'nullable|string|max:255',
                'lote' => 'nullable|string|max:255',
                'um' => 'nullable|string|max:20',
                'cantidad' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'motivo_salida' => 'nullable|string|max:255',
                'motivo_otro' => 'nullable|string|max:255',
                'entrega_paqueteria' => 'nullable|boolean',
                'entrega_recoleccion_planta' => 'nullable|boolean',
                'entrega_personal_empresa' => 'nullable|boolean',
                'entrega_otro' => 'nullable|boolean',
                'entrega_otro_txt' => 'nullable|string|max:255',
                'paq_empresa' => 'nullable|string|max:255',
                'paq_guia' => 'nullable|string|max:255',
                'dest_nombre' => 'nullable|string|max:255',
                'dest_direccion' => 'nullable|string|max:255',
                'dest_recibe' => 'nullable|string|max:255',
                'dest_correo' => 'nullable|email|max:255',
                'dest_telefono' => 'nullable|string|max:255',
                'docs_cc' => 'nullable|boolean',
                'docs_ft' => 'nullable|boolean',
                'docs_hs' => 'nullable|boolean',
                'docs_otro' => 'nullable|boolean',
                'docs_otro_txt' => 'nullable|string|max:255',
            ]);

            // Obtener sku si hay product_id
            $sku = $salida->sku;
            if (isset($validated['product_id']) && $validated['product_id'] != $salida->product_id) {
                $product = \App\Models\Product::find($validated['product_id']);
                if ($product) {
                    $sku = $product->sku;
                }
            }

            \DB::table('salida_muestras')->where('id', $id)->update([
                'folio_muestra' => $validated['folio_muestra'] ?? $salida->folio_muestra,
                'product_id' => $validated['product_id'] ?? $salida->product_id,
                'fecha_salida' => $validated['fecha_salida'] ?? $salida->fecha_salida,
                'nombre_comercial' => $validated['nombre_comercial'] ?? $salida->nombre_comercial,
                'sku' => $sku,
                'lote' => $validated['lote'] ?? $salida->lote,
                'um' => $validated['um'] ?? $salida->um,
                'cantidad' => $validated['cantidad'] ?? $salida->cantidad,
                'descripcion' => $validated['descripcion'] ?? $salida->descripcion,
                'motivo_salida' => $validated['motivo_salida'] ?? $salida->motivo_salida,
                'motivo_otro' => $validated['motivo_otro'] ?? $salida->motivo_otro,
                'entrega_paqueteria' => $request->has('entrega_paqueteria') ? 1 : 0,
                'entrega_recoleccion_planta' => $request->has('entrega_recoleccion_planta') ? 1 : 0,
                'entrega_personal_empresa' => $request->has('entrega_personal_empresa') ? 1 : 0,
                'entrega_otro' => $request->has('entrega_otro') ? 1 : 0,
                'entrega_otro_txt' => $validated['entrega_otro_txt'] ?? $salida->entrega_otro_txt,
                'paq_empresa' => $validated['paq_empresa'] ?? $salida->paq_empresa,
                'paq_guia' => $validated['paq_guia'] ?? $salida->paq_guia,
                'dest_nombre' => $validated['dest_nombre'] ?? $salida->dest_nombre,
                'dest_direccion' => $validated['dest_direccion'] ?? $salida->dest_direccion,
                'dest_recibe' => $validated['dest_recibe'] ?? $salida->dest_recibe,
                'dest_correo' => $validated['dest_correo'] ?? $salida->dest_correo,
                'dest_telefono' => $validated['dest_telefono'] ?? $salida->dest_telefono,
                'docs_cc' => $request->has('docs_cc') ? 1 : 0,
                'docs_ft' => $request->has('docs_ft') ? 1 : 0,
                'docs_hs' => $request->has('docs_hs') ? 1 : 0,
                'docs_otro' => $request->has('docs_otro') ? 1 : 0,
                'docs_otro_txt' => $validated['docs_otro_txt'] ?? $salida->docs_otro_txt,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Muestra actualizada correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Hubo un error al actualizar la muestra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reimprimirPdf($id)
    {
        $salida = \DB::table('salida_muestras')
            ->leftJoin('products', 'salida_muestras.product_id', '=', 'products.product_id')
            ->select('salida_muestras.*', 'products.name as nombre_producto_real')
            ->where('salida_muestras.id', $id)
            ->first();

        if (!$salida) {
            abort(404, 'Registro no encontrado');
        }

        $data = [
            'pagina_actual'      => 1,
            'paginas_total'      => 1,
            'folio_muestra'      => $salida->folio_muestra,
            'producto'           => $salida->nombre_producto_real ?: $salida->nombre_comercial,
            'fecha_salida'       => $salida->fecha_salida ? \Carbon\Carbon::parse($salida->fecha_salida)->format('d/m/Y') : '',
            'nombre_comercial'   => $salida->nombre_comercial,
            'sku'                => $salida->sku,
            'lote'               => $salida->lote,
            'um'                 => $salida->um,
            'cantidad'           => $salida->cantidad,
            'descripcion'        => $salida->descripcion,
            'motivo_salida'      => $salida->motivo_salida,
            'motivo_otro'        => $salida->motivo_otro,
            'entrega_paqueteria' => $salida->entrega_paqueteria,
            'entrega_recoleccion_planta' => $salida->entrega_recoleccion_planta,
            'entrega_personal_empresa'   => $salida->entrega_personal_empresa,
            'entrega_otro'               => $salida->entrega_otro,
            'entrega_otro_txt'           => $salida->entrega_otro_txt,
            'paq_empresa'        => $salida->paq_empresa,
            'paq_guia'           => $salida->paq_guia,
            'dest_nombre'        => $salida->dest_nombre,
            'dest_direccion'     => $salida->dest_direccion,
            'dest_recibe'        => $salida->dest_recibe,
            'dest_correo'        => $salida->dest_correo,
            'dest_telefono'      => $salida->dest_telefono,
            'docs_cc'            => $salida->docs_cc,
            'docs_ft'            => $salida->docs_ft,
            'docs_hs'            => $salida->docs_hs,
            'docs_otro'          => $salida->docs_otro,
            'docs_otro_txt'      => $salida->docs_otro_txt,
            'observaciones'      => '', 
            'solicitante_nombre' => '',
            'recolector_nombre'  => '',
            'autoriza_nombre'    => '',
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('formats.laboratory.03', $data)->setPaper('letter');
        
        return $pdf->stream('Reimpresion_Salida_' . $salida->folio_muestra . '.pdf');
    }

    public function pdf5(Request $request)
    {
        $validated = $request->validate([
            'pagina_actual'  => ['nullable','integer','min:1'],
            'paginas_total'  => ['nullable','integer','min:1'],
            'fecha_muestreo'   => ['nullable','date'],
            'folio_muestra'    => ['nullable','string','max:100'], 
            'proveedor'        => ['nullable','string','max:255'],
            'lote'             => ['nullable','string','max:100'],
            'vida_anaquel'     => ['nullable','string','max:100'],
            'tipo_inspeccion'  => ['nullable','string','max:100'],
            'tipo_muestra'     => ['nullable','string','max:255'],
            'obs_tipo_muestra' => ['nullable','string','max:2000'],
            'observaciones_generales_muestra' => ['nullable','string','max:5000'], 
            'materia_extrana'       => ['nullable','in:presente,ausente,otro'],
            'materia_extrana_otro'  => ['nullable','string','max:255'],
            'obs_materia_extrana'   => ['nullable','string','max:2000'],
            'metodo_humedad'  => ['nullable','string','max:2000'],
            'obs_humedad'     => ['nullable','string','max:2000'],
            'metodo_proteina' => ['nullable','string','max:2000'],
            'obs_proteina'    => ['nullable','string','max:2000'],
            'determinacion_patogenos'      => ['nullable','string','max:5000'],
            'obs_determinacion_patogenos'  => ['nullable','string','max:2000'],
            'granulometria'   => ['nullable','array'],
            'granulometria.*' => ['nullable','string','max:10'],
            'gran_otro_check' => ['nullable'],
            'gran_otro'       => ['nullable','string','max:50'],
            'obs_granulometria' => ['nullable','string','max:2000'],
            'cumple_color'         => ['nullable','in:si,no'],
            'obs_color'            => ['nullable','string','max:2000'],
            'olor_texto'           => ['nullable','string','max:255'],
            'obs_olor'             => ['nullable','string','max:2000'],
            'sabor_texto'          => ['nullable','string','max:255'],
            'obs_sabor'            => ['nullable','string','max:2000'],
            'sensorial_otro_check' => ['nullable'],
            'sensorial_otro'       => ['nullable','string','max:255'],
            'observaciones_generales_intro' => ['nullable','string','max:5000'],
            'tabla_resultados'                   => ['nullable','array'],
            'tabla_resultados.*.folio'           => ['nullable','string','max:100'],
            'tabla_resultados.*.ret10'           => ['nullable','string','max:50'],
            'tabla_resultados.*.ret24'           => ['nullable','string','max:50'],
            'tabla_resultados.*.ret50'           => ['nullable','string','max:50'],
            'tabla_resultados.*.ret65'           => ['nullable','string','max:50'],
            'tabla_resultados.*.ret85'           => ['nullable','string','max:50'],
            'tabla_resultados.*.ret100'          => ['nullable','string','max:50'],
            'tabla_resultados.*.ret120'          => ['nullable','string','max:50'],
            'tabla_resultados.*.ret150'          => ['nullable','string','max:50'],
            'tabla_resultados.*.ret200'          => ['nullable','string','max:50'],
            'tabla_resultados.*.humedad'         => ['nullable','string','max:50'],
            'tabla_resultados.*.proteina'        => ['nullable','string','max:50'],
            'tabla_resultados.*.ph'              => ['nullable','string','max:50'],
            'tabla_resultados.*.microbiologicos' => ['nullable','string','max:255'],
            'tabla_resultados.*.sensorial'       => ['nullable','string','max:255'],
            'anexo_folios'        => ['nullable','string','max:2000'],
            'anexo_observaciones' => ['nullable','string','max:4000'],
            'evidencias'          => ['nullable','array'],
            'evidencias.*'        => ['nullable','file','image','mimes:jpeg,jpg,png,webp','max:3072'],
            'anexo_a_items'                    => ['nullable','array'],
            'anexo_a_items.*.evidencias'       => ['nullable','array'],
            'anexo_a_items.*.evidencias.*'     => ['nullable','file','image','mimes:jpeg,jpg,png,webp','max:3072'],
            'anexo_b_items'                    => ['nullable','array'],
            'anexo_b_items.*.evidencias'       => ['nullable','array'],
            'anexo_b_items.*.evidencias.*'     => ['nullable','file','image','mimes:jpeg,jpg,png,webp','max:3072'],
            'conclusiones'     => ['nullable','string','max:8000'],
            'realizo_nombre'   => ['nullable','string','max:255'],
            'realizo_puesto'   => ['nullable','string','max:255'],
            'reviso_nombre'    => ['nullable','string','max:255'],
            'reviso_puesto'    => ['nullable','string','max:255'],
        ]);

        if (($validated['materia_extrana'] ?? 'ausente') !== 'otro') $validated['materia_extrana_otro'] = null;
        if (!$request->boolean('gran_otro_check')) $validated['gran_otro'] = null;
        if (!$request->boolean('sensorial_otro_check')) $validated['sensorial_otro'] = null;

        $getBase64 = function($file) {
            if (!$file) return null;
            return 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        };

        $anexoAItems = [];
        if (is_array($request->input('anexo_a_items'))) {
            foreach ($request->input('anexo_a_items') as $i => $row) {
                $imgs = [];
                if ($request->hasFile("anexo_a_items.$i.evidencias")) {
                    foreach ($request->file("anexo_a_items.$i.evidencias") as $file) {
                        if ($file->isValid()) $imgs[] = $getBase64($file);
                    }
                }
                $anexoAItems[] = [
                    'muestra' => $row['muestra'] ?? '',
                    'observaciones' => $row['observaciones'] ?? '',
                    'evidencias' => $imgs
                ];
            }
        }

        $anexoBItems = [];
        if (is_array($request->input('anexo_b_items'))) {
            foreach ($request->input('anexo_b_items') as $i => $row) {
                $imgs = [];
                if ($request->hasFile("anexo_b_items.$i.evidencias")) {
                    foreach ($request->file("anexo_b_items.$i.evidencias") as $file) {
                        if ($file->isValid()) $imgs[] = $getBase64($file);
                    }
                }
                $anexoBItems[] = [
                    'muestra' => $row['muestra'] ?? '',
                    'observaciones' => $row['observaciones'] ?? '',
                    'evidencias' => $imgs
                ];
            }
        }

        $evidenciasGrales = [];
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                if ($file->isValid()) $evidenciasGrales[] = $getBase64($file);
            }
        }

        $meshMap = ['#10'=>'ret10', '#24'=>'ret24', '#50'=>'ret50', '#65'=>'ret65', '#85'=>'ret85', '#100'=>'ret100', '#120'=>'ret120', '#150'=>'ret150', '#200'=>'ret200'];
        $selMallas = collect($request->input('granulometria', []))->map(fn($v) => '#'.ltrim(trim($v), '#'))->filter(fn($m) => isset($meshMap[$m]))->values();
        $retKeys = $selMallas->map(fn($m) => $meshMap[$m])->all();

        $tabla = collect($validated['tabla_resultados'] ?? [])->map(function($r) use ($retKeys){
            $out = ['folio'=>$r['folio']??'', 'humedad'=>$r['humedad']??'', 'proteina'=>$r['proteina']??'', 'ph'=>$r['ph']??'', 'microbiologicos'=>$r['microbiologicos']??'', 'sensorial'=>$r['sensorial']??''];
            foreach ($retKeys as $k) { $out[$k] = $r[$k] ?? ''; }
            return $out;
        })->all();

        $data = array_merge($validated, [
            'tabla_resultados' => $tabla,
            'granulometria' => $selMallas->all(),
            'evidencias' => $evidenciasGrales,
            'anexo_a_items' => $anexoAItems,
            'anexo_b_items' => $anexoBItems,
        ]);

        $view = 'formats.laboratory.05';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)->setPaper('letter');
        
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->get_canvas();
        $font = $dompdf->getFontMetrics()->getFont('Helvetica', 'normal');
        $canvas->page_text(517, 59, "Pág. {PAGE_NUM} de {PAGE_COUNT}", $font, 7, [0,0,0]);

        $slug = \Illuminate\Support\Str::slug($validated['proveedor'] ?: 'AnalisisInterno', '-');
        $fileName = 'AnalisisInterno_' . $slug . '_' . now()->format('d_m_Y_His') . '.pdf';

        return $pdf->stream($fileName);
    }
    
    public function pdf6(Request $request)
    {
        $validated = $request->validate([
            'pagina_actual'        => ['nullable','integer','min:1'],
            'paginas_total'        => ['nullable','integer','min:1'],
            'fecha_ingreso'        => ['nullable','date'],
            'fecha_emision'        => ['nullable','date'],
            'cliente_nombre'       => ['nullable','string','max:255'],
            'cliente_ciudad'       => ['nullable','string','max:255'],
            'cliente_direccion'    => ['nullable','string','max:500'],
            'cliente_telefono'     => ['nullable','string','max:50'],
            'cultivo'              => ['nullable','string','max:255'],
            'sistema'              => ['nullable','string','max:255'],
            'tipo_planta'          => ['nullable','string','max:255'],
            'peso_muestra'         => ['nullable','string','max:50'],
            'testigo'              => ['nullable'],
            'ubicacion'            => ['nullable','string','max:255'],
            'tipo_muestreo'        => ['nullable','string','max:255'],
            'responsable_muestreo' => ['nullable','string','max:255'],
            'proposito'            => ['nullable','string','max:1000'],
            'convencionales'       => ['nullable','array'],
            'convencionales.*.v'   => ['nullable','string','max:255'],
            'convencionales.*.r'   => ['nullable','string','max:255'],
            'convencionales.*.u'   => ['nullable','string','max:50'],
            'n_val'                => ['nullable','numeric'],
            'p_val'                => ['nullable','numeric'],
            'k_val'                => ['nullable','numeric'],
            'arena'                => ['nullable','numeric'],
            'limo'                 => ['nullable','numeric'],
            'arcilla'              => ['nullable','numeric'],
            'clasificacion'        => ['nullable','string','max:255'],
            'triangulo_src'        => ['nullable','string','max:1000'],
            'imagen_evidencia'     => ['nullable','file','image','mimes:jpeg,jpg,png','max:3072'], // 3 MB
            'imagen_titulo'        => ['nullable','string','max:255'],
        ]);

        $validated['testigo'] = $request->boolean('testigo');

        $convRows = collect($validated['convencionales'] ?? [])
            ->filter(fn($r) => is_array($r) && trim(($r['v'] ?? '').($r['r'] ?? '').($r['u'] ?? '')) !== '')
            ->values()
            ->all();

        $dataUri = null;
        $imgMime = null;
        $imgKb   = null;

        if ($request->hasFile('imagen_evidencia')) {
            $file   = $request->file('imagen_evidencia');
            $imgMime = $file->getMimeType() ?: 'image/jpeg';
            $bin     = file_get_contents($file->getRealPath());
            $dataUri = 'data:' . $imgMime . ';base64,' . base64_encode($bin);
            $imgKb   = (int) round(filesize($file->getRealPath()) / 1024);
        }

        $analysisId = null;
        $reportCode = null;

        DB::transaction(function () use ($validated, $convRows, $dataUri, $imgMime, $imgKb, &$analysisId, &$reportCode) {
            $analysisId = DB::table('soil_internal_analyses')->insertGetId([
                'report_code'          => null,
                'entry_date'           => $validated['fecha_ingreso'] ?? null,
                'issue_date'           => $validated['fecha_emision'] ?? null,
                'client_name'          => $validated['cliente_nombre'] ?? null,
                'client_city'          => $validated['cliente_ciudad'] ?? null,
                'client_address'       => $validated['cliente_direccion'] ?? null,
                'client_phone'         => $validated['cliente_telefono'] ?? null,
                'crop_type'            => $validated['cultivo'] ?? null,
                'system_type'          => $validated['sistema'] ?? null,
                'plant_type'           => $validated['tipo_planta'] ?? null,
                'sample_weight'        => $validated['peso_muestra'] ?? null,
                'is_control'           => $validated['testigo'] ? 1 : 0,
                'location'             => $validated['ubicacion'] ?? null,
                'sampling_type'        => $validated['tipo_muestreo'] ?? null,
                'sampling_responsible' => $validated['responsable_muestreo'] ?? null,
                'purpose'              => $validated['proposito'] ?? null,
                'n_value_mgkg'         => $validated['n_val'] ?? null,
                'p_value_mgkg'         => $validated['p_val'] ?? null,
                'k_value_mgkg'         => $validated['k_val'] ?? null,
                'sand_pct'             => $validated['arena'] ?? null,
                'silt_pct'             => $validated['limo'] ?? null,
                'clay_pct'             => $validated['arcilla'] ?? null,
                'classification'       => $validated['clasificacion'] ?? null,
                'triangle_src'         => $validated['triangulo_src'] ?? null,
                'image_path'           => $dataUri, 
                'image_caption'        => $validated['imagen_titulo'] ?? null,
                'image_mime'           => $imgMime,
                'image_size_kb'        => $imgKb,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            $reportCode = 'S' . str_pad(max($analysisId - 1, 0), 4, '0', STR_PAD_LEFT);

            DB::table('soil_internal_analyses')
                ->where('id', $analysisId)
                ->update([
                    'report_code' => $reportCode,
                    'updated_at'  => now(),
                ]);

            foreach ($convRows as $i => $r) {
                DB::table('soil_conventional_variables')->insert([
                    'soil_analysis_id' => $analysisId,
                    'variable_name'    => $r['v'] ?? null,
                    'result_text'      => $r['r'] ?? null,
                    'unit_text'        => $r['u'] ?? null,
                    'position_order'   => $i + 1,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        });

        $data = [
            'pagina_actual'        => (int)($validated['pagina_actual'] ?? 1),
            'paginas_total'        => (int)($validated['paginas_total'] ?? 2),
            'reporte'              => $reportCode,
            'fecha_ingreso'        => $validated['fecha_ingreso'] ?? '',
            'fecha_emision'        => $validated['fecha_emision'] ?? '',
            'cliente_nombre'       => $validated['cliente_nombre'] ?? '',
            'cliente_ciudad'       => $validated['cliente_ciudad'] ?? '',
            'cliente_direccion'    => $validated['cliente_direccion'] ?? '',
            'cliente_telefono'     => $validated['cliente_telefono'] ?? '',
            'cultivo'              => $validated['cultivo'] ?? '',
            'sistema'              => $validated['sistema'] ?? '',
            'tipo_planta'          => $validated['tipo_planta'] ?? '',
            'peso_muestra'         => $validated['peso_muestra'] ?? '',
            'testigo'              => $validated['testigo'],
            'ubicacion'            => $validated['ubicacion'] ?? '',
            'tipo_muestreo'        => $validated['tipo_muestreo'] ?? '',
            'responsable_muestreo' => $validated['responsable_muestreo'] ?? '',
            'proposito'            => $validated['proposito'] ?? '',
            'convencionales'       => $convRows,
            'n_val'                => $validated['n_val'] ?? null,
            'p_val'                => $validated['p_val'] ?? null,
            'k_val'                => $validated['k_val'] ?? null,
            'arena'                => $validated['arena'] ?? null,
            'limo'                 => $validated['limo'] ?? null,
            'arcilla'              => $validated['arcilla'] ?? null,
            'clasificacion'        => $validated['clasificacion'] ?? '',
            'triangulo_src'        => $validated['triangulo_src'] ?? '',
            'image_path'           => $dataUri, 
            'image_caption'        => $validated['imagen_titulo'] ?? '',
            'analysis_id'          => $analysisId,
        ];

        $view = 'formats.laboratory.06';
        $pdf  = Pdf::loadView($view, $data)->setPaper('letter');

        $slug = Str::slug(($data['cliente_nombre'] ?: 'AnalisisSuelo'), '-');
        $fileName = 'AnalisisSuelo_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function pdf9(Request $request)
    {
        $validated = $request->validate([
            'fecha_solicitud' => ['nullable','date'],
            'propuesta_nombre'=> ['nullable','string','max:255'],
            'aplicacion'      => ['nullable','array'],
            'aplicacion.*'    => ['in:agricola,pecuario,petfood,otro'],
            'ap_otro'         => ['nullable','string','max:255'],
            'uso_especifico'  => ['nullable','string','max:255'],
            'uso_otro'        => ['nullable','string','max:255'],
            'objetivo'        => ['nullable','string','max:5000'],
            'info_relevante'  => ['nullable','string','max:5000'],
            'composicion'     => ['nullable','string','max:8000'],
            'resultados'      => ['nullable','string','max:5000'],
            'autorizacion'                 => ['nullable','array'],
            'autorizacion.solvencia'       => ['nullable'],
            'autorizacion.solvencia_obs'   => ['nullable','string','max:1000'],
            'autorizacion.insumos'         => ['nullable'],
            'autorizacion.insumos_obs'     => ['nullable','string','max:1000'],
            'autorizacion.modo_tiempo'     => ['nullable'],
            'autorizacion.modo_tiempo_obs' => ['nullable','string','max:1000'],
            'autorizacion.viabilidad'      => ['nullable'],
            'autorizacion.viabilidad_obs'  => ['nullable','string','max:1000'],
            'anexos'         => ['nullable','string','max:8000'],
            'firmo_nombre'   => ['nullable','string','max:255'],
            'reviso_nombre'  => ['nullable','string','max:255'],
            'autorizo_nombre'=> ['nullable','string','max:255'],
        ]);

        $aplicacion = (array)($validated['aplicacion'] ?? []);
        $aut        = (array)($validated['autorizacion'] ?? []);
        $aut['solvencia']    = !empty($aut['solvencia']);
        $aut['insumos']      = !empty($aut['insumos']);
        $aut['modo_tiempo']  = !empty($aut['modo_tiempo']);
        $aut['viabilidad']   = !empty($aut['viabilidad']);

        if (!in_array('otro', $aplicacion, true)) {
            $validated['ap_otro'] = null;
        }

        $data = [
            'fecha_solicitud' => $validated['fecha_solicitud'] ?? '',
            'propuesta_nombre'=> $validated['propuesta_nombre'] ?? '',
            'aplicacion'      => $aplicacion,
            'ap_otro'         => $validated['ap_otro'] ?? '',
            'uso_especifico'  => $validated['uso_especifico'] ?? '',
            'uso_otro'        => $validated['uso_otro'] ?? '',
            'objetivo'        => $validated['objetivo'] ?? '',
            'info_relevante'  => $validated['info_relevante'] ?? '',
            'composicion'     => $validated['composicion'] ?? '',
            'resultados'      => $validated['resultados'] ?? '',
            'autorizacion'    => [
                'solvencia'       => $aut['solvencia'] ?? false,
                'solvencia_obs'   => $aut['solvencia_obs'] ?? '',
                'insumos'         => $aut['insumos'] ?? false,
                'insumos_obs'     => $aut['insumos_obs'] ?? '',
                'modo_tiempo'     => $aut['modo_tiempo'] ?? false,
                'modo_tiempo_obs' => $aut['modo_tiempo_obs'] ?? '',
                'viabilidad'      => $aut['viabilidad'] ?? false,
                'viabilidad_obs'  => $aut['viabilidad_obs'] ?? '',
            ],
            'anexos'          => $validated['anexos'] ?? '',
            'firmo_nombre'    => $validated['firmo_nombre'] ?? '',
            'reviso_nombre'   => $validated['reviso_nombre'] ?? '',
            'autorizo_nombre' => $validated['autorizo_nombre'] ?? '',
        ];

        $view = 'formats.laboratory.09';
        $pdf  = Pdf::loadView($view, $data)->setPaper('letter');

        $slug = Str::slug($data['propuesta_nombre'] ?: 'SolicitudFormulacion', '-');
        $fileName = 'SolicitudFormulacion_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        //return $pdf->stream($fileName);
 
        return $pdf->download($fileName);
    }

    public function pdf10(Request $request)
    {
        $validated = $request->validate([
            'fecha_formulacion'   => ['nullable','date'],
            'sku'                 => ['nullable','string','max:100'],
            'lote'                => ['nullable','string','max:100'],
            'pagina_actual'       => ['nullable','integer','min:1'],
            'paginas_total'       => ['nullable','integer','min:1'],
            'nombre_producto'     => ['required','string','max:255'],
            'aplicacion'          => ['nullable','array'],
            'aplicacion.*'        => ['in:agricola,pecuario,petfood,otro'],
            'ap_otro'             => ['nullable','string','max:255'],
            'uso_especifico'      => ['nullable','string','max:255'],
            'uso_otro'            => ['nullable','string','max:255'],
            'total_produccion_kg' => ['nullable','numeric','min:0'],
            'materias_primas'     => ['nullable','array'],
            'materias_primas.*.nombre'      => ['nullable','string','max:255'],
            'materias_primas.*.porcentaje'  => ['nullable','numeric','min:0'],
            'materias_primas.*.cantidad_kg' => ['nullable','numeric','min:0'],
            'materias_primas.*.obs'         => ['nullable','string','max:1000'],
            'vida_anaquel'        => ['nullable','string','max:255'],
            'obs_producto'        => ['nullable','string','max:5000'],
            'recomendaciones'     => ['nullable','string','max:8000'],
            'recom_etiqueta'      => ['nullable','string','max:8000'],
            'anexos'              => ['nullable','string','max:8000'],
        ]);

        $aplicacion = (array)($validated['aplicacion'] ?? []);
        if (!in_array('otro', $aplicacion, true)) {
            $validated['ap_otro'] = null;
        }

        $totalKg = isset($validated['total_produccion_kg'])
            ? (float)$validated['total_produccion_kg']
            : 1000.0;

        $items = array_values((array)($validated['materias_primas'] ?? []));
        $sumPor = 0.0;
        $sumKg  = 0.0;

        foreach ($items as $i => $it) {
            $nombre = trim($it['nombre'] ?? '');
            $por    = isset($it['porcentaje']) ? (float)$it['porcentaje'] : null;
            $kg     = isset($it['cantidad_kg']) ? (float)$it['cantidad_kg'] : null;
            $obs    = $it['obs'] ?? '';

            if ($por !== null && ($it['cantidad_kg'] === null || $it['cantidad_kg'] === '')) {
                $kg = round(($por / 100.0) * $totalKg, 3);
            }

            $items[$i] = [
                'nombre'     => $nombre,
                'porcentaje' => $por,
                'cantidad_kg'=> $kg,
                'obs'        => $obs,
            ];

            $sumPor += (float)($por ?? 0);
            $sumKg  += (float)($kg ?? 0);
        }

        $data = [
            'fecha_formulacion'   => $validated['fecha_formulacion'] ?? '',
            'sku'                 => $validated['sku'] ?? '',
            'lote'                => $validated['lote'] ?? '',
            'pagina_actual'       => (int)($validated['pagina_actual'] ?? 1),
            'paginas_total'       => (int)($validated['paginas_total'] ?? 2),
            'nombre_producto'     => $validated['nombre_producto'] ?? '',
            'aplicacion'          => $aplicacion,
            'ap_otro'             => $validated['ap_otro'] ?? '',
            'uso_especifico'      => $validated['uso_especifico'] ?? '',
            'uso_otro'            => $validated['uso_otro'] ?? '',
            'total_produccion_kg' => $totalKg,
            'materias_primas'     => $items,
            'sum_por'             => $sumPor,
            'sum_kg'              => $sumKg,
            'vida_anaquel'        => $validated['vida_anaquel'] ?? '',
            'obs_producto'        => $validated['obs_producto'] ?? '',
            'recomendaciones'     => $validated['recomendaciones'] ?? '',
            'recom_etiqueta'      => $validated['recom_etiqueta'] ?? '',
            'anexos'              => $validated['anexos'] ?? '',
        ];

        $view = 'formats.laboratory.10';
        $pdf  = Pdf::loadView($view, $data)->setPaper('letter');

        $slug = Str::slug($data['nombre_producto'] ?: 'Formulacion', '-');
        $fileName = 'Formulacion_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        //return $pdf->stream($fileName);

        return $pdf->download($fileName);
    }

    public function pdf11(Request $request)
    {
        $validated = $request->validate([
            'semana_rango'        => ['nullable','string','max:120'],
            'fecha_revision'      => ['nullable','date'],
            'proyecto'            => ['nullable','string','max:255'],
            'responsable'         => ['nullable','string','max:255'],
            'objetivos'               => ['nullable','array'],
            'objetivos.*.n'           => ['nullable','integer','min:1'],
            'objetivos.*.titulo'      => ['nullable','string','max:255'],
            'objetivos.*.descripcion' => ['nullable','string','max:2000'],
            'objetivos.*.horas'       => ['nullable','integer','min:0'],
            'resultados'            => ['nullable','array'],
            'resultados.*.n'        => ['nullable'],
            'resultados.*.texto'    => ['nullable','string','max:5000'],
            'resultados.*.cumple'   => ['nullable'],
            'hallazgos'               => ['nullable','array'],
            'hallazgos.*.hallazgo'    => ['nullable','string','max:2000'],
            'hallazgos.*.causa'       => ['nullable','string','max:2000'],
            'hallazgos.*.propuesta'   => ['nullable','string','max:2000'],
            'proxima_semana_rango' => ['nullable','string','max:120'],
            'plan_proxima'         => ['nullable','array'],
            'plan_proxima.*.plan'      => ['nullable','string','max:2000'],
            'plan_proxima.*.acciones'  => ['nullable','string','max:2000'],
        ]);

        $objetivos  = array_values($validated['objetivos'] ?? []);
        $resultados = array_values($validated['resultados'] ?? []);
        $hallazgos  = array_values($validated['hallazgos'] ?? []);
        $planNext   = array_values($validated['plan_proxima'] ?? []);
        $total_horas = 0;
        foreach ($objetivos as &$o) {
            $o['n']        = isset($o['n']) ? (int)$o['n'] : null;
            $o['titulo']   = trim($o['titulo'] ?? '');
            $o['descripcion'] = trim($o['descripcion'] ?? '');
            $o['horas']    = isset($o['horas']) ? (int)$o['horas'] : 0;
            $total_horas  += $o['horas'];
        }
        unset($o);

        $mapResultados = [];
        foreach ($resultados as $r) {
            $key = (string)($r['n'] ?? '');
            if ($key !== '') {
                $mapResultados[$key] = [
                    'n'      => (int)$r['n'],
                    'texto'  => trim($r['texto'] ?? ''),
                    'cumple' => !empty($r['cumple']),
                ];
            }
        }

        $resultados_alineados = [];
        foreach ($objetivos as $i => $o) {
            $n = (string)($o['n'] ?? ($i+1));
            $res = $mapResultados[$n] ?? ['n' => (int)$n, 'texto' => '', 'cumple' => false];
            $resultados_alineados[] = $res;
        }

        $planId = null;
        DB::transaction(function () use ($validated, $objetivos, $resultados_alineados, $hallazgos, $planNext, $total_horas, &$planId) {

            $planId = DB::table('weekly_work_plans')->insertGetId([
                'week_range'       => $validated['semana_rango'] ?? null,
                'review_date'      => $validated['fecha_revision'] ?? null,
                'project_name'     => $validated['proyecto'] ?? null,
                'responsible_name' => $validated['responsable'] ?? null,
                'total_hours'      => $total_horas,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            foreach ($objetivos as $i => $o) {
                if (empty($o['titulo']) && empty($o['descripcion']) && empty($o['n'])) continue;
                DB::table('weekly_objectives')->insert([
                    'plan_id'        => $planId,
                    'item_number'    => $o['n'] ?? ($i+1),
                    'title'          => $o['titulo'] ?? null,
                    'description'    => $o['descripcion'] ?? null,
                    'hours'          => $o['horas'] ?? 0,
                    'position_order' => $i + 1,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            foreach ($resultados_alineados as $i => $r) {
                DB::table('weekly_results')->insert([
                    'plan_id'          => $planId,
                    'objective_number' => $r['n'] ?? ($i+1),
                    'result_text'      => $r['texto'] ?? null,
                    'is_met'           => !empty($r['cumple']) ? 1 : 0,
                    'position_order'   => $i + 1,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            foreach ($hallazgos as $i => $h) {
                if (empty($h['hallazgo']) && empty($h['causa']) && empty($h['propuesta'])) continue;
                DB::table('weekly_findings')->insert([
                    'plan_id'        => $planId,
                    'finding_text'   => $h['hallazgo']  ?? null,
                    'cause_text'     => $h['causa']     ?? null,
                    'proposal_text'  => $h['propuesta'] ?? null,
                    'position_order' => $i + 1,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            $nextRange = $validated['proxima_semana_rango'] ?? null;
            foreach ($planNext as $i => $p) {
                if (empty($p['plan']) && empty($p['acciones']) && empty($nextRange)) continue;
                DB::table('weekly_next_actions')->insert([
                    'plan_id'          => $planId,
                    'next_week_range'  => $nextRange,
                    'plan_text'        => $p['plan']     ?? null,
                    'actions_text'     => $p['acciones'] ?? null,
                    'position_order'   => $i + 1,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        });

        $plan = DB::table('weekly_work_plans')->where('id', $planId)->first();

        $objs = DB::table('weekly_objectives')
                ->where('plan_id', $planId)
                ->orderBy('position_order')->get();

        $res  = DB::table('weekly_results')
                ->where('plan_id', $planId)
                ->orderBy('position_order')->get();

        $find = DB::table('weekly_findings')
                ->where('plan_id', $planId)
                ->orderBy('position_order')->get();

        $next = DB::table('weekly_next_actions')
                ->where('plan_id', $planId)
                ->orderBy('position_order')->get();

        $data = [
            'pagina_actual' => 1,
            'paginas_total' => 3,
            'semana_rango'   => $plan->week_range,
            'fecha_revision' => $plan->review_date,
            'proyecto'       => $plan->project_name,
            'responsable'    => $plan->responsible_name,
            'objetivos'     => $objs->map(fn($o)=>[
                                    'n' => $o->item_number,
                                    'titulo' => $o->title,
                                    'descripcion' => $o->description,
                                    'horas' => $o->hours,
                            ])->toArray(),
            'total_horas'   => $plan->total_hours,
            'resultados'    => $res->map(fn($r)=>[
                                    'n' => $r->objective_number,
                                    'texto' => $r->result_text,
                                    'cumple' => (bool)$r->is_met,
                            ])->toArray(),
            'hallazgos'     => $find->map(fn($h)=>[
                                    'hallazgo' => $h->finding_text,
                                    'causa'    => $h->cause_text,
                                    'propuesta'=> $h->proposal_text,
                            ])->toArray(),
            'proxima_semana_rango' => ($next[0]->next_week_range ?? '') ?: ($validated['proxima_semana_rango'] ?? ''),
            'plan_proxima'         => $next->map(fn($n)=>[
                                    'plan'     => $n->plan_text,
                                    'acciones' => $n->actions_text,
                            ])->toArray(),
        ];

        $view = 'formats.laboratory.11';
        $pdf  = Pdf::loadView($view, $data)->setPaper('letter');

        $slug = Str::slug(($data['semana_rango'] ?: 'semana'), '-');
        $fileName = 'PlanSemanal_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function pdf12(Request $request) 
    {
        $folioInput = trim((string)($request->input('folio') ?? $request->input('folio_muestra')));
        $request->merge(['folio' => $folioInput !== '' ? $folioInput : null]);

        $validated = $request->validate([
            'asesor'               => ['nullable','string','max:255'],
            'productor_id'         => ['nullable','integer','exists:customers,customer_id'],
            'productor'            => ['nullable','string','max:255'],
            'cultivo'              => ['nullable','string','max:255'],
            'folio'                => ['nullable','string','max:100', Rule::exists('soil_internal_analyses','report_code')],
            'producto_aplicar'     => ['nullable','string','max:500'],
            'peso_text'            => [
                'nullable','string','max:50',
                'regex:/^\s*\d+(?:[.,]\d+)?\s*(?:kg|kilo(?:s)?|g|gr|gram(?:o|os)?)\s*$/i'
            ],
            'peso'                 => ['nullable','numeric','min:0'],
            'objetivo'             => ['nullable','string','max:5000'],
            'condiciones'          => ['nullable','string','max:5000'],
            'ubicacion'            => ['nullable','string','max:500'],
            'ubicacion_nombre'     => ['nullable','string','max:255'],
            'croquis_src'          => ['nullable','url','max:1000'],
            'division_bloques'     => ['nullable','string','max:5000'],
            'tratamiento'          => ['nullable','string','max:5000'],
            'dosis'                => ['nullable','array'],
            'dosis.*'              => ['nullable','string','max:500'],
            'fecha_aplicacion'     => ['nullable','date'],
            'doc_muestreo'         => ['nullable','string','max:255'],
            'fechas_muestreo'      => ['nullable','array'],
            'fechas_muestreo.antes'=> ['nullable','date'],
            'fechas_muestreo.15'   => ['nullable','date'],
            'fechas_muestreo.25'   => ['nullable','date'],
            'fechas_muestreo.35'   => ['nullable','date'],
            'variables_agro'       => ['nullable','string','max:5000'],
            'observaciones'        => ['nullable','string','max:5000'],
        ]);

        $dosis = collect($validated['dosis'] ?? [])->filter(fn($v) => filled($v))->values()->all();

        $productorName = '';
        if (!empty($validated['productor_id'])) {
            $productorName = \App\Models\Customer::where('customer_id', $validated['productor_id'])->value('name') ?? '';
        }
        if (!$productorName && !empty($validated['productor'])) {
            $productorName = (string) $validated['productor'];
        }

        $toDate = function ($v) {
            if (!$v) return null;
            try { return \Carbon\Carbon::parse($v)->toDateString(); }
            catch (\Throwable $e) { return null; }
        };
        $fechasRaw = $validated['fechas_muestreo'] ?? [];
        $fechasForDb = [
            'antes' => $toDate($fechasRaw['antes'] ?? null),
            '15'    => $toDate($fechasRaw['15'] ?? null),
            '25'    => $toDate($fechasRaw['25'] ?? null),
            '35'    => $toDate($fechasRaw['35'] ?? null),
        ];
        $fechaAplicacionDb = $toDate($validated['fecha_aplicacion'] ?? null);

        $parsePeso = function (?string $raw) {
            if (!$raw) return [null, null]; 
            $raw = trim($raw);
            if ($raw === '') return [null, null];
            if (!preg_match('/^\s*(\d+(?:[.,]\d+)?)\s*(kg|kilo(?:s)?|g|gr|gram(?:o|os)?)\s*$/i', $raw, $m)) {
                return [null, null];
            }
            $num  = (float) str_replace(',', '.', $m[1]);
            $unit = strtolower($m[2]);
            if (\Illuminate\Support\Str::startsWith($unit, 'k')) {
                return [$num, 'kg'];
            }
            return [$num / 1000, 'g'];
        };

        [$pesoKg, $pesoUnit] = $parsePeso($validated['peso_text'] ?? null);

        if ($pesoKg === null && isset($validated['peso']) && $validated['peso'] !== null) {
            $pesoKg   = (float) $validated['peso'];
            $pesoUnit = 'kg';
        }
        $folio = $validated['folio'] ?? null;

        $record = \DB::transaction(function () use ($validated, $dosis, $fechasForDb, $fechaAplicacionDb, $folio) {
            return \App\Models\LaboratoryMonitoring::create([
                'asesor'             => $validated['asesor']            ?? null,
                'productor_id'       => $validated['productor_id']      ?? null,
                'cultivo'            => $validated['cultivo']           ?? null,
                'folio'              => $folio,
                'producto_aplicar'   => $validated['producto_aplicar']  ?? null,
                'peso_text'          => isset($validated['peso_text']) ? trim($validated['peso_text']) : null, 
                'objetivo'           => $validated['objetivo']          ?? null,
                'condiciones'        => $validated['condiciones']       ?? null,
                'ubicacion'          => $validated['ubicacion']         ?? null,
                'ubicacion_nombre'   => $validated['ubicacion_nombre']  ?? null,
                'division_bloques'   => $validated['division_bloques']  ?? null,
                'tratamiento'        => $validated['tratamiento']       ?? null,
                'dosis'              => $dosis,
                'fecha_aplicacion'   => $fechaAplicacionDb,
                'doc_muestreo'       => $validated['doc_muestreo']      ?? null,
                'fechas_muestreo'    => $fechasForDb,
                'variables_agro'     => $validated['variables_agro']    ?? null,
                'observaciones'      => $validated['observaciones']     ?? null,
            ]);
        });

        $fmtDate = function ($v) {
            if (!$v) return '';
            try { return \Carbon\Carbon::parse($v)->format('d/m/Y'); }
            catch (\Throwable $e) { return ''; }
        };
        $fechasMuestreoFmt = [
            'antes' => $fmtDate($record->fechas_muestreo['antes'] ?? null),
            '15'    => $fmtDate($record->fechas_muestreo['15'] ?? null),
            '25'    => $fmtDate($record->fechas_muestreo['25'] ?? null),
            '35'    => $fmtDate($record->fechas_muestreo['35'] ?? null),
        ];

        $pesoDisplay = '';
        if ($pesoKg !== null) {
            if ($pesoUnit === 'g') {
                $pesoDisplay = number_format($pesoKg * 1000, 0) . ' g';
            } else {
                $pesoDisplay = number_format($pesoKg, 2) . ' kg';
            }
        }

        $data = [
            'pagina_actual'      => 1,
            'paginas_total'      => 1,
            'asesor'             => $record->asesor ?? '',
            'productor'          => $productorName,
            'cultivo'            => $record->cultivo ?? '',
            'folio'              => $record->folio ?? '', 
            'producto_aplicar'   => $record->producto_aplicar ?? '',
            'peso'               => $pesoDisplay,
            'objetivo'           => $record->objetivo ?? '',
            'condiciones'        => $record->condiciones ?? '',
            'ubicacion'          => $record->ubicacion ?? '',
            'ubicacion_nombre'   => $record->ubicacion_nombre ?? '',
            'croquis_src'        => $validated['croquis_src'] ?? '',
            'division_bloques'   => $record->division_bloques ?? '',
            'tratamiento'        => $record->tratamiento ?? '',
            'dosis'              => $record->dosis ?? [],
            'fecha_aplicacion'   => $fmtDate($record->fecha_aplicacion),
            'doc_muestreo'       => $record->doc_muestreo ?? '',
            'fechas_muestreo'    => $record->fechas_muestreo ?? [],
            'fechas_muestreo_fmt'=> $fechasMuestreoFmt,
            'variables_agro'     => $record->variables_agro ?? '',
            'observaciones'      => $record->observaciones ?? '',
        ];

        $view = 'formats.laboratory.12';
        $pdf  = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)->setPaper('letter');

        $slugProyecto = \Illuminate\Support\Str::slug(($data['cultivo'] ?: 'seguimiento-campo'), '-');
        $fileName = 'ProtocoloSeguimiento_' . $slugProyecto . '_' . ($record->folio ?: 'sin-folio') . '.pdf';

        return $pdf->download($fileName);
    }

    public function pdf14(Request $request)
    {
        $MALLAS = ['10','24','50','65','85','100','120','150','200'];
        $rules = [
            'encabezado_libre'              => ['nullable','string','max:5000'],
            'registros'                     => ['required','array','min:1'],
            'registros.*.titulo'            => ['nullable','string','max:255'],
            'registros.*.fecha'             => ['nullable','date'],
            'registros.*.folio'             => ['nullable','string','max:255'],
            'registros.*.lote'              => ['nullable','string','max:255'],
            'registros.*.peso'              => ['nullable','string','max:255'],
            'registros.*.ph'                => ['nullable','string','max:255'],
            'registros.*.humedad'           => ['nullable','string','max:255'],
            'registros.*.proteina'          => ['nullable','string','max:255'],
            'registros.*.sensorial'         => ['nullable','array'],
            'registros.*.sensorial.color'   => ['nullable','string','max:255'],
            'registros.*.sensorial.olor'    => ['nullable','string','max:255'],
            'registros.*.sensorial.sabor'   => ['nullable','string','max:255'],
            'registros.*.granulometria'     => ['nullable','array'],
        ];

        foreach ($MALLAS as $m) {
            $rules["registros.*.granulometria.$m"] = ['nullable','numeric','between:0,100'];
        }

        $validated = $request->validate($rules);
        $rows = (array)($validated['registros'] ?? []);
        foreach ($rows as $i => $r) {
            $rows[$i]['titulo']        = $r['titulo']        ?? 'Registro '.($i+1);
            $rows[$i]['fecha']         = $r['fecha']         ?? '';
            $rows[$i]['folio']         = $r['folio']         ?? '';
            $rows[$i]['lote']          = $r['lote']          ?? '';
            $rows[$i]['peso']          = $r['peso']          ?? '';
            $rows[$i]['ph']            = $r['ph']            ?? '';
            $rows[$i]['humedad']       = $r['humedad']       ?? '';
            $rows[$i]['proteina']      = $r['proteina']      ?? '';
            $rows[$i]['observaciones'] = $r['observaciones'] ?? '';
            $sen = (array)($r['sensorial'] ?? []);
            $rows[$i]['sensorial'] = [
                'color' => $sen['color'] ?? '',
                'olor'  => $sen['olor']  ?? '',
                'sabor' => $sen['sabor'] ?? '',
            ];

            $g = (array)($r['granulometria'] ?? []);
            $gran = [];
            foreach ($MALLAS as $m) {

                $gran[$m] = $g[$m] ?? '';
            }
            $rows[$i]['granulometria'] = $gran;
        }

        if (empty($rows)) {
            $rows = [[
                'titulo'        => 'Registro 1',
                'fecha'         => '',
                'folio'         => '',
                'lote'          => '',
                'peso'          => '',
                'ph'            => '',
                'humedad'       => '',
                'proteina'      => '',
                'sensorial'     => ['color'=>'', 'olor'=>'', 'sabor'=>''],
                'granulometria' => array_fill_keys($MALLAS, ''), 
                'observaciones' => '',
            ]];
        }

        $data = [
            'pagina_actual'    => 1,
            'paginas_total'    => 1,
            'encabezado_libre' => $validated['encabezado_libre'] ?? '',
            'registros'        => $rows,
        ];

        $viewName = 'formats.laboratory.14'; 
        $pdf = Pdf::loadView($viewName, $data)->setPaper('letter');

        $fileName = 'Bitacora_' . Carbon::now()->format('Ymd_His') . '.pdf';
        return $pdf->stream($fileName);
    }

    public function pdf01pr(Request $request)
    {
        $validated = $request->validate([
            'fecha'                   => ['nullable','date'],
            'product_id'              => ['nullable','integer','exists:products,product_id'],
            'producto'                => ['nullable','string','max:255'],
            'sku'                     => ['nullable','string','max:100'],
            'pedido'                  => ['nullable','string','max:255'],
            'volumen_fabricar_l'      => ['nullable','numeric','min:0'],
            'volumen_producir_l'      => ['nullable','numeric','min:0'],
            'unidad_cantidades'       => ['nullable','string','max:20'],
            'materiales'              => ['nullable','array'],
            'materiales.*.nombre'     => ['nullable','string','max:255'],
            'materiales.*.cant_por_1l'=> ['nullable','numeric','min:0'],
            'materiales.*.unidad'     => ['nullable','string','max:50'],
            'materiales.*.total_utilizar'=> ['nullable','numeric','min:0'],
            'materiales.*.lote'       => ['nullable','string','max:100'],
            'recepcion_mp_nombre'     => ['nullable','string','max:255'],
            'recepcion_mp_fecha'      => ['nullable','date'],
            'procedimiento_extra'     => ['nullable','string','max:5000'],
            'controles'               => ['nullable','array'],
            'controles.*.param'       => ['nullable','string','max:255'],
            'controles.*.unidad'      => ['nullable','string','max:30'],
            'controles.*.valor'       => ['nullable','string','max:100'],
            'controles.*.obs'         => ['nullable','string','max:1000'],
            'lote_salida'             => ['nullable','string','max:100'],
            'fecha_salida'            => ['nullable','date'],
            'val_calidad_nombre'      => ['nullable','string','max:255'],
            'rec_almacen_nombre'      => ['nullable','string','max:255'],
        ]);

        if (!empty($validated['product_id'])) {
            $prod = \App\Models\Product::where('product_id', (int)$validated['product_id'])
                ->first(['product_id','name','sku']);

            if ($prod) {
                $validated['producto'] = $prod->name;
                $validated['sku']      = $prod->sku ?? ($validated['sku'] ?? '');
            }
        }

        $volFabricar = (float)($validated['volumen_fabricar_l'] ?? 0);
        $volProducir = (float)($validated['volumen_producir_l'] ?? 0);
        if ($volProducir <= 0) { $volProducir = $volFabricar; }

        $materiales = array_values((array)($validated['materiales'] ?? []));
        foreach ($materiales as $i => $m) {
            $cant1L = isset($m['cant_por_1l']) ? (float)$m['cant_por_1l'] : null;
            $tot    = isset($m['total_utilizar']) ? (float)$m['total_utilizar'] : null;

            if (($tot === null || $tot === 0.0) && $cant1L !== null && $volProducir > 0) {
                $materiales[$i]['total_utilizar'] = round($cant1L * $volProducir, 3);
            }
        }

        $controles = array_values((array)($validated['controles'] ?? []));
        $data = [
            'pagina_actual'  => 1,
            'paginas_total'  => 1,
            'fecha'          => $validated['fecha'] ?? '',
            'producto'       => $validated['producto'] = $prod->name,
            'sku'            => $validated['sku'] ?? '',
            'pedido'               => $validated['pedido'] ?? '',
            'volumen_fabricar_l'   => $volFabricar,
            'volumen_producir_l'   => $volProducir,
            'unidad_cantidades'    => $validated['unidad_cantidades'] ?? '',
            'materiales'           => $materiales,
            'recepcion_mp_nombre'  => $validated['recepcion_mp_nombre'] ?? '',
            'recepcion_mp_fecha'   => $validated['recepcion_mp_fecha'] ?? '',
            'procedimiento_extra'  => $validated['procedimiento_extra'] ?? '',
            'controles'            => $controles,
            'lote_salida'          => $validated['lote_salida'] ?? '',
            'fecha_salida'         => $validated['fecha_salida'] ?? '',
            'val_calidad_nombre'   => $validated['val_calidad_nombre'] ?? '',
            'rec_almacen_nombre'   => $validated['rec_almacen_nombre'] ?? '',
        ];

        $pdf  = \Barryvdh\DomPDF\Facade\Pdf::loadView('formats.laboratory.01pr', $data)->setPaper('letter');
        $slug = \Illuminate\Support\Str::slug($data['producto'] ?: 'orden-produccion', '-');
        $fileName = 'OrdenProduccion_' . $slug . '_' . \Carbon\Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($fileName);
    }

    //invetario de suelos
    public function exportMonitoringCsv(): StreamedResponse
    {
        $fileName = 'seguimiento12.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            'Pragma'              => 'no-cache',
        ];

        $callback = function () {
            $out = fopen('php://output', 'w');

            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, ['Folio', 'Productor', 'Ubicación', 'Cultivo', 'Tratamiento', 'Peso', 'Muestreo (Asesor técnico)']);

            $rows = DB::table('laboratory_monitoring as lm')
                ->leftJoin('customers as c', 'c.customer_id', '=', 'lm.productor_id')
                ->selectRaw('
                    lm.folio,
                    COALESCE(c.name, "")                          as productor,
                    COALESCE(lm.ubicacion_nombre, lm.ubicacion, "") as ubicacion,
                    lm.cultivo,
                    lm.tratamiento,
                    COALESCE(lm.peso_text, "")                    as peso,
                    lm.asesor
                ')
                ->orderBy('lm.id', 'asc') 
                ->cursor();

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->folio,
                    $r->productor,
                    $r->ubicacion,
                    $r->cultivo,
                    $r->tratamiento,
                    $r->peso,    
                    $r->asesor,
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function inv(Request $request)
    {
        $validated = $request->validate([
            'folio_muestra'    => 'nullable|string|max:50', 
            'tipo_muestra'     => 'required|string|max:150',
            'proveedor'        => 'nullable|string|max:255',
            'sku'              => 'nullable|string|max:100',
            'producto'         => 'required|string|max:255',
            'stock_inicial'    => 'nullable|numeric|min:0',
            'presentacion'     => 'nullable|string|max:100',
            'ubicacion_stock'  => 'nullable|string|max:255',
            'fecha_entrada'    => 'nullable|date',
            'fecha_salida'     => 'nullable|date',
            'cantidad_salida'  => 'nullable|numeric|min:0',
            'motivo_salida'    => 'nullable|string|max:255',
            'solicitante'      => 'nullable|string|max:255',
            'recolector'       => 'nullable|string|max:255',
            'cliente'          => 'nullable|string|max:255',
            'status'           => 'nullable|string|max:50',
        ]);

        unset($validated['stock_final']);

        $row = DB::transaction(function () use ($validated, $request) {

            // Check Proveedor
            if (!empty($validated['proveedor'])) {
                $supplierName = trim($validated['proveedor']);
                $existsSupplier = \App\Models\Supplier::where('name', $supplierName)->first();
                if (!$existsSupplier) {
                    $count = \App\Models\Supplier::count();
                    $next = $count + 1;
                    $prefix = 'SP';
                    \App\Models\Supplier::create([
                        'name' => $supplierName,
                        'supplier_code' => $prefix . $next,
                    ]);
                }
            }

            // Check Producto
            if (!empty($validated['producto'])) {
                $productName = trim($validated['producto']);
                $existsProduct = \App\Models\Product::where('name', $productName)->first();
                
                $sku = $validated['sku'] ?? null;
                if (!$existsProduct) {
                    if (empty($sku)) {
                        $lastProduct = \App\Models\Product::orderBy('product_id', 'desc')->first();
                        $nextId = $lastProduct ? $lastProduct->product_id + 1 : 1;
                        $sku = 'LAB' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
                        $validated['sku'] = $sku;
                    }
                    
                    \App\Models\Product::create([
                        'name' => $productName,
                        'sku' => $sku,
                        'sat_code' => '01010101', 
                        'category_id' => 1, 
                        'is_public' => 1
                    ]);
                } else {
                    if (empty($sku)) {
                        $validated['sku'] = $existsProduct->sku;
                    }
                }
            }

            if ($request->filled('folio_muestra')) {
                $folioFinal = $request->input('folio_muestra');
            } else {
                $maxFolio = DB::table('laboratory_samples')
                    ->lockForUpdate()
                    ->selectRaw('MAX(CAST(folio AS UNSIGNED)) AS max_folio')
                    ->value('max_folio');
                    
                $nextNum = max((int)$maxFolio, 1479) + 1;
                $folioFinal = (string)$nextNum;
            }

            $data = $validated;
            $data['folio'] = $folioFinal;
            if (empty($data['status'])) {
                $data['status'] = 'Fuera de laboratorio';
            }

            return LaboratorySample::create($data);
        });

        return response()->json([
            'ok'      => true,
            'message' => 'Registro guardado correctamente con folio: ' . $row->folio,
            'data'    => $row,
        ], 201);
    }

    //inventario de muestras
    public function exportCsv()
    {
        $fileName = 'laboratory_samples_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            'Pragma'              => 'no-cache',
        ];

        $columns = [
            'FOLIO','TIPO DE MUESTRA','PROVEEDOR','SKU','PRODUCTO',
            'STOCK INICIAL (gramos)','Presentación','UBICACIÓN STOCK',
            'FECHA ENTRADA LABORATORIO','FECHA SALIDA LABORATORIO',
            'CANTIDAD DE SALIDA (gramos)','STOCK FINAL (gramos)',
            'MOTIVO DE SALIDA','SOLICITANTE','RECOLECTOR','CLIENTE','ESTATUS',
        ];

        $callback = function () use ($columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, $columns);

            $rows = \DB::table('laboratory_samples')
                ->orderBy('id', 'asc')         
                ->select([
                    'folio','tipo_muestra','proveedor','sku','producto',
                    'stock_inicial','presentacion','ubicacion_stock',
                    'fecha_entrada','fecha_salida','cantidad_salida','stock_final',
                    'motivo_salida','solicitante','recolector','cliente','status',
                ])
                ->get();

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->folio,
                    $r->tipo_muestra,
                    $r->proveedor,
                    $r->sku,
                    $r->producto,
                    number_format((float)$r->stock_inicial, 2, '.', ''),
                    $r->presentacion,
                    $r->ubicacion_stock,
                    $r->fecha_entrada ? \Carbon\Carbon::parse($r->fecha_entrada)->format('d/m/Y') : '',
                    $r->fecha_salida ? \Carbon\Carbon::parse($r->fecha_salida)->format('d/m/Y') : '',
                    number_format((float)$r->cantidad_salida, 2, '.', ''),
                    number_format((float)$r->stock_final, 2, '.', ''),
                    $r->motivo_salida,
                    $r->solicitante,
                    $r->recolector,
                    $r->cliente,
                    $r->status ?? 'Fuera de laboratorio',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

}
 


