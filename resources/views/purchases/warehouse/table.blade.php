@php
  $isAdmin = auth()->user()->canany(['purchases.admin', 'quality.purchases']);
  $colCount = $isAdmin ? 10 : 5; 
@endphp

<table id="warehouse-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
    <thead class="bg-gray-50 text-gray-700 uppercase text-md">
        <tr>
            <th scope="col" class="px-6 py-4 text-right">FECHA</th>
            <th scope="col" class="px-6 py-4 text-right">PROVEEDOR</th>
            <th scope="col" class="px-6 py-4 text-right">DESCRIPCIÓN</th>
            <th scope="col" class="px-6 py-4 text-right">CANTIDAD</th>
            <th scope="col" class="px-6 py-4 text-right">UNIDAD</th>

            @can('purchases.admin')
                <th scope="col" class="px-6 py-4 text-right">CATEGORÍA</th>
                <th scope="col" class="px-6 py-4 text-right">INSUMO</th>
                <th scope="col" class="px-6 py-4 text-right">COSTO</th>
                <th scope="col" class="px-6 py-4 text-right">MONEDA</th>
                <th scope="col" class="px-6 py-4">ACTIONS</th>
            @endcan
        </tr>
    </thead>

    <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words max-w-xs">
        @forelse(($warehouse_entries ?? []) as $row)
            <tr>
                <td class="px-6 py-3 text-right">{{ $row->fecha_llegada }}</td>
                <td class="px-6 py-3 text-right">{{ $row->proveedor }}</td>
                <td class="px-6 py-3 text-right">
                    {{ \Illuminate\Support\Str::limit($row->descripcion, 80) }}
                </td>
                <td class="px-6 py-3 text-right">
                    {{ rtrim(rtrim(number_format((float)($row->cantidad ?? 0), 3, '.', ''), '0'), '.') }}
                </td>
                <td class="px-6 py-3 text-right">{{ $row->unidad }}</td>

                @can('purchases.admin')
                    <td class="px-6 py-3 text-right">
                        {{ $row->categoria ?? '-' }}
                    </td>
                    <td class="px-6 py-3 text-right font-semibold">{{ $row->insumo }}</td>
                    <td class="px-6 py-3 text-right">{{ number_format((float)($row->costo ?? 0), 4, '.', ',') }}</td>
                    <td class="px-6 py-3 text-right">{{ $row->moneda }}</td>

                    <td class="px-6 py-3 text-center align-middle">
                        <div class="flex justify-center gap-2">
                            <button
                                data-id="{{ $row->id }}"
                                data-target="edit-warehouse-entry"
                                class="open-modal edit-warehouse-btn text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2"
                                title="Edit">
                                <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                            </button>

                            <button
                                data-id="{{ $row->id }}"
                                class="delete-warehouse-btn text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2"
                                title="Delete">
                                <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
                            </button>
                        </div>
                    </td>
                @endcan
            </tr>
        @empty
            <tr>
                @can('purchases.admin')
                    <td colspan="10" class="px-6 py-6 text-center text-gray-500">No hay entradas registradas.</td>
                @else
                    <td colspan="5" class="px-6 py-6 text-center text-gray-500">No hay entradas registradas.</td>
                @endcan
            </tr>
        @endforelse
    </tbody>
</table>

@include('purchases.warehouse.edit')


@push('js')
<script>
$(document).ready(function () {

    // DataTable init
    if ($.fn.DataTable.isDataTable('#warehouse-table')) {
        $('#warehouse-table').DataTable().destroy();
    }

    const table = $('#warehouse-table').DataTable({
        lengthChange: false,
        searching: true,
        pageLength: 10,
        serverSide: false,
        responsive: true,
        autoWidth: false,
        language: {
            info: "Show _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "There aren't entries available",
            zeroRecords: "No results found",
            infoFiltered: "(filtered on _MAX_ total records)",
            paginate: { first:"First", last:"Last", next:"Next", previous:"Previous" }
        }
    });

    window._warehouseDT = table;

    // EDIT
    $('#warehouse-table tbody').on('click', '.edit-warehouse-btn', function(){
        const id = $(this).data('id');

        $.ajax({
            type: 'GET',
            url: `/insumos_entradas/${id}`,
            success: function(res){
                const e = res.entry || res;

                $('#wh_id').val(e.id);
                $('#wh_fecha_llegada').val(e.fecha_llegada);
                $('#wh_proveedor').val(e.proveedor);
                $('#wh_insumo').val(e.insumo);
                $('#wh_cantidad').val(e.cantidad);
                $('#wh_unidad').val(e.unidad);
                $('#wh_costo').val(e.costo);
                $('#wh_moneda').val(e.moneda);

                const desc = (e.descripcion ?? '');
                const $modal = $('#edit-warehouse-entry');
                const $desc = $modal.find('textarea[name="descripcion"]');

                if ($desc.length) {
                    $desc.val(desc).trigger('input').trigger('change');
                } else {
                    $modal.find('#wh_descripcion').val(desc).trigger('input').trigger('change');
                }

                $('#edit-warehouse-form').attr('action', `/insumos_entradas/${id}`);
            },
            error: function(){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar la entrada.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

  
    $('#edit-warehouse-form').off('submit').on('submit', function(e){
        e.preventDefault();

        const $form  = $(this);
        const action = $form.attr('action');

        if (!action) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se encontró la ruta de actualización (action del form vacío).',
                confirmButtonText: 'OK'
            });
            return;
        }

        let payload = $form.serialize();

        if (!payload.includes('_method=')) {
            payload += '&_method=PUT';
        } else {
            payload = payload.replace(/_method=[^&]*/g, '_method=PUT');
        }

        $.ajax({
            url: action,
            type: 'POST',
            data: payload,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(){
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Entry updated correctly.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr){
                let message = 'Ocurrió un error inesperado.';
                if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON.message;

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // DELETE
    $('#warehouse-table tbody').on('click', '.delete-warehouse-btn', function(){
        const id = $(this).data('id');
        const row = table.row($(this).closest('tr'));

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if(!result.isConfirmed) return;

            $.ajax({
                url: `/insumos_entradas/${id}`,
                method: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(){
                    row.remove().draw();
                    Swal.fire({
                        title: "Deleted!",
                        text: "The entry was removed.",
                        icon: "success"
                    });
                },
                error: function(xhr){
                    let message = 'Ocurrió un error inesperado.';
                    if (xhr.responseJSON && xhr.responseJSON.error) message = xhr.responseJSON.error;

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });

});
</script>
@endpush
