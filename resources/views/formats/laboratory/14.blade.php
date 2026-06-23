<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bitácora</title>
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
    .th-green td { background:#92D050; font-weight:bold; color:#000; }
    .muted{font-size:9pt;color:#111}
    .cb{font-family: DejaVu Sans, Arial, Helvetica, sans-serif;} 

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

{{-- ================================ ENCABEZADO ================================ --}}
<header>
  <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
    <tr>
      <td style="width:22%; text-align:center; border:1px solid #000; vertical-align:middle;">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
      </td>
      <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
        <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
          BITÁCORA
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>18-Febrero-2025
            </td>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de modificación:</b>--
            </td>
            <td style="width:24%; padding:4px; text-align:center;">
              <b>Versión:</b>--
            </td>
          </tr>
        </table>
      </td>
      <td style="width:20%; border:1px solid #000; vertical-align:middle; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>SSS-FOR-LID-14
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
      </td>
    </tr>
  </table>
</header>

<main>
  {{-- =========================== TARJETAS / REGISTROS =========================== --}}
  @php $rows = (array)($registros ?? []); @endphp

  @forelse($rows as $idx => $r)
    <table class="tbl b1" style="font-size:10pt; margin-top:{{ $idx===0 && empty($encabezado_libre) ? '10px' : '14px' }};">
      <tr class="th-green c">
        <td class="p4" colspan="6">{{ $r['titulo'] ?? ('Registro '.($idx+1)) }}</td>
      </tr>

      {{-- fecha/folio/lote --}}
      <tr>
        <td class="p4" colspan="2" style="width:33%;"><b>Fecha:</b> {{ $r['fecha'] ?? '' }}</td>
        <td class="p4" colspan="2" style="width:33%;"><b>Folio:</b> {{ $r['folio'] ?? '' }}</td>
        <td class="p4" colspan="2" style="width:34%;"><b>Lote:</b> {{ $r['lote'] ?? '' }}</td>
      </tr>

      {{-- peso/ph/humedad --}}
      <tr>
        <td class="p4" colspan="2" style="width:33%;"><b>Peso:</b> {{ $r['peso'] ?? '' }}</td>
        <td class="p4" colspan="2" style="width:33%;"><b>pH:</b> {{ $r['ph'] ?? '' }}</td>
        <td class="p4" colspan="2" style="width:34%;"><b>Humedad:</b> {{ $r['humedad'] ?? '' }}</td>
      </tr>

      {{-- proteína / sensorial --}}
      <tr>
        <td class="p4" colspan="3" style="width:40%;"><b>Proteína:</b> {{ $r['proteina'] ?? '' }}</td>
        <td class="p4" colspan="3" style="width:60%;">
          <b>Sensorial:</b>
          <div><b>Color:</b> {{ $r['sensorial']['color'] ?? '' }}</div>
          <div><b>Olor:</b>  {{ $r['sensorial']['olor'] ?? '' }}</div>
          <div><b>Sabor:</b> {{ $r['sensorial']['sabor'] ?? '' }}</div>
        </td>
      </tr>

      {{-- GRANULOMETRÍA--}}
      @php
        $g      = $r['granulometria'] ?? [];
        $mallas = ['10','24','50','65','85','100','120','150','200'];
        $pares = [];
        foreach ($mallas as $m) {
          $val = isset($g[$m]) ? trim((string)$g[$m]) : '';
          if ($val !== '') {
            $pares[] = ['label' => "Malla #$m", 'value' => $val];
          }
        }
      @endphp

      @if (count($pares))
        <tr>
          <td class="p4" colspan="6"><b>Granulometría</b></td>
        </tr>
        @for ($i = 0; $i < count($pares); $i += 3)
          <tr>
            @for ($j = 0; $j < 3; $j++)
              @php $k = $i + $j; @endphp
              @if (isset($pares[$k]))
                <td class="p4"><b>{{ $pares[$k]['label'] }}</b></td>
                <td class="p4">{{ $pares[$k]['value'] }}</td>
              @else
                <td class="p4" colspan="2"></td>
              @endif
            @endfor
          </tr>
        @endfor
      @endif

      <tr>
        <td class="p4"><b>Observaciones generales:</b></td>
        <td class="p4" colspan="5" style="height:56px; vertical-align:top;">
          {!! nl2br(e($r['observaciones'] ?? '')) !!}
        </td>
      </tr>
    </table>
  @empty
    <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
      <tr><td class="p6 c">Sin registros.</td></tr>
    </table>
  @endforelse

</main>
</body>
</html>
