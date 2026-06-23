<x-modal id="create-minuta">
    <form class="flex flex-col items-center w-full gap-2" id="create-minuta-form">
        @csrf
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Nueva Minuta</x-tittle-form>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="fecha_hora">Fecha y Hora</x-label>
                <x-input-1 type="datetime-local" required name="fecha_hora" id="fecha_hora"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="lugar">Lugar</x-label>
                <x-input-1 name="lugar" id="lugar" placeholder="Ej. Sala de juntas B" maxlength="255"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="tema_general">Tema General</x-label>
                <x-input-1 required name="tema_general" id="tema_general" placeholder="Asunto de la reunión"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="ponente">Ponente / Organizador</x-label>
                <x-input-1 name="ponente" id="ponente" placeholder="Nombre de quien dirige"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="flex-col border-2 border-gray-200 rounded-md p-2">
            <x-wrapper-form-1>
                <span class="tracking-[3px] bg-purple-700 text-white font-semibold px-2 py-1 rounded-md">Asistentes</span>
                <x-button-1 type="button" colorBtn="blue" id="add_asistente">Agregar Asistente</x-button-1>
            </x-wrapper-form-1>
            
            <div id="asistentes-container" class="w-full">
                <div class="wrapper-asistente border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative">
                    <span class="remove-asistente absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
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
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="flex-col border-2 border-gray-200 rounded-md p-2">
            <x-wrapper-form-1>
                <span class="tracking-[3px] bg-blue-500 text-white font-semibold px-2 py-1 rounded-md">Temas y Acuerdos</span>
                <x-button-1 type="button" colorBtn="blue" id="add_acuerdo">Agregar Acuerdo</x-button-1>
            </x-wrapper-form-1>
            
            <div id="acuerdos-container" class="w-full">
                <div class="wrapper-acuerdo border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative">
                    <span class="remove-acuerdo absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
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
                    </x-wrapper-form-1>
                </div>
            </div>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 id="save-minuta" colorBtn="green">Guardar Minuta</x-button-1>
        </x-wrapper-form-1>
    </form>

    <div id="asistente_template" class="hidden">
        <div class="wrapper-asistente border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative">
            <span class="remove-asistente absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
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

    <div id="acuerdo_template" class="hidden">
        <div class="wrapper-acuerdo border-2 border-dashed border-gray-200 rounded-md p-2 my-1 relative">
            <span class="remove-acuerdo absolute flex justify-center items-center text-white bg-red-500 hover:bg-red-600 w-6 h-6 top-1 right-1 rounded-full font-bold cursor-pointer p-1">X</span>
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
            </x-wrapper-form-1>
        </div>
    </div>
</x-modal>

@push('js')
<script>
$(function(){
    const father = $('#create-minuta');
    
    function reactiveMinuta(){
        father.on('click', '#add_asistente', function(){
            let template = $('#asistente_template').html();
            $('#asistentes-container').append(template);
        });

        father.on('click', '#add_acuerdo', function(){
            let template = $('#acuerdo_template').html();
            $('#acuerdos-container').append(template);
        });

        father.on('click', '.remove-asistente', function(){
            if($('#asistentes-container .wrapper-asistente').length > 1){
                $(this).closest('.wrapper-asistente').remove();
            }
        });

        father.on('click', '.remove-acuerdo', function(){
            if($('#acuerdos-container .wrapper-acuerdo').length > 1){
                $(this).closest('.wrapper-acuerdo').remove();
            }
        });

        father.on('click', '.close-modal, button[type="reset"]', function(){
            $('#asistentes-container').html($('#asistente_template').html());
            $('#acuerdos-container').html($('#acuerdo_template').html());
        });
    }

    function addMinuta(){
        father.find("#create-minuta-form").submit(function(event) {
            event.preventDefault();
            var form = father.find('#create-minuta-form')[0];
            var data = new FormData(form);
            
            father.find('#save-minuta').prop('disabled', true);
            
            $.ajax({
                type: 'POST',
                url: '/minutas',
                data: data,
                processData: false,
                contentType: false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'La minuta ha sido registrada correctamente.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    father.find('#save-minuta').prop('disabled', false);
                },
                error: function(xhr){
                    let message = 'Ocurrió un error inesperado.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        message = xhr.responseJSON.error;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                    father.find('#save-minuta').prop('disabled', false);
                }
            });
        });
    }
    
    reactiveMinuta();
    addMinuta();
});
</script>
@endpush