@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Solicitudes de Producción</h2>
        <a href="{{ route('inventory.index') }}" class="text-gray-600 hover:text-gray-900 bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded font-semibold text-sm shadow">
            Volver a Inventario
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg shadow" role="alert">
            <span class="font-medium">Éxito!</span> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="requests-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estatus</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($requests as $req)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $req->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold capitalize text-gray-700">{{ $req->area }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $req->applicant_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($req->status === 'Pendiente')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pendiente
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $req->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center space-x-2">
                                    <!-- Ver Detalle -->
                                    <button onclick="viewRequestDetails('{{ $req->id }}')" class="flex items-center justify-center w-8 h-8 text-white bg-sky-500 hover:bg-sky-600 rounded-md shadow-sm transition-all transform hover:scale-105" title="Ver Detalle">
                                        <img width="16" src="{{ asset('images/ver.png') }}" alt="Ver Detalle" class="icon-white" style="filter: brightness(0) invert(1);"/>
                                    </button>
                                    
                                    @if($req->status === 'Pendiente')
                                        <!-- Hacer Salida Producción -->
                                        <a href="{{ route('inventory.index', ['open_tx' => 'InternalOutput', 'req_id' => $req->id]) }}" class="flex items-center justify-center w-8 h-8 text-white bg-blue-500 hover:bg-blue-600 rounded-md shadow-sm transition-all transform hover:scale-105" title="Hacer Salida Producción">
                                            <img width="16" src="{{ asset('images/caja.png') }}" alt="Hacer Salida Producción" class="icon-white" style="filter: brightness(0) invert(1);"/>
                                        </a>
                                        
                                        <!-- Marcar Surtido -->
                                        <form action="{{ route('warehouse.attendProductionRequest', $req->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Marcar solicitud como surtida? (Asegúrate de haber hecho la salida en inventario)');">
                                            @csrf
                                            <button type="submit" class="flex items-center justify-center w-8 h-8 text-white bg-green-500 hover:bg-green-600 rounded-md shadow-sm transition-all transform hover:scale-105" title="Marcar Surtido">
                                                <img width="16" src="{{ asset('images/check.png') }}" alt="Marcar Surtido" class="icon-white" style="filter: brightness(0) invert(1);"/>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Contenido oculto para SweetAlert -->
                                <template id="details-html-{{ $req->id }}">
                                    <div class="text-left">
                                        <div class="mb-4">
                                            <strong>Comentarios:</strong> <br>
                                            <span class="text-gray-700">{{ $req->comments ?? 'Ningún comentario adicional' }}</span>
                                        </div>
                                        <table class="min-w-full divide-y divide-gray-200 border rounded-md overflow-hidden text-sm">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="px-4 py-2 text-left font-bold text-gray-700">Producto Solicitado</th>
                                                    <th class="px-4 py-2 text-left font-bold text-gray-700">Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($req->items as $item)
                                                <tr>
                                                    <td class="px-4 py-2 text-gray-800">{{ $item->product_name }}</td>
                                                    <td class="px-4 py-2 text-gray-900 font-bold">{{ $item->quantity }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function viewRequestDetails(id) {
        let templateContent = document.getElementById('details-html-' + id).innerHTML;
        
        Swal.fire({
            title: 'Detalles de la Solicitud #' + id,
            html: templateContent,
            width: '600px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                container: 'text-left'
            }
        });
    }
    
    $(document).ready(function() {
        $('#requests-table').DataTable({
            "order": [[ 0, "desc" ]],
            "lengthChange": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });
    });
</script>
@endsection
