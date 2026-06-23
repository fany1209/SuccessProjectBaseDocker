<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Certificado de Calidad — SSS-FOR-CAL-07</title>
  <style>
    @page { margin: 18px 24px; }
    body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
    .tbl    { width:100%; border-collapse:collapse; table-layout:fixed; }
    .b1 td, .b1 th { border:1px solid #000; }
    .c { text-align:center; } .l { text-align:left; } .r { text-align:right; }
    .p3 { padding:3px; } .p4 { padding:4px; } .p6 { padding:6px; }
    .title { text-align:center; font-weight:bold; margin: 14px 0 8px; font-size: 12pt; }
    .th-green { background:#92D050; color:#000; font-weight:bold; }
    td, th { word-wrap: break-word; }
    .small { font-size: 9pt; }
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
        CERTIFICADO DE CALIDAD
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
              SSS-FOR-CAL-07
        </div>
        <div style="padding:10px; text-align:center;">
            Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
        </td>
    </tr>
    <!-- CONSECUTIVO -->
    <td colspan="3" class="p4 l">
      <b>Consecutivo:</b> SCC25-{{ $consecutivo ?? '' }}
    </td>
  </tr>
</table>

{{-- ================= DATOS GENERALES ================= --}}
<p class="title">DATOS GENERALES</p>
<table class="tbl b1" style="font-size:10pt; margin-top:15px;" >
  <tr>
    <td class="p4 l" style="width:50%;"><b>Ciudad:</b> {{ $ciudad ?? '' }}</td>
    <td class="p4 l" style="width:50%;"><b>Fecha:</b> {{ $fecha ?? '' }}</td>
  </tr>
  <tr>
    <td class="p4 l"><b>Cliente:</b> {{ $cliente ?? '' }}</td>
    <td class="p4 l"><b>Producto:</b> {{ $producto ?? '' }}</td>
  </tr>
  <tr>
    <td class="p4 l"><b>Lote:</b> {{ $lote ?? '' }}</td>
    <td class="p4 l"><b>Cantidad:</b> {{ $cantidad ?? '' }}</td>
  </tr>
  <tr>
    <td class="p4 l"><b>Fecha de fabricación:</b> {{ $fecha_fabricacion ?? '' }}</td>
    <td class="p4 l"><b>Fecha de caducidad:</b> {{ $fecha_caducidad ?? '' }}</td>
  </tr>
</table>

{{-- ================= ANÁLISIS BROMATOLÓGICO ================= --}}
@php
  $bromato = is_array($bromato ?? null) ? $bromato : [];
  $bromato = array_values(array_filter($bromato, function($r){
    $p = trim($r['prueba'] ?? '');
    $e = trim($r['especificacion'] ?? '');
    $re= trim($r['resultado'] ?? '');
    return $p !== '' || $e !== '' || $re !== '';
  }));
@endphp

@if(count($bromato) > 0)
  <p class="title">ANÁLISIS BROMATOLÓGICO</p>
  <table class="tbl b1" style="font-size:10pt;">
    <thead>
      <tr class="c th-green">
        <th style="width:40%; padding:4px;">Prueba</th>
        <th style="width:30%; padding:4px;">Especificación</th>
        <th style="width:30%; padding:4px;">Resultado</th>
      </tr>
    </thead>
    <tbody>
      @foreach($bromato as $row)
        <tr>
          <td class="p4 c">{{ $row['prueba'] ?? '' }}</td>
          <td class="p4 c">{{ $row['especificacion'] ?? '' }}</td>
          <td class="p4 c">{{ $row['resultado'] ?? '' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif

{{-- ================= ANÁLISIS MICROBIOLÓGICOS ================= --}}
@php
  $micro = is_array($micro ?? null) ? $micro : [];
  $micro = array_values(array_filter($micro, function($r){
    $p = trim($r['prueba'] ?? '');
    $re= trim($r['resultado'] ?? '');
    $u = trim($r['unidades'] ?? '');
    return $p !== '' || $re !== '' || $u !== '';
  }));
@endphp

@if(count($micro) > 0)
  <p class="title">ANÁLISIS MICROBIOLÓGICOS</p>
  <table class="tbl b1" style="font-size:10pt;">
    <thead>
      <tr class="c th-green">
        <th style="width:40%; padding:4px;">Prueba</th>
        <th style="width:30%; padding:4px;">Resultado</th>
        <th style="width:30%; padding:4px;">Unidades</th>
      </tr>
    </thead>
    <tbody>
      @foreach($micro as $row)
        <tr>
          <td class="p4 c">{{ $row['prueba'] ?? '' }}</td>
          <td class="p4 c">{{ $row['resultado'] ?? '' }}</td>
          <td class="p4 c">{{ $row['unidades'] ?? '' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif

{{-- ================= PIE / RESPONSABLE (FIJO, oculta solo el sello si está vacío) ================= --}}
@php $noSello = trim((string)($no_sello ?? '')); @endphp
<table class="tbl" style="font-size:10pt; margin-top:14px;">
  @if($noSello !== '')
    <tr>
      <td class="p4 l" style="border:0;"><b>No. Sello:</b></td>
    </tr>
    <tr>
      <td class="p4 l" style="border:0;">{{ $noSello }}</td>
    </tr>
  @endif
  <tr>
    <td class="p4 l" style="border:0; font-weight:bold;">CERTIFICADO VALIDO SIN FIRMA</td>
  </tr>
  <tr>
    <td class="p4 l" style="border:0;">&nbsp;</td>
  </tr>
 <tr>
  <td class="p4 l" style="border:0;">
    {{ !empty($firmante_nombre) ? $firmante_nombre : 'MBP. Claudio Rugarcia Anaya' }}
  </td>
</tr>
<tr>
  <td class="p4 l" style="border:0;">
    {{ !empty($firmante_puesto) ? $firmante_puesto : 'Jefe de calidad' }}
  </td>
</tr>
<tr>
  <td class="p4 l" style="border:0;">
    Cédula profesional {{ !empty($firmante_cedula) ? $firmante_cedula : '11078201' }}
  </td>
</tr>

</table>
</body>
</html>
