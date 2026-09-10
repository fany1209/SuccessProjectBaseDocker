<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitud de Muestras para Clientes</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color:#000; }

    .w-full { width: 100%; }
    .tbl { width: 100%; border-collapse: collapse; table-layout: fixed; page-break-inside: avoid; }
    .b1 td, .b1 th { border: 1px solid #000; }
    td, th { vertical-align: middle; word-wrap: break-word; }
    .p4 { padding: 4px; } .p6 { padding: 6px; }
    .c { text-align: center; } .l { text-align: left; } .r { text-align: right; }
    .t12 { font-size: 12pt; font-weight: bold; }
    .t9  { font-size: 9pt; }
    .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
    .muted { font-size: 9pt; color:#111; }
    .th-green { background:#92D050; font-weight:bold; color:#000; }

    .nowrap  { white-space: nowrap; }
    .no-break{ white-space: nowrap; overflow-wrap: normal; word-break: normal; }
    .vtop    { vertical-align: top; }

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
          SOLICITUD DE MUESTRAS PARA CLIENTES
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
                25-Mayo-2023
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
          <b>Código:</b><br>SSS-FOR-LID-02
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
      </td>
    </tr>
  </table>
</header>
<main>

  @if(!empty($folio))
    <div style="width:100%; text-align:right; margin-bottom: 6px;">
      <span style="display:inline-block; border:1px solid #000; padding:4px 10px; font-weight:bold; font-size:10pt;">
        FOLIO: {{ $folio }}
      </span>
    </div>
  @endif

  @php
    $itemsList = [];
    if (!empty($items) && is_array($items)) {
        $itemsList = $items;
    } elseif (!empty($producto) || !empty($sku) || !empty($cantidad)) {
        $itemsList = [[
            'producto'        => $producto ?? '',
            'sku'             => $sku ?? '',
            'cantidad'        => $cantidad ?? '',
            'um'              => $um ?? '',
            'pres_ziploc'     => $pres_ziploc ?? 0,
            'pres_whirlpak'   => $pres_whirlpak ?? 0,
            'pres_metalizada' => $pres_metalizada ?? 0,
            'pres_frasco'     => $pres_frasco ?? 0,
            'pres_bidon'      => $pres_bidon ?? 0,
            'pres_otro'       => $pres_otro ?? 0,
            'pres_otro_txt'   => $pres_otro_txt ?? '',
            'lote_almacen'    => $lote_almacen ?? '',
            'lote_venta'      => $lote_venta ?? '',
            'docs_cc'         => $docs_cc ?? 0,
            'docs_ft'         => $docs_ft ?? 0,
            'docs_hs'         => $docs_hs ?? 0,
            'docs_otro'       => $docs_otro ?? 0,
            'docs_otro_txt'   => $docs_otro_txt ?? '',
        ]];
    }
    if (empty($itemsList)) {
        $itemsList = [[]];
    }
  @endphp

  @if(count($itemsList) <= 1)
    {{-- ============================ DATOS DE LA MUESTRA (ÍTEM ÚNICO) ============================ --}}
    @php $it = $itemsList[0] ?? []; @endphp
    <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
      <colgroup>
        <col style="width:25%;"> <col style="width:25%;"> <col style="width:25%;"> <col style="width:25%;">
      </colgroup>

      <tr class="th-green c">
        <td class="p4" colspan="4">DATOS DE LA MUESTRA</td>
      </tr>

      <tr>
        <td class="p4" colspan="2"><b>Fecha solicitud:</b> {{ $fecha_solicitud ?? '' }}</td>
        <td class="p4" colspan="2"><b>Fecha de recolección:</b> {{ $fecha_recoleccion ?? '' }}</td>
      </tr>

      <tr>
        <td class="p4" colspan="2"><b>Producto:</b> {{ $it['producto'] ?? ($it['product']['name'] ?? ($producto ?? '')) }}</td>
        <td class="p4" colspan="2"><b>SKU:</b> {{ $it['sku'] ?? ($it['product']['sku'] ?? ($sku ?? '')) }}</td>
      </tr>

      <tr>
        <td class="p4" colspan="2"><b>Cantidad:</b> {{ $it['cantidad'] ?? ($cantidad ?? '') }}</td>
        <td class="p4" colspan="2"><b>UM:</b> {{ $it['um'] ?? ($um ?? '') }}</td>
      </tr>

      <tr>
        @php
          $pZiploc = !empty($it['pres_ziploc'] ?? ($pres_ziploc ?? false));
          $pWhirl  = !empty($it['pres_whirlpak'] ?? ($pres_whirlpak ?? false));
          $pMeta   = !empty($it['pres_metalizada'] ?? ($pres_metalizada ?? false));
          $pFrasco = !empty($it['pres_frasco'] ?? ($pres_frasco ?? false));
          $pBidon  = !empty($it['pres_bidon'] ?? ($pres_bidon ?? false));
          $pOtro   = !empty($it['pres_otro'] ?? ($pres_otro ?? false));
          $pOtroTxt= $it['pres_otro_txt'] ?? ($pres_otro_txt ?? '');
        @endphp
        <td class="p4" colspan="4">
          <b>Presentación:</b>
          <span class="nowrap">Bolsa ziploc <span class="cb">{{ $pZiploc ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
          <span class="nowrap">Bolsa whirlpak <span class="cb">{{ $pWhirl ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
          <span class="nowrap">Bolsa metalizada <span class="cb">{{ $pMeta ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
          <span class="nowrap">Frasco <span class="cb">{{ $pFrasco ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
          <span class="nowrap">Bidón <span class="cb">{{ $pBidon ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
          <span class="nowrap">Otro <span class="cb">{{ $pOtro ? '☑' : '☐' }}</span></span>
          @if($pOtro && !empty($pOtroTxt))
            &nbsp;<b>Especifique:</b> {{ $pOtroTxt }}
          @endif
        </td>
      </tr>

      <tr>
        <td class="p4" colspan="2"><b>Lote almacén:</b> {{ $it['lote_almacen'] ?? ($lote_almacen ?? '') }}</td>
        <td class="p4" colspan="2"><b>Lote venta:</b> {{ $it['lote_venta'] ?? ($lote_venta ?? '') }}</td>
      </tr>

      <tr>
        @php
          $dCc   = !empty($it['docs_cc'] ?? ($docs_cc ?? false));
          $dFt   = !empty($it['docs_ft'] ?? ($docs_ft ?? false));
          $dHs   = !empty($it['docs_hs'] ?? ($docs_hs ?? false));
          $dOtro = !empty($it['docs_otro'] ?? ($docs_otro ?? false));
          $dOtroTxt = $it['docs_otro_txt'] ?? ($docs_otro_txt ?? '');
        @endphp
        <td class="p4" colspan="4">
          <b>Documentación solicitada:</b>
          <span class="nowrap">CC <span class="cb">{{ $dCc ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
          <span class="nowrap">FT <span class="cb">{{ $dFt ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
          <span class="nowrap">HS <span class="cb">{{ $dHs ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
          <span class="nowrap">Otro <span class="cb">{{ $dOtro ? '☑' : '☐' }}</span></span>
          @if($dOtro && !empty($dOtroTxt))
            &nbsp;&nbsp;<span class="nowrap"><b>Especifique:</b> {{ $dOtroTxt }}</span>
          @endif
        </td>
      </tr>
    </table>
  @else
    {{-- ============================ DATOS DE LA SOLICITUD (MÚLTIPLES ÍTEMS) ============================ --}}
    <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
      <colgroup>
        <col style="width:50%;"> <col style="width:50%;">
      </colgroup>
      <tr class="th-green c">
        <td class="p4" colspan="2">DATOS DE LA SOLICITUD</td>
      </tr>
      <tr>
        <td class="p4"><b>Fecha solicitud:</b> {{ $fecha_solicitud ?? '' }}</td>
        <td class="p4"><b>Fecha de recolección:</b> {{ $fecha_recoleccion ?? '' }}</td>
      </tr>
    </table>

    @foreach($itemsList as $index => $it)
      @php
        $pZiploc = !empty($it['pres_ziploc']);
        $pWhirl  = !empty($it['pres_whirlpak']);
        $pMeta   = !empty($it['pres_metalizada']);
        $pFrasco = !empty($it['pres_frasco']);
        $pBidon  = !empty($it['pres_bidon']);
        $pOtro   = !empty($it['pres_otro']);
        $pOtroTxt= $it['pres_otro_txt'] ?? '';

        $dCc   = !empty($it['docs_cc']);
        $dFt   = !empty($it['docs_ft']);
        $dHs   = !empty($it['docs_hs']);
        $dOtro = !empty($it['docs_otro']);
        $dOtroTxt = $it['docs_otro_txt'] ?? '';
      @endphp
      <table class="tbl b1" style="font-size:9.5pt; margin-top:8px;">
        <colgroup>
          <col style="width:25%;"> <col style="width:25%;"> <col style="width:25%;"> <col style="width:25%;">
        </colgroup>

        <tr class="th-green c">
          <td class="p4" colspan="4">DATOS DE LA MUESTRA #{{ $index + 1 }}</td>
        </tr>

        <tr>
          <td class="p4" colspan="2"><b>Producto:</b> {{ $it['producto'] ?? ($it['product']['name'] ?? '') }}</td>
          <td class="p4" colspan="2"><b>SKU:</b> {{ $it['sku'] ?? ($it['product']['sku'] ?? '') }}</td>
        </tr>

        <tr>
          <td class="p4" colspan="2"><b>Cantidad:</b> {{ $it['cantidad'] ?? '' }}</td>
          <td class="p4" colspan="2"><b>UM:</b> {{ $it['um'] ?? '' }}</td>
        </tr>

        <tr>
          <td class="p4" colspan="4">
            <b>Presentación:</b>
            <span class="nowrap">Bolsa ziploc <span class="cb">{{ $pZiploc ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
            <span class="nowrap">Bolsa whirlpak <span class="cb">{{ $pWhirl ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
            <span class="nowrap">Bolsa metalizada <span class="cb">{{ $pMeta ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
            <span class="nowrap">Frasco <span class="cb">{{ $pFrasco ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
            <span class="nowrap">Bidón <span class="cb">{{ $pBidon ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
            <span class="nowrap">Otro <span class="cb">{{ $pOtro ? '☑' : '☐' }}</span></span>
            @if($pOtro && !empty($pOtroTxt))
              &nbsp;<b>Especifique:</b> {{ $pOtroTxt }}
            @endif
          </td>
        </tr>

        <tr>
          <td class="p4" colspan="2"><b>Lote almacén:</b> {{ $it['lote_almacen'] ?? '' }}</td>
          <td class="p4" colspan="2"><b>Lote venta:</b> {{ $it['lote_venta'] ?? '' }}</td>
        </tr>

        <tr>
          <td class="p4" colspan="4">
            <b>Documentación solicitada:</b>
            <span class="nowrap">CC <span class="cb">{{ $dCc ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
            <span class="nowrap">FT <span class="cb">{{ $dFt ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
            <span class="nowrap">HS <span class="cb">{{ $dHs ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
            <span class="nowrap">Otro <span class="cb">{{ $dOtro ? '☑' : '☐' }}</span></span>
            @if($dOtro && !empty($dOtroTxt))
              &nbsp;&nbsp;<span class="nowrap"><b>Especifique:</b> {{ $dOtroTxt }}</span>
            @endif
          </td>
        </tr>
      </table>
    @endforeach
  @endif

  {{-- ============================== DATOS DEL CLIENTE ============================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c">
      <td class="p4" colspan="4">DATOS DEL CLIENTE</td>
    </tr>

    <tr>
      <td class="p4" colspan="4"><b>Nombre del cliente:</b> {{ $cliente_nombre ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="4"><b>Dirección:</b> {{ $cliente_direccion ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="4"><b>Correo electrónico:</b> {{ $cliente_correo ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="4"><b>Teléfono:</b> {{ $cliente_telefono ?? '' }}</td>
    </tr>

    <tr>
      @php $estatus = strtolower((string)($cliente_estatus ?? '')); @endphp
      <td class="p4" colspan="4">
        <b>Estatus del cliente:</b>
        Nuevo <span class="cb">{{ $estatus==='nuevo' ? '☑' : '☐' }}</span>
        &nbsp;&nbsp;
        Frecuente <span class="cb">{{ $estatus==='frecuente' ? '☑' : '☐' }}</span>
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="4"><b>Personal de seguimiento a cliente (si aplica):</b> {{ $personal_seguimiento ?? '' }}</td>
    </tr>

    <tr>
      @php
        $ent_paq    = !empty($entrega_paqueteria);
        $ent_emp    = !empty($entrega_personal_empresa);
        $ent_planta = !empty($entrega_recoleccion_planta);
        $ent_otro   = !empty($entrega_otro);
        $ent_otro_txt = $entrega_otro_txt ?? '';
      @endphp
      <td class="p4" colspan="4">
        <b>Tipo de entrega:</b>
        Paquetería <span class="cb">{{ $ent_paq ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Entrega por personal de la empresa <span class="cb">{{ $ent_emp ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Recolección en planta <span class="cb">{{ $ent_planta ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Otro <span class="cb">{{ $ent_otro ? '☑' : '☐' }}</span>
        @if($ent_otro && $ent_otro_txt)
          &nbsp;&nbsp;<b>Especifique:</b> {{ $ent_otro_txt }}
        @endif
      </td>
    </tr>
  </table>

  {{-- ===================== DATOS DE PAQUETERÍA (si aplica) ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c">
      <td class="p4" colspan="4">DATOS DE PAQUETERÍA (si aplica)</td>
    </tr>

    <tr>
      <td class="p4" colspan="4"><b>Nombre de la paquetería:</b> {{ $paq_nombre ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="4"><b>N.º Guía/Rastreo:</b> {{ $paq_guia ?? '' }}</td>
    </tr>
  </table>

  {{-- =============================== OBSERVACIONES =============================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c">
      <td class="p4" style="font-weight:bold;">OBSERVACIONES</td>
    </tr>
    <tr>
      <td class="p6" style="min-height:80px; vertical-align:top;">
        {{ $observaciones ?? '' }}
      </td>
    </tr>
  </table>

{{-- ==================================== FIRMA ==================================== --}}
  <table class="tbl" style="margin-top:36px; font-size:10pt;">
    <tr>
      <td style="width:100%; padding:0 14px;">
        @php
          $solicitante = trim((string)($solicitante_nombre ?? ''));
          $sol_puesto  = trim((string)($solicitante_puesto ?? ''));
          $mt_linea    = $solicitante !== '' ? 10 : 42; // menos espacio si ya mostramos el nombre
        @endphp

        @if($solicitante !== '')
          <div class="c t9" style="font-weight:bold;">
            {{ $solicitante }}
            @if($sol_puesto !== '')
              <span class="t9" style="font-weight:normal;"> — {{ $sol_puesto }}</span>
            @endif
          </div>
        @endif

        <div style="border-top:1px solid #000; height:0; margin-top: {{ $mt_linea }}px;"></div>
        <div class="c t9" style="margin-top:6px;">
          Nombre y firma del solicitante
        </div>
      </td>
    </tr>
  </table>

</main>
</body>
</html>
