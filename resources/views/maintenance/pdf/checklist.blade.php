<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checklist {{ $record->code }}</title>
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
        .l { text-align: left; }
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

        .bg-gray { background: #f2f2f2; font-weight: bold; }
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
        .tbl-firmas td { width: 50%; text-align: center; vertical-align: bottom; border: none; }
    </style>
</head>
<body>

{{-- ================= ENCABEZADO OFICIAL ESTANDARIZADO ================= --}}
<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed;">
    <tr>
      <td rowspan="2" style="width:22%; text-align:center; border:1px solid #000; padding: 5px; vertical-align: middle;">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
        @else
            <h2>LOGO</h2>
        @endif
      </td>

      <td colspan="3" style="width:58%; text-align:center; font-weight:bold; font-size:14pt; border:1px solid #000; padding:10px; vertical-align: middle; text-transform: uppercase;">
        CHECKLIST DE {{ $record->maintenancePlan->name ?? 'MANTENIMIENTO' }}
      </td>

      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
        <b>Código:</b><br> {{ $record->code }}
      </td>
    </tr>

    <tr>
      <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; height: 35px; vertical-align: middle;">
        <b>Fecha de elaboración:</b><br> {{ now()->format('d/m/Y') }}
      </td>
      <td style="width:19.3%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
        <b>Fecha Programada:</b><br> {{ $record->scheduled_date->format('d/m/Y') }}
      </td>
      <td style="width:19.4%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
        <b>Versión:</b><br> 01
      </td>
      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
        <b>Tipo:</b><br> {{ ($record->maintenancePlan->type ?? '') == 'frequent' ? 'Frecuente' : 'Profundo' }}
      </td>
    </tr>
  </table>
</header>

<main>
    {{-- ================= IDENTIFICACIÓN DEL EQUIPO ================= --}}
    <div class="section" style="margin-top: 10px;">
        <table class="tbl b1 t9">
            <tr>
                <td colspan="4" class="th-green c p6" style="font-size:10pt;">
                    DATOS GENERALES DEL EQUIPO
                </td>
            </tr>
            <tr>
                <td class="p6 bg-gray" style="width:20%;">Nombre del Equipo:</td>
                <td class="p6" colspan="3">
                    {{ $record->equipment->name ?? 'N/A' }}
                </td>
            </tr>
            <tr>
                <td class="p6 bg-gray" style="width:20%;">Código de Equipo:</td>
                <td class="p6" style="width:30%;">{{ $record->equipment->code ?? 'N/A' }}</td>
                <td class="p6 bg-gray" style="width:20%;">Área / Depto:</td>
                <td class="p6" style="width:30%;">{{ $record->equipment->area->name ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- ================= PUNTOS A REVISAR ================= --}}
    <div class="section">
        <table class="tbl b1 t9">
            <thead>
                <tr>
                    <th colspan="4" class="th-green c p6" style="font-size:10pt;">
                        PUNTOS CLAVE A REVISAR (CHECKLIST)
                    </th>
                </tr>
                <tr class="bg-gray c">
                    <th class="p4" style="width: 5%;">#</th>
                    <th class="p4" style="width: 55%;">Actividad / Punto de revisión</th>
                    <th class="p4" style="width: 15%;">Estado</th>
                    <th class="p4" style="width: 25%;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
            @if($record->maintenancePlan && $record->maintenancePlan->checklistItems->count() > 0)
                @foreach($record->maintenancePlan->checklistItems as $index => $item)
                    <tr>
                        <td class="p6 c">{{ $index + 1 }}</td>
                        <td class="p6 text-justify">
                            {{ $item->description }}
                        </td>
                        <td class="p6 c">
                            <span style="font-family: DejaVu Sans, Arial, sans-serif;">&#9744;</span> Bien 
                            &nbsp;&nbsp; 
                            <span style="font-family: DejaVu Sans, Arial, sans-serif;">&#9744;</span> Mal
                        </td>
                        <td class="p6">&nbsp;</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="p6 c">1</td>
                    <td class="p6 text-justify">Revisión general y lubricación de componentes</td>
                    <td class="p6 c">
                        <span style="font-family: DejaVu Sans, Arial, sans-serif;">&#9744;</span> Bien 
                        &nbsp;&nbsp; 
                        <span style="font-family: DejaVu Sans, Arial, sans-serif;">&#9744;</span> Mal
                    </td>
                    <td class="p6">&nbsp;</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    {{-- ================= NOTAS ADICIONALES ================= --}}
    <div class="section" style="margin-top: 15px;">
        <table class="tbl b1 t9">
            <tr>
                <td class="th-green c p6" style="font-size:10pt;">
                    COMENTARIOS GENERALES
                </td>
            </tr>
            <tr>
                <td style="height: 80px; vertical-align: top;" class="p6">
                    <!-- Espacio en blanco para que escriban -->
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= BLOQUE DE FIRMAS AUTORIZADAS ================= --}}
    <table class="tbl-firmas t9">
        <tr>
            <td colspan="2" style="font-weight:bold; text-align:left; padding-bottom:10px; font-size:10pt;">
                De conformidad:
            </td>
        </tr>
        <tr>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Nombre y Firma</span><br>
                <span style="font-size:8.5pt; color:#555;">Técnico Responsable</span>
            </td>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Nombre y Firma</span><br>
                <span style="font-size:8.5pt; color:#555;">Supervisor de Área</span>
            </td>
        </tr>
    </table>

</main>
</body>
</html>
