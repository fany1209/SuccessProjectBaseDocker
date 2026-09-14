<section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">
        <div class="flex justify-between items-center w-full my-1 gap-2">
            <p class="bg-purple-700 text-white p-2 rounded-md font-semibold tracking-[3px] shadow-sm text-xs sm:text-sm">
                 MINUTAS: {{ $total_minutas ?? 0 }}
            </p>
            <x-button data-target="create-minuta" 
                class="open-modal bg-green-500 hover:bg-green-600 text-white shadow-sm transition duration-150">
                + Nueva Minuta
            </x-button>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 w-full mb-3">
            <x-input class="w-full p-2" id="search-minuta" placeholder="Buscar por tema o ponente..."></x-input>
            
        </div>

        <div class="w-full bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <table id="minutas-table" class="w-full divide-y divide-gray-200 text-sm text-left table-auto">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-4 py-3">Fecha/Hora</th>
                        <th class="px-4 py-3">Lugar</th>
                        <th class="px-4 py-3">Tema</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-gray-800"></tbody>
            </table>
        </div>
    </div>

    @include('minutas.modals.addMinuta')
    @include('minutas.modals.editMinuta')
    @include('minutas.modals.viewMinuta')
</section>

@push('js')
<script>
$(document).ready(function(){
    
    let table = $('#minutas-table').DataTable({
        processing: true,
        ajax: {
            url: "{{ route('minutas.getSuppliers') }}",
            data: function (d) {
                d.status = $('#status-filter').val();
                d.search = $('#search-minuta').val();
            },
            dataSrc: 'minutas'
        },
        columns: [
            { data: 'fecha_hora', className: 'px-4 py-3' },
            { data: 'lugar', className: 'px-4 py-3' },
            { data: 'tema_general', className: 'px-4 py-3 font-semibold text-gray-700' },
            { 
                data: null,
                orderable: false,
                className: 'px-4 py-3 text-center',
                render: function(data, type, row) {
                    return `
                        <div class="flex justify-center gap-1">
                            <div class="flex flex-wrap justify-center gap-1">
                                    <a href="/minutas/${row.id_minuta}/pdf" target="_blank" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white rounded-sm p-2 shadow-sm transition flex items-center justify-center" 
                                    title="Descargar PDF">
                                        <img width="18" src="{{ asset('images/archivo.png') }}" alt="PDF"/>
                                    </a>

                                    <button data-id="${row.id_minuta}" class="view-btn text-white bg-green-500 hover:bg-green-600 rounded-sm p-2 shadow-sm transition" title="Ver Detalles">
                                        <i class="fas fa-eye text-xs"></i> Ver
                                    </button>

                                    <button data-id="${row.id_minuta}" class="edit-btn text-white bg-blue-500 hover:bg-blue-600 rounded-sm p-2 shadow-sm transition" title="Editar">
                                        <img width="18" src="{{ asset('images/editar.png') }}" alt="Edit"/>
                                    </button>

                                    @can('admin.dashboard')
                                    <button data-id="${row.id_minuta}" class="delete-btn text-white bg-red-500 hover:bg-red-600 rounded-sm p-2 shadow-sm transition" title="Eliminar">
                                        <img width="18" src="{{ asset('images/borrar.png') }}" alt="Delete"/>
                                    </button>
                                    @endcan

                                </div>
                    `;
                }
            }
        ],
        lengthChange: false,
        searching: false,
        pageLength: 15,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        }
    });

    $('#status-filter, #search-minuta').on('change keyup', function () {
        table.ajax.reload();
    });

    function initActions() {
        
        $('#minutas-table tbody').on('click', '.view-btn', function() {
            let id = $(this).data('id');
            $.get(`/minutas/${id}`, function(response) {
                let m = response.minuta;
                let father = $('#view-minuta');

                let safeEscape = window.escapeHtml || function(str){ return (str ?? '').toString().replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[s])); };

                let asistentes = m.asistente_nombre ? m.asistente_nombre.split(', ').map(n => `<li>• ${safeEscape(n)}</li>`).join('') : 'Ninguno';
                let acuerdos = m.acuerdo ? m.acuerdo.split(' | ').map(a => `<li class="mb-1 text-xs">📌 ${safeEscape(a)}</li>`).join('') : 'Sin acuerdos';

                let html = `
                    <div class="bg-gray-50 p-3 rounded mb-4 grid grid-cols-2 gap-2 text-sm border">
                        <p><strong>📅 Fecha:</strong> ${safeEscape(m.fecha_hora)}</p>
                        <p><strong>📍 Lugar:</strong> ${safeEscape(m.lugar)}</p>
                        <p class="col-span-2 border-t pt-1"><strong>🎙️ Ponente:</strong> ${safeEscape(m.ponente || 'No asignado')}</p>
                    </div>
                    <div class="mb-4">
                        <h4 class="font-bold text-purple-700 border-b text-xs mb-2">ASISTENTES</h4>
                        <ul class="text-xs text-gray-600 space-y-1">${asistentes}</ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-700 border-b text-xs mb-2 tracking-tighter">ACUERDOS DE LA REUNIÓN</h4>
                        <ul class="text-xs text-gray-600">${acuerdos}</ul>
                    </div>
                `;
                $('#view-content').html(html);
                father.css('display', 'flex').hide().fadeIn(200); 
            });
        });

        $('#minutas-table tbody').on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: "¿Eliminar minuta?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                confirmButtonText: "Borrar ahora"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/minutas/${id}`,
                        method: "DELETE",
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function() {
                            table.ajax.reload();
                            Swal.fire("Éxito", "Minuta eliminada correctamente.", "success");
                        }
                    });
                }
            });
        });
    }

     $('#minutas-table tbody').on('click', '.edit-btn', function() {
            let id = $(this).data('id');
            let modal = $('#edit-minuta');

            $.get(`/minutas/${id}`, function(response) {
                let m = response.minuta;
                
                $('#id_minuta').val(m.id_minuta);
                if(m.fecha_hora) $('#edit_fecha_hora').val(m.fecha_hora.replace(' ', 'T'));
                $('#edit_lugar').val(m.lugar);
                $('#edit_tema_general').val(m.tema_general);
                $('#edit_ponente').val(m.ponente);
                $('#asistentes-container-edit').empty();
                if(m.asistente_nombre) {
                    let nombres = m.asistente_nombre.split(', ');
                    let departamentos = m.asistente_departamento ? m.asistente_departamento.split(', ') : [];
                    
                    nombres.forEach((nom, index) => {
                        let template = $('#asistente_template_edit').html();
                        let $item = $(template);
                        $item.removeClass('hidden'); 
                        $item.find('input[name="asistente_nombre[]"]').val(nom);
                        $item.find('input[name="asistente_departamento[]"]').val(departamentos[index] || '');
                        $('#asistentes-container-edit').append($item);
                    });
                }

                $('#acuerdos-container-edit').empty();
                if(m.acuerdo) {
                    let temas = m.tema_tratado ? m.tema_tratado.split(' | ') : [];
                    let acuerdos = m.acuerdo.split(' | ');
                    let responsables = m.responsable ? m.responsable.split(', ') : [];
                    let fechas = m.fecha_cierre ? m.fecha_cierre.split(', ') : [];
                    let estatus = m.estatus ? m.estatus.split(', ') : [];
                    let fechasCompromiso = m.fecha_compromiso ? m.fecha_compromiso.split(', ') : [];

                    acuerdos.forEach((acu, index) => {
                        let template = $('#acuerdo_template_edit').html();
                        let $item = $(template);
                        $item.removeClass('hidden');
                        
                        $item.find('input[name="tema_tratado[]"]').val(temas[index] || '');
                        $item.find('input[name="acuerdo[]"]').val(acu);
                        $item.find('input[name="responsable[]"]').val(responsables[index] || '');
                        $item.find('input[name="fecha_compromiso[]"]').val(fechasCompromiso[index] || '');
                        $item.find('input[name="fecha_cierre[]"]').val(fechas[index] || '');
                        $item.find('select[name="estatus[]"]').val(estatus[index] || 'Pendiente');
                        
                        $('#acuerdos-container-edit').append($item);
                    });
                }

                modal.css('display', 'flex').hide().fadeIn(200);
            }).fail(function() {
                Swal.fire('Error', 'No se pudo obtener la información de la minuta', 'error');
            });
        });

    $(document).on('click', '.close-modal', function(e){
        e.preventDefault();
        $(this).closest('[id^="view-"], [id^="edit-"], [id^="create-"]').fadeOut(200);
    });

    $(document).on('click', '[id^="view-"], [id^="edit-"], [id^="create-"]', function(e) {
        if (e.target !== this) return; 
        $(this).fadeOut(200);
    });

    initActions();
});
</script>
@endpush