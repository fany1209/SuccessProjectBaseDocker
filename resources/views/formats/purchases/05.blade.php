@php
  $criteria = [
    (object)["factor" => 'Factor Economico', "criteria" => "Precio del producto", "description" => "El proveedor ofrece varios precios del producto; por ejemplo si ... que otorgan la competencia a comparación del proveedor actua"],
    (object)["factor" => '', "criteria" => "Descuentos", "description" => "El proveedor ofrece disponibilidad de descuentos acumulativos en... servicios especiales para clientes habituales y ofertas de productos."],
    (object)["factor" => '', "criteria" => "Seguros", "description" => "El proveedor ofrece en caso de perdida o daño parcial o total de la mercancía, la reposición de esta."],
    (object)["factor" => '', "criteria" => "Formas", "description" => "El proveedor ofrece distintas formas de pago como transferencias, efectivo, cheques, etc."],
    (object)["factor" => '', "criteria" => "Crédito", "description" => "El proveedor ofrece plazos de pago"],
    (object)["factor" => '', "criteria" => "Facturación", "description" => "El proveedor ofrece facturación rápida y sin complicaciones"],
    (object)["factor" => '', "criteria" => "Pedido mínimo", "description" => "El proveedor negocia la cantidad a consumir o fabricar de acuerdo a nuestras necesidades"],
    (object)["factor" => 'Factor de calidad', "criteria" => "Calidad de los productos", 'description' => "El proveedor ofrece la calidad que se requiere o mas en los productos solicitados"],
    (object)["factor" => '', "criteria" => "Garantías", 'description' => "El proveedor ofrece el tiempo de garantía en los productos "],
    (object)["factor" => '', "criteria" => "Servicio de atención técnica", 'description' => "El proveedor brinda asesoría técnica en la entrega del producto"],
    (object)["factor" => '', "criteria" => "Servicio de atención al cliente", 'description' => "El proveedor nos ofrece un servicio o producto con atención adecuada a nuestra necesidad"],
    (object)["factor" => '', "criteria" => "Asesoramiento técnico", 'description' => "El proveedor ofrece asistencia técnica posterior a la compra para aclaración de dudas y/o recomendaciones"],
    (object)["factor" => 'Otros Factores', "criteria" => "Plazo de entrega ", 'description' => "El proveedor entrega en tiempo de acuerdo a lo solicitado "],
    (object)["factor" => '', "criteria" => "Devoluciones", 'description' => "El proveedor acepta la devolución de producto por que no cumple con las especificaciones, por defecto, por violación del sello de garantía y en servicio por que no cumplió la necesidad, por falta de asesoría técnica, deficiencia en el servicio en general."],
    (object)["factor" => '', "criteria" => "Capacidad", 'description' => "El proveedor tiene solvencia de producción o distribución del producto o servicio requerido"],
    (object)["factor" => '', "criteria" => "Cantidades mínimas que fabrica", 'description' => "El proveedor tiene flexibilidad en cuanto a la venta por la cantidad de fabricación y pueden llegar a un acuerdo"],
    (object)["factor" => '', "criteria" => "Surtimiento de cantidades solicitadas", 'description' => "El proveedor surte en cantidades solicitadas y no manda excesos"],
    (object)["factor" => '', "criteria" => "Sectores con los que tiene experiencia", 'description' => "El proveedor tiene experiencia de trabajar con los sectores del mismo rubro"],
    (object)["factor" => '', "criteria" => "Referencias comerciales", 'description' => "Esto brindará confianza y credibilidad en cuanto a los precios, procesos logísticos y materias primas."],
    (object)["factor" => '', "criteria" => "Ubicación", 'description' => "Considera aspectos como los tiempos de desplazamiento, los posibles retrasos, la flexibilidad en las entregas, etc."],
    (object)["factor" => '', "criteria" => "Tamaño de la empresa", 'description' => "El proveedor es una empresa seria por micro que sea"],
    (object)["factor" => '', "criteria" => "Proveedor único", 'description' => "No existe otro proveedor para este producto"],
    (object)["factor" => '', "criteria" => "Capacitación", 'description' => "Comparte los conocimientos necesarios de los productos que ofrece"],
    (object)["factor" => '', "criteria" => "Certificaciones", 'description' => "Tipos de certificaciones que tiene"],
    (object)["factor" => '', "criteria" => "Tecnología", 'description' => "Aporta innovaciones a los clientes"],
    (object)["factor" => '', "criteria" => "Seguridad", 'description' => "Ofrece un seguro contra robo de mercancía, así como demostrar protección contra los fraudes a través de certificaciones."]
  ];
  $veredict = '';
  if($result<60){
    $veredict = 'No aceptable';
  }elseif ($result >= 60 && $result <80) {
    $veredict = 'Aceptable, pero trabajar en desarrollar o mejorar al proveedor';
  }elseif ($result >= 80 && $result <=100) {
    $veredict = 'Aceptable';
  }
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Criterios de selección de proveedores</title>
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
    .cb { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; } /* para ☑/☐ */
    td { word-wrap: break-word; overflow-wrap:anywhere; }

    header { position:fixed; top:-120px; left:0; right:0; height:110px; z-index:10; }
    footer { position:fixed; bottom:-60px; left:0; right:0; height:60px; font-size:9pt; z-index:10; }
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
          CRITERIO DE SELECCIÓN DE PROVEEDORES
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:9pt;">
          <tr>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de elaboración:</b><br>
              {{ $fecha_elaboracion ?? '30-Enero-2023' }}
            </td>
            <td style="width:38%; border-right:1px solid #000; padding:4px; text-align:center;">
              <b>Fecha de modificación:</b> {{ $fecha_modificacion ?? '--' }}
            </td>
            <td style="width:24%; padding:4px; text-align:center;">
              <b>Versión:</b> {{ $version ?? '--' }}
            </td>
          </tr>
        </table>
      </td>

      <td style="width:20%; border:1px solid #000; vertical-align:top; font-size:7pt; padding:0;">
        <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
          <b>Código:</b><br>SSS-FOR-COM-05
        </div>
        <div style="padding:10px; text-align:center;">
          Pág. {{ $pagina_actual ?? 1 }} de {{ $paginas_total ?? 1 }}
        </div>
      </td>
    </tr>
  </table>
</header>

<main style="margin-top:6px;">

  {{-- ======= DATOS DEL PROVEEDOR ======= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="4">DATOS DEL PROVEEDOR</td>
    </tr>
    <tr>
      <td class="p4" colspan="4"><b>Nombre o razón social del proveedor:</b> {{ $supplier ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4" colspan="4"><b>Dirección:</b> {{ $address ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4" colspan="4"><b>Fecha:</b> {{ $date ?? '' }}</td>
    </tr>
    <tr>
      <td class="p4" colspan="4"><b>Productos a consumir:</b> {{ $products ?? '' }}</td>
    </tr>
  </table>

  {{-- ======= CRITERIOS ======= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:15 px;">
    <tr class="th-green c" style="font-weight:bold;">
      <td class="p4" style="width:16%;">CRITERIOS</td>
      <td class="p4" style="width:18%;">CRITERIO</td>
      <td class="p4">DESCRIPCION DEL CRITERIO</td>
      <td class="p4" style="width:11%;">CUMPLE</td>
      <td class="p4" style="width:11%;">NO CUMPLE</td>
    </tr>
    
    @foreach($checks as $index => $check)
      <tr>
        <td class="p4">{{ $criteria[$index]->factor }}</td>
        <td class="p4">{{ $criteria[$index]->criteria }}</td>
        <td class="p4">{{ $criteria[$index]->description }}</td>
        <td class="p4"><label><input type="checkbox" {{ $check == 1 ? 'checked' : '' }}></label></td>
        <td class="p4"><label><input type="checkbox" {{ $check == 0 ? 'checked' : '' }}></label></td>
      </tr>
    @endforeach
  </table>

  {{-- ======= RESULTADO ======= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:12px;">
    <tr class="th-green c">
      <td class="p4" colspan="3">RESULTADO</td>
    </tr>
    <tr>
      <td class="p6" style="width:25%;"><b>Total de cumplimiento:</b></td>
      <td class="p6" style="width:15%; font-weight:bold;" class="c">{{ $result }}%</td>
      <td class="p6"><b>Veredicto:</b> {{ $veredict }}</td>
    </tr>
  </table>

  {{-- ======= LEYENDA ======= --}}
  <table class="tbl b1" style="font-size:10pt; margin-top:8px;">
    <tr class="th-green c">
      <td class="p4" colspan="3">LEYENDA</td>
    </tr>
    <tr>
      <td class="p4" style="width:20%;">80-100</td>
      <td class="p4" style="width:30%;">Aceptable</td>
      <td class="p4">Recomendado</td>
    </tr>
    <tr>
      <td class="p4">60-79</td>
      <td class="p4">Aceptable, pero trabajar en desarrollar o mejorar al proveedor</td>
      <td class="p4">Condicionado</td>
    </tr>
    <tr>
      <td class="p4">&lt; 60</td>
      <td class="p4">No aceptable</td>
      <td class="p4">No recomendado</td>
    </tr>
  </table>

</main>
</body>
</html>
