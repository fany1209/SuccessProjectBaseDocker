<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formulación</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color:#000; }
    .tbl{ width:100%; border-collapse:collapse; table-layout:fixed; }
    .b1 td,.b1 th{ border:1px solid #000; }
    td,th{ vertical-align:middle; word-wrap:break-word; }
    .p2{padding:2px}.p4{padding:4px}.p6{padding:6px}
    .c{text-align:center}.l{text-align:left}.r{text-align:right}
    .t12{font-size:12pt;font-weight:bold}
    .t9{font-size:9pt}
    .th-green{background:#92D050;font-weight:bold; color:#000;}
    .muted{font-size:9pt;color:#111}
    .cb{font-family: DejaVu Sans, Arial, Helvetica, sans-serif;} 
    .vtop{vertical-align:top}
    .nowrap{white-space:nowrap}

    header{ position:fixed; top:-120px; left:0; right:0; height:110px; z-index:10; }
    footer{ position:fixed; bottom:-60px; left:0; right:0; height:60px; font-size:9pt; z-index:10; }
    main  { margin-top:0; }
  </style>
</head>
<body>

{{-- ================================ ENCABEZADO ================================ --}}
<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
    <tr>
      <td style="width:22%; text-align:center; border:1px solid #000;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>
      <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
        <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
          FORMULACIÓN
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
              30-Enero-2023
            </td>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de actualización:</b>--
            </td>
            <td style="width:24%; padding:4px; text-align:center;">
              <b>Versión:</b>--
            </td>
          </tr>
        </table>
      </td>
      <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>SSS-FOR-LID-10
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 2 }}
        </div>
      </td>
    </tr>
  </table>
</header>
<main>

  <table class="tbl b1" style="font-size:10pt; margin-top:8px; width:100%;">
    <tr>
      <td class="p4" style="width:33%;">
        <b>Fecha de Formulación:</b> {{ $fecha_formulacion ?? '' }}
      </td>
      <td class="p4" style="width:33%;">
        <b>SKU:</b> {{ $sku ?? '' }}
      </td>
      <td class="p4" style="width:34%;">
        <b>Lote:</b> {{ $lote ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="3">
        <b>Nombre del producto:</b> {{ $nombre_producto ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" style="width:20%;"><b>CAMPO DE APLICACIÓN</b></td>
      <td class="p4" colspan="2">
        @php
          $ap = (array)($aplicacion ?? []);
          $m  = fn($k) => in_array($k, $ap) ? '☑' : '☐';
        @endphp

        Agrícola <span class="cb">{{ $m('agricola') }}</span>&nbsp;
        Pecuario <span class="cb">{{ $m('pecuario') }}</span>&nbsp;
        Petfood <span class="cb">{{ $m('petfood') }}</span>&nbsp;
        Otro <span class="cb">{{ $m('otro') }}</span>

        <div style="margin-top:2px;">
          <b>Especifique:</b>
          <span style="display:inline-block; min-width:160px; border-bottom:1px solid #000;">
            {{ $ap_otro ?? '' }}
          </span>
        </div>
      </td>
    </tr>

    <tr>
      <td class="p4"><b>Uso Específico:</b></td>
      <td class="p4" colspan="2">
        {{ $uso_especifico ?? '' }}

        <div style="margin-top:2px;">
          <b>Otro (especifique):</b>
          <span style="display:inline-block; min-width:160px; border-bottom:1px solid #000;">
            {{ $uso_otro ?? '' }}
          </span>
        </div>
      </td>
    </tr>
  </table>

  {{-- ============== FÓRMULA PARA PRODUCCIÓN ============== --}}
  @php
    $total_kg = (float)($total_produccion_kg ?? 1000);
    $items = (array)($materias_primas ?? []);
    foreach ($items as $i => $it) {
        $por = isset($it['porcentaje']) ? (float)$it['porcentaje'] : null;
        if (!isset($it['cantidad_kg']) && $por !== null) {
            $items[$i]['cantidad_kg'] = round(($por / 100.0) * $total_kg, 3);
        }
    }
    $sum_por = array_sum(array_map(fn($x)=> (float)($x['porcentaje'] ?? 0), $items));
    $sum_kg  = array_sum(array_map(fn($x)=> (float)($x['cantidad_kg'] ?? 0), $items));
  @endphp

  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr>
      <td class="p4" style="width:40%;"><b>Fórmula para Producción de:</b>{{ $nombre_producto ?? '' }}</td>
      <td class="p4" colspan="3"><b>{{ number_format($total_kg,0,'.',',') }} kg</b></td>
    </tr>
    <tr class="th-green c">
      <td class="p4" style="width:40%;">Materia prima</td>
      <td class="p4" style="width:10%;">%</td>
      <td class="p4" style="width:15%;">Cantidad</td>
      <td class="p4" style="width:35%;">Observaciones</td>
    </tr>
    @forelse($items as $r)
      <tr>
        <td class="p4">{{ $r['nombre'] ?? '' }}</td>
        <td class="p4 c">{{ isset($r['porcentaje']) ? rtrim(rtrim(number_format((float)$r['porcentaje'],3,'.',''), '0'),'.') : '' }}</td>
        <td class="p4 c">
          {{ isset($r['cantidad_kg']) ? rtrim(rtrim(number_format((float)$r['cantidad_kg'],3,'.',''), '0'),'.') : '' }} kg
        </td>
        <td class="p4">{{ $r['obs'] ?? '' }}</td>
      </tr>
    @empty
      <tr><td class="p4 c" colspan="4">Sin ingredientes.</td></tr>
    @endforelse
    <tr style="font-weight:bold;">
      <td class="p4 c">TOTAL</td>
      <td class="p4 c">{{ rtrim(rtrim(number_format($sum_por,3,'.',''), '0'),'.') }}%</td>
      <td class="p4 c">{{ rtrim(rtrim(number_format($sum_kg,3,'.',''), '0'),'.') }} kg</td>
      <td class="p4"></td>
    </tr>
  </table>

  {{-- ============== VIDA DE ANAQUEL / OBSERVACIONES / RECOMENDACIONES / ANEXOS ============== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px; width:100%;">
    <tr>
      <td class="p4">
        <b>Vida de anaquel:</b> {{ $vida_anaquel ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" style="vertical-align:top; height:52px;">
        <b>Observaciones del producto terminado:</b><br>
        {!! nl2br(e($obs_producto ?? '')) !!}
      </td>
    </tr>

    <tr>
      <td class="p4" style="text-align:justify;">
        <b>Recomendaciones Generales:</b><br>
        {!! nl2br(e($recomendaciones ?? '')) !!}
      </td>
    </tr>

    <tr>
      <td class="p4">
        <b>Recomendación en la etiqueta:</b><br>
        {!! nl2br(e($recom_etiqueta ?? '')) !!}
      </td>
    </tr>

    <tr>
      <td class="p4" style="vertical-align:top; height:60px;">
        <b>Anexos:</b><br>
        {{ $anexos ?? '' }}
      </td>
    </tr>
  </table>

</main>
</body>
</html>
