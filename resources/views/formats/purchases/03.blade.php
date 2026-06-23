<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla Comparativa - {{ $folio }}</title>
    <style>
        @page {
            margin: 150px 24px 24px 24px; 
        }
        header {
            position: fixed;
            top: -130px;    
            left: 0; right: 0;
            height: 110px;  
        }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 8.5pt; }
        .tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .b1 td, .b1 th { border: 0.75pt solid #000; }
        .c { text-align: center; }
        .p4 { padding: 4px; }
        
        .folio-right {
            text-align: right;
            margin-bottom: 5px;
            font-size: 10pt;
            font-weight: bold;
            color: #333;
        }

        .th-green {
            background: #92D050;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
        }
        .prod-img { 
            height: 60px; 
            width: 60px; 
            object-fit: contain; 
            display: block; 
            margin: 0 auto;
        }
        .price { font-weight: bold; white-space: nowrap; }

        .link-text {
            font-size: 7pt;
            word-wrap: break-word;
            color: #0056b3;
            text-decoration: none;
        }
    </style>
</head>
<body>

{{-- ================= ENCABEZADO (Fijo) ================= --}}
<header>
    <table class="tbl b1">
        <tr>
            <td style="width:22%; text-align:center;">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:50px;">
            </td>
            
            <td style="width:58%; vertical-align:top; padding:0;">
                <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:0.75pt solid #000;">
                    TABLA COMPARATIVA
                </div>

                <table style="width:100%; border-collapse:collapse; font-size:8pt; border:none;">
                    <tr>
                        <td style="width:33.3%; border:none; border-right:0.75pt solid #000; padding:4px; text-align:center;">
                            <b>Fecha de elaboración:</b><br>
                            30-Enero-2023
                        </td>
                        <td style="width:33.3%; border:none; border-right:0.75pt solid #000; padding:4px; text-align:center;">
                            <b>Fecha de actualización:</b><br>
                            --
                        </td>
                        <td style="width:33.3%; border:none; padding:4px; text-align:center;">
                            <b>Versión:</b><br>00
                        </td>
                    </tr>
                </table>
            </td>

            <td style="width:20%; vertical-align:top; font-size:8pt; padding:0;">
                <div style="border-bottom:0.75pt solid #000; padding:10px; text-align:center; font-weight:bold;">
                    Código:<br>SSS-FOR-COM-03
                </div>
                <div style="padding:10px; text-align:center;">
                    <b>Página:</b> 1 de 1
                </div>
            </td>
        </tr>
    </table>
</header>

{{-- ================= CUERPO PRINCIPAL ================= --}}
<main>
    <div class="folio-right">
        FOLIO: {{ $folio }}
    </div>

    <table class="tbl b1 c">
        <thead>
            <tr class="th-green">
                <th style="width: 10%;">Insumo</th>
                <th style="width: 5%;">Cant.</th>
                <th style="width: 10%;">Proveedor</th>
                <th style="width: 8%;">Precio Unt.</th>
                <th style="width: 10%;">Imagen</th>
                <th style="width: 12%;">Descripción</th>
                <th style="width: 8%;">Entrega</th>
                <th style="width: 15%;">Link</th>
                <th style="width: 10%;">Precio Total</th>
                <th style="width: 12%;">Comentarios</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $p)
            <tr>
                <td style="font-weight: bold;">{{ $p->insumo }}</td>
                <td>{{ $p->cantidad }}</td>
                <td>{{ $p->proveedor }}</td>
                <td class="price">$ {{ number_format($p->precio_unt, 2) }}</td>
                <td class="p4">
                    @if($p->imagen)
                        <img src="{{ $p->imagen }}" class="prod-img">
                    @else
                        <span style="font-size: 7pt; color: #ccc;">Sin imagen</span>
                    @endif
                </td>
                <td style="text-align: left; font-size: 8pt;" class="p4">{{ $p->descripcion }}</td>
                <td>{{ $p->entrega_estimada ?? 'No indica' }}</td>
                <td class="p4">
                    @if($p->link)
                        <div class="link-text">{{ $p->link }}</div>
                    @else
                        --
                    @endif
                </td>
                <td class="price">$ {{ number_format($p->precio_total, 2) }}</td>
                <td style="text-align: left; font-size: 8pt;" class="p4">{{ $p->comentarios }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</main>

</body>
</html>