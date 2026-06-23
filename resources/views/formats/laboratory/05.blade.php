<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Análisis Interno del Producto</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color:#000; }

    .w-full { width: 100%; }
    .tbl { width:100%; border-collapse:collapse; table-layout:fixed; }
    .b1 td, .b1 th { border:1px solid #000; }
    td, th { vertical-align: middle; word-wrap:break-word; }
    .p4{padding:4px}.p6{padding:6px}
    .c{text-align:center}.l{text-align:left}.r{text-align:right}
    .t12{font-size:12pt;font-weight:bold}
    .t9{font-size:9pt}
    .cb{font-family: DejaVu Sans, Arial, Helvetica, sans-serif;}
    .muted{font-size:9pt;color:#111}
    .th-green{background:#92D050;font-weight:bold;color:#000}
    .nowrap{ white-space: nowrap; }
    .no-break{ white-space: nowrap; overflow-wrap: normal; word-break: normal; }
    .vtop{ vertical-align: top; }
    .pb { page-break-before: always; }
    .avoid-split { page-break-inside: avoid; }

    header { position: fixed; top: -120px; left: 0; right: 0; height: 110px; z-index: 10; }
    footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 60px; font-size: 9pt; z-index: 10; }
    main   { margin-top: 0; }
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
          ANÁLISIS INTERNO DEL PRODUCTO
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
              30-Enero-2023
            </td>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de actualización:</b><br>
              --
            </td>
            <td style="width:24%; padding:4px; text-align:center;">
              <b>Versión:</b>--
            </td>
          </tr>
        </table>
      </td>

      <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>SSS-FOR-LID-05
        </div>
        <div style="padding:10px; text-align:center;">
          &nbsp;
        </div>
      </td>
    </tr>
  </table>
</header>

<main>

  {{-- =================== FECHA DE MUESTREO =================== --}}
  <table class="w-full" style="margin-top:8px;">
      <tr>
        <td class="l">
          <table class="b1" style="font-size:10pt; display:inline-table; border-collapse:collapse;">
            <tr>
              <td class="p6" style="font-weight:bold;">
                Folio muestra: {{ $folio_muestra ?? '—' }}
              </td>
            </tr>
          </table>
        </td>
        <td class="r">
          <table class="b1" style="font-size:10pt; display:inline-table; border-collapse:collapse;">
            <tr>
              <td class="p6" style="font-weight:bold;">
                Fecha de Muestreo: {{ $fecha_muestreo ?? '' }}
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

  {{-- ===================== BLOQUE SUPERIOR ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <colgroup>
      <col style="width:18%">
      <col style="width:32%">
      <col style="width:18%">
      <col style="width:32%">
    </colgroup>

    <tr>
      <td class="p4" colspan="2"><b>Proveedor:</b> {{ $proveedor ?? '' }}</td>
      <td class="p4" colspan="2"><b>Lote:</b> {{ $lote ?? '' }}</td>
    </tr>

    <tr>
      <td class="p4" colspan="2"><b>Vida de anaquel:</b> {{ $vida_anaquel ?? '' }}</td>
      <td class="p4" colspan="2"><b>Tipo de inspección:</b> {{ $tipo_inspeccion ?? '' }}</td>
    </tr>

  </table>

  {{-- ===================== TIPO DE MUESTRA / OBSERVACIONES ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
      <tr>
        <td class="p4" colspan="4">
          <b>TIPO DE MUESTRA:</b> {{ $tipo_muestra ?? '' }}
        </td>
      </tr>
      <tr>
        <td class="p4" colspan="4" style="vertical-align:top; white-space:pre-line;">
          <b>Observaciones específicas:</b><br>
          {{ $obs_tipo_muestra ?? '' }}
        </td>
      </tr>
      <tr>
        <td class="p4" colspan="4" style="vertical-align:top; white-space:pre-line; background-color: #fafafa;">
          <b>Observaciones generales de la muestra:</b><br>
          {{ $observaciones_generales_muestra ?? 'Sin observaciones adicionales.' }}
        </td>
      </tr>
    </table>

  {{-- ===================== PRESENCIA DE MATERIA EXTRAÑA ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="2">PRESENCIA DE MATERIA EXTRAÑA</td>
    </tr>
    <tr>
      @php
        $me = strtolower((string)($materia_extrana ?? 'ausente'));
      @endphp
      <td class="p4" style="width:50%;">Elija un elemento.<br>
        Presente <span class="cb">{{ $me==='presente' ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Ausente <span class="cb">{{ $me==='ausente' ? '☑' : '☐' }}</span> &nbsp;&nbsp;
        Otro <span class="cb">{{ !empty($materia_extrana_otro) ? '☑' : '☐' }}</span>
        @if(!empty($materia_extrana_otro)) &nbsp;&nbsp;<b>Especifique:</b> {{ $materia_extrana_otro }} @endif
      </td>
      <td class="p4"><b>Observaciones:</b> {{ $obs_materia_extrana ?? 'Sin presencia de materia extraña.' }}</td>
    </tr>
  </table>

  {{-- ===================== % HUMEDAD ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="2">% HUMEDAD</td>
    </tr>
    <tr>
      <td class="p4" style="width:50%;">
        <b>Método:</b><br>
        {!! nl2br(e(trim((string)($metodo_humedad ?? '')) !== ''
            ? $metodo_humedad
            : 'Por triplicado, colocando el producto en termobalanza MB23 OHAUS a peso constante.')) !!}
      </td>
      <td class="p4">
        <b>Observaciones:</b><br>
        {!! nl2br(e($obs_humedad ?? '')) !!}
      </td>
    </tr>
  </table>

  {{-- ===================== % PROTEÍNA ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="2">% PROTEÍNA</td>
    </tr>
    <tr>
      <td class="p4" style="width:50%;">
        <b>Método:</b><br>
        {!! nl2br(e(trim((string)($metodo_proteina ?? '')) !== ''
            ? $metodo_proteina
            : 'Se realiza determinación con metodología Kjeldahl en muestreo por duplicado de una muestra representativa de la carga (ver apartado anexos).')) !!}
      </td>
      <td class="p4">
        <b>Observaciones:</b><br>
        {!! nl2br(e($obs_proteina ?? '')) !!}
      </td>
    </tr>
  </table>

  {{-- ===================== DETERMINACIÓN DE PATÓGENOS ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="2">DETERMINACIÓN DE PATÓGENOS</td>
    </tr>
    <tr>
      <td class="p4" style="width:50%;">
        <b>Determinación de patógenos:</b><br>
        {!! nl2br(e($determinacion_patogenos ?? '')) !!}
      </td>
      <td class="p4">
        <b>Observaciones:</b><br>
        {!! nl2br(e($obs_determinacion_patogenos ?? '')) !!}
      </td>
    </tr>
  </table>

  {{-- ===================== GRANULOMETRÍA ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="2">GRANULOMETRÍA</td>
    </tr>
    <tr>
      <td class="p4" style="width:70%;">
        @php
          $g = (array)($granulometria ?? []);
          $m = fn($n) => in_array($n, $g) ? '☑' : '☐';
        @endphp
        Malla
        #10 <span class="cb">{{ $m('#10') }}</span> &nbsp;
        #24 <span class="cb">{{ $m('#24') }}</span> &nbsp;
        #50 <span class="cb">{{ $m('#50') }}</span> &nbsp;
        #65 <span class="cb">{{ $m('#65') }}</span> &nbsp;
        #85 <span class="cb">{{ $m('#85') }}</span> &nbsp;
        #100 <span class="cb">{{ $m('#100') }}</span> &nbsp;
        #120 <span class="cb">{{ $m('#120') }}</span> &nbsp;
        #150 <span class="cb">{{ $m('#150') }}</span> &nbsp;
        #200 <span class="cb">{{ $m('#200') }}</span> &nbsp;
        Otro <span class="cb">{{ !empty($gran_otro) ? '☑' : '☐' }}</span>
      </td>
      <td class="p4"><b>Especifique:</b> {{ $gran_otro ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td class="p4"><b>Observaciones:</b> {{ $obs_granulometria ?? 'N/A' }}</td>
      <td class="p4"></td>
    </tr>
  </table>

  {{-- ===================== SENSORIAL: COLOR / OLOR / SABOR ===================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="2">SENSORIAL</td>
    </tr>

    <tr>
      <td class="p4">
        <b>COLOR</b> — Cumple:
        @php $cc = strtolower((string)($cumple_color ?? '')); @endphp
        Sí <span class="cb">{{ $cc==='si' ? '☑' : '☐' }}</span>
        &nbsp; No <span class="cb">{{ $cc==='no' ? '☑' : '☐' }}</span>
      </td>
      <td class="p4">
        <b>Observaciones:</b><br>
        {!! nl2br(e($obs_color ?? '')) !!}
      </td>
    </tr>

    <tr>
      <td class="p4">
        <b>OLOR:</b> {{ $olor_texto ?? 'N/A' }}
      </td>
      <td class="p4">
        <b>Observaciones:</b><br>
        {!! nl2br(e($obs_olor ?? '')) !!}
      </td>
    </tr>

    <tr>
      <td class="p4">
        <b>SABOR:</b> {{ $sabor_texto ?? 'N/A' }}
      </td>
      <td class="p4">
        <b>Observaciones:</b><br>
        {!! nl2br(e($obs_sabor ?? '')) !!}
      </td>
    </tr>

    <tr>
      <td class="p4">
        <b>OTRO</b> <span class="cb">{{ !empty($sensorial_otro) ? '☑' : '☐' }}</span>
      </td>
      <td class="p4">
        <b>Especifique:</b><br>
        {!! nl2br(e($sensorial_otro ?? '')) !!}
      </td>
    </tr>
  </table>

  {{-- ===================== Observaciones Generales + Tabla de Resultados ===================== --}}
  <div class="avoid-split">
  @php
    $MESH_MAP = [
      '#10'=>'ret10', '#24'=>'ret24', '#50'=>'ret50', '#65'=>'ret65', '#85'=>'ret85',
      '#100'=>'ret100', '#120'=>'ret120', '#150'=>'ret150', '#200'=>'ret200',
    ];

    $granSel = collect($granulometria ?? [])->map(fn($v)=>strtolower(trim($v)))->all();
    $cols = ['folio'];
    foreach ($granSel as $m) {
      if (isset($MESH_MAP[$m])) $cols[] = $MESH_MAP[$m];
    }
    array_push($cols, 'humedad','proteina','ph','microbiologicos','sensorial');

    $labels = [
      'folio'           => 'Folio o mezcla',
      'humedad'         => '% Humedad',
      'proteina'        => '% Proteína',
      'ph'              => 'pH',
      'microbiologicos' => 'Microbiológicos',
    ];
    foreach ($MESH_MAP as $malla => $key) {
      $labels[$key] = '% Ret ' . $malla;
    }

    $rows = $tabla_resultados ?? [];
  @endphp

  <table class="tbl b1" style="font-size:10pt; margin-top:8px; width:100%;">
    <tr class="th-green c">
      <td class="p4" colspan="{{ max(count($cols),1) }}">Observaciones Generales</td>
    </tr>
    <tr>
      <td class="p6" colspan="{{ max(count($cols),1) }}" style="white-space:pre-line;">
        {{ $observaciones_generales_intro ?? '' }}
      </td>
    </tr>

    <tr class="c" style="font-weight:bold;">
      @foreach($cols as $c)
        <td class="p4">
          @if($c === 'sensorial')
            Sensorial (<span style="color:red;">color</span>,<br>
            <span style="color:#00acee;">olor</span>, sabor)
          @else
            {{ $labels[$c] ?? $c }}
          @endif
        </td>
      @endforeach
    </tr>

    @forelse($rows as $r)
      <tr class="c">
        @foreach($cols as $c)
          <td class="p4">
            @if($c === 'sensorial')
              @php
                $partes = preg_split('/[\s]+/', trim($r[$c] ?? ''));
              @endphp
              
              @if(count($partes) >= 1)
                <div style="color:red; font-weight:bold;">{{ $partes[0] }}</div>
              @endif
              
              @if(count($partes) >= 2)
                <div style="color:#00acee; font-weight:bold;">{{ $partes[1] }}</div>
              @endif
              
              @if(count($partes) >= 3)
                <div style="color:black; font-weight:bold;">{{ $partes[2] }}</div>
              @endif
            @else
              {{ $r[$c] ?? '' }}
            @endif
          </td>
        @endforeach
      </tr>
    @empty
      <tr>
        <td class="p4 c" colspan="{{ max(count($cols),1) }}">Sin datos.</td>
      </tr>
    @endforelse
  </table>
</div>
  {{-- ===================== ANEXO A ===================== --}}
  @php
    $anexoA = (isset($anexo_a_items) && is_array($anexo_a_items)) ? $anexo_a_items : [];
  @endphp

  <table class="tbl b1" style="font-size:10pt; width:100%; margin-top:8px; border-collapse:collapse;">
    <tr class="th-green c" style="font-weight:bold;">
      <td class="p4" colspan="3">ANEXO A — HALLAZGOS</td>
    </tr>
    <tr class="c" style="font-weight:bold;">
      <td class="p4" style="width:22%;">Folio o mezclas</td>
      <td class="p4" style="width:38%;">Observaciones generales</td>
      <td class="p4" style="width:40%;">Evidencias (imágenes)</td>
    </tr>

    @forelse($anexoA as $row)
      @php
        $folio = (string)($row['muestra'] ?? '');
        $obs   = (string)($row['observaciones'] ?? '');
        $imgs  = (array)($row['evidencias'] ?? []);
      @endphp
      <tr>
        <td class="p4 vtop">{{ $folio !== '' ? $folio : '—' }}</td>

        <td class="p4 vtop" style="white-space:pre-line;">
          {!! nl2br(e($obs)) !!}
        </td>

        <td class="p4 vtop">
          @if(count($imgs))
            <div style="font-size:0;">
              @foreach($imgs as $src)
                @php $src = (string)$src; @endphp
                @if($src !== '')
                  <span style="
                    display:inline-block;
                    width:48%;
                    margin:3px 1%;
                    vertical-align:top;
                    page-break-inside: avoid;
                  ">
                    <img src="{{ $src }}"
                         style="display:block; width:100%; max-width:180px; height:auto; border:1px solid #ccc; padding:2px;">
                  </span>
                @endif
              @endforeach
            </div>
          @else
            <span>Sin evidencias.</span>
          @endif
        </td>
      </tr>
    @empty
      <tr>
        <td class="p4 c" colspan="3">Sin registros.</td>
      </tr>
    @endforelse
  </table>

  {{-- ===================== ANEXO B ===================== --}}
  @php
    $anexoB = (isset($anexo_b_items) && is_array($anexo_b_items)) ? $anexo_b_items : [];
  @endphp

  <table class="tbl b1" style="font-size:10pt; width:100%; margin-top:8px; border-collapse:collapse;">
    <tr class="th-green c" style="font-weight:bold;">
      <td class="p4" colspan="3">ANEXO B — HALLAZGOS MICROBIOLÓGICOS</td>
    </tr>
    <tr class="c" style="font-weight:bold;">
      <td class="p4" style="width:22%;">Muestra</td>
      <td class="p4" style="width:38%;">Observaciones generales</td>
      <td class="p4" style="width:40%;">Evidencias (imágenes)</td>
    </tr>

    @forelse($anexoB as $row)
      @php
        $muestra = (string)($row['muestra'] ?? '');
        $obsB    = (string)($row['observaciones'] ?? '');
        $imgsB   = (array)($row['evidencias'] ?? []);
      @endphp
      <tr>
        <td class="p4 vtop">{{ $muestra !== '' ? $muestra : '—' }}</td>

        <td class="p4 vtop" style="white-space:pre-line;">
          {!! nl2br(e($obsB)) !!}
        </td>

        <td class="p4 vtop">
          @if(count($imgsB))
            <div style="font-size:0;">
              @foreach($imgsB as $src)
                @php $src = (string)$src; @endphp
                @if($src !== '')
                  <span style="
                    display:inline-block;
                    width:48%;
                    margin:3px 1%;
                    vertical-align:top;
                    page-break-inside: avoid;
                  ">
                    <img src="{{ $src }}"
                         style="display:block; width:100%; max-width:180px; height:auto; border:1px solid #ccc; padding:2px;">
                  </span>
                @endif
              @endforeach
            </div>
          @else
            <span>Sin evidencias.</span>
          @endif
        </td>
      </tr>
    @empty
      <tr>
        <td class="p4 c" colspan="3">Sin registros.</td>
      </tr>
    @endforelse
  </table>

  {{-- ===================== CONCLUSIONES + FIRMAS ===================== --}}
 <table class="tbl" style="font-size:10pt; margin-top:12px;">
    <tr>
      <td class="p6" colspan="2" style="font-weight:bold;">CONCLUSIONES:</td>
    </tr>
    <tr>
      <td class="p6" colspan="2" style="text-align:justify;">
        {{ $conclusiones ?? 'Durante el análisis realizado no se observó presencia de materia extraña; se identificaron características sensoriales propias del producto, así como la colorimetría del producto dentro de parámetros de especificación. Se determinó para el producto granulometría FINA (menor al 10% en malla 65) y humedad menor al 10% para todas las muestras. Se establece como producto ACEPTABLE.' }}
      </td>
    </tr>
    <tr>
      <td class="p6" style="width:50%; vertical-align: top;">
        <b>Realizó</b><br>
        {{ $realizo_nombre ?? '' }}<br>
        <span style="font-size: 9pt; color: #333;">{{ $realizo_puesto ?? '' }}</span>
      </td>
      <td class="p6" style="width:50%; text-align:right; vertical-align: top;">
        <b>Revisó</b><br>
        {{ $reviso_nombre ?? '' }}<br>
        <span style="font-size: 9pt; color: #333;">{{ $reviso_puesto ?? 'Jefe de laboratorio' }}</span>
      </td>
    </tr>
</table>

</main>
</body>
</html>
