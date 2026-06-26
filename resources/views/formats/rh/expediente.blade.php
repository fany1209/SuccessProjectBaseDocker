<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Descripción de Puesto - {{ $nombre_puesto ?? 'General' }}</title>
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

  /* Clases auxiliares para el cuerpo del formato */
  .bg-gray { background: #f2f2f2; font-weight: bold; width: 160px; }
  .text-justify { text-align: justify; }
  .linea-firma { width: 75%; margin: 0 auto; border-bottom: 1px solid #000; padding-top: 50px; margin-bottom: 5px; }

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

  /* Las firmas SÍ se quedan estrictamente unidas para que no aparezcan huérfanas */
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
  {{-- ================= ENCABEZADO OFICIAL ================= --}}
<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; table-layout: fixed;">
    <tr>
      <td rowspan="2" style="width:22%; text-align:center; border:1px solid #000; padding: 5px; vertical-align: middle;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>

      <td colspan="3" style="width:58%; text-align:center; font-weight:bold; font-size:14pt; border:1px solid #000; padding:10px; vertical-align: middle;">
        DESCRIPCIÓN DE PUESTO
      </td>

      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:5px; vertical-align: middle;">
        <b>Código:</b><br> SSS-FOR-REH-03
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
        <b>Versión:</b> 00
      </td>
      <td style="width:20%; border:1px solid #000; text-align:center; font-size:9pt; padding:4px; vertical-align: middle;">
        Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
      </td>
    </tr>
  </table>
</header>

<main>

    {{-- ================= IDENTIFICACIÓN DEL PUESTO ================= --}}
    <div class="section">
        <table class="tbl b1 t9">
            <tr>
                <td colspan="2" class="th-green c p6" style="font-size:10pt;">
                    IDENTIFICACIÓN DEL PUESTO
                </td>
            </tr>
            <tr>
                <td class="p6 bg-gray" style="width:25%;">
                    Nombre del Puesto:
                </td>
                <td class="p6" style="font-weight:bold; font-size:11pt; color:#157347;">
                    {{ Str::upper($nombre_puesto ?? '') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= MISIÓN ================= --}}
    <div class="section">
        <table class="tbl b1 t9">
            <tr>
                <td class="th-green c p6" style="font-size:10pt;">
                    MISIÓN DEL PUESTO
                </td>
            </tr>
            <tr>
                <td class="p6 text-justify" style="padding:10px;">
                    {!! nl2br(e($mision ?? '')) !!}
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= ESTRUCTURA ORGANIZATIVA ================= --}}
    @php
        $niveles = array_filter(
            array_map('trim', explode("\n", $estructura_organizativa ?? ''))
        );
    @endphp

    @if(count($niveles))
    <div class="section">
        <table class="tbl b1 t9">
            <tr>
                <td class="th-green c p6" style="font-size:10pt;">
                    NIVEL ESTRUCTURA ORGANIZATIVA
                </td>
            </tr>
            <tr>
                <td style="padding:20px; text-align:center;">
                    <div style="background:#efefef; border:4px solid #7ac143; border-radius:35px; padding:25px;">
                        @foreach($niveles as $nivel)
                            <div style="width:220px; margin:0 auto; border:2px solid #92D050; background:#fff; padding:12px; font-weight:bold;">
                                {{ $nivel }}
                            </div>
                            @if(!$loop->last)
                                <div style="width:2px; height:25px; background:#92D050; margin:0 auto;"></div>
                            @endif
                        @endforeach
                    </div>
                </td>
            </tr>
        </table>
    </div>
    @endif

    {{-- ================= PERFIL DEL PUESTO ================= --}}
    <div class="section">
        <table class="tbl b1 t9">
            <tr>
                <td colspan="2" class="th-green c p6" style="font-size:10pt;">
                    PERFIL DEL PUESTO
                </td>
            </tr>
            <tr>
                <td class="p6 bg-gray" style="width:25%;">
                    Escolaridad:
                </td>
                <td class="p6">
                    {{ $escolaridad ?? '' }}
                </td>
            </tr>
            <tr>
                <td class="p6 bg-gray">
                    Habilidades:
                </td>
                <td class="p6 text-justify">
                    {!! nl2br(e($habilidades ?? '')) !!}
                </td>
            </tr>
            <tr>
                <td class="p6 bg-gray">
                    Requisitos:
                </td>
                <td class="p6 text-justify">
                    {!! nl2br(e($requisitos ?? '')) !!}
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= FUNCIONES Y RESPONSABILIDADES ================= --}}
    <div class="section">
        <table class="tbl b1 t9">
            <tr>
                <td class="th-green c p6" style="font-size:10pt;">
                    FUNCIONES Y RESPONSABILIDADES
                </td>
            </tr>
            <tr>
                <td class="p6 text-justify" style="padding:10px;">
                    {!! nl2br(e($funciones_responsabilidades ?? '')) !!}
                </td>
            </tr>
        </table>
    </div>

    <p style="font-size:7.5pt; font-style:italic; color:#444; margin-top:10px;">
        *Nota: La descripción antes mencionada es enunciativa mas no limitativa,
        por lo que el titular del puesto se compromete a realizar las tareas
        inherentes a su área para el correcto cumplimiento de los objetivos de la empresa.
    </p>

    {{-- ================= FIRMAS ================= --}}
    <table class="tbl-firmas t9">
        <tr>
            <td colspan="2" style="font-weight:bold; text-align:left; padding-bottom:10px; font-size:10pt;">
                De conformidad:
            </td>
        </tr>
        <tr>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Titular del Puesto</span><br>
                <span style="font-size:8.5pt; color:#555;">Nombre completo y firma</span>
            </td>
            <td>
                <div class="linea-firma"></div>
                <span style="font-weight:bold;">Director General</span><br>
                <span style="font-size:8.5pt; color:#555;">William Paul Mc Lane Galván</span>
            </td>
        </tr>
    </table>

</main>
</body>
</html>