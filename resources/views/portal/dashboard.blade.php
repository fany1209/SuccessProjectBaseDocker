<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Cliente - Suministros Sustentables</title>
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
            /* Aplicamos una capa blanca con 92% de opacidad para que las imágenes del fondo sean sutiles */
            background-image: linear-gradient(rgba(248, 250, 252, 0.92), rgba(248, 250, 252, 0.92)), url("{{ asset('images/formats/agro.png') }}");
            /* Controla aquí el tamaño del patrón (ej. 100px de ancho) */
            background-size: 100px auto;
            background-repeat: repeat;
            background-attachment: fixed;
        }
        
        .bg-header-agro {
            background-image: linear-gradient(to right, rgba(6, 78, 59, 0.95), rgba(2, 44, 34, 0.85)), url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center 30%;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }

        .table-row-hover {
            transition: all 0.2s ease;
        }
        .table-row-hover:hover {
            background-color: rgba(240, 253, 244, 0.95); 
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
                    <h1 class="text-3xl font-bold tracking-tight mb-1">Panel de Cliente</h1>
                    <p class="text-emerald-100/80 font-medium">Empresa: <span class="text-white">{{ Auth::guard('client')->user()->empresa }}</span></p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-sm text-emerald-100/70">Bienvenido(a)</p>
                    <p class="font-semibold text-lg">{{ Auth::guard('client')->user()->nombre_contacto }}</p>
                </div>
                
                <form action="{{ route('portal.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-red-500/20 hover:bg-red-500/40 text-red-100 border border-red-500/30 rounded-xl transition-all duration-200 font-medium group">
                        <i class="fas fa-sign-out-alt group-hover:-translate-x-1 transition-transform"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-10 -mt-8 relative z-20">
        
        <div class="glass-panel rounded-2xl p-6 md:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    Historial de Facturas y Remisiones
                </h2>
                <div class="text-sm text-slate-500 font-medium">
                    <i class="fas fa-info-circle text-emerald-500 mr-1"></i> Documentos disponibles para descarga
                </div>
            </div>
            
            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/75 text-slate-600 text-sm uppercase tracking-wider">
                            <th class="p-4 font-semibold border-b border-slate-200">Folio / ID</th>
                            <th class="p-4 font-semibold border-b border-slate-200">Fecha</th>
                            <th class="p-4 font-semibold border-b border-slate-200 text-center">Acciones y Documentos</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                        @forelse($ventas as $venta)
                            @php
                                $documentosVenta = DB::table('portal_documents')
                                                     ->where('sale_id', $venta->sale_id)
                                                     ->get();
                                                     
                                $pdfManual = $documentosVenta->where('file_type', 'pdf')->first();
                                $xmlManual = $documentosVenta->where('file_type', 'xml')->first();
                                $coaManual = $documentosVenta->where('file_type', 'coa')->first();
                            @endphp

                            <tr class="table-row-hover bg-white/90">
                                <td class="p-4 font-bold text-emerald-700">
                                    #{{ $venta->folio ?? $venta->sale_id }}
                                </td>
                                <td class="p-4 font-medium text-slate-500 whitespace-nowrap">
                                    <i class="far fa-calendar-alt mr-2 text-slate-400"></i>{{ $venta->date }}
                                </td> 
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                        
                                        <a href="{{ route('portal.ver-detalle', $venta->sale_id) }}" class="flex items-center gap-1.5 bg-slate-800 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-slate-700 transition shadow-sm">
                                            <i class="fas fa-eye"></i> Detalle
                                        </a>
                                        
                                        <a href="{{ route('portal.descargar-pdf', $venta->sale_id) }}" class="flex items-center gap-1.5 bg-white border border-emerald-500 text-emerald-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-emerald-50 transition shadow-sm">
                                            <i class="fas fa-download"></i> Recibo PDF
                                        </a>

                                        @if($pdfManual)
                                            <a href="{{ asset('storage/' . $pdfManual->file_path) }}" download="{{ $pdfManual->file_name }}" class="flex items-center gap-1.5 bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-rose-700 transition shadow-sm" title="Descargar Factura PDF Externa">
                                                <i class="fas fa-file-pdf"></i> Factura PDF
                                            </a>
                                        @endif

                                        @if($xmlManual)
                                            <a href="{{ asset('storage/' . $xmlManual->file_path) }}" download="{{ $xmlManual->file_name }}" class="flex items-center gap-1.5 bg-amber-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-amber-600 transition shadow-sm" title="Descargar Factura XML">
                                                <i class="fas fa-file-code"></i> XML
                                            </a>
                                        @endif

                                        @if($coaManual)
                                            <a href="{{ asset('storage/' . $coaManual->file_path) }}" download="{{ $coaManual->file_name }}" class="flex items-center gap-1.5 bg-teal-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-teal-700 transition shadow-sm" title="Descargar Certificado COA">
                                                <i class="fas fa-certificate"></i> COA
                                            </a>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-4xl mb-3 text-slate-300"></i>
                                        <p class="font-medium">No hay facturas ni remisiones disponibles en este momento.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>
        
    </main>
</body>
</html>