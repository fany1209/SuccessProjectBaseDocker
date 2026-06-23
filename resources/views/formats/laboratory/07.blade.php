<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Instructivo Muestreo</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color:#000; }
    .tbl{ width:100%; border-collapse:collapse; table-layout:fixed; }
    .b1 td,.b1 th{ border:1px solid #000; }
    td,th{ vertical-align:top; word-wrap:break-word; }
    .p2{padding:2px}.p4{padding:4px}.p6{padding:6px}
    .c{text-align:center}.l{text-align:left}.r{text-align:right}
    .t12{font-size:12pt;font-weight:bold}
    .t9{font-size:9pt}
    .th-green{background:#92D050;font-weight:bold; color:#000;}
    .muted{font-size:9pt;color:#111}
    .indent{margin-left:18px}
    .mb4{margin-bottom:4px}
    .mb8{margin-bottom:8px}
    .pb{page-break-before:always}

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
          INSTRUCTIVO MUESTREO
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
              27-Septiembre-2024
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
          <b>Código:</b><br>SSS-FOR-LID-07
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 3 }}
        </div>
      </td>
    </tr>
  </table>
</header>
<main>

  {{-- ====================== OBJETIVO ====================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr class="th-green"><td class="p4"><b>Objetivo</b></td></tr>
    <tr>
      <td class="p6">
        1. Garantizar que cualquier materia prima que ingrese a SUCCESS cumpla con los estándares de calidad requeridos antes de ser utilizada en producción.<br>
        2. Garantizar que la cantidad de muestra sea suficiente en cualquier paso del proceso de inspección interna y/o externa.<br>
        <div class="mb4"></div>
        Las siguientes recomendaciones para el muestreo de materia prima y/o producto terminado se basan en normas internacionales y buenas prácticas industriales de control de calidad, que proporcionan un método técnico y científico para realizar un muestreo eficaz y representativo.
      </td>
    </tr>
  </table>

  {{-- ====================== TABLA DE RECOMENDACIONES ====================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr class="th-green c">
      <td class="p4" style="width:26%;">Carga de materia prima / producto terminado (ton)</td>
      <td class="p4" style="width:18%;">Rango de muestreo</td>
      <td class="p4" style="width:22%;">Total en almacén de laboratorio</td>
      <td class="p4" style="width:17%;">Peso mínimo (g)</td>
      <td class="p4" style="width:17%;">Peso máximo (g)</td>
    </tr>
    @php $rec = $tabla_recomendaciones ?? []; @endphp
    @forelse($rec as $r)
      <tr class="c">
        <td class="p4">{{ $r['carga'] ?? '' }}</td>
        <td class="p4">{{ $r['rango'] ?? '' }}</td>
        <td class="p4">{{ $r['total'] ?? '' }}</td>
        <td class="p4">{{ $r['min'] ?? '' }}</td>
        <td class="p4">{{ $r['max'] ?? '' }}</td>
      </tr>
    @empty
      <tr><td class="p4 c" colspan="5">Sin datos.</td></tr>
    @endforelse
  </table>

  <div class="muted" style="margin-top:6px;">Para ello:</div>

  {{-- ====================== 1. TOMA DE MUESTRA PECUARIA ====================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green"><td class="p4"><b>1. Toma de muestra pecuaria</b></td></tr>
    <tr>
      <td class="p6">
        <b>Muestra de retención:</b> La muestra se retiene en el almacén interno del laboratorio para necesidad futura de verificación. De ser necesario, se realiza el proceso de análisis interno y externo. Se recomienda extraer de manera general un 0.005 a 0.01% de la carga total recibida con parcelación dependiente de la presentación del empacado, en bolsa estéril tipo <i>whirlpak</i>.<br>
        <span class="indent">• Ejemplo: si se recibe una carga de 25 toneladas, entonces se decide tomar muestra de entre 1.25 y 5 kg.</span>
      </td>
    </tr>
  </table>

  {{-- ====================== 2. TÉCNICA DE MUESTREO ====================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr class="th-green"><td class="p4"><b>2. Técnica de muestreo</b></td></tr>
    <tr>
      <td class="p6">
        <b>Proceso de muestreo de producto a granel</b>
        <div class="indent">
          a) Utiliza una sonda de muestreo diseñada para productos en polvo o granulares para tomar <b>muestras de 500 g</b> (según el diagrama anexo) en bolsa estéril tipo <i>whirlpak</i>.<br>
          b) Inserta la sonda en diferentes puntos de la carga (arriba, medio y abajo) para asegurar que la muestra sea representativa de todo el lote; si es necesario asegúrate de retirar un poco de material en polvo de la superficie.<br>
          c) Mezcla bien la muestra obtenida antes de dividirla en la muestra para laboratorio y la muestra de retención.
        </div>

        <div class="mb8"></div>

        <b>Proceso de muestreo de producto en Big Bag</b>
        <div class="indent">
          a) Seleccionar aleatoriamente el 25% de los sacos (por ejemplo; se reciben 6 big bag; entonces, se muestrean 1 a 2 sacos).<br>
          b) Utiliza una sonda de muestreo diseñada para productos en polvo o granulares para <b>tomar dos muestras por big bag</b> (según el diagrama anexo) en bolsa estéril tipo <i>whirlpak</i>.
        </div>
      </td>
    </tr>
  </table>

  <div class="pb"></div>

  {{-- ====================== 2 (CONT.). PRODUCTO EN SACOS ====================== --}}
  <table class="tbl b1" style="font-size:10pt;">
    <tr class="th-green"><td class="p4"><b>Proceso de muestreo de producto en sacos (entaramados)</b></td></tr>
    <tr>
      <td class="p6">
        <div class="indent">
          a) Seleccionar aleatoriamente el <b>35%</b> de las tarimas recibidas (por ejemplo; se reciben 13 tarimas; entonces, se muestrean 5 a 6 tarimas).<br>
          b) Utiliza una sonda de muestreo diseñada para productos en polvo o granulares para <b>tomar muestra de 250 g por saco</b> (según el diagrama anexo).<br>
          c) Inserta la sonda en diferentes puntos del saco (arriba, medio y abajo) para asegurar que la muestra sea representativa de todo el lote; si es necesario asegúrate de retirar un poco de material en polvo de la superficie.<br>
          d) Mezcla bien la muestra obtenida antes de dividirla en la muestra para laboratorio y la muestra de retención.
        </div>
      </td>
    </tr>
  </table>

  {{-- ====================== 3. ALMACENAMIENTO ====================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr class="th-green"><td class="p4"><b>3. Almacenamiento</b></td></tr>
    <tr>
      <td class="p6">
        <b>Etiqueta:</b> La muestra debe ser etiquetada con
        <div class="indent">
          a) Número de lote (Responsable: Almacén)<br>
          b) Fecha de recepción (Responsable: Laboratorio)<br>
          c) Número de big bag (Responsable: Calidad)
        </div>
        <div class="mb4"></div>
        <b>Condiciones de almacenamiento:</b> Las muestras deben almacenarse en condiciones controladas (temperatura y humedad) para evitar cualquier alteración.
      </td>
    </tr>
  </table>

  {{-- ====================== 4. REGISTRO ====================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr class="th-green"><td class="p4"><b>4. Registro</b></td></tr>
    <tr>
      <td class="p6">
        Documenta el proceso de muestreo, incluyendo el método utilizado, los puntos de muestreo, y la cantidad tomada (Responsable: Almacén, Laboratorio y Calidad).<br>
        Registra cualquier observación relevante del estado del big bag al momento del muestreo como la integridad del envase (Responsable: Calidad).
      </td>
    </tr>
  </table>

  {{-- ====================== 5. FINALIZACIÓN ====================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr class="th-green"><td class="p4"><b>5. Finalización</b></td></tr>
    <tr>
      <td class="p6">
        Una vez realizado el muestreo y enviado al laboratorio, el big bag puede ser aceptado en el almacén como materia prima pendiente de los resultados del análisis de calidad. Si los resultados son conformes, se da la entrada definitiva; si no lo son, se toman las acciones correctivas necesarias (Responsable: Laboratorio).
      </td>
    </tr>
  </table>

  {{-- ====================== NOTA IMPORTANTE ====================== --}}
  <div class="muted" style="margin-top:12px;">
    <b>NOTA IMPORTANTE:</b> Para las muestras agro, el proceso se repite idéntico, pero en bolsa preferentemente de cierre hermético (Ziploc).
  </div>

</main>

</body>
</html>
