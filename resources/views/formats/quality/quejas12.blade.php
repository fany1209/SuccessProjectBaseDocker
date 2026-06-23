<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title> QUEJAS Y SUGERENCIAS</title>
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

{{-- ================= ENCABEZADO ================= --}}
<header>
<table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
  <tr>
    <td style="width:22%; text-align:center; border:1px solid #000;">
      <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
    </td>
    <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
      <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
        FORMATO DE QUEJAS Y SUGERENCIAS
      </div>

      <table style="width:100%; border-collapse:collapse; font-size:9pt;">
            <tr>
                <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
                    <b>Fecha de elaboración:</b><br>
                        14-Agosto-2025
                </td>
                <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
                    <b>Fecha de actualización:</b>--<br>
                </td>
                <td style="width:24%; padding:4px; text-align:center;">
                    <b>Versión:</b>00<br>
                </td>
            </tr>
        </table>
        </td>

        <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
            <b>Código:</b><br>
              SSS-FOR-CAL-12
        </div>
        <div style="padding:10px; text-align:center;">
            Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
        </td>
    </tr>
</table>
</header>
  <table class="tbl" style="margin-top:10px; border:none;">
    <tr>
      <td style="border:none; text-align:right; font-size:10pt;">
        Fecha: <span style="display:inline-block; min-width:160px; border-bottom:1px solid #000;">{{ $fecha ?? '' }}</span>
      </td>
    </tr>
  </table>

{{-- ===== TIPO DE SOLICITUD ===== --}}
<table class="tbl b1" style="font-size:10pt; margin-top:15px;">
  <tr class="th-green c"><td colspan="2" style="padding:4px;">TIPO DE SOLICITUD</td></tr>
  <tr>
    <td class="p4 l" colspan="2">
      Queja        <span class="cb">{{ ($tipo ?? '') === 'queja'        ? '☑' : '☐' }}</span> &nbsp;&nbsp;
      Reclamo      <span class="cb">{{ ($tipo ?? '') === 'reclamo'      ? '☑' : '☐' }}</span> &nbsp;&nbsp;
      Sugerencia   <span class="cb">{{ ($tipo ?? '') === 'sugerencia'   ? '☑' : '☐' }}</span> &nbsp;&nbsp;
      Felicitación <span class="cb">{{ ($tipo ?? '') === 'felicitacion' ? '☑' : '☐' }}</span>
    </td>
  </tr>
</table>

{{-- ===== DATOS DE IDENTIFICACIÓN (TIPO) ===== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr class="th-green c"><td colspan="2" style="padding:4px;">DATOS DE IDENTIFICACIÓN</td></tr>
    <tr><td class="p4 l" colspan="2"><b>Nombre y apellidos:</b> {{ $nombre ?? '' }}</td></tr>
    <tr>
      <td class="p4 l" colspan="2">
        Cliente    <span class="cb">{{ ($persona ?? '') === 'cliente'    ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Proveedor  <span class="cb">{{ ($persona ?? '') === 'proveedor'  ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Trabajador <span class="cb">{{ ($persona ?? '') === 'trabajador' ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Visita     <span class="cb">{{ ($persona ?? '') === 'visita'     ? '☑' : '☐' }}</span>
      </td>
    </tr>
    <tr><td class="p4 l" colspan="2"><b>Empresa:</b> {{ $empresa ?? '' }}</td></tr>
    <tr><td class="p4 l" colspan="2"><b>Área:</b> {{ $area ?? '' }}</td></tr>
    <tr><td class="p4 l" colspan="2"><b>Puesto que desempeña:</b> {{ $puesto ?? '' }}</td></tr>
    <tr><td class="p4 l" colspan="2"><b>Correo:</b> {{ $correo ?? '' }}</td></tr>
    <tr><td class="p4 l" colspan="2"><b>Teléfono:</b> {{ $telefono ?? '' }}</td></tr>
  </table>

  {{-- ===== MOTIVO ===== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr class="th-green c"><td colspan="2" style="padding:4px;">MOTIVO</td></tr>
    <tr>
      <td class="p4 l" colspan="2">
        Calidad del producto   <span class="cb">{{ !empty($m_calidad_producto)  ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Plazo de entrega       <span class="cb">{{ !empty($m_plazo_entrega)     ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Soporte técnico        <span class="cb">{{ !empty($m_soporte_tecnico)   ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Atención del personal  <span class="cb">{{ !empty($m_atencion_personal) ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Otro                   <span class="cb">{{ !empty($m_otro)              ? '☑' : '☐' }}</span>
        &nbsp;&nbsp; {{ $motivo_otro ?? '' }}
      </td>
    </tr>
  </table>

  <!-- Descripción -->
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr class="th-green c"><td style="padding:4px;">DESCRIPCIÓN DE LA QUEJA / SUGERENCIA</td></tr>
    <tr>
      <td class="p4 l" style="height:140px;">
        <i>* Describa la incidencia colocando la fecha en que sucedió *</i><br>
        <div style="min-height:120px;">{{ $descripcion ?? '' }}</div>
      </td>
    </tr>
  </table>

{{-- ===== RESPUESTA POR EMAIL ===== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr class="th-green c"><td style="padding:4px;">¿DESEA RECIBIR RESPUESTA POR EMAIL?</td></tr>
    <tr>
      <td class="p4 l">
        Sí <span class="cb">{{ ($respuesta_email ?? '') === 'si' ? '☑' : '☐' }}</span>
        &nbsp;&nbsp;
        No <span class="cb">{{ ($respuesta_email ?? '') === 'no' ? '☑' : '☐' }}</span>
      </td>
    </tr>
  </table>

  <!-- Aviso -->
  <p style="font-size:9pt; margin-top:12px; text-align:justify;">
   *Para que una queja o sugerencia sea tramitada y contestada es imprescindible que la persona que la plantee se identifique. 
   Los datos personales recogidos mediante este formato serán tratados de forma confidencial ajustándose a la legislación vigente en materia de protección de datos de carácter personal. 
   Agradecemos sus observaciones.
  </p>

</body>
</html>
