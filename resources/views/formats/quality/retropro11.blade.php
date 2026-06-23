<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Retroalimentación del Proveedor — SSS-FOR-CAL-11</title>
  <style>
    @page { margin: 140px 24px 18px 24px; }
    body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
    td, th { word-wrap: break-word; }
    .header {
      position: fixed; 
      top: -120px;   
      left: 0; 
      right: 0;
      height: 120px; 
    }
    .content { margin-top: 0; } 
    .tbl    { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .b1 td, .b1 th { border: 0.5px solid #000; }
    .c { text-align: center; } .l { text-align: left; } .r { text-align: right; }
    .p3 { padding: 3px; } .p4 { padding: 4px; } .p6 { padding: 6px; }
    .title { text-align:center; font-weight:bold; margin: 12px 0 6px; font-size: 12pt; }
    .th-green { background:#92D050; color:#000; font-weight:bold; }
    .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
    .firmas { width:100%; margin-top:22px; }
    .firma-linea { border-top: 1px solid #000; height: 26px; }
    .firma-etq { font-size: 9pt; padding-top:4px; }
  </style>
</head>
<body>

<!-- ================= ENCABEZADO (FIJO) ================= -->
<div class="header">
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
    <tr>
      <td style="width:22%; text-align:center; border:1px solid #000;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>
      <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
        <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
          REGISTRO DE RETROALIMENTACIÓN DEL PROVEEDOR
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
                23-Enero-2024
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
            SSS-FOR-CAL-11
        </div>
        <div style="padding:10px; text-align:center;">&nbsp;</div>
      </td>
    </tr>
  </table>
</div>

<div class="content">
  <table class="tbl" style="margin-top:15px;">
    <tr>
      <td style="border:none; text-align:right; font-size:10pt;">
        Fecha: <span style="display:inline-block; min-width:160px; border-bottom:1px solid #000;">{{ $fecha ?? '' }}</span>
      </td>
    </tr>
  </table>

{{-- ================= INFORMACIÓN DEL PROVEEDOR ================= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr class="th-green c">
      <td colspan="2" style="font-weight:bold; font-size:10pt; padding:4px;">
        INFORMACIÓN DEL PROVEEDOR
      </td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Nombre de la empresa:</b> {{ $empresa ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Nombre del contacto:</b> {{ $contacto ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" style="width:50%;"><b>Teléfono:</b> {{ $telefono ?? '' }}</td>
      <td class="p4 l" style="width:50%;"><b>Correo:</b> {{ $correo ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2"><b>Dirección:</b> {{ $direccion ?? '' }}</td>
    </tr>
  </table>

{{-- ================= DETALLES DE LA NO CONFORMIDAD ================= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c">
      <td colspan="2" style="font-weight:bold; font-size:10pt; padding:4px;">
        DETALLES DE LA NO CONFORMIDAD
      </td>
    </tr>

    <tr>
      <td class="p4 l" colspan="2"><b>Motivo de la no conformidad:</b> {{ $motivo ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4 l" style="width:70%;"><b>Fecha:</b> {{ $fecha_incidencia ?? '' }}</td>
      <td class="p4 l" style="width:30%;"><b>Hora:</b> {{ $hora_incidencia ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4 l" colspan="2"><b>Producto o servicio afectado:</b> {{ $producto_servicio ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4 l" colspan="2"><b>Referencia al contrato:</b> {{ $referencia_contrato ?? '' }}</td>
    </tr>

  <tr>
    <td class="p4 l" colspan="2" style="height:100px;">
      <b>Especificaciones incumplidas:</b>
      <div style="min-height:80px;">{{ $especificaciones ?? '' }}</div>
    </td>
  </tr>

    @if(!empty($especificaciones_imgs_b64) && is_array($especificaciones_imgs_b64))
    <tr>
      <td class="p4 l" colspan="2">
        <b>Evidencias:</b>
        <table width="100%" cellspacing="0" cellpadding="0" style="margin-top:6px;">
          <tr>
            @foreach($especificaciones_imgs_b64 as $i => $src)
              <td style="padding:4px; width:33%; text-align:center; vertical-align:top;">
                <img src="{{ $src }}" alt="Evidencia {{ $i+1 }}"
                    style="max-width:180px; max-height:150px; object-fit:contain; border:1px solid #ccc; padding:2px;">
              </td>
              @if(($i+1) % 3 === 0)
                </tr><tr>
              @endif
            @endforeach
          </tr>
        </table>
      </td>
    </tr>
    @endif
  </table>

    {{-- ================= IMPACTO ================= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr class="th-green c">
      <td colspan="2" style="font-weight:bold; font-size:10pt; padding:4px;">
          IMPACTO
      </td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2" style="height:90px;">
        <b>Consecuencias de la no conformidad en la Producción:</b>
        <div style="min-height:70px;">{{ $impacto_consecuencias ?? '' }}</div>
      </td>
    </tr>
    <tr>
      <td class="p4 l" style="width:50%;">
        <b>¿Implicó un costo adicional?</b>
        &nbsp; Sí <span class="cb">{{ ($costo_adicional ?? 'no') === 'si' ? '☑' : '☐' }}</span>
        &nbsp; No <span class="cb">{{ ($costo_adicional ?? 'no') === 'no' ? '☑' : '☐' }}</span>
      </td>
      <td class="p4 l" style="width:50%;">
        <b>¿Implicó retraso de producción?</b>
        &nbsp; Sí <span class="cb">{{ ($retraso_produccion ?? 'no') === 'si' ? '☑' : '☐' }}</span>
        &nbsp; No <span class="cb">{{ ($retraso_produccion ?? 'no') === 'no' ? '☑' : '☐' }}</span>
      </td>
    </tr>
    <tr>
      <td class="p4 l" colspan="2">
        <b>Importancia para Success:</b>
        &nbsp; Baja  <span class="cb">{{ ($importancia ?? 'media') === 'baja'  ? '☑' : '☐' }}</span>
        &nbsp; Media <span class="cb">{{ ($importancia ?? 'media') === 'media' ? '☑' : '☐' }}</span>
        &nbsp; Alta  <span class="cb">{{ ($importancia ?? 'media') === 'alta'  ? '☑' : '☐' }}</span>
      </td>
    </tr>
  </table>

{{-- ================= ACCIÓN CORRECTIVA ================= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr class="th-green c">
      <td colspan="2" style="font-weight:bold; font-size:10pt; padding:6px;">
        ACCIÓN CORRECTIVA
      </td>
    </tr>

    <tr>
      <td class="p6 l" colspan="2">
        <b>Medidas iniciales tomadas para mitigar el problema:<br>
        (Descripción de las acciones correctivas)</b> {{ $medidas_iniciales ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p6 l" colspan="2" style="height:100px;">
        <b>Propuesta de acción correctiva por parte del proveedor:</b>
        <div style="min-height:80px;">{{ $propuesta_accion ?? '' }}</div>
      </td>
    </tr>

    <tr>
      <td class="p6 l" colspan="2">
        <b>Fecha límite para la implementación:</b> {{ $fecha_limite ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p6 l" colspan="2">
        <b>Acción final:</b> {{ $accion_final ?? '' }}
      </td>
    </tr>
  </table>

 {{-- ================= SEGUIMIENTO Y VERIFICACIÓN ================= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr class="th-green c">
      <td colspan="2" style="font-weight:bold; font-size:10pt; padding:4px;">
        SEGUIMIENTO Y VERIFICACIÓN
      </td>
    </tr>

    <tr>
      <td class="p4 l" colspan="2">
        <b>Responsable de verificar la implementación de la accion correctiva:</b> {{ $resp_verificacion ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4 l" colspan="2">
        <b>Fecha de la verificación:</b> {{ $fecha_verificacion ?? '' }}
      </td>
    </tr>

    <tr>
      <td class="p4 l" colspan="2" style="height:100px;">
        <b>Resultados de la verificación:</b>
        <div style="min-height:80px;">{{ $resultados_verificacion ?? '' }}</div>
      </td>
    </tr>
</table>

<table class="firmas" style="border:none; width:100%; margin-top:22px;">
    <tr>
      <td style="width:48%; padding-right:2%; text-align:center;">
        <div style="min-height:20px; font-size:10pt;">
          {{ $firma_responsable_success ?? '' }}
        </div>
        <div class="firma-linea"></div>
        <div class="firma-etq">Nombre y firma del responsable de Success</div>
      </td>
      <td style="width:48%; padding-left:2%; text-align:center;">
        <div style="min-height:20px; font-size:10pt;">
          {{ $firma_representante_proveedor ?? '' }}
        </div>
        <div class="firma-linea"></div>
        <div class="firma-etq">Nombre y firma del representante del proveedor</div>
      </td>
    </tr>
  </table>  
</div>

</body>
</html>
