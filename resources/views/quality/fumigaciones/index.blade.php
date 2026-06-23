@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4">

    <section class="col-span-12 w-full flex flex-col items-center px-1">
        <div class="flex flex-col justify-center items-center w-full">

            <div class="mt-6 text-center w-full">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
                    Control de Fumigaciones
                </h1>
                <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
            </div>

            <x-section-1>
                <div class="flex justify-end items-center w-full my-1 gap-2">
                    <x-button data-target="add-fumigacion"
                        class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 text-white px-4 py-2 rounded shadow">
                        <i class="ri-add-line"></i> Programar Fumigación
                    </x-button>

                    @include('quality.fumigaciones.modals.create')
                    @include('quality.fumigaciones.modals.edit')

                    <button id="btn-open-edit-fumigacion" type="button" class="open-modal hidden"
                        data-target="edit-fumigacion"></button>
                </div>
            </x-section-1>

            <div class="w-full mt-6">
                <x-section-1>
                    <div class="mt-4 mb-6 text-center w-full">
                        <h2 class="text-xl font-bold text-[#198754] flex items-center justify-center gap-2">
                            <i class="ri-calendar-todo-line"></i> Calendario de Servicios
                        </h2>
                    </div>
                    <div id="calendar" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200"></div>
                </x-section-1>
            </div>

            <div class="w-full mt-10 mb-10">
                <div class="mb-4 text-center">
                    <h2 class="text-lg font-semibold text-gray-700 uppercase tracking-wider">Listado de Fumigaciones</h2>
                </div>
                <table id="fumigaciones-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-md">
                        <tr>
                            <th>Proveedor</th>
                            <th>Fecha Programada</th>
                            <th>Método de Aplicación</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                            <th class="w-32 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm">
                        @foreach ($todasLasFumigaciones as $fumigacion)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-blue-800 font-medium">{{ $fumigacion->proveedor }}</td>
                            
                            <td class="px-4 py-3 text-gray-600">
                                <div class="font-bold">{{ \Carbon\Carbon::parse($fumigacion->fecha_programada)->format('d/m/Y') }}</div>
                                <div class="text-xs italic">{{ \Carbon\Carbon::parse($fumigacion->fecha_programada)->format('h:i A') }}</div>
                            </td>

                            <td class="px-4 py-3">
                                <span class="bg-gray-200 text-gray-700 py-1 px-2 rounded-full text-xs font-bold">
                                    {{ $fumigacion->metodo_aplicacion }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                @php
                                    $color = [
                                        'Pendiente' => 'text-yellow-600 bg-yellow-100',
                                        'Realizado' => 'text-green-600 bg-green-100',
                                        'Cancelado' => 'text-red-600 bg-red-100'
                                    ][$fumigacion->estado] ?? 'text-gray-600 bg-gray-100';
                                @endphp
                                <span class="{{ $color }} px-2 py-1 rounded text-[10px] font-bold uppercase">
                                    {{ $fumigacion->estado }}
                                </span>
                            </td>
                            
                            <td class="px-4 py-3 text-gray-500 truncate" title="{{ $fumigacion->observaciones }}">
                                {{ Str::limit($fumigacion->observaciones, 40) }}
                            </td>
                            
                            <td class="px-4 py-3 text-center">
                                <div class="flex gap-1 justify-center items-center">
                                    <button type="button"
                                            data-id="{{ $fumigacion->id }}"
                                            data-proveedor="{{ $fumigacion->proveedor }}"
                                            data-fecha="{{ \Carbon\Carbon::parse($fumigacion->fecha_programada)->format('Y-m-d\TH:i') }}"
                                            data-metodo="{{ $fumigacion->metodo_aplicacion }}"
                                            data-estado="{{ $fumigacion->estado }}"
                                            data-observaciones="{{ $fumigacion->observaciones }}"
                                            class="btn-edit-fumigacion text-sm text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2 transition duration-200"
                                            title="Editar">
                                        <img width="18" src="{{ asset('images/editar.png') }}" alt="Editar">
                                    </button>

                                    <button type="button" data-id="{{ $fumigacion->id }}"
                                            class="btn-delete-fumigacion text-sm text-white bg-red-500 hover:bg-red-600 rounded-sm p-2 transition duration-200"
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

        </div>
    </section>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
    $(function() {
        // Inicializar DataTable
        const table = $('#fumigaciones-table').DataTable({
            pageLength: 10,
            lengthChange: false,
            language: {
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                zeroRecords: "No hay resultados",
                paginate: { next: "Siguiente", previous: "Anterior" },
                search: "Buscar:"
            }
        });

        // Configurar Calendario
        const calendarEl = document.getElementById('calendar');
        const eventos = @json($eventos);

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            events: eventos,
            eventClick: function(info) {
                const id = info.event.id;
                $(`.btn-edit-fumigacion[data-id="${id}"]`).click();
            }
        });
        calendar.render();

        /* LÓGICA DE EDITAR */
        $(document).on('click', '.btn-edit-fumigacion', function() {
            $('#btn-open-edit-fumigacion').trigger('click');
            
            $('#edit-fumigacion-id').val($(this).data('id'));
            $('#edit-proveedor').val($(this).data('proveedor'));
            $('#edit-fecha-programada').val($(this).data('fecha'));
            $('#edit-metodo-aplicacion').val($(this).data('metodo'));
            $('#edit-estado').val($(this).data('estado'));
            $('#edit-observaciones').val($(this).data('observaciones'));

            let idFumigacion = $(this).data('id');
            $('#edit-fumigacion-form').attr('action', '/fumigaciones/update/' + idFumigacion);
        });

        /* LÓGICA DE ELIMINAR */
        $(document).on('click', '.btn-delete-fumigacion', function() {
            const id = $(this).data('id');
            const url = "/fumigaciones/delete/" + id;

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará la fecha programada",
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
                        }
                    });
                }
            });
        });
    });
</script>

<style>
    .fc-theme-standard td, .fc-theme-standard th { border: 1px solid #e5e7eb; }
    .fc .fc-toolbar-title { font-size: 1.1rem; font-weight: 700; color: #374151; text-transform: uppercase; }
    .fc .fc-button-primary { background-color: #198754; border: none; font-size: 0.8rem; }
    .fc .fc-button-primary:hover { background-color: #157347; }
    .fc .fc-button-primary:disabled { background-color: #6fb494; }
    .fc-event { cursor: pointer; padding: 2px 4px; border-radius: 4px; }
</style>
@endpush