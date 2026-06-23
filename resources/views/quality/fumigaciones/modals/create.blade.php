<x-modal id="add-fumigacion">
    <form class="flex flex-col items-center w-full gap-2" id="add-fumigacion-form">
        @csrf

        <x-wrapper-form-1>
            <x-tittle-form>Programar Nueva Fumigación</x-tittle-form>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Proveedor <span class="text-red-500">*</span></x-label>
                <x-input-1 required name="proveedor" placeholder="Ej. Fumigaciones Express"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Fecha Programada <span class="text-red-500">*</span></x-label>
                <x-input-1 required type="datetime-local" name="fecha_programada" value="{{ date('Y-m-d\TH:i') }}"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label>Método de Aplicación</x-label>
                <select name="metodo_aplicacion" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white">
                    <option value="Aspersión" selected>Aspersión</option>
                    <option value="Gel">Gel</option>
                    <option value="Nebulización">Nebulización</option>
                    <option value="Termonebulización">Termonebulización</option>
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label>Estado</x-label>
                <select name="estado" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white">
                    <option value="Pendiente" selected>Pendiente</option>
                    <option value="Realizado">Realizado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </x-wrapper-form-2>

            <x-wrapper-form-2 class="md:col-span-2">
                <x-label>Observaciones</x-label>
                <textarea name="observaciones" placeholder="Detalles adicionales..." 
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 px-3 bg-white" rows="2"></textarea>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 type="button" class="close-modal" colorBtn="red" data-target="add-fumigacion">Cancel</x-button-1>
            <x-button-1 type="submit" id="save-fumigacion" colorBtn="green">Save</x-button-1>
        </x-wrapper-form-1>

    </form>
</x-modal>

@push('js')
<script>
$(function(){

    $('#add-fumigacion-form').on('submit', function(e){
        e.preventDefault();

        const form = $(this);
        const btn = $('#save-fumigacion');
        const btnText = btn.html();
        
        btn.prop('disabled', true).html('Guardando...');

        $.ajax({
            url: "{{ route('fumigaciones.store') }}",
            method: "POST",
            data: form.serialize(),
            success: function(response){
                $('.close-modal[data-target="add-fumigacion"]').trigger('click');
                form[0].reset();
                
                location.reload(); 
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: 'La fumigación se programó correctamente',
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function(xhr){
                btn.prop('disabled', false).html(btnText);
                
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let message = '';
                    for(let field in errors){
                        message += errors[field][0] + '\n';
                    }
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error de validación',
                        text: message,
                        confirmButtonColor: '#d33'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al guardar. Revisa la consola.',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            complete: function(){
                btn.prop('disabled', false).html(btnText);
            }
        });
    });
});
</script>
@endpush