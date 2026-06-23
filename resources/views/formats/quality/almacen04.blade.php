<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inspección de Recepción de Carga</title>
    <style>
    @page { margin: 18px 24px; }
    body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
    .w-full { width: 100%; }
    .tbl    { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .b1 td, .b1 th { border: 1px solid #000; }
    .c { text-align: center; }
    .p4 { padding: 4px; }
    .p6 { padding: 6px; }
    .t12 { font-size: 12pt; font-weight: bold; }
    .t9  { font-size: 9pt; }
    .title { text-align:center; font-weight: bold; margin: 10px 0 6px; }
    td { word-wrap: break-word; }
    .th-green {
        background:#92D050;
        font-weight:bold;
        color:#000;
    }
    .ph-img { height: 50px; border: 1px solid #000; margin: 2px 0; }
    .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
</style>
 <style>
    @page {
      margin: 140px 24px 24px 24px; 
    }
    header {
      position: fixed;
      top: -120px;    
      left: 0; right: 0;
      height: 110px;  
    }
    main { margin-top: 0; } 
  </style>
</head>
<body>

{{-- ================= ENCABEZADO ================= --}}
<header>
<table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
  <tr>
    <td style="width:22%; text-align:center; border:1px solid #000;">
      <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
    </td>
    <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
      <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
        INSPECCIÓN DE ALMACÉN
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
                SSS-FOR-CAL-04
        </div>
            <div style="padding:10px; text-align:center;">&nbsp;</div>
        </td>
    </tr>
</table>
</header>

<table class="tbl b1" style="font-size:9pt; margin-top:15px;">
  <tr class="th-green c">
    <td colspan="4" style="font-weight:bold; font-size:9pt; padding:3px;">
      Información de la inspección
    </td>
  </tr>

  <tr>
    <td colspan="4" style="padding:3px;"><b>Fecha de inspección:</b> {{ $fecha_inspeccion ?? '' }}</td>
  </tr>

  <tr>
    <td colspan="4" style="padding:3px;"><b>Inspector:</b> {{ $inspector ?? '' }}</td>
  </tr>

  <tr>
    <td colspan="2" style="width:50%; padding:3px;">
    <b>Inspección</b><br>

    @php
      $tv = (string)($turno ?? '');
      $map = [
        'matutina'  => '1',
        'vespertina'=> '2',
        'nocturna'  => '3',
      ];
      $tv = $map[$tv] ?? $tv;
      $turnoOpts = [
        '1'     => '1',
        '2'     => '2',
        '3'     => '3',
        'mixto' => 'Mixto',
      ];
    @endphp

    @foreach($turnoOpts as $val => $label)
      {{ $label }}
      <span class="cb">{{ $tv === (string)$val ? '☑' : '☐' }}</span><br>
    @endforeach
  </td>

    <td colspan="2" style="width:50%; padding:3px;">
      <b>Hora:</b> {{ $hora_turno ?? '' }}
    </td>
  </tr>

  <tr>
    <td colspan="4" style="padding:3px;">
      <b>Área:</b>
      Nave 1 <span class="cb">{{ !empty($area_nave1) ? '☑' : '☐' }}</span> &nbsp;&nbsp; 
      Nave 2 <span class="cb">{{ !empty($area_nave2) ? '☑' : '☐' }}</span> &nbsp;&nbsp; 
      Otro   <span class="cb">{{ !empty($area_otro_flag) ? '☑' : '☐' }}</span> &nbsp;&nbsp; 
      @if(!empty($area_otro_flag) && !empty($area_otro))
        <b>Especifique:</b> {{ $area_otro }}
      @endif
    </td>
  </tr>

  <tr>
    <td colspan="4" style="padding:3px;"><b>Responsable del área:</b> {{ $responsable ?? '' }}</td>
  </tr>
</table>

{{-- ================= TABLA PRINCIPAL DE OBSERVACIONES ================= --}}
<table class="tbl b1" style="font-size:9pt; margin-top:15px;">
  <thead>
    <tr class="c th-green">
      <th style="width:15%; padding:6px;" rowspan="2">Observación</th>
      <th style="width:28%; padding:6px;" rowspan="2">Evidencia</th>
      <th style="width:17%; padding:6px;" rowspan="2">Ubicación</th>
      <th style="width:12%; padding:6px;" colspan="2">Revisión</th>
      <th style="width:13%; padding:6px;" rowspan="2">Fecha de corrección</th>
      <th style="width:15%; padding:6px;" rowspan="2">Evidencia de corrección</th>
    </tr>
    <tr class="c th-green">
      <th style="width:6%; padding:4px;">Cumple</th>
      <th style="width:6%; padding:4px;">No cumple</th>
    </tr>
  </thead>
  <tbody>
    @forelse($obs_rows as $r)
      <tr>
        <td class="p6 l">{{ $r['name'] ?? '' }}</td>
        <td class="p6">
          @if(!empty($r['evidencia_img']))
            <img src="{{ $r['evidencia_img'] }}" style="display:block; max-height:80px; width:100%; object-fit:contain;">
          @endif
        </td>
        <td class="p6 l">{{ $r['ubicacion'] ?? '' }}</td>
        <td class="p6 c cb">{{ !empty($r['cumple']) ? '☑' : '☐' }}</td>
        <td class="p6 c cb">{{ !empty($r['no_cumple']) ? '☑' : '☐' }}</td>
        <td class="p6 c">{{ $r['fecha'] ?? '' }}</td>
        <td class="p6">
          @if(!empty($r['ev_corr_img']))
            <img src="{{ $r['ev_corr_img'] }}" style="display:block; max-height:80px; width:100%; object-fit:contain;">
          @endif
        </td>
      </tr>
    @empty
      <tr>
        <td class="p6 c" colspan="7">Sin observaciones.</td>
      </tr>
    @endforelse
  </tbody>
</table>

{{-- ================= COMENTARIOS DE ALMACÉN ================= --}}
<div class="title" style="margin-top:15px;">Comentarios de almacén</div>
<table class="tbl b1" style="font-size:10pt;">
  <tr>
    <td class="p6" style="height:80px; text-align:left; vertical-align:top;">
      {{ $comentarios ?? '' }}
    </td>
  </tr>
</table>

{{-- ================= COMENTARIOS DE CALIDAD ================= --}}
<div class="title" style="margin-top:15px;">Comentarios de calidad</div>
<table class="tbl b1" style="font-size:10pt;">
  <tr>
    <td class="p6" style="height:80px; text-align:left; vertical-align:top;">
      {{ $comentarios_q ?? '' }}
    </td>
  </tr>
</table>

</body>
</html>