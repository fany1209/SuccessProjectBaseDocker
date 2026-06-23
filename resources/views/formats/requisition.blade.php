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
    </style>
</head>

<body>
    @php
        $currentPage = 1;
        $totalPages = 1;
    @endphp
    <header>
        <table class="header flex-c">
            <tbody>
                <tr>
                    <td rowspan="2" style="width: 10%;">
                            <img src="{{ public_path('images/logo.png') }}" width="144">
                    </td>

                    <td colspan="3">
                        <p class="title">
                            REQUISICIÓN DE COMPRAS
                        </p>
                    </td>

                    <td>
                        <p class="text-header">
                            Código:<br>SSS-FOR-COM-04
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
                            Pág. 1 de 1
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </header>

    <main>
        <br>
        <br>
        <div class="w-full">
            <table class="table-bordered" style='width: 35%;'>
                <tbody>
                    <tr>
                        <td>
                            <p class="text-xs"><b>Consecutivo:</b>{{ htmlspecialchars($requisition->consecutive, ENT_QUOTES, 'UTF-8') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="text-xs"><b>Folio Tabla Comp:</b> 
                                {{ $requisition->comparative ? htmlspecialchars($requisition->comparative->folio, ENT_QUOTES, 'UTF-8') : 'N/A' }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="text-xs"><b>Fecha de elaboración:</b>
                                {{ htmlspecialchars($requisition->created_at->format('d-m-Y'), ENT_QUOTES, 'UTF-8') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="text-xs"><b>N° orden de compra:</b>{{ htmlspecialchars($requisition->purchase_order, ENT_QUOTES, 'UTF-8') }}</p>
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
                        <td colspan="2">
                            <p class="text-xs"><b>Nombre del solicitante:</b>{{ htmlspecialchars($requisition->applicant, ENT_QUOTES, 'UTF-8') }}</p>
                        </td>

                        <td colspan="2">
                            <p class="text-xs"><b>Departamento:</b>{{ htmlspecialchars($requisition->department, ENT_QUOTES, 'UTF-8') }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="">
                            <p class="text-xs font-bold">Requiere ficha técnica:</p>
                        </td>
                        <td class="text-center" style="">
                            <p class="text-xs text-in-line">{{ $requisition->data_sheet ? 'Si' : 'No' }}</p>
                        </td>
                        <td style="">
                            <p class="text-xs font-bold">Requiere hoja de seguridad:</p>
                        </td>
                        <td class="text-center" style="">
                            <p class="text-xs text-in-line">{{ $requisition->safety_sheet ? 'Si' : 'No' }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <br>
        <br>

        <div class="w-full">
            <table class="w-full table-bordered">
                <thead>
                    <tr class="text-center" style="background-color: #92D050">
                        <td style="height: 45px;">
                            <p class="text-xs font-bold">Descripción</p>
                        </td>

                        <td style="width: 90px;">
                            <p class="text-xs font-bold">Proveedor</p>
                        </td>

                        <td style="width: 80px;">
                            <p class="text-xs font-bold">Uso</p>
                        </td>

                        <td style="">
                            <p class="text-xs font-bold">Cantidad</p>
                        </td>

                        <td style="width: 150px;">
                            <p class="text-xs font-bold">Imagen Ilustrativa</p>
                        </td>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($requisition->products as $product)
                        <tr class="text-center">
                            <td>
                                {{ htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8') }}
                            </td>

                            <td>
                                <a href="{{ htmlspecialchars($product->url, ENT_QUOTES, 'UTF-8') }}">{{ htmlspecialchars($product->supplier, ENT_QUOTES, 'UTF-8') }}</a>
                            </td>

                            <td>
                                {{ htmlspecialchars($product->use, ENT_QUOTES, 'UTF-8') }}
                            </td>

                            <td>
                                {{ htmlspecialchars($product->quantity, ENT_QUOTES, 'UTF-8') }}
                            </td>

                            <td>
                                <img src="{{ htmlspecialchars($product->image_url, ENT_QUOTES, 'UTF-8') }}" width="80" alt="Error Image" style="padding-top: 5px; padding-bottom: 5px;">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>
