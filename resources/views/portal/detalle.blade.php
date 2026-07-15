<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Remisión #{{ $venta->folio ?? $venta->sale_id }} - Suministros Sustentables</title>
    <link rel="icon" href="{{ asset('images/successIconG.ico') }}" type="image/x-icon" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#f4fbf1', 100: '#e5f6df', 200: '#caeec0', 300: '#a3df95',
                            400: '#77c35a', 500: '#5ab03c', 600: '#439229', 700: '#367524',
                            800: '#2d5d21', 900: '#264d1f', 950: '#122a0e',
                        },
                        green: {
                            50: '#f4fbf1', 100: '#e5f6df', 200: '#caeec0', 300: '#a3df95',
                            400: '#77c35a', 500: '#5ab03c', 600: '#439229', 700: '#367524',
                            800: '#2d5d21', 900: '#264d1f', 950: '#122a0e',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }
        
        .bg-header-agro {
            background-image: linear-gradient(to right, rgba(6, 78, 59, 0.95), rgba(2, 44, 34, 0.85)), url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center 30%;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }

        .table-row-hover {
            transition: all 0.2s ease;
        }
        .table-row-hover:hover {
            background-color: #f0fdf4; 
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="antialiased text-slate-800 flex flex-col min-h-screen">
    
    <header class="bg-header-agro text-white py-8 px-6 lg:px-12 shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-400 rounded-full mix-blend-overlay filter blur-[80px] opacity-40"></div>
        
        <div class="w-full mx-auto relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="bg-white/10 p-3 rounded-2xl backdrop-blur-md border border-white/20 shadow-xl">
                    <img src="{{ asset('images/logo-alt.png') }}" alt="Logo" class="h-16 w-auto drop-shadow-md">
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight mb-1">Detalle de Factura/Remisión</h1>
                    <p class="text-emerald-100/80 font-medium">Empresa: <span class="text-white">{{ Auth::guard('client')->user()->empresa }}</span></p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-sm text-emerald-100/70">Visualizando Folio</p>
                    <p class="font-semibold text-xl text-emerald-300">#{{ $venta->folio ?? $venta->sale_id }}</p>
                </div>
                
                <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-2 px-5 py-2.5 bg-slate-800/50 hover:bg-slate-800 text-white border border-slate-700/50 rounded-xl transition-all duration-200 font-medium group backdrop-blur-sm">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Volver al Listado
                </a>
            </div>
        </div>
    </header>

    <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-10 -mt-8 relative z-20">
        
        <div class="glass-panel rounded-2xl p-6 md:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 border-b border-slate-200 pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                            <i class="fas fa-receipt"></i>
                        </div>
                        Remisión: <span class="text-emerald-700">#{{ $venta->folio ?? $venta->sale_id }}</span>
                    </h2>
                    <p class="text-slate-500 mt-2 font-medium flex gap-4">
                        <span><i class="far fa-calendar-alt text-emerald-500 mr-1"></i> Fecha: {{ $venta->date }}</span>
                        <span><i class="fas fa-tag text-emerald-500 mr-1"></i> Tipo: {{ $venta->sale_type }}</span>
                        <span><i class="fas fa-handshake text-emerald-500 mr-1"></i> Términos: {{ $venta->term ?? 'Contado' }}</span>
                    </p>
                </div>
            </div>
            
            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm mb-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 text-sm uppercase tracking-wider">
                            <th class="p-4 font-semibold border-b border-slate-200">Producto</th>
                            <th class="p-4 font-semibold border-b border-slate-200 text-center">Cantidad</th>
                            <th class="p-4 font-semibold border-b border-slate-200 text-right">Precio Unitario</th>
                            <th class="p-4 font-semibold border-b border-slate-200 text-center">¿Lleva IVA?</th>
                            <th class="p-4 font-semibold border-b border-slate-200 text-right">Importe c/ IVA</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
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
                            <tr class="table-row-hover bg-white">
                                <td class="p-4 text-slate-800 font-medium">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div>
                                            {{ $articulo->public_product_name ?? 'Producto ID: #'.$articulo->product_id }}
                                            @if($articulo->public_batch)
                                                <span class="block text-xs text-slate-400 mt-0.5"><i class="fas fa-layer-group text-slate-300 mr-1"></i> Lote: {{ $articulo->public_batch }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-center font-semibold text-slate-600">
                                    {{ number_format($cantidad, 2) }}
                                </td>
                                <td class="p-4 text-right font-medium text-slate-600">
                                    ${{ number_format($precio, 2) }}
                                </td>
                                <td class="p-4 text-center">
                                    @if($articulo->has_tax == 1)
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 border border-emerald-200 text-emerald-700 px-2.5 py-1 rounded-md text-xs font-semibold">
                                            <i class="fas fa-check-circle"></i> Sí (16%)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-500 px-2.5 py-1 rounded-md text-xs font-medium">
                                            <i class="fas fa-times-circle"></i> No
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right font-bold text-slate-800">
                                    ${{ number_format($importeTotalArticulo, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t border-slate-200">
                            <td colspan="4" class="p-3 pr-6 text-right text-slate-500 font-medium uppercase text-xs tracking-wide">Subtotal:</td>
                            <td class="p-3 text-right text-slate-700 font-semibold">${{ number_format($acumuladoSubtotal, 2) }}</td>
                        </tr>
                        <tr class="bg-slate-50">
                            <td colspan="4" class="p-3 pr-6 text-right text-slate-500 font-medium uppercase text-xs tracking-wide">IVA acumulado (16%):</td>
                            <td class="p-3 text-right text-slate-700 font-semibold">${{ number_format($acumuladoIva, 2) }}</td>
                        </tr>
                        <tr class="bg-emerald-50 border-t-2 border-emerald-200">
                            <td colspan="4" class="p-4 pr-6 text-right text-emerald-800 font-bold uppercase tracking-wide">Total de la Venta:</td>
                            <td class="p-4 text-right text-emerald-700 font-black text-xl">${{ number_format($granTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>
</body>
</html>