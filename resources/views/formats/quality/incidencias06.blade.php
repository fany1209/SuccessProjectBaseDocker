<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Incidencias — SSS-FOR-CAL-06</title>
  <style>
    /* Ajuste de márgenes del documento para dar espacio al encabezado repetible exactamente igual al otro */
    @page { 
      margin: 140px 24px 80px 24px; 
      size: A4 portrait; 
    }
    
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 10.5pt; }
    .tbl    { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .b1 td, .b1 th { border: 0.2px solid #000; }
    .c { text-align: center; }
    .p4 { padding: 4px; }
    .p6 { padding: 6px; }
    .t12 { font-size: 12pt; font-weight: bold; }
    .t9  { font-size: 9pt;  }
    .title { text-align:center; font-weight:bold; margin: 12px 0 6px; font-size: 11pt; }
    td { word-wrap: break-word; }
    .green-cell { background:#92D050; color:#000; }

    /* Estilo del encabezado fijo e idéntico en estructura al otro */
    header {
      position: fixed;
      top: -120px;    
      left: 0; right: 0;
      height: 110px;  
      z-index: 10;
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

  <header>
    <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed;">
        <tr>
            <td rowspan="2" style="width:22%; text-align:center; border:1px solid #000; padding: 5px; vertical-align: middle;">
                <img src="{{ $logo_src ?? public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
            </td>

            <td colspan="3" style="width:58%; text-align:center; font-weight:bold; font-size:13pt; border:1px solid #000; padding:10px; vertical-align: middle;">
                REPORTE DE INCIDENCIAS
            </td>

            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
                <b>Código:</b><br> SSS-FOR-CAL-06
            </td>
        </tr>

        <tr>
            <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; height: 35px; vertical-align: middle;">
                <b>Fecha de elaboración:</b><br>31-Julio-2023
            </td>
            <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
                <b>Fecha de actualización:</b><br> --
            </td>
            <td style="width:19.4%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
                <b>Versión:</b>00
            </td>
            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
                Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
            </td>
        </tr>
    </table>
  </header>

<main>
<table class="tbl b1" style="font-size:10pt; table-layout:fixed;">
  <colgroup>
    @for ($i = 0; $i < 100; $i++)
      <col style="width:1%;">
    @endfor
  </colgroup>

  <tr>
    <td colspan="100" class="c p3 green-cell" style="font-weight:bold; font-size:11pt; padding:3px; line-height:1.1;">
      INFORMACIÓN DEL PRODUCTO
    </td>
  </tr>

  <tr>
    <td colspan="54" style="padding:2px 4px; line-height:1.1;">
      <b>Folio:</b> {{ $folio ?? '' }}
    </td>
    <td colspan="46" style="padding:2px 4px; line-height:1.1;">
      <b>Fecha de recepción:</b> {{ $fecha_recepcion ?? '' }}
    </td>
  </tr>

  <tr>
    <td colspan="54" style="padding:2px 4px; line-height:1.1;">
      <b>Proveedor:</b> {{ $proveedor ?? '' }}
    </td>
    <td colspan="46" style="padding:2px 4px; line-height:1.1;">
      <b>Fecha de reporte:</b> {{ $fecha_reporte ?? '' }}
    </td>
  </tr>

  <tr>
    <td colspan="54" style="padding:2px 4px; line-height:1.1;">
      <b>Estado del producto:</b> {{ $mpptme ?? '' }}
    </td>
    <td colspan="46" style="padding:2px 4px; line-height:1.1;">
      @php
        $lt = $lote_tipo ?? null;
        $etiqueta = $lt ? ($lt === 'proveedor' ? ' (Proveedor)' : ' (Interno)') : '';
      @endphp
      <b>Lote{{ $etiqueta }}:</b> {{ $lote ?? $lote_interno ?? '' }}
    </td>
  </tr>

  <tr>
    <td colspan="54" style="padding:2px 4px; line-height:1.1;">
      <b>Producto:</b> {{ $producto ?? '' }}
    </td>
    <td colspan="46" style="padding:2px 4px; line-height:1.1;">
      <b>Cantidad Recibida:</b> {{ $remitidos ?? '' }}
    </td>
  </tr>

  <tr>
    <td colspan="54" style="padding:2px 4px; line-height:1.1;">
      <b>Fecha de incidencia:</b> {{ $fecha_incidencia ?? '' }}
    </td>
    <td colspan="46" style="padding:2px 4px; line-height:1.1;">
      <b>Incidencia:</b> {{ $incidencia ?? '' }}
    </td>
  </tr>

 {{-- ================= DESCRIPCIÓN DE LA INCIDENCIA ================= --}}
    @if(!empty($descripcion) && is_array($descripcion))
      <tr>
        <td colspan="100" class="c p3 green-cell" style="font-weight:bold; font-size:11pt; padding:3px; line-height:1.1;">
          DESCRIPCIÓN DE LA INCIDENCIA
        </td>
      </tr>

      <tr class="c">
        <th colspan="40" style="padding:4px;">Descripción</th>
        <th colspan="60" style="padding:4px;">Imágenes</th>
      </tr>

      @foreach($descripcion as $it)
        <tr style="page-break-inside: avoid;">
          <td colspan="40" class="p4" style="vertical-align: top; padding:10px 6px;">
            {{ $it['texto'] ?? '' }}
          </td>

          <td colspan="60" class="p4" style="text-align:center; vertical-align: middle; padding:10px 6px;">
            @php
              $imagenesFila = [];
              if (!empty($it['imgs']) && is_array($it['imgs'])) {
                  $imagenesFila = $it['imgs'];
              } elseif (!empty($it['img'])) {
                  $imagenesFila = [$it['img']];
              }
              
              $totalImgs = count($imagenesFila);
            @endphp

            @if($totalImgs > 0)
              <table style="width:100%; border-collapse:collapse; border:none; margin:0 auto;">
                <tr>
                  @foreach($imagenesFila as $img)
                    <td style="border:none; text-align:center; padding:2px; vertical-align:middle; width:{{ 100 / $totalImgs }}%;">
                      <img src="{{ $img }}" 
                           style="display:inline-block; margin:0 auto; height:110px; width:auto; max-width:98%; object-fit:contain; border:1px solid #ddd; border-radius:4px;" 
                           alt="Evidencia">
                    </td>
                  @endforeach
                </tr>
              </table>
            @else
              <span style="color:#999; font-size:8pt; font-style:italic;">Sin imagen</span>
            @endif
          </td>
        </tr>
      @endforeach
    @endif

  {{-- COMENTARIOS --}}
  <tr>
    <td colspan="100" class="c p3 green-cell" style="font-weight:bold; font-size:11pt; padding:3px; line-height:1.1;">
      COMENTARIOS
    </td>
  </tr>
  <tr>
    <td colspan="100" style="padding:4px 6px; text-align:left; line-height:1.3; font-size:10pt;">
      <div style="min-height:80px; page-break-inside: avoid;">
        {{ $comentarios ?? '' }}
        @if(empty($comentarios))
          &nbsp;
        @endif
      </div>
    </td>
  </tr>
</table>

{{-- ================= FIRMAS ================= --}}
<table class="tbl" style="border:none; width:100%; margin-top:30px;">
  <tr>
    <td style="border:none; padding-top:40px; width:60%;">
      <div style="width:360px; margin-left:0;">
        <div style="font-size:10pt; text-align:center; margin-bottom:4px;">
          {{ $firma_nombre ?: 'Nombre del responsable' }}
        </div>
        <div style="border-top:1px solid #000; width:100%; height:0;"></div>
        <div style="font-size:9pt; color:#555; text-align:center; margin-top:4px;">
          Depto. Calidad
        </div>
      </div>
    </td>
  </tr>
</table>
</main>

</body>
</html>