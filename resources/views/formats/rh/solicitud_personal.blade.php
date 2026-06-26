<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Personal - {{ $nombre_puesto ?? 'General' }}</title>
    <style>
        @page {
            margin: 20px 20px 20px 20px;
        }

        html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; }
        .w-full { width: 100%; }
        .tbl    { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .b1 td, .b1 th { border: 1px solid #000; }
        .c { text-align: center; }
        .p4 { padding: 4px; }
        .p6 { padding: 6px; }
        .t12 { font-size: 12pt; font-weight: bold; }
        .t9  { font-size: 9pt; }
        td { word-wrap: break-word; }
        .th-green { background:#92D050; font-weight:bold; color:#000; }
        .bg-gray { background: #f2f2f2; font-weight: bold; }
        
        /* Círculos estilizados para simular los del formato */
        .circle-check {
            border: 1px solid #000; 
            border-radius: 50%; 
            display: inline-block; 
            width: 10px; 
            height: 10px; 
            vertical-align: middle; 
            margin-left: 2px;
            margin-right: 12px;
        }

        .tbl-firmas { width: 100%; border-collapse: collapse; margin-top: 30px; page-break-inside: avoid; }
        .tbl-firmas td { width: 33.33%; text-align: center; vertical-align: bottom; border: none; }
        .linea-firma { width: 80%; margin: 0 auto; border-bottom: 1px solid #000; padding-top: 50px; margin-bottom: 5px; }

        tr, td, th {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed; margin-bottom: 10px;">
        <tr>
            <td rowspan="2" style="width:20%; text-align:center; border:1px solid #000; padding: 5px; vertical-align: middle;">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
            </td>
            <td colspan="3" style="width:60%; text-align:center; font-weight:bold; font-size:14pt; border:1px solid #000; padding:10px; vertical-align: middle;">
                SOLICITUD DE PERSONAL
            </td>
            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
                <b>Código:</b><br> SSS-FOR-REH-11
            </td>
        </tr>
        <tr>
            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; height: 35px; vertical-align: middle;">
                <b>Fecha de elaboración:</b><br> 30-Enero-2023
            </td>
            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
                <b>Fecha de actualización:</b><br> --
            </td>
            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
                <b>Versión:</b><br> 00
            </td>
            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
                <b>Pág.</b> 1 de 2
            </td>
        </tr>
    </table>

    <p style="font-size: 9pt; margin-bottom: 10px; text-align: justify; font-style: italic;">
        Este formato de requerimiento de personal tiene como objeto obtener la información necesaria sobre la vacante y el perfil del candidato requerido para ocupar el cargo. 
    </p>

    <table class="tbl b1 t9" style="margin-bottom: 10px;">
        <tr>
            <td colspan="2" class="th-green p6">LA NECESIDAD DE PERSONAL SE GENERA POR: </td>
        </tr>
        <tr>
            <td class="p6 bg-gray" style="width: 25%;">Fecha deseable de incorporación: </td>
            <td class="p6" style="width: 75%;">{{ $fecha_incorporacion ? \Carbon\Carbon::parse($fecha_incorporacion)->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray" style="vertical-align: top;">Motivo del requerimiento: </td>
            <td class="p6">
                <div style="line-height: 22px;">
                    Creación de nuevo puesto <span class="circle-check"></span>
                    Nueva sucursal <span class="circle-check"></span>
                    Incremento de producción <span class="circle-check"></span>
                    Despido <span class="circle-check"></span><br> 
                    Creación de nuevo proceso <span class="circle-check"></span>
                    Renuncia <span class="circle-check"></span>
                    Temporal <span class="circle-check"></span>
                    Reorganización de funciones del área <span class="circle-check"></span><br> 
                    Por incapacidad: Maternidad <span class="circle-check"></span>
                    Enfermedad general <span class="circle-check"></span>
                    Riesgo de trabajo <span class="circle-check"></span><br> 
                    No pasó persona anterior prueba de 3 meses <span class="circle-check"></span>
                    Crecimiento de la empresa <span class="circle-check"></span>
                    Por proyecto <span class="circle-check"></span><br> 
                    Otro <span class="circle-check"></span> Especifique: <span style="border-bottom: 1px solid #000; display: inline-block; width: 60%;">&nbsp;{{ $especifique_motivo ?? '' }}</span> 
                </div>
            </td>
        </tr>
    </table>

    <table class="tbl b1 t9" style="margin-bottom: 10px;">
        <tr>
            <td colspan="4" class="th-green p6">INFORMACIÓN BÁSICA </td>
        </tr>
        <tr>
            <td class="p6 bg-gray" style="width: 20%;">Nombre del puesto: </td>
            <td class="p6" colspan="3">{{ $nombre_puesto ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray" style="width: 20%;">Nº de vacantes a cubrir: </td>
            <td class="p6" style="width: 30%;">{{ $vacantes ?? '' }}</td>
            <td class="p6 bg-gray" style="width: 25%;">Número de personas que supervisa: </td>
            <td class="p6" style="width: 25%;">{{ $personas_supervisa ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Dedicación: </td>
            <td class="p6" colspan="3">
                Tiempo completo <span class="circle-check"></span>
                Medio tiempo <span class="circle-check"></span>
                Otro <span class="circle-check"></span> Especifique: <span style="border-bottom: 1px solid #000; display: inline-block; width: 40%;">&nbsp;{{ $especifique_dedicacion ?? '' }}</span> 
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Días a laborar / Horario: </td>
            <td class="p6" colspan="3">
                <span style="font-weight: bold; margin-right: 5px;">Días:</span> 
                L-V <span class="circle-check"></span> 
                L-S <span class="circle-check"></span>
                <span style="font-weight: bold; margin-left: 15px; margin-right: 5px;">Horario:</span> 
                Diurno <span class="circle-check"></span> 
                Nocturno <span class="circle-check"></span> 
                Mixto <span class="circle-check"></span>
                Otro <span class="circle-check"></span> Especifique: <span style="border-bottom: 1px solid #000; display: inline-block; width: 25%;">&nbsp;{{ $especifique_horario ?? '' }}</span> 
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Modalidad de contratación: </td>
            <td class="p6" colspan="3">
                Empleado <span class="circle-check"></span>
                Practicante <span class="circle-check"></span> 
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray" colspan="4">Descripción breve de las funciones básicas del puesto </td>
        </tr>
        <tr>
            <td class="p6" colspan="4" style="height: 60px; vertical-align: top;">
                {!! nl2br(e($funciones_basicas ?? '')) !!}
            </td>
        </tr>
    </table>

    <table class="tbl b1 t9" style="margin-bottom: 10px;">
        <tr>
            <td colspan="4" class="th-green p6">PERFIL PROFESIONAL </td>
        </tr>
        <tr>
            <td class="p6 bg-gray" style="width: 20%;">Formación académica: </td>
            <td class="p6" colspan="3">
                Secundaria <span class="circle-check"></span>
                Técnica <span class="circle-check"></span>
                Universidad <span class="circle-check"></span>
                Maestría <span class="circle-check"></span>
                Doctorado <span class="circle-check"></span> 
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Especialidad: </td>
            <td class="p6" style="width: 30%;">{{ $especialidad ?? '' }}</td>
            <td class="p6 bg-gray" style="width: 20%;">Profesión: </td>
            <td class="p6" style="width: 30%;">{{ $profesion ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Experiencia requerida: </td>
            <td class="p6" colspan="3">
                Años: <span style="border-bottom: 1px solid #000; display: inline-block; width: 40px; text-align:center;">{{ $experiencia_anos ?? '' }}</span>&nbsp;&nbsp;&nbsp;&nbsp;
                Meses: <span style="border-bottom: 1px solid #000; display: inline-block; width: 40px; text-align:center;">{{ $experiencia_meses ?? '' }}</span> 
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Cargos desempeñados: </td>
            <td class="p6" colspan="3">{{ $cargos_desempenados ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray" colspan="4">Competencias personales: </td>
        </tr>
        <tr>
            <td class="p6" colspan="4">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border:none; padding: 4px; width:33.3%;">1. {{ $competencia_1 ?? '' }}</td>
                        <td style="border:none; padding: 4px; width:33.3%;">3. {{ $competencia_3 ?? '' }}</td>
                        <td style="border:none; padding: 4px; width:33.3%;">5. {{ $competencia_5 ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="border:none; padding: 4px;">2. {{ $competencia_2 ?? '' }}</td>
                        <td style="border:none; padding: 4px;">4. {{ $competencia_4 ?? '' }}</td>
                        <td style="border:none; padding: 4px;">6. {{ $competencia_6 ?? '' }}</td>
                    </tr>
                </table> 
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray" colspan="4">Conocimientos adicionales: </td>
        </tr>
        <tr>
            <td class="p6" colspan="4" style="line-height: 22px;">
                <span style="font-weight:bold; margin-right:5px;">Inglés:</span>
                Básico <span class="circle-check"></span> Intermedio <span class="circle-check"></span> Avanzado <span class="circle-check"></span>
                <span style="font-weight:bold; margin-left:25px; margin-right:5px;">Excel:</span>
                Básico <span class="circle-check"></span> Intermedio <span class="circle-check"></span> Avanzado <span class="circle-check"></span> 
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Sexo: </td>
            <td class="p6">
                F <span class="circle-check"></span> M <span class="circle-check"></span> Indistinto <span class="circle-check"></span> 
            </td>
            <td class="p6 bg-gray">Edad sugerida: </td>
            <td class="p6">{{ $edad_sugerida ?? '' }} años</td>
        </tr>
        <tr>
            <td class="p6 bg-gray" colspan="4">Comentarios: </td>
        </tr>
        <tr>
            <td class="p6" colspan="4" style="height: 50px; vertical-align: top;">
                {!! nl2br(e($comentarios ?? '')) !!}
            </td>
        </tr>
    </table>

    <table class="tbl-firmas t9">
        <tr>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Solicitante</span><br>
                <span style="font-size:8.5pt; color:#555;">Nombre y firma</span> 
            </td>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Revisado</span><br>
                <span style="font-size:8.5pt; color:#555;">Nombre y firma</span> 
            </td>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Autorizado</span><br>
                <span style="font-size:8.5pt; color:#555;">Nombre y firma</span> 
            </td>
        </tr>
    </table>

</body>
</html>
