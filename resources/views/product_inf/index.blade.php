@extends('layouts.app')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

    <!-- Encabezado del Módulo -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-gray-200 gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-green-700 mb-1">
                <i class="ri-qr-code-line text-base"></i>
                <span>Módulo de Control y Trazabilidad</span>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Product Inf</h1>
            <p class="text-sm text-gray-600 mt-1">Generación y administración de Códigos QR permanentes con trazabilidad en tiempo real.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('main-menu') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition duration-150">
                <i class="ri-arrow-left-line mr-1.5"></i> Menú Principal
            </a>
            <button type="button" id="btn-open-maps" class="open-modal inline-flex items-center px-5 py-2.5 bg-[#198754] hover:bg-[#157347] text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition duration-200" data-target="maps">
                <i class="ri-add-circle-line mr-2 text-base"></i> Crear Nuevo Código QR
            </button>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('product-inf.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative w-full sm:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="ri-search-line"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por producto, lote, barcina, rack o UUID..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">
                    <i class="ri-filter-3-line mr-1"></i> Filtrar
                </button>
                @if(!empty($search))
                <a href="{{ route('product-inf.index') }}" class="w-full sm:w-auto px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition text-center">
                    Limpiar
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de Códigos QR Generados -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Historial de Códigos QR Generados</h2>
            <span class="text-xs text-gray-500 font-mono">{{ $qrs->total() }} registro(s)</span>
        </div>

        @if($qrs->isEmpty())
        <div class="py-16 px-4 text-center">
            <div class="w-16 h-16 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-qr-code-line text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">No se encontraron códigos QR</h3>
            <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">Aún no se han generado códigos QR de productos o no coinciden con la búsqueda actual.</p>
            <button type="button" class="open-modal inline-flex items-center px-4 py-2 bg-[#198754] text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-[#157347] transition" data-target="maps">
                <i class="ri-add-line mr-1.5"></i> Generar Primer Código QR
            </button>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                <thead class="bg-gray-50 text-xs text-gray-600 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-3.5">QR</th>
                        <th class="px-6 py-3.5">UUID</th>
                        <th class="px-6 py-3.5">Producto & Lote</th>
                        <th class="px-6 py-3.5">Ubicación (Rack)</th>
                        <th class="px-6 py-3.5">Peso Neto</th>
                        <th class="px-6 py-3.5">Descripción Detallada</th>
                        <th class="px-6 py-3.5">Fecha & Creador</th>
                        <th class="px-6 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($qrs as $item)
                    @php
                        $scanUrl = url('/rack/' . $item->uuid);
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-12 h-12 bg-white border border-gray-200 rounded-lg p-1 shadow-xs cursor-pointer hover:scale-105 transition btn-preview-qr" data-uuid="{{ $item->uuid }}" data-url="{{ $scanUrl }}" data-product="{{ $item->product ? $item->product->name : 'N/A' }}" data-batch="{{ $item->batch ?: 'N/A' }}" data-bag="{{ $item->bag_number ?: 'N/A' }}" data-location="{{ $item->location ? $item->location->name : 'N/A' }}" data-netweight="{{ number_format($item->net_weight, 2) }} {{ $item->unit ?: 'kg' }}" data-desc="{{ $item->description }}">
                                {!! \F9WebLtd\QrCode\Facades\QrCode::size(40)->generate($scanUrl) !!}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-1.5">
                                <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded" title="{{ $item->uuid }}">
                                    {{ substr($item->uuid, 0, 8) }}...
                                </span>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $scanUrl }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'URL copiada', showConfirmButton:false, timer:1500});" class="text-gray-400 hover:text-green-600" title="Copiar URL">
                                    <i class="ri-file-copy-line text-sm"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $item->product ? $item->product->name : 'N/A' }}</div>
                            <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500">
                                <span class="bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded font-mono font-medium">Lote: {{ $item->batch ?: 'S/L' }}</span>
                                @if(!empty($item->bag_number))
                                <span class="bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded font-mono font-medium">Barcina #{{ $item->bag_number }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                <i class="ri-map-pin-line mr-1 text-xs"></i> {{ $item->location ? $item->location->name : 'N/A' }}
                            </span>
                            @if($item->location && $item->location->warehouse)
                            <div class="text-[11px] text-gray-400 mt-0.5">{{ $item->location->warehouse->name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono font-semibold text-gray-800">
                            <div>{{ number_format($item->live_net_weight, 2) }} {{ $item->unit ?: 'kg' }}</div>
                            @if($item->cli && abs((float)$item->cli->net_weight - (float)$item->net_weight) > 0.001)
                            <span class="text-[10px] text-amber-600 block font-sans" title="Peso original al emitir el QR: {{ number_format($item->net_weight, 2) }}">
                                <i class="ri-refresh-line"></i> Actualizado (orig: {{ number_format($item->net_weight, 2) }})
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <p class="text-xs text-gray-600 line-clamp-2" title="{{ $item->description }}">
                                {{ $item->description ?: 'Sin descripción adicional.' }}
                            </p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                            <div class="font-medium text-gray-700">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/A' }}</div>
                            <div class="text-gray-400">{{ $item->creator ? $item->creator->name : 'Sistema' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-1">
                            <a href="{{ route('product-inf.rack', $item->uuid) }}" target="_blank" class="inline-flex items-center p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition" title="Abrir URL Pública">
                                <i class="ri-external-link-line"></i>
                            </a>
                            <button type="button" class="btn-print-direct inline-flex items-center p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md transition" data-uuid="{{ $item->uuid }}" data-url="{{ $scanUrl }}" data-product="{{ $item->product ? $item->product->name : 'N/A' }}" data-batch="{{ $item->batch ?: 'N/A' }}" data-bag="{{ $item->bag_number ?: 'N/A' }}" data-location="{{ $item->location ? $item->location->name : 'N/A' }}" data-netweight="{{ number_format($item->live_net_weight, 2) }} {{ $item->unit ?: 'kg' }}" title="Imprimir Etiqueta">
                                <i class="ri-printer-line"></i>
                            </button>
                            <a href="{{ route('product-inf.download-svg', $item->uuid) }}" class="inline-flex items-center p-1.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-md transition" title="Descargar SVG">
                                <i class="ri-download-2-line"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $qrs->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL 1: Maps del Módulo Warehouse (Reutilizado) --}}
@include('warehouse.modals.maps')

{{-- MODAL 2: Selección de Producto y Formulario de Descripción --}}
<x-modal id="productSelectModal">
    <div class="p-2 sm:p-4">
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-green-700">Paso 1 de 2: Selección de Producto</span>
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    Productos en la Posición: <span id="pos-selected-tag" class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-mono">--</span>
                </h3>
            </div>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <!-- Contenedor dinámico de productos en la ubicación -->
        <div id="location-products-container" class="space-y-3 max-h-72 overflow-y-auto mb-6 pr-1">
            <div class="text-center py-8 text-gray-500">
                <i class="ri-loader-4-line animate-spin text-3xl text-green-600 block mb-2"></i>
                Consultando productos en la ubicación...
            </div>
        </div>

        <!-- Formulario de Descripción a Detalle -->
        <div id="step-description-wrapper" class="hidden border-t pt-4">
            <div class="mb-3">
                <label for="product-detailed-desc" class="block text-sm font-semibold text-gray-800 mb-1">
                    Descripción a detalle sobre el producto <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-500 mb-2">
                    Ingresa las especificaciones, estado físico de la barcina/saco, observaciones de calidad o notas relevantes de almacenamiento.
                </p>
                <textarea id="product-detailed-desc" rows="3" maxlength="2500" placeholder="Ej. Tarima sellada con película plástica, saco en condiciones óptimas, lote de producción validado..." class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                <div class="text-right text-[11px] text-gray-400 mt-1">
                    <span id="char-counter">0</span> / 2500 caracteres
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" class="close-modal px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" id="btn-proceed-preview" class="px-5 py-2 bg-[#198754] text-white text-sm font-semibold rounded-lg hover:bg-[#157347] shadow-sm transition">
                    Continuar a Previsualización <i class="ri-arrow-right-line ml-1"></i>
                </button>
            </div>
        </div>
    </div>
</x-modal>

{{-- MODAL 3: Previsualización Completa de Información --}}
<x-modal id="productPreviewModal">
    <div class="p-2 sm:p-4">
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-green-700">Paso 2 de 2: Confirmación</span>
                <h3 class="text-xl font-bold text-gray-900">Previsualización de Información del Producto</h3>
            </div>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4 flex items-start gap-2.5">
            <i class="ri-information-line text-amber-600 text-lg mt-0.5 flex-shrink-0"></i>
            <p class="text-xs text-amber-800 leading-relaxed">
                Verifica que toda la información previa y la nueva descripción sean correctas. Al confirmar, el sistema generará una <strong>URL permanente estática</strong> y su correspondiente código QR vectorial que nunca expirará.
            </p>
        </div>

        <!-- Tabla Resumen de Datos Previamente Guardados -->
        <div class="border border-gray-200 rounded-xl overflow-hidden mb-4 text-sm">
            <div class="bg-gray-50 px-4 py-2 font-semibold text-xs text-gray-700 uppercase tracking-wider border-b">
                1. Información Guardada en Almacén
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 p-4 text-xs">
                <div><span class="text-gray-500">Ubicación / Rack:</span> <strong id="prev-location" class="text-emerald-700 font-mono">--</strong></div>
                <div><span class="text-gray-500">Concepto:</span> <strong id="prev-concept">--</strong></div>
                <div><span class="text-gray-500">Producto:</span> <strong id="prev-product" class="text-gray-900">--</strong></div>
                <div><span class="text-gray-500">Lote (Batch):</span> <strong id="prev-batch" class="font-mono">--</strong></div>
                <div><span class="text-gray-500">Barcina #:</span> <strong id="prev-bag" class="font-mono">--</strong></div>
                <div><span class="text-gray-500">Cantidad:</span> <strong id="prev-qty">--</strong></div>
                <div><span class="text-gray-500">Peso Unitario:</span> <strong id="prev-wpu">--</strong></div>
                <div><span class="text-gray-500">Peso Total Neto:</span> <strong id="prev-total" class="text-emerald-700 font-mono">--</strong></div>
            </div>
        </div>

        <!-- Nueva Descripción Registrada -->
        <div class="border border-gray-200 rounded-xl overflow-hidden mb-6 text-sm">
            <div class="bg-gray-50 px-4 py-2 font-semibold text-xs text-gray-700 uppercase tracking-wider border-b">
                2. Nueva Descripción a Detalle
            </div>
            <div class="p-4 bg-gray-50/50">
                <p id="prev-description" class="text-xs text-gray-800 whitespace-pre-line leading-relaxed font-normal">--</p>
            </div>
        </div>

        <div class="flex items-center justify-between border-t pt-4">
            <button type="button" id="btn-back-to-step1" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                <i class="ri-arrow-left-line mr-1"></i> Modificar Datos
            </button>
            <button type="button" id="btn-confirm-generate-qr" class="px-6 py-2 bg-[#198754] hover:bg-[#157347] text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center">
                <span id="btn-confirm-text">Confirmar y Generar Código QR</span>
                <i class="ri-qr-code-line ml-2"></i>
            </button>
        </div>
    </div>
</x-modal>

{{-- MODAL 4: Resultado del QR Generado y Opciones de Impresión / SVG --}}
<x-modal id="qrResultModal">
    <div class="p-2 sm:p-6 text-center">
        <div class="w-14 h-14 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
            <i class="ri-checkbox-circle-fill text-3xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-1">¡Código QR Generado con Éxito!</h3>
        <p class="text-xs text-gray-500 mb-6">El código QR ha sido vinculado al UUID permanente y nunca expirará.</p>

        <!-- Recuadro del QR Vectorial -->
        <div class="bg-white p-4 rounded-2xl shadow-lg border border-gray-200 inline-block mb-4 mx-auto" id="qr-result-svg-container">
            <!-- SVG insertado por JS -->
        </div>

        <!-- URL Permanente con botón de copiar -->
        <div class="max-w-md mx-auto bg-gray-50 rounded-lg p-2.5 border border-gray-200 flex items-center justify-between gap-2 mb-6 text-xs font-mono">
            <span id="qr-result-url-text" class="text-gray-700 truncate select-all">--</span>
            <button type="button" id="btn-copy-generated-url" class="px-2.5 py-1 bg-white border border-gray-300 rounded text-gray-700 hover:text-green-700 transition flex-shrink-0 flex items-center gap-1">
                <i class="ri-file-copy-line"></i> Copiar
            </button>
        </div>

        <!-- Opciones de Descarga / Impresión -->
        <div class="flex flex-wrap items-center justify-center gap-3">
            <button type="button" id="btn-print-generated-label" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center">
                <i class="ri-printer-line mr-2"></i> Imprimir Etiqueta
            </button>
            <a id="btn-download-svg-action" href="#" class="px-5 py-2.5 bg-[#198754] hover:bg-[#157347] text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center">
                <i class="ri-download-2-line mr-2"></i> Guardar como SVG
            </a>
            <a id="btn-visit-public-scan" href="#" target="_blank" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition flex items-center">
                <i class="ri-external-link-line mr-1.5"></i> Probar Escaneo
            </a>
        </div>

        <div class="mt-6 pt-4 border-t">
            <button type="button" class="close-modal px-4 py-1.5 text-xs text-gray-400 hover:text-gray-600">
                Cerrar ventana
            </button>
        </div>
    </div>
</x-modal>

{{-- SECCIÓN OCULTA PARA IMPRESIÓN LIMPIA DE ETIQUETA --}}
<div id="printable-label-wrapper" class="hidden">
    <div class="p-6 bg-white border-2 border-black max-w-sm mx-auto text-center font-sans">
        <div class="flex items-center justify-center space-x-2 border-b-2 border-black pb-2 mb-3">
            <img src="/images/logo.png" style="max-height: 35px; width: auto;" alt="Success">
            <div class="text-left">
                <h4 style="font-size: 11px; font-weight: bold; margin: 0; line-height: 1.1;">SUCCESS SUMINISTROS</h4>
                <p style="font-size: 8px; margin: 0; text-transform: uppercase;">Control de Almacén</p>
            </div>
        </div>

        <div id="print-qr-svg-holder" style="width: 170px; height: 170px; margin: 0 auto 10px auto;"></div>

        <div style="font-size: 13px; font-weight: bold; margin-bottom: 4px;" id="print-product-name"></div>
        
        <table style="width: 100%; font-size: 10px; border-collapse: collapse; margin-bottom: 8px; text-align: left;">
            <tr>
                <td style="padding: 2px; font-weight: bold; width: 40%;">Lote:</td>
                <td style="padding: 2px;" id="print-batch"></td>
            </tr>
            <tr>
                <td style="padding: 2px; font-weight: bold;">Barcina #:</td>
                <td style="padding: 2px;" id="print-bag"></td>
            </tr>
            <tr>
                <td style="padding: 2px; font-weight: bold;">Ubicación:</td>
                <td style="padding: 2px;" id="print-location"></td>
            </tr>
            <tr>
                <td style="padding: 2px; font-weight: bold;">Peso Neto:</td>
                <td style="padding: 2px; font-weight: bold;" id="print-weight"></td>
            </tr>
            <tr>
                <td style="padding: 2px; font-weight: bold;">Fecha:</td>
                <td style="padding: 2px;" id="print-date"></td>
            </tr>
        </table>

        <div style="font-size: 8px; font-family: monospace; color: #555; word-break: break-all;" id="print-uuid"></div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function(){

    // Configuración CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Estado del asistente de creación de QR
    let currentSelection = {
        locationName: null,
        product: null,
        description: ''
    };

    let generatedQrData = null;

    // Manejo de Modales
    function openModal(id) {
        $('#' + id).removeClass('hidden');
    }

    function closeModal(id) {
        $('#' + id).addClass('hidden');
    }

    $(document).on('click', '.open-modal', function(){
        const target = $(this).data('target');
        if (target) openModal(target);
    });

    $(document).on('click', '.close-modal', function(){
        $(this).closest('.fixed').addClass('hidden');
    });

    // Contador de caracteres en descripción
    $('#product-detailed-desc').on('input', function(){
        const len = $(this).val().length;
        $('#char-counter').text(len);
    });

    // Interceptar clic en spots del mapa de almacén (#maps .location-map)
    $(document).off('click', '#maps .location-map').on('click', '#maps .location-map', function(e){
        e.preventDefault();
        e.stopPropagation();

        const locationName = $(this).text().trim();
        if (!locationName) return;

        closeModal('maps');
        currentSelection.locationName = locationName;
        currentSelection.product = null;
        $('#pos-selected-tag').text(locationName);
        $('#step-description-wrapper').addClass('hidden');
        $('#product-detailed-desc').val('');
        $('#char-counter').text('0');

        openModal('productSelectModal');
        loadLocationProducts(locationName);
    });

    // Cargar productos en la ubicación seleccionada
    function loadLocationProducts(locationName) {
        const container = $('#location-products-container');
        container.html(`
            <div class="text-center py-8 text-gray-500">
                <i class="ri-loader-4-line animate-spin text-3xl text-green-600 block mb-2"></i>
                Consultando existencias en la ubicación <strong>${escapeHtml(locationName)}</strong>...
            </div>
        `);

        $.ajax({
            url: "{{ route('product-inf.location-products') }}",
            type: "GET",
            data: { location: locationName },
            dataType: "json",
            success: function(res) {
                const products = res.products || [];
                if (products.length === 0) {
                    container.html(`
                        <div class="text-center py-8 px-4 border-2 border-dashed border-gray-300 rounded-xl">
                            <i class="ri-inbox-line text-4xl text-gray-400 block mb-2"></i>
                            <h4 class="font-bold text-gray-700">Sin productos en esta posición</h4>
                            <p class="text-xs text-gray-500 mt-1">El rack ${escapeHtml(locationName)} actualmente no tiene stock asignado.</p>
                            <button type="button" class="mt-4 px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg" onclick="$('#productSelectModal').addClass('hidden'); $('#maps').removeClass('hidden');">
                                <i class="ri-arrow-left-line"></i> Elegir otro rack
                            </button>
                        </div>
                    `);
                    return;
                }

                let html = '';
                products.forEach((p, idx) => {
                    const formattedTotal = Number(p.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const formattedWpu = Number(p.wpu).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const hasQr = p.has_qr ? true : false;

                    html += `
                        <div class="product-item-card border-2 border-gray-200 hover:border-green-500 rounded-xl p-3.5 transition cursor-pointer bg-white" data-index="${idx}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 bg-green-50 text-green-700 rounded border border-green-200">${escapeHtml(p.cName || 'Almacén')}</span>
                                        ${p.bag_number ? `<span class="text-[11px] font-mono font-bold px-2 py-0.5 bg-blue-50 text-blue-700 rounded border border-blue-200">Barcina #${escapeHtml(p.bag_number)}</span>` : ''}
                                        ${hasQr ? `<span class="text-[11px] font-semibold px-2 py-0.5 bg-amber-50 text-amber-700 rounded border border-amber-200 flex items-center gap-1"><i class="ri-qr-code-line"></i> QR Ya Asociado</span>` : ''}
                                    </div>
                                    <h4 class="font-bold text-gray-900 text-sm">${escapeHtml(p.pName)}</h4>
                                    <div class="grid grid-cols-3 gap-2 mt-2 text-xs text-gray-600">
                                        <div><span class="text-gray-400 block text-[10px]">Lote:</span> <strong class="font-mono text-gray-800">${escapeHtml(p.batch || 'S/L')}</strong></div>
                                        <div><span class="text-gray-400 block text-[10px]">Cantidad:</span> <strong class="text-gray-800">${escapeHtml(p.quantity)}</strong></div>
                                        <div><span class="text-gray-400 block text-[10px]">Peso Neto Total:</span> <strong class="text-emerald-700 font-mono">${formattedTotal} ${escapeHtml(p.unit || 'kg')}</strong></div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 self-center">
                                    <span class="btn-select-indicator inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-[#198754] hover:text-white transition">
                                        Seleccionar
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                });

                container.html(html);

                // Guardar la lista cargada en memoria
                container.data('products-list', products);
            },
            error: function(xhr) {
                container.html(`
                    <div class="p-4 bg-red-50 text-red-700 rounded-lg text-xs">
                        Error al consultar productos de la ubicación. Inténtelo nuevamente.
                    </div>
                `);
            }
        });
    }

    // Selección de producto en la tarjeta
    $(document).on('click', '.product-item-card', function(){
        const container = $('#location-products-container');
        const list = container.data('products-list') || [];
        const idx = $(this).data('index');
        const selected = list[idx];

        if (!selected) return;

        $('.product-item-card').removeClass('border-green-600 bg-green-50/40').addClass('border-gray-200');
        $(this).removeClass('border-gray-200').addClass('border-green-600 bg-green-50/40');
        $('.product-item-card .btn-select-indicator').text('Seleccionar').removeClass('bg-green-600 text-white').addClass('bg-gray-100 text-gray-700');
        $(this).find('.btn-select-indicator').text('Seleccionado ✓').removeClass('bg-gray-100 text-gray-700').addClass('bg-green-600 text-white');

        currentSelection.product = selected;

        // Si ya tenía descripción guardada previamente, precargarla
        if (selected.existing_description) {
            $('#product-detailed-desc').val(selected.existing_description);
            $('#char-counter').text(selected.existing_description.length);
        }

        $('#step-description-wrapper').removeClass('hidden');
        $('#product-detailed-desc').focus();
    });

    // Botón Continuar a Previsualización
    $('#btn-proceed-preview').on('click', function(){
        if (!currentSelection.product) {
            Swal.fire('Atención', 'Por favor selecciona un producto de la lista.', 'warning');
            return;
        }

        const desc = $('#product-detailed-desc').val().trim();
        if (desc.length < 3) {
            Swal.fire('Descripción Requerida', 'Por favor escribe una descripción a detalle de al menos 3 caracteres.', 'info');
            $('#product-detailed-desc').focus();
            return;
        }

        currentSelection.description = desc;
        const p = currentSelection.product;

        // Llenar datos de previsualización
        $('#prev-location').text(currentSelection.locationName || p.lName || 'N/A');
        $('#prev-concept').text(p.cName || 'Almacén');
        $('#prev-product').text(p.pName || 'N/A');
        $('#prev-batch').text(p.batch || 'S/L');
        $('#prev-bag').text(p.bag_number ? '#' + p.bag_number : 'N/A');
        $('#prev-qty').text(p.quantity || '1');
        $('#prev-wpu').text((p.wpu || '--') + ' ' + (p.unit || 'kg'));
        $('#prev-total').text((Number(p.total).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})) + ' ' + (p.unit || 'kg'));
        $('#prev-description').text(desc);

        closeModal('productSelectModal');
        openModal('productPreviewModal');
    });

    // Regresar de previsualización al paso 1
    $('#btn-back-to-step1').on('click', function(){
        closeModal('productPreviewModal');
        openModal('productSelectModal');
    });

    // Confirmar y Generar Código QR
    $('#btn-confirm-generate-qr').on('click', function(){
        const btn = $(this);
        const p = currentSelection.product;
        if (!p) return;

        btn.prop('disabled', true).addClass('opacity-75');
        $('#btn-confirm-text').text('Generando QR...');

        const payload = {
            cli_id: p.cli_id || null,
            inventory_id: p.inventory_id || null,
            product_id: p.product_id || null,
            location_id: p.location_id || null,
            concept_id: p.concept_id || null,
            bag_number: p.bag_number || null,
            batch: p.batch || null,
            quantity: p.quantity || null,
            weight_per_unit: p.wpu || null,
            net_weight: p.total || null,
            unit: p.unit || 'kg',
            description: currentSelection.description,
            location_name: currentSelection.locationName
        };

        $.ajax({
            url: "{{ route('product-inf.store') }}",
            type: "POST",
            data: payload,
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false).removeClass('opacity-75');
                $('#btn-confirm-text').text('Confirmar y Generar Código QR');

                if (res.success && res.data) {
                    generatedQrData = res.data;
                    closeModal('productPreviewModal');

                    // Llenar modal de resultado
                    $('#qr-result-svg-container').html(res.data.qr_svg);
                    $('#qr-result-url-text').text(res.data.scan_url);
                    $('#btn-visit-public-scan').attr('href', res.data.scan_url);
                    $('#btn-download-svg-action').attr('href', "{{ url('product-inf') }}/" + res.data.uuid + "/download-svg");

                    openModal('qrResultModal');

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Código QR permanente generado',
                        showConfirmButton: false,
                        timer: 2500
                    });
                } else {
                    Swal.fire('Error', res.message || 'No fue posible generar el código QR.', 'error');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).removeClass('opacity-75');
                $('#btn-confirm-text').text('Confirmar y Generar Código QR');

                let errorMsg = 'Ocurrió un error al procesar la solicitud.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    });

    // Copiar URL generada
    $('#btn-copy-generated-url').on('click', function(){
        if (generatedQrData && generatedQrData.scan_url) {
            navigator.clipboard.writeText(generatedQrData.scan_url);
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'URL permanente copiada al portapapeles', showConfirmButton: false, timer: 1800 });
        }
    });

    // Imprimir Etiqueta desde Modal de Resultado
    $('#btn-print-generated-label').on('click', function(){
        if (!generatedQrData) return;
        printLabelSheet(generatedQrData);
    });

    // Imprimir directamente desde la tabla de historial
    $(document).on('click', '.btn-print-direct', function(){
        const data = {
            uuid: $(this).data('uuid'),
            scan_url: $(this).data('url'),
            product_name: $(this).data('product'),
            batch: $(this).data('batch'),
            bag_number: $(this).data('bag'),
            location_name: $(this).data('location'),
            net_weight: $(this).data('netweight')
        };
        printLabelSheet(data);
    });

    // Función para imprimir ficha física
    function printLabelSheet(item) {
        $('#print-product-name').text(item.product_name);
        $('#print-batch').text(item.batch || 'S/L');
        $('#print-bag').text(item.bag_number ? '#' + item.bag_number : 'N/A');
        $('#print-location').text(item.location_name || 'N/A');
        $('#print-weight').text(typeof item.net_weight === 'string' ? item.net_weight : (item.net_weight + ' ' + (item.unit || 'kg')));
        $('#print-date').text(new Date().toLocaleDateString('es-MX', { year:'numeric', month:'2-digit', day:'2-digit', hour:'2-digit', minute:'2-digit' }));
        $('#print-uuid').text('UUID: ' + item.uuid);

        // Generar SVG limpio en contenedor de impresión de forma 100% local
        const svgUrl = "{{ url('product-inf') }}/" + item.uuid + "/download-svg";
        $('#print-qr-svg-holder').html(`
            <img src="${svgUrl}" alt="QR" style="width:100%;height:100%;object-fit:contain;"/>
        `);

        // Abrir ventana emergente de impresión optimizada
        const printContents = document.getElementById('printable-label-wrapper').innerHTML;
        const printWindow = window.open('', '_blank', 'height=600,width=500');
        printWindow.document.write('<html><head><title>Etiqueta QR - Success</title>');
        printWindow.document.write('<style>body{font-family:sans-serif;margin:10px;text-align:center;} @page{size:auto;margin:5mm;}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        setTimeout(function(){
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    // Modal de vista rápida de QR desde la tabla
    $(document).on('click', '.btn-preview-qr', function(){
        const uuid = $(this).data('uuid');
        const url = $(this).data('url');
        const product = $(this).data('product');
        const batch = $(this).data('batch');
        const bag = $(this).data('bag');
        const location = $(this).data('location');
        const weight = $(this).data('netweight');
        const desc = $(this).data('desc');

        generatedQrData = {
            uuid: uuid,
            scan_url: url,
            product_name: product,
            batch: batch,
            bag_number: bag,
            location_name: location,
            net_weight: weight,
            description: desc
        };

        // Cargar SVG localmente desde nuestro endpoint del servidor
        const svgUrl = "{{ url('product-inf') }}/" + uuid + "/download-svg";
        $('#qr-result-svg-container').html(`
            <img src="${svgUrl}" alt="QR" class="w-48 h-48 mx-auto"/>
        `);
        $('#qr-result-url-text').text(url);
        $('#btn-visit-public-scan').attr('href', url);
        $('#btn-download-svg-action').attr('href', svgUrl);

        openModal('qrResultModal');
    });

});
</script>
@endpush
