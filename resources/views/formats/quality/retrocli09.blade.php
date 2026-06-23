<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title> REGISTRO DE RETROALIMENTACIÓN DEL CLIENTE</title>
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
    .t9  { font-size: 9pt; }
    .title { text-align:center; font-weight: bold; margin: 10px 0 6px; }
    td { word-wrap: break-word; }
    .th-green {
        background:#92D050;
        font-weight:bold;
        color:#000;
    }
    .ph-img { height: 50px; border: 1px solid #000; margin: 2px 0; }
    .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
</style>
 <style>
    @page {
      margin: 140px 24px 24px 24px; 
    }
    header {
      position: fixed;
      top: -120px;    
      left: 0; right: 0;
      height: 110px;  
    }
    main { margin-top: 0; } 
  </style>
</head>
<body>

<!-- ================= ENCABEZADO ================= -->
<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
    <tr>
      <td style="width:22%; text-align:center; border:1px solid #000;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>
      <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
        <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
          REGISTRO DE RETROALIMENTACIÓN DEL CLIENTE
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
                30-Enero-2023
            </td>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de actualización:</b><br>
              --
            </td>
            <td style="width:24%; padding:4px; text-align:center;">
              <b>Versión:</b><br>
              00
            </td>
          </tr>
        </table>
      </td>

      <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>
            SSS-FOR-CAL-09
        </div>
         <div style="padding:10px; text-align:center;">&nbsp;</div>
      </td>
    </tr>
  </table>
</header>

<table class="tbl" style="margin-top:15px;">
  <tr>
    <td style="border:none; text-align:right; font-size:10pt;">
      Fecha: <span style="display:inline-block; min-width:160px; border-bottom:1px solid #000;">{{ $fecha ?? '' }}</span>
    </td>
  </tr>
</table>

<table class="tbl" style="margin-top:15px;">
  <tr>
    <td style="border:none; font-size:10pt; text-align:justify;">
      Este formato asegura que toda la información relevante se recoja y se pueda seguir un proceso
      claro para resolver la queja o implementar la sugerencia.
    </td>
  </tr>
</table>

<!-- ====== INFORMACIÓN DEL CLIENTE ====== -->
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr>
      <th class="th-green c" colspan="2" style="padding:4px;">INFORMACION DEL CLIENTE</th>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Empresa:</b> {{ $empresa ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Nombre del contacto:</b> {{ $contacto ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" style="width:40%;"><b>Teléfono:</b> {{ $telefono ?? '' }}</td>
      <td class="p4 l" style="width:60%;"><b>Correo:</b> {{ $correo ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Dirección:</b> {{ $direccion ?? '' }}</td>
    </tr>
  </table>

<!-- ====== DETALLES DE LA NO CONFORMIDAD ====== -->
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr>
      <th class="th-green c" colspan="2" style="padding:4px;">DETALLES DE LA NO CONFORMIDAD</th>
    </tr>
    <tr>
      <td class="p4 l" style="width:60%;"><b>Fecha de la incidencia:</b> {{ $fecha_incidencia ?? '' }}</td>
      <td class="p4 l" style="width:40%;"><b>Hora de la incidencia:</b> {{ $hora_incidencia ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Producto o servicio:</b> {{ $producto_servicio ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2" style="height:120px;">
        <b>Descripción detallada:</b>
        <div style="min-height:90px;">{{ $descripcion ?? '' }}</div>
      </td>
    </tr>
    @if(!empty($evidencias_base64))
    <tr>
      <td class="p4 l" colspan="2">
        <b>Evidencias (imágenes):</b>
        <div style="margin-top:6px;">
          @foreach($evidencias_base64 as $img)
            <img src="{{ $img }}" style="height:90px; margin:4px; border:1px solid #000;">
          @endforeach
        </div>
      </td>
    </tr>
  @endif

    <!-- ====== IMPACTO ====== -->
    <tr>
      <th class="th-green c" colspan="2" style="padding:4px;">IMPACTO</th>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Cómo afectó al cliente:</b> {{ $impacto ?? '' }}</td>
    </tr>
  @php $imp = $importancia ?? ''; @endphp
  <tr>
    <td class="p4 l" colspan="2">
      <b>Importancia para el cliente:</b>
      &nbsp; Baja <span class="cb">{{ $imp === 'Baja'  ? '☑' : '☐' }}</span>
      &nbsp;&nbsp; Media <span class="cb">{{ $imp === 'Media' ? '☑' : '☐' }}</span>
      &nbsp;&nbsp; Alta <span class="cb">{{ $imp === 'Alta'  ? '☑' : '☐' }}</span>
    </td>
  </tr>


    <!-- ====== RESOLUCIÓN SOLICITADA ====== -->
    <tr>
      <th class="th-green c" colspan="2" style="padding:4px;">RESOLUCIÓN SOLICITADA</th>
    </tr>
    <tr>
      <td class="p4 l" colspan="2" style="height:90px;">
        <b>Lo que el cliente espera como solución:</b>
        <div style="min-height:70px;">{{ $resolucion ?? '' }}</div>
      </td>
    </tr>
    <!-- ====== ACCIÓN TOMADA ====== -->
    <tr>
      <th class="th-green c" colspan="2" style="padding:4px;">ACCIÓN TOMADA</th>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Medidas iniciales tomadas:</b> {{ $medidas ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Persona responsable:</b> {{ $responsable_accion ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Fecha de resolución:</b> {{ $fecha_resolucion ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Acción final:</b> {{ $accion_final ?? '' }}</td>
    </tr>

    <!-- ====== COMENTARIOS ADICIONALES ====== -->
    <tr>
      <th class="th-green c" colspan="2" style="padding:4px;">COMENTARIOS ADICIONALES</th>
    </tr>
    <tr>
      <td class="p4 l" colspan="2" style="height:90px;">
        {{ $comentarios_adicionales ?? '' }}
      </td>
    </tr>
  </table>
<!-- ====== FIRMAS ====== -->
  <table class="tbl" style="width:100%; border:none; margin-top:40px; font-size:9pt;">
    <tr>
      <td style="border:none; text-align:center; width:50%; padding:12px 8px;">
        <div style="height:48px;"></div>
        <div>________________________________________</div>
        <div style="margin-top:4px;">
          <b>Cliente:</b>
          {{ isset($cliente_firma) && $cliente_firma !== '' ? $cliente_firma : 'Nombre y firma del Cliente' }}
        </div>
      </td>
      <td style="border:none; text-align:center; width:50%; padding:12px 8px;">
        <div style="height:48px;"></div>
        <div>________________________________________</div>
        <div style="margin-top:4px;">
          <b>Receptor:</b>
          {{ isset($receptor_firma) && $receptor_firma !== '' ? $receptor_firma : 'Nombre y firma del Receptor' }}
        </div>
      </td>
    </tr>
  </table>
</body>
</html>

