@extends('layouts.app')

@section('content')
<div class="max-w-8xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Breadcrumb & Title -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('inventory.index') }}" class="hover:text-blue-600 flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Inventario
                </a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Recepción de Tarimas</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </span>
                Tarimas por Recibir desde Producción
            </h2>
            <p class="text-sm text-gray-500 mt-1">Tarimas en tránsito enviadas por producción listas para su ingreso oficial como Producto Terminado en almacén.</p>
        </div>
        <div>
            <a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
                Volver a Inventario
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 border border-gray-100">
        <table id="pending-pallets-table" class="display w-full text-sm text-left text-gray-600">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                <tr>
                    <th class="py-3 px-4">Tarima No.</th>
                    <th class="py-3 px-4">Tipo (Color)</th>
                    <th class="py-3 px-4">Sacos</th>
                    <th class="py-3 px-4">Peso Final (kg)</th>
                    <th class="py-3 px-4">Fecha de Envío</th>
                    <th class="py-3 px-4">Estado</th>
                    <th class="py-3 px-4 text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pallets as $pallet)
                @php
                    $finalWeight = $pallet->total_weight ?? ($pallet->current_sacks * 25);
                @endphp
                <tr class="hover:bg-gray-50 transition-colors border-b">
                    <td class="py-3 px-4 font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                        {{ $pallet->pallet_number }}
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                            @if($pallet->color_type == 'Natural') bg-amber-100 text-amber-800
                            @elseif($pallet->color_type == 'Mix') bg-purple-100 text-purple-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ $pallet->color_type }}
                        </span>
                    </td>
                    <td class="py-3 px-4 font-semibold text-gray-800">{{ $pallet->current_sacks }} sacos</td>
                    <td class="py-3 px-4 font-bold text-blue-700">{{ number_format($finalWeight, 2) }} kg</td>
                    <td class="py-3 px-4 text-gray-500">{{ $pallet->updated_at->format('d/m/Y H:i') }}</td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                            En Tránsito
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <button type="button" 
                            onclick="openAcceptModal({{ $pallet->pallet_id }}, '{{ $pallet->pallet_number }}', {{ $pallet->current_sacks }}, {{ $finalWeight }}, '{{ $pallet->color_type }}')" 
                            class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-1.5 px-3.5 rounded-lg text-xs shadow-sm transition-all hover:shadow-md cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Dar Entrada a Almacén
                        </button>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Registrar Entrada (makeTransaction style) -->
<div id="accept-inventory-modal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto flex items-center justify-center bg-gray-900/60 backdrop-blur-xs transition-opacity">
    <div class="relative w-full max-w-4xl max-h-[90vh] flex flex-col my-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden border border-gray-100">
            <!-- Modal header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-emerald-100 text-emerald-700 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            Entrada a Almacén: <span id="modal_display_pallet_number" class="text-blue-600"></span>
                        </h3>
                        <p class="text-xs text-gray-500">Transacción de Entrada (Input) para registrar la tarima como Producto Terminado en el inventario.</p>
                    </div>
                </div>
                <button type="button" onclick="closeAcceptModal()" class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal body -->
            <div class="p-6 space-y-6 overflow-y-auto">
                <form id="accept-inventory-form" class="space-y-6">
                    @csrf
                    <input type="hidden" id="accept_pallet_id" name="pallet_id">

                    <!-- Section 1: Tipo & Proveedor & Concepto -->
                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200/80 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                1. Datos de la Transacción
                            </span>
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Tipo: Input (Entrada)
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="modal_supplier_id" class="block mb-1 text-xs font-semibold text-gray-700">Proveedor</label>
                                <select id="modal_supplier_id" name="supplier_id" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5" required>
                                    @foreach($suppliers as $sup)
                                        <option value="{{ $sup->supplier_id }}" {{ ($sup->supplier_id == ($internalSupplier->supplier_id ?? null)) ? 'selected' : '' }}>
                                            {{ $sup->name }} ({{ $sup->supplier_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="modal_concept_id" class="block mb-1 text-xs font-semibold text-gray-700">Concepto de Entrada</label>
                                <select id="modal_concept_id" name="concept_id" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5" required>
                                    @foreach($concepts as $con)
                                        <option value="{{ $con->concept_id }}" {{ ($con->concept_id == ($internalConcept->concept_id ?? 2)) ? 'selected' : '' }}>
                                            {{ $con->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Producto, Lote y Pesos -->
                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200/80 space-y-4">
                        <div class="border-b border-gray-200 pb-2 flex justify-between items-center">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                2. Producto, Sacos y Peso Final
                            </span>
                            <span class="text-xs text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                Unidad de inventario: Kilogramos (Kg)
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label for="modal_product_id" class="block mb-1 text-xs font-semibold text-gray-700">Producto</label>
                                <select id="modal_product_id" name="product_id" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5" required>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->product_id }}" {{ ($prod->product_id == ($defaultProduct->product_id ?? 623)) ? 'selected' : '' }}>
                                            {{ $prod->name }} ({{ $prod->unit ?? 'Kg' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="modal_warehouse_batch" class="block mb-1 text-xs font-semibold text-gray-700">Lote / No. Tarima</label>
                                <input type="text" id="modal_warehouse_batch" name="warehouse_batch" class="bg-white border border-gray-300 text-gray-800 text-sm font-semibold rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label for="modal_quantity" class="block mb-1 text-xs font-semibold text-gray-700">Sacos en Tarima</label>
                                <input type="number" step="1" min="1" id="modal_quantity" name="quantity" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5" required oninput="recalculateWeights('sacks')">
                            </div>

                            <div>
                                <label for="modal_weight_per_unit" class="block mb-1 text-xs font-semibold text-gray-700">Peso Promedio por Saco (kg)</label>
                                <input type="number" step="0.01" min="0.1" id="modal_weight_per_unit" name="weight_per_unit" value="25" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5" required oninput="recalculateWeights('wpu')">
                            </div>

                            <div>
                                <label for="modal_final_weight" class="block mb-1 text-xs font-bold text-blue-900 flex items-center justify-between">
                                    <span>Peso Final a Ingresar (Kg)</span>
                                    <span class="text-emerald-600 text-[11px] font-normal">Stock Real</span>
                                </label>
                                <input type="number" step="0.01" min="0.1" id="modal_final_weight" name="final_weight" class="bg-blue-50/70 border-2 border-blue-400 text-blue-950 font-black text-lg rounded-lg block w-full p-2 focus:ring-2 focus:ring-blue-500" required oninput="recalculateWeights('final')">
                            </div>
                        </div>
                        <p class="text-xs text-blue-700 bg-blue-50/60 p-2 rounded-lg border border-blue-200/60 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            El valor de <strong>Peso Final a Ingresar (Kg)</strong> es la cantidad exacta que sumará a las existencias disponibles de Yeacali en Almacén.
                        </p>
                    </div>

                    <!-- Section 3: Ubicación -->
                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200/80 space-y-4">
                        <div class="border-b border-gray-200 pb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                3. Asignación de Almacén y Ubicación
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="modal_warehouse_id" class="block mb-1 text-xs font-semibold text-gray-700">Almacén</label>
                                <select id="modal_warehouse_id" onchange="filterLocationsByWarehouse()" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                                    <option value="">-- Todos los Almacenes --</option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->warehouse_id }}">{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="modal_location_id" class="block mb-1 text-xs font-semibold text-gray-700">Ubicación (Rack / Posición) <span class="text-red-500">*</span></label>
                                <select id="modal_location_id" name="location_id" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5" required>
                                    <option value="">Seleccione una ubicación</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->location_id }}" data-warehouse="{{ $loc->warehouse_id }}">
                                            {{ $loc->warehouse->name ?? 'Almacén General' }} - {{ $loc->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Datos de Transporte / Operador / Comentarios (Opcional) -->
                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200/80 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                4. Datos de Recepción y Transporte (Opcional)
                            </span>
                            <button type="button" onclick="toggleTransportSection()" class="text-xs font-semibold text-blue-600 hover:text-blue-800 cursor-pointer">
                                <span id="toggle-transport-btn-text">Mostrar / Ocultar</span>
                            </button>
                        </div>

                        <div id="transport-fields-container" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="modal_transport_line" class="block mb-1 text-xs font-semibold text-gray-700">Línea de Transporte</label>
                                    <select id="modal_transport_line" name="transport_line" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                                        <option value="">-- Ninguna / Interno --</option>
                                        @foreach($transport_lines as $tl)
                                            <option value="{{ $tl->transport_line_id }}">{{ $tl->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="modal_operator" class="block mb-1 text-xs font-semibold text-gray-700">Operador / Entregó</label>
                                    <input type="text" id="modal_operator" name="operator" placeholder="Nombre de quien entrega" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                                </div>

                                <div>
                                    <label for="modal_license_number" class="block mb-1 text-xs font-semibold text-gray-700">No. Licencia</label>
                                    <input type="text" id="modal_license_number" name="license_number" placeholder="Opcional" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="modal_unit_plates" class="block mb-1 text-xs font-semibold text-gray-700">Placas Unidad</label>
                                    <input type="text" id="modal_unit_plates" name="unit_plates" placeholder="Placas o vehículo" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                                </div>

                                <div>
                                    <label for="modal_trailer_plates" class="block mb-1 text-xs font-semibold text-gray-700">Placas Remolque</label>
                                    <input type="text" id="modal_trailer_plates" name="trailer_plates" placeholder="Placas remolque" class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                                </div>
                            </div>

                            <div>
                                <label for="modal_comments" class="block mb-1 text-xs font-semibold text-gray-700">Comentarios / Observaciones</label>
                                <textarea id="modal_comments" name="comments" rows="2" placeholder="Observaciones de la recepción o condiciones de la tarima..." class="bg-white border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50 gap-3">
                <button type="button" onclick="closeAcceptModal()" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-xs cursor-pointer">
                    Cancelar
                </button>
                <button type="button" onclick="submitAcceptPallet()" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm hover:shadow-md transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Registrar Entrada a Inventario
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const allLocations = @json($locations);

    document.addEventListener('DOMContentLoaded', function () {
        $('#pending-pallets-table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            responsive: true,
            order: [[0, 'desc']]
        });
    });

    function openAcceptModal(palletId, palletNumber, sacks, finalWeight, colorType) {
        document.getElementById('accept_pallet_id').value = palletId;
        document.getElementById('modal_display_pallet_number').innerText = palletNumber + ' (' + colorType + ')';
        document.getElementById('modal_warehouse_batch').value = palletNumber;
        document.getElementById('modal_quantity').value = sacks;
        document.getElementById('modal_weight_per_unit').value = 25;
        
        let calculatedFinal = finalWeight ? parseFloat(finalWeight) : (sacks * 25);
        document.getElementById('modal_final_weight').value = calculatedFinal.toFixed(2);

        // Reset warehouse filter and location
        document.getElementById('modal_warehouse_id').value = '';
        filterLocationsByWarehouse();

        document.getElementById('accept-inventory-modal').classList.remove('hidden');
    }

    function closeAcceptModal() {
        document.getElementById('accept-inventory-modal').classList.add('hidden');
    }

    function recalculateWeights(source) {
        const qtyInput = document.getElementById('modal_quantity');
        const wpuInput = document.getElementById('modal_weight_per_unit');
        const finalInput = document.getElementById('modal_final_weight');

        let sacks = parseFloat(qtyInput.value) || 0;
        let wpu = parseFloat(wpuInput.value) || 0;
        let finalWeight = parseFloat(finalInput.value) || 0;

        if (source === 'sacks' || source === 'wpu') {
            finalInput.value = (sacks * wpu).toFixed(2);
        } else if (source === 'final') {
            if (sacks > 0) {
                wpuInput.value = (finalWeight / sacks).toFixed(2);
            }
        }
    }

    function filterLocationsByWarehouse() {
        const selectedWh = document.getElementById('modal_warehouse_id').value;
        const locSelect = document.getElementById('modal_location_id');
        locSelect.innerHTML = '<option value="">Seleccione una ubicación</option>';

        allLocations.forEach(loc => {
            if (!selectedWh || loc.warehouse_id == selectedWh) {
                const whName = loc.warehouse ? loc.warehouse.name : 'Almacén General';
                const opt = document.createElement('option');
                opt.value = loc.location_id;
                opt.textContent = `${whName} - ${loc.name}`;
                locSelect.appendChild(opt);
            }
        });
    }

    function toggleTransportSection() {
        const cont = document.getElementById('transport-fields-container');
        cont.classList.toggle('hidden');
    }

    function submitAcceptPallet() {
        const palletId = document.getElementById('accept_pallet_id').value;
        const locationId = document.getElementById('modal_location_id').value;
        const supplierId = document.getElementById('modal_supplier_id').value;
        const conceptId = document.getElementById('modal_concept_id').value;
        const productId = document.getElementById('modal_product_id').value;
        const batch = document.getElementById('modal_warehouse_batch').value.trim();
        const quantity = parseFloat(document.getElementById('modal_quantity').value);
        const weightPerUnit = parseFloat(document.getElementById('modal_weight_per_unit').value);
        const finalWeight = parseFloat(document.getElementById('modal_final_weight').value);

        if (!locationId) {
            Swal.fire({
                icon: 'warning',
                title: 'Ubicación Requerida',
                text: 'Por favor seleccione la ubicación (Rack/Posición) donde se almacenará la tarima.'
            });
            return;
        }

        if (!batch) {
            Swal.fire({
                icon: 'warning',
                title: 'Lote Requerido',
                text: 'El número de lote / tarima es obligatorio.'
            });
            return;
        }

        if (!quantity || quantity <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad Inválida',
                text: 'La cantidad de sacos debe ser mayor a 0.'
            });
            return;
        }

        if (!finalWeight || finalWeight <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peso Final Inválido',
                text: 'El peso final en Kg debe ser mayor a 0.'
            });
            return;
        }

        closeAcceptModal();

        Swal.fire({
            title: 'Registrando Entrada...',
            text: `Ingresando tarima como Producto Terminado con ${finalWeight.toFixed(2)} kg de stock.`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const payload = {
            location_id: locationId,
            supplier_id: supplierId,
            concept_id: conceptId,
            product_id: productId,
            warehouse_batch: batch,
            quantity: quantity,
            weight_per_unit: weightPerUnit,
            final_weight: finalWeight,
            transport_line: document.getElementById('modal_transport_line').value || null,
            operator: document.getElementById('modal_operator').value || null,
            license_number: document.getElementById('modal_license_number').value || null,
            unit_plates: document.getElementById('modal_unit_plates').value || null,
            trailer_plates: document.getElementById('modal_trailer_plates').value || null,
            comments: document.getElementById('modal_comments').value || null
        };

        fetch(`/inventory/pallets/${palletId}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Error al procesar la entrada.');
            }
            return data;
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: '¡Entrada Exitosa!',
                text: data.message || 'Tarima recibida e ingresada al almacén correctamente.',
                timer: 2200,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Hubo un error al registrar la entrada de la tarima.'
            }).then(() => {
                document.getElementById('accept-inventory-modal').classList.remove('hidden');
            });
        });
    }
</script>
@endsection
