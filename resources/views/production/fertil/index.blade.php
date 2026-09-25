@extends('layouts.app')

@section('content')
<div class="max-w-8xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Producción de Fertil</h2>
        <div class="flex space-x-2">
            <button type="button" onclick="openRequestModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold text-sm shadow">
                Solicitar a Almacén
            </button>
            <a href="{{ url('production') }}" class="text-gray-600 hover:text-gray-900 bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded font-semibold text-sm shadow">
                Volver a Formatos
            </a>
        </div>
    </div>

    <!-- Tabs Header -->
    <div class="mb-4 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="fertilTab" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg text-green-600 hover:text-green-600 border-green-600 font-bold" id="produccion-tab" type="button" role="tab" onclick="switchTab('produccion')">
                    Registro de Producción
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 font-bold" id="inventario-tab" type="button" role="tab" onclick="switchTab('inventario')">
                    Inventario y Stock
                </button>
            </li>
        </ul>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            <span class="font-medium">Éxito!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Tabs Content -->
    <div id="fertilTabContent">
        <!-- Produccion Tab -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" id="produccion">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Registros de Producción</h3>
                <button type="button" onclick="openCreateProduction()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                    + Nuevo Registro
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table id="production-table" class="display w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th>ID</th>
                            <th>Fecha de Preparación</th>
                            <th>Kg Preparados</th>
                            <th>Fecha de Ensacado</th>
                            <th>Kg Ensacados</th>
                            <th># de Sacos</th>
                            <th>Descripción</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productions as $p)
                        <tr>
                            <td>{{ $p->fertil_production_id }}</td>
                            <td>{{ $p->fecha_preparacion ? \Carbon\Carbon::parse($p->fecha_preparacion)->format('d/m/Y') : '' }}</td>
                            <td>{{ $p->kg_preparados }}</td>
                            <td>{{ $p->fecha_ensacado ? \Carbon\Carbon::parse($p->fecha_ensacado)->format('d/m/Y') : '' }}</td>
                            <td>{{ $p->kg_ensacados }}</td>
                            <td>{{ $p->num_sacos }}</td>
                            <td>{{ $p->descripcion }}</td>
                            <td class="text-right flex justify-end gap-2">
                                <button type="button" onclick="editProduction({{ $p }})" class="text-white bg-blue-500 hover:bg-blue-600 rounded-sm w-8 h-8 flex items-center justify-center" title="Editar">
                                    <img class="w-4 h-4" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                                </button>
                                <form action="{{ route('production.fertil.destroyProduction', $p->fertil_production_id) }}" method="POST" class="m-0 flex" onsubmit="confirmDelete(event, this, 'registro');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-sm w-8 h-8 flex items-center justify-center" title="Eliminar">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventario Tab -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hidden" id="inventario">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700">Inventario y Stock</h3>
                <button type="button" onclick="openCreateInventory()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                    + Nuevo Producto
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="inventory-table" class="display w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th>ID</th>
                            <th>Descripción o Producto</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Stock Min</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventories as $inv)
                        <tr>
                            <td>{{ $inv->fertil_inventory_id }}</td>
                            <td class="font-medium text-gray-900">{{ $inv->producto_descripcion }}</td>
                            <td>
                                @if($inv->cantidad <= $inv->stock_min)
                                    <span class="text-red-600 font-bold">{{ $inv->cantidad }}</span>
                                @else
                                    <span class="text-green-600 font-bold">{{ $inv->cantidad }}</span>
                                @endif
                            </td>
                            <td>{{ $inv->unidad }}</td>
                            <td>{{ $inv->stock_min }}</td>
                            <td class="text-right flex justify-end gap-2">
                                <button type="button" onclick="openOutputInventory({{ $inv }})" class="text-white bg-green-500 hover:bg-green-600 rounded-sm w-8 h-8 flex items-center justify-center" title="Registrar Salida">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                </button>
                                <button type="button" onclick="openHistoryModal({{ $inv->fertil_inventory_id }}, '{{ $inv->producto_descripcion }}')" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-sm w-8 h-8 flex items-center justify-center" title="Ver Historial">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <button type="button" onclick="editInventory({{ $inv }})" class="text-white bg-blue-500 hover:bg-blue-600 rounded-sm w-8 h-8 flex items-center justify-center" title="Editar">
                                    <img class="w-4 h-4" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                                </button>
                                <form action="{{ route('production.fertil.destroyInventory', $inv->fertil_inventory_id) }}" method="POST" class="m-0 flex" onsubmit="confirmDelete(event, this, 'producto');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-sm w-8 h-8 flex items-center justify-center" title="Eliminar">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('production.fertil.modals.production_modal')
@include('production.fertil.modals.inventory_modal')
@include('production.fertil.modals.output_modal')
@include('production.fertil.modals.history_modal')
@include('production.fertil.modals.request_modal')

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#production-table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            responsive: true,
            order: [[0, 'desc']]
        });
        
        $('#inventory-table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            responsive: true,
            order: [[0, 'desc']]
        });

        @php
            $lowStockItems = $inventories->filter(function($i) { 
                return $i->cantidad <= $i->stock_min && $i->stock_min > 0; 
            });
        @endphp
        @if($lowStockItems->count() > 0)
            let alertMessage = '<ul class="text-left mt-2 pl-4 list-disc">';
            @foreach($lowStockItems as $item)
                alertMessage += '<li><b>{{ $item->producto_descripcion }}</b>: Stock actual {{ $item->cantidad }} (Mín: {{ $item->stock_min }})</li>';
            @endforeach
            alertMessage += '</ul>';
            
            Swal.fire({
                icon: 'warning',
                title: 'Atención: Stock Bajo',
                html: 'Los siguientes productos están por debajo de su stock mínimo configurado:<br>' + alertMessage,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#f59e0b'
            });
        @endif
    });

    function switchTab(tabName) {
        document.getElementById('produccion').classList.add('hidden');
        document.getElementById('inventario').classList.add('hidden');
        
        let prodTab = document.getElementById('produccion-tab');
        let invTab = document.getElementById('inventario-tab');
        
        prodTab.className = "inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 font-bold";
        invTab.className = "inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 font-bold";
        
        document.getElementById(tabName).classList.remove('hidden');
        
        if(tabName === 'produccion') {
            prodTab.className = "inline-block p-4 border-b-2 rounded-t-lg text-green-600 hover:text-green-600 border-green-600 font-bold";
        } else {
            invTab.className = "inline-block p-4 border-b-2 rounded-t-lg text-green-600 hover:text-green-600 border-green-600 font-bold";
        }
    }

    function confirmDelete(event, form, type) {
        event.preventDefault();
        let textMsg = type === 'producto' ? '¿Estás seguro de eliminar este producto?' : '¿Estás seguro de eliminar este registro?';
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: textMsg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData(form);
                $.ajax({
                    url: form.action,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message || 'El registro ha sido eliminado correctamente.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un problema al intentar eliminar.'
                        });
                    }
                });
            }
        });
    }
</script>
