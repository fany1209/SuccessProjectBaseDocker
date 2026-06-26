<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Vacaciones - {{ $vacation->employee_name ?? 'General' }}</title>
    <style>
        @page {
            margin: 140px 24px 80px 24px;
        }

        html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
        .w-full { width: 100%; }
        .tbl    { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 10px; }
        .b1 td, .b1 th { border: 1px solid #000; }
        .c { text-align: center; }
        .r { text-align: right; }
        .p4 { padding: 4px; }
        .p6 { padding: 6px; }
        .t12 { font-size: 12pt; font-weight: bold; }
        .t9  { font-size: 9pt; }
        .title { text-align:center; font-weight: bold; margin: 10px 0 6px; }
        td { word-wrap: break-word; }
        .th-green { background:#92D050; font-weight:bold; color:#000; }
        .ph-img { height: 50px; border: 1px solid #000; margin: 2px 0; }
        .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }

        header {
            position: fixed;
            top: -120px;    
            left: 0; right: 0;
            height: 110px;  
            z-index: 10;
        }
        main { margin-top: 0; }

        .bg-gray { background: #f2f2f2; font-weight: bold; width: 160px; }
        .text-justify { text-align: justify; }
        .linea-firma { width: 80%; margin: 0 auto; border-bottom: 1px solid #000; padding-top: 50px; margin-bottom: 5px; }

        .section {
            page-break-inside: auto !important;
            break-inside: auto !important;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
        }

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
        SOLICITUD DE VACACIONES
      </td>

      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
        <b>Código:</b><br> SSS-FOR-REH-08
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

    {{-- ================= FECHAS DE CONTROL DE SOLICITUD ================= --}}
    <div class="section" style="margin-bottom: 10px;">
        <table class="tbl t9">
            <tr>
                <td style="width: 60%;"></td>
                <td class="p4 bg-gray r" style="border: 1px solid #000; width: 20%;">Fecha Solicitud:</td>
                <td class="p4 c" style="border: 1px solid #000; width: 20%;">{{ $vacation->request_date ?? date('d/m/Y') }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="p4 bg-gray r" style="border: 1px solid #000;">Fecha Aprobación:</td>
                <td class="p4 c" style="border: 1px solid #000;">{{ $vacation->approval_date ?? '' }}</td>
            </tr>
        </table>
    </div>

    {{-- ================= IDENTIFICACIÓN DEL TRABAJADOR ================= --}}
    <div class="section">
        <table class="tbl b1 t9">
            <tr>
                <td colspan="4" class="th-green c p6" style="font-size:10pt;">
                    DATOS GENERALES DEL TRABAJADOR
                </td>
            </tr>
            <tr>
                <td class="p6 bg-gray" style="width:20%;">Nombre del Empleado:</td>
                <td class="p6" colspan="3">
                    {{ $vacation->employee_name ?? '' }}
                </td>
            </tr>
            <tr>
                <td class="p6 bg-gray" style="width:20%;">Área / Depto:</td>
                <td class="p6" style="width:30%;">{{ $vacation->department ?? '' }}</td>
                <td class="p6 bg-gray" style="width:20%;">Puesto:</td>
                <td class="p6" style="width:30%;">{{ $vacation->position ?? '' }}</td>
            </tr>
        </table>
    </div>

    {{-- ================= CONTROL DE DÍAS POR ANTIGÜEDAD ================= --}}
    <div class="section">
        <table class="tbl b1 t9">
            <tr>
                <td colspan="4" class="th-green c p6" style="font-size:10pt;">
                    CONTROL Y SEGUIMIENTO DE DÍAS
                </td>
            </tr>
            <tr>
                <td class="p6 bg-gray" style="width:25%;">Antigüedad:</td>
                <td class="p6 c" style="width:25%;">{{ $vacation->seniority_years ?? '' }} años</td>
                <td class="p6 bg-gray" style="width:25%;">Días Correspondientes:</td>
                <td class="p6 c" style="width:25%;">{{ $vacation->days_earned ?? '' }}</td>
            </tr>
            <tr>
                <td class="p6 bg-gray">Días Disponibles:</td>
                <td class="p6 c">{{ $vacation->days_available ?? '' }}</td>
                <td class="p6 bg-gray">N° de Días Requeridos:</td>
                <td class="p6 c">{{ $vacation->days_requested ?? '' }}</td>
            </tr>
            <tr>
                <td class="p6 bg-gray">N° de Días Otorgados:</td>
                <td class="p6 c">{{ $vacation->days_granted ?? '' }}</td>
                <td class="p6 bg-gray">N° de Días Restantes:</td>
                <td class="p6 c">{{ $vacation->days_remaining ?? '' }}</td>
            </tr>
        </table>
    </div>

    {{-- ================= PERIODO DE VACACIONES SOLICITADAS ================= --}}
    <div class="section">
        <table class="tbl b1 t9">
            <tr>
                <td colspan="3" class="th-green c p6" style="font-size:10pt;">
                    PERIODO DE VACACIONES AUTORIZADAS
                </td>
            </tr>
            <tr class="bg-gray c">
                <td class="p4" style="width: 33.33%;">Fecha de Inicio</td>
                <td class="p4" style="width: 33.33%;">Fecha de Término</td>
                <td class="p4" style="width: 33.33%;">Fecha de Regreso a Labores</td>
            </tr>
            <tr class="c">
                <td class="p6">{{ $vacation->start_date ?? '___/___/______' }}</td>
                <td class="p6">{{ $vacation->end_date ?? '___/___/______' }}</td>
                <td class="p6">{{ $vacation->return_date ?? '___/___/______' }}</td>
            </tr>
        </table>
    </div>

    {{-- ================= JORNADA DE TRABAJO ================= --}}
    <div class="section">
        <table class="tbl b1 t9">
            <tr>
                <td colspan="2" class="th-green c p6" style="font-size:10pt;">
                    JORNADA DE TRABAJO ASIGNADA
                </td>
            </tr>
            <tr class="c">
                <td class="p6" style="width: 50%;">
                    Lunes a Viernes &nbsp; <input type="radio" {{ (isset($vacation->work_schedule) && $vacation->work_schedule == 'L-V') ? 'checked' : '' }}>
                </td>
                <td class="p6" style="width: 50%;">
                    Lunes a Sábado &nbsp; <input type="radio" {{ (isset($vacation->work_schedule) && $vacation->work_schedule == 'L-S') ? 'checked' : '' }}>
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= ACTIVIDADES PENDIENTES ================= --}}
    <div class="section">
        <table class="tbl b1 t9">
            <thead>
                <tr>
                    <th colspan="3" class="th-green c p6" style="font-size:10pt;">
                        ACTIVIDADES PENDIENTES A TOMAR EN CUENTA EN SU AUSENCIA
                    </th>
                </tr>
                <tr class="bg-gray c">
                    <th class="p4" style="width: 50%;">Actividades</th>
                    <th class="p4" style="width: 25%;">Responsable</th>
                    <th class="p4" style="width: 25%;">Firma de Enterado</th>
                </tr>
            </thead>
            <tbody>
            @if(isset($vacation->pending_tasks) && count($vacation->pending_tasks) > 0)
                @foreach($vacation->pending_tasks as $task)
                    <tr>
                        <td class="p6 text-justify">
                            {!! !empty($task->description) ? e($task->description) : '&nbsp;' !!}
                        </td>
                        <td class="p6 c">
                            {!! !empty($task->responsible_name) ? e($task->responsible_name) : '&nbsp;' !!}
                        </td>
                        <td class="p6">&nbsp;</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="p6">&nbsp;</td>
                    <td class="p6">&nbsp;</td>
                    <td class="p6">&nbsp;</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    {{-- ================= BLOQUE DE FIRMAS AUTORIZADAS ================= --}}
    <table class="tbl-firmas t9">
        <tr>
            <td colspan="3" style="font-weight:bold; text-align:left; padding-bottom:10px; font-size:10pt;">
                De conformidad:
            </td>
        </tr>
        <tr>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Nombre y Firma</span><br>
                <span style="font-size:8.5pt; color:#555;">Solicitante</span>
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

