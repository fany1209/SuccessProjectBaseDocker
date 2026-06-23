<x-modal id="edit-minuta">
    <form class="flex flex-col items-center w-full gap-2" id="edit-minuta-form">
        @csrf
        <input type="hidden" name="id_minuta" id="id_minuta">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Editar Minuta</x-tittle-form>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_fecha_hora">Fecha y Hora</x-label>
                <x-input-1 type="datetime-local" required name="fecha_hora" id="edit_fecha_hora"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_lugar">Lugar</x-label>
                <x-input-1 name="lugar" id="edit_lugar" placeholder="Ej. Sala de juntas B" maxlength="255"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_tema_general">Tema General</x-label>
                <x-input-1 required name="tema_general" id="edit_tema_general" placeholder="Asunto de la reunión"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_ponente">Ponente / Organizador</x-label>
                <x-input-1 name="ponente" id="edit_ponente" placeholder="Nombre de quien dirige"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="flex-col border-2 border-purple-200 rounded-md p-2">
            <x-wrapper-form-1>
                <span class="tracking-[3px] bg-purple-700 text-white font-semibold px-2 py-1 rounded-md">Asistentes</span>
                <x-button-1 type="button" colorBtn="blue" id="add_asistente_edit">+ Añadir</x-button-1>
            </x-wrapper-form-1>
            <div id="asistentes-container-edit" class="w-full"></div>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="flex-col border-2 border-blue-200 rounded-md p-2">
            <x-wrapper-form-1>
                <span class="tracking-[3px] bg-blue-600 text-white font-semibold px-2 py-1 rounded-md">Temas y Acuerdos</span>
                <x-button-1 type="button" colorBtn="blue" id="add_acuerdo_edit">+ Añadir</x-button-1>
            </x-wrapper-form-1>
            <div id="acuerdos-container-edit" class="w-full"></div>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 id="update-minuta" colorBtn="blue">Actualizar Cambios</x-button-1>
        </x-wrapper-form-1>
    </form>

    <div id="asistente_template_edit" class="hidden">
        <div class="wrapper-asistente-edit border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative">
            <span class="remove-asistente-edit absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Nombre</x-label>
                    <x-input-1 name="asistente_nombre[]" placeholder="Nombre completo"></x-input-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label>Departamento</x-label>
                    <x-input-1 name="asistente_departamento[]" placeholder="Área"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>
        </div>
    </div>

    <div id="acuerdo_template_edit" class="hidden">
        <div class="wrapper-acuerdo-edit border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative">
            <span class="remove-acuerdo-edit absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
            
            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Tema Tratado</x-label>
                    <x-input-1 name="tema_tratado[]" placeholder="Resumen"></x-input-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label>Acuerdo</x-label>
                    <x-input-1 name="acuerdo[]" placeholder="Acuerdo alcanzado"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label>Responsable</x-label>
                    <x-input-1 name="responsable[]" placeholder="Nombre"></x-input-1>
                </x-wrapper-form-2>
                
                @can('admin.dashboard')
                    <x-wrapper-form-2>
                        <x-label>Fecha Compromiso</x-label>
                        <x-input-1 type="date" name="fecha_compromiso[]"></x-input-1>
                    </x-wrapper-form-2>

                    <x-wrapper-form-2>
                        <x-label>Fecha Cierre</x-label>
                        <x-input-1 type="date" name="fecha_cierre[]"></x-input-1>
                    </x-wrapper-form-2>

                    <x-wrapper-form-2>
                        <x-label>Estatus</x-label>
                        <x-select-1 name="estatus[]">
                            <option value="Pendiente">Pendiente</option>
                            <option value="En proceso">En proceso</option>
                            <option value="Finalizado">Finalizado</option>
                        </x-select-1>
                    </x-wrapper-form-2>
                @else
                    <input type="hidden" name="fecha_compromiso[]" value="">
                    <input type="hidden" name="fecha_cierre[]" value="">
                    <input type="hidden" name="estatus[]" value="Pendiente">
                @endcan
            </x-wrapper-form-1>
        </div>
    </div>
</x-modal>

@push('js')
<script>
$(function(){
    const father = $('#edit-minuta');
    
    function reactiveMinutaEdit(){
        father.on('click', '#add_asistente_edit', function(){
            let template = $('#asistente_template_edit').html();
            $('#asistentes-container-edit').append(template);
        });

        father.on('click', '#add_acuerdo_edit', function(){
            let template = $('#acuerdo_template_edit').html();
            $('#acuerdos-container-edit').append(template);
        });

        father.on('click', '.remove-asistente-edit', function(){
            if($('#asistentes-container-edit .wrapper-asistente-edit').length > 1){
                $(this).closest('.wrapper-asistente-edit').remove();
            } else {
                Swal.fire('Atención', 'Debe haber al menos un asistente.', 'warning');
            }
        });

        father.on('click', '.remove-acuerdo-edit', function(){
            if($('#acuerdos-container-edit .wrapper-acuerdo-edit').length > 1){
                $(this).closest('.wrapper-acuerdo-edit').remove();
            } else {
                Swal.fire('Atención', 'Debe haber al menos un acuerdo.', 'warning');
            }
        });
    }

    function updateMinuta(){
        father.find("#edit-minuta-form").submit(function(event) {
            event.preventDefault();
            var form = father.find('#edit-minuta-form')[0];
            var data = new FormData(form);
            let id = $('#id_minuta').val(); 

            if(!id){
                Swal.fire('Error', 'No se encontró el ID de la minuta.', 'error');
                return;
            }
            
            father.find('#update-minuta').prop('disabled', true);
            
            $.ajax({
                type: 'POST',
                url: `/minutas/${id}`, 
                data: data,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'La minuta ha sido actualizada correctamente.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        location.reload();
                    });
                },
                error: function(xhr){
                    let message = 'Ocurrió un error inesperado al actualizar.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        message = xhr.responseJSON.error;
                    }
                    Swal.fire('Error', message, 'error');
                    father.find('#update-minuta').prop('disabled', false);
                }
            });
        });
    }
    
    reactiveMinutaEdit();
    updateMinuta();
});
</script>
@endpush