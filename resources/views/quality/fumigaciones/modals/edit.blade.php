<x-modal id="edit-fumigacion">
    <form class="flex flex-col items-center w-full gap-2" id="edit-fumigacion-form" method="POST">
        @csrf

        <x-wrapper-form-1>
            <x-tittle-form>Editar Fumigación Programada</x-tittle-form>
        </x-wrapper-form-1>

        <input type="hidden" id="edit-fumigacion-id" name="id">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Proveedor <span class="text-red-500">*</span></x-label>
                <x-input-1 required name="proveedor" id="edit-proveedor" placeholder="Ej. Fumigaciones Express"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Fecha Programada <span class="text-red-500">*</span></x-label>
                <x-input-1 required type="datetime-local" name="fecha_programada" id="edit-fecha-programada"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Método de Aplicación</x-label>
                <select name="metodo_aplicacion" id="edit-metodo-aplicacion" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white">
                    <option value="Aspersión">Aspersión</option>
                    <option value="Gel">Gel</option>
                    <option value="Nebulización">Nebulización</option>
                    <option value="Termonebulización">Termonebulización</option>
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Estado</x-label>
                <select name="estado" id="edit-estado" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Realizado">Realizado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </x-wrapper-form-2>

            <x-wrapper-form-2 class="md:col-span-2">
                <x-label>Observaciones</x-label>
                <textarea name="observaciones" id="edit-observaciones" placeholder="Detalles adicionales..." 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white" rows="2"></textarea>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 type="button" class="close-modal" colorBtn="red" data-target="edit-fumigacion">Cancel</x-button-1>
            <x-button-1 type="submit" id="update-fumigacion" colorBtn="green">Actualizar</x-button-1>
        </x-wrapper-form-1>

    </form>
</x-modal>

@push('js')
<script>
$(function(){
    /* ACTUALIZAR FUMIGACIÓN (AJAX) */
    $('#edit-fumigacion-form').on('submit', function(e){
        e.preventDefault();

        const form = $(this);
        const btn = $('#update-fumigacion');
        const url = form.attr('action'); 
        
        btn.prop('disabled', true).html('Actualizando...');

        $.ajax({
            url: url,
            method: "POST",
            data: form.serialize(),
            success: function(response){
                $('.close-modal[data-target="edit-fumigacion"]').trigger('click');
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: 'El registro se actualizó correctamente',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload(); 
                });
            },
            error: function(xhr){
                btn.prop('disabled', false).html('Actualizar');
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar el registro.',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });
});
</script>
@endpush