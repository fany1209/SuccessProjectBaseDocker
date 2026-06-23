<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Salida de Muestras</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color:#000; }
    .w-full { width: 100%; }
    .tbl { width:100%; border-collapse:collapse; table-layout:fixed; }
    .b1 td, .b1 th { border:1px solid #000; }
    td, th { vertical-align: middle; word-wrap:break-word; }
    .p4{padding:4px}.p6{padding:6px}
    .c{text-align:center}.l{text-align:left}.r{text-align:right}
    .t12{font-size:12pt;font-weight:bold}
    .t9{font-size:9pt}
    .cb{font-family: DejaVu Sans, Arial, Helvetica, sans-serif;} 
    .muted{font-size:9pt;color:#111}
    .th-green{background:#92D050;font-weight:bold;color:#000}
    .nowrap{ white-space: nowrap; }
    .no-break{ white-space: nowrap; overflow-wrap: normal; word-break: normal; }
    .vtop{ vertical-align: top; }

    header { position: fixed; top: -120px; left: 0; right: 0; height: 110px; z-index: 10; }
    footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 60px; font-size: 9pt; z-index: 10; }
    main   { margin-top: 0; }
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
          SALIDA DE MUESTRAS
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
                25-Mayo-2024
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

      <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>SSS-FOR-LID-03
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
      </td>
    </tr>
  </table>
</header>
<main>

  {{-- =================== FOLIO MUESTRA =================== --}}
    <div style="width:100%; text-align:right; margin-top:6px;">
      <span style="display:inline-block; border:1px solid #000; padding:6px 10px; font-weight:bold;">
        Folio muestra: {{ $folio_muestra }}
      </span>
    </div>

  {{-- ========================== INFORMACIÓN DE LA MUESTRA ========================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <colgroup>
      <col style="width:18%;">  {{-- etiqueta --}}
      <col style="width:32%;">  {{-- valor   --}}
      <col style="width:18%;">  
      <col style="width:32%;">  
    </colgroup>

    <tr class="th-green c">
      <td class="p4" colspan="4">INFORMACIÓN DE LA MUESTRA</td>
    </tr>

    <tr>
      <td class="p4" colspan="4" style="vertical-align:top;">
        <b>Producto:</b> {{ $producto ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="4" style="vertical-align:top;">
        <b>Nombre comercial:</b> {{ $nombre_comercial ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="4" style="padding:0;">
        <table style="width:100%; border-collapse:collapse;">
          <tr>
            <td class="p4" style="border:none; width:33%; border-right:1px solid #000;">
              <b>Lote:</b> {{ $lote ?? '' }}
            </td>
            <td class="p4" style="border:none; width:34%; border-right:1px solid #000;">
              <b>SKU:</b> {{ $sku ?? '' }}
            </td>
            <td class="p4" style="border:none; width:33%;">
              <b>Fecha de salida:</b> {{ $fecha_salida ?? '' }}
            </td>
          </tr>
        </table>
      </td>
    </tr>

    @php $um_val = strtolower((string)($um ?? '')); @endphp
    <tr>
      <td class="p4" colspan="4" style="padding:0;">
        <table style="width:100%; border-collapse:collapse;">
          <tr>
            <td class="p4" style="border:none; width:50%; border-right:1px solid #000;">
              <b>Cantidad:</b> {{ $cantidad ?? '' }}
            </td>
            <td class="p4" style="border:none; width:50%;">
              <b>UM:</b>
              g <span class="cb">{{ $um_val==='g' ? '☑' : '☐' }}</span> &nbsp;
              kg <span class="cb">{{ $um_val==='kg' ? '☑' : '☐' }}</span> &nbsp;
              ml <span class="cb">{{ $um_val==='ml' ? '☑' : '☐' }}</span> &nbsp;
              l <span class="cb">{{ $um_val==='l' ? '☑' : '☐' }}</span>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="4" style="min-height:70px; vertical-align:top;">
        <b>Descripción:</b> {{ $descripcion ?? '' }}
      </td>
    </tr>
  </table>

  {{-- ================================ DATOS DE SALIDA ================================ --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c">
      <td class="p4" colspan="4">DATOS DE SALIDA</td>
    </tr>

    <tr>
      @php
        $motivo = strtolower((string)($motivo_salida ?? '')); 
        $motivo_otro_txt = $motivo_otro ?? '';
      @endphp
      <td class="p4 nowrap"><b>Motivo de salida:</b></td>
      <td class="p4" colspan="3">
        Muestra a cliente <span class="cb">{{ $motivo==='cliente' ? '☑' : '☐' }}</span> &nbsp;
        Análisis de laboratorio <span class="cb">{{ $motivo==='analisis' ? '☑' : '☐' }}</span> &nbsp;
        Pruebas desarrollo <span class="cb">{{ $motivo==='desarrollo' ? '☑' : '☐' }}</span> &nbsp;
        Producto caducado <span class="cb">{{ $motivo==='caducado' ? '☑' : '☐' }}</span> &nbsp;
        Exposición <span class="cb">{{ $motivo==='exposicion' ? '☑' : '☐' }}</span> &nbsp;
        Otro <span class="cb">{{ $motivo==='otro' ? '☑' : '☐' }}</span>
        @if($motivo==='otro' && !empty($motivo_otro_txt))
          &nbsp;&nbsp;<b>Especifique:</b> {{ $motivo_otro_txt }}
        @endif
      </td>
    </tr>

    <tr>
      @php
        $ent_paq    = !empty($entrega_paqueteria);
        $ent_planta = !empty($entrega_recoleccion_planta);
        $ent_emp    = !empty($entrega_personal_empresa);
        $ent_otro   = !empty($entrega_otro);
        $ent_otro_txt = $entrega_otro_txt ?? '';
      @endphp
      <td class="p4 nowrap"><b>Tipo de entrega:</b></td>
      <td class="p4" colspan="3">
        Paquetería <span class="cb">{{ $ent_paq ? '☑' : '☐' }}</span> &nbsp;
        Recolección en planta <span class="cb">{{ $ent_planta ? '☑' : '☐' }}</span> &nbsp;
        Entrega por personal de la empresa <span class="cb">{{ $ent_emp ? '☑' : '☐' }}</span> &nbsp;
        Otro <span class="cb">{{ $ent_otro ? '☑' : '☐' }}</span>
        @if($ent_otro && $ent_otro_txt)
          &nbsp;&nbsp;<b>Especifique:</b> {{ $ent_otro_txt }}
        @endif
      </td>
    </tr>
  </table>

  {{-- ======================= DATOS DE PAQUETERÍA (si aplica) ======================= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c">
      <td class="p4" colspan="4">DATOS DE PAQUETERÍA (si aplica)</td>
    </tr>

    <tr>
      <td class="p4" colspan="2" style="border-right:1px solid #000;">
        <b>Empresa paquetería:</b> {{ $paq_empresa ?? '' }}
      </td>

      <td class="p4" colspan="2" style="border-left:none;">
        <b>Guía:</b> {{ $paq_guia ?? '' }}
      </td>
    </tr>
  </table>

  {{-- ======================= DATOS DE DESTINATARIO (si aplica) ======================= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c">
      <td class="p4" colspan="4">DATOS DE DESTINATARIO (si aplica)</td>
    </tr>

    <tr>
      <td class="p4" colspan="4" style="vertical-align:top;">
        <b>Nombre:</b> {{ $dest_nombre ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="4" style="vertical-align:top;">
        <b>Dirección:</b> {{ $dest_direccion ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="4" style="vertical-align:top;">
        <b>Nombre de quien recibe:</b> {{ $dest_recibe ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="4" style="vertical-align:top;">
        <b>Correo electrónico:</b> {{ $dest_correo ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="4" style="vertical-align:top;">
        <b>Teléfono:</b> {{ $dest_telefono ?? '' }}
      </td>
    </tr>

    @php
      $dcc = !empty($docs_cc);
      $dft = !empty($docs_ft);
      $dhs = !empty($docs_hs);
      $dot = !empty($docs_otro);
      $dot_txt = $docs_otro_txt ?? '';
    @endphp
    <tr>
      <td class="p4" colspan="4" style="vertical-align:top;">
        <b>Documentación anexa:</b>
        &nbsp;CC <span class="cb">{{ $dcc ? '☑' : '☐' }}</span>
        &nbsp;&nbsp;FT <span class="cb">{{ $dft ? '☑' : '☐' }}</span>
        &nbsp;&nbsp;HS <span class="cb">{{ $dhs ? '☑' : '☐' }}</span>
        &nbsp;&nbsp;Otro <span class="cb">{{ $dot ? '☑' : '☐' }}</span>
        @if($dot && $dot_txt)
          &nbsp;&nbsp;<b>Especifique:</b> {{ $dot_txt }}
        @endif
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="4" style="vertical-align:top; padding:8px;">
        <b>Observaciones:</b>
        <div style="margin-top:4px; white-space:pre-wrap; word-wrap:break-word;">
          {{ $observaciones ?? '' }}
        </div>
      </td>
    </tr>
  </table>

  {{-- ==================================== FIRMAS ==================================== --}}
  <table class="tbl" style="margin-top:36px; font-size:10pt;">
    <tr>
      <td style="width:33%; padding:0 14px; text-align:center;">
        <div class="t9" style="margin-bottom:4px;">
          <b>{{ $solicitante_nombre ?? '' }}</b>
        </div>
        <div style="border-top:1px solid #000; height:0; margin-top:28px;"></div>
        <div class="t9" style="margin-top:6px;">
          Nombre del solicitante
        </div>
      </td>

      <td style="width:33%; padding:0 14px; text-align:center;">
        <div class="t9" style="margin-bottom:4px;">
          <b>{{ $recolector_nombre ?? '' }}</b>
        </div>
        <div style="border-top:1px solid #000; height:0; margin-top:28px;"></div>
        <div class="t9" style="margin-top:6px;">
          Nombre y firma de recolector
        </div>
      </td>

      <td style="width:34%; padding:0 14px; text-align:center;">
        <div class="t9" style="margin-bottom:4px;">
          <b>{{ $autoriza_nombre ?? '' }}</b>
        </div>
        <div style="border-top:1px solid #000; height:0; margin-top:28px;"></div>
        <div class="t9" style="margin-top:6px;">
          Nombre y firma de quien autoriza salida
        </div>
      </td>
    </tr>
  </table>

</main>
</body>
</html>
