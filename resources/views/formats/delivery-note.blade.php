<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF Document</title>

    <style>
        header {
            font-family: Arial, Helvetica, sans-serif;
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

<header>
    <table class="w-full">
        <tbody>
            <tr>
                <td style="width: 15%;">
                    <img src="images/logo.png" width="170">
                </td>

                <td class="w-full text-center">
                    <p class="text-xl font-bold uppercase">Nota de remision division
                        @if ($sale->customer != null)
                            {{ $sale->sector?->name }}
                        @else
                            {{ $sale->prospect?->sector?->name }}
                        @endif
                    </p>

                    <p class="text-xs">SUCCESS Suministros Sustentables</p>
                    <p class="text-xs">Emiliano Zapata #7 Col. Rancho Nuevo, Apaseo el Grande, Gto.</p>
                    <p class="text-xs">Tel: 4616169975 / 4611568547</p>
                    <p style="color:rgba(rgb(0, 81, 255))" class="text-xs">www.suministrossustentables.com</p>
                </td>

                <td style="width: 70%;">
                    <div class="text-center">
                        <p class="text-center text-sm" style="display: inline-block;">Folio:</p>
                        <p class="text-center text-sm" style="display: inline-block; color:red;">{{ $sale->folio }}
                        </p>
                    </div>

                    <div class="text-center">
                        <img src="images/qrSuccess.png" width="72" >
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</header>

<body>
    <br>
    <div class="w-full">
        <table class="w-full text-xs">
            <tbody>
                <tr>
                    <td>
                        <p class="text-in-line font-bold">Fecha:</p>
                        <p class="text-in-line">{{ $sale->date }}</p>
                    </td>

                    <td style="width: 10%;">
                        <p class="text-in-line font-bold">1ra vez:</p>
                        <input type="radio" {{ $sale->first_time ? 'checked' : '' }} />
                    </td>

                    <td style="width: 10%;">
                        <p class="text-in-line font-bold">Cliente:</p>
                        <input type="radio" {{ $sale->is_customer ? 'checked' : '' }} />
                    </td>

                    <td>
                        <p class="text-in-line font-bold">OC:</p>
                        <p class="text-in-line">{{ $sale->purchase_order }}</p>
                    </td>

                    <td style="width: 10%;">
                        <p class="text-in-line font-bold">Factura:</p>
                        <input type="radio" {{ $sale->invoice == null ? '' : 'checked' }} />
                    </td>

                    <td>
                        <p class="text-in-line font-bold">No. Factura:</p>
                        <p class="text-in-line">{{ $sale->invoice }}</p>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <p class="text-in-line font-bold">Nombre:</p>
                        @if ($sale->customer != null)
                            <p class="text-in-line">{{ $sale->customer?->name }}</p>
                        @else
                            <p class="text-in-line">{{ $sale->prospect?->name }}</p>
                        @endif
                    </td>

                    <td colspan="2">
                        <p class="text-in-line font-bold">RFC:</p>
                        @if ($sale->customer != null)
                            <p class="text-in-line">{{ $sale->customer?->rfc }}</p>
                        @else
                            <p class="text-in-line">{{ $sale->prospect?->rfc }}</p>
                        @endif
                    </td>

                    <td>
                        <p class="text-in-line font-bold">Código de Cliente:</p>
                        @if ($sale->customer != null)
                            <p class="text-in-line">{{ $sale->customer?->customer_code }}</p>
                        @else
                            <p class="text-in-line">{{ $sale->prospect?->customer_code }}</p>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <p class="text-in-line font-bold">Domicilio:</p>
                        @if ($sale->customer != null)
                            <p class="text-in-line">{{ $sale->customer?->address }}</p>
                        @else
                            <p class="text-in-line">{{ $sale->prospect?->address }}</p>
                        @endif
                    </td>

                    <td>
                        <p class="text-in-line font-bold">CP:</p>
                        @if ($sale->customer != null)
                            <p class="text-in-line">{{ $sale->customer?->postal_code }}</p>
                        @else
                            <p class="text-in-line">{{ $sale->prospect?->postal_code }}</p>
                        @endif
                    </td>

                    <td colspan="2">
                        <p class="text-in-line font-bold">Colonia:</p>
                        @if ($sale->customer != null)
                            <p class="text-in-line">{{ $sale->customer?->district }}</p>
                        @else
                            <p class="text-in-line">{{ $sale->prospect?->district }}</p>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <p class="text-in-line font-bold">Ciudad:</p>
                        @if ($sale->customer != null)
                            <p class="text-in-line">{{ $sale->customer?->city }}</p>
                        @else
                            <p class="text-in-line">{{ $sale->prospect?->city }}</p>
                        @endif
                    </td>

                    <td colspan="2">
                        <p class="text-in-line font-bold">País:</p>
                        @if ($sale->customer != null)
                            <p class="text-in-line">{{ $sale->customer?->country }}</p>
                        @else
                            <p class="text-in-line">{{ $sale->prospect?->country }}</p>
                        @endif
                    </td>

                    <td colspan="2">
                        <p class="text-in-line font-bold">Teléfono:</p>
                        @if ($sale->customer != null)
                            <p class="text-in-line">{{ $sale->customer?->phone }}</p>
                        @else
                            <p class="text-in-line">{{ $sale->prospect?->phone }}</p>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td colspan="6">
                        <p class="text-in-line font-bold">Correo:</p>
                        @if ($sale->customer != null)
                            <p class="text-in-line">{{ $sale->customer?->email }}</p>
                        @else
                            <p class="text-in-line">{{ $sale->prospect?->email }}</p>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="w-full text-xs">
            <tbody>
                <tr>
                    <td colspan="6" class="text-center font-bold uppercase">
                        Tipo de Venta
                    </td>
                </tr>

                <tr>
                    <td style="width: 13%;">
                        <div style="align-items: center;">
                            <p class="text-in-line font-bold">Contado:</p>
                            <input type="radio" {{ strtolower($sale->sale_type ?? '') == 'cash' ? 'checked' : '' }} />
                        </div>
                    </td>

                    <td style="width: 13%;">
                        <div style="align-items: center;">
                            <p class="text-in-line font-bold">Crédito:</p>
                            <input type="radio" {{ strtolower($sale->sale_type ?? '') == 'credit' ? 'checked' : '' }} />
                        </div>
                    </td>

                    <td>
                        <p class="text-in-line font-bold">Especifique:</p>
                        <p class="text-in-line">{{ $sale->term }}</p>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

    @php
        $bgImagePath = '';
        if ($type == 2) {
            $bgImagePath = public_path('images/formats/agro.png');
        } else {
            $bgImagePath = public_path('images/formats/pecuario.png');
        }
    @endphp

    <!-- AQUI ESTÁ EL CAMBIO: min-height en lugar de height fijo, permitiendo que crezca con los productos -->
    <div style="display: block; position: relative; width: 100%; min-height: 280px; height: auto;">
        
        @if($bgImagePath != '' && file_exists($bgImagePath))
            <div style="position: absolute; top: 10px; left: 0; right: 0; text-align: center; z-index: -1;">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($bgImagePath)) }}" 
                     style="width: 260px; height: 260px; opacity: 0.18;" 
                     alt="Fondo Marca de Agua">
            </div>
        @endif

        <table class="w-full table-bordered" style="background-color: transparent; position: relative; z-index: 1;">
            <thead>
                <tr>
                    <th style="width: 11%;">
                        <p class="font-bold">CANTIDAD</p>
                    </th>

                    <th style="width: 6%;">
                        <p class="font-bold">UM</p>
                    </th>

                    <th style="width: 11%;">
                        <p class="font-bold">SKU</p>
                    </th>

                    <th>
                        <p class="font-bold">DESCRIPCION</p>
                    </th>

                    <th style="width: 11%;">
                        <p class="font-bold">LOTE</p>
                    </th>

                    <th style="width: 8%;">
                        <p class="font-bold">COSTO</p>
                    </th>

                    <th style="width: 10%;">
                        <p class="font-bold">IMPORTE</p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $count = 0;
                    $hasMSVC = false;
                    $subtotalSum = 0;
                    $taxSum = 0;
                @endphp

                @if(isset($sale->products))
                    @foreach ($sale->products as $item)
                        @php
                            $filaCosto = $item->pivot->cost ?? 0;
                            $filaCant = $item->pivot->quantity ?? 0;
                            $filaImporte = $filaCosto * $filaCant;

                            if ($filaCosto == 0 || $filaCosto == null) {
                                $hasMSVC = true;
                            }

                            if (isset($item->pivot->invoice_val) && !$item->pivot->invoice_val && $filaCosto > 0) {
                                $subtotalSum += $filaImporte;

                                if (isset($item->pivot->has_tax) && $item->pivot->has_tax == 1) {
                                    $taxSum += ($filaImporte * 0.16);
                                }
                            }
                        @endphp
                        <tr style="background-color: transparent;">
                            <td style="background-color: transparent;">
                                {{ $item->pivot->quantity ?? '' }}
                            </td>

                            <td style="background-color: transparent;">
                                {{ $item->unit ?? '' }}
                            </td>

                            <td style="background-color: transparent;">
                                {{ $item->sku ?? '' }}
                            </td>

                            <td style="background-color: transparent;">
                                {{ $item->pivot->public_product_name ?? '' }}
                            </td>

                            <td style="background-color: transparent;">
                                {{ $item->pivot->public_batch ?? '' }}
                            </td>

                            <td style="background-color: transparent;">
                                @if (isset($item->pivot->invoice_val) && $item->pivot->invoice_val)
                                    Valor Factura
                                @elseif($filaCosto == 0)
                                    MSVC
                                @else
                                    ${{ number_format($filaCosto, 2, '.', ',') }}
                                @endif
                            </td>

                            <td style="background-color: transparent;">
                                @if (isset($item->pivot->invoice_val) && $item->pivot->invoice_val)
                                    Valor Factura
                                @elseif($filaCosto == 0)
                                    MSVC
                                @else
                                    ${{ number_format($filaImporte, 2, '.', ',') }}
                                @endif
                            </td>
                        </tr>
                        @php
                            $count++;
                        @endphp
                    @endforeach
                @endif

                @if ($count < 7)
                    @for ($i = $count; $i < 7; $i++)
                        <tr style="background-color: transparent;">
                            <td style="background-color: transparent;"></td>
                            <td style="background-color: transparent;"></td>
                            <td style="background-color: transparent;"></td>
                            <td style="background-color: transparent;"></td>
                            <td style="background-color: transparent;"></td>
                            <td style="background-color: transparent;"></td>
                            <td style="background-color: transparent;"></td>
                        </tr>
                    @endfor
                @endif

                <tr style="background-color: transparent;">
                    <td colspan="6" style="background-color: transparent;">
                        <p class="font-bold text-xs text-right">SUBTOTAL</p>
                    </td>
                    <td style="background-color: transparent;">
                        @if ($subtotalSum == 0)
                            <p class="font-bold text-center">----------</p>
                        @else
                            <p class="font-bold">${{ number_format($subtotalSum, 2, '.', ',') }}</p>
                        @endif
                    </td>
                </tr>

                <tr style="background-color: transparent;">
                    <td colspan="6" style="background-color: transparent;">
                        <p class="font-bold text-xs text-right">IVA (16%)</p>
                    </td>
                    <td style="background-color: transparent;">
                        @if ($taxSum == 0)
                            <p class="font-bold text-center">----------</p>
                        @else
                            <p class="font-bold">${{ number_format($taxSum, 2, '.', ',') }}</p>
                        @endif
                    </td>
                </tr>

                <tr style="background-color: transparent;">
                    <td colspan="6" style="background-color: transparent;">
                        <p class="font-bold text-xs text-right">TOTAL</p>
                    </td>

                    <td style="background-color: transparent;">
                        @if (($subtotalSum + $taxSum) == 0)
                            <p class="font-bold text-center">----------</p>
                        @else
                            <p class="font-bold">${{ number_format($subtotalSum + $taxSum, 2, '.', ',') }}</p>
                        @endif
                    </td>
                </tr>
                
                <!-- AQUI ESTÁ EL CAMBIO: El texto MSVC ahora está DENTRO de la tabla como una fila final -->
                @if($hasMSVC)
                <tr style="background-color: transparent; border: none;">
                    <td colspan="7" style="border: none; text-align: left; padding-top: 10px;">
                        <p class="text-xs font-bold">* MSVC: MUESTRA SIN VALOR COMERCIAL</p>
                    </td>
                </tr>
                @endif

            </tbody>
        </table>
    </div>

    <br>
    <br>

    <div class="w-full">
        <table class="w-full">
            <tbody>
                <tr>
                    <td class='text-left'>
                        <p class="text-in-line font-bold text-xs">Nombre del asesor:</p>
                        <p class="text-in-line text-xs">{{ $sale->user?->name ?? '' }}</p>
                    </td>

                    <td rowspan="2" style="width: 40%; vertical-align: bottom;">
                        <p class="text-center">_____________________________</p>
                        <p class="font-bold text-xs text-center">Nombre y firma de recepción y conformidad</p>
                    </td>
                </tr>

                <tr>
                    <td>
                        <p class="text-in-line font-bold text-xs">Número de identificación: </p>
                        <p class="text-in-line text-xs">{{ $sale->user?->id ?? '' }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>