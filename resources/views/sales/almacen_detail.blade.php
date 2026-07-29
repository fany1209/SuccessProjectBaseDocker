@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Validación de Venta: Folio {{ $sale->folio }}</h4>
            <span class="badge bg-light text-dark">
                Estado Almacén: {{ strtoupper($sale->almacen_status) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Vendedor:</strong> {{ $sale->user->name ?? 'N/A' }}<br>
                    <strong>Fecha:</strong> {{ $sale->date }}<br>
                    <strong>Tipo:</strong> {{ $sale->sale_type }}
                </div>
                <div class="col-md-6">
                    <strong>Cliente/Prospecto:</strong> {{ $sale->customer ? $sale->customer->name : ($sale->prospect ? $sale->prospect->name : 'N/A') }}<br>
                    @if($sale->almacen_status == 'postponed')
                        <strong class="text-warning">Pospuesto hasta:</strong> {{ $sale->almacen_postponed_date }}<br>
                        <strong class="text-warning">Motivo:</strong> {{ $sale->almacen_comment }}
                    @elseif($sale->almacen_status == 'cancelled')
                        <strong class="text-danger">Motivo Cancelación:</strong> {{ $sale->almacen_comment }}
                    @endif
                </div>
            </div>

            <h5>Productos</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Lote</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->products as $product)
                        <tr>
                            <td>{{ $product->pivot->public_product_name ?: $product->name }}</td>
                            <td>{{ $product->pivot->public_batch ?: 'N/A' }}</td>
                            <td>{{ $product->pivot->quantity }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($sale->almacen_status == 'pending' && Auth::user()->hasAnyRole(['Warehouse', 'Admin']))
            <div class="mt-4 text-end">
                <button type="button" class="btn btn-danger open-modal" data-target="cancelModal">Cancelar Venta</button>
                <button type="button" class="btn btn-warning open-modal" data-target="postponeModal">Posponer Venta</button>
                <button type="button" class="btn btn-success" id="btnConfirmSale">Confirmar y Descontar Inventario</button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Posponer -->
<x-modal id="postponeModal">
  <div class="bg-white rounded-lg p-6 w-[500px]">
      <div class="flex justify-between border-b pb-2 mb-4">
        <h5 class="text-xl font-bold">Posponer Venta</h5>
        <button type="button" class="close-modal font-bold text-gray-500 hover:text-gray-800">X</button>
      </div>
      <div>
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Fecha esperada de confirmación</label>
            <input type="date" id="postponeDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
        </div>
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Motivo</label>
            <textarea id="postponeReason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" rows="3"></textarea>
        </div>
      </div>
      <div class="flex justify-end mt-4 gap-2">
        <button type="button" class="close-modal bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cerrar</button>
        <button type="button" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600" id="btnSavePostpone">Guardar</button>
      </div>
  </div>
</x-modal>

<!-- Modal Cancelar -->
<x-modal id="cancelModal">
  <div class="bg-white rounded-lg p-6 w-[500px]">
      <div class="flex justify-between border-b pb-2 mb-4">
        <h5 class="text-xl font-bold">Cancelar Venta</h5>
        <button type="button" class="close-modal font-bold text-gray-500 hover:text-gray-800">X</button>
      </div>
      <div>
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">Motivo de cancelación</label>
            <textarea id="cancelReason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" rows="3"></textarea>
        </div>
      </div>
      <div class="flex justify-end mt-4 gap-2">
        <button type="button" class="close-modal bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cerrar</button>
        <button type="button" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" id="btnSaveCancel">Confirmar Cancelación</button>
      </div>
  </div>
</x-modal>
@endsection

@push('js')
<script>
$(document).ready(function() {
    function sendAction(action, data = {}) {
        data.action = action;
        data._token = '{{ csrf_token() }}';
        
        $.ajax({
            url: '{{ route('sales.almacen_action', $sale->sale_id) }}',
            type: 'POST',
            data: data,
            success: function(res) {
                if(res.success) {
                    Swal.fire('Éxito', res.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Error en el servidor', 'error');
            }
        });
    }

    $('#btnConfirmSale').click(function() {
        Swal.fire({
            title: '¿Confirmar venta?',
            text: "Esto descontará los productos del inventario y notificará a ventas.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, confirmar'
        }).then((result) => {
            if (result.isConfirmed) {
                sendAction('confirm');
            }
        });
    });

    $('#btnSavePostpone').click(function() {
        let date = $('#postponeDate').val();
        let reason = $('#postponeReason').val();
        if(!date || !reason) {
            return Swal.fire('Atención', 'Ambos campos son obligatorios', 'warning');
        }
        sendAction('postpone', {date, reason});
    });

    $('#btnSaveCancel').click(function() {
        let reason = $('#cancelReason').val();
        if(!reason) {
            return Swal.fire('Atención', 'El motivo es obligatorio', 'warning');
        }
        sendAction('cancel', {reason});
    });
});
</script>
@endpush
