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
    .tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
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

  {{-- ============================ DATOS DE LA MUESTRA ============================ --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <colgroup>
      <col> <col> <col> <col>
    </colgroup>

    <tr class="th-green c">
      <td class="p4" colspan="4">DATOS DE LA MUESTRA</td>
    </tr>

    <tr>
      <td class="p4" colspan="4"><b>Fecha solicitud:</b> {{ $fecha_solicitud ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="2"><b>Producto:</b> {{ $producto ?? '' }}</td>
      <td class="p4" colspan="2"><b>SKU:</b> {{ $sku ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="2"><b>Cantidad:</b> {{ $cantidad ?? '' }}</td>
      <td class="p4" colspan="2"><b>UM:</b> {{ $um ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="4">
        <b>Presentación:</b>
        <span class="nowrap">Bolsa ziploc <span class="cb">{{ !empty($pres_ziploc) ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
        <span class="nowrap">Bolsa whirlpak <span class="cb">{{ !empty($pres_whirlpak) ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
        <span class="nowrap">Bolsa metalizada <span class="cb">{{ !empty($pres_metalizada) ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
        <span class="nowrap">Frasco <span class="cb">{{ !empty($pres_frasco) ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
        <span class="nowrap">Bidón <span class="cb">{{ !empty($pres_bidon) ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
        <span class="nowrap">Otro <span class="cb">{{ !empty($pres_otro) ? '☑' : '☐' }}</span></span>
        @if(!empty($pres_otro) && !empty($pres_otro_txt))
          &nbsp;<b>Especifique:</b> {{ $pres_otro_txt }}
        @endif
      </td>
    </tr>

    <tr>
      <td class="p4" colspan="2"><b>Lote almacén:</b> {{ $lote_almacen ?? '' }}</td>
      <td class="p4" colspan="2"><b>Lote venta:</b> {{ $lote_venta ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="2"><b>Fecha de recolección:</b> {{ $fecha_recoleccion ?? '' }}</td>
      <td class="p4" colspan="2">
        <b>Documentación solicitada:</b>
        <span class="nowrap">CC <span class="cb">{{ !empty($docs_cc) ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
        <span class="nowrap">FT <span class="cb">{{ !empty($docs_ft) ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
        <span class="nowrap">HS <span class="cb">{{ !empty($docs_hs) ? '☑' : '☐' }}</span></span>&nbsp;&nbsp;
        <span class="nowrap">Otro <span class="cb">{{ !empty($docs_otro) ? '☑' : '☐' }}</span></span>
        @if(!empty($docs_otro) && !empty($docs_otro_txt))
          &nbsp;&nbsp;<span class="nowrap"><b>Especifique:</b> {{ $docs_otro_txt }}</span>
        @endif
      </td>
    </tr>
  </table>

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
