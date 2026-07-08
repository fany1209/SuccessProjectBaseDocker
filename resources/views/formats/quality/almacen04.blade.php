<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inspección de Almacén - SSS-FOR-CAL-04</title>
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
        .l { text-align: left; }
        .p4 { padding: 4px; }
        .p6 { padding: 6px; }
        .t9  { font-size: 9pt; }
        td { word-wrap: break-word; }
        .th-green { background:#92D050; font-weight:bold; color:#000; text-align: center; padding: 5px; }
        .bg-gray { background: #f2f2f2; font-weight: bold; }
        .text-justify { text-align: justify; }

        .cb {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
        }

        header {
            position: fixed;
            top: -120px;    
            left: 0; right: 0;
            height: 110px;  
            z-index: 10;
        }
        main { margin-top: 0; }
        tr { page-break-inside: avoid; }

        /* ====== CONTROL DE TAMAÑO FIJO PARA LAS IMÁGENES EN LA TABLA ====== */
        .img-pdf {
            display: block;
            margin: 0 auto;
            width: 80px;
            height: 80px;
            object-fit: cover; /* Evita que las fotos se deformen o estiren al encuadrar */
            border-radius: 4px;
        }
    </style>
</head>
<body>

{{-- ================= ENCABEZADO OFICIAL SSS-FOR-CAL-04 ================= --}}
<header>
    <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed;">
        <tr>
            <td rowspan="2" style="width:22%; text-align:center; border:1px solid #000; padding: 5px; vertical-align: middle;">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
            </td>

            <td colspan="3" style="width:58%; text-align:center; font-weight:bold; font-size:13pt; border:1px solid #000; padding:10px; vertical-align: middle;">
                INSPECCIÓN DE ALMACÉN
            </td>

            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
                <b>Código:</b><br> SSS-FOR-CAL-04
            </td>
        </tr>

        <tr>
            <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; height: 35px; vertical-align: middle;">
                <b>Fecha de elaboración:</b><br>30-Enero-2023
            </td>
            <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
                <b>Fecha de actualización:</b><br> --
            </td>
            <td style="width:19.4%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
                <b>Versión:</b>00
            </td>
            <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
                &nbsp;
            </td>
        </tr>
    </table>
</header>

<main>
    {{-- ================= INFO INSPECCIÓN ================= --}}
    <table class="tbl b1" style="font-size:9pt; margin-top:15px;">
        <tr class="th-green c">
            <td colspan="4">Información de la inspección</td>
        </tr>

        <tr>
            <td colspan="4" class="p4"><b>Fecha de inspección:</b> {{ $fecha_inspeccion ?? '' }}</td>
        </tr>

        <tr>
            <td colspan="4" class="p4"><b>Inspector:</b> {{ $inspector ?? '' }}</td>
        </tr>

        <tr>
            <td colspan="2" class="p4">
                <b>Inspección</b><br>

                @php
                    $tv = (string)($turno ?? '');
                    $map = ['matutina'=>'1','vespertina'=>'2','nocturna'=>'3'];
                    $tv = $map[$tv] ?? $tv;

                    $turnoOpts = [
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        'mixto' => 'Mixto',
                    ];
                @endphp

                @foreach($turnoOpts as $val => $label)
                    {{ $label }}
                    <span class="cb">{{ $tv === (string)$val ? '☑' : '☐' }}</span><br>
                @endforeach
            </td>

            <td colspan="2" class="p4">
                <b>Hora:</b> {{ $hora_turno ?? '' }}
            </td>
        </tr>

        <tr>
            <td colspan="4" class="p4">
                <b>Área:</b>
                Nave 1 <span class="cb">{{ !empty($area_nave1) ? '☑' : '☐' }}</span>
                &nbsp;&nbsp;
                Nave 2 <span class="cb">{{ !empty($area_nave2) ? '☑' : '☐' }}</span>
                &nbsp;&nbsp;
                Otro <span class="cb">{{ !empty($area_otro_flag) ? '☑' : '☐' }}</span>

                @if(!empty($area_otro_flag) && !empty($area_otro))
                    <br><b>Especifique:</b> {{ $area_otro }}
                @endif
            </td>
        </tr>

        <tr>
            <td colspan="4" class="p4"><b>Responsable del área:</b> {{ $responsable ?? '' }}</td>
        </tr>
    </table>

    {{-- ================= OBSERVACIONES ================= --}}
    <table class="tbl b1" style="font-size:9pt; margin-top:15px;">
        <thead>
            <tr class="c th-green">
                <th style="width:15%;">Observación</th>
                <th style="width:28%;">Evidencia</th>
                <th style="width:17%;">Ubicación</th>
                <th colspan="2" style="width:12%;">Revisión</th>
                <th style="width:13%;">Fecha de corrección</th>
                <th style="width:15%;">Evidencia de corrección</th>
            </tr>
            <tr class="c th-green">
                <th colspan="3"></th>
                <th style="width:6%;">Cumple</th>
                <th style="width:6%;">No cumple</th>
                <th colspan="2"></th>
            </tr>
        </thead>

        <tbody>
            @forelse($obs_rows as $r)
            <tr>
                <td class="p6 l" style="vertical-align: middle;">{{ $r['name'] ?? '' }}</td>

                <td class="p6 c" style="vertical-align: middle;">
                    @if(!empty($r['evidencia_img']))
                        <img src="{{ $r['evidencia_img'] }}" class="img-pdf">
                    @endif
                </td>

                <td class="p6 l" style="vertical-align: middle;">{{ $r['ubicacion'] ?? '' }}</td>
                <td class="p6 c cb" style="vertical-align: middle;">{{ !empty($r['cumple']) ? '☑' : '☐' }}</td>
                <td class="p6 c cb" style="vertical-align: middle;">{{ !empty($r['no_cumple']) ? '☑' : '☐' }}</td>
                <td class="p6 c" style="vertical-align: middle;">{{ $r['fecha'] ?? '' }}</td>

                <td class="p6 c" style="vertical-align: middle;">
                    @if(!empty($r['ev_corr_img']))
                        <img src="{{ $r['ev_corr_img'] }}" class="img-pdf">
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="p6 c">Sin observaciones.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ================= COMENTARIOS ================= --}}
    <div class="title" style="margin-top:15px;">Comentarios de almacén</div>
    <table class="tbl b1" style="font-size:10pt;">
        <tr>
            <td class="p6" style="height:80px; vertical-align: top;">{{ $comentarios ?? '' }}</td>
        </tr>
    </table>

    <div class="title" style="margin-top:15px;">Comentarios de calidad</div>
    <table class="tbl b1" style="font-size:10pt;">
        <tr>
            <td class="p6" style="height:80px; vertical-align: top;">{{ $comentarios_q ?? '' }}</td>
        </tr>
    </table>
</main>

</body>
</html>