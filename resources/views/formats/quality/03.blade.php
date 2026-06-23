<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inspección de Recepción de Carga</title>
   <style>
  @page {
    margin: 140px 24px 80px 24px;
  }

  html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

  body  { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; }
  .w-full { width: 100%; }
  .tbl    { width: 100%; border-collapse: collapse; table-layout: fixed; }
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
</style>

</head>
<body>
  {{-- ================= ENCABEZADO ================= --}}
<header>
<table style="width:100%; border-collapse:collapse; border:1px solid #000; font-family:Arial, sans-serif; font-size:11pt;">
  <tr>
    <td style="width:22%; text-align:center; border:1px solid #000;">
      <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
    </td>
    <td style="width:58%; border:1px solid #000; vertical-align:top; padding:0;">
      <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
        HOJA DE SEGURIDAD
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

        <!-- CÓDIGO Y PÁGINAS -->
        <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
            <b>Código:</b><br>
              SSS-FOR-CAL-03
        </div>
        <div style="padding:10px; text-align:center;">&nbsp;</div>

        </td>
    </tr>
</table>
</header>

<div class="content">

  {{-- ================= NOMBRE DEL PRODUCTO ================= --}}
<p style="text-align:center; font-size:14pt; font-weight:bold; margin:10px 0;">
  {{ $producto ?? 'NOMBRE DEL PRODUCTO' }}
</p>

{{-- ================= 1. IDENTIFICACIÓN DEL PRODUCTO Y DE LA EMPRESA ================= --}}
<table class="tbl b1" style="margin-top:10px; font-size:8pt; border:1px solid #92D050;">
  <tr class="th-green">
    <td colspan="2" style="font-weight:bold; padding:4px;">
      SECCIÓN 1.- IDENTIFICACIÓN DEL PRODUCTO Y EMPRESA
    </td>
  </tr>

  <tr>
    <td style="width:30%; padding:4px; background:#f2f2f2;"><b>USO</b></td>
    <td style="width:70%; padding:4px;">{{ $uso ?? '' }}</td>
  </tr>

  <tr>
    <td style="padding:4px; background:#f9f9f9;"><b>SINÓNIMOS</b></td>
    <td style="padding:4px;">{{ $sinonimo ?? '' }}</td>
  </tr>

  <tr>
    <td style="padding:4px; background:#f2f2f2;"><b>EMPRESA</b></td>
    <td style="padding:4px;">SUCCESS SUMINISTROS SUSTENTABLES S.A. DE C.V.</td>
  </tr>

  <tr>
    <td style="padding:4px; background:#f9f9f9;"><b>DOMICILIO</b></td>
    <td style="padding:4px;">Emiliano Zapata No. 7, Col. Rancho Nuevo, C.P. 38197, Apaseo el Grande, Guanajuato, México.</td>
  </tr>

  <tr>
    <td style="padding:4px; background:#f2f2f2;"><b>TELÉFONOS</b></td>
    <td style="padding:4px;">(+52) 461 156 8547, 461 616 9975</td>
  </tr>

  <tr>
    <td style="padding:4px; background:#f9f9f9;"><b>CONTACTO</b></td>
    <td style="padding:4px;">
      <a href="mailto:contacto@suministrossustentables.com">contacto@suministrossustentables.com</a>
    </td>
  </tr>

  <tr>
    <td style="padding:4px; background:#f2f2f2;"><b>TELÉFONO DE EMERGENCIA</b></td>
    <td style="padding:4px;">
      55 52 38 27 81 y 55 52 38 27 83 – Centro Toxicológico y de Monitoreo Químico, Biológico, Radiológico y Nuclear
    </td>
  </tr>
</table>


{{-- ================= 2. IDENTIFICACIÓN DE LOS PELIGROS ================= --}}
<table class="tbl b1" style="margin-top:15px; font-size:8pt; border:1px solid #92D050;">
  <tr class="th-green">
    <td colspan="2" style="font-weight:bold; padding:4px;">
      SECCIÓN 2.- IDENTIFICACIÓN DE LOS PELIGROS
    </td>
  </tr>
  <tr>
    <td colspan="2" style="font-weight:bold; padding:4px 4px 0 4px;">PICTOGRAMA DE PELIGRO</td>
  </tr>

{{-- === Área de pictogramas === --}}
  @php
    $pics = array_values($pictogramas ?? []);  
    $n    = count($pics);
  @endphp

  @if($n > 0)
    <tr>
      <td colspan="2" style="padding:4px 6px;">
        <table style="width:100%; border-collapse:collapse;">
          @php
            $perRow = $n <= 3 ? $n : ceil($n / 2);
            $chunks = collect($pics)->chunk($perRow);
          @endphp

          @foreach($chunks as $row)
            @php
              $cols = max(1, count($row));
              $w    = 100 / $cols;
            @endphp
            <tr>
              @foreach($row as $path)
                <td style="width:{{ number_format($w, 4, '.', '') }}%; text-align:center; padding:6px;">
                  <img src="{{ $path }}" alt="Pictograma" style="max-height:48px;">
                </td>
              @endforeach
            </tr>
          @endforeach
        </table>
      </td>
    </tr>
  @else
    <tr><td colspan="2" style="padding:6px;"><div style="height:48px; border:1px dashed #999;"></div></td></tr>
  @endif

    <tr>
    <td colspan="2" style="font-weight:bold; padding:4px 4px 4px;">INDICADORES DE PELIGRO</td>
  </tr>

  @if(!empty($h_codes))
    @foreach($h_codes as $i => $h)
      <tr>
        <td style="width:20%; padding:4px; background:{{ $i % 2 === 0 ? '#f2f2f2' : '#f9f9f9' }};">
          {{ $h['code'] ?? 'H—' }}
        </td>
        <td style="width:80%; padding:4px; background:{{ $i % 2 === 0 ? '#f2f2f2' : '#f9f9f9' }};">
          {{ $h['text'] ?? '' }}
        </td>
      </tr>
    @endforeach
  @else
    <tr>
      <td style="width:20%; padding:4px; background:#f2f2f2;">HXXX</td>
      <td style="width:80%; padding:4px; background:#f2f2f2;">Indicador</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;">HXXX</td>
      <td style="padding:4px; background:#f9f9f9;">&nbsp;</td>
    </tr>
  @endif

  <tr>
    <td colspan="2" style="font-weight:bold; padding:4px 4px 4px;">CONSEJOS DE PRECAUCIÓN</td>
  </tr>

  @if(!empty($p_codes))
    @foreach($p_codes as $i => $p)
      <tr>
        <td style="padding:4px; background:{{ $i % 2 === 0 ? '#f2f2f2' : '#f9f9f9' }};">{{ $p['code'] ?? 'P—' }}</td>
        <td style="padding:4px; background:{{ $i % 2 === 0 ? '#f2f2f2' : '#f9f9f9' }};">{{ $p['text'] ?? '' }}</td>
      </tr>
    @endforeach
  @else
    <tr>
      <td style="padding:4px; background:#f2f2f2;">PXXX</td>
      <td style="padding:4px; background:#f2f2f2;">Consejo</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;">PXXX</td>
      <td style="padding:4px; background:#f9f9f9;">&nbsp;</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;">&nbsp;</td>
      <td style="padding:4px; background:#f2f2f2;">&nbsp;</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;">&nbsp;</td>
      <td style="padding:4px; background:#f9f9f9;">&nbsp;</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;">&nbsp;</td>
      <td style="padding:4px; background:#f2f2f2;">&nbsp;</td>
    </tr>
  @endif
  </table>

  {{-- ================= 3. COMPOSICIÓN ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <tr class="th-green">
      <td colspan="3" style="font-weight:bold; padding:4px;">
        SECCIÓN 3.- COMPOSICIÓN
      </td>
    </tr>
    <tr style="background:#f2f2f2;">
      <td style="width:55%; padding:4px; font-weight:bold;">Identidad química de la sustancia</td>
      <td style="width:20%; padding:4px; font-weight:bold; text-align:center;">% En volumen</td>
      <td style="width:25%; padding:4px; font-weight:bold; text-align:center;">No. CAS</td>
    </tr>

    {{-- Filas de componentes principales --}}
    @if(!empty($componentes) && count($componentes))
      @foreach($componentes as $c)
        <tr>
          <td class="p4 l">{{ $c['nombre'] ?? '' }}</td>
          <td class="p4 c">{{ $c['porcentaje'] ?? '' }}</td>
          <td class="p4 c">{{ $c['cas'] ?? '' }}</td>
        </tr>
      @endforeach
    @else
      <tr>
        <td class="p4" style="height:4px;">&nbsp;</td>
        <td class="p4">&nbsp;</td>
        <td class="p4">&nbsp;</td>
      </tr>
      <tr>
        <td class="p4" style="height:4px;">&nbsp;</td>
        <td class="p4">&nbsp;</td>
        <td class="p4">&nbsp;</td>
      </tr>
    @endif

    <tr>
      <td colspan="3" style="background:#f2f2f2; font-weight:bold; padding:4px;">
        Aditivos/Coadyuvantes
      </td>
    </tr>

    @if(!empty($aditivos) && count($aditivos))
      @foreach($aditivos as $a)
        <tr>
          <td class="p4 l">{{ $a['nombre'] ?? '' }}</td>
          <td class="p4 c">{{ $a['porcentaje'] ?? '' }}</td>
          <td class="p4 c">{{ $a['cas'] ?? '' }}</td>
        </tr>
      @endforeach
    @else
      <tr>
        <td class="p4" style="height:4px;">&nbsp;</td>
        <td class="p4">&nbsp;</td>
        <td class="p4">&nbsp;</td>
      </tr>
      <tr>
        <td class="p4" style="height:4px;">&nbsp;</td>
        <td class="p4">&nbsp;</td>
        <td class="p4">&nbsp;</td>
      </tr>
    @endif
  </table>

  {{-- ================= 4. PRIMEROS AUXILIOS ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <!-- Encabezado -->
    <tr class="th-green">
      <td colspan="2" style="font-weight:bold; padding:4px;">
        SECCIÓN 4.- PRIMEROS AUXILIOS
      </td>
    </tr>

    <tr>
      <td style="padding:4px; background:#f2f2f2; width:35%;"><b>CONTACTO CON LOS OJOS</b></td>
      <td style="padding:4px; width:65%;">{{ $aux_ojos ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;"><b>CONTACTO CON LA PIEL</b></td>
      <td style="padding:4px;">{{ $aux_piel ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;"><b>INGESTIÓN</b></td>
      <td style="padding:4px;">{{ $aux_ingestion ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;"><b>INHALACIÓN</b></td>
      <td style="padding:4px;">{{ $aux_inhalacion ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;"><b>SÍNTOMAS Y EFECTOS MÁS</b></td>
      <td style="padding:4px;">{{ $aux_sintomas ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;"><b>AGUDOS O CRÓNICOS</b></td>
      <td style="padding:4px;">{{ $aux_agudos ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;"><b>TRATAMIENTO ESPECIAL</b></td>
      <td style="padding:4px;">{{ $aux_tratamiento ?? '' }}</td>
    </tr>
  </table>

  {{-- ================= 5. MEDIDAS CONTRA INCENDIOS ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <!-- Encabezado -->
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 5.- MEDIDAS CONTRA INCENDIOS
      </td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;"><b>MEDIOS DE EXTINCIÓN APROPIADOS</b><br>{{ $extincion ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;"><b>PELIGROS ESPECÍFICOS DE LA SUSTANCIA</b><br>{{ $peligros_incendio ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;"><b>QUÍMICA PELIGROSA O MEZCLA</b><br>{{ $quimica_peligrosa ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;">
        <b>MEDIDAS ESPECIALES QUE DEBERÁN SEGUIR LOS GRUPOS DE COMBATE CONTRA INCENDIO</b><br>{{ $medidas_especiales ?? '' }}
      </td>
    </tr>
  </table>

  {{-- ================= 6. MEDIDAS EN CASO DE FUGA O DERRAME ACCIDENTAL ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <!-- Encabezado -->
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 6.- MEDIDAS EN CASO DE FUGA O DERRAME ACCIDENTAL
      </td>
    </tr>
    <!-- Filas -->
    <tr>
      <td style="padding:4px; background:#f2f2f2;"><b>EQUIPO DE PROTECCIÓN PERSONAL</b><br>{{ $fuga_equipo ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;"><b>PRECAUCIONES MEDIO AMBIENTE</b><br>{{ $fuga_precauciones ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;"><b>MÉTODOS Y MATERIALES DE CONTENCIÓN Y LIMPIEZA</b><br>{{ $fuga_metodos ?? '' }}</td>
    </tr>
  </table>

  {{-- ================= 7. MANEJO Y ALMACENAMIENTO ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <!-- Encabezado -->
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 7.- MANEJO Y ALMACENAMIENTO
      </td>
    </tr>
    <!-- Filas -->
    <tr>
      <td style="padding:4px; background:#f2f2f2;"><b>MANEJO SEGURO</b><br>{{ $manejo_seguro ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;"><b>ALMACENAMIENTO SEGURO</b><br>{{ $almacenamiento_seguro ?? '' }}</td>
    </tr>
  </table>

  {{-- ================= 8. CONTROLES DE EXPOSICIÓN / PROTECCIÓN PERSONAL ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <!-- Encabezado -->
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 8.- CONTROLES DE EXPOSICIÓN / PROTECCIÓN PERSONAL
      </td>
    </tr>
    <!-- Filas -->
    <tr>
      <td style="padding:4px; background:#f2f2f2;"><b>PARÁMETROS DE CONTROL</b><br>{{ $control_parametros ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;"><b>CONTROLES TÉCNICOS APROPIADOS</b><br>{{ $controles_tecnicos ?? '' }}</td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;"><b>PROTECCIÓN PERSONAL</b><br>{{ $proteccion_personal ?? '' }}</td>
    </tr>
  </table>

  {{-- ================= 9. PROPIEDADES FÍSICAS Y QUÍMICAS ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 9.- PROPIEDADES FÍSICAS Y QUÍMICAS
      </td>
    </tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>APARIENCIA</b><br>{{ $apariencia ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>COLOR</b><br>{{ $color ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>OLOR</b><br>{{ $olor ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>UMBRAL OLFATIVO</b><br>{{ $umbral_olfativo ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>pH</b><br>{{ $ph ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>PUNTO DE FUSIÓN</b><br>{{ $fusion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>PUNTO INICIAL E INTERVALO DE EBULLICIÓN</b><br>{{ $ebullicion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>PUNTO DE INFLAMACIÓN</b><br>{{ $inflamacion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>VELOCIDAD DE EVAPORACIÓN</b><br>{{ $evaporacion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>INFLAMABILIDAD (SÓLIDO O GAS)</b><br>{{ $inflamabilidad ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>LÍMITE SUPERIOR / INFERIOR DE INFLAMABILIDAD O EXPLOSIÓN</b><br>{{ $limite_inflamabilidad ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>PRESIÓN DE VAPOR</b><br>{{ $presion_vapor ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>DENSIDAD DE VAPOR</b><br>{{ $densidad_vapor ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>DENSIDAD RELATIVA</b><br>{{ $densidad_relativa ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>SOLUBILIDAD (ES)</b><br>{{ $solubilidad ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>COEFICIENTE DE PARTICIÓN n-OCTANOL/AGUA</b><br>{{ $coef_particion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>TEMPERATURA DE IGNICIÓN ESPONTÁNEA</b><br>{{ $ignicion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>TEMPERATURA DE DESCOMPOSICIÓN</b><br>{{ $descomposicion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>VISCOSIDAD (CINEMÁTICA)</b><br>{{ $viscosidad ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>PESO MOLECULAR</b><br>{{ $peso_molecular ?? '' }}</td></tr>
  </table>

  {{-- ================= 10. ESTABILIDAD Y REACTIVIDAD ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 10.- ESTABILIDAD Y REACTIVIDAD
      </td>
    </tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>REACTIVIDAD</b><br>{{ $reactividad ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>ESTABILIDAD QUÍMICA</b><br>{{ $estabilidad_quimica ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>POSIBILIDAD DE REACCIONES PELIGROSAS</b><br>{{ $reacciones ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>CONDICIONES QUE DEBEN EVITARSE</b><br>{{ $condiciones ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>MATERIALES INCOMPATIBLES</b><br>{{ $incompatibles ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>PRODUCTOS DE DESCOMPOSICIÓN PELIGROSOS</b><br>{{ $productos ?? '' }}</td></tr>
  </table>

  {{-- ================= 11. INFORMACIÓN TOXICOLÓGICA ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 11.- INFORMACIÓN TOXICOLÓGICA
      </td>
    </tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>TOXICIDAD AGUDA</b><br>{{ $toxicidad_aguda ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>CORROSIÓN/IRRITACIÓN CUTÁNEA</b><br>{{ $irritacion_cutanea ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>LESIÓN OCULAR GRAVE / IRRITACIÓN OCULAR</b><br>{{ $irritacion_ocular ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>SENSIBILIZACIÓN RESPIRATORIA O CUTÁNEA</b><br>{{ $sensibilizacion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>MUTAGENICIDAD EN CÉLULAS GERMINALES</b><br>{{ $mutagenicidad ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>CARCINOGENICIDAD</b><br>{{ $carcinogenicidad ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>TOXICIDAD PARA LA REPRODUCCIÓN</b><br>{{ $reproduccion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>TOXICIDAD SISTEMÁTICA ESPECÍFICA DEL ÓRGANO BLANCO - EXPOSICIÓN ÚNICA</b><br>{{ $sistemica_unica ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>TOXICIDAD SISTEMÁTICA ESPECÍFICA DEL ÓRGANO BLANCO - EXPOSICIONES REPETIDAS</b><br>{{ $sistemica_repetida ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>PELIGRO POR ASPIRACIÓN</b><br>{{ $aspiracion ?? '' }}</td></tr>
  </table>

  {{-- ================= 12. INFORMACIÓN ECOTOXICOLÓGICA ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 12.- INFORMACIÓN ECOTOXICOLÓGICA
      </td>
    </tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>TOXICIDAD</b><br>{{ $eco_toxicidad ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>PERSISTENCIA Y DEGRADABILIDAD</b><br>{{ $eco_persistencia ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>POTENCIAL DE BIOACUMULACIÓN</b><br>{{ $eco_bioacumulacion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>MOVILIDAD EN EL SUELO</b><br>{{ $eco_movilidad ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>OTROS EFECTOS ADVERSOS</b><br>{{ $eco_otros ?? '' }}</td></tr>
  </table>

  {{-- ================= 13. INFORMACIÓN RELATIVA A LA ELIMINACIÓN DEL PRODUCTO ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 13.- INFORMACIÓN RELATIVA A LA ELIMINACIÓN DEL PRODUCTO
      </td>
    </tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>MÉTODOS DE ELIMINACIÓN</b><br>{{ $eliminacion_metodos ?? '' }}</td></tr>
  </table>

  {{-- ================= 14. INFORMACIÓN RELATIVA AL TRANSPORTE ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 14.- INFORMACIÓN RELATIVA AL TRANSPORTE
      </td>
    </tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>NÚMERO ONU</b><br>{{ $trans_onu ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>DESIGNACIÓN OFICIAL DE TRANSPORTE</b><br>{{ $trans_designacion ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>CLASE RELATIVAS AL TRANSPORTE</b><br>{{ $trans_clase ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>GRUPO DE EMBALAJE</b><br>{{ $trans_embalaje ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f2f2f2;"><b>RIESGOS AMBIENTALES</b><br>{{ $trans_riesgos ?? '' }}</td></tr>
    <tr><td style="padding:4px; background:#f9f9f9;"><b>PRECAUCIONES ESPECIALES PARA EL USUARIO</b><br>{{ $trans_precauciones ?? '' }}</td></tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;">
        <b>TRANSPORTE A GRANEL CON ARREGLO AL ANEXO II DE MARPOL 73/78 Y AL CÓDIGO IBC</b><br>{{ $trans_granel ?? '' }}
      </td>
    </tr>
  </table>

  @php
    // ¿Hay algún dato de transporte?
    $__t_vals = [
      $trans_onu ?? '', $trans_designacion ?? '', $trans_clase ?? '',
      $trans_embalaje ?? '', $trans_riesgos ?? '', $trans_precauciones ?? '',
      $trans_granel ?? ''
    ];
    $__hasTransport = false;
    foreach ($__t_vals as $__v) {
        if (is_string($__v) && trim($__v) !== '') { $__hasTransport = true; break; }
    }
  @endphp

  {{-- ================= 15. INFORMACIÓN REGLAMENTARIA ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <tr class="th-green">
      <td style="font-weight:bold; padding:4px;">
        SECCIÓN 15.- INFORMACIÓN REGLAMENTARIA
      </td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f2f2f2;">
      Regulaciones federales, estatales e internacionales de seguridad, salud y medio ambiente/legislación específica para la sustancia o la mezcla.
      </td>
    </tr>
    <tr>
      <td style="padding:4px; background:#f9f9f9;">
        Conforme a la NOM-018-STPS-2015. La sustancia es clasificada y etiquetada de acuerdo con el Sistema Globalmente Armonizado (SGA).
      </td>
    </tr>
  </table>

{{-- ================= 16. OTRA INFORMACIÓN ================= --}}
  <table class="tbl b1" style="margin-top:15px; font-size:9pt; border:1px solid #92D050; width:100%; border-collapse:collapse;">
    <tr class="th-green">
      <td colspan="2" style="font-weight:bold; padding:4px;">
        SECCIÓN 16.- OTRA INFORMACIÓN
      </td>
    </tr>

    <tr style="background:#f2f2f2;">
      <td style="width:40%; padding:4px;"><b>ABREVIATURAS Y ACRÓNIMOS</b></td>
      <td style="width:60%; padding:0;">
        <div style="padding:4px;"><b>NA</b>: No aplica</div>
        <div style="padding:4px;"><b>ND</b>: No disponible / No hay datos</div>
        <div style="padding:4px;"><b>DL<sub>50</sub></b>: Dosis letal media</div>
        <div style="padding:4px;"><b>CL<sub>50</sub></b>: Concentración letal media</div>
        <div style="padding:4px;"><b>CE<sub>50</sub></b>: Concentración efectiva media</div>
      </td>
    </tr>

    <tr style="background:#f2f2f2;">
      <td style="width:40%; padding:4px;"><b>FECHA DE ELABORACIÓN</b></td>
      <td style="width:60%; padding:4px;">{{ $fecha_elaboracion ?? '30/Enero/2023' }}</td>
    </tr>

    <tr style="background:#f9f9f9;">
      <td style="padding:4px;"><b>NÚMERO DE REVISIÓN</b></td>
      <td style="padding:4px;">{{ $revision ?? '1' }}</td>
    </tr>

    <tr style="background:#f2f2f2;">
      <td style="padding:4px;"><b>REFERENCIA</b></td>
      <td style="padding:4px;">Elaborado bajo la NOM-018-STPS-2015</td>
    </tr>

    <tr>
      <td colspan="2" style="padding:4px; text-align:justify;">
        La información proporcionada en esta hoja de seguridad se considera correcta, pero no es exhaustiva y deberá utilizarse únicamente como orientación. Los datos contenidos en este documento están basados en el conocimiento actual de la sustancia química o mezcla y es aplicable a las precauciones de seguridad apropiadas para el producto. Success Suministros Sustentables, la proporción de buena fe, sin embargo, no hace ninguna representación en cuanto a su integridad o exactitud. Es intención que se utilice este documento sólo como una guía para el manejo del material con las precauciones de seguridad apropiadas por personal capacitado en el uso y manejo de este producto.
      </td>
    </tr>
  </table>

</div>
</body>
</html>
