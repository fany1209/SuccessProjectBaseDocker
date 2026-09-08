@extends('layouts.app')

@section('content')
<div class="max-w-8xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Producción de Levadura</h2>
    </div>

    <!-- Tabs Header -->
    <div class="mb-4 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg text-blue-600 hover:text-blue-600 border-blue-600 font-bold" id="produccion-tab" type="button" role="tab" onclick="switchTab('produccion')">
                    Producción (Barcinas)
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 font-bold" id="tarimas-tab" type="button" role="tab" onclick="switchTab('tarimas')">
                    Trazabilidad de Tarimas
                </button>
            </li>
        </ul>
    </div>

    <!-- Tabs Content -->
    <div id="myTabContent">
        <!-- Produccion Tab -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" id="produccion">
            <table id="yeast-table" class="display w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th>Fecha</th>
                        <th>No. Barcina</th>
                        <th>Peso Interno</th>
                        <th>Peso Externo</th>
                        <th>Natural</th>
                        <th>Mix</th>
                        <th>Blanca</th>
                        <th>Total Sacos</th>
                        <th>Producto Terminado (kg)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($yeastProductions as $yp)
                    <tr>
                        <td>{{ $yp->date ? \Carbon\Carbon::parse($yp->date)->format('d/m/Y') : '' }}</td>
                        <td>{{ $yp->bag_number }}</td>
                        <td>{{ $yp->internal_weight }}</td>
                        <td>{{ $yp->external_weight }}</td>
                        <td>{{ $yp->bags_natural }}</td>
                        <td>{{ $yp->bags_mix }}</td>
                        <td>{{ $yp->bags_white }}</td>
                        <td>{{ $yp->bags_quantity }}</td>
                        <td>{{ $yp->finished_product_kg }}</td>
                        <td class="text-right">
                            <button type="button" onclick="editYeast({{ $yp->yeast_production_id }}, '{{ $yp->internal_weight }}', '{{ $yp->external_weight }}', '{{ $yp->bags_natural }}', '{{ $yp->bags_mix }}', '{{ $yp->bags_white }}', '{{ $yp->finished_product_kg }}')" class="text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2" title="Edit">
                                <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tarimas Tab -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hidden" id="tarimas">
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Control de Tarimas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Tarimas Abiertas (Tarjetas) -->
                @foreach(['Natural', 'Mix', 'Blanca'] as $color)
                    @php 
                        $openPallet = $pallets->where('color_type', $color)->where('status', 'Abierta')->first();
                    @endphp
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-bold text-gray-800">Tarima {{ $color }} (Abierta)</h4>
                            @if($color == 'Natural') <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-amber-400">NAT</span>
                            @elseif($color == 'Mix') <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-orange-400">MIX</span>
                            @else <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-gray-400">BLC</span>
                            @endif
                        </div>
                        @if($openPallet)
                            <p class="text-2xl font-bold text-blue-600">{{ $openPallet->current_sacks }} / 40</p>
                            <p class="text-sm text-gray-500 mb-2">ID: {{ $openPallet->pallet_number }}</p>
                            
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($openPallet->current_sacks / 40) * 100 }}%"></div>
                            </div>
                            
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-1">Aportes actuales:</p>
                                <ul class="text-xs text-gray-600 list-disc pl-4">
                                    @foreach($openPallet->yeastProductions as $yp)
                                        <li>Barcina <b>{{ $yp->bag_number }}</b>: {{ $yp->pivot->sacks_contributed }} sacos</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic mt-2">No hay tarima abierta de este color.</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Tabla de Tarimas Cerradas -->
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Historial de Tarimas Cerradas</h3>
            <table id="pallets-table" class="display w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th>Tarima No.</th>
                        <th>Tipo (Color)</th>
                        <th>Estado</th>
                        <th>Inventario</th>
                        <th>Sacos</th>
                        <th>Barcinas que la formaron</th>
                        <th>Fecha de Cierre</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pallets->where('status', 'Cerrada') as $pallet)
                    <tr>
                        <td class="font-bold text-gray-800">{{ $pallet->pallet_number }}</td>
                        <td>{{ $pallet->color_type }}</td>
                        <td><span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-green-400">Cerrada</span></td>
                        <td>
                            @if($pallet->inventory_status == 'Pendiente')
                                <button type="button" onclick="sendToInventory({{ $pallet->pallet_id }})" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-xs">
                                    Enviar a Almacén
                                </button>
                            @elseif($pallet->inventory_status == 'Enviada')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-yellow-400">En Tránsito</span>
                            @else
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-emerald-400">En Inventario</span>
                            @endif
                        </td>
                        <td>{{ $pallet->current_sacks }}</td>
                        <td>
                            <ul class="list-disc pl-4 text-xs">
                                @foreach($pallet->yeastProductions as $yp)
                                    <li><b>{{ $yp->bag_number }}</b> ({{ $yp->pivot->sacks_contributed }} sacos)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $pallet->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('production.yeast.modals.edit')

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#yeast-table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            responsive: true,
            order: [[0, 'desc']]
        });
        
        $('#pallets-table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            responsive: true,
            order: [[5, 'desc']] // Order by closed date descending
        });
    });

    function switchTab(tabName) {
        // Hide all contents
        document.getElementById('produccion').classList.add('hidden');
        document.getElementById('tarimas').classList.add('hidden');
        
        // Reset all tabs styles
        let prodTab = document.getElementById('produccion-tab');
        let tarimasTab = document.getElementById('tarimas-tab');
        
        prodTab.className = "inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 font-bold";
        tarimasTab.className = "inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 font-bold";
        
        // Show selected content and activate tab style
        document.getElementById(tabName).classList.remove('hidden');
        
        if(tabName === 'produccion') {
            prodTab.className = "inline-block p-4 border-b-2 rounded-t-lg text-blue-600 hover:text-blue-600 border-blue-600 font-bold";
        } else {
            tarimasTab.className = "inline-block p-4 border-b-2 rounded-t-lg text-blue-600 hover:text-blue-600 border-blue-600 font-bold";
        }
    }

    function editYeast(id, iw, ew, bn, bm, bw, fpk) {
        document.getElementById('edit_yeast_id').value = id;
        document.getElementById('edit_internal_weight').value = iw;
        document.getElementById('edit_external_weight').value = ew;
        document.getElementById('edit_bags_natural').value = bn;
        document.getElementById('edit_bags_mix').value = bm;
        document.getElementById('edit_bags_white').value = bw;
        document.getElementById('edit_finished_product_kg').value = fpk;
        
        document.getElementById('edit-yeast-modal').classList.remove('hidden');
    }

    function sendToInventory(palletId) {
        Swal.fire({
            title: '¿Enviar a Almacén?',
            text: 'La tarima se marcará como "En Tránsito" y pasará a la bandeja de recepción de Almacén.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Enviando...',
                    text: 'Por favor espere un momento.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/production/yeast/pallet/${palletId}/send-inventory`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || 'Error al enviar al almacén.');
                    }
                    return data;
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Enviada!',
                        text: data.message || 'Tarima enviada al almacén correctamente.',
                        timer: 1800,
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
                        text: error.message || 'Hubo un error al enviar al almacén.'
                    });
                });
            }
        });
    }
</script>
