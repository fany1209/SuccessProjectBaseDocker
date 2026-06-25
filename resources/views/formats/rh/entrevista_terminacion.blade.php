<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entrevista de Terminación Laboral</title>
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
  .t12 { font-size: 12pt; font-weight: bold; }
  .t9  { font-size: 9pt; }
  td { word-wrap: break-word; }
  .th-green { background:#92D050; font-weight:bold; color:#000; }
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

{{-- ================= ENCABEZADO OFICIAL SSS-FOR-REH-04 ================= --}}
<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed;">
    <tr>
      <td rowspan="2" style="width:22%; text-align:center; border:1px solid #000; padding:5px; vertical-align:middle;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>

      <td colspan="3" style="width:58%; text-align:center; font-weight:bold; font-size:14pt; border:1px solid #000; padding:10px; vertical-align:middle;">
        ENTREVISTA DE TERMINACIÓN LABORAL
      </td>

      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align:middle;">
        <b>Código:</b><br>
        SSS-FOR-REH-04
      </td>
    </tr>

    <tr>
      <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; height:35px; vertical-align:middle;">
        <b>Fecha de elaboración:</b><br>
        30-Enero-2023
      </td>

      <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align:middle;">
        <b>Fecha de actualización:</b> --
      </td>

      <td style="width:19.4%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align:middle;">
        <b>Versión:</b>
        00
      </td>

      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align:middle;">
        &nbsp;
      </td>
    </tr>
  </table>
</header>

<main>
    <div style="text-align: right; margin-bottom: 10px; font-weight: bold; font-size: 10pt;">
        Fecha: {{ $fecha_hoy }}
    </div>

    {{-- ================= CUADRÍCULA DE IDENTIFICACIÓN ================= --}}
    <table class="tbl b1 t9">
        <tr>
            <td class="p6 bg-gray" style="width: 20%;">Nombre:</td>
            <td class="p6" colspan="3" style="font-weight: bold; font-size:10.5pt;">{{ $nombre ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray" style="width: 20%;">Área:</td>
            <td class="p6" style="width: 30%;">{{ $area ?? '' }}</td>
            <td class="p6 bg-gray" style="width: 20%;">Puesto:</td>
            <td class="p6" style="width: 30%;">{{ $puesto ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Jefe inmediato:</td>
            <td class="p6" colspan="3">{{ $jefe_inmediato ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Último día laboral:</td>
            <td class="p6" colspan="3" style="font-weight: bold;">{{ $fecha_ultimo_dia ?? '' }}</td>
        </tr>
    </table>

    {{-- ================= PREGUNTAS EN BLOQUES DE EXCEL ================= --}}
    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">¿Qué te hizo empezar a buscar un nuevo trabajo?</td>
        </tr>
        <tr>
            <td class="p6 text-justify" style="min-height: 35px;">{{ $que_hizo_buscar ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">¿Cuál es la razón principal por la que renuncias?</td>
        </tr>
        <tr>
            <td class="p6" style="line-height: 1.8;">
                @php
                    $razones = ['Problemas personales', 'Cambio de empleo', 'Horario', 'Mal ambiente de trabajo', 'Estrés', 'Presión', 'Trabajo pesado', 'Salario bajo', 'Crecimiento laboral', 'Falta de capacitación', 'Mal trato laboral'];
                    $razonMarcada = $razon_principal ?? '';
                @endphp
                @foreach($razones as $r)
                    <span style="display: inline-block; width: 32%; font-size: 8.5pt;">
                        ( {{ $razonMarcada == $r ? 'X' : ' ' }} ) {{ $r }}
                    </span>
                @endforeach
            </td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4" colspan="2">¿El trabajo cumplía con las expectativas que tenías?</td>
        </tr>
        <tr>
            <td class="p6" style="width: 20%; font-weight: bold; text-align: center; background: #fafafa; vertical-align: middle;">
                ( {{ ($cumplio_expectativas ?? '') == 'SI' ? 'X' : ' ' }} ) SI &nbsp;&nbsp;&nbsp;&nbsp;
                ( {{ ($cumplio_expectativas ?? '') == 'NO' ? 'X' : ' ' }} ) NO
            </td>
            <td class="p6 text-justify">
                <b>¿Por qué?:</b> {{ $expectativas_porque ?? 'N/A' }}
            </td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">¿Hace cuánto pensabas dejar tu puesto de trabajo?</td>
        </tr>
        <tr>
            <td class="p6">{{ $hace_cuanto_pensaba ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">¿Habló con alguien sobre sus inquietudes antes de decidir irte?</td>
        </tr>
        <tr>
            <td class="p6">{{ $hablo_con_alguien ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">¿Qué te haría reconsiderar tu decisión de irte?</td>
        </tr>
        <tr>
            <td class="p6 text-justify">{{ $que_haria_reconsiderar ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">Si tuvieras la oportunidad de realizar algún cambio en la empresa o en tu trabajo, ¿Cuál sería?</td>
        </tr>
        <tr>
            <td class="p6 text-justify">{{ $cambio_empresa ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">A lo largo de su estancia, ¿Recibió algún comentario constructivo que le haya ayudado con su desempeño?</td>
        </tr>
        <tr>
            <td class="p6 text-justify">{{ $comentario_constructivo ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">¿Qué es lo que más te gustó de trabajar con nosotros?</td>
        </tr>
        <tr>
            <td class="p6 text-justify">{{ $que_mas_gusto ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">¿Qué te hizo decidir aceptar el nuevo trabajo?</td>
        </tr>
        <tr>
            <td class="p6 text-justify">{{ $decidir_aceptar ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4" colspan="2">¿Recomendaría el nombre de la organización a un amigo como buen lugar para trabajar?</td>
        </tr>
        <tr>
            <td class="p6" style="width: 20%; font-weight: bold; text-align: center; background: #fafafa; vertical-align: middle;">
                ( {{ ($recomendaria ?? '') == 'SI' ? 'X' : ' ' }} ) SI &nbsp;&nbsp;&nbsp;&nbsp;
                ( {{ ($recomendaria ?? '') == 'NO' ? 'X' : ' ' }} ) NO
            </td>
            <td class="p6 text-justify">
                <b>¿Por qué?:</b> {{ $recomendaria_porque ?? 'N/A' }}
            </td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">¿En qué podríamos mejorar como empresa?</td>
        </tr>
        <tr>
            <td class="p6" style="line-height: 1.8;">
                @php
                    $mejoras = ['Instalaciones', 'Liderazgo', 'Designación de actividades', 'Motivación', 'Organización interna', 'Salario', 'Prestaciones', 'Comunicación', 'Horario', 'Herramientas y tecnología'];
                    $mejoraMarcada = $mejorar_empresa ?? '';
                @endphp
                @foreach($mejoras as $m)
                    <span style="display: inline-block; width: 32%; font-size: 8.5pt;">
                        ( {{ $mejoraMarcada == $m ? 'X' : ' ' }} ) {{ $m }}
                    </span>
                @endforeach
                @if(!empty($mejorar_empresa_otro))
                    <div style="margin-top: 5px; font-size: 8.5pt; border-top: 1px dashed #ccc; padding-top:4px;">
                        <b>Otro:</b> {{ $mejorar_empresa_otro }}
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">¿Cuál fue tu grado de satisfacción durante el desarrollo de tus labores en la empresa?</td>
        </tr>
        <tr>
            <td class="p6 c" style="font-weight: bold; background: #fafafa; padding: 8px 0;">
                ( {{ ($grado_satisfaccion ?? '') == 'Muy satisfecho' ? 'X' : ' ' }} ) Muy Satisfecho
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                ( {{ ($grado_satisfaccion ?? '') == 'Satisfecho' ? 'X' : ' ' }} ) Satisfecho
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                ( {{ ($grado_satisfaccion ?? '') == 'Insatisfecho' ? 'X' : ' ' }} ) Insatisfecho
            </td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <tr>
            <td class="th-green p4">Algún comentario que quieras agregar de tu estancia en esta empresa:</td>
        </tr>
        <tr>
            <td class="p6 text-justify" style="min-height: 45px;">{{ $comentario_adicional ?? 'Ninguno.' }}</td>
        </tr>
    </table>
</main>

</body>
</html>