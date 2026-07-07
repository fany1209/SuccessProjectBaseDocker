<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente de Practicante - {{ $practicante->nombre ?? '' }}</title>
    <style>
        @page { margin: 140px 24px 80px 24px; }
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .w-full { width: 100%; }
        .tbl { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .b1 td, .b1 th { border: 1px solid #000; padding: 5px; }
        .th-green { background: #92D050; font-weight: bold; text-align: center; }
        .bg-gray { background: #f2f2f2; font-weight: bold; }
        
        header {
            position: fixed;
            top: -120px;    
            left: 0; right: 0;
            height: 110px;  
            z-index: 10;
        }
        main { margin-top: 0; }
        
        .section { margin-bottom: 10px;  }
        table { page-break-inside: auto; }
        tr {  page-break-after: auto; }
    </style>
</head>
<body>

<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed;">
    <tr>
      <td rowspan="2" style="width:22%; text-align:center; border:1px solid #000; padding: 5px; vertical-align: middle;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>

      <td colspan="3" style="width:58%; text-align:center; font-weight:bold; font-size:14pt; border:1px solid #000; padding:10px; vertical-align: middle;">
        EXPEDIENTE DE PRACTICANTES
      </td>

      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
        <b>Código:</b><br> SSS-FOR-REH-06
      </td>
    </tr>

    <tr>
      <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; height: 35px; vertical-align: middle;">
        <b>Fecha de elaboración:</b><br> 30-Enero-2023
      </td>
      <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
        <b>Fecha de actualización:</b><br> --
      </td>
      <td style="width:19.4%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
        <b>Versión:</b><br> 00
      </td>
      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
        </td>
    </tr>
  </table>
</header>

<main>
    
    <div style="float: right; width: 100px; margin-bottom: 10px;">
        <div style="border: 1px dashed #000; width: 80px; height: 100px; text-align: center; color: #777; font-size: 8px; overflow: hidden;">
            @if(!empty($practicante->foto_base64))
                <img src="{{ $practicante->foto_base64 }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <div style="padding-top: 35px; line-height: 11px;">
                    PEGAR FOTO<br>TAMAÑO<br>INFANTIL
                </div>
            @endif
        </div>
    </div>

  <div class="section" style="clear: both;">
    <table class="tbl b1">
        <tr>
            <td colspan="4" class="th-green">DATOS DEL PRACTICANTE</td>
        </tr>
        <tr>
            <td colspan="4">
                <b>Nombre:</b> {{ $practicante->nombre ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%;">
                <b>Institución:</b> {{ $practicante->institucion ?? '' }}
            </td>
            <td colspan="2" style="width: 50%;">
                <b>Dirección Institución:</b> {{ $practicante->direccion_inst ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%;">
                <b>N° Control:</b> {{ $practicante->n_control ?? '' }}
            </td>
            <td colspan="2" style="width: 50%;">
                <b>Edad:</b> {{ $practicante->edad ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%;">
                <b>Carrera:</b> {{ $practicante->carrera ?? '' }}
            </td>
            <td colspan="2" style="width: 50%;">
                <b>Sexo:</b> {{ $practicante->sexo ?? '' }} &nbsp;&nbsp; <b>Edo. Civil:</b> {{ $practicante->estado_civil ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%;">
                <b>Teléfono:</b> {{ $practicante->telefono ?? '' }}
            </td>
            <td colspan="2" style="width: 50%;">
                <b>Correo:</b> {{ $practicante->correo ?? '' }}
            </td>
        </tr>
    </table>
  </div>

 <div class="section">
    <table class="tbl b1" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="4" class="th-green" style="padding: 5px;">CONTROL DE PERIODO Y HORARIOS</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Ingreso:</b> {{ $practicante->fecha_ingreso ?? '' }}
            </td>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Horario:</b> {{ $practicante->horario_laboral ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Est. Término:</b> {{ $practicante->fecha_estimada ?? '' }}
            </td>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Horas:</b> {{ $practicante->horas_cubrir ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Real Término:</b> {{ $practicante->fecha_real ?? '' }}
            </td>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Periodo:</b> {{ $practicante->periodo_proyectado ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 4px;">
                <b>Días Asistencia:</b> 
                @if(isset($practicante->dias_asistencia) && is_array($practicante->dias_asistencia))
                    {{ implode(', ', $practicante->dias_asistencia) }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 4px;">
                <b>Medio Origen:</b> {{ $practicante->medio_origen ?? '' }}
            </td>
        </tr>
    </table>
  </div>

  <div class="section">
    <table class="tbl b1" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="4" class="th-green" style="padding: 5px;">INFORMACIÓN DE PROYECTO Y SEGURIDAD</td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 4px;">
                <b>Proyecto:</b> {{ $practicante->nombre_proyecto ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Área:</b> {{ $practicante->area_aplicacion ?? '' }}
            </td>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Asesor Ext.:</b> {{ $practicante->asesor_externo ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 4px;">
                <b>Firma Acuerdo:</b> {{ $practicante->firma_acuerdo ?? '' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Seg. Social:</b> {{ $practicante->seguridad_social ?? '' }}
            </td>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>N° Seg.:</b> {{ $practicante->nss ?? '' }}
            </td>
        </tr>
    </table>
  </div>

  <div class="section">
    <table class="tbl b1" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="4" class="th-green" style="padding: 5px;">CARGA ACADÉMICA PENDIENTE</td>
        </tr>
        <tr>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Materias Pend.:</b> {{ $practicante->materias_pendientes ?? 'NO' }}
            </td>
            <td colspan="2" style="width: 50%; padding: 4px;">
                <b>Materia:</b> {{ $practicante->materia_nombre ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td colspan="4" style="padding: 4px;">
                <b>Horario:</b> {{ $practicante->materia_horario ?? 'N/A' }}
            </td>
        </tr>
    </table>
  </div>

  <div class="section">
      <table class="tbl b1">
          <tr><td colspan="3" class="th-green">REQUISITOS DOCUMENTALES</td></tr>
          <tr class="bg-gray"><td style="width: 50%;">Requisito</td><td style="text-align:center; width: 10%;">Estatus</td><td style="width: 40%;">Comentario</td></tr>
          @if(isset($practicante->req))
              @foreach($practicante->req as $item)
              <tr>
                  <td>{{ $item['nombre'] }}</td>
                  <td style="text-align:center;">{{ isset($item['ok']) ? 'OK' : 'PENDIENTE' }}</td>
                  <td>{{ $item['com'] ?? '' }}</td>
              </tr>
              @endforeach
          @endif
      </table>
  </div>

 <div class="section">
      <table class="tbl b1" style="width: 100%; border-collapse: collapse;">
          <tr>
              <td colspan="4" class="th-green" style="padding: 5px;">DATOS FAMILIARES</td>
          </tr>
          <tr>
              <td colspan="2" style="width: 50%; padding: 4px;">
                  <b>Tiene Hijos:</b> {{ $practicante->tiene_hijos ?? 'NO' }} ({{ $practicante->cuantos_hijos ?? '0' }})
              </td>
              <td colspan="2" style="width: 50%; padding: 4px;">
                  <b>Pareja Trabaja:</b> {{ $practicante->pareja_trabaja ?? 'NO' }}
              </td>
          </tr>
          <tr>
              <td colspan="2" style="width: 50%; padding: 4px;">
                  <b>Hijo 1:</b> {{ $practicante->hijo1_nombre ?? 'N/A' }} ({{ $practicante->hijo1_edad ?? '' }})
              </td>
              <td colspan="2" style="width: 50%; padding: 4px;">
                  <b>Empresa Pareja:</b> {{ $practicante->pareja_empresa ?? 'N/A' }}
              </td>
          </tr>
          <tr>
              <td colspan="2" style="width: 50%; padding: 4px;">
                  <b>Hijo 2:</b> {{ $practicante->hijo2_nombre ?? 'N/A' }} ({{ $practicante->hijo2_edad ?? '' }})
              </td>
              <td colspan="2" style="width: 50%; padding: 4px;">
                  <b>Tel. Pareja:</b> {{ $practicante->pareja_telefono ?? 'N/A' }}
              </td>
          </tr>
      </table>
  </div>

  <div class="section">
      <table class="tbl b1" style="width: 100%; border-collapse: collapse;">
          <tr>
              <td colspan="4" class="th-green" style="padding: 5px;">CONTACTO DE EMERGENCIA Y SALUD</td>
          </tr>
          <tr>
              <td colspan="4" style="padding: 4px;">
                  <b>Llamar a:</b> {{ $practicante->emergencia_nombre ?? '' }} ({{ $practicante->emergencia_parentesco ?? '' }}) - {{ $practicante->emergencia_telefono ?? '' }}
              </td>
          </tr>
          <tr>
              <td colspan="2" style="width: 50%; padding: 4px;">
                  <b>Tipo de Sangre:</b> {{ $practicante->tipo_sangre ?? '' }}
              </td>
              <td colspan="2" style="width: 50%; padding: 4px;">
                  <b>Alergias:</b> {{ $practicante->alergias ?? 'N/A' }}
              </td>
          </tr>
      </table>
  </div>

  <table style="width: 100%; border-collapse: collapse; margin-top: 50px; ">
      <tr>
          <td style="text-align: center; border: none;">
              <div style="width: 40%; margin: 0 auto; border-bottom: 1px solid #000; padding-top: 40px; margin-bottom: 5px;"></div>
              <span style="font-weight:bold; font-size: 10pt;">Nombre y firma</span>
          </td>
      </tr>
  </table>
</main>
</body>
</html>