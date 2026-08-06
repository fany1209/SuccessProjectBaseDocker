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

<!-- Modal Selección de Lotes -->
<x-modal id="lotSelectionModal">
  <div class="bg-white rounded-lg p-6 w-[700px] max-h-[80vh] overflow-y-auto">
      <div class="flex justify-between border-b pb-2 mb-4">
        <h5 class="text-xl font-bold">Seleccionar Lotes para Descontar</h5>
        <button type="button" class="close-modal font-bold text-gray-500 hover:text-gray-800">X</button>
      </div>
      <p class="text-sm text-gray-600 mb-4">Selecciona de qué lote descontar cada producto. El stock disponible se muestra entre paréntesis.</p>
      <div id="lotSelectionBody">
          <p class="text-gray-400 text-center py-4">Cargando lotes...</p>
      </div>
      <div class="flex justify-end mt-4 gap-2">
        <button type="button" class="close-modal bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</button>
        <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" id="btnConfirmLots">Confirmar y Descontar</button>
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

    // Al dar clic en Confirmar, primero cargamos los lotes disponibles
    $('#btnConfirmSale').click(function() {
        $('#lotSelectionBody').html('<p class="text-gray-400 text-center py-4">Cargando lotes...</p>');
        $('#lotSelectionModal').removeClass('hidden');

        $.get('{{ route("sales.almacen_lots", $sale->sale_id) }}', function(res) {
            if (!res.products || res.products.length === 0) {
                $('#lotSelectionBody').html('<p class="text-red-500 text-center py-4">No se encontraron productos para esta venta.</p>');
                return;
            }

            let html = '';
            res.products.forEach((product, index) => {
                let lotsHtml = '';
                if (product.lots.length === 0) {
                    lotsHtml = '<p class="text-red-500 text-sm">No hay lotes con stock en ubicaciones de almacén para este producto.</p>';
                } else {
                    lotsHtml = `<select name="lot_${index}" class="lot-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" data-product-id="${product.product_id}" data-quantity="${product.quantity_required}">`;
                    lotsHtml += '<option value="">-- Seleccionar lote y ubicación --</option>';
                    product.lots.forEach(lot => {
                        lotsHtml += `<option value="${lot.cli_id}" data-stock="${lot.net_weight}">Lote: ${lot.batch} → ${lot.location_name} (Disponible: ${parseFloat(lot.net_weight).toLocaleString()} kg)</option>`;
                    });
                    lotsHtml += '</select>';
                }

                html += `
                    <div class="mb-4 p-3 border rounded-lg ${product.lots.length === 0 ? 'bg-red-50 border-red-200' : 'bg-gray-50'}">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-sm">${product.product_name}</span>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Cantidad requerida: ${product.quantity_required}</span>
                        </div>
                        ${lotsHtml}
                        <p class="lot-stock-warning text-xs text-red-500 mt-1 hidden">El stock del lote seleccionado es insuficiente.</p>
                    </div>
                `;
            });

            $('#lotSelectionBody').html(html);

            // Validar stock al seleccionar lote
            $(document).off('change', '.lot-select').on('change', '.lot-select', function() {
                let selected = $(this).find(':selected');
                let stock = parseFloat(selected.data('stock')) || 0;
                let required = parseFloat($(this).data('quantity')) || 0;
                let warning = $(this).closest('.mb-4').find('.lot-stock-warning');

                if (selected.val() && stock < required) {
                    warning.removeClass('hidden').text(`Stock insuficiente. Disponible: ${stock}, Requerido: ${required}`);
                } else {
                    warning.addClass('hidden');
                }
            });
        }).fail(function() {
            $('#lotSelectionBody').html('<p class="text-red-500 text-center py-4">Error al cargar los lotes.</p>');
        });
    });

    // Confirmar con lotes seleccionados
    $('#btnConfirmLots').click(function() {
        let lotAssignments = [];
        let valid = true;

        $('.lot-select').each(function() {
            let cliId = $(this).val();
            let quantity = $(this).data('quantity');
            let selected = $(this).find(':selected');
            let stock = parseFloat(selected.data('stock')) || 0;

            if (!cliId) {
                valid = false;
                Swal.fire('Atención', 'Debes seleccionar un lote y ubicación para cada producto.', 'warning');
                return false;
            }

            if (stock < quantity) {
                valid = false;
                Swal.fire('Atención', `Stock insuficiente en la ubicación seleccionada. Disponible: ${stock}, Requerido: ${quantity}`, 'warning');
                return false;
            }

            lotAssignments.push({
                cli_id: cliId,
                quantity: quantity
            });
        });

        if (!valid) return;

        Swal.fire({
            title: '¿Confirmar venta?',
            text: "Se descontará el inventario del lote y ubicación seleccionados.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, confirmar'
        }).then((result) => {
            if (result.isConfirmed) {
                sendAction('confirm', { lot_assignments: lotAssignments });
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

