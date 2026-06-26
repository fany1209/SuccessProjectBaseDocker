<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluación del Desempeño - {{ $nombre_evaluado ?? 'General' }}</title>
    <style>
        @page {
            /* Márgenes muy reducidos para estirar el documento horizontal y verticalmente */
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
        
        .tbl-firmas { width: 100%; border-collapse: collapse; margin-top: 30px; page-break-inside: avoid; }
        .tbl-firmas td { width: 50%; text-align: center; vertical-align: bottom; border: none; }
        .linea-firma { width: 75%; margin: 0 auto; border-bottom: 1px solid #000; padding-top: 50px; margin-bottom: 5px; }

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
                EVALUACIÓN DEL DESEMPEÑO
            </td>

            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
                <b>Código:</b><br> SSS-FOR-REH-09
            </td>
        </tr>

        <tr>
            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; height: 30px; vertical-align: middle;">
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

    <table class="tbl b1 t9" style="margin-bottom: 10px;">
        <tr>
            <td class="p6 bg-gray" style="width:18%;">Nombre del evaluado:</td>
            <td class="p6" style="width:32%;">{{ $nombre_evaluado ?? '' }}</td>
            <td class="p6 bg-gray" style="width:18%;">Fecha de evaluación:</td>
            <td class="p6" style="width:32%;">{{ $fecha_evaluacion ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Área:</td>
            <td>{{ $area ?? '' }}</td>
            <td class="p6 bg-gray">Puesto:</td>
            <td>{{ $puesto_evaluado ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Nombre del evaluador:</td>
            <td>{{ $nombre_evaluador ?? '' }}</td>
            <td class="p6 bg-gray">Fecha de ingreso:</td>
            <td>{{ $fecha_ingreso ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Puesto:</td>
            <td>{{ $puesto_evaluador ?? '' }}</td>
            <td class="p6 bg-gray">Tipo de evaluación:</td>
            <td>
                <span style="margin-right: 15px; display: inline-block;">
                    Semestral <span style="border: 1px solid #000; border-radius: 50%; display: inline-block; width: 10px; height: 10px; vertical-align: middle; margin-left: 2px;"></span>
                </span>
                <span style="display: inline-block;">
                    Anual <span style="border: 1px solid #000; border-radius: 50%; display: inline-block; width: 10px; height: 10px; vertical-align: middle; margin-left: 2px;"></span>
                </span>
            </td>
        </tr>
    </table>

    <table class="tbl b1 t9">
        <thead>
            <tr class="th-green c">
                <th class="p4" style="width: 64%; text-align: left; padding-left: 6px;">ÁREA DEL DESEMPEÑO</th>
                <th class="p4" style="width: 6%;">4</th>
                <th class="p4" style="width: 6%;">3</th>
                <th class="p4" style="width: 6%;">2</th>
                <th class="p4" style="width: 6%;">1</th>
                <th class="p4" style="width: 12%;">PUNTAJE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="p6">Identifica de manera precisa las actividades a realizar</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Establece y acuerda prioridades de las actividades</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Define los procesos de solución para resolver algún contratiempo</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Define y acuerda quién debe estar involucrado en la solución de problemas</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Identifica y documenta de manera precisa la Causa Raíz, las acciones que deben tomar al hacer las correcciones de los problemas y eliminarlos.</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Termina su trabajo oportunamente</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Cumple con las tareas que se le encomiendan</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Realiza un volumen adecuado de trabajo</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Tiene los objetivos de la empresa claros</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Es constante su esfuerzo diario</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Tiene indicadores de medición</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Tiene velocidad de reacción en determinada circunstancia</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Tiene capacidad de reacción en determinada circunstancia</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            
            <tr class="bg-gray">
                <td colspan="6" class="p6" style="letter-spacing: 1px;">HABILIDADES FUNCIONALES</td>
            </tr>
            
            <tr>
                <td class="p6">Elaboración de documentación en general (formatos, procedimientos, etc.)</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Adherencia con procedimientos e instructivos</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Cumplimiento del RIT</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Trabaja en equipo</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Tiene capacidad de adaptación</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Respeto por cadena de mando</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Entrega oportuna y alineada en tiempos de trabajo</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Control de estrés</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Capacidad de negociación</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Comunicación asertiva</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Innova y crea</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Tiene iniciativa</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Toma decisiones</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Realiza la mejora continua</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Brinda motivación</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Es empático</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Contribuye a la organización y el crecimiento personal</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Sentido de pertenencia</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">No tiene problemas a la hora de acatar ordenes</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Tiene disposición en realizar cualquier actividad</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Volumen de trabajo útil y rapidez en la ejecución del mismo</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Entrega de trabajo en tiempo y forma</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Puede trabajar sin supervisión frecuente</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Entrega trabajo de calidad</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Que tan frecuente comete errores en su trabajo</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Utiliza los recursos de la mejor manera</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Control de emociones , comportamientos bajo control, incluso en situaciones de mucha presión</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Comportamiento ético</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Profesionalismo</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
            <tr>
                <td class="p6">Aprendizaje de errores</td>
                <td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td><td class="c"></td>
            </tr>
        </tbody>
    </table>

    <table class="tbl-firmas t9">
        <tr>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Nombre y firma del empleado</span>
            </td>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Nombre y firma del evaluador</span>
            </td>
        </tr>
    </table>

</body>
</html>