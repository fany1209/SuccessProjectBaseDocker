<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Orden de Producción</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body  { margin:0; font-family: Arial, Helvetica, sans-serif; font-size:11pt; color:#000; }
    .tbl{ width:100%; border-collapse:collapse; table-layout:fixed; }
    .b1 td,.b1 th{ border:1px solid #000; }
    td,th{ vertical-align:top; word-wrap:break-word; }
    .p2{padding:2px}.p4{padding:4px}.p6{padding:6px}
    .c{text-align:center}.l{text-align:left}.r{text-align:right}
    .t12{font-size:12pt;font-weight:bold}
    .t9{font-size:9pt}
    .th-green{background:#92D050;font-weight:bold; color:#000;}
    .muted{font-size:9pt;color:#111}

    header{ position:fixed; top:-120px; left:0; right:0; height:110px; z-index:10; }
    footer{ position:fixed; bottom:-60px; left:0; right:0; height:60px; font-size:9pt; z-index:10; }
    main  { margin-top:0; }
    header td, header th {
      vertical-align: middle !important; 
    }
    header img {
      display:block;  
      margin:0 auto; 
    }
  </style>
</head>
<body>

<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
    <tr>
      <td style="width:22%; text-align:center; border:1px solid #000; vertical-align:middle;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>
      <td style="width:58%; border:1px solid #000; vertical-align:middle; padding:0;">
        <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
          ORDEN DE PRODUCCIÓN
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>25-Enero-2025
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
      <td style="width:20%; border:1px solid #000; vertical-align:middle; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>SSS-FOR-PRO-01
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
      </td>
    </tr>
  </table>
</header>
<main>

  {{-- ====== CABECERA DE ORDEN ====== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr class="th-green c">
      <td class="p4" colspan="6">ORDEN DE PRODUCCIÓN</td>
    </tr>

    <tr>
      <td class="p4" colspan="6"><b>Fecha:</b> {{ $fecha ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="6"><b>Producto a fabricar:</b> {{ $producto ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="6"><b>Código de producto (SKU):</b> {{ $sku ?? '' }}</td>
    </tr>
  </table>

{{-- ====== CANTIDADES ====== --}}
  @php
    $vol_fabricar = (float)($volumen_fabricar_l ?? 0);
    $vol_producir = (float)($volumen_producir_l ?? $vol_fabricar);
    $tipo_doc     = $tipo ?? 'Pedido'; // Pedido, Muestra, etc.

    // Unidad dinámica desde el form (fallback a 'L')
    $unidad_raw   = isset($unidad_cantidades) ? trim((string)$unidad_cantidades) : 'L';
    $unidad       = $unidad_raw !== '' ? $unidad_raw : 'L';
    $u            = ' ' . $unidad; // separador con espacio
  @endphp

  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" style="width:25%;">TIPO</td>
      <td class="p4" style="width:25%;">CANTIDAD</td>
      <td class="p4" style="width:50%;">PRODUCIR CANTIDAD</td>
    </tr>
    <tr class="c">
      <td class="p4">{{ $tipo_doc }}</td>
      <td class="p4">{{ number_format($vol_fabricar, 0, '.', ',') }}{{ $u }}</td>
      <td class="p4">Producir: {{ number_format($vol_producir, 0, '.', ',') }}{{ $u }}</td>
    </tr>
  </table>

  {{-- ====== MATERIALES ====== --}}
  @php
    $rows = (array)($materiales ?? []);
    foreach ($rows as $i => $row) {
      $c1l = isset($row['cant_por_1l']) ? (float)$row['cant_por_1l'] : null;
      if (!isset($row['total_utilizar']) && $c1l !== null) {
        $rows[$i]['total_utilizar'] = round($c1l * $vol_producir, 3);
      }
    }
  @endphp

  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" style="width:40%;">MATERIALES</td>
      <td class="p4" style="width:15%;">CANTIDAD PARA 1 L</td>
      <td class="p4" style="width:10%;">UNIDAD</td>
      <td class="p4" style="width:15%;">TOTAL A UTILIZAR</td>
      <td class="p4" style="width:20%;">LOTE</td>
    </tr>
    @forelse($rows as $r)
      <tr>
        <td class="p4">{{ $r['nombre'] ?? '' }}</td>
        <td class="p4 c">{{ isset($r['cant_por_1l']) ? rtrim(rtrim(number_format((float)$r['cant_por_1l'],3,'.',''), '0'),'.') : '' }}</td>
        <td class="p4 c">{{ $r['unidad'] ?? '' }}</td>
        <td class="p4 c">
          {{ isset($r['total_utilizar']) ? rtrim(rtrim(number_format((float)$r['total_utilizar'],3,'.',''), '0'),'.') : '' }}
          {{ $r['unidad'] ?? '' }}
        </td>
        <td class="p4">{{ $r['lote'] ?? '' }}</td>
      </tr>
    @empty
      <tr><td class="p4 c" colspan="5">Sin materiales.</td></tr>
    @endforelse
  </table>

  {{-- ====== RECEPCIÓN DE MATERIA PRIMA ====== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="2">RECEPCIÓN DE MATERIA PRIMA</td>
    </tr>
    <tr>
      <td class="p4" style="width:35%;"><b>Nombre</b></td>
      <td class="p4" style="width:65%;">{{ $recepcion_mp_nombre ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Fecha</b></td>
      <td class="p4">{{ $recepcion_mp_fecha ?? '' }}</td>
    </tr>
  </table>


  {{-- ====== PROCEDIMIENTO DE FABRICACIÓN ====== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c"><td class="p4" colspan="4">PROCEDIMIENTO DE FABRICACIÓN</td></tr>
    <tr>
      <td class="p4" colspan="4" style="height:48px; vertical-align:top;">
        1. Revisar documento “formato de formulación” (SSS-FOR-LID-10) correspondiente al producto.<br>
        {!! nl2br(e($procedimiento_extra ?? '')) !!}
      </td>
    </tr>
  </table>

  {{-- ====== CONDICIONES DE CONTROL ====== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" style="width:30%;">CONDICIONES DE CONTROL</td>
      <td class="p4" style="width:15%;">UNIDAD</td>
      <td class="p4" style="width:15%;">VALOR</td>
      <td class="p4" style="width:40%;">OBSERVACIONES</td>
    </tr>
    @php
      $ctrl = $controles ?? [
        ['param'=>'Temperatura','unidad'=>'°C','valor'=>'','obs'=>''],
        ['param'=>'Agitación','unidad'=>'rpm','valor'=>'','obs'=>''],
        ['param'=>'Otro','unidad'=>'','valor'=>'','obs'=>''],
      ];
    @endphp
    @foreach($ctrl as $c1)
      <tr>
        <td class="p4">{{ $c1['param'] ?? '' }}</td>
        <td class="p4 c">{{ $c1['unidad'] ?? '' }}</td>
        <td class="p4 c">{{ $c1['valor'] ?? '' }}</td>
        <td class="p4">{{ $c1['obs'] ?? '' }}</td>
      </tr>
    @endforeach
  </table>

  {{-- ====== SALIDA DE PRODUCTO ====== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="2">SALIDA DE PRODUCTO</td>
    </tr>
    <tr>
      <td class="p4" style="width:30%;"><b>N° de Lote</b></td>
      <td class="p4" style="width:70%;">{{ $lote_salida ?? '' }}</td>
    </tr>
  </table>

  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" style="width:50%;">VALIDACIÓN DEL ÁREA DE CALIDAD</td>
      <td class="p4" style="width:50%;">RECEPCIÓN DEL ÁREA DE ALMACÉN</td>
    </tr>
    <tr>
      <td class="p6" style="height:64px; vertical-align:bottom;">
        <div style="border-top:1px solid #000; height:0; margin:0 24px 6px;"></div>
        <div class="c">Nombre: {{ $val_calidad_nombre ?? '' }}</div>
      </td>
      <td class="p6" style="height:64px; vertical-align:bottom;">
        <div style="border-top:1px solid #000; height:0; margin:0 24px 6px;"></div>
        <div class="c">Nombre: {{ $rec_almacen_nombre ?? '' }}</div>
      </td>
    </tr>
  </table>

</main>
</body>
</html>
