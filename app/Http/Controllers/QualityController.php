<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\InspectionW;
use App\Models\Observacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

use PhpParser\Node\Expr\Print_;

use function Pest\Laravel\options;

class QualityController extends Controller
{
        public function index()
        {
            $products  = Product::orderBy('name')->get(['product_id','name']);

            $suppliers = DB::table('suppliers')
                ->select('supplier_id', 'supplier_code', 'name')
                ->orderBy('name')
                ->get();

            $customers = DB::table('customers')
                ->select('customer_id','name')   
                ->get();

            return view('quality', compact('products', 'suppliers', 'customers'));
        }

        public function incidencias()
        {
            $rows = DB::table('incidencias')
                ->select('folio','descripcion')
                ->orderByDesc('fecha_incidencia')
                ->limit(100)
                ->get();

            return response()->json($rows);
        }

        public function store(Request $req)
        {
            $supplierCode = trim((string)$req->input('supplier'));
            $supplierName = trim((string)$req->input('supplier_name'));
            $arrival      = $req->input('arrival_date');
            $inspection   = $req->input('inspection_date');

            $fmtDate = function ($v) {
                if (!$v) return null;
                try { return \Carbon\Carbon::parse($v)->format('d/m/Y'); }
                catch (\Throwable $e) { return null; }
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
                if ($v < 0)   $v = 0;
                if ($v > $m)  $v = $m;

                $evals[$k] = $v;
                $obs[$k]   = isset($inObs[$k]) ? trim((string) $inObs[$k]) : '';
                $total    += $v;
            }
            $status = $total >= 70 ? 'ACEPTABLE' : 'NO ACEPTABLE';

            $isTrue = function ($v) {
                $v = strtolower(trim((string)$v));
                return in_array($v, ['1','si','sí','on','true','yes'], true);
            };
            $stripBulletsLine = function (string $s) {
                $s = trim($s);
                return preg_replace('/^\s*(?:[\-\*\x{2022}\x{25CF}\x{00B7}•]+|\d+[\.\)])\s*/u', '', $s);
            };
            $toList = function (string $txt) use ($stripBulletsLine) {
                if ($txt === '') return [];
                $out = [];
                foreach (preg_split("/\r\n|\n|\r/", $txt) as $line) {
                    $line = $stripBulletsLine($line);
                    if ($line !== '') $out[] = $line;
                }
                return $out;
            };

            $inc       = (array)$req->input('incidents', []);
            $hasInc    = $isTrue($inc['has'] ?? 0);
            $folioRawI = trim((string)($inc['folio'] ?? ''));
            $folioMostrar = $hasInc
                ? (($folioRawI !== '' && strtolower($folioRawI) !== 'na') ? $folioRawI : 'S/F')
                : 'NA';

            $descRaw   = trim((string)($inc['description'] ?? ''));
            $actsRaw   = trim((string)($inc['actions']     ?? ''));
            $descItems = $toList($descRaw);
            $actsItems = $toList($actsRaw);

            if ($hasInc && $folioMostrar !== 'NA' && $folioMostrar !== 'S/F') {
                $row = \DB::table('incidencias')->where('folio', $folioMostrar)->first();
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
                $certificate = \DB::table('supplier_certificates')->find($certId);
            }

            $data = [
                'paginas_total'      => 1,
                'proveedor'          => $supplierName,
                'codigo_proveedor'   => $supplierCode,
                'fecha_llegada'      => $arrivalDMY,
                'fecha_inspeccion'   => $inspectionDMY,
                'productos'          => $productos,
                'total_cantidad'     => $totalQty,
                'evals'              => $evals,
                'obs'                => $obs,
                'release_total'      => $total,
                'release_status'     => $status,
                'hasInc'                => $hasInc,
                'folioInc'              => $folioMostrar,
                'descItems'             => $descItems,
                'descTexto'             => $descRaw,
                'actItems'              => $actsItems,
                'actTexto'              => $actsRaw,
                'inspector_nombre'      => $inspectorNombre,
                'has_certificate'       => $hasCert,
                'certificate'           => $certificate,
                'producto_liberado'     => $productoLiberado,     
                'folio'                 => $folioLiberacionMostrar, 
            ];

            $pdf = \PDF::loadView('formats.quality.recepcion01', $data)->setPaper('letter');
            return $pdf->download('inspeccion_'.now()->format('Ymd_His').'.pdf');
        }

        public function getData()
        {
            $suppliers = DB::table('suppliers')->select('supplier_code','name')->get();
            return response()->json(['suppliers'=>$suppliers]);
        }

        public function getProducts()
        {
            $rows = DB::table('inventory as i')
                ->leftJoin('products as p', 'p.product_id', '=', 'i.product_id')
                ->select('p.product_id', 'p.name', 'i.batch')
                ->orderBy('p.name')
                ->get();

            $grouped = $rows->groupBy('product_id')->map(function($items){
                return [
                    'product_id' => $items->first()->product_id,
                    'name'       => $items->first()->name,
                    'batches'    => $items->pluck('batch')->filter()->unique()->values()->all(),
                ];
            })->values();

            return response()->json(['products' => $grouped]);
        }

        public function pdf2(Request $req)
        {
            $req->validate([
                'product_image'   => 'nullable|file|image|mimes:jpeg,jpg,png,webp|max:5120',
                'nutricional_img' => 'nullable|file|image|mimes:jpeg,jpg,png,webp|max:5120',
            ]);

            $producto = null;

            if ($req->filled('product_id')) {
                $producto = Product::select('product_id','name')->find($req->input('product_id'));
            } else {
                $firstProd = collect($req->input('products', []))->first();
                if ($firstProd) {
                    if (!empty($firstProd['product_id'])) {
                        $producto = Product::select('product_id','name')->find($firstProd['product_id']);
                    } elseif (!empty($firstProd['name'])) {
                        $producto = Product::select('product_id','name')
                            ->where('name', $firstProd['name'])->first();
                    }
                }
            }

            $productoTitulo = $producto->name ?? 'NOMBRE PRODUCTO';
            $imagenPath = null;
            if ($req->hasFile('product_image') && $req->file('product_image')->isValid()) {
                $stored = $req->file('product_image')->store('products', 'public');
                $imagenPath = public_path('storage/'.$stored);
            }

            $nutricionalPath = null;
            if ($req->hasFile('nutricional_img') && $req->file('nutricional_img')->isValid()) {
                $storedNutri = $req->file('nutricional_img')->store('nutrition', 'public');
                $nutricionalPath = public_path('storage/'.$storedNutri);
            }

            $mapKV = fn($rows) => collect($rows ?: [])
                ->map(fn($r) => [
                    'k' => isset($r['k']) ? trim((string)$r['k']) : '',
                    'v' => isset($r['v']) ? trim((string)$r['v']) : '',
                ])
                ->filter(fn($r) => $r['k'] !== '' || $r['v'] !== '')
                ->values()
                ->all();

            $toNullable = function($val) {
                $t = trim((string) ($val ?? ''));
                return $t === '' ? null : $t;
            };

            $carOrg     = $mapKV($req->input('car_org'));
            $carFis     = $mapKV($req->input('car_fis'));
            $macroelems = $mapKV($req->input('macro'));
            $microelems = $mapKV($req->input('micro'));
            $microbio   = $mapKV($req->input('microbio'));
            $descripcionBreve = $toNullable($req->input('product_desc'));
            $inst_tecnicas    = $toNullable($req->input('inst_tecnicas'));
            $almacenamiento   = $toNullable($req->input('almacenamiento'));
            $presentacion     = $toNullable($req->input('presentacion')); 
            $vida_anaquel     = $toNullable($req->input('vida_anaquel'));
            $uso_aplicaciones = $toNullable($req->input('uso_aplicaciones'));
            $org_apa          = $toNullable($req->input('org_apa'));
            $org_color        = $toNullable($req->input('org_color'));
            $org_olor         = $toNullable($req->input('org_olor'));
            $presentacion_img = [];
            if ($req->has('presentacion_img')) {
                $raw = $req->input('presentacion_img');
                if (is_array($raw)) {
                    $presentacion_img = collect($raw)
                        ->map(fn($f) => trim((string)$f))
                        ->filter()
                        ->values()
                        ->all();
                } elseif (is_string($raw)) {
                    $decoded = json_decode($raw, true);
                    if (is_array($decoded)) {
                        $presentacion_img = collect($decoded)
                            ->map(fn($f) => trim((string)$f))
                            ->filter()
                            ->values()
                            ->all();
                    }
                }
            }

            $presentacion_base = asset('images/presentaciones');
            $propInput = $req->input('prop', []);
            $propOrder = [
                'tipo'            => 'Tipo',
                'parte_extraida'  => 'Parte extraída',
                'activo'          => 'Activo',
            ];
            $prop = [];
            foreach ($propOrder as $key => $label) {
                $val = $toNullable($propInput[$key] ?? null);
                if ($val !== null) {
                    $prop[] = ['k' => $label, 'v' => $val];
                }
            }

            $data = [
                'pagina_actual'     => 1,
                'paginas_total'     => 1,
                'producto_titulo'   => $productoTitulo,
                'descripcion_breve' => $descripcionBreve,
                'imagen_path'       => $imagenPath,       
                'nutricional_path'  => $nutricionalPath,   
                'org_apa'           => $org_apa,
                'org_color'         => $org_color,
                'org_olor'          => $org_olor,
                'car_org'           => $carOrg,
                'car_fis'           => $carFis,
                'macroelems'        => $macroelems,
                'microelems'        => $microelems,
                'microbio'          => $microbio,
                'inst_tecnicas'     => $inst_tecnicas,
                'almacenamiento'    => $almacenamiento,
                'presentacion'      => $presentacion,        
                'presentacion_img'  => $presentacion_img,    
                'presentacion_base' => $presentacion_base,   
                'vida_anaquel'      => $vida_anaquel,
                'uso_aplicaciones'  => $uso_aplicaciones,
                'prop'              => $prop,
            ];

            $pdf = Pdf::loadView('formats.quality.02', $data)
                    ->setPaper('letter');
            $slug = \Illuminate\Support\Str::slug($productoTitulo, '_');
            return $pdf->download("ficha_tecnica_{$slug}_".now()->format('Ymd_His').".pdf");
        }

        public function pdf3(Request $req)
        {
            $req->validate([
                'product_id'    => ['nullable','integer','exists:products,product_id'],
                'pictos'        => ['array'],
                'pictos.*'      => ['in:explosion,inflamable,comburente,gas-a-presion,corrosivo,toxicidad-aguda,peligro-salud,peligro-grave-para-la-salud'],
                'pictogramas'   => ['nullable','array','max:6'],
                'pictogramas.*' => ['nullable','file','image','mimes:jpeg,jpg,png,webp','max:2048'],
            ]);

            $producto = $req->filled('product_id')
                ? Product::whereKey($req->input('product_id'))->value('name')
                : null;
            $producto = $producto ?: trim((string) $req->input('producto', 'NOMBRE DEL PRODUCTO'));

            $PICTO_MAP = [
                'explosion'                   => public_path('images/ghs/explosion.jpg'),
                'inflamable'                  => public_path('images/ghs/inflamable.jpg'),
                'comburente'                  => public_path('images/ghs/comburente.jpg'),
                'gas-a-presion'               => public_path('images/ghs/gas-a-presion.jpg'),
                'corrosivo'                   => public_path('images/ghs/corrosivo.jpg'),
                'toxicidad-aguda'             => public_path('images/ghs/toxicidad-aguda.jpg'),
                'peligro-salud'               => public_path('images/ghs/peligro-salud.jpg'),
                'peligro-grave-para-la-salud' => public_path('images/ghs/peligro-grave-para-la-salud.jpg'),
            ];

            $pictogramas = [];
            $selected = array_values(array_unique(array_filter((array)$req->input('pictos', []))));
            foreach ($selected as $slug) {
                if (isset($PICTO_MAP[$slug]) && file_exists($PICTO_MAP[$slug])) {
                    $pictogramas[] = $PICTO_MAP[$slug];
                    if (count($pictogramas) >= 6) break;
                }
            }
            if (count($pictogramas) < 6 && $req->hasFile('pictogramas')) {
                foreach ($req->file('pictogramas') as $file) {
                    if ($file && $file->isValid()) {
                        $stored = $file->store('sds/pictos', 'public');
                        $pictogramas[] = public_path('storage/'.$stored);
                        if (count($pictogramas) >= 6) break;
                    }
                }
            }
            $map3 = function($rows){
                return collect($rows ?: [])
                    ->map(function($r){
                        return [
                            'nombre'     => trim($r['nombre']     ?? ''),
                            'porcentaje' => trim($r['porcentaje'] ?? ''),
                            'cas'        => trim($r['cas']        ?? ''),
                        ];
                    })
                    ->filter(fn($r) => $r['nombre'] !== '' || $r['porcentaje'] !== '' || $r['cas'] !== '')
                    ->values()->all();
            };
            $componentes = $map3($req->input('componentes'));
            $aditivos    = $map3($req->input('aditivos'));
            $mapHP = function ($rows, string $prefix) {
                return collect($rows ?: [])
                    ->map(function ($r) use ($prefix) {
                        $code = strtoupper(trim($r['code'] ?? ''));
                        $text = trim($r['text'] ?? '');
                        if ($code !== '' && !preg_match('/^[HP]\d{3}(?:\+\d{3})?$/i', $code)) {
                            if (preg_match('/^\d{3}(?:\+\d{3})?$/', $code)) $code = $prefix.$code;
                        }
                        return ['code' => $code, 'text' => $text];
                    })
                    ->filter(fn($r) => $r['code'] !== '' || $r['text'] !== '')
                    ->values()->all();
            };
            $parseCodes = function (?string $txt, string $prefix) {
                $txt = trim((string) $txt);
                if ($txt === '') return [];
                $items = preg_split('/[\r\n;]+/', $txt);
                $rows = [];
                foreach ($items as $raw) {
                    $raw = trim($raw);
                    if ($raw === '') continue;
                    if (preg_match('/\b(' . $prefix . '\d{3}(?:\+\d{3})?)\s*[:\-]?\s*(.+)$/i', $raw, $m)) {
                        $rows[] = ['code' => strtoupper($m[1]), 'text' => trim($m[2])];
                    } else {
                        $rows[] = ['code' => $prefix.'—', 'text' => $raw];
                    }
                }
                return $rows;
            };
            $h_codes = $mapHP($req->input('h_codes'), 'H');
            $p_codes = $mapHP($req->input('p_codes'), 'P');
            if (empty($h_codes)) $h_codes = $parseCodes($req->input('indicadores_peligro', ''), 'H');
            if (empty($p_codes)) $p_codes = $parseCodes($req->input('consejos_precaucion', ''), 'P');

            $textFields = [
                'uso','sinonimo',
                'aux_ojos','aux_piel','aux_ingestion','aux_inhalacion','aux_sintomas','aux_agudos','aux_tratamiento',
                'extincion','peligros_incendio','quimica_peligrosa','medidas_especiales',
                'fuga_equipo','fuga_precauciones','fuga_metodos',
                'manejo_seguro','almacenamiento_seguro',
                'control_parametros','controles_tecnicos','proteccion_personal',
                'apariencia','color','olor','umbral_olfativo','ph','fusion','ebullicion','inflamacion','evaporacion',
                'inflamabilidad','limite_inflamabilidad','presion_vapor','densidad_vapor','densidad_relativa',
                'solubilidad','coef_particion','ignicion','descomposicion','viscosidad','peso_molecular',
                'reactividad','estabilidad_quimica','reacciones','condiciones','incompatibles','productos',
                'toxicidad_aguda','irritacion_cutanea','irritacion_ocular','sensibilizacion','mutagenicidad',
                'carcinogenicidad','reproduccion','sistemica_unica','sistemica_repetida','aspiracion',
                'eco_toxicidad','eco_persistencia','eco_bioacumulacion','eco_movilidad','eco_otros',
                'eliminacion_metodos',
                'trans_onu','trans_designacion','trans_clase','trans_embalaje','trans_riesgos','trans_precauciones','trans_granel',
                'fecha_elaboracion','revision','referencia','disclaimer',
            ];

            $data = [
                'pagina_actual'  => 1,
                'paginas_total'  => 1,
                'producto'       => $producto,
                'pictogramas'    => $pictogramas,
                'componentes'    => $componentes,
                'aditivos'       => $aditivos,
                'h_codes'        => $h_codes,
                'p_codes'        => $p_codes,
            ];
            foreach ($textFields as $f) {
                $v = $req->input($f, null);
                if (is_string($v)) $v = trim($v);
                if ($v !== null && $v !== '') $data[$f] = $v;
            }
            $transKeys = ['trans_onu','trans_designacion','trans_clase','trans_embalaje','trans_riesgos','trans_precauciones','trans_granel'];
            $data['has_transport'] = collect($transKeys)->contains(function($k) use ($req){
                return trim((string)$req->input($k, '')) !== '';
            });

            $pdf    = Pdf::loadView('formats.quality.03', $data)->setPaper('letter');
            $dompdf = $pdf->getDomPDF();
            $dompdf->render();
            $canvas = $dompdf->get_canvas();
            $w      = $canvas->get_width();
            $h      = $canvas->get_height();
            $fm   = $dompdf->getFontMetrics();
            $font = $fm->getFont('DejaVu Sans', 'normal');
            $size = 7;                      
            $text = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';
            $ml = 24 * 0.75;
            $mr = 24 * 0.75;
            $innerW = $w - $ml - $mr;
            $colStart = $ml + ($innerW * 0.80);
            $colWidth = $innerW * 0.20;
            $pad = 10 * 0.75;               
            $colStart += $pad;
            $colWidth -= $pad * 2;
            $textWidth = $fm->getTextWidth($text, $font, $size);
            $x = $colStart + max(0, ($colWidth - $textWidth) / 2);
            $offset = 29; 
            $x += $offset;
            $y = 58; 
            $canvas->page_text($x, $y, $text, $font, $size, [0,0,0]);
            $slug = Str::slug($producto, '_');
            return $pdf->download("hoja_de_seguridad_{$slug}_".now()->format('Ymd_His').".pdf");
        }

        public function pdf4(Request $req)
        {
            $validated = $req->validate([
                'inspection_id' => ['required','integer','exists:inspections_w,id'],
            ]);

            $inspection = InspectionW::with('observaciones')->findOrFail($validated['inspection_id']);

            $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : null;

            // Función para resolver y convertir imágenes a Base64
            // Resuelve restricciones de chroot en DomPDF y compatibilidad con WebP
            $toDomPdfBase64 = function (?string $relativePath): ?string {
                if (!$relativePath) {
                    return null;
                }

                $clean = ltrim(parse_url($relativePath, PHP_URL_PATH), '/');

                // 1. Matriz de directorios candidatos (prioriza public_html hermano de producción)
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

                if (!$realFile) {
                    \Illuminate\Support\Facades\Log::warning("DomPDF [pdf4]: No se encontró la imagen en disco: {$relativePath}");
                    return null;
                }

                $ext = strtolower(pathinfo($realFile, PATHINFO_EXTENSION));

                // 2. Si es WebP, convertir a JPEG en memoria con GD porque DomPDF no soporta WebP nativo
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
                if ($content === false) {
                    return null;
                }

                return 'data:' . $mime . ';base64,' . base64_encode($content);
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

            $isNave1 = in_array('nave1', $areas, true);
            $isNave2 = in_array('nave2', $areas, true);
            $isOtro  = in_array('otro',  $areas, true);

            $normalizeTurno = function($t) {
                $t = strtolower(trim((string)$t));
                $map = [
                    '1'=>'1','2'=>'2','3'=>'3','mixto'=>'mixto',
                    'matutina'=>'1','mañana'=>'1','am'=>'1',
                    'vespertina'=>'2','tarde'=>'2','pm'=>'2',
                    'nocturna'=>'3','noche'=>'3',
                ];
                return $map[$t] ?? null;
            };
            $turnoNorm = $normalizeTurno($inspection->turno);
            $turnoFlags = [
                't1' => $turnoNorm === '1',
                't2' => $turnoNorm === '2',
                't3' => $turnoNorm === '3',
                'tm' => $turnoNorm === 'mixto',
            ];

            $viewData = [
                'pagina_actual'    => 1,
                'paginas_total'    => 1,
                'fecha_inspeccion' => $fmt($inspection->fecha_inspeccion),
                'inspector'        => $inspection->inspector,
                'hora_turno'       => $inspection->hora_turno,
                'turno'            => $inspection->turno,  
                'turno_norm'       => $turnoNorm,           
                'turno_flags'      => $turnoFlags,          
                'area_nave1'       => $isNave1,
                'area_nave2'       => $isNave2,
                'area_otro_flag'   => $isOtro,
                'area_otro'        => $inspection->area_otro,
                'responsable'      => $inspection->responsable,
                'fecha_correccion' => $fmt(optional($inspection->observaciones->first())->fecha),
                'comentarios'      => $inspection->comentarios,
                'comentarios_q'    => $inspection->comentarios_q, 
                'obs_rows'         => $obsRows,
            ];

            try {
                $pdf    = \Barryvdh\DomPDF\Facade\Pdf::loadView('formats.quality.almacen04', $viewData)
                            ->setPaper('letter');

                $dompdf = $pdf->getDomPDF();
                $dompdf->set_option('isHtml5ParserEnabled', true);
                $dompdf->set_option('isRemoteEnabled', true);

                // Configurar chroot para permitir el directorio public_html hermano en producción
                $chroots = array_values(array_filter([
                    realpath(base_path()),
                    realpath(base_path('../public_html')),
                    realpath(public_path()),
                    realpath(storage_path('app/public')),
                ]));
                if (!empty($chroots)) {
                    $dompdf->set_option('chroot', $chroots);
                }

                $dompdf->render();
                $canvas = $dompdf->get_canvas();
                $w      = $canvas->get_width();
                $fm     = $dompdf->getFontMetrics();
                $font   = $fm->getFont('DejaVu Sans', 'normal');
                $size   = 7;
                $text   = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';
                $ml = 24 * 0.75; $mr = 24 * 0.75; $innerW = $w - $ml - $mr;
                $colStart = $ml + ($innerW * 0.80); $colWidth = $innerW * 0.20;
                $pad = 10 * 0.75; $colStart += $pad; $colWidth -= $pad * 2;
                $textWidth = $fm->getTextWidth($text, $font, $size);
                $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 29;
                $y = 58;
                $canvas->page_text($x, $y, $text, $font, $size, [0,0,0]);

                \Illuminate\Support\Facades\DB::table('pdf_clicks')->insert([
                    'reference_id' => (int) $validated['inspection_id'],
                    'pdf_type'     => 'A',
                    'user_id'      => \Illuminate\Support\Facades\Auth::id(),
                    'generated_at' => now(),
                ]);

                $slug = \Illuminate\Support\Str::slug('inspeccion_almacen', '_');
                return $pdf->download($slug.'_'.now()->format('Ymd_His').'.pdf');

            } catch (\Throwable $e) {
                report($e);
                return back()->withErrors(['pdf' => 'No se pudo generar el PDF 4. Revisa vista/datos e imágenes locales.']);
            }
        }

        public function almacenStore(Request $req)
        {
            $mapTurno = [
                '1' => '1', '2' => '2', '3' => '3', 'mixto' => 'mixto',
                'matutina' => '1', 'mañana' => '1', 'am' => '1',
                'vespertina' => '2', 'tarde' => '2', 'pm' => '2',
                'nocturna' => '3', 'noche' => '3',
            ];
            $rawTurno = strtolower(trim((string) $req->input('turno')));
            $turnoNorm = $mapTurno[$rawTurno] ?? null;

            if ($turnoNorm !== null) {
                $req->merge(['turno' => $turnoNorm]);
            } else {
                if ($rawTurno === '') {
                    $req->merge(['turno' => null]);
                }
            }

            $v = $req->validate([
                'fecha_inspeccion'     => ['nullable','date'],
                'inspector'            => ['nullable','string','max:255'],
                'hora_turno'           => ['nullable','date_format:H:i'],
                'turno'                => ['nullable', 'in:1,2,3,mixto'],
                'area'                 => ['nullable','array'],
                'area.*'               => ['in:nave1,nave2,otro'],
                'area_otro'            => ['nullable','string','max:255'],
                'responsable'          => ['nullable','string','max:255'],
                'comentarios'          => ['nullable','string'],
                'comentarios_q'        => ['nullable','string'],
                'obs'                  => ['nullable','array'],
                'obs.*.name'           => ['nullable','string','max:255'],
                'obs.*.rev'            => ['nullable','in:cumple,no_cumple'],
                'obs.*.fecha'          => ['nullable','date'],
                'obs.*.evidencia_file' => ['nullable','file','image','mimes:jpeg,jpg,png,webp','max:5120'],
                'obs.*.ubicacion'      => ['nullable','string','max:255'],
            ]);

            DB::transaction(function() use ($req, $v) {
                $rec = new InspectionW();
                $rec->fecha_inspeccion = $v['fecha_inspeccion'] ?? null;
                $rec->inspector        = $v['inspector'] ?? null;
                $rec->hora_turno       = $v['hora_turno'] ?? null;
                $rec->turno            = $v['turno'] ?? null;
                $rec->area             = !empty($v['area'] ?? []) ? array_values($v['area']) : null;
                $rec->area_otro        = $v['area_otro'] ?? null;
                $rec->responsable      = $v['responsable'] ?? null;
                $rec->comentarios      = $v['comentarios'] ?? null;
                $rec->comentarios_q    = $v['comentarios_q'] ?? null;
                $rec->user_id          = optional($req->user())->id;
                $rec->save();

                $obsIn = $req->input('obs', []);
                foreach ($obsIn as $i => $row) {
                    $name  = trim((string)($row['name'] ?? ''));
                    $rev   = in_array(($row['rev'] ?? ''), ['cumple','no_cumple'], true) ? $row['rev'] : null;
                    $fecha = $row['fecha'] ?? null;
                    $evidencia_path = null;

                    if ($req->hasFile("obs.$i.evidencia_file")) {
                        $f = $req->file("obs.$i.evidencia_file");
                        if ($f && $f->isValid()) {
                            $ext = $f->guessExtension() ?: 'jpg';
                            $filename = time() . '_' . Str::uuid() . '.' . $ext;
                            $targetBase = is_dir(base_path('../public_html')) ? base_path('../public_html') : public_path();
                            $targetDir = $targetBase . '/inspections_w/evidencias';
                            if (!file_exists($targetDir)) {
                                @mkdir($targetDir, 0755, true);
                            }
                            $f->move($targetDir, $filename);
                            $evidencia_path = 'inspections_w/evidencias/' . $filename;
                        }
                    }

                    if ($name === '' && !$evidencia_path && !$fecha && !$rev) {
                        continue;
                    }

                    Observacion::create([
                        'inspection_id'  => $rec->id,
                        'name'           => ($name !== '') ? $name : null,
                        'rev'            => $rev,
                        'fecha'          => $fecha ?: null,
                        'evidencia_path' => $evidencia_path,
                        'ubicacion'      => isset($row['ubicacion']) && trim($row['ubicacion']) !== '' ? trim($row['ubicacion']) : null,
                    ]);
                }
            });

            return back()->with('ok', 'Inspección de almacén guardada correctamente.');
        }
        public function getInspections()
        {
            $user = \Auth::user();

            $inspections = InspectionW::with(['observaciones' => function ($q) {
                    $q->orderBy('id');
                }])
                ->select(
                    'id',
                    'fecha_inspeccion',
                    'inspector',
                    'hora_turno',
                    'turno',
                    'area',
                    'area_otro',
                    'responsable',
                    'comentarios',
                    'comentarios_q',
                    'status'
                )
                ->get();

            $fmtYmd = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('Y-m-d') : null;
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
            $turnoLabel = fn($norm) => [
                '1' => '1',
                '2' => '2',
                '3' => '3',
                'mixto' => 'Mixto',
            ][$norm] ?? null;

            $normalizeArea = function ($raw) {
                if (is_array($raw)) {
                    $arr = $raw;
                } elseif (is_string($raw)) {
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $arr = $decoded;
                    } else {
                        $arr = array_map('trim', array_filter(explode(',', $raw)));
                    }
                } else {
                    $arr = [];
                }
                $allowed = ['nave1','nave2','otro'];
                $arr = array_values(array_unique(array_filter($arr, fn($v) => in_array($v, $allowed, true))));
                return $arr;
            };

            $payload = $inspections->map(function ($r) use ($fmtYmd, $normalizeTurno, $turnoLabel, $normalizeArea, $user) {
                $obs = $r->observaciones->map(function ($o) use ($fmtYmd) {
                    $evi = $o->evidencia_path;
                    $cor = $o->ev_corr_path;
                    return [
                        'id'            => $o->id,
                        'name'          => $o->name,
                        'rev'           => $o->rev,
                        'fecha'         => $fmtYmd($o->fecha),
                        'evidencia_url' => $evi ? asset($evi) : null,
                        'ev_corr_url'   => $cor ? asset($cor) : null,
                        'evidencia_path'=> $evi,
                        'ev_corr_path'  => $cor,
                        'ubicacion' => $o->ubicacion,
                    ];
                })->values()->all();

                $turnoRaw  = $r->turno;
                $turnoNorm = $normalizeTurno($turnoRaw);
                $turnoLbl  = $turnoLabel($turnoNorm);
                $areaArr = $normalizeArea($r->area);

                return [
                    'id'               => $r->id,
                    'status'           => $r->status,
                    'fecha_inspeccion' => $fmtYmd($r->fecha_inspeccion),
                    'inspector'        => $r->inspector,
                    'hora_turno'       => $r->hora_turno,
                    'turno_raw'        => $turnoRaw,     
                    'turno'            => $turnoNorm,  
                    'turno_label'      => $turnoLbl,  
                    'area'             => $areaArr,      
                    'area_otro'        => $r->area_otro,
                    'responsable'      => $r->responsable,
                    'comentarios'      => $r->comentarios,
                    'comentarios_q'    => $r->comentarios_q,
                    'obs'              => $obs,
                    'canUpdate'        => $user?->can('quality.update')  ?? false,
                    'canDelete'        => $user?->can('quality.delete')  ?? false,
                    'canUpdateW'       => $user?->can('quality.updateW') ?? false,
                ];
            });

            return response()->json(['inspections' => $payload], 200);
        }
        public function pdf6(Request $request)
        {
            $coalesce = function (Request $r, array $keys) {
                foreach ($keys as $k) {
                    $v = $r->input($k);
                    if ($v !== null && $v !== '') return $v;
                }
                return null;
            };
            
            $digits = function ($v) {
                if (!is_string($v) && !is_numeric($v)) return null;
                if (preg_match('/\d+/', (string)$v, $m)) return (int)$m[0];
                return null;
            };

            $supplierId = $coalesce($request, ['supplier_id', 'proveedor_id', 'supplier']);
            if (!$supplierId) {
                $supCode = $coalesce($request, ['supplier_code','codigo_proveedor','proveedor_codigo','codigo']);
                if ($supCode) {
                    $supplierId = DB::table('suppliers')->where('supplier_code', $supCode)->value('supplier_id');
                }
                if (!$supplierId) {
                    $supName = $coalesce($request, ['supplier_name','proveedor','supplierText','supplier_nombre']);
                    if ($supName) {
                        $supplierId = DB::table('suppliers')->where('name', $supName)->value('supplier_id')
                                ?: DB::table('suppliers')->whereRaw('LOWER(TRIM(name)) = LOWER(TRIM(?))', [$supName])->value('supplier_id');
                    }
                }
                if (!$supplierId) {
                    $raw = $coalesce($request, ['supplier','proveedor']);
                    if ($raw) {
                        $n = $digits($raw);
                        if ($n && DB::table('suppliers')->where('supplier_id', $n)->exists()) $supplierId = $n;
                    }
                }
            }

            $productId = $coalesce($request, ['product_id','producto_id','product','producto','prod','prod_id']);
            if ($productId && !is_numeric($productId)) {
                $n = $digits($productId);
                if ($n && DB::table('products')->where('product_id', $n)->exists()) $productId = $n;
            }
            if (!$productId) {
                $prodCode = $coalesce($request, ['product_code','codigo_producto','clave_producto','sku']);
                if ($prodCode) {
                    $productId = DB::table('products')->where('product_code', $prodCode)->value('product_id')
                            ?: DB::table('products')->where('sku', $prodCode)->value('product_id');
                }
                if (!$productId) {
                    $prodName = $coalesce($request, ['product_name','producto','productText','producto_nombre','product_label']);
                    if ($prodName) {
                        $productId = DB::table('products')->where('name', $prodName)->value('product_id')
                                ?: DB::table('products')->whereRaw('LOWER(TRIM(name)) = LOWER(TRIM(?))', [$prodName])->value('product_id')
                                ?: DB::table('products')->where('name', 'like', '%'.trim($prodName).'%')->value('product_id');
                    }
                }
            }

            $rem = $coalesce($request, ['remitidos','kg','kilos','kg_total','litros','lts','l']);
            $rem = $rem !== null ? trim((string)$rem) : null;
            $loteValor = $coalesce($request, ['lote','lote_interno','lote_proveedor']);
            $loteTipo  = strtolower(trim((string)$request->input('lote_tipo', 'interno')));
            if (!in_array($loteTipo, ['interno','proveedor'], true)) {
                $soloProveedor = $request->filled('lote_proveedor') && !$request->filled('lote_interno') && !$request->filled('lote');
                $loteTipo = $soloProveedor ? 'proveedor' : 'interno';
            }

            $request->merge([
                'supplier_id'  => $supplierId ? (int)$supplierId : $supplierId,
                'product_id'   => $productId  ? (int)$productId  : $productId,
                'remitidos'    => $rem,
                'lote'         => $loteValor,
                'lote_tipo'    => $loteTipo,
            ]);

            if (!$productId) {
                $debug = [
                    'received_keys' => array_keys($request->all()),
                    'hint' => 'Asegúrate de enviar un input/select con name="product_id" (ID numérico) o al menos product_code/sku/product_name.',
                ];
                return response(
                    "No se pudo resolver product_id.\n\n".json_encode($debug, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),
                    422
                )->header('Content-Type','text/plain; charset=UTF-8');
            }

            $v = Validator::make($request->all(), [
                'folio'            => 'nullable|string|max:16',
                'supplier_id'      => 'required|integer|min:1|exists:suppliers,supplier_id',
                'product_id'       => 'required|integer|min:1|exists:products,product_id',
                'supplier_name'    => 'nullable|string|max:150',
                'product_name'     => 'nullable|string|max:255',
                'fecha_recepcion'  => 'nullable|date',
                'fecha_reporte'    => 'nullable|date',
                'mpptme'           => 'required|in:MP,PT,PP',
                'lote'             => 'nullable|string|max:100',             
                'lote_tipo'        => 'nullable|in:interno,proveedor',      
                'remitidos'        => 'required|string|max:50',
                'fecha_incidencia' => 'nullable|date',
                'incidencia'       => 'nullable|string|max:100',
                'descripcion'      => 'nullable',
                'descripcion.*.texto'      => 'nullable|string|max:1000',
                'descripcion.*.imagenes.*' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
                'imagenes'         => 'nullable|array',
                'imagenes.*'       => 'file|mimes:jpg,jpeg,png,webp|max:5120',
                'firma_nombre'     => 'nullable|string|max:150',
                'comentarios'      => 'nullable|string', 
            ]);

            if ($v->fails()) {
                return response(
                    "Errores de validación:\n".json_encode($v->errors()->toArray(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),
                    422
                )->header('Content-Type', 'text/plain; charset=UTF-8');
            }

            $data         = $v->validated();
            $toNull       = fn($val) => ($val === '' || $val === null) ? null : $val;
            $categoria    = $data['mpptme']; unset($data['mpptme']);
            $supplierName = $data['supplier_name'] ?? DB::table('suppliers')->where('supplier_id', $data['supplier_id'])->value('name');
            $productName  = $data['product_name']  ?? DB::table('products')->where('product_id',  $data['product_id'])->value('name');

            try {
                DB::beginTransaction();

                $folio = $data['folio'] ?? '';
                if ($folio === '') {
                    $yy     = now()->format('y');
                    $prefix = "SRI{$yy}";
                    $lastFolio = DB::table('incidencias')
                        ->where('folio', 'like', $prefix.'%')
                        ->orderByDesc('id')
                        ->lockForUpdate()
                        ->value('folio');

                    $lastSeq = 0;
                    if ($lastFolio && preg_match('/^SRI'.$yy.'(\d{3})$/', $lastFolio, $m)) {
                        $lastSeq = (int)$m[1];
                    }
                    $nextSeq = $lastSeq + 1;
                    if ($nextSeq > 999) {
                        throw new \RuntimeException('Se alcanzó el límite de folios para el año '.$yy);
                    }
                    $folio = $prefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
                }

                $descArr             = []; 
                $relativePublicPaths = []; 
                $descItemsForView    = []; 
                $descInput = $request->input('descripcion');
                $descFiles = $request->file('descripcion');
                $esFormatoNuevo = is_array($descInput) && isset($descInput[0]) && is_array($descInput[0]);

                if ($esFormatoNuevo) {
                    foreach ($descInput as $i => $row) {
                        $texto = trim((string)($row['texto'] ?? ''));
                        
                    $imgsAbsForThisRow = [];
                    
                    $files = $descFiles[$i]['imagenes'] ?? [];
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    
                    foreach ($files as $file) {
                        if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                            $ext    = $file->guessExtension() ?: 'jpg';
                            $name   = uniqid('inc_', true) . '.' . $ext;
                            $stored = $file->storeAs('incidencias/'.$folio, $name, 'public'); 
                            $imgRel = 'storage/'.$stored;
                            
                            $imgAbs = storage_path('app/public/'.$stored); 
                            
                            $relativePublicPaths[] = $imgRel;
                            if (file_exists($imgAbs)) {
                                $b64 = base64_encode(file_get_contents($imgAbs));
                                $ext = strtolower(pathinfo($imgAbs, PATHINFO_EXTENSION));
                                $imgsAbsForThisRow[] = 'data:image/'.$ext.';base64,'.$b64;
                            } else {
                                $imgsAbsForThisRow[] = $imgAbs;
                            }
                        }
                    }
                         if ($texto !== '' || count($imgsAbsForThisRow) > 0) {
                        $descArr[] = $texto;
                        $descItemsForView[] = ['texto' => $texto, 'imgs' => $imgsAbsForThisRow];
                        }
                    }
                } else {
                    if (is_string($descInput)) {
                        $descArr = preg_split("/\r\n|\n|\r/", $descInput);
                    } elseif (is_array($descInput)) {
                        $descArr = $descInput;
                    } else {
                        $descArr = [];
                    }
                    $descArr = array_values(array_filter(array_map(fn($v)=>trim((string)$v), $descArr), fn($v)=>$v!==''));

                    if ($request->hasFile('imagenes')) {
                        foreach ($request->file('imagenes') as $file) {
                            if (!$file->isValid()) continue;
                            $ext    = $file->guessExtension() ?: 'jpg';
                            $name   = uniqid('inc_', true) . '.' . $ext;
                            $stored = $file->storeAs('incidencias/'.$folio, $name, 'public');
                            $rel    = 'storage/'.$stored;
                            $relativePublicPaths[] = $rel;
                        }
                    }

                    $max = max(count($descArr), count($relativePublicPaths));
                    for ($i = 0; $i < $max; $i++) {
                        $texto = $descArr[$i] ?? '';
                        $imgRel= $relativePublicPaths[$i] ?? null;
                        $imgData = null;
                    if ($imgRel) {
                        $imgAbs = base_path('../public_html/' . $imgRel);
                        if (file_exists($imgAbs)) {
                            $b64 = base64_encode(file_get_contents($imgAbs));
                            $ext = strtolower(pathinfo($imgAbs, PATHINFO_EXTENSION));
                            $imgData = 'data:image/'.$ext.';base64,'.$b64;
                        } else {
                            $imgData = $imgAbs;
                        }
                    }
                        $descItemsForView[] = [
                            'texto' => $texto,
                            'img'   => $imgData,
                        ];
                    }
                }

                $insertData = [
                    'folio'            => $folio,
                    'supplier_id'      => (int)$data['supplier_id'],
                    'product_id'       => (int)$data['product_id'],
                    'supplier_name'    => $supplierName,
                    'product_name'     => $productName,
                    'fecha_recepcion'  => $toNull($data['fecha_recepcion'] ?? null),
                    'fecha_reporte'    => $toNull($data['fecha_reporte'] ?? null),
                    'categoria'        => $categoria,
                    'lote'             => $toNull($data['lote'] ?? null),            
                    'lote_tipo'        => $toNull($request->input('lote_tipo','interno')),
                    'remitidos'        => $toNull($data['remitidos'] ?? null),
                    'fecha_incidencia' => $toNull($data['fecha_incidencia'] ?? null),
                    'incidencia'       => $toNull($data['incidencia'] ?? null),
                    'descripcion'      => json_encode($descArr, JSON_UNESCAPED_UNICODE), 
                    'comentarios'      => $toNull($data['comentarios'] ?? null), 
                    'imagenes'         => json_encode($relativePublicPaths, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), 
                    'firma_nombre'     => $toNull($request->input('firma_nombre') ?? null),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];

                $incidenciaId = DB::table('incidencias')->insertGetId($insertData);
                DB::commit();
                
                $viewData = [
                    'pagina_actual'       => 1,
                    'paginas_total'       => 1,
                    'fecha_elaboracion'   => now()->format('d-M-Y'),
                    'fecha_actualizacion' => now()->format('d-M-Y'),
                    'version'             => '00',
                    'folio'            => $insertData['folio'],
                    'proveedor'        => $insertData['supplier_name'],
                    'producto'         => $insertData['product_name'],
                    'fecha_recepcion'  => $insertData['fecha_recepcion'],
                    'fecha_reporte'    => $insertData['fecha_reporte'],
                    'mpptme'           => $insertData['categoria'],
                    'lote'             => $insertData['lote'],        
                    'lote_tipo'        => $insertData['lote_tipo'],   
                    'remitidos'        => $insertData['remitidos'],
                    'fecha_incidencia' => $insertData['fecha_incidencia'],
                    'incidencia'       => $insertData['incidencia'],
                    'descripcion'      => $descItemsForView, 
                    'imagenes'         => array_map(fn($rel) => storage_path('app/public/' . str_replace('storage/', '', $rel)), $relativePublicPaths),
                    
                    'comentarios'      => $insertData['comentarios'],
                    'firma_nombre'     => $insertData['firma_nombre'] ?? ($request->input('firma_nombre') ?? ''),
                ];

                $pdf = \PDF::loadView('formats.quality.incidencias06', $viewData)->setPaper('a4','landscape');
                
                if (app()->bound('debugbar')) {
                    try { app('debugbar')->disable(); } catch (\Throwable $e) {}
                }

                @ini_set('zlib.output_compression', '0');
                if (function_exists('apache_setenv')) { @apache_setenv('no-gzip', '1'); }
                while (ob_get_level() > 0) { @ob_end_clean(); }

                $filename = $insertData['folio'].'.pdf';
                $tmpDir   = storage_path('app/tmp');
                if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0775, true); }
                $filePath = $tmpDir.'/'.$filename;
                file_put_contents($filePath, $pdf->output());

                return response()->download(
                    $filePath,
                    $filename,
                    [
                        'Content-Type'        => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                        'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma'              => 'no-cache',
                    ]
                )->deleteFileAfterSend(true);

            } catch (\Throwable $e) {
                DB::rollBack();
                return response("No se pudo guardar/generar el PDF.\n".$e->getMessage(), 500)
                    ->header('Content-Type', 'text/plain; charset=UTF-8');
            }
        }

        public function pdf7(Request $request)
        {
            $data = $request->validate([
                'consecutivo'               => 'nullable|string|max:50',
                'ciudad'                    => 'nullable|string|max:150',
                'fecha'                     => 'nullable|date',
                'cliente'                   => 'nullable|string|max:255',
                'producto'                  => 'nullable|string|max:255',
                'lote'                      => 'nullable|string|max:100',
                'cantidad'                  => 'nullable|string|max:100',
                'fecha_fabricacion'         => 'nullable|date',
                'fecha_caducidad'           => 'nullable|date',
                'bromato'                   => 'nullable|array',
                'bromato.*.prueba'          => 'nullable|string|max:255',
                'bromato.*.especificacion'  => 'nullable|string|max:255',
                'bromato.*.resultado'       => 'nullable|string|max:255',
                'micro'                     => 'nullable|array',
                'micro.*.prueba'            => 'nullable|string|max:255',
                'micro.*.resultado'         => 'nullable|string|max:255',
                'micro.*.unidades'          => 'nullable|string|max:255',
                'no_sello'                  => 'nullable|string|max:100',
                'no_tarimas'                => 'nullable|integer|min:0',
                'fecha_salida_cedis'        => 'nullable|date',
                'certificado_tarima'        => 'nullable|string|max:255',
                'muestra_o_pf'              => 'nullable|in:Muestra,PT',
                'firmante_nombre'           => 'nullable|string|max:255',
                'firmante_cedula'           => 'nullable|string|max:50',
                'firmante_puesto'           => 'nullable|string|max:255',
            ]);

            \DB::table('quality_certificates')->insert([
                'producto'              => $data['producto'] ?? null,
                'cliente'               => $data['cliente'] ?? null,
                'fecha'                 => $data['fecha'] ?? null,
                'lote'                  => $data['lote'] ?? null,
                'cantidad'              => $data['cantidad'] ?? null,
                'folio'                 => $data['consecutivo'] ?? null,
                'no_tarimas'            => $data['no_tarimas'] ?? null,
                'fecha_salida_cedis'    => $data['fecha_salida_cedis'] ?? null,
                'certificado_tarima'    => $data['certificado_tarima'] ?? null,
                'muestra_o_pf'          => $data['muestra_o_pf'] ?? null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            $pdfData = \Illuminate\Support\Arr::except($data, [
                'no_tarimas',
                'fecha_salida_cedis',
                'certificado_tarima',
                'muestra_o_pf',
                'firmante_nombre',
                'firmante_cedula',
                'firmante_puesto',
            ]);

            $viewData = array_merge($pdfData, [
                'firmante_nombre' => $data['firmante_nombre'] ?? '',
                'firmante_cedula' => $data['firmante_cedula'] ?? '',
                'firmante_puesto' => $data['firmante_puesto'] ?? '',
                'pagina_actual'       => 1,
                'paginas_total'       => 1,
                'fecha_elaboracion'   => now()->format('d-M-Y'),
                'fecha_actualizacion' => now()->format('d-M-Y'),
                'version'             => '00',
            ]);

            $pdf = \PDF::loadView('formats.quality.07', $viewData)->setPaper('a4', 'portrait');

            return $pdf->download('certificado_calidad.pdf');
        }

        public function pdf8(Request $req)
        {
            $rows = [];
            $batchs = $req->input('batch') ?? [];
            $observations = $req->input('observation') ?? [];
            $options = $req->input('option') ?? [];
            $options = array_values($options);
            foreach($batchs as $i => $items){
                foreach($items as $j => $batch){
                    $rows[] = [
                        'batch' => $batch,
                        'observation' => $observations[$i][$j] ?? null,
                        'options' => $options[$i] ?? [],
                    ];
                }
            }
            if ($req->has('fecha_inspeccion')) {
                $raw = trim((string)$req->input('fecha_inspeccion'));
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
                    try {
                        $req->merge([
                            'fecha_inspeccion' => \Carbon\Carbon::parse($raw)->format('d/m/Y')
                        ]);
                    } catch (\Throwable $e) {  }
                }
            }

            $incSing = (array) $req->input('incident',  []);    
            $incPlur = (array) $req->input('incidents', []);     
            $incFlat = [
                'has'     => $req->input('inc_has_8',     $req->input('inc_has')),
                'folio'   => $req->input('inc_folio_8',   $req->input('inc_folio')),
                'desc'    => $req->input('inc_desc_8',    $req->input('inc_desc')),
                'actions' => $req->input('inc_actions_8', $req->input('inc_actions')),
            ];
            $mergedInc = [
                'has'         => $incSing['has']         ?? $incPlur['has']         ?? $incFlat['has']     ?? '0',
                'folio'       => $incSing['folio']       ?? $incPlur['folio']       ?? $incFlat['folio']   ?? null,
                'description' => $incSing['description'] ?? $incPlur['description'] ?? $incFlat['desc']    ?? null,
                'actions'     => $incSing['actions']     ?? $incPlur['actions']     ?? $incFlat['actions'] ?? null,
            ];
            $to01 = function ($v) {
                $s = mb_strtolower((string)$v);
                return in_array($s, ['1','si','sí','true'], true) ? '1' : '0';
            };
            $req->merge([
                'inc_has'     => $to01($mergedInc['has']),
                'inc_folio'   => $mergedInc['folio'],
                'inc_desc'    => $mergedInc['description'],
                'inc_actions' => $mergedInc['actions'],
            ]);

            $data = $req->validate([
                'supplier_id'                => ['required','integer','exists:suppliers,supplier_id'],
                'supplier_code'              => ['nullable','string','max:50'],
                'fecha_inspeccion'           => ['required','date_format:d/m/Y'],
                'items'                      => ['required','array','min:1'],
                'items.*.product_id'         => ['required','integer','exists:products,product_id'],
                'items.*.lote'               => ['nullable','string','max:100'],
                'items.*.presentacion'       => ['nullable','string','max:100'],
                'items.*.cantidad'           => ['nullable','numeric','min:0'],
                'items.*.empaque'            => ['nullable','string','max:100'],
                'tarima'                     => ['nullable','in:si,no'],
                'lib_envase_sellado'         => ['nullable','in:aplica,no_aplica'],
                'lib_envase_sellado_obs'     => ['nullable','string','max:500'],
                'lib_embalaje_limpio'        => ['nullable','in:aplica,no_aplica'],
                'lib_embalaje_limpio_obs'    => ['nullable','string','max:500'],
                'lib_identificacion'         => ['nullable','in:aplica,no_aplica'],
                'lib_identificacion_obs'     => ['nullable','string','max:500'],
                'lib_otro'                   => ['nullable','in:aplica,no_aplica'],
                'lib_otro_obs'               => ['nullable','string','max:500'],
                'tr_sello_seguridad'         => ['nullable','in:aplica,no_aplica'],
                'tr_sello_seguridad_obs'     => ['nullable','string','max:500'],
                'tr_fumigacion'              => ['nullable','in:aplica,no_aplica'],
                'tr_fumigacion_obs'          => ['nullable','string','max:500'],
                'tr_limpieza'                => ['nullable','in:aplica,no_aplica'],
                'tr_limpieza_obs'            => ['nullable','string','max:500'],
                'tr_aromas'                  => ['nullable','in:aplica,no_aplica'],
                'tr_aromas_obs'              => ['nullable','string','max:500'],
                'tr_puertas'                 => ['nullable','in:aplica,no_aplica'],
                'tr_puertas_obs'             => ['nullable','string','max:500'],
                'tr_piso'                    => ['nullable','in:aplica,no_aplica'],
                'tr_piso_obs'                => ['nullable','string','max:500'],
                'tr_techo'                   => ['nullable','in:aplica,no_aplica'],
                'tr_techo_obs'               => ['nullable','string','max:500'],
                'tr_paredes'                 => ['nullable','in:aplica,no_aplica'],
                'tr_paredes_obs'             => ['nullable','string','max:500'],
                'tr_otro'                    => ['nullable','in:aplica,no_aplica'],
                'tr_otro_obs'                => ['nullable','string','max:500'],
                'inc_has'                    => ['nullable','in:0,1'],
                'inc_folio'                  => ['nullable','string','max:50'],
                'inc_desc'                   => ['nullable','string'],
                'inc_actions'                => ['nullable','string'],
                'inspector_nombre'           => ['nullable','string','max:255'],
            ]);

            $fechaObj = \Carbon\Carbon::createFromFormat('d/m/Y', $data['fecha_inspeccion']);
            $fechaDMY = $fechaObj->format('d/m/Y');
            $supplier = Supplier::where('supplier_id', $data['supplier_id'])->firstOrFail();
            $supplierCode = $data['supplier_code'] ?: ($supplier->supplier_code ?? '');

            $items = collect($data['items'])->values()->map(function ($it, $i) {
                $prod = Product::where('product_id', $it['product_id'])->first();
                return [
                    'n'            => $i + 1,
                    'product_name' => $prod?->name ?? '',
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
                'envase_sellado'  => ['val' => $data['lib_envase_sellado']  ?? '', 'obs' => $data['lib_envase_sellado_obs']  ?? ''],
                'embalaje_limpio' => ['val' => $data['lib_embalaje_limpio'] ?? '', 'obs' => $data['lib_embalaje_limpio_obs'] ?? ''],
                'identificacion'  => ['val' => $data['lib_identificacion']  ?? '', 'obs' => $data['lib_identificacion_obs']  ?? ''],
                'otro'            => ['val' => $data['lib_otro']            ?? '', 'obs' => $data['lib_otro_obs']            ?? ''],
            ];
            $trRows = [
                'sello_seguridad' => ['val' => $data['tr_sello_seguridad'] ?? '', 'obs' => $data['tr_sello_seguridad_obs'] ?? ''],
                'fumigacion'      => ['val' => $data['tr_fumigacion']      ?? '', 'obs' => $data['tr_fumigacion_obs']      ?? ''],
                'limpieza'        => ['val' => $data['tr_limpieza']        ?? '', 'obs' => $data['tr_limpieza_obs']        ?? ''],
                'aromas'          => ['val' => $data['tr_aromas']          ?? '', 'obs' => $data['tr_aromas_obs']          ?? ''],
                'puertas'         => ['val' => $data['tr_puertas']         ?? '', 'obs' => $data['tr_puertas_obs']         ?? ''],
                'piso'            => ['val' => $data['tr_piso']            ?? '', 'obs' => $data['tr_piso_obs']            ?? ''],
                'techo'           => ['val' => $data['tr_techo']           ?? '', 'obs' => $data['tr_techo_obs']           ?? ''],
                'paredes'         => ['val' => $data['tr_paredes']         ?? '', 'obs' => $data['tr_paredes_obs']         ?? ''],
                'otro'            => ['val' => $data['tr_otro']            ?? '', 'obs' => $data['tr_otro_obs']            ?? ''],
            ];

            $incHas   = ($data['inc_has'] ?? '0') === '1';
            $rawFolio = trim((string)($data['inc_folio'] ?? ''));
            $descRaw  = trim((string)($data['inc_desc']    ?? ''));
            $actsRaw  = trim((string)($data['inc_actions'] ?? ''));
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

            $viewData = [
                'generated_at'        => now()->format('d-M-Y H:i'),
                'fecha_actualizacion' => '--',
                'version'             => '00',
                'codigo_formato'      => 'SSS-FOR-CAL-08',
                'proveedor'           => $supplier->name ?? '',
                'codigo_proveedor'    => $supplierCode,
                'fecha_inspeccion'    => $fechaDMY,
                'items'               => $items,
                'total_cantidad'      => $totalCantidad,
                'tarima'              => $data['tarima'] === 'si' ? 'Sí' : ($data['tarima'] === 'no' ? 'No' : ''),
                'lib_rows'            => $libRows,
                'tr_rows'             => $trRows,
                'inc' => [
                    'has'           => $incHas,
                    'folio'         => $folioMostrar,
                    'desc'          => $incHas ? $descRaw : null,
                    'actions'       => $incHas ? $actsRaw : null,
                    'desc_list'     => ($incHas && !empty($descList))    ? $descList    : null,
                    'actions_list'  => ($incHas && !empty($actionsList)) ? $actionsList : null,
                ],
                'has_incidencias'       => $incHas,
                'inc_folio'             => $folioMostrar,
                'inc_descripcion'       => $descRaw,
                'inc_descripcion_items' => $descList,
                'inc_acciones'          => $actsRaw,
                'inc_acciones_items'    => $actionsList,
                'inspector_nombre'    => $data['inspector_nombre'] ?? '',
                'rows' => $rows
            ];
            try {
                $pdf = Pdf::loadView('formats.quality.inspeccion08', $viewData)
                        ->setPaper('a4', 'portrait');

                $dompdf = $pdf->getDomPDF();
                $dompdf->set_option('isHtml5ParserEnabled', true);
                $dompdf->set_option('isRemoteEnabled', true);
                $dompdf->render();

                DB::table('pdf_clicks')->insert([
                    'reference_id' => (int) $data['supplier_id'],
                    'pdf_type'     => 'B',          
                    'user_id'      => Auth::id(),
                    'generated_at' => now(),
                ]);

                return $pdf->download('RCV-'.now()->format('Ymd-His').'.pdf');

            } catch (\Throwable $e) {
                report($e);
                return back()->withErrors(['pdf' => 'No se pudo generar el PDF. Revisa la vista/datos e imágenes locales.'])->withInput();
            }
        }

        public function pdf9(Request $request)
        {
            $v = Validator::make($request->all(), [
                'fecha'               => 'required|date',
                'customer_id'         => 'required|exists:customers,customer_id',
                'contacto'            => 'nullable|string|max:150',
                'telefono'            => 'nullable|string|max:50',
                'correo'              => 'nullable|email|max:150',
                'direccion'           => 'nullable|string|max:255',
                'fecha_incidencia'    => 'required|date',
                'hora_incidencia'     => 'nullable',
                'producto_servicio'   => 'required|string|max:150',
                'descripcion'         => 'required|string',
                'impacto'             => 'required|string',
                'importancia'         => 'required|in:Baja,Media,Alta',
                'resolucion'          => 'required|string',
                'medidas'             => 'required|string',
                'responsable_accion'  => 'required|string|max:150',
                'fecha_resolucion'    => 'nullable|date',
                'accion_final'        => 'required|string',
                'comentarios_adicionales' => 'nullable|string',
                'evidencias'          => 'nullable|array|max:6',
                'evidencias.*'        => 'nullable|file|image|mimes:jpeg,jpg,png,webp|max:3072',
                'cliente_firma'       => 'nullable|string|max:150',
                'receptor_firma'      => 'nullable|string|max:150',
            ], [
                'evidencias.*.image'  => 'Cada archivo debe ser una imagen válida.',
                'evidencias.*.mimes'  => 'Formatos permitidos: JPG, PNG, WEBP.',
                'evidencias.*.max'    => 'Cada imagen no debe exceder 3 MB.',
                'evidencias.max'      => 'Máximo 6 imágenes.',
            ]);
            if ($v->fails()) {
                return back()->withErrors($v)->withInput();
            }
            $data = $v->validated();
            $customer = Customer::select('customer_id','name')
                ->where('customer_id', $data['customer_id'])
                ->firstOrFail();
            $data['empresa'] = $customer->name;

            foreach (['fecha', 'fecha_incidencia', 'fecha_resolucion'] as $f) {
                if (!empty($data[$f])) {
                    $data[$f] = Carbon::parse($data[$f])
                        ->locale('es')
                        ->translatedFormat('j \\d\\e F \\d\\e Y');
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

            $pdf = Pdf::loadView('formats.quality.retrocli09', $data)
                    ->setPaper('letter', 'portrait');
            $dompdf = $pdf->getDomPDF();
            $dompdf->render();
            $canvas = $dompdf->get_canvas();
            $w      = $canvas->get_width();
            $fm   = $dompdf->getFontMetrics();
            $font = $fm->getFont('DejaVu Sans', 'normal'); 
            $size = 7;
            $text = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';
            $ml = 24 * 0.75; $mr = 24 * 0.75; 
            $innerW = $w - $ml - $mr;
            $colStart = $ml + ($innerW * 0.80);
            $colWidth = $innerW * 0.20;
            $pad = 10 * 0.75;
            $colStart += $pad; 
            $colWidth -= $pad * 2;
            $textWidth = $fm->getTextWidth($text, $font, $size);
            $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 29; 
            $y = 70; 
            $canvas->page_text($x, $y, $text, $font, $size, [0,0,0]);
            $filename = 'RetroalimentacionCliente_'.now()->format('Ymd_His').'.pdf';
            return $pdf->download($filename);
        }

       public function pdf10(Request $request)
{
    \Carbon\Carbon::setLocale('es');

    $v = \Illuminate\Support\Facades\Validator::make($request->all(), [
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

    $data = $v->validated();

    if (array_key_exists('observaciones', $data)) {
        if (is_array($data['observaciones'])) {
            $data['observaciones'] = implode("\n", array_filter(array_map(
                fn($v) => trim((string)$v),
                $data['observaciones']
            )));
        } else {
            $data['observaciones'] = trim((string)$data['observaciones']);
        }
        if ($data['observaciones'] === '') {
            $data['observaciones'] = null;
        }
    } else {
        $data['observaciones'] = null;
    }

    $customer = \Illuminate\Support\Facades\DB::table('customers')
        ->select('customer_id','name','customer_code')
        ->where('customer_id', $data['customer_id'])
        ->first();

    if (!$customer) {
        return back()->withErrors(['customer_id' => 'Cliente no encontrado.'])->withInput();
    }

    $data['cliente']        = $customer->name;
    $data['codigo_cliente'] = $customer->customer_code ?? '';

    if (!empty($data['fecha_inspeccion'])) {
        $data['fecha_inspeccion'] = \Carbon\Carbon::parse($data['fecha_inspeccion'])
            ->locale('es')
            ->translatedFormat('j \\d\\e F \\d\\e Y');
    }

    $items = collect($data['items'] ?? []);
    $productIds = $items->pluck('product_id')->filter()->unique();

    $namesById = \Illuminate\Support\Facades\DB::table('products')
        ->whereIn('product_id', $productIds)
        ->pluck('name', 'product_id');

    $data['items'] = $items->map(function ($row) use ($namesById) {
        $row['product_name'] = $namesById[$row['product_id']] ?? '';
        if (isset($row['cantidad']) && $row['cantidad'] !== '') {
            $row['cantidad'] = is_numeric($row['cantidad']) ? $row['cantidad'] + 0 : $row['cantidad'];
        } else {
            $row['cantidad'] = null;
        }
        return $row;
    })->values()->toArray();

    $evidencias_base64 = [];
    if ($request->hasFile('evidencias')) {
        foreach ($request->file('evidencias') as $file) {
            if ($file->isValid()) {
                $path = $file->getRealPath();
                $type = $file->getClientMimeType();
                $imgData = file_get_contents($path);
                $base64 = 'data:' . $type . ';base64,' . base64_encode($imgData);
                $evidencias_base64[] = $base64;
            }
        }
    }
    $data['evidencias'] = $evidencias_base64;

    try {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('formats.quality.salida10', $data)
                ->setPaper('letter', 'portrait');

        if (app()->bound('debugbar')) {
            try { app('debugbar')->disable(); } catch (\Throwable $e) {}
        }

        @ini_set('zlib.output_compression', '0');
        if (function_exists('apache_setenv')) { @apache_setenv('no-gzip', '1'); }
        while (ob_get_level() > 0) { @ob_end_clean(); }

        $dompdf = $pdf->getDomPDF();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        \Illuminate\Support\Facades\DB::table('pdf_clicks')->insert([
            'reference_id' => (int) $data['customer_id'],
            'pdf_type'     => 'C',
            'user_id'      => \Illuminate\Support\Facades\Auth::id(),
            'generated_at' => now(),
        ]);

        $canvas = $dompdf->get_canvas();
        $ml = 24 * 0.75;
        $mr = 24 * 0.75;
        $mt = 140 * 0.75;
        $headerHeightPt = 120 * 0.75;
        $innerW = $canvas->get_width() - $ml - $mr;
        $colStart = $ml + ($innerW * 0.80);
        $colWidth = $innerW * 0.20;
        $pad = 10 * 0.75;
        $colStart += $pad;
        $colWidth -= $pad * 2;
        $fm   = $dompdf->getFontMetrics();
        $font = $fm->getFont('DejaVu Sans', 'normal');
        $size = 7;
        $text = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';
        $textWidth = $fm->getTextWidth($text, $font, $size);
        $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 25;
        $y = ($mt - $headerHeightPt) + 42;
        $canvas->page_text($x, $y, $text, $font, $size, [0,0,0]);
        $filename = 'InspeccionSalida_' . now()->format('Ymd_His') . '.pdf';
        $tmpDir   = storage_path('app/tmp');
        if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0775, true); }
        $filePath = $tmpDir . '/' . $filename;
        file_put_contents($filePath, $pdf->output());

        return response()->download(
            $filePath,
            $filename,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'              => 'no-cache',
            ]
        )->deleteFileAfterSend(true);

    } catch (\Throwable $e) {
        report($e);
        return back()->withErrors([
            'pdf' => 'Ocurrió un problema al generar el PDF: '.$e->getMessage()
        ])->withInput();
    }
}

        public function pdf11(Request $request)
        {
            Carbon::setLocale('es');
            $v = Validator::make($request->all(), [
                'fecha'                   => 'required|date',
                'supplier_id'             => 'required|exists:suppliers,supplier_id',
                'contacto'                => 'nullable|string|max:150',
                'telefono'                => 'nullable|string|max:50',
                'correo'                  => 'nullable|email|max:150',
                'direccion'               => 'nullable|string|max:255',
                'motivo'                  => 'required|string|max:255',
                'fecha_incidencia'        => 'nullable|date',
                'hora_incidencia'         => 'nullable',
                'producto_servicio'       => 'nullable|string|max:150',
                'referencia_contrato'     => 'nullable|string|max:150',
                'especificaciones'        => 'nullable|string',
                'impacto_consecuencias'   => 'nullable|string',
                'costo_adicional'         => 'nullable|in:si,no',
                'retraso_produccion'      => 'nullable|in:si,no',
                'importancia'             => 'nullable|in:baja,media,alta',
                'medidas_iniciales'       => 'nullable|string',
                'propuesta_accion'        => 'nullable|string',
                'fecha_limite'            => 'nullable|date',
                'accion_final'            => 'nullable|string',
                'resp_verificacion'       => 'nullable|string|max:150',
                'fecha_verificacion'      => 'nullable|date',
                'resultados_verificacion' => 'nullable|string',
                'especificaciones_imgs'   => 'nullable|array|max:6',
                'especificaciones_imgs.*' => 'file|image|mimes:jpeg,jpg,png,webp|max:3072',
                'firma_responsable_success'     => 'nullable|string|max:150',
                'firma_representante_proveedor' => 'nullable|string|max:150',
            ], [
                'especificaciones_imgs.*.image' => 'Cada archivo debe ser una imagen válida.',
                'especificaciones_imgs.*.mimes' => 'Formatos permitidos: JPG, PNG, WEBP.',
                'especificaciones_imgs.*.max'   => 'Cada imagen no debe exceder 3 MB.',
                'especificaciones_imgs.max'     => 'Máximo 6 imágenes.',
            ]);

            if ($v->fails()) {
                return back()->withErrors($v)->withInput();
            }

            $data = $v->validated();
            $supplier = DB::table('suppliers')
                ->select('supplier_id','name','supplier_code')
                ->where('supplier_id', $data['supplier_id'])
                ->firstOrFail();

            $data['empresa']       = $supplier->name;
            $data['supplier_code'] = $supplier->supplier_code ?? '';
            $data['costo_adicional']    = $data['costo_adicional']    ?? 'no';
            $data['retraso_produccion'] = $data['retraso_produccion'] ?? 'no';
            $data['importancia']        = $data['importancia']        ?? 'media';

            foreach (['fecha','fecha_incidencia','fecha_limite','fecha_verificacion'] as $f) {
                if (!empty($data[$f])) {
                    $data[$f] = Carbon::parse($data[$f])
                        ->locale('es')
                        ->translatedFormat('j \\d\\e F \\d\\e Y');
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

            $pdf = Pdf::loadView('formats.quality.retropro11', $data)
                    ->setPaper('letter', 'portrait');

            $dompdf = $pdf->getDomPDF();
            $dompdf->render();
            $canvas = $dompdf->get_canvas();
            $w    = $canvas->get_width();
            $fm   = $dompdf->getFontMetrics();
            $font = $fm->getFont('DejaVu Sans', 'normal'); 
            $size = 7;
            $text = 'Pág. {PAGE_NUM} de {PAGE_COUNT}';
            $ml = 24 * 0.75; 
            $mr = 24 * 0.75; 
            $innerW = $w - $ml - $mr;
            $colStart = $ml + ($innerW * 0.80); 
            $colWidth = $innerW * 0.20;
            $pad = 10 * 0.75; 
            $colStart += $pad;
            $colWidth -= $pad * 2;
            $textWidth = $fm->getTextWidth($text, $font, $size);
            $x = $colStart + max(0, ($colWidth - $textWidth) / 2) + 29; 
            $y = 70; 
            $canvas->page_text($x, $y, $text, $font, $size, [0,0,0]);
            $filename = 'RetroalimentacionProveedor_'.now()->format('Ymd_His').'.pdf';
            return $pdf->download($filename);
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
                'motivo_otro'      => [
                    'nullable','string','max:200',
                    function($attr,$val,$fail) use ($request){
                        $motivos = (array) $request->input('motivos', []);
                        if (in_array('otro', $motivos) && !filled($val)) {
                            $fail('Debe especificar el motivo en "Otro".');
                        }
                    }
                ],

                'descripcion'      => 'required|string',
                'respuesta_email'  => 'required|in:si,no',
            ]);

            if ($v->fails()) {
                return back()->withErrors($v)->withInput();
            }

            $data = $v->validated();

            if (!empty($data['fecha'])) {
                $data['fecha'] = Carbon::parse($data['fecha'])
                    ->locale('es')
                    ->translatedFormat('j \\d\\e F \\d\\e Y');
            }

            $motivos = (array) ($data['motivos'] ?? []);
            $data['m_calidad_producto']  = in_array('calidad_producto', $motivos);
            $data['m_plazo_entrega']     = in_array('plazo_entrega', $motivos);
            $data['m_soporte_tecnico']   = in_array('soporte_tecnico', $motivos);
            $data['m_atencion_personal'] = in_array('atencion_personal', $motivos);
            $data['m_otro']              = in_array('otro', $motivos);
            $data['motivo_otro']         = $data['motivo_otro'] ?? '';

            $pdf = Pdf::loadView('formats.quality.quejas12', $data)
                    ->setPaper('letter', 'portrait');

            $filename = 'Quejas_Sugerencias_'.now()->format('Ymd_His').'.pdf';
            return $pdf->download($filename);
        }

        private function makeFolio(): string
        {
            $prefix = 'INC-'.now()->format('Ymd');
            $count  = DB::table('incidencias')->whereDate('created_at', now()->toDateString())->count() + 1;
            return sprintf('%s-%03d', $prefix, $count);
        }

        private function normalizeKg($value): float
        {
            $n = str_replace(',', '', (string)$value);
            return round((float)$n, 3);
        }

        private function normalizeDescripcion($desc): array
        {
            if (is_string($desc)) {
                $desc = preg_split("/\r\n|\n|\r/", $desc);
            }
            if (!is_array($desc)) return [];
            $clean = array_map(fn($v) => trim((string)$v), $desc);
            return array_values(array_filter($clean, fn($v) => $v !== ''));
        }
                
        public function getInspection(Request $request)
        {
            $id = $request->input('id');
            $inspection = InspectionW::find($id);

            $observations = Observacion::select('id','name', 'rev', 'ubicacion','evidencia_path')
                ->where('inspection_id',$id)->get()
                ->map(function($obs){
                    return [
                        'id' => $obs->id,
                        'name' => $obs->name,
                        'rev' => $obs->rev,
                        'ubicacion'       => $obs->ubicacion,                                     
                        'evidencia_url' => $obs->evidencia_path ? asset($obs->evidencia_path) : null,
                    ];
                });

            return response()->json(['inspection'=>$inspection,'observations'=>$observations]);
        }

        public function eliminarObs(Request $request)
        {
            $id = $request->id;
            $path =$request->path;
            $observation = Observacion::find($id);
            if (Storage::disk('public')->exists($path)){
                Storage::disk('public')->delete($path);
            }
            if($observation) {
                $observation->delete();
                return response()->json(['success' => true, 'message' => 'observation deleted']);
            } else {
                return response()->json(['success' => false, 'message' => 'observation not deleted'], 404);
            }
        }

        public function destroy($id)
        {
            $inspection = InspectionW::findOrFail($id);
            $inspection->delete();

            return response()->json(['success' => true]);
        }

        public function update(Request $request)
        {
            $request->validate([
                'inspection_id' => ['required','integer','exists:inspections_w,id'],
                'fecha_inspeccion' => ['nullable','date'],
                'inspector' => ['nullable','string','max:255'],
                'hora_turno' => ['nullable','string','max:10'],
                'turno' => ['nullable', 'in:1,2,3,mixto'],
                'area' => ['nullable','array'],
                'area.*' => ['in:nave1,nave2,otro'],
                'area_otro' => ['nullable','string','max:255'],
                'responsable' => ['nullable','string','max:255'],
                'comentarios_q' => ['nullable','string'],
                'obs' => ['nullable','array'],
                'obs.*.id' => ['nullable','integer','exists:observaciones,id'],
                'obs.*.name' => ['nullable','string','max:255'],
                'obs.*.rev' => ['nullable','in:cumple,no_cumple'],
                'obs.*.fecha' => ['nullable','date'],
                'obs.*.evidencia_file' => ['nullable','file','image','mimes:jpeg,jpg,png,webp','max:5120'],
                'obs.*.ubicacion' => ['nullable','string','max:255'],
            ]);

            $inspection = InspectionW::findOrFail($request->inspection_id);
            $inspection->fecha_inspeccion = $request->fecha_inspeccion;
            $inspection->inspector = $request->inspector;
            $inspection->hora_turno = $request->hora_turno;
            $inspection->turno = $request->turno;
            $inspection->area = $request->area ?? [];
            $inspection->area_otro = $request->area_otro;

            if (!empty($request->responsable)) {
                $inspection->responsable = $request->responsable;
            }
            $inspection->comentarios_q = $request->comentarios_q;
            $inspection->save();

            if ($request->obs) {
                foreach ($request->obs as $obsData) {
                    $obs = isset($obsData['id']) 
                        ? Observacion::find($obsData['id'])
                        : new Observacion();

                    $obs->inspection_id = $inspection->id;
                    $obs->name = $obsData['name'] ?? null;
                    $obs->rev = $obsData['rev'] ?? null;

                    if (!empty($obsData['fecha'])) {
                        $obs->fecha = $obsData['fecha'];
                    }

                    $obs->ubicacion = isset($obsData['ubicacion']) && trim($obsData['ubicacion']) !== ''
                    ? trim($obsData['ubicacion'])
                    : null;

                    if(isset($obsData['evidencia_file']) && $obsData['evidencia_file'] instanceof \Illuminate\Http\UploadedFile && $obsData['evidencia_file']->isValid()){
                        $f = $obsData['evidencia_file'];
                        $ext = $f->guessExtension() ?: 'jpg';
                        $filename = time() . '_' . Str::uuid() . '.' . $ext;
                        $targetBase = is_dir(base_path('../public_html')) ? base_path('../public_html') : public_path();
                        $targetDir = $targetBase . '/inspections_w/evidencias';
                        if (!file_exists($targetDir)) {
                            @mkdir($targetDir, 0755, true);
                        }
                        $f->move($targetDir, $filename);
                        $obs->evidencia_path = 'inspections_w/evidencias/' . $filename;
                    } elseif(isset($obsData['existing_evidencia_path'])) {
                        $obs->evidencia_path = $obsData['existing_evidencia_path'];
                    }

                    $obs->save();
                }
            }

            return response()->json(['success' => true]);
        }

        public function getWarehouseInspection(Request $request)
        {
            $inspection = InspectionW::with('observaciones')->findOrFail($request->id);

            return response()->json([
                'id'            => $inspection->id,
                'responsable'   => $inspection->responsable,
                'comentarios'   => $inspection->comentarios,
                'comentarios_q' => $inspection->comentarios_q,
                'observaciones' => $inspection->observaciones->map(function($obs){
                    return [
                        'id'            => $obs->id,
                        'name'          => $obs->name,
                        'ubicacion'     => $obs->ubicacion,         
                        'fecha'         => $obs->fecha,
                        'evidencia_path'=> $obs->evidencia_path,
                        'ev_corr_path'  => $obs->ev_corr_path,
                    ];
                })->values(),
            ]);
        }

        public function updatew(Request $request, $id)
        {
            $request->validate([
                'responsable'        => ['nullable', 'string', 'max:255'],
                'comentarios'        => ['nullable', 'string'],
                'obs'                => ['nullable', 'array'],
                'obs.*.id'           => ['nullable', 'integer', 'exists:observaciones,id'],
                'obs.*.name'         => ['nullable', 'string', 'max:255'],
                'obs.*.fecha'        => ['nullable', 'date'],
                'obs.*.ev_corr_file' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            ]);

            $inspection = InspectionW::findOrFail($id);
            $inspection->responsable = $request->input('responsable');
            $inspection->comentarios = $request->input('comentarios');

            if ($inspection->status != 1) {
                $inspection->status = 1;
            }

            $inspection->save();

            $obsData = $request->input('obs', []);

            foreach ($obsData as $i => $obs) {
                if (!empty($obs['id'])) {
                    $observacion = Observacion::find($obs['id']);
                    if (!$observacion) {
                        $observacion = new Observacion();
                        $observacion->inspection_id = $inspection->id;
                        $observacion->name = $obs['name'] ?? null;
                    }
                } else {
                    $observacion = new Observacion();
                    $observacion->inspection_id = $inspection->id;
                    $observacion->name = $obs['name'] ?? null;
                }

                if (!empty($obs['fecha'])) {
                    $observacion->fecha = $obs['fecha'];
                }

                if ($request->hasFile("obs.$i.ev_corr_file")) {
                    $file = $request->file("obs.$i.ev_corr_file");
                    if ($file && $file->isValid()) {
                        $ext = $file->guessExtension() ?: 'jpg';
                        $filename = time() . '_' . Str::uuid() . '.' . $ext;
                        $targetBase = is_dir(base_path('../public_html')) ? base_path('../public_html') : public_path();
                        $targetDir = $targetBase . '/observaciones';
                        if (!file_exists($targetDir)) {
                            @mkdir($targetDir, 0755, true);
                        }
                        $file->move($targetDir, $filename);
                        $observacion->ev_corr_path = 'observaciones/' . $filename;
                    }
                }

                $observacion->save();
            }

            return response()->json(['message' => 'Changes saved successfully']);
        }

        public function checkPending()
        {
            $user = Auth::user();
            if (!$user->roles()->where('name', 'Warehouse')->exists()) {
                return response()->json(['pending' => []]);
            }

            $pending = InspectionW::where('status', 0)->get();
            return response()->json(['pending' => $pending]);
        }

        public function pdfGenerationsChartData(Request $req)
        {
            $from = $req->date('from');
            $to   = $req->date('to');

            $q = DB::table('pdf_clicks')
                ->whereIn('pdf_type', ['A', 'B', 'C']);

            if ($from) {
                $q->where('generated_at', '>=', $from->startOfDay());
            }

            if ($to) {
                $q->where('generated_at', '<=', $to->endOfDay());
            }

            $rows = $q->select('pdf_type', DB::raw('COUNT(*) as total'))
                ->groupBy('pdf_type')
                ->orderBy('pdf_type')
                ->get();

            $data = [['Tipo', 'Generados']];
            foreach ($rows as $r) {
                $label = match ($r->pdf_type) {
                    'A' => 'SSS-FOR-CAL-04',
                    'B' => 'SSS-FOR-CAL-08',
                    'C' => 'SSS-FOR-CAL-10',
                };

                $data[] = [$label, (int) $r->total];
            }

            return response()->json(['data' => $data]);
        }


        public function getInspectionWView(Request $request)
        {
            try {
                $id = $request->id;

                if (!$id) {
                    return response()->json(['message' => 'ID no recibido'], 400);
                }

                $inspection = InspectionW::with('observaciones')->find($id);

                if (!$inspection) {
                    return response()->json(['message' => 'Inspección no encontrada'], 404);
                }

                return response()->json([
                    'id' => $inspection->id,
                    'responsable' => $inspection->responsable,
                    'inspector' => $inspection->inspector,
                    'comentarios' => $inspection->comentarios_q,

                    'observaciones' => $inspection->observaciones->map(function ($obs) {
                        return [
                            'name' => $obs->name,
                            'ubicacion' => $obs->ubicacion,
                            'fecha' => $obs->fecha,
                            'evidencia_path' => $obs->evidencia_path,
                            'ev_corr_path' => $obs->ev_corr_path,
                        ];
                    })
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Error interno',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        }

