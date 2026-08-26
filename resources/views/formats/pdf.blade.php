<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF Document</title>
    <style>
        .table1 {
            border-collapse: collapse;
            width: 100%;
        }

        .table2Cells {
            border: 1px solid #555555;
            text-align: left;
            padding-left: 5px;
            padding-right: 5px;
        }

        .cells-1 {
            border: 1px solid #555555;
            text-align: center;
        }

        .cells-2 {
            text-align: center;
        }

        .table-headers {
            border: 1px solid #444444;
            text-align: center;
            background-color: #46d64e;
        }

        .text-sm {
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        .text-sm-black {
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        .title {
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            margin: 0;
        }

        .subTitle {
            font-size: 15px;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
        }

        .fullDiv {
            width: 100%;
            text-align: center;
        }

        .inline-p p {
            display: inline-block;
        }
    </style>
</head>

<header>
    <table class="table1">
        <tbody>
            <tr>
                <td rowspan="2" style="width: 10%;" class="cells-1">
                    <img src="images/logo.png" width="144">
                </td>

                <td colspan="3" class="cells-1">
                    <p class="title">
                        {{ $title }}
                    </p>
                </td>

                <td class="cells-1">
                    <p class="text-sm">
                        @if ($type == 'inputs')
                            Código:<br>SSS-FOR-ALM-01
                        @else
                            Código:<br>SSS-FOR-ALM-04
                        @endif
                    </p>
                </td>
            </tr>

            <tr>

                <td class="cells-1">
                    <p class="text-sm">
                        Fecha de elaboración:<br>30-Enero-2023
                    </p>
                </td>

                <td class="cells-1">
                    <p class="text-sm">
                        Fecha de actualización: --
                    </p>
                </td>

                <td class="cells-1" style="width: 11%;">
                    <p class="text-sm">
                        Versión: 00
                    </p>
                </td>

                <td class="cells-1">
                    <p class="text-sm">
                        Pág. 1 de 1
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</header>

<body>
    <br>
    {{-- SECCION DE DATOS GENERALES --}}
    <div style="width: 100%;">
        <div class="fullDiv">
            <p class="subTitle">Datos Generales</p>
        </div>

        <div style="width: 100%;">
            <table class="table1">
                <tbody>
                    <tr>
                        <td colspan="2" class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">
                                {{ $type == 'inputs' ? 'Proveedor:' : 'Cliente:' }}</p>
                            <p class="text-sm-black">
                                {{ $type == 'inputs' ? ($movement->supplier->name ?? 'N/A') : ($movement->customer->name ?? 'N/A') }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">
                                {{ $type == 'inputs' ? 'Código de Proveedor:' : 'Código de cliente:' }}</p>
                            <p class="text-sm-black">
                                {{ $type == 'inputs' ? ($movement->supplier->supplier_code ?? 'N/A') : ($movement->customer->customer_code ?? 'N/A') }}
                            </p>
                        </td>
                        <td class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">
                                {{ $type == 'inputs' ? 'Fecha de recepción:' : 'Fecha de salida:' }}</p>
                            <p class="text-sm-black">{{ $movement->created_at->format('d/m/Y') }}</p>
                        </td>
                    </tr>

                    {{-- ✅ SOLO OUTPUTS: VENDEDOR --}}
                    @if ($type != 'inputs')
                    <tr>
                        <td colspan="2" class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">Vendedor:</p>
                            <p class="text-sm-black">{{ $movement->vendedor ?? 'N/A' }}</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <br>

    {{-- SECCION DE PRODUCTOS --}}
    <div style="width: 100%;">
        <div class="fullDiv">
            <p class="subTitle">Datos del Producto</p>
        </div>

        <div style="width: 100%;">
            @php
                $flag = false;
            @endphp
            @foreach ($movement->products as $product)
                @if ($product->pivot->label_batch)
                    @php
                        $flag = true;
                    @endphp
                @else
                    @php
                        $flag = false;
                    @endphp
                @endif
            @endforeach
            <table class="table1">
                <thead>
                    <tr>
                        <th class="table-headers" style="width: 4%">
                            <p class="text-sm-black">
                                N°
                            </p>
                        </th>

                        <th class="table-headers" style="width: 15%;">
                            <p class="text-sm-black">
                                SKU
                            </p>
                        </th>

                        <th class="table-headers">
                            <p class="text-sm-black">
                                Producto
                            </p>
                        </th>

                        <th class="table-headers" style="width: 19%;">
                            <p class="text-sm-black">
                                N° de lote
                            </p>
                        </th>

                        @if ($flag)
                            <th class="table-headers" style="width: 19%;">
                                <p class="text-sm-black">
                                    Lote de salida
                                </p>
                            </th>
                        @endif

                        <th class="table-headers" style="width: 13%;">
                            <p class="text-sm-black">
                                Cantidad
                            </p>
                        </th>

                        <th class="table-headers" style="width: 5%;">
                            <p class="text-sm-black">
                                UM
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movement->products as $product)
                        <tr>
                            <td class="cells-1">
                                <p class="text-sm-black">
                                    {{ $loop->index + 1 }}
                                </p>
                            </td>

                            <td class="cells-1">
                                <p class="text-sm-black">
                                    {{ $product->sku }}
                                </p>
                            </td>

                            <td class="cells-1">
                                <p class="text-sm-black">
                                    {{ $product->name }}
                                </p>
                            </td>

                            <td class="cells-1">
                                <p class="text-sm-black">
                                    {{ $product->pivot->warehouse_batch }}
                                </p>
                            </td>

                            @if ($flag)
                                <td class="cells-1">
                                    <p class="text-sm-black">
                                        {{ $product->pivot->label_batch }}
                                    </p>
                                </td>
                            @endif

                            <td class="cells-1">
                                <p class="text-sm-black">
                                    {{ $product->pivot->quantity }}
                                </p>
                            </td>

                            <td class="cells-1">
                                <p class="text-sm-black">
                                    {{ $product->unit }}
                                </p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <br>

    @if (!($type != 'inputs' && isset($movement->customer) && $movement->customer->customer_code === 'INT-PROD'))
    {{-- SECCION DE TRANSPORTE --}}
    <div style="width: 100%;">
        <div class="fullDiv">
            <p class="subTitle">Datos del Transporte</p>
        </div>

        <div style="width: 100%;">
            <table class="table1">
                <tbody>
                    <tr>
                        <td colspan="2" class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">
                                Línea de transporte:
                            </p>
                            <p class="text-sm-black">
                                {{ $movement->transportLine->name ?? 'N/A' }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">
                                Nombre del operador:
                            </p>
                            <p class="text-sm-black">
                                {{ $movement->operator }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">
                                N° de licencia:
                            </p>
                            <p class="text-sm-black">
                                {{ $movement->license_number }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">
                                Sello de seguridad:
                            </p>
                            <p class="text-sm-black">
                                {{ $movement->security_seal ? 'Si' : 'No' }}
                            </p>
                        </td>

                        <td class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">
                                Numero de sello de seguridad:
                            </p>
                            <p class="text-sm-black">
                                {{ $movement->security_seal ? $movement->security_seal_number : 'N/A' }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">
                                Placas de la unidad:
                            </p>
                            <p class="text-sm-black">
                                {{ $movement->unit_plates }}
                            </p>
                        </td>

                    </tr>

                    <tr>
                        <td colspan="2" class="table2Cells inline-p">
                            <p class="text-sm-black" style="font-weight: bold;">
                                Placas del remolque:
                            </p>
                            <p class="text-sm-black">
                                {{ isset($movement->trailer_plates) ? $movement->trailer_plates : 'N/A' }}
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <br>
    @endif

    {{-- SECCION DE COMENTARIOS --}}
    @if (!empty($movement->comments))
        <div style="width: 100%;">
            <div class="fullDiv">
                <p class="subTitle">Comentarios</p>
            </div>
            <div style="width: 100%;">
                <table class="table1">
                    <tbody>
                        <tr>
                            <td class="table2Cells inline-p" style="padding: 10px;">
                                <p class="text-sm-black">{{ $movement->comments }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <br>
    <br>
    <br>

    {{-- SECCION DE FIRMAS --}}
    @if ($type == 'inputs')
        <div style="width: 100%;">
            <table class="table1">
                <tbody>
                    <tr>
                        <td class="cells-2">
                            <p style="font-weight: bold; margin: 3;">
                                _______________________________
                            </p>
                            <p class="text-sm-black">
                                Nombre y firma de quien recibe
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        <div style="width: 100%;">
            <table class="table1">
                <tbody>
                    <tr>
                        <td class="cells-2">
                            <p style="font-weight: bold; margin: 3;">
                                _______________________________
                            </p>
                            <p class="text-sm-black">
                                Nombre y firma
                            </p>
                            <p class="text-sm-black">
                                Almacén
                            </p>
                        </td>

                        <td class="cells-2">
                            <p style="font-weight: bold; margin: 3;">
                                _______________________________
                            </p>
                            <p class="text-sm-black">
                                Nombre y firma
                            </p>
                            <p class="text-sm-black">
                                Operador
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</body>

</html>
