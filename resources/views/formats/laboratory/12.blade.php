<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Protocolo de seguimiento en campo</title>
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
    .cb{font-family: DejaVu Sans, Arial, Helvetica, sans-serif;} 
    .h60{height:60px}.h80{height:80px}.h100{height:100px}

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
          PROTOCOLO DE SEGUIMIENTO EN CAMPO
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>10-Enero-2025
            </td>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de modificación:</b>--
            </td>
            <td style="width:24%; padding:4px; text-align:center;">
              <b>Versión:</b>--
            </td>
          </tr>
        </table>
      </td>
      <td style="width:20%; border:1px solid #000; vertical-align:middle; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>SSS-FOR-LID-12
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
      </td>
    </tr>
  </table>
</header>
<main>

  {{-- Datos generales --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr>
      <td class="p4" style="width:50%;"><b>Asesor técnico:</b> {{ $asesor ?? '' }}</td>
      <td class="p4" style="width:50%;"><b>Productor:</b> {{ $productor ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Cultivo a tratar:</b> {{ $cultivo ?? '' }}</td>
      <td class="p4"><b>FOLIO:</b> {{ $folio ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4" colspan="2"><b>Producto para aplicar:</b> {{ $producto_aplicar ?? '' }}</td>
    </tr>
       <tr>
      <th style="text-align:left; padding:6px; border:1px solid #000;">Peso</th>
      <td style="padding:6px; border:1px solid #000;">
        {{ isset($peso) && $peso !== '' ? $peso : 'N/A' }}
      </td>
    </tr>
  </table>

  {{-- Objetivo / Condiciones / Ubicación --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr>
      <td class="p4" style="width:22%;"><b>Objetivo:</b></td>
      <td class="p4" colspan="3" style="height:60px; vertical-align:top;">{!! nl2br(e($objetivo ?? '')) !!}</td>
    </tr>
    <tr>
      <td class="p4"><b>Condiciones del campo:</b></td>
      <td class="p4" colspan="3" style="height:60px; vertical-align:top;">{!! nl2br(e($condiciones ?? '')) !!}</td>
    </tr>
    <tr>
      <td class="p4"><b>Coordenadas:</b></td>
      <td class="p4" colspan="2">{{ $ubicacion ?? '' }}</td>
      <td class="p4" style="width:28%; text-align:center;">
        <b>Croquis:</b><br>
        @if(!empty($croquis_src))
          <img src="{{ $croquis_src }}" style="width:100%; height:120px; object-fit:contain;">
        @else
          <div class="h100"></div>
        @endif
      </td>
    </tr>
    <tr>
      <td class="p4"><b>Ubicación:</b></td>
      <td class="p4" colspan="3">{{ $ubicacion_nombre ?? '' }}</td>
    </tr>
  </table>

  {{-- División en bloques / Tratamiento / Dosis --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr>
      <td class="p4" style="width:22%;"><b>División en bloques:</b></td>
      <td class="p4" colspan="3" style="height:60px; vertical-align:top;">{!! nl2br(e($division_bloques ?? '')) !!}</td>
    </tr>
    <tr>
      <td class="p4"><b>Tratamiento:</b></td>
      <td class="p4" colspan="3" style="height:60px; vertical-align:top;">{!! nl2br(e($tratamiento ?? '')) !!}</td>
    </tr>
    <tr>
      <td class="p4"><b>Dosis:</b></td>
      <td class="p4" colspan="3" style="height:80px; vertical-align:top;">
        @php $d = (array)($dosis ?? []); @endphp
        @if(empty($d))
          <div class="h80"></div>
        @else
          <ol style="margin:0 0 0 18px; padding:0;">
            @foreach($d as $item)<li>{{ $item }}</li>@endforeach
          </ol>
        @endif
      </td>
    </tr>
  </table>

  {{-- Fechas --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr>
      <td class="p4" style="width:50%;"><b>Fecha de aplicación:</b> {{ $fecha_aplicacion ?? '' }}</td>
      <td class="p4" style="width:50%;"><b>Documento resultado de muestreo:</b> {{ $doc_muestreo ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4" colspan="2">
        <b>Fechas de muestreos:</b><br>
        @php
          $fm = $fechas_muestreo ?? [];
          $fmt = fn($k) => $fm[$k] ?? '';
        @endphp
        1. (Antes de aplicación) {{ $fmt('antes') }} &nbsp;&nbsp;
        2. (15 días) {{ $fmt('15') }} &nbsp;&nbsp;
        3. (25 días) {{ $fmt('25') }} &nbsp;&nbsp;
        4. (35 días) {{ $fmt('35') }}
      </td>
    </tr>
  </table>

  {{-- Variables agronómicas --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr>
      <td class="p4" style="width:28%;"><b>Variables agronómicas:</b></td>
      <td class="p4" style="height:80px; vertical-align:top;">{!! nl2br(e($variables_agro ?? '')) !!}</td>
    </tr>
  </table>

  {{-- Observaciones adicionales --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr>
      <td class="p4" style="width:28%;"><b>Observaciones adicionales:</b></td>
      <td class="p4 h80">{!! nl2br(e($observaciones ?? '')) !!}</td>
    </tr>
  </table>

  {{-- Nota --}}
  <div class="muted" style="margin-top:8px;">
    <b>NOTA:</b> Las evidencias serán muestreo de suelo y tejido vegetal y material fotográfico.
    El muestreo y reporte de las evidencias lo realizará el asesor técnico, y el análisis de parámetros de suelo y tejido vegetal
    lo realizará el personal de laboratorio.
  </div>

</main>
</body>
</html>
