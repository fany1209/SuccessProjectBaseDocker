<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Análisis Interno de Suelo</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color:#000; }
    .tbl{ width:100%; border-collapse:collapse; table-layout:fixed; }
    .b1 td,.b1 th{ border:1px solid #000; }
    td,th{ vertical-align:middle; word-wrap:break-word; }
    .p4{padding:4px}.p6{padding:6px}
    .c{text-align:center}.l{text-align:left}.r{text-align:right}
    .th-green{background:#92D050;font-weight:bold;color:#000}
    .muted{font-size:9pt;color:#111}
    .pb{ page-break-before: always; }

    header { position: fixed; top: -120px; left: 0; right: 0; height: 110px; z-index: 10; }
    footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 60px; font-size: 9pt; z-index: 10; }
    main   { margin-top: 0; }

    .mini-table{ width:100%; border-collapse:collapse; }
    .mini-row-label{ width:80px; font-size:9pt; font-weight:bold; padding:2px 6px; }
    .track{ height:14px; border:1px solid #999; background:#fff; }
    .fill{ height:14px; }
    .val-azul   { background:#0ea5e9; } 
    .val-amar   { background:#eab308; } 
    .val-naranj { background:#f97316; }

    .pill{display:inline-block; padding:2px 6px; border-radius:10px; font-size:9pt; font-weight:bold; border:1px solid #aaa}
    .pill-bajo{  background:#fff3e6; color:#a14700; border-color:#f97316; }
    .pill-medio{ background:#fff9d1; color:#8a6d00; border-color:#eab308; }
    .pill-alto{  background:#e7f5ff; color:#0b6aa6; border-color:#0ea5e9; }
  </style>
</head>
<body>

{{-- ================================ ENCABEZADO ================================ --}}
<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
    <tr>
      <td style="width:22%; text-align:center; border:1px solid #000;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>
      <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
        <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
          ANÁLISIS INTERNO DE SUELO
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>27-Septiembre-2024
            </td>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de actualización:</b><br>--
            </td>
            <td style="width:24%; padding:4px; text-align:center;">
              <b>Versión:</b>--
            </td>
          </tr>
        </table>
      </td>
      <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">SSS-FOR-LID-06</div>
        <div style="padding:10px; text-align:center;">Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 2 }}</div>
      </td>
    </tr>
  </table>
</header>

<main>

  {{-- ========= SUBTÍTULO DEL INFORME ========= --}}
  <div style="margin-top:8px; text-align:center; font-size:10pt;">
    <div style="background:#92D050; font-weight:bold; padding:4px;">Informe de Resultados</div>
    <div style="padding:4px; font-weight:bold;">ANÁLISIS DE SUELOS</div>
  </div>

  {{-- ========= REPORTE / FECHAS ========= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px; width:100%; table-layout:fixed;">
    <colgroup><col style="width:50%"><col style="width:50%"></colgroup>
    <tr>
      <td class="p4" rowspan="2" style="vertical-align:middle;"><b>Reporte N°:</b> {{ $reporte ?? '' }}</td>
      <td class="p4"><b>Fecha de Ingreso:</b> {{ $fecha_ingreso ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Fecha de Emisión:</b> {{ $fecha_emision ?? '' }}</td>
    </tr>
  </table>

  {{-- ========= INFORMACIÓN DEL CLIENTE ========= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px; width:100%; table-layout:fixed;">
    <tr class="th-green c"><td class="p4">Información del Cliente</td></tr>
    <tr><td class="p4"><b>Nombre del Productor:</b> {{ $cliente_nombre ?? '' }}</td></tr>
    <tr><td class="p4"><b>Ciudad:</b> {{ $cliente_ciudad ?? '' }}</td></tr>
    <tr><td class="p4"><b>Dirección:</b> {{ $cliente_direccion ?? '' }}</td></tr>
    <tr><td class="p4"><b>Teléfono de contacto:</b> {{ $cliente_telefono ?? '' }}</td></tr>
  </table>

  {{-- ========= INFORMACIÓN DE LA MUESTRA ========= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:10px; width:100%; table-layout:fixed;">
    <colgroup><col style="width:33.33%"><col style="width:33.33%"><col style="width:33.34%"></colgroup>
    <tr class="th-green c"><td class="p4" colspan="3">Información de la Muestra</td></tr>
    <tr>
      <td class="p4"><b>Tipo de Cultivo:</b> {{ $cultivo ?? '' }}</td>
      <td class="p4"><b>Sistema:</b> {{ $sistema ?? '' }}</td>
      <td class="p4"><b>Tipo:</b> {{ $tipo_planta ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4" colspan="2"><b>Peso de la muestra:</b> {{ $peso_muestra ?? '' }}</td>
      <td class="p4"><b>Testigo:</b> {{ !empty($testigo) ? 'Sí' : 'No' }}</td>
    </tr>
    <tr><td class="p4" colspan="3"><b>Ubicación:</b> {{ $ubicacion ?? '' }}</td></tr>
    <tr><td class="p4" colspan="3"><b>Tipo de muestreo:</b> {{ $tipo_muestreo ?? '' }}</td></tr>
    <tr><td class="p4" colspan="3"><b>Responsable del muestreo:</b> {{ $responsable_muestreo ?? '' }}</td></tr>
    <tr><td class="p4" colspan="3"><b>Propósito:</b> {{ $proposito ?? '' }}</td></tr>
  </table>

  @once
  @php
    if (!function_exists('classifyRange')) {
      function classifyRange($value, $L){
        $val = is_numeric($value) ? (float)$value : null;
        $b = (float)($L['BAJO']  ?? 0);
        $m = (float)($L['MEDIO'] ?? 0);
        $a = (float)($L['ALTO']  ?? 0);
        if ($val === null) return 'SIN DATO';
        if ($val <= $b) return 'BAJO';
        if ($val <= $m) return 'MEDIO';
        if ($val <= $a) return 'ALTO';
        return 'ALTO+'; 
      }
    }

    if (!function_exists('dompdfBarRows')) {
     
      function dompdfBarRows($value, $L, $axisStep=10, $barClass='val-azul') {
        $val = is_numeric($value) ? (float)$value : null;
        $b = (float)($L['BAJO']  ?? 0);
        $m = (float)($L['MEDIO'] ?? 0);
        $a = (float)($L['ALTO']  ?? 0);
        $axis = $a;
        if ($val !== null) {
          if     ($val <= $b) $axis = $b;
          elseif ($val <= $m) $axis = $m;
          elseif ($val <= $a) $axis = $a;
          else                 $axis = $val;
        }
        $axis = max($axisStep, ceil($axis / $axisStep) * $axisStep);
        $pct = ($val !== null && $axis > 0) ? min(100, max(0, ($val / $axis) * 100)) : 0;
        $range = classifyRange($val, $L);
        $rowAlto  = in_array($range, ['ALTO','ALTO+']);
        $rowMedio = ($range === 'MEDIO');
        $rowBajo  = ($range === 'BAJO');
        $row = function($label, $active) use ($pct, $barClass){
          $w = $active ? number_format($pct,2,'.','').'%' : '0%';
          return '
            <tr>
              <td class="mini-row-label">'.$label.'</td>
              <td>
                <div class="track">
                  <div class="fill '.$barClass.'" style="width:'.$w.';"></div>
                </div>
              </td>
            </tr>
          ';
        };

        echo '
        <table class="mini-table">
          '.$row('ALTO',  $rowAlto).'
          '.$row('MEDIO', $rowMedio).'
          '.$row('BAJO',  $rowBajo).'
        </table>';
      }
    }
  @endphp
  @endonce

  {{-- ========= MACRONUTRIENTES========= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c"><td class="p4">Macronutrientes</td></tr>
    @php
      $L_N = ['BAJO'=>10,'MEDIO'=>18,'ALTO'=>20];
      $rN = classifyRange($n_val ?? null, $L_N);
      $pillN = $rN==='BAJO'?'pill-bajo':($rN==='MEDIO'?'pill-medio':($rN==='ALTO'?'pill-alto':'pill-medio'));
    @endphp
    <tr>
      <td class="p4">
        <div style="display:flex; justify-content:space-between; gap:8px; align-items:center;">
          <div><b>Nitrógeno</b> <span class="pill {{ $pillN }}">{{ $rN }}</span></div>
          <div><b>{{ is_numeric($n_val ?? null) ? (int)$n_val : '—' }}</b> mg/kg</div>
        </div>
        @php dompdfBarRows($n_val ?? null, $L_N, 10, 'val-azul'); @endphp
      </td>
    </tr>
  </table>

  <table class="tbl b1" style="font-size:10pt;">
    @php
      $L_P = ['BAJO'=>15,'MEDIO'=>29,'ALTO'=>30];
      $rP = classifyRange($p_val ?? null, $L_P);
      $pillP = $rP==='BAJO'?'pill-bajo':($rP==='MEDIO'?'pill-medio':($rP==='ALTO'?'pill-alto':'pill-medio'));
    @endphp
    <tr>
      <td class="p4">
        <div style="display:flex; justify-content:space-between; gap:8px; align-items:center;">
          <div><b>Fósforo</b> <span class="pill {{ $pillP }}">{{ $rP }}</span></div>
          <div><b>{{ is_numeric($p_val ?? null) ? (int)$p_val : '—' }}</b> mg/kg</div>
        </div>
        @php dompdfBarRows($p_val ?? null, $L_P, 10, 'val-amar'); @endphp
      </td>
    </tr>
    @php
      $L_K = ['BAJO'=>100,'MEDIO'=>199,'ALTO'=>200];
      $rK = classifyRange($k_val ?? null, $L_K);
      $pillK = $rK==='BAJO'?'pill-bajo':($rK==='MEDIO'?'pill-medio':($rK==='ALTO'?'pill-alto':'pill-medio'));
    @endphp
    <tr>
      <td class="p4">
        <div style="display:flex; justify-content:space-between; gap:8px; align-items:center;">
          <div><b>Potasio</b> <span class="pill {{ $pillK }}">{{ $rK }}</span></div>
          <div><b>{{ is_numeric($k_val ?? null) ? (int)$k_val : '—' }}</b> mg/kg</div>
        </div>
        @php dompdfBarRows($k_val ?? null, $L_K, 10, 'val-naranj'); @endphp
      </td>
    </tr>
  </table>
  @once
@php
if (!function_exists('clasificarTextura')) {
  
  function clasificarTextura($arena, $limo, $arcilla, $tolerancia = 0.5) {
    if (!is_numeric($arena) || !is_numeric($limo) || !is_numeric($arcilla)) {
      return 'NO SUMA 100, REVISAR DATOS';
    }
    $A = (float)$arena;   
    $L = (float)$limo;    
    $C = (float)$arcilla;  

    $suma = $A + $L + $C;
    if (abs($suma - 100) > $tolerancia) {
      return 'NO SUMA 100, REVISAR DATOS';
    }

    if ($C <= 12 && $A <= (20 - $C)) {
      return 'Limosa';
    }
    if ($C <= 27 && $A <= (50 - $C)) {
      return 'Franco limosa';
    }
    $condFranca1 = ($C <= 20 && $C > 7 && $A <= 52);
    $condFranca2 = ($C <= 27 && $C > 20 && $A <= (72 - $C));
    if ($condFranca1 || $condFranca2) {
      return 'Franca';
    }
    if ($C <= 10 && $A > (85 + ($C / 2.0))) {
      return 'Arenosa';
    }
    if ($C <= 15 && $A > (70 + $C)) {
      return 'Areno francosa';
    }
    if ($C <= 20) {
      return 'Franco arenosa';
    }
    if ($C <= 40 && $A <= 20) {
      return 'Franco arcillo limosa';
    }
    if ($C <= 40 && $A <= 45) {
      return 'Franco arcillosa';
    }
    if ($C <= 35) {
      return 'Franco arcillo arenosa';
    }
    if ($C <= 60 && $A <= (60 - $C)) {
      return 'Arcillo limosa';
    }
    if ($A > 45) {
      return 'Arcillo arenosa';
    }
    return 'Arcillosa';
  }
}
@endphp
@endonce

{{-- ========= DETERMINACIÓN DE TEXTURA ========= --}}
<table class="tbl b1" style="font-size:10pt; margin-top:12px;">
  <tr class="th-green c">
    <td class="p4" colspan="4">Determinación de Textura</td>
  </tr>
  <tr class="c" style="font-weight:bold;">
    <td class="p4" style="width:25%;">Arena</td>
    <td class="p4" style="width:25%;">Limo</td>
    <td class="p4" style="width:25%;">Arcilla</td>
    <td class="p4" style="width:25%;">Clasificación de Suelo</td>
  </tr>
  <tr class="c">
    <td class="p4">{{ $arena ?? '' }} %</td>
    <td class="p4">{{ $limo ?? '' }} %</td>
    <td class="p4">{{ $arcilla ?? '' }} %</td>
    <td class="p4">
      {{ clasificarTextura($arena ?? null, $limo ?? null, $arcilla ?? null) }}
    </td>
  </tr>
</table>

@if(!empty($image_path))
  <div style="background:#16a34a;color:#fff;font-weight:bold;padding:6px 8px;">EVIDENCIA FOTOGRÁFICA</div>
  <div style="border:1px solid #000;padding:6px;text-align:center;display:inline-block;page-break-inside:avoid;">
    <img src="{{ $image_path }}"
         alt="Evidencia"
         style="max-width:100mm;max-height:75mm;width:auto;height:auto;object-fit:contain;display:block;margin:0 auto;">
    @if(!empty($image_caption))
      <div style="font-size:9pt;margin-top:4px;">{{ $image_caption }}</div>
    @endif
  </div>
@endif


</main>
</body>
</html>
