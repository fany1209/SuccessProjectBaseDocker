@php
    $date = new DateTime($evaluation_date);
    $month = (int) $date->format('n');
    $year = $date->format('Y');
    if($month >= 1 && $month <= 3) {
        $month_start = "January";
        $month_end = "March";
    }elseif($month >= 4 && $month <= 6) {
        $month_start = "April";
        $month_end = "July";
    }elseif($month >= 7 && $month <= 9) {
        $month_start = "August";
        $month_end = "October";
    }elseif($month >= 10 && $month <= 12) {
        $month_start = "November";
        $month_end = "December";
    }
    $period = "$month_start - $month_end, $year";
    $questions = [
        (object)[ 
            "question" => "Cumplimiento en producto (Entrega de producto en tiempo)", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Cumple siempre o entrega antes de lo pactado",
                "2" => "Incumple eventualmente",
                "3" => "Incumple permanentemente"
            ]
        ],
        (object)[ 
            "question" => "Entrega de producto en cantidad", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Siempre cumple con las cantidades pedidas ocomprometidas",
                "2" => "Algunas veces no cumple con las cantidades pedidas ocomprometidas",
                "3" => "Generalmente incumple con las cantidades pedidas o comprometidas"
            ]
        ],
        (object)[ 
            "question" => "Cumplimiento en servicios (Entrega de servicios en tiempo)", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Cumple siempre o entrega antes de lo pactado",
                "2" => "Incumple eventualmente",
                "3" => "Incumple permanentemente"
            ]
        ],
        (object)[ 
            "question" => "Entrega de servicio en cantidad y forma", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Siempre cumple con las cantidades pedidas o comprometidas",
                "2" => "Algunas veces no cumple con las cantidades pedidas o comprometidas",
                "3" => "Generalmente incumple con las cantidades pedidas o comprometidas"
            ]
        ],
        (object)[ 
            "question" => "Calidad (Conformidad)", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Siempre cumple con las especificaciones del producto o servicio prestado",
                "2" => "Algunas veces cumple con la calidad del producto o servicio prestado",
                "3" => "La mayoría de las veces no cumple con la calidad del producto o servicio prestado"
            ]
        ],
        (object)[ 
            "question" => "Capacidad de respuesta", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Atiende compras urgentes de forma inmediata",
                "2" => "La capacidad para cumplir urgencias no es la suficiente",
                "3" => "No tiene la capacidad para cubrir urgencias"
            ]
        ],
        (object)[ 
            "question" => "Gestión (Facturación)", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "La facturacion es oportuna",
                "2" => "La factura es ocasional",
                "3" => "No cumple oportunamente con la facturacion"
            ]
        ],
        (object)[ 
            "question" => "Post contractual (Reclamaciones)", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "Atiende oportunamente las reclamaciones presentadas",
                "2" => "Atiende ocacionalmente las reclamaciones presentadas",
                "3" => "No atiende reclamaciones"
            ]
        ],
        (object)[ 
            "question" => "Servicio post venta", 
            "answers" =>[
                "0" => "No aplica",
                "1" => "La asesoria es oportuna y acertada",
                "2" => "La asesoria es ocasional",
                "3" => "No presenta servicio de asesorias"
            ]
        ],
    ];
@endphp
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>EVALUACION DE PROVEEDORES</title>
  <style>
    @page { margin: 140px 24px 80px 24px; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body { margin:0; font-family: Arial, Helvetica, sans-serif; font-size:11pt; color:#000; }
    .tbl { width:100%; border-collapse:collapse; table-layout:fixed; }
    .b1 td, .b1 th { border:1px solid #000; }
    .c { text-align:center; }
    .l { text-align:left; }
    .r { text-align:right; }
    .p4 { padding:4px; }
    .p6 { padding:6px; }
    .t9 { font-size:9pt; }
    .th-green { background:#92D050; font-weight:bold; color:#000; }
    .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
    td { word-wrap: break-word; overflow-wrap:anywhere; }
    .no-split {
      page-break-inside: avoid;
    }

    header { position:fixed; top:-120px; left:0; right:0; height:110px; z-index:10; }
    footer { position:fixed; bottom:-60px; left:0; right:0; height:60px; font-size:9pt; z-index:10; }

  ul {
    list-style-type: none;
    padding: 0;
  }

  li {
    margin: 5px 0;
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
          EVALUACIÓN DE PROVEEDORES
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
                30-Enero-2023
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

      <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>SSS-FOR-COM-06
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
      </td>
    </tr>
  </table>
</header>

<main style="margin-top:6px;">

  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="6">DATOS DEL PROVEEDOR / EVALUACIÓN</td>
    </tr>
    <tr>
      <td class="p4" colspan="6"><b>Proveedor:</b> {{ $supplier ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4" colspan="3"><b>RFC:</b> {{ $rfc ?? '' }}</td>
      <td class="p4" colspan="3"><b>Dirección:</b> {{ $address ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4" colspan="2"><b>Fecha de evaluación:</b> {{ $evaluation_date ?? '' }}</td>
      <td class="p4" colspan="2"><b>Evaluador(a):</b> {{ $evaluator ?? '' }}</td>
      <td class="p4" colspan="2"><b>Periodo evaluado:</b> {{ $period ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4" colspan="6"><b>Producto/servicio evaluado:</b> {{ $products ?? '' }}</td>
    </tr>
  </table>

  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c" style="font-weight:bold;">
      <td class="p4" style="width:30%;">CARACTERISTICAS / CRITERIO</td>
      <td class="p4">CRITERIOS</td>
      <td class="p4" style="width:12%;">CALIFICACION</td>
    </tr>

    @foreach ($questions as $index => $question)        
    <tr>
      <td class="p4">
          <b>{{ $question->question }}</b>
      </td>
      <td class="p4">
        <ul>
          @foreach ($question->answers as $key => $answer)
          <li>
            <input type="checkbox" {{ $questions_answers[$index]['answer'] == $key ? 'checked' : '' }} ><label>{{ $answer }}</label>
          </li>
          @endforeach
        </ul>
      </td>
      <td class="p4 c">{{ $questions_answers[$index]['qualification'] }}</td>
    </tr>
    @endforeach
  </table>

  {{-- =================== OBSERVACIONES =================== --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px; page-break-before: always;">
    <tr class="th-green c">
      <td class="p4">OBSERVACIONES / ACCIONES DE MEJORA</td>
    </tr>
    <tr>
      <td class="p6" style="height:90px; vertical-align:top;">{{ $observations ?? '' }}</td>
    </tr>
  </table>

  {{-- =================== TABLA DE CLASIFICACIÓN FINAL =================== --}}
  <table class="tbl b1 no-split" style="font-size:10pt; margin-top:18px;">
    <tr class="th-green c">
      <td class="p4" colspan="3">CLASIFICACIÓN DEL PROVEEDOR</td>
    </tr>

    <tr class="c" style="font-weight:bold; background:#eaeaea;">
      <td class="p4" style="width:18%;">Calificación obtenida</td>
      <td class="p4" style="width:10%;">Categoría</td>
      <td class="p4">Clasificación</td>
    </tr>

    <tr>
      <td class="p4 c">100 - 90</td>
      <td class="p4 c">A</td>
      <td class="p4">
        Confiable, cumple ampliamente los requisitos para asegurar la calidad de los productos.
        Preferirlo al comprar.
      </td>
    </tr>

    <tr>
      <td class="p4 c">90 - 70</td>
      <td class="p4 c">B</td>
      <td class="p4">
        Aceptable, cumple satisfactoriamente con requisitos para asegurar la calidad de lo suministrado.
      </td>
    </tr>

    <tr>
      <td class="p4 c">&lt; 70</td>
      <td class="p4 c">C</td>
      <td class="p4">
        No confiable, los productos suministrados deben ser sometidos a inspecciones rigurosas.
        Requiere de asesoría y seguimiento permanente.
        Comprarle cuando el proveedor de categoría A y B no pueda cumplir.
      </td>
    </tr>
  </table>


</main>
</body>
</html>
