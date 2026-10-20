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
        <!-- Header con Logo y Acciones -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center space-x-3">
                <img src="/images/logo.png" alt="Success Logo" class="h-10 w-auto object-contain">
                <div>
                    <h1 class="text-xs uppercase tracking-wider font-bold text-[#198754]">Sistema de Trazabilidad</h1>
                    <p class="text-sm font-semibold text-slate-700">Ficha Técnica de Identificación</p>
                </div>
            </div>
            <div class="no-print flex items-center space-x-2.5">
                <button type="button" id="btn-realizar-salida-header" class="btn-trigger-salida inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition-all transform active:scale-95 cursor-pointer">
                    <i class="ri-logout-box-r-line mr-1.5 text-sm"></i> Realizar Salida
                </button>
                <button onclick="window.print()" class="inline-flex items-center px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition cursor-pointer">
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

                <!-- Banner de Acción Rápida: Realizar Salida -->
                <div class="no-print mb-6 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-green-500/10 border border-emerald-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-600 to-green-500 text-white flex items-center justify-center font-bold shadow-md flex-shrink-0">
                            <i class="ri-box-3-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800">
                                Gestión de Salida de Almacén
                            </h3>
                            <p class="text-xs text-slate-600">
                                Disminuye el inventario de este lote asociándolo a una venta pendiente o a consumo interno.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-trigger-salida w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 active:scale-95 text-white text-xs font-bold rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center flex-shrink-0 cursor-pointer">
                        <i class="ri-logout-box-r-line mr-1.5 text-sm"></i> Realizar Salida
                    </button>
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

    <!-- Modal: Selección de Destino de Salida (Venta vs Producción) -->
    <div id="modal-salida-tipo" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeSalidaModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                <!-- Header del Modal -->
                <div class="bg-gradient-to-r from-emerald-600 to-green-700 px-6 py-5 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xl">
                                <i class="ri-logout-box-r-line"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold" id="modal-title">¿El tipo de salida es para Venta o Producción?</h3>
                                <p class="text-xs text-emerald-100">Selecciona el tipo de salida a gestionar</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeSalidaModal()" class="text-emerald-100 hover:text-white transition rounded-lg p-1.5 hover:bg-white/10 cursor-pointer">
                            <i class="ri-close-line text-2xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Resumen de Producto Escaneado -->
                <div class="px-6 pt-5 pb-2">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Producto Escaneado</span>
                            <h4 class="text-sm font-extrabold text-slate-800 line-clamp-1">
                                {{ $qr->product ? $qr->product->name : 'N/A' }}
                            </h4>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Lote</span>
                            <span class="text-xs font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                {{ $qr->batch ?: 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Opciones Venta / Producción -->
                <div class="p-6 space-y-4">
                    <!-- Opción 1: Salida para Venta -->
                    <a href="{{ route('inventory.index', ['tab' => 'sales', 'product_id' => $qr->product_id ?? ($qr->product->product_id ?? 0), 'sale_status' => 'pending']) }}"
                       class="group block p-4 rounded-xl border-2 border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/40 transition-all shadow-sm hover:shadow-md cursor-pointer">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 group-hover:bg-emerald-600 group-hover:text-white text-emerald-700 flex items-center justify-center text-2xl transition-colors flex-shrink-0">
                                <i class="ri-shopping-cart-2-fill"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-base font-extrabold text-slate-800 group-hover:text-emerald-800 transition-colors">
                                        Salida para Venta
                                    </h4>
                                    <span class="text-[11px] font-bold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Recomendado
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 group-hover:text-slate-700 leading-relaxed">
                                    Te llevará a la vista de ventas en inventario, filtrando automáticamente las ventas <strong>pendientes</strong> que incluyen este producto para continuar con la disminución de inventario habitual.
                                </p>
                            </div>
                            <div class="self-center text-slate-400 group-hover:text-emerald-600 transition-transform group-hover:translate-x-1">
                                <i class="ri-arrow-right-s-line text-2xl"></i>
                            </div>
                        </div>
                    </a>

                    <!-- Opción 2: Salida para Producción -->
                    <a href="{{ route('inventory.index', ['tab' => 'inventory', 'open_tx' => 'InternalOutput', 'product_id' => $qr->product_id ?? ($qr->product->product_id ?? 0), 'batch' => $qr->batch]) }}"
                       class="group block p-4 rounded-xl border-2 border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/40 transition-all shadow-sm hover:shadow-md cursor-pointer">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 rounded-xl bg-indigo-100 group-hover:bg-indigo-600 group-hover:text-white text-indigo-700 flex items-center justify-center text-2xl transition-colors flex-shrink-0">
                                <i class="ri-settings-4-fill"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-base font-extrabold text-slate-800 group-hover:text-indigo-800 transition-colors">
                                        Salida para Producción
                                    </h4>
                                    <span class="text-[11px] font-bold uppercase px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 border border-indigo-300">
                                        Interno
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 group-hover:text-slate-700 leading-relaxed">
                                    Registra una <strong>salida interna / traspaso</strong> a planta para procesos productivos o consumo de materia prima.
                                </p>
                            </div>
                            <div class="self-center text-slate-400 group-hover:text-indigo-600 transition-transform group-hover:translate-x-1">
                                <i class="ri-arrow-right-s-line text-2xl"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Footer del modal -->
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-end">
                    <button type="button" onclick="closeSalidaModal()" class="px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 transition shadow-sm cursor-pointer">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Rol no autorizado -->
    <div id="modal-unauthorized" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-unauth-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeUnauthorizedModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl flex-shrink-0">
                        <i class="ri-shield-user-line"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800" id="modal-unauth-title">Acceso Restringido</h3>
                        <p class="text-xs text-slate-500">Se requiere rol de Almacén</p>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                    Tu usuario actual (<strong>{{ $currentUser ? $currentUser->name : 'Usuario' }}</strong>) no tiene los permisos del área de <strong>Almacén</strong> o <strong>Administrador</strong> para autorizar salidas de inventario.
                </p>
                <div class="flex flex-col sm:flex-row gap-2.5 justify-end">
                    <button type="button" onclick="closeUnauthorizedModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                        Cerrar
                    </button>
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-[#198754] hover:bg-[#157347] text-white text-xs font-bold rounded-xl transition text-center shadow-sm cursor-pointer">
                        Iniciar sesión con otra cuenta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const isAuthenticated = @json(auth()->check());
        const isWarehouseUser = @json($isWarehouseUser ?? false);
        const salidaUrl = "{{ route('product-inf.salida', $qr->uuid) }}";

        function openSalidaModal() {
            const modal = document.getElementById('modal-salida-tipo');
            if (modal) modal.classList.remove('hidden');
        }

        function closeSalidaModal() {
            const modal = document.getElementById('modal-salida-tipo');
            if (modal) modal.classList.add('hidden');
        }

        function openUnauthorizedModal() {
            const modal = document.getElementById('modal-unauthorized');
            if (modal) modal.classList.remove('hidden');
        }

        function closeUnauthorizedModal() {
            const modal = document.getElementById('modal-unauthorized');
            if (modal) modal.classList.add('hidden');
        }

        document.querySelectorAll('.btn-trigger-salida').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!isAuthenticated) {
                    window.location.href = salidaUrl;
                    return;
                }
                if (!isWarehouseUser) {
                    openUnauthorizedModal();
                    return;
                }
                openSalidaModal();
            });
        });

        // Detectar si venimos de redirección tras autenticación exitosa o parámetros action / error
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');
            const error = urlParams.get('error');

            if (action === 'salida') {
                if (!isAuthenticated) {
                    window.location.href = salidaUrl;
                } else if (isWarehouseUser) {
                    openSalidaModal();
                } else {
                    openUnauthorizedModal();
                }
            } else if (error === 'unauthorized_role') {
                openUnauthorizedModal();
            }
        });
    </script>
</body>
</html>
