<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF Document</title>
    <style>
        .text-white {
            color: white;
        }

        .text-green {
            color: greenyellow;
        }

        .header {
            border-collapse: collapse;
            width: 100%;
        }

        .header td {
            border: 1px solid #555555;
            text-align: center;
        }

        .title {
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            margin: 0;
        }

        .text-header {
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        p {
            margin: 0%;
            padding: 0%;
        }

        .w-full {
            width: 100%;
        }

        .h-full {
            height: 100%;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-uxs {
            font-size: 0.55rem;
            line-height: 1rem;
        }

        .text-xs {
            font-size: 0.75rem;
            line-height: 1rem;
        }

        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .text-base {
            font-size: 1rem;
            line-height: 1.5rem;
        }

        .text-lg {
            font-size: 1.125rem;
            line-height: 1.75rem;
        }

        .text-xl {
            font-size: 1.25rem;
            line-height: 1.75rem;
        }

        .text-in-line {
            display: inline-block;

        }

        .table-bordered {
            border-collapse: collapse;
            border: 1px solid #000000;
            opacity: 0.2;
        }

        .table-bordered td {
            border: 1px solid #000000;
            padding: 0px 5px 0px 5px;
            font-size: 0.75rem;
            line-height: 1rem;
            height: 25px;
        }

        .table-bordered th {
            border: 1px solid #000000;
            font-size: 0.75rem;
            line-height: 1rem;
        }

        header {
            position: fixed;
            left: 0;
            right: 0;
            top: -79.3;
            margin-left: 1.2cm;
            margin-right: 1.2cm;
        }

        main {
            margin-left: 1.2cm;
            margin-right: 1.2cm;
        }

        footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -114;
            height: 140px;

            /* background-color: #053A59; */

            /* padding: 5px; */
        }

        @page {
            margin-top: 4cm;
            margin-left: 0;
            margin-right: 0;
            margin-bottom: 4cm;
        }

        .triangle-up {
            width: .5px;
            height: 0;
            border-right: 1000px solid transparent;
            border-bottom: 139px solid #053A59;

        }
    </style>
</head>

<body>
    @php
        $currentPage = 1;
        $products = count($quote->products);
        $totalPages = 1;
        $count = 1;
        $cicle_index = 0;
        if ($products >= 11) {
            $totalPages = 2;
        }
    @endphp
    <header>
        <table class="header">
            <tbody>
                <tr>
                    <td rowspan="2" style="width: 10%;">
                        <img src="images/logo.png" width="144">
                    </td>

                    <td colspan="3">
                        <p class="title">
                            COTIZACIÓN
                        </p>
                    </td>

                    <td>
                        <p class="text-header">
                            Código:<br>SSS-FOR-COM-03
                        </p>
                    </td>
                </tr>

                <tr>

                    <td>
                        <p class="text-header">
                            Fecha de elaboración:<br>30-Enero-2023
                        </p>
                    </td>

                    <td>
                        <p class="text-header">
                            Fecha de actualización: --
                        </p>
                    </td>

                    <td style="width: 11%;">
                        <p class="text-header">
                            Versión: 00
                        </p>
                    </td>

                    <td>
                        <p class="text-header">
                            Pág. {{ $count >= 13 ? '2' : '1' }} de {{ $totalPages }}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </header>

    <footer>
        <div class="triangle-up text-in-line">
            <div style="padding: 50px 0px 0px 25px; width: 400px;">
                <table>
                    <tbody>
                        <tr>
                            <td style="width: 160px;">
                                <p class="text-uxs text-white">Emiliano Zapata No.7</p>
                            </td>
                            <td>
                                <p class="text-uxs text-green">+52 461 156 8547</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="text-uxs text-white">Col. Rancho Nuevo C.P. 38197</p>
                            </td>
                            <td>
                                <p class="text-uxs text-green">+52 461 616 9975</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p class="text-uxs text-white">Apaseo el Grande, Gto. México</p>
                            </td>
                            <td>
                                <p class="text-uxs text-green">www.suministrossustentables.com</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </footer>

    <main>
        <div class="w-full">
            <table class="w-full table-bordered">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <p class="text-in-line font-bold">Empresa:</p>
                            <p class="text-in-line">{{ $quote->company }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 100%;">
                            <p class="text-in-line font-bold">Folio:</p>
                            <p class="text-in-line">{{ $quote->folio }}</p>
                        </td>

                        <td style="width: 100%;">
                            <p class="text-in-line font-bold">Fecha:</p>
                            <p class="text-in-line">{{ $quote->created_at->format('Y-m-d') }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="width: 100%;">
                            <p class="text-in-line font-bold">Atención:</p>
                            <p class="text-in-line">{{ $quote->attention }}</p>
                        </td>

                        <td style="width: 100%;">
                            <p class="text-in-line font-bold">Departamento:</p>
                            <p class="text-in-line">{{ $quote->department }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="width: 100%;">
                            <p class="text-in-line font-bold">Teléfono:</p>
                            <p class="text-in-line">{{ $quote->phone }}</p>
                        </td>

                        <td style="width: 100%;">
                            <p class="text-in-line font-bold">Email:</p>
                            <p class="text-in-line">{{ $quote->email }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <br>

        <div class="w-full">
            <p class="text-xs">Estimado(a) {{ $quote->attention }}, tengo el gusto de responder a tu solicitud. A
                continuación,
                encontrarás la cotización
                {{ $products > 1 ? 'de los productos solicitados' : 'del producto solicitado.' }}
            </p>
        </div>

        <br>

        {{-- SECCION DE PRODUCTOS --}}
        <div class="w-full">
            <table class="w-full table-bordered">
                <thead>
                    <tr class="text-center" style="background-color: #d4d4d4">
                        <td>
                            <p class="font-bold">Producto</p>
                        </td>

                        <td>
                            <p class="font-bold">Cantidad</p>
                        </td>

                        <td>
                            <p class="font-bold">Unidad</p>
                        </td>

                        <td>
                            <p class="font-bold">Presentación</p>
                        </td>

                        <td>
                            <p class="font-bold">Precio por unidad</p>
                        </td>

                        <td>
                            <p class="font-bold">Importe</p>
                        </td>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($quote->products as $item)
                        @php
                            $count++;
                            if ($cicle_index >= 13) {
                                $currentPage = 2;
                            }
                            $cicle_index++;
                        @endphp
                        <tr>
                            <td>
                                <p>{{ $item->pivot->quote_product_name }}</p>
                            </td>

                            <td>
                                <p>{{ $item->pivot->quantity }}</p>
                            </td>

                            <td>
                                <p>{{ $item->unit }}</p>
                            </td>

                            <td>
                                <p>{{ $item->pivot->presentation }}</p>
                            </td>

                            <td>
                                <p>${{ number_format($item->pivot->cost, 2, '.', ',') }}</p>
                            </td>

                            <td>
                                <p>${{ number_format($item->pivot->quantity * $item->pivot->cost, 2, '.', ',') }}</p>
                            </td>
                        </tr>
                    @endforeach

                    {{-- <tr>
                        <td colspan="4" class="text-right">
                            <p class="font-bold">Subtotal</p>
                        </td>

                        <td>
                            <p>${{ number_format($subtotal, 2, '.', ',') }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="4" class="text-right">
                            <p class="font-bold">Iva</p>
                        </td>

                        <td>
                            <p>${{ number_format($iva, 2, '.', ',') }}</p>
                        </td>
                    </tr> --}}

                    <tr>
                        <td colspan="5" class="text-right">
                            <p class="font-bold">Total</p>
                        </td>

                        <td>
                            <p class="font-bold">${{ number_format($total, 2, '.', ',') }}</p>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <br>
        <br>

        <div class="w-full">
            <table class="w-full table-bordered">

                <tbody>
                    <tr>
                        <td>
                            <p class="text-in-line font-bold">Lugar de entrega:</p>
                            <p class="text-in-line">{{ $quote->place_of_delivery }}</p>
                        </td>
                    </tr>

                    {{-- <tr>
                        <td>
                            <p class="text-in-line font-bold">Presentación:</p>
                            <p class="text-in-line">{{$quote->}}</p>
                        </td>
                    </tr> --}}

                    <tr>
                        <td>
                            <p class="text-in-line font-bold">Especificación de transporte:</p>
                            <p class="text-in-line">{{ $quote->transport_specification }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p class="text-in-line font-bold">Fecha de entrega:</p>
                            <p class="text-in-line">{{ $quote->deadline }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p class="text-in-line font-bold">Términos y condiciones:</p>
                            <p class="text-in-line">{{ $quote->terms }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p class="font-bold">Nota:</p>
                            <p>Los precios antes mencionados son netos.</p>
                            <p>La cotización es válida por 30 días a partir de su emisión.</p>
                            <p>{{ $quote->notes }}</p>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>

        <br>

        <div class="w-full">
            <p class="text-xs">Esperando que nuestra propuesta sea de tu interés, quedo pendiente de tus indicaciones
                para proseguir con nuestro proyecto.
            </p>
        </div>

        <br>

        <div class="w-full">
            <p class="text-xs">Atentamente,</p>
            <p class="text-xs font-bold">{{ $quote->user->name }}</p>
        </div>

    </main>
</body>

</html>
