<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Laboratory\LaboratoryRepository;
use App\Http\Requests\Laboratory\CustomerSampleRequestStoreRequest;
use App\Http\Requests\Laboratory\CustomerSampleRequestUpdateRequest;
use App\Http\Requests\Laboratory\LaboratorySampleInvRequest;
use App\Http\Requests\Laboratory\SalidaMuestraRequest;
use App\Http\Resources\Laboratory\CustomerSampleRequestResource;
use App\Models\CustomerSampleRequest;
use App\Models\Product;
use App\Traits\UtilResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaboratoryController extends Controller
{
    protected UtilResponse $utilResponse;
    protected LaboratoryRepository $laboratoryRepo;

    public function __construct(UtilResponse $utilResponse, LaboratoryRepository $laboratoryRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->laboratoryRepo = $laboratoryRepo;
    }

    public function index()
    {
        try {
            $data = $this->laboratoryRepo->getIndexData();
            return view('laboratory', $data);
        } catch (\Throwable $e) {
            Log::error('Error loading laboratory index', [
                'action'    => 'LaboratoryController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return back()->withErrors('Error al cargar la vista de laboratorio.');
        }
    }

    public function previewFormat01(Request $request)
    {
        return view('formats.laboratory.01', []);
    }

    public function pdfFormat01(Request $request)
    {
        $pdf = Pdf::loadView('formats.laboratory.01', [])->setPaper('letter');
        return $pdf->stream('Formato_01.pdf');
    }

    public function pdf1(Request $request)
    {
        try {
            $validated = $request->validate([
                'folio_muestra'             => ['nullable', 'string', 'max:50'],
                'product_id'                => ['required', 'integer', 'exists:products,product_id'],
                'nombre_comercial'          => ['nullable', 'string', 'max:255'],
                'sku'                       => ['nullable', 'string', 'max:100'],
                'lote'                      => ['nullable', 'string', 'max:100'],
                'fecha_entrada'             => ['nullable', 'date'],
                'fecha_caducidad'           => ['nullable', 'date'],
                'descripcion'               => ['nullable', 'string', 'max:2000'],
                'supplier_id'               => ['nullable', 'integer', 'exists:suppliers,supplier_id'],
                'origen_muestra'            => ['nullable', 'in:proveedor,produccion,almacen,otro'],
                'origen_otro'               => ['nullable', 'string', 'max:255'],
                'objetivo_muestra'          => ['nullable', 'array'],
                'objetivo_muestra.*'        => ['in:inspeccion,retencion,analisis,desarrollo,exposicion,otro'],
                'objetivo_otro'             => ['nullable', 'string', 'max:255'],
                'cantidad'                  => ['nullable', 'numeric'],
                'um'                        => ['nullable', 'in:g,kg,l,ml,otro'],
                'um_otro'                   => ['nullable', 'string', 'max:50'],
                'docs_ccf'                  => ['nullable'],
                'docs_ft'                   => ['nullable'],
                'docs_hs'                   => ['nullable'],
                'docs_otro'                 => ['nullable'],
                'docs_otro_txt'             => ['nullable', 'string', 'max:255'],
                'observaciones_laboratorio' => ['nullable', 'string', 'max:5000'],
                'firma_entrega_nombre'      => ['nullable', 'string', 'max:255'],
                'firma_recepcion_nombre'    => ['nullable', 'string', 'max:255'],
            ]);

            $b = fn(string $k) => $request->boolean($k);
            $validated['docs_ccf']  = $b('docs_ccf');
            $validated['docs_ft']   = $b('docs_ft');
            $validated['docs_hs']   = $b('docs_hs');
            $validated['docs_otro'] = $b('docs_otro');

            $obj = $request->input('objetivo_muestra', []);
            $objArr = is_array($obj) ? $obj : [];
            $validated['objetivo_muestra'] = $objArr;

            if (($validated['origen_muestra'] ?? null) !== 'otro') $validated['origen_otro'] = null;
            if (!in_array('otro', $objArr)) $validated['objetivo_otro'] = null;
            if (($validated['um'] ?? null) !== 'otro') $validated['um_otro'] = null;
            if (!$validated['docs_otro']) $validated['docs_otro_txt'] = null;

            $product = Product::find($validated['product_id']);
            $productName = $product->name ?? '';
            $sku = $validated['sku'] ?? ($product->sku ?? '');

            $supplier = !empty($validated['supplier_id'])
                ? DB::table('suppliers')->where('supplier_id', $validated['supplier_id'])->first()
                : null;
            $supplierName = $supplier->name ?? '';

            $fmtDate = function ($v) {
                if (!$v) return '';
                try { return Carbon::parse($v)->format('d/m/Y'); } catch (\Throwable $e) { return ''; }
            };

            $data = array_merge($validated, [
                'producto'        => $productName,
                'proveedor'       => $supplierName,
                'sku'             => $sku,
                'fecha_entrada'   => $fmtDate($validated['fecha_entrada'] ?? null),
                'fecha_caducidad' => $fmtDate($validated['fecha_caducidad'] ?? null),
                'cantidad'        => isset($validated['cantidad']) && $validated['cantidad'] !== null ? number_format((float) $validated['cantidad'], 2) : '',
            ]);

            if (empty($data['folio_muestra'])) {
                $maxId = (int) DB::table('reception_of_samples')->max('id');
                $data['folio_muestra'] = 'RM-' . str_pad((string) ($maxId + 1), 6, '0', STR_PAD_LEFT);
            }

            $pdf = Pdf::loadView('formats.laboratory.01', $data)->setPaper('letter');
            $fileName = 'RecepcionMuestras_' . $data['folio_muestra'] . '_' . now()->format('d_m_Y_His') . '.pdf';

            return $pdf->download($fileName);
        } catch (\Throwable $e) {
            Log::error('Error in pdf1', ['exception' => $e->getMessage()]);
            return $this->utilResponse->errorResponse('Error al generar PDF de recepción', 500);
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
            'items.*.cantidad'           => ['nullable', 'max:100'],
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
        $validated['entrega_paqueteria']         = $b('entrega_paqueteria');
        $validated['entrega_personal_empresa']   = $b('entrega_personal_empresa');
        $validated['entrega_recoleccion_planta'] = $b('entrega_recoleccion_planta');
        $validated['entrega_otro']               = $b('entrega_otro');

        if (!$validated['entrega_otro']) $validated['entrega_otro_txt'] = null;

        $fmtDate = function ($v) {
            if (!$v) return '';
            try { return Carbon::parse($v)->format('d/m/Y'); } catch (\Throwable $e) { return ''; }
        };

        $itemsProcessed = [];
        foreach ($validated['items'] as $it) {
            $prod = Product::find($it['product_id']);
            $it['producto'] = $prod ? $prod->name : '';
            $it['sku'] = !empty($it['sku']) ? $it['sku'] : ($prod ? $prod->sku : '');

            $itemB = fn(string $k) => isset($it[$k]) && filter_var($it[$k], FILTER_VALIDATE_BOOLEAN);
            $it['pres_ziploc']     = $itemB('pres_ziploc');
            $it['pres_whirlpak']   = $itemB('pres_whirlpak');
            $it['pres_metalizada'] = $itemB('pres_metalizada');
            $it['pres_frasco']     = $itemB('pres_frasco');
            $it['pres_bidon']      = $itemB('pres_bidon');
            $it['pres_otro']       = $itemB('pres_otro');
            $it['docs_cc']         = $itemB('docs_cc');
            $it['docs_ft']         = $itemB('docs_ft');
            $it['docs_hs']         = $itemB('docs_hs');
            $it['docs_otro']       = $itemB('docs_otro');

            if (!$it['pres_otro']) $it['pres_otro_txt'] = null;
            if (!$it['docs_otro']) $it['docs_otro_txt'] = null;

            $itemsProcessed[] = $it;
        }

        $firstItem = $itemsProcessed[0] ?? [];
        $data = array_merge($validated, [
            'fecha_solicitud'   => $fmtDate($validated['fecha_solicitud'] ?? null),
            'fecha_recoleccion' => $fmtDate($validated['fecha_recoleccion'] ?? null),
            'items'             => $itemsProcessed,
            'producto'          => $firstItem['producto'] ?? '',
            'sku'               => $firstItem['sku'] ?? '',
            'um'                => $firstItem['um'] ?? '',
            'cantidad'          => $firstItem['cantidad'] ?? '',
            'lote_almacen'      => $firstItem['lote_almacen'] ?? '',
            'lote_venta'        => $firstItem['lote_venta'] ?? '',
        ]);

        $pdf = Pdf::loadView('formats.laboratory.02', $data)->setPaper('letter');
        $slugCliente = Str::slug($validated['cliente_nombre'] ?? 'cliente', '_');
        $fileName = 'SolicitudMuestras_' . $slugCliente . '_' . Carbon::now()->format('d_m_Y_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function store2(CustomerSampleRequestStoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $items = $validated['items'] ?? [];
            unset($validated['items']);

            $record = $this->laboratoryRepo->storeCustomerRequest($validated, $items);

            return response()->json([
                'ok'      => true,
                'success' => true,
                'flag'    => true,
                'message' => 'Request saved successfully with folio ' . $record->folio,
                'id'      => $record->id,
                'folio'   => $record->folio,
                'data'    => $record,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error in store2', [
                'action'    => 'LaboratoryController@store2',
                'user_id'   => auth()->id(),
                'payload'   => $request->all(),
                'exception' => $e->getMessage(),
            ]);
            return response()->json(['ok' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getCustomerRequestsJson(): JsonResponse
    {
        try {
            $data = $this->laboratoryRepo->getCustomerRequests();

            $data = $data->map(function ($req) {
                $productNames = $req->items->pluck('product.name')->filter()->implode(', ');
                if (empty($productNames) && $req->product_id) {
                    $product = Product::find($req->product_id);
                    $productNames = $product ? $product->name : '';
                }
                $req->producto_nombre = $productNames ?: 'N/A';
                return $req;
            });

            return response()->json([
                'success' => true,
                'flag'    => true,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in getCustomerRequestsJson', ['exception' => $e->getMessage()]);
            return $this->utilResponse->errorResponse('Error al consultar solicitudes', 500);
        }
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate(['status' => 'required|in:0,1,2']);

        try {
            $this->laboratoryRepo->updateCustomerRequestStatus((int) $id, (int) $request->status);
            return response()->json(['ok' => true, 'success' => true, 'flag' => true]);
        } catch (\Throwable $e) {
            Log::error('Error in updateStatus', ['id' => $id, 'exception' => $e->getMessage()]);
            return response()->json(['ok' => false, 'success' => false], 500);
        }
    }

    public function showCustomerRequest($id): JsonResponse
    {
        try {
            $record = $this->laboratoryRepo->findCustomerRequestOrFail((int) $id);
            return response()->json(['data' => $record, 'success' => true, 'flag' => true]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Record not found'], 404);
        } catch (\Throwable $e) {
            Log::error('Error in showCustomerRequest', ['id' => $id, 'exception' => $e->getMessage()]);
            return $this->utilResponse->errorResponse('Error al consultar solicitud', 500);
        }
    }

    public function updateCustomerRequest(CustomerSampleRequestUpdateRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $items = $validated['items'] ?? [];
            unset($validated['items']);

            $row = $this->laboratoryRepo->updateCustomerRequest((int) $id, $validated, $items);

            return response()->json([
                'ok'      => true,
                'success' => true,
                'flag'    => true,
                'message' => 'Record updated',
                'data'    => $row,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['ok' => false, 'message' => 'Record not found'], 404);
        } catch (\Throwable $e) {
            Log::error('Error in updateCustomerRequest', ['id' => $id, 'exception' => $e->getMessage()]);
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyCustomerRequest($id): JsonResponse
    {
        try {
            $deleted = $this->laboratoryRepo->deleteCustomerRequest((int) $id);
            if (!$deleted) {
                return response()->json(['ok' => false, 'message' => 'Record not found'], 404);
            }
            return response()->json(['ok' => true, 'success' => true]);
        } catch (\Throwable $e) {
            Log::error('Error in destroyCustomerRequest', ['id' => $id, 'exception' => $e->getMessage()]);
            return response()->json(['ok' => false], 500);
        }
    }

    public function reprintPdf2($id)
    {
        try {
            $record = $this->laboratoryRepo->findCustomerRequestOrFail((int) $id);

            $fmtDate = function ($v) {
                if (!$v) return '';
                try { return Carbon::parse($v)->format('d/m/Y'); } catch (\Throwable $e) { return ''; }
            };

            $itemsProcessed = [];
            foreach ($record->items as $it) {
                $itemsProcessed[] = [
                    'producto'        => $it->product ? $it->product->name : '',
                    'sku'             => $it->sku ?: ($it->product ? $it->product->sku : ''),
                    'um'              => $it->um,
                    'cantidad'        => $it->cantidad,
                    'lote_almacen'    => $it->lote_almacen,
                    'lote_venta'      => $it->lote_venta,
                    'pres_ziploc'     => (bool) $it->pres_ziploc,
                    'pres_whirlpak'   => (bool) $it->pres_whirlpak,
                    'pres_metalizada' => (bool) $it->pres_metalizada,
                    'pres_frasco'     => (bool) $it->pres_frasco,
                    'pres_bidon'      => (bool) $it->pres_bidon,
                    'pres_otro'       => (bool) $it->pres_otro,
                    'pres_otro_txt'   => $it->pres_otro_txt,
                    'docs_cc'         => (bool) $it->docs_cc,
                    'docs_ft'         => (bool) $it->docs_ft,
                    'docs_hs'         => (bool) $it->docs_hs,
                    'docs_otro'       => (bool) $it->docs_otro,
                    'docs_otro_txt'   => $it->docs_otro_txt,
                ];
            }

            $firstItem = $itemsProcessed[0] ?? [];
            $data = [
                'folio'                      => $record->folio,
                'fecha_solicitud'            => $fmtDate($record->fecha_solicitud),
                'fecha_recoleccion'          => $fmtDate($record->fecha_recoleccion),
                'cliente_nombre'             => $record->cliente_nombre,
                'cliente_direccion'          => $record->cliente_direccion,
                'cliente_correo'             => $record->cliente_correo,
                'cliente_telefono'           => $record->cliente_telefono,
                'cliente_estatus'            => $record->cliente_estatus,
                'personal_seguimiento'       => $record->personal_seguimiento,
                'entrega_paqueteria'         => (bool) $record->entrega_paqueteria,
                'entrega_personal_empresa'   => (bool) $record->entrega_personal_empresa,
                'entrega_recoleccion_planta' => (bool) $record->entrega_recoleccion_planta,
                'entrega_otro'               => (bool) $record->entrega_otro,
                'entrega_otro_txt'           => $record->entrega_otro_txt,
                'paq_nombre'                 => $record->paq_nombre,
                'paq_guia'                   => $record->paq_guia,
                'observaciones'              => $record->observaciones,
                'solicitante_nombre'         => $record->solicitante_nombre,
                'items'                      => $itemsProcessed,
                'producto'                   => $firstItem['producto'] ?? '',
                'sku'                        => $firstItem['sku'] ?? '',
                'um'                         => $firstItem['um'] ?? '',
                'cantidad'                   => $firstItem['cantidad'] ?? '',
                'lote_almacen'               => $firstItem['lote_almacen'] ?? '',
                'lote_venta'                 => $firstItem['lote_venta'] ?? '',
            ];

            $pdf = Pdf::loadView('formats.laboratory.02', $data)->setPaper('letter');
            $slugCliente = Str::slug($record->cliente_nombre ?? 'cliente', '_');
            $fileName = 'SolicitudMuestras_' . $slugCliente . '_' . Carbon::now()->format('d_m_Y_His') . '.pdf';

            return $pdf->download($fileName);
        } catch (\Throwable $e) {
            Log::error('Error in reprintPdf2', ['id' => $id, 'exception' => $e->getMessage()]);
            return abort(404, 'Registro no encontrado');
        }
    }

    public function muestrasIndex()
    {
        return view('laboratory.table_customer_sample_requests');
    }

    public function pdf3(SalidaMuestraRequest $request)
    {
        $validated = $request->validated();

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

        $product     = Product::find($validated['product_id']);
        $productName = $product->name ?? '';
        $sku         = $validated['sku'] ?? ($product->sku ?? '');

        $fmtDate = function ($v) {
            if (!$v) return '';
            try { return Carbon::parse($v)->format('d/m/Y'); } catch (\Throwable $e) { return ''; }
        };

        $fechaSalidaGuardar = !empty($validated['fecha_salida'])
            ? Carbon::parse($validated['fecha_salida'])->toDateString()
            : now()->toDateString();

        $salidaG = (float) str_replace(',', '', $validated['cantidad'] ?? '0');

        try {
            $this->laboratoryRepo->processSampleOutput($validated, $productName, $sku, $fechaSalidaGuardar, $salidaG);

            $data = array_merge($validated, [
                'pagina_actual' => 1,
                'paginas_total' => 1,
                'producto'      => $productName,
                'fecha_salida'  => $fmtDate($validated['fecha_salida'] ?? null),
            ]);

            $pdf = Pdf::loadView('formats.laboratory.03', $data)->setPaper('letter');
            $fileName = 'Salida_' . Str::slug($productName) . '_' . now()->format('Ymd_His') . '.pdf';

            return $pdf->download($fileName);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error in pdf3', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al procesar la salida: ' . $e->getMessage());
        }
    }

    public function indexMuestras()
    {
        $products = Product::orderBy('name')->get(['product_id', 'name', 'sku']);

        $batchesByProduct = DB::table('inventory')
            ->select('product_id', 'batch')
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

    public function getMuestras(Request $request): JsonResponse
    {
        try {
            $muestras = $this->laboratoryRepo->getMuestras($request->motivo, $request->search);
            return response()->json(['muestras' => $muestras, 'success' => true]);
        } catch (\Throwable $e) {
            Log::error('Error in getMuestras', ['exception' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyMuestra($id): JsonResponse
    {
        try {
            $deleted = $this->laboratoryRepo->deleteMuestra((int) $id);
            if (!$deleted) {
                return response()->json(['error' => 'No se encontró el registro de la muestra de salida.'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Muestra de salida eliminada correctamente.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in destroyMuestra', ['id' => $id, 'exception' => $e->getMessage()]);
            return response()->json(['error' => 'Hubo un error al eliminar la muestra: ' . $e->getMessage()], 500);
        }
    }

    public function updateMuestra(Request $request, $id): JsonResponse
    {
        try {
            $salida = $this->laboratoryRepo->findMuestra((int) $id);
            if (!$salida) {
                return response()->json(['error' => 'No se encontró el registro.'], 404);
            }

            $validated = $request->validate([
                'folio_muestra'    => 'nullable|string|max:50',
                'product_id'       => 'nullable|integer',
                'fecha_salida'     => 'nullable|date',
                'nombre_comercial' => 'nullable|string|max:255',
                'lote'             => 'nullable|string|max:255',
                'um'               => 'nullable|string|max:20',
                'cantidad'         => 'nullable|string|max:255',
                'descripcion'      => 'nullable|string|max:1000',
                'motivo_salida'    => 'nullable|string|max:255',
                'motivo_otro'      => 'nullable|string|max:255',
                'paq_empresa'      => 'nullable|string|max:255',
                'paq_guia'         => 'nullable|string|max:255',
                'dest_nombre'      => 'nullable|string|max:255',
                'dest_direccion'   => 'nullable|string|max:255',
                'dest_recibe'      => 'nullable|string|max:255',
                'dest_correo'      => 'nullable|email|max:255',
                'dest_telefono'    => 'nullable|string|max:50',
                'docs_otro_txt'    => 'nullable|string|max:255',
            ]);

            $b = fn(string $k) => $request->boolean($k);
            $validated['entrega_paqueteria']         = $b('entrega_paqueteria') ? 1 : 0;
            $validated['entrega_recoleccion_planta'] = $b('entrega_recoleccion_planta') ? 1 : 0;
            $validated['entrega_personal_empresa']   = $b('entrega_personal_empresa') ? 1 : 0;
            $validated['entrega_otro']               = $b('entrega_otro') ? 1 : 0;
            $validated['docs_cc']                    = $b('docs_cc') ? 1 : 0;
            $validated['docs_ft']                    = $b('docs_ft') ? 1 : 0;
            $validated['docs_hs']                    = $b('docs_hs') ? 1 : 0;
            $validated['docs_otro']                  = $b('docs_otro') ? 1 : 0;

            if (($validated['motivo_salida'] ?? null) !== 'otro') $validated['motivo_otro'] = null;
            if (!$validated['entrega_otro']) $validated['entrega_otro_txt'] = null;
            if (!$validated['docs_otro'])    $validated['docs_otro_txt'] = null;

            $this->laboratoryRepo->updateMuestra((int) $id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Muestra de salida actualizada correctamente.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in updateMuestra', ['id' => $id, 'exception' => $e->getMessage()]);
            return response()->json(['error' => 'Hubo un error al actualizar la muestra: ' . $e->getMessage()], 500);
        }
    }

    public function reimprimirPdf($id)
    {
        try {
            $salida = $this->laboratoryRepo->findMuestra((int) $id);
            if (!$salida) {
                return abort(404, 'Registro no encontrado');
            }

            $product = Product::find($salida->product_id);
            $productName = $product ? $product->name : '';

            $fmtDate = function ($v) {
                if (!$v) return '';
                try { return Carbon::parse($v)->format('d/m/Y'); } catch (\Throwable $e) { return ''; }
            };

            $data = (array) $salida;
            $data['pagina_actual'] = 1;
            $data['paginas_total'] = 1;
            $data['producto']      = $productName;
            $data['fecha_salida']  = $fmtDate($salida->fecha_salida);

            $pdf = Pdf::loadView('formats.laboratory.03', $data)->setPaper('letter');
            $fileName = 'Salida_' . Str::slug($productName) . '_' . now()->format('Ymd_His') . '.pdf';

            return $pdf->download($fileName);
        } catch (\Throwable $e) {
            Log::error('Error in reimprimirPdf', ['id' => $id, 'exception' => $e->getMessage()]);
            return abort(500, 'Error al generar el PDF');
        }
    }

    public function pdf5(Request $request)
    {
        try {
            $validated = $request->validate([
                'folio'               => ['nullable','string','max:50'],
            'fecha'               => ['nullable','date'],
            'solicitante'         => ['nullable','string','max:255'],
            'analista'            => ['nullable','string','max:255'],
            'objetivo'            => ['nullable','string'],
            'conclusiones'        => ['nullable','string'],
            'anexo_a_items'       => ['nullable','array'],
            'anexo_b_items'       => ['nullable','array'],
            'evidencias'          => ['nullable','array'],
        ]);

        $getBase64 = function($file) {
            if (!$file) return null;
            return 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        };

        $anexoA = [];
        if ($request->has('anexo_a_items')) {
            foreach ($request->input('anexo_a_items') as $i => $item) {
                $imgs = [];
                if ($request->hasFile("anexo_a_items.$i.evidencias")) {
                    foreach ($request->file("anexo_a_items.$i.evidencias") as $file) {
                        if ($file->isValid()) $imgs[] = $getBase64($file);
                    }
                }
                $anexoA[] = [
                    'descripcion' => $item['descripcion'] ?? '',
                    'imagenes'    => $imgs
                ];
            }
        }

        $anexoB = [];
        if ($request->has('anexo_b_items')) {
            foreach ($request->input('anexo_b_items') as $i => $item) {
                $imgs = [];
                if ($request->hasFile("anexo_b_items.$i.evidencias")) {
                    foreach ($request->file("anexo_b_items.$i.evidencias") as $file) {
                        if ($file->isValid()) $imgs[] = $getBase64($file);
                    }
                }
                $anexoB[] = [
                    'descripcion' => $item['descripcion'] ?? '',
                    'imagenes'    => $imgs
                ];
            }
        }

        $evidenciasGrales = [];
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                if ($file->isValid()) $evidenciasGrales[] = $getBase64($file);
            }
        }

        $data = [
            'folio'        => $validated['folio'] ?? 'S/F',
            'fecha'        => !empty($validated['fecha']) ? Carbon::parse($validated['fecha'])->format('d/m/Y') : now()->format('d/m/Y'),
            'solicitante'  => $validated['solicitante'] ?? '',
            'analista'     => $validated['analista'] ?? '',
            'objetivo'     => $validated['objetivo'] ?? '',
            'conclusiones' => $validated['conclusiones'] ?? '',
            'anexo_a'      => $anexoA,
            'anexo_b'      => $anexoB,
            'evidencias'   => $evidenciasGrales,
        ];

        $pdf = Pdf::loadView('formats.laboratory.05', $data)->setPaper('letter');
            $slug = Str::slug($data['folio'], '_');
            $fileName = 'AnalisisInterno_' . $slug . '_' . now()->format('d_m_Y_His') . '.pdf';

            return $pdf->stream($fileName);
        } catch (\Throwable $e) {
            Log::error('Error in pdf5', ['exception' => $e->getMessage()]);
            throw $e;
        }
    }

    public function pdf6(Request $request)
    {
        $validated = $request->validate([
            'fecha_ingreso'        => ['nullable', 'date'],
            'fecha_emision'        => ['nullable', 'date'],
            'cliente_nombre'       => ['nullable', 'string', 'max:150'],
            'cliente_ciudad'       => ['nullable', 'string', 'max:120'],
            'cliente_direccion'    => ['nullable', 'string', 'max:255'],
            'cliente_telefono'     => ['nullable', 'string', 'max:50'],
            'cultivo'              => ['nullable', 'string', 'max:100'],
            'sistema'              => ['nullable', 'string', 'max:100'],
            'tipo_planta'          => ['nullable', 'string', 'max:100'],
            'peso_muestra'         => ['nullable', 'string', 'max:50'],
            'testigo'              => ['nullable', 'boolean'],
            'ubicacion'            => ['nullable', 'string', 'max:150'],
            'tipo_muestreo'        => ['nullable', 'string', 'max:100'],
            'responsable_muestreo' => ['nullable', 'string', 'max:120'],
            'proposito'            => ['nullable', 'string', 'max:255'],
            'n_val'                => ['nullable', 'numeric'],
            'p_val'                => ['nullable', 'numeric'],
            'k_val'                => ['nullable', 'numeric'],
            'arena'                => ['nullable', 'numeric'],
            'limo'                 => ['nullable', 'numeric'],
            'arcilla'              => ['nullable', 'numeric'],
            'clasificacion'        => ['nullable', 'string', 'max:100'],
            'triangulo_src'        => ['nullable', 'string'],
            'imagen_titulo'        => ['nullable', 'string', 'max:200'],
            'imagen_evidencia'     => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png', 'max:3072'],
            'convencionales'       => ['nullable', 'array'],
        ]);

        $validated['testigo'] = $request->boolean('testigo');

        $convRows = [];
        if (!empty($validated['convencionales']) && is_array($validated['convencionales'])) {
            foreach ($validated['convencionales'] as $r) {
                if (!is_array($r)) continue;
                $v = trim((string)($r['v'] ?? ''));
                $res = trim((string)($r['r'] ?? ''));
                $u = trim((string)($r['u'] ?? ''));
                if ($v !== '' || $res !== '' || $u !== '') {
                    $convRows[] = ['v' => $v, 'r' => $res, 'u' => $u];
                }
            }
        }

        $dataUri = null;
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

            $reportCode = 'S' . str_pad((string) max($analysisId - 1, 0), 4, '0', STR_PAD_LEFT);

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
            'id'                   => $analysisId,
            'reporte'              => $reportCode,
            'fecha_ingreso'        => $validated['fecha_ingreso'] ?? null,
            'fecha_emision'        => $validated['fecha_emision'] ?? null,
            'cliente_nombre'       => $validated['cliente_nombre'] ?? null,
            'cliente_ciudad'       => $validated['cliente_ciudad'] ?? null,
            'cliente_direccion'    => $validated['cliente_direccion'] ?? null,
            'cliente_telefono'     => $validated['cliente_telefono'] ?? null,
            'cultivo'              => $validated['cultivo'] ?? null,
            'sistema'              => $validated['sistema'] ?? null,
            'tipo_planta'          => $validated['tipo_planta'] ?? null,
            'peso_muestra'         => $validated['peso_muestra'] ?? null,
            'testigo'              => $validated['testigo'],
            'ubicacion'            => $validated['ubicacion'] ?? null,
            'tipo_muestreo'        => $validated['tipo_muestreo'] ?? null,
            'responsable_muestreo' => $validated['responsable_muestreo'] ?? null,
            'proposito'            => $validated['proposito'] ?? null,
            'convencionales'       => $convRows,
            'n_val'                => $validated['n_val'] ?? null,
            'p_val'                => $validated['p_val'] ?? null,
            'k_val'                => $validated['k_val'] ?? null,
            'arena'                => $validated['arena'] ?? null,
            'limo'                 => $validated['limo'] ?? null,
            'arcilla'              => $validated['arcilla'] ?? null,
            'clasificacion'        => $validated['clasificacion'] ?? null,
            'triangulo_src'        => $validated['triangulo_src'] ?? null,
            'imagen_evidencia_src' => $dataUri,
            'imagen_titulo'        => $validated['imagen_titulo'] ?? null,
        ];

        $pdf = Pdf::loadView('formats.laboratory.06', $data)->setPaper('letter');
        $slug = Str::slug($reportCode ?: 'reporte', '-');
        $fileName = 'AnalisisSuelo_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function pdf7(Request $request)
    {
        $pdf = Pdf::loadView('formats.laboratory.07', [])->setPaper('letter');
        return $pdf->stream('Instructivo_Muestreo_' . now()->format('Ymd_His') . '.pdf');
    }

    public function pdf9(Request $request)
    {
        $validated = $request->validate([
            'fecha_solicitud'        => ['nullable', 'date'],
            'folio'                  => ['nullable', 'string', 'max:50'],
            'solicitante'            => ['nullable', 'string', 'max:150'],
            'tipo_producto'          => ['nullable', 'string', 'max:150'],
            'ph_esperado'            => ['nullable', 'string', 'max:50'],
            'densidad_esperada'      => ['nullable', 'string', 'max:50'],
            'color_apariencia'       => ['nullable', 'string', 'max:150'],
            'olor_caracteristico'    => ['nullable', 'string', 'max:150'],
            'tamano_lote'            => ['nullable', 'string', 'max:100'],
            'fecha_entrega_esperada' => ['nullable', 'date'],
            'nombre_comercial'       => ['nullable', 'string', 'max:200'],
            'aplicacion_uso'         => ['nullable', 'string', 'max:255'],
            'presentacion_deseada'   => ['nullable', 'string', 'max:150'],
            'observaciones'          => ['nullable', 'string'],
            'responsable_nombre'     => ['nullable', 'string', 'max:150'],
            'ingredientes'           => ['nullable', 'array'],
        ]);

        $ingredientesRows = [];
        if (!empty($validated['ingredientes']) && is_array($validated['ingredientes'])) {
            foreach ($validated['ingredientes'] as $ing) {
                if (!is_array($ing)) continue;
                $ingredientesRows[] = [
                    'ingrediente' => trim((string)($ing['ingrediente'] ?? '')),
                    'funcion'     => trim((string)($ing['funcion'] ?? '')),
                    'concentracion' => trim((string)($ing['concentracion'] ?? '')),
                    'comentarios' => trim((string)($ing['comentarios'] ?? '')),
                ];
            }
        }

        $data = [
            'fecha_solicitud'        => $validated['fecha_solicitud'] ?? null,
            'folio'                  => $validated['folio'] ?? 'SF-' . date('Ymd'),
            'solicitante'            => $validated['solicitante'] ?? null,
            'tipo_producto'          => $validated['tipo_producto'] ?? null,
            'ph_esperado'            => $validated['ph_esperado'] ?? null,
            'densidad_esperada'      => $validated['densidad_esperada'] ?? null,
            'color_apariencia'       => $validated['color_apariencia'] ?? null,
            'olor_caracteristico'    => $validated['olor_caracteristico'] ?? null,
            'tamano_lote'            => $validated['tamano_lote'] ?? null,
            'fecha_entrega_esperada' => $validated['fecha_entrega_esperada'] ?? null,
            'nombre_comercial'       => $validated['nombre_comercial'] ?? null,
            'aplicacion_uso'         => $validated['aplicacion_uso'] ?? null,
            'presentacion_deseada'   => $validated['presentacion_deseada'] ?? null,
            'observaciones'          => $validated['observaciones'] ?? null,
            'responsable_nombre'     => $validated['responsable_nombre'] ?? null,
            'ingredientes'           => $ingredientesRows,
        ];

        $pdf = Pdf::loadView('formats.laboratory.09', $data)->setPaper('letter');
        $slug = Str::slug($data['folio'], '-');
        $fileName = 'SolicitudFormulacion_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function pdf10(Request $request)
    {
        $validated = $request->validate([
            'fecha_elaboracion'    => ['nullable', 'date'],
            'folio'                => ['nullable', 'string', 'max:50'],
            'codigo_producto'      => ['nullable', 'string', 'max:100'],
            'version'              => ['nullable', 'string', 'max:50'],
            'nombre_producto'      => ['nullable', 'string', 'max:200'],
            'tipo_formulacion'     => ['nullable', 'string', 'max:150'],
            'tamano_lote_base'     => ['nullable', 'string', 'max:100'],
            'densidad_objetivo'    => ['nullable', 'string', 'max:50'],
            'ph_objetivo'          => ['nullable', 'string', 'max:50'],
            'color_apariencia'     => ['nullable', 'string', 'max:150'],
            'olor'                 => ['nullable', 'string', 'max:150'],
            'observaciones'        => ['nullable', 'string'],
            'elaboro_nombre'       => ['nullable', 'string', 'max:150'],
            'reviso_nombre'        => ['nullable', 'string', 'max:150'],
            'aprobo_nombre'        => ['nullable', 'string', 'max:150'],
            'materias_primas'      => ['nullable', 'array'],
            'instrucciones'        => ['nullable', 'array'],
        ]);

        $data = [
            'fecha_elaboracion' => $validated['fecha_elaboracion'] ?? null,
            'folio'             => $validated['folio'] ?? 'FOR-' . date('Ymd'),
            'codigo_producto'   => $validated['codigo_producto'] ?? null,
            'version'           => $validated['version'] ?? '1.0',
            'nombre_producto'   => $validated['nombre_producto'] ?? null,
            'tipo_formulacion'  => $validated['tipo_formulacion'] ?? null,
            'tamano_lote_base'  => $validated['tamano_lote_base'] ?? null,
            'densidad_objetivo' => $validated['densidad_objetivo'] ?? null,
            'ph_objetivo'       => $validated['ph_objetivo'] ?? null,
            'color_apariencia'  => $validated['color_apariencia'] ?? null,
            'olor'              => $validated['olor'] ?? null,
            'observaciones'     => $validated['observaciones'] ?? null,
            'elaboro_nombre'    => $validated['elaboro_nombre'] ?? null,
            'reviso_nombre'     => $validated['reviso_nombre'] ?? null,
            'aprobo_nombre'     => $validated['aprobo_nombre'] ?? null,
            'materias_primas'   => $validated['materias_primas'] ?? [],
            'instrucciones'     => $validated['instrucciones'] ?? [],
        ];

        $pdf = Pdf::loadView('formats.laboratory.10', $data)->setPaper('letter');
        $slug = Str::slug($data['folio'], '-');
        $fileName = 'Formulacion_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function pdf11(Request $request)
    {
        $validated = $request->validate([
            'semana_rango'         => ['nullable', 'string', 'max:100'],
            'fecha_revision'       => ['nullable', 'date'],
            'proyecto'             => ['nullable', 'string', 'max:150'],
            'responsable'          => ['nullable', 'string', 'max:150'],
            'total_horas'          => ['nullable', 'integer'],
            'objetivos'            => ['nullable', 'array'],
            'resultados'           => ['nullable', 'array'],
            'hallazgos'            => ['nullable', 'array'],
            'proxima_semana_rango' => ['nullable', 'string', 'max:100'],
            'plan_proxima'         => ['nullable', 'array'],
        ]);

        $data = [
            'pagina_actual'        => 1,
            'paginas_total'        => 3,
            'semana_rango'         => $validated['semana_rango'] ?? null,
            'fecha_revision'       => $validated['fecha_revision'] ?? null,
            'proyecto'             => $validated['proyecto'] ?? null,
            'responsable'          => $validated['responsable'] ?? null,
            'total_horas'          => $validated['total_horas'] ?? null,
            'objetivos'            => $validated['objetivos'] ?? [],
            'resultados'           => $validated['resultados'] ?? [],
            'hallazgos'            => $validated['hallazgos'] ?? [],
            'proxima_semana_rango' => $validated['proxima_semana_rango'] ?? null,
            'plan_proxima'         => $validated['plan_proxima'] ?? [],
        ];

        $pdf = Pdf::loadView('formats.laboratory.11', $data)->setPaper('letter');
        $slug = Str::slug($data['semana_rango'] ?: 'semana', '-');
        $fileName = 'PlanSemanal_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function pdf12(Request $request)
    {
        $validated = $request->validate([
            'folio'             => ['nullable', 'string', 'max:50'],
            'fecha_inicio'      => ['nullable', 'date'],
            'nombre_proyecto'   => ['nullable', 'string', 'max:200'],
            'responsable'       => ['nullable', 'string', 'max:150'],
            'tipo_estudio'      => ['nullable', 'string', 'max:150'],
            'duracion_estimada' => ['nullable', 'string', 'max:100'],
            'objetivo_general'  => ['nullable', 'string'],
            'alcance'           => ['nullable', 'string'],
            'etapas'            => ['nullable', 'array'],
            'recursos'          => ['nullable', 'array'],
            'criterios_exito'   => ['nullable', 'string'],
        ]);

        $data = [
            'folio'             => $validated['folio'] ?? 'PROT-' . date('Ymd'),
            'fecha_inicio'      => $validated['fecha_inicio'] ?? null,
            'nombre_proyecto'   => $validated['nombre_proyecto'] ?? null,
            'responsable'       => $validated['responsable'] ?? null,
            'tipo_estudio'      => $validated['tipo_estudio'] ?? null,
            'duracion_estimada' => $validated['duracion_estimada'] ?? null,
            'objetivo_general'  => $validated['objetivo_general'] ?? null,
            'alcance'           => $validated['alcance'] ?? null,
            'etapas'            => $validated['etapas'] ?? [],
            'recursos'          => $validated['recursos'] ?? [],
            'criterios_exito'   => $validated['criterios_exito'] ?? null,
        ];

        $pdf = Pdf::loadView('formats.laboratory.12', $data)->setPaper('letter');
        $slug = Str::slug($data['nombre_proyecto'] ?: 'proyecto', '_');
        $fileName = 'ProtocoloSeguimiento_' . $slug . '_' . ($data['folio'] ?: 'sin-folio') . '.pdf';

        return $pdf->download($fileName);
    }

    public function pdf14(Request $request)
    {
        $validated = $request->validate([
            'fecha'            => ['nullable', 'date'],
            'folio'            => ['nullable', 'string', 'max:50'],
            'equipo'           => ['nullable', 'string', 'max:150'],
            'modelo'           => ['nullable', 'string', 'max:100'],
            'numero_serie'     => ['nullable', 'string', 'max:100'],
            'responsable'      => ['nullable', 'string', 'max:150'],
            'actividad'        => ['nullable', 'string', 'max:200'],
            'descripcion'      => ['nullable', 'string'],
            'condiciones'      => ['nullable', 'string'],
            'observaciones'    => ['nullable', 'string'],
            'proximo_servicio' => ['nullable', 'date'],
        ]);

        $data = [
            'fecha'            => $validated['fecha'] ?? now()->toDateString(),
            'folio'            => $validated['folio'] ?? 'BIT-' . date('Ymd'),
            'equipo'           => $validated['equipo'] ?? null,
            'modelo'           => $validated['modelo'] ?? null,
            'numero_serie'     => $validated['numero_serie'] ?? null,
            'responsable'      => $validated['responsable'] ?? null,
            'actividad'        => $validated['actividad'] ?? null,
            'descripcion'      => $validated['descripcion'] ?? null,
            'condiciones'      => $validated['condiciones'] ?? null,
            'observaciones'    => $validated['observaciones'] ?? null,
            'proximo_servicio' => $validated['proximo_servicio'] ?? null,
        ];

        $pdf = Pdf::loadView('formats.laboratory.14', $data)->setPaper('letter');
        $fileName = 'Bitacora_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function pdf01pr(Request $request)
    {
        $validated = $request->validate([
            'orden_numero'      => ['nullable', 'string', 'max:50'],
            'fecha_orden'       => ['nullable', 'date'],
            'producto_nombre'   => ['nullable', 'string', 'max:200'],
            'lote_produccion'   => ['nullable', 'string', 'max:100'],
            'cantidad_ordenada' => ['nullable', 'string', 'max:100'],
            'especificaciones'  => ['nullable', 'string'],
            'responsable'       => ['nullable', 'string', 'max:150'],
            'materias_primas'   => ['nullable', 'array'],
        ]);

        $data = [
            'orden_numero'      => $validated['orden_numero'] ?? 'OP-' . date('Ymd'),
            'fecha_orden'       => $validated['fecha_orden'] ?? now()->toDateString(),
            'producto_nombre'   => $validated['producto_nombre'] ?? null,
            'lote_produccion'   => $validated['lote_produccion'] ?? null,
            'cantidad_ordenada' => $validated['cantidad_ordenada'] ?? null,
            'especificaciones'  => $validated['especificaciones'] ?? null,
            'responsable'       => $validated['responsable'] ?? null,
            'materias_primas'   => $validated['materias_primas'] ?? [],
        ];

        $pdf = Pdf::loadView('formats.laboratory.01pr', $data)->setPaper('letter');
        $slug = Str::slug($data['producto_nombre'] ?: 'producto', '_');
        $fileName = 'OrdenProduccion_' . $slug . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function exportMonitoringCsv(): StreamedResponse
    {
        $fileName = 'seguimiento12.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID', 'Fecha', 'Responsable', 'Proyecto', 'Actividad', 'Avance', 'Observaciones',
            ]);

            $rows = DB::table('laboratory_monitorings')->orderBy('id', 'desc')->get();
            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r->id ?? '',
                    $r->fecha ?? '',
                    $r->responsable ?? '',
                    $r->proyecto ?? '',
                    $r->actividad ?? '',
                    $r->avance ?? '',
                    $r->observaciones ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function inv(LaboratorySampleInvRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $row = $this->laboratoryRepo->storeSampleInv($validated, $request->input('folio_muestra'));

            return response()->json([
                'ok'      => true,
                'success' => true,
                'flag'    => true,
                'message' => 'Registro guardado correctamente con folio: ' . $row->folio,
                'data'    => $row,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error in inv', ['exception' => $e->getMessage()]);
            return $this->utilResponse->errorResponse('Error al registrar inventario de muestra', 500);
        }
    }

    public function exportCsv(): StreamedResponse
    {
        $fileName = 'laboratory_samples_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $samples = $this->laboratoryRepo->getSamplesForExport();

        $callback = function () use ($samples) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID', 'Folio', 'Tipo Muestra', 'Proveedor', 'SKU', 'Producto',
                'Stock Inicial', 'Presentación', 'Ubicación Stock', 'Fecha Entrada',
                'Fecha Salida', 'Cantidad Salida', 'Motivo Salida', 'Solicitante',
                'Recolector', 'Cliente', 'Estatus', 'Stock Final', 'Fecha Registro',
            ]);

            foreach ($samples as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->folio,
                    $s->tipo_muestra,
                    $s->proveedor,
                    $s->sku,
                    $s->producto,
                    $s->stock_inicial,
                    $s->presentacion,
                    $s->ubicacion_stock,
                    $s->fecha_entrada,
                    $s->fecha_salida,
                    $s->cantidad_salida,
                    $s->motivo_salida,
                    $s->solicitante,
                    $s->recolector,
                    $s->cliente,
                    $s->status,
                    $s->stock_final,
                    $s->created_at,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
