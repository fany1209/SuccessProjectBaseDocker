<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inspección de Recepción de Embalaje</title>
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
        .t9  { font-size: 9pt;  }
        .title { text-align:center; font-weight: bold; margin: 10px 0 6px; }

        td { word-wrap: break-word; }
        .tbl { width:100%; border-collapse:collapse; table-layout:fixed; }
        .tbl td, .tbl th {
            border:1px solid #000;
            padding:4px;
            font-size:10pt;
        }
        .c { text-align:center; }
        .th-green {
            background:#92D050;
            font-weight:bold;
            color:#000;
        }
        .footer {
        position: fixed;
        left: 0; right: 0; bottom: 0;
        height: 35px;
        text-align: center;
        font-size: 9pt;
        color: #333;
        line-height: 35px;
    }
    </style>
</head>
<body>

<div class="footer">
  Generado el {{ $generated_at ?? now('America/Mexico_City')->format('d/m/Y H:i') }}
</div>

{{-- =================== CABECERA =================== --}}
<table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
  <tr>
    <td style="width:22%; text-align:center; border:1px solid #000;">
      <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
    </td>

    <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
      <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
        INSPECCIÓN DE RECEPCIÓN DE EMBALAJE
      </div>

    <table style="width:100%; border-collapse:collapse; font-size:8.5pt;">
        <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
                <b>Fecha de elaboración:</b><br>
                21-Marzo-2025
            </td>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
                <b>Fecha de actualización:</b>--<br>
            </td>
            <td style="width:24%; padding:4px; text-align:center;">
                <b>Versión:</b>00
            </td>
        </tr>
    </table>
    </td>

    <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
      <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>
          SSS-FOR-CAL-01
      </div>
      <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
      </div>
    </td>
  </tr>
</table>

{{-- =================== DATOS GENERALES =================== --}}
<div class="title" style="margin-top:20px;">Datos Generales</div>

<table class="tbl b1 t9">
    <tr>
        <td class="p6" style="width:50%;"><b>Proveedor:</b> {{ $proveedor ?? 'name' }}</td>
        <td class="p6" style="width:50%;"><b>Fecha de Llegada:</b> {{ $fecha_llegada ?? 'date' }}</td>
    </tr>
    <tr>
        <td class="p6"><b>Código de Proveedor:</b> {{ $codigo_proveedor ?? 'codigo' }}</td>
        <td class="p6"><b>Fecha de Inspección:</b> {{ $fecha_inspeccion ?? 'date' }}</td>
    </tr>
</table>

{{-- =================== DATOS DEL PRODUCTO =================== --}}
<p class="c" style="font-weight:bold; margin:15px 0 6px;">Datos del producto</p>

<table class="tbl" style="width:100%; border-collapse:collapse; table-layout:fixed;">
    <thead>
        <tr class="c th-green" >
            <th style="width:50%; padding:10px 6px;">Producto</th>
            <th style="width:16.6%; padding:10px 6px;">N° de lote</th>
            <th style="width:16.7%; padding:10px 6px;">Cantidad</th>
            <th style="width:16.7%; padding:10px 6px;">Empaque</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($productos) && count($productos) > 0)
            @foreach($productos as $p)
                <tr class="c">
                    <td style="padding:2px 4px; font-size:12px;">{{ $p->product_name ?? $p['product_name'] ?? '—' }}</td>
                    <td style="padding:2px 4px; font-size:12px;">{{ $p->batch ?? $p['batch'] ?? '—' }}</td>
                    <td style="padding:2px 4px; font-size:12px;">{{ $p->qty ?? $p['qty'] ?? '—' }}</td>
                    <td style="padding:2px 4px; font-size:12px;">{{ $p->pack ?? $p['pack'] ?? '—' }}</td>
                </tr>
            @endforeach
        @else
            <tr class="c">
                <td style="padding:2px 4px; font-size:12px;">—</td>
                <td style="padding:2px 4px; font-size:12px;">—</td>
                <td style="padding:2px 4px; font-size:12px;">—</td>
                <td style="padding:2px 4px; font-size:12px;">—</td>
            </tr>
        @endif
    </tbody>
</table>

{{-- =================== LIBERACIÓN DE PRODUCTO =================== --}}
<p class="c" style="font-weight:bold; margin:15px 0 6px;">Liberación de producto</p>

  @php
      $rubros = [
          ['k'=>'cantidad',       't'=>'Cantidad solicitada',                                  'm'=>5],
          ['k'=>'identificacion', 't'=>'Identificación del producto (nombre, lote, cantidad)', 'm'=>5],
          ['k'=>'empaque',        't'=>'Empaque (no roto, no sucio)',                          'm'=>10],
          ['k'=>'sellado',        't'=>'Sellado (embonado de tapa, sin derrames)',             'm'=>15],
          ['k'=>'limpieza',       't'=>'Libre de materia extraña y fauna nociva',              'm'=>15],
          ['k'=>'caducidad',      't'=>'Fecha de caducidad vigente',                           'm'=>25],
          ['k'=>'certificado',    't'=>'Certificado de calidad de proveedor',                  'm'=>25],
      ];

      $evals  = $evals  ?? [];     
      $obs    = $obs    ?? [];      
      $total  = $release_total ?? collect($rubros)->sum(fn($r) => (int)($evals[$r['k']] ?? 0));
      $status = $release_status ?? (($total >= 70) ? 'ACEPTABLE' : 'NO ACEPTABLE');
  @endphp

  <table class="tbl" style="width:100%; border-collapse:collapse; table-layout:fixed;">
      <thead>
          <tr class="c th-green">
              <th style="width:42%; padding:2px 4px; font-size:12px;">Concepto</th>
              <th style="width:12%; padding:2px 4px; font-size:12px;">Valor</th>
              <th style="width:16%; padding:2px 4px; font-size:12px;">Evaluación</th>
              <th style="width:30%; padding:2px 4px; font-size:12px;">Observaciones</th>
          </tr>
      </thead>
      <tbody>
          @foreach($rubros as $r)
              <tr>
                  <td style="padding:2px 4px; font-size:12px; text-align:left;">{{ $r['t'] }}</td>
                  <td class="c" style="padding:2px 4px; font-size:12px;">{{ $r['m'] }}</td>
                  <td class="c" style="padding:2px 4px; font-size:12px;">{{ (int)($evals[$r['k']] ?? 0) }}</td>
                  <td style="padding:2px 4px; font-size:12px;">{{ $obs[$r['k']] ?? '' }}</td>
              </tr>
          @endforeach
          <tr>
              <td style="padding:2px 4px; font-size:12px; font-weight:bold; text-align:left;">Total</td>
              <td class="c" style="font-weight:bold; padding:2px 4px; font-size:12px;">100</td>
              <td class="c" style="font-weight:bold; padding:2px 4px; font-size:12px;">{{ $total }}</td>
              <td style="font-weight:bold; text-align:center; font-size:12px;">{{ $status }}</td>
          </tr>
      </tbody>
  </table>

  <p style="text-align:right; font-size:10pt; margin:15px 0; padding-right:90px;">
      Estado de la carga:
      @php $color = ($status === 'ACEPTABLE') ? 'green' : 'red'; @endphp
      <b><u><span style="color:{{ $color }};">{{ $status }}</span></u></b>
  </p>

  <p><strong>Certificado de Calidad:</strong> 
    @if($has_certificate)
        @php
            $certName = '';
            if (isset($certificate) && $certificate) {
                $certData = \DB::table('supplier_certificates as sc')
                    ->join('suppliers as s', 'sc.supplier_id', '=', 's.supplier_id')
                    ->join('files as f', 'sc.file_id', '=', 'f.file_id')
                    ->join('products as p', 'f.product_id', '=', 'p.product_id')
                    ->where('sc.id', $certificate->id)
                    ->select('s.name as supplier_name', 'p.name as product_name', 'sc.fecha_emision')
                    ->first();
                
                if ($certData) {
                    $fecha = $certData->fecha_emision ? " (Emitido: {$certData->fecha_emision})" : '';
                    $certName = "{$certData->supplier_name} - {$certData->product_name}{$fecha}";
                }
            }
        @endphp
        Sí 
        @if($certName)
            <br> <span style="font-size: 10pt; color: #555;">{{ $certName }}</span>
        @endif
    @else
        No
    @endif
  </p>

    @php
    $lib = strtolower($producto_liberado ?? '');
    $isSi = $lib === 'si';
    $isNo = $lib === 'no';
    $folioMostrar = trim($folio ?? '') !== '' ? trim($folio) : 'NA';
    $mark = fn($cond) => $cond ? 'X' : '';
  @endphp

  <table class="tbl b1" style="width:100%; border-collapse:collapse; table-layout:fixed; font-size:12px;">
    <thead>
      <tr class="c" style="background:#f3f4f6; font-weight:bold;">
        <th style="width:70%; padding:4px; text-align:left;">Producto liberado</th>
        <th style="width:15%; padding:4px;">Sí</th>
        <th style="width:15%; padding:4px;">No</th>
      </tr>
    </thead>
    <tbody>
      <tr class="c">
        <td style="padding:4px; text-align:left;">Seleccione una opción</td>
        <td style="padding:4px;">{{ $mark($isSi) }}</td>
        <td style="padding:4px;">{{ $mark($isNo) }}</td>
      </tr>
      <tr>
        <td style="padding:4px; text-align:left;" colspan="3">
          <b>Folio:</b> {{ $folioMostrar }}
        </td>
      </tr>
    </tbody>
  </table>

{{-- =================== INCIDENCIAS =================== --}}
<table class="tbl" style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:15px;">
  <thead>
    <tr class="c th-green">
      <th style="width:48%; font-size:12px;">Incidencias</th>
      <th style="width:16%; font-size:12px;">
        Sí <span style="font-family: DejaVu Sans;">{{ $hasInc ? '☑' : '☐' }}</span>
      </th>
      <th style="width:16%; font-size:12px;">
        No <span style="font-family: DejaVu Sans;">{{ $hasInc ? '☐' : '☑' }}</span>
      </th>
      <th style="width:20%; font-size:12px; text-align:left;">
        Folio: {{ $folioInc ?? 'NA' }}
      </th>
    </tr>
  </thead>

  @if($hasInc)
  <tbody>
    <tr>
      <td colspan="4" style="font-size:12px;">
        <b>Descripción:</b>

        @if(!empty($descItems))
          <ul>
            @foreach($descItems as $li)
              <li>{{ $li }}</li>
            @endforeach
          </ul>
        @elseif(!empty($descTexto))
          {!! nl2br(e($descTexto)) !!}
        @else
          —
        @endif
      </td>
    </tr>

    <tr>
      <td colspan="4" style="font-size:12px;">
        <b>Acciones implementadas:</b>

        @if(!empty($actItems))
          <ul>
            @foreach($actItems as $li)
              <li>{{ $li }}</li>
            @endforeach
          </ul>
        @elseif(!empty($actTexto))
          {!! nl2br(e($actTexto)) !!}
        @else
          —
        @endif
      </td>
    </tr>
  </tbody>
  @endif
</table>


{{-- =================== FIRMA =================== --}}
  <br><br><br>
  <table class="tbl" style="border:none; width:100%; margin-top:10px;">
    <tr>
      <td style="border:none; text-align:left; padding-top:40px; width:60%; font-size:12px;">
        <span>Nombre de quien realizó la inspección:&nbsp;</span>
        <span style="display:inline-block; min-width:320px; border-bottom:1px solid #000; line-height:1.6;">
          {{ $inspector_nombre ?? '' }}
        </span>
      </td>
    </tr>
  </table>

</body>
</html>
