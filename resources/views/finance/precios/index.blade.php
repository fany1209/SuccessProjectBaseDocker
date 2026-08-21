@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4">

    <section class="col-span-12 w-full flex flex-col items-center px-1">
        <div class="flex flex-col justify-center items-center w-full">

            <div class="mt-6 text-center w-full">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
                    Precios de Proveedores
                </h1>
                <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
            </div>

            <x-section-1>
                <div class="flex justify-end items-center w-full my-1 gap-2">
                    <x-button data-target="add-precio"
                        class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 text-white px-4 py-2 rounded shadow">
                        <i class="ri-add-line"></i> Agregar Precio
                    </x-button>

                    @include('finance.precios.modals.create')
                    @include('finance.precios.modals.edit')

                    <button id="btn-open-edit-precio" type="button" class="open-modal hidden"
                        data-target="edit-precio"></button>
                </div>
            </x-section-1>

            <div class="w-full mt-4">
                <table id="precios-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                        <tr>
                            <th>Insumo / SAT</th>
                            <th>Proveedor</th>
                            <th>Precio / IVA</th>
                            <th>Moneda</th>
                            <th>Fecha Cotización</th>
                            <th class="w-32 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm">
                        @foreach ($todosLosPrecios as $precio)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800">{{ $precio->insumo }}</div>
                                <div class="text-xs text-gray-500 font-mono italic">SAT: {{ $precio->clave_sat ?? 'N/A' }}</div>
                            </td>
                            
                            <td class="px-4 py-3 text-blue-800">{{ $precio->proveedor }}</td>
                            
                            <td class="px-4 py-3">
                                <div class="font-bold text-green-700">${{ number_format($precio->precio, 2) }}</div>
                                <div class="text-[10px] uppercase font-bold {{ $precio->tiene_iva ? 'text-blue-500' : 'text-red-400' }}">
                                    {{ $precio->tiene_iva ? '+ IVA (16%)' : 'Sin IVA' }}
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <span class="bg-gray-200 text-gray-700 py-1 px-2 rounded-full text-xs font-bold">{{ $precio->moneda }}</span>
                            </td>
                            
                            <td class="px-4 py-3 text-gray-500" data-sort="{{ \Carbon\Carbon::parse($precio->fecha_cotizacion)->format('Y-m-d') }}">
                                {{ \Carbon\Carbon::parse($precio->fecha_cotizacion)->format('d/m/Y') }}
                            </td>
                            
                            <td class="px-4 py-3 text-center">
                                <div class="flex gap-1 justify-center items-center">
                                    <button type="button"
                                            data-id="{{ $precio->id }}"
                                            data-insumo="{{ $precio->insumo }}"
                                            data-sat="{{ $precio->clave_sat }}"
                                            data-proveedor="{{ $precio->proveedor }}"
                                            data-precio="{{ $precio->precio }}"
                                            data-iva="{{ $precio->tiene_iva }}"
                                            data-moneda="{{ $precio->moneda }}"
                                            data-fecha="{{ $precio->fecha_cotizacion }}"
                                            class="btn-edit-precio text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2 transition duration-200"
                                            title="Editar">
                                        <img width="18" src="{{ asset('images/editar.png') }}" alt="Editar">
                                    </button>

                                    <button type="button" data-id="{{ $precio->id }}"
                                            class="btn-delete-precio text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2 transition duration-200"
                                            title="Eliminar">
                                        <img width="18" src="{{ asset('images/borrar.png') }}" alt="Borrar">
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @include('finance.precios.grafica')
        </div>
    </section>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function() {
        const table = $('#precios-table').DataTable({
            pageLength: 15,
            lengthChange: false,
            language: {
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                zeroRecords: "No hay resultados",
                paginate: { next: "Siguiente", previous: "Anterior" },
                search: "Buscar:"
            }
        });

        /* EDITAR PRECIO */
        $(document).on('click', '.btn-edit-precio', function() {
            $('#btn-open-edit-precio').trigger('click');
            
            $('#edit-precio-id').val($(this).data('id'));
            $('#edit-insumo').val($(this).data('insumo'));
            $('#edit-proveedor').val($(this).data('proveedor'));
            $('#edit-precio-val').val($(this).data('precio'));
            $('#edit-moneda').val($(this).data('moneda'));
            $('#edit-fecha').val($(this).data('fecha'));
            $('#edit-clave-sat').val($(this).data('sat')); 
            
            if ($(this).data('iva') == 1) {
                $('#edit-tiene-iva').prop('checked', true);
            } else {
                $('#edit-tiene-iva').prop('checked', false);
            }

            let idPrecio = $(this).data('id');
            $('#edit-precio-form').attr('action', '/precios/update/' + idPrecio);
        });

        /* ELIMINAR PRECIO */
        $(document).on('click', '.btn-delete-precio', function() {
            const id = $(this).data('id');
            const url = "/precios/delete/" + id;

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'POST', 
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Eliminado!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload(); 
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'No se pudo eliminar el registro.', 'error');
                        }
                    });
                }
            });
        });

        /* AUTO-REFRESH DESDE FACTURAS */
        window.addEventListener('storage', function(e) {
            if (e.key === 'factura_updated') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Nuevos precios agregados. Actualizando...',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            }
        });
    });
</script>
@endpush