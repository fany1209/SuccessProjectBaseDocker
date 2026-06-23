<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitud de Formulación</title>
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
    .th-green{background:#92D050;font-weight:bold;color:#000}
    .muted{font-size:9pt;color:#111}
    .cb{font-family: DejaVu Sans, Arial, Helvetica, sans-serif;} /* ☑/☐ */
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
          SOLICITUD DE FORMULACIÓN
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
              30-Agosto-2024
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
          <b>Código:</b><br>SSS-FOR-LID-09
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 2 }}
        </div>
      </td>
    </tr>
  </table>
</header>
<main>

  {{-- ===================== FECHA DE SOLICITUD ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr>
      <td class="p6 c" style="font-weight:bold;">Fecha de Solicitud: {{ $fecha_solicitud ?? '' }}</td>
    </tr>
  </table>

  {{-- ===================== PROPUESTA / CAMPO DE APLICACIÓN ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr>
      <td class="p4" style="width:28%;"><b>Propuesta del nombre del producto:</b></td>
      <td class="p4" colspan="3">{{ $propuesta_nombre ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Campo de aplicación</b> <span class="muted"></span></td>
      <td class="p4" colspan="3">
        @php
          $ap = (array)($aplicacion ?? []); $m = fn($k)=>in_array($k,$ap)?'☑':'☐';
        @endphp
        Agrícola <span class="cb">{{ $m('agricola') }}</span> &nbsp;
        Pecuario <span class="cb">{{ $m('pecuario') }}</span> &nbsp;
        Petfood <span class="cb">{{ $m('petfood') }}</span> &nbsp;
        Otro <span class="cb">{{ $m('otro') }}</span>
        @if(in_array('otro',$ap) && !empty($ap_otro))
          &nbsp;&nbsp;<b>Especifique:</b> {{ $ap_otro }}
        @endif
      </td>
    </tr>
    <tr>
      <td class="p4"><b>Uso Específico:</b> <span class="muted">Elija un elemento.</span></td>
      <td class="p4" colspan="3">{{ $uso_especifico ?? '' }} @if(!empty($uso_otro)) &nbsp; <b>Otro:</b> {{ $uso_otro }} @endif</td>
    </tr>
  </table>

{{-- ===================== INFORMACIÓN DEL DESARROLLO / FORMULACIÓN ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px; width:100%;">
    <tr class="th-green c">
      <td class="p4">INFORMACIÓN DEL DESARROLLO / FORMULACIÓN</td>
    </tr>

    <tr>
      <td class="p4" style="height:42px; vertical-align:top;">
        <b>Objetivo:</b>
        <span class="muted"></span>
        {{ $objetivo ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" style="height:42px; vertical-align:top;">
        <b>Información Relevante:</b>
        <span class="muted"></span>
        {{ $info_relevante ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" style="height:60px; vertical-align:top;">
        <b>Composición:</b>
        <span class="muted"></span>
        {{ $composicion ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" style="height:42px; vertical-align:top;">
        <b>Resultados:</b>
        <span class="muted"></span>
        {{ $resultados ?? '' }}
      </td>
    </tr>
  </table>

  {{-- ===================== INFORMACIÓN DE AUTORIZACIÓN ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c">
      <td class="p4" colspan="3">INFORMACIÓN DE AUTORIZACIÓN</td>
    </tr>
    <tr class="c" style="font-weight:bold;">
      <td class="p4" style="width:60%;">Criterio</td>
      <td class="p4" style="width:12%;">Cumple</td>
      <td class="p4" style="width:28%;">Observaciones</td>
    </tr>

    @php
      $au = (array)($autorizacion ?? []);
      $fmt = fn($k)=>!empty($au[$k]) ? '☑' : '☐';
      $obs = fn($k)=>$au[$k.'_obs'] ?? '';
    @endphp

    <tr>
      <td class="p4">Cuenta con solvencia técnico-científica</td>
      <td class="p4 c cb">{{ $fmt('solvencia') }}</td>
      <td class="p4">{{ $obs('solvencia') }}</td>
    </tr>
    <tr>
      <td class="p4">Se tienen los insumos e infraestructura (interna o externa)</td>
      <td class="p4 c cb">{{ $fmt('insumos') }}</td>
      <td class="p4">{{ $obs('insumos') }}</td>
    </tr>
    <tr>
      <td class="p4">Tiene establecido modo y tiempo de conservación y uso en cuenta del cliente</td>
      <td class="p4 c cb">{{ $fmt('modo_tiempo') }}</td>
      <td class="p4">{{ $obs('modo_tiempo') }}</td>
    </tr>
    <tr>
      <td class="p4">Es viable para la producción eficientemente y con control a costos de producción de proceso final</td>
      <td class="p4 c cb">{{ $fmt('viabilidad') }}</td>
      <td class="p4">{{ $obs('viabilidad') }}</td>
    </tr>
  </table>

  {{-- ===================== ANEXOS ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr>
      <td class="p4" style="width:12%;"><b>Anexos:</b></td>
      <td class="p4" style="height:60px;vertical-align:top;">{{ $anexos ?? '' }}</td>
    </tr>
  </table>

  {{-- ===================== FIRMAS ===================== --}}
  <table class="tbl" style="margin-top:28px; font-size:10pt;">
    <tr>
      <td class="p6 c" style="width:33%;">
        <div style="border-top:1px solid #000; height:0; margin:38px 24px 6px;"></div>
        <div class="muted"> &nbsp; Formuló</div>
        <div>{{ $firmo_nombre ?? '' }}</div>
      </td>
      <td class="p6 c" style="width:33%;">
        <div style="border-top:1px solid #000; height:0; margin:38px 24px 6px;"></div>
        <div class="muted"> &nbsp; Revisó</div>
        <div>{{ $reviso_nombre ?? '' }}</div>
      </td>
      <td class="p6 c" style="width:34%;">
        <div style="border-top:1px solid #000; height:0; margin:38px 24px 6px;"></div>
        <div class="muted"> &nbsp; Autorizó</div>
        <div>{{ $autorizo_nombre ?? '' }}</div>
      </td>
    </tr>
  </table>

</main>
</body>
</html>
