<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inspección de Recepción</title>
    <style>
        @page { margin: 18px 24px; }
        body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
        .w-full { width: 100%; }
        .tbl    { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .b1 td, .b1 th { border: 1px solid #000; }
        .c { text-align: center; }
        .p3 { padding: 3px; }
        .p4 { padding: 4px; }
        .p6 { padding: 6px; }
        .t12 { font-size: 12pt; font-weight: bold; }
        .t9  { font-size: 9pt;  }
        .title { text-align:center; font-weight: bold; margin: 10px 0 6px; }
        td { word-wrap: break-word; }
        .th-green {
            background:#92D050;
            font-weight:bold;
            color:#000;
        }
        .green-cell { background:#92D050; color:#000; font-weight:normal; }
        .subdatos td { border-top:none; border-bottom:none; }
        .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
    </style>
</head>
<body>

{{-- ================= ENCABEZADO ================= --}}
<table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
  <tr>
    <td style="width:22%; text-align:center; border:1px solid #000;">
      <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
    </td>
    <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
      <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
        INSPECCIÓN DE RECEPCIÓN
      </div>

      <table style="width:100%; border-collapse:collapse; font-size:9pt;">
            <tr>
                <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
                    <b>Fecha de elaboración:</b><br>
                      30-Enero-2023
                </td>
                <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
                    <b>Fecha de actualización:</b>--<br>
                </td>
                <td style="width:24%; padding:4px; text-align:center;">
                    <b>Versión:</b>00<br>
                </td>
            </tr>
        </table>
        </td>

        <!-- CÓDIGO Y PÁGINAS -->
        <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
            <b>Código:</b><br>
                {{ $codigo_formato ?? 'SSS-FOR-CAL-08' }}
        </div>
        <div style="padding:10px; text-align:center;">
            Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
        </td>
    </tr>
</table>

    {{-- ================= DATOS GENERALES ================= --}}
    <div class="title" style="margin-top:15px;">Datos Generales</div>
        <table class="tbl b1 t9">
            <tr>
                <td class="p6" colspan="2" style="font-size:12px;">
                    <b>Proveedor:</b> {{ $proveedor ?? 'STD Soluciones Tecnológicas Diversas' }}
                </td>
            </tr>
            <tr>
                <td class="p6">
                    <b>Código de Proveedor:</b> {{ $codigo_proveedor ?? 'SPP133' }}
                </td>
                <td class="p6">
                    <b>Fecha de entrada:</b> {{ $fecha_inspeccion ?? '12/05/2025' }}
                </td>
            </tr>
        </table>

    {{-- ================= DATOS DE CARGA ================= --}}
    <p class="c" style="font-weight:bold; margin:15px 0 6px;">Datos de la Carga</p>
        <table class="tbl b1">
            <thead>
                <tr class="c th-green">
                    <th style="width:5%; padding:6px; font-size:12px;">N°</th>
                    <th style="width:40%; padding:6px; font-size:12px;">Producto</th>
                    <th style="width:16.7%; padding:6px; font-size:12px;">N° de lote</th>
                    <th style="width:13.8%; padding:6px; font-size:12px;">Presentación</th>
                    <th style="width:10%; padding:6px; font-size:12px;">Cantidad</th>
                    <th style="width:14.5%; padding:6px; font-size:12px;">Empaque</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($items ?? []) as $it)
                    <tr class="c">
                        <td style="font-size:12px;">{{ $it['n'] ?? '' }}</td>
                        <td style="font-size:12px; text-align:center;">{{ $it['product_name'] ?? '' }}</td>
                        <td style="font-size:12px;">{{ $it['lote'] ?? '' }}</td>
                        <td style="font-size:12px;">{{ $it['presentacion'] ?? '' }}</td>
                        <td style="font-size:12px; text-align:right;">
                            @php
                            $q = $it['cantidad'] ?? null;
                            echo is_numeric($q) ? number_format((float)$q, 2) : '';
                            @endphp
                        </td>
                        <td style="font-size:12px;">{{ $it['empaque'] ?? '' }}</td>
                    </tr>
                @empty
                    <tr class="c">
                        <td colspan="6" style="font-size:12px; padding:6px;">Sin registros</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    {{-- ================= LIBERACIÓN DE CARGA ================= --}}
    <p class="c" style="font-weight:bold; margin:15px 0 6px;">Liberación de carga</p>
        <table class="tbl b1" style="width:70%; margin:15px auto;">
            <thead>
                <tr class="c">
                    <th style="width:70%; font-size:12px; background:#92D050; color:#000; font-weight:bold;">
                        El producto viene en tarima
                    </th>
                    <th style="width:15%; font-size:12px;">
                        Sí <span style="font-family: DejaVu Sans, sans-serif;">{{ ($tarima ?? '') === 'Sí' ? '☑' : '☐' }}</span>
                    </th>
                    <th style="width:15%; font-size:12px;">
                        No <span style="font-family: DejaVu Sans, sans-serif;">{{ ($tarima ?? '') === 'No' ? '☑' : '☐' }}</span>
                    </th>
                </tr>
            </thead>
        </table>

    <table class="tbl b1" style="margin-top:15px;  font-size:12px;">
        <thead>
            <tr class="c th-green">
                <th style="width:42%;">Concepto</th>
                <th style="width:12%;">Cumple</th>
                <th style="width:16%;">No Cumple</th>
                <th style="width:30%;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @php $r = $lib_rows['envase_sellado'] ?? null; $v = strtolower($r['val'] ?? ''); $ap = $v==='aplica'; $nap = ($v==='no aplica'||$v==='no_aplica'); @endphp
            <tr>
                <td style="text-align:left;">Envase sellado (sin derrames)</td>
                <td class="c">{{ $ap ? 'X' : '' }}</td>
                <td class="c">{{ $nap ? 'X' : '' }}</td>
                <td>{{ $r['obs'] ?? '' }}</td>
            </tr>
            @php $r = $lib_rows['embalaje_limpio'] ?? null; $v = strtolower($r['val'] ?? ''); $ap = $v==='aplica'; $nap = ($v==='no aplica'||$v==='no_aplica'); @endphp
            <tr>
                <td style="text-align:left;">Embalaje (emplaye limpio, sin rasgaduras y/o roto)</td>
                <td class="c">{{ $ap ? 'X' : '' }}</td>
                <td class="c">{{ $nap ? 'X' : '' }}</td>
                <td>{{ $r['obs'] ?? '' }}</td>
            </tr>
            @php $r = $lib_rows['identificacion'] ?? null; $v = strtolower($r['val'] ?? ''); $ap = $v==='aplica'; $nap = ($v==='no aplica'||$v==='no_aplica'); @endphp
            <tr>
                <td style="text-align:left;">Identificación del producto (nombre, lote, cantidad)</td>
                <td class="c">{{ $ap ? 'X' : '' }}</td>
                <td class="c">{{ $nap ? 'X' : '' }}</td>
                <td>{{ $r['obs'] ?? '' }}</td>
            </tr>
            @php $r = $lib_rows['otro'] ?? null; $v = strtolower($r['val'] ?? ''); $ap = $v==='aplica'; $nap = ($v==='no aplica'||$v==='no_aplica'); @endphp
            <tr>
                <td style="text-align:left;">Otro (especifique)</td>
                <td class="c">{{ $ap ? 'X' : '' }}</td>
                <td class="c">{{ $nap ? 'X' : '' }}</td>
                <td>{{ $r['obs'] ?? '' }}</td>
            </tr>
        </tbody>
    </table>

{{-- ================= VERIFICACIÓN DEL TRANSPORTE ================= --}}
    <p class="c" style="font-weight:bold; margin:15px 0 6px;">Verificación del transporte</p>
        <table class="tbl b1" style="margin-top:0; font-size:12px;">
            <thead>
                <tr class="c th-green">
                    <th style="width:42%;">Concepto</th>
                    <th style="width:12%;">Aplica</th>
                    <th style="width:16%;">No Aplica</th>
                    <th style="width:30%;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                $mk = function($key) use($tr_rows){
                    $r = $tr_rows[$key] ?? null;
                    $v = strtolower($r['val'] ?? '');
                    return [
                    'ap'  => $v==='aplica',
                    'nap' => ($v==='no aplica'||$v==='no_aplica'),
                    'obs' => $r['obs'] ?? '',
                    ];
                };
                @endphp
                @php $st = $mk('sello_seguridad'); @endphp
                <tr>
                    <td style="text-align:left;">Sello de seguridad (sin violar, colocado correctamente)</td>
                    <td class="c">{{ $st['ap'] ? 'X' : '' }}</td>
                    <td class="c">{{ $st['nap'] ? 'X' : '' }}</td>
                    <td>{{ $st['obs'] }}</td>
                </tr>
                @php $st = $mk('fumigacion'); @endphp
                <tr>
                    <td style="text-align:left;">Certificado de fumigación vigente</td>
                    <td class="c">{{ $st['ap'] ? 'X' : '' }}</td>
                    <td class="c">{{ $st['nap'] ? 'X' : '' }}</td>
                    <td>{{ $st['obs'] }}</td>
                </tr>
                @php $st = $mk('limpieza'); @endphp
                <tr>
                    <td style="text-align:left;">Limpieza (libre de basura, material o equipo extraño, sin vómito, sin heces fecales)</td>
                    <td class="c">{{ $st['ap'] ? 'X' : '' }}</td>
                    <td class="c">{{ $st['nap'] ? 'X' : '' }}</td>
                    <td>{{ $st['obs'] }}</td>
                </tr>
                @php $st = $mk('aromas'); @endphp
                <tr>
                    <td style="text-align:left;">Libre de aromas atípicos</td>
                    <td class="c">{{ $st['ap'] ? 'X' : '' }}</td>
                    <td class="c">{{ $st['nap'] ? 'X' : '' }}</td>
                    <td>{{ $st['obs'] }}</td>
                </tr>
                @php $st = $mk('puertas'); @endphp
                <tr>
                    <td style="text-align:left;">Puertas herméticas</td>
                    <td class="c">{{ $st['ap'] ? 'X' : '' }}</td>
                    <td class="c">{{ $st['nap'] ? 'X' : '' }}</td>
                    <td>{{ $st['obs'] }}</td>
                </tr>
                @php $st = $mk('piso'); @endphp
                <tr>
                    <td style="text-align:left;">Piso (sin orificios, sin desprendimiento de maderas, sellados)</td>
                    <td class="c">{{ $st['ap'] ? 'X' : '' }}</td>
                    <td class="c">{{ $st['nap'] ? 'X' : '' }}</td>
                    <td>{{ $st['obs'] }}</td>
                </tr>
                @php $st = $mk('techo'); @endphp
                <tr>
                    <td style="text-align:left;">Techo (sellado, sin desprendimiento de pintura)</td>
                    <td class="c">{{ $st['ap'] ? 'X' : '' }}</td>
                    <td class="c">{{ $st['nap'] ? 'X' : '' }}</td>
                    <td>{{ $st['obs'] }}</td>
                </tr>
                @php $st = $mk('paredes'); @endphp
                <tr>
                    <td style="text-align:left;">Paredes sin astillas o filos cortantes</td>
                    <td class="c">{{ $st['ap'] ? 'X' : '' }}</td>
                    <td class="c">{{ $st['nap'] ? 'X' : '' }}</td>
                    <td>{{ $st['obs'] }}</td>
                </tr>
                @php $st = $mk('otro'); @endphp
                <tr>
                    <td style="text-align:left;">Otro (especifique):</td>
                    <td class="c">{{ $st['ap'] ? 'X' : '' }}</td>
                    <td class="c">{{ $st['nap'] ? 'X' : '' }}</td>
                    <td>{{ $st['obs'] }}</td>
                </tr>
            </tbody>
        </table>

{{-- ================= INCIDENCIAS ================= --}}

    <table class="tbl b1" style="width:100%; border-collapse:collapse; margin-top:15px; font-size:12px;">
        <thead>
            <tr class="c">
                <th class="th-green" style="width:48%; padding:2px 4px; font-size:12px; text-align:center;">
                    Incidencias
                </th>
                <th style="width:12%; padding:2px 4px; font-size:12px;">
                    Sí <span style="font-family: DejaVu Sans, sans-serif;">{{ ($inc['has'] ?? false) ? '☑' : '☐' }}</span>
                </th>
                <th style="width:12%; padding:2px 4px; font-size:12px;">
                    No <span style="font-family: DejaVu Sans, sans-serif;">{{ ($inc['has'] ?? false) ? '☐' : '☑' }}</span>
                </th>
                <th class="th-green" style="width:28%; padding:2px 4px; font-size:12px; text-align:left;">
                    Folio: {{ $inc['folio'] ?? '' }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" style="padding:6px; font-size:12px; line-height:1.4; text-align:left;">
                    <b>Descripción:</b>
                    <ul style="margin:4px 0 4px 18px; padding:0; font-size:12px;">
                        @php $descLines = preg_split('/\r\n|\r|\n/', $inc['desc'] ?? ''); @endphp
                        @foreach($descLines as $line)
                            @if(strlen(trim($line))) <li>{{ trim($line) }}</li> @endif
                        @endforeach
                    </ul>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding:6px; font-size:12px; line-height:1.4; text-align:left;">
                    <b>Acciones que se implementaron:</b>
                    <ul style="margin:4px 0 4px 18px; padding:0; font-size:12px;">
                        @php $actLines = preg_split('/\r\n|\r|\n/', $inc['actions'] ?? ''); @endphp
                        @foreach($actLines as $line)
                            @if(strlen(trim($line))) <li>{{ trim($line) }}</li> @endif
                        @endforeach
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ================= FIRMAS ================= --}}
    <table class="tbl" style="border:none; width:100%; margin-top:20px;">
        <tr>
            <td style="border:none; text-align:left; width:60%; padding-top:40px;">
                <span style="font-size:12px;">Nombre de quien realizó la inspección: {{ $inspector_nombre ?? '__________________________________________' }}</span>
            </td>
        </tr>
    </table>
    @if ($rows != [])
    <table class="tbl b1" style="font-size:9pt; width:100%; border-collapse:collapse; margin-top:12px;">
        <thead>
            <tr class="c">
                <th style="width:12%;  padding:4px;">No.Tarima</th>
                <th style="width:8%;  padding:4px;">Lote</th>
                <th style="width:15%; padding:4px;">Identificación de la tarima<br>(nombre, lote, cantidad)</th>
                <th style="width:12%; padding:4px;">Tarima en buen estado</th>
                <th style="width:12%; padding:4px;">Emplaye sin rasgaduras o roto</th>
                <th style="width:10%; padding:4px;">No rota / No astillada</th>
                <th style="width:8%;  padding:4px;">Libre de fauna</th>
                <th style="width:9%;  padding:4px;">Libre de materia extraña</th>
                <th style="width:10%; padding:4px;">Saco sellado<br>(sin derrames)</th>
                <th style="width:14%; padding:4px;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $index => $item)
            <tr>
            <td class="p3">{{ $index + 1 }}</td>
            <td class="p3 c">{{ $item['batch'] ?? 'No Batch' }}</td>
            <td class="p3 c cb">{{ isset($item['options']['ident_ok']) ? '✓' : '✗' }}</td>
            <td class="p3 c cb">{{ isset($item['options']['buen_estado']) ? '✓' : '✗' }}</td>
            <td class="p3 c cb">{{ isset($item['options']['emplaye_ok']) ? '✓' : '✗' }}</td>
            <td class="p3 c cb">{{ isset($item['options']['no_rota']) ? '✓' : '✗' }}</td>
            <td class="p3 c cb">{{ isset($item['options']['fauna_libre']) ? '✓' : '✗' }}</td>
            <td class="p3 c cb">{{ isset($item['options']['materia_libre']) ? '✓' : '✗' }}</td>
            <td class="p3 c cb">{{ isset($item['options']['saco_sellado']) ? '✓' : '✗' }}</td>
            <td class="p3">{{ $item['observation'] ?? 'No Observation' }}</td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>
    @endif
</body>
</html>
