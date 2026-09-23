<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cotización {{ $folio }}</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
    .w-full { width: 100%; }
    .tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .b1 td, .b1 th { border: 1px solid #000; }
    .c { text-align: center; }
    .r { text-align: right; }
    .p4 { padding: 4px; }
    .p6 { padding: 6px; }
    .t12 { font-size: 12pt; font-weight: bold; }
    .title { text-align:center; font-weight: bold; margin: 10px 0 6px; text-transform: uppercase; }
    .th-green { background:#92D050; font-weight:bold; color:#000; }
    
    header { position: fixed; top: -120px; left: 0; right: 0; height: 110px; z-index: 10; }
    footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 60px; font-size: 9pt; z-index: 10; }
    
    .folio-box { text-align: right; margin-top: 10px; font-size: 11pt; margin-bottom: 5px; }
    .folio-val { font-weight: bold; border-bottom: 1px solid #000; padding: 0 10px; min-width: 100px; display: inline-block; }
  </style>
</head>
<body>

<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed;">
    <tr>
      <td rowspan="2" style="width:22%; text-align:center; border:1px solid #000; padding:5px; vertical-align:middle;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>

      <td colspan="3" style="width:58%; text-align:center; font-weight:bold; font-size:14pt; border:1px solid #000; padding:10px; vertical-align:middle;">
        COTIZACIÓN
      </td>

      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align:middle;">
        <b>Código:</b><br>
        {{ $codigo_formato }}
      </td>
    </tr>

    <tr>
      <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; height:35px; vertical-align:middle;">
        <b>Fecha de elaboración:</b><br>
        {{ $fecha_elaboracion }}
      </td>

      <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align:middle;">
        <b>Fecha de actualización:</b>{{ $fecha_actualizacion }}
      </td>

      <td style="width:19.4%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align:middle;">
        <b>Versión:</b>
        {{ $version }}
      </td>

      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align:middle;">
        Pág. {{ $pagina }}
      </td>
    </tr>
  </table>
</header>
<main>
  <div class="folio-box">
    <b>Folio:</b> <span class="folio-val">{{ $folio }}</span>
  </div>

  <table class="tbl b1" style="font-size:10pt;">
    <tr class="th-green">
      <td colspan="2" class="p4 c">DATOS GENERALES</td>
    </tr>
    <tr>
      <td class="p4" style="width:30%;"><b>Empresa:</b></td>
      <td class="p4">{{ $empresa }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Atención:</b></td>
      <td class="p4">{{ $atencion }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Lugar y Fecha:</b></td>
      <td class="p4">{{ $fecha_texto }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Departamento:</b></td>
      <td class="p4">{{ $departamento }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Teléfono:</b></td>
      <td class="p4">{{ $phone }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Moneda:</b></td>
      <td class="p4"><span style="font-weight:bold; color: {{ ($moneda ?? 'MXN') === 'USD' ? '#0066cc' : '#000' }};">{{ ($moneda ?? 'MXN') === 'USD' ? 'Dólares Americanos (USD)' : 'Pesos Mexicanos (MXN)' }}</span></td>
    </tr>
  </table>

  <p style="font-size:10pt; margin-top:15px;">
    Estimado(a) <b>{{ $atencion }}</b>, tengo el gusto de responder a su solicitud. A continuación, encontrará cotización de producto solicitado:
  </p>

  {{-- TABLA DE PRODUCTOS CON DESGLOSE FISCAL --}}
  <table class="tbl b1" style="font-size:9pt; margin-top:10px;">
    <thead>
      <tr class="th-green c">
        <th class="p6" style="width:28%;">Producto</th>
        <th class="p6" style="width:12%;">Req. (Cant.)</th>
        <th class="p6" style="width:12%;">Unidad</th> 
        <th class="p6" style="width:18%;">Presentación</th> 
        <th class="p6" style="width:15%;">Precio Unit. ({{ $moneda ?? 'MXN' }})</th>
        <th class="p6" style="width:15%;">Subtotal ({{ $moneda ?? 'MXN' }})</th>
      </tr>
    </thead>
    <tbody>
    @php 
        $subtotalCalculado = 0; 
    @endphp
    @foreach($productos as $prod)
    <tr class="c">
        <td class="p4" style="text-align:left;">{{ $prod->quote_product_name }}</td>
        <td class="p4">{{ number_format($prod->quantity, 2) }}</td>
        <td class="p4">{{ $prod->unit ?? 'N/A' }}</td>
        <td class="p4">{{ $prod->presentation ?? 'N/A' }}</td>
        <td class="p4">$ {{ number_format($prod->cost, 2) }}</td>
        <td class="p4">$ {{ number_format($prod->quantity * $prod->cost, 2) }}</td>
    </tr>
    @php 
        $subtotalCalculado += ($prod->quantity * $prod->cost); 
    @endphp
    @endforeach
</tbody>
<tfoot>
    @php
        $subtotalFinal = 0;
        $ivaCalculado = 0;

        foreach($productos as $prod) {
            $importeFila = $prod->quantity * $prod->cost;
            $subtotalFinal += $importeFila;
            $ivaCalculado += ($importeFila * ($prod->iva ?? 0.16));
        }

        $totalFinal = $subtotalFinal + $ivaCalculado;
    @endphp

    <tr>
        <td colspan="5" class="r p4" style="font-weight:bold;">SUBTOTAL ({{ $moneda ?? 'MXN' }}):</td>
        <td class="c p4" style="font-weight:bold;">$ {{ number_format($subtotalFinal, 2) }} {{ $moneda ?? 'MXN' }}</td>
    </tr>
    <tr>
        <td colspan="5" class="r p4" style="font-weight:bold;">IVA:</td>
        <td class="c p4" style="font-weight:bold;">$ {{ number_format($ivaCalculado, 2) }} {{ $moneda ?? 'MXN' }}</td>
    </tr>
    <tr>
        <td colspan="5" class="r p4" style="font-weight:bold; font-size: 11pt;">TOTAL GENERAL ({{ $moneda ?? 'MXN' }}):</td>
        <td class="c p4" style="font-weight:bold; background:#f2f2f2; font-size: 11pt;">$ {{ number_format($totalFinal, 2) }} {{ $moneda ?? 'MXN' }}</td>
    </tr>
</tfoot>
  </table>

  <table class="tbl b1" style="font-size:10pt; margin-top:20px;">
    <tr class="th-green">
      <td colspan="2" class="p4 c">CONDICIONES COMERCIALES</td>
    </tr>
    <tr>
      <td class="p4" style="width:30%;"><b>Incoterm:</b></td>
      <td class="p4">{{ $incoterm }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Transporte:</b></td>
      <td class="p4">{{ $transporte }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Tiempo de entrega:</b></td>
      <td class="p4">{{ $tiempo_entrega }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Términos y condiciones:</b></td>
      <td class="p4">{{ $terminos }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Notas:</b></td>
      <td class="p4">{{ $notas }}</td>
    </tr>
  </table>

  <div style="margin-top:30px; font-size:10pt;">
    <p>Atentamente,<br><br><b>{{ $firma_nombre }}</b></p>
  </div>
</main>

<footer>
  <table style="width:100%; border-collapse:collapse; font-size:8pt; font-family:Arial, sans-serif; background:#f2f2f2; border-top:2px solid #92D050;">
    <tr>
      <td style="width:35%; text-align:left; vertical-align:middle; padding:4px;">
        Emiliano Zapata No. 7, Col. Rancho Nuevo<br>
        C.P. 38197, Apaseo el Grande, Gto, México
      </td>

      <td style="width:30%; text-align:center; vertical-align:middle; padding:4px;">
        <img src="{{ public_path('images/qrSuccess.png') }}" alt="QR" style="height: 45px; width: auto;">
      </td>

      <td style="width:35%; text-align:right; vertical-align:middle; padding:4px;">
        +52 461 156.8547 / +52 461 616 9975<br>
        www.suministrossustentables.com
      </td>
    </tr>
  </table>
</footer>

</body>
</html>