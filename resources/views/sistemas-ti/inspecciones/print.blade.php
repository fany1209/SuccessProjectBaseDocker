<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cédula Técnica - {{ $inspection->folio }}</title>
    <style>
        @page { margin: 15px; }
        html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; background: #fff; }
        .print-tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .print-tbl td, .print-tbl th { border: 1px solid #000; padding: 4px; word-wrap: break-word; }
        .bg-gray { background: #f2f2f2 !important; font-weight: bold; }
        .th-green { background:#92D050 !important; font-weight:bold; color:#000; }
        .c { text-align: center; }
        .header-table { width:100%; border-collapse:collapse; border:1px solid #000; margin-bottom: 15px; }
        .header-table td { border:1px solid #000; vertical-align: middle; }
    </style>
</head>
<body onload="window.print()">

    <!-- 1. ENCABEZADO INSTITUCIONAL -->
    <table class="header-table">
        <tr>
            <td rowspan="2" style="width:20%; text-align:center; padding: 5px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:50px; max-width:100%; object-fit:contain;" onerror="this.style.display='none'">
            </td>
            <td colspan="3" style="width:60%; text-align:center; font-weight:bold; font-size:14pt; padding:10px; !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                CÉDULA TÉCNICA DE REVISIÓN Y DIAGNÓSTICO
            </td>
            <td style="width:20%; text-align:center; font-size:9pt; padding:5px;">
                <b>Código:</b><br> STI-FOR-01
            </td>
        </tr>
        <tr>
            <td style="width:20%; text-align:center; font-size:9pt; padding:4px; height: 35px;">
                <b>Fecha de elaboración:</b><br> 11-Ago-2026
            </td>
            <td style="width:20%; text-align:center; font-size:9pt; padding:4px;">
                <b>Fecha de actualización:</b><br> --
            </td>
            <td style="width:20%; text-align:center; font-size:9pt; padding:4px;">
                <b>Versión:</b><br> 01
            </td>
            <td style="width:20%; text-align:center; font-size:9pt; padding:4px;">
                <b>Pág.</b> 1 de 1
            </td>
        </tr>
    </table>

    <!-- 2. DATOS DEL EQUIPO -->
    <table class="print-tbl" style="margin-bottom: 15px;">
        <tr>
            <td class="bg-gray" style="width: 20%;">Folio:</td>
            <td style="width: 30%; color: red; font-weight: bold;">{{ $inspection->folio }}</td>
            <td class="bg-gray" style="width: 20%;">Fecha:</td>
            <td style="width: 30%;">{{ \Carbon\Carbon::parse($inspection->date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="bg-gray">Marca:</td>
            <td>{{ $inspection->brand ?: 'N/A' }}</td>
            <td class="bg-gray">Modelo:</td>
            <td>{{ $inspection->model ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bg-gray">Número de Serie:</td>
            <td>{{ $inspection->serial_number ?: 'N/A' }}</td>
            <td class="bg-gray">Ubicación:</td>
            <td>{{ $inspection->location ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bg-gray">Área / Responsable:</td>
            <td colspan="3">{{ $inspection->area ?: 'N/A' }}</td>
        </tr>
    </table>

    <!-- 3. CHECKLIST -->
    <table class="print-tbl" style="margin-bottom: 15px;">
        <tr>
            <td class="th-green c" style="width: 55%;">Requisito / Punto de Inspección</td>
            <td class="th-green c" style="width: 15%;">Cumple</td>
            <td class="th-green c" style="width: 15%;">No Cumple</td>
            <td class="th-green c" style="width: 15%;">No Aplica</td>
        </tr>
        <tr>
            <td>Limpieza física exterior e interior libre de polvo</td>
            <td class="c font-bold">{{ $inspection->req1 === 'cumple' ? 'X' : '' }}</td>
            <td class="c font-bold">{{ $inspection->req1 === 'nocumple' ? 'X' : '' }}</td>
            <td class="c font-bold">{{ $inspection->req1 === 'na' ? 'X' : '' }}</td>
        </tr>
        <tr>
            <td>Estado óptimo de periféricos (Teclado, Mouse, Pantalla)</td>
            <td class="c font-bold">{{ $inspection->req2 === 'cumple' ? 'X' : '' }}</td>
            <td class="c font-bold">{{ $inspection->req2 === 'nocumple' ? 'X' : '' }}</td>
            <td class="c font-bold">{{ $inspection->req2 === 'na' ? 'X' : '' }}</td>
        </tr>
        <tr>
            <td>Cargador, cables de alimentación y conectores en buen estado</td>
            <td class="c font-bold">{{ $inspection->req3 === 'cumple' ? 'X' : '' }}</td>
            <td class="c font-bold">{{ $inspection->req3 === 'nocumple' ? 'X' : '' }}</td>
            <td class="c font-bold">{{ $inspection->req3 === 'na' ? 'X' : '' }}</td>
        </tr>
        <tr>
            <td>Funcionamiento correcto del Sistema Operativo y Software</td>
            <td class="c font-bold">{{ $inspection->req4 === 'cumple' ? 'X' : '' }}</td>
            <td class="c font-bold">{{ $inspection->req4 === 'nocumple' ? 'X' : '' }}</td>
            <td class="c font-bold">{{ $inspection->req4 === 'na' ? 'X' : '' }}</td>
        </tr>
        <tr>
            <td>Antivirus actualizado y activo</td>
            <td class="c font-bold">{{ $inspection->req5 === 'cumple' ? 'X' : '' }}</td>
            <td class="c font-bold">{{ $inspection->req5 === 'nocumple' ? 'X' : '' }}</td>
            <td class="c font-bold">{{ $inspection->req5 === 'na' ? 'X' : '' }}</td>
        </tr>
    </table>

    <!-- 4. OBSERVACIONES -->
    <table class="print-tbl" style="margin-bottom: 30px;">
        <tr>
            <td class="th-green" style="padding: 6px;">Observaciones / Hallazgos:</td>
        </tr>
        <tr>
            <td style="height: 80px; vertical-align: top;">{{ $inspection->observations }}</td>
        </tr>
    </table>

    <!-- 5. FIRMAS -->
    <table style="width: 100%; text-align: center; margin-top: 50px;">
        <tr>
            <td style="width: 50%;">
                <div style="border-top: 1px solid #000; width: 220px; margin: 0 auto; padding-top: 5px; font-weight: bold; font-size: 10pt;">
                    Firma del Inspector / Técnico
                </div>
            </td>
            <td style="width: 50%;">
                <div style="border-top: 1px solid #000; width: 220px; margin: 0 auto; padding-top: 5px; font-weight: bold; font-size: 10pt;">
                    Firma de Conformidad (Usuario)
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
