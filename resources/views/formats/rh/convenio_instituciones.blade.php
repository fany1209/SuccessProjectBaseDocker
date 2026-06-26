<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Información de Convenio con Instituciones - {{ $escuela ?? 'General' }}</title>
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

        tr, td, th {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <!-- 1. ENCABEZADO INSTITUCIONAL -->
    <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed; margin-bottom: 15px;">
        <tr>
            <!-- LOGO DE LA EMPRESA -->
            <td rowspan="2" style="width:20%; text-align:center; border:1px solid #000; padding: 5px; vertical-align: middle;">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
            </td>

            <!-- TITULO DEL FORMATO -->
            <td colspan="3" style="width:60%; text-align:center; font-weight:bold; font-size:14pt; border:1px solid #000; padding:10px; vertical-align: middle;">
                INFORMACIÓN DE CONVENIO CON INSTITUCIONES
            </td>

            <!-- CODIGO DEL DOCUMENTO -->
            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
                <b>Código:</b><br> SSS-FOR-REH-12
            </td>
        </tr>

        <tr>
            <!-- FECHAS Y CONTROL DE VERSIONES -->
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
                <b>Pág.</b> 1 de 1
            </td>
        </tr>
    </table>

    <!-- 2. CUERPO DEL CONVENIO -->
    <table class="tbl b1 t9">
        <tr>
            <td class="p6 bg-gray" style="width: 20%;">N° de convenio:</td>
            <td class="p6" style="width: 30%;">{{ $no_convenio ?? '' }}</td>
            <td class="p6 bg-gray" style="width: 20%;">Teléfono:</td>
            <td class="p6" style="width: 30%;">{{ $telefono ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Escuela:</td>
            <td class="p6" style="font-weight: bold;">{{ $escuela ?? '' }}</td>
            <td class="p6 bg-gray">Subdirectora de planeación y Vinculación:</td>
            <td class="p6">{{ $subdirectora ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Domicilio:</td>
            <td class="p6">{{ $domicilio ?? '' }}</td>
            <td class="p6 bg-gray">Jefe del departamento de Gestión tecnológica y vinculación:</td>
            <td class="p6">{{ $jefe_departamento ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Representante:</td>
            <td class="p6">
                {{ $representante ?? '' }}<br>
                <span style="font-size: 8.5pt; color: #555;"><b>Puesto:</b> Director</span>
            </td>
            <td class="p6 bg-gray" rowspan="3" style="vertical-align: top;">Alcances del convenio:</td>
            <td class="p6" style="vertical-align: top;">
                • Realizar actividades de acuerdo al Programa de residencias Profesionales
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Tiempo de residencias:</td>
            <td class="p6">{{ $tiempo_residencias ?? '' }}</td>
            <td class="p6" style="vertical-align: top;">
                • Complementar la formación académica del residente y dar aplicación práctica a la formación teórica
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Fecha del convenio:</td>
            <td class="p6">{{ $fecha_convenio ? \Carbon\Carbon::parse($fecha_convenio)->format('d/m/Y') : '' }}</td>
            <td class="p6" style="vertical-align: top;">
                • Mantener la confidencialidad, reserva de la información y protección de datos personales y de la empresa
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Tiempo de validez del convenio:</td>
            <td class="p6">{{ $validez_convenio ?? '' }}</td>
            <td class="p6 bg-gray">Clave de la escuela y/o institución:</td>
            <td class="p6">{{ $clave_escuela ?? '' }}</td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Representante de la empresa:</td>
            <td class="p6" colspan="3">
                {{ $representante_empresa ?? '' }}<br>
                <span style="font-size: 8.5pt; color: #555;"><b>Puesto:</b> Director</span>
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Persona responsable del resguardo del convenio por parte de la empresa:</td>
            <td class="p6" colspan="3">
                {{ $responsable_resguardo ?? '' }}<br>
                <span style="font-size: 8.5pt; color: #555;"><b>Puesto:</b> Líder del SGC</span>
            </td>
        </tr>
        <tr>
            <td class="p6 bg-gray">Consecutivo:</td>
            <td class="p6" colspan="3" style="font-weight: bold; color: #157347;">
                {{ $consecutivo ?? '' }}
            </td>
        </tr>
    </table>

</body>
</html>
