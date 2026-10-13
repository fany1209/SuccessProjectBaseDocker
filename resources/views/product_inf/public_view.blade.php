<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $qr->product ? $qr->product->name : 'Información de Producto' }} | Success ERP</title>
    <link rel="icon" href="{{ asset('images/successIconG.ico') }}" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-gray-50 to-slate-200 min-h-screen text-slate-800 font-sans antialiased py-8 px-4 sm:px-6 lg:px-8">

    <div class="max-w-2xl mx-auto">
        <!-- Header con Logo -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <img src="/images/logo.png" alt="Success Logo" class="h-10 w-auto object-contain">
                <div>
                    <h1 class="text-xs uppercase tracking-wider font-bold text-[#198754]">Sistema de Trazabilidad</h1>
                    <p class="text-sm font-semibold text-slate-700">Ficha Técnica de Identificación</p>
                </div>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition">
                    <i class="ri-printer-line mr-1 text-slate-500"></i> Imprimir
                </button>
            </div>
        </div>

        <!-- Tarjeta Principal de Información -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden print-card">
            
            <!-- Banner de Estado en Tiempo Real -->
            <div class="px-6 py-4 {{ $isActive ? 'bg-gradient-to-r from-emerald-600 to-green-600 text-white' : 'bg-gradient-to-r from-slate-600 to-slate-700 text-white' }} flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $isActive ? 'bg-green-300' : 'bg-slate-400' }} opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 {{ $isActive ? 'bg-green-100' : 'bg-slate-300' }}"></span>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-widest">
                        {{ $isActive ? 'Producto Activo en Almacén' : 'Lote Despachado / Registro Histórico' }}
                    </span>
                </div>
                <span class="text-xs font-mono bg-white/20 px-2.5 py-1 rounded-md backdrop-blur-sm">
                    En Tiempo Real
                </span>
            </div>

            <div class="p-6 sm:p-8">
                <!-- Nombre del Producto y Lote -->
                <div class="border-b border-slate-100 pb-6 mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 bg-green-50 text-green-700 rounded-md border border-green-200">
                            {{ $qr->concept ? $qr->concept->name : 'Almacén' }}
                        </span>
                        @if(!empty($qr->bag_number))
                        <span class="text-xs font-bold px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md border border-blue-200 font-mono">
                            Barcina #{{ $qr->bag_number }}
                        </span>
                        @endif
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ $qr->product ? $qr->product->name : 'Producto sin catálogo' }}
                    </h2>
                    @if($qr->product && !empty($qr->product->sku))
                    <p class="text-xs text-slate-500 font-mono mt-1">SKU: {{ $qr->product->sku }}</p>
                    @endif
                </div>

                <!-- Grid de Métricas Principales -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <span class="text-xs text-slate-500 uppercase tracking-wider block font-medium">Lote / Batch</span>
                        <span class="text-lg font-bold text-slate-800 font-mono break-all">{{ $qr->batch ?: 'N/A' }}</span>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <span class="text-xs text-slate-500 uppercase tracking-wider block font-medium">Ubicación / Rack</span>
                        <span class="text-lg font-bold text-emerald-700">{{ $currentLocation }}</span>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <span class="text-xs text-slate-500 uppercase tracking-wider block font-medium">Almacén</span>
                        <span class="text-base font-semibold text-slate-800 truncate block">{{ $warehouseName }}</span>
                    </div>

                    <div class="bg-emerald-50/70 rounded-xl p-4 border border-emerald-100">
                        <span class="text-xs text-emerald-700 uppercase tracking-wider block font-medium">Peso Neto Total</span>
                        <span class="text-lg font-bold text-emerald-800 font-mono">
                            {{ number_format($currentNetWeight, 2) }} <span class="text-xs font-sans">{{ $qr->unit ?: 'kg' }}</span>
                        </span>
                    </div>
                </div>

                <!-- Detalles de Cantidad y Unidad -->
                <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100 mb-6 flex flex-wrap items-center justify-between gap-4 text-sm">
                    <div>
                        <span class="text-slate-500 block text-xs">Cantidad registrada:</span>
                        <span class="font-bold text-slate-800">{{ number_format($currentQuantity, 2) }} uds</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs">Peso unitario:</span>
                        <span class="font-bold text-slate-800">{{ number_format($currentWpu, 2) }} {{ $qr->unit ?: 'kg' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-xs">Fecha de Emisión:</span>
                        <span class="font-bold text-slate-800">{{ $qr->created_at ? $qr->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                </div>

                <!-- Descripción a Detalle del Producto -->
                <div class="mb-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2 flex items-center">
                        <i class="ri-file-text-line mr-1.5 text-[#198754]"></i> Descripción a Detalle del Producto
                    </h3>
                    <div class="bg-gradient-to-r from-amber-50/60 to-orange-50/30 border-l-4 border-amber-400 p-4 rounded-r-xl">
                        <p class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                            {{ $qr->description ?: 'Sin observaciones adicionales registradas para este producto.' }}
                        </p>
                    </div>
                </div>

                <!-- Código QR Vectorial y URL Permanente -->
                <div class="border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white p-2 rounded-xl shadow-md border border-slate-200 flex-shrink-0 w-28 h-28 flex items-center justify-center">
                            {!! $qrSvg !!}
                        </div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#198754] block mb-1">
                                Código QR Permanente
                            </span>
                            <p class="text-xs text-slate-600 mb-2">
                                Este código nunca expira y consulta información en tiempo real desde la base de datos central.
                            </p>
                            <span class="text-[11px] font-mono text-slate-400 select-all break-all block">
                                UUID: {{ $qr->uuid }}
                            </span>
                        </div>
                    </div>
                    <div class="no-print flex flex-col gap-2 w-full sm:w-auto">
                        <a href="{{ route('product-inf.download-svg', $qr->uuid) }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#198754] text-white text-xs font-semibold rounded-lg hover:bg-[#157347] transition shadow-sm">
                            <i class="ri-download-2-line mr-1.5"></i> Descargar SVG
                        </a>
                    </div>
                </div>

                <!-- Footer de Trazabilidad -->
                <div class="mt-8 pt-4 border-t border-slate-100 text-center text-xs text-slate-400">
                    <p>Registrado por: <strong class="text-slate-600">{{ $qr->creator ? $qr->creator->name : 'Personal de Almacén' }}</strong> &bull; Success Suministros Sustentables &copy; {{ date('Y') }}</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
