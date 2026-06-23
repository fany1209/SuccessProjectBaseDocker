<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ficha Técnica</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
    .w-full { width: 100%; }
    .tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .b1 td, .b1 th { border: 1px solid #000; }
    .c { text-align: center; }
    .p4 { padding: 4px; }
    .p6 { padding: 6px; }
    .t12 { font-size: 12pt; font-weight: bold; }
    .t9 { font-size: 9pt; }
    .title { text-align:center; font-weight: bold; margin: 10px 0 6px; }
    td { word-wrap: break-word; }
    .th-green { background:#92D050; font-weight:bold; color:#000; }
    .ph-img { height: 50px; border: 1px solid #000; margin: 2px 0; }
    .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }

    header {
      position: fixed; top: -120px; left: 0; right: 0; height: 110px; z-index: 10;
    }
    footer {
      position: fixed; bottom: -60px; left: 0; right: 0; height: 60px; font-size: 9pt; z-index: 10;
    }
    main { margin-top: 0; }
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
          FICHA TÉCNICA
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br> 30-Enero-2023
            </td>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de actualización:</b><br> --
            </td>
            <td style="width:24%; padding:4px; text-align:center;">
              <b>Versión:</b> 00
            </td>
          </tr>
        </table>
      </td>
      <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br> SSS-FOR-CAL-02
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
      </td>
    </tr>
  </table>
</header>

{{-- ================= TÍTULO / DESCRIPCIÓN / IMAGEN ================= --}}
<p class="title" style="margin-top:15px;">{{ $producto_titulo ?? 'NOMBRE PRODUCTO' }}</p>

@php
  $tieneDescripcion = filled($descripcion_breve ?? null);
  $tieneImagen = !empty($imagen_path ?? null);
@endphp

@if($tieneDescripcion || $tieneImagen)
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr>
      @if($tieneDescripcion)
        <td class="p6 l" style="width:{{ $tieneImagen ? '65%' : '100%' }};">
          <b>Descripción:</b><br>
          {{ $descripcion_breve }}
        </td>
      @endif

      @if($tieneImagen)
        <td class="p6 c" style="width:{{ $tieneDescripcion ? '35%' : '100%' }}; vertical-align:middle;">
          <img src="{{ $imagen_path }}" alt="Imagen del producto" style="max-height:120px;">
        </td>
      @endif
    </tr>
  </table>
@endif

{{-- ================= ORGANOLEPTICAS + FISICOQUÍMICAS ================= --}}
@php
  $tieneOrg = !empty($car_org ?? []);
  $tieneFis = !empty($car_fis ?? []);
@endphp

@if($tieneOrg || $tieneFis)
  <table style="width:100%; border-collapse:collapse; margin-top:15px;">
    <tr>
      @if($tieneOrg)
        <td style="width:{{ $tieneFis ? '50%' : '100%' }}; vertical-align:top; {{ $tieneFis ? 'padding-right:5px;' : '' }}">
          <table class="tbl b1" style="font-size:10pt; width:100%;">
            <tr class="th-green c">
              <td colspan="2" style="font-weight:bold; font-size:10pt; padding:4px;">CARACTERÍSTICAS ORGANOLEPTICAS</td>
            </tr>
            @foreach($car_org as $r)
              <tr>
                <td class="p4 l" style="width:40%;"><b>{{ $r['k'] ?? '' }}</b></td>
                <td class="p4 l" style="width:60%;">{{ $r['v'] ?? '' }}</td>
              </tr>
            @endforeach
          </table>
        </td>
      @endif

      @if($tieneFis)
        <td style="width:{{ $tieneOrg ? '50%' : '100%' }}; vertical-align:top; {{ $tieneOrg ? 'padding-left:5px;' : '' }}">
          <table class="tbl b1" style="font-size:10pt; width:100%;">
            <tr class="th-green c">
              <td colspan="2" style="font-weight:bold; font-size:10pt; padding:4px;">CARACTERÍSTICAS FISICOQUÍMICAS</td>
            </tr>
            @foreach($car_fis as $r)
              <tr>
                <td class="p4 l" style="width:40%;"><b>{{ $r['k'] ?? '' }}</b></td>
                <td class="p4 l" style="width:60%;">{{ $r['v'] ?? '' }}</td>
              </tr>
            @endforeach
          </table>
        </td>
      @endif
    </tr>
  </table>
@endif

{{-- ================= MACRO + MICRO ================= --}}
@php
  $tieneMacro = !empty($macroelems ?? []);
  $tieneMicro = !empty($microelems ?? []);
@endphp

@if($tieneMacro || $tieneMicro)
  <table style="width:100%; border-collapse:collapse; margin-top:15px;">
    <tr>
      @if($tieneMacro)
        <td style="width:{{ $tieneMicro ? '50%' : '100%' }}; vertical-align:top; {{ $tieneMicro ? 'padding-right:5px;' : '' }}">
          <table class="tbl b1" style="font-size:10pt; width:100%;">
            <tr class="th-green c">
              <td colspan="2" style="font-weight:bold; font-size:10pt; padding:4px;">MACROELEMENTOS (mg/100 g)</td>
            </tr>
            @foreach($macroelems as $r)
              <tr>
                <td class="p4 l" style="width:40%;"><b>{{ $r['k'] ?? '' }}</b></td>
                <td class="p4 l" style="width:60%;">{{ $r['v'] ?? '' }}</td>
              </tr>
            @endforeach
          </table>
        </td>
      @endif

      @if($tieneMicro)
        <td style="width:{{ $tieneMacro ? '50%' : '100%' }}; vertical-align:top; {{ $tieneMacro ? 'padding-left:5px;' : '' }}">
          <table class="tbl b1" style="font-size:10pt; width:100%;">
            <tr class="th-green c">
              <td colspan="2" style="font-weight:bold; font-size:10pt; padding:4px;">MICROELEMENTOS (mg/100 g)</td>
            </tr>
            @foreach($microelems as $r)
              <tr>
                <td class="p4 l" style="width:40%;"><b>{{ $r['k'] ?? '' }}</b></td>
                <td class="p4 l" style="width:60%;">{{ $r['v'] ?? '' }}</td>
              </tr>
            @endforeach
          </table>
        </td>
      @endif
    </tr>
  </table>
@endif

{{-- ================= MICROBIOLOGÍA ================= --}}
@if(!empty($microbio ?? []))
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr class="th-green c">
      <td colspan="2" style="font-weight:bold; font-size:10pt; padding:4px;">MICROBIOLOGÍA</td>
    </tr>
    @foreach($microbio as $r)
      <tr>
        <td class="p4 l" style="width:40%;"><b>{{ $r['k'] ?? '' }}</b></td>
        <td class="p4 l" style="width:60%;">{{ $r['v'] ?? '' }}</td>
      </tr>
    @endforeach
  </table>
@endif

@php
  $prop = $prop ?? [];
  $tieneProp = is_array($prop) && count($prop) > 0;
@endphp

@if($tieneProp)
  <table class="tbl b1" style="width:100%; border-collapse:collapse; margin-top:15px;">
    <tr class="th-green c">
      <td colspan="2" style="font-weight:bold; font-size:10pt; padding:4px;">PROPIEDADES DEL PRODUCTO</td>
    </tr>

    @foreach($prop as $row)
      <tr>
        <td style="width:38%; background:#8ED14F; font-weight:bold; padding:6px; border:1px solid #000;">
          {{ $row['k'] }}
        </td>

        <td style="width:62%; padding:6px; border:1px solid #000;">
          {!! nl2br(e($row['v'])) !!}
        </td>
      </tr>
    @endforeach
  </table>
@endif

{{-- ================= INSTRUCCIONES TÉCNICAS + ALMACENAMIENTO ================= --}}
@php
  $tieneInst = filled($inst_tecnicas ?? null);
  $tieneAlm  = filled($almacenamiento ?? null);
@endphp

@if($tieneInst || $tieneAlm)
  <table style="width:100%; border-collapse:collapse; margin-top:15px;">
    <tr>
      @if($tieneInst)
        <td style="width:{{ $tieneAlm ? '50%' : '100%' }}; vertical-align:top; {{ $tieneAlm ? 'padding-right:5px;' : '' }}">
          <table class="tbl b1" style="font-size:10pt; width:100%;">
            <tr class="th-green c">
              <td style="font-weight:bold; font-size:10pt; padding:4px;">INSTRUCCIONES TÉCNICAS</td>
            </tr>
            <tr>
              <td class="p4 l" style="line-height:1.25;">{!! nl2br(e($inst_tecnicas)) !!}</td>
            </tr>
          </table>
        </td>
      @endif

      @if($tieneAlm)
        <td style="width:{{ $tieneInst ? '50%' : '100%' }}; vertical-align:top; {{ $tieneInst ? 'padding-left:5px;' : '' }}">
          <table class="tbl b1" style="font-size:10pt; width:100%;">
            <tr class="th-green c">
              <td style="font-weight:bold; font-size:10pt; padding:4px;">ALMACENAMIENTO</td>
            </tr>
            <tr>
              <td class="p4 l" style="line-height:1.25;">{!! nl2br(e($almacenamiento)) !!}</td>
            </tr>
          </table>
        </td>
      @endif
    </tr>
  </table>
@endif

{{-- ================= PRESENTACIÓN + VIDA DE ANAQUEL ================= --}}
  @php
    use Illuminate\Support\Collection;
    $presentacion     = $presentacion     ?? null;            
    $presentacion_img = $presentacion_img ?? [];               
    $vida_anaquel     = $vida_anaquel     ?? null;

    if ($presentacion_img instanceof Collection) {
        $files = $presentacion_img;
    } elseif (is_array($presentacion_img)) {
        $files = collect($presentacion_img);
    } else {
        $files = collect(json_decode((string)$presentacion_img, true) ?: []);
    }

    $hasImgs   = $files->isNotEmpty();
    $tienePres = $hasImgs || filled($presentacion);
    $tieneVida = filled($vida_anaquel);
    $thumb = 100; 
    $imgBase = public_path('images/quality/presentacion');
    $grid = $files->filter()->values()->chunk(3);
  @endphp

  @if($tienePres || $tieneVida)
    <table style="width:100%; border-collapse:collapse; margin-top:15px;">
      <tr>
        @if($tienePres)
          <td style="width:{{ $tieneVida ? '50%' : '100%' }}; vertical-align:top; {{ $tieneVida ? 'padding-right:5px;' : '' }}">
            <table class="tbl b1" style="font-size:10pt; width:100%;">
              <tr class="th-green c">
                <td style="font-weight:bold; font-size:10pt; padding:4px;">PRESENTACIÓN</td>
              </tr>
              <tr>
                <td class="p4 l" style="line-height:1.25;">
                  @if($hasImgs)
                    <table style="width:100%; border-collapse:collapse; border:0;">
                      @foreach($grid as $row)
                        <tr>
                          @foreach($row as $file)
                            @php $path = $imgBase . DIRECTORY_SEPARATOR . $file; @endphp
                            <td style="width:33.3%; text-align:center; padding:6px; border:0;">
                              @if(is_readable($path))
                                <img src="file://{{ $path }}"
                                    alt=""
                                    style="width:{{ $thumb }}px; height:{{ $thumb }}px; object-fit:contain; border:1px solid #ddd; border-radius:6px;">
                              @endif
                            </td>
                          @endforeach
                          @for($i=$row->count(); $i<3; $i++)
                            <td style="width:25%; padding:6px; border:0;"></td>
                          @endfor
                        </tr>
                      @endforeach
                    </table>
                  @else
                    {!! nl2br(e($presentacion)) !!}
                  @endif
                </td>
              </tr>
            </table>
          </td>
        @endif

        @if($tieneVida)
          <td style="width:{{ $tienePres ? '50%' : '100%' }}; vertical-align:top; {{ $tienePres ? 'padding-left:5px;' : '' }}">
            <table class="tbl b1" style="font-size:10pt; width:100%;">
              <tr class="th-green c">
                <td style="font-weight:bold; font-size:10pt; padding:4px;">VIDA DE ANAQUEL</td>
              </tr>
              <tr>
                <td class="p4 l" style="line-height:1.25;">{!! nl2br(e($vida_anaquel)) !!}</td>
              </tr>
            </table>
          </td>
        @endif
      </tr>
    </table>
  @endif

{{-- ================= INSTRUCCIONES DE USO Y APLICACIONES ================= --}}
@if(filled($uso_aplicaciones ?? null))
  <table class="tbl b1" style="font-size:10pt; margin-top:15px;">
    <tr class="th-green c">
      <td style="font-weight:bold; font-size:10pt; padding:4px;">INSTRUCCIONES DE USO Y APLICACIONES</td>
    </tr>
    <tr>
      <td class="p4 l" style="line-height:1.25;">{!! nl2br(e($uso_aplicaciones)) !!}</td>
    </tr>
  </table>
@endif

{{-- ================= INFORMACIÓN NUTRICIONAL ================= --}}
  @php
    $tieneNutricional = !empty($nutricional_path) && is_readable($nutricional_path);
    $nutriMaxWidth  = '250px';  
    $nutriMaxHeight = '300px'; 
  @endphp

  @if($tieneNutricional)
    <table class="tbl b1" style="width:100%; border-collapse:collapse; margin-top:15px;">
      <tr class="th-green c">
        <td style="font-weight:bold; font-size:10pt; padding:4px;">INFORMACIÓN NUTRICIONAL</td>
      </tr>
      <tr>
        <td style="padding:8px;">
          <img
            src="file://{{ $nutricional_path }}"
            alt="Información nutricional"
            style="max-width: {{ $nutriMaxWidth }}; max-height: {{ $nutriMaxHeight }}; height:auto; display:block; object-fit:contain; margin: 0 auto;">
        </td>
      </tr>
    </table>
  @endif

<footer>
  <table style="width:100%; border-collapse:collapse; font-size:9pt; font-family:Arial, sans-serif; background:#f2f2f2; border-top:2px solid #92D050;">
    <tr>
      <td style="width:50%; text-align:left; vertical-align:top; padding:4px;">
        Emiliano Zapata No.7<br>
        Col. Rancho Nuevo C.P 38197<br>
        Apaseo el Grande, Gto. México
      </td>
      <td style="width:50%; text-align:right; vertical-align:top; padding:4px;">
        +52 461 156 8547<br>
        +52 461 616 9975<br>
        <a href="https://www.suministrossustentables.com" target="_blank">www.suministrossustentables.com</a>
      </td>
    </tr>
  </table>
</footer>

</body>
</html>
