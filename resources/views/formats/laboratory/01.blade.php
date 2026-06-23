<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recepción de Muestras</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color:#000; }
    .w-full { width: 100%; }
    .tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .b1 td, .b1 th { border: 1px solid #000; }
    .c { text-align: center; }
    .l { text-align: left; }
    .r { text-align: right; }
    .p4 { padding: 4px; }
    .p6 { padding: 6px; }
    .t12 { font-size: 12pt; font-weight: bold; }
    .t9  { font-size: 9pt; }
    .title { text-align:center; font-weight: bold; margin: 14px 0 8px; }
    td { word-wrap: break-word; }
    .th-green { background:#92D050; font-weight:bold; color:#000; }
    .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; } 

    header {
      position: fixed; top: -120px; left: 0; right: 0; height: 110px; z-index: 10;
    }
    footer {
      position: fixed; bottom: -60px; left: 0; right: 0; height: 60px; font-size: 9pt; z-index: 10;
    }
    main { margin-top: 0; }
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
          RECEPCIÓN DE MUESTRAS
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
                08-Julio-2024
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
          <b>Código:</b><br>SSS-FOR-LID-01
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
      </td>
    </tr>
  </table>
</header>

<main>

  {{-- =============== FOLIO =============== --}}
  @if(!empty($folio_muestra))
    <div style="width:100%; text-align:right; margin-top:6px;">
      <span style="display:inline-block; border:1px solid #000; padding:6px 10px; font-weight:bold;">
        FOLIO MUESTRA: {{ $folio_muestra }}
      </span>
    </div>
  @endif

  {{-- ======================= INFORMACIÓN DE LA MUESTRA ======================= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px; table-layout:auto;">
    <tr class="th-green c">
      <td class="p4" colspan="4">INFORMACIÓN DE LA MUESTRA</td>
    </tr>

    <tr>
      <td class="p4" style="width:18%; white-space:nowrap;"><b>Producto:</b></td>
      <td class="p4" colspan="3">{{ $producto ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" style="width:18%; white-space:nowrap;"><b>Nombre comercial:</b></td>
      <td class="p4" colspan="3">{{ $nombre_comercial ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" style="white-space:nowrap;"><b>LOTE:</b></td>
      <td class="p4" style="white-space:nowrap;">{{ $batch ?? '' }}</td>

      <td class="p4" colspan="2" style="white-space:nowrap; border-left:none; padding-left:6px;">
        <b>SKU:</b> {{ $sku ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4" style="white-space:nowrap;"><b>Fecha de entrada:</b></td>
      <td class="p4" style="white-space:nowrap;">{{ $fecha_entrada ?? '' }}</td>

      <td class="p4" colspan="2" style="white-space:nowrap; border-left:none; padding-left:6px;">
        <b>Fecha de caducidad:</b> {{ $fecha_caducidad ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4"><b>Descripción:</b></td>
      <td class="p4" colspan="3">{{ $descripcion ?? '' }}</td>
    </tr>

    <tr>
      @php
        $origen = strtolower((string)($origen_muestra ?? ''));
        $otroTxt = $origen_otro ?? '';
        function chk($v,$t){ return $v===$t ? '☑' : '☐'; }
      @endphp
      <td class="p4"><b>Origen de la muestra:</b></td>
      <td class="p4" colspan="3">
        Proveedor <span class="cb">{{ chk($origen,'proveedor') }}</span> &nbsp;&nbsp;
        Producción <span class="cb">{{ chk($origen,'produccion') }}</span> &nbsp;&nbsp;
        Almacén <span class="cb">{{ chk($origen,'almacen') }}</span> &nbsp;&nbsp;
        Otro <span class="cb">{{ chk($origen,'otro') }}</span>
        @if($origen==='otro' && $otroTxt)
          &nbsp;&nbsp;<b>Especifique:</b> {{ $otroTxt }}
        @endif
      </td>
    </tr>
  </table>

  {{-- ========================== DATOS DE RECEPCIÓN ========================== --}}
 <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c">
      <td class="p4" colspan="6">DATOS DE RECEPCIÓN</td>
    </tr>

    <tr>
      @php
        // Convertimos a minúsculas para asegurar la comparación
        $objetivoStr = strtolower((string)($objetivo_muestra ?? ''));
        $objOtro = $objetivo_otro ?? '';
        
        // Función auxiliar interna para checar si la opción existe en la cadena
        // Usamos str_contains para ver si el valor está dentro del string de la BD
      @endphp
      <td class="p4" style="width:20%;"><b>Objetivo de la muestra:</b></td>
      <td class="p4" colspan="5">
        Inspección <span class="cb">{{ str_contains($objetivoStr, 'inspeccion') ? '☑' : '☐' }}</span> &nbsp;
        Retención <span class="cb">{{ str_contains($objetivoStr, 'retencion') ? '☑' : '☐' }}</span> &nbsp;
        Análisis <span class="cb">{{ str_contains($objetivoStr, 'analisis') ? '☑' : '☐' }}</span> &nbsp;
        Desarrollo <span class="cb">{{ str_contains($objetivoStr, 'desarrollo') ? '☑' : '☐' }}</span> &nbsp;
        Exposición <span class="cb">{{ str_contains($objetivoStr, 'exposicion') ? '☑' : '☐' }}</span> &nbsp;
        Otro <span class="cb">{{ str_contains($objetivoStr, 'otro') ? '☑' : '☐' }}</span>
        
        @if(str_contains($objetivoStr, 'otro') && $objOtro)
          &nbsp;&nbsp;<b>Especifique:</b> {{ $objOtro }}
        @endif
      </td>
    </tr>

    <tr>
      <td class="p4"><b>Cantidad:</b></td>
      <td class="p4" style="width:18%;">{{ $cantidad ?? '' }}</td>

      @php
        $um_val = strtolower((string)($um ?? ''));
        $um_otro_txt = $um_otro ?? '';
      @endphp
      <td class="p4" style="width:10%; border-right:none;"><b>UM:</b></td>
      <td class="p4" colspan="3" style="width:32%; border-left:none;">
        g <span class="cb">{{ $um_val==='g' ? '☑' : '☐' }}</span> &nbsp;
        kg <span class="cb">{{ $um_val==='kg' ? '☑' : '☐' }}</span> &nbsp;
        l <span class="cb">{{ $um_val==='l' ? '☑' : '☐' }}</span> &nbsp;
        ml <span class="cb">{{ $um_val==='ml' ? '☑' : '☐' }}</span> &nbsp;
        Otro <span class="cb">{{ $um_val==='otro' ? '☑' : '☐' }}</span>
        @if($um_val==='otro' && $um_otro_txt)
          &nbsp;<b>Especifique:</b> {{ $um_otro_txt }}
        @endif
      </td>
    </tr>

    <tr>
      <td class="p4"><b>Cantidad:</b></td>
      <td class="p4" style="width:18%;">{{ $cantidad ?? '' }}</td>

      @php
        $um_val = strtolower((string)($um ?? ''));
        $um_otro_txt = $um_otro ?? '';
      @endphp
      <td class="p4" style="width:10%; border-right:none;"><b>UM:</b></td>
      <td class="p4" colspan="3" style="width:32%; border-left:none;">
        g <span class="cb">{{ $um_val==='g' ? '☑' : '☐' }}</span> &nbsp;
        kg <span class="cb">{{ $um_val==='kg' ? '☑' : '☐' }}</span> &nbsp;
        l <span class="cb">{{ $um_val==='l' ? '☑' : '☐' }}</span> &nbsp;
        ml <span class="cb">{{ $um_val==='ml' ? '☑' : '☐' }}</span> &nbsp;
        Otro <span class="cb">{{ $um_val==='otro' ? '☑' : '☐' }}</span>
        @if($um_val==='otro' && $um_otro_txt)
          &nbsp;<b>Especifique:</b> {{ $um_otro_txt }}
        @endif
      </td>
    </tr>

    <tr>
      <td class="p4" style="width:12%;"><b>Proveedor:</b></td>
      <td class="p4" colspan="5">{{ $proveedor ?? '' }}</td>
    </tr>

    <tr>
      @php
        $doc_ccf = !empty($docs_ccf);
        $doc_ft  = !empty($docs_ft);
        $doc_hs  = !empty($docs_hs);
        $doc_otro = !empty($docs_otro);
        $doc_otro_txt = $docs_otro_txt ?? '';
      @endphp
      <td class="p4"><b>Documentación anexa:</b></td>
      <td class="p4" colspan="5">
        CCF <span class="cb">{{ $doc_ccf ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        FT <span class="cb">{{ $doc_ft ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        HS <span class="cb">{{ $doc_hs ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Otro <span class="cb">{{ $doc_otro ? '☑' : '☐' }}</span>
        @if($doc_otro && $doc_otro_txt)
          &nbsp;&nbsp;<b>Especifique:</b> {{ $doc_otro_txt }}
        @endif
      </td>
    </tr>

    <tr>
      <td class="p4"><b>Observaciones calidad:</b></td>
      <td class="p4" colspan="5" style="height:70px; vertical-align:top;">
        {{ $observaciones ?? '' }}
      </td>
    </tr>
  </table>

  {{-- ======================= OBSERVACIONES LABORATORIO ======================= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr class="th-green c">
      <td class="p4" style="font-weight:bold;">OBSERVACIONES LABORATORIO</td>
    </tr>
    <tr>
      <td class="p6" style="min-height:90px; vertical-align:top;">
        {{ $observaciones_laboratorio ?? '' }}
      </td>
    </tr>
  </table>

  {{-- ============================ FIRMAS ============================ --}}
  <table class="tbl" style="margin-top:36px; font-size:10pt;">
    <tr>
      <td style="width:50%; padding:0 14px;">
        @if(!empty($firma_entrega_nombre))
          <div class="c" style="font-weight:bold; margin-bottom:4px;">
            {{ $firma_entrega_nombre }}
          </div>
        @else
          <div style="height:14px;"></div>
        @endif

        <div style="border-top:1px solid #000; height:0; margin-top:6px;"></div>
        <div class="c t9" style="margin-top:6px;">
          Nombre y firma de quien entrega en laboratorio
        </div>
      </td>

      <td style="width:50%; padding:0 14px;">
        @if(!empty($firma_recepcion_nombre))
          <div class="c" style="font-weight:bold; margin-bottom:4px;">
            {{ $firma_recepcion_nombre }}
          </div>
        @else
          <div style="height:14px;"></div>
        @endif

        <div style="border-top:1px solid #000; height:0; margin-top:6px;"></div>
        <div class="c t9" style="margin-top:6px;">
          Nombre y firma de recepción en laboratorio
        </div>
      </td>
    </tr>
  </table>

</main>
</body>
</html>
