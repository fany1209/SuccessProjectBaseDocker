{{--
Operators
Actualizar Operator
Fecha de creación: 14-10-2025
Creado por: Jacob
Actualizado por: Jacob
Fecha de actualización: 14-10-2025
--}}
<x-modal id="edit-operator">
    <form class="flex flex-col items-center w-full gap-2" id="edit-operator-form">
        @csrf
        <x-wrapper-form-1>
            <x-tittle-form>Edit Operator</x-tittle-form>
            <x-input-1 type="hidden" name="operator_id"></x-input-1>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="name">Name</x-label>
                <x-input-1 required name="name" maxlength="100"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="license">License</x-label>
                <x-input-1 required name="license" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>
        <x-wrapper-form-1>
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Close</x-button-1>
            <x-button-1 id="update-operator" colorBtn="blue">Finish</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
@push('js')
<script>
$(document).ready(function(){
    function updateOperator(){
        $("#edit-operator-form").submit(function(event) {
            event.preventDefault();
            var form = $('#edit-operator-form')[0];
            var data = new FormData(form);
            var id = $('#operator_id').val();
            data.append('_method', 'PUT');
            $.ajax({
                type:'POST',
                url:`/operator/${id}`,
                data:data,
                processData:false,
                contentType:false,
                success: function(){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'The Operator was updated successfully.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error:function(xhr){
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
                }
            });
        });
    }
    updateOperator();
});
</script>
@endpush