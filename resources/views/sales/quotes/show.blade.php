@extends('layouts.app')

@push('css')
<style>
    .view-container { background-color: #f4f4f4; padding: 40px 0; }
    .quote-paper { 
        background: white; 
        width: 210mm; 
        margin: 0 auto; 
        padding: 20mm; 
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        font-family: Arial, Helvetica, sans-serif;
        color: #000;
        position: relative;
    }
    
    .tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .b1 td, .b1 th { border: 1px solid #000; }
    .c { text-align: center; }
    .r { text-align: right; }
    .p4 { padding: 4px; }
    .p6 { padding: 6px; }
    .th-green { background: #92D050 !important; font-weight: bold; color: #000; }
    
    .header-table { width: 100%; border-collapse: collapse; border: 1px solid #000; font-size: 9pt; margin-bottom: 20px; }
    .header-table td { border: 1px solid #000; }

    .folio-box { text-align: right; margin-top: 10px; font-size: 11pt; margin-bottom: 5px; }
    .folio-val { font-weight: bold; border-bottom: 1px solid #000; padding: 0 10px; min-width: 100px; display: inline-block; }

    .footer-custom { 
        background: #f2f2f2; 
        border-top: 2px solid #92D050; 
        padding: 10px; 
        font-size: 9pt; 
        margin-top: 40px;
    }

    @media print {
        .no-print { display: none !important; }
        .view-container { padding: 0; background: white; }
        .quote-paper { box-shadow: none; width: 100%; padding: 0; }
        body { background: white; }
    }
</style>
@endpush

@section('content')
<div class="view-container">
    
    <div class="max-w-4xl mx-auto mb-6 flex justify-between no-print px-4">
        <a href="{{ route('quotes') }}" class="bg-gray-600 text-white px-4 py-2 rounded font-bold hover:bg-gray-700 transition shadow-md">
            ← Volver al listado
        </a>
    </div>

    <div class="quote-paper">
        
        <table class="header-table">
            <tr>
                <td style="width:22%; text-align:center; padding: 10px;">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:50px;">
                </td>
                <td style="width:58%; vertical-align:top; padding:0;">
                    <div style="text-align:center; font-weight:bold; font-size:14pt; padding:10px; border-bottom:1px solid #000;">
                        COTIZACIÓN
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
                <td style="width:20%; vertical-align:top; font-size:7pt; padding:0;">
                    <div style="border-bottom:1px solid #000; padding:10px; text-align:center;">
                        <b>Código:</b><br> SSS-FOR-COM-03
                    </div>
                    <div style="padding:10px; text-align:center;">
                        Pág. 1 de 1
                    </div>
                </td>
            </tr>
        </table>

        <div class="folio-box">
            <b>Folio:</b> <span class="folio-val">{{ $quote->folio }}</span>
        </div>

        <table class="tbl b1" style="font-size:10pt;">
            <tr class="th-green">
                <td colspan="2" class="p4 c">DATOS GENERALES</td>
            </tr>
            <tr>
                <td class="p4" style="width:30%;"><b>Empresa:</b></td>
                <td class="p4">{{ $quote->company }}</td>
            </tr>
            <tr>
                <td class="p4"><b>Atención:</b></td>
                <td class="p4">{{ $quote->attention }}</td>
            </tr>
            <tr>
                <td class="p4"><b>Lugar y Fecha:</b></td>
                <td class="p4">Apaseo el Grande, Guanajuato, México a {{ \Carbon\Carbon::parse($quote->date)->translatedFormat('d \d\e F \d\e\l Y').'.' }}</td>
            </tr>
            <tr>
                <td class="p4"><b>Departamento:</b></td>
                <td class="p4">{{ $quote->department ?? 'Compras' }}</td>
            </tr>
        </table>

        <p style="font-size:10pt; margin-top:15px;">
            Estimado(a) <b>{{ $quote->attention }}</b>, tengo el gusto de responder a su solicitud. A continuación, encontrará cotización de producto solicitado:
        </p>

        <table class="tbl b1" style="font-size:9pt; margin-top:10px;">
            <thead>
                <tr class="th-green c">
                    <th class="p6" style="width:28%;">Producto</th>
                    <th class="p6" style="width:12%;">Req. (Cant.)</th>
                    <th class="p6" style="width:12%;">Unidad</th>
                    <th class="p6" style="width:18%;">Presentación</th>
                    <th class="p6" style="width:15%;">Precio Unit.</th>
                    <th class="p6" style="width:15%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $subtotalCalculado = 0; 
                @endphp
                @foreach($quote->details as $prod)
                <tr class="c">
                    <td class="p4" style="text-align:left;">{{ $prod->quote_product_name }}</td>
                    <td class="p4">{{ number_format($prod->quantity, 2) }}</td>
                    <td class="p4">{{ $prod->unit ?? 'N/A' }}</td>
                    <td class="p4">{{ $prod->presentation ?? 'N/A' }}</td>
                    <td class="p4">$ {{ number_format($prod->cost, 2) }}</td>
                    <td class="p4">$ {{ number_format($prod->quantity * $prod->cost, 2) }}</td>
                </tr>
                @php 
                    $subtotalCalculado += ($prod->quantity * $prod->cost); 
                @endphp
                @endforeach
            </tbody>
         <tfoot>
                @php
                    $subtotalCalculado = 0;
                    $ivaCalculado = 0;

                    foreach($quote->details as $prod) {
                        $importeFila = $prod->quantity * $prod->cost;
                        $subtotalCalculado += $importeFila;
                        $ivaCalculado += ($importeFila * ($prod->iva ?? 0.16));
                    }

                    $totalFinal = $subtotalCalculado + $ivaCalculado;
                @endphp

                <tr>
                    <td colspan="5" class="r p4" style="font-weight:bold;">SUBTOTAL (MXN):</td>
                    <td class="c p4" style="font-weight:bold;">$ {{ number_format($subtotalCalculado, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="r p4" style="font-weight:bold;">IVA:</td>
                    <td class="c p4" style="font-weight:bold;">$ {{ number_format($ivaCalculado, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="r p4" style="font-weight:bold; font-size: 11pt;">TOTAL GENERAL (MXN):</td>
                    <td class="c p4" style="font-weight:bold; background:#f2f2f2; font-size: 11pt;">$ {{ number_format($totalFinal, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <table class="tbl b1" style="font-size:10pt; margin-top:20px;">
            <tr class="th-green">
                <td colspan="2" class="p4 c">CONDICIONES COMERCIALES</td>
            </tr>
            <tr>
                <td class="p4" style="width:30%;"><b>Incoterm:</b></td>
                <td class="p4">{{ $quote->place_of_delivery ?? 'LAB Apaseo El Grande.' }}</td>
            </tr>
            <tr>
                <td class="p4"><b>Transporte:</b></td>
                <td class="p4">{{ $quote->transport_specification ?? 'Paquetería consolidada' }}</td>
            </tr>
            <tr>
                <td class="p4"><b>Tiempo de entrega:</b></td>
                <td class="p4">{{ $quote->deadline ?? '6 días hábiles una vez recibida la orden de compra y pago.' }}</td>
            </tr>
            <tr>
                <td class="p4"><b>Términos y condiciones:</b></td>
                <td class="p4">{{ $quote->terms ?? 'Contado 100%' }}</td>
            </tr>
            <tr>
                <td class="p4"><b>Notas:</b></td>
                <td class="p4">{{ $quote->notes ?? 'Los precios antes mencionados son netos. La cotización es válida por 15 días.' }}</td>
            </tr>
        </table>

        <div style="margin-top:30px; font-size:10pt;">
            <p>Atentamente,<br><br><br><b>{{ $quote->user->name ?? 'Manola Ramírez' }}</b></p>
        </div>

        <div class="footer-custom">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:50%; text-align:left; vertical-align:top;">
                        Emiliano Zapata No. 7, Col. Rancho Nuevo<br>
                        C.P. 38197, Apaseo el Grande, Gto, México
                    </td>
                    <td style="width:50%; text-align:right; vertical-align:top;">
                        +52 461 156 8547 / +52 461 616 9975<br>
                        <b>www.suministrossustentables.com</b>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection