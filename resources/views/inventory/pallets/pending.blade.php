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
            <div class="p-6 overflow-y-auto">
                <form id="accept-inventory-form" class="flex flex-col items-center w-full gap-2">
                    @csrf
                    <input type="hidden" id="accept_pallet_id" name="pallet_id">

                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="modal_supplier_id">Proveedor</x-label>
                            <x-select-1 id="modal_supplier_id" name="supplier_id" required>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->supplier_id }}" {{ ($sup->supplier_id == ($internalSupplier->supplier_id ?? null)) ? 'selected' : '' }}>
                                        {{ $sup->name }} ({{ $sup->supplier_code }})
                                    </option>
                                @endforeach
                            </x-select-1>
                        </x-wrapper-form-2>

                        <x-wrapper-form-2>
                            <x-label for="modal_concept_id">Concepto de Entrada</x-label>
                            <x-select-1 id="modal_concept_id" name="concept_id" required>
                                @foreach($concepts as $con)
                                    <option value="{{ $con->concept_id }}" {{ ($con->concept_id == ($internalConcept->concept_id ?? 2)) ? 'selected' : '' }}>
                                        {{ $con->name }}
                                    </option>
                                @endforeach
                            </x-select-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>

                    <x-wrapper-form-1 class="flex-col border-2 border-gray-200 rounded-md p-2">
                        <x-wrapper-form-1>
                            <span class="tracking-[3px] bg-blue-500 text-white font-semibold px-2 py-1 rounded-md">Products</span>
                        </x-wrapper-form-1>

                        <div class="w-full">
                            <div class="wrapper border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative">
                                <x-wrapper-form-1>
                                    <x-wrapper-form-2>
                                        <x-label for="modal_product_id">Product</x-label>
                                        <x-select-1 id="modal_product_id" name="product_id" required>
                                            @foreach($products as $prod)
                                                <option value="{{ $prod->product_id }}" {{ ($prod->product_id == ($defaultProduct->product_id ?? 623)) ? 'selected' : '' }}>
                                                    {{ $prod->name }} ({{ $prod->unit ?? 'Kg' }})
                                                </option>
                                            @endforeach
                                        </x-select-1>
                                    </x-wrapper-form-2>
                                </x-wrapper-form-1>

                                <x-wrapper-form-1>
                                    <x-wrapper-form-2>
                                        <x-label for="modal_quantity">Quantity (Sacos)</x-label>
                                        <x-input-1 type="number" step="1" min="1" id="modal_quantity" name="quantity" required oninput="recalculateWeights('sacks')"></x-input-1>
                                    </x-wrapper-form-2>

                                    <x-wrapper-form-2>
                                        <x-label for="modal_weight_per_unit">Weight per unit</x-label>
                                        <x-input-1 type="number" step="0.01" min="0.1" id="modal_weight_per_unit" name="weight_per_unit" value="25" required oninput="recalculateWeights('wpu')"></x-input-1>
                                    </x-wrapper-form-2>
                                </x-wrapper-form-1>

                                <x-wrapper-form-1 class="location-assignment-wrapper">
                                    <x-wrapper-form-2>
                                        <x-label for="modal_warehouse_id">Warehouse (Optional)</x-label>
                                        <x-select-1 id="modal_warehouse_id" onchange="filterLocationsByWarehouse()">
                                            <option value="">Select a warehouse</option>
                                            @foreach($warehouses as $wh)
                                                <option value="{{ $wh->warehouse_id }}">{{ $wh->name }}</option>
                                            @endforeach
                                        </x-select-1>
                                    </x-wrapper-form-2>

                                    <x-wrapper-form-2 class="location-id-wrapper">
                                        <x-label for="modal_location_id">Location</x-label>
                                        <x-select-1 id="modal_location_id" name="location_id" required>
                                            <option value="">Select a location</option>
                                            @foreach($locations as $loc)
                                                <option value="{{ $loc->location_id }}" data-warehouse="{{ $loc->warehouse_id }}">
                                                    {{ $loc->warehouse->name ?? 'Almacén General' }} - {{ $loc->name }}
                                                </option>
                                            @endforeach
                                        </x-select-1>
                                    </x-wrapper-form-2>
                                </x-wrapper-form-1>

                                <x-wrapper-form-1>
                                    <x-wrapper-form-2>
                                        <x-label for="modal_warehouse_batch">Warehouse batch</x-label>
                                        <x-input-1 type="text" id="modal_warehouse_batch" name="warehouse_batch" required readonly></x-input-1>
                                    </x-wrapper-form-2>
                                    
                                    <x-wrapper-form-2>
                                        <x-label for="modal_final_weight">Total Final Weight (Kg)</x-label>
                                        <x-input-1 type="number" step="0.01" min="0.1" id="modal_final_weight" name="final_weight" required oninput="recalculateWeights('final')"></x-input-1>
                                    </x-wrapper-form-2>
                                </x-wrapper-form-1>
                                
                                <x-wrapper-form-1>
                                    <x-wrapper-form-2><p class="total mt-2 text-sm font-semibold rounded-md tracking-[2px] text-white bg-gray-700 px-2 py-1" id="total_weight_display">Total: 0</p></x-wrapper-form-2>
                                </x-wrapper-form-1>
                            </div>
                        </div>
                    </x-wrapper-form-1>



                    <x-wrapper-form-1>
                        <x-wrapper-form-2>
                            <x-label for="modal_comments">Comments</x-label>
                            <x-textarea-1 id="modal_comments" name="comments" rows="2" placeholder="Observaciones de la recepción..."></x-textarea-1>
                        </x-wrapper-form-2>
                    </x-wrapper-form-1>

                </form>
            </div>

            <!-- Modal footer -->
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-200 bg-gray-50 gap-3 w-full">
                <x-wrapper-form-1>
                    <x-wrapper-form-1>
                        <x-button-1 type="button" colorBtn="red" onclick="closeAcceptModal()">Close</x-button-1>
                    </x-wrapper-form-1>
                    <x-button-1 id="save-transaction" type="button" colorBtn="green" onclick="submitAcceptPallet()">Finish</x-button-1>
                </x-wrapper-form-1>
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
        
        let displayTotal = document.getElementById('total_weight_display');
        if(displayTotal) displayTotal.innerText = 'Total: ' + calculatedFinal.toFixed(2);

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
        const display = document.getElementById('total_weight_display');

        let sacks = parseFloat(qtyInput.value) || 0;
        let wpu = parseFloat(wpuInput.value) || 0;
        let finalWeight = parseFloat(finalInput.value) || 0;

        if (source === 'sacks' || source === 'wpu') {
            finalInput.value = (sacks * wpu).toFixed(2);
            if(display) display.innerText = 'Total: ' + (sacks * wpu).toFixed(2);
        } else if (source === 'final') {
            if (sacks > 0) {
                wpuInput.value = (finalWeight / sacks).toFixed(2);
            }
            if(display) display.innerText = 'Total: ' + finalWeight.toFixed(2);
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
            transport_line: null,
            operator: null,
            license_number: null,
            unit_plates: null,
            trailer_plates: null,
            comments: document.querySelector('textarea[name="comments"]') ? document.querySelector('textarea[name="comments"]').value : null
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
