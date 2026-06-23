<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Minuta - SSS-FOR-REH-10</title>
    <style>
    @page { margin: 130px 24px 40px 24px; }
    
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #000; }
    
    .w-full { width: 100%; }
    .tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .b1 td, .b1 th { border: 1px solid #000; padding: 4px; }
    .c { text-align: center; }
    .l { text-align: left; }
    .bg-gray { background: #D9D9D9; font-weight: bold; }
    .th-green { background: #92D050; font-weight: bold; }
    
    header {
        position: fixed;
        top: -110px; 
        left: 0; right: 0;
        height: 110px;
    }

    main table:first-child {
        margin-top: 0px !important;
    }

    .logo { height: 55px; }
    
    .section-title {
        background: #E2EFDA;
        font-weight: bold;
        padding: 4px;
        border: 1px solid #000;
        margin-top: 10px;
        font-size: 9pt;
        text-align: center;
    }
</style>
</head>
<body>

<header>
<table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
  <tr>
    <td style="width:22%; text-align:center; border:1px solid #000;">
      <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
    </td>
    <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
      <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
        MINUTA
      </div>

      <table style="width:100%; border-collapse:collapse; font-size:9pt;">
            <tr>
                <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
                    <b>Fecha de elaboración:</b><br>
                        30-Enero-2023
                </td>
                <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
                    <b>Fecha de actualización:</b>--<br>
                </td>
                <td style="width:24%; padding:4px; text-align:center;">
                    <b>Versión:</b>00<br>
                </td>
            </tr>
        </table>
        </td>

        <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
            <b>Código:</b><br>
                SSS-FOR-REH-10
        </div>
            <div style="padding:10px; text-align:center;">
                Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
            </div>
        </td>
    </tr>
</table>
</header>

<main>
    {{-- ================= DATOS DE LA REUNIÓN ================= --}}
    <table class="tbl b1" style="margin-top: 5px;">
        <tr>
            <td class="th-green" style="width: 12%;">Fecha:</td>
            <td style="width: 38%;">{{ $fecha }}</td>
            <td class="th-green" style="width: 12%;">Hora:</td>
            <td style="width: 38%;">{{ $hora }}</td>
        </tr>
        <tr>
            <td class="th-green">Lugar:</td>
            <td colspan="3">{{ $lugar }}</td>
        </tr>
        <tr>
            <td class="th-green">Tema:</td>
            <td colspan="3">{{ $tema_general }}</td>
        </tr>
        <tr>
            <td class="th-green">Ponente:</td>
            <td colspan="3">{{ $ponente }}</td>
        </tr>
        <tr>
            <td class="th-green">Elaboró:</td>
            <td colspan="3">{{ $creado_por }}</td>
        </tr>
    </table>

    {{-- ================= SECCIÓN ASISTENTES ================= --}}
    <div class="section-title">Asistentes</div>
    <table class="tbl b1">
        <thead>
            <tr class="th-green c">
                <th style="width: 40%;">Nombre</th>
                <th style="width: 35%;">Departamento</th>
                <th style="width: 25%;">Firma</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $asistentes_list = array_filter(explode(', ', $asistente_nombre ?? ''));
                $deptos_list = array_filter(explode(', ', $asistente_departamento ?? ''));
            @endphp
            @forelse($asistentes_list as $index => $nombre)
                <tr>
                    <td>{{ $nombre }}</td>
                    <td>{{ $deptos_list[$index] ?? '' }}</td>
                    <td style="height: 30px;"></td>
                </tr>
            @empty
                @for($i=0; $i<6; $i++)
                    <tr style="height: 25px;"><td></td><td></td><td></td></tr>
                @endfor
            @endforelse
        </tbody>
    </table>

    {{-- ================= TABLA SEGUIMIENTO ================= --}}
    <div class="section-title">Temas y Acuerdos</div>
    <table class="tbl b1">
        <thead>
            <tr class="th-green c" style="font-size: 8pt;">
                <th style="width: 20%;">Tema tratado</th>
                <th style="width: 20%;">Acuerdo</th>
                <th style="width: 15%;">Responsable</th>
                <th style="width: 15%;">Fecha Comp.</th> <th style="width: 15%;">Fecha cierre</th>
                <th style="width: 15%;">Estatus</th>
            </tr>
        </thead>
        <tbody>
            @php
                $temas = explode(' | ', $tema_tratado ?? '');
                $acuerdos = explode(' | ', $acuerdo ?? '');
                $responsables = explode(', ', $responsable ?? '');
                $fechas_comp = explode(', ', $fecha_compromiso ?? ''); 
                $fechas = explode(', ', $fecha_cierre ?? '');
                $estatus_list = explode(', ', $estatus ?? '');
            @endphp

            @forelse($acuerdos as $i => $acc)
                @if(!empty($acc))
                <tr style="font-size: 8pt;">
                    <td>{{ $temas[$i] ?? '' }}</td>
                    <td>{{ $acc }}</td>
                    <td class="c">{{ $responsables[$i] ?? '' }}</td>
                    <td class="c">{{ $fechas_comp[$i] ?? '' }}</td>
                    <td class="c">{{ $fechas[$i] ?? '' }}</td>
                    <td class="c">{{ $estatus_list[$i] ?? '' }}</td>
                </tr>
                @endif
            @empty
                @for($i=0; $i<6; $i++)
                    <tr style="height: 30px;">
                        <td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                @endfor
            @endforelse
        </tbody>
    </table>
</main>

</body>
</html>