<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Plan de trabajo semanal</title>
<style>
  @page { margin: 140px 24px 80px 24px; }
  html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  body  { margin:0; font-family: Arial, Helvetica, sans-serif; font-size:11pt; color:#000; }
  .tbl{ width:100%; border-collapse:collapse; table-layout:fixed; }
  .b1 td,.b1 th{ border:1px solid #000; }
  td,th{ vertical-align:top; word-wrap:break-word; }
  .p2{padding:2px}.p4{padding:4px}.p6{padding:6px}
  .c{text-align:center}.l{text-align:left}.r{text-align:right}
  .t12{font-size:12pt;font-weight:bold}
  .t9{font-size:9pt}
  .th-green{background:#92D050;font-weight:bold; color:#000;}
  .muted{font-size:9pt;color:#111}
  .cb{font-family: DejaVu Sans, Arial, Helvetica, sans-serif;} 
  .pb{ page-break-before:always }

  header{ position:fixed; top:-120px; left:0; right:0; height:110px; z-index:10; }
  footer{ position:fixed; bottom:-60px; left:0; right:0; height:60px; font-size:9pt; z-index:10; }
  main  { margin-top:0; }
  header td, header th {
    vertical-align: middle !important; 
  }
  header img {
    display:block;          
    margin:0 auto;         
  }
</style>
</head>
<body>

<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
    <tr>
      <td style="width:22%; text-align:center; border:1px solid #000; vertical-align:middle;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>
      <td style="width:58%; border:1px solid #000; vertical-align:middle; padding:0;">
        <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
          PLAN DE TRABAJO SEMANAL
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>30-Enero-2023
            </td>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de actualización:</b>--
            </td>
            <td style="width:24%; padding:4px; text-align:center;">
              <b>Versión:</b>--
            </td>
          </tr>
        </table>
      </td>
      <td style="width:20%; border:1px solid #000; vertical-align:middle; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>SSS-FOR-LID-11
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 3 }}
        </div>
      </td>
    </tr>
  </table>
</header>
<main>

  {{-- Título de sección y semana --}}
  <p style="margin-top:12px; font-weight:bold;">Plan de trabajo Semanal para el Logro de Objetivos</p>
  <table class="tbl" style="margin-top:4px; font-size:10pt;">
    <tr>
      <td style="width:70%; border:0;">
        <b>Semana:</b> {{ $semana_rango ?? '' }}
      </td>
      <td style="width:30%; border:0;" class="r">
        <b>Fecha Revisión:</b> {{ $fecha_revision ?? '' }}
      </td>
    </tr>
  </table>

  {{-- Proyecto / Responsable --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px;">
    <tr>
      <td class="p4" style="width:50%;"><b>Nombre del Proyecto:</b> {{ $proyecto ?? '' }}</td>
      <td class="p4" style="width:50%;"><b>Responsable:</b> {{ $responsable ?? '' }}</td>
    </tr>
  </table>

  {{-- 1. Objetivos de la Semana --}}
  <div style="margin-top:14px; font-weight:bold;">1. Objetivos de la Semana</div>

  @php
    $objetivos = (array)($objetivos ?? []);
    $total_horas = array_sum(array_map(fn($x)=> (int)($x['horas'] ?? 0), $objetivos));
  @endphp

  <table class="tbl b1" style="font-size:10pt; margin-top:6px;">
    <tr class="th-green c">
      <td class="p4" style="width:10%;">Objetivos</td>
      <td class="p4" style="width:58%;">Descripción</td>
      <td class="p4" style="width:12%;">Tiempo estimado por semana (h)</td>
    </tr>

    @forelse($objetivos as $i => $o)
      <tr>
        <td class="p4"><b>{{ ($o['n'] ?? $i+1) }}.</b> {{ $o['titulo'] ?? '' }}</td>
        <td class="p4">{{ $o['descripcion'] ?? '' }}</td>
        <td class="p4 c">{{ $o['horas'] ?? '' }}</td>
      </tr>
    @empty
      <tr><td class="p4 c" colspan="3">Sin objetivos.</td></tr>
    @endforelse

    <tr>
      <td class="p4 r" colspan="2"><b>Total</b></td>
      <td class="p4 c"><b>{{ $total_horas }}</b></td>
    </tr>
  </table>

  <div style="margin-top:14px; font-weight:bold;">2. Resultados Alcanzados</div>

  {{-- Encabezado de tabla de resultados --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:4px;">
    <tr class="th-green c">
      <td class="p4" style="width:10%;">Objetivo</td>
      <td class="p4" style="width:70%;">Resultados</td>
      <td class="p4" style="width:20%;">Cumple</td>
    </tr>

    @php $resultados = (array)($resultados ?? []); @endphp
    @foreach($objetivos as $i => $o)
      @php $r = $resultados[$i] ?? []; @endphp
      <tr>
        <td class="p4">{{ $o['n'] ?? $i+1 }}</td>
        <td class="p4">{{ $r['texto'] ?? '' }}</td>
        <td class="p4 c cb">{{ !empty($r['cumple']) ? '☑' : '☐' }}</td>
      </tr>
    @endforeach
  </table>

  {{-- 3. Análisis de Hallazgos --}}
  <div style="margin-top:16px; font-weight:bold;">3. Análisis de Hallazgos</div>
  <table class="tbl b1" style="font-size:10pt; margin-top:6px;">
    <tr class="th-green c">
      <td class="p4" style="width:40%;">Hallazgo</td>
      <td class="p4" style="width:30%;">Causa</td>
      <td class="p4" style="width:30%;">Propuesta</td>
    </tr>
    @php $hallazgos = (array)($hallazgos ?? []); @endphp
    @forelse($hallazgos as $h)
      <tr>
        <td class="p4">{{ $h['hallazgo'] ?? '' }}</td>
        <td class="p4">{{ $h['causa'] ?? '' }}</td>
        <td class="p4">{{ $h['propuesta'] ?? '' }}</td>
      </tr>
    @empty
      <tr><td class="p4" colspan="3" style="height:60px;"></td></tr>
    @endforelse
  </table>

  {{-- 4. Plan de Acción para la Próxima Semana --}}
  <div style="margin-top:16px; font-weight:bold;">
    4. Plan de Acción para la Próxima Semana <span class="muted">({{ $proxima_semana_rango ?? '' }})</span>
  </div>

  <table class="tbl b1" style="font-size:10pt; margin-top:6px;">
    <tr class="th-green c">
      <td class="p4" style="width:50%;">Plan</td>
      <td class="p4" style="width:50%;">Acciones</td>
    </tr>
    @php $plan = (array)($plan_proxima ?? []); @endphp
    @forelse($plan as $p)
      <tr>
        <td class="p4">{{ $p['plan'] ?? '' }}</td>
        <td class="p4">{{ $p['acciones'] ?? '' }}</td>
      </tr>
    @empty
      <tr><td class="p4" colspan="2" style="height:160px;"></td></tr>
    @endforelse
  </table>

</main>
</body>
</html>
