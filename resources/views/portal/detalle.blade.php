<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Remisión #{{ $venta->folio ?? $venta->sale_id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Remisión: #{{ $venta->folio ?? $venta->sale_id }}</h1>
                <p class="text-gray-500">Fecha de Venta: {{ $venta->date }}</p>
                <p class="text-sm text-gray-600">Tipo: {{ $venta->sale_type }} | Términos: {{ $venta->term ?? 'Contado' }}</p>
            </div>
            <a href="{{ route('portal.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition text-sm">
                Volver al Listado
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full bg-white border border-gray-200 rounded shadow-sm">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-left border-b">
                        <th class="p-3 font-semibold">Producto</th>
                        <th class="p-3 font-semibold text-center">Cantidad</th>
                        <th class="p-3 font-semibold text-right">Precio Unitario</th>
                        <th class="p-3 font-semibold text-center">¿Lleva IVA?</th>
                        <th class="p-3 font-semibold text-right">Importe c/ IVA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php 
                        $acumuladoSubtotal = 0;
                        $acumuladoIva = 0;
                        $granTotal = 0; 
                    @endphp
                    @foreach($articulos as $articulo)
                        @php 
                            $cantidad = $articulo->quantity ?? $articulo->net_weight ?? 0;
                            $precio = $articulo->cost ?? $articulo->price ?? 0;
                            
                            $subtotalArticulo = $cantidad * $precio;
                            $ivaArticulo = ($articulo->has_tax == 1) ? ($subtotalArticulo * 0.16) : 0;
                            $importeTotalArticulo = $subtotalArticulo + $ivaArticulo;

                            $acumuladoSubtotal += $subtotalArticulo;
                            $acumuladoIva += $ivaArticulo;
                            $granTotal += $importeTotalArticulo;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-gray-800 font-medium">
                                {{ $articulo->public_product_name ?? 'Producto ID: #'.$articulo->product_id }}
                                @if($articulo->public_batch)
                                    <span class="block text-xs text-gray-400">Lote: {{ $articulo->public_batch }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-center text-gray-600">
                                {{ number_format($cantidad, 2) }}
                            </td>
                            <td class="p-3 text-right text-gray-600">
                                ${{ number_format($precio, 2) }}
                            </td>
                            <td class="p-3 text-center">
                                @if($articulo->has_tax == 1)
                                    <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold">Sí (16%)</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs">No</span>
                                @endif
                            </td>
                            <td class="p-3 text-right font-semibold text-gray-900">
                                ${{ number_format($importeTotalArticulo, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 border-t">
                        <td colspan="4" class="p-2 text-right text-gray-600 font-medium">Subtotal:</td>
                        <td class="p-2 text-right text-gray-900 font-semibold">${{ number_format($acumuladoSubtotal, 2) }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td colspan="4" class="p-2 text-right text-gray-600 font-medium">IVA acumulado:</td>
                        <td class="p-2 text-right text-gray-900 font-semibold">${{ number_format($acumuladoIva, 2) }}</td>
                    </tr>
                    <tr class="bg-gray-100 font-bold border-t-2">
                        <td colspan="4" class="p-3 text-right text-gray-700 text-lg">Total de la Venta:</td>
                        <td class="p-3 text-right text-blue-600 text-xl">${{ number_format($granTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</body>
</html>