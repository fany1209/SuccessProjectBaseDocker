<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DNC - {{ $dnc->employee_name ?? 'Documento' }}</title>
    <style>
        @page {
            margin: 140px 24px 80px 24px;
        }

        html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; }
        .w-full { width: 100%; }
        .tbl    { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 10px; }
        .b1 td, .b1 th { border: 1px solid #000; }
        .c { text-align: center; }
        .r { text-align: right; }
        .l { text-align: left; }
        .p4 { padding: 4px; }
        .p6 { padding: 6px; }
        .t12 { font-size: 12pt; font-weight: bold; }
        .t9  { font-size: 9pt; }
        .t8  { font-size: 8pt; }
        td { word-wrap: break-word; }
        
        .th-green { background:#92D050; font-weight:bold; color:#000; }
        .bg-gray { background: #f2f2f2; font-weight: bold; width: 120px; }
        
        header {
            position: fixed;
            top: -120px;    
            left: 0; right: 0;
            height: 110px;  
            z-index: 10;
        }
        main { margin-top: 0; }
        .section {
            page-break-inside: auto !important;
            break-inside: auto !important;
            margin-bottom: 15px;
        }
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; }

        .linea-firma { width: 80%; margin: 0 auto; border-bottom: 1px solid #000; padding-top: 50px; margin-bottom: 5px; }

        .tbl-firmas {
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 25px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .tbl-firmas td { width: 33.33%; text-align: center; vertical-align: bottom; border: none; }
    </style>
</head>
<body>

{{-- ================= ENCABEZADO OFICIAL ESTANDARIZADO ================= --}}
<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed;">
    <tr>
      <td rowspan="2" style="width:22%; text-align:center; border:1px solid #000; padding: 5px; vertical-align: middle;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>
      <td colspan="3" style="width:58%; text-align:center; font-weight:bold; font-size:14pt; border:1px solid #000; padding:10px; vertical-align: middle;">
        DETECCIÓN DE NECESIDADES DE CAPACITACIÓN
      </td>
      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
        <b>Código:</b><br> SSS-FOR-REH-05
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
    {{-- ================= DATOS GENERALES ================= --}}
 <div class="section">
    <table class="tbl b1 t9">
        <tr>
            <td colspan="4" class="th-green c p6" style="font-size:10pt;">
                DATOS GENERALES DEL TRABAJADOR
            </td>
        </tr>
        <tr>
            <td class="p6" colspan="4">
                <b>Nombre:</b> {{ $dnc->employee_name ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="p6" colspan="2" style="width: 50%;">
                <b>Puesto:</b> {{ $dnc->position ?? '' }}
            </td>
            <td class="p6" colspan="2" style="width: 50%;">
                <b>Área:</b> {{ $dnc->department ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="p6" colspan="2" style="width: 50%;">
                <b>Antigüedad en la empresa:</b> {{ $dnc->emp_seniority ?? '' }}
            </td>
            <td class="p6" colspan="2" style="width: 50%;">
                <b>Antigüedad en el puesto:</b> {{ $dnc->pos_seniority ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="p6" colspan="4">
                <b>Fecha de evaluación:</b> {{ $dnc->evaluation_date ?? date('d/m/Y') }}
            </td>
        </tr>
    </table>
</div>

    {{-- ================= CUESTIONARIO ================= --}}
    <div class="section">
        <table class="tbl b1 t8">
            <thead>
                <tr class="th-green c">
                    <th class="p6" style="width: 35%;">Pregunta</th>
                    <th class="p6" style="width: 5%;">SI</th>
                    <th class="p6" style="width: 5%;">NO</th>
                    <th class="p6" style="width: 25%;">Nombre del curso</th>
                    <th class="p6" style="width: 30%;">¿Cómo lo aplicaría en mi área de trabajo?</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($dnc->questions) && is_array($dnc->questions))
                    @foreach($dnc->questions as $q)
                    <tr>
                        <td class="p6" style="text-align: justify;">{{ $q['pregunta'] ?? '' }}</td>
                        <td class="p6 c">{!! (isset($q['respuesta']) && $q['respuesta'] == 'SI') ? 'X' : '&nbsp;' !!}</td>
                        <td class="p6 c">{!! (isset($q['respuesta']) && $q['respuesta'] == 'NO') ? 'X' : '&nbsp;' !!}</td>
                        <td class="p6 c">{!! !empty($q['curso']) ? e($q['curso']) : '&nbsp;' !!}</td>
                        <td class="p6 c text-justify">{!! !empty($q['aplicacion']) ? e($q['aplicacion']) : '&nbsp;' !!}</td>
                    </tr>
                    @endforeach
                @endif
                
                @if(isset($dnc->extra_questions) && is_array($dnc->extra_questions))
                    @foreach($dnc->extra_questions as $extra)
                        @if(!empty($extra['curso']))
                        <tr>
                            <td class="p6">{{ $extra['pregunta'] ?? '¿En cuál tema le gustaría ser capacitado?' }}</td>
                            <td class="p6 bg-gray"></td>
                            <td class="p6 bg-gray"></td>
                            <td class="p6 c">{{ $extra['curso'] ?? '' }}</td>
                            <td class="p6 c text-justify">{{ $extra['aplicacion'] ?? '' }}</td>
                        </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    {{-- ================= FIRMAS ================= --}}
    <table class="tbl-firmas t9">
        <tr>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Nombre y Firma</span><br>
                <span style="font-size:8.5pt; color:#555;">Colaborador Evaluado</span>
            </td>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Nombre y Firma</span><br>
                <span style="font-size:8.5pt; color:#555;">Jefe Inmediato</span>
            </td>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Nombre y Firma</span><br>
                <span style="font-size:8.5pt; color:#555;">Recursos Humanos</span>
            </td>
        </tr>
    </table>

</main>
</body>
</html>