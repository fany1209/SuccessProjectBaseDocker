<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente de Personal - {{ $nombre ?? 'General' }}</title>
   <style>
  @page {
    margin: 140px 24px 80px 24px;
  }

  html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

  body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 10.5pt; }
  .w-full { width: 100%; }
  .tbl    { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 12px; }
  .b1 td, .b1 th { border: 1px solid #000; }
  .c { text-align: center; }
  .p4 { padding: 4px; }
  .p6 { padding: 6px; }
  .t9  { font-size: 9pt; }
  td { word-wrap: break-word; }
  .th-green { background:#92D050; font-weight:bold; color:#000; text-align: center; padding: 5px; }
  .bg-gray { background: #f2f2f2; font-weight: bold; }
  .text-justify { text-align: justify; }

  header {
    position: fixed;
    top: -120px;    
    left: 0; right: 0;
    height: 110px;  
    z-index: 10;
  }
  main { margin-top: 0; }
  tr { page-break-inside: avoid; }
</style>
</head>
<body>

{{-- ================= ENCABEZADO OFICIAL SSS-FOR-REH-01 ================= --}}
<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed;">
    <tr>
      <td rowspan="2" style="width:22%; text-align:center; border:1px solid #000; padding: 5px; vertical-align: middle;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>

      <td colspan="3" style="width:58%; text-align:center; font-weight:bold; font-size:13pt; border:1px solid #000; padding:10px; vertical-align: middle;">
        EXPEDIENTE DE PERSONAL
      </td>

      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
        <b>Código:</b><br> SSS-FOR-REH-01
      </td>
    </tr>

    <tr>
      <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; height: 35px; vertical-align: middle;">
        <b>Fecha de elaboración:</b>30-Enero-2023<br>
      </td>
      <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
        <b>Fecha de actualización:</b><br> --
      </td>
      <td style="width:19.4%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
        <b>Versión:</b><br>00
      </td>
      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
        &nbsp; </td>
    </tr>
  </table>
</header>

<main>
    {{-- ================= FOTOGRAFÍA Y NOMBRE FLOTANTE ================= --}}
    <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 15px; margin-top: 5px;">
      <tr>
        <td style="vertical-align: middle; text-align: left; border: none; padding-right: 20px; font-size: 11pt;">
            <b>Nombre:</b> {{ $nombre ?? '' }}
        </td>
        
        <td style="vertical-align: middle; text-align: right; width: 3cm; border: none;">
            @if(!empty($foto))
                <img src="{{ $foto }}" style="width: 2.5cm; height: 3.2cm; object-fit: cover; border: 1px solid #000;">
            @else
                <div style="display: inline-block; width: 2.5cm; height: 3.2cm; border: 1px dashed #666; text-align: center; line-height: 3.2cm; color: #666; font-size: 8pt;">
                    FOTOGRAFÍA
                </div>
            @endif
        </td>
      </tr>
    </table>

    {{-- ================= DATOS PERSONALES Y FAMILIARES ================= --}}
    <table class="tbl b1 t9">
        <tr>
            <td colspan="4" class="th-green">DATOS PERSONALES Y FAMILIARES</td>
        </tr>
        <tr>
            <td class="p6 bg-gray" style="width: 20%;">Sexo:</td>
            <td class="p6" style="width: 30%;">{{ $sexo ?? 'N/A' }}</td>
            <td class="p6 bg-gray" style="width: 20%;">Estado Civil:</td>
            <td class="p6" style="width: 30%;">{{ $estado_civil ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">¿Tiene hijos?:</td>
            <td class="p6">{{ $tiene_hijos ?? 'No' }}</td>
            <td class="p6 bg-gray">Cuántos:</td>
            <td class="p6">{{ $cuantos_hijos ?? '0' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">¿Su pareja trabaja?:</td>
            <td class="p6">{{ $pareja_trabaja ?? 'N/A' }}</td>
            <td class="p6 bg-gray">Empresa donde labora:</td>
            <td class="p6">{{ $empresa_pareja ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- ================= DATOS MÉDICOS Y DE EMERGENCIA ================= --}}
    <table class="tbl b1 t9">
        <tr>
            <td colspan="4" class="th-green">DATOS MÉDICOS Y DE EMERGENCIA</td>
        </tr>
        <tr>
            <td class="p6 bg-gray" style="width: 20%;">Tipo de Sangre:</td>
            <td class="p6" style="width: 30%;">{{ $tipo_sangre ?? 'N/A' }}</td>
            <td class="p6 bg-gray" style="width: 20%;">¿Es alérgico a algo?:</td>
            <td class="p6" style="width: 30%;">{{ $alergico ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Alergias / Padecimientos:</td>
            <td class="p6 text-justify" colspan="3">{{ $alergias_desc ?? 'Ninguno reportado.' }}</td>
        </tr>
        <tr>
            <td colspan="4" class="p6 c bg-gray" style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
                EN CASO DE ACCIDENTE AVISAR A:
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Nombre:</td>
            <td class="p6">{{ $accidente_nombre ?? 'N/A' }}</td>
            <td class="p6 bg-gray">Parentesco:</td>
            <td class="p6">{{ $accidente_parentesco ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Teléfono:</td>
            <td class="p6" colspan="3">{{ $accidente_telefono ?? 'N/A' }}</td>
        </tr>
    </table>

   {{-- ================= REQUISITOS (DOCUMENTACIÓN) ================= --}}
    <table class="tbl b1 t9">
        <tr>
            <td colspan="3" class="th-green">REQUISITOS / DOCUMENTACIÓN DEL EXPEDIENTE</td>
        </tr>
        <tr class="bg-gray c">
            <td class="p4" style="width: 40%;">Requisito</td>
            <td class="p4" style="width: 15%;">Estatus</td>
            <td class="p4" style="width: 45%;">Comentario</td>
        </tr>
        @php
            $listaRequisitos = [
                'Fotografía infantil', 'Solicitud de empleo o CV', 'Copia de acta de nacimiento',
                'Copia de comprobante de domicilio', 'Copia de INE', 'Copia de CURP', 'Copia de RFC',
                'Número de Seguro Social', 'Copia del último grado de estudios', 'Constancias de cursos tomados',
                'Copia de licencia de manejo', '2 cartas de recomendación laboral'
            ];
            
            $reqsMarcados = is_array($requisitos) ? $requisitos : [];
        @endphp
        
        @foreach($listaRequisitos as $req)
            @php
                $estatus = '';
                $comentario = '';
                
                foreach($reqsMarcados as $dato) {
                    if(isset($dato['nombre']) && $dato['nombre'] === $req) {
                        $estatus = $dato['status'] ?? '';
                        $comentario = $dato['comentario'] ?? '';
                        break;
                    }
                }
            @endphp
            <tr>
                <td class="p6">
                    {{ $req }}
                </td>
                <td class="p6 c" style="font-weight: bold; color: {{ $estatus == 'OK' ? '#16a34a' : ($estatus == 'PENDIENTE' ? '#ea580c' : '#000') }};">
                    {{ $estatus }}
                </td>
                <td class="p6">
                    {{ $comentario }}
                </td>
            </tr>
        @endforeach
    </table>

    {{-- ================= HISTORIAL LABORAL INTERNO ================= --}}
    <table class="tbl b1 t9">
        <tr>
            <td colspan="4" class="th-green">HISTORIAL LABORAL INTERNO</td>
        </tr>
        <tr>
            <td colspan="2" class="p4 c bg-gray" style="width: 50%;">PERIODO 1</td>
            <td colspan="2" class="p4 c bg-gray" style="width: 50%;">PERIODO 2</td>
        </tr>
        <tr>
            <td class="p6 bg-gray" style="width: 20%;">Fecha Ingreso:</td>
            <td class="p6" style="width: 30%;">{{ $fecha_ingreso_1 ?? '--' }}</td>
            <td class="p6 bg-gray" style="width: 20%;">Fecha Ingreso:</td>
            <td class="p6" style="width: 30%;">{{ $fecha_ingreso_2 ?? '--' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Duración:</td>
            <td class="p6">{{ $duracion_1 ?? '--' }}</td>
            <td class="p6 bg-gray">Duración:</td>
            <td class="p6">{{ $duracion_2 ?? '--' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Fecha Baja:</td>
            <td class="p6">{{ $fecha_baja_1 ?? '--' }}</td>
            <td class="p6 bg-gray">Fecha Baja:</td>
            <td class="p6">{{ $fecha_baja_2 ?? '--' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Motivo Baja:</td>
            <td class="p6" colspan="3">
                {{ $motivo_baja ?? 'N/A' }}
                @if(!empty($motivo_baja_otro))
                    - {{ $motivo_baja_otro }}
                @endif
            </td>
        </tr>
    </table>

    {{-- ================= VACACIONES (TABLA DINÁMICA DE LEY) ================= --}}
    <table class="tbl b1 t9" style="text-align:center;">
        <tr>
            <td colspan="12" class="th-green">DÍAS DE VACACIONES POR AÑO</td>
        </tr>
        <tr style="font-weight:bold;">
            @for($i = 1; $i <= 12; $i++)
                <td class="p4" style="background: {{ ($anios_empresa ?? 0) == $i ? '#92D050' : '#f0f0f0' }};">
                    Año {{ $i }}
                </td>
            @endfor
        </tr>
        <tr>
            @php
                $diasPorAno = [1=>12, 2=>14, 3=>16, 4=>18, 5=>20, 6=>22, 7=>22, 8=>22, 9=>22, 10=>22, 11=>24, 12=>24];
            @endphp
            @foreach($diasPorAno as $ano => $dias)
                <td class="p4" style="background: {{ ($anios_empresa ?? 0) == $ano ? '#92D050' : '#fff' }}; font-weight: {{ ($anios_empresa ?? 0) == $ano ? 'bold' : 'normal' }};">
                    {{ $dias }}
                </td>
            @endforeach
        </tr>
    </table>
    
    <div style="font-size: 8.5pt; text-align: right; margin-top: 5px;">
        <b>Años cumplidos en la empresa:</b> {{ $anios_empresa ?? '0' }} &nbsp;&nbsp;|&nbsp;&nbsp;
        <b>Días que le corresponden:</b> {{ $dias_vacaciones ?? '0' }}
    </div>

</main>
</body>
</html>