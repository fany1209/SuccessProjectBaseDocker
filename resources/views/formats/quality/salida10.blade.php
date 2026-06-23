<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inspección de Salida de Producto — SSS-FOR-CAL-10</title>
  <style>
    @page { margin: 140px 24px 24px 24px; } 
    body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
    header {
      position: fixed;
      top: -120px;            
      left: 0; right: 0;
      height: 120px;          
      z-index: 10;
      background: #fff;
    }
    main { margin-top: 0; }
    .tbl    { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .b1 td, .b1 th { border: 1px solid #000; }
    .c { text-align: center; }
    .l { text-align: left; }
    .r { text-align: right; }
    .p4 { padding: 4px; }
    .p6 { padding: 3px; }
    .t9 { font-size: 9pt; }
    .title { text-align:center; font-weight:bold; margin: 12px 0 6px; font-size: 12pt; }
    td { word-wrap: break-word; }
    .th-green { background:#92D050; color:#000; font-weight:bold; }
    .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; } 
    .ph-img { height: 52px; border:1px solid #000; margin: 2px 0; }
    .tick {
      font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
      font-size: 11pt;
      line-height: 1;
    }
    .no-break { page-break-inside: avoid; page-break-after: avoid; }
    .evidencia-img { max-width: 100%; max-height: 180px; object-fit: contain; }
  </style>
</head>
<body>

  <header>
    <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
      <tr>
        <td style="width:22%; text-align:center; border:1px solid #000;">
          <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
        </td>
        <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
          <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
            INSPECCIÓN DE SALIDA DE PRODUCTO
          </div>

          <table style="width:100%; border-collapse:collapse; font-size:9pt;">
            <tr>
              <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
                <b>Fecha de elaboración:</b><br>
                30-Enero-2023
              </td>
              <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
                <b>Fecha de actualización:</b> --<br>
              </td>
              <td style="width:24%; padding:4px; text-align:center;">
                <b>Versión:</b> 00<br>
              </td>
            </tr>
          </table>
        </td>

        <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
          <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
            <b>Código:</b><br>
            SSS-FOR-CAL-10
          </div>
          <div style="padding:10px; text-align:center;">&nbsp;</div>
        </td>
      </tr>
    </table>
  </header>

  <main>
    {{-- ================= DATOS GENERALES ================= --}}
    <p class="c" style="font-weight:bold; margin:14px 0 6px;">Datos Generales</p>
    <table class="tbl b1" style="font-size:9pt; table-layout:fixed; margin-top:15px;">
      <colgroup>
        <col style="width:10%;">
        <col style="width:20%;">
        <col style="width:30%;">
        <col style="width:40%;">
      </colgroup>
      <tr>
        <td class="p6" colspan="6">
            <b>Cliente:</b>
            {{ $codigo_cliente ?? '' }} - {{ $cliente ?? '' }}
        </td>
        <td class="p6" colspan="4"><b>Fecha de inspección:</b> {{ $fecha_inspeccion ?? '' }}</td>
      </tr>
    </table>

    {{-- ================= DATOS DEL PRODUCTO ================= --}}
    <p class="c" style="font-weight:bold; margin:14px 0 6px;">Datos del producto</p>
    <table class="tbl b1" style="font-size:8pt;">
      <thead>
        <tr class="c th-green">
          <th style="width:6%;  padding:6px;">N°</th>
          <th style="width:36%; padding:6px;">Producto</th>
          <th style="width:16%; padding:6px;">N° de lote</th>
          <th style="width:14%; padding:6px;">Presentación</th>
          <th style="width:12%; padding:6px;">Cantidad</th>
          <th style="width:16%; padding:6px;">Empaque</th>
        </tr>
      </thead>
      <tbody>
        @forelse(($items ?? []) as $idx => $it)
          <tr class="c">
            <td class="p4">{{ $idx + 1 }}</td>
            <td class="p4 l">{{ $it['product_name'] ?? '' }}</td>
            <td class="p4">{{ $it['lote'] ?? '' }}</td>
            <td class="p4">{{ $it['presentacion'] ?? '' }}</td>
            <td class="p4">
              @php
                $q = $it['cantidad'] ?? '';
                echo is_numeric($q) ? rtrim(rtrim(number_format($q, 2, '.', ''), '0'), '.') : $q;
              @endphp
            </td>
            <td class="p4">{{ $it['empaque'] ?? '' }}</td>
          </tr>
        @empty
          <tr class="c">
            <td class="p4" colspan="6">Sin productos capturados.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- ================= LIBERACIÓN DE PRODUCTO ================= --}}
    @php
      $tick = fn($v,$opt) => (($v ?? null) === $opt) ? '✓' : '';
    @endphp

    <p class="c" style="font-weight:bold; margin:14px 0 6px;">Liberación de producto</p>
    <table class="tbl b1" style="font-size:8pt;">
      <tr class="th-green c">
        <td colspan="3" style="padding:5px;"><b>Requisito</b></td>
      </tr>
      <tr class="th-green c">
        <td style="width:70%; padding:5px;"><b>Limpieza</b></td>
        <td style="width:15%; padding:5px;"><b>Cumple</b></td>
        <td style="width:15%; padding:5px;"><b>No Aplica</b></td>
      </tr>
      <tr>
        <td class="p4">Libre de fauna nociva</td>
        <td class="c"><span class="tick">{{ $tick($lib_fauna_nociva ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_fauna_nociva ?? null, 'no_aplica') }}</span></td>
      </tr>
      <tr>
        <td class="p4">Empaque o emplaye limpio</td>
        <td class="c"><span class="tick">{{ $tick($lib_empaque_limpio ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_empaque_limpio ?? null, 'no_aplica') }}</span></td>
      </tr>
      <tr>
        <td class="p4">Empaque,envase y/o emplaye limpio, sin rasgaduras y/o rotos</td>
        <td class="c"><span class="tick">{{ $tick($lib_empaque_sin_rupturas ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_empaque_sin_rupturas ?? null, 'no_aplica') }}</span></td>
      </tr>
      <tr>
        <td class="p4">Libre de materia extraña (basura, piedras, tierra, cenizas, lodo, etc.)</td>
        <td class="c"><span class="tick">{{ $tick($lib_libre_materia_extrana ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_libre_materia_extrana ?? null, 'no_aplica') }}</span></td>
      </tr>
      <tr>
        <td class="p4">Mezcla homogénea / sin aglomeraciones</td>
        <td class="c"><span class="tick">{{ $tick($lib_mezcla_homogenea ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_mezcla_homogenea ?? null, 'no_aplica') }}</span></td>
      </tr>
      <tr>
        <td class="p4">Envase sellado (sin derrames)</td>
        <td class="c"><span class="tick">{{ $tick($lib_envase_sellado ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_envase_sellado ?? null, 'no_aplica') }}</span></td>
      </tr>
      <tr>
        <td class="p4">Correcto llevado a la marca de volumen</td>
        <td class="c"><span class="tick">{{ $tick($lib_marca_volumen ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_marca_volumen ?? null, 'no_aplica') }}</span></td>
      </tr>

      <tr class="th-green c">
        <td colspan="3" style="padding:5px;"><b>Datos especificados en etiqueta</b></td>
      </tr>
      <tr>
        <td class="p4">Lote</td>
        <td class="c"><span class="tick">{{ $tick($lib_etiq_lote ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_etiq_lote ?? null, 'no_aplica') }}</span></td>
      </tr>
      <tr>
        <td class="p4">Nombre del producto</td>
        <td class="c"><span class="tick">{{ $tick($lib_etiq_nombre ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_etiq_nombre ?? null, 'no_aplica') }}</span></td>
      </tr>
      <tr>
        <td class="p4">Fecha de caducidad</td>
        <td class="c"><span class="tick">{{ $tick($lib_etiq_caducidad ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_etiq_caducidad ?? null, 'no_aplica') }}</span></td>
      </tr>
      <tr>
        <td class="p4">Contenido neto</td>
        <td class="c"><span class="tick">{{ $tick($lib_etiq_contenido ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_etiq_contenido ?? null, 'no_aplica') }}</span></td>
      </tr>
      <tr>
        <td class="p4">Recomendaciones</td>
        <td class="c"><span class="tick">{{ $tick($lib_etiq_recomendaciones ?? null, 'cumple') }}</span></td>
        <td class="c"><span class="tick">{{ $tick($lib_etiq_recomendaciones ?? null, 'no_aplica') }}</span></td>
      </tr>
    </table>

    {{-- ================= OBSERVACIONES ================= --}}
    @php
      $__obs = $observaciones ?? null;
      if (is_array($__obs)) {
          $__obs = implode("\n", array_filter(array_map(fn($v)=>trim((string)$v), $__obs)));
      } else {
          $__obs = trim((string)$__obs);
      }
    @endphp

    @if($__obs !== '')
      <table class="tbl b1" style="font-size:10pt; width:100%; margin-top:14px;">
        <thead>
          <tr class="c th-green">
            <th style="padding:5px;">Observaciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="p6" style="min-height:90px; text-align:left; vertical-align:top; white-space:pre-line;">
              {{ $__obs }}
            </td>
          </tr>
        </tbody>
      </table>
    @endif

    {{-- ================= VERIFICACIÓN DEL TRANSPORTE ================= --}}
    @php
      $tickEnt = fn($v,$opt) => (($v ?? null) === $opt) ? '&#10003;' : '';
    @endphp

    <p class="c" style="font-weight:bold; margin:14px 0 6px;">Verificación del transporte</p>
    <table class="tbl b1" style="font-size:8pt;">
      <thead>
        <tr class="c th-green">
          <th style="width:40%; padding:6px;">Concepto</th>
          <th style="width:10%; padding:6px;">Cumple</th>
          <th style="width:13%; padding:6px;">No aplica</th>
          <th style="width:47%; padding:6px;">Observaciones</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="p6 l">Certificado de fumigación</td>
          <td class="p6 c cb">{!! $tickEnt($tr_fumigacion ?? null, 'cumple') !!}</td>
          <td class="p6 c cb">{!! $tickEnt($tr_fumigacion ?? null, 'no_aplica') !!}</td>
          <td class="p6">{{ $tr_fumigacion_obs ?? '' }}</td>
        </tr>
        <tr>
          <td class="p6 l">Limpieza (libre de basura, material o equipo extraño, sin vómito, sin heces fecales)</td>
          <td class="p6 c cb">{!! $tickEnt($tr_limpieza ?? null, 'cumple') !!}</td>
          <td class="p6 c cb">{!! $tickEnt($tr_limpieza ?? null, 'no_aplica') !!}</td>
          <td class="p6">{{ $tr_limpieza_obs ?? '' }}</td>
        </tr>
        <tr>
          <td class="p6 l">Libre de aromas atípicos</td>
          <td class="p6 c cb">{!! $tickEnt($tr_aromas ?? null, 'cumple') !!}</td>
          <td class="p6 c cb">{!! $tickEnt($tr_aromas ?? null, 'no_aplica') !!}</td>
          <td class="p6">{{ $tr_aromas_obs ?? '' }}</td>
        </tr>
        <tr>
          <td class="p6 l">Puertas herméticas</td>
          <td class="p6 c cb">{!! $tickEnt($tr_puertas ?? null, 'cumple') !!}</td>
          <td class="p6 c cb">{!! $tickEnt($tr_puertas ?? null, 'no_aplica') !!}</td>
          <td class="p6">{{ $tr_puertas_obs ?? '' }}</td>
        </tr>
        <tr>
          <td class="p6 l">Piso (sin orificios, sin desprendimiento de maderas, sellados)</td>
          <td class="p6 c cb">{!! $tickEnt($tr_piso ?? null, 'cumple') !!}</td>
          <td class="p6 c cb">{!! $tickEnt($tr_piso ?? null, 'no_aplica') !!}</td>
          <td class="p6">{{ $tr_piso_obs ?? '' }}</td>
        </tr>
        <tr>
          <td class="p6 l">Techo (sellado, sin desprendimiento de pintura)</td>
          <td class="p6 c cb">{!! $tickEnt($tr_techo ?? null, 'cumple') !!}</td>
          <td class="p6 c cb">{!! $tickEnt($tr_techo ?? null, 'no_aplica') !!}</td>
          <td class="p6">{{ $tr_techo_obs ?? '' }}</td>
        </tr>
        <tr>
          <td class="p6 l">Paredes sin astillas o filos cortantes</td>
          <td class="p6 c cb">{!! $tickEnt($tr_paredes ?? null, 'cumple') !!}</td>
          <td class="p6 c cb">{!! $tickEnt($tr_paredes ?? null, 'no_aplica') !!}</td>
          <td class="p6">{{ $tr_paredes_obs ?? '' }}</td>
        </tr>
        <tr>
          <td class="p6 l">Otro (especifique):</td>
          <td class="p6 c cb">{!! $tickEnt($tr_otro ?? null, 'cumple') !!}</td>
          <td class="p6 c cb">{!! $tickEnt($tr_otro ?? null, 'no_aplica') !!}</td>
          <td class="p6">{{ $tr_otro_obs ?? '' }}</td>
        </tr>
      </tbody>
    </table>

    {{-- ================= EVIDENCIA FOTOGRÁFICA ================= --}}
   @if(!empty($evidencias) && count($evidencias) > 0)
      @php
        $anchoCelda = 100 / count($evidencias);
      @endphp
      <div class="no-break">
        <p class="c" style="font-weight:bold; margin:14px 0 6px;">Evidencia Fotográfica</p>
        <table class="tbl b1" style="font-size:8pt; text-align:center;">
          <tr>
            @foreach($evidencias as $img)
              <td style="padding: 10px; width: {{ $anchoCelda }}%; vertical-align: middle;">
                <img src="{{ $img }}" class="evidencia-img" alt="Evidencia">
              </td>
            @endforeach
          </tr>
        </table>
      </div>
    @endif

    {{-- ================= FIRMAS ================= --}}
    <table class="tbl no-break" style="border:none; margin-top:30px;">
      <tr>
        <td style="border:none; width:60%; font-size:8pt; padding-top:36px; text-align:center;">
          <div style="width:75%; margin:0 auto; border-top:1px solid #000; height:0;"></div>
          <div style="margin-top:4px; font-size:9pt;">
            {{ $inspector_nombre ?? '' }}
          </div>
          <div style="font-size:8pt; margin-top:2px;">
            Nombre de quien realizó la inspección
          </div>
        </td>
      </tr>
    </table>
  </main>
</body>
</html>