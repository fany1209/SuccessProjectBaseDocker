<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Orden de Compra</title>
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

    header { position: fixed; top: -120px; left: 0; right: 0; height: 110px; z-index: 10; }
    footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 60px; font-size: 9pt; z-index: 10; }
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
          ORDEN DE COMPRA
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
                30-Enero-2023
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
          <b>Código:</b><br>SSS-FOR-COM-01
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
      </td>
    </tr>
  </table>
</header>

<main>
<div style="margin-top:10px; text-align:right;">
    <span style="display:inline-block; border:1px solid #000; padding:6px 10px; font-weight:bold;">
        ORDEN DE COMPRA: {{ $cc_code ?? '—' }}
    </span>
</div>

 <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr class="th-green c">
        <td class="p4" colspan="6">DATOS DEL PROVEEDOR</td>
    </tr>

    <tr>
        <td class="p4" colspan="6"><b>Proveedor:</b> {{ $supplier_name ?? '' }}</td>
    </tr>

    <tr>
        <td class="p4" colspan="2" style="width:33%;"><b>RFC:</b> {{ $rfc ?? '' }}</td>
        <td class="p4" colspan="2" style="width:34%;"><b>Contacto:</b> {{ $contact ?? '' }}</td>
        <td class="p4" colspan="2" style="width:33%;"><b>Teléfono:</b> {{ $phone ?? '' }}</td>
    </tr>

    <tr>
        <td class="p4" colspan="3"><b>Correo:</b> {{ $email ?? '' }}</td>
        <td class="p4" colspan="3"><b>Forma de pago:</b> {{ $method_payment ?? '' }}</td>
    </tr>

    <tr>
        <td class="p4" colspan="6"><b>Dirección:</b> {{ $address ?? '' }}</td>
    </tr>

    <tr>
        <td class="p4" colspan="3"><b>Método de pago:</b> {{ $payment_method ?? '' }}</td>
        <td class="p4" colspan="3"><b>Uso de CFDI:</b> {{ $cfdi ?? '' }}</td>
    </tr>
 </table>

 <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
  <tr class="th-green c">
    <td class="p4" colspan="6">DATOS DE LA SOLICITUD</td>
  </tr>

    <tr>
    <td class="p4" colspan="6" style="text-align:center;">
        SUCCESS SUMINISTROS SUSTENTABLES, S.A. DE C.V.
    </td>
    </tr>

      <tr>
        <td class="p4" colspan="6" style="text-align:center;">
            Emiliano Zapata #7 Col. Rancho Nuevo CP 38197 Apaseo el Grande, Gto.
        </td>
    </tr>

    <tr>
        <td class="p4" colspan="3"><b>Fecha:</b> {{ $application_date ?? '' }}</td>
        <td class="p4" colspan="3"><b>Cotización:</b> {{ $price ?? '' }}</td>
    </tr>

    <tr>
        <td class="p4" colspan="3">
        <b>RFC:</b> SSS2011308Z4
        </td>
        <td class="p4" colspan="3">
        <b>Solicitante:</b> {{ $solicitante ?? '' }}
        </td>
    </tr>

    <tr>
        <td class="p4" colspan="3"><b>Tiempo de entrega:</b> {{ $delivery_time ?? '' }}</td>
        <td class="p4" colspan="3"><b>Fecha de entrega:</b> {{ $delivery_date ?? '' }}</td>
    </tr>

    <tr>
        <td class="p4" colspan="2">
        <b>Teléfono:</b>461 616 9975 / 461 156 8547 
        </td>
        <td class="p4" colspan="4">
        <b>Correo:</b> contab.suministros@gmail.com, mramirez@suministrossustentables.com
        </td>
    </tr>
 </table>

 <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c" style="font-weight:bold;">
        <td class="p4" style="width:8%;">No.</td>
        <td class="p4">Descripción</td>
        <td class="p4" style="width:14%;">Cantidad</td>
        <td class="p4" style="width:18%;">Precio Unit.</td>
        <td class="p4" style="width:18%;">Importe</td>
    </tr>

    @php $i=1; @endphp
    @forelse(($items ?? []) as $row)
        <tr>
        <td class="p4 c">{{ $i++ }}</td>
        <td class="p4">{{ $row['description'] ?? '' }}</td>
        <td class="p4 r">{{ number_format((float)($row['quantity'] ?? 0), 2) }}</td>
        <td class="p4 r">{{ number_format((float)($row['unit_price'] ?? 0), 2) }}</td>
        <td class="p4 r">{{ number_format( ((float)($row['quantity'] ?? 0) * (float)($row['unit_price'] ?? 0)), 2) }}
</td>
        </tr>
    @empty
        <tr>
        <td class="p6 c" colspan="5" style="height:80px;">Sin partidas</td>
        </tr>
    @endforelse

    <tr>
        <td class="p4 r" colspan="4"><b>Subtotal</b></td>
        <td class="p4 r">{{ number_format((float)($subtotal ?? 0), 2) }}</td>
    </tr>
    <tr>
        <td class="p4 r" colspan="4"><b>IVA 16%</b></td>
        <td class="p4 r">{{ number_format((float)($iva ?? 0), 2) }}</td>
    </tr>
    <tr>
        <td class="p4 r" colspan="4"><b>Total {{ $moneda ?? 'MXN' }}</b></td>
        <td class="p4 r"><b>{{ number_format((float)($total ?? 0), 2) }}</b></td>
    </tr>
 </table>

 <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr class="th-green c">
        <td class="p4" style="font-weight:bold;">NÚMERO DE GUÍA</td>
    </tr>
    <tr>
        <td class="p6" style="height:36px; vertical-align:middle;">
            {{ $guia ?? 'NA' }}
        </td>
    </tr>
 </table>

</main>
</body>
</html>
