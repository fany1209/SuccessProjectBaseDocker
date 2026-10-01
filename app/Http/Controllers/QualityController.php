<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Quality\QualityRepository;
use App\Http\Requests\Quality\QualityAlmacenStoreRequest;
use App\Http\Requests\Quality\QualityUpdateInspectionRequest;
use App\Http\Requests\Quality\QualityUpdateWRequest;
use App\Models\Customer;
use App\Models\InspectionW;
use App\Models\Observacion;
use App\Models\Product;
use App\Models\Supplier;
use App\Traits\UtilResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class QualityController extends Controller
{
    protected UtilResponse $utilResponse;
    protected QualityRepository $qualityRepo;

    public function __construct(UtilResponse $utilResponse, QualityRepository $qualityRepo)
    {
        $this->utilResponse = $utilResponse;
        $this->qualityRepo = $qualityRepo;
    }

    protected function getPublicHtmlPath(string $subpath = ''): string
    {
        $base = is_dir(base_path('../public_html')) ? base_path('../public_html') : public_path();
        return $subpath ? $base . DIRECTORY_SEPARATOR . ltrim($subpath, '/\\') : $base;
    }

    public function index()
    {
        try {
            $data = $this->qualityRepo->getIndexData();
            return view('quality', $data);
        } catch (\Throwable $e) {
            Log::error('Error loading quality index', [
                'action'    => 'QualityController@index',
                'user_id'   => auth()->id(),
                'exception' => $e->getMessage(),
            ]);
            return back()->withErrors('Error al cargar el módulo de calidad.');
        }
    }

    public function ver()
    {
        return view('quality', $this->qualityRepo->getIndexData());
    }

    public function pdf5Form()
    {
        return view('quality', $this->qualityRepo->getIndexData());
    }

    public function incidencias(): JsonResponse
    {
        try {
            $rows = $this->qualityRepo->getIncidencias();
            return response()->json($rows);
        } catch (\Throwable $e) {
            Log::error('Error in incidencias', ['exception' => $e->getMessage()]);
            return response()->json([], 500);
        }
    }

    public function getData(): JsonResponse
    {
        try {
            $suppliers = $this->qualityRepo->getSuppliersData();
            return response()->json(['suppliers' => $suppliers]);
        } catch (\Throwable $e) {
            Log::error('Error in getData', ['exception' => $e->getMessage()]);
            return response()->json(['suppliers' => []], 500);
        }
    }

    public function getProducts(): JsonResponse
    {
        try {
            $grouped = $this->qualityRepo->getGroupedInventoryProducts();
            return response()->json(['products' => $grouped]);
        } catch (\Throwable $e) {
            Log::error('Error in getProducts', ['exception' => $e->getMessage()]);
            return response()->json(['products' => []], 500);
        }
    }

    public function store(Request $req)
    {
        try {
            $supplierCode = trim((string)$req->input('supplier'));
            $supplierName = trim((string)$req->input('supplier_name'));
            $arrival      = $req->input('arrival_date');
            $inspection   = $req->input('inspection_date');

            $fmtDate = function ($v) {
                if (!$v) return null;
                try { return Carbon::parse($v)->format('d/m/Y'); } catch (\Throwable $e) { return null; }
            };

            $arrivalDMY    = $fmtDate($arrival);
            $inspectionDMY = $fmtDate($inspection);
            $productos = collect($req->input('products', []))
                ->filter(fn($r) => !empty($r['name']) || (!empty($r['qty']) && $r['qty'] > 0))
                ->map(fn($r) => [
                    'product_name' => $r['name'] ?? '',
                    'batch'        => $r['lot']  ?? '',
                    'qty'          => $r['qty']  ?? '',
                    'pack'         => $r['pack'] ?? '',
                ])
                ->values()
                ->all();

            $totalQty = collect($productos)->sum(function ($r) {
                $q = $r['qty'] ?? 0;
                return is_numeric($q) ? (float)$q : 0;
            });

            $max = [
                'cantidad'       => 5,
                'identificacion' => 5,
                'empaque'        => 10,
                'sellado'        => 15,
                'limpieza'       => 15,
                'caducidad'      => 25,
                'certificado'    => 25,
            ];

            $inEval = (array) $req->input('release_eval', []);
            $inObs  = (array) $req->input('release_obs',  []);
            $evals = [];
            $obs   = [];
            $total = 0;

            foreach ($max as $k => $m) {
                $v = (int) ($inEval[$k] ?? 0);
                if ($v < 0)  $v = 0;
                if ($v > $m) $v = $m;

                $evals[$k] = $v;
                $obs[$k]   = isset($inObs[$k]) ? trim((string) $inObs[$k]) : '';
                $total    += $v;
            }
            $status = $total >= 70 ? 'ACEPTABLE' : 'NO ACEPTABLE';

            $isTrue = fn($v) => in_array(strtolower(trim((string)$v)), ['1', 'si', 'sí', 'on', 'true', 'yes'], true);

            $stripBulletsLine = fn(string $s) => preg_replace('/^\s*(?:[\-\*\x{2022}\x{25CF}\x{00B7}•]+|\d+[\.\)])\s*/u', '', trim($s));

            $toList = function (string $txt) use ($stripBulletsLine) {
                if ($txt === '') return [];
                $out = [];
                foreach (preg_split("/\r\n|\n|\r/", $txt) as $line) {
                    $line = $stripBulletsLine($line);
                    if ($line !== '') $out[] = $line;
                }
                return $out;
            };

            $inc          = (array)$req->input('incidents', []);
            $hasInc       = $isTrue($inc['has'] ?? 0);
            $folioRawI    = trim((string)($inc['folio'] ?? ''));
            $folioMostrar = $hasInc
                ? (($folioRawI !== '' && strtolower($folioRawI) !== 'na') ? $folioRawI : 'S/F')
                : 'NA';

            $descRaw   = trim((string)($inc['description'] ?? ''));
            $actsRaw   = trim((string)($inc['actions']     ?? ''));
            $descItems = $toList($descRaw);
            $actsItems = $toList($actsRaw);

            if ($hasInc && $folioMostrar !== 'NA' && $folioMostrar !== 'S/F') {
                $row = DB::table('incidencias')->where('folio', $folioMostrar)->first();
                if ($row) {
                    if (empty($descItems) && !empty($row->descripcion)) {
                        $descItems = $toList((string)$row->descripcion);
                    }
                    if (empty($actsItems) && !empty($row->acciones)) {
                        $actsItems = $toList((string)$row->acciones);
                    }
                }
            }

            $productoLiberado = strtolower((string) $req->input('producto_liberado', ''));
            $folioRawL        = trim((string) $req->input('folio', ''));
            $folioLiberacionMostrar = ($folioRawL !== '' && strtolower($folioRawL) !== 'na')
                ? $folioRawL
                : 'S/F';

            $inspectorNombre = trim((string)$req->input('inspector_nombre', ''));
            $hasCert   = $req->boolean('has_certificate', false);
            $certId    = $req->input('certificate_id');
            $certificate = null;
            if ($hasCert && $certId) {
                $certificate = DB::table('supplier_certificates')->find($certId);
            }

            $data = [
                'paginas_total'          => 1,
                'proveedor'              => $supplierName,
                'codigo_proveedor'       => $supplierCode,
                'fecha_llegada'          => $arrivalDMY,
                'fecha_inspeccion'       => $inspectionDMY,
                'productos'              => $productos,
                'total_cantidad'         => $totalQty,
                'evals'                  => $evals,
                'obs'                    => $obs,
                'release_total'          => $total,
                'release_status'         => $status,
                'hasInc'                 => $hasInc,
                'folioInc'               => $folioMostrar,
                'descItems'              => $descItems,
                'descTexto'              => $descRaw,
                'actItems'               => $actsItems,
                'actTexto'               => $actsRaw,
                'inspector_nombre'       => $inspectorNombre,
                'has_certificate'        => $hasCert,
                'certificate'            => $certificate,
                'producto_liberado'      => $productoLiberado,
                'folio'                  => $folioLiberacionMostrar,
            ];

            $pdf = Pdf::loadView('formats.quality.recepcion01', $data)->setPaper('letter');
            return $pdf->download('inspeccion_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in store', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar el formato de recepción: ' . $e->getMessage());
        }
    }

    public function almacenStore(QualityAlmacenStoreRequest $req)
    {
        try {
            $validated = $req->validated();
            $obsInput  = $req->input('obs', []);
            $files     = [];

            if ($req->has('obs')) {
                foreach ($obsInput as $i => $item) {
                    if ($req->hasFile("obs.$i.evidencia_file")) {
                        $files[$i] = $req->file("obs.$i.evidencia_file");
                    }
                }
            }

            $this->qualityRepo->storeAlmacenInspection($validated, $obsInput, $files, optional($req->user())->id);

            return back()->with('ok', 'Inspección de almacén guardada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error in almacenStore', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al guardar la inspección de almacén: ' . $e->getMessage());
        }
    }

    public function getInspections(): JsonResponse
    {
        try {
            $payload = $this->qualityRepo->getInspections(Auth::user());
            return response()->json(['inspections' => $payload], 200);
        } catch (\Throwable $e) {
            Log::error('Error in getInspections', ['exception' => $e->getMessage()]);
            return response()->json(['inspections' => []], 500);
        }
    }

    public function getInspection(Request $request): JsonResponse
    {
        try {
            $id = (int) $request->input('id');
            $inspection = $this->qualityRepo->findInspection($id);

            $observations = Observacion::select('id', 'name', 'rev', 'ubicacion', 'evidencia_path')
                ->where('inspection_id', $id)
                ->get()
                ->map(function ($obs) {
                    return [
                        'id'            => $obs->id,
                        'name'          => $obs->name,
                        'rev'           => $obs->rev,
                        'ubicacion'     => $obs->ubicacion,
                        'evidencia_url' => $obs->evidencia_path ? asset($obs->evidencia_path) : null,
                    ];
                });

            return response()->json(['inspection' => $inspection, 'observations' => $observations]);
        } catch (\Throwable $e) {
            Log::error('Error in getInspection', ['exception' => $e->getMessage()]);
            return response()->json(['inspection' => null, 'observations' => []], 500);
        }
    }

    public function eliminarObs(Request $request): JsonResponse
    {
        try {
            $id = (int) $request->id;
            $path = $request->path;

            $deleted = $this->qualityRepo->deleteObservation($id, $path);
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'observation deleted']);
            }

            return response()->json(['success' => false, 'message' => 'observation not deleted'], 404);
        } catch (\Throwable $e) {
            Log::error('Error in eliminarObs', ['exception' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error al eliminar observación: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->qualityRepo->deleteInspection((int) $id);
            return response()->json(['success' => $deleted]);
        } catch (\Throwable $e) {
            Log::error('Error in destroy', ['id' => $id, 'exception' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(QualityUpdateInspectionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $id = (int) $validated['inspection_id'];
            $obsInput = $request->input('obs', []);
            $files = [];

            if ($request->has('obs')) {
                foreach ($obsInput as $i => $item) {
                    if ($request->hasFile("obs.$i.evidencia_file")) {
                        $files[$i] = $request->file("obs.$i.evidencia_file");
                    }
                }
            }

            $this->qualityRepo->updateInspection($id, $validated, $obsInput, $files);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Error in update inspection', ['exception' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getWarehouseInspection(Request $request): JsonResponse
    {
        try {
            $inspection = $this->qualityRepo->findInspectionWithObservations((int) $request->id);
            if (!$inspection) {
                return response()->json(['message' => 'Inspección no encontrada'], 404);
            }

            return response()->json([
                'id'            => $inspection->id,
                'responsable'   => $inspection->responsable,
                'comentarios'   => $inspection->comentarios,
                'comentarios_q' => $inspection->comentarios_q,
                'observaciones' => $inspection->observaciones->map(function ($obs) {
                    return [
                        'id'             => $obs->id,
                        'name'           => $obs->name,
                        'ubicacion'      => $obs->ubicacion,
                        'fecha'          => $obs->fecha,
                        'evidencia_path' => $obs->evidencia_path,
                        'ev_corr_path'   => $obs->ev_corr_path,
                    ];
                })->values(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in getWarehouseInspection', ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'Error al obtener inspección'], 500);
        }
    }

    public function updatew(QualityUpdateWRequest $request, $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $obsInput  = $request->input('obs', []);
            $files     = [];

            if ($request->has('obs')) {
                foreach ($obsInput as $i => $item) {
                    if ($request->hasFile("obs.$i.ev_corr_file")) {
                        $files[$i] = $request->file("obs.$i.ev_corr_file");
                    }
                }
            }

            $this->qualityRepo->updateWarehouseInspection((int) $id, $validated['comentarios'] ?? null, $obsInput, $files);

            return response()->json(['message' => 'Changes saved successfully', 'success' => true]);
        } catch (\Throwable $e) {
            Log::error('Error in updatew', ['id' => $id, 'exception' => $e->getMessage()]);
            return response()->json(['message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    public function checkPending(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->roles()->where('name', 'Warehouse')->exists()) {
                return response()->json(['pending' => []]);
            }

            $pending = $this->qualityRepo->getPendingInspections();
            return response()->json(['pending' => $pending]);
        } catch (\Throwable $e) {
            Log::error('Error in checkPending', ['exception' => $e->getMessage()]);
            return response()->json(['pending' => []], 500);
        }
    }

    public function pdfGenerationsChartData(Request $req): JsonResponse
    {
        try {
            $from = $req->date('from');
            $to   = $req->date('to');

            $data = $this->qualityRepo->getPdfGenerationsChartData($from, $to);
            return response()->json(['data' => $data]);
        } catch (\Throwable $e) {
            Log::error('Error in pdfGenerationsChartData', ['exception' => $e->getMessage()]);
            return response()->json(['data' => [['Tipo', 'Generados']]], 500);
        }
    }

    public function getInspectionWView(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            if (!$id) {
                return response()->json(['message' => 'ID no recibido'], 400);
            }

            $inspection = $this->qualityRepo->findInspectionWithObservations((int) $id);
            if (!$inspection) {
                return response()->json(['message' => 'Inspección no encontrada'], 404);
            }

            return response()->json([
                'id'            => $inspection->id,
                'responsable'   => $inspection->responsable,
                'inspector'     => $inspection->inspector,
                'comentarios'   => $inspection->comentarios_q,
                'observaciones' => $inspection->observaciones->map(function ($obs) {
                    return [
                        'name'           => $obs->name,
                        'ubicacion'      => $obs->ubicacion,
                        'fecha'          => $obs->fecha,
                        'evidencia_path' => $obs->evidencia_path,
                        'ev_corr_path'   => $obs->ev_corr_path,
                    ];
                }),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in getInspectionWView', ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'Error interno', 'error' => $e->getMessage()], 500);
        }
    }

    public function pdf2(Request $req)
    {
        try {
            $productId = $req->input('product_id');
            $producto = Product::find($productId);

            $productoTitulo = $producto->name ?? 'NOMBRE PRODUCTO';
            $imagenPath = null;
            if ($req->hasFile('product_image') && $req->file('product_image')->isValid()) {
                $stored = $req->file('product_image')->store('products', 'public');
                $targetBase = $this->getPublicHtmlPath();
                $imagenPath = $targetBase . '/storage/' . $stored;
            }

            $nutricionalPath = null;
            if ($req->hasFile('nutricional_img') && $req->file('nutricional_img')->isValid()) {
                $storedNutri = $req->file('nutricional_img')->store('nutrition', 'public');
                $targetBase = $this->getPublicHtmlPath();
                $nutricionalPath = $targetBase . '/storage/' . $storedNutri;
            }

            $mapKV = fn($rows) => collect($rows ?: [])
                ->map(fn($r) => [
                    'k' => isset($r['k']) ? trim((string)$r['k']) : '',
                    'v' => isset($r['v']) ? trim((string)$r['v']) : '',
                ])
                ->filter(fn($r) => $r['k'] !== '' || $r['v'] !== '')
                ->values()
                ->all();

            $data = [
                'pagina_actual'     => 1,
                'paginas_total'     => 1,
                'producto_titulo'   => $productoTitulo,
                'descripcion_breve' => $req->input('descripcion_breve', ''),
                'imagen_path'       => $imagenPath,
                'nutricional_path'  => $nutricionalPath,
                'org_apa'           => $req->input('org_apa', ''),
                'org_color'         => $req->input('org_color', ''),
                'org_olor'          => $req->input('org_olor', ''),
                'car_org'           => $mapKV($req->input('car_org', [])),
                'car_fis'           => $mapKV($req->input('car_fis', [])),
                'macroelems'        => $mapKV($req->input('macroelems', [])),
                'microelems'        => $mapKV($req->input('microelems', [])),
                'microbio'          => $mapKV($req->input('microbio', [])),
                'inst_tecnicas'     => $req->input('inst_tecnicas', ''),
                'almacenamiento'    => $req->input('almacenamiento', ''),
                'presentacion'      => $req->input('presentacion', ''),
                'presentacion_img'  => $req->input('presentacion_img', null),
                'presentacion_base' => $req->input('presentacion_base', null),
            ];

            $pdf = Pdf::loadView('formats.quality.02', $data)->setPaper('letter');
            $slug = Str::slug($productoTitulo, '_');
            return $pdf->stream('FichaTecnica_' . $slug . '_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in pdf2', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar Ficha Técnica: ' . $e->getMessage());
        }
    }

    public function pdf3(Request $req)
    {
        try {
            $pictogramas = [];
            if ($req->hasFile('pictogramas')) {
                foreach ($req->file('pictogramas') as $file) {
                    if ($file && $file->isValid()) {
                        $stored = $file->store('sds/pictos', 'public');
                        $pictogramas[] = $this->getPublicHtmlPath('storage/' . $stored);
                        if (count($pictogramas) >= 6) break;
                    }
                }
            }

            $map3 = function ($rows) {
                return collect($rows ?: [])
                    ->map(fn($r) => [
                        'nombre'     => trim($r['nombre']     ?? ''),
                        'porcentaje' => trim($r['porcentaje'] ?? ''),
                        'cas'        => trim($r['cas']        ?? ''),
                    ])
                    ->filter(fn($r) => $r['nombre'] !== '' || $r['porcentaje'] !== '' || $r['cas'] !== '')
                    ->values()->all();
            };

            $data = [
                'pagina_actual'        => 1,
                'paginas_total'        => 1,
                'producto_titulo'      => $req->input('producto_nombre', 'PRODUCTO'),
                'pictogramas'          => $pictogramas,
                'componentes'          => $map3($req->input('componentes')),
                'aditivos'             => $map3($req->input('aditivos')),
                'consejos_prudencia'   => $req->input('consejos_prudencia', []),
                'indicaciones_peligro' => $req->input('indicaciones_peligro', []),
            ];

            $pdf = Pdf::loadView('formats.quality.03', $data)->setPaper('letter');
            $slug = Str::slug($data['producto_titulo'], '_');
            return $pdf->stream('HojaSeguridad_' . $slug . '_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in pdf3', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar Hoja de Seguridad: ' . $e->getMessage());
        }
    }

    public function pdf4(Request $req)
    {
        $validated = $req->validate([
            'inspection_id' => ['required', 'integer', 'exists:inspections_w,id'],
        ]);

        try {
            $inspection = InspectionW::with('observaciones')->findOrFail($validated['inspection_id']);
            $fmt = fn($d) => $d ? Carbon::parse($d)->format('d/m/Y') : null;

            $toDomPdfBase64 = function (?string $relativePath): ?string {
                if (!$relativePath) return null;
                $clean = ltrim(parse_url($relativePath, PHP_URL_PATH), '/');

                $candidates = array_values(array_filter([
                    base_path('../public_html/' . $clean),
                    public_path($clean),
                    storage_path('app/public/' . str_replace('storage/', '', $clean)),
                    public_path('storage/' . str_replace('storage/', '', $clean)),
                    base_path($clean),
                ]));

                $realFile = null;
                foreach ($candidates as $cand) {
                    if (file_exists($cand) && is_file($cand) && is_readable($cand)) {
                        $realFile = $cand;
                        break;
                    }
                }

                if (!$realFile) return null;

                $ext = strtolower(pathinfo($realFile, PATHINFO_EXTENSION));
                if ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
                    $img = @imagecreatefromwebp($realFile);
                    if ($img !== false) {
                        ob_start();
                        imagejpeg($img, null, 85);
                        $data = ob_get_clean();
                        imagedestroy($img);
                        return 'data:image/jpeg;base64,' . base64_encode($data);
                    }
                }

                $mime = match ($ext) {
                    'png'   => 'image/png',
                    'gif'   => 'image/gif',
                    'webp'  => 'image/webp',
                    'svg'   => 'image/svg+xml',
                    default => 'image/jpeg',
                };

                $content = @file_get_contents($realFile);
                return $content !== false ? 'data:' . $mime . ';base64,' . base64_encode($content) : null;
            };

            $obsRows = $inspection->observaciones->map(function ($o) use ($fmt, $toDomPdfBase64) {
                return [
                    'name'          => (string) $o->name,
                    'fecha'         => $fmt($o->fecha),
                    'evidencia_img' => $toDomPdfBase64($o->evidencia_path),
                    'ev_corr_img'   => $toDomPdfBase64($o->ev_corr_path),
                    'rev'           => $o->rev ?? null,
                    'cumple'        => ($o->rev ?? null) === 'cumple',
                    'no_cumple'     => ($o->rev ?? null) === 'no_cumple',
                    'evidencia'     => $o->evidencia ?? '',
                    'ev_corr'       => $o->ev_corr ?? '',
                    'ubicacion'     => $o->ubicacion ?? '',
                ];
            })->values()->all();

            $areasRaw = $inspection->area;
            if (is_string($areasRaw)) {
                $areasArr = json_decode($areasRaw, true);
                if (!is_array($areasArr)) $areasArr = array_map('trim', explode(',', $areasRaw));
            } elseif (is_array($areasRaw)) {
                $areasArr = $areasRaw;
            } else {
                $areasArr = [];
            }
            $areas = array_values(array_unique(array_filter($areasArr)));

            $normalizeTurno = function ($t) {
                $t = strtolower(trim((string)$t));
                $map = [
                    '1' => '1', '2' => '2', '3' => '3', 'mixto' => 'mixto',
                    'matutina' => '1', 'mañana' => '1', 'am' => '1',
                    'vespertina' => '2', 'tarde' => '2', 'pm' => '2',
                    'nocturna' => '3', 'noche' => '3',
                ];
                return $map[$t] ?? null;
            };

            $turnoNorm = $normalizeTurno($inspection->turno);

            $viewData = [
                'pagina_actual'    => 1,
                'paginas_total'    => 1,
                'fecha_inspeccion' => $fmt($inspection->fecha_inspeccion),
                'inspector'        => $inspection->inspector,
                'hora_turno'       => $inspection->hora_turno,
                'turno'            => $inspection->turno,
                'turno_norm'       => $turnoNorm,
                'turno_flags'      => [
                    't1' => $turnoNorm === '1',
                    't2' => $turnoNorm === '2',
                    't3' => $turnoNorm === '3',
                    'tm' => $turnoNorm === 'mixto',
                ],
                'area_nave1'       => in_array('nave1', $areas, true),
                'area_nave2'       => in_array('nave2', $areas, true),
                'area_otro_flag'   => in_array('otro', $areas, true),
                'area_otro'        => $inspection->area_otro,
                'responsable'      => $inspection->responsable,
                'fecha_correccion' => $fmt(optional($inspection->observaciones->first())->fecha),
                'comentarios'      => $inspection->comentarios,
                'comentarios_q'    => $inspection->comentarios_q,
                'obs_rows'         => $obsRows,
            ];

            $pdf = Pdf::loadView('formats.quality.almacen04', $viewData)->setPaper('letter');
            return $pdf->stream('LiberacionCalidad_' . $inspection->id . '_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in pdf4', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar PDF de liberación: ' . $e->getMessage());
        }
    }

    public function pdf6(Request $request)
    {
        try {
            $folio = $request->input('folio') ?: ('INC-' . now()->format('Ymd-His'));

            $relativePublicPaths = [];
            $descItemsForView = [];

            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $file) {
                    if (!$file->isValid()) continue;
                    $ext = $file->guessExtension() ?: 'jpg';
                    $name = uniqid('inc_', true) . '.' . $ext;
                    $stored = $file->storeAs('incidencias/' . $folio, $name, 'public');
                    $relativePublicPaths[] = 'storage/' . $stored;
                }
            }

            $descInput = $request->input('descripcion', []);
            if (is_string($descInput)) {
                $descArr = array_values(array_filter(preg_split("/\r\n|\n|\r/", $descInput)));
            } elseif (is_array($descInput)) {
                $descArr = $descInput;
            } else {
                $descArr = [];
            }

            $viewData = [
                'folio'            => $folio,
                'fecha_reporte'    => $request->input('fecha_reporte', now()->toDateString()),
                'supplier_name'    => $request->input('supplier_name', ''),
                'product_name'     => $request->input('product_name', ''),
                'lote'             => $request->input('lote', ''),
                'remitidos'        => $request->input('remitidos', ''),
                'fecha_incidencia' => $request->input('fecha_incidencia', now()->toDateString()),
                'incidencia'       => $request->input('incidencia', ''),
                'descripcion_rows' => $descArr,
                'imagenes'         => $relativePublicPaths,
                'comentarios'      => $request->input('comentarios', ''),
                'firma_nombre'     => $request->input('firma_nombre', ''),
            ];

            $pdf = Pdf::loadView('formats.quality.incidencias06', $viewData)->setPaper('a4', 'landscape');
            return $pdf->stream('ReporteIncidencias_' . $folio . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in pdf6', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar Reporte de Incidencias: ' . $e->getMessage());
        }
    }

    public function pdf7(Request $request)
    {
        try {
            $viewData = [
                'fecha'       => $request->input('fecha', now()->toDateString()),
                'titulo'      => $request->input('titulo', 'Instructivo de Calidad'),
                'descripcion' => $request->input('descripcion', ''),
            ];

            $pdf = Pdf::loadView('formats.quality.07', $viewData)->setPaper('a4', 'portrait');
            return $pdf->download('Instructivo_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in pdf7', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar instructivo: ' . $e->getMessage());
        }
    }

    public function pdf8(Request $req)
    {
        try {
            $rows = [];
            $batchs = (array) ($req->input('batch') ?? []);
            $observations = (array) ($req->input('observation') ?? []);
            $options = array_values((array) ($req->input('option') ?? []));

            foreach ($batchs as $i => $items) {
                if (is_array($items)) {
                    foreach ($items as $j => $batch) {
                        $rows[] = [
                            'batch'       => $batch,
                            'observation' => $observations[$i][$j] ?? null,
                            'options'     => $options[$i] ?? [],
                        ];
                    }
                }
            }

            $fechaRaw = (string)$req->input('fecha_inspeccion', now()->format('d/m/Y'));
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaRaw)) {
                try {
                    $fechaDMY = Carbon::parse($fechaRaw)->format('d/m/Y');
                } catch (\Throwable $e) {
                    $fechaDMY = $fechaRaw;
                }
            } else {
                $fechaDMY = $fechaRaw;
            }

            $supplier = null;
            if ($req->filled('supplier_id')) {
                $supplier = Supplier::where('supplier_id', $req->input('supplier_id'))->first();
            }
            $supplierName = $supplier?->name ?? (string)$req->input('supplier_name', 'STD Soluciones Tecnológicas Diversas');
            $supplierCode = $req->input('supplier_code') ?: ($supplier?->supplier_code ?? 'SPP133');

            $itemsRaw = (array) $req->input('items', []);
            $items = collect($itemsRaw)->values()->map(function ($it, $i) {
                $prodName = '';
                if (!empty($it['product_id'])) {
                    $prod = Product::where('product_id', $it['product_id'])->first();
                    $prodName = $prod?->name ?? '';
                } elseif (!empty($it['product_name'])) {
                    $prodName = $it['product_name'];
                }
                return [
                    'n'            => $i + 1,
                    'product_name' => $prodName,
                    'lote'         => $it['lote'] ?? '',
                    'presentacion' => $it['presentacion'] ?? '',
                    'cantidad'     => $it['cantidad'] ?? null,
                    'empaque'      => $it['empaque'] ?? '',
                ];
            })->all();

            $totalCantidad = collect($items)->sum(
                fn($i) => is_numeric($i['cantidad'] ?? null) ? (float)$i['cantidad'] : 0
            );

            $libRows = [
                'envase_sellado'  => ['val' => $req->input('lib_envase_sellado', ''),  'obs' => $req->input('lib_envase_sellado_obs', '')],
                'embalaje_limpio' => ['val' => $req->input('lib_embalaje_limpio', ''), 'obs' => $req->input('lib_embalaje_limpio_obs', '')],
                'identificacion'  => ['val' => $req->input('lib_identificacion', ''),  'obs' => $req->input('lib_identificacion_obs', '')],
                'otro'            => ['val' => $req->input('lib_otro', ''),            'obs' => $req->input('lib_otro_obs', '')],
            ];

            $trRows = [
                'sello_seguridad' => ['val' => $req->input('tr_sello_seguridad', ''), 'obs' => $req->input('tr_sello_seguridad_obs', '')],
                'fumigacion'      => ['val' => $req->input('tr_fumigacion', ''),      'obs' => $req->input('tr_fumigacion_obs', '')],
                'limpieza'        => ['val' => $req->input('tr_limpieza', ''),        'obs' => $req->input('tr_limpieza_obs', '')],
                'aromas'          => ['val' => $req->input('tr_aromas', ''),          'obs' => $req->input('tr_aromas_obs', '')],
                'puertas'         => ['val' => $req->input('tr_puertas', ''),         'obs' => $req->input('tr_puertas_obs', '')],
                'piso'            => ['val' => $req->input('tr_piso', ''),            'obs' => $req->input('tr_piso_obs', '')],
                'techo'           => ['val' => $req->input('tr_techo', ''),           'obs' => $req->input('tr_techo_obs', '')],
                'paredes'         => ['val' => $req->input('tr_paredes', ''),         'obs' => $req->input('tr_paredes_obs', '')],
                'otro'            => ['val' => $req->input('tr_otro', ''),            'obs' => $req->input('tr_otro_obs', '')],
            ];

            $incHas = (string)($req->input('inc_has', '0')) === '1';
            $rawFolio = trim((string)($req->input('inc_folio', '')));
            $descRaw  = trim((string)($req->input('inc_desc', '')));
            $actsRaw  = trim((string)($req->input('inc_actions', '')));
            $folioMostrar = $incHas
                ? (($rawFolio !== '' && strtolower($rawFolio) !== 'na') ? $rawFolio : 'S/F')
                : 'NA';

            $toList = function (string $txt) {
                if ($txt === '') return [];
                $lines = preg_split("/\r\n|\n|\r/", $txt);
                $out = [];
                foreach ($lines as $s) {
                    $s = ltrim(trim($s), "• \t");
                    if ($s !== '') $out[] = $s;
                }
                return $out;
            };
            $descList    = $toList($descRaw);
            $actionsList = $toList($actsRaw);

            $tarimaVal = strtolower((string)$req->input('tarima', ''));
            $tarimaStr = ($tarimaVal === 'si' || $tarimaVal === 'sí') ? 'Sí' : (($tarimaVal === 'no') ? 'No' : '');

            $viewData = [
                'generated_at'          => now()->format('d-M-Y H:i'),
                'fecha_actualizacion'   => '--',
                'version'               => '00',
                'codigo_formato'        => 'SSS-FOR-CAL-08',
                'proveedor'             => $supplierName,
                'codigo_proveedor'      => $supplierCode,
                'fecha_inspeccion'      => $fechaDMY,
                'items'                 => $items,
                'total_cantidad'        => $totalCantidad,
                'tarima'                => $tarimaStr,
                'lib_rows'              => $libRows,
                'tr_rows'               => $trRows,
                'inc' => [
                    'has'          => $incHas,
                    'folio'        => $folioMostrar,
                    'desc'         => $incHas ? $descRaw : null,
                    'actions'      => $incHas ? $actsRaw : null,
                    'desc_list'    => ($incHas && !empty($descList))    ? $descList    : null,
                    'actions_list' => ($incHas && !empty($actionsList)) ? $actionsList : null,
                ],
                'has_incidencias'       => $incHas,
                'inc_folio'             => $folioMostrar,
                'inc_descripcion'       => $descRaw,
                'inc_descripcion_items' => $descList,
                'inc_acciones'          => $actsRaw,
                'inc_acciones_items'    => $actionsList,
                'inspector_nombre'      => $req->input('inspector_nombre', ''),
                'rows'                  => $rows,
            ];

            $pdf = Pdf::loadView('formats.quality.inspeccion08', $viewData)->setPaper('a4', 'portrait');

            if ($req->filled('supplier_id')) {
                DB::table('pdf_clicks')->insert([
                    'reference_id' => (int) $req->input('supplier_id'),
                    'pdf_type'     => 'B',
                    'user_id'      => Auth::id() ?? 1,
                    'generated_at' => now(),
                ]);
            }

            return $pdf->stream('RCV-' . now()->format('Ymd-His') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in pdf8', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar inspección 08: ' . $e->getMessage());
        }
    }

    public function pdf9(Request $request)
    {
        $v = Validator::make($request->all(), [
            'fecha'                   => 'required|date',
            'customer_id'             => 'required|exists:customers,customer_id',
            'contacto'                => 'nullable|string|max:150',
            'telefono'                => 'nullable|string|max:50',
            'correo'                  => 'nullable|email|max:150',
            'direccion'               => 'nullable|string|max:255',
            'fecha_incidencia'        => 'required|date',
            'hora_incidencia'         => 'nullable',
            'producto_servicio'       => 'required|string|max:150',
            'descripcion'             => 'required|string',
            'impacto'                 => 'required|string',
            'importancia'             => 'required|in:Baja,Media,Alta',
            'resolucion'              => 'required|string',
            'medidas'                 => 'required|string',
            'responsable_accion'      => 'required|string|max:150',
            'fecha_resolucion'        => 'nullable|date',
            'accion_final'            => 'required|string',
            'comentarios_adicionales' => 'nullable|string',
            'evidencias'              => 'nullable|array|max:6',
            'evidencias.*'            => 'nullable|file|image|mimes:jpeg,jpg,png,webp|max:3072',
            'cliente_firma'           => 'nullable|string|max:150',
            'receptor_firma'          => 'nullable|string|max:150',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        try {
            $data = $v->validated();
            $customer = Customer::select('customer_id', 'name')->where('customer_id', $data['customer_id'])->firstOrFail();
            $data['empresa'] = $customer->name;

            foreach (['fecha', 'fecha_incidencia', 'fecha_resolucion'] as $f) {
                if (!empty($data[$f])) {
                    $data[$f] = Carbon::parse($data[$f])->locale('es')->translatedFormat('j \\d\\e F \\d\\e Y');
                }
            }

            $data['evidencias_base64'] = [];
            if ($request->hasFile('evidencias')) {
                foreach ($request->file('evidencias') as $file) {
                    if (!$file->isValid()) continue;
                    $mime = $file->getMimeType();
                    $b64  = base64_encode(file_get_contents($file->getRealPath()));
                    $data['evidencias_base64'][] = "data:{$mime};base64,{$b64}";
                }
            }

            $pdf = Pdf::loadView('formats.quality.retrocli09', $data)->setPaper('letter', 'portrait');
            return $pdf->stream('CartaGarantia_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in pdf9', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar carta de garantía: ' . $e->getMessage());
        }
    }

    public function pdf10(Request $request)
    {
        Carbon::setLocale('es');

        $v = Validator::make($request->all(), [
            'customer_id'                  => 'required|exists:customers,customer_id',
            'fecha_inspeccion'             => 'required|date',
            'items'                        => 'required|array|min:1',
            'items.*.product_id'           => 'required|exists:products,product_id',
            'items.*.lote'                 => 'required|string|max:100',
            'items.*.presentacion'         => 'nullable|string|max:150',
            'items.*.cantidad'             => 'nullable|numeric|min:0',
            'items.*.empaque'              => 'nullable|string|max:150',
            'lib_fauna_nociva'             => 'nullable|in:cumple,no_aplica',
            'lib_empaque_limpio'           => 'nullable|in:cumple,no_aplica',
            'lib_empaque_sin_rupturas'     => 'nullable|in:cumple,no_aplica',
            'lib_libre_materia_extrana'    => 'nullable|in:cumple,no_aplica',
            'lib_mezcla_homogenea'         => 'nullable|in:cumple,no_aplica',
            'lib_envase_sellado'           => 'nullable|in:cumple,no_aplica',
            'lib_marca_volumen'            => 'nullable|in:cumple,no_aplica',
            'lib_etiq_lote'                => 'nullable|in:cumple,no_aplica',
            'lib_etiq_nombre'              => 'nullable|in:cumple,no_aplica',
            'lib_etiq_caducidad'           => 'nullable|in:cumple,no_aplica',
            'lib_etiq_contenido'           => 'nullable|in:cumple,no_aplica',
            'lib_etiq_recomendaciones'     => 'nullable|in:cumple,no_aplica',
            'tr_fumigacion'                => 'nullable|in:cumple,no_aplica',
            'tr_limpieza'                  => 'nullable|in:cumple,no_aplica',
            'tr_aromas'                    => 'nullable|in:cumple,no_aplica',
            'tr_puertas'                   => 'nullable|in:cumple,no_aplica',
            'tr_piso'                      => 'nullable|in:cumple,no_aplica',
            'tr_techo'                     => 'nullable|in:cumple,no_aplica',
            'tr_paredes'                   => 'nullable|in:cumple,no_aplica',
            'tr_otro'                      => 'nullable|in:cumple,no_aplica',
            'tr_fumigacion_obs'            => 'nullable|string|max:300',
            'tr_limpieza_obs'              => 'nullable|string|max:300',
            'tr_aromas_obs'                => 'nullable|string|max:300',
            'tr_puertas_obs'               => 'nullable|string|max:300',
            'tr_piso_obs'                  => 'nullable|string|max:300',
            'tr_techo_obs'                 => 'nullable|string|max:300',
            'tr_paredes_obs'               => 'nullable|string|max:300',
            'tr_otro_obs'                  => 'nullable|string|max:300',
            'inspector_nombre'             => 'nullable|string|max:150',
            'observaciones'                => 'nullable',
            'evidencias'                   => 'nullable|array|max:3',
            'evidencias.*'                 => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        try {
            $data = $v->validated();

            if (array_key_exists('observaciones', $data)) {
                if (is_array($data['observaciones'])) {
                    $data['observaciones'] = implode("\n", array_filter(array_map(fn($val) => trim((string)$val), $data['observaciones'])));
                } else {
                    $data['observaciones'] = trim((string)$data['observaciones']);
                }
            } else {
                $data['observaciones'] = null;
            }

            $customer = DB::table('customers')
                ->select('customer_id', 'name', 'customer_code')
                ->where('customer_id', $data['customer_id'])
                ->firstOrFail();

            $data['cliente']        = $customer->name;
            $data['codigo_cliente'] = $customer->customer_code ?? '';

            if (!empty($data['fecha_inspeccion'])) {
                $data['fecha_inspeccion'] = Carbon::parse($data['fecha_inspeccion'])
                    ->locale('es')
                    ->translatedFormat('j \\d\\e F \\d\\e Y');
            }

            $items = collect($data['items'] ?? []);
            $productIds = $items->pluck('product_id')->filter()->unique();

            $namesById = DB::table('products')->whereIn('product_id', $productIds)->pluck('name', 'product_id');

            $data['items'] = $items->map(function ($row) use ($namesById) {
                $row['product_name'] = $namesById[$row['product_id']] ?? '';
                $row['cantidad'] = (isset($row['cantidad']) && $row['cantidad'] !== '') ? (is_numeric($row['cantidad']) ? (float)$row['cantidad'] : $row['cantidad']) : null;
                return $row;
            })->values()->toArray();

            $evidencias_base64 = [];
            if ($request->hasFile('evidencias')) {
                foreach ($request->file('evidencias') as $file) {
                    if ($file->isValid()) {
                        $type = $file->getClientMimeType();
                        $b64 = base64_encode(file_get_contents($file->getRealPath()));
                        $evidencias_base64[] = 'data:' . $type . ';base64,' . $b64;
                    }
                }
            }
            $data['evidencias'] = $evidencias_base64;

            $pdf = Pdf::loadView('formats.quality.salida10', $data)->setPaper('letter', 'portrait');

            DB::table('pdf_clicks')->insert([
                'reference_id' => (int) $data['customer_id'],
                'pdf_type'     => 'C',
                'user_id'      => Auth::id(),
                'generated_at' => now(),
            ]);

            return $pdf->stream('CertificadoCalidad_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in pdf10', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar certificado de calidad: ' . $e->getMessage());
        }
    }

    public function pdf11(Request $request)
    {
        Carbon::setLocale('es');

        $v = Validator::make($request->all(), [
            'fecha'                         => 'required|date',
            'supplier_id'                   => 'required|exists:suppliers,supplier_id',
            'contacto'                      => 'nullable|string|max:150',
            'telefono'                      => 'nullable|string|max:50',
            'correo'                        => 'nullable|email|max:150',
            'direccion'                     => 'nullable|string|max:255',
            'motivo'                        => 'required|string|max:255',
            'fecha_incidencia'              => 'nullable|date',
            'hora_incidencia'               => 'nullable',
            'producto_servicio'             => 'nullable|string|max:150',
            'referencia_contrato'           => 'nullable|string|max:150',
            'especificaciones'              => 'nullable|string',
            'impacto_consecuencias'         => 'nullable|string',
            'costo_adicional'               => 'nullable|in:si,no',
            'retraso_produccion'            => 'nullable|in:si,no',
            'importancia'                   => 'nullable|in:baja,media,alta',
            'medidas_iniciales'             => 'nullable|string',
            'propuesta_accion'              => 'nullable|string',
            'fecha_limite'                  => 'nullable|date',
            'accion_final'                  => 'nullable|string',
            'resp_verificacion'             => 'nullable|string|max:150',
            'fecha_verificacion'            => 'nullable|date',
            'resultados_verificacion'       => 'nullable|string',
            'especificaciones_imgs'         => 'nullable|array|max:6',
            'especificaciones_imgs.*'       => 'file|image|mimes:jpeg,jpg,png,webp|max:3072',
            'firma_responsable_success'     => 'nullable|string|max:150',
            'firma_representante_proveedor' => 'nullable|string|max:150',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        try {
            $data = $v->validated();
            $supplier = DB::table('suppliers')
                ->select('supplier_id', 'name', 'supplier_code')
                ->where('supplier_id', $data['supplier_id'])
                ->firstOrFail();

            $data['empresa']            = $supplier->name;
            $data['supplier_code']      = $supplier->supplier_code ?? '';
            $data['costo_adicional']    = $data['costo_adicional'] ?? 'no';
            $data['retraso_produccion'] = $data['retraso_produccion'] ?? 'no';
            $data['importancia']        = $data['importancia'] ?? 'media';

            foreach (['fecha', 'fecha_incidencia', 'fecha_limite', 'fecha_verificacion'] as $f) {
                if (!empty($data[$f])) {
                    $data[$f] = Carbon::parse($data[$f])->locale('es')->translatedFormat('j \\d\\e F \\d\\e Y');
                }
            }

            $data['especificaciones_imgs_b64'] = [];
            if ($request->hasFile('especificaciones_imgs')) {
                foreach ($request->file('especificaciones_imgs') as $file) {
                    if (!$file->isValid()) continue;
                    $mime = $file->getMimeType();
                    $b64  = base64_encode(file_get_contents($file->getRealPath()));
                    $data['especificaciones_imgs_b64'][] = "data:{$mime};base64,{$b64}";
                }
            }

            $pdf = Pdf::loadView('formats.quality.retropro11', $data)->setPaper('letter', 'portrait');
            return $pdf->download('RetroalimentacionProveedor_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in pdf11', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar retroalimentación proveedor: ' . $e->getMessage());
        }
    }

    public function pdf12(Request $request)
    {
        Carbon::setLocale('es');

        $v = Validator::make($request->all(), [
            'fecha'            => 'required|date',
            'tipo'             => 'required|in:queja,reclamo,sugerencia,felicitacion',
            'nombre'           => 'required|string|max:150',
            'persona'          => 'required|in:cliente,proveedor,trabajador,visita',
            'empresa'          => 'nullable|string|max:150',
            'area'             => 'nullable|string|max:150',
            'puesto'           => 'nullable|string|max:150',
            'correo'           => 'nullable|email|max:150',
            'telefono'         => 'nullable|string|max:50',
            'motivos'          => 'nullable|array',
            'motivos.*'        => 'in:calidad_producto,plazo_entrega,soporte_tecnico,atencion_personal,otro',
            'motivo_otro'      => 'nullable|string|max:200',
            'descripcion'      => 'required|string',
            'respuesta_email'  => 'required|in:si,no',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        try {
            $data = $v->validated();

            if (!empty($data['fecha'])) {
                $data['fecha'] = Carbon::parse($data['fecha'])->locale('es')->translatedFormat('j \\d\\e F \\d\\e Y');
            }

            $motivos = (array) ($data['motivos'] ?? []);
            $data['m_calidad_producto']  = in_array('calidad_producto', $motivos, true);
            $data['m_plazo_entrega']     = in_array('plazo_entrega', $motivos, true);
            $data['m_soporte_tecnico']   = in_array('soporte_tecnico', $motivos, true);
            $data['m_atencion_personal'] = in_array('atencion_personal', $motivos, true);
            $data['m_otro']              = in_array('otro', $motivos, true);
            $data['motivo_otro']         = $data['motivo_otro'] ?? '';

            $pdf = Pdf::loadView('formats.quality.quejas12', $data)->setPaper('letter', 'portrait');
            return $pdf->download('Quejas_Sugerencias_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error in pdf12', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al generar formato de quejas: ' . $e->getMessage());
        }
    }
}
